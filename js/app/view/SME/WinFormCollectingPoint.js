/*
* @Author: nikolius
* @Date:   2017-09-07 14:50:06
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-27 15:08:11
*/

/*
    Param2 yg diperlukan ketika load View ini
    - opsiDisplay
    - MemberID
    - VehID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.SME.WinFormCollectingPoint' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.SME.WinFormCollectingPoint',
    title: lang('Collecting Point Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '80%',
    height: '60%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store
        
        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();
        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.SME.WinFormCollectingPoint-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.495,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointID',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-OrgType',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-OrgType',
                        value:'agent'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-OrgID',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-OrgID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointDisplayID',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointDisplayID',
                        fieldLabel: lang('Collecting Point ID'),
                        labelAlign:'top',
                        labelWidth: 200,
                        readOnly:true
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointName',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointName',
                        fieldLabel: lang('Collecting Point Name'),
                        labelAlign:'top',
                        labelWidth: 200
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-ProvinceID',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-ProvinceID',
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
                                Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-DistrictID').setValue('');
                                Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-SubdistrictID').setValue('');
                                Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-VillageID').setValue('');
                            }
                        }
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-DistrictID',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-DistrictID',
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
                                Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-SubdistrictID').setValue('');
                                Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-VillageID').setValue('');
                            }
                        }
                    },{
                        html:'<div></div>'
                    },{
                        html:'<div></div>'
                    }]
                },{
                    columnWidth: 0.495,
                    margin:'0 10 0 0',
                    style:'padding-left:15px;',
                    layout:'form',
                    items:[{
                        xtype: 'combobox',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-SubdistrictID',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-SubdistrictID',
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
                                Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-VillageID').setValue('');
                            }
                        }
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-VillageID',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-VillageID',
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
                                id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-Latitude',
                                name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-Latitude',
                                labelAlign: 'top',
                                fieldLabel: lang('Latitude')
                            }]
                        },{
                            columnWidth: 0.495,
                            layout:'form',
                            style: 'margin-left: 5px',
                            items: [{
                                xtype: 'textfield',
                                id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-Longitude',
                                name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-Longitude',
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
                        id: 'Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointAddress',
                        name: 'Koltiva.view.SME.WinFormCollectingPoint-Form-CollectpointAddress',
                        height: 90  
                    }]
                }]
            }]
        }]

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.SME.WinFormCollectingPoint-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var formCollectingPoint = Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form').getForm();
                if (formCollectingPoint.isValid()) {
                    formCollectingPoint.submit({
                        url: m_api + '/tph/tph_form_new',
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

                            //form reset
                            formCollectingPoint.reset();

                            //refresh store vehicle yg manggil
                            Ext.data.StoreManager.lookup('Koltiva.store.SME.GridTraderCollectingPoint').load();

                            //tutup popup
                            thisObj.close();
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
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            console.log(thisObj.viewVar.MemberID);

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-OrgID').setValue(thisObj.viewVar.MemberID);

            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
                formNya.getForm().load({
                    url: m_api + '/tph/basic_data_form_new',
                    method: 'GET',
                    params: {
                        CollectpointID: thisObj.viewVar.CollectpointID
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
                                Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-ProvinceID').setValue(r.data.ProvinceID);
                                if (success == true) {
                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.ProvinceID
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-DistrictID').setValue(r.data.DistrictID);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.DistrictID
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-SubdistrictID').setValue(r.data.SubDistrictID);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.SubDistrictID
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-Form-VillageID').setValue(r.data.VillageID);
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

                        if(thisObj.viewVar.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.SME.WinFormCollectingPoint-BtnSave').setVisible(false);
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
    }
});