/*
* @Author: nikolius
* @Date:   2018-04-19 10:13:51
* @Last Modified by:   nikolius
* @Last Modified time: 2018-04-23 14:20:55
*/

Ext.define('Koltiva.view.Staffuser.WinFormImportFarmer' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staffuser.WinFormImportFarmer',
    title: lang('Farmer Assignment Import'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '50%',
    height: '50%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    bodyStyle: {
        "background-color": "#F0F0F0"
    },
    style: 'background-color:#F0F0F0;',
    padding: 6,
    scrollOffset: 20,
    initComponent: function() {
        var thisObj = this;

        //STORE
        thisObj.StoreGridMappingFA = Ext.create('Koltiva.store.IMS.GridMappingFA', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        thisObj.StoreCmbFilterFA = Ext.create('Koltiva.store.IMS.CmbFilterFA', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Staffuser.WinFormImportFarmer-Form',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 250
            },
            fileUpload: true,
            layout: 'form',
            items: [{
                xtype: 'fileuploadfield',
                fieldLabel: lang('File') + ' (type: xlsx)',
                labelWidth: 125,
                id: 'Koltiva.view.Staffuser.WinFormImportFarmer-Form-FileImport',
                name: 'Koltiva.view.Staffuser.WinFormImportFarmer-Form-FileImport',
                style:'padding-left : 20px; padding-right:20px',
                buttonText: 'Browse',
                allowBlank: false,
                listeners: {
                    'change': function (fb, v) {
                        var form = Ext.getCmp('Koltiva.view.Staffuser.WinFormImportFarmer-Form').getForm();
                        form.submit({
                            url: m_api + '/staffuser/import_farmer_assign',
                            waitMsg: 'Sending and importing file...',
                            params: {
                                StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                                StaffID:thisObj.viewVar.StaffID
                            },
                            success: function (fp, o) {
                                var r = Ext.decode(o.response.responseText);

                                Ext.MessageBox.show({
                                    title: lang('Success'),
                                    msg: r.message+ '. ( Inserted : '+r.Insert+ ', Exist : '+r.Exist+' )',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                thisObj.StoreGridMappingFA.load();
                            },
                            failure: function (fp, o) {
                                var r = Ext.decode(o.response.responseText);
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }
                }
            }, {
                //id:'Koltiva.view.Grower.FormMainGrower-ConsentLetterUrl',
                //html:'<a style="text-decoration:underline;" href="'+varjs.config.base_url+'api/files/template-import-ims-candidate-map-fa.xlsx" target="_blank">Download Template File for Import</a>'
                html: '<a style="text-decoration:underline;" href="' + varjs.config.base_url + 'api/staffuser/farmer_assign_data/' + thisObj.viewVar.StaffAssignmentID+'/'+thisObj.viewVar.StaffID + '" target="_blank">Download Template File for Import</a>'
            }]
        }];

        thisObj.buttons = [{
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    }
});