/******************************************
 *  Author : nikolius.lau@gmail.com   
 *  Created On : Wed Sep 05 2018
 *  File : WinFormAnnouncement.js
 *******************************************/

/*
Param
- OpsiDisplay
- CallerStore
- AnnID
*/

Ext.define('Koltiva.view.CMS.WinFormAnnouncement' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.CMS.WinFormAnnouncement',
    title: lang('Announcement Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            if(thisObj.viewVar.OpsiDisplay == 'update'){
                //load data form
                Ext.getCmp('Koltiva.view.CMS.WinFormAnnouncement-Form').getForm().load({
                    url: m_api + '/cms/announcement_form_open',
                    method: 'GET',
                    params: {
                        AnnID: thisObj.viewVar.AnnID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);
                    },
                    failure: function(form, action) {
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: lang('Failed to retrieve data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });

            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreCmbPartner = Ext.create('Koltiva.store.ComboGeneral.CmbPartnerCommon');

        //items ------------------------------------------------------------------------------------ (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.CMS.WinFormAnnouncement-Form',
            fileUpload: true,
            padding:'12 30 12 12',            
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    defaults: {
                        labelWidth: 200
                    },                    
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-AnnID',
                        name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-AnnID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-Title',
                        name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-Title',
                        allowBlank: false,
                        fieldLabel: lang('Title')
                    },{
                        xtype: 'htmleditor',
                        id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-Content',
                        name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-Content',
                        fieldLabel: lang('Content'),                        
                        height: 320,
                        padding: '2',
                        enableColors: true,
                        enableAlignments: true,
                        enableSourceEdit: true,
                        enableFont: true,
                        enableFontSize: true,
                        enableFormat: true,
                        enableLinks: true,
                        enableLists: true,
                        allowBlank: false
                    },{
                        html:'<div style="margin-top:5px;"></div>',
                    },{
                        fieldLabel: lang('Status Type'),
                        xtype: 'radiogroup',                        
                        id:'Koltiva.view.CMS.WinFormAnnouncement-Form-RowStatusType',                        
                        columns: 2,
                        allowBlank: false,
                        msgTarget: 'under',
                        items:[{
                            boxLabel: 'Public',
                            name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-StatusType',
                            inputValue: 'public',
                            id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-StatusTypePublic',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: 'Private',
                            name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-StatusType',
                            inputValue: 'private',
                            id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-StatusTypePrivate',
                            listeners:{
                                change: function(){
                                    if(this.checked == true){
                                        Ext.getCmp('Koltiva.view.CMS.WinFormAnnouncement-Form-PartnerIDImplode').setVisible(true);
                                        Ext.getCmp('Koltiva.view.CMS.WinFormAnnouncement-Form-RowRoleAccess').setVisible(true);
                                    }else{
                                        Ext.getCmp('Koltiva.view.CMS.WinFormAnnouncement-Form-PartnerIDImplode').setVisible(false);
                                        Ext.getCmp('Koltiva.view.CMS.WinFormAnnouncement-Form-RowRoleAccess').setVisible(false);
                                    }

                                    //Scroll lagi kebawah
                                    setTimeout(function(){
                                        var d = Ext.getCmp('Koltiva.view.CMS.WinFormAnnouncement').body.dom;
                                        d.scrollTop = d.scrollHeight - d.offsetHeight;
                                    }, 500);

                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'itemselector',
                        flex:true,
                        id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-PartnerIDImplode',
                        name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-PartnerIDImplode',
                        fieldLabel: lang('Partner Access'),
                        fromTitle: lang('Available'),
                        toTitle: lang('Selected'),
                        anchor: '100%',
                        height:300,
                        hidden:true,
                        store: thisObj.StoreCmbPartner,
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        html:'<div style="margin-top:-5px;"></div>',
                    },{
                        fieldLabel: lang('Role Access'),
                        xtype: 'checkboxgroup',                        
                        id:'Koltiva.view.CMS.WinFormAnnouncement-Form-RowRoleAccess',                        
                        hidden:true,
                        columns: 3,
                        items:[{
                            boxLabel: lang('Farmer'),
                            name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-RoleAccessFarmer',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-RoleAccessFarmer',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Trader'),
                            name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-RoleAccessTrader',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-RoleAccessTrader',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Staff'),
                            name: 'Koltiva.view.CMS.WinFormAnnouncement-Form-RoleAccessStaff',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-RoleAccessStaff',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    }]
                }]
            }]
        }];
        //items ------------------------------------------------------------------------------------ (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.CMS.WinFormAnnouncement-Form-BtnSave',
            handler: function () {                
            	var FormNya = Ext.getCmp('Koltiva.view.CMS.WinFormAnnouncement-Form').getForm();

            	if(FormNya.isValid()){                    
            		FormNya.submit({
                        url: m_api + '/cms/announcement_input',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            FormNya.reset();

                            //tutup popup
                            thisObj.close();
                            
                            //refresh store yg manggil
                            thisObj.viewVar.CallerStore.load();                            
                        },
                        failure: function(fp, o){
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
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
            	}else{
            		Ext.MessageBox.show({
                        title: 'Attention',
                        msg: lang('Form not complete yet!'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }                
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});