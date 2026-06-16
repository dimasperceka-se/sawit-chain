/*
* @Author: nikolius
* @Date:   2017-05-17 15:33:17
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-29 17:56:22
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
    function hideAllGrowerAdvFilter(){
        Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowEnumerator').setVisible(false);
        Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowHandphone').setVisible(false);
        Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowAge').setVisible(false);
        Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowMaritalStatus').setVisible(false);
        Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCollection').setVisible(false);
        Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCreated').setVisible(false);
        Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateSynced').setVisible(false);
        Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowLastUpdatedDate').setVisible(false);
    }

    function resetAdvancedFilterLs(){
        localStorage.setItem('patchouli_grower_adv_ls', JSON.stringify({
            pAdvRowEnumerator: "",
            pAdvTextEnumerator: "",
            pAdvRowHandphone: "",
            pAdvTextHandphone: "",
            pAdvRowAge: "",
            pAdvOpAge: "",
            pAdvTextAge: "",
            pAdvRowMaritalStatus: "",
            pAdvMaritalStatus: "",
            pAdvRowDateCollection: "",
            pAdvDateCollectionBegin: "",
            pAdvDateCollectionEnd: "",
            pAdvRowDateCreated: "",
            pAdvDateCreatedBegin: "",
            pAdvDateCreatedEnd: "",
            pAdvRowDateSynced: "",
            pAdvDateSyncedBegin: "",
            pAdvDateSyncedEnd: "",
            pAdvRowLastUpdatedDate: "",
            pAdvLastUpdatedBegin: "",
            pAdvLastUpdatedEnd: "",
        }));
    }

    function setAdvancedFilterLs(){
        var dBegin = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionBegin').getValue();
        var dEnd = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionEnd').getValue();
        dBegin = Ext.Date.format(dBegin, 'Y-m-d');
        dEnd = Ext.Date.format(dEnd, 'Y-m-d');
        var dcBegin = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedBegin').getValue();
        var dcEnd = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedEnd').getValue();
        dcBegin = Ext.Date.format(dcBegin, 'Y-m-d');
        dcEnd = Ext.Date.format(dcEnd, 'Y-m-d');
        var dsBegin = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedBegin').getValue();
        var dsEnd = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedEnd').getValue();
        dsBegin = Ext.Date.format(dsBegin, 'Y-m-d');
        dsEnd = Ext.Date.format(dsEnd, 'Y-m-d');
        var luBegin = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateBegin').getValue();
        var luEnd = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateEnd').getValue();
        luBegin = Ext.Date.format(luBegin, 'Y-m-d');
        luEnd = Ext.Date.format(luEnd, 'Y-m-d');

        localStorage.setItem('patchouli_grower_adv_ls', JSON.stringify({
            pAdvRowHandphone: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowHandphone').isVisible(),
            pAdvTextHandphone: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-textHandphone').getValue(),
            pAdvRowAge: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowAge').isVisible(),
            pAdvOpAge: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbOpAge').getValue(),
            pAdvTextAge: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-textAge').getValue(),
            pAdvRowMaritalStatus: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowMaritalStatus').isVisible(),
            pAdvMaritalStatus: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbMaritalStatus').getValue(),
            pAdvRowDateCollection: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCollection').isVisible(),
            pAdvDateCollectionBegin: dBegin,
            pAdvDateCollectionEnd: dEnd,
            pAdvRowDateCreated: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCreated').isVisible(),
            pAdvDateCreatedBegin: dcBegin,
            pAdvDateCreatedEnd: dcEnd,
            pAdvRowDateSynced: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateSynced').isVisible(),
            pAdvDateSyncedBegin: dsBegin,
            pAdvDateSyncedEnd: dsEnd,
            pAdvRowLastUpdatedDate: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowLastUpdatedDate').isVisible(),
            pAdvLastUpdatedBegin: luBegin,
            pAdvLastUpdatedEnd: luEnd,
            pAdvRowEnumerator: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowEnumerator').isVisible(),
            pAdvTextEnumerator: Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbEnumerator').getValue()
        }));
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Grower.WinAdvancedFilter' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Grower.WinAdvancedFilter',
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
        var cmb_filter = Ext.create('Koltiva.store.Grower.CmbAdvancedFilter');
        var cmb_filter_operation = Ext.create('Koltiva.store.Grower.CmbAdvancedFilterOperation');
        var cmb_filter_marital_status = Ext.create('Koltiva.store.Grower.CmbAdvancedFilterMaritalStatus');
        var cmb_enumerator = Ext.create('Koltiva.store.Grower.CmbAdvancedFilterEnumerator');

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
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-cmbFilter',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-cmbFilter[]',
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
                                hideAllGrowerAdvFilter();
                                var filterDipilih = Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbFilter').getValue();
                                if(filterDipilih.length > 0){
                                    for (var i = 0; i < filterDipilih.length; i++) {
                                        switch (filterDipilih[i]) {
                                            case 'Handphone':
                                                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowHandphone').setVisible(true);
                                            break;
                                            case 'Age':
                                                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowAge').setVisible(true);
                                            break;
                                            case 'MaritalStatus':
                                                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowMaritalStatus').setVisible(true);
                                            break;
                                            case 'DateCollection':
                                                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCollection').setVisible(true);
                                            break;
                                            case 'DateCreated':
                                                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCreated').setVisible(true);
                                            break;
                                            case 'DateSynced':
                                                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateSynced').setVisible(true);
                                            break;
                                            case 'LastUpdatedDate':
                                                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowLastUpdatedDate').setVisible(true);
                                            break;
                                            case 'Enumerator':
                                                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowEnumerator').setVisible(true);
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
                id: 'Koltiva.view.Grower.WinAdvancedFilter-rowHandphone',
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
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-textHandphone',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-textHandphone'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Grower.WinAdvancedFilter-rowAge',
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
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-cmbOpAge',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-cmbOpAge',
                        editable: false
                    }]
                },{
                    columnWidth: 0.1,
                    layout: 'form',
                    items:[{
                        xtype:'numberfield',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-textAge',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-textAge'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Grower.WinAdvancedFilter-rowMaritalStatus',
                hidden: true,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Marital Status'),
                    }]
                },{
                    columnWidth: 0.2,
                    layout: 'form',
                    items:[{
                        xtype: 'combobox',
                        store: cmb_filter_marital_status,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-cmbMaritalStatus',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-cmbMaritalStatus',
                        editable: false
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Grower.WinAdvancedFilter-rowDateCollection',
                hidden: true,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Date Collection'),
                    }]
                },{
                    columnWidth: 0.2,
                    layout: 'form',
                    items:[{
                        xtype: 'datefield',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionBegin',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionBegin',
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
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionEnd',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionEnd',
                        format: 'Y-m-d'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Grower.WinAdvancedFilter-rowDateCreated',
                hidden: true,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Date Created'),
                    }]
                },{
                    columnWidth: 0.2,
                    layout: 'form',
                    items:[{
                        xtype: 'datefield',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedBegin',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedBegin',
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
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedEnd',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedEnd',
                        format: 'Y-m-d'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Grower.WinAdvancedFilter-rowDateSynced',
                hidden: true,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Date Synced'),
                    }]
                },{
                    columnWidth: 0.2,
                    layout: 'form',
                    items:[{
                        xtype: 'datefield',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedBegin',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedBegin',
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
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedEnd',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedEnd',
                        format: 'Y-m-d'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Grower.WinAdvancedFilter-rowLastUpdatedDate',
                hidden: true,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Last Updated Date'),
                    }]
                },{
                    columnWidth: 0.2,
                    layout: 'form',
                    items:[{
                        xtype: 'datefield',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateBegin',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateBegin',
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
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateEnd',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateEnd',
                        format: 'Y-m-d'
                    }]
                }]
            },{
                layout: 'column',
                border: false,
                id: 'Koltiva.view.Grower.WinAdvancedFilter-rowEnumerator',
                hidden: true,
                items:[{
                    columnWidth: 0.15,
                    layout: 'form',
                    items:[{
                        xtype: 'label',
                        cls: 'x-form-item-label',
                        text: lang('Enumerator'),
                    }]
                },{
                    columnWidth: 0.2,
                    layout: 'form',
                    items:[{
                        xtype: 'combobox',
                        store: cmb_enumerator,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        id: 'Koltiva.view.Grower.WinAdvancedFilter-cmbEnumerator',
                        name: 'Koltiva.view.Grower.WinAdvancedFilter-cmbEnumerator',
                        editable: false
                    }]
                }]
            }]
        }]

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

            if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowAge').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbOpAge').getValue() == null) isAllFilled = false;
                if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-textAge').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowMaritalStatus').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbMaritalStatus').getValue() == null) isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCollection').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionBegin').getValue() == "" && Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionEnd').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCreated').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedBegin').getValue() == "" && Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedEnd').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateSynced').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedBegin').getValue() == "" && Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedEnd').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowLastUpdatedDate').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateBegin').getValue() == "" && Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateEnd').getValue() == "") isAllFilled = false;
            }
            if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowEnumerator').isVisible() == true) {
                if (Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbEnumerator').getValue() == null) isAllFilled = false;
            }

            var isFilterSelected = true;
            if(
                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowHandphone').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowAge').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowMaritalStatus').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowEnumerator').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCollection').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCreated').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateSynced').isVisible() == false &&
                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowLastUpdatedDate').isVisible() == false
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
                Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().loadPage(1);

                Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter').close(); //tutup popup
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
            Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().loadPage(1);
            Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter').close(); //tutup popup
        }
    },{
        text: lang('Close'),
        icon: varjs.config.base_url + 'images/icons/new/close.png',
        cls: 'Sfr_BtnFormGrey',
        overCls: 'Sfr_BtnFormGrey-Hover',
        handler: function() {
            Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            var patchouli_grower_adv_ls = JSON.parse(localStorage.getItem('patchouli_grower_adv_ls'));
            var filterValue = [];
            console.log(patchouli_grower_adv_ls);

            if(patchouli_grower_adv_ls != null){
                if(patchouli_grower_adv_ls.pAdvRowHandphone == true){
                    filterValue.push('Handphone');
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowHandphone').setVisible(true);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-textHandphone').setValue(patchouli_grower_adv_ls.pAdvTextHandphone);
                }
                if(patchouli_grower_adv_ls.pAdvRowAge == true){
                    filterValue.push('Age');
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowAge').setVisible(true);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbOpAge').setValue(patchouli_grower_adv_ls.pAdvOpAge);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-textAge').setValue(patchouli_grower_adv_ls.pAdvTextAge);
                }
                if(patchouli_grower_adv_ls.pAdvRowMaritalStatus == true){
                    filterValue.push('MaritalStatus');
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowMaritalStatus').setVisible(true);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbMaritalStatus').setValue(patchouli_grower_adv_ls.pAdvMaritalStatus);
                }
                if(patchouli_grower_adv_ls.pAdvRowDateCollection == true){
                    filterValue.push('DateCollection');
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCollection').setVisible(true);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionBegin').setValue(patchouli_grower_adv_ls.pAdvDateCollectionBegin);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCollectionEnd').setValue(patchouli_grower_adv_ls.pAdvDateCollectionEnd);
                }
                if(patchouli_grower_adv_ls.pAdvRowDateCreated == true){
                    filterValue.push('DateCreated');
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateCreated').setVisible(true);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedBegin').setValue(patchouli_grower_adv_ls.pAdvDateCreatedBegin);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateCreatedEnd').setValue(patchouli_grower_adv_ls.pAdvDateCreatedEnd);
                }
                if(patchouli_grower_adv_ls.pAdvRowDateSynced == true){
                    filterValue.push('DateSynced');
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowDateSynced').setVisible(true);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedBegin').setValue(patchouli_grower_adv_ls.pAdvDateSyncedBegin);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateDateSyncedEnd').setValue(patchouli_grower_adv_ls.pAdvDateSyncedEnd);
                }
                if(patchouli_grower_adv_ls.pAdvRowLastUpdatedDate == true){
                    filterValue.push('LastUpdatedDate');
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowLastUpdatedDate').setVisible(true);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateBegin').setValue(patchouli_grower_adv_ls.pAdvLastUpdatedDateBegin);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-dateLastUpdatedDateEnd').setValue(patchouli_grower_adv_ls.pAdvLastUpdatedDateEnd);
                }
                if(patchouli_grower_adv_ls.pAdvRowEnumerator == true){
                    filterValue.push('Enumerator');
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-rowEnumerator').setVisible(true);
                    Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbEnumerator').setValue(patchouli_grower_adv_ls.pAdvTextEnumerator);
                }
            }

            Ext.getCmp('Koltiva.view.Grower.WinAdvancedFilter-cmbFilter').setValue(filterValue);
        }
    }
});