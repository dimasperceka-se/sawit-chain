Ext.onReady(function () {
    
    Ext.create("Ext.Container", {
        renderTo: "ext-content",
        id: "panel-report-cooperatives",
        layout:"fit",
        items: [{
            xtype: "panel",
            titleAlign: "center",
            title: "TRACEABILITY MOBILE SYNCHRONIZATION",
            bodyStyle: "background:#fff;",
            layout:{
                type:"column"
            },
            items: [{
                xtype: "form",
                columnWidth:0.2,
                layout: "vbox",
                id: "form-filter-traceability-sync",
                padding: 5,
                height: 900,
                titleAlign:"center",
                style:"background:#424141 !important;",
                bodyStyle:"background:#424141 !important;",
                defaults: {
                    labelAlign: "top",
                    labelWidth: 100,
                    labelStyle: "margin-top:3px;",
                    margin: 3
                },
                width:240,
                items: [
                    {
                        xtype: "buttongroup",
                        title: "<b>Batch Date</b>",
                        plain:true,
                        columns: 2,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "datefield",
                            name: "batch_date_from",
                            emptyText: "Start From"
                        },{
                            xtype: "datefield",
                            name: "batch_date_to",
                            emptyText: "Until Date"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>Transaction Date</b>",
                        columns: 2,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "datefield",
                            name: "trans_date_from",
                            emptyText: "Start From"
                        },{
                            xtype: "datefield",
                            name: "trans_date_to",
                            emptyText: "Until Date"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>Batch Number</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "textfield",
                            name: "batch_number",
                            width:210,
                            emptyText: "All Batch Number"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>No. PO</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "textfield",
                            name: "DestPO",
                            width:210,
                            emptyText: "All PO Number"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>No. Faktur</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "textfield",
                            name: "FakturNumber",
                            width:210,
                            emptyText: "All Faktur Number"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>District</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "districtid",
                            width:210,
                            emptyText: "All District",
                            store: Ext.create("Ext.data.Store", {
                                fields: ["District", "DistrictID"],
                                data: Ext.JSON.decode(m_district)
                            }),
                            queryMode: "local",
                            displayField: "District",
                            valueField: "DistrictID"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>Sub District</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "subdistrict",
                            width:210,
                            emptyText: "All Sub District",
                            store: Ext.create("Ext.data.Store", {
                                fields: ["SubDistrictID", "SubDistrict","DistrictID"],
                                data: Ext.JSON.decode(m_subdistrict)
                            }),
                            queryMode: "local",
                            displayField: "SubDistrict",
                            valueField: "SubDistrictID"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>Villages</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "village",
                            width:210,
                            emptyText: "All Village",
                            store: Ext.create("Ext.data.Store", {
                                fields: ["Village", "VillageID","SubDistrictID"],
                                data: Ext.JSON.decode(m_village)
                            }),
                            queryMode: "local",
                            displayField: "Village",
                            valueField: "VillageID",
                            listeners:{
                                select: function(c,v){
                                    var village = v[0].data.VillageID;
                                    var store = Ext.data.StoreManager.lookup('filter-cpg-report-traceability-sync');
                                    store.filter("VillageID",village);
                                },
                                change: function(c,v) {
                                    if(!v){
                                        var store = Ext.data.StoreManager.lookup('filter-cpg-report-traceability-sync');
                                        store.clearFilter();
                                    }
                                }
                            }
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>FarmerID</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "textfield",
                            name: "FarmerID",
                            width:210,
                            emptyText: "All FarmerID"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>Farmer Group</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "cpg",
                            width:210,
                            emptyText: "All Farmer Group",
                            store: Ext.create("Ext.data.Store", {
                                storeId:'filter-cpg-report-traceability-sync',
                                fields: ["CPGid", "GroupName","Village"],
                                data: Ext.JSON.decode(m_cpg)
                            }),
                            queryMode: "local",
                            displayField: "GroupName",
                            valueField: "CPGid"
                        }]
                    },
                    {
                        xtype: "buttongroup",
                        title: "<b>Buying Unit</b>",
                        columns: 1,
                        defaults: {
                            scale: "small",
                            width:100,
                            margin:4
                        },
                        items: [{
                            xtype: "combo",
                            name: "orgid",
                            width:210,
                            emptyText: "All Buying Unit",
                            store: Ext.create("Ext.data.Store", {
                                fields: ["name", "orgid"],
                                data: Ext.JSON.decode(m_pedagang)
                            }),
                            queryMode: "local",
                            displayField: "name",
                            valueField: "orgid"
                        }]
                    },
                    {
                        xtype:"container",
                        layout:{
                            type:"hbox"
                        },
                        items:[{
                            xtype: "button",
                            margin: "33 4",
                            width:100,
                            text: "Generate Grid",
                            handler: function () {
                                var frmval = Ext.getCmp("form-filter-traceability-sync").getValues();
                                var store = Ext.getCmp("grid-report-traceability-sync").getStore();
                                store.getProxy().extraParams = frmval;
                                store.load();

                            }
                        },
                        {
                            xtype: "button",
                            margin: "33 4",
                            width:100,
                            text: "Export to xls",
                            handler: function () {

                                function EncodeQueryData(data)
                                {
                                    var ret = [];
                                    var d;
                                    for (d in data) {
                                        ret.push(encodeURIComponent(d) + "=" + encodeURIComponent(data[d]));
                                        }
                                    return ret.join("&");
                                    
                                }

                                var frmval = Ext.getCmp("form-filter-traceability-sync").getValues();
                                frmval.xls = "true";
                                var store = Ext.getCmp("grid-report-traceability-sync").getStore();
                                var url = store.getProxy().url;
                                var querystring = EncodeQueryData(frmval);
                                window.location = url + "?" + querystring;

                            }
                        }]
                    }
                ]
            },{
                xtype: "gridpanel",
                columnWidth:0.8,
                id: "grid-report-traceability-sync",
                height: 900,
                store: Ext.create("Ext.data.Store", {
                    storeId: "store-grid-report-traceability-sync",
                    fields: [
                        "DestPO",
                        "FakturNumber",
                        "InvoiceNumber",
                        "SupplyBatchNumber",
                        "BuyingUnit",
                        "BatchDate",
                        "DateTransaction",
                        "FarmerID",
                        "FarmerName",
                        "District",
                        "SubDistrict",
                        "Village",
                        "CPGid",
                        "GroupName",
                        "Bruto",
                        "Moisture",
                        "BeanCount",
                        "Waste",
                        "ContractPrice",
                        "NetPrice",
                        "Netto",
                        "TotalPayment"
                    ],
                    proxy: {
                        type: "ajax",
                        url: m_report_path,
                        reader: {
                            type: "json",
                            root: "data"
                        }
                    },
                    autoLoad: true
                }),
                columns: [
                    {text: lang("No. Surat Jalan"), dataIndex: "DestPO", width: 200},
                    {text: lang("No. Faktur"), dataIndex: "FakturNumber", width: 200},
                    {text: lang("No. Batch"), dataIndex: "SupplyBatchNumber", width: 200},
                    {text: lang("No. Transaksi"), dataIndex: "InvoiceNumber", width: 200},
                    {text: lang("Unit Pembelian"), dataIndex: "BuyingUnit", width: 150},
                    {text: lang("Tgl Batch"), dataIndex: "BatchDate", width: 120},
                    {text: lang("Tgl Transaksi"), dataIndex: "DateTransaction", width: 120},
                    {text: lang("ID Petani"), dataIndex: "FarmerID", width: 250},
                    {text: lang("Nama Petani"), dataIndex: "FarmerName", width: 200},
                    {text: lang("Kabupaten"), dataIndex: "District", width: 200},
                    {text: lang("Kecamatan"), dataIndex: "SubDistrict", width: 100},
                    {text: lang("Desa"), dataIndex: "Village", width: 100},
                    {text: lang("ID Kelompok Petani"), dataIndex: "CPGid", width: 100},
                    {text: lang("Nama Kelompok Petani"), dataIndex: "GroupName", width: 100},
                    {text: lang("Berat Kotor(kg)"), dataIndex: "Bruto", width: 100},
                    {text: lang("Kadar Air"), dataIndex: "Moisture", width: 100},
                    {text: lang("Jumlah Biji"), dataIndex: "BeanCount", width: 100},
                    {text: lang("Sampah(%)"), dataIndex: "Waste", width: 100},
                    {text: lang("Harga Tanpa Potongan(Rp)"), dataIndex: "ContractPrice", width: 100},
                    {text: lang("Harga Dgn Potongan(Rp)"), dataIndex: "NetPrice", width: 100},
                    {text: lang("Berat Bersih(kg)"), dataIndex: "Netto", width: 100},
                    {text: lang("Total(Rp)"), dataIndex: "TotalPayment", width: 250}
                ],
                dockedItems: [{
                    xtype: "pagingtoolbar",
                    store: Ext.data.StoreManager.lookup("store-grid-report-traceability-sync"), //
                    dock: "bottom",
                    displayInfo: true
                }]
            }]
        }]
    });

});
