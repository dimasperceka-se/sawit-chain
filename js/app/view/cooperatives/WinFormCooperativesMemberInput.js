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
    - CooperativesID
*/

Ext.define('Koltiva.view.Cooperatives.WinFormCooperativesMemberInput' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Cooperatives.WinFormCooperativesMemberInput',
    title: lang('Cooperatives Member Input'),
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

        var storeCooperativesMemberInputGrid = Ext.create('Koltiva.store.Cooperatives.CooperativesMemberInputGrid');
        storeCooperativesMemberInputGrid.setStoreVar({
            CoopID : thisObj.viewVar.CoopID
        })

        var cmb_enumerator = Ext.create('Koltiva.store.FarmerGroup.CmbEnumerator');

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Cooperatives.WinFormCooperativesMemberInput-gridInput',
            style: 'border:1px solid #CCC;margin:5px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeCooperativesMemberInputGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeCooperativesMemberInputGrid,
                dock: 'bottom',
                displayInfo: true,
                style:'padding:4px 12px 4px 4px;'
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    id: 'Koltiva.view.Cooperatives.WinFormCooperativesMemberInput-gridInput-textSearch',
                    xtype: 'textfield',
                    width: 300,
                    emptyText: lang('Cari berdasar nama/ID')
                },{
                    id: 'Koltiva.view.Cooperatives.WinFormCooperativesMemberInput-gridInput-villageSearch',
                    xtype: 'textfield',
                    width: 250,
                    emptyText: lang('Cari berdasar village')
                },{
                    id: 'Koltiva.view.Cooperatives.WinFormCooperativesMemberInput-gridInput-Enumerator',
                    xtype: 'combobox',
                    store: cmb_enumerator,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    width: 300,
                    emptyText: lang('Enumerator')
                },{
                    xtype: 'button',
                    text: lang('Search'),
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    cls:'Sfr_BtnGridGreen', 
                    overCls:'Sfr_BtnGridGreen-Hover',
                    handler: function() {
                        storeCooperativesMemberInputGrid.setStoreVar({
                            CoopID: thisObj.viewVar.CoopID,
                            textSearch: Ext.getCmp('Koltiva.view.Cooperatives.WinFormCooperativesMemberInput-gridInput-textSearch').getValue(),
                            villageSearch: Ext.getCmp('Koltiva.view.Cooperatives.WinFormCooperativesMemberInput-gridInput-villageSearch').getValue(),
                            Enumerator: Ext.getCmp('Koltiva.view.Cooperatives.WinFormCooperativesMemberInput-gridInput-Enumerator').getValue()
                        });
                        storeCooperativesMemberInputGrid.load();
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
            id: 'Koltiva.view.Cooperatives.WinFormCooperativesMemberInput-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var records = storeCooperativesMemberInputGrid.queryBy(function(record) {
                    return record.get('chdata') === true;
                });
                var ids = [];
                records.each(function(record) {
                    ids.push(record.get('MemberID'));
                });

                if(ids.length > 0){
                    //insert kan ke tabel
                    Ext.Ajax.request({
                        url: m_api + '/cooperatives/coop_member_input',
                        method: 'POST',
                        params: {
                            MemberID: Ext.encode(ids),
                            CoopID: thisObj.viewVar.CoopID
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
                            var cmb_farmer_group_member = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.CmbCooperativesMember');
                            cmb_farmer_group_member.setStoreVar({CoopID:thisObj.viewVar.CoopID});
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
            var store_grid = Ext.data.StoreManager.lookup('Koltiva.store.Cooperatives.CooperativesMemberInputGrid');
            store_grid.setStoreVar({CoopID:thisObj.viewVar.CoopID});
            store_grid.load();
        }
    }
});