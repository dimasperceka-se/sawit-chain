Ext.define('Koltiva.view.menu.Form' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.menu.Form',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        Ext.util.CSS.createStyleSheet([
            '.disabledListItem {',
            '    color:#eadada',
            '}'
        ].join('\n'));
        var thisObj = this;
       // var SupplyTransID = thisObj.viewVar.SupplyTransID;
        //var SupplyBatchID = thisObj.viewVar.SupplyBatchID;
        //var storeComboTransStatus = Ext.create('Koltiva.store.menu.StoreMainGridMenu');

        //StoreComboSysAct    = Ext.create('Koltiva.store.menu.StoreComboSysAct');
        var StoreCmbSysAct          = Ext.create('Koltiva.store.menu.StoreCmbSysAct');
        var StoreMenuParent    = Ext.create('Koltiva.store.menu.StoreMenuParent');
        

        function backToList(){
            Ext.getCmp('Koltiva.view.menu.Form').destroy(); //destory current view
            if(Ext.getCmp('Koltiva.view.menu.MainGridMenu') == undefined){
                Ext.create('Koltiva.view.menu.MainGridMenu', {
                    opsiDisplay: 'view',
                    viewVar: {
                        
                    }
                });
            }else{
                Ext.create('Koltiva.view.menu.MainGridMenu', {
                    opsiDisplay: 'view',
                    viewVar: {
                        //SupplyTransID: sm[0].get('SupplyTransID'),
                        
                    }
                });
            }
        }
		
        var my_item_selector_instance = Ext.create("Ext.ux.form.ItemSelector", {
            anchor: '100%',
            //fieldLabel: 'ItemSelectorxx',
            imagePath: '../ux/images/',
			id:'ComboSysActDisplays',
			name:'ComboSysActDisplays',
            store: StoreCmbSysAct,
            displayField: 'label',
            valueField: 'id',
            allowBlank: false,
            msgTarget: 'side',
            fromTitle: 'Available',
            toTitle: 'Selected'
        });
   
           
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Form Menu'),
            frame: true,
            //id: 'Koltiva.view.menu.MainFormMenu-form',
            id: 'Koltiva.view.menu.MainFormMenu-form',
            fileUpload: false,
            jsonSubmit:true,
            margin:'0 0 20 0',
            items: [{
                layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'panel',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[ {
                                columnWidth: 1,
                                layout:'form',
                                items:[{
                                    xtype: 'panel',
                                    items: [{
                                        layout: 'column',
                                        items: [{
                                            columnWidth: 0.5,
                                            layout: 'form',
                                            padding:5,
                                            items:[{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.menu.MainFormMenu-form-MenuId',
                                                name: 'MenuID',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'combobox',
                                                labelWidth: 150,
                                                fieldLabel: lang('Menu Parent'),
                                                id: 'Koltiva.view.menu.MainFormMenu-form-MenuParentId',
                                                name: 'MenuParentId',
                                                allowBlank: true,
                                                store : StoreMenuParent,
                                                valueField : 'MenuId',
                                                displayField :'MenuName',
                                                //queryMode: 'local',
                                                emptyText: lang('Skip this field if as the main Menu'),
                                                
                                            },
                                            {
                                                xtype: 'textfield',
                                                labelWidth: 150,
                                                fieldLabel: lang('Menu Name'),
                                                id: 'Koltiva.view.menu.MainFormMenu-form-MenuName',
                                                name: 'MenuName',
                                                allowBlank: false,
                                                
                                            }, {
                                                xtype: 'textfield',
                                                labelWidth: 150,
                                                fieldLabel: lang('Menu Module'),
                                                id: 'Koltiva.view.menu.MainFormMenu-form-MenuModule',
                                                name: 'MenuModule',
                                                allowBlank: false
                                            }, {
                                                xtype: 'radiogroup',
                                                labelWidth: 150,
                                                fieldLabel: lang('Show'),
                                                //id: 'Koltiva.view.menu.MainFormMenu-form-MenuShow',
                                                //name: 'MenuShow',
                                                items : [
                                                    {boxLabel : 'Yes', name:'MenuShow', id:'MenuShowYes', inputValue:'Yes'},
                                                    {boxLabel : 'No', name:'MenuShow', id:'MenuShowNo', inputValue:'No'}
                                                ]
                                            }
                                            ]
                                        }, {
                                            columnWidth: 0.5,
                                            layout: 'form',
                                            padding:5,
                                            items:[
                                                {
                                                    xtype: 'textfield',
                                                    labelWidth: 150,
                                                    fieldLabel: lang('Menu Icon'),
                                                    id: 'Koltiva.view.menu.MainFormMenu-form-MenuIcon',
                                                    name: 'MenuIcon',
                                                    allowBlank: true
                                                },
                                                {
                                                    xtype: 'numberfield',
                                                    labelWidth: 150,
                                                    fieldLabel: lang('Menu Order'),
                                                    id: 'Koltiva.view.menu.MainFormMenu-form-MenuOrder',
                                                    name: 'MenuOrder',
                                                    allowBlank: false
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    labelWidth: 150,
                                                    fieldLabel: lang('Menu Jenis'),
                                                    id: 'Koltiva.view.menu.MainFormMenu-form-MenuJenis',
                                                    name: 'MenuJenis',
                                                    allowBlank: true
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    labelWidth: 150,
                                                    fieldLabel: lang('Menu Param'),
                                                    id: 'Koltiva.view.menu.MainFormMenu-form-MenuParam',
                                                    name: 'MenuParam',
                                                    allowBlank: true
                                                }
                                            ]
                                        }]// end field menu
                                    }]//
                                },// end panel form menu
								{
									xtype:'panel',
									title: lang('Sys Act'),
									border: true,
									height:200,
									overflowY: 'auto',
									//padding:'0 0 0 0',
									items:[
                                        {
                                            layout: 'column',
                                            border: false,
											height: '500',
                                            items:[{
                                                columnWidth: 1,
                                                layout:{
                                                type:'vbox',
                                                align:'stretch'
                                                },
                                                items:[
													my_item_selector_instance
												]
                                            }]
                                        }
                                    ]// end item Sys Menu Act
								},
								
								]
                            }]
                        }], //disini
                        buttons: [{
                            text: 'Save',
                            margin: '5 15 5 5',
                            scale: 'large',
                            ui: 's-button',
                            id: 'Koltiva.view.menu.MainFormMenu-btnSave',
                            cls: 's-blue',
                            handler: function () {
                                

                                if (objPanelBasicData.isValid()) {
                                    var method = 'POST';
                                        objPanelBasicData.submit({
                                            url: m_api + '/menus/menu_api/add_menu',
                                            method:method,
                                            waitMsg: lang('Saving data'),
                                            success: function(fp, o) {
                                                
                                                Ext.MessageBox.show({
                                                    title: 'Information',
                                                    msg: lang('Data saved'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-success'
                                                });
                                                

                                            },
                                            failure: function(fp, o){
                                                var pesanNya;
                                                if(o.message != undefined){
                                                    pesanNya = o.message;
                                                }else{
                                                    pesanNya = lang('Connection error');
                                                }
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: pesanNya,
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            }
                                        });
                                } else {
                                    Ext.MessageBox.show({
                                        title: 'Attention',
                                        msg: lang('Form not valid yet'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-info'
                                    });
                                }
                              
                            }
                        }, {
                            text: lang('Back'),
                            margin: '5 15 5 5',
                            scale: 'large',
                            ui: 's-button',
                            id: 'Koltiva.view.Traceability.Transaction.Warehouse.FormWarehouseTransaction-btnCancel',
                            cls: '',
                            handler: function () {
                                backToList();
                            }
                        }]
                    }]
                }]
            },
			//my_item_selector_instance
			
			
			],
            listeners:{
                afterrender: function(c){
                    var MenuId = thisObj.viewVar.MenuId;
                    var Tipe = thisObj.viewVar.Tipe;
                    var opsiDisplay = thisObj.viewVar.opsiDisplay;
                    //alert(MenuId);
                    if(Tipe=='update'){

                    Ext.Ajax.request({
                        url: m_api + '/menus/menu_api/show_menuById',
                        method: 'GET',
                        params: {
                            MenuId: MenuId
                        },
                        success: function(fp, o){
                            var r = Ext.decode(fp.responseText);
    
                            //console.log(r);

                            Ext.getCmp('Koltiva.view.menu.MainFormMenu-form-MenuId').setValue(r.SysMenu.MenuId);
                            Ext.getCmp('Koltiva.view.menu.MainFormMenu-form-MenuParentId').setValue(r.SysMenu.MenuParentId);
                            Ext.getCmp('Koltiva.view.menu.MainFormMenu-form-MenuName').setValue(r.SysMenu.MenuName);
                            Ext.getCmp('Koltiva.view.menu.MainFormMenu-form-MenuModule').setValue(r.SysMenu.MenuModule);
                            Ext.getCmp('Koltiva.view.menu.MainFormMenu-form-MenuIcon').setValue(r.SysMenu.MenuIcon);
                            Ext.getCmp('Koltiva.view.menu.MainFormMenu-form-MenuOrder').setValue(r.SysMenu.MenuOrder);
                            Ext.getCmp('Koltiva.view.menu.MainFormMenu-form-MenuJenis').setValue(r.SysMenu.MenuJenis);
                            Ext.getCmp('Koltiva.view.menu.MainFormMenu-form-MenuParam').setValue(r.SysMenu.MenuParam);
                            
                            if(r.SysMenu.MenuShow=="Yes"){
                                Ext.getCmp('MenuShowYes').setValue(true);
                            }else{
                                Ext.getCmp('MenuShowNo').setValue(true);
                            }

                            Ext.getCmp('ComboSysActDisplays').setValue(r.SysActSelected);

                            if (Ext.getCmp('Koltiva.view.menu.MainFormMenu-form').isValid()) {
                            
                            }else{
                                
                            }
                        }
                     });


                    }else{
                        Ext.getCmp('MenuShowYes').setValue(true);
                    }
                    
                }
            }
        });

        
        
        
        thisObj.items = [
            /*{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id:'Koltiva.view.Traceability.Transaction.Warehouse.FormWarehouseTransaction-title',
               html:'<h3 style="margin:0px 0 7px 0;padding:0px;">'+lang('MENU FORM')+'</h3>',
               style:'font-weight:bold;'
            },{
                id: 'Koltiva.view.Traceability.Transaction.Warehouse.FormWarehouseTransaction-labelInfoInsert',
                html:'',
            }]
        },*/{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to Menu List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        backToList();
                    }
                }
            }
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 1,
                items:[
                    objPanelBasicData
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//
        this.callParent(arguments);
    },
    listeners: {
        afterrender: function(){
            
        }
    }
});