Ext.define('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel',
    title: lang('Farmer Labour Postline'),
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
                Ext.getCmp('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-BtnAdd').setDisabled(true)
            }
        },
        expand: function() {
            var thisObj = this;
            thisObj.storeGridFarmerLabourPostline.setStoreVar({MemberID:thisObj.viewVar.MemberID});
            thisObj.storeGridFarmerLabourPostline.load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        //store
        thisObj.storeGridFarmerLabourPostline = Ext.create('Koltiva.store.FarmerLabourPostline.GridFarmerLabourPostline');

        thisObj.dockedItems = [{
            xtype: 'pagingtoolbar',
            store: thisObj.storeGridFarmerLabourPostline,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            baseCls: 'bgToolbarTitlePanel',
            dock: 'top',
            items:[{
                xtype: 'tbtext',
                style:'font-weight:bold;text-decoration:underline;',
                text: lang('List of Farmer Labour Postline')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                id:'Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-BtnAdd',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    let winFormFarmerLabourPostline = Ext.create('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline',{
                        viewVar: {
                            opsiDisplay:'insert',
                            callerStore: thisObj.storeGridFarmerLabourPostline,
                            MemberID:thisObj.viewVar.MemberID,
                            LaboID:null,
                            LaboPostID: null
                        }
                    });

                    if (!winFormFarmerLabourPostline.isVisible()) {
                        winFormFarmerLabourPostline.center();
                        winFormFarmerLabourPostline.show();
                    } else {
                        winFormFarmerLabourPostline.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.storeGridFarmerLabourPostline,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                id: 'Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline-ButtonActionGrid',
                width: '10%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        if(Ext.isDefined(Ext.getCmp('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline-contextMenuGridFarmerLabourPostline'))){
                            Ext.getCmp('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline-contextMenuGridFarmerLabourPostline').destroy();
                        }

                        thisObj.contextMenuGridFarmerLabourPostline = Ext.create('Ext.menu.Menu',{
                            cls: 'Sfr_ConMenu',
                            id:"Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline-contextMenuGridFarmerLabourPostline",
                            items:[{
                                icon: varjs.config.base_url + 'images/icons/new/view.png',
                                text: lang('View'),
                                handler: function() {
                                    var sm = Ext.getCmp('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline').getSelectionModel().getSelection()[0];

                                    let winFormFarmerLabourPostline = Ext.create('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline',{
                                        viewVar: {
                                            opsiDisplay:'view',
                                            callerStore: thisObj.storeGridFarmerLabourPostline,
                                            MemberID:sm.get('MemberID'),
                                            LaboID:sm.get('LaboID'),
                                            LaboPostID: sm.get('LaboPostID')
                                        }
                                    });

                                    if (!winFormFarmerLabourPostline.isVisible()) {
                                        winFormFarmerLabourPostline.center();
                                        winFormFarmerLabourPostline.show();
                                    } else {
                                        winFormFarmerLabourPostline.close();
                                    }
                                }
                            },{
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                text: lang('Update'),
                                id: 'Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline-ButtonActionGrid-update',
                                hidden: m_act_update,
                                handler: function() {
                                    var sm = Ext.getCmp('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline').getSelectionModel().getSelection()[0];

                                    let winFormFarmerLabourPostline = Ext.create('Koltiva.view.FarmerLabourPostline.WinFormFarmerLabourPostline',{
                                        viewVar: {
                                            opsiDisplay:'update',
                                            callerStore: thisObj.storeGridFarmerLabourPostline,
                                            MemberID:sm.get('MemberID'),
                                            LaboID:sm.get('LaboID'),
                                            LaboPostID: sm.get('LaboPostID')
                                        }
                                    });

                                    if (!winFormFarmerLabourPostline.isVisible()) {
                                        winFormFarmerLabourPostline.center();
                                        winFormFarmerLabourPostline.show();
                                    } else {
                                        winFormFarmerLabourPostline.close();
                                    }
                                }
                            },{
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                text: lang('Delete'),
                                id: 'Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline-ButtonActionGrid-delete',
                                hidden: m_act_delete,
                                handler: function(){
                                    var sm = Ext.getCmp('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline').getSelectionModel().getSelection()[0];

                                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please Wait',
                                                url: m_api + '/grower/labour_postline',
                                                method: 'DELETE',
                                                params: {
                                                    LaboPostID: sm.get('LaboPostID')
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
                                                    thisObj.storeGridFarmerLabourPostline.setStoreVar({MemberID:thisObj.viewVar.MemberID});
                                                    thisObj.storeGridFarmerLabourPostline.load();
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

                        thisObj.contextMenuGridFarmerLabourPostline.showAt(e.getXY());

                        if (thisObj.viewVar.opsiDisplay == "view") {
                            Ext.getCmp('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline-ButtonActionGrid-update').setVisible(false)
                            Ext.getCmp('Koltiva.view.FarmerLabourPostline.FarmerLabourPostlinePanel-gridFarmerLabourPostline-ButtonActionGrid-delete').setVisible(false)
                        }
                    }
                }]
            },{
                text: lang('Labo Post ID'),
                dataIndex: 'LaboPostID',
                hidden:true
            },{
                text: lang('Labo ID'),
                dataIndex: 'LaboID',
                hidden:true
            },{
                text: lang('MemberID'),
                dataIndex: 'MemberID',
                hidden:true
            },{
                text: lang('Name'),
                dataIndex: 'LaboName',
                width: '20%'
            },{
                text: lang('Survey Number'),
                dataIndex: 'survey_number',
                width: '12%',
                hidden: true
            },{
                text: lang('Conducting a post-line survey'),
                dataIndex: 'ConductingPostline',
                align:'center',
                width: '35%',
                hidden: false,
                renderer: function(value){
                    return lang(value);
                }
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