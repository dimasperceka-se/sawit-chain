// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
function hideAllAdvFilter(){
    Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-rowDateTransaction').setVisible(false);
}

function resetAdvancedFilterLs(){
    localStorage.setItem('patchouli_adv_ls', JSON.stringify({
        pAdvRowDateTransaction: ""
        ,pAdvDateTransactionBegin: ""
        ,pAdvDateTransactionEnd: ""
    }));
}

function setAdvancedFilterLs(){
    var dBegin = Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionBegin').getValue();
    var dEnd = Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionEnd').getValue();
    dBegin = Ext.Date.format(dBegin, 'Y-m-d');
    dEnd = Ext.Date.format(dEnd, 'Y-m-d');

    localStorage.setItem('patchouli_adv_ls', JSON.stringify({
        pAdvRowDateTransaction: Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-rowDateTransaction').isVisible()
        ,pAdvDateTransactionBegin: dBegin
        ,pAdvDateTransactionEnd: dEnd
    }));
}

function loadPageGridDashboard() {
    var storeTransactionFarmer = Ext.getCmp("DashboardTransactionSupplier-Grid").getStore();
    var patchouli_adv_ls = JSON.parse(localStorage.getItem('patchouli_adv_ls'));
    if(patchouli_adv_ls != null){
        pAdvRowDateTransaction = patchouli_adv_ls.pAdvRowDateTransaction;
        pAdvDateTransactionBegin = patchouli_adv_ls.pAdvDateTransactionBegin;
        pAdvDateTransactionEnd = patchouli_adv_ls.pAdvDateTransactionEnd;
    }else{
        pAdvRowDateTransaction = "";
        pAdvDateTransactionBegin = "";
        pAdvDateTransactionEnd = "";
    }
    
    storeTransactionFarmer.proxy.extraParams.AdvRowDateTransaction = pAdvRowDateTransaction;
    storeTransactionFarmer.proxy.extraParams.AdvDateTransactionBegin = pAdvDateTransactionBegin;
    storeTransactionFarmer.proxy.extraParams.AdvDateTransactionEnd = pAdvDateTransactionEnd;
    
    storeTransactionFarmer.load();
}
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Dboard.WinAdvancedFilterMill' ,{
extend: 'Ext.window.Window',
id: 'Koltiva.view.Dboard.WinAdvancedFilterMill',
title: lang('Filter'),
closable: true,
modal: true,
closeAction: 'destroy',
width: '60%',
height: '44%',
overflowY: 'auto',
initComponent: function() {
    var thisObj = this;

    var cmb_filter = Ext.create('Koltiva.store.Dboard.CmbAdvancedFilter');

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
                    id: 'Koltiva.view.Dboard.WinAdvancedFilterMill-cmbFilter',
                    name: 'Koltiva.view.Dboard.WinAdvancedFilterMill-cmbFilter[]',
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
                columnWidth: 0.3,
                layout:'column',
                border: false,
                margin: '5 0 0 15',
                items:[{
                    columnWidth: 1,
                    border: false,
                    layout:{
                        type:'hbox',
                        pack:'left',
                        align: 'middle'
                    },
                    items:[{
                        xtype: 'button',
                        text: 'Reload Filter',
                        handler: function() {
                            hideAllAdvFilter();
                            var filterDipilih = Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-cmbFilter').getValue();
                            if(filterDipilih.length > 0){
                                for (var i = 0; i < filterDipilih.length; i++) {
                                    switch (filterDipilih[i]) {
                                        case 'DateTransaction':
                                            Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-rowDateTransaction').setVisible(true);
                                        break;
                                    }
                                }
                            }
                        }
                    },{
                        xtype: 'button',
                        text: 'Reset Filter',
                        style: 'background:#FF5566;margin-left:10px;',
                        handler: function() {
                            hideAllAdvFilter();
                            resetAdvancedFilterLs();
                            // Ext.getCmp('DashboardTransactionFarmer-Grid').getStore().loadPage(1);
                            loadPageGridDashboard();
                            Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill').close(); //tutup popup
                        }
                    }]
                }]
            }]
        },{
            html:'<hr style="border:1px solid #F3E3B6;" />'
        },{
            layout: 'column',
            border: false,
            id: 'Koltiva.view.Dboard.WinAdvancedFilterMill-rowDateTransaction',
            hidden: true,
            items:[{
                columnWidth: 0.15,
                layout: 'form',
                items:[{
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    text: lang('Date Transaction'),
                }]
            },{
                columnWidth: 0.2,
                layout: 'form',
                items:[{
                    xtype: 'datefield',
                    name: 'Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionBegin',
                    id: 'Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionBegin',
                    format: 'Y-m-d'
                }]
            },{
                columnWidth: 0.05,
                layout: 'form',
                items:[{
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    style: 'padding-left:15px;',
                    text: lang('to'),
                }]
            },{
                columnWidth: 0.2,
                layout: 'form',
                items:[{
                    xtype: 'datefield',
                    name: 'Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionEnd',
                    id: 'Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionEnd',
                    format: 'Y-m-d'
                }]
            }]
        }]
    }]

    this.callParent(arguments);
},
buttons: [{
    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
    text: lang('Search'),
    cls:'Sfr_BtnGridBlue',
    overCls:'Sfr_BtnGridBlue-Hover',
    handler: function() {
        //cek apakah sudah terisi semua..
        var isAllFilled = true;

        if (Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-rowDateTransaction').isVisible() == true) {
            if (Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionBegin').getValue() == "" && Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionEnd').getValue() == "") isAllFilled = false;
        }

        var isFilterSelected = true;
        if(
            Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-rowDateTransaction').isVisible() == false
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
            // Ext.getCmp('DashboardTransactionFarmer-Grid').getStore().loadPage(1);
            loadPageGridDashboard();
            Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill').close(); //tutup popup
        }
    }
},{
    icon: varjs.config.base_url + 'images/icons/new/close.png',
    text: lang('Close'),
    cls:'Sfr_BtnFormGrey',
    overCls:'Sfr_BtnFormGrey-Hover',
    handler: function() {
        Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill').close();
    }
}],
listeners: {
    afterRender: function(component, eOpts){
        var patchouli_adv_ls = JSON.parse(localStorage.getItem('patchouli_adv_ls'));
        var filterValue = [];

        if(patchouli_adv_ls != null){
            if(patchouli_adv_ls.pAdvRowDateTransaction == true){
                filterValue.push('DateTransaction');
                Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-rowDateTransaction').setVisible(true);
                Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionBegin').setValue(patchouli_adv_ls.pAdvDateTransactionBegin);
                Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-DateTransactionEnd').setValue(patchouli_adv_ls.pAdvDateTransactionEnd);
            }
        }

        Ext.getCmp('Koltiva.view.Dboard.WinAdvancedFilterMill-cmbFilter').setValue(filterValue);
    }
}
});