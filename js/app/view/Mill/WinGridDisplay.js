/*
* @Author: nikolius
* @Date:   2017-08-03 16:36:11
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-04 09:46:49
*/

Ext.define('Koltiva.view.Mill.WinGridDisplay' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Mill.WinGridDisplay',
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
        var combo_store_field_display = Ext.create('Koltiva.store.Mill.CmbGridFieldDisplay');

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
                    id: 'Koltiva.view.Mill.WinGridDisplay-fieldDisplay',
                    name: 'Koltiva.view.Mill.WinGridDisplay-fieldDisplay',
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
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colid').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colName').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colAlias').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colAlias').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colAddress').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colProvince').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colDistrict').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colDesa').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colKecamatan').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colLastUpdated').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colStatusPerusahaan').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colTahunTerbentuk').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colPhone').setVisible(false);
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-colTotalPermanentEmployee').setVisible(false);

            var visibleField = Ext.getCmp('Koltiva.view.Mill.WinGridDisplay-fieldDisplay').getValue();
            if(visibleField.length > 0){
                for (var i = 0; i < visibleField.length; i++) {
                    Ext.getCmp(visibleField[i]).setVisible(true);
                }
            }

            //tutup popup
            Ext.getCmp('Koltiva.view.Mill.WinGridDisplay').close();
            Ext.MessageBox.hide();
        }
    },{
        text: lang('Close'),
        icon: varjs.config.base_url + 'images/icons/new/close.png',
        cls: 'Sfr_BtnFormGrey',
        overCls: 'Sfr_BtnFormGrey-Hover',
        handler: function() {
            Ext.getCmp('Koltiva.view.Mill.WinGridDisplay').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            //set nilainya detek dari yg visible saja
            var arrDisplay = [];

            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colid').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colid');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colName').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colName');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colAlias').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colAlias');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colAlias').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colAlias');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colAddress').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colAddress');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colProvince').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colProvince');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colDistrict').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colDistrict');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colDesa').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colDesa');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colKecamatan').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colKecamatan');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colLastUpdated').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colLastUpdated');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colStatusPerusahaan').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colStatusPerusahaan');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colTahunTerbentuk').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colTahunTerbentuk');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colPhone').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colPhone');
            if(Ext.getCmp('Koltiva.view.Mill.GridMainMill-colTotalPermanentEmployee').isVisible() == true) arrDisplay.push('Koltiva.view.Mill.GridMainMill-colTotalPermanentEmployee');

            //set nilainya
            Ext.getCmp('Koltiva.view.Mill.WinGridDisplay-fieldDisplay').setValue(arrDisplay);

        }
    }
});