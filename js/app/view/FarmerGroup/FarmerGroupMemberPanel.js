/*
* @Author: nikolius
* @Date:   2017-11-09 13:53:41
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-10 10:58:03
*/

/*
    Param2 yg diperlukan ketika load View ini
    - FarmerGroupID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.FarmerGroup.FarmerGroupMemberPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmerGroup.FarmerGroupMemberPanel',
    title: lang('Farmer Group Member'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeFarmerGroupMember = Ext.create('Koltiva.store.FarmerGroup.FarmerGroupMemberPanelGrid');

        var contextMenuGridFarmerGroupMemberPanel = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View Profile'),
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.FarmerGroup.FarmerGroupMemberPanelGrid-gridPanel').getSelectionModel().getSelection()[0];
                    preview_cetak_surat(m_url_farmer_profile + '/MemberID/' + sm.get('MemberID'));
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.FarmerGroup.FarmerGroupMemberPanelGrid-gridPanel').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/farmer_group/farmer_group_member',
                                method: 'DELETE',
                                params: {
                                    FarmerGroupID: sm.get('FarmerGroupID'),
                                    MemberID: sm.get('MemberID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    storeFarmerGroupMember.load();

                                    //load combo farmer group member
                                    var cmb_farmer_group_member = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbFarmerGroupMember');
                                    cmb_farmer_group_member.setStoreVar({FarmerGroupID:thisObj.viewVar.FarmerGroupID});
                                    cmb_farmer_group_member.load();
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

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.FarmerGroup.FarmerGroupMemberPanelGrid-gridPanel',
            loadMask: true,
            minHeight:125,
            selType: 'rowmodel',
            store: storeFarmerGroupMember,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeFarmerGroupMember,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add Member'),
                    hidden: m_act_add,
                    handler: function() {
                        var WinFormFarmerGroupMemberInput = Ext.create('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput');
                        WinFormFarmerGroupMemberInput.setViewVar({
                            opsiDisplay:'insert',
                            callerStore: storeFarmerGroupMember,
                            FarmerGroupID:thisObj.viewVar.FarmerGroupID
                        });
                        if (!WinFormFarmerGroupMemberInput.isVisible()) {
                            WinFormFarmerGroupMemberInput.center();
                            WinFormFarmerGroupMemberInput.show();
                        } else {
                            WinFormFarmerGroupMemberInput.close();
                        }
                    }
                },{
                    xtype: 'splitbutton',
                    text: lang('Export'),
                    icon: varjs.config.base_url + 'images/icons/new/export.png',
                    cls: 'Sfr_BtnGridPaleBlue',
                    cls: 'Sfr_BtnGridPaleBlue',
                    handler: function () {
                        Ext.MessageBox.show({
                            msg: 'Please wait...',
                            progressText: 'Exporting...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-download', //custom class in msg-box.html
                            animateTarget: 'mb7'
                        });
                        
                        var param_string = '?FarmerGroupID='+thisObj.viewVar.FarmerGroupID;

                        Ext.Ajax.request({
                            url: m_api + '/farmer_group/export_farmers/' + param_string,

                            method: 'GET',
                            waitMsg: lang('Please Wait'),
                            timeout: 360000,
                            success: function (data) {
                                Ext.MessageBox.hide();
                                var jsonResp = JSON.parse(data.responseText);
                                window.location = jsonResp.filenya;
                            },
                            failure: function () {
                                Ext.MessageBox.hide();
                                Ext.MessageBox.show({
                                    title: 'Notifications',
                                    msg: 'Failed to export, Please try again.',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });

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
                        contextMenuGridFarmerGroupMemberPanel.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('FarmerGroupID'),
                dataIndex: 'FarmerGroupID',
                hidden:true
            },{
                text: lang('MemberID'),
                dataIndex: 'MemberID',
                hidden:true
            },{
                text: lang('FarmerID'),
                dataIndex: 'MemberDisplayID',
                flex: 1
            },{
                text: lang('Farmer Name'),
                dataIndex: 'MemberName',
                flex: 1
            },{
                text: lang('Village'),
                dataIndex: 'Village',
                flex: 1
            },{
                text: lang('Enumerator'),
                dataIndex: 'Enumerator',
                flex: 1
            }]
        }];

        this.callParent(arguments);
    }
});