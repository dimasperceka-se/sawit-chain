Ext.require('Ext.tab.*');

Ext.onReady(function(){



////GRID purchase
var storeDeposito = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['InterestDepositCoopID','CoopID','name','memberSavingNo','amount','CreatedDate','totalDeposito','bungaDeposito','totalDeposito','savingTypeName'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_api+'/cooperatives/deposito',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

function displayFormWindow(){
    if(!win.isVisible()){
        // DataFormpurchase.getForm().reset();
        win.show();
    } else {
        win.hide(this, function() {});
        win.toFront();
    }
}



//GRID purchase
var gridDeposito = Ext.create('Ext.grid.Panel', {
        store: storeDeposito,
        renderTo: 'ext-content',
        width: '100%',
        title:'Perhitungan Bunga Deposito',
        minHeight: 450,
        id:'gridDeposito',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                set_data_purchase(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storeDeposito,   
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Hitung'),
                scope: this,
                handler : function(){
                      Ext.Ajax.request({
                             waitMsg: 'Please Wait',
                             url: m_api+'/cooperatives/deposit_interest',
                             method: 'GET',
                             params: {
                                 // id: smb.raw.DetailID
                             },
                             success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('Info', obj.message);
                                storeDeposito.load();
                             },
                             failure: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                             }
                          });
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Update'),
                scope: this,
                hidden:true,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('gridDeposito').getSelectionModel().getSelection()[0];
                  set_data_purchase(sm)
                   Ext.getCmp('ToolbarDetailpurchase').show();
                },
                cls : m_act_update
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                hidden:true,
                scope: this,
                handler : function(){
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  link(m_retur+sm.get('PurchaseID'));
                },
                cls : m_act_update
            },{
               itemId: 'remove',
               hidden:true,
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: lang('Hapus'),
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('gridDeposito').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                  // console.log(smb.data)
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.data.PurchaseID},
                        success: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           switch(obj.success){
                              case true: store.load();
                              break;
                              default: Ext.MessageBox.alert('Warning',obj.message);
                              break;
                           }
                        },
                        failure: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                        }
                     });
                     }
                 });
               }
            },'->','Periode:',{
              xtype: 'datefield',
              name: 'searchAwal',
                   width:100,
              id: 'searchAwal',
              format: 'Y-m-d',
              emptyText: lang('Awal'),
          }, {
              xtype: 'label',
              text: ' s.d '
          },{
                   width:100,
              xtype: 'datefield',
              name: 'searchAkhir',
              id: 'searchAkhir',
              format: 'Y-m-d',
              emptyText: lang('Akhir'),
          }, {
              xtype: 'button',
              margin: '0px 0px 0px 6px',
              text: 'Search',
              handler: function () {
                  storeDeposito.load({
                      params: {
                          Awal: Ext.getCmp('searchAwal').getSubmitValue(),
                          Akhir: Ext.getCmp('searchAkhir').getSubmitValue(),
                          // OrgType: Ext.getCmp('searchOrgType').getValue(),
                          // OrgID: Ext.getCmp('searchOrgID').getValue(),
                      }
                  });
              }
          }]
        }],        
        columns: [
        {
          text:'MemberID',
          dataIndex:'MemberID',
          hidden:true,
        },             
        {
            text: lang('No'),
            xtype: 'rownumberer',
            width:'5%'
        },{
            text: lang('Nama Nasabah'),
            width: '15%',
            dataIndex: 'name'
        },{
            text: lang('Nomor Simpanan'),
            width: '15%',
            dataIndex: 'memberSavingNo'
        },{
            text: lang('Nama Produk'),
            width: '10%',
            dataIndex: 'savingTypeName'
        },{
            text: lang('Rate Deposito'),
            width: '10%',
            dataIndex: 'bungaDeposito'
        },{
            text: lang('Total Simpanan'),
            flex:2,
            width: '20%',
            dataIndex: 'totalDeposito',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00'
        },{
            text: lang('Total Bunga'),
            width: '13%',
            dataIndex: 'amount',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00'
        },{
            text: lang('Tanggal Update'),
            width: '13%',
            dataIndex: 'CreatedDate'
        }]
    });


