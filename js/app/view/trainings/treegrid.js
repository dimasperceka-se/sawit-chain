Ext.define('Koltiva.view.trainings.treegrid', {
    extend: 'Ext.tree.Panel',
    renderTo: 'ext-content',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
        'Ext.tree.*',
        // 'Ext.ux.CheckColumn',
        'Koltiva.model.trainings.list',
        'Koltiva.view.trainings.form',
    ],    
    xtype: 'tree-grid',    
    
    title: 'Trainings',
    height: 600,
    width: '100%',
    useArrows: true,
    rootVisible: false,
    multiSelect: false,
    singleExpand: false,
    
    initComponent: function() {
        Ext.apply(this, {
            store: Ext.create('Koltiva.store.trainings.list'),
            columns: [{
                xtype: 'treecolumn', //this is so we know which column will show the tree
                text: 'Trainings',
                flex: 2,
                sortable: true,
                dataIndex: 'CpgTrainings'
            },{
                text: 'Abbreviation',
                flex: 1,
                dataIndex: 'CpgAbbre',
                sortable: true
            }, 
            ],
            dockedItems: [
                {
                    xtype: 'toolbar',
                    items: [
                        {
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            text: lang('Add'),
                            scope: this,
                            hidden: !m_act_add,
                            handler: this.onAddClick,
                        },
                        {
                            icon: varjs.config.base_url+'images/icons/silk/pencil.png', 
                            //cls:'Sfr_BtnGridBlue', 
                            //overCls:'Sfr_BtnGridBlue-Hover',
                            text: lang('Edit'),
                            scope: this,
                            hidden: !m_act_update,
                            handler: this.onEditClick,
                        },
                        {
                            icon: varjs.config.base_url+'images/icons/silk/delete.png',
                            text: lang('Delete'),
                            scope: this,
                            hidden: !m_act_delete,
                            handler: this.onDeleteClick,
                        },
                    ]
                }
            ]
        });
        this.callParent();
    },
    
    onAddClick: function() {
        var frm = Ext.create('Koltiva.view.trainings.form');
        win = Ext.create('Ext.Window',{
            title: lang('Trainings'),
            closable: true,
            modal: true,
            autoScroll: true,
            width: '30%',
            items:[frm]
        }).show();
    },
    
    onEditClick: function() {
        if (grid.getSelectionModel().getSelection().length === 0) {
            Ext.Msg.alert('Warning', lang('Please select data to edit'));
            return false;
        }
        var frm = Ext.create('Koltiva.view.trainings.form');
        selectedId = grid.getSelectionModel().getSelection()[0].data.CpgTrainingsID;
        frm.getForm().load({method: 'GET', params: {CPGTrainingsID: selectedId}});
        win = Ext.create('Ext.Window',{
            title: lang('Trainings'),
            closable: true,
            modal: true,
            autoScroll: true,
            width: '30%',
            items:[frm]
        }).show();
    },
    
    onDeleteClick: function() {
        var data = grid.getSelectionModel().getSelection()[0].data;
        console.log(data);
        Ext.MessageBox.confirm('Message', data.CpgTrainings + '<br/>' + lang('Apakah anda mau menghapus data ini?'), function (btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_crud,
                    method: 'DELETE',
                    params: {
                        CpgTrainingsID: data.CpgTrainingsID
                    },
                    success: function (response, opts) {
                        var obj = Ext.decode(response.responseText);                        
                        Ext.MessageBox.alert('Success', obj.msg);
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