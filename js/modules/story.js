Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['StoryID','FarmerID','CPGid','FarmerName','Birthdate','GroupName','desa','TotalLahan','produksi','photo','File'],
    });
   var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            params: {
            'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    function submitOnEnter(field, event) {
    	if (event.getKey() == event.ENTER) {
           store.load({
           params: {
               key: Ext.getCmp('key').getValue()
           }});
    	}
    }    
            
    Ext.create('Ext.Panel', {
       store: store,
       width: '100%',
       id:'images-view',
       minHeight:500,
       renderTo: 'ext-content',
       frame: false,
       loadMask: true,
       style: 'border:1px solid #999999;',
       dockedItems: [{
              xtype: 'pagingtoolbar',
              store: store,   // same store GridPanel is using
              dock: 'bottom',
              displayInfo: true
       },{
            xtype: 'toolbar',
            items: [{
                 xtype :'button',
                 icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                 text: 'Add',
                 handler: function() {
                   displayFormWindow();
                 }
            },{
               name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
               id: 'key',
               xtype:'textfield',
               listeners: {
              	   specialkey: submitOnEnter
              	}
           },{
                 xtype :'button',
                 icon: varjs.config.base_url+'images/icons/silk/search.png',
                 margin: '0px 0px 0px -10px',
                 text: 'Search',
                 handler: function() {
                   store.load({
                     params: {
                         key: Ext.getCmp('key').getValue()
                   }});
                 }
            }]
         },{
            xtype: 'toolbar',
            id:'toolbar_dua',
            hidden:true,
            items: [{
              xtype: 'textfield', 
              hidden:true,
              id: 'story_id'
           },{
              xtype: 'textfield', 
              hidden:true,
              id: 'farmer_id'
           },{
              xtype: 'textfield', 
              hidden:true,
              id: 'farmer_name'
           },{
              xtype: 'textfield', 
              hidden:true,
              id: 'birthdate'
           },{
              xtype: 'textfield', 
              hidden:true,
              id: 'group_name'
           },{
              xtype: 'textfield', 
              hidden:true,
              id: 'desa_'
           },{
              xtype: 'textfield', 
              hidden:true,
              id: 'total_lahan'
           },{
              xtype: 'textfield', 
              hidden:true,
              id: 'produksi_'
           },{
               itemId: 'edit',
               icon: varjs.config.base_url+'images/icons/silk/update.png',
               text: 'Edit',
               cls:m_act_delete,
               scope: this,
               handler : function(){
                    displayFormWindow();
                    Ext.getCmp('StoryID').setValue(Ext.getCmp('story_id').getValue());
                    Ext.getCmp('FarmerID').setValue(Ext.getCmp('farmer_id').getValue());
                    Ext.getCmp('FarmerName').setValue(Ext.getCmp('farmer_name').getValue());
                    Ext.getCmp('Birthdate').setValue(Ext.getCmp('birthdate').getValue());
                    Ext.getCmp('GroupName').setValue(Ext.getCmp('group_name').getValue());
                    Ext.getCmp('desa').setValue(Ext.getCmp('desa_').getValue());
                    Ext.getCmp('TotalLahan').setValue(Ext.getCmp('total_lahan').getValue());
                    Ext.getCmp('produksi').setValue(Ext.getCmp('produksi_').getValue());
               }
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: 'Hapus',
               scope: this,
               handler : function(){
                 Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?' , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  Ext.getCmp('story_id').getValue()},
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
                           Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                        }
                     });
                     }
                 });
               }
            }]
         }],
         items: Ext.create('Ext.view.View', {
            store: store,
            tpl: [
                '<tpl for=".">',
                    '<div class="thumb-wrap" id="{FarmerID}" style="display:inline-block;width:50%;padding-right:10px;">',
                        '<table style="margin:10px;padding:10px;table-layout:fix;display:inline-block;border:1px dotted" width="100%"><tr><td rowspan="7" style="padding:5px 5px 5px 0px;vertical-align:top;width:130px">'+
                           '<img style="border: 1px double;" width="115" src="'+m_api+'images/Photo/{photo}"'+
                           ' title="{fileTitle:htmlEncode}"></td><td>{FarmerName} [{FarmerID}]</td></tr>'+
                           '<tr><td>{Birthdate}</td></tr>'+
                           '<tr><td>{GroupName}</td></tr>'+
                           '<tr><td>{desa}</td></td></tr>'+
                           '<tr><td>{TotalLahan} Ha</td></tr>'+
                           '<tr><td>{produksi}</td></tr>'+
                           '<tr><td><a href="'+m_api+'files/story/{File}" class="btn">Download PDF</a></td></tr></table>',
                     '</div>',
                '</tpl>'
            ],
            multiSelect: false,
            minHeight: 500,
            trackOver: true,
            overItemCls: 'x-item-over',
            itemSelector: 'div.thumb-wrap',
            emptyText: 'No images to display',
            plugins: [
                Ext.create('Ext.ux.DataView.DragSelector', {}),
                Ext.create('Ext.ux.DataView.LabelEditor', {dataIndex: 'name'})
            ],
            prepareData: function(data) {
                Ext.apply(data, {
                    shortName: Ext.util.Format.ellipsis(data.name, 15),
                    sizeString: Ext.util.Format.fileSize(data.size),
                    dateString: Ext.util.Format.date(data.lastmod, "m/d/Y g:i a")
                });
                return data;
            },
            listeners: {
                selectionchange: function(dv, nodes ){
                     if (nodes.length!=1) Ext.getCmp('toolbar_dua').setVisible(false);
                     else {
                        Ext.getCmp('toolbar_dua').setVisible(true);
                        Ext.getCmp('story_id').setValue(nodes[0].data.StoryID);
                        Ext.getCmp('farmer_id').setValue(nodes[0].data.FarmerID);
                        Ext.getCmp('farmer_name').setValue(nodes[0].data.FarmerName);
                        Ext.getCmp('birthdate').setValue(nodes[0].data.Birthdate);
                        Ext.getCmp('group_name').setValue(nodes[0].data.GroupName);
                        Ext.getCmp('desa_').setValue(nodes[0].data.desa);
                        Ext.getCmp('total_lahan').setValue(nodes[0].data.TotalLahan);
                        Ext.getCmp('produksi_').setValue(nodes[0].data.produksi);
                     }
                }
            }
        })
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

   var ds = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'_search',
            params: {
            'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 350,
        autoScroll: true,
        width: 500,
        bodyPadding: 5,
        id:'dataForm',
        fileUpload: true,
         enctype:'multipart/form-data',
         id:'upload',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
             xtype: 'textfield',
             id: 'StoryID',
             name: 'StoryID',
             hidden:true
         },{
             xtype: 'textfield',
             id: 'FarmerID',
             name: 'FarmerID',
             hidden:true
         },{
            xtype: 'combo',
            store: ds,
            id:"FarmerName",
            fieldLabel: 'Nama',
            typeAhead: false,
            hideTrigger:true,
            listConfig: {
                loadingText: 'Searching...',
                emptyText: 'No matching posts found.',

                // Custom rendering template for each item
                getInnerTpl: function() {
                    return '<div class="search-item">' +
                        '<h3><span>{GroupName} ({CPGid})</span>{FarmerName} ({FarmerID})</h3>' +
                        '{desa}' +
                    '</div>';
                }
            },
            pageSize: 10,

            // override default onSelect to do redirect
            listeners: {
                select: function(combo, selection) {
                    var post = selection[0];
                    Ext.getCmp('FarmerID').setValue(post.get('FarmerID'));
                    Ext.getCmp('FarmerName').setValue(post.get('FarmerName'));
                    Ext.getCmp('Birthdate').setValue(post.get('Birthdate'));
                    Ext.getCmp('GroupName').setValue(post.get('GroupName'));
                    Ext.getCmp('desa').setValue(post.get('desa'));
                    Ext.getCmp('TotalLahan').setValue(post.get('TotalLahan'));
                    Ext.getCmp('produksi').setValue(post.get('produksi'));
                }
            }
        },{
             xtype: 'textfield',
             fieldLabel: 'Tanggal Lahir',
             id: 'Birthdate',
             name: 'Birthdate',
             readOnly:true
         },{
             xtype: 'textfield',
             fieldLabel: 'Nama Group',
             id: 'GroupName',
             name: 'GroupName',
             readOnly:true
         },{
             xtype: 'textfield',
             fieldLabel: 'Wilayah',
             id: 'desa',
             name: 'desa',
             readOnly:true
         },{
             xtype: 'textfield',
             fieldLabel: 'Luas Kebun (Ha)',
             id: 'TotalLahan',
             name: 'TotalLahan',
             readOnly:true
         },{
             xtype: 'textfield',
             fieldLabel: 'Produksi (Kg)',
             id: 'produksi',
             name: 'produksi',
             readOnly:true
         },{            
            xtype: 'fileuploadfield',
            fieldLabel: 'file',
            id: 'file',
            name: 'file',
            buttonText: 'Browse'
        }],
        buttons: [{
            id:'saveButton',
            text: 'Save',
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               var form = Ext.getCmp('upload').getForm();
               form.submit({
                   url: m_crud,
                   waitMsg: 'Sending files....',
                   success: function(fp, o) {
                        store.load();
                       win.hide();
                   }
               });
            }
        },{
             text: 'Close',
               margin: '5px',
               scale: 'large',
             ui: 's-button',
             cls: 's-grey',
             disabled: false,
             handler: function() {
                 win.hide();
             }
        }]
    });
    var win = Ext.create('widget.window', {
        title: 'Add Success Story',
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'hide',
        width: 520,
        height: 400,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });

});
