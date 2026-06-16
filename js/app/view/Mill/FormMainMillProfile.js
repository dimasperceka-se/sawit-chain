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
    var lat = Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Latitude').getValue();
    var longs = Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Longitude').getValue();
	 
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

Ext.define('Koltiva.view.Mill.FormMainMillProfile' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.FormMainMillProfile',
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
        var cmb_status_mill = Ext.create('Koltiva.store.Mill.CmbStatusMill');
        var cmb_mill_group = Ext.create('Koltiva.store.Mill.CmbMillGroup');
        var cmb_legalstatus = Ext.create('Koltiva.store.ComboGeneral.CmbLegalStatus');
        var MainGridProducts = Ext.create('Koltiva.view.Mill.GridProductProfile');
        //store yg dipakai (end)

        //Panel Basic Data ================================================================================================= (Begin)
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Mill Data'),
            frame: true,
            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData',
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
                    title: lang('Basic Data'),
                    padding: '0 0 0 5',
                    items:[{
                        columnWidth: 1,
                        layout:'form',
                        items:[{
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
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-GeneralSection',
                                    style: 'margin-top:10px;',
                                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillID',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillID',
                                    inputType: 'hidden'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'hiddenfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-SupplychainID',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-SupplychainID'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillDisplayID',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillDisplayID',
                                    fieldLabel: lang('Mill ID'),
                                    labelAlign:'top',
                                    readOnly:true
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillName',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillName',
                                    fieldLabel: lang('Mill Name'),
                                    labelAlign:'top',
                                    allowBlank: false
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-CompanyName',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-CompanyName',
                                    fieldLabel: lang('Company Name'),
                                    labelAlign:'top',
                                    allowBlank: false
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Alias',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Alias',
                                    fieldLabel: lang('Alias'),
                                    labelAlign:'top',
                                    allowBlank: false
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillGroup',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillGroup',
                                    store: cmb_mill_group,
                                    fieldLabel: lang('Mill Group'),
                                    labelAlign:'top',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Year',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Year',
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
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Status',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Status',
                                    store: cmb_legalstatus,
                                    fieldLabel: lang('Legal Status of the Company'),
                                    labelAlign:'top',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id'
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PermanentEmployeeMale',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PermanentEmployeeMale',
                                    fieldLabel: lang('Male Permanent Employee'),
                                    labelAlign: 'top',
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                }]
                            },{
                                columnWidth: 0.5,
                                margin:'0 10 0 0',
                                style:'margin-left:15px;',
                                layout:'form',
                                items:[{
                                    xtype: 'panel',
                                    title: lang('Mill Logo'),
                                    frame: false,
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-ComunicationSection',
                                    style: 'margin-top:10px;',
                                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                },{
                                    layout:'column',
                                    border:false,
                                    items:[{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style: 'margin-right: 5px',
                                        items:[{
                                            xtype: 'image',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Photo',
                                            width: '200px',
                                            height:'200px',
                                            src: m_api_base_url + '/images/default_photo/business-logo.jpg'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PhotoOld',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PhotoOld',
                                            inputType: 'hidden'
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        layout:'vbox',
                                        style: 'margin-left: 5px',
                                        items:[{
                                            html:'<h3 style="width: 210px;height: 66px;font-family: OpenSans;font-size: 15px;font-weight: normal;font-stretch: normal;font-style: normal;line-height: normal;letter-spacing: normal;color: #2a2e32;">'+lang('Image file size no larger than 10MB.Supported formats: JPEG, JPG, PNG.Use a high quality image: 512x512px')+'</h3>'
                                        }]
                                    }]
                                },{
                                    layout:'column',
                                    border:false,
                                    style:'margin-top:-20px',
                                    items:[{
                                        columnWidth: 1,
                                        border: false,
                                        layout:'form',
                                        items:[{
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang('Mill Logo'),
                                            labelAlign: 'top',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MemberPhotoInput',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MemberPhotoInput',
                                            buttonText: 'Browse',
                                            listeners: {
                                                'change': function (fb, v) {
                                                    objPanelBasicData.submit({
                                                        url: m_api + '/mill/image_mill',
                                                        clientValidation: false,
                                                        params: {
                                                            opsiDisplay: thisObj.opsiDisplay,
                                                            MillID: Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillID').getValue()
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Photo').setSrc(o.result.file);
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PhotoOld').setValue(o.result.filepath);
                                                        }
                                                    });
                                                }
                                            }
                                        }]
                                    }]
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PermanentEmployeeFemale',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PermanentEmployeeFemale',
                                    fieldLabel: lang('Female Permanent Employee'),
                                    labelAlign: 'top',
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-TemporaryEmployeeMale',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-TemporaryEmployeeMale',
                                    fieldLabel: lang('Male Temporary Employee'),
                                    labelAlign: 'top',
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-TemporaryEmployeeFemale',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-TemporaryEmployeeFemale',
                                    fieldLabel: lang('Female Temporary Employee'),
                                    labelAlign: 'top',
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'panel',
                                    title: lang('Headquarters'),
                                    frame: false,
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-OtherInformationSection',
                                    style: 'margin-top:10px;',
                                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style: 'margin-right: 5px',
                                        items:[{
                                            xtype: 'image',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhoto',
                                            width: '200px',
                                            height:'200px',
                                            src: m_api_base_url + '/images/default_photo/business-logo.jpg'
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        layout:'vbox',
                                        style: 'margin-left: 5px',
                                        items:[{
                                            html:'<h3 style="width: 210px;height: 66px;font-family: OpenSans;font-size: 15px;font-weight: normal;font-stretch: normal;font-style: normal;line-height: normal;letter-spacing: normal;color: #2a2e32;">'+lang('Image file size no larger than 10MB.Supported formats: JPEG, JPG, PNG.Use a high quality image: 512x512px')+'</h3>'
                                        }]
                                    },{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhotoOld',
                                        name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhotoOld',
                                        inputType: 'hidden'
                                    }]
                                },{
                                    xtype: 'textarea',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HeadQuarterAddress',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HeadQuarterAddress',
                                    fieldLabel: lang('Headquarters Address'),
                                    labelAlign: 'top'
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
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang('Location Photo'),
                                            labelAlign: 'top',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhotoInput',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhotoInput',
                                            buttonText: 'Browse',
                                            listeners: {
                                                'change': function (fb, v) {
                                                    objPanelBasicData.submit({
                                                        url: m_api + '/mill/image_mill_location',
                                                        clientValidation: false,
                                                        params: {
                                                            opsiDisplay: thisObj.opsiDisplay,
                                                            MillID: Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillID').getValue()
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhoto').setSrc(o.result.file);
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhotoOld').setValue(o.result.filepath);
                                                        }
                                                    });
                                                }
                                            }
                                        }]
                                    }]
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Elevation',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Elevation',
                                    labelAlign: 'top',
                                    fieldLabel: lang('Elevation'),
                                    hidden: true
                                }]
                            },]
                        },
                        {
                            layout:'column',
                            border:false,
                            style:'margin-top:10px',
                            items:[{
                                columnWidth: 1,
                                border: false,
                                layout:'form',
                                items:[{
                                    title: 'Production',
                                    frame: false,
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-GeneralSections',
                                    style: 'margin-top:10px;',
                                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                },{
                                    html:'<div></div>'
                                }]
                            },{
                                columnWidth: 0.495,
                                layout:'form',
                                style: 'margin-left: 5px',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-ProductionCapacity',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-ProductionCapacity',
                                    fieldLabel: lang('Production Capacity (MT/Hr)'),
                                    labelAlign: 'top',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PlasmaFarmer',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PlasmaFarmer',
                                    fieldLabel: lang('Number of Plasma Farmers'),
                                    labelAlign: 'top'
                                }]
                            },{
                                columnWidth: 0.495,
                                layout:'form',
                                style: 'margin-left: 5px',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-WorkHour',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-WorkHour',
                                    fieldLabel: lang('Work Hour (per Hour)'),
                                    labelAlign:'top'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-EstimatedSmallholderFarmer',
                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-EstimatedSmallholderFarmer',
                                    fieldLabel: lang('Number of Estimated Smallholder Farmers'),
                                    labelAlign: 'top'
                                }]
                            }]
                        },{
                            html:'<div></div>'
                        },{
                            fieldLabel: lang('Oil Extraction Rate (OER)'),
                            xtype: 'radiogroup',
                            allowBlank: false,
                            msgTarget: 'side',
                            columns: 2,
                            items:[{
                                boxLabel: lang('Manual'),
                                name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HaveOer',
                                inputValue: '1',
                                id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HaveOer1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Auto'),
                                name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HaveOer',
                                inputValue: '2',
                                id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HaveOer2',
                                listeners:{
                                    change: function(){
                                        if(this.checked == true){
                                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HaveOer2').setDisabled(false);
                                        }else{
                                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-HaveOer').setDisabled(true);
                                        }
                                        return false;
                                    }
                                }
                            }]
                        }
                        ,{
                            xtype: 'form',
                            autoScroll: true,
                            // disabled:true,
                            id:'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-ProductGrid',
                            width:'100%',
                            padding:5,
                            style: 'border:2px solid #ADD2ED, margin-top:10px', 
                            items: [MainGridProducts]
                        },{
                            layout:'column',
                            border:false,
                            style:'margin-top:10px',
                            items:[{
                                columnWidth: 1,
                                border: false,
                                layout:'form',
                                items:[{
                                    title: lang('Address and Location'),
                                    frame: false,
                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Address-location',
                                    style: 'margin-top:10px;',
                                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                },{
                                    html:'<div></div>'
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style: 'margin-right: 5px',
                                        items:[{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Province',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Province',
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
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-District').setValue('');
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Subdistrict').setValue('');
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Village').setValue('');
                                                }
                                            }
                                        },{
                                            html:'<div></div>'
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-District',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-District',
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
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Subdistrict').setValue('');
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Village').setValue('');
                                                }
                                            }
                                        },{
                                            html:'<div></div>'
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Subdistrict',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Subdistrict',
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
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Village').setValue('');
                                                }
                                            }
                                        },{
                                            html:'<div></div>'
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Village',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Village',
                                            store: cmb_village,
                                            fieldLabel: lang('Village'),
                                            labelAlign:'top',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            allowBlank: false
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style: 'margin-left: 5px',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Phone',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Phone',
                                            fieldLabel: lang('Phone'),
                                            labelAlign:'top'
                                        },{
                                            html:'<div></div>'
                                        },{
                                            xtype: 'textarea',
                                            fieldLabel: lang('Address'),
                                            labelAlign:'top',
                                            id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Address',
                                            name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Address',
                                            height: 90
                                        },{
                                            html:'<div></div>'
                                        },{
                                            html:'<div></div>'
                                        },{
                                            html:'<div></div>'
                                        },{
                                            layout: 'column',
                                            border: false,
                                            items:[{
                                                columnWidth: 0.495,
                                                layout:'form',
                                                style: 'margin-right: 5px',
                                                items:[{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Latitude',
                                                        name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Latitude',
                                                        labelAlign: 'top',
                                                        fieldLabel: lang('Latitude')
                                                    }
                                                ]
                                            },{
                                                columnWidth: 0.495,
                                                layout:'form',
                                                style: 'margin-left: 5px',
                                                items:[{
                                                    xtype: 'textfield',
                                                    id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Longitude',
                                                    name: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Longitude',
                                                    labelAlign: 'top',
                                                    fieldLabel: lang('Longitude')
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            layout:'column',
                            border:false,
                            style:'margin-top:-20px',
                            items:[{
                                xtype: 'component',
                                autoEl: {
                                    html: '<div id="map" style="width:100%;height:250px;background:#e1e1e1;border:1px solid #e1e1e1;border-radius: 1%"></div>',
                                    style:'width:100%;'
                                }	
                            }] 
                        }]
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.Mill.FormMainMillProfile-FormBasicData-btnSaveForm',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    if (objPanelBasicData.isValid()) {
                        objPanelBasicData.submit({
                            url: m_api + '/mill/mill_profile_form',
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

                                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile'); //load current view
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

        //panel Mill Staff Data =======================================================================================// 
        var objPanelMillStaff = Ext.create('Koltiva.view.Mill.MillStaffPanel');
        thisObj.objPanelMillStaff = objPanelMillStaff;
        ObjPanelKanan.push(objPanelMillStaff);
        //

        //===================== PLANTATION STATUS =======================================================//
        ObjPanelPlantationStatus = Ext.create('Koltiva.view.PlotSurvey.PanelPlantationStatus', {
            viewVar: {
                MemberID: thisObj.viewVar.MillID,
                CallFrom: 'Mill'
            }
        });
        ObjPanelKanan.push(ObjPanelPlantationStatus);

        //===================== PLANTATION POLYGON =======================================================//
        ObjPanelPlotPolygon = Ext.create('Koltiva.view.PlotPolygon.PlotPolygonPanel', {
            viewVar: {
                MemberID: thisObj.viewVar.MillID,
                CallFrom: 'Mill'
            }
        });
        ObjPanelKanan.push(ObjPanelPlotPolygon);
        
        //===================== SP Code =======================================================//
        var ObjPanelSPCode = Ext.create('Koltiva.view.Mill.SPCodePanelProfile');
        var ObjPanelSPCode = Ext.create('Koltiva.view.Mill.SPCodePanelProfile', {
            viewVar: {
                MillID: thisObj.viewVar.MillID,
                CallFrom: 'Mill'
            }
        });
        ObjPanelKanan.push(ObjPanelSPCode);

        //isi layout utama ================================================================================================= (Begin)
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                html:'<h3 style="margin:0px;padding:0px;">'+lang('Mill Data')+'</h3>'
            },{
                id: 'Koltiva.view.Mill.FormMainMillProfile-labelInfoInsert',
                html:'',
            }]
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

            //Khusus WAGS
            if(m_partner == '14'){
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MillGroup').setVisible(false);
            }

            //insert
            if(thisObj.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-labelInfoInsert').update('<h5 style="margin:8px 0 0 15px;padding:0px;">('+lang('Add New Mill')+')</h5>');

                //form reset
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');

                //Trader Survey
                thisObj.objPanelMillStaff.collapse();
                thisObj.objPanelMillStaff.setViewVar({
                    MillID:null
                });

                //load store plot status
                var grid_mill_staff = Ext.data.StoreManager.lookup('Koltiva.store.Mill.GridMillStaff');
                grid_mill_staff.setStoreVar({MillID:null});
                grid_mill_staff.load();

                thisObj.ObjPanelSPCode.collapse();
                thisObj.ObjPanelSPCode.setViewVar({
                    MillID:null
                });
            }

            //update
            if(thisObj.opsiDisplay == 'update' || thisObj.opsiDisplay == 'view'){
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-labelInfoInsert').update('');

                //form reset
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');

                //load data form
                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData').getForm().load({
                    url: m_api + '/mill/mill_basic_data_form_profile',
                    method: 'GET',
                    params: {
                        PartnerID: this.viewVar.PartnerID,
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        console.log(r);
                        //untuk handle combo bertingkat
                        var cmb_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
                        var cmb_district = Ext.data.StoreManager.lookup('store.Grower.CmbDistrict');
                        var cmb_subdistrict = Ext.data.StoreManager.lookup('store.Grower.CmbSubdistrict');
                        var cmb_village = Ext.data.StoreManager.lookup('store.Grower.CmbVillage');
                        var grid_sp_code_panel = Ext.data.StoreManager.lookup('store.Mill.GridSPCodePanel');

                        cmb_province.load({
                            callback: function(records, operation, success){
                                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Province').setValue(r.data.Province);
                                if (success == true) {
                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.Province
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-District').setValue(r.data.District);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.District
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Subdistrict').setValue(r.data.Subdistrict);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.Subdistrict
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Village').setValue(r.data.Village);
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

                        //set photo
                        Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-PhotoOld').setValue(r.data.PhotoSrcPath);
                        if(r.data.PhotoSrc != ""){
                            var fotoUser = r.data.PhotoSrc;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Photo').setSrc(fotoUser+'?'+angkaRand);
                                } else {
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                                }
                            });
                        }

                        //set LocationPhoto
                        Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhotoOld').setValue(r.data.LocationPhotoPath);
                        if(r.data.LocationPhoto != ""){
                            var fotoUserLocation = r.data.LocationPhoto;
                            var angkaRandLocation = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUserLocation, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhoto').setSrc(fotoUserLocation+'?'+angkaRandLocation);
                                } else {
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                                }
                            });
                        }

                        if(Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-SupplychainID').getValue() != '') {
                            var grid_product = Ext.data.StoreManager.lookup('Koltiva.store.Mill.GridProductProfile');
                            grid_product.setStoreVar({SupplychainProductID:thisObj.viewVar.SupplychainProductID});
                            grid_product.load();    
                        } 

                        //Trader Survey
                        thisObj.objPanelMillStaff.expand();
                        thisObj.objPanelMillStaff.setViewVar({
                            MillID:r.data.MillID
                        });

                        //load store
                        var grid_mill_staff = Ext.data.StoreManager.lookup('Koltiva.store.Mill.GridMillStaff');
                        grid_mill_staff.setStoreVar({MillID:r.data.MillID});
                        grid_mill_staff.load();

                        grid_sp_code_panel.load({
                            params: {
                                MillID: r.data.MillID,
                                CallFrom: 'Mill'
                            },
                        })

                        if(thisObj.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-MemberPhotoInput').setVisible(false);
                            Ext.getCmp('Koltiva.view.Mill.FormMainMillProfile-FormBasicData-LocationPhotoInput').setVisible(false);
                        }

                        init_map();//gmaps3 
                        Ext.MessageBox.hide();
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