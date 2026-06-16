Ext.define('Koltiva.view.Basic.NewBank.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Basic.NewBank.MainGrid',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:-12px 0 0 0;',
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid').setFilterLs();
            Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank').getStore().loadPage(1);
        }
    },
    setFilterLs: function () {
        localStorage.setItem('palm_newbank_ls', JSON.stringify({
            opsiCall: 'simple',
            ptextSearch: Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank-textSearch').getValue()
        }));
    },
    SetGridColumnCustom: function (ColDisplayArr) {
        var thisObj = this;

        //Set Hide Semua terlebih dahulu
        thisObj.SetGridColumnHideAll();

        if (ColDisplayArr.length > 0) {
            for (var i = 0; i < ColDisplayArr.length; i++) {
                Ext.getCmp(ColDisplayArr[i]).setVisible(true);
            }
        }
    },
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;

            //Div nya Filter Region
//            document.getElementById('divCommonContentRegion').style.display = 'none';

            var palm_newbank_ls = JSON.parse(localStorage.getItem('palm_newbank_ls'));
            if (palm_newbank_ls != null) {
                if (palm_newbank_ls.opsiCall != undefined) {
                    if (palm_newbank_ls.opsiCall == "advanced") {
                        Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank-BtnSimplifiedGrid').setVisible(true);
                    } else {
                        Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank-BtnSimplifiedGrid').setVisible(false);
                    }
                }

                if (palm_newbank_ls.ptextSearch != undefined) {
                    Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank-textSearch').setValue(palm_newbank_ls.ptextSearch);
                }
            }

            var palm_newbank_grid_ls = JSON.parse(localStorage.getItem('palm_newbank_grid_ls'));
            if (palm_newbank_grid_ls != null) {
                if (palm_newbank_grid_ls.opsiShow != undefined) {
                    if (palm_newbank_grid_ls.opsiShow == "custom") {
                        //Sesuaikan
                        thisObj.SetGridColumnCustom(palm_newbank_grid_ls.ColDisplayArr);
                        Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank-BtnSimplifiedGrid').setVisible(true);
                    } else {
                        //Tampilan Grid Column Default
                        thisObj.SetGridColumnDefault();
                        Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank-BtnSimplifiedGrid').setVisible(false);
                    }
                }
            }
        }
    },
    initComponent: function () {
        var thisObj = this;

        //Define Store Main Grid
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Basic.NewBank.MainGrid');

        //Context Menu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank').getSelectionModel().getSelection()[0];
                        
                        Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid').destroy(); //destory current view
                        var FormMainNewBank = [];

                        //create object View untuk FormMainGrower
                        if (Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm') == undefined) {
                            FormMainNewBank = Ext.create('Koltiva.view.Basic.NewBank.MainForm', {
                                viewVar: {
                                    opsiDisplay: 'update',
                                    BankID: sm.get('BankID')
                                }
                            });
                        } else {
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm').destroy();
                            FormMainNewBank = Ext.create('Koltiva.view.Basic.NewBank.MainForm', {
                                viewVar: {
                                    opsiDisplay: 'update',
                                    BankID: sm.get('BankID')
                                }
                            });
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/bank/newbank_remove',
                                    method: 'DELETE',
                                    params: {
                                        BankID: sm.get('BankID')
                                    },
                                    success: function (response, opts) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data deleted'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //refresh store
                                        thisObj.StoreGridMain.load();
                                    },
                                    failure: function (response, o) {
                                        var pesanNya;
                                        if (o.result.message != undefined) {
                                            pesanNya = o.result.message;
                                        } else {
                                            pesanNya = lang('Connection error');
                                        }
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: pesanNya,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                });
                            }
                        });

                    }
                }]
        });

        thisObj.items = [{
                layout: 'column',
                border: false,
                items: [
                    {
                        columnWidth: 0.3,
                        layout: 'form',
                        items: [{}]
                    }, {
                        columnWidth: 0.7,
                        xtype: 'panel',
                        frame: false,
                        id: 'Koltiva.view.Basic.NewBank.MainGrid-gridInformation',
                        hidden: true,
                        html: ''
                    }
                ]
            }, {
                xtype: 'grid',
                id: 'Koltiva.view.Basic.NewBank.MainGrid-NewBank',
                style: 'border:1px solid #CCC;margin-top:4px;',
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
                enableColumnHide: false,
                //height: 550,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreGridMain,
                        dock: 'bottom',
                        displayInfo: true,
                        displayMsg: 'Showing {0} to {1} of {2} entries'
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                text: lang('Add'),
                                cls: 'Sfr_BtnGridGreen',
                                overCls: 'Sfr_BtnGridGreen-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid').destroy(); //destory current view
                                    var FormMainNewBank = [];

                                    //create object View untuk FormMainGrower
                                    if (Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm') == undefined) {
                                        FormMainNewBank = Ext.create('Koltiva.view.Basic.NewBank.MainForm', {
                                            viewVar: {
                                                opsiDisplay: 'insert'
                                            }
                                        });
                                    } else {
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm').destroy();
                                        FormMainNewBank = Ext.create('Koltiva.view.Basic.NewBank.MainForm', {
                                            viewVar: {
                                                opsiDisplay: 'insert'
                                            }
                                        });
                                    }
                                }
                            }, {
                                xtype: 'tbspacer',
                                flex: 1
                            }, {
                                name: 'key',
                                id: 'Koltiva.view.Basic.NewBank.MainGrid-NewBank-textSearch',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 400,
                                emptyText: lang('Search by BankCode / BankName') + ', ' + lang('press_enter_search'),
                                listeners: {
                                    specialkey: thisObj.submitOnEnterGrid
                                }
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/visible-field.png',
                                text: lang('Default Grid'),
                                hidden: true,
                                cls: 'Sfr_BtnGridPaleBlue',
                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                id: 'Koltiva.view.Basic.NewBank.MainGrid-NewBank-BtnSimplifiedGrid',
                                handler: function () {
                                    localStorage.setItem('palm_newbank_ls', JSON.stringify({
                                        opsiCall: 'simple',
                                        ptextSearch: Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank-textSearch').getValue()
                                    }));

                                    localStorage.setItem('palm_newbank_grid_ls', JSON.stringify({
                                        opsiShow: 'default'
                                    }));
                                    thisObj.SetGridColumnDefault();

                                    thisObj.StoreGridMain.load();

                                    //Hilangkan Tombol
                                    Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank-BtnSimplifiedGrid').setVisible(false);
                                }
                            },
                            {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid-NewBank').getStore().loadPage(1);
                                }
                            }]
                    }],
                columns: [
                    {
                        text: '',
                        xtype: 'actioncolumn',
                        width: '4%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    thisObj.ContextMenuGrid.showAt(e.getXY());
                                }
                            }]
                    },
                    {
                        text: 'No',
                        xtype: 'rownumberer',
                        width: '5%'
                    }, {
                        text: lang('BankCode'),
                        dataIndex: 'BankCode',
                        id: 'Koltiva.view.Basic.NewBank.MainGrid-NewBank-ColBankCode',
                        flex: 1
                    }, {
                        text: lang('BankName'),
                        dataIndex: 'BankName',
                        id: 'Koltiva.view.Basic.NewBank.MainGrid-NewBank-ColBankName',
                        flex: 2
                    }, {
                        text: lang('BankDesc'),
                        dataIndex: 'BankDesc',
                        id: 'Koltiva.view.Basic.NewBank.MainGrid-NewBank-ColBankDesc',
                        flex: 2
                    }
                ]
            }];

        this.callParent(arguments);
    }
});