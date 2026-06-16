Ext.define('Koltiva.view.Basic.Kml.list' ,{
    id: 'Koltiva.view.Basic.Kml.list',
    extend: 'Ext.grid.Panel',
    renderTo:'ext-content',
    width: '100%',
    minHeight: 550,
    style: 'border:1px solid #CCC;',
    renderTo: 'ext-content',
    loadMask: true,
    selType: 'rowmodel',
    initComponent: function() {
        this.store = Ext.create('Koltiva.store.Basic.Kml.list');

        this.dockedItems = [{
            xtype: 'pagingtoolbar',
            store: this.store, 
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: !m_act_add,
                scope: this,
                handler: this.onAddClick
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: !m_act_update,
                scope: this,
                handler: this.onEditClick
            }, {
                itemId: 'remove',
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                cls: m_act_delete,
                hidden: !m_act_delete,
                text: lang('Hapus'),
                scope: this,
                handler: this.onDeleteClick
            }, {
                name: 'sKey',
                id: 'sKey',
                xtype: 'textfield',
                emptyText: lang('Cari berdasar nama/ID')
            }, {
                id: 'sProvinsi',
                name: 'sProvinsi',
                xtype: 'combo',
                store: Ext.create('Koltiva.store.provinsi'),
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                hidden: false,
                value: m_param,
                emptyText: lang('Province'),
                listeners: {
                }
            }, {
                id: 'sCategory',
                name: 'sCategory',
                xtype: 'combo',
                store: Ext.create('Koltiva.store.Basic.Kml.category'),
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                emptyText: lang('Category')
            }, {
                xtype: 'button',
                id: 'btnSimpleSearch',
                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                margin: '0px 0px 0px 6px',
                text: lang('Search'),
                handler: function() {
                    var grid = Ext.getCmp('Koltiva.view.Basic.Kml.list');
                    grid.store.load({
                        params: {
                            ProvinceID: Ext.getCmp('sProvinsi').getValue(),
                            CategoryID: Ext.getCmp('sCategory').getValue(),
                            key: Ext.getCmp('sKey').getValue(),
                        }
                    })
                }
            },
            ]
        }];

        this.columns = [
        {
            text: lang('ID'),
            dataIndex: 'id',
            hidden: true
        }, {
            text: lang('No'),
            xtype: 'rownumberer',
            width: '50'
        }, {
            text: lang('Name'),
            flex: 1,
            dataIndex: 'Name'
        }, {
        //     text: lang('File'),
        //     flex: 1,
        //     dataIndex: 'File'
        // }, {
            text: lang('Province'),
            flex: 1,
            dataIndex: 'Province'
        }, {
            text: lang('District'),
            flex: 1,
            dataIndex: 'District'
        }, {
            text: lang('SubDistrict'),
            flex: 1,
            dataIndex: 'SubDistrict'
        }, {
            text: lang('Village'),
            flex: 1,
            dataIndex: 'Village'
        }, {
            text: lang('Category'),
            flex: 1,
            dataIndex: 'category'
        }, 
        ];

        this.callParent(arguments);
    },    
    
    onAddClick: function() {
        if(Ext.getCmp('Koltiva.view.Basic.Kml.form'))Ext.getCmp('Koltiva.view.Basic.Kml.form').destroy();
        var frm = Ext.create('Koltiva.view.Basic.Kml.form');
        win = Ext.create('Ext.Window',{
            title: lang('Upload KML'),
            closable: true,
            modal: true,
            autoScroll: true,
            width: '30%',
            items:[frm]
        }).show();
    },
    
    onEditClick: function() {
        if(Ext.getCmp('Koltiva.view.Basic.Kml.form'))Ext.getCmp('Koltiva.view.Basic.Kml.form').destroy();
        var grid = Ext.getCmp('Koltiva.view.Basic.Kml.list');
        if (grid.getSelectionModel().getSelection().length === 0) {
            Ext.Msg.alert('Warning', lang('Please select data to edit'));
            return false;
        }
        var frm = Ext.create('Koltiva.view.Basic.Kml.form');
        selectedId = grid.getSelectionModel().getSelection()[0].data.ID;
        frm.getForm().load({method: 'GET', params: {ID: selectedId}});
        win = Ext.create('Ext.Window',{
            title: lang('Upload KML'),
            closable: true,
            modal: true,
            autoScroll: true,
            width: '30%',
            items:[frm]
        }).show();
    },
    
    onDeleteClick: function() {
        var grid = Ext.getCmp('Koltiva.view.Basic.Kml.list');
        var data = grid.getSelectionModel().getSelection()[0].data;
        Ext.MessageBox.confirm('Message', data.Name + '<br/>' + lang('Apakah anda mau menghapus data ini?'), function (btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/basic/kml',
                    method: 'DELETE',
                    params: {
                        ID: data.ID
                    },
                    success: function (response, opts) {
                        var obj = Ext.decode(response.responseText);                        
                        Ext.MessageBox.alert('Success', 'KML has been deleted');
                        grid.store.load();
                    },
                    failure: function (response, opts) {
                        var obj = Ext.decode(response.responseText);
                        Ext.MessageBox.alert('Error', obj.msg);
                    }
                });
            }
        });
    },
});
