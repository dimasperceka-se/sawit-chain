Ext.define('Koltiva.view.dhis.list' ,{
    extend: 'Ext.grid.Panel',
    alias: 'widget.dhislist',
    renderTo:'ext-content',
    width: '100%',
    minHeight: 750,
    style: 'border:1px solid #CCC;',
    loadMask: true,
    selType: 'rowmodel',
    initComponent: function() {

      var me = this;
      var advSearch = Ext.create('Koltiva.view.dhis.advsearch',{grid:me, hidden:true});

      this.store = Ext.create('Koltiva.store.dhis.list');

      this.dockedItems = [{
          xtype: 'pagingtoolbar',
          store: this.store, // same store GridPanel is using
          dock: 'bottom',
          displayInfo: true
      },{
        xtype:'toolbar',
        dock: 'top',
        layout:{
          type:'hbox',
          align:'center'
        },
        style:'background:#F0F0F0',
        defaults:{
          margin:'0px 5px',
        },
        items:[
          advSearch,
          {
          xtype:'container',
          id:'frm-dhis-simple-search',
          layout:{
            type:'hbox',
            align:'center'
          },
          defaults:{
            margin:'0px 5px'
          },
          items:[{
            xtype:'textfield',
            id:'txt-search-dhis-register',
            width:500,
            emptyText: lang('Cari berdasar nama/ID')
          },{
              id: 'cmb-dhis-simple-search-province',
              xtype: 'combo',
              emptyText: lang('Province'),
              store: Ext.create('Koltiva.store.provinsi'),
              displayField: 'label',
              valueField: 'id',
              queryMode: 'local',
              width:250,
              listeners: {
                  select: function(cb, nv, ov) {
                    Ext.getCmp('cmb-dhis-simple-search-district').setValue('');
                    Ext.getCmp('cmb-dhis-simple-search-district').store.load({
                        params: {
                          p: cb.getValue()
                        }
                    });

                  }
              }
          },
          {
              id: 'cmb-dhis-simple-search-district',
              xtype: 'combo',
              emptyText: lang('District'),
              store: Ext.create('Koltiva.store.kabupaten'),
              displayField: 'label',
              valueField: 'id',
              queryMode: 'local',
              width:250
          }]
        },
        {
            xtype: 'button',
            id:'btn-adv-search-dhis-register',
            icon: varjs.config.base_url + 'images/icons/silk/page_white_wrench.png',
            margin: '0px 0px 0px 6px',
            text: lang('Advanced Search'),
            handler: function() {

              advSearch.show();
              Ext.getCmp('frm-dhis-simple-search').hide();
              Ext.getCmp('btn-sync-to-middleware-dhis-register').hide();
              this.hide();

            }
        },
        {
          xtype:'container',
          id:'btn-sync-to-middleware-dhis-register',
          items:[
            {
              xtype:'button',
              width:150,
              align:'right',
              margin: '0px 0px 0px 6px',
              text: lang('Search'),
              handler: function(){
                var name = Ext.getCmp('txt-search-dhis-register').getValue();
                var district = Ext.getCmp('cmb-dhis-simple-search-district').getValue();

                me.store.getProxy().extraParams = {
                  'name': name,
                  'district': district
                };

                me.store.load({params:{start:0}});
              }
            },
            {
              xtype:'button',
              width:150,
              align:'right',
              margin: '0px 0px 0px 6px',
              text: lang('Sync to Middleware'),
              handler: function(){
                var sel = me.getSelectionModel().getSelection();
                var ids = [];

                if(sel.length > 0) {

                  Ext.each(sel,function(one,idx,all){
                    ids.push(one.get('FarmerID'));
                  });

                  Ext.MessageBox.show({
                     title:'Save Changes?',
                     msg: 'Are you sure, you want to syncronize these data?',
                     buttons: Ext.MessageBox.YESNO,
                     fn: function(r){ console.log(r);
                       Ext.Ajax.request({
                          url: m_request_sync,
                          method:'POST',
                          params: {
                              data: Ext.JSON.encode(ids)
                          },
                          success: function(response){
                              var text = response.responseText;
                              var obj = Ext.JSON.decode(text);
                              if(obj.success == true){
                                Ext.MessageBox.show({
                                   title:'Success',
                                   msg: 'Syncronize successfull',
                                   buttons: Ext.MessageBox.OK,
                                   fn: function(){}
                               });
                             } else {
                               Ext.MessageBox.show({
                                  title:'Failed',
                                  msg: 'Syncronize unsuccessfull',
                                  buttons: Ext.MessageBox.OK,
                                  fn: function(){}
                              });
                             }
                          }
                      });
                     }
                   });
                } else {
                  Ext.MessageBox.show({
                     title:'Failed, no selection',
                     msg: 'Please select data you want to syncronize',
                     buttons: Ext.MessageBox.YESNO,
                     fn: function(){}
                 });
                }
              }
            }
          ]
        }]
      }];

      this.selType = 'checkboxmodel';
      this.columns = [{
          text: lang('No'),
          xtype: 'rownumberer',
          width: '3%'
      }, {
          text: lang('Synced'),
          width:75,
          align:'center',
          dataIndex: 'Synced',
          renderer: function(v) {
            if(v == ''){
              return '<img src="'+varjs.config.base_url + 'images/check_error.png'+'" alt="Not Synced" />';
            } else {
              return '<img src="'+varjs.config.base_url + 'images/tick.png'+'" alt="Synced" />';
            }
          }
      }, {
          text: lang('Farmer Name'),
          flex:true,
          dataIndex: 'FarmerName'
      }, {
          text: lang('Group Name'),
          width: '15%',
          dataIndex: 'GroupName'
      }, {
          text: lang('Village Name'),
          width: '15%',
          dataIndex: 'Village'
      }, {
          text: lang('Sub District'),
          width: '15%',
          dataIndex: 'SubDistrict'
      }, {
          text: lang('Certification'),
          width: 100,
          dataIndex: 'YearCertification'
      }, {
          text: lang('Land Size'),
          width: 100,
          dataIndex: 'LandSize'
      }, {
          text: lang('Production'),
          width: 120,
          dataIndex: 'Production'
      }];

      this.callParent(arguments);
    }
});
