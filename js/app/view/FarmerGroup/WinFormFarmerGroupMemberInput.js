/*
* @Author: nikolius
* @Date:   2017-11-09 15:39:49
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-10 10:36:30
*/

/*
    Param2 yg diperlukan ketika load View ini
    - opsiDisplay
    - Store yg panggil
    - FarmerGroupID
*/

Ext.define('Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput',
    title: lang('Farmer Group Member Input'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '78%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var storeFarmerGroupMemberInputGrid = Ext.create('Koltiva.store.FarmerGroup.FarmerGroupMemberInputGrid');

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-gridInput',
            style: 'border:1px solid #CCC;margin:5px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeFarmerGroupMemberInputGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeFarmerGroupMemberInputGrid,
                dock: 'bottom',
                displayInfo: true,
                style:'padding:4px 12px 4px 4px;'
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/silk/find.png',
                    cls: 'Sfr_BtnGridPaleBlue',
                    text: lang('Advanced Filter'),
                    handler: function () {
                        //advanced search
                        var winAdvFilter = Ext.create('Koltiva.view.FarmerGroup.WinAdvancedFilter');
                        if (!winAdvFilter.isVisible()) {
                            winAdvFilter.center();
                            winAdvFilter.show();
                        } else {
                            winAdvFilter.close();
                        }
                    }
                }]
            }],
            columns: [{
                dataIndex: 'MemberID',
                hidden: true
            },{
                xtype : 'checkcolumn',
                text : '&nbsp;',
                dataIndex : 'chdata',
                width:'6%'
            },{
                text: lang('FarmerID'),
                flex:1,
                dataIndex: 'MemberDisplayID'
            },{
                text: lang('Farmer Name'),
                flex:1,
                dataIndex: 'MemberName'
            },{
                text: lang('Subdistrict'),
                flex:1,
                dataIndex: 'SubDistrict'
            },{
                text: lang('Village'),
                flex:1,
                dataIndex: 'Village'
            },{
                text: lang('Enumerator'),
                flex:1,
                dataIndex: 'Enumerator'
            }]
        }];

        thisObj.buttons = [{
            text: lang('Add Member'),
            id: 'Koltiva.view.FarmerGroup.WinFormFarmerGroupMemberInput-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var records = storeFarmerGroupMemberInputGrid.queryBy(function(record) {
                    return record.get('chdata') === true;
                });
                var ids = [];
                records.each(function(record) {
                    ids.push(record.get('MemberID'));
                });

                if(ids.length > 0){
                    //insert kan ke tabel
                    Ext.Ajax.request({
                        url: m_api + '/farmer_group/farmer_group_member_input',
                        method: 'POST',
                        params: {
                            MemberID: Ext.encode(ids),
                            FarmerGroupID: thisObj.viewVar.FarmerGroupID
                        },
                        success: function(response, o) {
                            var obj = Ext.decode(response.responseText);

                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.viewVar.callerStore.load();

                            //load combo farmer group member
                            var cmb_farmer_group_member = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbFarmerGroupMember');
                            cmb_farmer_group_member.setStoreVar({FarmerGroupID:thisObj.viewVar.FarmerGroupID});
                            cmb_farmer_group_member.load();

                            thisObj.close();
                        },
                        failure: function(response, o){
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: Ext.decode(response.responseText),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });

                }else{
                    Ext.MessageBox.show({
                        title: 'Notifications',
                        msg: 'No item selected',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //load store gridnya
            var store_grid = Ext.data.StoreManager.lookup('Koltiva.store.FarmerGroup.FarmerGroupMemberInputGrid');
            store_grid.setStoreVar({FarmerGroupID:thisObj.viewVar.FarmerGroupID});
            store_grid.load();

            var store_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
            store_province.setStoreVar({FarmerGroupID:thisObj.viewVar.FarmerGroupID});
            store_province.load();
        }
    }
});