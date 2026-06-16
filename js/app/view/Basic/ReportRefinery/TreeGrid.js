Ext.define('Koltiva.view.Basic.ReportRefinery.TreeGrid', {
    extend: 'Ext.tree.Panel',
    renderTo: 'ext-content',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
        'Ext.tree.*',
        // 'Ext.ux.CheckColumn',
        'Koltiva.model.Basic.ReportRefinery.List',
        'Koltiva.view.Basic.ReportRefinery.Form',
    ],    
    xtype: 'tree-grid',
    title: 'Report Mill Refinery',
    height: 700,
    width: '100%',
    useArrows: true,
    rootVisible: false,
    multiSelect: false,
    singleExpand: false,
    initComponent: function() {
        Ext.apply(this, {
            store: Ext.create('Koltiva.store.Basic.ReportRefinery.List'),
            dockedItems: [{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    xtype: 'button',
                    id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-gridToolbar-BtnExport',
                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                    margin: '0px 10px 0px 6px',
                    text: lang('Export'),
                    cls:'Sfr_BtnGridGreen',
                    overCls:'Sfr_BtnGridGreen-Hover',
                    handler: function() {

                        Ext.MessageBox.show({
                            msg: 'Please wait...',
                            progressText: 'Exporting...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-download', //custom class in msg-box.html
                            animateTarget: 'mb7'
                        });

                        try {
                            Ext.destroy(Ext.get('downloadIframe'));
                        }
                        catch(e) {}

                        Ext.Ajax.request({
                            url: m_api+'/report_mill_refinery/export_excel_refinery',
                        
                            method: 'GET',
                            waitMsg: lang('Please Wait'),
                            timeout: 360000,
                            success: function(data) {
                                Ext.MessageBox.hide();
                                var jsonResp = JSON.parse(data.responseText);
                                window.location = jsonResp.filenya;
                            },
                            failure: function() {
                                Ext.MessageBox.hide();
                                Ext.MessageBox.show({
                                    title: 'Notifications',
                                    msg: 'Failed to export, Please try again.',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                        
                    }
                }, {
                    xtype: 'container',
                    flex: 1
                }]
            }],
            columns: [
                {
                    xtype: 'treecolumn', 
                    text: lang('Organization'), 
                    icon: varjs.config.base_url + 'images/icons/silk/user_star.png',
                    flex: 1,
                    sortable: true, 
                    dataIndex: 'Name',
                    leaf:false,
                    cls: 'Sfr_BtnFormGrey'
                },
                {
                    text: lang('Total Transaction'),
                    dataIndex: 'total_transaction',
                    sortable: true, 
                    flex: 1,
                    cls: 'Sfr_BtnGridPaleBlue',
                    align: 'center'
                },
                {
                    text: lang('Capacity Deliver Net (KG)'),
                    dataIndex: 'VolumeNetto',
                    sortable: true, 
                    flex: 1,
                    cls: 'Sfr_BtnFormGrey',
                    align: 'center'
                },
                {
                    text: lang('Detail Transaction'),
                    dataIndex: 'SupplyOrgID',
                    sortable: true, 
                    flex: 1,
                    cls: 'Sfr_BtnGridPaleBlue',
                    align: 'center'
                },
                {
                    text: lang('Total Farmer'),
                    dataIndex: 'total_farmer',
                    sortable: true, 
                    flex: 1,
                    cls: 'Sfr_BtnFormGrey',
                    align: 'center'
                }
            ],
        });
        
        this.callParent();
    },  
    onViewClick: function() {
        if (grid.getSelectionModel().getSelection().length === 0) {
            Ext.Msg.alert('Warning', lang('Please select data to view'));
            return false;
        }
        var frm = Ext.create('Koltiva.view.Basic.ReportRefinery.Form');
        selectedId = grid.getSelectionModel().getSelection()[0].data.PartnerID;
        console.log(selectedId+'test');
        frm.getForm().load({method: 'GET', params: {PartnerID: selectedId}});
        win = Ext.create('Ext.Window',{
            title: lang('Report'),
            closable: true,
            modal: true,
            autoScroll: true,
            width: '40%',
            items:[frm]
        }).show();
    }
});