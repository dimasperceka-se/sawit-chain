/*
* @Author: nikolius
* @Date:   2017-07-19 10:35:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-06 16:17:06
*/

Ext.define('Koltiva.view.SME.WinGridDisplay' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.SME.WinGridDisplay',
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
        var combo_store_field_display = Ext.create('Koltiva.store.SME.CmbGridFieldDisplay');

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
                    id: 'Koltiva.view.SME.WinGridDisplay-fieldDisplay',
                    name: 'Koltiva.view.SME.WinGridDisplay-fieldDisplay',
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
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colid').setVisible(false);
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colFarmerName').setVisible(false);
            // Ext.getCmp('Koltiva.view.SME.GridMainTrader-colMemberRole').setVisible(false);
            // Ext.getCmp('Koltiva.view.SME.GridMainTrader-colBirthdate').setVisible(false);
            // Ext.getCmp('Koltiva.view.SME.GridMainTrader-colAge').setVisible(false);
            // Ext.getCmp('Koltiva.view.SME.GridMainTrader-colHandphone').setVisible(false);
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colDateCollection').setVisible(false);
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colProvince').setVisible(false);
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colDistrict').setVisible(false);
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colDesa').setVisible(false);
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colKecamatan').setVisible(false);
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colEnumerator').setVisible(false);
            Ext.getCmp('Koltiva.view.SME.GridMainTrader-colLastUpdated').setVisible(false);

            var visibleField = Ext.getCmp('Koltiva.view.SME.WinGridDisplay-fieldDisplay').getValue();
            if(visibleField.length > 0){
                for (var i = 0; i < visibleField.length; i++) {
                    Ext.getCmp(visibleField[i]).setVisible(true);
                }
            }

            //tutup popup
            Ext.getCmp('Koltiva.view.SME.WinGridDisplay').close();
            Ext.MessageBox.hide();
        }
    },{
        text: lang('Close'),
        icon: varjs.config.base_url + 'images/icons/new/close.png',
        cls: 'Sfr_BtnFormGrey',
        overCls: 'Sfr_BtnFormGrey-Hover',
        handler: function() {
            Ext.getCmp('Koltiva.view.SME.WinGridDisplay').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            //set nilainya detek dari yg visible saja
            var arrDisplay = [];

            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colid').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colid');
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colFarmerName').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colFarmerName');
            // if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colMemberRole').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colMemberRole');
            // if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colBirthdate').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colBirthdate');
            // if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colAge').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colAge');
            // if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colHandphone').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colHandphone');
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colDateCollection').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colDateCollection');
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colProvince').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colProvince');
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colDistrict').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colDistrict');
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colDesa').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colDesa');
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colKecamatan').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colKecamatan');
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colEnumerator').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colEnumerator');
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-colLastUpdated').isVisible() == true) arrDisplay.push('Koltiva.view.SME.GridMainTrader-colLastUpdated');

            //set nilainya
            Ext.getCmp('Koltiva.view.SME.WinGridDisplay-fieldDisplay').setValue(arrDisplay);
        }
    }
});