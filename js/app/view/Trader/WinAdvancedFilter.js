/*
* @Author: nikolius
* @Date:   2017-07-19 13:34:40
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-19 13:57:11
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
    function hideAllGrowerAdvFilter(){
        Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowHandphone').setVisible(false);
        Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowAge').setVisible(false);
    }

    function resetAdvancedFilterLs(){
        localStorage.setItem('patchouli_trader_ls', JSON.stringify({
            pAdvRowHandphone: "",
            pAdvTextHandphone: "",
            pAdvRowAge: "",
            pAdvOpAge: "",
            pAdvTextAge: ""
        }));
    }

    function setAdvancedFilterLs(){
        localStorage.setItem('patchouli_trader_ls', JSON.stringify({
            pAdvRowHandphone: Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowHandphone').isVisible(),
            pAdvTextHandphone: Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-textHandphone').getValue(),
            pAdvRowAge: Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowAge').isVisible(),
            pAdvOpAge: Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-cmbOpAge').getValue(),
            pAdvTextAge: Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-textAge').getValue()
        }));
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Trader.WinAdvancedFilter' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Trader.WinAdvancedFilter',
    title: lang('Filter'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    height: '44%',
    overflowY: 'auto',
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai
        var cmb_filter = Ext.create('Koltiva.store.Trader.CmbAdvancedFilter');
        var cmb_filter_operation = Ext.create('Koltiva.store.Grower.CmbAdvancedFilterOperation');

        //isi formnya
        thisObj.items = [{
            xtype:'panel',
            border: false,
            padding:'5 23 5 15',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Add Filter'),
                    }]
                },{
                    columnWidth: 0.5,
                    layout: 'form',
                    items:[{
                        xtype: 'boxselect',
                        id: 'Koltiva.view.Trader.WinAdvancedFilter-cmbFilter',
                        name: 'Koltiva.view.Trader.WinAdvancedFilter-cmbFilter[]',
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        store: cmb_filter,
                        stacked: true,
                        pinList: false,
                        triggerOnClick: false,
                        filterPickList: true
                    }]
                },{
                    columnWidth: 0.15,
                    layout: 'form',
                    margin: '1 0 0 15',
                    items:[{
                        xtype: 'button',
                        text: 'Reload Filter',
                        handler: function() {
                            hideAllGrowerAdvFilter();

                            var filterDipilih = Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-cmbFilter').getValue();
                            if(filterDipilih.length > 0){
                                for (var i = 0; i < filterDipilih.length; i++) {
                                    switch (filterDipilih[i]) {
                                        case 'Handphone':
                                            Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowHandphone').setVisible(true);
                                        break;
                                        case 'Age':
                                            Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowAge').setVisible(true);
                                        break;
                                    }
                                }
                            }
                        }
                    }]
                },{
                    columnWidth: 0.15,
                    layout: 'form',
                    margin: '1 0 0 2',
                    items:[{
                        xtype: 'button',
                        text: 'Reset Filter',
                        style: {
                            background: '#FF5566'
                        },
                        handler: function() {
                            hideAllGrowerAdvFilter();
                            resetAdvancedFilterLs();
                            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getStore().loadPage(1);
                            Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter').close(); //tutup popup
                        }
                    }]
                }]
            },{
                html:'<hr style="border:1px solid #F3E3B6;" />'
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Trader.WinAdvancedFilter-rowHandphone',
                hidden: true,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Handphone'),
                    }]
                },{
                    columnWidth: 0.25,
                    layout: 'form',
                    items:[{
                        xtype:'textfield',
                        id: 'Koltiva.view.Trader.WinAdvancedFilter-textHandphone',
                        name: 'Koltiva.view.Trader.WinAdvancedFilter-textHandphone'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Trader.WinAdvancedFilter-rowAge',
                hidden: true,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Age'),
                    }]
                },{
                    columnWidth: 0.075,
                    layout: 'form',
                    items:[{
                        store: cmb_filter_operation,
                        xtype: 'combobox',
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        id: 'Koltiva.view.Trader.WinAdvancedFilter-cmbOpAge',
                        name: 'Koltiva.view.Trader.WinAdvancedFilter-cmbOpAge',
                        editable: false
                    }]
                },{
                    columnWidth: 0.1,
                    layout: 'form',
                    items:[{
                        xtype:'numberfield',
                        id: 'Koltiva.view.Trader.WinAdvancedFilter-textAge',
                        name: 'Koltiva.view.Trader.WinAdvancedFilter-textAge'
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    buttons: [{
        text: lang('Search'),
        margin: '5px',
        scale: 'large',
        ui: 's-button',
        cls: 's-blue',
        handler: function() {
            //cek apakah sudah terisi semua..
            var isAllFilled = true;

            if (Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowHandphone').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-textHandphone').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowAge').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-cmbOpAge').getValue() == null) isAllFilled = false;
                if (Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-textAge').getValue() == "") isAllFilled = false;
            }

            var isFilterSelected = true;
            if(
                Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowHandphone').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowAge').isVisible() == false
            ){
                isFilterSelected = false;
            }

            if (isFilterSelected == false) {
                Ext.MessageBox.show({
                    title: 'Notifications',
                    msg: 'No filter selected',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-warning'
                });
            }

            if (isAllFilled == false) {
                Ext.MessageBox.show({
                    title: 'Notifications',
                    msg: 'Selected filter must be filled',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-warning'
                });
            }

            if(isFilterSelected == true && isAllFilled == true){
                resetAdvancedFilterLs();
                setAdvancedFilterLs();
                Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getStore().loadPage(1);

                Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter').close(); //tutup popup
            }
        }
    },{
        text: lang('Close'),
        margin: '5px',
        scale: 'large',
        ui: 's-button',
        cls: 's-grey',
        handler: function() {
            Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            var patchouli_trader_ls = JSON.parse(localStorage.getItem('patchouli_trader_ls'));
            var filterValue = [];

            if(patchouli_trader_ls != null){
                if(patchouli_trader_ls.pAdvRowHandphone == true){
                    filterValue.push('Handphone');
                    Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowHandphone').setVisible(true);
                    Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-textHandphone').setValue(patchouli_trader_ls.pAdvTextHandphone);
                }
                if(patchouli_trader_ls.pAdvRowAge == true){
                    filterValue.push('Age');
                    Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-rowAge').setVisible(true);
                    Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-cmbOpAge').setValue(patchouli_trader_ls.pAdvOpAge);
                    Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-textAge').setValue(patchouli_trader_ls.pAdvTextAge);
                }
            }

            Ext.getCmp('Koltiva.view.Trader.WinAdvancedFilter-cmbFilter').setValue(filterValue);
        }
    }
});