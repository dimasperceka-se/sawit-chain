
/*
	Dipakai Di Form Pengiriman
*/

 
Ext.define('Koltiva.view.Traceability.Reception.GridProduct' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Reception.GridProduct', 
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: false, 
    collapsible:false, 
    margin:'0 0 0 0',
    initComponent: function() {
        var thisObj = this;
		
        var MainGridProduct = Ext.create('Koltiva.store.Traceability.Reception.MainGridProduct');
		thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability.Reception.GridProduct-Grid',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            minHeight:300,
                            selType: 'rowmodel',
                            store: MainGridProduct,
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
                                text: lang('Product Name'),
                                dataIndex: 'ProductName',
                                flex:1,
                            },{
                                text: lang('Oil Type'),
                                dataIndex: 'OilType',
                                flex:1,
                            },{
                                text: lang('Product Percentage'),
                                dataIndex: 'ProductPercentage',
                                flex:1,
                            },{
                                text: lang('Product Netto'),
                                dataIndex: 'ProductNetto',
                                flex:1,
                            }] 
				}];
				this.callParent(arguments);
    }
});

 