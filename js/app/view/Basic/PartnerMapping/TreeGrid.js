Ext.define('Koltiva.view.Basic.PartnerMapping.TreeGrid', {
    extend: 'Ext.tree.Panel',
    renderTo: 'ext-content',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
        'Ext.tree.*',
        // 'Ext.ux.CheckColumn',
        'Koltiva.model.Basic.PartnerMapping.List',
        'Koltiva.view.Basic.PartnerMapping.Form',
    ],    
    xtype: 'tree-grid',    
    
    title: 'Partner Mapping',
    height: 600,
    width: '100%',
    useArrows: true,
    rootVisible: false,
    multiSelect: false,
    singleExpand: false,
    
    initComponent: function() {
        Ext.apply(this, {
            store: Ext.create('Koltiva.store.Basic.PartnerMapping.List'),
            columns: [{
                xtype: 'treecolumn', //this is so we know which column will show the tree
                text: 'Partner Mapping',
                flex: 2,
                sortable: true,
                dataIndex: 'PartnerFullName'
            }
            ],
            dockedItems: [
                {
                    xtype: 'toolbar',
                    items: [
                        {
                            icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                            text: lang('Edit'),
                            scope: this,
                            hidden: !m_act_update,
                            handler: this.onEditClick,
                        }
                    ]
                }
            ]
        });
        this.callParent();
    },
    
    onEditClick: function() {
        if (grid.getSelectionModel().getSelection().length === 0) {
            Ext.Msg.alert('Warning', lang('Please select data to edit'));
            return false;
        }
        var frm = Ext.create('Koltiva.view.Basic.PartnerMapping.Form');
        selectedId = grid.getSelectionModel().getSelection()[0].data.PartnerID;
        console.log(selectedId+'test');
        frm.getForm().load({method: 'GET', params: {PartnerID: selectedId}});
        win = Ext.create('Ext.Window',{
            title: lang('Partner Mapping'),
            closable: true,
            modal: true,
            autoScroll: true,
            width: '40%',
            items:[frm]
        }).show();
    }
});