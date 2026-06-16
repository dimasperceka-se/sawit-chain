Ext.define('Koltiva.view.Traceability.Transaction.TransactionList' ,{
    extend: 'Ext.grid.Panel',
    id: 'Koltiva.view.Traceability.Transaction.TransactionList',
    //title: lang('Agent Staff'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        //var thisObj = this;
        //load store
        //thisObj.storeGridTraderStaff.setStoreVar({MemberID:thisObj.viewVar.MemberID});
        //thisObj.storeGridTraderStaff.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai (begin)
        var storeGridMainTransaction = Ext.create('Koltiva.store.Traceability.Transaction.MainGridTransaction');
        //var cmbStaffRole = Ext.create('Koltiva.store.Traceability.Transaction.ComboStaffRole');
        //store yg dipakai (end)

        /*var contextMenuGrid = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                itemId: 'Koltiva.view.Traceability.Transaction.MainGrid-contextMenuViewItem',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridRegisterStaff').getSelectionModel().getSelection()[0];

                    //buka popup form
                    var WinRegisterStaffForm = Ext.create('Koltiva.view.Traceability.Transaction.WinRegisterStaffForm',{
                        viewVar: {
                            opsiDisplay: 'view',
                            RegID: sm.get('RegID')
                        }
                    });
                    if (!WinRegisterStaffForm.isVisible()) {
                        WinRegisterStaffForm.center();
                        WinRegisterStaffForm.show();
                    } else {
                        WinRegisterStaffForm.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                itemId: 'Koltiva.view.Traceability.Transaction.MainGrid-contextMenuUpdateItem',
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridRegisterStaff').getSelectionModel().getSelection()[0];

                    //buka popup form
                    var WinRegisterStaffForm = Ext.create('Koltiva.view.Traceability.Transaction.WinRegisterStaffForm',{
                        viewVar: {
                            opsiDisplay: 'update',
                            RegID: sm.get('RegID')
                        }
                    });
                    if (!WinRegisterStaffForm.isVisible()) {
                        WinRegisterStaffForm.center();
                        WinRegisterStaffForm.show();
                    } else {
                        WinRegisterStaffForm.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/email.png',
                text: lang('Send Registration Email'),
                itemId: 'Koltiva.view.Traceability.Transaction.MainGrid-contextMenuSendEmailItem',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridRegisterStaff').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', lang('Yakin untuk mengirimkan email registrasi kepada staff yang sudah dipilih ini ?'), function(btn) {
                        if (btn == 'yes') {

                            Ext.MessageBox.show({
                                msg: 'Please wait, Sending Email',
                                progressText: 'sending...',
                                width: 300,
                                wait: true,
                                waitConfig: {
                                    interval: 200
                                },
                                icon: 'ext-mb-download', //custom class in msg-box.html
                                animateTarget: 'mb7'
                            });

                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/basic_staff/register_staff_send_email_registration',
                                method: 'POST',
                                params: {
                                    RegID: sm.get('RegID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.hide();

                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Email Sent'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                                },
                                failure: function(response, opts) {
                                    Ext.MessageBox.hide();

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
                        }
                    });
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                itemId: 'Koltiva.view.Traceability.Transaction.MainGrid-contextMenuDeleteItem',
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridRegisterStaff').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/basic_staff/register_staff',
                                method: 'DELETE',
                                params: {
                                    RegID: sm.get('RegID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store FamLab
                                    //Ext.data.StoreManager.lookup('store.Grower.GridMemberFamilyLabour').load();
                                    storeGridMainTransaction.setStoreVar({
                                        Role: Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-SearchComboRole').getValue(),
                                        StringNameUsername: Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-SearchStringRoleUsername').getValue()
                                    });
                                    storeGridMainTransaction.load()
                                },
                                failure: function(response, opts) {
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
                        }
                    });

                }
            }]
        });*/

        thisObj.items = [{
                columnWidth: 0.5,
                layout: 'form',
                items:[{
                    xtype: 'grid',
                    id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridRegisterStaff',
                    style: 'border:1px solid #CCC;margin-top:4px;',
                    loadMask: true,
                    selType: 'rowmodel',
                    store: storeGridMainTransaction,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar',
                        store: storeGridMainTransaction,
                        dock: 'bottom',
                        displayInfo: true
                    },{
                        xtype: 'toolbar',
                        dock:'top',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            text: lang('Add'),
                            hidden: m_act_add,
                            handler: function() {
                                //buka popup form
                                var WinRegisterStaffForm = Ext.create('Koltiva.view.Traceability.Transaction.WinRegisterStaffForm',{
                                    viewVar: {
                                        opsiDisplay: 'insert'
                                    }
                                });
                                if (!WinRegisterStaffForm.isVisible()) {
                                    WinRegisterStaffForm.center();
                                    WinRegisterStaffForm.show();
                                } else {
                                    WinRegisterStaffForm.close();
                                }
                            }
                        },{
                            xtype:'tbspacer',
                            flex:1
                        },{
                            id: 'Koltiva.view.Traceability.Transaction.MainGrid-SearchComboRole',
                            name: 'Koltiva.view.Traceability.Transaction.MainGrid-SearchComboRole',
                            xtype: 'combo',
                            width: 190,
                            //store: cmbStaffRole,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            selectOnFocus: true,
                            emptyText: lang('Search by Staff Role'),
                        },{
                            name: 'Koltiva.view.Traceability.Transaction.MainGrid-SearchStringRoleUsername',
                            id: 'Koltiva.view.Traceability.Transaction.MainGrid-SearchStringRoleUsername',
                            xtype: 'textfield',
                            width: 300,
                            emptyText: lang('Search by Staff Name / Username')
                        },{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/silk/search.png',
                            margin: '0px 10px 0px 6px',
                            text: lang('Search Staff'),
                            handler: function() {
                                storeGridMainTransaction.setStoreVar({
                                    Role: Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-SearchComboRole').getValue(),
                                    StringNameUsername: Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-SearchStringRoleUsername').getValue()
                                });
                                storeGridMainTransaction.load()
                            }
                        }]
                    }],
                    columns: [{
                        text: lang('Action'),
                        xtype:'actioncolumn',
                        width:'4%',
                        items:[{
                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                            handler: function(grid, rowIndex, colIndex, item, e, record) {
                                /*contextMenuGrid.showAt(e.getXY());

                                var sm = record;
                                if(sm.data.StatusRegistered == "Yes"){
                                    contextMenuGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuUpdateItem').setVisible(false);
                                    contextMenuGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuSendEmailItem').setVisible(false);
                                    contextMenuGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuDeleteItem').setVisible(false);
                                }else{
                                    contextMenuGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuUpdateItem').setVisible(true);
                                    contextMenuGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuSendEmailItem').setVisible(true);
                                    contextMenuGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuDeleteItem').setVisible(true);
                                }*/
                            }
                        }]
                    },{
                        text: 'ID',
                        dataIndex: 'RegID',
                        hidden: true
                    },{
                        text: lang('Email'),
                        dataIndex: 'Email',
                        width:'14%'
                    },{
                        text: lang('Username'),
                        dataIndex: 'Username',
                        width:'14%'
                    },{
                        text: lang('Name'),
                        dataIndex: 'Fullname',
                        width:'17%'
                    },{
                        text: lang('User Role'),
                        dataIndex: 'UserRole',
                        width:'8%'
                    },{
                        text: lang('Association'),
                        dataIndex: 'ObjLabel',
                        width:'15%'
                    },{
                        text: lang('Status Registered'),
                        dataIndex: 'StatusRegistered',
                        width:'8%'
                    },{
                        text: lang('Last Updated'),
                        dataIndex: 'LastUpdatedLabel',
                        width:'17%'
                    }]
                }]
            }];

        this.callParent(arguments);
    }
});


