
/*
	Dipakai Di Form Pengiriman
*/

 
Ext.define('Koltiva.view.Traceability.Reception.GridDispatch' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Reception.GridDispatch', 
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: false, 
    collapsible:false, 
    margin:'0 0 0 0',
    initComponent: function() {
        var thisObj = this;
		
        var MainGridTransactionPengiriman = Ext.create('Koltiva.store.Traceability.Reception.MainGridDispatch');
		thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability.Reception.GridDispatch-Grid',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            minHeight:300,
                            store: MainGridTransactionPengiriman,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [
							{
                                xtype: 'toolbar',
                                dock:'top',
                                items: []
                            }],
                            columns: [{
                                text: lang('Despatch Number'),
                                dataIndex : 'DespatchNumber',
                                flex:1
                            },{
                                text: lang('Despatch Number'),
                                dataIndex : 'DespatchNumber',
                                flex:1
                            },{
                                text: lang('Company Name'),
                                dataIndex: 'CompanyName',
                                flex:1,
                            },{
                                text: lang('Product Name'),
                                dataIndex: 'ProductName',
                                flex:1,
                            },{
                                text: lang('Despatch Volume'),
                                dataIndex: 'DespatchVolume',
                                flex:1,
                            }] 
				}];
				this.callParent(arguments);
    }
});

 