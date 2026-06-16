Ext.Loader.setConfig({enabled: true});

Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux/DataView/');

// Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.util.*',
    'Ext.view.View',        
    'Ext.ux.DataView.DragSelector'    
]);

Ext.onReady(function(){
    
    Ext.tip.QuickTipManager.init();
         
    ImageModel = Ext.define('ImageModel', {
        extend : 'Ext.data.Model',        
        fields: [         
          // {name: 'group'},
          // {name: 'list'},
           {name: 'MonitoringFilesID'},
           {name: 'MonitoringID'},           
           {name: 'FileTitle'},  
           {name: 'FilePath'},
           {name: 'FileName'},
           {name: 'FileType'},
           {name: 'FileSize',    type: 'float'},           
           {name: 'DateCreated'},
           {name: 'CreatedBy'},
           {name: 'nama'},
           {name: 'ket'}
        ]
    });
    var store_gallery = Ext.create('Ext.data.Store', {
        model    : 'ImageModel',
        autoLoad : true,
        pageSize : 16,
        proxy: {
            type : 'ajax',
            url  : m_grid,                        
            reader: {
               type : 'json',
               root : 'data',
               totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.Category = Ext.getCmp('Category').getValue();
                store.proxy.extraParams.Type = Ext.getCmp('Type').getValue();
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
            }
        },
    });
    var mc_category = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data : [
            {"id":"Farmer",    "label":lang("Farmer")},
            {"id":"Garden",    "label":lang("Garden")},
            {"id":"CPG",       "label":lang("CPG")},
            {"id":"Nursery",   "label":lang("Nursery")},
            {"id":"Compost",   "label":lang("Compost")},
            {"id":"Demoplot",  "label":lang("Demoplot")},
            {"id":"Coop",      "label":lang("Coop")},
            {"id":"Warehouse", "label":lang("Warehouse")},
            {"id":"Trader",    "label":lang("Trader")},
            {"id":"SCE",       "label":lang("SCE")},
            {"id":"Village",       "label":lang("Village")},
        ]
    }); 
    var mc_type = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data : [
            {"id":"Farmer",    "label":lang("Farmer")},
            // {"id":"Garden",    "label":lang("Garden")},
            {"id":"CPG",       "label":lang("CPG")},
            // {"id":"Demoplot",  "label":lang("Demoplot")},
            {"id":"Coop",      "label":lang("Coop")},
            // {"id":"Warehouse", "label":lang("Warehouse")},
            // {"id":"Trader",    "label":lang("Trader")},
            // {"id":"SCE",       "label":lang("SCE")},
            // {"id":"Village",       "label":lang("Village")},
        ]
    });  
    
    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
           store_gallery.load({
           params: {
               key: Ext.getCmp('key').getValue()
           }});
        }
    }    
    

    Ext.create('Ext.Panel', {
      store     : store_gallery,
      width     : '100%',      
      id        : 'images-view',
      minHeight :  1005,
      renderTo  : 'ext-content',
      frame     :  false,
      loadMask  :  true,
      style     : 'border:1px solid #999999;',
      dockedItems  : [
        {
              xtype : 'pagingtoolbar',              
              store : store_gallery,   
              dock  : 'bottom',
              displayInfo: true
        },
        {
            xtype : 'toolbar',
            items : [                                          
              {
                id           : 'Category',
                name         : 'Category',
                xtype        : 'combo',
                store        :  mc_category,
                displayField : 'label',
                valueField   : 'id',
                queryMode    : 'local',
                emptyText    : lang('Category'),
                listeners    : {
                  change: function (cb, nv, ov) {
                    if (nv == 'Nursery' || nv == 'Compost') {
                      Ext.getCmp('Type').show();
                    } else {
                      Ext.getCmp('Type').hide();
                    }
                  }
                }
              },
              {
                id           : 'Type',
                name         : 'Type',
                xtype        : 'combo',
                store        :  mc_type,
                displayField : 'label',
                valueField   : 'id',
                queryMode    : 'local',
                emptyText    : lang('Type'),
                hidden       : true,
                listeners    : {
                  change: function (cb, nv, ov) {
                  }
                }
              },         
              {
               xtype     : 'textfield',
               emptyText : lang('Activity Title'),
               name      : 'key',
               id        : 'key',
               width     : 360,               
               listeners : {
                   specialkey: submitOnEnter
                }
              },
              {
                 xtype   :'button',
                 icon    : varjs.config.base_url+'images/icons/silk/search.png',
                 margin  : '0px 0px 0px -10px',
                  text    : lang('Search'),
                  handler : function() {
                    store_gallery.load({
                      params: {
                          Category: Ext.getCmp('Category').getValue(),
                          Type: Ext.getCmp('Type').getValue(),
                          key: Ext.getCmp('key').getValue()
                      }
                    });
                  }                 
              },'->',   
              {
                xtype   : 'textfield', 
                hidden  : true,
                id      : 'foto_id'
              },
              {
                xtype   : 'textfield', 
                hidden  : true,
                id      : 'foto_name'
              },           
              // REMOVE
              {
                itemId  : 'remove',
                hidden  : true,
                id      : 'remove',
                icon    : varjs.config.base_url+'images/icons/silk/delete.png',
                cls     : m_act_delete,
                text    : lang('Delete'),
                scope   : this,
                handler : function(){
                    if (!Ext.getCmp('foto_id').getValue()) {
                        Ext.MessageBox.alert('Failed', lang('Please select photo first !'));
                        
                        Ext.getCmp('remove').setVisible(false);
                        Ext.getCmp('cancel').setVisible(false);
                        
                        Ext.getCmp('foto_id').setValue("")
                        Ext.getCmp('foto_name').setValue("")

                    } else {
                         Ext.MessageBox.confirm('Message', lang('Do you want to delete this photo ?'), function(btn){
                        if(btn == 'yes'){
                            Ext.Ajax.request({                                            
                                url      : m_grid,
                                method   : 'DELETE',
                                params   : {
                                    id   : Ext.getCmp('foto_id').getValue(),
                                    name : Ext.getCmp('foto_name').getValue()
                                },
                                waitMsg  : lang('Deleting photo ...'),
                                success: function(response, opts){
                                   
                                    store_gallery.load(); 

                                    Ext.getCmp('foto_id').setValue("")
                                    Ext.getCmp('foto_name').setValue("")
                                },
                                failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('Failed','Photo could not be delete');
                                }
                            });
                       }
                   });
                    }
                   
                }
              },
              // Batal
              {
                itemId  : 'cancel',
                hidden  : true,
                id      : 'cancel',
                icon    : varjs.config.base_url+'images/icons/silk/building_go.png',
                cls     : m_act_cancle,
                text    : lang('Cancel'),
                scope   : this,
                handler : function(){                    
                    store_gallery.load();
                    Ext.getCmp('remove').setVisible(false);
                    Ext.getCmp('cancel').setVisible(false);
                    Ext.getCmp('foto_id').setValue("")
                    Ext.getCmp('foto_name').setValue("")
                }
              }
                
            ]
        },
        {
            xtype  : 'toolbar',
            id     : 'toolbar_dua',
            hidden : false,
            items  : [
                {
                      xtype      : 'displayfield',                                
                      hidden     : false,
                      style      : 'fontSize: 8px;',                                
                      id         : 'file_detail',
                      name       : 'file_detail'
                }               
            ]
        }
      ],
      items: Ext.create('Ext.view.View', {
            style        : 'padding:5px;border-top:1px solid #CCC;',
            store        : store_gallery,
            tpl          : [
                        '<tpl for=".">',
                            '<div class="thumb-wrap" id="{MonitoringFilesID}" style="border:1px solid #CCC;width:239px;height:300px;">', 
                                '<div class="thumb">',
                                    '<img style="width:221px;height:180px;" src="api/images/photo_activity/{FileName}" title="{FileName:htmlEncode}">',
                                '</div>',
                                '<h5 style="px;line-height:1;margin-bottom:2px"><a style="line-height:1" href="#" class="hover-effect">{FileTitle:htmlEncode}</a></h5>',
                                '<ul style="font-size:11px;margin-top:5px; margin-bottom:3px" class="unstyled inline blog-info">',
                                  '<li style="padding-bottom:0px"><i class="icon-calendar"></i> {DateCreated} &nbsp;<i class="icon-edit"></i> {nama}</li>',                                  
                                '</ul>', 
                                '<p style="margin-top:-5px">Diskripsi:</p>', 
                                '<div style="margin-top:-8px">{shortName:htmlEncode}</div>',
                            '</div>',
                        '</tpl>',
                        '<div class="x-clear"></div>'
  // '<tpl for=".">',
  //   '<div class="x-grid-group dataview">',
  //     '<div class="x-grid-group-hd dataview-group-header"><div style="font-weight: bold;">{group} ({[values.list.length]} Image(s))</div></div>',
  //     '<div class="x-grid-group-body">',
  //       '<tpl for="list">',
  //         '<div class="thumb-wrap" id="{MonitoringFilesID}" style="border:1px solid #CCC;width:239px;height:300px;float: left;margin: 4px 0 4px 4px;padding: 5px">', 
  //           '<div class="thumb">',
  //               '<img style="width:221px;height:180px;" src="api/images/photo_activity/{FileName}" title="{FileName:htmlEncode}">',
  //           '</div>',          
  //           '<h5 style="px;line-height:1;margin-bottom:2px"><a style="line-height:1" href="#" class="hover-effect">{FileTitle:htmlEncode}</a></h5>',
  //           '<ul style="font-size:11px;margin-top:5px; margin-bottom:3px" class="unstyled inline blog-info">',
  //             '<li style="padding-bottom:0px"><i class="icon-calendar"></i> {DateCreated} &nbsp;<i class="icon-edit"></i> {nama}</li>',                                  
  //           '</ul>', 
  //           '<p style="margin-top:-5px">Diskripsi:</p>', 
  //           '<div style="margin-top:-8px">{shortName:htmlEncode}</div>',
  //         '</div>',
  //       '</tpl>',
  //       '<div class="x-clear"></div>',
  //     '</div>',
  //   '</div>',
  // '</tpl>',
  // '<div class="dataview-border"></div>'
            ],

            multiSelect  : true,
            minHeight    : 165,            
            trackOver    : true, 
                                        
            overItemCls  : 'x-item-over',
            itemSelector : 'div.thumb-wrap',
            emptyText    : lang('No photo to display'),
            
            prepareData: function(data) {
                Ext.apply(data, {
                    shortName: Ext.util.Format.ellipsis(data.ket, 56),
                    sizeString: Ext.util.Format.fileSize(data.size),
                    dateString: Ext.util.Format.date(data.lastmod, "m/d/Y g:i a")
                });
                return data;
            },

            listeners: {
                selectionchange: function(dv, nodes){
                    if (nodes.length!=1) Ext.getCmp('toolbar_dua').setVisible(false);
                    else {                                

                        Ext.getCmp('toolbar_dua').setVisible(true);
                        
                        Ext.getCmp('foto_id').setValue(nodes[0].data.MonitoringFilesID);
                        Ext.getCmp('foto_name').setValue(nodes[0].data.FileName);
                        Ext.getCmp('file_detail').setValue('Foto : "'+nodes[0].data.FileName+'" - klik dua kali untuk preview.');
                        
                        Ext.getCmp('remove').setVisible(true);
                        Ext.getCmp('cancel').setVisible(true);                               
                    }
                },

                itemdblclick : function(dv, record, item, index, e) {
                    if (!Ext.getCmp('foto_name').getValue()) {
                        Ext.MessageBox.alert('Warning', lang('Please select photo first !'));
                        return;
                    }                                  
                    
                    displayFormWindow();
                    Ext.getCmp('foto_view').setSrc(m_photo+Ext.getCmp('foto_name').getValue());
                   
                }
            }
      })
    });
    
    function displayFormWindow(){
        if(!win.isVisible()){
            DataForm.getForm().reset();
            Ext.getCmp('foto_view').setSrc('');
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }

    
    var DataForm = Ext.create('Ext.form.Panel', {
        frame       : false,
        autoScroll  : false,
        width       : 560,
        height      : 400,        
        bodyPadding : 5,
        id          : 'dataForm',        
        fieldDefaults   : {
            labelAlign  : 'left',
            labelWidth  : 100,
            anchor      : '100%'
        },
        items        : [
          {
              xtype  : 'image',
              id     : 'foto_view',
              height : '337px',
              width  : '550px',
              style  : 'padding:5px;border:1px solid #CCC;',
          }          
        ],
        buttons      : [         
          {
            text     : lang('Close'),
            margin   : '5px',
            scale    : 'large',
            ui       : 's-button',
            cls      : 's-grey',
            disabled : false,
            handler  : function() {
                 win.hide();
            }
          }
        ]
    });

    var win      = Ext.create('widget.window', {
        title       : 'Preview',
        id          : 'win',
        closable    :  true,
        modal       : true,
        closeAction : 'hide',
        width       : 580,
        height      : 450,
        layout      : {
            type    : 'border',
            padding : 5
        },
        items: [DataForm]
    });

});
