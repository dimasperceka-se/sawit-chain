/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Wed May 27 2020
 *  File : GridFarmGateRelation.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    - PersonID
*/


function setFilterLs() {
    localStorage.setItem('patchouli_grower_ls', JSON.stringify({
        key: Ext.getCmp('Koltiva.view.Staffuser.GridFarmGateRelation-textSearch').getValue()
    }));
}
    
function submitOnEnterGridGrower(field, event) {
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('Koltiva.view.Staffuser.GridFarmGateRelation-MainGrid').getStore().loadPage(1);
    }
}

function displayFormStaffRelation(displayMethod,StaffID,viewOnly,StaffRelID) {
    var cmb_sme_relation = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/basic_staff/list_sme_staff',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
        }
    });

    cmb_sme_relation.load();

    var winFormStaffRelation = Ext.create('widget.window', {
        title: lang('Form FarmGate Staff Relation'),
        id: 'winFormStaffRelation',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '40%',
        height: '60%',
        overflowY: 'auto',
        bodyStyle:{"background-color":"#F0F0F0"},
        style:'background-color:#F0F0F0;',
        padding:6,
        scrollOffset: 20,
        items:[{
            xtype: 'form',
            id: 'winFormDataStaffRelation',
            fileUpload: true,
            padding:'5 20 5 8',
            items:[{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 0.49,
                    padding: 4,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'StaffRelID',
                        name: 'StaffRelID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'StaffID',
                        name: 'StaffID',
                        value:StaffID
                    },{
                        xtype: 'combobox',
                        fieldLabel: lang('Buying Unit'),
                        allowBlank: false,
                        store: cmb_sme_relation,
                        queryMode: 'local',
                        displayField: 'label',
                        labelAlign:'top',
                        valueField: 'id',
                        id: 'SupplychainID',
                        name: 'SupplychainID'
                    }]
                },{
                    columnWidth: 0.49,
                    padding: 4,
                    layout:'form',
                    items:[{
                        xtype: 'datefield',
                        fieldLabel: lang('Start Date'),
                        labelAlign:'top',
                        id: 'StartDate',
                        name: 'StartDate',
                        format:'Y-m-d'
                    },{
                        xtype: 'datefield',
                        fieldLabel: lang('End Date'),
                        labelAlign:'top',
                        id: 'EndDate',
                        name: 'EndDate',
                        format:'Y-m-d'
                    }]
                }]
            }]
        }],
        buttons:[{
            id: 'saveButtonRelation',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = Ext.getCmp('winFormDataStaffRelation').getForm();

                if (form.isValid()) {
                    form.submit({
                        url: m_api + '/traceability_api/supplychain_staff/submit',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved');
                            winFormStaffRelation.close();
                            Ext.getCmp('Koltiva.view.Staffuser.GridFarmGateRelation-MainGrid').getStore().loadPage(1)
                        },
                        failure: function(fp, o){
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Failed to save data',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Please fill the required field',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            id: 'winBtnCloseRelation',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winFormStaffRelation.close();
            }
        }]
    });

    
    if(displayMethod == 'update' || displayMethod == 'view'){
        Ext.getCmp('winFormDataStaffRelation').getForm().load({
            url: m_api + '/traceability_api/supplychain_staff/data',
            method: 'GET',
            params: {
                StaffRelID: StaffRelID
            },
            success: function(form, action) {
                console.log(action.response.responseText);
                var r = Ext.decode(action.response.responseText);
            }
        });
    }
    

    //show windows
    if (!winFormStaffRelation.isVisible()) {
        winFormStaffRelation.show();
    } else {
        winFormStaffRelation.close();
    }
}

Ext.define('Koltiva.view.Staffuser.GridFarmGateRelation' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Staffuser.GridFarmGateRelation',
    style:'margin-top:15px;',
    title:lang('FarmGate Staff Relation'),
    frame: true,
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['StaffRelID', 'MemberDisplayID','Name','StartDate','EndDate'],
            pageSize: 50,
            remoteSort: true,
            autoLoad:true,
            proxy: {
                type: 'ajax',
                url: m_api + '/traceability_api/supplychain_staff/fetch',
                params: {
                    'X-API-KEY': '030584'
                },
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    var key;
                    var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
                    if(patchouli_grower_ls != null){
                        key        = patchouli_grower_ls.key;
                    }else{
                        key        = "";
                    }
                    store.proxy.extraParams.key = Ext.getCmp('Koltiva.view.Staffuser.GridFarmGateRelation-textSearch').getValue();
                    store.proxy.extraParams.StaffID = thisObj.viewVar.StaffID;
                }
            }
        });
    
        thisObj.contextMenuGridRelation = Ext.create('Ext.menu.Menu', {
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Staffuser.GridFarmGateRelation-MainGrid').getSelectionModel().getSelection()[0];

                    displayFormStaffRelation('update',thisObj.viewVar.StaffID,true,sm.get('StaffRelID'));
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Staffuser.GridFarmGateRelation-MainGrid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/supplychain_staff/data',
                                method: 'DELETE',
                                params: {
                                    StaffRelID: sm.get('StaffRelID')
                                },
                                success: function (response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
        
                                    //refresh store
                                    setFilterLs();
                                    Ext.getCmp('Koltiva.view.Staffuser.GridFarmGateRelation-MainGrid').getStore().load();
                                },
                                failure: function (response, opts) {
                                    var pesanNya;
                                    if (o.result.message != undefined) {
                                        pesanNya = o.result.message;
                                    } else {
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
            }, ]
        });

        thisObj.items = [{
        	xtype:'grid',
            id: 'Koltiva.view.Staffuser.GridFarmGateRelation-MainGrid',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.MainGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Staffuser.GridFarmGateRelation-gridToolbar',
                store: thisObj.MainGrid,
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png',
                        cls: 'Sfr_BtnGridGreen',
                        overCls: 'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        handler: function () {
                            displayFormStaffRelation('add',thisObj.viewVar.StaffID,true,'');
                        }
                    },
                    {
                        name: 'key',
                        baseCls: 'Sfr_TxtfieldSearchGrid',
                        id: 'Koltiva.view.Staffuser.GridFarmGateRelation-textSearch',
                        xtype: 'textfield',
                        width: 400,
                        emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('Press \'Enter\' to search'),
                        minLength: 3,
                        msgTarget: 'side',
                        listeners: {
                            specialkey: submitOnEnterGridGrower,
                        }
                    },{
                        xtype: 'tbspacer',
                        flex: 0.5
                    }
                ]
            }],
            columns: [{
                text: lang('Action'),
                xtype: 'actioncolumn',
                width: '10%',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.contextMenuGridRelation.showAt(e.getXY());
                    }
                }]
            }, {
                id: 'view.basic.staff.relation-StaffRelID',
                text: lang('ID'),
                dataIndex: 'StaffRelID',
                hidden: true
            },{
                id: 'view.basic.staff.relation-MemberDisplayID',
                text: lang('ID'),
                flex:1,
                dataIndex: 'MemberDisplayID',
            },{
                id: 'view.basic.staff.relation-Name',
                text: lang('Name'),
                flex:1,
                dataIndex: 'Name',
            }, {
                id: 'view.basic.staff.relation-StartDate',
                text: lang('Start Date'),
                flex:1,
                dataIndex: 'StartDate'
            }, {
                id: 'view.basic.staff.relation-EndDate',
                text: lang('End Date'),
                flex:1,
                dataIndex: 'EndDate'
            }]
        }];

        this.callParent(arguments);
    }
});