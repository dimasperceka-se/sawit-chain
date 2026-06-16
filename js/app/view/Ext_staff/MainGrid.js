/******************************************
 *  Author : fashah.darullah@koltiva.com  
 *  Created On : Thu Jan 16 2020
 *  File : MainGrid.js
 *******************************************/
Ext.define('Koltiva.view.Ext_staff.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Ext_staff.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function(component, eOpts){
            var thisObj = this;
            document.getElementById('Sfr_Cont_IdBoxInfoDataGrid').style.display = 'block';
            document.getElementById('Sfr_IdBoxInfoDataGrid').style.display = 'block';
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Store
        thisObj.StoreGridMainExtStaff = Ext.create('Koltiva.store.Ext_staff.MainGrid',{
            storeVar:{
                KeySearch : null
            }
        });

        //ContextMenu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
	        items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                cls:'Sfr_BtnConMenuWhite',
                itemId: 'Koltiva.view.Ext_staff.MainGrid-ContextMenuView',
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-Grid').getSelectionModel().getSelection()[0];
                    Ext.getCmp('Koltiva.view.Ext_staff.MainGrid').destroy(); //destory current view
                    
                    var FormMainFarmer = [];
                    if(Ext.getCmp('Koltiva.view.Ext_staff.MainForm') == undefined){
                        FormMainFarmer = Ext.create('Koltiva.view.Ext_staff.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'view',
                                PersonID: sm.get('PersonID'),
                                StaffID: sm.get('StaffID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Ext_staff.MainForm').destroy();
                        FormMainFarmer = Ext.create('Koltiva.view.Ext_staff.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'view',
                                PersonID: sm.get('PersonID'),
                                StaffID: sm.get('StaffID')
                            }
                        });
                    }
	            }
	        },{
	            icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls:'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                itemId: 'Koltiva.view.Ext_staff.MainGrid-ContextMenuUpdate',
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-Grid').getSelectionModel().getSelection()[0];
                    Ext.getCmp('Koltiva.view.Ext_staff.MainGrid').destroy(); //destory current view
                    
                    var FormMainFarmer = [];
                    if(Ext.getCmp('Koltiva.view.Ext_staff.MainForm') == undefined){
                        FormMainFarmer = Ext.create('Koltiva.view.Ext_staff.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                PersonID: sm.get('PersonID'),
                                StaffID: sm.get('StaffID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Ext_staff.MainForm').destroy();
                        FormMainFarmer = Ext.create('Koltiva.view.Ext_staff.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                PersonID: sm.get('PersonID'),
                                StaffID: sm.get('StaffID')
                            }
                        });
                    }
	            }
	        },{
	            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite',
	            hidden: m_act_delete,
                itemId: 'Koltiva.view.Ext_staff.MainGrid-ContextMenuDelete',
	            handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-Grid').getSelectionModel().getSelection()[0];
                    
                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/staffuser/staff_data',
                                method: 'DELETE',
                                params: {
                                    PersonID: sm.get('PersonID'),
                                    StaffID: sm.get('StaffID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.StoreGridMainExtStaff.load();
                                },
                                failure: function(rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                    catch(err) {
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: lang('Connection Error'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
                            });
                            
                        }
                    });

	            }
	        }]
	    });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Ext_staff.MainGrid-Grid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMainExtStaff,
            enableColumnHide: false,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMainExtStaff,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: lang('Showing')+' {0} '+lang('to')+' {1} '+lang('of')+' {2} '+lang('data')
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    hidden: true, //di non aktifkan disini
                    cls:'Sfr_BtnGridGreen',
                    overCls:'Sfr_BtnGridGreen-Hover',
                    id: 'Koltiva.view.Ext_staff.MainGrid-BtnAdd',
                    handler: function() {
                        Ext.getCmp('Koltiva.view.Ext_staff.MainGrid').destroy(); //destory current view
                    	var FormMainFarmer = [];

                        //create object View untuk FormMainGrower
                        if(Ext.getCmp('Koltiva.view.Ext_staff.MainForm') == undefined){
                            FormMainFarmer = Ext.create('Koltiva.view.Ext_staff.MainForm', {
                            	viewVar: {
                                    OpsiDisplay: 'insert',
                                    PanelDisplayID: null
		                        }
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Ext_staff.MainForm').destroy();
                            FormMainFarmer = Ext.create('Koltiva.view.Ext_staff.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'insert',
                                    PanelDisplayID: null
		                        }
                            });
                        }
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },
                {
                    name: 'Koltiva.view.Ext_staff.MainGrid-TxtSearchNama',
                    id: 'Koltiva.view.Ext_staff.MainGrid-TxtSearchNama',
                    xtype: 'textfield',
                    baseCls: 'Sfr_TxtfieldSearchGrid',
                    width: 400,
                    emptyText: lang('Search by Name'),
                    listeners: {
                        specialkey: thisObj.submitOnEnterGrid
                    }
                }, {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                    text: lang('Search'),
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    handler: function () {
                        console.log(Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-TxtSearchNama').getValue());
                        thisObj.StoreGridMainExtStaff.storeVar.KeySearch = Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-TxtSearchNama').getValue();
                        thisObj.StoreGridMainExtStaff.loadPage(1);
                    }
                },
                {
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    id: 'Koltiva.view.Ext_staff.MainGrid-BtnReload',
                    handler: function() {
                        Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-Grid').getStore().loadPage(1);
                    }
                }]
            }],
            columns:[{
            	text: '',
                xtype:'actioncolumn',
                flex: 5,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('PersonID'),
                dataIndex: 'PersonID',
                hidden: true
            },{
                text: lang('StaffID'),
                dataIndex: 'StaffID',
                hidden: true
            },{
                text: lang('Name'),
                dataIndex: 'PersonNm',
                flex: 20
            },{
                text: lang('User Name'),
                dataIndex: 'UserName',
                flex: 20
            },{
                text: lang('Gender'),
                dataIndex: 'Gender',
                flex: 7.5,
                renderer: function (value) {
                    var RetVal;

                    if(value != null && value != ''){
                        switch(value){
                            case 'm':
                                RetVal = lang('Male');
                            break;
                            case 'f':
                                RetVal = lang('Female');
                            break;
                            case 'o':
                                RetVal = lang('Other');
                            break;
                            default:
                                RetVal = '-';
                            break;
                        }
                    }else{
                        RetVal = '-';
                    }

                    return RetVal;
                }
            },{
                text: lang('Province'),
                dataIndex: 'Province',
                flex: 20
            },{
                text: lang('District'),
                dataIndex: 'District',
                flex: 20
            },{
               text: lang('Role'),
               dataIndex: 'StaffPositionLabel',
               flex: 15
            },{
                text: lang('User Created'),
                dataIndex: 'UserCreated',
                flex: 10,
                renderer: function (t, meta, record) {
                    let LabelUser;
                    if(record.data.UserCreated == '1') LabelUser = lang('Yes');
                    if(record.data.UserCreated == '2') LabelUser = lang('No');
                    
                    return LabelUser;
                }
            },{
                text: lang('Reference Staff'),
                dataIndex: 'ReferenceStaff',
                flex: 15
            },{
                text: lang('Modified By'),
                dataIndex: 'ModifiedBy',
                flex: 15
            }]
        }];

        this.callParent(arguments);
    },
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-Grid').getStore().storeVar.KeySearch = Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-TxtSearchNama').getValue();
            Ext.getCmp('Koltiva.view.Ext_staff.MainGrid-Grid').getStore().loadPage(1);
        }
    }
});
function testJSON(text){
    try{
        JSON.parse(text);
        return true;
    }
    catch (error){
        return false;
    }
}