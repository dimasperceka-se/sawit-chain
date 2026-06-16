/*
* @Author: nikolius
* @Date:   2017-07-19 10:35:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-06 16:17:06
*/

Ext.define('Koltiva.view.Trader.WinGridDisplay' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Trader.WinGridDisplay',
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
        var combo_store_field_display = Ext.create('Koltiva.store.Trader.CmbGridFieldDisplay');

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
                    id: 'Koltiva.view.Trader.WinGridDisplay-fieldDisplay',
                    name: 'Koltiva.view.Trader.WinGridDisplay-fieldDisplay',
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
        margin: '5px',
        scale: 'large',
        ui: 's-button',
        cls: 's-blue',
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
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colid').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colFarmerName').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colMemberRole').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colBirthdate').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colAge').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colHandphone').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colDateCollection').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colProvince').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colDistrict').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colDesa').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colKecamatan').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colEnumerator').setVisible(false);
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colLastUpdated').setVisible(false);

            var visibleField = Ext.getCmp('Koltiva.view.Trader.WinGridDisplay-fieldDisplay').getValue();
            if(visibleField.length > 0){
                for (var i = 0; i < visibleField.length; i++) {
                    Ext.getCmp(visibleField[i]).setVisible(true);
                }
            }

            //tutup popup
            Ext.getCmp('Koltiva.view.Trader.WinGridDisplay').close();
            Ext.MessageBox.hide();
        }
    },{
        text: lang('Close'),
        margin: '5px',
        scale: 'large',
        ui: 's-button',
        cls: 's-grey',
        handler: function() {
            Ext.getCmp('Koltiva.view.Trader.WinGridDisplay').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            //set nilainya detek dari yg visible saja
            var arrDisplay = [];

            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colid').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colid');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colFarmerName').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colFarmerName');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colMemberRole').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colMemberRole');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colBirthdate').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colBirthdate');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colAge').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colAge');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colHandphone').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colHandphone');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colDateCollection').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colDateCollection');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colProvince').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colProvince');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colDistrict').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colDistrict');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colDesa').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colDesa');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colKecamatan').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colKecamatan');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colEnumerator').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colEnumerator');
            if(Ext.getCmp('Koltiva.view.Trader.GridMainTrader-colLastUpdated').isVisible() == true) arrDisplay.push('Koltiva.view.Trader.GridMainTrader-colLastUpdated');

            //set nilainya
            Ext.getCmp('Koltiva.view.Trader.WinGridDisplay-fieldDisplay').setValue(arrDisplay);
        }
    }
});