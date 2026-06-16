/*
* @Author: nikolius
* @Date:   2017-07-18 17:29:35
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-06 15:46:41
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

    var contextMenuGridTrader = Ext.create('Ext.menu.Menu',{
        items:[{
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('View'),
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.Trader.GridMainTrader').destroy(); //destory current view
                //create object View untuk FormMainTrader
                if(Ext.getCmp('Koltiva.view.Trader.FormMainTrader') == undefined){
                    var FormMainTrader = Ext.create('Koltiva.view.Trader.FormMainTrader', {
                        opsiDisplay: 'view',
                        viewVar: {
                            MemberID: sm.get('MemberIDInc')
                        }
                    });
                }else{
                    //destroy, create ulang
                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader').destroy();
                    var FormMainTrader = Ext.create('Koltiva.view.Trader.FormMainTrader', {
                        opsiDisplay: 'view',
                        viewVar: {
                            MemberID: sm.get('MemberIDInc')
                        }
                    });
                }

            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.Trader.GridMainTrader').destroy(); //destory current view
                //create object View untuk FormMainTrader
                if(Ext.getCmp('Koltiva.view.Trader.FormMainTrader') == undefined){
                    var FormMainTrader = Ext.create('Koltiva.view.Trader.FormMainTrader', {
                        opsiDisplay: 'update',
                        viewVar: {
                            MemberID: sm.get('MemberIDInc')
                        }
                    });
                }else{
                    //destroy, create ulang
                    Ext.getCmp('Koltiva.view.Trader.FormMainTrader').destroy();
                    var FormMainTrader = Ext.create('Koltiva.view.Trader.FormMainTrader', {
                        opsiDisplay: 'update',
                        viewVar: {
                            MemberID: sm.get('MemberIDInc')
                        }
                    });
                }

            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: m_act_delete,
            handler: function(){
                var sm = Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/trader_mem/member',
                            method: 'DELETE',
                            params: {
                                MemberID: sm.get('MemberIDInc')
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
                                setFilterLs();
                                Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getStore().load();
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

    function setFilterLs() {
        localStorage.setItem('patchouli_trader_ls',
            JSON.stringify({
                ptextSearch: Ext.getCmp('Koltiva.view.Trader.GridMainTrader-textSearch').getValue(),
                pCmbRoleSearch: Ext.getCmp('Koltiva.view.Trader.GridMainTrader-CmbRoleSearch').getValue()
            })
        );
    }

    function submitOnEnterGridTrader(field, event) {
        if (event.getKey() == event.ENTER) {
            setFilterLs();
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getStore().loadPage(1);
        }
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Trader.GridMainTrader' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Trader.GridMainTrader',
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';

            //isikan variabel dari local storage
            var patchouli_trader_ls = JSON.parse(localStorage.getItem('patchouli_trader_ls'));
            if(patchouli_trader_ls != null){
                Ext.getCmp('Koltiva.view.Trader.GridMainTrader-textSearch').setValue(patchouli_trader_ls.ptextSearch);
            }

            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getStore().load();
        }
    },

    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridMain = Ext.create('Koltiva.store.Trader.GridMain');
        var cmb_role_trader = Ext.create('Koltiva.store.Trader.CmbRoleTrader');

        //items
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.3,
                layout: 'form',
                items:[{
                    xtype: 'button',
                    id: 'Koltiva.view.Trader.GridMainTrader-btnListView',
                    style:'margin-top:5px;',
                    text: lang('All SME'),
                    arrowAlign: 'right',
                    menu : [{
                        text: lang('All SME'),
                        listeners: {
                            click: function(){
                                alert('All SME Click')
                            }
                        }
                    },{
                        text: lang('New This Week'),
                        listeners: {
                            click: function(){
                                alert('New This Week Click')
                            }
                        }
                    },{
                        text: lang('Modified This Week'),
                        listeners: {
                            click: function(){
                                alert('Modified This Week Click')
                            }
                        }
                    },{
                        text: lang('Recently Views'),
                        listeners: {
                            click: function(){
                                alert('Recently Views Click')
                            }
                        }
                    }]
                }]
            },{
                columnWidth: 0.7,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.Trader.GridMainTrader-gridInformation',
                html: ''
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.Trader.GridMainTrader-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            minHeight:125,
            store: storeGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Trader.GridMainTrader-gridToolbar',
                store: storeGridMain,
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
                        Ext.getCmp('Koltiva.view.Trader.GridMainTrader').destroy(); //destory current view

                        //create object View untuk FormMainTrader
                        if(Ext.getCmp('Koltiva.view.Trader.FormMainGrower') == undefined){
                            var FormMainTrader = Ext.create('Koltiva.view.Trader.FormMainTrader', {
                                opsiDisplay: 'insert'
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Trader.FormMainTrader').destroy();
                            var FormMainTrader = Ext.create('Koltiva.view.Trader.FormMainTrader', {
                                opsiDisplay: 'insert'
                            });
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                    text: lang('Export'),
                    hidden: m_act_export,
                    handler: function() {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('Coming Soon!'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                },{
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.Trader.GridMainTrader-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                    listeners: {
                        specialkey: submitOnEnterGridTrader
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    id: 'Koltiva.view.Trader.GridMainTrader-CmbRoleSearch',
                    xtype: 'combobox',
                    store: cmb_role_trader,
                    emptyText: lang('Filter Role'),
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    multiSelect: true,
                    width: 300,
                    listeners: {
                        change: function(cb, nv, ov) {
                            setFilterLs();
                            Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getStore().loadPage(1);
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        //reload
                        setFilterLs();
                        Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridMainGrid').getStore().loadPage(1);
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/silk/grid.png', cls:'Sfr_BtnGridPaleBlue',
                    handler: function() {
                        //display field grid
                        var winGridDisplay = Ext.create('Koltiva.view.Trader.WinGridDisplay');
                        if (!winGridDisplay.isVisible()) {
                            winGridDisplay.center();
                            winGridDisplay.show();
                        } else {
                            winGridDisplay.close();
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/silk/find.png', cls:'Sfr_BtnGridPaleBlue',
                    handler: function() {
                        //advanced search
                        var winAdvFilter = Ext.create('Koltiva.view.Trader.WinAdvancedFilter');
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
                text: lang('Action'),
                xtype:'actioncolumn',
                width:70,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridTrader.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'MemberIDInc',
                hidden:true
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colid',
                text: lang('SME ID'),
                dataIndex: 'id',
                width: '10%'
            }, {
                id: 'Koltiva.view.Trader.GridMainTrader-colFarmerName',
                text: lang('Name'),
                width: '19%',
                dataIndex: 'Name'
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colMemberRole',
                text: lang('Role'),
                width: '18%',
                dataIndex: 'MemberRole'
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colBirthdate',
                text: lang('Birthdate'),
                dataIndex: 'Birthdate',
                width: '6%'
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colAge',
                text: lang('Age'),
                dataIndex: 'Age',
                width:'3%'
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colHandphone',
                text: lang('Handphone'),
                dataIndex: 'Handphone',
                width:'7%'
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colProvince',
                text: lang('Province'),
                dataIndex: 'Province',
                hidden:true
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colDistrict',
                text: lang('District'),
                dataIndex: 'District',
                hidden:true
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colKecamatan',
                text: lang('Kecamatan'),
                width: '10%',
                dataIndex: 'Kecamatan'
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colDesa',
                text: lang('Desa'),
                width: '12%',
                dataIndex: 'Desa'
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colDateCollection',
                text: lang('Date Collection'),
                width: '10%',
                dataIndex: 'DateCollection',
                hidden:true
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colEnumerator',
                text: lang('Enumerator'),
                width: '9%',
                dataIndex: 'Enumerator'
            },{
                id: 'Koltiva.view.Trader.GridMainTrader-colLastUpdated',
                text: lang('Last Updated'),
                width: '11%',
                hidden: true,
                dataIndex: 'LastUpdated'
            }]
        }];

        this.callParent(arguments);
    }
});