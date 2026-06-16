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
                var sm = Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.SME.GridMainTraderMill').destroy(); //destory current view
                //create object View untuk FormMainTrader
                if(Ext.getCmp('Koltiva.view.SME.FormMainTrader') == undefined){
                    var FormMainTrader = Ext.create('Koltiva.view.SME.FormMainTrader', {
                        opsiDisplay: 'view',
                        from : 'SMEMill',
                        viewVar: {
                            MemberID: sm.get('MemberIDInc'),
                            MemberTypeID: sm.get('MemberTypeID')
                        }
                    });
                }else{
                    //destroy, create ulang
                    Ext.getCmp('Koltiva.view.SME.FormMainTrader').destroy();
                    var FormMainTrader = Ext.create('Koltiva.view.SME.FormMainTrader', {
                        opsiDisplay: 'view',
                        viewVar: {
                            MemberID: sm.get('MemberIDInc'),
                            MemberTypeID: sm.get('MemberTypeID')
                        }
                    });
                }

            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.SME.GridMainTraderMill').destroy(); //destory current view
                //create object View untuk FormMainTrader
                if(Ext.getCmp('Koltiva.view.SME.FormMainTrader') == undefined){
                    var FormMainTrader = Ext.create('Koltiva.view.SME.FormMainTrader', {
                        opsiDisplay: 'update',
                        from : 'SMEMill',
                        viewVar: {
                            MemberID: sm.get('MemberIDInc'),
                            MemberTypeID: sm.get('MemberTypeID')
                        }
                    });
                }else{
                    //destroy, create ulang
                    Ext.getCmp('Koltiva.view.SME.FormMainTrader').destroy();
                    var FormMainTrader = Ext.create('Koltiva.view.SME.FormMainTrader', {
                        opsiDisplay: 'update',
                        viewVar: {
                            MemberID: sm.get('MemberIDInc'),
                            MemberTypeID: sm.get('MemberTypeID')
                        }
                    });
                }

            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/export.png',
            text: lang('Export Farmer Relations'),
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getSelectionModel().getSelection()[0];
               
                var MemberID =  sm.get('MemberIDInc');

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
                
                try {
                    Ext.destroy(Ext.get('downloadIframe'));
                }
                catch(e) {}

                Ext.DomHelper.append(document.body, {
                    tag: 'iframe',
                    id:'downloadIframe',
                    frameBorder: 0,
                    width: 0,
                    height: 0,
                    css: 'display:none;visibility:hidden;height:0px;',
                    src: m_api+'/sme/export_trader_farmers?MemberID='+MemberID
                });
                Ext.MessageBox.hide();
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Edit Partner Ownership'),
            hidden: m_act_set_partner_trader,
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getSelectionModel().getSelection()[0];
               
                var frm = Ext.create('Koltiva.view.SME.FormSetPartner');
                frm.getForm().load({method: 'GET', params: {MemberID: sm.get('MemberIDInc')}});
                win = Ext.create('Ext.Window',{
                    title: lang('Edit SME Ownership'),
                    closable: true,
                    modal: true,
                    autoScroll: true,
                    width: '40%',
                    items:[frm]
                }).show();
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: m_act_delete,
            handler: function(){
                var sm = Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/sme/member',
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
                                Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getStore().load();
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
                ptextSearch     : Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-textSearch').getValue(),
                ptextSearchDesa : Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-textSearchDesa').getValue(),
                pCmbRoleSearch  : Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-CmbRoleSearch').getValue()
            })
        );
    }    

    function getFilterLs() {
        var filters = {};
        var ptextSearch;
        var ptextSearchDesa;

        var patchouli_trader_ls = JSON.parse(localStorage.getItem('patchouli_trader_ls'));
        if (patchouli_trader_ls != null){
            ptextSearch         = patchouli_trader_ls.ptextSearch;
            ptextSearchDesa     = patchouli_trader_ls.ptextSearchDesa;
            pCmbRoleSearch      = patchouli_trader_ls.pCmbRoleSearch;
            pAdvRowHandphone    = patchouli_trader_ls.pAdvRowHandphone;
            pAdvTextHandphone   = patchouli_trader_ls.pAdvTextHandphone;
            pAdvRowAge          = patchouli_trader_ls.pAdvRowAge;
            pAdvOpAge           = patchouli_trader_ls.pAdvOpAge;
            pAdvTextAge         = patchouli_trader_ls.pAdvTextAge;
        } else {
            ptextSearch       = "";
            ptextSearchDesa   = "";
            pCmbRoleSearch    = "";
            pAdvRowHandphone  = "";
            pAdvTextHandphone = "";
            pAdvRowAge        = "";
            pAdvOpAge         = "";
            pAdvTextAge       = "";
        }

        filters.prov                    = m_ProvinceID;
        filters.kab                     = m_DistrictID;
        filters.kec                     = m_SubDistrictID;
        filters.textSearch              = ptextSearch;
        filters.textSearchDesa          = ptextSearchDesa;
        filters.roleSearch              = (pCmbRoleSearch !== undefined) ? pCmbRoleSearch.toString() : "";
        filters.AdvRowHandphone         = (pAdvRowHandphone != undefined) ? pAdvRowHandphone : "";
        filters.AdvTextHandphone        = (pAdvTextHandphone != undefined) ? pAdvTextHandphone : "";
        filters.AdvRowAge               = (pAdvRowAge != undefined) ? pAdvRowAge : "";
        filters.AdvOpAge                = (pAdvOpAge != undefined) ? pAdvOpAge : "";
        filters.AdvTextAge              = (pAdvTextAge != undefined) ? pAdvTextAge : "";

        return filters;
    }

    function submitOnEnterGridTrader(field, event) {
        if (event.getKey() == event.ENTER) {
            setFilterLs();
            Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getStore().loadPage(1);
        }
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.SME.GridMainTraderMill' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.SME.GridMainTraderMill',
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';

            //isikan variabel dari local storage
            var patchouli_trader_ls = JSON.parse(localStorage.getItem('patchouli_trader_ls'));
            if(patchouli_trader_ls != null){
                Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-textSearch').setValue(patchouli_trader_ls.ptextSearch);
            }

            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getStore().load();
        }
    },

    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridMain = Ext.create('Koltiva.store.SME.GridMainTraderMill');
        var cmb_role_trader = Ext.create('Koltiva.store.SME.CmbRoleTrader');

        //items
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [ ]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.SME.GridMainTraderMill-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            minHeight:125,
            store: storeGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.SME.GridMainTraderMill-gridToolbar',
                store: storeGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    text: lang('Add'),
                    icon: varjs.config.base_url + 'images/icons/new/add.png', 
                    cls:'Sfr_BtnGridGreen', 
                    overCls:'Sfr_BtnGridGreen-Hover',
                    hidden: m_act_add,
                    handler: function() {
                        Ext.getCmp('Koltiva.view.SME.GridMainTraderMill').destroy(); //destory current view

                        //create object View untuk FormMainTrader
                        if(Ext.getCmp('Koltiva.view.SME.FormMainGrower') == undefined){
                            var FormMainTrader = Ext.create('Koltiva.view.SME.FormMainTrader', {
                                opsiDisplay: 'insert',
                                from : 'SMEMill'
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.SME.FormMainTrader').destroy();
                            var FormMainTrader = Ext.create('Koltiva.view.SME.FormMainTrader', {
                                opsiDisplay: 'insert'
                            });
                        }
                    }
                },{
                    text: lang('Export'),
                    icon: varjs.config.base_url + 'images/icons/new/export.png', 
                    cls:'Sfr_BtnGridPaleBlue',
                    hidden: m_act_export,
                    handler: function() {
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

                        var filter          = getFilterLs();
                        var keys            = Object.keys(filter);
                        var param_string    = '?search=1';
                        $.each(keys, function(index, val) {
                            param_string += '&'+val+'='+filter[val];
                        });
                        
                        try {
                            Ext.destroy(Ext.get('downloadIframe'));
                        }
                        catch(e) {}

                        Ext.DomHelper.append(document.body, {
                            tag: 'iframe',
                            id:'downloadIframe',
                            frameBorder: 0,
                            width: 0,
                            height: 0,
                            css: 'display:none;visibility:hidden;height:0px;',
                            src: m_api+'/sme/export_traders_mill/'+param_string
                        });
                        Ext.MessageBox.hide();

                    }
                },{
                    name: 'key',
                    id: 'Koltiva.view.SME.GridMainTraderMill-textSearch',
                    xtype: 'textfield',
                    width: 300,
                    emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                    listeners: {
                        specialkey: submitOnEnterGridTrader
                    }
                },
                {
                    name: 'keyDesa',
                    id: 'Koltiva.view.SME.GridMainTraderMill-textSearchDesa',
                    xtype: 'textfield',
                    width: 300,
                    hidden: m_act_search_desa,
                    emptyText: lang('Cari berdasar Desa')+', '+lang('Press \'Enter\' to search'),
                    listeners: {
                        specialkey: submitOnEnterGridTrader
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    id: 'Koltiva.view.SME.GridMainTraderMill-CmbRoleSearch',
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
                            Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getStore().loadPage(1);
                        }
                    }
                },{
                    tooltip: lang('Reload'),
                    icon: varjs.config.base_url + 'images/icons/new/reload.png', 
                    cls:'Sfr_BtnGridBlue', 
                    overCls:'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        //reload
                        setFilterLs();
                        Ext.getCmp('Koltiva.view.SME.GridMainTraderMill-gridMainGrid').getStore().loadPage(1);
                    }
                },{
                    tooltip: lang('Custom Field Grid'),
                    icon: varjs.config.base_url + 'images/icons/silk/grid.png', 
                    cls:'Sfr_BtnGridPaleBlue',
                    handler: function() {
                        //display field grid
                        var winGridDisplay = Ext.create('Koltiva.view.SME.WinGridDisplay');
                        if (!winGridDisplay.isVisible()) {
                            winGridDisplay.center();
                            winGridDisplay.show();
                        } else {
                            winGridDisplay.close();
                        }
                    }
                },{
                    tooltip: lang('Advanced Filter'),
                    icon: varjs.config.base_url + 'images/icons/silk/find.png', 
                    cls:'Sfr_BtnGridPaleBlue',
                    handler: function() {
                        //advanced search
                        var winAdvFilter = Ext.create('Koltiva.view.SME.WinAdvancedFilter');
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
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridTrader.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'MemberIDInc',
                hidden:true
            },{
                text: 'MemberTypeID',
                dataIndex: 'MemberTypeID',
                hidden:true
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colid',
                text: lang('SME ID'),
                dataIndex: 'id',
                width: '10%'
            },
			{ 
                text: lang('SME Name'),
                width: '19%',
                dataIndex: 'agCompanyName'
            },
			{ 
                text: lang('SME Status'),
                width: '19%',
                dataIndex: 'StatusSME'
            },
			{
                id: 'Koltiva.view.SME.GridMainTraderMill-colFarmerName',
                text: lang('Trader Name'),
                width: '19%',
                dataIndex: 'Name'
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colNrFarmer',
                text: lang('Nr Of Farmer'),
                dataIndex: 'NrFarmer',
                width:'10%'
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colCoordinate',
                text: lang('Coordinate'),
                dataIndex: 'GPS',
                width:'10%'
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colProvince',
                text: lang('Province'),
                dataIndex: 'Province', 
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colDistrict',
                text: lang('District'),
                dataIndex: 'District', 
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colKecamatan',
                text: lang('Kecamatan'),
                width: '10%',
                dataIndex: 'Kecamatan'
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colDesa',
                text: lang('Desa'),
                width: '12%',
                dataIndex: 'Desa'
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colDateCollection',
                text: lang('Date Collection'),
                width: '10%',
                dataIndex: 'DateCollection',
                hidden:true
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colEnumerator',
                text: lang('Enumerator'),
                width: '9%',
                dataIndex: 'Enumerator'
            },{
                id: 'Koltiva.view.SME.GridMainTraderMill-colLastUpdated',
                text: lang('Last Updated'),
                width: '11%',
                hidden: true,
                dataIndex: 'LastUpdated'
            }]
        }];

        this.callParent(arguments);
    }
});