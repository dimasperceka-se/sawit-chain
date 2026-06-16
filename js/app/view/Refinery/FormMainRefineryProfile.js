/*
* @Author: muhammad hidayaturrohman
* @Date:   2020-12-03
*/
/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar (RefineryID)
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
    var lat = Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Latitude').getValue();
    var longs = Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Longitude').getValue();
	 
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

Ext.define('Koltiva.view.Refinery.FormMainRefineryProfile' ,{
extend: 'Ext.panel.Panel',
id: 'Koltiva.view.Refinery.FormMainRefineryProfile',
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
    var cmb_status_refinery = Ext.create('Koltiva.store.Refinery.CmbStatusRefinery');
    var cmb_refinery_group = Ext.create('Koltiva.store.Refinery.CmbRefineryGroup');
    var cmb_legalstatus = Ext.create('Koltiva.store.ComboGeneral.CmbLegalStatus');
    var MainGridProducts = Ext.create('Koltiva.view.Refinery.GridProductProfile');
    //store yg dipakai (end)

    //Panel Basic Data ================================================================================================= (Begin)
    var objPanelBasicData = Ext.create('Ext.form.Panel',{
        title: lang('Refinery Data'),
        frame: true,
        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData',
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
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-GeneralSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryID',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryID'
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-SupplychainID',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-SupplychainID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryDisplayID',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryDisplayID',
                                fieldLabel: lang('Refinery ID'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryName',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryName',
                                fieldLabel: lang('Refinery Name'),
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-CompanyName',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-CompanyName',
                                fieldLabel: lang('Company Name'),
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Alias',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Alias',
                                fieldLabel: lang('Alias'),
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Year',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Year',
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
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Status',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Status',
                                store: cmb_legalstatus,
                                fieldLabel: lang('Legal Status of the Company'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>'
                            }]
                        },{
                            columnWidth: 0.5,
                            margin:'0 10 0 0',
                            style:'margin-left:15px;',
                            layout:'form',
                            items:[{
                                xtype: 'panel',
                                title: lang('Refinery Logo'),
                                frame: false,
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-ComunicationSection',
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
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Photo',
                                        width: '200px',
                                        height:'200px',
                                        src: m_api_base_url + '/images/default_photo/business-logo.jpg'
                                    },{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-PhotoOld',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-PhotoOld',
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
                                        fieldLabel: lang('Refinery Logo'),
                                        labelAlign: 'top',
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-MemberPhotoInput',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-MemberPhotoInput',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                objPanelBasicData.submit({
                                                    url: m_api + '/mill/image_mill',
                                                    clientValidation: false,
                                                    params: {
                                                        opsiDisplay: thisObj.opsiDisplay,
                                                        RefineryID: Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryID').getValue()
                                                    },
                                                    waitMsg: 'Sending Photo...',
                                                    success: function (fp, o) {
                                                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Photo').setSrc(o.result.file);
                                                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-PhotoOld').setValue(o.result.filepath);
                                                    }
                                                });
                                            }
                                        }
                                    }]
                                }]
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'panel',
                                title: lang('Headquarters'),
                                frame: false,
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-OtherInformationSection',
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
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhoto',
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
                                    id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhotoOld',
                                    name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhotoOld',
                                    inputType: 'hidden'
                                }]
                            },{
                                xtype: 'textarea',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-HeadQuarterAddress',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-HeadQuarterAddress',
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
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhotoInput',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhotoInput',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                objPanelBasicData.submit({
                                                    url: m_api + '/mill/image_mill_location',
                                                    clientValidation: false,
                                                    params: {
                                                        opsiDisplay: thisObj.opsiDisplay,
                                                        RefineryID: Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-RefineryID').getValue()
                                                    },
                                                    waitMsg: 'Sending Photo...',
                                                    success: function (fp, o) {
                                                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhoto').setSrc(o.result.file);
                                                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhotoOld').setValue(o.result.filepath);
                                                    }
                                                });
                                            }
                                        }
                                    }]
                                }]
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Elevation',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Elevation',
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
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-GeneralSections',
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
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-ProductionCapacity',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-ProductionCapacity',
                                fieldLabel: lang('Production Capacity'),
                                labelAlign: 'top',
                            }]
                        },{
                            columnWidth: 0.495,
                            layout:'form',
                            style: 'margin-left: 5px',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-WorkHour',
                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-WorkHour',
                                fieldLabel: lang('Work Hour (per Hour)'),
                                labelAlign:'top'
                            }]
                        }]
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'form',
                        autoScroll: true,
                        // disabled:true,
                        id:'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-ProductGrid',
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
                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Address-location',
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
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Province',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Province',
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
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-District').setValue('');
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Subdistrict').setValue('');
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-District',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-District',
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
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Subdistrict').setValue('');
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Subdistrict',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Subdistrict',
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
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Village',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Village',
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
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Phone',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Phone',
                                        fieldLabel: lang('Phone'),
                                        labelAlign:'top'
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'textarea',
                                        fieldLabel: lang('Address'),
                                        labelAlign:'top',
                                        id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Address',
                                        name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Address',
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
                                                    id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Latitude',
                                                    name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Latitude',
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
                                                id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Longitude',
                                                name: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Longitude',
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
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            id: 'Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-btnSaveForm',
            cls: 's-blue',
            handler: function () {
                if (objPanelBasicData.isValid()) {
                    objPanelBasicData.submit({
                        url: m_api + '/refinery/refinery_profile_form',
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

                            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile').load();
                            
                            //create object View untuk FormMainTrader
                            if(Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile') == undefined){
                                var FormMainTrader = Ext.create('Koltiva.view.Refinery.FormMainRefineryProfile', {
                                    opsiDisplay: 'update',
                                    viewVar: {
                                        RefineryID: o.result.RefineryID
                                    }
                                });
                            }else{
                                //destroy, create ulang
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile').destroy();
                                var FormMainTrader = Ext.create('Koltiva.view.Refinery.FormMainRefineryProfile', {
                                    opsiDisplay: 'update',
                                    viewVar: {
                                        RefineryID: o.result.RefineryID
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

    //panel Refinery Staff Data ======================================================================================= 
    var objPanelRefineryStaff = Ext.create('Koltiva.view.Refinery.RefineryStaffPanel');
    thisObj.objPanelRefineryStaff = objPanelRefineryStaff;
    ObjPanelKanan.push(objPanelRefineryStaff);
    //

   //===================== SP Code =======================================================//
   ObjPanelSPCode = Ext.create('Koltiva.view.Refinery.SPCodePanelProfile');
   ObjPanelSPCode = Ext.create('Koltiva.view.Refinery.SPCodePanelProfile', {
       viewVar: {
           RefineryID: thisObj.viewVar.RefineryID,
           CallFrom: 'refinery'
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
            html:'<h3 style="margin:0px;padding:0px;">'+lang('Refinery Data')+'</h3>'
        },{
            id: 'Koltiva.view.Refinery.FormMainRefineryProfile-labelInfoInsert',
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

        //insert
        if(thisObj.opsiDisplay == 'insert'){
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-labelInfoInsert').update('<h5 style="margin:8px 0 0 15px;padding:0px;">('+lang('Add New Refinery')+')</h5>');

            //form reset
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData').getForm().reset();
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');

            //Trader Survey
            thisObj.objPanelRefineryStaff.collapse();
            thisObj.objPanelRefineryStaff.setViewVar({
                RefineryID:null
            });

            //load store plot status
            var grid_refinery_staff = Ext.data.StoreManager.lookup('Koltiva.store.Refinery.GridRefineryStaff');
            grid_refinery_staff.setStoreVar({RefineryID:null});
            grid_refinery_staff.load();

        }

        //update
        if(thisObj.opsiDisplay == 'update' || thisObj.opsiDisplay == 'view'){
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-labelInfoInsert').update('');

            //form reset
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData').getForm().reset();
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');

            //load data form
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData').getForm().load({
                url: m_api + '/refinery/refinery_basic_data_form_profile',
                method: 'GET',
                params: {
                    PartnerID: this.viewVar.PartnerID
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    //untuk handle combo bertingkat
                    var cmb_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
                    var cmb_district = Ext.data.StoreManager.lookup('store.Grower.CmbDistrict');
                    var cmb_subdistrict = Ext.data.StoreManager.lookup('store.Grower.CmbSubdistrict');
                    var cmb_village = Ext.data.StoreManager.lookup('store.Grower.CmbVillage');
                    var grid_sp_code_panel = Ext.data.StoreManager.lookup('store.Refinery.GridSpCodePanel');
                    cmb_province.load({
                        callback: function(records, operation, success){
                            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Province').setValue(r.data.Province);
                            if (success == true) {
                                cmb_district.load({
                                    params: {
                                        ProvinceID: r.data.Province
                                    },
                                    callback: function(records, operation, success){
                                        if (success == true) {
                                            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-District').setValue(r.data.District);
                                            cmb_subdistrict.load({
                                                params: {
                                                    DistrictID: r.data.District
                                                },
                                                callback: function(records, operation, success){
                                                    if (success == true) {
                                                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Subdistrict').setValue(r.data.Subdistrict);
                                                        cmb_village.load({
                                                            params: {
                                                                SubdistrictID: r.data.Subdistrict
                                                            },
                                                            callback: function(records, operation, success){
                                                                if (success == true) {
                                                                    Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Village').setValue(r.data.Village);
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
                    Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-PhotoOld').setValue(r.data.PhotoSrcPath);
                    if(r.data.PhotoSrc != ""){
                        var fotoUser = r.data.PhotoSrc;
                        var angkaRand = Math.floor((Math.random() * 100) + 1);
                        checkImageExists(fotoUser, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Photo').setSrc(fotoUser+'?'+angkaRand);
                            } else {
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                            }
                        });
                    }

                    //set LocationPhoto
                    Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhotoOld').setValue(r.data.LocationPhotoPath);
                    if(r.data.LocationPhoto != ""){
                        var fotoUserLocation = r.data.LocationPhoto;
                        var angkaRandLocation = Math.floor((Math.random() * 100) + 1);
                        checkImageExists(fotoUserLocation, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhoto').setSrc(fotoUserLocation+'?'+angkaRandLocation);
                            } else {
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                            }
                        });
                    }

                    //Trader Survey
                    thisObj.objPanelRefineryStaff.expand();
                    thisObj.objPanelRefineryStaff.setViewVar({
                        RefineryID:r.data.RefineryID
                    });

                    if(Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-SupplychainID').getValue() != '') {
                        var grid_product = Ext.data.StoreManager.lookup('Koltiva.store.Refinery.GridProductProfile');
                        grid_product.setStoreVar({SupplychainProductID:thisObj.viewVar.SupplychainProductID});
                        grid_product.load();    
                    } 

                    //load store
                    var grid_refinery_staff = Ext.data.StoreManager.lookup('Koltiva.store.Refinery.GridRefineryStaff');
                    grid_refinery_staff.setStoreVar({RefineryID:r.data.RefineryID});
                    grid_refinery_staff.load();

                    grid_sp_code_panel.load({
                        params: {
                            RefineryID: r.data.RefineryID,
                            CallFrom: 'refinery'
                        },
                    })

                    if(thisObj.opsiDisplay == 'view'){
                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-MemberPhotoInput').setVisible(false);
                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-LocationPhotoInput').setVisible(false);
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