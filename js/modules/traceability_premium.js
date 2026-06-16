Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        pageSize: 30,
        fields: ['SupplychainID','GroupName','name','trans','bruto','netto','total','sudah','belum'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            reader: {
                type: 'json',
                root: 'data',
            }
        }
    });
    var mc_jenis = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{"id":"koperasi","label": lang("Koperasi")},
            {"id":"bu","label": lang("Buying Unit")},
            {"id":"petani","label": lang("Petani")}]
    });
    var mc_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'_district',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_cpg = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_crud+'_cpg',
            reader: {
                type: 'json',
                root: 'data'
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
        storeId:'store_detail',
        fields: [{name:'DestPO'},{name:'SupplyTransID'},{name:'DateTransaction'},{name:'bruto',type:'float'},{name:'netto',type:'float'},
            {name:'total',type:'float'},{name:'belum',type:'float'},{name:'sudah',type:'float'}],
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
    var store_kuitansi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        storeId:'store_kuitansi',
        fields: [{name:'PaymentID'},{name:'PaymentNumber'},{name:'PaymentNetto',type:'float'},{name:'PaymentPremium',type:'float'},
            {name:'PaymentDate'}],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_crud+'s_kuitansi',
            reader: {
                type: 'json',
                root: 'data',
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
   						fieldLabel: lang('No Kuitansi'),
   						id: 'nomor',
   						name: 'nomor',
   						readOnly:true,
                    listeners: {
                        change: function(cb, nv, ov) {
                            if (Ext.getCmp('nomor').getValue()!='') {
                              Ext.getCmp('cetak').show();
                              Ext.getCmp('cetak_rincian').show();
                            } else {
                              Ext.getCmp('cetak').hide();
                              Ext.getCmp('cetak_rincian').hide();
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
   						id: 'berat',
   						name: 'berat'
   					},{
   						hidden:true,
   						xtype: 'textfield',
   						id: 'premi',
   						name: 'premi'
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
   						id: 'penerima_type',
   						name: 'penerima_type'
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
   						fieldLabel: lang('Bruto'),
   						id: 'bruto',
   						name: 'bruto',
      				   width:'100%',
   						hidden:true
   					},{
   						xtype: 'textfield',
   						fieldLabel: lang('Netto'),
   						id: 'netto',
   						name: 'netto',
      				   width:'100%',
   						readOnly:true
   					},{
   						xtype: 'textfield',
   						fieldLabel: lang('Premium'),
   						id: 'premium',
   						name: 'premium',
      				   width:'100%',
   						readOnly:true,
                    listeners: {
                        change: function(cb, nv, ov) {
                            if (Ext.getCmp('premium').getValue()>0) Ext.getCmp('save').show();
                            else Ext.getCmp('save').hide();
                        }
                    }
   					}]
      			},{
					xtype: 'fieldset',
					title: 'Kuitansi',
					padding:0,
					flex: 1,
					items: [{
            xtype: 'gridpanel',
            autoScroll:true,
            height:260,
            id:'grid_kuitansi',
            padding:0,
            features: [{
                 ftype: 'summary'
            }],
            store: store_kuitansi,
            width: '97%',
            loadMask: true,
            selType: 'rowmodel',
            columns: [{
              xtype: 'gridcolumn',
              text: lang('ID'),
              dataIndex:'PaymentNumber',
              width:'40%'
            },{
              text: lang('Tanggal'),
              dataIndex: 'PaymentDate',
              width:'28%'
            },{
              text: lang('Total'),
              dataIndex: 'PaymentPremium',
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
                     Ext.getCmp('nomor').setValue(sm.get('PaymentNumber'))
                     Ext.getCmp('netto').setValue(sm.get('PaymentNetto'))
                     Ext.getCmp('premium').setValue(Ext.util.Format.number(sm.get('PaymentPremium'), '0,000.00'))
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
					title: 'Transaksi',
					flex: 1,
					items: [{

            xtype: 'gridpanel',
            autoScroll:true,
            height:445,
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
              dataIndex:'SupplyTransID',
              summaryType: 'count',
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
              text: lang('Bruto'),
              dataIndex: 'bruto',
              summaryType: 'sum',
              width:'10%',
              align: 'right',
              renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                  return Ext.util.Format.number(value, '0,000.00');
              }
            },{
              text: lang('Netto'),
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
                  setKuitansi(s);
               },
               deselect: function(dv, record, item, index, e) {
                  var s = this.getSelectionModel().getSelection();
                  setKuitansi(s);
               }
           	},
               selModel: Ext.create('Ext.selection.CheckboxModel', {
                      checkOnly : true, // for prevent clicked grid row and canceled all check box checked status
                  })

               }]
            },{
                xtype:'button',
                margin:'2',
                hidden:true,
                text:lang('select unpaid'),
                handler:function(){
                    var grid = Ext.getCmp('grid_detail');
                    var stores = grid.getStore();
                    var sm = grid.getSelectionModel();
                    sm.deselectAll();
                    var paids = [];
                    var unpaids = [];

                    stores.each(function(one,index,all){
                      if(one.get('sudah') == 0) {
                        unpaids.push(one);
                      }
                    });

                    unpaids.length;
                    sm.select(unpaids);
                    Ext.Msg.alert('Selected', unpaids.length + ' record(s) selected.');

                }
            },{
                xtype:'button',
                margin:'2',
                hidden:true,
                text:lang('select paid'),
                handler:function(){
                    var grid = Ext.getCmp('grid_detail');
                    var stores = grid.getStore();
                    var sm = grid.getSelectionModel();
                    sm.deselectAll();
                    var paids = [];

                    stores.each(function(one,index,all){
                      if(one.get('belum') == 0) {
                        paids.push(one);
                      }
                    });

                    sm.select(paids);
                    Ext.Msg.alert('Selected', paids.length + ' record(s) selected.');
                }
            },
            {
                xtype:'button',
                margin:'2',
                text:lang('filter unpaid'),
                handler:function(){
                    var grid = Ext.getCmp('grid_detail');
                    var stores = grid.getStore();
                    var sm = grid.getSelectionModel();
                    sm.deselectAll();

                    stores.filterBy(function(one){
                        if(one.get('sudah') == 0) {
                            return true;
                        } else {
                            return false;
                        }
                    });
                }
            },{
                xtype:'button',
                margin:'2',
                text:lang('filter paid'),
                handler:function(){
                    var grid = Ext.getCmp('grid_detail');
                    var stores = grid.getStore();
                    var sm = grid.getSelectionModel();
                    sm.deselectAll();

                    stores.filterBy(function(one){
                        if(one.get('belum') == 0) {
                            return true;
                        } else {
                            return false;
                        }
                    });
                }
            },{
                xtype:'button',
                margin:'2',
                text:lang('clear filter'),
                handler:function(){
                    var grid = Ext.getCmp('grid_detail');
                    var stores = grid.getStore();
                    stores.clearFilter();
                }
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
            text: lang('Save'), //bener yang ini nih
            margin: '5px',
            scale: 'large',
            hidden:true,
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle;
                if (Ext.getCmp('nomor').getValue()!='') urle = m_crud+'u'; else urle = m_crud;
                var griddetail = Ext.getCmp('grid_detail');
                var sm = griddetail.getSelectionModel();
                var sel = sm.getSelection();
                if(sel.length > 0){

                } else {
                    Ext.Msg.alert('Failed', 'Please select data to generate');
                }

                form.submit({
                    url: urle,
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        store_detail.load({params: {id: o.result.id,jenis: Ext.getCmp('jenis').getValue(),
                           district: Ext.getCmp('district').getValue(),
                           awal: Ext.getCmp('awal').getSubmitValue(),akhir: Ext.getCmp('akhir').getSubmitValue()}});
                        store_kuitansi.load({params: {id: o.result.id,jenis: Ext.getCmp('jenis').getValue(),
                           district: Ext.getCmp('district').getValue(),
                           awal: Ext.getCmp('awal').getSubmitValue(),akhir: Ext.getCmp('akhir').getSubmitValue()}});
                        Ext.getCmp('netto').setValue();
                        Ext.getCmp('premium').setValue();
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
               Ext.getCmp('premium').setValue();
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
               store.load({params: {jenis: Ext.getCmp('jenis').getValue(),district: Ext.getCmp('district').getValue(),key: Ext.getCmp('key').getValue(),
                  awal: Ext.getCmp('awal').getSubmitValue(),akhir: Ext.getCmp('akhir').getSubmitValue()}});
                win.hide();
            }
        }]
    });
    function setKuitansi(s) {
      var netto,bruto,premium,i;
      var id = new Array();
      var berat = new Array();
      var premi = new Array();
      netto = bruto = premium = i = 0;
      Ext.each(s, function (item) {
         if (item.data.belum>0) {
            bruto += item.data.bruto;
            netto += item.data.netto;
            premium += item.data.belum;
            id[i] = item.data.SupplyTransID;
            berat[i] = item.data.netto;
            premi[i] = item.data.total;
            i++;
         }
      });
      Ext.getCmp('bruto').setValue(Math.round(bruto * 100) / 100)
      Ext.getCmp('netto').setValue(Math.round(netto * 100) / 100)
      Ext.getCmp('premium').setValue(Math.round(premium * 100) / 100)
      Ext.getCmp('id').setValue(id)
      Ext.getCmp('berat').setValue(berat)
      Ext.getCmp('premi').setValue(premi)
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
        if (Ext.getCmp('jenis').getValue()==null) err = 'Silahkan pilih jenis';
        else if (Ext.getCmp('awal').getValue()==null) err = 'Silahkan isi awal periode';
        else if (Ext.getCmp('akhir').getValue()==null) err = 'Silahkan isi akhir periode';
        if (err!='') {
         Ext.MessageBox.alert('Warning', err);
         return
        }
        store.proxy.extraParams = {
            district: Ext.getCmp('district').getValue(),
            jenis: Ext.getCmp('jenis').getValue(),
            key: Ext.getCmp('key').getValue(),
            awal: Ext.getCmp('awal').getSubmitValue(),
            akhir: Ext.getCmp('akhir').getSubmitValue(),
            cpg: Ext.getCmp('combo-cpg').getValue(),
            paid: Ext.getCmp('chk_paid').getValue()
        };
        store.load();
    }
    function setUpdate(r) {
         Ext.getCmp('pemberi').setValue(r.kiri.pemberi);
         Ext.getCmp('penerima').setValue(r.kiri.penerima);
         Ext.getCmp('penerima_type').setValue(r.kiri.penerima_type);
         Ext.getCmp('penerima_id').setValue(r.kiri.penerima_id);
         Ext.getCmp('pemberi_id').setValue(r.kiri.pemberi_id);
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
            Ext.Ajax.request({
               url: m_crud,
               method: 'GET',
               params: {id: sm.get('SupplychainID'),jenis:Ext.getCmp('jenis').getValue(),
                  district: Ext.getCmp('district').getValue(),
                  akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()},
               success: function(fp, o){
                   var r = Ext.decode(fp.responseText);
                   setUpdate(r)
               }
            });
            store_detail.load({params: {id: sm.get('SupplychainID'),jenis:Ext.getCmp('jenis').getValue(),
               district: Ext.getCmp('district').getValue(),
               akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()}});
            store_kuitansi.load({params: {id: sm.get('SupplychainID'),jenis:Ext.getCmp('jenis').getValue(),
               district: Ext.getCmp('district').getValue(),
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
                  Ext.Ajax.request({
                     url: m_crud,
                     method: 'GET',
                     params: {id: sm.get('SupplychainID'),jenis:Ext.getCmp('jenis').getValue(),
                        district: Ext.getCmp('district').getValue(),
                        akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()},
                     success: function(fp, o){
                        var r = Ext.decode(fp.responseText);
                        setUpdate(r)
                     }
                  });
                  store_detail.load({params: {id: sm.get('SupplychainID'),jenis:Ext.getCmp('jenis').getValue(),
                     district: Ext.getCmp('district').getValue(),
                     akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()}});
                  store_kuitansi.load({params: {id: sm.get('SupplychainID'),jenis:Ext.getCmp('jenis').getValue(),
                     district: Ext.getCmp('district').getValue(),
                     akhir:Ext.getCmp('akhir').getSubmitValue(),awal:Ext.getCmp('awal').getSubmitValue()}});
               },
               cls : m_act_update
            },{
                emptyText: '-- ' + lang('Jenis') + '--',
                id: 'jenis',
                name: 'jenis',
                xtype: 'combo',
                labelWidth: 60,
                store:mc_jenis,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
               listeners: {
              		specialkey: submitOnEnter,
              		change: function (cb, nv, ov) {
                            if (this.value!='koperasi') Ext.getCmp('district').show();
                            else Ext.getCmp('district').hide();
                            if(this.value == 'petani') {
                                Ext.getCmp('combo-cpg').show();
                                grid.columns[2].setVisible(true);
                            } else {
                                Ext.getCmp('combo-cpg').hide();
                                grid.columns[2].setVisible(false);
                            }
                 	}
              	}
            },{
                emptyText: '-- ' + lang('district') + '--',
                id: 'district',
                hidden:true,
                name: 'district',
                xtype: 'combo',
                labelWidth: 60,
                store:mc_district,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
               listeners: {
              		specialkey: submitOnEnter,
                        change: function (cb, nv, ov) {
                            mc_cpg.proxy.extraParams = {district:this.value};
                            mc_cpg.load();
                 	}
              	}
            },{
                emptyText: '-- ' + lang('cpg') + '--',
                id: 'combo-cpg',
                hidden:true,
                name: 'cpg',
                xtype: 'combo',
                labelWidth: 60,
                store:mc_cpg,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    specialkey: submitOnEnter
                }
            },{
               xtype: 'textfield',
               emptyText: lang('Cari dengan Nama/ID'),
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
                emptyText: '-- ' + lang('Awal') + '--',
                padding:5,
                width:120,
               listeners: {
              		specialkey: submitOnEnter
              	}
            },{
                xtype: 'datefield',
                format: 'Y-m-d',
                fieldLabel: '',
                id: 'akhir',
                name: 'akhir',
                emptyText: '-- ' + lang('Akhir') + '--',
                padding:5,
                width:120,
               listeners: {
              		specialkey: submitOnEnter
              	}
            },{
              xtype:'checkbox',
              checked:true,
              id:'chk_paid',
              boxLabel:'Include paid payment',
              name:'paid'
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
        text: lang('No'),
        xtype: 'rownumberer',
        width:'6%'
    },{
        text: lang('CPG'),
        width: '12%',
        dataIndex: 'GroupName'
    },{
        text: lang('Name'),
        width: '24%',
        dataIndex: 'name'
    },{
        text: lang('Transaksi'),
        width: '5%',
        align: 'right',
        dataIndex: 'trans'
    },{
        text: lang('Bruto'),
        width: '8%',
        dataIndex: 'bruto',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    },{
        text: lang('Netto'),
        width: '8%',
        dataIndex: 'netto',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    },{
        text: lang('Total'),
        width: '10%',
        dataIndex: 'total',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    },{
        text: lang('Terbayarkan'),
        width: '10%',
        dataIndex: 'sudah',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    },{
        text: lang('Terhutang'),
        width: '10%',
        dataIndex: 'belum',
        align: 'right',
        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
        }
    }]
   });
});
