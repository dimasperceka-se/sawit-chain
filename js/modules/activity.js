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
    // Store  ================================================== START 
    var store_activity = Ext.create('Ext.data.Store', {
        extend   : 'Ext.data.Model',
        fields   : ['MonitoringID','ObjectCategory','ObjectType','ObjectID','ObjectName','VillageID','Desa','ObjectIDName','Description','VisitDate','VisitTime'],
        autoLoad : true,
        pageSize : 10,
        proxy    : {
            type : 'ajax',
            url  : m_grid,
            reader   : {
                type : 'json',
                root : 'data',
                totalProperty: 'total'
            }
        }
    });
    ImageModel         = Ext.define('ImageModel', {
        extend : 'Ext.data.Model',        
        fields: [
           {name: 'MF_id'},
           {name: 'MonitoringFilesID'},
           {name: 'MonitoringID'},
           {name: 'MonID'},
           {name: 'FileTitle'},
           {name: 'FilePath'},
           {name: 'FileName'},
           {name: 'FileType'},
           {name: 'FileSize',    type: 'float'},           
           {name: 'DateCreated'},
           {name: 'nama'}
        ]
    });
    var store_foto     = Ext.create('Ext.data.Store', {
        model    : 'ImageModel',
        autoLoad : true,
        pageSize : 5,
        proxy: {
            type : 'ajax',
            url  : m_foto_grid,            
            reader: {
               type : 'json',
               root : 'data',
               totalProperty: 'total'
            }
        }
    });
    // Store  ================================================== END
    function displayFormWindow(){
        Ext.getCmp('ObjectCategory').setReadOnly(false);
        Ext.getCmp('ObjectType').setReadOnly(false);
        Ext.getCmp('NameID').setReadOnly(false);
        if(!win.isVisible()){
            Form_Activity.getForm().reset();
            win.show();                     
            Ext.getCmp('ObjectCategory').focus(true,true);
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    } 
    function validateFileExtension(fileName) {
       // var exp = /^.*.(jpg|JPG|png|PNG|txt|TXT)$/;
       var exp = /^.*.(jpg|JPG|png|PNG|bmp|BMP|gif|GIF|txt|TXT)$/;
       return (exp.test(fileName));
    } 
    // ======================================================= ||
    var mc_Provinsi    = Ext.create('Ext.data.Store', {
        extend   : 'Ext.data.Model',
        fields   : ['id','label'],
        autoLoad : true,
        pageSize : 10,
        proxy    : {
            type : 'ajax',
            url  : m_Provinsi,
            //extraParams : {prov: m_param},
            reader   : {
                type : 'json',
                root : 'data'
            }
        }
    });
    var mc_Kabupaten   = Ext.create('Ext.data.Store', {
        extend   : 'Ext.data.Model',
        fields   : ['id','label'],
        autoLoad : true,
        pageSize : 10,
        proxy: {
            type: 'ajax',
            url: m_Kabupaten,
            //extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Kecamatan = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      autoLoad: true,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_Kecamatan,
         reader: {
            type: 'json',
            root: 'data'
         }
      }
    });
    var mc_Desa = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['label','id'],
      autoLoad: true,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_Desa,
         reader: {
            type: 'json',
            root: 'data'
         }
      }
    });
    // ======================================================= ||
    Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type     : 'ajax',
            url      : m_objectid,           
            reader   : {
                type : 'json',
                root : 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'name', mapping: 'name'},
            {name: 'village', mapping: 'village'}
        ]
    });    
    var mc_object = Ext.create('Ext.data.Store', {
        pageSize        : 10,
        model           : 'Post'
    });    
    var mc_category = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data : [
            {"id":"Farmer",    "label":"Farmer"},
            {"id":"Garden",    "label":"Garden"},
            {"id":"CPG",       "label":"CPG"},
            {"id":"Nursery",   "label":"Nursery"},
            {"id":"Compost",   "label":"Compost"},
            {"id":"Demoplot",  "label":"Demoplot"},
            {"id":"Coop",      "label":"Coop"},
            {"id":"Warehouse", "label":"Warehouse"},
            {"id":"Trader",    "label":"Trader"},
            {"id":"SCE",       "label":"SCE"},
            {"id":"Village",       "label":"Village"},
        ]
    });    
    var mc_order = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {'label': 'All'}, 
            {'label': 'Farmer'}, 
            {'label': 'Garden'}, 
            {'label': 'CPG'}, 
            {'label': 'Nursery'}, 
            {'label': 'Compost'}, 
            {'label': 'Demoplot'}, 
            {'label': 'Coop'}, 
            {'label': 'Warehouse'}, 
            {'label': 'Trader'}, 
            {'label': 'SCE'},
            {'label': 'Village'},
        ],
        autoLoad: true
    });
    // ======================================================= ||
    var contextMenu = Ext.create('Ext.menu.Menu', {
        items: [
            // Update
            {
                icon    : varjs.config.base_url + 'images/icons/silk/pencil.png',
                text    : lang('Update'),
                cls     : m_act_update,
                scope   : this,
                handler : function () {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    if (!Ext.getCmp('grid').getSelectionModel().getSelection()[0]) {
                        Ext.MessageBox.show({
                            title   : 'Warning !',
                            msg     : lang('Please select data first !'),
                            buttons : Ext.MessageBox.OK, 
                            icon    : Ext.MessageBox.WARNING
                        });
                        return;
                    }                           
                    displayFormWindow();    
                    Ext.getCmp('foto').focus(true,true);                
                    Ext.Ajax.request({

                        url    : m_crud,
                        method : 'GET',
                        params : {
                            id: sm.get('MonitoringID')
                        },
                        success : function(fp, o){
                            var r = Ext.decode(fp.responseText);
                            Ext.getCmp('MonitoringID').setValue(sm.get('MonitoringID'));
                            
                            Ext.getCmp('ObjectCategory').setValue(r.ObjectCategory);
                            Ext.getCmp('ObjectCategory').setReadOnly(true);
                            Ext.getCmp('ObjectCategorys').setValue(r.ObjectCategory);
                            
                            Ext.getCmp('ObjectType').setValue(r.ObjectType);
                            Ext.getCmp('ObjectType').setReadOnly(true);
                            Ext.getCmp('ObjectTypes').setValue(r.ObjectType);

                            var Object_cat  = Ext.getCmp('ObjectCategorys').getValue();
                            var Object_type = Ext.getCmp('ObjectTypes').getValue();
                            
                            //console.log(Object_cat==='nursery');
                            switch (Object_cat) {
                                
                                case 'Farmer':
                                    Ext.getCmp('ObjectType').hide();                           
                                    Ext.getCmp('Type1').setValue(true);
                                    break;                                     
                                case 'Garden':
                                    Ext.getCmp('ObjectType').hide();                           
                                    Ext.getCmp('Type10').setValue(true);
                                    break; 
                                case 'CPG':
                                    Ext.getCmp('ObjectType').hide();
                                    Ext.getCmp('Type4').setValue(true);
                                    break;
                               case 'Nursery':
                                    Ext.getCmp('ObjectType').show();
                                    if (Object_type == 'Farmer'){
                                        Ext.getCmp('Type1').setValue(true);
                                        Ext.getCmp('Type4').setValue(false);
                                        break; 
                                    }else if (Object_type == 'CPG'){
                                        Ext.getCmp('Type4').setValue(true);
                                        break; 
                                    }else if (Object_type == 'Coop'){
                                        Ext.getCmp('Type7').setValue(true);
                                        break; 
                                    }
                                    break;
                                case 'Compost':                                
                                    Ext.getCmp('ObjectType').show();
                                    if (Object_type == 'Farmer'){
                                        Ext.getCmp('Type1').setValue(true);
                                        break; 
                                    }else if (Object_type == 'CPG'){
                                        Ext.getCmp('Type4').setValue(true);
                                        break; 
                                    }else if (Object_type == 'Coop'){
                                        Ext.getCmp('Type7').setValue(true);
                                        break; 
                                    }                                
                                    break;
                                case 'Demoplot':    
                                    Ext.getCmp('ObjectType').hide();                           
                                    Ext.getCmp('Type5').setValue(true);                                
                                    break;
                                case 'Coop':    
                                    Ext.getCmp('ObjectType').hide();                           
                                    Ext.getCmp('Type7').setValue(true);                                
                                    break;       
                                case 'Warehouse':    
                                    Ext.getCmp('ObjectType').hide();                           
                                    Ext.getCmp('Type6').setValue(true);                                
                                    break;
                                case 'Trader':    
                                    Ext.getCmp('ObjectType').hide();                           
                                    Ext.getCmp('Type8').setValue(true);                                
                                    break;       
                                case 'SCE':    
                                    Ext.getCmp('ObjectType').hide();                           
                                    Ext.getCmp('Type9').setValue(true);                                
                                    break;       
                                case 'Village':
                                    Ext.getCmp('ObjectType').hide();                           
                                    Ext.getCmp('Type11').setValue(true);
                                    break; 
                                default:                                                                
                                    Ext.getCmp('ObjectType').hide();
                                    Ext.getCmp('Type1').hide(); 
                                    Ext.getCmp('Type2').hide(); Ext.getCmp('Type2').setValue(false);
                                    Ext.getCmp('Type3').hide(); Ext.getCmp('Type3').setValue(false);
                                    Ext.getCmp('Type4').hide(); Ext.getCmp('Type4').setValue(false);
                                    Ext.getCmp('Type5').hide(); Ext.getCmp('Type5').setValue(false);
                                    Ext.getCmp('Type6').hide(); Ext.getCmp('Type6').setValue(false);
                                    Ext.getCmp('Type7').hide(); Ext.getCmp('Type7').setValue(false);
                                    Ext.getCmp('Type8').hide(); Ext.getCmp('Type8').setValue(false);
                                    Ext.getCmp('Type9').hide(); Ext.getCmp('Type9').setValue(false);
                                    Ext.getCmp('Type10').hide(); Ext.getCmp('Type10').setValue(false);
                                    Ext.getCmp('Type11').hide(); Ext.getCmp('Type11').setValue(false);
                                    break;                           
                            }
                            
                            Ext.getCmp('NameID').setValue(''+r.ObjectID+' - '+r.ObjectName); //(r.ObjectID);
                            Ext.getCmp('NameID').setReadOnly(true);
                            
                            Ext.getCmp('ObjectID').setValue(r.ObjectID);
                            Ext.getCmp('ObjectName').setValue(r.ObjectName);
                            Ext.getCmp('VillageID').setValue(r.VillageID);

                            Ext.getCmp('VisitDate').setValue(r.VisitDate);
                            Ext.getCmp('VisitTime').setValue(r.VisitTime);
                            
                            Ext.getCmp('Description').setValue(r.Description);
                            
                            Ext.getCmp('images-view').enable()

                            Ext.getCmp('add').setVisible(true);
                            Ext.getCmp('foto').enable();
                            Ext.getCmp('title').enable();

                            Ext.getCmp('remove').setVisible(false);
                            Ext.getCmp('cancel').setVisible(false);                                    

                            Ext.getCmp('title').setValue("")
                            Ext.getCmp('foto').setValue("")

                            Ext.getCmp('foto_id').setValue("")
                            Ext.getCmp('foto_name').setValue("")


                            Ext.getCmp('save').setVisible(false);
                            Ext.getCmp('update').setVisible(true);

                            /* ok */
                            var proxy = store_foto.getProxy();
                                proxy.extraParams = {
                                     mid: Ext.getCmp('MonitoringID').getValue()// sm.get('MonitoringId')
                                };
                            store_foto.load();                                              
                        }
                        
                    });
                }                       
            },
            // Cancel 
            {
                itemId  : 'remove',
                icon    : varjs.config.base_url+'images/icons/silk/delete.png',
                cls     : m_act_delete,
                text    : lang('Delete'),
                scope   : this,
                handler : function(){
                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];  
                    if (! Ext.getCmp('grid').getSelectionModel().getSelection()[0]) {                                
                        Ext.MessageBox.show({
                            title   : 'Warning !', 
                            msg     : lang('Please select data first !'), 
                            buttons : Ext.MessageBox.OK, 
                            icon    : Ext.MessageBox.WARNING
                        });
                        return;
                    }                           
                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), 
                        function(btn){
                            if(btn == 'yes'){
                                Ext.Ajax.request({                                            
                                    url        :  m_crud,
                                    method     : 'DELETE',
                                    params     : {
                                        MonitoringID:smb.raw.MonitoringID
                                    },
                                    waitMsg  : lang('Deleting data ...'),
                                    success    : function(response, opts){
                                       //Ext.MessageBox.alert('Success', lang('Data delete successfully.'));
                                        var obj = Ext.decode(response.responseText);
                                        switch(obj.success){
                                            case true: 
                                                 store_activity.load({
                                                     params: {                                   
                                                         prov : Ext.getCmp('Provinsi').getValue(),
                                                         kab  : Ext.getCmp('Kabupaten').getValue(),
                                                         kec  : Ext.getCmp('Kecamatan').getValue(),
                                                         des  : Ext.getCmp('Desa').getValue(),
                                                         category_name: Ext.getCmp('Order').getValue()
                                                     }
                                                }); 
                                                Ext.getCmp('add').setVisible(true);     
                                                Ext.getCmp('remove').setVisible(false);
                                                Ext.getCmp('cancel').setVisible(false);
                                               break;
                                            default:
                                                Ext.MessageBox.show({
                                                    title   : 'Warning !', 
                                                    msg     : lang('Failed to delete record, because activity have photo !'), 
                                                    buttons : Ext.MessageBox.OK, 
                                                    icon    : Ext.MessageBox.WARNING
                                                }); 
                                                Ext.getCmp('add').setVisible(true);                                                            
                                                Ext.getCmp('remove').setVisible(false);
                                                Ext.getCmp('cancel').setVisible(false);
                                                break;
                                       }
                                    },
                                    failure: function(response, opts){
                                       var obj = Ext.decode(response.responseText);
                                       Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        }
                    );
                }
            }
        ]
    });
    // ======================================================= ||
    var Form_Activity = Ext.widget('form',{
        frame            : false,        
        autoScroll       : true,                
        bodyPadding      : 5,        
        fileUpload       : true,
        enctype          : 'multipart/form-data',
        id               : 'form_activity',
        fieldDefaults    : {labelAlign:'left',labelWidth:90},
        items            : [
            {
                layout   :'column',
                border   : false,
                items    : [
                    {
                        columnWidth  : .48,  
                        layout       : 'form',
                        border       : false,
                        padding      : 5,
                        items        : [
                            {
                                xtype  : 'container',
                                flex   : 1,
                                layout : 'anchor',
                                items  : [
                                    {
                                        xtype        : 'textfield',
                                        //fieldLabel   : lang('Activity ID'), 
                                        id           : 'MonitoringID',
                                        name         : 'MonitoringID',
                                        disabled     : true,
                                        anchor       : '90%',
                                        inputType    : 'hidden'
                                    },                                    
                                    {
                                        id           : 'ObjectCategory',
                                        name         : 'ObjectCategory',
                                        xtype        : 'combobox', 
                                        fieldLabel   : lang('Category Name'),                 
                                        store        : mc_category,
                                        valueField   : 'id', displayField: 'label', 
                                        emptyText    : lang('Choose Category Name'),
                                        queryMode    : 'local', 
                                        typeAhead    :  true, forceSelection: true,
                                        allowBlank   : false,                                        
                                        anchor       : '100%',
                                        listeners    : {
                                            
                                            'change': function(fb, v){ 
                                                var Object_Category = Ext.getCmp('ObjectCategory').getValue();                                
                                                //console.log(Object_Category==='nursery');
                                                switch (Object_Category) { 
                                                    case 'Farmer':
                                                        Ext.getCmp('ObjectType').hide();                           
                                                        Ext.getCmp('Type1').setValue(true);
                                                        break; 
                                                    case 'Garden':
                                                        Ext.getCmp('ObjectType').hide();                           
                                                        Ext.getCmp('Type10').setValue(true);
                                                        break; 
                                                    case 'CPG':
                                                        Ext.getCmp('ObjectType').hide();
                                                        Ext.getCmp('Type4').setValue(true);
                                                        break;
                                                    case 'Nursery':
                                                        Ext.getCmp('ObjectType').show();
                                                        Ext.getCmp('Type1').show(); //Ext.getCmp('Type1').setValue(true);
                                                        Ext.getCmp('Type2').hide();
                                                        Ext.getCmp('Type3').hide();
                                                        Ext.getCmp('Type4').show(); //Ext.getCmp('Type4').setValue(false);
                                                        Ext.getCmp('Type5').hide();
                                                        Ext.getCmp('Type6').hide();
                                                        Ext.getCmp('Type7').show(); //Ext.getCmp('Type7').setValue(false);
                                                        Ext.getCmp('Type8').hide();
                                                        Ext.getCmp('Type9').hide();
                                                        break;
                                                    case 'Compost':                                
                                                        Ext.getCmp('ObjectType').show();
                                                        Ext.getCmp('Type1').show(); Ext.getCmp('Type1').setValue(true);
                                                        Ext.getCmp('Type2').hide();
                                                        Ext.getCmp('Type3').hide();
                                                        Ext.getCmp('Type4').show(); Ext.getCmp('Type4').setValue(false);
                                                        Ext.getCmp('Type5').hide();
                                                        Ext.getCmp('Type6').hide();
                                                        Ext.getCmp('Type7').show(); Ext.getCmp('Type7').setValue(false);
                                                        Ext.getCmp('Type8').hide();
                                                        Ext.getCmp('Type9').hide();
                                                        break;
                                                    case 'Demoplot':    
                                                        Ext.getCmp('ObjectType').hide();                           
                                                        Ext.getCmp('Type5').setValue(true);                                
                                                        break;
                                                    case 'Coop':    
                                                        Ext.getCmp('ObjectType').hide();                           
                                                        Ext.getCmp('Type7').setValue(true);                                
                                                        break;       
                                                    case 'Warehouse':    
                                                        Ext.getCmp('ObjectType').hide();                           
                                                        Ext.getCmp('Type6').setValue(true);                                
                                                        break;
                                                    case 'Trader':    
                                                        Ext.getCmp('ObjectType').hide();                           
                                                        Ext.getCmp('Type8').setValue(true);                                
                                                        break;       
                                                    case 'SCE':    
                                                        Ext.getCmp('ObjectType').hide();                           
                                                        Ext.getCmp('Type9').setValue(true);                                
                                                        break;       
                                                    case 'Village':
                                                        Ext.getCmp('ObjectType').hide();                           
                                                        Ext.getCmp('Type11').setValue(true);
                                                        break; 
                                                    default:                                                                
                                                        Ext.getCmp('ObjectType').hide();
                                                        Ext.getCmp('Type1').hide(); 
                                                        Ext.getCmp('Type2').hide(); Ext.getCmp('Type2').setValue(false);
                                                        Ext.getCmp('Type3').hide(); Ext.getCmp('Type3').setValue(false);
                                                        Ext.getCmp('Type4').hide(); Ext.getCmp('Type4').setValue(false);
                                                        Ext.getCmp('Type5').hide(); Ext.getCmp('Type5').setValue(false);
                                                        Ext.getCmp('Type6').hide(); Ext.getCmp('Type6').setValue(false);
                                                        Ext.getCmp('Type7').hide(); Ext.getCmp('Type7').setValue(false);
                                                        Ext.getCmp('Type8').hide(); Ext.getCmp('Type8').setValue(false);
                                                        Ext.getCmp('Type9').hide(); Ext.getCmp('Type9').setValue(false);
                                                        break;
                                                }                       
                                            },
                                            select: function(combo, selection) {                       
                                                var proxy = mc_object.getProxy();
                                                proxy.extraParams = {
                                                    categoryID:Ext.getCmp('ObjectCategory').getValue(),
                                                    typeID:Ext.getCmp('ObjectType').getValue()
                                                };
                                                Ext.getCmp('NameID').setValue("")
                                                Ext.getCmp('ObjectID').setValue("")
                                                Ext.getCmp('ObjectName').setValue("")
                                                Ext.getCmp('VillageID').setValue("")
                                            }
                                        }
                                    },
                                    {
                                        xtype        : 'textfield',
                                        id           : 'ObjectCategorys',
                                        name         : 'ObjectCategorys',
                                        inputType    : 'hidden'
                                    },
                                    // Object Type
                                    {
                                        xtype        : 'radiogroup',
                                        id           : 'ObjectType',
                                        name         : 'ObjectType',                                        
                                        fieldLabel   : lang('Type Name'),                
                                        columns      : 3,
                                        vertical     : true,
                                        hidden       : true,
                                        items        : [
                                            {
                                                xtype      : 'radiofield',
                                                id         : 'Type1',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Farmer',
                                                inputValue : 'Farmer',
                                                hidden     :  true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type2',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Compost',
                                                inputValue : 'Compost',                        
                                                hidden     :  true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type3',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Nursery',
                                                inputValue : 'Nursery',
                                                hidden     : true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type4',
                                                name       : 'ObjectType',
                                                boxLabel   : 'CPG',
                                                inputValue : 'CPG',
                                                hidden     : true,
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type5',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Demoplot',
                                                inputValue : 'Demoplot',
                                                hidden     : true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type6',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Warehouse',
                                                inputValue : 'Warehouse',
                                                hidden     : true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type7',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Coop',
                                                inputValue : 'Coop',
                                                hidden     : true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type8',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Trader',
                                                inputValue : 'Trader',
                                                hidden     : true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type9',
                                                name       : 'ObjectType',
                                                boxLabel   : 'SCE',
                                                inputValue : 'SCE',
                                                hidden     : true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type10',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Garden',
                                                inputValue : 'Garden',
                                                hidden     : true
                                            },{
                                                xtype      : 'radiofield',
                                                id         : 'Type11',
                                                name       : 'ObjectType',
                                                boxLabel   : 'Village',
                                                inputValue : 'Village',
                                                hidden     : true
                                            }
                                        ],
                                        listeners    : {
                                            change: function(field, newValue, oldValue) {
                                                var value = newValue.show;
                                                if (Ext.isArray(value)) {
                                                    return;
                                                }
                                                var proxy = mc_object.getProxy();
                                                proxy.extraParams = {
                                                    categoryID : Ext.getCmp('ObjectCategory').getValue(),
                                                    typeID     : Ext.getCmp('ObjectType').getValue()
                                                };
                                                
                                                Ext.getCmp('ObjectID').setValue("")
                                            }
                                        }              
                                    },
                                    {
                                        xtype        : 'textfield',
                                        id           : 'ObjectTypes',
                                        name         : 'ObjectTypes',
                                        inputType    : 'hidden'
                                    }, 
                                    // Name ID           
                                    {
                                        xtype        : 'combo',
                                        store        :  mc_object,
                                        id           : 'NameID',
                                        name         : 'NameID',
                                        displayField : 'id',                                       
                                        fieldLabel   : lang('ID / Name'),
                                        emptyText    : lang('Insert ID / Name'),
                                        typeAhead    : false,
                                        hideTrigger  : true,
                                        minChars     : 1,
                                        anchor       : '100%',
                                        listConfig   : {
                                            loadingText : lang('Searching...'),
                                            emptyText   : lang('No matching ID / Name found.'),
                                            getInnerTpl : function() {
                                                return  '<div class="search-item">' +
                                                            '{id} - {name}' +
                                                            '{excerpt}' +
                                                        '</div>';
                                            }
                                        },
                                        pageSize     : 10,
                                        allowBlank   : false,
                                        listeners    : {
                                            select: function(combo, selection) {
                                                var post = selection[0];
                                                if (post) {              
                                                    Ext.getCmp('NameID').setValue(''+post.get('id')+' - '+post.get('name'));
                                                    Ext.getCmp('ObjectID').setValue(post.get('id'));
                                                    Ext.getCmp('ObjectName').setValue(post.get('name'));
                                                    Ext.getCmp('VillageID').setValue(post.get('village'));
                                                }                       
                                            }
                                        }
                                    },                                    
                                    {
                                        xtype        : 'textfield',                                        
                                        id           : 'ObjectID',
                                        name         : 'ObjectID',
                                        //fieldLabel   : lang('ObjectID'),
                                        inputType    : 'hidden',  
                                        anchor       : '90%'                                      
                                    },
                                    {
                                        xtype        : 'textfield',                                        
                                        id           : 'ObjectName',
                                        //fieldLabel   : lang('ObjectName'),
                                        name         : 'ObjectName',
                                        inputType    : 'hidden',                                        
                                        anchor       : '90%'
                                    },
                                    {
                                        xtype        : 'textfield',                                        
                                        id           : 'VillageID',
                                        //fieldLabel   : lang('ObjectName'),
                                        name         : 'VillageID',
                                        inputType    : 'hidden',                                        
                                        anchor       : '90%'
                                    }
                                ]
                            }  
                        ]
                    },
                    {
                        columnWidth  : .51,
                        layout       : 'form',
                        border       : false,                      
                        padding      : 5,
                        items        : [
                            {
                                xtype  : 'container',
                                flex   : 1,
                                layout : 'anchor',
                                items  : [
                                    {
                                        xtype                : 'fieldcontainer',
                                        fieldLabel           : lang('Visit'),
                                        layout               : 'hbox',
                                        combineErrors        : true,
                                        defaultType          : 'textfield',
                                        defaults             : {
                                            hideLabel        : 'true'
                                        },
                                        items: [
                                            {
                                                xtype        : 'datefield',                            
                                                id           : 'VisitDate',
                                                name         : 'VisitDate',
                                                format       : 'Y-m-d',
                                                altFormats   : 'Y-m-d',
                                                submitFormat : 'Y-m-d',
                                                emptyText    :  lang('Visit Date'),
                                                allowBlank   : false,
                                                anchor       : '90%'  
                                            }, {
                                                xtype        : 'timefield',                                                
                                                id           : 'VisitTime',
                                                name         : 'VisitTime',
                                                minValue     : '0:00 AM',
                                                maxValue     : '23:30',
                                                margins      : '0 0 0 6',
                                                emptyText    :  lang('Visit Time'),
                                                anchor       : '25%',
                                                increment    : 30,
                                                format       : 'H:i:s',
                                                allowBlank   : false                              
                                            }
                                        ]
                                    },            
                                    { 
                                        xtype        : 'textareafield',
                                        fieldLabel   : lang('Description'),
                                        id           : 'Description',
                                        name         : 'Description',
                                        allowBlank   : false,
                                        anchor       : '100%',
                                    }     
                                ]
                            } 
                        ]
                    }                             
                ]      
            },
            {
                xtype       : 'tabpanel',
                id          : 'images-view',
                flex        : 1,
                margin      : 2,
                activeTab   : 0,
                disabled    : true,                
                border      : true,                
                height      : 310,
                plain       : true,
                dockedItems : [
                    {
                        xtype        : 'pagingtoolbar',                        
                        store        : store_foto,
                        dock         : 'bottom',
                        displayInfo  : true
                    },
                    {
                        xtype        : 'toolbar',
                        id           : 'toolbar_satu',
                        items        : [
                            
                            {
                                xtype         : 'fileuploadfield',
                                fieldLabel    : lang('Photo'),
                                labelWidth    : 40,
                                id            : 'foto',
                                name          : 'foto',
                                emptyText     : lang('Select photo'),
                                width         : 330,     
                                disabled      : true,                                
                                //allowBlank    : false,                                   
                                buttonText    : 'Browse'                                                            
                            },                           
                            {
                                xtype         : 'textfield',
                                fieldLabel    : lang('Photo Title'),
                                labelWidth    : 80,
                                id            : 'title',
                                name          : 'title',
                                disabled      : true,
                                //allowBlank    : false,
                                width         : 340                                
                            },'->',

                            {
                                itemId  : 'add',
                                hidden  : true,
                                id      : 'add',
                                icon    : varjs.config.base_url+'images/icons/silk/add.png',
                                cls     : m_act_delete,
                                text    : lang('Save Photo'),
                                scope   : this,
                                handler    : function() {
                                    var form = Ext.getCmp('form_activity').getForm();                                                          
                                    form.submit({
                                        url     : m_foto,
                                        method  : 'POST',
                                        params   : {
                                            id   : Ext.getCmp('MonitoringID').getValue(),                                           
                                           title : Ext.getCmp('title').getValue()
                                        },
                                        waitMsg : lang('Uploading photo ...'),
                                        success: function (form, action) {
                                            //Ext.Msg.alert('Successful upload!', 'Image update info: ' + action.result.updateInfo);
                                            var proxy = store_foto.getProxy();
                                                proxy.extraParams = {
                                                     mid: Ext.getCmp('MonitoringID').getValue()
                                                };
                                            store_foto.load(); 
                                            Ext.getCmp('title').setValue("")
                                        },                                       
                                        failure: function (form, action) {
                                            Ext.MessageBox.show({
                                                title   : 'Save Failed !', 
                                                msg     : action.result.error, 
                                                buttons : Ext.MessageBox.OK, 
                                                icon    : Ext.MessageBox.WARNING
                                            }); 
                                        }                                      
                                    });                                       
                                }               
                            },                            
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
                                        
                                        Ext.getCmp('add').setVisible(true);    
                                        Ext.getCmp('remove').setVisible(false);
                                        Ext.getCmp('cancel').setVisible(false);
                                        
                                        Ext.getCmp('title').setValue("")
                                        Ext.getCmp('foto_id').setValue("")
                                        Ext.getCmp('foto_name').setValue("")

                                    } else {
                                         Ext.MessageBox.confirm('Message', lang('Do you want to delete this photo ?'), function(btn){
                                        if(btn == 'yes'){
                                            Ext.Ajax.request({                                            
                                                url      : m_foto,
                                                method   : 'DELETE',
                                                params   : {
                                                    id   : Ext.getCmp('foto_id').getValue(),                                                    
                                                    name : Ext.getCmp('foto_name').getValue()
                                                },
                                                waitMsg  : lang('Deleting photo ...'),
                                                success: function(response, opts){
                                                    var obj    = Ext.decode(response.responseText);                                                    
                                                    var proxy  = store_foto.getProxy();
                                                    proxy.extraParams = {
                                                        mid: Ext.getCmp('MonitoringID').getValue()// sm.get('MonitoringId')
                                                    };
                                                    store_foto.load(); 
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
                            {
                                itemId  : 'cancel',
                                hidden  : true,
                                id      : 'cancel',
                                icon    : varjs.config.base_url+'images/icons/silk/building_go.png',
                                cls     : m_act_cancle,
                                text    : lang('Cancel'),
                                scope   : this,
                                handler : function(){
                                    var proxy = store_foto.getProxy();
                                        proxy.extraParams = {
                                             mid: Ext.getCmp('MonitoringID').getValue()// sm.get('MonitoringId')
                                        };
                                    store_foto.load();

                                    Ext.getCmp('add').setVisible(true);
                                    Ext.getCmp('foto').enable();
                                    Ext.getCmp('title').enable();

                                    Ext.getCmp('remove').setVisible(false);
                                    Ext.getCmp('cancel').setVisible(false);                                    

                                    Ext.getCmp('title').setValue("")
                                    Ext.getCmp('foto').setValue("")

                                    Ext.getCmp('foto_id').setValue("")
                                    Ext.getCmp('foto_name').setValue("")
                                }
                            },
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
                            {
                                xtype   : 'textfield', 
                                hidden  : true,
                                id      : 'foto_titles'
                            },
                            {
                                xtype   : 'textfield', 
                                hidden  : true,
                                id      : 'foto_dates'
                            }    
                        ]
                    },
                    {
                        xtype        : 'toolbar',
                        id           : 'toolbar_dua',
                        dock         : 'bottom',
                        hidden       : false,
                        items        : [
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
                items       : Ext.create('Ext.view.View', {
                    title   : lang('Photo Activity'),
                    style   : 'padding:5px;border-top:1px solid #CCC;',
                    store   : store_foto,                    
                    
                    tpl     : [
                        '<tpl for=".">',
                            '<div class="thumb-wrap" id="{MonitoringFilesID}" >', 
                                '<div class="thumb">',
                                    '<img src="api/images/photo_activity/{FileName}" title="{FileName:htmlEncode}">',
                                '</div>',                                
                            '</div>',
                        '</tpl>',
                        '<div class="x-clear"></div>'
                    ],
                   
                    multiSelect  : true,
                    minHeight    : 165,
                    trackOver    : true, 
                                        
                    overItemCls  : 'x-item-over',
                    itemSelector : 'div.thumb-wrap',
                    emptyText    : lang('No photo to display'),
                    
                    listeners: {
                        
                        selectionchange: function(dv, nodes){
                            if (nodes.length!=1) Ext.getCmp('toolbar_dua').setVisible(false);
                            else {                                

                                Ext.getCmp('toolbar_dua').setVisible(true);
                                
                                Ext.getCmp('foto_id').setValue(nodes[0].data.MF_id);
                                Ext.getCmp('foto_name').setValue(nodes[0].data.FileName);
                                Ext.getCmp('foto_titles').setValue(nodes[0].data.FileTitle);
                                Ext.getCmp('foto_dates').setValue('Date : '+nodes[0].data.DateCreated+', by : '+nodes[0].data.nama);

                                Ext.getCmp('file_detail').setValue('Foto : "'+nodes[0].data.FileName+'" - klik dua kali untuk preview.');
                                
                                Ext.getCmp('add').setVisible(false); 
                                Ext.getCmp('foto').disable();
                                Ext.getCmp('title').disable();
                               
                                Ext.getCmp('remove').setVisible(true);
                                Ext.getCmp('cancel').setVisible(true);                               
                            }
                        },

                        itemdblclick : function(dv, record, item, index, e) {
                            if (!Ext.getCmp('foto_name').getValue()) {
                                Ext.MessageBox.alert('Warning', lang('Please select photo first !'));
                                return;
                            }
                            displayFormShow();
                            Ext.getCmp('foto_view').setSrc(m_photo+Ext.getCmp('foto_name').getValue());
                            Ext.getCmp('foto_title').setValue(Ext.getCmp('foto_titles').getValue());
                            Ext.getCmp('foto_date').setValue(Ext.getCmp('foto_dates').getValue());
                           
                            //preview_video('api/images/photo_activity/' + Ext.getCmp('foto_name').getValue());
                        }
                    }
                })                
            }                      
        ],
        buttons          : [
            {
                id         : 'save',
                text       : lang('Save'),
                hidden     : true,
                margin     : '5px',
                scale      : 'large',
                ui         : 's-button',
                cls        : 's-blue',
                handler    : function() {
                    var form = this.up('form').getForm();                                                            
                    form.submit({                        
                        url      : m_crud,
                        method   : 'POST',
                        params   : {
                            id   : Ext.getCmp('MonitoringID').getValue() 
                        },
                        waitMsg : lang('Saving data ...'),
                        success : function(fp, o) {                            
                            Ext.MessageBox.show({
                                title   : 'Informasi !', 
                                msg     : lang('Data saved successfully.'), 
                                buttons : Ext.MessageBox.OK, 
                                icon    : Ext.MessageBox.INFO
                            });                            
                            Ext.getCmp('images-view').enable()
                            Ext.getCmp('save').setVisible(false);
                            Ext.getCmp('update').setVisible(true);
                            Ext.getCmp('MonitoringID').setValue(o.result.newMon); 
                        },
                        failure : function(response, opts) {                            
                            //Ext.Msg.alert('Failed', lang('Cannot save data.'));
                            Ext.MessageBox.show({
                                title   : 'Informasi !', 
                                msg     : lang('Cannot save data !'), 
                                buttons : Ext.MessageBox.OK, 
                                icon    : Ext.MessageBox.WARNING
                            });
                        }
                    });                                        
                }               
            },
            {
                id         : 'update',
                text       : lang('Update'),
                hidden     : true,
                margin     : '5px',
                scale      : 'large',
                ui         : 's-button',
                cls        : 's-blue',
                handler    : function() {
                    var form = this.up('form').getForm();                                                           
                    form.submit({                        
                        url      : m_crud,
                        method   : 'POST',
                        params   : {
                            id   : Ext.getCmp('MonitoringID').getValue()
                        },
                        waitMsg : lang('Updating data ...'),
                        success : function(fp, o) {
                            Ext.MessageBox.show({
                                title   : 'Informasi !', 
                                msg     : lang('Data update successfully.'), 
                                buttons : Ext.MessageBox.OK, 
                                icon    : Ext.MessageBox.INFO
                            });
                            Ext.getCmp('images-view').enable()
                            Ext.getCmp('save').setVisible(false);
                            Ext.getCmp('update').setVisible(true);
                        },
                        failure : function(response, opts) {                            
                            Ext.MessageBox.show({
                                title   : 'Informasi !', 
                                msg     : lang('Cannot update data !'), 
                                buttons : Ext.MessageBox.OK, 
                                icon    : Ext.MessageBox.WARNING
                            });
                        }
                    });
                    win.hide(this, function() {
                         store_activity.load({
                             params: {                                   
                                 prov : Ext.getCmp('Provinsi').getValue(),
                                 kab  : Ext.getCmp('Kabupaten').getValue(),
                                 kec  : Ext.getCmp('Kecamatan').getValue(),
                                 des  : Ext.getCmp('Desa').getValue(),
                                 category_name: Ext.getCmp('Order').getValue()
                             }
                        }); 
                    });                                        
                }               
            },
            {
                id         : 'close',
                text       : lang('Close'),                
                margin     : '5px',
                scale      : 'large',
                ui         : 's-button',
                cls        : 's-grey',                
                handler    : function() {
                    win.hide(this, function() {
                        //store_activity.load(); 
                        store_activity.load({
                             params: {                                   
                                 prov : Ext.getCmp('Provinsi').getValue(),
                                 kab  : Ext.getCmp('Kabupaten').getValue(),
                                 kec  : Ext.getCmp('Kecamatan').getValue(),
                                 des  : Ext.getCmp('Desa').getValue(),
                                 category_name: Ext.getCmp('Order').getValue()
                             }
                        });                       
                    });
                }
            }
        ]
    });    
    var win  = Ext.create('widget.window', {
        title            : lang('Data Activity'),
        id               : 'win',
        closable         : true,
        modal            : true,
        closeAction      : 'hide',        
        frame            : false,
        width            : 900,
        height           : 570,
        layout           : {type:'fit',padding:5},
        items            : [Form_Activity]
    });
    var grid = Ext.create('Ext.grid.Panel', {
        store            : store_activity,
        width            : '100%',
        id               : 'grid',
        minHeight        :  450,
        style            : 'border:1px solid #CCC;',
        renderTo         : 'ext-content',
        loadMask         : true,
        selType          : 'rowmodel',
        listeners        : {
            // Klik satu kali
            itemclick: function(view, record, item, index, e){
                contextMenu.showAt(e.getXY());
            },
            // Klik dua kali
            itemdblclick : function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                Ext.Ajax.request({
                    url     : m_crud,
                    method  : 'GET',
                    params  : {
                        id: sm.get('MonitoringID')
                    },
                    success : function(fp, o){
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('MonitoringID').setValue(sm.get('MonitoringID'));
                        
                        Ext.getCmp('ObjectCategory').setValue(r.ObjectCategory);
                        Ext.getCmp('ObjectCategorys').setValue(r.ObjectCategory);
                        
                        Ext.getCmp('ObjectType').setValue(r.ObjectType);
                        Ext.getCmp('ObjectTypes').setValue(r.ObjectCategory);

                        var Object_cat  = Ext.getCmp('ObjectCategorys').getValue();
                        var Object_type = Ext.getCmp('ObjectTypes').getValue();
                        
                        //console.log(Object_cat==='nursery');
                        switch (Object_cat) {
                            
                            case 'Farmer':
                                Ext.getCmp('ObjectType').hide();                           
                                Ext.getCmp('Type1').setValue(true);
                                break; 
                            case 'CPG':
                                Ext.getCmp('ObjectType').hide();
                                Ext.getCmp('Type4').setValue(true);
                                break;
                           case 'Nursery':
                                Ext.getCmp('ObjectType').show();
                                if (Object_type == 'Farmer'){
                                    Ext.getCmp('Type1').setValue(true);
                                    Ext.getCmp('Type4').setValue(false);
                                    break; 
                                }else if (Object_type == 'CPG'){
                                    Ext.getCmp('Type4').setValue(true);
                                    break; 
                                }else if (Object_type == 'Coop'){
                                    Ext.getCmp('Type7').setValue(true);
                                    break; 
                                }
                                break;
                            case 'Compost':                                
                                Ext.getCmp('ObjectType').show();
                                if (Object_type == 'Farmer'){
                                    Ext.getCmp('Type1').setValue(true);
                                    break; 
                                }else if (Object_type == 'CPG'){
                                    Ext.getCmp('Type4').setValue(true);
                                    break; 
                                }else if (Object_type == 'Coop'){
                                    Ext.getCmp('Type7').setValue(true);
                                    break; 
                                }                                
                                break;
                            case 'Demoplot':    
                                Ext.getCmp('ObjectType').hide();                           
                                Ext.getCmp('Type5').setValue(true);                                
                                break;
                            case 'Coop':    
                                Ext.getCmp('ObjectType').hide();                           
                                Ext.getCmp('Type7').setValue(true);                                
                                break;       
                            case 'Warehouse':    
                                Ext.getCmp('ObjectType').hide();                           
                                Ext.getCmp('Type6').setValue(true);                                
                                break;
                            case 'Trader':    
                                Ext.getCmp('ObjectType').hide();                           
                                Ext.getCmp('Type8').setValue(true);                                
                                break;       
                            case 'SCE':    
                                Ext.getCmp('ObjectType').hide();                           
                                Ext.getCmp('Type9').setValue(true);                                
                                break;       
                            default:                                                                
                                Ext.getCmp('ObjectType').hide();
                                Ext.getCmp('Type1').hide(); 
                                Ext.getCmp('Type2').hide(); Ext.getCmp('Type2').setValue(false);
                                Ext.getCmp('Type3').hide(); Ext.getCmp('Type3').setValue(false);
                                Ext.getCmp('Type4').hide(); Ext.getCmp('Type4').setValue(false);
                                Ext.getCmp('Type5').hide(); Ext.getCmp('Type5').setValue(false);
                                Ext.getCmp('Type6').hide(); Ext.getCmp('Type6').setValue(false);
                                Ext.getCmp('Type7').hide(); Ext.getCmp('Type7').setValue(false);
                                Ext.getCmp('Type8').hide(); Ext.getCmp('Type8').setValue(false);
                                Ext.getCmp('Type9').hide(); Ext.getCmp('Type9').setValue(false);
                                break;                           
                        }
                        
                        Ext.getCmp('NameID').setValue(''+r.ObjectID+' - '+r.ObjectName); //(r.ObjectID);
                        
                        Ext.getCmp('ObjectID').setValue(r.ObjectID);
                        Ext.getCmp('ObjectName').setValue(r.ObjectName);
                        Ext.getCmp('VillageID').setValue(r.VillageID);

                        Ext.getCmp('VisitDate').setValue(r.VisitDate);
                        Ext.getCmp('VisitTime').setValue(r.VisitTime);
                        
                        Ext.getCmp('Description').setValue(r.Description);
                        
                        Ext.getCmp('images-view').enable()

                        Ext.getCmp('save').setVisible(false);
                        Ext.getCmp('update').setVisible(true);

                        /* ok */
                        var proxy = store_foto.getProxy();
                            proxy.extraParams = {
                                 mid: Ext.getCmp('MonitoringID').getValue()// sm.get('MonitoringId')
                            };
                        store_foto.load();                                              
                    }
                });
            }
        },
        dockedItems      : [
            {
                xtype        : 'pagingtoolbar',
                store        : store_activity,
                dock         : 'bottom',
                displayInfo  : true
            },
            {
                xtype        : 'toolbar',
                items        : [
                    // BUTTON : ADD =>
                    {
                        icon     : varjs.config.base_url+'images/icons/silk/add.png', 
                        text     : lang('Add'),
                        scope    : this,                       
                        cls      : m_act_add,
                        handler  : function(){
                            displayFormWindow();
                            
                            Ext.getCmp('images-view').disable();
                            
                            Ext.getCmp('save').setVisible(true);
                            Ext.getCmp('update').setVisible(false);

                            Ext.getCmp('add').setVisible(true); 
                            Ext.getCmp('foto').enable();
                            Ext.getCmp('title').enable();

                            Ext.getCmp('remove').setVisible(false);
                            Ext.getCmp('cancel').setVisible(false);
                            
                            var proxy = store_foto.getProxy();
                                proxy.extraParams = {
                                    id: Ext.getCmp('MonitoringID').setValue("")
                                };
                            store_foto.load();
                        }
                    },
                    // BUTTON : UPDATE =>
                    {
                        icon    : varjs.config.base_url + 'images/icons/silk/pencil.png',
                        text    : lang('Update'),
                        cls     : m_act_update,
                        hidden  : true,
                        scope   : this,
                        handler : function () {
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            if (!Ext.getCmp('grid').getSelectionModel().getSelection()[0]) {
                                Ext.MessageBox.show({
                                    title   : 'Warning !',
                                    msg     : lang('Please select data first !'),
                                    buttons : Ext.MessageBox.OK,
                                    icon    : Ext.MessageBox.WARNING
                                });
                                return;
                            }                                   
                            displayFormWindow();                            
                            Ext.Ajax.request({
                                url    : m_crud,
                                method : 'GET',
                                params : {
                                    id: sm.get('MonitoringID')
                                },
                                success : function(fp, o){
                                    var r = Ext.decode(fp.responseText);
                                    Ext.getCmp('MonitoringID').setValue(sm.get('MonitoringID'));

                                    Ext.getCmp('ObjectCategory').setValue(r.ObjectCategory);

                                    Ext.getCmp('ObjectType').setValue(r.ObjectType);
                                    
                                    Ext.getCmp('NameID').setValue(''+r.ObjectID+' - '+r.ObjectName); //(r.ObjectID);
                                    
                                    Ext.getCmp('ObjectID').setValue(r.ObjectID);
                                    Ext.getCmp('ObjectName').setValue(r.ObjectName);
                                    Ext.getCmp('VillageID').setValue(r.VillageID);
                                    
                                    Ext.getCmp('VisitDate').setValue(r.VisitDate);
                                    Ext.getCmp('VisitTime').setValue(r.VisitTime);

                                    Ext.getCmp('Description').setValue(r.Description);
                                    
                                    Ext.getCmp('images-view').enable()

                                    Ext.getCmp('save').setVisible(false);
                                    Ext.getCmp('update').setVisible(true);

                                    Ext.getCmp('add').setVisible(true);
                                    Ext.getCmp('foto').enable();
                                    Ext.getCmp('title').enable();

                                    Ext.getCmp('remove').setVisible(false);
                                    Ext.getCmp('cancel').setVisible(false);

                                    /* ok */
                                    var proxy = store_foto.getProxy();
                                        proxy.extraParams = {
                                            mid: sm.get('MonitoringID')
                                        };
                                    store_foto.load();                                              
                                }
                            });
                        }                       
                    },
                    // BUTTON : DELETE =>
                    {
                        itemId  : 'remove',
                        icon    : varjs.config.base_url+'images/icons/silk/delete.png',
                        cls     : m_act_delete,
                        text    : lang('Delete'),
                        hidden  : true,
                        scope   : this,
                        handler : function(){
                            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            if (! Ext.getCmp('grid').getSelectionModel().getSelection()[0]) {
                                Ext.MessageBox.show({
                                    title   : 'Warning !',
                                    msg     : lang('Please select data first !'), 
                                    buttons : Ext.MessageBox.OK, 
                                    icon    : Ext.MessageBox.WARNING
                                });
                                return;
                            }                           
                            Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), 
                                function(btn){
                                    if(btn == 'yes'){
                                        Ext.Ajax.request({                                            
                                            url        :  m_crud,
                                            method     : 'DELETE',
                                            params     : {
                                                MonitoringID:smb.raw.MonitoringID
                                            },
                                            waitMsg  : lang('Deleting data ...'),
                                            success    : function(response, opts){
                                               //Ext.MessageBox.alert('Success', lang('Data delete successfully.'));
                                                var obj = Ext.decode(response.responseText);
                                                switch(obj.success){
                                                    case true: 
                                                        store_activity.load({
                                                            params: {                                   
                                                                prov : Ext.getCmp('Provinsi').getValue(),
                                                                kab  : Ext.getCmp('Kabupaten').getValue(),
                                                                kec  : Ext.getCmp('Kecamatan').getValue(),
                                                                des  : Ext.getCmp('Desa').getValue(),
                                                                category_name: Ext.getCmp('Order').getValue()
                                                            }
                                                        }); 
                                                        Ext.getCmp('add').setVisible(true);
                                                        Ext.getCmp('foto').enable();
                                                        Ext.getCmp('title').enable();
                                                        
                                                        Ext.getCmp('remove').setVisible(false);
                                                        Ext.getCmp('cancel').setVisible(false);
                                                        break;
                                                    default:
                                                        Ext.MessageBox.show({
                                                            title   : 'Warning !', 
                                                            msg     : lang('Failed to delete record, because activity have photo !'), 
                                                            buttons : Ext.MessageBox.OK, 
                                                            icon    : Ext.MessageBox.WARNING
                                                        });                                                         
                                                        Ext.getCmp('add').setVisible(true);
                                                        Ext.getCmp('foto').enable();
                                                        Ext.getCmp('title').enable();
                                                        
                                                        Ext.getCmp('remove').setVisible(false);
                                                        Ext.getCmp('cancel').setVisible(false);
                                                        break;
                                               }
                                            },
                                            failure: function(response, opts){
                                               var obj = Ext.decode(response.responseText);
                                               Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                            }
                                        });
                                    }
                                }
                            );
                        }
                    },                                     
                    {
                        id           : 'Provinsi',
                        name         : 'Provinsi',
                        xtype        : 'combo',
                        store        :  mc_Provinsi,
                        displayField : 'label',
                        valueField   : 'id',
                        queryMode    : 'local',
                        emptyText    : lang('Provinsi'),
                        listeners    : {
                            change: function (cb, nv, ov) {
                                mc_Kabupaten.load({
                                    params: {
                                        key: Ext.getCmp('Provinsi').getValue()
                                    }
                                });                               
                                Ext.getCmp('Kabupaten').enable();
                                Ext.getCmp('Kabupaten').setValue("");
                                Ext.getCmp('Kecamatan').setValue("");
                                Ext.getCmp('Desa').setValue("");
                            }
                        }
                    },
                    {
                        id: 'Kabupaten',
                        name: 'Kabupaten',
                        xtype: 'combo',                       
                        store:mc_Kabupaten,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        emptyText    : lang('Kabupaten'),
                        disabled:'true',
                        listeners    : {
                            change: function (cb, nv, ov) {
                                mc_Kecamatan.load({
                                    params: {                                        
                                        key : Ext.getCmp('Kabupaten').getValue()
                                    }
                                });
                                Ext.getCmp('Kecamatan').enable();                                
                                Ext.getCmp('Kecamatan').setValue("");
                                Ext.getCmp('Desa').setValue("");
                            }
                        }                        
                    },
                    {   
                        id: 'Kecamatan',
                        name: 'Kecamatan',
                        xtype: 'combo',                        
                        store:mc_Kecamatan,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        emptyText    : lang('Kecamatan'),
                        disabled: 'true',
                        listeners: {
                          change: function (cb, nv, ov) {
                             mc_Desa.load({
                                params: {
                                   key: Ext.getCmp('Kecamatan').getValue()
                             }});
                             Ext.getCmp('Desa').enable();
                             Ext.getCmp('Desa').setValue("");
                          }
                        }
                    },
                    {
                        id           : 'Desa',
                        name         : 'Desa',
                        xtype        : 'combo',                        
                        store        : mc_Desa,
                        displayField : 'label',                        
                        valueField   : 'id',
                        queryMode    : 'local',
                        emptyText    : lang('Desa'),
                        disabled     : 'true',                        
                        anchor       : '90%',                        
                        listeners: {
                          change: function (cb, nv, ov) {
                            store_activity.load({
                                 params: {                                   
                                     prov : Ext.getCmp('Provinsi').getValue(),
                                     kab  : Ext.getCmp('Kabupaten').getValue(),
                                     kec  : Ext.getCmp('Kecamatan').getValue(),
                                     des  : Ext.getCmp('Desa').getValue()
                                 }
                            });
                            Ext.getCmp('Order').setValue("");
                          }
                        }                                        
                    },                    
                    '->',
                    {
                        id           : 'Order',
                        name         : 'Order',
                        fieldLabel   : lang('Category Name'),
                        xtype        : 'combo',
                        store        : mc_order,
                        displayField : 'label',
                        valueField   : 'label',
                        queryMode    : 'local',
                        selectOnFocus: true,
                        labelAlign   : 'right',
                        listeners    : {
                            change: function (cb, nv, ov) {
                                store_activity.load({
                                    params: {                                        
                                        category_name: Ext.getCmp('Order').getValue(),
                                        des  : Ext.getCmp('Desa').getValue()
                                    }
                                });
                            }
                        }
                    }


                ]
            }
        ],
        columns          : [
            {
                text        : 'Monitoring ID',
                dataIndex   : 'MonitoringID',
                hidden      : true
            },
            {
                text        : 'No',
                xtype       : 'rownumberer',
                align       : 'center',
                width       : '5%'
            },
            {
                text        : lang('Category Name'), 
                width       : '10%',
                dataIndex   : 'ObjectCategory',
            },
             {
                text        : lang('Type Name'), 
                width       : '10%',
                dataIndex   : 'ObjectType',
            },
            {
                text        : lang('ID / Name'), 
                width       : '24%',
                dataIndex   : 'ObjectIDName',
            },            
            {
                text        : lang('Visit Date'), 
                width       : '10%',
                dataIndex   : 'VisitDate',
            },
            {
                text        : lang('Visit Time'), 
                width       : '10%',
                dataIndex   : 'VisitTime',
            },
            {
                text        : lang('Description'), 
                width       : '30%',
                dataIndex   : 'Description',
            }
        ]
    });
    // =======================================================
    function displayFormShow(){
        if(!win_show.isVisible()){
            Form_show.getForm().reset();
            Ext.getCmp('foto_view').setSrc('');
            win_show.show();
        } else {
            win_show.hide(this, function() {});
            win_show.toFront();
        }
    }   
    
    var Form_show = Ext.create('Ext.form.Panel', {
        frame       : false,
        autoScroll  : false,
        width       : 560,
        height      : 450,        
        bodyPadding : 5,
        id          : 'Form_show', 
        fieldDefaults    : {labelAlign:'left',labelWidth:70},       
        items        : [
            {
                xtype      : 'image',
                id         : 'foto_view',
                height     : '337px',
                width      : '550px',
                style      : 'padding:5px;border:1px solid #CCC; background-color:#F2F2F2;',
            }, {
                xtype      : 'displayfield',
                id         : 'foto_title',
                name       : 'foto_title',
                //style      : 'padding:5px;font-size:15px;font-weight:bold;',
                style      : {
                   fontSize   : '18px',
                   fontFamily : 'Verdana, sans-serif',   
                   fontweight : 'bold'                
               }
            },
             {
                xtype      : 'displayfield',                                      
                id         : 'foto_date',
                name       : 'foto_date',
                style      : 'padding:5px;margin-top: -10px;',                
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
                 win_show.hide();
            }
          }
        ]
    });

    var win_show      = Ext.create('widget.window', {
        title       : 'Preview',
        id          : 'win_show',
        closable    :  true,
        modal       : true,
        closeAction : 'hide',
        width       : 580,
        height      : 500,
        layout      : {type:'border',padding:5},
        items       : [Form_show]
    });
});
