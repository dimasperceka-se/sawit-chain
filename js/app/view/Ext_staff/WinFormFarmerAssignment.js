/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 06-02-2020
 *  File : WinFormStaff.js
 *******************************************/

/*
 Param2 yg diperlukan ketika load View ini
 - Title 
 - OpsiDisplay
 - CallerStore
 - MemberID
 - PersonalID
 - Role
 - Phonecode
 - Url
 */

 Ext.define('Koltiva.view.Ext_staff.WinFormFarmerAssignment', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Add Farmer Assignment'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: 500,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;

            //form reset
            var FormNya = Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form');
            FormNya.getForm().reset();
            Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffID').setValue(thisObj.viewVar.StaffID);

            if (thisObj.viewVar.Title) {
                Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment').setTitle(thisObj.viewVar.Title);
            }

            if (thisObj.viewVar.OpsiDisplay == 'insert') {
                
            }

            if (thisObj.viewVar.OpsiDisplay == 'update' || thisObj.viewVar.OpsiDisplay == 'view') {
                    //load store gridnya
                var store_grid = Ext.data.StoreManager.lookup('Koltiva.store.Ext_staff.FarmerList');
                store_grid.setStoreVar(
                    {
                        StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                        StaffID: thisObj.viewVar.StaffID
                    }
                );
                store_grid.load();

                if(thisObj.viewVar.OpsiDisplay == 'update'){
                    Ext.getCmp('buttonImportFarmer').setVisible(true);
                }

                if (thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-BtnSave').setVisible(false);
                    Ext.getCmp('buttonAddFarmer').setVisible(false);
                }

                //console.log(thisObj.viewVar.PersonID);
                FormNya.getForm().load({
                    url: m_api+'/ext_staff/farmer_assign_data_form',
                    method: 'GET',
                    params: {
                        StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                    },
                    success: function (form, action) {
                        var r = Ext.decode(action.response.responseText);
                        
                        if(r.data.StatusCode == "active"){
                            Ext.getCmp('buttonImportFarmer').setVisible(true);
                            Ext.getCmp('buttonAddFarmer').setVisible(true);
                            Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-BtnSave').setVisible(true);
                        }else{
                            Ext.getCmp('buttonImportFarmer').setVisible(false);
                            Ext.getCmp('buttonAddFarmer').setVisible(false);
                            Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-BtnSave').setVisible(false);
                        }
                    },
                    failure: function (form, action) {
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
    initComponent: function () {
        var thisObj = this;
        var labelWidth = 150;

        //store
        let cmb_status = Ext.create('Koltiva.store.ComboGeneral.CmbStatusNew');
        var storeFarmerList = Ext.create('Koltiva.store.Ext_staff.FarmerList');

        //ContextMenu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
	        items:[{
	            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite',
	            hidden: m_act_delete,
                itemId: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-grid-ContextMenuDelete',
	            handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-grid').getSelectionModel().getSelection()[0];
                    
                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/ext_staff/supplier',
                                method: 'DELETE',
                                params: {
                                    StaffAssignmentMemberID: sm.get('StaffAssignmentMemberID')
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
                                    storeFarmerList.load();
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

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 1,
                items: [{
                    xtype: 'form',
                    id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form',
                    padding: '10px 20px 10px 20px',
                    items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 0.485,
                            layout: 'form',
                            items: [{
                                xtype: 'textfield',
                                inputType: 'hidden',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffID',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffID'
                            },{
                                xtype: 'textfield',
                                inputType: 'hidden',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffAssignmentID',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffAssignmentID'
                            }, {
                                xtype: 'textfield',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffAssignmentExtID',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffAssignmentExtID',
                                fieldLabel: lang('ID'),
                                readOnly: true,
                                labelWidth: labelWidth
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StartDate',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StartDate',
                                fieldLabel: lang('Start Date'),
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                format: 'Y-m-d'
                            }]
                        }, {
                            columnWidth: 0.5,
                            style: 'padding:10px 0px 10px 20px;',
                            layout: 'form',
                            items: [{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StatusCode',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StatusCode',
                                fieldLabel: lang('Status'),
                                store: cmb_status,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                baseCls: 'Sfr_FormInputMandatory',
                                editable: false,
                                allowBlank: false
                            },{
                                xtype: 'datefield',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-EndDate',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-EndDate',
                                fieldLabel: lang('End Date'),
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                format: 'Y-m-d'
                            },,{
                                xtype: 'textareafield',
                                id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-Description',
                                name: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-Description',
                                fieldLabel: lang('Description')
                            }]
                        }]
                    }]
                },{
                    xtype: 'grid',
                    id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-grid',
                    style: 'margin: 10px 20px 10px 20px; border:1px solid #CCC;',
                    cls: 'Sfr_GridNew',
                    loadMask: true,
                    selType: 'rowmodel',
                    store: storeFarmerList,
                    enableColumnHide: false,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: GetDefaultContentNoData()
                    },
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: storeFarmerList,
                        dock: 'bottom',
                        displayInfo: true,
                        displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/add.png',
                            text: lang('Add Farmer'),
                            hidden: m_act_add,
                            cls: 'Sfr_BtnGridGreen',
                            id:'buttonAddFarmer',
                            overCls: 'Sfr_BtnGridGreen-Hover',
                            handler: function () {
                                var StaffAssignmentID = Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffAssignmentID').getValue();
                                if(StaffAssignmentID == ''){
                                    Ext.MessageBox.show({
                                        title: lang('Error'),
                                        msg: lang('Save Data First'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });

                                    return;
                                }

                                var WinFormFarmerList = Ext.create('Koltiva.view.Ext_staff.WinFormFarmerList', {
                                    viewVar: {
                                        CallerStore: storeFarmerList,
                                        OpsiDisplay: 'insert',
                                        StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                                        StaffID:thisObj.viewVar.StaffID
                                    }
                                });
                                if (!WinFormFarmerList.isVisible()) {
                                    WinFormFarmerList.center();
                                    WinFormFarmerList.show();
                                } else {
                                    WinFormFarmerList.close();
                                }
                            }
                        },{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                            text: lang('Import Farmer'),
                            hidden: true,
                            cls: 'Sfr_BtnGridGreen',
                            id:'buttonImportFarmer',
                            overCls: 'Sfr_BtnGridGreen-Hover',
                            handler: function () {
                                var StaffAssignmentID = Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-StaffAssignmentID').getValue();
                                if(StaffAssignmentID == ''){
                                    Ext.MessageBox.show({
                                        title: lang('Error'),
                                        msg: lang('Save Data First'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });

                                    return;
                                }

                                var WinFormImportFarmer = Ext.create('Koltiva.view.Ext_staff.WinFormImportFarmer', {
                                    viewVar: {
                                        CallerStore: storeFarmerList,
                                        OpsiDisplay: 'insert',
                                        StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                                        StaffID:thisObj.viewVar.StaffID
                                    }
                                });
                                if (!WinFormImportFarmer.isVisible()) {
                                    WinFormImportFarmer.center();
                                    WinFormImportFarmer.show();
                                } else {
                                    WinFormImportFarmer.close();
                                }
                            }
                        }, {
                            xtype: 'tbspacer',
                            flex: 1
                        },{
                            id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-textSearch',
                            xtype: 'textfield',
                            width: 300,
                            emptyText: lang('Search by Name/ID')
                        },{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            cls: 'Sfr_BtnGridGreen',
                            overCls: 'Sfr_BtnGridGreen-Hover',
                            handler: function () {
                                storeFarmerList.setStoreVar({
                                    StaffID:thisObj.viewVar.StaffID,
                                    StaffAssignmentID: thisObj.viewVar.StaffAssignmentID,
                                    textSearch: Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-textSearch').getValue()
                                });
                                storeFarmerList.load();
                            }
                        }]
                    }],
                    columns: [{
                        text: '',
                        xtype: 'actioncolumn',
                        width: '3%',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                            handler: function (grid, rowIndex, colIndex, item, e, record) {
                                thisObj.ContextMenuGrid.showAt(e.getXY());
                            }
                        }]
                    }, {
                        text: 'No',
                        width: '3%',
                        xtype: 'rownumberer'
                    }, {
                        text: lang('StaffAssignmentMemberID'),
                        dataIndex: 'StaffAssignmentMemberID',
                        hidden: true
                    }, {
                        text: lang('MemberID'),
                        dataIndex: 'MemberID',
                        hidden: true
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'MemberDisplayID',
                        flex: 1
                    }, {
                        text: lang('Farmer Name'),
                        dataIndex: 'MemberName',
                        flex: 1
                    }, {
                        text: lang('Gender'),
                        dataIndex: 'Gender',
                        flex: 1
                    }, {
                        text: lang('Province'),
                        dataIndex: 'Province',
                        flex: 1
                    }, {
                        text: lang('District'),
                        dataIndex: 'District',
                        flex: 1
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form-BtnSave',
            handler: function () {
                var FormNya = Ext.getCmp('Koltiva.view.Ext_staff.WinFormFarmerAssignment-Form').getForm();
                if (FormNya.isValid()) {
                    FormNya.submit({
                        url: m_api+'/ext_staff/farmer_assign_data',
                        method: 'POST',
                        params: {
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
                        },
                        waitMsg: 'Saving data...',
                        success: function (fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //refresh store yg manggil
                            thisObj.viewVar.CallerStore.load();

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function (fp, o) {
                            try {
                                var r = Ext.decode(o.response.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            } catch (err) {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Connection Error',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
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
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            text: lang('Close'),
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});