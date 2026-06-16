<style>
    .summary, .groupuser {
        background: #99bbe8 none repeat scroll 0 0;
        color: #4f6b72;
        font: 11px arial,helvetica,tahoma,sans-serif;
        width: 100%;
        border: 1px solid #99BBE8;
    }
    .summary #title, .groupuser #title .summary #title {
        background: #dfe8f6 none repeat scroll 0 0;
        color: #416aa3;
        font-weight: normal;
        padding: 3px 3px 3px 10px;
        width: 40%;
    }
    .summary #title1, .groupuser #title1 .summary #title1 {
        background: #dfe8f6 none repeat scroll 0 0;
        color: #416aa3;
        font-weight: normal;
        padding: 3px 3px 3px 10px;
        border: 1px solid #99BBE8;
    }
    .summary #cont, .groupuser #cont {
        background-color: #fff;
        color: #000000;
        padding: 3px 3px 3px 10px;
        border: 1px solid #99BBE8;
    }

</style>
<div style="padding:10px">
    <table width="100%" cellspacing="1" cellpadding="1" class="summary" >
  <tr>
    <td colspan="2" id="title1"><div align="center">General Ledger</div></td>
  </tr>
  <tr>
    <td width="13%" id="title1">Print Out Date</td>
    <td id="cont"><?php echo $print_out; ?></td>
  </tr>
  <tr>
    <td id="title1">Periode</td>
    <td id="cont"><?php echo $period; ?></td>
  </tr>
  <tr>
      <td id="title1">Notes</td>
      <td id="cont"><p><b style="color:red">*</b> This Journal is unposted</p><p><b style="color:red">**</b> This Journal is mark as deleted</p></td>
  </tr>
</table>
<p>&nbsp;</p>

<?php foreach($gl_balance as $row): 
     
     /*
      * Kenapa ada ini?
      * Karena... buat bersih2 journal yang ngga ada journal_id
      */
     $jid = array();
     foreach($row['data'] as $item){
         if(strlen($item['journalID']) > 0){
            array_push($jid, $item['journalID']);
         }
     }
     
     if(count($jid) == 0){
         $row['data'] = array();
     }
?>

<?php
    $ob = 0;        
    if($forward){
        foreach ($forward as $item_forward){
            //var_dump($item_forward);
            if($row['COACODE'] == $item_forward['coaCode']){

                if(count($balance_amount) > 0){
                    foreach ($balance_amount as $item_forward_balance){
                        //var_dump($item_forward_balance);
                        if($row['COACODE'] == $item_forward_balance['coaCode']){
                            if($row['coaType'] == '1'){
                                $ob = ($item_forward['DEBIT_FORWARD']) - ($item_forward['CREDIT_FORWARD']) + $item_forward_balance['coaBalanceAmount'];
                            }elseif($row['coaType'] == '2'){
                                $ob = ($item_forward['CREDIT_FORWARD']) - ($item_forward['DEBIT_FORWARD']) + $item_forward_balance['coaBalanceAmount'];
                            }
                        }
                    }
                }else{

                    if($row['coaType'] == '1'){
                        $ob = $ob = ($item_forward['DEBIT_FORWARD'] - $item_forward['CREDIT_FORWARD']);
                    }elseif($row['coaType'] == '2'){
                        $ob = ($item_forward['CREDIT_FORWARD'] - $item_forward['DEBIT_FORWARD']);
                    }
                }
            }
        }
        //$ob = $item_forward_balance['COA_BALANCE_AMOUNT'];
    }else{
        if($balance_amount){
            foreach ($balance_amount as $item_forward_balance){
                if($row['COACODE'] == $item_forward_balance['coaCode']){
                    $ob = $item_forward_balance['coaBalanceAmount'];
                }
            }
        }
    }
