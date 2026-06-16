/*
* @Author: nikolius
* @Date:   2017-11-08 17:46:33
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:01
*/

/*
    Param2 yg diperlukan ketika load View ini
    - opsiDisplay
    - CooperativesID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)


// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)


function init_map() {
    var lat     = Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Latitude').getValue();
    var longs   = Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Longitude').getValue();

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
                latLng: [lat, longs]
            }
        });
    }
}

Ext.define('Koltiva.view.Cooperatives.FormMainCooperatives' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Cooperatives.FormMainCooperatives',
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

        //store yg dipakai (begin)
        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();
        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');
        var cmb_cooperatives_members = Ext.create('Koltiva.store.ComboGeneral.CmbCooperativesMember');

        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        var cmb_sme = Ext.create('Koltiva.store.ComboGeneral.CmbSMEDealer');
        //store yg dipakai (end)

        var cmb_legalstatus = Ext.create('Ext.data.Store',{
            fields: ['id', 'label'],
                data: [{
                    "id": "1",
                    "label": lang('Yes')
                },{
                    "id": "2",
                    "label": lang('No')
                }
            ]
        });

        var GroupExtID = Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-GroupExtID');  
        GroupExtID = 'External Group ID';  

        // var RowWagsGroupCat = Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-RowWagsGroupCat');
        // RowWagsGroupCat = 'Group Category';

        //Panel Basic Data ===================================== (Begin)
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Basic Data'),
            frame: true,
            id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData',
            fileUpload: true,
            margin:'0 0 20 0',
            items: [{
                layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.495,
                            layout:'form',
                            style:'padding-right:15px;',
                            items:[{
                                xtype: 'panel',
                                title: lang('Cooperative Information'),
                                frame: false,
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-GeneralSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopID',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopID',
                                fieldLabel: lang('Cooperatives ID'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopCode',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopCode',
                                fieldLabel: lang('External Cooperatives ID'),
                                labelAlign:'top'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopName',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CoopName',
                                fieldLabel: lang('Cooperatives Name'),
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-DateCollection',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-DateCollection',
                                fieldLabel: lang('Date Collection'),
                                //labelWidth: 150,
                                labelAlign:'top',
                                style: 'margin-bottom:15px;',
                                allowBlank: false,
                                format: 'Y-m-d H:i:s'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-LegalStatus',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-LegalStatus',
                                store: cmb_legalstatus,
                                fieldLabel: lang('Legal Status of Cooperatives'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-YearEstablished',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-YearEstablished',
                                store: cmb_year_option,
                                fieldLabel: lang('Year Established'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'panel',
                                title: lang('Location'),
                                frame: false,
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-LocationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Province',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Province',
                                store: cmb_province,
                                fieldLabel: lang('Province'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        cmb_district.load({
                                            params: {
                                                ProvinceID: nv
                                            }
                                        });
                                        cmb_sme.load({
                                            params: {
                                                ProvinceID: nv
                                            }
                                        });
                                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-District').setValue('');
                                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Subdistrict').setValue('');
                                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-VillageID').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-District',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-District',
                                store: cmb_district,
                                fieldLabel: lang('District'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        cmb_subdistrict.load({
                                            params: {
                                                DistrictID: nv
                                            }
                                        });
                                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Subdistrict').setValue('');
                                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-VillageID').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Subdistrict',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Subdistrict',
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
                                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-VillageID').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-VillageID',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-VillageID',
                                store: cmb_village,
                                fieldLabel: lang('Village'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-ZipCode',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-ZipCode',
                                labelAlign: 'top',
                                fieldLabel: lang('Zip code')
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textarea',
                                fieldLabel: lang('Address'),
                                labelAlign:'top',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Address',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Address',
                                height: 90
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
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Latitude',
                                        name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Latitude',
                                        labelAlign: 'top',
                                        fieldLabel: lang('Latitude')
                                    }]
                                },{
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-left: 5px',
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Longitude',
                                        name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Longitude',
                                        labelAlign: 'top',
                                        fieldLabel: lang('Longitude')
                                    }]
                                }]
                            },{
                                xtype: 'component',
                                autoEl: {
                                    html: '<div id="map" style="width:100%;height:250px;background:#e1e1e1;border:1px solid #e1e1e1;"></div>',
                                    style: 'width:100%;'
                                }
                            }]
                        },{
                            columnWidth: 0.5,
                            margin:'0 10 0 0',
                            style:'padding-left:15px;',
                            layout:'form',
                            items:[{
                                xtype: 'panel',
                                title: lang('Communucation and Media'),
                                frame: false,
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CommunicationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Website',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Website',
                                labelAlign: 'top',
                                fieldLabel: lang('Website')
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Linked',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Linked',
                                labelAlign: 'top',
                                fieldLabel: lang('Linked')
                            },
                            {
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Phone',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Phone',
                                fieldLabel: lang('Phone'),
                                labelAlign:'top'
                            },
                            {
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Fax',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Fax',
                                fieldLabel: lang('Fax'),
                                labelAlign:'top'
                            },
                            {
                                xtype: 'textfield',
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Email',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Email',
                                fieldLabel: lang('Email'),
                                labelAlign:'top'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'panel',
                                title: lang('Certification'),
                                frame: false,
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-CertificationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                fieldLabel: lang('Is this Cooperative independently certified'),
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                allowBlank: true,
                                msgTarget: 'side',
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Certificate',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Certificate1',
                                    style: 'margin-top:-10px;',
                                    listeners:{
                                        change: function(){
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-IndicateNumber').setVisible(true);
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolPurchased').setVisible(true);
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolProcess').setVisible(true);
                                            }else{
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-IndicateNumber').setVisible(false);
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolPurchased').setVisible(false);
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolProcess').setVisible(false);
                                            }
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Certificate',
                                    inputValue: '2',
                                    id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Certificate2',
                                    style: 'margin-top:-10px;',
                                    listeners:{
                                        change: function(){
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-IndicateNumber').setVisible(false);
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolPurchased').setVisible(false);
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolProcess').setVisible(false);
                                            }else{
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-IndicateNumber').setVisible(true);
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolPurchased').setVisible(true);
                                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolProcess').setVisible(true);
                                            }
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'numberfield',
                                hidden:true,
                                labelAlign:'top',
                                fieldLabel:lang('If independently certified, indicate ID Number'),
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-IndicateNumber',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-IndicateNumber'
                            },{
                                xtype: 'numberfield',
                                hidden:true,
                                labelAlign:'top',
                                fieldLabel:lang('Estimated certified volumes purchased for CH (Kg)'),
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolPurchased',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolPurchased'
                            },{
                                xtype: 'numberfield',
                                hidden:true,
                                labelAlign:'top',
                                fieldLabel:lang('Estimated certified volumes processed for CH (Kg)'),
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolProcess',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EstCertVolProcess'
                            },{
                                xtype: 'panel',
                                title: lang('Employees'),
                                frame: false,
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-EmployeesSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'numberfield',
                                labelAlign:'top',
                                fieldLabel:lang('Number of permanent workers'),
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-PermanentWorkers',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-PermanentWorkers'
                            },{
                                xtype: 'numberfield',
                                labelAlign:'top',
                                fieldLabel:lang('Number of temporary workers'),
                                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-TemporaryWorkers',
                                name: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-TemporaryWorkers'
                            }]
                        }]
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-btnSaveForm',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    if (objPanelBasicData.isValid()) {
                        objPanelBasicData.submit({
                            url: m_api + '/cooperatives/coop',
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

                                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives').destroy(); //destory current view
                                var FormMainCooperatives = [];

                                //create object View
                                if(Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives') == undefined){
                                    FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            CoopID: o.result.CoopID
                                        }
                                    });
                                }else{
                                    //destroy, create ulang
                                    Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives').destroy();
                                    FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            CoopID: o.result.CoopID
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
        //Panel Basic Data ===================================== (End)

        //Panel Cooperatives Member ===================================== (Begin)
        var objPanelCooperativesMember = Ext.create('Koltiva.view.Cooperatives.CooperativesMemberPanel');
        thisObj.objPanelCooperativesMember = objPanelCooperativesMember;
        //Panel Cooperatives Member ===================================== (End)

        //Panel Cooperatives Farmer Group ===================================== (Begin)
        var objPanelCooperativesFarmerGroup = Ext.create('Koltiva.view.Cooperatives.CooperativesFarmerGroupPanel');
        thisObj.objPanelCooperativesFarmerGroup = objPanelCooperativesFarmerGroup;
        //Panel Cooperatives Farmer Group ===================================== (End)

        //isi layout utama ================================================================================================= (Begin)
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                html:'<h3 style="margin:0px;padding:0px;">'+lang('Cooperatives Data')+'</h3>'
            },{
                id: 'Koltiva.view.Cooperatives.FormMainCooperatives-labelInfoInsert',
                html:'',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to Cooperatives List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives').destroy(); //destory current view
                        var GridMainCooperatives = [];

                        if(Ext.getCmp('Koltiva.view.Cooperatives.GridMain') == undefined){
                            GridMainCooperatives = Ext.create('Koltiva.view.Cooperatives.GridMain');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Cooperatives.GridMain').destroy();
                            GridMainCooperatives = Ext.create('Koltiva.view.Cooperatives.GridMain');
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
                items:[
                    thisObj.objPanelCooperativesMember,
                    thisObj.objPanelCooperativesFarmerGroup
                ]
            }]
        }];
        //isi layout utama ================================================================================================= (Begin)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'none';
            // Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-RowWagsGroupCat').setVisible(false);
            Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-ZipCode').setVisible(false);

            //insert
            if(thisObj.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-labelInfoInsert').update('<h5 style="margin:8px 0 0 15px;padding:0px;">('+lang('Add New Cooperatives')+')</h5>');

                // Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Chairman').setDisabled(true);
                // Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Secretary').setDisabled(true);
                // Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Treasurer').setDisabled(true);
                // Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-HaveManagement1').setDisabled(true);
                // Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-HaveManagement0').setDisabled(true);
                // Ext.get('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-HaveManagementLabel').setStyle('opacity',0.3);

                thisObj.objPanelCooperativesMember.setVisible(false);
                thisObj.objPanelCooperativesFarmerGroup.setVisible(false);
            }

            //view || update
            if(thisObj.opsiDisplay == 'view' || thisObj.opsiDisplay == 'update'){
                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-labelInfoInsert').update('');

                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Province').setReadOnly(true);
                Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-District').setReadOnly(true);

                if(thisObj.opsiDisplay == 'view'){
                    Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-btnSaveForm').setVisible(false);
                }

                //load combo Cooperatives dl
                var cmb_cooperatives_members = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbCooperativesMember');
                cmb_cooperatives_members.setStoreVar({CoopID:thisObj.viewVar.CoopID});
                cmb_cooperatives_members.load({
                    callback: function(records, operation, success){
                        if (success == true) {
                            //load data form
                            Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData').getForm().load({
                                url: m_api + '/cooperatives/coop',
                                method: 'GET',
                                params: {
                                    CoopID: thisObj.viewVar.CoopID
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
                                            Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Province').setValue(r.data.ProvinceID);
                                            if (success == true) {
                                                cmb_district.load({
                                                    params: {
                                                        ProvinceID: r.data.ProvinceID
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-District').setValue(r.data.DistrictID);
                                                            cmb_subdistrict.load({
                                                                params: {
                                                                    DistrictID: r.data.DistrictID
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-Subdistrict').setValue(r.data.SubDistrictID);
                                                                        cmb_village.load({
                                                                            params: {
                                                                                SubdistrictID: r.data.SubDistrictID
                                                                            },
                                                                            callback: function(records, operation, success){
                                                                                if (success == true) {
                                                                                    Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives-FormBasicData-VillageID').setValue(r.data.VillageID);
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

                                    //panel Cooperatives Member
                                    thisObj.objPanelCooperativesMember.setViewVar({
                                        CoopID:thisObj.viewVar.CoopID
                                    });
                                    thisObj.objPanelCooperativesFarmerGroup.setViewVar({
                                        CoopID:thisObj.viewVar.CoopID
                                    });

                                    var grid_farmer_group_member = Ext.data.StoreManager.lookup('Koltiva.store.Cooperatives.CooperativesMemberPanelGrid');
                                    grid_farmer_group_member.setStoreVar({CoopID:thisObj.viewVar.CoopID});
                                    grid_farmer_group_member.load();
                                    
                                    var grid_farmer_group_new = Ext.data.StoreManager.lookup('Koltiva.store.Cooperatives.CooperativesFarmerGroupPanelGrid');
                                    grid_farmer_group_new.setStoreVar({CoopID:thisObj.viewVar.CoopID});
                                    grid_farmer_group_new.load();

                                    init_map();
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
                });
            }
        }
    }
});