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

Ext.define('Koltiva.view.SME.WinFormVehicle' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.SME.WinFormVehicle',
    title: lang('Vehicle Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '45%',
    height: '55%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store
        var cmb_vehicle_capacity = Ext.create('Koltiva.store.SME.CmbVehicleCapacity');
        var cmb_brand_vehicle = Ext.create('Koltiva.store.SME.CmbBrandVehicle');
        var cmb_tipe_vehicle = Ext.create('Koltiva.store.SME.CmbTipeVehicle');
        var cmb_staff_trader = Ext.create('Koltiva.store.SME.CmbStaffTrader');
        thisObj.cmb_staff_trader = cmb_staff_trader;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.SME.WinFormVehicle-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.SME.WinFormVehicle-Form-VehID',
                        name: 'Koltiva.view.SME.WinFormVehicle-Form-VehID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.SME.WinFormVehicle-Form-MemberID',
                        name: 'Koltiva.view.SME.WinFormVehicle-Form-MemberID'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.SME.WinFormVehicle-Form-BrandID',
                        name: 'Koltiva.view.SME.WinFormVehicle-Form-BrandID',
                        store: cmb_brand_vehicle,
                        fieldLabel: lang('Brand'),
                        labelAlign:'top',
                        labelWidth: 200,
                        allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.SME.WinFormVehicle-Form-VehName',
                        name: 'Koltiva.view.SME.WinFormVehicle-Form-VehName',
                        store: cmb_tipe_vehicle,
                        fieldLabel: lang('Type'),
                        labelAlign:'top',
                        labelWidth: 200,
                        allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.SME.WinFormVehicle-Form-VehPoliceNr',
                        name: 'Koltiva.view.SME.WinFormVehicle-Form-VehPoliceNr',
                        fieldLabel: lang('Police Number / Registration Number'),
                        labelAlign:'top',
                        allowBlank: false
                    },{
                        html:'<div></div>'
                    },{
                        fieldLabel: lang('Ownership'),
                        labelWidth: 200,
                        xtype: 'radiogroup',
                        labelAlign:'top',
                        columns: 2,
                        items:[{
                            boxLabel: lang('Private Owner'),
                            name: 'Koltiva.view.SME.WinFormVehicle-Form-Ownership',
                            inputValue: '1',
                            id: 'Koltiva.view.SME.WinFormVehicle-Form-Ownership1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Rented'),
                            name: 'Koltiva.view.SME.WinFormVehicle-Form-Ownership',
                            inputValue: '2',
                            id: 'Koltiva.view.SME.WinFormVehicle-Form-Ownership2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.SME.WinFormVehicle-Form-Remark',
                        name: 'Koltiva.view.SME.WinFormVehicle-Form-Remark',
                        fieldLabel: lang('Comment'),
                        labelAlign:'top',
                        labelWidth: 200,
                        allowBlank: false
                    },{
                        html:'<div></div>'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.SME.WinFormVehicle-Form-VehCapacity',
                        name: 'Koltiva.view.SME.WinFormVehicle-Form-VehCapacity',
                        fieldLabel: lang('Capacity')+' (kg)',
                        labelAlign:'top',
                        labelWidth: 200,
                        store: cmb_vehicle_capacity,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.SME.WinFormVehicle-Form-StaffID',
                        name: 'Koltiva.view.SME.WinFormVehicle-Form-StaffID',
                        store: cmb_staff_trader,
                        fieldLabel: lang('Driver'),
                        labelAlign:'top',
                        labelWidth: 200,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        hidden: true
                    }]
                }]
            }]
        }]

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.SME.WinFormVehicle-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var formVehicle = Ext.getCmp('Koltiva.view.SME.WinFormVehicle-Form').getForm();
                if (formVehicle.isValid()) {
                    formVehicle.submit({
                        url: m_api + '/sme/trader_vehicle',
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
                            formVehicle.reset();

                            //refresh store vehicle yg manggil
                            Ext.data.StoreManager.lookup('Koltiva.store.SME.GridTraderVehicle').load();

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

            //load combo trader staff
            thisObj.cmb_staff_trader.setStoreVar({MemberID:thisObj.viewVar.MemberID});
            thisObj.cmb_staff_trader.load();

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.SME.WinFormVehicle-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.SME.WinFormVehicle-Form-MemberID').setValue(thisObj.viewVar.MemberID);

            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
                formNya.getForm().load({
                    url: m_api + '/sme/trader_vehicle_form',
                    method: 'GET',
                    params: {
                        VehID: thisObj.viewVar.VehID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        if(thisObj.viewVar.opsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.SME.WinFormVehicle-BtnSave').setVisible(false);
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