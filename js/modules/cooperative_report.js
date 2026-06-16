Ext.onReady(function() {


    Ext.create('Ext.Container', {
        renderTo: 'ext-content',
        id: 'panel-report-cooperatives',
        layout:'hbox',
        items: [{
            xtype: 'panel',
            minHeight: 600,
            titleAlign: 'center',
            title: 'COOPERATIVE REPORT GENERATOR',
            layout: 'vbox',
            bodyStyle: 'background:#fff;',
            defaults: {
                margin: 5
            },
            items: [{
              xtype:'hidden',
              value:'',
              id:'hidden-subject-cooperative-report',
            },{
                xtype: 'buttongroup',
                width: 200,
                title: '<b>Report Subject</b>',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                defaults: {
                    margin: 5
                },
                items: [{
                    xtype: 'button',
                    ui: 's-button',
                    scale:'large',
                    padding:10,
                    style:'text-align:center',
                    cls:'s-green',
                    id:'btn-cooperative-report-member',
                    text: 'Member',
                    handler: function(){
                      this.disable();
                      Ext.getCmp('hidden-subject-cooperative-report').setValue('member');
                      Ext.getCmp('btn-cooperative-report-trans').enable();
                      Ext.getCmp('grid-report-cooperatives').setTitle('MEMBER');
                    }
                }, {
                    xtype: 'button',
                    ui: 's-button',
                    scale:'large',
                    padding:10,
                    style:'text-align:center',
                    id:'btn-cooperative-report-trans',
                    text: 'Transaction',
                    cls:'s-green',
                    handler:function(){
                      this.disable();
                      Ext.getCmp('hidden-subject-cooperative-report').setValue('trans');
                      Ext.getCmp('btn-cooperative-report-member').enable();
                      Ext.getCmp('grid-report-cooperatives').setTitle('TRANSACTION');
                    }
                }]
            }, {
                xtype: 'buttongroup',
                width: 200,
                title: '<b>Columns</b>',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                defaults: {
                    margin: 5
                },
                items: [{
                    xtype: 'boxselect',
                    itemId: 'valuesSelect',
                    id: 'valuesSelect',
                    hidden: false,
                    flex:true,
                    name: 'columns',
                    id:'cooperatives-report-columns',
                    store: Ext.create('Ext.data.Store', {
                        fields: ['column'],
                        autoLoad: true,
                        proxy: {
                            type: 'rest',
                            url: m_api + '/report/getcols',
                            reader: {
                                type: 'json',
                                root: 'data',
                                totalProperty: 'total'
                            }
                        }
                    }),
                    valueField: 'column',
                    displayField: 'column',
                    listeners: {
                      change: function(c,v){

                      }
                    }
                }]
            },{
                xtype: 'button',
                ui: 's-button',
                scale:'large',
                padding:10,
                width:200,
                style:'text-align:center',
                id:'btn-cooperative-report-generate',
                text: 'GENERATE',
                cls:'s-blue',
                handler:function(){
                  var cols = [];
                  var subject = Ext.getCmp('hidden-subject-cooperative-report').getValue();
                  var selectedcols = Ext.getCmp('cooperatives-report-columns').getValue();

                  Ext.define('Generated', {
                     extend: 'Ext.data.Model',
                     fields: selectedcols
                  });

                  var store = Ext.create('Ext.data.Store', {
                     model:'Generated',
                     proxy: {
                         type: 'ajax',
                         extraParams:{type:subject},
                         url: m_api + '/report/generatedata',
                         reader: {
                             type: 'json',
                             root: 'data'
                         }
                     },
                     autoLoad: false
                  });
                  Ext.each(selectedcols,function(one,idx,all){
                    cols.push({
                      text:one,
                      dataIndex:one,
                      width:300
                    });
                  });
                  Ext.getCmp('grid-report-cooperatives').reconfigure(store,cols);
                  store.load();
                }
            },{
                xtype: 'button',
                ui: 's-button',
                scale:'large',
                padding:10,
                width:200,
                style:'text-align:center',
                id:'btn-cooperative-report-xls',
                text: 'EXPORT TO XLS',
                cls:'s-green',
                handler:function(){

                  function EncodeQueryData(data)
                  {
                     var ret = [];
                     for (var d in data)
                        ret.push(encodeURIComponent(d) + "=" + encodeURIComponent(data[d]));
                     return ret.join("&");
                  }

                  var cols = [];
                  var subject = Ext.getCmp('hidden-subject-cooperative-report').getValue();
                  var selectedcols = Ext.getCmp('cooperatives-report-columns').getValue();

                  Ext.define('Generated', {
                     extend: 'Ext.data.Model',
                     fields: selectedcols
                  });

                  var store = Ext.create('Ext.data.Store', {
                     model:'Generated',
                     proxy: {
                         type: 'ajax',
                         extraParams:{type:subject},
                         url: m_api + '/report/generatedata',
                         reader: {
                             type: 'json',
                             root: 'data'
                         }
                     },
                     autoLoad: false
                  });

                  Ext.each(selectedcols,function(one,idx,all){
                    cols.push({
                      text:one,
                      dataIndex:one,
                      width:300
                    });
                  });

                  Ext.getCmp('grid-report-cooperatives').reconfigure(store,cols);
                  store.load();

                  var url = store.getProxy().url;
                  var querystring = EncodeQueryData({cols:Ext.JSON.encode(selectedcols),xls:'true'}); console.log(querystring);
                  window.open(url + '?' + querystring);
                }
            }]
        }, {
            xtype: 'gridpanel',
            flex: true,
            titleAlign:'center',
            margin:'0 0 0 5',
            id: 'grid-report-cooperatives',
            store:Ext.create('Ext.data.Store', {
               proxy: {
                   type: 'ajax',
                   url: m_api + '/report/generatedata',
                   reader: {
                       type: 'json',
                       root: 'data'
                   }
               },
               autoLoad: false
           }),
            columns: []
        }]
    });

});
