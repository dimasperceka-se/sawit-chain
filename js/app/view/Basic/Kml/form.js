Ext.define('Koltiva.view.Basic.Kml.form',{
    id: 'Koltiva.view.Basic.Kml.form',
    extend:'Ext.form.Panel',
    autoScroll: true,
    bodyPadding: 5,
    fileUpload: true,
    layout:'anchor',
    enctype: 'multipart/form-data',
    url: m_api + '/basic/kml',
    fieldDefaults: {
        labelAlign: 'left',
        labelWidth: 100,
        width: '100%',
        anchor: '100%'
    },
    items: [
    {
        xtype:'panel',
        padding: 5,
        style: 'border:2px solid #D6EDA4',
        items:[
        {
            id: 'Koltiva.view.Basic.Kml.form.ID',
            xtype: 'hiddenfield',
            name: 'ID'
        }, 
        {
            id: 'Koltiva.view.Basic.Kml.form.Name',
            name: 'Name',
            xtype: 'textfield',
            fieldLabel: lang('Name'),
            allowBlank: false
        }, 
        {
            id: 'Koltiva.view.Basic.Kml.form.CategoryID',
            name: 'CategoryID',
            xtype: 'combo',
            fieldLabel: lang('Category'),
            store: Ext.create('Koltiva.store.Basic.Kml.category'),
            displayField: 'label',
            valueField: 'id',
            queryMode: 'local',
            allowBlank: false
        }, 
        {
            id: 'Koltiva.view.Basic.Kml.form.ProvinceID',
            name: 'ProvinceID',
            xtype: 'combo',
            fieldLabel: lang('Province'),
            store: Ext.create('Koltiva.store.Basic.Kml.provinsi'),
            displayField: 'label',
            valueField: 'id',
            queryMode: 'local',
            allowBlank: true,
            listeners: {
                change: function(cb, nv, ov) {
                    Ext.getCmp('Koltiva.view.Basic.Kml.form.DistrictID').store.load({
                        params: {
                            ProvinceID: nv
                        }
                    });
                    Ext.getCmp('Koltiva.view.Basic.Kml.form.DistrictID').enable();
                }
            }
        }, {
            id: 'Koltiva.view.Basic.Kml.form.DistrictID',
            name: 'DistrictID',
            xtype: 'combo',
            fieldLabel: lang('District'),
            disabled: 'true',
            store: Ext.create('Koltiva.store.Basic.Kml.kabupaten'),
            displayField: 'label',
            valueField: 'id',
            queryMode: 'local',
            allowBlank: true,
            listeners: {
                change: function(cb, nv, ov) {
                    Ext.getCmp('Koltiva.view.Basic.Kml.form.SubDistrictID').store.load({
                        params: {
                            DistrictID: nv
                        }
                    });
                    Ext.getCmp('Koltiva.view.Basic.Kml.form.SubDistrictID').enable();
                    // ds.getProxy().setExtraParam("district", Ext.getCmp('Koltiva.view.Basic.Kml.form.Kabupaten').getValue())
                }
            }
        }, {
            id: 'Koltiva.view.Basic.Kml.form.SubDistrictID',
            name: 'SubDistrictID',
            xtype: 'combo',
            fieldLabel: lang('SubDistrict'),
            store: Ext.create('Koltiva.store.Basic.Kml.kecamatan'),
            displayField: 'label',
            valueField: 'id',
            queryMode: 'local',
            disabled: 'true',
            listeners: {
                change: function(cb, nv, ov) {
                    Ext.getCmp('Koltiva.view.Basic.Kml.form.VillageID').store.load({
                        params: {
                            SubDistrictID: nv
                        }
                    });
                    Ext.getCmp('Koltiva.view.Basic.Kml.form.VillageID').enable();
                }
            }
        }, {
            id: 'Koltiva.view.Basic.Kml.form.VillageID',
            name: 'VillageID',
            xtype: 'combo',
            fieldLabel: lang('Village'),
            store: Ext.create('Koltiva.store.Basic.Kml.desa'),
            displayField: 'label',
            disabled: 'true',
            valueField: 'id',
            queryMode: 'local'
        }, 
        {
            id: 'Koltiva.view.Basic.Kml.form.FileName',
            name: 'FileName',
            xtype: 'textfield',
            fieldLabel: lang('FileName'),
            disabled: true
        }, 
        {
            xtype: 'filefield',
            name: 'kml',
            fieldLabel: 'KML',
            msgTarget: 'side',
            allowBlank: true,
            anchor: '100%',
            buttonText: 'Select File...'
        },
        {
            id: 'Koltiva.view.Basic.Kml.form.Color',
            name: 'Color',
            xtype: 'textfield',
            fieldLabel: lang('Color'),
            allowBlank: true
        }, 
        ]
    }
    ],
        buttons: [{
            id: 'Koltiva.view.Basic.Kml.form.saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                if (form.isValid()) {
                    form.submit({
                        // url: m_api + '/basic/kml',
                        waitMsg: lang('Sending data...'),
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', lang('Data saved.'));
                            var grid = Ext.getCmp('Koltiva.view.Basic.Kml.list');
                            win.hide(this, function() {
                                grid.store.load();
                            });
                        },
                        failure: function(form, action) {
                            Ext.Msg.alert('Failed', action.result.msg);
                        }
                    });
                }
            }
        }, {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                win.hide();
            }
        }]
});
