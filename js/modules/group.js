Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

Ext.onReady(function(){
   localStorage.removeItem("group_menu_business_unit_palmoil_new");
   localStorage.removeItem("group_menu_business_unit_palmoil_partner_new");

   Ext.tip.QuickTipManager.init();
   var opsiCall = "";
   var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['GroupId','GroupName', 'GroupDescription', 'GroupUnitId','UnitName','ActiveUser', 'GroupStatus', 'IsLocked'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
   store.on('beforeload', function() {
      var proxy = store.getProxy();
      var keyParam = Ext.getCmp('key').getValue();
      proxy.setExtraParam('key', keyParam);
      proxy.setExtraParam('unitId', Ext.getCmp('sUnitId').getValue());
   });

    var mc_unit = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['UnitId','UnitName'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_unit,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayFormWindow(){
        if(opsiCall !== "view")
          Ext.getCmp('btn-save-group').show();

            if (opsiCall == "update") {
              if (m_is_admin != 1) {
                 Ext.getCmp('GroupPartnerID').setReadOnly(true);
              }
            }

        else
          Ext.getCmp('btn-save-group').hide();

        if(!win.isVisible()){
            GroupForm.getForm().reset();
            win.show();
            Ext.getCmp('GroupName').focus(true,true);
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }

    var storeAksiList = Ext.create('Ext.data.ArrayStore', {
        fields: [{
            name:'value',
            type:'int'
        },{
            name:'text',
            type:'string'
        },{
            name:'MenuId'
        },{
            name:'AksiId'
        },{
            name:'child'
        }],
        proxy: {
            type: 'ajax',
            url: m_aksi_list,
            reader: {
                type:'json',
                root:'data'
            }
        },
        autoLoad: true
    });

    var storeSelectedMenu = Ext.create('Ext.data.ArrayStore', {
        fields: [{
            name:'value',
            type:'int'
        },{
            name:'text',
            type:'string'
        }],
        autoLoad: false
    });

    var ds = Ext.create('Ext.data.ArrayStore', {
        fields: [{
            name:'value',
            type:'int'
        },{
            name:'text',
            type:'string'
        },{
            name:'MenuId'
        },{
            name:'AksiId'
        },{
            name:'child'
        }],
        proxy: {
            type: 'ajax',
            url: m_aksi,
            extraParams: {id:  ''},
            reader: {
                type:'json',
                root:'data'
            }
        },
        autoLoad: false,
        listeners:{
            load:function(){
                var selected = [];
                var selector = Ext.getCmp("itemselector-field");
                ds.data.each(function(item, index, totalItems ) {
                    selected.push(item.data['value']);
                });
                selector.setValue(selected);
                reloadMenu();
            }
        }
    });

    var storeReportList = Ext.create('Ext.data.ArrayStore', {
        fields: [{
            name:'value',
            type:'string'
        },{
            name:'text',
            type:'string'
        }],
        proxy: {
            type: 'ajax',
            url: m_report_list,
            reader: {
                type:'json',
                root:'data'
            }
        },
        autoLoad: true
    });

    var dsReport = Ext.create('Ext.data.ArrayStore', {
        fields: [{
            name:'value',
            type:'string'
        },{
            name:'text',
            type:'string'
        }],
        proxy: {
            type: 'ajax',
            url: m_report,
            extraParams: {id:''},
            reader: {
                type:'json',
                root:'data'
            }
        },
        autoLoad: false,
        listeners:{
            load:function(){
                var selected2 = [];
                var selector2 = Ext.getCmp("itemselector-report");
                dsReport.data.each(function(item, index, totalItems ) {
                    selected2.push(item.data['value']);
                });
                selector2.setValue(selected2);
            }
        }
    });
    var storeGroupFilterBy = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
        {
            "id": "",
            "label": lang("Select")
        }, {
            "id": "1",
            "label": lang("Partner")
        }, {
            "id": "2",
            "label": lang("Sce")
        }, {
            "id": "3",
            "label": lang("Trader")
        }, {
            "id": "4",
            "label": lang("Cooperative")
        }, {
            "id": "5",
            "label": lang("Warehouse")
        },
        ]
    });

    var mc_partner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/common/combo_partner',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var cmb_internal_program = Ext.create('Koltiva.store.ComboGeneral.CmbInternalProgramNew', {
        storeVar: {
            PartnerID: 243
        }
    });
    
    //var GroupForm = Ext.create('Ext.form.Panel', {
    var GroupForm = Ext.widget('form',{
        frame: false,
        height: 600,
        autoScroll: true,
        width: 950,
        bodyPadding: 5,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
            xtype: 'textfield',
            id: 'GroupId',
            name: 'GroupId',
            inputType:'hidden'
        },{
            xtype: 'textfield',
            fieldLabel: 'Nama',
            id: 'GroupName',
            name: 'GroupName'
        },{
            xtype: 'textareafield',
            fieldLabel: 'Deskripsi',
            id: 'GroupDescription',
            name: 'GroupDescription'
        },{
            id: 'GroupPartnerID',
            name: 'GroupPartnerID',
            xtype: 'combobox',
            width: 100,
            allowBlank: true,
            fieldLabel: lang('Partner'),
            store: mc_partner,
            readOnly: false,
            displayField: 'label',
            queryMode: 'local',
            valueField: 'id',
            listeners: {
                change: function(cb, nv, ov) {
                    Ext.getCmp('GroupMenuBusinessUnit').setValue(null);
                    Ext.getCmp('GroupMenuBusinessUnit').getStore().storeVar.PartnerID = nv;
                    Ext.getCmp('GroupMenuBusinessUnit').getStore().load();

                    setTimeout(() => {
                        if (localStorage.getItem('group_menu_business_unit_palmoil_new') != null) {
                            if (parseInt(localStorage.getItem('group_menu_business_unit_palmoil_partner_new')) == parseInt(nv)) {
                                Ext.getCmp('GroupMenuBusinessUnit').setValue(localStorage.getItem('group_menu_business_unit_palmoil_new'));
                            }
                        }
                    }, 1200);
                }
            }
        },{
            id: 'GroupUnitId',
            name: 'GroupUnitId',
            xtype: 'combobox',
            width: 100,
            fieldLabel: 'Unit',
            store:mc_unit,
            displayField: 'UnitName',
            queryMode:'local',
            valueField: 'UnitId'
        },{
            xtype: 'itemselector',
            name: 'itemselector',
            fieldLabel:'Select roles',
            id: 'itemselector-field',
            anchor: '90%',
            height:320,
            store: storeAksiList,
            displayField: 'text',
            valueField: 'value',
            value: [],
            allowBlank: true,
            msgTarget: 'side',
            fromTitle: 'Available',
            toTitle: 'Selected',
            listeners: {
                change: function() {
                    reloadMenu();
                }
            }
        },{
            layout: 'column',
            bodyStyle: 'padding:5px 5px 10px 2px',
            xtype: 'container',
            columns: 3,
            autoEl: 'div',
            items: [{
                xtype: 'button',
                scale: 'small',
                //ui: 's-button',
                //cls: 's-blue',
                text:'Select All',
                style: {marginLeft:'11%',marginBottom:'1%'},
                handler: function() {
                    var arrVal = []; // create an empty array
                    var selector = Ext.getCmp("itemselector-field");
                    selector.store.each(function(item, index, totalItems ) {
                        arrVal.push(item.data['value']);
                    });
                    selector.setValue(arrVal);
                    reloadMenu();
                }
            },{
                xtype: 'button',
                scale: 'small',
                //ui: 's-button',
                //cls: 's-blue',
                text:'Unselect All',
                style: {marginLeft:'10px',marginBottom:'1%'},
                handler: function() {
                    Ext.getCmp("itemselector-field").reset();
                }
            }]
        },{
            id: 'GroupMenuId',
            name: 'GroupMenuId',
            xtype: 'combobox',
            width: 100,
            fieldLabel: 'Menu',
            store: storeSelectedMenu,
            displayField: 'text',
            queryMode:'local',
            valueField: 'value'
        },{
            xtype: 'itemselector',
            flex:true,
            id: 'GroupMenuBusinessUnit',
            name: 'GroupMenuBusinessUnit',
            allowBlank:true,
            msgTarget: 'side',
            fieldLabel: lang('Business Unit'),
            fromTitle: lang('Available'),
            toTitle: lang('Selected'),
            anchor: '100%',
            height:320,
            store: cmb_internal_program,
            displayField: 'label',
            valueField: 'id',
            value: []
        },{ 
            id: 'GroupFilterBy',
            name: 'GroupFilterBy',
            xtype: 'combobox',
            width: 50,
            fieldLabel: lang('Filter By'),
            store: storeGroupFilterBy,
            displayField: 'label',
            queryMode:'local',
            valueField: 'id'
        },{ 
        // report
            xtype: 'itemselector',
            name: 'itemselectorReport',
            fieldLabel:'Select report',
            id: 'itemselector-report',
            anchor: '90%',
            height:320,
            store: storeReportList,
            displayField: 'text',
            valueField: 'value',
            value: [],
            allowBlank: true,
            msgTarget: 'side',
            fromTitle: 'Available',
            toTitle: 'Selected'
        },{
            layout: 'column',
            bodyStyle: 'padding:5px 5px 0',
            xtype: 'container',
            columns: 3,
            autoEl: 'div',
            items: [{
                xtype: 'button',
                scale: 'small',
                //ui: 's-button',
                //cls: 's-blue',
                text:'Select All',
                style: {marginLeft:'11%'},
                handler: function() {
                    var arrVal2 = []; // create an empty array
                    var selector2 = Ext.getCmp("itemselector-report");
                    selector2.store.each(function(item, index, totalItems ) {
                        arrVal2.push(item.data['value']);
                    });
                    selector2.setValue(arrVal2);
                }
            },{
                xtype: 'button',
                scale: 'small',
                text:'Unselect All',
                style: {marginLeft:'10px'},
                handler: function() {
                    Ext.getCmp("itemselector-report").reset();
                }
            }]
        }],
        buttons: [{
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'btn-save-group',
            handler: function() {
                var form = this.up('form').getForm();
                //var params = Ext.getCmp('menuu').getValue();
                var methode;
                if (Ext.getCmp('GroupId').getValue()=='') methode = 'POST'; else methode = 'PUT';

                form.submit({
                    //url: m_crud+'?'+ Ext.urlEncode(params),
                    url: m_crud,
                    method : methode,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });

                win.hide(this, function() {
                    store.load();
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

                localStorage.removeItem("group_menu_business_unit_palmoil_new");
                localStorage.removeItem("group_menu_business_unit_palmoil_partner_new");
            }
        }]
    });

    var win = Ext.create('widget.window', {
        title: 'Data Group',
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'hide',
        width: 970,
        frame:false,
        minWidth: 370,
        height: 650,
        layout: {
            type: 'fit'
        },
        items: [GroupForm]
    });

   var storeGroupUserList = Ext.create('Ext.data.Store', {
      model: 'GroupUser.Model',
      fields: ['UserId','UserRealName', 'UserName', 'UserActive','UserType' , 'GroupName'],
      autoLoad: true,
      pageSize: 20,
      proxy: {
         type: 'ajax',
         url: m_crud + '_user',
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
   storeGroupUserList.on('beforeload', function() {
      var proxy = storeGroupUserList.getProxy();
      var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
      proxy.setExtraParam('groupId', sm.get('GroupId'));
   });

   function displayGroupUserWindow() {
      var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
      if (!winGroupUser.isVisible()) {
         winGroupUser.show();
      } else {
         winGroupUser.hide(this, function() {});
         winGroupUser.toFront();
      }
   }

   function displayGroupProgramWindow(id) {

    function checkchangeaction(n,checked, callback) {
        n.eachChild(function(child){
            child.set({checked:checked});
            if(child.childNodes.length > 0) {
                checkchangeaction(child,checked, callback);
            }
        });
        if (typeof callback === "function") {
            callback();
        }
    }

    Ext.Ajax.request({
        url:  m_api+'/system/groupprogramselected',
        method: 'GET',
        params: {
            id: id
        },
        success: function(fp, o) {
            var obj = Ext.JSON.decode(fp.responseText);

            var win = Ext.create('Ext.Window',{
                id:'winGroupProgram',
                title:lang('Group Questioner Access'),
                modal:true,
                constraint:true,
                width:1050,
                minWidth:400,
                layout:{
                    type:'column'
                },
                items:[{
                    xtype:'gridpanel',
                    columnWidth:.3,
                    height:300,
                    id:'grid-group-mw_program',
                    store: Ext.create('Ext.data.ArrayStore', {
                        fields: ['value','text','program_status','orgs','use_json','parent_table','uid_field','reference_field'],
                        proxy: {
                            type: 'ajax',
                            url: m_api+'/system/groupprogramlist',
                            extraParams:{
                                id:id
                            },
                            reader: {
                                type: 'json',
                                root: 'data'
                            }
                        },
                        autoLoad: true
                    }),
                    columns:[
                        {
                            text: lang('Available Questioner'),
                            flex: 1,
                            dataIndex: 'text'
                        },
                        {
                            text: lang('Status'),
                            width: 75,
                            align:'center',
                            dataIndex: 'program_status',
                            renderer: function(v) {
                                return v === 1 ? '<span style="color:green;font-weight:bold;">Yes</span>' : '<span style="color:red;font-weight:bold;">No</span>';
                            }
                        }   
                    ],
                    listeners: {
                        selectionchange: function(c,r) {
                            
                            if(r.length > 0) {
                                var progid = r[0].data.value;
                                var orgs = r[0].data.orgs;
                                var stat = r[0].data.program_status;
                                var use_json = r[0].data.use_json;
                                var parent_table = r[0].data.parent_table;
                                var ref_field = r[0].data.reference_field;
                                var field = r[0].data.uid_field;
                                console.log(r);
                                Ext.getCmp('checkbox-program-group-access').enable();
                                Ext.getCmp('checkbox-program-group-access').setValue(stat);
                                
                                if(stat === 1 || stat === true) {
                                    
                                    Ext.getCmp('fieldset-program-config').enable();
                                    Ext.getCmp('tree-orgunits').enable();

                                    var o = orgs.split(',');
                                    Ext.each(o,function(one,idx,all){
                                        var node = Ext.getCmp('tree-orgunits').getStore().getNodeById(one);
                                        if(node) {
                                            node.set({checked:true});
                                            checkchangeaction(node,true);
                                        }
                                    });
                                    Ext.getCmp('cmb-main-program-table').setValue(parent_table);
                                    Ext.getCmp('cmb-main-program-field').setValue(field);
                                    Ext.getCmp('cmb-uid-field-reference').setValue(ref_field);
                                    Ext.getCmp('checkbox-use-json').setValue(use_json);

                                } else {
                                    Ext.getCmp('frm-program-config').getForm().reset();
                                    Ext.getCmp('fieldset-program-config').disable();
                                    Ext.getCmp('tree-orgunits').disable();
                                    Ext.getCmp('tree-orgunits').getRootNode().cascadeBy(function(node) { 
                                        node.set({checked:false}); 
                                    });
                                }
                            } else {
                                Ext.getCmp('checkbox-program-group-access').disable();
                                Ext.getCmp('frm-program-config').getForm().reset();
                                Ext.getCmp('fieldset-program-config').disable();
                                Ext.getCmp('tree-orgunits').disable();
                                Ext.getCmp('tree-orgunits').getRootNode().cascadeBy(function(node) { 
                                    node.set({checked:false}); 
                                });
                            }

                        }
                    }
                },{
                    xtype:'form',
                    columnWidth:.7,
                    margin:'0 5',
                    id:'frm-program-config',
                    items:[
                        {
                            xtype:'checkbox',
                            margin:'10 5',
                            disabled:true,
                            id:'checkbox-program-group-access',
                            boxLabel:lang('PROGRAM GROUP ACCESS'),
                            listeners:{
                                change:function(c,v) {
                                    var rec = Ext.getCmp('grid-group-mw_program').getSelectionModel().getSelection();
                                    if(rec.length > 0) {
                                        v = v ? 1 : 0;
                                        rec[0].data.program_status = v;
                                        Ext.getCmp('grid-group-mw_program').getStore().commitChanges();

                                        //update form on true
                                        if(v) {
                                            Ext.getCmp('fieldset-program-config').enable();
                                            Ext.getCmp('tree-orgunits').enable();
                                        } else {
                                            Ext.getCmp('fieldset-program-config').disable();
                                            Ext.getCmp('tree-orgunits').disable();
                                        }
                                        
                                        Ext.getCmp('grid-group-mw_program').getView().refresh();
                                    }
                                }
                            }
                        },
                        {
                            xtype:'fieldset',
                            title:lang('Program Configuration'),
                            layout:'column',
                            margin:5,
                            padding:10,
                            disabled:true,
                            id:'fieldset-program-config',
                            items:[
                                Ext.create('Ext.tree.Panel', {
                                    columnWidth:.6,
                                    height: 300,
                                    disabled:false,
                                    title:lang('Program Regional Access'),
                                    style:'border:none;background:none;',
                                    store: Ext.create('Ext.data.TreeStore', {
                                        autoLoad: true,
                                        proxy: {
                                            type: 'ajax',
                                            url: m_api + '/system/regiontree',
                                        }
                                    }),
                                    listeners:{
                                        checkchange: function(n, checked){
                                            checkchangeaction(n,checked, function(){
                                                var rec = Ext.getCmp('grid-group-mw_program').getSelectionModel().getSelection();
                                                var o = [];
                                                var tree = Ext.getCmp('tree-orgunits').getChecked();
                                                Ext.each(tree,function(one,idx,all){
                                                    if(one.data.id !== 'root') {
                                                        o.push(one.data.id);
                                                    }
                                                });
            
                                                rec[0].data.orgs = o.join(',');
                                                Ext.getCmp('grid-group-mw_program').getStore().commitChanges();
                                            });
            
                                        }
                                    },
                                    disabled:true,
                                    id:'tree-orgunits',
                                    rootVisible: false
                                }),
                                {
                                    xtype:'container',
                                    padding:'0 15',
                                    columnWidth:.4,
                                    items:[
                                        {
                                            xtype:'checkbox',
                                            name:'use_json',
                                            id:'checkbox-use-json',
                                            listeners:{
                                                change:function(c,v) {
                                                    var rec = Ext.getCmp('grid-group-mw_program').getSelectionModel().getSelection();
                                                    if(rec.length > 0) {
                                                        v = v ? 1 : 0;
                                                        rec[0].data.use_json = v;
                                                        Ext.getCmp('grid-group-mw_program').getStore().commitChanges();
                                                    }
                                                }
                                            },
                                            boxLabel:lang('USE JSON DATA') + '<div style="color:#666;margin-top:-5px;padding-left:20px;">' + lang('Survey Data will be saved in json format') + '</div>'
                                        },
                                        {
                                            id: 'cmb-main-program-table',
                                            name: 'program_table',
                                            xtype: 'combo',
                                            store: Ext.create('Ext.data.ArrayStore', {
                                                fields: ['table_name'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'ajax',
                                                    url: m_api + '/system/cmbprogramtable',
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data'
                                                    }
                                                }
                                            }),
                                            fieldLabel:lang('Parent Program Table'),
                                            labelAlign:'top',
                                            width:250,
                                            displayField: 'table_name',
                                            valueField: 'table_name',
                                            queryMode: 'local',
                                            emptyText: lang('Select Program Table'),
                                            submitEmptyText: false,
                                            listeners:{
                                                select: function(c,v) {
                                                    v = v[0].data.table_name;
                                                    var s = Ext.getCmp('cmb-main-program-field').getStore();
                                                    var r = Ext.getCmp('cmb-uid-field-reference').getStore();
                                                    s.proxy.extraParams = {'table':v}
                                                    s.load();

                                                    r.proxy.extraParams = {'table':v}
                                                    r.load();
                                                },
                                                change:function(c,v) {
                                                    var rec = Ext.getCmp('grid-group-mw_program').getSelectionModel().getSelection();
                                                    if(rec.length > 0) {
                                                        rec[0].data.parent_table = v;
                                                        Ext.getCmp('grid-group-mw_program').getStore().commitChanges();
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            id: 'cmb-main-program-field',
                                            name: 'program_field',
                                            xtype: 'combo',
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['column_name'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'ajax',
                                                    url: m_api + '/system/cmbprogramfield',
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data'
                                                    }
                                                }
                                            }),
                                            fieldLabel:lang('Object UID Field'),
                                            labelAlign:'top',
                                            width:250,
                                            displayField: 'column_name',
                                            valueField: 'column_name',
                                            queryMode: 'local',
                                            emptyText: 'UID Field',
                                            submitEmptyText: false,
                                            listeners:{
                                                change:function(c,v) {
                                                    var rec = Ext.getCmp('grid-group-mw_program').getSelectionModel().getSelection();
                                                    if(rec.length > 0) {
                                                        rec[0].data.uid_field = v;
                                                        Ext.getCmp('grid-group-mw_program').getStore().commitChanges();
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            id: 'cmb-uid-field-reference',
                                            name: 'field_reference',
                                            xtype: 'combo',
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['column_name'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'ajax',
                                                    url: m_api + '/system/cmbprogramfield',
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data'
                                                    }
                                                }
                                            }),
                                            fieldLabel:lang('Object UID Reference'),
                                            labelAlign:'top',
                                            width:250,
                                            displayField: 'column_name',
                                            valueField: 'column_name',
                                            queryMode: 'local',
                                            emptyText: 'Field Reference',
                                            submitEmptyText: false,
                                            listeners:{
                                                change:function(c,v) {
                                                    var rec = Ext.getCmp('grid-group-mw_program').getSelectionModel().getSelection();
                                                    if(rec.length > 0) {
                                                        rec[0].data.reference_field = v;
                                                        Ext.getCmp('grid-group-mw_program').getStore().commitChanges();
                                                    }
                                                }
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }],
                buttons:[{
                    text: lang('Save'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function() {
                        var programs = Ext.getCmp('grid-group-mw_program').getStore().getRange();
                        var fi = [];
                        Ext.each(programs,function(one,idx,all){
                            fi.push(one.data);
                        });

                        Ext.Ajax.request({
                            url:  m_api+'/system/updategroupprogramselected',
                            method: 'POST',
                            params: {
                                id: id,
                                programs:Ext.JSON.encode(fi)
                            },
                            success: function(fp, o) {
                                win.close();
                            }
                        });
                    }
                }, {
                    text: 'Close',
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false,
                    handler: function() {
                        win.close();
                    }
                }]
            });

            win.show();
            
            Ext.getCmp('tree-orgunits').getRootNode().cascadeBy(function(node) {
                node.set({checked:true});
            });

            
        }
    });
}

   var winGroupUser = Ext.create('widget.window', {
      title: 'Group User List',
      id: 'winGroupUser',
      closable: true,
      modal: true,
      closeAction: 'hide',
      width: 1050,
      frame: false,
      minWidth: 370,
      height: 500,
      layout: {
         type: 'fit'
      },
      items: [{
         id: 'gridGroupUser',
         xtype: 'gridpanel',
         store: storeGroupUserList,
         style: 'border:1px solid #CCC;',
         loadMask: true,
         selType: 'rowmodel',
         dockedItems: [{
            store: storeGroupUserList,
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            displayInfo: true
         }],
         columns: [{
            text: 'No',
            xtype: 'rownumberer',
            width: '5%'
         },{
            text: 'User ID',
            width: '6%',
            dataIndex: 'UserId'
         },{
            text: 'Real Name',
            width: '18%',
            dataIndex: 'UserRealName'
         },{
            text: 'User Name',
            width: '18%',
            dataIndex: 'UserName'
         },{
            text: 'Active',
            width: '7%',
            dataIndex: 'UserActive'
         },{
            text: 'User Type',
            width: '15%',
            dataIndex: 'UserType'
         },{
            text: 'Group',
            width: '28%',
            dataIndex: 'GroupName'
         }
         ]
      }],
      buttons: [{
         text: 'Close',
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-grey',
         disabled: false,
         handler: function() {
            winGroupUser.hide();
         }
      }]
   });

   var contextMenuGrid = Ext.create('Ext.menu.Menu',{
      items: [{
         icon: varjs.config.base_url + 'images/icons/new/view.png',
         text: 'User List',
         handler: function() {
            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
            storeGroupUserList.load({
               params: {
                  groupId: sm.get('GroupId'),
                  page:1,
                  start:0,
                  limit:20
               }
            });
            displayGroupUserWindow();
            if (sm.get('IsLocked') == 1)
                Ext.MessageBox.alert(lang('Warning'), lang('This group has been locked'));
         }
      },{
         icon: varjs.config.base_url + 'images/icons/new/view.png',
         text: 'View',
         hidden: false,
         handler: function(){
          opsiCall = "view";
            displayFormWindow();
            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
            Ext.Ajax.request({
               url: m_crud,
               method: 'GET',
               params: {id: sm.get('GroupId')},
               success: function(fp, o){
                  var r = Ext.decode(fp.responseText);
                  Ext.getCmp('GroupId').setValue(sm.get('GroupId'));
                  Ext.getCmp('GroupName').setValue(r.GroupName);
                  Ext.getCmp('GroupDescription').setValue(r.GroupDescription);
                  Ext.getCmp('GroupPartnerID').setValue(r.GroupPartnerID);
                  Ext.getCmp('GroupUnitId').setValue(r.GroupUnitId);
                  Ext.getCmp('GroupFilterBy').setValue(r.GroupFilterBy);
                  setTimeout(function(){
                     Ext.getCmp('GroupMenuId').setValue(parseInt(r.GroupMenuId));
                  }, 1000);
                  itemmenu = '';
                  /* ok */
                  ds.load({
                     params: {
                        id: sm.get('GroupId')
                     },
                     callback: function(records, operation, success) {
                        reloadMenu();
                     }
                  });
                  dsReport.load({
                     params: {
                        id: sm.get('GroupId')
                     }
                  });

                   setTimeout(() => {
                       if (r.GroupMenuBusinessUnit != null && r.GroupMenuBusinessUnit != undefined){
                            var arrGroupMenuBusinessUnit = r.GroupMenuBusinessUnit.split(',');

                            localStorage.setItem("group_menu_business_unit_palmoil_new", arrGroupMenuBusinessUnit);
                            localStorage.setItem("group_menu_business_unit_palmoil_partner_new", r.GroupPartnerID);

                            Ext.getCmp('GroupMenuBusinessUnit').setValue(arrGroupMenuBusinessUnit);
                        }
                    }, 1200);
                  //Set Business Unit
               }
            });
            if (sm.get('IsLocked') == 1)
                Ext.MessageBox.alert(lang('Warning'), lang('This group has been locked'));
         }
      },{
         icon: varjs.config.base_url + 'images/icons/new/update.png',
         text: 'Update',
         hidden:m_act_update,
         handler: function(){
          opsiCall = "update"
            displayFormWindow();
            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
            Ext.Ajax.request({
               url: m_crud,
               method: 'GET',
               params: {id: sm.get('GroupId')},
               success: function(fp, o){
                  var r = Ext.decode(fp.responseText);
                  Ext.getCmp('GroupId').setValue(sm.get('GroupId'));
                  Ext.getCmp('GroupName').setValue(r.GroupName);
                  Ext.getCmp('GroupDescription').setValue(r.GroupDescription);
                  Ext.getCmp('GroupPartnerID').setValue(r.GroupPartnerID);
                  Ext.getCmp('GroupUnitId').setValue(r.GroupUnitId);
                  Ext.getCmp('GroupFilterBy').setValue(r.GroupFilterBy);
                  setTimeout(function(){
                     Ext.getCmp('GroupMenuId').setValue(parseInt(r.GroupMenuId));
                  }, 1000);
                  itemmenu = '';
                  /* ok */
                  ds.load({
                     params: {
                        id: sm.get('GroupId')
                     },
                     callback: function(records, operation, success) {
                        reloadMenu();
                     }
                  });
                  dsReport.load({
                     params: {
                        id: sm.get('GroupId')
                     }
                  });

                    //Set Business Unit
                    setTimeout(() => {
                       if (r.GroupMenuBusinessUnit != null && r.GroupMenuBusinessUnit != undefined){
                            var arrGroupMenuBusinessUnit = r.GroupMenuBusinessUnit.split(',');

                            localStorage.setItem("group_menu_business_unit_palmoil_new", arrGroupMenuBusinessUnit);
                            localStorage.setItem("group_menu_business_unit_palmoil_partner_new", r.GroupPartnerID);

                            Ext.getCmp('GroupMenuBusinessUnit').setValue(arrGroupMenuBusinessUnit);
                        }
                    }, 1200);
               }
            });
            if (sm.get('IsLocked') == 1)
                Ext.MessageBox.alert(lang('Warning'), lang('This group has been locked'));
         }
      },{
         icon: varjs.config.base_url + 'images/icons/new/delete.png',
         text: 'Delete',
         hidden:m_act_delete,
         handler: function() {
            let additionalMsg = "";
            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
            if (smb.get('IsLocked') == 1)
              additionalMsg = lang("This group has been locked") + ", ";
            Ext.MessageBox.confirm('Message', additionalMsg + lang('Yakin hapus data?') , function(btn){
               if(btn == 'yes'){
                  Ext.Ajax.request({
                     waitMsg: 'Please Wait',
                     url: m_crud,
                     method : 'DELETE',
                     params: {GroupId:  smb.raw.GroupId},
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
      },'-',{
        icon: varjs.config.base_url + 'images/icons/silk/application_view_list.png',
        text: lang('Questioner List'),
        handler: function() {
            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
            storeGroupUserList.load({
                params: {
                    groupId: sm.get('GroupId'),
                    page: 1,
                    start: 0,
                    limit: 20
                }
            });
            displayGroupProgramWindow(sm.get('GroupId'));
        }
    }
      ]
   });

    var grid = Ext.create('Ext.grid.Panel', {
       store: store,
       width: '100%',
       id:'grid',
       minHeight:250,
       //title: 'Group List',
       style: 'border:1px solid #CCC;',
       renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       listeners : {
         itemclick: function(view, record, item, index, e){
            contextMenuGrid.showAt(e.getXY());
         }
       },
       dockedItems: [{
              xtype: 'pagingtoolbar',
              store: store,   // same store GridPanel is using
              dock: 'bottom',
              displayInfo: true
         },{
            xtype: 'toolbar',
            items: [
            {
               icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
               text: 'Add',
               scope: this,
               handler : displayFormWindow,
               cls : m_act_add
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:'hide-icon',
               text: 'Hapus',
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?' , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_crud,
                        method : 'DELETE',
                        params: {GroupId:  smb.raw.GroupId},
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
            },{
               xtype: 'textfield',
               name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
               id: 'key',
               emptyText: 'Group Name',
               submitEmptyText:false,
               listeners: {
                  specialkey: submitOnEnter
               }
            },{
               id: 'sUnitId',
               name: 'sUnitId',
               xtype: 'combo',
               store:mc_unit,
               displayField: 'UnitName',
               valueField: 'UnitId',
               queryMode: 'local',
               emptyText: 'Group Unit',
               submitEmptyText:false,
               width:350
            },{
               xtype: 'button',
               margin: '0px 0px 0px 6px',
               text: 'Search',
               handler: function () {
                  store.load({
                     params: {
                        key: Ext.getCmp('key').getValue(),
                        unitId : Ext.getCmp('sUnitId').getValue(),
                        page:1,
                        start:0,
                        limit:50
                     }
                  });
               }
            }
            ]
    }],
    columns: [
    {
        text: 'Group ID',
        dataIndex: 'GroupId',
        hidden:true
    },
    {
        text: 'No',
        xtype: 'rownumberer',
        flex: 0.3
    },
    {
        text: 'Group Name',
        flex: 1,
        dataIndex: 'GroupName',
    },
    {
        text: 'Description',
        flex: 1,
        dataIndex: 'GroupDescription',
    },
    {
        text: 'Unit',
        flex: 1,
        dataIndex: 'UnitName',
    },
    {
        text: 'Active User',
        flex: 1,
        dataIndex: 'ActiveUser',
    },{
        text: 'Group Status',
        flex: 1,
        dataIndex: 'GroupStatus'
    },{
        text: 'Lock Status',
        flex: 1,
        dataIndex: 'IsLocked',
        renderer: function(value){
            if (value == 1) {
                return lang('Yes');
            }
            return lang('No');
        }
    }]
   });

   function submitOnEnter(field, event) {
      if (event.getKey() == event.ENTER) {
         store.load({
            params: {
              key: Ext.getCmp('key').getValue(),
              unitId : Ext.getCmp('sUnitId').getValue()
            }
         });
      }
   }

    function reloadMenu () {
        var value = Ext.getCmp('GroupMenuId').getValue();
        var itemSelectorField   = Ext.getCmp('itemselector-field');
        var fieldList           = itemSelectorField.toField.store.getRange();
        var exist = false;
        storeSelectedMenu.removeAll();
        $.each(fieldList, function(index, val) {
            if (parseInt(val.data.AksiId) == 1 && parseInt(val.data.child) == 0) {
                if (value == parseInt(val.data.MenuId)) {
                    exist = true;
                }
                storeSelectedMenu.add({
                    value:val.data.MenuId,
                    text:val.data.text,
                });
            }
        });
        if (!exist) {
            Ext.getCmp('GroupMenuId').setValue('');
        } else {
            Ext.getCmp('GroupMenuId').setValue(value);
        }
    }
});

