Ext.define('Koltiva.view.UserAffiliate.AffiliatedGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.UserAffiliate.AffiliatedGrid',
    title: lang('Affiliated'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: false,
    collapsible: true,
    margin:'5 5 5 5',
    initComponent: function() {
        var thisObj = this;
        
        thisObj.MainGrid = Ext.create('Koltiva.store.UserAffiliate.AffiliatedGrid',{
         storeVar: {
                UserId: thisObj.viewVar.UserId
            }
        });

        thisObj.ContextMenu = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: false,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.UserAffiliate.AffiliatedGrid-MainGrid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/system/user_affiliated',
                                method: 'DELETE',
                                params: {
                                    UserId: thisObj.viewVar.UserId,
                                    UserIdAff: sm.get('UserId')
                                },
                                success: function(rp, o) {
                                    var r = Ext.decode(rp.responseText);
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                                
                                    Ext.getCmp('Koltiva.view.UserAffiliate.AffiliatedGrid').MainGrid.load();
                                },
                                failure: function(rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                    catch(err) {
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
                        }
                    });
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.UserAffiliate.AffiliatedGrid-MainGrid',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.MainGrid,
            overflowY: 'scroll',
            maxHeight: 500,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock:'bottom',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add Affiliate'),
                    hidden: !thisObj.viewVar.IsUpdate,
                    handler: function() {
                        var AffiliateWindow = Ext.create('Koltiva.view.UserAffiliate.AffiliateWindow', {
                            viewVar: {
                                UserId: thisObj.viewVar.UserId,
                                Caller: Ext.getCmp('Koltiva.view.UserAffiliate.AffiliatedGrid')
                            }
                        });
                        if (!AffiliateWindow.isVisible()) {
                            AffiliateWindow.center();
                            AffiliateWindow.show();
                        } else {
                            AffiliateWindow.close();
                        }
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                width: '5%',
                hidden: !thisObj.viewVar.IsUpdate,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenu.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('UserId'),
                dataIndex: 'UserId',
                hidden:true
            },{
                text: lang('Real Name'),
                dataIndex: 'UserRealName',
                flex: 2
            },{
                text: lang('Username'),
                dataIndex: 'UserName',
                flex: 2
            },{
                text: lang('Active'),
                dataIndex: 'UserActive',
                flex: 2
            }]
        }];

        this.callParent(arguments);
    },
    AddAffiliates: function(IdSelectedArr){
        var thisObj = this;
        var UserIds = IdSelectedArr.join(',');

        Ext.Ajax.request({
            waitMsg: 'Please Wait',
            url: m_api + '/system/user_affiliate',
            method: 'POST',
            params: {
                UserId: thisObj.viewVar.UserId,
                UserIds: UserIds
            },
            success: function(rp, o) {
                var r = Ext.decode(rp.responseText);
                Ext.MessageBox.show({
                    title: 'Information',
                    msg: r.message,
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-success'
                });

                //Store load
                thisObj.MainGrid.load();
            },
            failure: function(rp, o) {
                try {
                    var r = Ext.decode(rp.responseText);
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: r.message,
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
                catch(err) {
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
    }
});