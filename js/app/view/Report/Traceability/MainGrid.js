Ext.define('Koltiva.view.Report.Traceability.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Report.Traceability.MainGrid',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this;
        var CmbMill = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_crud + 'combo_mill',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                beforeload: function (store_viewf, operation) {
                    store_viewf.proxy.extraParams.PartnerID = Ext.getCmp('RTCPartnerID').getValue()
                }
            }
        });

        var CmbDO = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_crud + 'combo_do',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                beforeload: function (store_viewf, operation) {
                    store_viewf.proxy.extraParams.PartnerID = Ext.getCmp('RTCPartnerID').getValue()
                }
            }
        });

        var CmbAgent = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_crud + 'combo_agent',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                beforeload: function (store_viewf, operation) {
                    store_viewf.proxy.extraParams.PartnerID = Ext.getCmp('RTCPartnerID').getValue()
                }
            }
        });
        var GridMill = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            pageSize: 50,
            fields: [
                {name: 'District', type: 'string'},
                {name: 'SubDistrict', type: 'string'},
                {name: 'MillID', type: 'integer'},
                {name: 'MillName', type: 'string'},
                {name: 'MillBatchID', type: 'integer'},
                {name: 'DateTransaction', type: 'string'},
                {name: 'VolumeNetto', type: 'float'},
                {name: 'FFB', type: 'integer'},
                {name: 'BatchFrom', type: 'string'}
            ],
            autoload: false,
            proxy: {
                type: 'ajax',
                url: m_crud + 'grid_mill',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                beforeload: function (storeWH, operation) {
                    storeWH.proxy.extraParams.partnerid     = Ext.getCmp('RTCPartnerID').getRawValue();
                    storeWH.proxy.extraParams.startd        = Ext.getCmp('RTCstart').getRawValue();
                    storeWH.proxy.extraParams.end           = Ext.getCmp('RTCend').getRawValue();
                    storeWH.proxy.extraParams.mill          = Ext.getCmp('RTCMill').getValue();
                    storeWH.proxy.extraParams.do            = Ext.getCmp('RTCDO').getValue();
                    storeWH.proxy.extraParams.agent         = Ext.getCmp('RTCAgent').getValue();
                },
                load: function(store, record) {
                    var netto = store.proxy.reader.jsonData.TotalNetto;
                    if(netto==undefined){
                        netto = 0;
                    }
                    Ext.getCmp('MillTotalNetto').setText('Total Netto : '+netto+' Kg');
                }
            }
        });
        var GridDO = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            pageSize: 50,
            fields: [
                {name: 'District', type: 'string'},
                {name: 'SubDistrict', type: 'string'},
                {name: 'DOID', type: 'integer'},
                {name: 'DOName', type: 'string'},
                {name: 'DOBatchID', type: 'integer'},
                {name: 'DOBatchDate', type: 'string'},
                {name: 'VolumeNetto', type: 'float'},
                {name: 'FFB', type: 'integer'},
                {name: 'MillID', type: 'integer'},
                {name: 'MillName', type: 'string'},
                {name: 'MillBatchID', type: 'integer'}
            ],
            autoload: false,
            proxy: {
                type: 'ajax',
                url: m_crud + 'grid_do',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                beforeload: function (storeWH, operation) {
                    storeWH.proxy.extraParams.partnerid     = Ext.getCmp('RTCPartnerID').getRawValue();
                    storeWH.proxy.extraParams.startd        = Ext.getCmp('RTCstart').getRawValue();
                    storeWH.proxy.extraParams.end           = Ext.getCmp('RTCend').getRawValue();
                    storeWH.proxy.extraParams.mill          = Ext.getCmp('RTCMill').getValue();
                    storeWH.proxy.extraParams.do            = Ext.getCmp('RTCDO').getValue();
                    storeWH.proxy.extraParams.agent         = Ext.getCmp('RTCAgent').getValue();
                },
                load: function(store, record) {
                    var netto = store.proxy.reader.jsonData.TotalNetto;
                    if(netto==undefined){
                        netto = 0;
                    }
                    Ext.getCmp('DOTotalNetto').setText('Total Netto : '+netto+' Kg');
                }
            }
        });
        var GridAgent = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            pageSize: 50,
            fields: [
                {name: 'District', type: 'string'},
                {name: 'SubDistrict', type: 'string'},
                {name: 'AgentID', type: 'integer'},
                {name: 'AgentName', type: 'string'},
                {name: 'AgentBatchID', type: 'integer'},
                {name: 'AgentBatchDate', type: 'string'},
                {name: 'VolumeNetto', type: 'float'},
                {name: 'FFB', type: 'integer'},
                {name: 'DOID', type: 'integer'},
                {name: 'DOName', type: 'string'},
                {name: 'DOBatchID', type: 'integer'},
                {name: 'MillID', type: 'integer'},
                {name: 'MillName', type: 'string'},
                {name: 'MillBatchID', type: 'integer'}
            ],
            autoload: false,
            proxy: {
                type: 'ajax',
                url: m_crud + 'grid_agent',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                beforeload: function (storeWH, operation) {
                    storeWH.proxy.extraParams.partnerid     = Ext.getCmp('RTCPartnerID').getRawValue();
                    storeWH.proxy.extraParams.startd        = Ext.getCmp('RTCstart').getRawValue();
                    storeWH.proxy.extraParams.end           = Ext.getCmp('RTCend').getRawValue();
                    storeWH.proxy.extraParams.mill          = Ext.getCmp('RTCMill').getValue();
                    storeWH.proxy.extraParams.do            = Ext.getCmp('RTCDO').getValue();
                    storeWH.proxy.extraParams.agent         = Ext.getCmp('RTCAgent').getValue();
                },
                load: function(store, record) {
                    var netto = store.proxy.reader.jsonData.TotalNetto;
                    if(netto==undefined){
                        netto = 0;
                    }
                    Ext.getCmp('AgentTotalNetto').setText('Total Netto : '+netto+' Kg');
                }
            }
        });
        var GridFarmer = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            pageSize: 50,
            fields: [
                {name: 'District', type: 'string'},
                {name: 'SubDistrict', type: 'string'},
                {name: 'MemberDisplayID', type: 'string'},
                {name: 'MemberName', type: 'string'},
                {name: 'PlotNr', type: 'string'},
                {name: 'DateTransaction', type: 'string'},
                {name: 'VolumeNetto', type: 'float'},
                {name: 'FFB', type: 'integer'},
                {name: 'AgentID', type: 'integer'},
                {name: 'AgentName', type: 'string'},
                {name: 'AgentBatchID', type: 'integer'},
                {name: 'DOID', type: 'integer'},
                {name: 'DOName', type: 'string'},
                {name: 'DOBatchID', type: 'integer'},
                {name: 'MillID', type: 'integer'},
                {name: 'MillName', type: 'string'},
                {name: 'MillBatchID', type: 'integer'}
            ],
            autoload: false,
            proxy: {
                type: 'ajax',
                url: m_crud + 'grid_farmer',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                beforeload: function (storeWH, operation) {
                    storeWH.proxy.extraParams.partnerid     = Ext.getCmp('RTCPartnerID').getRawValue();
                    storeWH.proxy.extraParams.startd        = Ext.getCmp('RTCstart').getRawValue();
                    storeWH.proxy.extraParams.end           = Ext.getCmp('RTCend').getRawValue();
                    storeWH.proxy.extraParams.mill          = Ext.getCmp('RTCMill').getValue();
                    storeWH.proxy.extraParams.do            = Ext.getCmp('RTCDO').getValue();
                    storeWH.proxy.extraParams.agent         = Ext.getCmp('RTCAgent').getValue();
                },
                load: function(store, record) {
                    var netto = store.proxy.reader.jsonData.TotalNetto;
                    if(netto==undefined){
                        netto = 0;
                    }
                    Ext.getCmp('FarmerTotalNetto').setText('Total Netto : '+netto+' Kg');
                }
            }
        });
        //store yg dipakai (begin)
        //var storeComboFarmer = Ext.create('Koltiva.store.Report.Traceability.ComboFarmer');   
        thisObj.items = [{
            xtype: 'fieldset',
            title: 'Traceability',
            id: 'RTCe',
            height: 2100,
            items: [{
                xtype: 'form',
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%'
                },
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .10,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            fieldLabel: '',
                            id: 'RTCstart',
                            name: 'start',
                            width: 120,
                            emptyText: lang('Awal'),
                            value:  Ext.Date.format( Ext.Date.add (new Date(),Ext.Date.YEAR,-1), 'Y-m-d'),
                            padding: 5,
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_CH.load();
                                }
                            }
                        }]
                    }, {
                        columnWidth: .04,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'label',
                            text: lang('s.d.')
                        }]
                    }, {
                        columnWidth: .10,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            fieldLabel: '',
                            id: 'RTCend',
                            name: 'end',
                            emptyText: lang('Akhir'),
                            padding: 5,
                            value : Ext.Date.format(new Date(), 'Y-m-d'),
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_CH.load();
                                }
                            }
                        }]
                    }, {
                        columnWidth: .12,
                        layout: 'form',
                        id: 'FormPartnerID',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- Partner --',
                            id: 'RTCPartnerID',
                            name: 'PartnerID',
                            xtype: 'combo',
                            labelWidth: 60,
                            //store: mc_Partner,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    //mc_Mill.load();
                                    //mc_CH.load();
                                }
                            }
                        }]
                    }, {
                        columnWidth: .15,
                        layout: 'form',
                        id: 'RTClayoutJenisSertifikasi',
                        hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: lang('All Transaction'),
                            id: 'RTCjenisSertifikasi',
                            name: 'jenisSertifikasi',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            //store: mc_jenis_sertifikasi,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    
                                }
                            },
                            value: ''
                        }]
                    }, {
                        columnWidth: .25,
                        layout: 'form',
                        //hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: lang('All Mill'),
                            id: 'RTCMill',
                            name: 'Mill',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            store: CmbMill,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    
                                }
                            }
                        }]
                    }, {
                        columnWidth: .12,
                        layout: 'form',
                        //hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: lang('All DO'),
                            id: 'RTCDO',
                            name: 'DO',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            store: CmbDO,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    
                                }
                            }
                        }]
                    }, {
                        columnWidth: .12,
                        layout: 'form',
                        //hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: lang('All SME'),
                            id: 'RTCAgent',
                            name: 'Agent',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            store: CmbAgent,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    
                                }
                            }
                        }]
                    },
                    {
                        xtype: 'textfield',
                        id: 'RTCBatchIDWH',
                        name: 'BatchIDWH',
                        hidden:true
                    },
                    {
                        xtype: 'textfield',
                        id: 'RTCBatchIDCH',
                        name: 'BatchIDCH',
                        hidden:true
                    },
                    {
                        xtype: 'textfield',
                        id: 'RTCBatchIDBS',
                        name: 'BatchIDBS',
                        hidden:true
                    },
                    {
                        columnWidth: .10,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'button',
                            id: 'RTCbtnViewPremium',
                            name: 'btnViewPremium',
                            text: 'View',
                            padding: 3,
                            handler: function () {
                                /*Ext.getCmp('RTCBatchIDWH').setValue('');
                                Ext.getCmp('RTCBatchIDCH').setValue('');
                                Ext.getCmp('RTCBatchIDBS').setValue('');*/
                                GridMill.load();
                                GridDO.load();
                                GridAgent.load();
                                GridFarmer.load();
                            }
                        }]
                    }]
                }, {
                    xtype: 'gridpanel',
                    id: 'RTgrid_Mill_trans',
                    padding: 6,
                    width: '100%',
                    border: true,
                    title: lang('Mill'),
                    store: GridMill,
                    height: 500,
                    features: [{
                        ftype: 'summary'
                    }],
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: GridMill,
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                            xtype: 'button',
                            text: 'Export Excel', hidden: true,
                            handler: function () {                            
                                
                            }
                        }, {
                            xtype: 'label',
                            height: 20,
                            text: ''
                        }, {
                            xtype: 'label',
                            id: 'MillTotalNetto',
                            text: 'Total Netto : -'
                        }]
                    }],
                    columns: [{
                        header: lang('District'),
                        dataIndex: 'District',
                        flex: 2
                    }, {
                        header: lang('Sub District'),
                        dataIndex: 'SubDistrict',
                        flex: 2
                    }, {
                        header: lang('Mill ID'),
                        dataIndex: 'MillID',
                        flex: 1
                    }, {
                        header: lang('Mill Name'),
                        dataIndex: 'MillName',
                        flex: 2
                    }, {
                        header: lang('Mill Batch ID'),
                        dataIndex: 'MillBatchID',
                        flex: 1
                    }, {
                        header: lang('Mill Batch Date'),
                        dataIndex: 'DateTransaction',
                        flex: 1
                    }, {
                        header: lang('Netto')+' (Kg)',
                        dataIndex: 'VolumeNetto',
                        flex: 1,
                        align: 'right',
                        renderer: Ext.util.Format.numberRenderer('0,000.00'),
                        summaryType: 'sum'
                    }, {
                        header: lang('FFB'),
                        dataIndex: 'FFB',
                        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                            if(value==null||value=='') return '-';
                        },
                        flex: 0.5
                    }, {
                        header: lang('From'),
                        dataIndex: 'BatchFrom',
                        flex: 2
                    }],
                    listeners : {
                        itemdblclick: function(view, record, item, index, e){
                           /*var smWH = Ext.getCmp('RTgrid_Mill_trans').getSelectionModel().getSelection()[0];
                           Ext.getCmp('RTCBatchIDWH').setValue(smWH.get('SupplyBatchID'));
                           Ext.getCmp('RTCBatchIDCH').setValue('');
                           Ext.getCmp('RTCBatchIDBS').setValue('');
                           storeCH.load();
                           storeBS.load();
                           storeFarmer.load();*/
                        }
                    },
                }, {
                    xtype: 'gridpanel',
                    id: 'RTgrid_ch_trans',
                    padding: 6,
                    width: '100%',
                    border: true,
                    title: lang('DO'),
                    store: GridDO,
                    height: 500,
                    features: [{
                        ftype: 'summary'
                    }],
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: GridDO,
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                            xtype: 'button',
                            text: 'Export Excel', hidden: true,
                            handler: function () {                            
                                
                            }
                        }, {
                            xtype: 'label',
                            height: 20,
                            text: ''
                        }, {
                            xtype: 'label',
                            id: 'DOTotalNetto',
                            text: 'Total Netto : -'
                        }]
                    }],
                    columns: [{
                        header: lang('District'),
                        dataIndex: 'District',
                        flex: 2
                    }, {
                        header: lang('Sub District'),
                        dataIndex: 'SubDistrict',
                        flex: 2
                    }, {
                        header: lang('DO ID'),
                        dataIndex: 'DOID',
                        flex: 1
                    }, {
                        header: lang('DO Name'),
                        dataIndex: 'DOName',
                        flex: 2
                    }, {
                        header: lang('DO Batch ID'),
                        dataIndex: 'DOBatchID',
                        flex: 1
                    }, {
                        header: lang('Transaction Date'),
                        dataIndex: 'DOBatchDate',
                        flex: 1
                    }, {
                        header: lang('Netto')+' (Kg)',
                        dataIndex: 'VolumeNetto',
                        flex: 1,
                        align: 'right',
                        renderer: Ext.util.Format.numberRenderer('0,000.00'),
                        summaryType: 'sum'
                    }, {
                        header: lang('FFB'),
                        dataIndex: 'FFB',
                        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                            if(value==null||value=='') return '-';
                        },
                        flex: 0.5
                    }, {
                        header: lang('Mill ID'),
                        dataIndex: 'MillID',
                        flex: 1
                    }, {
                        header: lang('Mill Name'),
                        dataIndex: 'MillName',
                        flex: 2
                    }, {
                        header: lang('Mill Batch ID'),
                        dataIndex: 'MillBatchID',
                        flex: 1
                    }],
                    listeners : {
                        itemdblclick: function(view, record, item, index, e){
                           /*var smCH = Ext.getCmp('RTgrid_ch_trans').getSelectionModel().getSelection()[0];
                           Ext.getCmp('RTCBatchIDCH').setValue(smCH.get('SupplyBatchID'));
                           Ext.getCmp('RTCBatchIDBS').setValue('');
                           storeBS.load();
                           storeFarmer.load();*/
                        }
                    },
                }, {
                    xtype: 'gridpanel',
                    id: 'RTgrid_bs_trans',
                    padding: 6,
                    width: '100%',
                    border: true,
                    title: lang('Agent'),
                    store: GridAgent,
                    height: 500,
                    features: [{
                        ftype: 'summary'
                    }],
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: GridAgent,
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                            xtype: 'button',
                            text: 'Export Excel', hidden: true,
                            handler: function () {                            
                                
                            }
                        }, {
                            xtype: 'label',
                            height: 20,
                            text: ''
                        },{
                            xtype: 'label',
                            id: 'AgentTotalNetto',
                            text: 'Total Netto : -'
                        }]
                    }],
                    columns: [{
                        header: lang('District'),
                        dataIndex: 'District',
                        width: 200
                    }, {
                        header: lang('Sub District'),
                        dataIndex: 'SubDistrict',
                        width: 200
                    }, {
                        header: lang('SME ID'),
                        dataIndex: 'AgentID',
                        width: 100
                    }, {
                        header: lang('SME Name'),
                        dataIndex: 'Agent Name',
                        width: 200
                    }, {
                        header: lang('SME Batch ID'),
                        dataIndex: 'AgentBatchID',
                        width: 100
                    }, {
                        header: lang('Transaction Date'),
                        dataIndex: 'AgentBatchDate',
                        width: 100
                    }, {
                        header: lang('Netto')+' (Kg)',
                        dataIndex: 'VolumeNetto',
                        width: 100,
                        align: 'right',
                        renderer: Ext.util.Format.numberRenderer('0,000.00'),
                        summaryType: 'sum'
                    }, {
                        header: lang('FFB'),
                        dataIndex: 'FFB',
                        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                            if(value==null||value=='') return '-';
                        },
                        width: 50
                    }, {
                        header: lang('DO ID'),
                        dataIndex: 'DOID',
                        width: 100
                    }, {
                        header: lang('DO Name'),
                        dataIndex: 'DOName',
                        width: 200
                    }, {
                        header: lang('DO Batch ID'),
                        dataIndex: 'DOBatchID',
                        width: 100
                    }, {
                        header: lang('Mill ID'),
                        dataIndex: 'MillID',
                        width: 100
                    }, {
                        header: lang('Mill Name'),
                        dataIndex: 'MillName',
                        width: 200
                    }, {
                        header: lang('Mill Batch ID'),
                        dataIndex: 'MillBatchID',
                        width: 100
                    }],
                    listeners : {
                        itemdblclick: function(view, record, item, index, e){
                           var smBS = Ext.getCmp('RTgrid_bs_trans').getSelectionModel().getSelection()[0];
                           Ext.getCmp('RTCBatchIDBS').setValue(smBS.get('SupplyBatchID'));
                           storeFarmer.load();
                        }
                    },
                }, {
                    xtype: 'gridpanel',
                    id: 'RTgrid_farmer_trans',
                    padding: 6,
                    width: '100%',
                    border: true,
                    title: lang('Farmer'),
                    store: GridFarmer,
                    height: 500,
                    features: [{
                        ftype: 'summary'
                    }],
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: GridFarmer,
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                            xtype: 'button',
                            text: 'Export Excel',
                            hidden:true,
                            handler: function () {                            
                                url     = m_crud + 'print_transaction_farmer' + '?'
                                    +'startd='+ Ext.getCmp('RTCstart').getRawValue()
                                    +'&end='+ Ext.getCmp('RTCend').getRawValue()
                                    +'&wh='+ Ext.getCmp('RTCMill').getValue()
                                    +'&ch='+ Ext.getCmp('RTCDO').getValue()
                                    +'&bs='+ Ext.getCmp('RTCAgent').getValue()
                                    +'&sert='+ Ext.getCmp('RTCjenisSertifikasi').getValue()
                                    +'&BatchID='+ Ext.getCmp('RTCBatchIDWH').getValue()                                
                                window.open(url);
                            }
                        }, {
                            xtype: 'label',
                            height: 20,
                            text: ''
                        }, {
                            xtype: 'label',
                            id: 'FarmerTotalNetto',
                            text: 'Total Netto : -'
                        }]
                    }],
                    columns: [{
                        header: lang('District'),
                        dataIndex: 'District',
                        width: 150
                    }, {
                        header: lang('Sub District'),
                        dataIndex: 'SubDistrict',
                        width: 150
                    }, {
                        header: lang('Farmer ID'),
                        dataIndex: 'MemberDisplayID',
                        width: 150
                    }, {
                        header: lang('Farmer Name'),
                        dataIndex: 'MemberName',
                        width: 150
                    }, {
                        header: lang('Plot Nr'),
                        dataIndex: 'PlotNr',
                        width: 50
                    }, {
                        header: lang('Date Transaction'),
                        dataIndex: 'DateTransaction',
                        width: 100
                    }, {
                        header: lang('Netto'),
                        dataIndex: 'VolumeNetto',
                        width: 100,
                        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                            if(value==null) return '';
                        }
                    }, {
                        header: lang('FFB'),
                        dataIndex: 'FFB',
                        renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                            if(value==null||value=='') return '-';
                        },
                        flex: 0.5
                    }, {
                        header: lang('SME ID'),
                        dataIndex: 'AgentID',
                        width: 100
                    }, {
                        header: lang('SME Name'),
                        dataIndex: 'Agent Name',
                        width: 200
                    }, {
                        header: lang('SME Batch ID'),
                        dataIndex: 'AgentBatchID',
                        width: 100
                    }, {
                        header: lang('DO ID'),
                        dataIndex: 'DOID',
                        width: 100
                    }, {
                        header: lang('DO Name'),
                        dataIndex: 'DOName',
                        width: 200
                    }, {
                        header: lang('DO Batch ID'),
                        dataIndex: 'DOBatchID',
                        width: 100
                    }, {
                        header: lang('Mill ID'),
                        dataIndex: 'MillID',
                        width: 100
                    }, {
                        header: lang('Mill Name'),
                        dataIndex: 'MillName',
                        width: 200
                    }, {
                        header: lang('Mill Batch ID'),
                        dataIndex: 'MillBatchID',
                        width: 100
                    }],
                    listeners : {
                       itemclick: function(view, record, item, index, e){
                           //contextMenu.showAt(e.getXY());
                       }
                    },
                }]
            }]
        }];

        this.callParent(arguments);
        if(m_partner!="" && m_partner!="1" && m_partner!="37"){
            Ext.getCmp('FormPartnerID').hide();
            Ext.getCmp('RTCPartnerID').setValue(m_partner);
            /*mc_Mill.load({
                params: {
                    PartnerID: Ext.getCmp('RTCPartnerID').getValue()
                }
            });*/
        }
    }
});