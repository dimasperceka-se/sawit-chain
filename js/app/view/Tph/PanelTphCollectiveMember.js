/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon May 06 2019
 *  File : PanelTphCollectiveMember.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    - CollectpointID
*/

Ext.define('Koltiva.view.Tph.PanelTphCollectiveMember' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Tph.PanelTphCollectiveMember',
    title: lang('Collective TPH Members'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Koltiva.store.Tph.PanelTphCollectiveMemberGrid',{
        	storeVar: {
                CollectpointID: thisObj.viewVar.CollectpointID
            }
        });

        thisObj.ContextMenu = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete this member'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Tph.PanelTphCollectiveMember-MainGrid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/tph/collective_member',
                                method: 'DELETE',
                                params: {
                                    CollectpointID: sm.get('CollectpointID'),
                                    MemberID: sm.get('MemberID')
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
                                
                                    //refresh store
                                    Ext.getCmp('Koltiva.view.Tph.PanelTphCollectiveMember').MainGrid.load();
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
            id: 'Koltiva.view.Tph.PanelTphCollectiveMember-MainGrid',
            loadMask: true,
            minHeight:125,
            selType: 'rowmodel',
            store: thisObj.MainGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add Member'),
                    hidden: m_act_add,
                    handler: function() {

                        //Get List ExceptionID
                        var ArrExceptionID = [];
                        thisObj.MainGrid.each(function(rec){
                            ArrExceptionID.push(rec.get('MemberID'));
                        });
                        var ExceptionID = ArrExceptionID.join(',');
                        //console.log(ExceptionID);

                        var WinSelectMemberGeneralMultiple = Ext.create('Koltiva.view.Widget.WinSelectMemberGeneralMultiple', {
                            viewVar: {
                                ListType: 'farmer',
                                ExceptionID: ExceptionID,
                                CompCaller: Ext.getCmp('Koltiva.view.Tph.PanelTphCollectiveMember')
                            }
                        });
                        if (!WinSelectMemberGeneralMultiple.isVisible()) {
                            WinSelectMemberGeneralMultiple.center();
                            WinSelectMemberGeneralMultiple.show();
                        } else {
                            WinSelectMemberGeneralMultiple.close();
                        }
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                width: '10%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenu.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('CollectpointID'),
                dataIndex: 'CollectpointID',
                hidden:true
            },{
                text: lang('MemberID'),
                dataIndex: 'MemberID',
                hidden:true
            },{
                text: lang('FarmerID'),
                dataIndex: 'MemberDisplayID',
                flex: 2
            },{
                text: lang('Farmer Name'),
                dataIndex: 'MemberName',
                flex: 4
            },{
                text: lang('Age'),
                dataIndex: 'Age',
                flex: 1
            },{
                text: lang('Village'),
                dataIndex: 'Village',
                flex: 2
            }]
        }];

        this.callParent(arguments);
    },
    SelectMemberGeneralMultipleFunc: function(IdSelectedArr){
        var thisObj = this;
        var MemberIDSel = IdSelectedArr.join(',');

        Ext.Ajax.request({
            waitMsg: 'Please Wait',
            url: m_api + '/tph/collective_add_member',
            method: 'POST',
            params: {
                MemberIDSel: MemberIDSel,
                CollectpointID: thisObj.viewVar.CollectpointID
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