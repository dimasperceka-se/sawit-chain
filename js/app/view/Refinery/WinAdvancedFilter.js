/*
* @Author: muhamad hidayaturrohman
* @Date:   2020-11-05
* @Last Modified by:  muhamad hidayaturrohman
* @Last Modified time: 2020-11-05
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
function hideAllGrowerAdvFilter(){
    Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowStatusPerusahaan').setVisible(false);
    Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTahunTerbentuk').setVisible(false);
    Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowPhone').setVisible(false);
    Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowHavePhoto').setVisible(false);
    Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTotalPermanentEmployee').setVisible(false);
}

function resetAdvancedFilterLs(){
    localStorage.setItem('patchouli_refinery_ls', JSON.stringify({
        pAdvRowStatusPerusahaan: "",
        pAdvCmbStatusPerusahaan: "",
        pAdvRowTahunTerbentuk: "",
        pAdvCmbOpTahunTerbentuk: "",
        pAdvTextTahunTerbentuk: "",
        pAdvRowPhone: "",
        pAdvTextPhone: "",
        pAdvRowHavePhoto: "",
        pAdvCmbHavePhoto: "",
        pAdvRowTotalPermanentEmployee: "",
        pAdvCmbOpTotalPermanentEmployee: "",
        pAdvTextTotalPermanentEmployee: ""
    }));
}

function setAdvancedFilterLs(){
    localStorage.setItem('patchouli_refinery_ls', JSON.stringify({
        pAdvRowStatusPerusahaan: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowStatusPerusahaan').isVisible(),
        pAdvCmbStatusPerusahaan: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbStatusPerusahaan').getValue(),
        pAdvRowTahunTerbentuk: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTahunTerbentuk').isVisible(),
        pAdvCmbOpTahunTerbentuk: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTahunTerbentuk').getValue(),
        pAdvTextTahunTerbentuk: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textTahunTerbentuk').getValue(),
        pAdvRowPhone: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowPhone').isVisible(),
        pAdvTextPhone: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textPhone').getValue(),
        pAdvRowHavePhoto: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowHavePhoto').isVisible(),
        pAdvCmbHavePhoto: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbHavePhoto').getValue(),
        pAdvRowTotalPermanentEmployee: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTotalPermanentEmployee').isVisible(),
        pAdvCmbOpTotalPermanentEmployee: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTotalPermanentEmployee').getValue(),
        pAdvTextTotalPermanentEmployee: Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textTotalPermanentEmployee').getValue()
    }));
}
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Refinery..WinAdvancedFilter' ,{
extend: 'Ext.window.Window',
id: 'Koltiva.view.Refinery..WinAdvancedFilter',
title: lang('Filter'),
closable: true,
modal: true,
closeAction: 'destroy',
width: '60%',
height: '53%',
overflowY: 'auto',
initComponent: function() {
    var thisObj = this;

    //store yg dipakai
    var cmb_filter = Ext.create('Koltiva.store.Refinery..CmbAdvancedFilter');
    var cmb_filter_operation = Ext.create('Koltiva.store.Grower.CmbAdvancedFilterOperation');
    var cmb_status_perusahaan = Ext.create('Koltiva.store.Refinery..CmbStatusPerusahaan');
    var cmb_yes_no = Ext.create('Koltiva.store.Refinery..CmbYesNo');

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
                    id: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbFilter',
                    name: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbFilter[]',
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
                columnWidth: .3,
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
                            hideAllGrowerAdvFilter();

                            var filterDipilih = Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbFilter').getValue();
                            if(filterDipilih.length > 0){
                                for (var i = 0; i < filterDipilih.length; i++) {
                                    switch (filterDipilih[i]) {
                                        case 'StatusPerusahaan':
                                            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowStatusPerusahaan').setVisible(true);
                                        break;
                                        case 'TotalPermanentEmployee':
                                            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTotalPermanentEmployee').setVisible(true);
                                        break;
                                        case 'TahunTerbentuk':
                                            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTahunTerbentuk').setVisible(true);
                                        break;
                                        case 'Phone':
                                            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowPhone').setVisible(true);
                                        break;
                                        case 'HavePhoto':
                                            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowHavePhoto').setVisible(true);
                                        break;
                                    }
                                }
                            }
                        }
                    }]
                }]
            }]
        },{
            html:'<hr style="border:1px solid #F3E3B6;" />'
        },{
            layout: 'column',
            border: false,
            id: 'Koltiva.view.Refinery..WinAdvancedFilter-rowStatusPerusahaan',
            hidden: true,
            items:[{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    text: lang('Status Perusahaan'),
                }]
            },{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype: 'combobox',
                    store: cmb_status_perusahaan,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbStatusPerusahaan',
                    name: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbStatusPerusahaan',
                    editable: false
                }]
            }]
        },{
            layout: 'column',
            border: false,
            id: 'Koltiva.view.Refinery..WinAdvancedFilter-rowTotalPermanentEmployee',
            hidden: true,
            items:[{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    text: lang('Total Permanent Employee'),
                }]
            },{
                columnWidth: 0.1,
                layout: 'form',
                items:[{
                    store: cmb_filter_operation,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTotalPermanentEmployee',
                    name: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTotalPermanentEmployee',
                    editable: false
                }]
            },{
                columnWidth: 0.1,
                layout: 'form',
                items:[{
                    xtype:'numericfield',
                    id: 'Koltiva.view.Refinery..WinAdvancedFilter-textTotalPermanentEmployee',
                    name: 'Koltiva.view.Refinery..WinAdvancedFilter-textTotalPermanentEmployee',
                    allowNegative: false,
                    minValue: 1
                }]
            }]
        },{
            layout: 'column',
            border: false,
            id: 'Koltiva.view.Refinery..WinAdvancedFilter-rowTahunTerbentuk',
            hidden: true,
            items:[{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    text: lang('Tahun Terbentuk'),
                }]
            },{
                columnWidth: 0.1,
                layout: 'form',
                items:[{
                    store: cmb_filter_operation,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTahunTerbentuk',
                    name: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTahunTerbentuk',
                    editable: false
                }]
            },{
                columnWidth: 0.1,
                layout: 'form',
                items:[{
                    xtype:'numericfield',
                    id: 'Koltiva.view.Refinery..WinAdvancedFilter-textTahunTerbentuk',
                    name: 'Koltiva.view.Refinery..WinAdvancedFilter-textTahunTerbentuk',
                    allowNegative: false,
                    minValue: 1970
                }]
            }]
        },{
            layout: 'column',
            border: false,
            id: 'Koltiva.view.Refinery..WinAdvancedFilter-rowPhone',
            hidden: true,
            items:[{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    text: lang('Phone'),
                }]
            },{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype:'textfield',
                    id: 'Koltiva.view.Refinery..WinAdvancedFilter-textPhone',
                    name: 'Koltiva.view.Refinery..WinAdvancedFilter-textPhone'
                }]
            }]
        },{
            layout: 'column',
            border: false,
            id: 'Koltiva.view.Refinery..WinAdvancedFilter-rowHavePhoto',
            hidden: true,
            items:[{
                columnWidth: 0.25,
                layout: 'form',
                items:[{
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    text: lang('Have Photo'),
                }]
            },{
                columnWidth: 0.1,
                layout: 'form',
                items:[{
                    xtype: 'combobox',
                    store: cmb_yes_no,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbHavePhoto',
                    name: 'Koltiva.view.Refinery..WinAdvancedFilter-cmbHavePhoto',
                    editable: false
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

        if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowStatusPerusahaan').isVisible() == true) {
            if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbStatusPerusahaan').getValue() == null) isAllFilled = false;
        }
        if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTahunTerbentuk').isVisible() == true) {
            if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTahunTerbentuk').getValue() == null) isAllFilled = false;
            if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textTahunTerbentuk').getValue() == "") isAllFilled = false;
        }
        if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTotalPermanentEmployee').isVisible() == true) {
            if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTotalPermanentEmployee').getValue() == null) isAllFilled = false;
            if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textTotalPermanentEmployee').getValue() == "") isAllFilled = false;
        }
        if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowPhone').isVisible() == true) {
            if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textPhone').getValue() == "") isAllFilled = false;
        }
        if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowHavePhoto').isVisible() == true) {
            if (Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbHavePhoto').getValue() == null) isAllFilled = false;
        }

        var isFilterSelected = true;
        if(
            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowStatusPerusahaan').isVisible() == false &&
            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTahunTerbentuk').isVisible() == false &&
            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTotalPermanentEmployee').isVisible() == false &&
            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowPhone').isVisible() == false &&
            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowHavePhoto').isVisible() == false
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
            Ext.getCmp('Koltiva.view.Refinery..GridMainRefinery-gridMainGrid').getStore().loadPage(1);
            Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter').close(); //tutup popup
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
        Ext.getCmp('Koltiva.view.Refinery..GridMainRefinery-gridMainGrid').getStore().loadPage(1);
        Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter').close(); //tutup popup
    }
},{
    text: lang('Close'),
    icon: varjs.config.base_url + 'images/icons/new/close.png',
    cls: 'Sfr_BtnFormGrey',
    overCls: 'Sfr_BtnFormGrey-Hover',
    handler: function() {
        Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter').close();
    }
}],
listeners: {
    afterRender: function(component, eOpts){
        var patchouli_refinery_ls = JSON.parse(localStorage.getItem('patchouli_refinery_ls'));
        var filterValue = [];

        if(patchouli_refinery_ls != null){

            if(patchouli_refinery_ls.pAdvRowStatusPerusahaan == true){
                filterValue.push('StatusPerusahaan');
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowStatusPerusahaan').setVisible(true);
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbStatusPerusahaan').setValue(patchouli_refinery_ls.pAdvCmbStatusPerusahaan);
            }

            if(patchouli_refinery_ls.pAdvRowTahunTerbentuk == true){
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTahunTerbentuk').setVisible(true);
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTahunTerbentuk').setValue(patchouli_refinery_ls.pAdvCmbOpTahunTerbentuk);
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textTahunTerbentuk').setValue(patchouli_refinery_ls.pAdvTextTahunTerbentuk);
            }

            if(patchouli_refinery_ls.pAdvRowPhone == true){
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowPhone').setVisible(true);
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textPhone').setValue(patchouli_refinery_ls.pAdvTextPhone);
            }

            if(patchouli_refinery_ls.pAdvRowHavePhoto == true){
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowHavePhoto').setVisible(true);
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbHavePhoto').setValue(patchouli_refinery_ls.pAdvCmbHavePhoto);
            }

            if(patchouli_refinery_ls.pAdvRowTotalPermanentEmployee == true){
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-rowTotalPermanentEmployee').setVisible(true);
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbOpTotalPermanentEmployee').setValue(patchouli_refinery_ls.pAdvCmbOpTotalPermanentEmployee);
                Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-textTotalPermanentEmployee').setValue(patchouli_refinery_ls.pAdvTextTotalPermanentEmployee);
            }

        }

        Ext.getCmp('Koltiva.view.Refinery..WinAdvancedFilter-cmbFilter').setValue(filterValue);
    }
}
});