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
        Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowHandphone').setVisible(false);
        Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowAge').setVisible(false);
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
            pAdvRowHandphone: Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowHandphone').isVisible(),
            pAdvTextHandphone: Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-textHandphone').getValue(),
            pAdvRowAge: Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowAge').isVisible(),
            pAdvOpAge: Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-cmbOpAge').getValue(),
            pAdvTextAge: Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-textAge').getValue()
        }));
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.SME.WinAdvancedFilter' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.SME.WinAdvancedFilter',
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
        var cmb_filter = Ext.create('Koltiva.store.SME.CmbAdvancedFilter');
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
                        id: 'Koltiva.view.SME.WinAdvancedFilter-cmbFilter',
                        name: 'Koltiva.view.SME.WinAdvancedFilter-cmbFilter[]',
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

                            var filterDipilih = Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-cmbFilter').getValue();
                            if(filterDipilih.length > 0){
                                for (var i = 0; i < filterDipilih.length; i++) {
                                    switch (filterDipilih[i]) {
                                        case 'Handphone':
                                            Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowHandphone').setVisible(true);
                                        break;
                                        case 'Age':
                                            Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowAge').setVisible(true);
                                        break;
                                    }
                                }
                            }
                        }
                    }]
                }]
            },{
                html:'<hr style="border:1px solid #F3E3B6;" />'
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.SME.WinAdvancedFilter-rowHandphone',
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
                        id: 'Koltiva.view.SME.WinAdvancedFilter-textHandphone',
                        name: 'Koltiva.view.SME.WinAdvancedFilter-textHandphone'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.SME.WinAdvancedFilter-rowAge',
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
                        id: 'Koltiva.view.SME.WinAdvancedFilter-cmbOpAge',
                        name: 'Koltiva.view.SME.WinAdvancedFilter-cmbOpAge',
                        editable: false
                    }]
                },{
                    columnWidth: 0.1,
                    layout: 'form',
                    items:[{
                        xtype:'numberfield',
                        id: 'Koltiva.view.SME.WinAdvancedFilter-textAge',
                        name: 'Koltiva.view.SME.WinAdvancedFilter-textAge'
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    buttons: [{
        text: lang('Search'),
        icon: varjs.config.base_url + 'images/icons/new/search-white.png',
        cls: 'Sfr_BtnFormBlue',
        overCls: 'Sfr_BtnFormBlue-Hover',
        handler: function() {
            //cek apakah sudah terisi semua..
            var isAllFilled = true;

            if (Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowHandphone').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-textHandphone').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowAge').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-cmbOpAge').getValue() == null) isAllFilled = false;
                if (Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-textAge').getValue() == "") isAllFilled = false;
            }

            var isFilterSelected = true;
            if(
                Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowHandphone').isVisible() == false &&
                Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowAge').isVisible() == false
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
                
                if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-gridMainGrid') != undefined){
                    Ext.getCmp('Koltiva.view.SME.GridMainTrader-gridMainGrid').getStore().loadPage(1);
                }
                
                if(Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid') != undefined){
                    Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getStore().loadPage(1);
                }

                Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter').close(); //tutup popup
            }
        }
    },{
        text: lang('Reset Filter'),
        icon: varjs.config.base_url + 'images/icons/new/delete.svg',
        cls:'Sfr_BtnFormRed',
        overCls:'Sfr_BtnFormRed-Hover',
        handler: function() {
            hideAllGrowerAdvFilter();
            resetAdvancedFilterLs();
                
            if(Ext.getCmp('Koltiva.view.SME.GridMainTrader-gridMainGrid') != undefined){
                Ext.getCmp('Koltiva.view.SME.GridMainTrader-gridMainGrid').getStore().loadPage(1);
            }
            
            if(Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid') != undefined){
                Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getStore().loadPage(1);
            }

            Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter').close(); //tutup popup
        }
    },{
        text: lang('Close'),
        icon: varjs.config.base_url + 'images/icons/new/close.png',
        cls: 'Sfr_BtnFormGrey',
        overCls: 'Sfr_BtnFormGrey-Hover',
        handler: function() {
            Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            var patchouli_trader_ls = JSON.parse(localStorage.getItem('patchouli_trader_ls'));
            var filterValue = [];

            if(patchouli_trader_ls != null){
                if(patchouli_trader_ls.pAdvRowHandphone == true){
                    filterValue.push('Handphone');
                    Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowHandphone').setVisible(true);
                    Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-textHandphone').setValue(patchouli_trader_ls.pAdvTextHandphone);
                }
                if(patchouli_trader_ls.pAdvRowAge == true){
                    filterValue.push('Age');
                    Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-rowAge').setVisible(true);
                    Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-cmbOpAge').setValue(patchouli_trader_ls.pAdvOpAge);
                    Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-textAge').setValue(patchouli_trader_ls.pAdvTextAge);
                }
            }

            Ext.getCmp('Koltiva.view.SME.WinAdvancedFilter-cmbFilter').setValue(filterValue);
        }
    }
});