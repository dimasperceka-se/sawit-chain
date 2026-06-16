/**
 * @author [Fashah Darullah]
 * @email [fashah.darullah@koltiva.com]
 * @create date 2019-08-21 11:25:59
 * @modify date 2019-08-21 11:25:59
 * @desc [description]
 */

function resetData(){
    Ext.getCmp('Koltiva.view.FarmCloud.FormSelectContact-gridContact-textSearch').setValue('');
    setFilterLs();
}

var storeGridContact = Ext.create('Koltiva.store.FarmCloud.UserManagementGrid');

Ext.define('Koltiva.view.FarmCloud.FormSelectContact' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmCloud.FormSelectContact',
    title: lang('Add New Contact'),
    closable: false,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: '60%',
    overflowY: 'auto',
    formVar: false,
    submitOnEnterGridContact: function(field, event){
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.FarmCloud.FormSelectContact').setFilterLs();
            Ext.getCmp('Koltiva.view.FarmCloud.FormSelectContact-gridContact').getStore().loadPage(1);
        }
    },
    setFilterLs: function(){
        localStorage.setItem('ct_farmer_ls', JSON.stringify({
            opsiCall: 'simple',
            ptextSearch: Ext.getCmp('Koltiva.view.FarmCloud.FormSelectContact-gridContact-textSearch').getValue()
        }));
    },
    setFormVar: function(value){
        this.formVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var ContactRecords = [];
        var OldID = [];
        var OldContactRecords = Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-gridContact').getStore().data.items;
       
        if(OldContactRecords.length > 0){
            Ext.each(OldContactRecords, function (item) {
                OldID.push(item.data.PersonExtID);
                ContactRecords.push({
                    PersonName: item.data.PersonName,
                    Email: item.data.Email,
                    PersonExtID: item.data.PersonExtID,
                    GroupName: item.data.GroupName
                });
            });
        }

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.FarmCloud.FormSelectContact-Form',
            padding:'5 8 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.34,
                            margin:'0 0 0 0',
                            layout:'form',
                            items:[{
                                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                                id: 'Koltiva.view.FarmCloud.FormSelectContact-gridContact-textSearch',
                                xtype: 'textfield',
                                fieldLabel: lang('Name'),
                                labelSeparator: '',
                                labelAlign:'top',
                                emptyText: lang('Find By Name/ID')+', '+lang('Press \'Enter\' to search'),
                                listeners: {
                                    specialkey: thisObj.submitOnEnterGridContact
                                }
                            }]
                        }]
                    }],
                }]
            },{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    style:'padding-right:0px;',
                    layout:'form',
                    items:[{
                        title: 'List of Contacts',
                        xtype: 'grid',
                        id: 'Koltiva.view.FarmCloud.FormSelectContact-gridContact',
                        loadMask: true,
                        store: storeGridContact,
                        viewConfig: {
                            deferEmptyText: false,
                            emptyText: lang('Search Contact by Name')
                        },
                        minHeight:125,
                        selType: 'checkboxmodel',
                        dockedItems: [{
                            xtype: 'pagingtoolbar',
                            id: 'Koltiva.view.FarmCloud.FormSelectContact-gridToolbar',
                            store: storeGridContact,
                            dock: 'bottom',
                            displayInfo: true
                        }],
                        columns: [{
                            text: lang('PersonExtID'),
                            dataIndex: 'PersonExtID',
                            hidden:true
                        },{
                            text: 'Name',
                            dataIndex: 'PersonName',
                            width: '30%'
                        },{
                            text: 'Email',
                            dataIndex: 'Email',
                            width: '35%'
                        },{
                            text: 'Group Name',
                            dataIndex: 'GroupName',
                            width: '35%'
                        }],
                        listeners: {
                            itemclick: function( elm, record, item, index, e, eOpts ) {
                                if(record.data.Email == null){
                                    Ext.MessageBox.show({
                                        title: lang('Error'),
                                        msg: 'Please add email first to this contact data',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            }
                        }
                    }]
                }]
            }],
            listeners: {
                afterrender: function(){
                    if(thisObj.formVar.opsiDisplay == 'insert'){
                        //form reset
                        Ext.getCmp('Koltiva.view.FarmCloud.FormSelectContact-Form').getForm().reset();
                    }
                }
            }
        }];

        thisObj.buttons = [{
            text: 'Save to Recipient List',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.FarmCloud.FormSelectContact-Form-BtnSave',
            handler: function () {
                var formSelectContact = Ext.getCmp('Koltiva.view.FarmCloud.FormSelectContact-Form').getForm();
                if (formSelectContact.isValid()) {
                    var gridContact = Ext.getCmp('Koltiva.view.FarmCloud.FormSelectContact-gridContact');
                    var selected = 
                    gridContact.getSelectionModel().getSelection();
                    Ext.each(selected, function (item) {
                        if(OldID.includes(item.data.PersonExtID) == false){
                            ContactRecords.push({
                                PersonName  : item.data.PersonName,
                                Email       : item.data.Email,
                                PersonExtID : item.data.PersonExtID,
                                GroupName   : item.data.GroupName
                            });
                        }
                        Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-gridContact').getStore().loadData(ContactRecords);
                        thisObj.close();
                    });
                    //refresh store FamLab yg manggil
                    // /*Ext.data.StoreManager.lookup('Koltiva.store.ContactList.Contacts').load();
                    // thisObj.close();*/
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

        this.callParent(arguments);
    },
    listeners: {
        afterrender: function(){
            var thisObj = this;
        }
    }
});