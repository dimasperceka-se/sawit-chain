Ext.define('Scpp.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.datagrid',

    initComponent: function(){

        this.editing = Ext.create('Ext.grid.plugin.CellEditing');

        Ext.apply(this, {
            iconCls: 'icon-grid',
            frame: true,
            plugins: [this.editing],
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    iconCls: 'icon-add',
                    text: 'Add',
                    scope: this,
                    handler: this.onAddClick
                }]
            }],
            columns: [{
                text: 'ID',
                width: 40,
                sortable: true,
                resizable: false,
                draggable: false,
                hideable: false,
                menuDisabled: true,
                dataIndex: 'UnitId'
            }, {
                header: 'Nama',
                flex: 1,
                sortable: true,
                dataIndex: 'UnitName',
                field: {
                    type: 'textfield'
                }
            }, {
                header: 'Deskripsi',
                flex: 1,
                sortable: true,
                dataIndex: 'UnitDescription',
                field: {
                    type: 'textfield'
                }
            }]
        });
        this.callParent();
        this.getSelectionModel().on('selectionchange', this.onSelectChange, this);
    }
});

Ext.define('Scpp.Model', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'UnitId',
        type: 'int',
        useNull: true
    }, 'UnitName', 'UnitDescription'],
    validations: [{
        type: 'length',
        field: 'Nama',
        min: 1
    }]
});

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            api: {
                read: m_read,
                create: m_create,
                update: m_update,
                destroy: m_destroy
            },
            reader: {
                type: 'json',
                successProperty: 'success',
                root: 'data',
                messageProperty: 'message'
            }
        }
    });

    var main = Ext.create('Ext.container.Container', {
        padding: '0 0 0 20',
        width: 700,
        height: 450,
        renderTo: 'ext-content',
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        items: [{
            itemId: 'grid',
            xtype: 'datagrid',
            title: 'Unit',
            flex: 1,
            store: store
        }]
    });
});