////END GRID purchase


 // var tabpurchase = Ext.widget('tabpanel', {
 //        renderTo: 'ext-content',
 //        plain:true,
 //        autoWidth:true,
 //        activeTab: 0,
 //        defaults :{
 //            // bodyPadding: 10
 //        },
 //        items: [gridDeposito,gridHutang]
 //    });

///functiin 
function calcSubtotal(d)
{
    // console.log(d)
    // Ext.getCmp('Subtotal').setValue(12312321);
    var SubTotal = 0;
    // d.each(function(d){
    //     // data.push(rec.get('field'));
    //     console.log(d)
    //     SubTotal+=d.data.Total;
    // });

    d.forEach(function(v) {
        console.log(v);
        SubTotal+=v.data.Total;
    });
    Ext.getCmp('Subtotal').setValue(SubTotal);
}

function calcGrandTotal()
{
    var Subtotal = Ext.getCmp('Subtotal').getValue()*1;
    var pajak = Ext.getCmp('pajak').getValue()*1;
    var diskon = Ext.getCmp('diskon').getValue()*1;
    
    Ext.getCmp('grandtotal').setValue((Subtotal+pajak)-diskon);
}

function calcSisa()
{
    var grandtotal = Ext.getCmp('grandtotal').getValue()*1;
    var totalbayar = Ext.getCmp('totalbayar').getValue()*1;

    Ext.getCmp('sisabayar').setValue(grandtotal-totalbayar);
}

function calcPelunasan()
{
    var sisabayar = Ext.getCmp('sisabayartmp').getValue()*1;
    var pelunasan = Ext.getCmp('pelunasan').getValue()*1;

    Ext.getCmp('sisabayar').setValue(sisabayar-pelunasan);
}

function set_data_purchase(data)
{
  // console.log(data);
  Ext.getCmp('pelunasan').show();
  Ext.getCmp('Subtotal').hide();

  Ext.getCmp('SupplierName').setValue(data.data.SupplierName);
  Ext.getCmp('tglpurchase').setValue(data.data.Date);
  Ext.getCmp('memo').setValue(data.data.JournalMemo);
  Ext.getCmp('PurchaseID').setValue(data.data.PurchaseID);
  Ext.getCmp('SupplierID').setValue(data.data.MemberID);
  Ext.getCmp('SupplierTypeID').setValue(1);

  // Ext.getCmp('Subtotal').setValue(data.data.MemberID);
  Ext.getCmp('pajak').setValue(data.data.Pajak);
  Ext.getCmp('pajak').setReadOnly(true);
  Ext.getCmp('diskon').setValue(data.data.Diskon);
  Ext.getCmp('diskon').setReadOnly(true);
  Ext.getCmp('grandtotal').setValue(data.data.Total);
  Ext.getCmp('totalbayar').setValue(data.data.Pembayaran);
  Ext.getCmp('totalbayar').setReadOnly(true);
  Ext.getCmp('sisabayar').setValue(data.data.SisaBayar);
  Ext.getCmp('sisabayartmp').setValue(data.data.SisaBayar);
  
  if(data.data.DueDate!==null)
  {
    var duedate = Ext.getCmp('duedate');
    duedate.show();
    if(data.data.DueDate!==null)
    {
      var strdd = data.data.DueDate;
      var dd = strdd.split('-');
      var tglnya = dd[2]+'/'+dd[1]+'/'+dd[0];
      duedate.setValue(tglnya);

      // Ext.getCmp('typeBayar').setValue(2);
      // var val = {rb : 2};
      Ext.getCmp('typeBayar').setValue({typeBayarRb : 2});
    } else {
      Ext.getCmp('typeBayar').setValue({typeBayarRb : 1});
    }
   
  } 

  Ext.getCmp('pelunasan').setValue(0);

  store_detail.load({
                  params:{
                    PurchaseID:data.data.PurchaseID
                  }
                });
}

function disable_field(opt)
{
  Ext.getCmp('tglpurchase').setDisabled(opt);
  Ext.getCmp('memo').setDisabled(opt);
  Ext.getCmp('SupplierName').setDisabled(opt);
  Ext.getCmp('tglpurchase').setDisabled(opt);
  Ext.getCmp('tglpurchase').setDisabled(opt);
  Ext.getCmp('tglpurchase').setDisabled(opt);
}



});