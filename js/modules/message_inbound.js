Ext.onReady(function(){
	Ext.tip.QuickTipManager.init();
	Ext.define('Scpp.Model', {
		extend: 'Ext.data.Model',
		fields: ['id', 'type', 'to', 'msisdn', 'messageId', 'message-timestamp', 'text', 'keyword', 'concat', 'concat-ref', 'concat-total', 'concat-part', 'data', 'udh', 'insert-timestamp'],
	});
	var store = Ext.create('Ext.data.Store', {
		model: 'Scpp.Model',
		autoLoad: true,
		pageSize: 10,
		proxy: {
			type: 'ajax',
			url: m_crud+'s',
			// params: {
			// 	'X-API-KEY': '030584'
			// },
			reader: {
				type: 'json',
				root: 'data',
				totalProperty: 'total'
			}
		}
	});

	var grid = Ext.create('Ext.grid.Panel', {
		store: store,
		width: '100%',
		id:'grid',
		minHeight:250,
		//title: 'CPG Batch List',
		style:'border:1px solid #CCC;',
		renderTo: 'ext-content',
		loadMask: true,
		selType: 'rowmodel',
		columns: [
			// {
			// 	text: lang('to'), 
			// 	width: '25%',
			// 	dataIndex: 'to'
			// }
			{
				text: lang('messageId'), 
				width: '15%',
				dataIndex: 'messageId'
			}
			,{
				text: lang('msisdn'), 
				width: '15%',
				dataIndex: 'msisdn'
			}
			,{
				text: lang('message-timestamp'), 
				width: '15%',
				dataIndex: 'message-timestamp'
			}
			,{
				text: lang('insert-timestamp'), 
				width: '15%',
				dataIndex: 'insert-timestamp'
			}
			,{
				text: lang('keyword'), 
				width: '10%',
				dataIndex: 'keyword'
			}
			,{
				text: lang('text'), 
				width: '30%',
				dataIndex: 'text'
			}
		]
		,listeners: {
			itemdblclick: function(dataview, index, item, e) {
				// if (Ext.getCmp('toolbar_dua').isVisible()) Ext.getCmp('toolbar_dua').setVisible(false);
				// else Ext.getCmp('toolbar_dua').setVisible(true);
			}
		} 
		,dockedItems: [
			{
				xtype: 'pagingtoolbar',
				store: store,   
				dock: 'bottom',
				displayInfo: true
			}
			,{
				xtype: 'toolbar',
				items: [
					// {
					// 	xtype :'button',
					// 	icon: varjs.config.base_url+'images/icons/silk/disk_upload.png',
					// 	text: 'Upload',
					// 	handler: function() {
					// 		displayFormWindow();
					// 	}
					// }
					{
						name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
						id: 'key',
						xtype:'textfield',
						listeners: {
							specialkey: submitOnEnter
						}
					}
					,{
						xtype :'button',
						icon: varjs.config.base_url+'images/icons/silk/search.png',
						margin: '0px 0px 0px -10px',
						text: 'Search',
						handler: function() {
							store.load({
								params: {
									key: Ext.getCmp('key').getValue()
								}});
						}
					}
				]
			}
		],      
	});

	function submitOnEnter(field, event) {
		if (event.getKey() == event.ENTER) {
			store.load({
				params: {
					key: Ext.getCmp('key').getValue()
				}});
		}
	};
});
