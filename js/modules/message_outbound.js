Ext.onReady(function(){
	Ext.tip.QuickTipManager.init();
	Ext.define('Scpp.Model', {
		extend: 'Ext.data.Model',
		fields: ['to', 'message', 'message-id', 'status', 'remaining-balance', 'message-price', 'network', 'error-text', 'insert-timestamp'],
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
			{
				text: lang('MessageId'), 
				width: '15%',
				dataIndex: 'message-id'
			}
			,{
				text: lang('To'), 
				width: '15%',
				dataIndex: 'to'
			}
			,{
				text: lang('Message'), 
				width: '25%',
				dataIndex: 'message'
			}
			,{
				text: lang('Status'), 
				width: '5%',
				dataIndex: 'status'
			}
			,{
				text: lang('Error'), 
				width: '25%',
				dataIndex: 'error-text'
			}
			,{
				text: lang('Insert Timestamp'), 
				width: '15%',
				dataIndex: 'insert-timestamp'
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
					{
						xtype :'button',
						icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
						text: lang('Create'),
						handler: function() {
							displayFormWindow();
						}
					}
					,{
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
	function displayFormWindow(){
		if(!win.isVisible()){
			win.show();
		} else {
			win.hide(this, function() {});
			win.toFront();
		}
	}

	var DataForm = Ext.create('Ext.form.Panel', {
		frame: false,
		height: 200,
		autoScroll: true,
		width: 580,
		bodyPadding: 5,
		id: 'dataForm',
		fileUpload: true,
		id:'create',
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 100,
			anchor: '100%'
		},
		items: [
			{
				xtype: 'textfield',
				id: 'number',
				name: 'number',
				fieldLabel: lang('Pone Number')
			}
			,{
				xtype: 'textareafield',
				id: 'text',
				name: 'text',
				fieldLabel: lang('Text')
			}
		],
		buttons: [
			{
				id:'saveButton',
				text: lang('Send'),
				margin: '5px',
				scale: 'large',
				ui: 's-button',
				cls: 's-blue',
				handler: function() {
					var form = Ext.getCmp('create').getForm();
					form.submit({
						url: m_crud,
						waitMsg: lang('Sending message....'),
						success: function(fp, o) {
							store.load();
							win.hide();
						}
					});
				}
			}
			,{
				text: 'Close',
				margin: '5px',
				scale: 'large',
				ui: 's-button',
				cls: 's-grey',
				disabled: false,
				handler: function() {
					win.hide();
				}
			}
		]
	});
	var win = Ext.create('widget.window', {
		title: 'Upload File',
		id:'win',
		closable: true,
		modal:true,
		closeAction: 'hide',
		width: 600,
		height: 250,
		layout: {
			type: 'border',
			padding: 5
		},
		items: [DataForm]
	});
});
