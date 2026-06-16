/*
* @Author: nikolius
* @Date:   2017-10-26 17:09:05
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-06 09:26:12
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

//override time out ajax exts js yg cuman 30 detikan
Ext.Ajax.timeout = 1200000;
Ext.override(Ext.form.Basic, {
    timeout: Ext.Ajax.timeout / 1000
});
Ext.override(Ext.data.proxy.Server, {
    timeout: Ext.Ajax.timeout
});
Ext.override(Ext.data.Connection, {
    timeout: Ext.Ajax.timeout
});

Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();

    var store_main_grid = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','District','DateEstablishedGrid','DescriptionGrid','labelCH','ProgSert','TypeOfBean'],
        autoLoad: true,
        pageSize: 50,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/ims/main_list_master',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.searchDesc = Ext.getCmp('mainGridSearchDesc').getValue();
            }
        }
    });

    /*============================================ Function (Begin) ==================================================*/
    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store_main_grid.load({
                params: {
                    page: 1,
                    start: 0,
                    limit: 50
                }
            });
            store_main_grid.loadPage(1);
        }
    }
    /*============================================ Function (End)   ==================================================*/

    var contextMenuMainGrid = Ext.create('Ext.menu.Menu', {
        cls: 'Sfr_ConMenu',
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var sm = Ext.getCmp('mainGridObj').getSelectionModel().getSelection()[0];
                    displayFormImsEvent('view', sm.get('id'), store_main_grid);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {
                    var sm = Ext.getCmp('mainGridObj').getSelectionModel().getSelection()[0];
                    displayFormImsEvent('update', sm.get('id'), store_main_grid);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('mainGridObj').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ims/ims_event',
                                method: 'DELETE',
                                params: {
                                    IMSMasterID: sm.get('id')
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
                                    store_main_grid.load();
                                },
                                failure: function (response, opts) {
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

    var main_grid = Ext.create('Ext.grid.Panel', {
        store: store_main_grid,
        width: '99%',
        id: 'mainGridObj',
        minHeight: 250,
        cls: 'Sfr_GridNew',
        title: lang('IMS Certificate Holder'),
        style: 'border:1px solid #CCC;margin:10px;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store_main_grid,
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png',
                        text: lang('Add'),
                        cls: 'Sfr_BtnGridGreen',
                        overCls: 'Sfr_BtnGridGreen-Hover',
                        hidden: m_act_add,
                        scope: this,
                        handler: function () {
                            displayFormImsEvent('insert', null, store_main_grid);
                        }
                    }, {
                        xtype: 'textfield',
                        emptyText: lang('Description'),
                        width: 280,
                        name: 'mainGridSearchDesc',
                        baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'mainGridSearchDesc',
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    }, {
                        xtype: 'button',
                        margin: '0px 0px 0px 6px',
                        icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                        cls: 'Sfr_BtnGridGreen',
                        overCls: 'Sfr_BtnGridGreen-Hover',
                        text: lang('Search'),
                        handler: function () {
                            store_main_grid.load({
                                params: {
                                    page: 1,
                                    start: 0,
                                    limit: 50
                                }
                            });
                            store_main_grid.loadPage(1);
                        }
                    }]
            }],
        columns: [{
                text: '',
                xtype: 'actioncolumn',
                width: '4%',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                            contextMenuMainGrid.showAt(e.getXY());
                        }
                    }]
            }, {
                dataIndex: 'id',
                hidden: true
            }, {
                dataIndex: 'labelCH',
                text: lang('Certificate Holder'),
                flex: 2 
            }, {
                dataIndex: 'District',
                text: lang('District'),
                flex: 1
            }, {
                dataIndex: 'ProgSert',
                text: lang('Certificate Program'),
                width: '15%'
            }, {
                dataIndex: 'TypeOfBean',
                text: lang('Type of Bean'),
                width: '10%'
            }, {
                dataIndex: 'DescriptionGrid',
                text: lang('Description'),
                flex: 2
            }, {
                dataIndex: 'DateEstablishedGrid',
                text: lang('Date Established'),
                width: '9%'
            }]
    });
});