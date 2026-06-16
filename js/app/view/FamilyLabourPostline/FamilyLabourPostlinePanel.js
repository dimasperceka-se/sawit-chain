Ext.define('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel',
    title: lang('Family Labour Postline'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible: true,
    collapsed: true,
    margin:'0 0 20 8',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            if (thisObj.viewVar.opsiDisplay == "view") {
                Ext.getCmp('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-BtnAdd').setDisabled(true)
            }
        },
        expand: function() {
            var thisObj = this;
            thisObj.storeGridFamilyLabourPostline.setStoreVar({MemberID:thisObj.viewVar.MemberID});
            thisObj.storeGridFamilyLabourPostline.load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        //store
        thisObj.storeGridFamilyLabourPostline = Ext.create('Koltiva.store.FamilyLabourPostline.GridFamilyLabourPostline');

        thisObj.dockedItems = [{
            xtype: 'pagingtoolbar',
            store: thisObj.storeGridFamilyLabourPostline,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            baseCls: 'bgToolbarTitlePanel',
            dock: 'top',
            items:[{
                xtype: 'tbtext',
                style:'font-weight:bold;text-decoration:underline;',
                text: lang('List of Family Labour Postline')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                id:'Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-BtnAdd',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    let winFormFamilyLabourPostline = Ext.create('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline',{
                        viewVar: {
                            opsiDisplay:'insert',
                            callerStore: thisObj.storeGridFamilyLabourPostline,
                            MemberID:thisObj.viewVar.MemberID,
                            FamLabID:null,
                            FamLabPostID: null
                        }
                    });

                    if (!winFormFamilyLabourPostline.isVisible()) {
                        winFormFamilyLabourPostline.center();
                        winFormFamilyLabourPostline.show();
                    } else {
                        winFormFamilyLabourPostline.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.storeGridFamilyLabourPostline,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                id: 'Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline-ButtonActionGrid',
                width: '10%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        if(Ext.isDefined(Ext.getCmp('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline-contextMenuGridFamilyLabourPostline'))){
                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline-contextMenuGridFamilyLabourPostline').destroy();
                        }

                        thisObj.contextMenuGridFamilyLabourPostline = Ext.create('Ext.menu.Menu',{
                            cls: 'Sfr_ConMenu',
                            id:"Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline-contextMenuGridFamilyLabourPostline",
                            items:[{
                                icon: varjs.config.base_url + 'images/icons/new/view.png',
                                text: lang('View'),
                                handler: function() {
                                    var sm = Ext.getCmp('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline').getSelectionModel().getSelection()[0];

                                    let winFormFamilyLabourPostline = Ext.create('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline',{
                                        viewVar: {
                                            opsiDisplay:'view',
                                            callerStore: thisObj.storeGridFamilyLabourPostline,
                                            MemberID:sm.get('MemberID'),
                                            FamLabID:sm.get('FamLabID'),
                                            FamLabPostID: sm.get('FamLabPostID')
                                        }
                                    });

                                    if (!winFormFamilyLabourPostline.isVisible()) {
                                        winFormFamilyLabourPostline.center();
                                        winFormFamilyLabourPostline.show();
                                    } else {
                                        winFormFamilyLabourPostline.close();
                                    }
                                }
                            },{
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                text: lang('Update'),
                                id: 'Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline-ButtonActionGrid-update',
                                hidden: m_act_update,
                                handler: function() {
                                    var sm = Ext.getCmp('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline').getSelectionModel().getSelection()[0];

                                    let winFormFamilyLabourPostline = Ext.create('Koltiva.view.FamilyLabourPostline.WinFormFamilyLabourPostline',{
                                        viewVar: {
                                            opsiDisplay:'update',
                                            callerStore: thisObj.storeGridFamilyLabourPostline,
                                            MemberID:sm.get('MemberID'),
                                            FamLabID:sm.get('FamLabID'),
                                            FamLabPostID: sm.get('FamLabPostID')
                                        }
                                    });

                                    if (!winFormFamilyLabourPostline.isVisible()) {
                                        winFormFamilyLabourPostline.center();
                                        winFormFamilyLabourPostline.show();
                                    } else {
                                        winFormFamilyLabourPostline.close();
                                    }
                                }
                            },{
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                text: lang('Delete'),
                                id: 'Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline-ButtonActionGrid-delete',
                                hidden: m_act_delete,
                                handler: function(){
                                    var sm = Ext.getCmp('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline').getSelectionModel().getSelection()[0];

                                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please Wait',
                                                url: m_api + '/grower/family_labour_postline',
                                                method: 'DELETE',
                                                params: {
                                                    FamLabPostID: sm.get('FamLabPostID')
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
                                                    thisObj.storeGridFamilyLabourPostline.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                                    thisObj.storeGridFamilyLabourPostline.load();
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
                        });

                        thisObj.contextMenuGridFamilyLabourPostline.showAt(e.getXY());

                        if (thisObj.viewVar.opsiDisplay == "view") {
                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline-ButtonActionGrid-update').setVisible(false)
                            Ext.getCmp('Koltiva.view.FamilyLabourPostline.FamilyLabourPostlinePanel-gridFamilyLabourPostline-ButtonActionGrid-delete').setVisible(false)
                        }
                    }
                }]
            },{
                text: lang('Fam Lab Post ID'),
                dataIndex: 'FamLabPostID',
                hidden:true
            },{
                text: lang('Fam Lab ID'),
                dataIndex: 'FamLabID',
                hidden:true
            },{
                text: lang('MemberID'),
                dataIndex: 'MemberID',
                hidden:true
            },{
                text: lang('Name'),
                dataIndex: 'FamLabName',
                width: '20%'
            },{
                text: lang('Survey Number'),
                dataIndex: 'survey_number',
                width: '12%',
                hidden: true
            },{
                text: lang('Conducting a post-line survey'),
                dataIndex: 'conducting_postline',
                align:'center',
                width: '35%',
                hidden: false,
                renderer: function(value){
                    return lang(value);
                }
            },{
                text: lang('Interview Date'),
                dataIndex: 'FamLabInterviewDate',
                width: '18%'
            },{
                text: lang('Date Created'),
                dataIndex: 'DateCreated',
                width: '25%',
                hidden: false
            },{
                text: lang('Date Updated'),
                dataIndex: 'DateUpdated',
                width: '25%',
                hidden: false
            }]
        }];

        this.callParent(arguments);
    }
});