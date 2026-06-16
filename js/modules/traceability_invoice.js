Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SupplychainID','name','berat','total','cetak_sudah','cetak_belum','bayar_sudah','bayar_belum',
            'pemberi','penerima','pemberi_id','penerima_id',   'bank_id'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            reader: {
                type: 'json',
                root: 'data',
            }
        }
    });
    function displayFormWindow(){
        if(!win.isVisible()){
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }
    var store_detail = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        storeId:'store_detail',
        fields: [{name:'SupplyBatchID'},{name:'DestPO'},{name:'SupplyTransID'},{name:'DateTransaction'},{name:'jumlah_karung',type:'int'},
            {name:'netto',type:'float'},{name:'total',type:'float'},{name:'belum',type:'float'},{name:'sudah',type:'float'},
            {name:'harga',type:'float'},{name:'cetak'}],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_crud+'s_detail',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var store_invoice = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        storeId:'store_invoice',
        fields: [{name:'InvoiceID'},{name:'InvoiceNumber'},{name:'InvoicejumlahKarung',type:'float'},{name:'InvoiceBerat',type:'float'},
            {name:'InvoiceTotal',type:'float'},{name:'InvoiceDate'}],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_crud+'s_invoice',
            reader: {
                type: 'json',
                root: 'data',
            }
        }
    });
    var mc_bank = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label', 'no', 'an'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'_bank',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1204,
        bodyPadding: 5,
        id:'dataForm',
         fieldDefaults: {
            labelWidth: 70,
            anchor: '100%',
         },
        items: [{
		   layout: 'column',
         items: [{
			   columnWidth: 0.3,
			   width:'100%',
            margin: 5,
			   items:[{
      			xtype: 'fieldset',
      			title:'Pembayaran',
      			padding:5,
      			flex: 1,
      			items: [{
   						xtype: 'textfield',
      				   width:'100%',
   						fieldLabel: lang('No Invoice'),
   						id: 'nomor',
   						name: 'nomor',
   						readOnly:true,
                    listeners: {
                        change: function(cb, nv, ov) {
                            if (Ext.getCmp('nomor').getValue()!='') {
                              Ext.getCmp('cetak').show();
                              Ext.getCmp('bayar').show();
                              //Ext.getCmp('cetak_rincian').show();
                            } else {
                              Ext.getCmp('bayar').hide();
                              Ext.getCmp('cetak').hide();
                              //Ext.getCmp('cetak_rincian').hide();
                            }
                        }
                    }
   			      },{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'id',
   						name: 'id'
   					},{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'berats',
   						name: 'berats'
   					},{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'hargas',
   						name:'hargas',
   					},{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'karungs',
   						name: 'karungs'
   					},{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'berat',
   						name: 'berat'
   					},{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'tota',
   						name: 'tota'
   					},{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'pemberi_id',
   						name: 'pemberi_id'
   					},{
   						xtype: 'textfield',
   						fieldLabel: lang('Pemberi'),
   						id: 'pemberi',
   						name: 'pemberi',
      				   width:'100%',
   						readOnly:true
   					},{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'penerima_id',
   						name: 'penerima_id'
   					},{
   						xtype: 'textfield',
      				   width:'100%',
   						fieldLabel: lang('Penerima'),
   						id: 'penerima',
   						name: 'penerima',
   						readOnly:true
   					},{
   						xtype: 'textfield',
   						fieldLabel: lang('Karung'),
   						id: 'karung',
   						name: 'karung',
      				   width:'100%',
   						hidden:true
   					},{
   						xtype: 'textfield',
   						fieldLabel: lang('Berat'),
   						id: 'berat',
   						name: 'berat',
      				   width:'100%',
   						readOnly:true
   					},{
   						xtype: 'textfield',
   						fieldLabel: lang('Total'),
   						id: 'total',
   						name: 'total',
      				   width:'100%',
   						readOnly:true,
                    listeners: {
                        change: function(cb, nv, ov) {
                            if (Ext.getCmp('total').getValue()>0) Ext.getCmp('save').show();
                            else Ext.getCmp('save').hide();
                        }
                    }
   					},{
         			xtype: 'fieldset',
         			title:'Rekening Bank',
         			padding:5,
         			flex: 1,
         			items: [{
   						 fieldLabel: lang('Nama'),
                      id: 'bank_nama',
                      name: 'bank_nama',
                      xtype: 'combo',
                      store:mc_bank,
                      displayField: 'label',
                      valueField: 'id',
                      queryMode: 'local',
                      listeners: {
                       change: function (cb, nv, ov) {
                         var b = this.value;
                         var bb = b.split('-');
                    		 Ext.getCmp('bank_rekening').setValue(bb[2])
                    		 Ext.getCmp('bank_an').setValue(bb[3])
                    		 Ext.getCmp('bank_id').setValue(bb[0])
                    	  }
                    	 }
   					},{
   						xtype: 'hidden',
   						id: 'bank_id',
   						name: 'bank_id'
   					},{
   						xtype: 'textfield',
   						fieldLabel: lang('Nomor'),
   						id: 'bank_rekening',
   						name: 'bank_rekening',
      				   width:'100%',
   						readOnly:true
   					},{
   						xtype: 'textfield',
   						fieldLabel: lang('Atas Nama'),
   						id: 'bank_an',
   						name: 'bank_an',
      				   width:'100%',
   						readOnly:true
   					}]
   			     }]
      			},{
					xtype: 'fieldset',
					title: 'Invoice',
					padding:0,
					flex: 1,
					items: [{
            xtype: 'gridpanel',
            autoScroll:true,
            height:260,
            id:'grid_invoice',
            padding:0,
            features: [{
                 ftype: 'summary'
            }],
            store: store_invoice,
            width: '97%',
            loadMask: true,
            selType: 'rowmodel',
            columns: [{
              xtype: 'gridcolumn',
              text: lang('ID'),
              dataIndex:'InvoiceNumber',
              width:'40%'
            },{
              text: lang('Tanggal'),
              dataIndex: 'InvoiceDate',
              width:'28%'
            },{
              text: lang('Total'),
              dataIndex: 'InvoiceTotal',
              width:'30%',
              summaryType: 'sum',
              align: 'right',
              renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                  return Ext.util.Format.number(value, '0,000.00');
              }
            }],
            listeners: {
                 itemdblclick: function(dv, record, item, index, e) {
                     var sm = record;
                     //console.log(sm)
                     Ext.getCmp('nomor').setValue(sm.get('InvoiceNumber'))
                     Ext.getCmp('berat').setValue(sm.get('InvoiceBerat'))
                     Ext.getCmp('total').setValue(Ext.util.Format.number(sm.get('InvoiceTotal'), '0,000.00'))
                 }
             },
               }]
            }]
			},{
				columnWidth: 0.7,
            margin: 5,
				items:[{
					xtype: 'fieldset',
					padding:0,
					title: 'Batch',
					flex: 1,
					items: [{

            xtype: 'gridpanel',
            autoScroll:true,
            height:600,
            id:'grid_detail',
            features: [{
                 ftype: 'summary'
            }],
            store: store_detail,
            width: '97%',
            loadMask: true,
            selType: 'rowmodel',
            columns: [{
              xtype: 'gridcolumn',
              text: lang('ID'),
              dataIndex:'SupplyBatchID',
              width:'7%'
            },{
              text: lang('PO'),
              dataIndex: 'DestPO',
              width:'10%'
            },{
              text: lang('Tanggal'),
              dataIndex: 'DateTransaction',
              width:'12%'
            },{
              text: lang('Karung'),
              dataIndex: 'jumlah_karung',
              summaryType: 'sum',
              width:'10%',
              align: 'right'
            },{
              text: lang('Berat'),
              dataIndex: 'netto',
              width:'10%',
              align: 'right',
              summaryType: 'sum',
              renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                  return Ext.util.Format.number(value, '0,000.00');
              }
            },{
              text: lang('Total'),
              dataIndex: 'total',
              width:'15%',
              summaryType: 'sum',
              align: 'right',
              renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                  return Ext.util.Format.number(value, '0,000.00');
              }
            },{
              text: lang('Terhutang'),
              dataIndex: 'belum',
              width:'15%',
              align: 'right',
              summaryType: 'sum',
              renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                  return Ext.util.Format.number(value, '0,000.00');
              }
            },{
              text: lang('Terbayar'),
              dataIndex: 'sudah',
              width:'15%',
              align: 'right',
              summaryType: 'sum',
              renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                  return Ext.util.Format.number(value, '0,000.00');
              }
            }],
            listeners: {
               select: function(dv, record, item, index, e) {
                  var s = this.getSelectionModel().getSelection();
                  setInvoice(s);
               },
               deselect: function(dv, record, item, index, e) {
                  var s = this.getSelectionModel().getSelection();
                  setInvoice(s);
               }
           	},
               selModel: Ext.create('Ext.selection.CheckboxModel', {
                      checkOnly : true, // for prevent clicked grid row and canceled all check box checked status
                  })

               }]
            }]
         }]
		}],
        buttons: [{
            id: 'cetak',
            text: lang('Cetak'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            scope: this,
            hidden:true,
            handler : function(){
                preview_cetak_surat(m_crud+'_cetak/'+Ext.getCmp('nomor').getValue());
            }
		},{
            id: 'bayar',
            text: lang('Bayar'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            hidden:true,
            handler : function(){
                var form = this.up('form').getForm();
                var urle;
                urle = m_crud+'_bayar';
                form.submit({
                    url: urle,
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        store_detail.load({params: {id: o.result.id,
                           awal: Ext.getCmp('awal').getSubmitValue(),akhir: Ext.getCmp('akhir').getSubmitValue()}});
                        store_invoice.load({params: {id: o.result.id,
                           awal: Ext.getCmp('awal').getSubmitValue(),akhir: Ext.getCmp('akhir').getSubmitValue()}});
                        Ext.getCmp('berat').setValue();
                        Ext.getCmp('total').setValue();
                       Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
            }
		},{
            id: 'cetak_rincian',
            text: lang('Cetak Rincian'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            scope: this,
            hidden:true,
            handler : function(){
                preview_cetak_surat(m_crud+'_rincian_cetak/'+Ext.getCmp('nomor').getValue());
            }
		},{
            id:'save',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            hidden:true,
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle;
                if (Ext.getCmp('nomor').getValue()!='') urle = m_crud+'u'; else urle = m_crud;
                form.submit({
                    url: urle,
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        store_detail.load({params: {id: o.result.id,
                           awal: Ext.getCmp('awal').getSubmitValue(),akhir: Ext.getCmp('akhir').getSubmitValue()}});
                        store_invoice.load({params: {id: o.result.id,
                           awal: Ext.getCmp('awal').getSubmitValue(),akhir: Ext.getCmp('akhir').getSubmitValue()}});
                        Ext.getCmp('berat').setValue();
                        Ext.getCmp('total').setValue();
                       Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
            }
        },{
            text: lang('Reset'),
            margin: '5px',
            scale: 'large',
			   ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
               Ext.getCmp('netto').setValue();
               Ext.getCmp('total').setValue();
               Ext.getCmp('nomor').setValue();
               Ext.getCmp('grid_detail').getSelectionModel().deselectAll()
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
			   ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
               store.load({params: {key: Ext.getCmp('key').getValue(),
                  awal: Ext.getCmp('awal').getSubmitValue(),akhir: Ext.getCmp('akhir').getSubmitValue()}});
                win.hide();
            }
        }]
    });
    function setInvoice(s) {
      var berati,totali,i;
      var id = new Array();
      var berat = new Array();
      var harga = new Array();
      var karung = new Array();
      berati = totali = i = 0;
      Ext.each(s, function (item) {
         if (item.data.belum>0 && item.data.cetak=='0') {
            //karung += item.data.karung;
            berati += item.data.netto;
            totali += item.data.total;
            id[i] = item.data.SupplyBatchID;
            berat[i] = item.data.netto;
            harga[i] = item.data.harga;
            karung[i] = item.data.jumlah_karung;
            i++;
         }
      });
      //Ext.getCmp('karung').setValue(Math.round(karung * 100) / 100)
      Ext.getCmp('berat').setValue(Math.round(berati * 100) / 100)
      Ext.getCmp('total').setValue(Math.round(totali * 100) / 100)
      Ext.getCmp('id').setValue(id)
      Ext.getCmp('berats').setValue(berat)
      Ext.getCmp('hargas').setValue(harga)
      Ext.getCmp('karungs').setValue(karung)
    }
    var win = Ext.create('widget.window', {
        title: lang('Data Detail'),
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
    function submitOnEnter(field, event) {
    	if (event.getKey() == event.ENTER) {
    	    search()
    	}
    }    
    function search() {
         var err='';
        if (Ext.getCmp('awal').getValue()==null) err = 'Silahkan isi awal periode';
        else if (Ext.getCmp('akhir').getValue()==null) err = 'Silahkan isi akhir periode';
        if (err!='') {
         Ext.MessageBox.alert('Warning', err);
         return
        }
        store.load({
        params: {
            key: Ext.getCmp('key').getValue(),
            awal: Ext.getCmp('awal').getSubmitValue(),
            akhir: Ext.getCmp('akhir').getSubmitValue()
        }});
    }
    function setUpdate(r) {
         Ext.getCmp('pemberi').setValue(r.get('pemberi'));
         Ext.getCmp('penerima').setValue(r.get('penerima'));
         Ext.getCmp('penerima_id').setValue(r.get('penerima_id'));
         Ext.getCmp('pemberi_id').setValue(r.get('pemberi_id'));
         mc_bank.load({
            params: {
               id: r.get('pemberi_id')
            },
            callback: function () {
               Ext.getCmp('bank_nama').setValue(r.get('bank_id'));
            }
         });
    }
    var grid = Ext.create('Ext.grid.Panel', {
       store: store,
       width: '100%',
       id:'grid',
       style: 'border:1px solid #CCC;',
       renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       listeners : {
          itemclick: function(dv, record, item, index, e) {
            displayFormWindow();
            var sm = record;
            setUpdate(sm)
            store_detail.load({params: {id: sm.get('SupplychainID'),
               akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()}});
            store_invoice.load({params: {id: sm.get('SupplychainID'),
               akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()}});
          }
       },       
       dockedItems: [{
            xtype: 'toolbar',
            items: [{
               icon: varjs.config.base_url+'images/icons/silk/pencil.png', 
               text: lang('Bayar'),
               scope: this,
               handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  setUpdate(sm)
                  store_detail.load({params: {id: sm.get('SupplychainID'),
                     akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()}});                     
                  store_invoice.load({params: {id: sm.get('SupplychainID'),
                     akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()}});
               },
               cls : m_act_update
            },{
               xtype: 'textfield',
               emptyText: 'Cari dengan Nama/ID',
               name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
               id: 'key',
               listeners: {
              		specialkey: submitOnEnter
              	}
           },{
                xtype: 'datefield',
                format: 'Y-m-d',
                fieldLabel: '',
                id: 'awal',
                name: 'awal',
                emptyText: '-- Awal --',
                padding:5,
               listeners: {
              		specialkey: submitOnEnter
              	}
            },{
                xtype: 'datefield',
                format: 'Y-m-d',
                fieldLabel: '',
                id: 'akhir',
                name: 'akhir',
                emptyText: '-- Akhir --',
                padding:5,
               listeners: {
              		specialkey: submitOnEnter
              	}
            },{
              xtype :'button',
              margin: '0px 0px 0px 6px',
              text: lang('Search'),
              handler: function() {
                  search()
              }
           }]
    }],     
    columns: [{
        text: lang('ID'),
        dataIndex: 'SupplychainID',
        hidden:true
    },{
        text: lang('Name'), 
        width: '27%',
        dataIndex: 'name'
    },{
        text: lang('Berat'), 
        width: '10%',
        dataIndex: 'berat',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    },{
        text: lang('Total'), 
        width: '15%',
        dataIndex: 'total',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    },{
        text: lang('Invoice Tercetak'), 
        width: '10%',
        dataIndex: 'cetak_sudah',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    },{
        text: lang('Belum Dicetak'), 
        width: '10%',
        dataIndex: 'cetak_belum',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
         }
   },{
        text: lang('Terbayarkan'), 
        width: '10%',
        dataIndex: 'bayar_sudah',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    },{
        text: lang('Terhutang'), 
        width: '10%',
        dataIndex: 'bayar_belum',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    }]
   });
});
