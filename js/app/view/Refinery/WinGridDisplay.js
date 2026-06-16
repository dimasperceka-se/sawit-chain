/*
* @Author: muhammad hidayaturrohman
* @Date:   2020-11-05
* @Last Modified by:   muhammad hidayaturrohman
* @Last Modified time: 2020-11-05
*/

Ext.define('Koltiva.view.Refinery.WinGridDisplay' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Refinery.WinGridDisplay',
    title: lang('Field to Display'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '40%',
    height: '55%',
    overflowY: 'auto',
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai
        var combo_store_field_display = Ext.create('Koltiva.store.Refinery.CmbGridFieldDisplay');

        //isi formnya
        thisObj.items = [{
            layout: 'column',
            border: false,
            padding:'5 20 5 5',
            items:[{
                columnWidth: 1,
                layout:{
                    type:'vbox',
                    align:'stretch'
                },
                items:[{
                    xtype: 'itemselector',
                    flex:true,
                    id: 'Koltiva.view.Refinery.WinGridDisplay-fieldDisplay',
                    name: 'Koltiva.view.Refinery.WinGridDisplay-fieldDisplay',
                    fieldLabel: '',
                    fromTitle: lang('Available Fields'),
                    toTitle: lang('Visible Fields'),
                    anchor: '100%',
                    store: combo_store_field_display,
                    displayField: 'label',
                    valueField: 'id',
                    value: [],
                    allowBlank: false,
                    msgTarget: 'side'
                }]
            }]
        }]

        this.callParent(arguments);
    },
    buttons: [{
        text: lang('Save'),
        icon: varjs.config.base_url + 'images/icons/new/save.png',
        cls: 'Sfr_BtnFormBlue',
        overCls: 'Sfr_BtnFormBlue-Hover',
        handler: function() {
            Ext.MessageBox.show({
                msg: 'Please wait...',
                progressText: 'Displaying...',
                width: 300,
                wait: true,
                waitConfig: {
                    interval: 200
                },
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });

            //set ke hidden dl semuanya
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colid').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colName').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colAlias').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colAlias').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colAddress').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colProvince').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colDistrict').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colDesa').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colKecamatan').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colLastUpdated').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colStatusPerusahaan').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colTahunTerbentuk').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colPhone').setVisible(false);
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colTotalPermanentEmployee').setVisible(false);

            var visibleField = Ext.getCmp('Koltiva.view.Refinery.WinGridDisplay-fieldDisplay').getValue();
            if(visibleField.length > 0){
                for (var i = 0; i < visibleField.length; i++) {
                    Ext.getCmp(visibleField[i]).setVisible(true);
                }
            }

            //tutup popup
            Ext.getCmp('Koltiva.view.Refinery.WinGridDisplay').close();
            Ext.MessageBox.hide();
        }
    },{
        text: lang('Close'),
        icon: varjs.config.base_url + 'images/icons/new/close.png',
        cls: 'Sfr_BtnFormGrey',
        overCls: 'Sfr_BtnFormGrey-Hover',
        handler: function() {
            Ext.getCmp('Koltiva.view.Refinery.WinGridDisplay').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            //set nilainya detek dari yg visible saja
            var arrDisplay = [];

            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colid').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colid');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colName').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colName');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colAlias').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colAlias');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colAlias').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colAlias');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colAddress').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colAddress');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colProvince').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colProvince');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colDistrict').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colDistrict');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colDesa').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colDesa');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colKecamatan').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colKecamatan');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colLastUpdated').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colLastUpdated');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colStatusPerusahaan').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colStatusPerusahaan');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colTahunTerbentuk').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colTahunTerbentuk');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colPhone').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colPhone');
            if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-colTotalPermanentEmployee').isVisible() == true) arrDisplay.push('Koltiva.view.Refinery.GridMainRefinery-colTotalPermanentEmployee');

            //set nilainya
            Ext.getCmp('Koltiva.view.Refinery.WinGridDisplay-fieldDisplay').setValue(arrDisplay);

        }
    }
});