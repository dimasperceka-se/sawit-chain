Ext.define('Koltiva.view.Partner.PanelExternalProgram', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Partner.PanelExternalProgram',
    style: 'margin-left:5px;margin-top:15px',
    title: lang('External Program'),
    frame: true,
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
        }
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Koltiva.store.Partner.MainGridPanelExternalProgram', {
            storeVar: {
                PartnerID: thisObj.viewVar.PartnerID
            }
        });

        thisObj.ContextMenu = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Partner.PanelExternalProgram-MainGrid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/partner_new/external_program',
                                method: 'DELETE',
                                params: {
                                    PartnerID: thisObj.viewVar.PartnerID,
                                    BuInExID: sm.get('BuInExID')
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
                                    thisObj.MainGrid.load();
                                },
                                failure: function (rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    } catch (err) {
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: lang('Connection Error'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Partner.PanelExternalProgram-MainGrid',
            cls: 'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            style: 'border:1px solid #CCC;',
            store: thisObj.MainGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            height: 300,
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    handler: function () {
                        var WinFormExternalProgram = Ext.create('Koltiva.view.Partner.WinFormExternalProgram', {
                            viewVar: {
                                OpsiDisplay: 'insert',
                                PartnerID: thisObj.viewVar.PartnerID,
                                BuInExID: null,
                                BuInExName: null,
                                CallerStore: thisObj.MainGrid
                            }
                        });
                        if (!WinFormExternalProgram.isVisible()) {
                            WinFormExternalProgram.center();
                            WinFormExternalProgram.show();
                        } else {
                            WinFormExternalProgram.close();
                        }
                    }
                }]
            }],
            columns: [{
                text: ' ',
                xtype: 'actioncolumn',
                width: '10%',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                            thisObj.ContextMenu.showAt(e.getXY());
                        }
                    }]
            },{
                text: lang('BuInExID'),
                dataIndex: 'BuInExID',
                hidden: true
            },{
                text: lang('Program Name'),
                dataIndex: 'BuInExName',
                width: '89%'
            }]
        }];

        this.callParent(arguments);
    }
});