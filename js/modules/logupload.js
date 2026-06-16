Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['logID', 'logCategory', 'logFileName', 'logStatus', 'UserExecuted', 'UserRealName', 'DateExecuted'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            params: {
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id: 'grid',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                        fieldLabel: 'Key',
                        labelWidth: 30,
                        xtype: 'textfield',
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    }, {
                        xtype: 'button',
                        margin: '0px 0px 0px 6px',
                        text: 'Search',
                        handler: function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue()
                                }
                            });
                        }
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
                        margin: '0px 0px 0px 6px',
                        text: 'Export',
                        handler: function() {
                            window.open(m_cetak_xls_log_upload + '/' + Ext.getCmp('key').getValue());
                        }
                    }]
            }],
        columns: [
            {
                dataIndex: 'UnitId',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            },
            {
                text: 'Log Category',
                width: '10%',
                dataIndex: 'logCategory'
            },
            {
                text: 'File Name',
                width: '35%',
                dataIndex: 'logFileName'
            },
            {
                text: 'Status',
                width: '10%',
                dataIndex: 'logStatus'
            },
            {
                text: 'User',
                width: '20%',
                dataIndex: 'UserRealName'
            },
            {
                text: 'Date',
                width: '20%',
                dataIndex: 'DateExecuted'
            }
        ]
    });
    
    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue()
                }
            });
        }
    }
});
