function submitOnEnterGridFarmerGroup(field, event){
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('Koltiva.view.Cooperatives.GridMain-gridMainGrid').getStore().loadPage(1);
    }
}

function setFilterLs(){
    localStorage.setItem('patchouli_coop_ls',
        JSON.stringify({
            ptextSearch: Ext.getCmp('Koltiva.view.Cooperatives.GridMain-textSearch').getValue()
        })
    );
}

Ext.define('Koltiva.view.Cooperatives.GridMain' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Cooperatives.GridMain',
    renderTo:'ext-content',
    width: '100%',
    minHeight: 550,
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    loadMask: true,
    selType: 'rowmodel',
    listeners: {
        afterRender: function(){
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';

            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('Koltiva.view.Cooperatives.GridMain-gridMainGrid').getStore().load();
        }
    },
    initComponent: function() {

        this.store = Ext.create('Koltiva.store.Cooperatives.GridMain');

        var contextMenuGridFarmerGroup = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Cooperatives.GridMain-gridMainGrid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.Cooperatives.GridMain').destroy(); //destory current view
                    var FormMainCooperatives = [];

                    //create object View
                    if(Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives') == undefined){
                        FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
                            opsiDisplay: 'view',
                            viewVar: {
                                CoopID: sm.get('CoopID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives').destroy();
                        FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
                            opsiDisplay: 'view',
                            viewVar: {
                                CoopID: sm.get('CoopID')
                            }
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: !m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Cooperatives.GridMain-gridMainGrid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.Cooperatives.GridMain').destroy(); //destory current view
                    var FormMainCooperatives = [];

                    //create object View
                    if(Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives') == undefined){
                        FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
                            opsiDisplay: 'update',
                            viewVar: {
                                CoopID: sm.get('CoopID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives').destroy();
                        FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
                            opsiDisplay: 'update',
                            viewVar: {
                                CoopID: sm.get('CoopID')
                            }
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: !m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Cooperatives.GridMain-gridMainGrid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/cooperatives/coop',
                                method: 'DELETE',
                                params: {
                                    CoopID: sm.get('CoopID')
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
                                    Ext.getCmp('Koltiva.view.Cooperatives.GridMain-gridMainGrid').getStore().load();
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

        this.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.3,
                layout: 'form',
                items:[{
                    xtype: 'button',
                    id: 'Koltiva.view.FarmerGroup.GridMainFarmerGroup-btnListView',
                    style:'margin-top:5px;',
                    text: lang('All Farmer Group'),
                    arrowAlign: 'right',
                    hidden: true,
                    menu : [{
                        text: lang('All Farmer Group'),
                        listeners: {
                            click: function(){
                                alert('All Farmer Group Click')
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
                id: 'Koltiva.view.Cooperatives.GridMain-gridInformation',
                html: ''
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.Cooperatives.GridMain-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            minHeight:250,
            selType: 'rowmodel',
            store: this.store,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: this.store,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: !m_act_add,
                    handler: function() {
                        Ext.getCmp('Koltiva.view.Cooperatives.GridMain').destroy(); //destory current view
                        var FormMainCooperatives = [];

                        //create object View
                        if(Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives') == undefined){
                            FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
                                opsiDisplay: 'insert'
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives').destroy();
                            FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
                                opsiDisplay: 'insert'
                            });
                        }
                    }
                },{
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.Cooperatives.GridMain-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                    listeners: {
                        specialkey: submitOnEnterGridFarmerGroup
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.3,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridFarmerGroup.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'id',
                hidden: true
            },{
                text: lang('Code'),
                flex: 1,
                dataIndex: 'CoopCode'
            }, {
                text: lang('Nama'),
                flex: 1,
                dataIndex: 'CoopName'
            }, {
                text: lang('Year Established'),
                flex: 1,
                dataIndex: 'YearEstablished'
            }, {
                text: lang('Phone'),
                flex: 1,
                dataIndex: 'Phone'
            }, {
                text: lang('Subdistrict'),
                flex: 1,
                dataIndex: 'Subdistrict'
            }, {
                text: lang('District'),
                flex: 1,
                dataIndex: 'District'
            }]
        }];

        this.callParent(arguments);

    //     this.dockedItems = [{
    //         xtype: 'pagingtoolbar',
    //         store: this.store, // same store GridPanel is using
    //         dock: 'bottom',
    //         displayInfo: true
    //     }, {
    //         xtype: 'toolbar',
    //         items: [{
    //             icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
    //             text: lang('Add'),
    //             hidden: !m_act_add,
    //             scope: this,
    //             handler: function() {
    //                 Ext.getCmp('Koltiva.view.Cooperatives.GridMain').destroy(); //destory current view
    //                 var FormMainCooperatives = [];

    //                 //create object View
    //                 if(Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives') == undefined){
    //                     FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
    //                         opsiDisplay: 'insert'
    //                     });
    //                 }else{
    //                     //destroy, create ulang
    //                     Ext.getCmp('Koltiva.view.Cooperatives.FormMainCooperatives').destroy();
    //                     FormMainCooperatives = Ext.create('Koltiva.view.Cooperatives.FormMainCooperatives', {
    //                         opsiDisplay: 'insert'
    //                     });
    //                 }
    //             },
    //             cls: m_act_add
    //         }, {
    //           icon: varjs.config.base_url + 'images/icons/new/update.png',
    //           text: lang('Update'),
    //           hidden: !m_act_update,
    //           scope: this,
    //           handler: function() {

    //           },
    //           cls: m_act_update
    //       }, {
    //           itemId: 'remove',
    //           icon: varjs.config.base_url + 'images/icons/new/delete.png',
    //           cls: m_act_delete,
    //           hidden: !m_act_delete,
    //           text: lang('Hapus'),
    //           scope: this,
    //           handler: function() {

    //           }
    //       }, {
    //           name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
    //           id: 'key',
    //           xtype: 'textfield',
    //           emptyText: lang('Cari berdasar nama/ID')
    //       }, {
    //           id: 'sProvinsi',
    //           name: 'sProvinsi',
    //           xtype: 'combo',
    //           store: Ext.create('Koltiva.store.provinsi'),
    //           displayField: 'label',
    //           valueField: 'id',
    //           queryMode: 'local',
    //           hidden: true,
    //           value: m_param,
    //           listeners: {
    //               change: function(cb, nv, ov) {
    //                   mc_Kabupaten.load({
    //                       params: {
    //                           key: Ext.getCmp('sProvinsi').getValue()
    //                       }
    //                   });
    //                   Ext.getCmp('sKabupaten').enable();
    //               }
    //           }
    //       }, {
    //           id: 'sKabupaten',
    //           name: 'sKabupaten',
    //           xtype: 'combo',
    //           store: Ext.create('Koltiva.store.kabupaten'),
    //           displayField: 'label',
    //           valueField: 'label',
    //           queryMode: 'local'
    //       }, {
    //           xtype: 'button',
    //           id: 'btnSimpleSearch',
    //           icon: varjs.config.base_url + 'images/icons/silk/search.png',
    //           margin: '0px 0px 0px 6px',
    //           text: lang('Search'),
    //           handler: function() {

    //           }
    //       }, {
    //           xtype: 'button',
    //           id: 'btnAdvSearch',
    //           icon: varjs.config.base_url + 'images/icons/silk/page_white_wrench.png',
    //           margin: '0px 0px 0px 6px',
    //           text: lang('Advanced Search'),
    //           handler: function() {

    //           }
    //       }]
    //   }];

    //   this.columns = [{
    //       text: lang('ID'),
    //       dataIndex: 'id',
    //       hidden: true
    //   }, {
    //       text: lang('No'),
    //       xtype: 'rownumberer',
    //       width: '5%'
    //   }, {
    //       text: lang('Code'),
    //       width: '15%',
    //       dataIndex: 'CoopCode'
    //   }, {
    //       text: lang('Nama'),
    //       width: '15%',
    //       dataIndex: 'CoopName'
    //   }, {
    //       text: lang('Phone'),
    //       width: '10%',
    //       dataIndex: 'Phone'
    //   }, {
    //       text: lang('Email'),
    //       width: '15%',
    //       dataIndex: 'Email'
    //   }, {
    //       text: lang('Tahun Terbentuk'),
    //       width: '15%',
    //       dataIndex: 'TahunTerbentuk'
    //   }, {
    //       text: lang('Status'),
    //       width: '10%',
    //       dataIndex: 'Status'
    //   }, {
    //       text: lang('District'),
    //       width: '15%',
    //       dataIndex: 'District'
    //   }];

    //   this.callParent(arguments);
    }
});
