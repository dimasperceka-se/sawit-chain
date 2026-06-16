Ext.onReady(function(){
   var mc_Provinsi = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      autoLoad: true,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_Provinsi,
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
   var mc_Kabupaten = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_Kabupaten,
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
    var mc_jenis_bu = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            "label": "Farmer"
        }, {
            "label": "Non Farmer"
        }]
    });
   var mc_bu = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['SupplychainID','Name'],
      autoLoad: true,
      proxy: {
         type: 'ajax',
         url: m_bu,
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
   var tab = Ext.create('Ext.Panel', {
     renderTo: 'ext-content',
     height : 680,
     frame: false,
     items: [{
     xtype: 'fieldset',
     title : 'Tracebility',
     id:'e',
     items :[{
        xtype: 'form',
        padding:5,
        fieldDefaults: {
            labelAlign: 'center',
            labelWidth: 160,
            anchor: '100%'
        },
        items :[{
         layout:'column',
         border: false,
         items: [{
            columnWidth: .40,
            layout: 'form',
            padding:3,
            border: false,
            items:[{
                   emptyText: '-- Buying Unit --',
                   id: 'BuyingUnit',
                   name: 'BuyingUnit',
                   xtype: 'combo',
                   labelWidth: 60,
                   store:mc_bu,
                   displayField: 'Name',
                   valueField: 'SupplychainID',
                   queryMode: 'local',
                   //listeners: {
                     //  if ()
                   //}
               }]
            },{
            columnWidth: .15,
            layout: 'form',
            //hidden:true,
            padding:3,
            border: false,
            items:[{
                   emptyText: '-- '+lang('Jenis')+' --',
                   id: 'JenisBuyingUnit',
                   name: 'JenisBuyingUnit',
                   xtype: 'combo',
                   labelWidth: 60,
                   store:mc_jenis_bu,
                   displayField: 'label',
                   valueField: 'label',
                   queryMode: 'local'
               }]
            },{
            columnWidth: .2,
            layout: 'form',
            padding:3,
            border: false,
            items:[{
                xtype: 'datefield',
                format: 'Y-m-d',
                fieldLabel: '',
                id: 'start',
                name: 'start',
                emptyText: '-- '+lang('Awal')+' --',
                padding:5
               }]
            },{
            columnWidth: .04,
            layout: 'form',
            padding:3,
            border: false,
            items:[{
                   xtype: 'label',
                   text:lang('s.d.')
               }]
            },{
            columnWidth: .2,
            layout: 'form',
            padding:3,
            border: false,
            items:[{
                xtype: 'datefield',
                format: 'Y-m-d',
                fieldLabel: '',
                id: 'end',
                name: 'end',
                emptyText: '-- '+lang('Akhir')+' --',
                padding:5
               }]
            }]
         }],
        buttons: [{
            id: 'Tracebility',
            text: lang('Cetak'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               preview_cetak_surat(m_cetak_tracebility+'/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                  Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/'+
                  Ext.getCmp('JenisBuyingUnit').getValue());
            }
        },{
            id: 'Tracebility_detail',
            text: 'Detail',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               preview_cetak_surat(m_cetak_tracebility+'_detail/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                  Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/'+
                  Ext.getCmp('JenisBuyingUnit').getValue(),1100,'40%');
            }
        },{
            id: 'Tracebility_per_petani',
            text: lang('Per Petani'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               preview_cetak_surat(m_cetak_tracebility+'_perpetani/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                  Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/'+
                  Ext.getCmp('JenisBuyingUnit').getValue(),1100,'40%');
            }
        }]
    }]
    }]
   });

});
