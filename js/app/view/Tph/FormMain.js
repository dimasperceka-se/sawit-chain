/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu May 02 2019
 *  File : FormMain.js
 *******************************************/

Ext.define('Koltiva.view.Tph.FormMain' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Tph.FormMain',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    OpsiDisplay: false,
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

        var ObjPanelBasicForm = Ext.create('Ext.form.Panel',{
            title: lang('Basic Data'),
            frame: true,
            id: 'Koltiva.view.Tph.FormMain-Form',
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
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-right:15px;',
                            items:[{
                                xtype: 'panel',
                                title: lang('General Data'),
                                frame: false,
                                id: 'Koltiva.view.Tph.FormMain-GeneralDataSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.Tph.FormMain-CollectpointID',
                                name: 'Koltiva.view.Tph.FormMain-CollectpointID'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Tph.FormMain-CollectpointDisplayID',
                                name: 'Koltiva.view.Tph.FormMain-CollectpointDisplayID',
                                fieldLabel: lang('TPH ID'),
                                labelAlign:'top',
                                readOnly:true
                            },{
                                html:'<div></div>'
                            },{
                                fieldLabel: lang('Type'),
                                labelAlign:'top',
                                xtype: 'radiogroup',
                                columns: 3,
                                allowBlank: false,
                                msgTarget: 'side',
                                id: 'Koltiva.view.Tph.FormMain-RowOrgType',
                                items:[{
                                    boxLabel: lang('SME'),
                                    name: 'Koltiva.view.Tph.FormMain-OrgType',
                                    inputValue: 'agent',
                                    id: 'Koltiva.view.Tph.FormMain-OrgTypeAgent',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Farmer'),
                                    name: 'Koltiva.view.Tph.FormMain-OrgType',
                                    inputValue: 'farmer',
                                    id: 'Koltiva.view.Tph.FormMain-OrgTypeFarmer',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('Collective'),
                                    name: 'Koltiva.view.Tph.FormMain-OrgType',
                                    inputValue: 'collective',
                                    id: 'Koltiva.view.Tph.FormMain-OrgTypeCollective',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }] 
                            },{
                                layout:'column',
                                border:false,
                                items:[{
                                    columnWidth: 1,
                                    border: false,
                                    layout:{
                                        type:'hbox',
                                        pack:'left',
                                        align: 'stretch'
                                    },
                                    items:[{
                                        xtype: 'hiddenfield',
                                        id: 'Koltiva.view.Tph.FormMain-OrgID',
                                        name: 'Koltiva.view.Tph.FormMain-OrgID'
                                    },{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Tph.FormMain-OrgIDLabel',
                                        name: 'Koltiva.view.Tph.FormMain-OrgIDLabel',
                                        flex: 11,
                                        allowBlank: false,
                                        emptyText: lang('Responsible'),
                                        readOnly:true
                                    },{
                                        xtype:'button',
                                        text: '...',
                                        tooltip: lang('Select Member'),
                                        style:'margin-left:12px;',
                                        flex: 1,
                                        handler: function() {
                                            var OrgType = Ext.ComponentQuery.query('[name=Koltiva.view.Tph.FormMain-OrgType]')[0].getGroupValue();
                                            var ListType;

                                            switch(OrgType){
                                                case 'agent':
                                                    ListType = 'agent';
                                                break;
                                                case 'farmer':
                                                case 'collective':
                                                    ListType = 'farmer';
                                                break;
                                                default:
                                                    Ext.MessageBox.show({
                                                        title: 'Information',
                                                        msg: lang('Type must be selected first'),
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-info'
                                                    });
                                                break;
                                            }

                                            var WinSelectMemberGeneral = Ext.create('Koltiva.view.Widget.WinSelectMemberGeneral', {
                                                viewVar: {
                                                    ListType: ListType,
                                                    CompID: Ext.getCmp('Koltiva.view.Tph.FormMain-OrgID'),
                                                    CompLabel: Ext.getCmp('Koltiva.view.Tph.FormMain-OrgIDLabel')
                                                }
                                            });
                                            if (!WinSelectMemberGeneral.isVisible()) {
                                                WinSelectMemberGeneral.center();
                                                WinSelectMemberGeneral.show();
                                            } else {
                                                WinSelectMemberGeneral.close();
                                            }
                                        }
                                    }]
                                }]
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Tph.FormMain-CollectpointName',
                                name: 'Koltiva.view.Tph.FormMain-CollectpointName',
                                allowBlank: false,
                                fieldLabel: lang('Name'),
                                labelAlign: 'top'
                            },{
                                html:'<div></div>'
                            }]
                        },{
                            columnWidth: 0.499,
                            layout:'form',
                            style:'padding-left:9px;',
                            items:[{
                                xtype: 'panel',
                                title: lang('Location'),
                                frame: false,
                                id: 'Koltiva.view.Tph.FormMain-LocationSection',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Tph.FormMain-ProvinceID',
                                name: 'Koltiva.view.Tph.FormMain-ProvinceID',
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
                                        Ext.getCmp('Koltiva.view.Tph.FormMain-DistrictID').setValue('');
                                        Ext.getCmp('Koltiva.view.Tph.FormMain-SubdistrictID').setValue('');
                                        Ext.getCmp('Koltiva.view.Tph.FormMain-VillageID').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Tph.FormMain-DistrictID',
                                name: 'Koltiva.view.Tph.FormMain-DistrictID',
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
                                        Ext.getCmp('Koltiva.view.Tph.FormMain-SubdistrictID').setValue('');
                                        Ext.getCmp('Koltiva.view.Tph.FormMain-VillageID').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Tph.FormMain-SubdistrictID',
                                name: 'Koltiva.view.Tph.FormMain-SubdistrictID',
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
                                        Ext.getCmp('Koltiva.view.Tph.FormMain-VillageID').setValue('');
                                    }
                                }
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Tph.FormMain-VillageID',
                                name: 'Koltiva.view.Tph.FormMain-VillageID',
                                store: cmb_village,
                                fieldLabel: lang('Village'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                allowBlank: false
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
                                        id: 'Koltiva.view.Tph.FormMain-Latitude',
                                        name: 'Koltiva.view.Tph.FormMain-Latitude',
                                        labelAlign: 'top',
                                        fieldLabel: lang('Latitude')
                                    }]
                                },{
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style: 'margin-left: 5px',
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.Tph.FormMain-Longitude',
                                        name: 'Koltiva.view.Tph.FormMain-Longitude',
                                        labelAlign: 'top',
                                        fieldLabel: lang('Longitude')
                                    }]
                                }]
                            },{
                                html:'<div></div>'
                            },{
                                xtype: 'textarea',
                                fieldLabel: lang('Address'),
                                labelAlign:'top',
                                id: 'Koltiva.view.Tph.FormMain-CollectpointAddress',
                                name: 'Koltiva.view.Tph.FormMain-CollectpointAddress',
                                height: 90  
                            }]
                        }]
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.Tph.FormMain-BtnSaveForm',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    if (ObjPanelBasicForm.isValid()) {
                        ObjPanelBasicForm.submit({
                            url: m_api + '/tph/tph_form',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(rp, o){
                                var r = Ext.decode(o.response.responseText);
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });
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

                                    //Rerender
                                    Ext.getCmp('Koltiva.view.Tph.FormMain').destroy();
                                    if(Ext.getCmp('Koltiva.view.Tph.FormMain') == undefined){
                                        var FormMainTrader = Ext.create('Koltiva.view.Tph.FormMain', {
                                            OpsiDisplay: 'update',
                                            viewVar: {
                                                CollectpointID: r.CollectpointID
                                            }
                                        });
                                    }else{
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.Tph.FormMain').destroy();
                                        var FormMainTrader = Ext.create('Koltiva.view.Tph.FormMain', {
                                            OpsiDisplay: 'update',
                                            viewVar: {
                                                CollectpointID: r.CollectpointID
                                            }
                                        });
                                    }
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
                            msg: lang('Form not complete yet'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }]
        });

        if(thisObj.OpsiDisplay == 'update' || thisObj.OpsiDisplay == 'view'){
            thisObj.PanelTphCollectiveMember = Ext.create('Koltiva.view.Tph.PanelTphCollectiveMember', {
                viewVar: {
                    CollectpointID: this.viewVar.CollectpointID
                }
            });
        }else{
            thisObj.PanelTphCollectiveMember = [];
        }

        //isi layout utama ================================================================================================= (Begin)
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.Tph.FormMain-LabelInfoTitle',
                html:'<h3 style="margin:0px 0px 4px 0px;padding:0px;">'+lang('TPH Data')+'</h3>',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Tph.FormMain').destroy(); //destory current view

                        if(Ext.getCmp('Koltiva.view.Tph.GridMainTph') == undefined){
                            var GridMain = Ext.create('Koltiva.view.Tph.GridMainTph');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Tph.GridMainTph').destroy();
                            var GridMain = Ext.create('Koltiva.view.Tph.GridMainTph');
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
                    ObjPanelBasicForm
                ]
            },{
                //RIGHT CONTENT
                columnWidth: 0.4,
                items:[
                    thisObj.PanelTphCollectiveMember
                ]
            }]
        }];
        //isi layout utama ================================================================================================= (End)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'none';

            //form reset
            Ext.getCmp('Koltiva.view.Tph.FormMain-Form').getForm().reset();

            //insert
            if(thisObj.OpsiDisplay == 'update' || thisObj.OpsiDisplay == 'view'){
                Ext.getCmp('Koltiva.view.Tph.FormMain-Form').getForm().load({
                    url: m_api + '/tph/basic_data_form',
                    method: 'GET',
                    params: {
                        CollectpointID: this.viewVar.CollectpointID
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
                                Ext.getCmp('Koltiva.view.Tph.FormMain-ProvinceID').setValue(r.data.ProvinceID);
                                if (success == true) {
                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.ProvinceID
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.Tph.FormMain-DistrictID').setValue(r.data.DistrictID);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.DistrictID
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.Tph.FormMain-SubdistrictID').setValue(r.data.SubDistrictID);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.SubDistrictID
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.Tph.FormMain-VillageID').setValue(r.data.VillageID);
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

                        //Set Title Form
                        Ext.getCmp('Koltiva.view.Tph.FormMain-LabelInfoTitle').update('<h3 style="margin:0px 0px 4px 0px;padding:0px;">'+r.data.CollectpointDisplayID+' - '+r.data.CollectpointName+'</h3>');
                        Ext.getCmp('Koltiva.view.Tph.FormMain-LabelInfoTitle').doLayout();
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

                if(thisObj.OpsiDisplay == 'view'){
                    Ext.getCmp('Koltiva.view.Tph.FormMain-BtnSaveForm').setVisible(false);
                }
            }
        }
    }
});