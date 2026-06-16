Ext.define('Koltiva.view.dhis.advsearch' ,{
    extend: 'Ext.form.Panel',
    alias: 'widget.searchpnl',
    grid: false,
    layout:{
      type:'table',
      columns:1
    },
    initComponent: function() {

      var me = this;

      if(me.grid) {

        /** Kagakk jadii..
        var grid = me.grid;
        grid.on('render',function(){
          //ambil store dari grid
          var store = grid.store;
          var model = store.model;
          var cols = grid.columns;
          console.log(cols);
        });
        */

        me.items = [
          {
            xtype:'container',
            layout:{
              type:'hbox'
            },
            margin:'0px 0px 10px 0px',
            defaults:{
              margin:2
            },
            items:[{
              xtype:'tbtext',
              width:150,
              text:lang('Pick filters')
            },{
                xtype:'boxselect',
                width:600,
                id:'box-adv-filter-dhis-register',
                store: Ext.create('Koltiva.store.dhis.searchpicker'),
                queryMode: 'local',
                displayField: 'label',
                valueField: 'id'
            },{
              xtype:'button',
              text:'Reload Filters',
              handler: function() {

                Ext.getCmp('main-container-search-dhis-register').getForm().reset();

                //hide
                Ext.getCmp('container-adv-search-farmerid-dhis-register').hide();
                Ext.getCmp('container-adv-search-production-dhis-register').hide();
                Ext.getCmp('container-adv-search-yearcertification-dhis-register').hide();
                Ext.getCmp('container-adv-search-landsize-dhis-register').hide();
                Ext.getCmp('container-adv-search-province-dhis-register').hide();
                Ext.getCmp('container-adv-search-synced-dhis-register').hide();

                var picks = Ext.getCmp('box-adv-filter-dhis-register').getValue();
                Ext.each(picks,function(one,idx,all){
                    Ext.getCmp(one).show();
                });
                Ext.getCmp('main-container-search-dhis-register').doLayout();
              }
            }]
          },
          {
            xtype:'form',
            id:'main-container-search-dhis-register',
            items:[{
              xtype:'container',
              hidden:true,
              id:'container-adv-search-farmerid-dhis-register',
              layout:{
                type:'hbox'
              },
              defaults:{
                margin:2
              },
              items:[
                {
                  xtype:'tbtext',
                  width:150,
                  text:lang('Farmer ID or Name')
                },
                Ext.create('Ext.form.ComboBox', {
                    width:100,
                    id:'operator-adv-search-farmerid-dhis-register',
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'label'],
                        data : [
                            {"id":"like", "label":"Like"},
                            {"id":"equal", "label":"Equals"}
                        ]
                    }),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }),
                {
                  xtype:'textfield',
                  id:'txt-adv-search-farmerid-dhis-register',
                  width:500,
                  emptyText: lang('Cari berdasar nama/ID')
                }
              ]
            },
            {
              xtype:'container',
              layout:{
                type:'hbox'
              },
              hidden:true,
              id:'container-adv-search-production-dhis-register',
              defaults:{
                margin:2
              },
              items:[
                {
                  xtype:'tbtext',
                  width:150,
                  text:lang('Production')
                },
                Ext.create('Ext.form.ComboBox', {
                    width:100,
                    id:'operator-adv-search-production-dhis-register',
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'label'],
                        data : [
                            {"id":"greater", "label":">"},
                            {"id":"fewer", "label":"<"},
                            {"id":"equal", "label":"="}
                        ]
                    }),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }),
                {
                  xtype:'numericfield',
                  id:'txt-adv-search-production-dhis-register',
                  emptyText:lang('Production')
                }
              ]
            },
            {
              xtype:'container',
              layout:{
                type:'hbox'
              },
              hidden:true,
              id:'container-adv-search-province-dhis-register',
              defaults:{
                margin:2
              },
              items:[
                {
                  xtype:'tbtext',
                  width:150,
                  text:lang('District')
                },
                {
                    id:'cmb-adv-search-province-dhis-register',
                    xtype: 'combo',
                    width:250,
                    emptyText: lang('Province'),
                    store: Ext.create('Koltiva.store.provinsi'),
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    listeners: {
                        select: function(cb, nv, ov) {
                          Ext.getCmp('cmb-adv-search-district-dhis-register').setValue('');
                          Ext.getCmp('cmb-adv-search-district-dhis-register').store.load({
                              params: {
                                p: cb.getValue()
                              }
                          });

                        }
                    }
                },
                {
                    xtype: 'combo',
                    width:250,
                    id:'cmb-adv-search-district-dhis-register',
                    emptyText: lang('District'),
                    store: Ext.create('Koltiva.store.kabupaten'),
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local'
                }
              ]
            },
            {
              xtype:'container',
              layout:{
                type:'hbox'
              },
              hidden:true,
              id:'container-adv-search-landsize-dhis-register',
              defaults:{
                margin:2
              },
              items:[
                {
                  xtype:'tbtext',
                  width:150,
                  text:lang('Land Size')
                },
                Ext.create('Ext.form.ComboBox', {
                    width:100,
                    id:'operator-adv-search-landsize-dhis-register',
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'label'],
                        data : [
                            {"id":"greater", "label":">"},
                            {"id":"fewer", "label":"<"},
                            {"id":"equal", "label":"="}
                        ]
                    }),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }),
                {
                  xtype:'numericfield',
                  id:'txt-adv-search-landsize-dhis-register',
                  emptyText:lang('Land Size')
                }
              ]
            },
            {
              xtype:'container',
              layout:{
                type:'hbox'
              },
              defaults:{
                margin:2
              },
              hidden:true,
              id:'container-adv-search-yearcertification-dhis-register',
              items:[
                {
                  xtype:'tbtext',
                  width:150,
                  text:lang('Year Certification')
                },
                Ext.create('Ext.form.ComboBox', {
                    width:100,
                    id:'cmb-adv-search-year-certification-dhis-register',
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'label'],
                        data : [
                            {"id":"2010", "label":"2010"},
                            {"id":"2011", "label":"2011"},
                            {"id":"2012", "label":"2012"},
                            {"id":"2013", "label":"2013"},
                            {"id":"2014", "label":"2014"},
                            {"id":"2015", "label":"2015"},
                            {"id":"2016", "label":"2016"},
                        ]
                    }),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                })
              ]
            },
            {
              xtype:'container',
              layout:{
                type:'hbox'
              },
              defaults:{
                margin:2
              },
              hidden:true,
              id:'container-adv-search-synced-dhis-register',
              items:[
                {
                  xtype:'tbtext',
                  width:150,
                  text:lang('Synced')
                },
                Ext.create('Ext.form.ComboBox', {
                    width:100,
                    id:'cmb-adv-search-synced-dhis-register',
                    store: Ext.create('Ext.data.Store', {
                        fields: ['id', 'label'],
                        data : [
                            {"id":"yes", "label":"Yes"},
                            {"id":"no", "label":"No"},
                            {"id":"all", "label":"All"}
                        ]
                    }),
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                })
              ]
            }]
          },
          {
            xtype:'container',
            margin:'10px 0px 0px 5px',
            layout:{
              type:'hbox'
            },
            items:[
              {
                xtype:'button',
                flex:false,
                width:120,
                align:'right',
                margin: '0px 0px 0px 6px',
                text: lang('Search'),
                handler: function(){
                  var grid = me.grid;
                  var proxy = grid.store.getProxy();

                  proxy.extraParams = {
                    name: Ext.getCmp('txt-adv-search-farmerid-dhis-register').getValue(),
                    nameop: Ext.getCmp('operator-adv-search-farmerid-dhis-register').getValue(),
                    district:  Ext.getCmp('cmb-adv-search-district-dhis-register').getValue(),
                    synced:  Ext.getCmp('cmb-adv-search-synced-dhis-register').getValue(),
                    prod: Ext.getCmp('txt-adv-search-production-dhis-register').getValue(),
                    prodop: Ext.getCmp('operator-adv-search-production-dhis-register').getValue(),
                    ycert: Ext.getCmp('cmb-adv-search-year-certification-dhis-register').getValue(),
                    landsize: Ext.getCmp('txt-adv-search-landsize-dhis-register').getValue(),
                    landsizeop: Ext.getCmp('operator-adv-search-landsize-dhis-register').getValue()
                  };

                  grid.store.load();
                }
              },
              {
                xtype:'button',
                flex:false,
                width:150,
                align:'right',
                margin: '0px 0px 0px 6px',
                text: lang('Simple Search'),
                handler: function(){
                  me.hide();
                  me.getForm().reset();
                  Ext.getCmp('frm-dhis-simple-search').show();
                  Ext.getCmp('btn-adv-search-dhis-register').show();
                  Ext.getCmp('btn-sync-to-middleware-dhis-register').show();

                }
              },
              {
                xtype:'button',
                flex:false,
                width:150,
                align:'right',
                margin: '0px 0px 0px 6px',
                text: lang('Sync Selected to Middleware'),
                handler: function(){

                  var sel = me.grid.getSelectionModel().getSelection();
                  var ids = [];

                  if(sel.length > 0) {

                    Ext.MessageBox.show({
                       title:'Sync Data?',
                       msg: 'Are you sure, you want to syncronize these data?',
                       buttons: Ext.MessageBox.YESNO,
                       fn: function(r){
                         if(r == 'yes'){

                             var p = Ext.MessageBox.show({
                                title: 'Please wait',
                                msg: 'Syncronizing items...',
                                progressText: 'Initializing...',
                                width:300,
                                progress:true,
                                closable:true
                            });

                            Ext.each(sel,function(one,idx,all){

                              Ext.Ajax.request({
                                url: m_request_sync + '/' + one.get('FarmerID'),
                                method:'POST',
                                async:false,
                                timeout:360000,
                                params: {
                                    data: Ext.JSON.encode([one.get('FarmerID')])
                                },
                                success: function(response){
                                    var text = response.responseText;
                                    var obj = Ext.JSON.decode(text);
                                    if(obj.success == true){
                                      console.log(((idx+1)+'/'+sel.length) + ': ' + ((idx+1)/sel.length));
                                      p.updateProgress(((idx+1)/sel.length), Math.round(((idx+1)/sel.length) * 100) + '% completed');
                                      if(((idx+1)/sel.length) == 1){
                                        p.close();
                                      }

                                   } else {
                                     Ext.MessageBox.show({
                                        title:'Failed',
                                        msg: 'Syncronize unsuccessfull',
                                        buttons: Ext.MessageBox.OK,
                                        fn: function(){}
                                    });

                                    return false;

                                   }
                                }
                              });

                            });

                         }
                       }
                     });



                  } else {
                    Ext.MessageBox.show({
                       title:'Failed, no selection',
                       msg: 'Please select data you want to syncronize',
                       buttons: Ext.MessageBox.OK,
                       fn: function(){}
                   });
                  }

                  /*
                  var sel = me.grid.getSelectionModel().getSelection();
                  var ids = [];

                  if(sel.length > 0) {

                    Ext.each(sel,function(one,idx,all){
                      if(one.get('Synced') == ''){
                        ids.push(one.get('FarmerID'));
                      }
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
                  */
                }
              },
              {
                xtype:'button',
                flex:false,
                width:150,
                align:'right',
                margin: '0px 0px 0px 6px',
                text: lang('Sync All to Middleware'),
                handler: function(){

                  Ext.MessageBox.show({
                     title:'Save Changes?',
                     msg: 'Are you sure, you want to syncronize all data?',
                     buttons: Ext.MessageBox.YESNO,
                     fn: function(r){ console.log(r);
                       if(r == 'yes'){
                         Ext.Ajax.request({
                            url: m_request_sync_all,
                            method:'POST',
                            timeout:3600000,
                            params: {
                              name:       Ext.getCmp('txt-adv-search-farmerid-dhis-register').getValue(),
                              nameop:     Ext.getCmp('operator-adv-search-farmerid-dhis-register').getValue(),
                              district:   Ext.getCmp('cmb-adv-search-district-dhis-register').getValue(),
                              synced:     Ext.getCmp('cmb-adv-search-synced-dhis-register').getValue(),
                              prod:       Ext.getCmp('txt-adv-search-production-dhis-register').getValue(),
                              prodop:     Ext.getCmp('operator-adv-search-production-dhis-register').getValue(),
                              ycert:      Ext.getCmp('cmb-adv-search-year-certification-dhis-register').getValue(),
                              landsize:   Ext.getCmp('txt-adv-search-landsize-dhis-register').getValue(),
                              landsizeop: Ext.getCmp('operator-adv-search-landsize-dhis-register').getValue()
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
                     }
                   });

                }
              }
            ]
          }
        ];
      }

      this.callParent(arguments);
    }
});
