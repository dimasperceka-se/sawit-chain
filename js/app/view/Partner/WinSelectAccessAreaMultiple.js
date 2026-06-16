Ext.define('Koltiva.view.Partner.WinSelectAccessAreaMultiple' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Partner.WinSelectAccessAreaMultiple',
    title: lang('Region'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '82%',
    height: 600,
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Store ========================= (Begin)
        thisObj.CmbFilterCountry = Ext.create('Koltiva.store.ComboGeneral.CmbFilterCountry');

        thisObj.StoreGridMain = Ext.create('Koltiva.store.Partner.GridWinSelectAccessAreaMultiple',{
            storeVar: {
                PartnerID: thisObj.viewVar.PartnerID,
                TxtSearch: null,
                CmbFilterCountry: null
            }
        });
        //Store ========================= (End)

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Partner.WinSelectAccessAreaMultiple-MainGrid',
            style: 'border:1px solid #CCC;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            height:500,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            selModel: {
                selType: 'checkboxmodel',
                checkOnly: true,
                multiSelect: true,
                mode: "MULTI",
                headerWidth: 50
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true,
                style:'padding-right:12px;'
            },{
                xtype: 'toolbar',
                items: [{
                    store: thisObj.CmbFilterCountry,
                    editable: false,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.Partner.WinSelectAccessAreaMultiple-CmbFilterCountry',
                    name: 'Koltiva.view.Partner.WinSelectAccessAreaMultiple-CmbFilterCountry',
                    emptyText: lang('All Country'),
                    style: 'margin-top:5px;',
                    listeners: {
                        change: function (cb, nv, ov) {
                            return false;
                        }
                    }
                },{
                    name: 'Koltiva.view.Partner.WinSelectAccessAreaMultiple-TxtSearchLabel',
                    id: 'Koltiva.view.Partner.WinSelectAccessAreaMultiple-TxtSearchLabel',
                    xtype: 'textfield',
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    width: 350,
                    emptyText: lang('Search by District Name')
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    text: lang('Search'),
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        thisObj.StoreGridMain.storeVar.TxtSearch = Ext.getCmp('Koltiva.view.Partner.WinSelectAccessAreaMultiple-TxtSearchLabel').getValue();
                        thisObj.StoreGridMain.storeVar.CmbFilterCountry = Ext.getCmp('Koltiva.view.Partner.WinSelectAccessAreaMultiple-CmbFilterCountry').getValue();
                        thisObj.StoreGridMain.load();
                    }
                }]
            }],
            columns: [{
                dataIndex: 'DistrictID',
                hidden: true
            },{
                text: 'No',
                xtype: 'rownumberer',
                flex: 0.1
            },{
                text: lang('Country'),
                dataIndex: 'CountryName',
                flex: 1
            },{
                text: lang('Province'),
                dataIndex: 'ProvinceName',
                flex: 1
            },{
                text: lang('District'),
                dataIndex: 'DistrictName',
                flex: 1
            }]
        }];

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            text: lang('Add to List'),
            handler: function () {
                let group_access_area = JSON.parse(localStorage.getItem('appkolti_group_access_area'));
                let selection = Ext.getCmp('Koltiva.view.Partner.WinSelectAccessAreaMultiple-MainGrid').getSelectionModel().getSelection();
                let districts = [];

                if(selection.length > 0) {
                    // Add data yang sudah diselect
                    if (group_access_area != null) {
                        if (group_access_area.itemAdded != null) {
                            SelectPart = group_access_area.itemAdded;
                            Ext.each(SelectPart, function (row, index, value) {
                                districts.push(row);
                            });
                        }
                    }
                    
                    // Add ke data yang baru diselect
                    Ext.each(selection, function (row, index, value) {
                        districts.push(row.data.DistrictID);
                    });

                    // Update value local storage delete
                    try {
                        if (Array.isArray(districts) && Array.isArray(group_access_area.itemDeleted)) {
                            group_access_area.itemDeleted = group_access_area.itemDeleted.filter(x => !districts.includes(x));
                        }
                    }
                    catch(err) {
                        console.log(`object null`);
                    }

                    // Simpan kedalam Store
                    //Set LocalStorage ================================= (Begin)
                    localStorage.setItem('appkolti_group_access_area', JSON.stringify({
                        itemAdded: districts,
                        itemDeleted: (group_access_area != null) ? group_access_area.itemDeleted : null
                    }));
                    //Set LocalStorage ================================= (End)

                    // Load Data Select Multiple
                    // thisObj.StoreGridMain.storeVar.GroupId = thisObj.viewVar.GroupId;
                    // thisObj.StoreGridMain.storeVar.TxtSearch = Ext.getCmp('Koltiva.view.Partner.WinSelectAccessAreaMultiple-TxtSearchLabel').getValue();
                    // thisObj.StoreGridMain.storeVar.CmbFilterCountry = Ext.getCmp('Koltiva.view.Partner.WinSelectAccessAreaMultiple-CmbFilterCountry').getValue();
                    // thisObj.StoreGridMain.storeVar.CmbFilterProvince = Ext.getCmp('Koltiva.view.Partner.WinSelectAccessAreaMultiple-CmbFilterProvince').getValue();
                    // thisObj.StoreGridMain.load();

                    // Load grid parent
                    thisObj.viewVar.ParentGrid.load();

                    // Close
                    thisObj.close();
                } else {
                    Ext.MessageBox.show({
                        title: lang('Information'),
                        msg: lang('No region selected'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            text: lang('Add All Region to List'),
            hidden:true,
            handler: function () {
                //Cek apakah ada datanya
                if(thisObj.StoreGridMain.getRange().length > 0) {
                    localStorage.setItem('appkolti_group_access_area', JSON.stringify({
                        selectAll: true,
                        options: {
                            filterTxtSearch: null,
                            filterCountry: thisObj.viewVar.CountryID,
                            filterDistrict: null
                        }
                    }));
                } else {
                    Ext.MessageBox.show({
                        title: lang('Information'),
                        msg: lang('No region'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            text: lang('Close'),
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});