/*
* @Author: Nikolius Lau
* @Date:   2018-08-09 14:59:20
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-09 16:14:44
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSStaffID
    - IMSMasterID
    - IMSID
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinImsEventStaffEditWorkArea' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea',
    title: lang('IMS Event Staff - Update Work Area'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '32%',
    height: '44%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var cmb_province = Ext.create('Koltiva.store.ComboGeneral.CmbProvinceWorkArea');
        var cmb_district = Ext.create('Koltiva.store.ComboGeneral.CmbDistrictWorkArea');

        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form',
            padding: '5 20 5 8',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 150,
                padding: 10
            },
            items: [{
                xtype: 'panel',
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 1,
                        layout: 'form',
                        items: [{
	                    	xtype: 'hiddenfield',
	                        id: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-IMSStaffID',
	                        name: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-IMSStaffID',
	                        value: thisObj.viewVar.IMSStaffID
	                    },{
	                    	xtype: 'textfield',
	                        id: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-StaffName',
	                        name: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-StaffName',
	                        fieldLabel: lang('Staff Name')
	                    },{
                        	xtype: 'combobox',
                            id: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-Province',
                            name: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-Province',
                            store: cmb_province,
                            fieldLabel: lang('Province'),
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'id',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    cmb_district.load({
                                        params: {
                                            Province: nv
                                        }
                                    });
                                    Ext.getCmp('Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-District').setValue('');
                                }
                            }
                        },{
                        	xtype: 'combobox',
                            id: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-District',
                            name: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-District',
                            store: cmb_district,
                            fieldLabel: lang('District'),
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'id'
                        }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
                text: lang('Save'),
                margin: '5 15 5 5',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-BtnSave',
                handler: function () {
                    Ext.Ajax.request({
                        url: m_api + '/ims/ims_event_staff_work_area',
                        method: 'POST',
                        params: {
                            IMSStaffID: thisObj.viewVar.IMSStaffID,
                            WorkAreaID: Ext.getCmp('Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-District').getValue()
                        },
                        success: function (response, action) {
                            //console.log(response);
                            var objReturn = Ext.decode(response.responseText);

                            switch (objReturn.success_val) {
                                case true:
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Success'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb7',
                                        icon: 'ext-mb-success'
                                    });

                                    thisObj.viewVar.CallerStore.load();
                                    thisObj.close();
                                    break;
                                case false:
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: objReturn.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb7',
                                        icon: 'ext-mb-info'
                                    });
                                    break;
                            }
                        },
                        failure: function (response, action) {
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Network Connection Error',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }
            }, {
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                margin: '5px',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            Ext.Ajax.request({
                url: m_api + '/ims/ims_event_staff_work_area_form_open',
                method: 'POST',
                params: {
                    IMSStaffID: thisObj.viewVar.IMSStaffID
                },
                success: function(response, action) {
                	var objReturn = Ext.decode(response.responseText);
                	//console.log(objReturn);
                	Ext.getCmp('Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-StaffName').setValue(objReturn.StaffName);

                	var cmb_province = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbProvinceWorkArea');
                	var cmb_district = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbDistrictWorkArea');

                	cmb_province.load({
                        callback: function(records, operation, success){
                            if (success == true) {
                            	Ext.getCmp('Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-Province').setValue(objReturn.ProvinceID);
                            	cmb_district.load({
                                    params: {
                                        Province: objReturn.ProvinceID
                                    },
                                    callback: function(records, operation, success){
                                        if (success == true) {
                                        	Ext.getCmp('Koltiva.view.IMS.WinImsEventStaffEditWorkArea-Form-District').setValue(objReturn.WorkAreaID);
                                        }
                                    }
                                });
                            }
                        }
                    });
                },
                failure: function(response, action){
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Network Connection Error',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });
        }
    }
});