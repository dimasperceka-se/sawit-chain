/*
* @Author: Nikolius Lau
* @Date:   2018-08-15 10:21:11
* @Last Modified by:   Nikolius Lau
* @Last Modified time: 2018-08-15 12:33:55
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.view.IMS.WinImsAcqProImportCertFarmer' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinImsAcqProImportCertFarmer',
    title: lang('Import Existing Certified Farmer (Excel) '),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '46%',
    height: '28%',
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

        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.IMS.WinImsAcqProImportCertFarmer-Form',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 160
            },
            fileUpload: true,
        	layout:'form',
            items:[{
            	xtype: 'fileuploadfield',
                fieldLabel: lang('File')+' (type: xlsx)',
                id: 'Koltiva.view.IMS.WinImsAcqProImportCertFarmer-Form-FileImport',
                name: 'Koltiva.view.IMS.WinImsAcqProImportCertFarmer-Form-FileImport',
                buttonText: 'Browse',
                cls: 'Sfr_BtnGridGreen',
                overCls: 'Sfr_BtnGridGreen-Hover',
                allowBlank: false,
                listeners: {
                	'change': function(fb, v){
	                	var FormNya = Ext.getCmp('Koltiva.view.IMS.WinImsAcqProImportCertFarmer-Form').getForm();
	                	FormNya.submit({
	                		url: m_crud+'import_cert_farmer_selection',
	                        waitMsg: 'Sending and Importing file...',
	                        params: {IMSID: thisObj.viewVar.IMSID},
	                        success: function(fp, o){
	                        	var r = Ext.decode(o.response.responseText);

	                        	Ext.MessageBox.show({
		                            title: lang('Success'),
		                            msg: lang('Data Imported'),
		                            buttons: Ext.MessageBox.OK,
		                            animateTarget: 'mb9',
		                            icon: 'ext-mb-success'
		                        });

		                        thisObj.viewVar.CallerStore.load();
	                        },
	                        failure: function(fp, o){
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
            },{
            	id:'Koltiva.view.IMS.WinImsAcqProImportCertFarmer-Form-TemplateUrl',
                html:'<a style="text-decoration:underline;" href="'+varjs.config.base_url+'api/ims/ims_import_cert_farmer_selection/'+thisObj.viewVar.IMSID+'" target="_blank">Download Template File for Import</a>'
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