/////////////////////////////

Ext.define('Koltiva.view.Traceability.Transaction.TransactionList' ,{
    extend: 'Ext.grid.Panel',
    id: 'mainGridTransaction',
    width: '99.5%',
    style: 'border:1px solid #CCC;margin:25px 5px 5px 5px;',
    renderTo: 'ext-content',
    loadMask: true,
    title: lang('Initial Data by Subdistrict'),
    selType: 'rowmodel',
    initComponent: function() {
        //var objMainGridSubdistrict = this;

        //store grid
        //objMainGridSubdistrict.store = Ext.create('Koltiva.store.DataAdm.OffData.MainListSubdistrict');

        //this.callParent(arguments);
    },
    features: [{
        ftype: 'groupingsummary',
        groupHeaderTpl: '{name}',
        hideGroupedHeader: true,
        enableGroupingMenu: true,
        showSummaryRow: false,
        startCollapsed: true
    }],
    columns: [{
        text: lang('Region'),
        dataIndex: 'Subdistrict',
        width: '50%'
    },{
        header: lang('District'),
        sortable: true,
        dataIndex: 'DistrictLabel'
    },{
        text: lang('Query Available'),
        dataIndex: 'Query_Available',
        width: '25%',
        renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
            if(value == lang('Ya')){
                metaData.tdAttr = 'style="color:green;font-weight:bold;"';
            }else{
                metaData.tdAttr = 'style="color:red;font-weight:bold;"';
            }
            return value;
        }
    },{
        text: lang('File Available'),
        dataIndex: 'File_Available',
        width: '24.5%',
        renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
            if(value == lang('Ya')){
                metaData.tdAttr = 'style="color:green;font-weight:bold;"';
            }else{
                metaData.tdAttr = 'style="color:red;font-weight:bold;"';
            }
            return value;
        }
    }],
    listeners: {
        itemclick: function(view, record, item, index, e){
            var sm = record.data;

            if(sm.Query_Available == lang('Yes')){
                contextMenuGridSub.setRowData(sm);
                contextMenuGridSub.showAt(e.getXY());

                if(sm.File_Available == lang('No')){
                    Ext.getCmp('Koltiva-view-DataAdm-OffData-ContextMenuGridSubdistrict-download').setVisible(false);
                }else{
                    Ext.getCmp('Koltiva-view-DataAdm-OffData-ContextMenuGridSubdistrict-download').setVisible(true);
                }
            }
        }
    }
});