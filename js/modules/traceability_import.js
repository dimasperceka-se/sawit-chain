Ext.onReady(function(){

   var mc_bu_warehouse = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['SupplychainID','Name'],
      autoLoad: true,
      proxy: {
         type: 'ajax',
         url: m_bu+'_warehouse',
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
	
	/*var mc_template = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [
            {'id':'TemplateUploadTraceability.xls', "label": "ADM"},
            {'id':'TemplateUploadTraceabilityCargill.xls', "label": "Cargill"},
            {'id':'TemplateUploadTraceabilityBTCacao.xls', "label": "BT Cacao"},
            {'id':'TemplateUploadTraceabilityBTCacaoWH.xls', "label": "BT Cacao dari Farmer"},
            {'id':'TemplateUploadTraceabilityJebeKoko.xls', "label": "JEBE KOKO"},
        ]
    });*/
	
	var mc_template = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      autoLoad: true,
      proxy: {
         type: 'ajax',
         url: m_bu+'_template_supplychain',
         reader: {
            type: 'json',
            root: 'data'
         }
      }
	});

   var mc_bu_bu = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['SupplychainID','Name'],
      autoLoad: true,
      proxy: {
         type: 'ajax',
         url: m_bu+'_bu',
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });

   var DataForm = Ext.create('Ext.form.Panel', {
      frame: false,
      renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       width: '100%',
      items: [{
       dockedItems: [{
            xtype: 'toolbar',
            items: [{
                  xtype: 'form',
                  fileUpload: true,
                  enctype:'multipart/form-data',
                  id:'upload_file',
                  items: [{
                     layout:'column',
                     border: false,
                     width:1200,
                     items: [{
                        columnWidth: .2,
                        padding:3,
                        border: false,
                        layout: {
                           type: 'vbox',
                           align: 'stretch'
                        },
                        items:[{                  
                            emptyText: '-- Warehouse --',
                            id: 'Warehouse',
                            name: 'Warehouse',
                            xtype: 'combo',
                            anchor: '100%',
                            store:mc_bu_warehouse,
                            displayField: 'Name',
                            valueField: 'SupplychainID',
                            queryMode: 'local',
                             listeners: {
                                 change: function(cb, nv, ov) {
                                    Ext.getCmp('BuyingUnit').setValue();
                                     mc_bu_bu.load({
                                            params: {
                                             wh: this.value
                                         }
                                     });
                                 }
                             }
                        }]
                     },{
                        columnWidth: .2,
                        padding:3,
                        border: false,
                        layout: {
                           type: 'vbox',
                           align: 'stretch'
                        },
                        items:[{                  
                            emptyText: '-- Buying Unit --',
                            id: 'BuyingUnit',
                            name: 'BuyingUnit',
                            xtype: 'combo',
                            anchor: '100%',
                            store:mc_bu_bu,
                            displayField: 'Name',
                            valueField: 'SupplychainID',
                            queryMode: 'local'
                        }]
                     },{
                        columnWidth: 0.2,
                        layout: 'form',
                        border: false,
                        defaultType: 'checkboxfield',
                        items: [{
                            boxLabel: lang('District Trader'),
                            name: 'DistrictTrader',
                            inputValue: '1',
                            id: 'DistrictTrader',
                            listeners: {
                                
                            }
                        }]
                    }, {
                        columnWidth: .2,
                        padding:3,
                        border: false,
                        layout: {
                           type: 'vbox',
                           align: 'stretch'
                        },
                        items:[{
                           xtype: 'fileuploadfield',
                            width:'100%',
                           labelWidth: 120,
                           anchor: '50%',
                           id: 'file',
                           name: 'file',
                           buttonText: 'Browse',
                           listeners: {
                             'change': function(fb, v){
                                 var form = Ext.getCmp('upload_file').getForm();
                                 form.submit({
                                     url: m_crud+'_upload',
                                     waitMsg: 'Sending and insert file...',
                                     success: function(fp, o) {console.log(o)
                                          if(o.result.success=='false') Ext.MessageBox.alert('Warning','Data petani ini tidak ditemukan '+
                                             o.result.name+'. Silahkan perbaiki data');
                                          else if(o.result.success=='false_sertifikasi') Ext.MessageBox.alert('Warning','Data petani berikut tidak tersertifikasi '+
                                             o.result.name_sertifikasi+'. Silahkan perbaiki data');
										  else if(o.result.success=='false_nopo') Ext.MessageBox.alert('Warning','No PO sudah terdaftar ('+
                                             o.result.nopo+'). Silahkan perbaiki data');
										  else if(o.result.success=='false_date') Ext.MessageBox.alert('Warning','Tanggal PO tidak sesuai Format atau melebihi dari tanggal sekarang ('+
                                             o.result.tanggal_po+'). Silahkan perbaiki data');
										  else if(o.result.success=='false_transdate') Ext.MessageBox.alert('Warning',lang('Tanggal transaksi tidak sesuai Format atau melebihi dari tanggal sekarang') + ' ('+
                                             o.result.tanggal_transaksi+'). ' + lang('Silahkan perbaiki data'));
                                          else Ext.MessageBox.alert('Success','Date imported succesfully');
                                     }
                                 });
                             }
                           }
                        }]
                     },{
                        columnWidth: .2,
                        padding:3,
                        border: false,
                        layout: {
                           type: 'vbox',
                           align: 'stretch'
                        },
                        items:[{                  
                            emptyText: '-- Download File Template --',
                            id: 'Template',
                            name: 'Template',
                            xtype: 'combo',
                            anchor: '100%',
                            store:mc_template,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
							listeners: {
								'change': function(fb, v){
									var links = Ext.getCmp('Template').getValue();
									window.open(m_url+links);
								}
							}
                        }]
                     }]
                 }]
               }]
         }]
		}/*,{
			xtype: 'gridpanel',
			//store: store,
			width: '100%',
			minHeight: 550,
			//title: 'Survey List',
			style: 'border:1px solid #CCC;',
			loadMask: true,
			selType: 'rowmodel',
			//dockedItems: [],
			columns: [{
            text: lang('No'),
            xtype: 'rownumberer',
            width:'5%'
       },{
            text: lang('Batch Number'),
            dataIndex: 'SupplyBatchNumber',
             width:'10%'
        },{
            text: lang('PO'),
            dataIndex: 'DestPO',
             width:'10%'
        },{
            text: lang('Jenis'),
            dataIndex: 'jenis',
             width:'13%'
        },{
             text: lang('Unit'),
             width: '15%',
             dataIndex: 'Name'
         },{
             text: lang('Tanggal'),
             width: '8%',
             dataIndex: 'SupplyBatchDate'
         },{
             text: lang('Bruto'),
             width: '7%',
             dataIndex: 'VolumeBruto',
              renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                  return Ext.util.Format.number(value, '0,000.00');
              }
         },{
             text: lang('Netto'),
             width: '7%',
             dataIndex: 'VolumeNetto',
              renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                  return Ext.util.Format.number(value, '0,000.00');
              }
         },{
             text: lang('Status'),
             width: '10%',
             dataIndex: 'SupplyDestStatus'
         },{
             text: lang('Destination'),
             width: '14%',
             dataIndex: 'NameDest'
         }],
			viewConfig: {
				stripeRows: false,
				getRowClass: function (record) {
					return record.get('error_status') == 1 ? 'error' : 'no-error';
				}
			},
			buttons: [{
				id: 'asaveButton',
				text: lang('Proses'),
				margin: '5px',
				scale: 'large',
				ui: 's-button',
				cls: 's-blue ',
				buttonAlign: 'left',
				handler: function () {
					var form = Ext.getCmp('upload').getForm();
					form.submit({
						url: m_crud + '_upload_data',
						waitMsg: 'Memindahkan data...',
						success: function (fp, o) {
							Ext.MessageBox.alert('Success', 'Data saved.');
						}
					});
				}
			}]
		}*/]
   });
});
