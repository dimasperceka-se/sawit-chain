/*
* @Author: muhammad hidayaturrohman
* @Date:   2020-11-05
* @Last Modified by:   muhammad hidayaturrohman
* @Last Modified time: 2020-11-05
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
    var lat = Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Latitude').getValue();
    var longs = Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Longitude').getValue();
	 
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

Ext.define('Koltiva.view.Refinery.FormMainRefinery' ,{
extend: 'Ext.panel.Panel',
id: 'Koltiva.view.Refinery.FormMainRefinery',
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
    var MainGridProducts = Ext.create('Koltiva.view.Refinery.GridProduct');

    //Panel Basic Data ================================================================================================= (Begin)
    var objPanelBasicData = Ext.create('Ext.form.Panel',{
        title: lang('Refinery Data'),
        frame: true,
        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData',
        fileUpload: true,
        margin:'0 0 20 0',
        padding: '10 0 0 0',
        items: [{
            xtype: 'form',
            flex: 1,
            margin: 2,
            activeTab: 0,
            plain: true,
            cls:'tabSce',
            items:[{
                xtype: 'form',
                padding: '0 0 0 5',
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                       
                    },
                    {
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.495,
                            layout:'form',
                            style:'margin-right:10px; margin-top:-20px;',
                            items:[{
                                title: lang('Basic Data'),
                                frame: false,
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-GeneralSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryID',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-SupplychainID',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-SupplychainID',
                                inputType: 'hidden'
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-SupplychainProductID',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-SupplychainProductID',
                                inputType: 'hidden'
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryDisplayID',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryDisplayID',
                                fieldLabel: lang('Refinery ID'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryName',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryName',
                                fieldLabel: lang('Refinery Name'),
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-CompanyName',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-CompanyName',
                                fieldLabel: lang('Company Name'),
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Alias',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Alias',
                                fieldLabel: lang('Alias'),
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Year',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Year',
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
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Status',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Status',
                                store: cmb_legalstatus,
                                fieldLabel: lang('Legal Status of the Company'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>'
                            }
                        ]
                        },
                        {
                            columnWidth: 0.5,
                            // margin:'0 15 0 0',
                            style:'margin-top:-20px;',
                            layout:'form',
                            items:[{
                                title: lang('Refinery Logo'),
                                frame: false,
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-ComunicationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-right: 5px',
                                    items: [{
                                        xtype: 'image',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Photo',
                                        width: '200px',
                                        height:'200px',
                                        src: m_api_base_url + '/images/default_photo/business-logo.jpg'
                                    }]
                                },
                                {
                                    columnWidth: 0.495,
                                    layout:'vbox',
                                    style: 'margin-left: 2px',
                                    items:[{
                                        html:'<h3 style="width: 210px;height: 66px;font-family: OpenSans;font-size: 15px;font-weight: normal;font-stretch: normal;font-style: normal;line-height: normal;letter-spacing: normal;color: #2a2e32;">'+lang('Image file size no larger than 10MB.Supported formats: JPEG, JPG, PNG.Use a high quality image: 512x512px')+'</h3>'
                                    },]
                                }]
                            },{ 
                                xtype: 'fileuploadfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-MemberPhotoInput',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-MemberPhotoInput',
                                buttonText: 'Browse',
                                listeners: {
                                    'change': function (fb, v) {
                                        objPanelBasicData.submit({
                                            url: m_api + '/refinery/image_refinery',
                                            clientValidation: false,
                                            params: {
                                                opsiDisplay: thisObj.opsiDisplay,
                                                RefineryID: Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryID').getValue()
                                            },
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Photo').setSrc(o.result.file);
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-PhotoOld').setValue(o.result.filepath);
                                            }
                                        });
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                title: lang('Headquarters'),
                                frame: false,
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-OtherInformationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                layout: 'column',
                                border: false,
                                items:[{
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-right: 5px',
                                    items: [{
                                        xtype: 'image',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhoto',
                                        width: '200px',
                                        height:'200px',
                                        src: m_api_base_url + '/images/default_photo/business-logo.jpg'
                                    }]
                                },
                                {
                                    columnWidth: 0.495,
                                    layout:'vbox',
                                    style: 'margin-left: 2px',
                                    items:[{
                                        html:'<h3 style="width: 210px;height: 66px;font-family: OpenSans;font-size: 15px;font-weight: normal;font-stretch: normal;font-style: normal;line-height: normal;letter-spacing: normal;color: #2a2e32;">'+lang('Image file size no larger than 10MB.Supported formats: JPEG, JPG, PNG.Use a high quality image: 512x512px')+'</h3>'
                                    },]
                                }]
                            },{ 
                                xtype: 'fileuploadfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhotoInput',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhotoInput',
                                buttonText: 'Browse',
                                listeners: {
                                    'change': function (fb, v) {
                                        objPanelBasicData.submit({
                                            url: m_api + '/refinery/image_refinery_location',
                                            clientValidation: false,
                                            params: {
                                                opsiDisplay: thisObj.opsiDisplay,
                                                RefineryID: Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryID').getValue()
                                            },
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhoto').setSrc(o.result.file);
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhotoOld').setValue(o.result.filepath);
                                            }
                                        });
                                    }
                                }
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhotoOld',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhotoOld',
                                inputType: 'hidden'
                            },{
                                xtype: 'textarea',
                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-HeadQuarterAddress',
                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-HeadQuarterAddress',
                                fieldLabel: lang('Headquarters Address'),
                                labelAlign: 'top'
                            },{
                                html:'<div></div>'
                            },
                            ]
                        }],
                    },{
                        layout:'column',
                        border:false,
                        style:'margin-top:-20px',
                        items:[{
                            columnWidth: 1,
                            border: false,
                            layout:'form',
                            items:[{
                                title: 'Production',
                                frame: false,
                                id: 'Koltiva.view.Refinery.MainForm-FormBasicData-GeneralSections',
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
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-ProductionCapacity',
                                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-ProductionCapacity',
                                        fieldLabel: lang('Production Capacity (MT/Hour)'),
                                        labelAlign:'top'
                                    }, {
                                        html:'<div></div>'
                                    }]
                                },{
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-left: 5px',
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-WorkHour',
                                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-WorkHour',
                                        fieldLabel: lang('Work Hour (per Hour)'),
                                        labelAlign:'top'
                                    },{
                                        html:'<div></div>'
                                    }]
                                }]
                            }]
                        }]
                    },{
                        xtype: 'form',
                        autoScroll: true,
                        // disabled:true,
                        id:'Koltiva.view.Refinery.MainForm-FormBasicData-ProductGrid',
                        width:'100%',
                        padding:5,
                        style: 'border:2px solid #ADD2ED', 
                        items: [MainGridProducts]
                    },{
                        layout:'column',
                        border:false,
                        style:'margin-top:-20px',
                        items:[{
                            columnWidth: 1,
                            border: false,
                            layout:'form',
                            items:[{
                                title: lang('Address and Location'),
                                frame: false,
                                id: 'Koltiva.view.Refinery.MainForm-FormBasicData-GeneralSection',
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
                                    items: [{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Province',
                                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Province',
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
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-District').setValue('');
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Subdistrict').setValue('');
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-District',
                                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-District',
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
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Subdistrict').setValue('');
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Subdistrict',
                                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Subdistrict',
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
                                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Village').setValue('');
                                            }
                                        }
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Village',
                                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Village',
                                        store: cmb_village,
                                        fieldLabel: lang('Village'),
                                        labelAlign:'top',
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        allowBlank: false
                                    },{
                                        html:'<div></div>'
                                    }
                                ]},{
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-left: 5px',
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Phone',
                                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Phone',
                                        fieldLabel: lang('Phone'),
                                        labelAlign:'top'
                                    },{
                                        html:'<div></div>'
                                    },{
                                        xtype: 'textarea',
                                        fieldLabel: lang('Address'),
                                        labelAlign:'top',
                                        id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Address',
                                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Address',
                                        height: 125
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items:[{
                                            columnWidth: 0.495,
                                            layout:'form',
                                            style: 'margin-right: 5px',
                                            items: [{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Latitude',
                                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Latitude',
                                                labelAlign: 'top',
                                                fieldLabel: lang('Latitude')
                                            }]
                                        },{
                                            columnWidth: 0.495,
                                            layout:'form',
                                            style: 'margin-left: 5px',
                                            items: [{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Longitude',
                                                name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Longitude',
                                                labelAlign: 'top',
                                                fieldLabel: lang('Longitude')
                                            }]
                                        }]
                                    },
                                ]}]
                            }]
                            },
                        ]
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
                    }
                ]
            },]
        }],
        buttons: [
            {
                text: lang('Cancel'),
                id: 'Koltiva.view.Refinery.FormMainRefinery.FormBasicData-Form-ButtonReset',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                html: 'src="' + varjs.config.base_url + '"',
                disabled: false,
                listeners: {
                    click: {
                        element: 'el',
                        preventDefault: true,
                        fn: function(e, target){
                            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery').destroy(); //destory current view
                            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery') == undefined){
                                var GridMainTrader = Ext.create('Koltiva.view.Refinery.GridMainRefinery');
                            }
                        }
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                text: lang('Print Profile'),
                handler: function() {
                   
                }
            },{
                text: lang('Save'),
                id: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-btnSaveForm',
                cls: 'Sfr_BtnFormGreen',
                overCls: 'Sfr_BtnFormGreen-Hover',
                handler: function () {
                if (objPanelBasicData.isValid()) {
                    objPanelBasicData.submit({
                        url: m_api + '/refinery/refinery_form',
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

                            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery').destroy(); //destory current view
                            //create object View untuk FormMainTrader
                            if(Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery') == undefined){
                                var FormMainTrader = Ext.create('Koltiva.view.Refinery.FormMainRefinery', {
                                    opsiDisplay: 'update',
                                    viewVar: {
                                        RefineryID: o.result.RefineryID
                                    }
                                });
                            }else{
                                //destroy, create ulang
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery').destroy();
                                var FormMainTrader = Ext.create('Koltiva.view.Refinery.FormMainRefinery', {
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
    }]
    });
    //Panel Basic Data ================================================================================================= (End)

    //panel Refinery Staff Data ======================================================================================= 
    var objPanelRefineryStaff = Ext.create('Koltiva.view.Refinery.RefineryStaffPanel');
    thisObj.objPanelRefineryStaff = objPanelRefineryStaff;
    ObjPanelKanan.push(objPanelRefineryStaff);

    // var ObjPanelSPCode = Ext.create('Koltiva.view.Refinery.SPCodePanel');
    // thisObj.ObjPanelSPCode = ObjPanelSPCode;
    // ObjPanelKanan.push(ObjPanelSPCode);

    //===================== SP Code =======================================================//
    ObjPanelSPCode = Ext.create('Koltiva.view.Refinery.SPCodePanel', {
        viewVar: {
            RefineryID: thisObj.viewVar.RefineryID,
            CallFrom: 'Refinery'
        }
    });

    ObjPanelKanan.push(ObjPanelSPCode);

    //isi layout utama ================================================================================================= (Begin)
    thisObj.items = [{
        border:false,
        layout:{
            type:'hbox'
        },
        items:[{
            // html:'<h3 style="margin:0px;padding:0px;">'+lang('Refinery Data')+'</h3>'
        },{
            id: 'Koltiva.view.Refinery.FormMainRefinery-labelInfoInsert',
            html:'',
        }]
    },{
        html:'<br />'
    },{
        layout: 'column',
        border: false,
        items: [{
            //LEFT CONTENT
            columnWidth: 0.7,
            items:[
                objPanelBasicData
            ]
        },{
            //RIGHT CONTENT
            columnWidth: 0.3,
            items: ObjPanelKanan
        },
    ]
    }];
    //isi layout utama ================================================================================================= (End)

    this.callParent(arguments);
},
listeners: {
    afterRender: function(){
        var thisObj = this;

        //hilangkan view Filter region
        document.getElementById('divCommonContentRegion').style.display = 'none';
        
        //insert
        if(thisObj.opsiDisplay == 'insert'){

            Ext.getCmp('Koltiva.view.Refinery.GridProduct').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.MainForm-FormBasicData-GeneralSections').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-ProductionCapacity').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-WorkHour').setVisible(false);

            //form reset
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData').getForm().reset();
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/mill-location.jpg');

            //Trader Survey
            thisObj.objPanelRefineryStaff.collapse();
            thisObj.objPanelRefineryStaff.setViewVar({
                RefineryID:null
            });

            //load store plot status
            var grid_refinery_staff = Ext.data.StoreManager.lookup('Koltiva.store.Refinery.GridRefineryStaff');
            grid_refinery_staff.setStoreVar({RefineryID:null});
            grid_refinery_staff.load();

            // thisObj.ObjPanelSPCode.collapse();
            // thisObj.ObjPanelSPCode.setViewVar({
            //     MillID:null
            // });
        }

        //update
        if(thisObj.opsiDisplay == 'update' || thisObj.opsiDisplay == 'view'){
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-labelInfoInsert').update('');

            //form reset
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData').getForm().reset();
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/mill-location.jpg');

            //load data form
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData').getForm().load({
                url: m_api + '/refinery/refinery_basic_data_form',
                method: 'GET',
                params: {
                    RefineryID: this.viewVar.RefineryID
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
                            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Province').setValue(r.data.Province);
                            if (success == true) {
                                cmb_district.load({
                                    params: {
                                        ProvinceID: r.data.Province
                                    },
                                    callback: function(records, operation, success){
                                        if (success == true) {
                                            Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-District').setValue(r.data.District);
                                            cmb_subdistrict.load({
                                                params: {
                                                    DistrictID: r.data.District
                                                },
                                                callback: function(records, operation, success){
                                                    if (success == true) {
                                                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Subdistrict').setValue(r.data.Subdistrict);
                                                        cmb_village.load({
                                                            params: {
                                                                SubdistrictID: r.data.Subdistrict
                                                            },
                                                            callback: function(records, operation, success){
                                                                if (success == true) {
                                                                    Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Village').setValue(r.data.Village);
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
                    Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-MemberPhotoInput').setValue(r.data.PhotoSrcPath);
                    if(r.data.PhotoSrc != ""){
                        var fotoUser = r.data.PhotoSrc;
                        var angkaRand = Math.floor((Math.random() * 100) + 1);
                        checkImageExists(fotoUser, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Photo').setSrc(fotoUser+'?'+angkaRand);
                            } else {
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-Photo').setSrc(m_api_base_url + '/images/default_photo/business-logo.jpg');
                            }
                        });
                    }

                    //set LocationPhoto
                    Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhotoOld').setValue(r.data.LocationPhotoPath);
                    if(r.data.LocationPhoto != ""){
                        var fotoUserLocation = r.data.LocationPhoto;
                        var angkaRandLocation = Math.floor((Math.random() * 100) + 1);
                        checkImageExists(fotoUserLocation, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhoto').setSrc(fotoUserLocation+'?'+angkaRandLocation);
                            } else {
                                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhoto').setSrc(m_api_base_url + '/images/default_photo/mill-location.jpg');
                            }
                        });
                    }

                    if(Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-SupplychainID').getValue() != '') {
                        var grid_product = Ext.data.StoreManager.lookup('Koltiva.store.Refinery.GridProduct');
                        grid_product.setStoreVar({SupplychainProductID:thisObj.viewVar.SupplychainProductID});
                        grid_product.load();    
                    } 

                    thisObj.objPanelRefineryStaff.expand(
                    thisObj.objPanelRefineryStaff.setViewVar({
                        RefineryID:thisObj.viewVar.RefineryID
                    }));

                    //load store
                    var grid_refinery_staff = Ext.data.StoreManager.lookup('Koltiva.store.Refinery.GridRefineryStaff');
                    grid_refinery_staff.setStoreVar({RefineryID:thisObj.viewVar.RefineryID});
                    grid_refinery_staff.load();

                    if(thisObj.opsiDisplay == 'view'){
                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-btnSaveForm').setVisible(false);
                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-MemberPhotoInput').setVisible(false);
                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery-FormBasicData-LocationPhotoInput').setVisible(false);
                    }

                    init_map();//gmaps3 
                    Ext.MessageBox.hide();
                },
              
                failure: function(form, action) {
                    Ext.MessageBox.hide();
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