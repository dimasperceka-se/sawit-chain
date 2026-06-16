/******************************************
 *  Author : hasbycs@gmail.com
 *  Created On : 2021-10-06
 *  File : MainForm.js
 *******************************************/
/*
 Param2 yg diperlukan ketika load View ini
 - OpsiDisplay
 - TrainFarmerID
 */

Ext.define('Koltiva.view.Coaching.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Coaching.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;
            // document.getElementById('Sfr_IdBoxInfoDataGrid').style.display = 'none';
            // document.getElementById('ContentTopBar').style.display = 'none';

            //Nilai default
            //Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-IsCert2').setValue(true);

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {

                if (thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-BtnSave').setVisible(false);
                    Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingPhotoInput').setVisible(false);
                    Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignatureInput').setVisible(false);
                }

                Ext.getCmp('isCertifiedPanel').setReadOnly(true);
                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-IMSID').setReadOnly(true);

                //load formnya
                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form').getForm().load({
                    url: m_api + '/coaching/coaching_form_open',
                    method: 'GET',
                    params: {
                        CoachingID: thisObj.viewVar.CoachingID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        //Set Read Only
                        Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-FarmerSupplierIDAuto').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-PersonIDAuto').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-Username').setReadOnly(true);

                        if (r.data.CoachingPhoto != null) {
                            Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingPhoto').update('<a href="' + r.data.CoachingPhoto + '" data-lightbox="image-1" data-title="Coaching Photo" title="View Coaching Photo"><img src="' + r.data.CoachingPhoto + '" style="height:150px;margin:0px 5px 5px 0px;float:right;" /></a>');
                        } else {
                            Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingPhoto').update('<img src="' + m_api_base_url + '/images/no-image-icon-port.png" style="height:150px;margin:0px 5px 5px 0px;float:right;" />');
                        }
                        if (r.data.CoachingRecipientSignature != null) {
                            Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignature').update('<a href="' + r.data.CoachingRecipientSignature + '" data-lightbox="image-1" data-title="Coaching Recipient Signature" title="View Coaching Recipient Signature"><img src="' + r.data.CoachingRecipientSignature + '" style="height:150px;margin:0px 5px 5px 0px;float:right;" /></a>');
                        } else {
                            Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignature').update('<img src="' + m_api_base_url + '/images/signature.png" style="height:150px;margin:0px 5px 5px 0px;float:right;" />');
                        }
                    },
                    failure: function (form, action) {
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

            if (thisObj.viewVar.OpsiDisplay != 'insert') {
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
    initComponent: function () {
        var thisObj = this;
        var labelWidth = 220;

        //Store yg dipakai =============================================================== (Begin)
        thisObj.CmbAutoStaffOfficer = Ext.create('Koltiva.store.ComboGeneral.CmbAutoStaff');
        thisObj.CmbAutoFarmer = Ext.create('Koltiva.store.ComboGeneral.CmbAutoFarmer');
        thisObj.cmb_ims = Ext.create('Koltiva.store.Coaching.CoachingIMS');

        thisObj.StoreMainFormDetailTask = Ext.create('Koltiva.store.Coaching.CoachingTask', {
            storeVar: {
                CoachingID: thisObj.viewVar.CoachingID
            }
        });
        //Store yg dipakai =============================================================== (End)

        thisObj.ObjPanelMain = Ext.create('Ext.panel.Panel', {
            title: lang('Coaching Form'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            collapsible: true,
            style: 'margin-top:0px;padding-top:0px;',
            items: [{
                    xtype: 'form',
                    id: 'Koltiva.view.Coaching.MainForm-Form',
                    fileUpload: true,
                    buttonAlign: 'right',
                    cls: 'Sfr_PanelSubLayoutForm',
                    items: [{
                            layout: 'column',
                            border: false,
                            padding: 10,
                            items: [{
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    style: 'margin-right:20px;',
                                    items: [{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-CoachingID',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-CoachingID',
                                            inputType: 'hidden'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-ActivityID',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-ActivityID',
                                            inputType: 'hidden'
                                        }, {
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Coaching IMS'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            msgTarget: 'side',
                                            id:'isCertifiedPanel',
                                            columns: 2,
                                            items: [{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.Coaching.MainForm-Form-isCertified',
                                                inputValue: '1',
                                                id: 'Koltiva.view.Coaching.MainForm-Form-isCertified1',
                                                listeners: {
                                                    change: function () {
                                                        if(thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update'){
                                                            Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-IMSID').setReadOnly(true);
                                                        }else{
                                                            if(this.checked == true){
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-IMSID').setReadOnly(false);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-IMSID').setReadOnly(true);
                                                            }
                                                        }
                                                        return false;
                                                    }
                                                }
                                            }, {
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.Coaching.MainForm-Form-isCertified',
                                                inputValue: '2',
                                                id: 'Koltiva.view.Coaching.MainForm-Form-isCertified2',
                                                listeners: {
                                                    change: function () {
                                                        if(thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update'){
                                                            Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-IMSID').setReadOnly(true);
                                                        }else{
                                                            if(this.checked == true){
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-IMSID').setReadOnly(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-IMSID').setReadOnly(false);
                                                            }
                                                        }
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-IMSID',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-IMSID',
                                            store: thisObj.cmb_ims,
                                            fieldLabel: lang('IMS Event'),
                                            labelWidth:labelWidth,
                                            queryMode: 'local',
                                            displayField: 'label',
                                            readOnly:true,
                                            valueField: 'id'
                                        }, {
                                            xtype: 'combo',
                                            store: thisObj.CmbAutoFarmer,
                                            id: 'Koltiva.view.Coaching.MainForm-Form-FarmerSupplierIDAuto',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-FarmerSupplierIDAuto',
                                            displayField: 'label',
                                            labelWidth:labelWidth,
                                            valueField: 'id',
                                            fieldLabel: lang('Farmer Name'),
                                            baseCls: 'Sfr_FormInputMandatory',
                                            typeAhead: false,
                                            hideTrigger: true,
                                            anchor: '100%',
                                            emptyText: lang('Enter farmer name or farmer ID to search'),
                                            listConfig: {
                                                loadingText: lang('Searching'),
                                                emptyText: lang('No matching data found'),
                                                getInnerTpl: function () {
                                                    return '<div class="search-item">' + '{displayid} - {name}' + '</div>';
                                                }
                                            },
                                            pageSize: 10,
                                            listeners: {
                                                select: function (combo, selection) {
                                                    var rec = selection[0];
                                                    Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-SupplierID').setValue(rec.get('id'));
                                                }
                                            }
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-SupplierID',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-SupplierID',
                                            inputType: 'hidden'
                                        }, {
                                            xtype: 'combo',
                                            store: thisObj.CmbAutoStaffOfficer,
                                            id: 'Koltiva.view.Coaching.MainForm-Form-PersonIDAuto',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-PersonIDAuto',
                                            displayField: 'label',
                                            valueField: 'id',
                                            fieldLabel: lang('User Staff FA'),
                                            baseCls: 'Sfr_FormInputMandatory',
                                            typeAhead: false,
                                            hideTrigger: true,
                                            anchor: '100%',
                                            emptyText: lang('Enter staff name or partner to search'),
                                            listConfig: {
                                                loadingText: lang('Searching'),
                                                emptyText: lang('No matching data found'),
                                                getInnerTpl: function () {
                                                    return '<div class="search-item">' + '{name} ({partner})' + '</div>';
                                                }
                                            },
                                            pageSize: 10,
                                            listeners: {
                                                select: function (combo, selection) {
                                                    var rec = selection[0];
                                                    Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-PersonID').setValue(rec.get('id'));
                                                }
                                            }
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-PersonID',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-PersonID',
                                            inputType: 'hidden'
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-Username',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-Username',
                                            inputType: 'hidden'
                                        }, {
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Coaching Recipient'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            msgTarget: 'side',
                                            columns: 2,
                                            items: [{
                                                    boxLabel: lang('Registered Farmer'),
                                                    name: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipient',
                                                    inputValue: '1',
                                                    id: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipient1',
                                                    listeners: {
                                                        change: function () {
                                                            if(this.checked == true){
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-FarmerWorkerName').setVisible(false);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-FarmerWorkerName').setVisible(true);
                                                            }
                                                            return false;
                                                        }
                                                    }
                                                }, {
                                                    boxLabel: lang('Farmer Worker'),
                                                    name: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipient',
                                                    inputValue: '2',
                                                    id: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipient2',
                                                    listeners: {
                                                        change: function () {
                                                            if(this.checked == true){
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-FarmerWorkerName').setVisible(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-FarmerWorkerName').setVisible(false);
                                                            }
                                                            return false;
                                                        }
                                                    }
                                                }, {
                                                    boxLabel: lang('Household Member'),
                                                    name: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipient',
                                                    inputValue: '3',
                                                    id: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipient3',
                                                    listeners: {
                                                        change: function () {
                                                            if(this.checked == true){
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-FarmerWorkerName').setVisible(true);
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-FarmerWorkerName').setVisible(false);
                                                            }
                                                            return false;
                                                        }
                                                    }
                                                }]
                                        }, {
                                            xtype: 'textfield',
                                            width: 110,
                                            id: 'Koltiva.view.Coaching.MainForm-Form-FarmerWorkerName',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-FarmerWorkerName',
                                            fieldLabel: lang('Coaching Recipient Name'),
                                            labelWidth: labelWidth,
                                            hidden: true
                                        }, {
                                            xtype: 'datefield',
                                            width: 110,
                                            id: 'Koltiva.view.Coaching.MainForm-Form-CoachingDate',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-CoachingDate',
                                            format: 'Y-m-d',
                                            fieldLabel: lang('Coaching Date'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        }, {
                                            xtype: 'timefield',
                                            width: 110,
                                            id: 'Koltiva.view.Coaching.MainForm-Form-TimeStart',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-TimeStart',
                                            format: 'H:i:s',
                                            fieldLabel: lang('Time Start'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        }, {
                                            xtype: 'timefield',
                                            width: 110,
                                            id: 'Koltiva.view.Coaching.MainForm-Form-TimeEnd',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-TimeEnd',
                                            format: 'H:i:s',
                                            fieldLabel: lang('Time End'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-Latitude',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-Latitude',
                                            fieldLabel: lang('Latitude'),
                                            labelWidth: labelWidth
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-Longitude',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-Longitude',
                                            fieldLabel: lang('Longitude'),
                                            labelWidth: labelWidth
                                        }, {
                                            xtype: 'numericfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-PhSample1',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-PhSample1',
                                            fieldLabel: lang('PhSample1'),
                                            labelWidth: labelWidth
                                        }, {
                                            xtype: 'numericfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-PhSample2',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-PhSample2',
                                            fieldLabel: lang('PhSample2'),
                                            labelWidth: labelWidth
                                        }, {
                                            xtype: 'numericfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-PhSample3',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-PhSample3',
                                            fieldLabel: lang('PhSample3'),
                                            labelWidth: labelWidth
                                        }, {
                                            xtype: 'textareafield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-Remark',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-Remark',
                                            fieldLabel: lang('Remark')
                                        }]
                                }, {
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    style: 'margin-left:20px;',
                                    items: [{
                                            xtype: 'panel',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-CoachingPhoto',
                                            html: '<img src="' + m_api_base_url + '/assets/images/no-image-icon-port.png" style="height:150px;margin:0px;float:right;" />'
                                        }, {
                                            html: '<div style="height:18px;">&nbsp;</div>'
                                        }, {
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang("Coaching Photo"),
                                            id: 'Koltiva.view.Coaching.MainForm-Form-CoachingPhotoInput',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-CoachingPhotoInput',
                                            baseCls: 'Sfr_FormBrowseBtn',
                                            labelWidth: labelWidth,
                                            buttonText: 'Browse',
                                            listeners: {
                                                'change': function (fb, v) {
                                                    Ext.getCmp('Koltiva.view.Coaching.MainForm-Form').getForm().submit({
                                                        url: m_api + '/coaching/coaching_photo',
                                                        clientValidation: false,
                                                        params: {
                                                            OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                                            CoachingID: Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingID').getValue()
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                                                //Insert
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingPhoto').update('<img src="' + m_api_base_url + '/files/tmp/' + o.result.file + '" style="height:150px;margin:0px;float:right;" />');
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingPhotoOld').setValue(o.result.file);
                                                            } else {
                                                                //Update / View
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingPhoto').update('<img src="' + o.result.file + '" style="height:150px;margin:0px;float:right;" />');
                                                            }
                                                        },
                                                        failure: function (fp, o) {
                                                            Ext.MessageBox.show({
                                                                title: lang('Information'),
                                                                msg: lang('Upload process failed, please upload a image file'),
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        }
                                                    });
                                                }
                                            }
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-CoachingPhotoOld',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-CoachingPhotoOld',
                                            inputType: 'hidden'
                                        }, {
                                            xtype: 'panel',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignature',
                                            html: '<img src="' + m_api_base_url + '/assets/images/signature.png" style="height:150px;margin:0px;float:right;" />'
                                        }, {
                                            html: '<div style="height:18px;">&nbsp;</div>'
                                        }, {
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang("Coaching Recipient Signature"),
                                            id: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignatureInput',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignatureInput',
                                            baseCls: 'Sfr_FormBrowseBtn',
                                            labelWidth: labelWidth,
                                            buttonText: 'Browse',
                                            listeners: {
                                                'change': function (fb, v) {
                                                    Ext.getCmp('Koltiva.view.Coaching.MainForm-Form').getForm().submit({
                                                        url: m_api + '/coaching/coaching_signature',
                                                        clientValidation: false,
                                                        params: {
                                                            OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                                            CoachingID: Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingID').getValue()
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                                                //Insert
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignature').update('<img src="' + m_api_base_url + '/files/tmp/' + o.result.file + '" style="height:150px;margin:0px;float:right;" />');
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignatureOld').setValue(o.result.file);
                                                            } else {
                                                                //Update / View
                                                                Ext.getCmp('Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignature').update('<img src="' + o.result.file + '" style="height:150px;margin:0px;float:right;" />');
                                                            }
                                                        },
                                                        failure: function (fp, o) {
                                                            Ext.MessageBox.show({
                                                                title: lang('Information'),
                                                                msg: lang('Upload process failed, please upload a image file'),
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        }
                                                    });
                                                }
                                            }
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignatureOld',
                                            name: 'Koltiva.view.Coaching.MainForm-Form-CoachingRecipientSignatureOld',
                                            inputType: 'hidden'
                                        }]
                                }]
                        }],
                    buttons: [{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                            text: lang('Save'),
                            cls: 'Sfr_BtnFormBlue',
                            overCls: 'Sfr_BtnFormBlue-Hover',
                            id: 'Koltiva.view.Coaching.MainForm-Form-BtnSave',
                            handler: function () {
                                var Formnya = Ext.getCmp('Koltiva.view.Coaching.MainForm-Form').getForm();

                                if (Formnya.isValid()) {
                                    Formnya.submit({
                                        url: m_api + '/coaching/coaching_data',
                                        method: 'POST',
                                        waitMsg: lang('Saving data'),
                                        params: {
                                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
                                        },
                                        success: function (fp, o) {
                                            var r = Ext.decode(o.response.responseText);

                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: lang('Data saved'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-success',
                                                fn: function (btn) {
                                                    if (btn == 'ok') {
                                                        Ext.getCmp('Koltiva.view.Coaching.MainForm').destroy(); //destory current view
                                                        var FormMain = [];

                                                        if (Ext.getCmp('Koltiva.view.Coaching.MainForm') == undefined) {
                                                            FormMain = Ext.create('Koltiva.view.Coaching.MainForm', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'update',
                                                                    CoachingID: r.CoachingID
                                                                }
                                                            });
                                                        } else {
                                                            //destroy, create ulang
                                                            Ext.getCmp('Koltiva.view.Coaching.MainForm').destroy();
                                                            FormMain = Ext.create('Koltiva.view.Coaching.MainForm', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'update',
                                                                    CoachingID: r.CoachingID
                                                                }
                                                            });
                                                        }
                                                    }
                                                }
                                            });
                                        },
                                        failure: function (fp, o) {
                                            try {
                                                var r = Ext.decode(o.response.responseText);
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: r.message,
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            } catch (err) {
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
                                } else {
                                    Ext.MessageBox.show({
                                        title: lang('Attention'),
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

        thisObj.ContextMenuDetailTask = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.Coaching.MainForm-DetailTask').getSelectionModel().getSelection()[0];
                        var WinFormCoachingTask = Ext.create('Koltiva.view.Coaching.WinFormCoachingTask', {
                            viewVar: {
                                CallerStore: thisObj.StoreMainFormDetailTask,
                                OpsiDisplay: 'view',
                                ActivityNCID: sm.get('ActivityNCID')
                            }
                        });
                        if (!WinFormCoachingTask.isVisible()) {
                            WinFormCoachingTask.center();
                            WinFormCoachingTask.show();
                        } else {
                            WinFormCoachingTask.close();
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_update,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.Coaching.MainForm-DetailTask').getSelectionModel().getSelection()[0];
                        var WinFormCoachingTask = Ext.create('Koltiva.view.Coaching.WinFormCoachingTask', {
                            viewVar: {
                                CallerStore: thisObj.StoreMainFormDetailTask,
                                OpsiDisplay: 'update',
                                ActivityNCID: sm.get('ActivityNCID')
                            }
                        });
                        if (!WinFormCoachingTask.isVisible()) {
                            WinFormCoachingTask.center();
                            WinFormCoachingTask.show();
                        } else {
                            WinFormCoachingTask.close();
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_delete,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.Coaching.MainForm-DetailTask').getSelectionModel().getSelection()[0];

                        Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/coaching/coaching_task',
                                    method: 'DELETE',
                                    params: {
                                        ActivityNCID: sm.get('ActivityNCID')
                                    },
                                    success: function (response, opts) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data deleted'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //refresh store
                                        thisObj.StoreMainFormDetailTask.load();
                                    },
                                    failure: function (rp, o) {
                                        try {
                                            var r = Ext.decode(rp.responseText);
                                            Ext.MessageBox.show({
                                                title: lang('Error'),
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        } catch (err) {
                                            Ext.MessageBox.show({
                                                title: lang('Error'),
                                                msg: lang('Connection Error'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    }
                                });
                            }
                        });

                    }
                }]
        });

        thisObj.ObjPanelDetail = [];
        if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {

            thisObj.ObjPanelDetail = Ext.create('Ext.panel.Panel', {
                title: lang('Coaching Task'),
                frame: true,
                cls: 'Sfr_PanelLayoutForm',
                collapsible: true,
                style: 'margin-top:0px;padding-top:0px;',
                items: [{
                        xtype: 'grid',
                        id: 'Koltiva.view.Coaching.MainForm-DetailTask',
                        style: 'border:1px solid #CCC;',
                        cls: 'Sfr_GridNew',
                        loadMask: true,
                        selType: 'rowmodel',
                        store: thisObj.StoreMainFormDetailTask,
                        enableColumnHide: false,
                        viewConfig: {
                            deferEmptyText: false,
                            emptyText: GetDefaultContentNoData()
                        },
                        dockedItems: [{
                                xtype: 'pagingtoolbar',
                                store: thisObj.StoreMainFormDetailTask,
                                dock: 'bottom',
                                displayInfo: true,
                                displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
                            }, {
                                xtype: 'toolbar',
                                dock: 'top',
                                items: [{
                                        xtype: 'button',
                                        icon: varjs.config.base_url + 'images/icons/new/add.png',
                                        text: lang('Add Task'),
                                        hidden: m_act_add,
                                        cls: 'Sfr_BtnGridGreen',
                                        overCls: 'Sfr_BtnGridGreen-Hover',
                                        handler: function () {
                                            var WinFormCoachingTask = Ext.create('Koltiva.view.Coaching.WinFormCoachingTask', {
                                                viewVar: {
                                                    CallerStore: thisObj.StoreMainFormDetailTask,
                                                    OpsiDisplay: 'insert',
                                                    CoachingID: thisObj.viewVar.CoachingID,
                                                    ActivityID : Ext.getCmp("Koltiva.view.Coaching.MainForm-Form-ActivityID").getValue()
                                                }
                                            });
                                            if (!WinFormCoachingTask.isVisible()) {
                                                WinFormCoachingTask.center();
                                                WinFormCoachingTask.show();
                                            } else {
                                                WinFormCoachingTask.close();
                                            }
                                        }
                                    }, {
                                        xtype: 'tbspacer',
                                        flex: 1
                                    }/*, {
                                     icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                     text: lang('Print Attendance'),
                                     scope: this,
                                     cls: 'Sfr_BtnGridPaleBlue',
                                     overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                     handler: function () {
                                     var url_cetak = m_api + '/printout/train_farmer_attendance/' + thisObj.viewVar.TrainFarmerID + '/' + Ext.getCmp('Koltiva.view.Coaching.MainForm-DetailTask-CmbFilterTrainingDays').getValue();
                                     preview_cetak_surat(url_cetak);
                                     }
                                     }*/]
                            }],
                        columns: [{
                                text: '',
                                xtype: 'actioncolumn',
                                width: '3%',
                                items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                                            thisObj.ContextMenuDetailTask.showAt(e.getXY());
                                        }
                                    }]
                            }, {
                                text: 'No',
                                width: '3%',
                                xtype: 'rownumberer'
                            }, {
                                text: lang('CoachingID'),
                                dataIndex: 'CoachingID',
                                hidden: true
                            }, {
                                text: lang('CoachingTopicID'),
                                dataIndex: 'CoachingTopicID',
                                hidden: true
                            },
                            {
                                text: lang('CoachingCategoryID'),
                                dataIndex: 'CoachingCategoryID',
                                hidden: true
                            },
                            {
                                text: lang('Deadline'),
                                dataIndex: 'Deadline',
                                flex: 1
                            },
                            {
                                text: lang('Topic'),
                                dataIndex: 'CoachingTopic',
                                flex: 2
                            },
                            {
                                text: lang('Subtopic'),
                                dataIndex: 'Subtopic',
                                flex: 1
                            },
                            {
                                text: lang('Urgently Status'),
                                dataIndex: 'UrgentlyStatus',
                                flex: 1,
                                renderer: function (value) {
                                    var RetVal;
                                    if(value != null && value != ''){
                                        RetVal = lang(value);
                                    }else{
                                        RetVal = '-';
                                    }
                                    return RetVal;
                                }
                            }, {
                                text: lang('Finding'),
                                dataIndex: 'Finding',
                                flex: 3,
                                renderer: function (value) {
                                    var ContentFinding = '';
                                    ContentFinding = ` 
                                            <div class="DivContentGridColumn" style="font: normal 13px/17px 'Abel',Helvetica,Arial,sans-serif;">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="100%" valign="top">
                                                            ` + value + `
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>`;
                                    return ContentFinding;
                                }
                            }, {
                                text: lang('Activity Type'),
                                dataIndex: 'ActivityType',
                                flex: 1,
                                renderer: function (value) {
                                    var RetVal;
                                    if(value != null && value != ''){
                                        RetVal = lang(value);
                                    }else{
                                        RetVal = '-';
                                    }
                                    return RetVal;
                                }
                            }, {
                                text: lang('Recommendation'),
                                dataIndex: 'Recommendation',
                                flex: 3,
                                renderer: function (value) {
                                    var ContentRecom = '';
                                    ContentRecom = ` 
                                            <div class="DivContentGridColumn" style="font: normal 13px/17px 'Abel',Helvetica,Arial,sans-serif;">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="100%" valign="top">
                                                            ` + value + `
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>`;
                                    return ContentRecom;
                                }
                            }, {
                                text: lang('Status'),
                                dataIndex: 'Status',
                                flex: 1,
                                renderer: function (value) {
                                    var RetVal;
                                    if(value != null && value != ''){
                                        RetVal = lang(value);
                                    }else{
                                        RetVal = '-';
                                    }
                                    return RetVal;
                                }
                            }]
                    }]
            });
        }

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [
            {
                xtype: 'panel',
                border: false,
                layout: {
                    type: 'hbox'
                },
                items: [{
                        id: 'Koltiva.view.Coaching.MainForm-labelInfoInsert',
                        html: '<div id="header_title_farmer">' + lang('Coaching Data') + '</div>'
                    }]
            }, 
            {
                items: [{
                        id: 'Koltiva.view.Coaching.MainForm-LinkBackToList',
                        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Coaching.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Coaching List') + '</a></li></div>'
                    }]
            }, 
            {
                html: '<br />'
            }, 
            {
                layout: 'column',
                border: false,
                items: [{
                        //LEFT CONTENT
                        columnWidth: 1,
                        items: [
                            thisObj.ObjPanelMain,
                            {
                                html: '<br>'
                            },
                            thisObj.ObjPanelDetail
                        ]
                    }]
            }];
        //========================================================== LAYOUT UTAMA (End) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.Coaching.MainForm').destroy(); //destory current view
        var GridMainCoaching = [];
        if (Ext.getCmp('Koltiva.view.Coaching.MainGrid') == undefined) {
            GridMainCoaching = Ext.create('Koltiva.view.Coaching.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Coaching.MainGrid').destroy();
            GridMainCoaching = Ext.create('Koltiva.view.Coaching.MainGrid');
        }
    }
});
