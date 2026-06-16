Ext.onReady(function () {

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        renderTo: 'ext-content',
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        items: [{
            xtype: 'form',
            fileUpload: true,
            enctype: 'multipart/form-data',
            id: 'upload_file',
            items: [{
                xtype: 'fileuploadfield',
                fieldLabel: 'File (.zip)',
                labelWidth: 120,
                anchor: '50%',
                id: 'file',
                padding: 5,
                name: 'File',
                buttonText: 'Browse',
                listeners: {
                    'change': function (fb, v) {
                        var form = Ext.getCmp('upload_file').getForm();
                        form.submit({
                            url: m_crud + 'upload',
                            waitMsg: 'Sending and insert file...',
                            failure: function(fp, o) {
                                Ext.MessageBox.alert(o.result.infos, o.result.message);
                            }
                        });
                    }
                }
            },
			{
				xtype: 'fieldset',
				padding: '10 10 10 10',
				items: [{
					items: [{
						xtype: 'container',
                        //html: '<p> </p><p>Allowed files : </p> <ol><li>File name : ktv_farmer.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_family.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_ppiscore2012.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_nutrition.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_farmer_post_harvest.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_farmer_garden.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_farmer_financial.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_farmer_garden_status.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_farmer_other_land.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_certification.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_certification_audit_log.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_certification_signature.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_cpg_batch_trainings_attendance.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_village.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_village_crop.[Date Y-m-d]-[UserId].csv</li><li>File name : ktv_village_infrastructure.[Date Y-m-d]-[UserId].csv</li></ol><p>Example : ktv_farmer.2015-12-21-176.csv<p>',
						html: '<p> </p><p>Allowed files : </p> <ul><li>.zip</li></ul>',
					}]
				}]
			}]
        }]
    });
});
