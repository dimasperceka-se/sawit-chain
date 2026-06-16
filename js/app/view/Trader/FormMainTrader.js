/*
* @Author: nikolius
* @Date:   2017-07-19 14:12:09
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-15 16:07:42
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar
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

Ext.define('Koltiva.view.Trader.FormMainTrader' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Trader.FormMainTrader',
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

        var cmb_education = Ext.create('Koltiva.store.Grower.CmbEducation');
        var cmb_legalstatus = Ext.create('Koltiva.store.ComboGeneral.CmbLegalStatus');
        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        //store yg dipakai (end)

        //panel Form Basic Data ======================================================================================= (Begin)
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Basic Data'),
            frame: true,
            id: 'Koltiva.view.Trader.FormMainTrader-FormBasicData',
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
                            style:'padding-right:25px;',
                            items:[{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.Trader.FormMainTrader-MemberID',
                                name: 'Koltiva.view.Trader.FormMainTrader-MemberID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Trader.FormMainTrader-MemberDisplayID',
                                name: 'Koltiva.view.Trader.FormMainTrader-MemberDisplayID',
                                fieldLabel: lang('SME ID'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.Trader.FormMainTrader-DateCollection',
                                name: 'Koltiva.view.Trader.FormMainTrader-DateCollection',
                                fieldLabel: lang('Date Collection'),
                                //labelWidth: 150,
                                labelAlign:'top',
                                style: 'margin-bottom:15px;',
                                allowBlank: false,
                                format: 'Y-m-d H:i:s'
                            },{
                                html:'<div style="height:20px;"></div><div class="subtitleForm">'+lang('Business Information')+'</div>'
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Trader.FormMainTrader-agCompanyName',
                                name: 'Koltiva.view.Trader.FormMainTrader-agCompanyName',
                                fieldLabel: lang('Company Name'),
                                //labelWidth: 150,
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Trader.FormMainTrader-agYearEstablished',
                                name: 'Koltiva.view.Trader.FormMainTrader-agYearEstablished',
                                store: cmb_year_option,
                                fieldLabel: lang('Year Established'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Trader.FormMainTrader-agLegalStatusCompany',
                                name: 'Koltiva.view.Trader.FormMainTrader-agLegalStatusCompany',
                                store: cmb_legalstatus,
                                fieldLabel: lang('Legal Status of Company'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                html: '<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Trader.FormMainTrader-Province',
                                name: 'Koltiva.view.Trader.FormMainTrader-Province',
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
                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-District').setValue('');
                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-Subdistrict').setValue('');
                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-Village').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Trader.FormMainTrader-District',
                                name: 'Koltiva.view.Trader.FormMainTrader-District',
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
                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-Subdistrict').setValue('');
                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-Village').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Trader.FormMainTrader-Subdistrict',
                                name: 'Koltiva.view.Trader.FormMainTrader-Subdistrict',
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
                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-Village').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Trader.FormMainTrader-Village',
                                name: 'Koltiva.view.Trader.FormMainTrader-Village',
                                store: cmb_village,
                                fieldLabel: lang('Village'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                allowBlank: false
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textarea',
                                fieldLabel: lang('Address'),
                                labelAlign:'top',
                                id: 'Koltiva.view.Trader.FormMainTrader-Address',
                                name: 'Koltiva.view.Trader.FormMainTrader-Address',
                                height: 65
                            },{
                                html:'<br /><div class="subtitleForm">'+lang('SME Role')+'</div>'
                            },{
                                xtype: 'fieldcontainer',
                                fieldLabel: lang('Role'),
                                labelWidth: 80,
                                layout: 'vbox',
                                defaultType: 'checkboxfield',
                                items: [{
                                    boxLabel  : lang('Trader'),
                                    name      : 'Koltiva.view.Trader.FormMainTrader-CbRoleTrader',
                                    id        : 'Koltiva.view.Trader.FormMainTrader-CbRoleTrader',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Village Collector'),
                                    name      : 'Koltiva.view.Trader.FormMainTrader-CbRoleVilCol',
                                    id        : 'Koltiva.view.Trader.FormMainTrader-CbRoleVilCol',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Dealer'),
                                    name      : 'Koltiva.view.Trader.FormMainTrader-CbRoleDealer',
                                    id        : 'Koltiva.view.Trader.FormMainTrader-CbRoleDealer',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Ramp'),
                                    name      : 'Koltiva.view.Trader.FormMainTrader-CbRoleRamp',
                                    id        : 'Koltiva.view.Trader.FormMainTrader-CbRoleRamp',
                                    inputValue: '1'
                                },{
                                    boxLabel  : lang('Delivery Order Holder'),
                                    name      : 'Koltiva.view.Trader.FormMainTrader-CbRoleDoHolder',
                                    id        : 'Koltiva.view.Trader.FormMainTrader-CbRoleDoHolder',
                                    inputValue: '1'
                                }]
                            }]
                        },{
                            columnWidth: 0.5,
                            margin:'0 10 0 0',
                            style:'padding-left:15px;border-left:1px dashed gray;',
                            layout:'form',
                            items:[{
                                xtype: 'label', margin:0, padding:0, padding:5, columns:2, cls: 'x-form-item-label', html:'<div class="companyLabel">'+lang('GPS Location')+'</div>',
                                margin: '-20px 0 0 0'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Trader.FormMainTrader-Latitude',
                                name: 'Koltiva.view.Trader.FormMainTrader-Latitude',
                                allowNegative: false,
                                labelAlign:'top',
                                fieldLabel: lang('Latitude')
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Trader.FormMainTrader-Longitude',
                                name: 'Koltiva.view.Trader.FormMainTrader-Longitude',
                                allowNegative: false,
                                labelAlign:'top',
                                fieldLabel: lang('Longitude')
                            },{
                                layout:'column',
                                border:false,
                                items:[{
                                    columnWidth: 1,
                                    border: false,
                                    layout:{
                                        type:'hbox',
                                        pack:'end'
                                    },
                                    items:[{
                                        xtype: 'image',
                                        id: 'Koltiva.view.Trader.FormMainTrader-agBusinessLocation',
                                        width: '225px',
                                        height:'175px',
                                        src: m_api_base_url + '/images/default_photo/agent-location.jpg'
                                    },{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Trader.FormMainTrader-agBusinessLocationOld',
                                        name: 'Koltiva.view.Trader.FormMainTrader-agBusinessLocationOld',
                                        inputType: 'hidden'
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
                                        fieldLabel: lang('Location Photo'),
                                        labelAlign: 'top',
                                        id: 'Koltiva.view.Trader.FormMainTrader-agBusinessLocationInput',
                                        name: 'Koltiva.view.Trader.FormMainTrader-agBusinessLocationInput',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                objPanelBasicData.submit({
                                                    url: m_api + '/trader_mem/image_member_business_photo',
                                                    clientValidation: false,
                                                    params: {
                                                        opsiDisplay: thisObj.opsiDisplay,
                                                        MemberID: Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberID').getValue()
                                                    },
                                                    waitMsg: 'Sending Photo...',
                                                    success: function (fp, o) {
                                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-agBusinessLocation').setSrc(m_api_base_url + '/images/trader_business/' + o.result.file);
                                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-agBusinessLocationOld').setValue(o.result.file);
                                                    }
                                                });
                                            }
                                        }
                                    }]
                                }]
                            },{
                                html:'<br /><div class="subtitleForm">'+lang('Business Owner')+'</div>'
                            },{
                                layout:'column',
                                border:false,
                                items:[{
                                    columnWidth: 1,
                                    border: false,
                                    layout:{
                                        type:'hbox',
                                        pack:'end'
                                    },
                                    items:[{
                                        xtype: 'image',
                                        id: 'Koltiva.view.Trader.FormMainTrader-MemberPhoto',
                                        width: '175px',
                                        height:'200px',
                                        src: m_api_base_url + '/images/default_photo/male-business.jpg'
                                    },{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Trader.FormMainTrader-MemberPhotoOld',
                                        name: 'Koltiva.view.Trader.FormMainTrader-MemberPhotoOld',
                                        inputType: 'hidden'
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
                                        fieldLabel: lang('Photo'),
                                        labelAlign: 'top',
                                        id: 'Koltiva.view.Trader.FormMainTrader-MemberPhotoInput',
                                        name: 'Koltiva.view.Trader.FormMainTrader-MemberPhotoInput',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                objPanelBasicData.submit({
                                                    url: m_api + '/trader_mem/image_member',
                                                    clientValidation: false,
                                                    params: {
                                                        opsiDisplay: thisObj.opsiDisplay,
                                                        MemberID: Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberID').getValue()
                                                    },
                                                    waitMsg: 'Sending Photo...',
                                                    success: function (fp, o) {
                                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/trader/' + o.result.file);
                                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberPhotoOld').setValue(o.result.file);
                                                    }
                                                });
                                            }
                                        }
                                    }]
                                }]
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Trader.FormMainTrader-Fullname',
                                name: 'Koltiva.view.Trader.FormMainTrader-Fullname',
                                fieldLabel: lang('SME Name'),
                                //labelWidth: 150,
                                labelAlign:'top',
                                allowBlank: false
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Trader.FormMainTrader-Nin',
                                name: 'Koltiva.view.Trader.FormMainTrader-Nin',
                                fieldLabel: lang('National Identification Number'),
                                //labelWidth: 180,
                                labelAlign:'top'
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.Trader.FormMainTrader-DateOfBirth',
                                name: 'Koltiva.view.Trader.FormMainTrader-DateOfBirth',
                                fieldLabel: lang('Date of Birth'),
                                //labelWidth: 150,
                                labelAlign:'top',
                                allowBlank: false,
                                format: 'Y-m-d'
                            },{
                                html:'<div></div>',
                            },{
                                fieldLabel: lang('Gender'),
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                allowBlank: false,
                                msgTarget: 'side',
                                columns: 2,
                                items:[{
                                    boxLabel: lang('Male'),
                                    name: 'Koltiva.view.Trader.FormMainTrader-Gender',
                                    inputValue: 'm',
                                    id: 'Koltiva.view.Trader.FormMainTrader-GenderMale',
                                    style: 'margin-top:-10px;',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Female'),
                                    name: 'Koltiva.view.Trader.FormMainTrader-Gender',
                                    inputValue: 'f',
                                    id: 'Koltiva.view.Trader.FormMainTrader-GenderFemale',
                                    style: 'margin-top:-10px;',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                html:'<div></div>',
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Trader.FormMainTrader-Handphone',
                                name: 'Koltiva.view.Trader.FormMainTrader-Handphone',
                                fieldLabel: lang('Handphone'),
                                labelAlign:'top'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                vtype: 'email',
                                id: 'Koltiva.view.Trader.FormMainTrader-Email',
                                name: 'Koltiva.view.Trader.FormMainTrader-Email',
                                fieldLabel: lang('Email'),
                                labelAlign:'top'
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Trader.FormMainTrader-Education',
                                name: 'Koltiva.view.Trader.FormMainTrader-Education',
                                store: cmb_education,
                                fieldLabel: lang('Last Education'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
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
                id: 'Koltiva.view.Trader.FormMainTrader-btnSave',
                cls: 's-blue',
                handler: function () {
                    if (objPanelBasicData.isValid()) {

                        //cek apakah role ada di pilih (begin)
                        var validRole = false;

                        if(
                            Ext.getCmp('Koltiva.view.Trader.FormMainTrader-CbRoleTrader').getValue() == "1" ||
                            Ext.getCmp('Koltiva.view.Trader.FormMainTrader-CbRoleVilCol').getValue() == "1" ||
                            Ext.getCmp('Koltiva.view.Trader.FormMainTrader-CbRoleRamp').getValue() == "1" ||
                            Ext.getCmp('Koltiva.view.Trader.FormMainTrader-CbRoleDealer').getValue() == "1" ||
                            Ext.getCmp('Koltiva.view.Trader.FormMainTrader-CbRoleDoHolder').getValue() == "1"
                        ){
                            validRole = true;
                        }

                        if(validRole == false){
                            Ext.MessageBox.show({
                                title: 'Attention',
                                msg: lang('Role must be choose at least one'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                            return false;
                        }
                        //cek apakah role ada di pilih (end)

                        objPanelBasicData.submit({
                            url: m_api + '/trader_mem/member',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(fp, o) {
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data saved'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                Ext.getCmp('Koltiva.view.Trader.FormMainTrader').destroy(); //destory current view
                                //create object View untuk FormMainTrader
                                if(Ext.getCmp('Koltiva.view.Trader.FormMainTrader') == undefined){
                                    var FormMainTrader = Ext.create('Koltiva.view.Trader.FormMainTrader', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            MemberID: o.result.MemberIDInc
                                        }
                                    });
                                }else{
                                    //destroy, create ulang
                                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader').destroy();
                                    var FormMainTrader = Ext.create('Koltiva.view.Trader.FormMainTrader', {
                                        opsiDisplay: 'update',
                                        viewVar: {
                                            MemberID: o.result.MemberIDInc
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
        //panel Form Basic Data ======================================================================================= (End)

        //panel Mill Staff Data ======================================================================================= (Begin)
        var objPanelTraderVehicle = Ext.create('Koltiva.view.Trader.TraderVehiclePanel');
        thisObj.objPanelTraderVehicle = objPanelTraderVehicle;
        //panel Mill Staff Data ======================================================================================= (End)

        //panel Trader Survey Summary ======================================================================================= (Begin)
        var objPanelTraderSurvey = Ext.create('Koltiva.view.TraderSurvey.TraderSurveyPanelSummary');
        thisObj.objPanelTraderSurveyPanel = objPanelTraderSurvey;
        //panel Trader Survey Summary ======================================================================================= (End)
        
        //panel Mill Staff Data ======================================================================================= (Begin)
        var objPanelTraderStaff = Ext.create('Koltiva.view.Trader.TraderStaffPanel');
        thisObj.objPanelTraderStaff = objPanelTraderStaff;
        //panel Mill Staff Data ======================================================================================= (End)

        //isi layout utama ================================================================================================= (Begin)
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                html:'<h3 style="margin:0px 0 7px 0;padding:0px;">'+lang('SME Data')+'</h3>'
            },{
                id: 'Koltiva.view.Trader.FormMainTrader-labelInfoInsert',
                html:'',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to SME List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader') == undefined){
                            var GridMainTrader = Ext.create('Koltiva.view.Trader.GridMainTrader');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Trader.GridMainTrader').destroy();
                            var GridMainTrader = Ext.create('Koltiva.view.Trader.GridMainTrader');
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
                    thisObj.objPanelTraderStaff,
                    thisObj.objPanelTraderVehicle,
                    thisObj.objPanelTraderSurveyPanel
                ]
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

            //hidden panel yg dicek by Role semua =================== (begin)
            thisObj.objPanelTraderSurveyPanel.setVisible(false);
            thisObj.objPanelTraderSTAPanel.setVisible(false);
            //hidden panel yg dicek by Role semua =================== (end)

            //set label
            if(this.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-labelInfoInsert').update('<h5 style="margin:8px 0 7px 15px;padding:0px;">('+lang('Add New SME')+')</h5>');

                //form reset
                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/default_photo/male-business.jpg');
                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-agBusinessLocation').setSrc(m_api_base_url + '/images/default_photo/agent-location.jpg');

                //Trader Survey
                thisObj.objPanelTraderSurveyPanel.collapse();
                thisObj.objPanelTraderSurveyPanel.setViewVar({
                    MemberID:null
                });

                //trader vehicle
                thisObj.objPanelTraderVehicle.collapse();
                thisObj.objPanelTraderVehicle.setViewVar({
                    MemberID:null
                });

                //Trader Staff
                thisObj.objPanelTraderStaff.collapse();
                thisObj.objPanelTraderStaff.setViewVar({
                    MemberID:null
                });

                //load store trader survey summary
                var grid_trader_survey = Ext.data.StoreManager.lookup('store.TraderSurvey.GridTraderSurveySummary');
                grid_trader_survey.setStoreVar({MemberID:null});
                grid_trader_survey.load();

            }else{
                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-labelInfoInsert').update('');
            }

            if(this.opsiDisplay == 'update' || this.opsiDisplay == 'view'){
                //console.log(this.formVar);

                //khusus view only
                if(this.opsiDisplay == 'view'){
                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader-btnSave').setVisible(false);
                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberPhotoInput').setVisible(false);
                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader-agBusinessLocationInput').setVisible(false);
                }

                //form reset
                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/user.png');

                //load data form
                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-FormBasicData').getForm().load({
                    url: m_api + '/trader_mem/member_basic_data_form',
                    method: 'GET',
                    params: {
                        MemberID: this.viewVar.MemberID
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
                                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-Province').setValue(r.data.Province);
                                if (success == true) {
                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.Province
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.Trader.FormMainTrader-District').setValue(r.data.District);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.District
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.Trader.FormMainTrader-Subdistrict').setValue(r.data.Subdistrict);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.Subdistrict
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-Village').setValue(r.data.Village);
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
                        if(r.data.PhotoSrc != ""){
                            var fotoUser = m_api_base_url + '/images/trader/'+r.data.Province+'/'+ r.data.PhotoSrc;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberPhoto').setSrc(fotoUser+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/default_photo/female-business.jpg');
                                    }else{
                                        Ext.getCmp('Koltiva.view.Trader.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/default_photo/male-business.jpg');
                                    }
                                }
                            });
                        }

                        //set photo
                        if(r.data.PhotoBusinessLocation != ""){
                            var fotoUserBusiness = m_api_base_url + '/images/trader_business/'+r.data.Province+'/'+ r.data.PhotoBusinessLocation;
                            var angkaRandBusiness = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUserBusiness, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader-agBusinessLocation').setSrc(fotoUserBusiness+'?'+angkaRand);
                                } else {
                                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader-agBusinessLocation').setSrc(m_api_base_url + '/images/default_photo/agent-location.jpg');
                                }
                            });
                        }

                        //Trader Staff
                        thisObj.objPanelTraderStaff.expand();
                        thisObj.objPanelTraderStaff.setViewVar({
                            MemberID:thisObj.viewVar.MemberID
                        });
                        thisObj.objPanelTraderStaff.loadStoreGrid();

                        //trader vehicle
                        thisObj.objPanelTraderVehicle.expand();
                        thisObj.objPanelTraderVehicle.setViewVar({
                            MemberID:thisObj.viewVar.MemberID
                        });
                        thisObj.objPanelTraderVehicle.loadStoreGrid();
                        
                        //baru cek buka panel mana sesuai role nya =============================== (begin)
                        var RoleCheck;
                        for (i = 0; i < r.data.ArrSurID.length; i++) {
                            RoleCheck = parseInt(r.data.ArrSurID[i]);
                            switch(RoleCheck){
                                case 5: //Trader
                                    thisObj.objPanelTraderSurveyPanel.setVisible(true);

                                    //Trader Survey
                                    thisObj.objPanelTraderSurveyPanel.expand();
                                    thisObj.objPanelTraderSurveyPanel.setViewVar({
                                        MemberID:thisObj.viewVar.MemberID
                                    });

                                    //load store trader survey summary
                                    var grid_trader_survey = Ext.data.StoreManager.lookup('store.TraderSurvey.GridTraderSurveySummary');
                                    grid_trader_survey.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                    grid_trader_survey.load();
                                break;
                                case 6:

                                break;
                                case 7:

                                break;
                                case 8:

                                break;
                                case 9:

                                break;
                            }
                        }
                        //baru cek buka panel mana sesuai role nya =============================== (end)

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