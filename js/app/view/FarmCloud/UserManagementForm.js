/*
* @Author: Fashah Darullah
* @Date:   2019-06-12 11:19:19
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar
*/

Ext.define('Koltiva.view.FarmCloud.UserManagementForm' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmCloud.UserManagementForm',
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
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Div nya Filter Region
            document.getElementById('divCommonContentRegion').style.display = 'none';
            // document.getElementById('main-breadcrumb').style.display = 'none';
            // Ext.getCmp('Koltiva.view.Farmer.TypeMainForm-FormBasicData-CertBecomeNon').setVisible(false);

        	if(thisObj.viewVar.opsiDisplay == 'insert'){
        		//form reset
                Ext.getCmp('Koltiva.view.Farmer.TypeMainForm-FormBasicData').getForm().reset();

                //Farmer Type
                Ext.getCmp('Koltiva.view.Farmer.TypeMainForm-FormBasicData-PersonExtID').setReadOnly(true);
            }
            
            if(thisObj.viewVar.opsiDisplay == 'view'){
        		//Set ReadOnly
                Ext.getCmp('Koltiva.view.Trader.MainForm-FormBasicData-PhotoInput').setVisible(false);
                Ext.getCmp('Koltiva.view.Farmer.TypeMainForm-FormBasicData').getForm().load({
                    url: m_api + '/farmcloud/form_user_view',
                    method: 'GET',
                    params: {
                        PersonExtID: thisObj.viewVar.PersonExtID
                    },
                    success: function(form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);
                    },
                    failure: function(form, action) {
                        Ext.MessageBox.hide();
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
        },
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.opsiDisplay != 'insert') {
                Ext.MessageBox.show({
                    msg: 'Please wait...',
                    progressText: 'Loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 200
                    },
                    icon: 'ext-mb-info', //custom class in msg-box.html
                    animateTarget: 'mb9'
                });
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Store yg dipakai =============================================================== (Begin)
        var cmb_marital_status = Ext.create('Koltiva.store.Grower.CmbAdvancedFilterMaritalStatus');
        var cmb_education = Ext.create('Koltiva.store.Grower.CmbEducation');
        //Store yg dipakai =============================================================== (End)

        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Account'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.Farmer.TypeMainForm-FormBasicData',
            fileUpload: true,
            collapsible:true,
		    buttonAlign : 'center',
            items: [{
                layout: 'column',
                border: false,
                padding:10,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    cls: 'Sfr_PanelLayoutFormContainer',
                    items:[{
                        xtype: 'panel',
                        flex: 1,
                        activeTab: 0,
                        plain: true,
                        cls:'Sfr_TabForm',
                        id: 'Koltiva.view.Farmer.TypeMainForm-FormBasicData-Tab',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[
                                {
                                    columnWidth: 0.5,
                                    layout:'form',
                                    style:'padding:10px 5px 10px 20px;',
                                    defaults: {
                                        labelAlign: 'left',
                                        labelWidth: 150
                                    },
                                    items:[
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Farmer.TypeMainForm-FormBasicData-PersonExtID',
                                            name: 'PersonExtID',
                                            fieldLabel: lang('Person ID'),
                                            queryMode: 'local',
                                            allowBlank: true,
                                            valueField: 'id',
                                            readOnly: true,
                                            hidden:true
                                        },
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Farmer.TypeMainForm-FormBasicData-PersonName',
                                            name: 'PersonName',
                                            fieldLabel: lang('Name'),
                                            labelAlign:'top',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Trader.MainForm-FormBasicData-DateOfBirth',
                                            name: 'DateOfBirth',
                                            fieldLabel: lang('Date of Birth'),
                                            //labelWidth: 150,
                                            labelAlign:'top',
                                            // allowBlank: false,
                                            format: 'Y-m-d'
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            fieldLabel: lang('Jenis Kelamin'),
                                            xtype: 'radiogroup',
                                            width: '100%',
                                            allowBlank: false,
                                            readOnly:true,
                                            id:'Koltiva.view.Farmer.TypeMainForm-FormBasicData-GroupGender',
                                            defaults: {xtype: "radio",name: 'Gender'},
                                            labelAlign:'top',
                                            items: [{
                                                boxLabel: lang('Laki-laki'),
                                                inputValue: 'Male'
                                            }, {
                                                boxLabel: lang('Perempuan'),
                                                inputValue: 'Female'
                                            }]
                                        },{html:'<div style="height:3px;">&nbsp;</div>'}, 
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: lang('Province'),
                                            labelWidth: 200,
                                            labelAlign:'top',
                                            id: 'ProvinceName',
                                            name: 'ProvinceName',
                                            readOnly:true
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},{
                                            xtype: 'combobox',
                                            fieldLabel: lang('District'),
                                            labelWidth: 200,
                                            labelAlign:'top',
                                            id: 'DistrictName',
                                            name: 'DistrictName',
                                            readOnly:true
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'combobox',
                                            fieldLabel: lang('Sub District'),
                                            labelWidth: 200,
                                            labelAlign:'top',
                                            id: 'SubDistrictName',
                                            name: 'SubDistrictName',
                                            readOnly:true
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'combobox',
                                            fieldLabel: lang('Village Name'),
                                            labelWidth: 200,
                                            labelAlign:'top',
                                            id: 'VillageName',
                                            name: 'VillageName',
                                            readOnly:true
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'textarea',
                                            fieldLabel: lang('Address'),
                                            readOnly:true,
                                            labelAlign:'top',
                                            id: 'Address',
                                            name: 'Address',
                                            height: 100
                                        },{html:'<div style="height:3px;">&nbsp;</div>'}
                                    ]
                                },
                                {
                                    columnWidth: 0.5,
                                    layout:'form',
                                    style:'padding:10px 5px 10px 20px;border-left:1px dashed gray;',
                                    defaults: {
                                        labelAlign: 'left',
                                        labelWidth: 150
                                    },
                                    items:[
                                        {
                                            xtype:'panel',
                                            id:'Koltiva.view.Trader.MainForm-FormBasicData-Photo',
                                            html:'<img src="'+m_api_base_url+'/images/Photo/default-user.png" style="height:150px;margin:10px;float:right;" />'
                                        },{
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang('Photo'),
                                            labelAlign:'top',
                                            id: 'Koltiva.view.Trader.MainForm-FormBasicData-PhotoInput',
                                            name: 'Photo',
                                            buttonText: 'Browse',
                                            
                                            listeners: {
                                                'change': function (fb, v) {
                                                    Ext.getCmp('Koltiva.view.Trader.MainForm-FormBasicData').getForm().submit({
                                                        url: m_api + '/trader_new/photo_trader',
                                                        clientValidation: false,
                                                        params: {
                                                            opsiDisplay: thisObj.viewVar.opsiDisplay,
                                                            TraderID: Ext.getCmp('Koltiva.view.Trader.MainForm-FormBasicData-TraderID').getValue(),
                                                            ProvinceID: Ext.getCmp('Koltiva.view.Trader.MainForm-FormBasicData-Province').getValue(),
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            Ext.getCmp('Koltiva.view.Trader.MainForm-FormBasicData-Photo').update('<img src="'+m_api_base_url + '/images/Photo_trader/' + o.result.file+'" style="height:150px;margin:10px;float:right;" />');
                                                            Ext.getCmp('PhotoOld').setValue(o.result.photoInput);
                                                        }
                                                    });
                                                }
                                            }
                                        },{
                                            xtype: 'hiddenfield',
                                            id: 'PhotoOld',
                                            name: 'Photo2',
                                            fieldLabel: lang('Photo2'),
                                            labelAlign:'top',
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'textfield',
                                            id: 'HandphoneType',
                                            name: 'HandphoneType',
                                            fieldLabel: lang('Handphone Type'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'textfield',
                                            id: 'HandPhone',
                                            name: 'HandPhone',
                                            fieldLabel: lang('Handphone'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'textfield',
                                            id: 'Email',
                                            name: 'Email',
                                            fieldLabel: lang('Email'),
                                            labelAlign:'top',
                                            readOnly:true
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'combobox',
                                            id: 'MaritalStatus',
                                            name: 'MaritalStatus',
                                            store: cmb_marital_status,
                                            fieldLabel: lang('Marital Status'),
                                            queryMode: 'local',
                                            displayField: 'label',
                                            labelAlign:'top',
                                            valueField: 'id',
                                            readOnly:true
                                        },{ html: '<div style="height:13px;">&nbsp;</div>' },{
                                            xtype: 'combobox',
                                            id: 'Education',
                                            name: 'Education',
                                            store: cmb_education,
                                            labelAlign:'top',
                                            fieldLabel: lang('Education'),
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            readOnly:true
                                        }
                                    ]
                                }
                            ]
                        }],
                        listeners: {
                            'tabchange': function (tabPanel, tab) {
                                switch(tab.id){
                                    case 'Koltiva.view.Farmer.TypeMainForm-FormBasicData-TabContract':
                                        thisObj.ObjPanelContractGridCertificationContract.store.setStoreVar({FarmerID: Ext.getCmp('Koltiva.view.Farmer.TypeMainForm-FormBasicData-FarmerID').getValue()});
                                        thisObj.ObjPanelContractGridCertificationContract.store.load();
                                    break;
                                }
                            }
                        }
                    }]
                }]
            }]
        });
        //Panel Basic ==================================== (End)

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.Grower.FormMainGrower-labelInfoInsert',
                html:'<div id="header_title_farmer">'+lang('Account')+'</div>'
            }]
        },{
            items:[{
                id: 'Koltiva.view.Grower.FormMainGrower-LinkBackToList',
                html:'<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.FarmCloud.UserManagementForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="'+varjs.config.base_url+'images/icons/new/back.png" width="20" />&nbsp;&nbsp;'+lang('Back to User Management List')+'</a></li></div>'
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
                    thisObj.ObjPanelBasicData
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    BackToList: function(){
        Ext.getCmp('Koltiva.view.FarmCloud.UserManagementForm').destroy(); //destory current view
        var GridMainGrower = [];
        if(Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid') == undefined){
            GridMainGrower = Ext.create('Koltiva.view.FarmCloud.UserManagementGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.FarmCloud.UserManagementGrid');
        }
    }
});
function getCBSValue(cb, nameIn, nameOut){
    try{
         var r = cb.getStore().find(nameIn,cb.getValue());
         return cb.getStore().getAt(r).get(nameOut);
    }
    catch(err){
         return'error';
    }
}