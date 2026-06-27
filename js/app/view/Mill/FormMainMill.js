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
    var lat = Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Latitude').getValue();
    var longs = Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Longitude').getValue();
	 
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

Ext.define('Koltiva.view.Mill.FormMainMill' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.FormMainMill',
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
        var MainGridProducts = Ext.create('Koltiva.view.Mill.GridProduct');
        //store yg dipakai (end)

        //label if user Wags
        if(m_partner == 14)
        {
            var MillID = Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-MillDisplayID');     
            MillID = 'GFW Mill ID';

        } else {
            var MillID = Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-MillDisplayID');     
            MillID = 'Mill ID';
        }

        //Panel Basic Data ================================================================================================= (Begin)
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Mill Data'),
            frame: true,
            id: 'Koltiva.view.Mill.FormMainMill-FormBasicData',
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
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-GeneralSection',
                                    style: 'margin-top:10px;',
                                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillID',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillID',
                                    inputType: 'hidden'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID',
                                    inputType: 'hidden'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainProductID',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainProductID',
                                    inputType: 'hidden'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillDisplayID',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillDisplayID',
                                    fieldLabel: MillID,
                                    readOnly:true,
                                    labelAlign:'top'
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillName',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillName',
                                    fieldLabel: lang('Mill Name'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-CompanyName',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-CompanyName',
                                    fieldLabel: lang('Company Name'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    html:'<div></div>',
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Alias',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Alias',
                                    fieldLabel: lang('Alias'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillGroup',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillGroup',
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
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Year',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Year',
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
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Status',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Status',
                                    store: cmb_legalstatus,
                                    fieldLabel: lang('Legal Status of the Company'),
                                    labelAlign:'top',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id'
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numberfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-PlasmaFarmer',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-PlasmaFarmer',
                                    fieldLabel: lang('Number of Plasma farmers'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numberfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-EstimatedSmallholderFarmer',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-EstimatedSmallholderFarmer',
                                    fieldLabel: lang('Number of farmers'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    xtype: 'numberfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Capacity',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Capacity',
                                    fieldLabel: lang('Production Capacity'),
                                    allowBlank: false,
                                    labelAlign:'top'
                                },{
                                    layout:'column',
                                    border:false,
                                    style:'margin-top:20px',
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
                                margin:'0 10 0 0',
                                style:'margin-left:15px;',
                                layout:'form',
                                items:[{
                                    xtype: 'panel',
                                    title: lang('Mill Logo'),
                                    frame: false,
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-ComunicationSection',
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
                                            id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Photo',
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
                                    }]
                                },{
                                    xtype: 'fileuploadfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MemberPhotoInput',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MemberPhotoInput',
                                    buttonText: 'Browse',
                                    listeners: {
                                        'change': function (fb, v) {
                                            objPanelBasicData.submit({
                                                url: m_api + '/mill/image_mill',
                                                clientValidation: false,
                                                params: {
                                                    opsiDisplay: thisObj.opsiDisplay,
                                                    MillID: Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-MillID').getValue()
                                                },
                                                waitMsg: 'Sending Photo...',
                                                success: function (fp, o) {
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Photo').setSrc(o.result.file);
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-PhotoOld').setValue(o.result.filepath);
                                                }
                                            });
                                        }
                                    }
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-PhotoOld',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-PhotoOld',
                                    inputType: 'hidden'
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-PermanentEmployeeMale',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-PermanentEmployeeMale',
                                    fieldLabel: lang('Male Permanent Employee'),
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
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-OtherInformationSection',
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
                                            id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhoto',
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
                                    }]
                                },{
                                    xtype: 'fileuploadfield',
                                    // fieldLabel: lang('Location Photo'),
                                    labelAlign: 'top',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhotoInput',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhotoInput',
                                    buttonText: 'Browse',
                                    listeners: {
                                        'change': function (fb, v) {
                                            objPanelBasicData.submit({
                                                url: m_api + '/mill/image_mill_location',
                                                clientValidation: false,
                                                params: {
                                                    opsiDisplay: thisObj.opsiDisplay,
                                                    MillID: Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-MillID').getValue()
                                                },
                                                waitMsg: 'Sending Photo...',
                                                success: function (fp, o) {
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhoto').setSrc(o.result.file);
                                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhotoOld').setValue(o.result.filepath);
                                                }
                                            });
                                        }
                                    }
                                },{
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhotoOld',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhotoOld',
                                    inputType: 'hidden'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-PermanentEmployeeFemale',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-PermanentEmployeeFemale',
                                    fieldLabel: lang('Female Permanent Employee'),
                                    labelAlign: 'top',
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-TemporaryEmployeeMale',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-TemporaryEmployeeMale',
                                    fieldLabel: lang('Male Temporary Employee'),
                                    labelAlign: 'top',
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'numericfield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-TemporaryEmployeeFemale',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-TemporaryEmployeeFemale',
                                    fieldLabel: lang('Female Temporary Employee'),
                                    labelAlign: 'top',
                                    allowNegative: false,
                                    minValue: 0,
                                    hidden: true
                                },{
                                    html:'<div></div>'
                                },{
                                    xtype: 'textarea',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-HeadQuarterAddress',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-HeadQuarterAddress',
                                    fieldLabel: lang('Headquarters Address'),
                                    labelAlign: 'top'
                                },{
                                    html:'<div></div>'
                                }]
                            }]
                        }]
                    },{
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
                                id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-GeneralSections',
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
                                id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-ProductionCapacity',
                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-ProductionCapacity',
                                fieldLabel: lang('Production Capacity (MT/Hour)'),
                                labelAlign:'top'
                            }]
                        },{
                            columnWidth: 0.495,
                            layout:'form',
                            style: 'margin-left: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-WorkHour',
                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-WorkHour',
                                fieldLabel: lang('Work Hour (per Hour)'),
                                labelAlign:'top'
                            }]
                        },{
                            html:'<div></div>'
                        }]
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'form',
                        autoScroll: true,
                        // disabled:true,
                        id:'Koltiva.view.Mill.FormMainMill-FormBasicData-ProductGrid',
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
                                id: 'Koltiva.view.Mill.MainForm-FormBasicData-GeneralSection',
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
                                        id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Province',
                                        name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Province',
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
                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-District').setValue('');
                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Subdistrict').setValue('');
                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-District',
                                        name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-District',
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
                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Subdistrict').setValue('');
                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Subdistrict',
                                        name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Subdistrict',
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
                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Village',
                                        name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Village',
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
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-left: 5px',
                                    items:[{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Phone',
                                        name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Phone',
                                        fieldLabel: lang('Phone'),
                                        labelAlign:'top'
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'textarea',
                                        fieldLabel: lang('Address'),
                                        labelAlign:'top',
                                        id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Address',
                                        name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Address',
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
                                                id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Latitude',
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Latitude',
                                                labelAlign: 'top',
                                                fieldLabel: lang('Latitude')
                                            }]
                                        },{
                                            columnWidth: 0.495,
                                            layout:'form',
                                            style: 'margin-left: 5px',
                                            items:[{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Longitude',
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-Longitude',
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
                        style:'margin-top:10px',
                        items:[{
                            xtype: 'component',
                            autoEl: {
                                html: '<div id="map" style="width:100%;height:250px;background:#e1e1e1;border:1px solid #e1e1e1;border-radius: 1%"></div>',
                                style:'width:100%;'
                            }	
                        }] 
                    }]
                },{
                    xtype: 'panel',
                    title: lang('Additional Data'),
                    padding: '0 10 10 10',
                    items:[{
                        columnWidth: 1,
                        layout:'form',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 0.9,
                                layout:'form',
                                style:'padding-right:25px;',
                                items:[{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;',
                                        items: [{
                                            fieldLabel: lang('Participate in Socialization'),
                                            labelAlign:'top',
                                            xtype: 'radiogroup',
                                            msgTarget: 'side',
                                            columns: 2,
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatus',
                                                inputValue: '1',
                                                id: 'Koltiva.view.Mill.FormMainMill-SocializationStatusYes',
                                                style: 'margin-top:-10px;',
                                                listeners:{
                                                    change: function(){
                                                        if (this.checked) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatusDate').setDisabled(false);
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatusDate').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatus',
                                                inputValue: '2',
                                                id: 'Koltiva.view.Mill.FormMainMill-SocializationStatusNo',
                                                style: 'margin-top:-10px;',
                                                listeners:{
                                                    change: function(){
                                                        if (this.checked) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatusDate').setDisabled(true);
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatusDate').setDisabled(false);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;padding-top:15px;',
                                        items:[{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatusDate',
                                            name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SocializationStatusDate',
                                            labelAlign:'top',
                                            format: 'Y-m-d',
                                            disabled: true,
                                        }]
                                    }]
                                },{
                                    html:'<div></div>',
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;',
                                        items: [{
                                            fieldLabel: lang('NDA Sent'),
                                            labelAlign:'top',
                                            xtype: 'radiogroup',
                                            msgTarget: 'side',
                                            columns: 2,
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDASent',
                                                inputValue: '1',
                                                id: 'Koltiva.view.Mill.FormMainMill-NDASentYes',
                                                style: 'margin-top:-10px;',
                                                listeners:{
                                                    change: function(){
                                                        if (this.checked) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDASentDate').setDisabled(false);
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDASentDate').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDASent',
                                                inputValue: '2',
                                                id: 'Koltiva.view.Mill.FormMainMill-NDASentNo',
                                                style: 'margin-top:-10px;',
                                                listeners:{
                                                    change: function(){
                                                        if (this.checked) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDASentDate').setDisabled(true);
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDASentDate').setDisabled(false);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;padding-top:15px;',
                                        items:[{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDASentDate',
                                            name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDASentDate',
                                            labelAlign:'top',
                                            format: 'Y-m-d',
                                            disabled: true,
                                        }]
                                    }]
                                },{
                                    html:'<div></div>',
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;',
                                        items: [{
                                            fieldLabel: lang('NDA Agreed'),
                                            labelAlign:'top',
                                            xtype: 'radiogroup',
                                            msgTarget: 'side',
                                            columns: 2,
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgree',
                                                inputValue: '1',
                                                id: 'Koltiva.view.Mill.FormMainMill-NDAAgreeYes',
                                                style: 'margin-top:-10px;',
                                                listeners:{
                                                    change: function(){
                                                        if (this.checked) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgreeDate').setDisabled(false);
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgreeDate').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgree',
                                                inputValue: '2',
                                                id: 'Koltiva.view.Mill.FormMainMill-NDAAgreeNo',
                                                style: 'margin-top:-10px;',
                                                listeners:{
                                                    change: function(){
                                                        if (this.checked) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgreeDate').setDisabled(true);
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgreeDate').setDisabled(false);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;padding-top:15px;',
                                        items:[{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgreeDate',
                                            name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDAAgreeDate',
                                            labelAlign:'top',
                                            format: 'Y-m-d',
                                            disabled: true,
                                        }]
                                    }]
                                },{
                                    html:'<div></div>',
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;',
                                        items: [{
                                            fieldLabel: lang('NDA Signed'),
                                            labelAlign:'top',
                                            xtype: 'radiogroup',
                                            msgTarget: 'side',
                                            columns: 2,
                                            items:[{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDASigned',
                                                inputValue: '1',
                                                id: 'Koltiva.view.Mill.FormMainMill-NDASignedYes',
                                                style: 'margin-top:-10px;',
                                                listeners:{
                                                    change: function(){
                                                        if (this.checked) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDASignedDate').setDisabled(false);
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDASignedDate').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDASigned',
                                                inputValue: '2',
                                                id: 'Koltiva.view.Mill.FormMainMill-NDASignedNo',
                                                style: 'margin-top:-10px;',
                                                listeners:{
                                                    change: function(){
                                                        if (this.checked) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDASignedDate').setDisabled(true);
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-NDASignedDate').setDisabled(false);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;padding-top:15px;',
                                        items:[{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDASignedDate',
                                            name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-NDASignedDate',
                                            labelAlign:'top',
                                            format: 'Y-m-d',
                                            disabled: true,
                                        }]
                                    }]
                                },{
                                    html:'<div></div>',
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;',
                                        items: [{
                                                fieldLabel: lang('Participate in Program'),
                                                labelAlign:'top',
                                                xtype: 'radiogroup',
                                                msgTarget: 'side',
                                                columns: 2,
                                                items:[{
                                                    boxLabel: lang('Yes'),
                                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatus',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.Mill.FormMainMill-ParticipationStatusYes',
                                                    style: 'margin-top:-10px;',
                                                    listeners:{
                                                        change: function(){
                                                            if (this.checked) {
                                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatusDate').setDisabled(false);
                                                            } else {
                                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatusDate').setDisabled(true);
                                                            }
                                                        }
                                                    }
                                                },{
                                                    boxLabel: lang('No'),
                                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatus',
                                                    inputValue: '2',
                                                    id: 'Koltiva.view.Mill.FormMainMill-ParticipationStatusNo',
                                                    style: 'margin-top:-10px;',
                                                    listeners:{
                                                        change: function(){
                                                            if (this.checked) {
                                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatusDate').setDisabled(true);
                                                            } else {
                                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatusDate').setDisabled(false);
                                                            }
                                                        }
                                                    }
                                                }]
                                        }]
                                    },{
                                        columnWidth: 0.495,
                                        layout:'form',
                                        style:'padding-right:25px;padding-top:15px;',
                                        items:[{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatusDate',
                                            name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-ParticipationStatusDate',
                                            labelAlign:'top',
                                            format: 'Y-m-d',
                                            disabled: true,
                                        }]
                                    }]
                                },{
                                    html:'<div></div>',
                                }]
                            },{
                                columnWidth: 0.1,
                                margin:'0 10 0 0',
                                style:'padding-left:15px;border-left:1px dashed gray;',
                                layout:'form',
                                hidden: true,
                                items:[{
                                    xtype: 'datefield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-VisitDate',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-VisitDate',
                                    fieldLabel: lang('Date of Visit'),
                                    //labelWidth: 150,
                                    labelAlign:'top',
                                    style: 'margin-bottom:15px;',
                                    format: 'Y-m-d'
                                },{
                                    xtype: 'datefield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-RecruitDate',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-RecruitDate',
                                    fieldLabel: lang('Date of Recruit'),
                                    //labelWidth: 150,
                                    labelAlign:'top',
                                    style: 'margin-bottom:15px;',
                                    format: 'Y-m-d'
                                },{
                                    xtype: 'datefield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-TrainingDate',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-TrainingDate',
                                    fieldLabel: lang('Date of Training'),
                                    //labelWidth: 150,
                                    labelAlign:'top',
                                    style: 'margin-bottom:15px;',
                                    format: 'Y-m-d'
                                },{
                                    xtype: 'datefield',
                                    id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SurveyDate',
                                    name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-SurveyDate',
                                    fieldLabel: lang('Date of Survey'),
                                    //labelWidth: 150,
                                    labelAlign:'top',
                                    style: 'margin-bottom:15px;',
                                    format: 'Y-m-d'
                                }]
                            }]
                        }]
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.Mill.FormMainMill-FormBasicData-btnSaveForm',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    if (objPanelBasicData.isValid()) {
                        objPanelBasicData.submit({
                            url: m_api + '/mill/mill_form',
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

                                Ext.getCmp('Koltiva.view.Mill.FormMainMill').destroy(); //destory current view
                                //create object View untuk FormMainTrader
                                if(Ext.getCmp('Koltiva.view.Mill.FormMainMill') == undefined){
                                    var FormMainTrader = Ext.create('Koltiva.view.Mill.FormMainMill', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            MillID: o.result.MillID
                                        }
                                    });
                                }else{
                                    //destroy, create ulang
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill').destroy();
                                    var FormMainTrader = Ext.create('Koltiva.view.Mill.FormMainMill', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            MillID: o.result.MillID
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

        //panel Mill Staff Data ======================================================================================= 
        var objPanelMillStaff = Ext.create('Koltiva.view.Mill.MillStaffPanel');
        thisObj.objPanelMillStaff = objPanelMillStaff;
        ObjPanelKanan.push(objPanelMillStaff);

        // var ObjPanelSPCode = Ext.create('Koltiva.view.Mill.SPCodePanel');
        // thisObj.ObjPanelSPCode = ObjPanelSPCode;
        // ObjPanelKanan.push(ObjPanelSPCode);

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
        ObjPanelSPCode = Ext.create('Koltiva.view.Mill.SPCodePanel', {
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
                id: 'Koltiva.view.Mill.FormMainMill-labelInfoInsert',
                html:'',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Mill List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Mill.FormMainMill').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.Mill.GridMainMill') == undefined){
                            var GridMainTrader = Ext.create('Koltiva.view.Mill.GridMainMill');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Mill.GridMainMill').destroy();
                            var GridMainTrader = Ext.create('Koltiva.view.Mill.GridMainMill');
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
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-MillGroup').setVisible(false);
            }

            //insert
            if(thisObj.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-labelInfoInsert').update('<h5 style="margin:8px 0 0 15px;padding:0px;">('+lang('Add New Mill')+')</h5>');
                Ext.getCmp('Koltiva.view.Mill.GridProduct').setVisible(false);
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-GeneralSections').setVisible(false);
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-ProductionCapacity').setVisible(false);
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-WorkHour').setVisible(false);
            
                //form reset
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/mill-location.jpg');

                //Trader Survey
                thisObj.objPanelMillStaff.collapse();
                thisObj.objPanelMillStaff.setViewVar({
                    MillID:null
                });

                var grid_mill_staff = Ext.data.StoreManager.lookup('Koltiva.store.Mill.GridMillStaff');
                grid_mill_staff.setStoreVar({MillID:null});
                grid_mill_staff.load();

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
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-labelInfoInsert').update('');
                
                //form reset
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/mill-location.jpg');

                //load data form
                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData').getForm().load({
                    url: m_api + '/mill/mill_basic_data_form',
                    method: 'GET',
                    params: {
                        MillID: this.viewVar.MillID
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
                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Province').setValue(r.data.Province);
                                if (success == true) {
                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.Province
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-District').setValue(r.data.District);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.District
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Subdistrict').setValue(r.data.Subdistrict);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.Subdistrict
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Village').setValue(r.data.Village);
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
                        Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-MemberPhotoInput').setValue(r.data.PhotoSrcPath);
                        if(r.data.PhotoSrc != ""){
                            var fotoUser = r.data.PhotoSrc;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Photo').setSrc(fotoUser+'?'+angkaRand);
                                } else {
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                                }
                            });
                        }

                        //set LocationPhoto
                        Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhotoOld').setValue(r.data.LocationPhotoPath);
                        if(r.data.LocationPhoto != ""){
                            var fotoUserLocation = r.data.LocationPhoto;
                            var angkaRandLocation = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUserLocation, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhoto').setSrc(fotoUserLocation+'?'+angkaRandLocation);
                                } else {
                                    Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/mill-location.jpg');
                                }
                            });
                        }
                        
                        if(Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID').getValue() != '') {
                            var grid_product = Ext.data.StoreManager.lookup('Koltiva.store.Mill.GridProduct');
                            grid_product.setStoreVar({SupplychainProductID:thisObj.viewVar.SupplychainProductID});
                            grid_product.load();    
                        } 
                        
                        //Trader Survey
                        thisObj.objPanelMillStaff.expand();
                        thisObj.objPanelMillStaff.setViewVar({
                            MillID:thisObj.viewVar.MillID
                        });

                        //load store
                        var grid_mill_staff = Ext.data.StoreManager.lookup('Koltiva.store.Mill.GridMillStaff');
                        grid_mill_staff.setStoreVar({MillID:thisObj.viewVar.MillID});
                        grid_mill_staff.load();

                        if(thisObj.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-btnSaveForm').setVisible(false);
                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-MemberPhotoInput').setVisible(false);
                            Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-LocationPhotoInput').setVisible(false);
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