/*
* @Author: nikolius
* @Date:   2017-08-04 09:59:38
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-08 12:02:57
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
    function hideAllGrowerAdvFilter(){
        Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowStatusPerusahaan').setVisible(false);
        Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTahunTerbentuk').setVisible(false);
        Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowPhone').setVisible(false);
        Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowHavePhoto').setVisible(false);
        Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTotalPermanentEmployee').setVisible(false);
    }

    function resetAdvancedFilterLs(){
        localStorage.setItem('patchouli_mill_ls', JSON.stringify({
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
        localStorage.setItem('patchouli_mill_ls', JSON.stringify({
            pAdvRowStatusPerusahaan: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowStatusPerusahaan').isVisible(),
            pAdvCmbStatusPerusahaan: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbStatusPerusahaan').getValue(),
            pAdvRowTahunTerbentuk: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTahunTerbentuk').isVisible(),
            pAdvCmbOpTahunTerbentuk: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbOpTahunTerbentuk').getValue(),
            pAdvTextTahunTerbentuk: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textTahunTerbentuk').getValue(),
            pAdvRowPhone: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowPhone').isVisible(),
            pAdvTextPhone: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textPhone').getValue(),
            pAdvRowHavePhoto: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowHavePhoto').isVisible(),
            pAdvCmbHavePhoto: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbHavePhoto').getValue(),
            pAdvRowTotalPermanentEmployee: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTotalPermanentEmployee').isVisible(),
            pAdvCmbOpTotalPermanentEmployee: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbOpTotalPermanentEmployee').getValue(),
            pAdvTextTotalPermanentEmployee: Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textTotalPermanentEmployee').getValue()
        }));
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Mill.WinAdvancedFilter' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Mill.WinAdvancedFilter',
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
        var cmb_filter = Ext.create('Koltiva.store.Mill.CmbAdvancedFilter');
        var cmb_filter_operation = Ext.create('Koltiva.store.Grower.CmbAdvancedFilterOperation');
        var cmb_status_perusahaan = Ext.create('Koltiva.store.Mill.CmbStatusPerusahaan');
        var cmb_yes_no = Ext.create('Koltiva.store.Mill.CmbYesNo');

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
                        id: 'Koltiva.view.Mill.WinAdvancedFilter-cmbFilter',
                        name: 'Koltiva.view.Mill.WinAdvancedFilter-cmbFilter[]',
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

                                var filterDipilih = Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbFilter').getValue();
                                if(filterDipilih.length > 0){
                                    for (var i = 0; i < filterDipilih.length; i++) {
                                        switch (filterDipilih[i]) {
                                            case 'StatusPerusahaan':
                                                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowStatusPerusahaan').setVisible(true);
                                            break;
                                            case 'TotalPermanentEmployee':
                                                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTotalPermanentEmployee').setVisible(true);
                                            break;
                                            case 'TahunTerbentuk':
                                                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTahunTerbentuk').setVisible(true);
                                            break;
                                            case 'Phone':
                                                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowPhone').setVisible(true);
                                            break;
                                            case 'HavePhoto':
                                                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowHavePhoto').setVisible(true);
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
                id: 'Koltiva.view.Mill.WinAdvancedFilter-rowStatusPerusahaan',
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
                        id: 'Koltiva.view.Mill.WinAdvancedFilter-cmbStatusPerusahaan',
                        name: 'Koltiva.view.Mill.WinAdvancedFilter-cmbStatusPerusahaan',
                        editable: false
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Mill.WinAdvancedFilter-rowTotalPermanentEmployee',
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
                        id: 'Koltiva.view.Mill.WinAdvancedFilter-cmbOpTotalPermanentEmployee',
                        name: 'Koltiva.view.Mill.WinAdvancedFilter-cmbOpTotalPermanentEmployee',
                        editable: false
                    }]
                },{
                    columnWidth: 0.1,
                    layout: 'form',
                    items:[{
                        xtype:'numericfield',
                        id: 'Koltiva.view.Mill.WinAdvancedFilter-textTotalPermanentEmployee',
                        name: 'Koltiva.view.Mill.WinAdvancedFilter-textTotalPermanentEmployee',
                        allowNegative: false,
                        minValue: 1
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Mill.WinAdvancedFilter-rowTahunTerbentuk',
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
                        id: 'Koltiva.view.Mill.WinAdvancedFilter-cmbOpTahunTerbentuk',
                        name: 'Koltiva.view.Mill.WinAdvancedFilter-cmbOpTahunTerbentuk',
                        editable: false
                    }]
                },{
                    columnWidth: 0.1,
                    layout: 'form',
                    items:[{
                        xtype:'numericfield',
                        id: 'Koltiva.view.Mill.WinAdvancedFilter-textTahunTerbentuk',
                        name: 'Koltiva.view.Mill.WinAdvancedFilter-textTahunTerbentuk',
                        allowNegative: false,
                        minValue: 1970
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Mill.WinAdvancedFilter-rowPhone',
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
                        id: 'Koltiva.view.Mill.WinAdvancedFilter-textPhone',
                        name: 'Koltiva.view.Mill.WinAdvancedFilter-textPhone'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Mill.WinAdvancedFilter-rowHavePhoto',
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
                        id: 'Koltiva.view.Mill.WinAdvancedFilter-cmbHavePhoto',
                        name: 'Koltiva.view.Mill.WinAdvancedFilter-cmbHavePhoto',
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

            if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowStatusPerusahaan').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbStatusPerusahaan').getValue() == null) isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTahunTerbentuk').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbOpTahunTerbentuk').getValue() == null) isAllFilled = false;
                if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textTahunTerbentuk').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTotalPermanentEmployee').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbOpTotalPermanentEmployee').getValue() == null) isAllFilled = false;
                if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textTotalPermanentEmployee').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowPhone').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textPhone').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowHavePhoto').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbHavePhoto').getValue() == null) isAllFilled = false;
            }

            var isFilterSelected = true;
            if(
                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowStatusPerusahaan').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTahunTerbentuk').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTotalPermanentEmployee').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowPhone').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowHavePhoto').isVisible() == false
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
                Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().loadPage(1);
                Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter').close(); //tutup popup
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
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().loadPage(1);
            Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter').close(); //tutup popup
        }
    },{
        text: lang('Close'),
        icon: varjs.config.base_url + 'images/icons/new/close.png',
        cls: 'Sfr_BtnFormGrey',
        overCls: 'Sfr_BtnFormGrey-Hover',
        handler: function() {
            Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            var patchouli_mill_ls = JSON.parse(localStorage.getItem('patchouli_mill_ls'));
            var filterValue = [];

            if(patchouli_mill_ls != null){

                if(patchouli_mill_ls.pAdvRowStatusPerusahaan == true){
                    filterValue.push('StatusPerusahaan');
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowStatusPerusahaan').setVisible(true);
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbStatusPerusahaan').setValue(patchouli_mill_ls.pAdvCmbStatusPerusahaan);
                }

                if(patchouli_mill_ls.pAdvRowTahunTerbentuk == true){
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTahunTerbentuk').setVisible(true);
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbOpTahunTerbentuk').setValue(patchouli_mill_ls.pAdvCmbOpTahunTerbentuk);
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textTahunTerbentuk').setValue(patchouli_mill_ls.pAdvTextTahunTerbentuk);
                }

                if(patchouli_mill_ls.pAdvRowPhone == true){
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowPhone').setVisible(true);
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textPhone').setValue(patchouli_mill_ls.pAdvTextPhone);
                }

                if(patchouli_mill_ls.pAdvRowHavePhoto == true){
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowHavePhoto').setVisible(true);
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbHavePhoto').setValue(patchouli_mill_ls.pAdvCmbHavePhoto);
                }

                if(patchouli_mill_ls.pAdvRowTotalPermanentEmployee == true){
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-rowTotalPermanentEmployee').setVisible(true);
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbOpTotalPermanentEmployee').setValue(patchouli_mill_ls.pAdvCmbOpTotalPermanentEmployee);
                    Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-textTotalPermanentEmployee').setValue(patchouli_mill_ls.pAdvTextTotalPermanentEmployee);
                }

            }

            Ext.getCmp('Koltiva.view.Mill.WinAdvancedFilter-cmbFilter').setValue(filterValue);
        }
    }
});