?>
<?php if( count($row['data']) > 0) { // munculkan semua data yang punya begining balance ?>
<table width="100%" cellspacing="1" cellpadding="1" class="summary" style="margin-bottom:10px">
  <tr>
    <td id="title1" width="4%">NO. <?php echo count($row['data']); ?></td>
    <td id="title1" width="11%">ID</td>
    <td id="title1" width="12%">SRC</td>
    <td id="title1" width="10%">DATE</td>
    <td id="title1" width="28%">MEMO</td>
    <td id="title1" width="8%">DEBIT</td>
    <td id="title1" width="8%">KREDIT</td>
    <td id="title1" width="11%">Ending Balance</td>
  </tr>
  <tr id="cont" >
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr id="cont" >
    <td>&nbsp;</td>
    <td id="cont"><?php echo $row['COACODE']; ?></td>
    <td id="cont"><?php echo $row['COATITLE']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr id="cont" style="background:#F4F4F4; font-weight: bold;">
    <td>&nbsp;</td>
    <td nowrap="nowrap" id="cont" style="background:#F4F4F4;">Begining Balance</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td nowrap="nowrap" id="cont" style="background:#F4F4F4;text-align:right;"><?php echo ($ob >= 0)?number_format($ob,2,'.',','):''?></td>
    <td nowrap="nowrap" id="cont" style="background:#F4F4F4;text-align:right;"><?php echo ($ob <= 0)?number_format($ob,2,'.',','):''?></td>
    <td>&nbsp;</td>
  </tr>
  
<?php $i=1;$debit=0; $kredit=0;
 
  foreach($row['data'] as $item) {
    $const     = ($row['coaType']==1) ? 1 : -1 ;
    	if($item['journalDetailType'] == 1){
    		$debit += $item['journalDetailSum'];
    		$ob = $ob + ($const * $item['journalDetailSum']);
    	}
    	if($item['journalDetailType'] == 2){
    		$kredit += $item['journalDetailSum'];
    		$ob = $ob - ($const * $item['journalDetailSum']);
    	}
?>
	<?php //batas muncul semua coa ato tidak... if(!empty($item['JOURNAL_TYPE_CODE'])) { ?>
	<?php if(!empty($item['journalID'])) { ?>
  <script>
      function editJurnalLink(val){
	    var rowedit = Ext.create('Ext.grid.plugin.CellEditing', {
		listeners: {
		    edit:function(editor,e){

		    }
		}
	    });
	    var win = new Ext.Window({
		title:'Journal - Edit',
		align:'center',
		constrain:true,
		modal:true,
		width:'100%',
		height:500,
		y:100,
		id:'win-edit-journal',
		buttonAlign:'center',
		bodyStyle:'background:#ffffff',
		items:[
		    {
			xtype:'form',
			items:[
			    {
				xtype:'hidden',
				name:'journalID'
			    },
			    {
				xtype:'container',
				layout:{
				    type:'hbox'
				},
				defaults:{
				    labelAlign:'top',
				    margin:10,
				    labelSeparator:'',
				    labelStyle:'font-weight:bold'
				},
				items:[
				    {
					xtype:'datefield',
					name:'journalDate',
					fieldLabel: 'Date <b style="color:red">*</b>',
					value:new Date()
				    },
				    {
					xtype: 'combo',
					disabledCls: 'disabled',
					fieldLabel: 'Type <b style="color:red">*</b>',
					allowBlank: false,
					store: Ext.create('Ext.data.Store', {
					    fields: ['JOURNAL_TYPE_CODE', 'JOURNAL_TYPE_DESC'],
					    autoLoad: true,
					    proxy: {
						type: 'rest',
						url: window.location + 'api/common/get-combo', // url that will load data with respect to start and limit params
						extraParams: {
						    table: 'r_journal_type',
						    name: 'JOURNAL_TYPE_DESC',
						    id: 'JOURNAL_TYPE_CODE'
						},
						reader: {
						    type: 'json',
						    root: 'data',
						    totalProperty: 'total'
						}
					    }
					}),
					displayField: 'JOURNAL_TYPE_DESC',
					valueField: 'JOURNAL_TYPE_CODE',
					name: 'JOURNAL_TYPE_CODE'

				    },
				    {
					xtype:'textfield',
					fieldLabel:'Memo',
					flex:1,
					name:'journalMemo'
				    }
				]
			    },
			    {
				xtype:'grid',
				id:'grid-journal-detail',
				flex:1,
				features: [{
				    ftype: 'summary',
				    dock: 'bottom'
				}],
				tbar:[
					{
					xtype:'button',
					iconCls:'add',
					text:'Add Row',
					handler:function(c){
					    var grid = c.up('.grid');
					    var last = grid.store.getCount();
					    // empty record
					    grid.store.insert(last, Ext.create('Core.Module.Journal.model.Detail',{
						COA_CODE:'',
						DEBET:'',
						KREDIT:'',
						JOURNAL_DETAIL_TYPE:'',
						JOURNAL_DETAIL_DESC:'',
						CURRENCY_NAME:'',
						JOURNAL_DETAIL_ORIG:'',
						JOURNAL_DETAIL_SUM:'',
						JOURNAL_DETAIL_EX_RATE:1
					    }));

					    rowedit.startEdit(last, 0);
					}
				    },
				    {
					xtype:'button',
					iconCls:'add',
					text:'Remove Row',
					handler:function(c){
					    var grid = Ext.getCmp('grid-journal-detail');
					    var sm = grid.getSelectionModel();
					    var sel = sm.getSelection();
					    grid.store.remove(sel);
					}
				    }
				],
				store:Ext.create('Ext.data.Store', {
				    model: 'Core.Module.Journal.model.Detail',
				    autoLoad: true,
				    remoteSort: true,
				    proxy: {
					type: 'rest',
					url: window.location + 'api/journal/get-detail/'+val, // url that will load data with respect to start and limit params
					reader: {
					    type: 'json',
					    root: 'data',
					    totalProperty: 'total',
					    idProperty: 'JOURNAL_DETAIL_ID'
					},
					writer: {
					    type: 'json'
					},
					api: {
					    destroy: window.location + 'api/journal/delete'
					},
					appendId: true
				    }
				}),
				plugins:[rowedit],
				selType: 'cellmodel',
				columns:[{
				    header: 'COA',
				    dataIndex: 'COA_CODE',
				    width:150,
				    renderer:function(v){
					return '<span style="font-family:courier new">' + v + '</span>';
				    },
				    field: {
					xtype: 'combo',
					disabledCls: 'disabled',
					id:'cmb-coa-journal-detail',
					listeners:{
					    select:function(c,r){
						var grid = Ext.getCmp('grid-journal-detail');
						var sm = grid.getSelectionModel();
						var sel = sm.getSelection();

						var value = r[0].data.COA_TITLE;
						sel[0].set('COA_TITLE',value);
					    }
					},
					store: Ext.create('Ext.data.Store', {
					    fields: ['COA_CODE', 'COA_TITLE'],
					    autoLoad: true,
					    proxy: {
						type: 'rest',
						url: window.location + 'api/journal/get-combo', // url that will load data with respect to start and limit params
						extraParams: {
						    table: 'r_coa',
						    name: 'COA_TITLE',
						    id: 'COA_CODE'
						},
						reader: {
						    type: 'json',
						    root: 'data',
						    totalProperty: 'total'
						}
					    }
					}),
					listConfig: {
					    loadingText: 'Searching...',
					    minWidth:350,
					    getInnerTpl: function() {
						return '{COA_CODE} - {COA_TITLE}';
					    }
					},
					minChars:1,
					displayField: 'COA_CODE',
					valueField: 'COA_CODE',
					name: 'COA_CODE'

				    }
				},{
				    header: 'Coa Title',
				    dataIndex: 'COA_TITLE',
				    width:300,
				    renderer:function(v){
					return '<span style="font-family:courier new">' + v + '</span>';
				    }
				},{
				    header: 'Curr',
				    dataIndex: 'CURRENCY_NAME',
				    width:50,
				    renderer:function(v,r){
					return '<span style="font-family:courier new">' + v + '</span>';
				    },
				    editor: {
					xtype: 'combo',
					disabledCls: 'disabled',
					id:'cmb-currency-journal-detail',
					listeners:{
					    select:function(c,r){
						var grid = Ext.getCmp('grid-journal-detail');
						var sm = grid.getSelectionModel();
						var sel = sm.getSelection();

						var value = r[0].data.CURRENCY_ID;

						sel[0].set('CURRENCY_ID',value);
					    }
					},
					store: Ext.create('Ext.data.Store', {
					    fields: ['CURRENCY_ID', 'CURRENCY_NAME'],
					    autoLoad: true,
					    proxy: {
						type: 'rest',
						url: window.location + 'api/common/get-combo', // url that will load data with respect to start and limit params
						extraParams: {
						    table: 'r_currency',
						    name: 'CURRENCY_ID',
						    id: 'CURRENCY_NAME'
						},
						reader: {
						    type: 'json',
						    root: 'data',
						    totalProperty: 'total'
						}
					    }
					}),
					displayField: 'CURRENCY_NAME',
					valueField: 'CURRENCY_NAME',
					name: 'CURRENCY_NAME'
				    }
				},{
				    header: 'Original Amount',
				    dataIndex: 'JOURNAL_DETAIL_ORIG',
				    width:170,
				    align:'right',
				    renderer:function(v){
					return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
				    },
				    editor: {
					xtype: 'numericfield'
				    }
				},{
				    header: 'Exchange Rate',
				    dataIndex: 'JOURNAL_DETAIL_EX_RATE',
				    width:70,
				    align:'right',
				    renderer:function(v){
					return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
				    },
				    editor: {
					xtype: 'numericfield'
				    }
				},{
				    header: 'D/K',
				    dataIndex: 'JOURNAL_DETAIL_TYPE',
				    width:80,
				    renderer:function(v,r){
					return '<span style="font-family:courier new">' + v + '</span>';
				    },
				    editor: {
					xtype: 'combo',
					disabledCls: 'disabled',
					id:'cmb-side-journal-detail',
					listeners:{
					    select:function(c,r){
						var grid = Ext.getCmp('grid-journal-detail');
						var sm = grid.getSelectionModel();
						var sel = sm.getSelection();

						var value = r[0].data.SIDE;

						sel[0].set('SIDE',value);

						if(value === 'DEBET'){
						    sel[0].set('DEBET',(sel[0].get('JOURNAL_DETAIL_EX_RATE') * sel[0].get('JOURNAL_DETAIL_ORIG')));
						    sel[0].set('KREDIT',0);
						}

						if(value === 'KREDIT'){
						    sel[0].set('KREDIT',(sel[0].get('JOURNAL_DETAIL_EX_RATE') * sel[0].get('JOURNAL_DETAIL_ORIG')));
						    sel[0].set('DEBET',0);
						}
					    }
					},
					store: Ext.create('Ext.data.Store', {
					    fields: ['SIDE'],
					    autoLoad: true,
					    data: [
						{SIDE: 'DEBET'},
						{SIDE: 'KREDIT'}
					    ]
					}),
					displayField: 'SIDE',
					valueField: 'SIDE',
					name: 'SIDE'
				    }
				},{
				    header: 'Debet',
				    align:'right',
				    dataIndex: 'DEBET',
				    width:170,
				    summaryType: 'sum',
				    summaryRenderer: function(value, summaryData, dataIndex) {
					return '<span style="font-family:courier new; font-weight:bold">' + Ext.util.Format.number(value,'0,000.00') + '</span>';
				    },
				    renderer:function(v){
					return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
				    }
				},{
				    header: 'Kredit',
				    align:'right',
				    dataIndex: 'KREDIT',
				    width:170,
				    summaryType: 'sum',
				    summaryRenderer: function(value, summaryData, dataIndex) {
					return '<span style="font-family:courier new; font-weight:bold">' + Ext.util.Format.number(value,'0,000.00') + '</span>';
				    },
				    renderer:function(v){
					return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
				    }
				},{
				    header: 'Description',
				    dataIndex: 'JOURNAL_DETAIL_DESC',
				    width:180,
				    renderer:function(v){
					return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
				    },
				    editor: {
					type: 'textfield'
				    }
				}]
			    }
			]
		    }
		],
		bbar: [
		    '->',

		    {
			xtype:'button',
			text:'Save',
			id:'btn-save-frm-Journal',
			iconCls:'save',
			handler:function(c){
			    var frm = form.getForm();
			    var id = frm.getValues();
			    if(id.JOURNAL_ID === ''){
				var url = window.location + 'api/journal/add';
				var method = 'POST';

			    } else {
				var url = window.location + 'api/journal/edit/'+id.JOURNAL_ID;
				var method = 'PUT';

			    }
			    var data_grid = Ext.getCmp('grid-journal-detail').store;
			    var detail = [];

			    data_grid.each(function(value, index, rec){
				detail.push(value.data);
			    });

			    frm.submit({
				url: url,
				method: method,
				params:{data:Ext.JSON.encode(detail)},
				success: function(f, resp) {
				    Ext.Msg.alert('Success', 'Journal successfully saved');
				    win.close();
				    var params = Ext.getCmp('frm-ledger').getForm().getValues();
				    var loader = Ext.getCmp('panel-ledger').getLoader();
				    loader.load({
					params:params
				    });
				},
				failure: function(f, resp) {
				    Ext.Msg.alert('Success', 'Cannot save journal');
				}
			    });
			}
		    },
		    {
			xtype:'button',
			text:'Cancel',
			iconCls:'cancel',
			id:'btn-cancel-frm-Journal',
			handler:function(){
			    form.getForm().reset();
			    win.close();
			}
		    }
		]
	    });
	    win.show();
	    var form = win.down('form');
	    
	    form.getForm().load({
		url:window.location + 'api/journal/get/' + val,
		method:'GET',
		success:function(c,r){

		}
	    });
	}
  </script>
  <tr id="cont">
    <td valign="top" id="cont"><div align="center"><?php if($item['journalIsPosted'] == 0){ echo '<span style="color:red">*</span>'; } elseif($item['journalIsPosted'] == 2) { echo '<span style="color:red">**</span>'; }?><span title="Click to Edit"><a href="javascript:void(0)" onclick="editJurnalLink('<?php echo $item['journalID']; ?>');"><?php echo $i++; ?></a></span></div></td>
    <td valign="top" id="cont"><?php //echo $item['JOURNAL_NUMBER']; ?></td>
    <td valign="top" id="cont"><?php echo $item['journalTypeCode']; ?></td>
    <td valign="top" id="cont"><div align="left"><?php echo $item['journalDate']; ?></div></td>
    <td valign="top" id="cont"><?php if($item['journalDetailDesc']) echo $item['journalDetailDesc']; else echo $item['journalMemo']; ?></td>
    <td valign="top" id="cont"><div align="right"><?php echo ($item['DEBIT']!=0)?number_format($item['DEBIT'],2,'.',','):'';?></div></td>
    <td valign="top" id="cont"><div align="right"><?php echo ($item['CREDIT']!=0)?number_format($item['CREDIT'],2,'.',','):'';?></div></td>
    <td valign="top" id="cont"><div align="right"><?php echo number_format($ob,2,'.',','); ?> </div></td>
  </tr>
	<?php } ?>
  <?php  } ?>
  <tr id="cont">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr id="cont" >
    <td id="title1">&nbsp;</td>
    <td id="title1">&nbsp;</td>
    <td id="title1">&nbsp;</td>
    <td id="title1">&nbsp;</td>
    <td id="title1">Total</td>
    <td id="title1"><div align="right"><?php echo number_format($debit,2,'.',','); ?></div></td>
    <td id="title1"><div align="right"><?php echo number_format($kredit,2,'.',','); ?></div></td>
    <td id="title1"><div align="right"><?php echo number_format($ob,2,'.',','); ?></div></td>
  </tr>
  <tr id="cont" >
    <td id="title1">&nbsp;</td>
    <td id="title1">&nbsp;</td>
    <td id="title1">&nbsp;</td>
    <td id="title1">&nbsp;</td>
    <td id="title1">Net Activity</td>
    <td id="title1">&nbsp;</td>
    <td id="title1"><div align="right"><?php echo number_format($debit-$kredit,2,'.',','); ?></div></td>
    <td id="title1">&nbsp;</td>
  </tr>
  <?php } ?>
</table>
<?php endforeach ?>
</div>

