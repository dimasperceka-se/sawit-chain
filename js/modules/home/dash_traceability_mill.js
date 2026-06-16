var currentLocation = window.location.href;

var html2 = `<div class="main-content">
<div class="row">
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box1"></div>
                    <div class="desc">
                        ${lang('Total Farmer')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/petani2.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="supplybase">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box2"></div>
                    <div class="desc">
                        ${lang('Total Dealer')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/trader.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="kebun">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box3"></div>
                    <div class="desc">
                        ${lang('Total number of Farmer selling')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/petani2.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="transaction">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box4"></div>
                    <div class="desc">
                        ${lang('Total number of transactions from Farmers (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/trader.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="volume">
            </ul>
        </div>
    </div>

     <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box5"></div>
                    <div class="desc">
                        ${lang('Total number transactions from Dealer')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/cpg2.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="supplybase">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box6"></div>
                    <div class="desc">
                        ${lang('Traceable volume received at mill (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/trader.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="kebun">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box7"></div>
                    <div class="desc">
                        ${lang('Total CPO Production (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/mill/icon-palmoil-mill-total-cpo.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="transaction">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box8"></div>
                    <div class="desc">
                        ${lang('Total PK Production (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/mill/icon-palmoil-mill-total-pko.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="volume">
            </ul>
        </div>
    </div>

    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box9"></div>
                    <div class="desc">
                        ${lang('Total number of CPO Refinery Transactions')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/WAREHOUSE.PNG`}"></div>
            </div>
            <ul class="widget-list colapsed" id="supplybase">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box10"></div>
                    <div class="desc">
                        ${lang('Total number of PK Refinery Transactions')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/WAREHOUSE.PNG`}"></div>
            </div>
            <ul class="widget-list colapsed" id="kebun">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box11"></div>
                    <div class="desc">
                        ${lang('Total Stock CPO (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/mill/icon-palmoil-mill-total-cpo.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="transaction">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box12"></div>
                    <div class="desc">
                        ${lang('Total Stock PK (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/mill/icon-palmoil-mill-total-pko.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="volume">
            </ul>
        </div>
    </div>

    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box13"></div>
                    <div class="desc">
                        ${lang('Total Traceable CPO dispatched to Refinery (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/dispatch-car.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="supplybase">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box14"></div>
                    <div class="desc">
                        ${lang('Total Traceable PK dispatched to Refinery (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/dispatch-car.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="kebun">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box15"></div>
                    <div class="desc">
                        ${lang('Total Traceable CPO received at the Refinery (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/dispatch-car.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="transaction">
            </ul>
        </div>
    </div>
    <div class="col-md-3">
        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div class="widget-head widget-tile">
                <div class="col-md-8">
                    <div class="value" id="box16"></div>
                    <div class="desc">
                        ${lang('Total Traceable PK received at the Refinery (TON)')}
                    </div>
                </div>
                <div class="widget-icon col-md-4"><img src="${`${currentLocation}img/general/dispatch-car.png`}"></div>
            </div>
            <ul class="widget-list colapsed" id="volume">
            </ul>
        </div>
    </div>
</div>

<div class="col-md-6 xs-mt-20">
    <div class="box gradient">
        <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div id="source_ffb"></div>
        </div>
    </div>
</div>

<div class="col-md-6 xs-mt-20">
    <div class="box gradient">
        <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div id="Traceable_Sales"></div>
        </div>
    </div>
</div>

<div class="col-md-12 xs-mt-10">
    <div class="box gradient">
        <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div id="FFB_Traceability_Percentage"></div>
        </div>
    </div>
</div>

<div class="col-md-6 xs-mt-10">
    <div class="box gradient">
        <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div id="Number_of_Supplier_Transaction_per_MONTH"></div>
        </div>
    </div>
</div>

<div class="col-md-6 xs-mt-10">
    <div class="box gradient">
        <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div id="Quantity_Sold_per_Supplier"></div>
        </div>
    </div>
</div>

<div class="col-md-6 xs-mt-10">
    <div class="box gradient">
        <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div id="palmoil_production"></div>
        </div>
    </div>
</div>

<div class="col-md-6 xs-mt-10">
    <div class="box gradient">
        <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
            <div id="palmoil_dispatch"></div>
        </div>
    </div>
</div>


</div>`

var cmbyears = Ext.create('Koltiva.store.Dashboard.CmbYears');

var cmbProvince = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: [
        'id'
        ,'name'
    ],
    autoLoad: true,
    storeVar: false,
    proxy: {
        type: 'ajax',
        url: m_api+'/dashboard/region_session/',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listener:{}
});

var cmbDistrict = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: [
        'id'
        ,'name'
    ],
    autoLoad: true,
    storeVar: false,
    proxy: {
        type: 'ajax',
        url: m_api+'/dashboard/district_session/',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listener:{}
});

var GridTransactionSupplier = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: [
        'SupplierID'
        ,'SupplierName'
        ,'District'
        ,'SubDistrict'
        ,'totalffbreceived'
    ],
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: m_api + '/dboard/store_transaction_supplier_new',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store_viewf, operation) {
            var m_subdistrict = localStorage.getItem('sub')!=null && localStorage.getItem('sub')!=undefined ? localStorage.getItem('sub') : '';
            var m_village = localStorage.getItem('vil')!=null && localStorage.getItem('vil')!=undefined ? localStorage.getItem('vil') : '';

            store_viewf.proxy.extraParams.awal = $('#datepicker1').val();
            store_viewf.proxy.extraParams.akhir = $('#datepicker2').val();
            store_viewf.proxy.extraParams.district = m_district;
            store_viewf.proxy.extraParams.subdistrict = m_subdistrict;
            store_viewf.proxy.extraParams.village = m_village;
            store_viewf.proxy.extraParams.keyword_filter = Ext.getCmp('DashboardTransactionSupplier-textSearch').getValue();
            store_viewf.proxy.extraParams.year_filter = Ext.getCmp('DashboardTransactionSupplier-dropdownYears').getValue();
            store_viewf.proxy.extraParams.province_filter = Ext.getCmp('DashboardTransactionSupplier-dropdownProvince').getValue();
            store_viewf.proxy.extraParams.district_filter = Ext.getCmp('DashboardTransactionSupplier-dropdownDistrict').getValue();
        }
    }
});

var tab = Ext.create('Ext.Panel', {
    renderTo: 'ext-content',
    height: 2600,
    frame: false,
    items: [{
        xtype: 'panel',
        border: false,
        id: 'sshFilter',
        items: []
    },
    {
        xtype: 'panel',
        border: false,
        items: [{
            xtype: 'form',
            items: [{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: 1,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: 
                            [{
                                layout: 'column',
                                border: false,
                                items: [{
                                        columnWidth: 1,
                                        layout: 'form',
                                        padding: 5,
                                        border: false,
                                        items: [
                                        {
                                            xtype: 'panel',
                                            width: '100%',
                                            html: html2
                                        }]
                                    },
                                    {
                                        columnWidth: 1,
                                        layout: 'form',
                                        padding: 5,
                                        border: false,
                                        hidden:false,
                                        items: [
                                            {
                                            xtype: 'grid',
                                            store: GridTransactionSupplier,
                                            id: 'DashboardTransactionSupplier-Grid',
                                            style: 'border:1px solid #CCC;margin-top:4px;',
                                            loadMask: true,
                                            selType: 'rowmodel',
                                            height : "auto",
                                            width: '100%',
                                            viewConfig: {
                                                deferEmptyText: false,
                                                emptyText: lang('No data Available'),
                                            },
                                            dockedItems: [
                                                {
                                                    xtype: 'pagingtoolbar',
                                                    id: 'DashboardTransactionSupplier-gridToolbar',
                                                    store: GridTransactionSupplier,
                                                    dock: 'bottom',
                                                    displayInfo: true
                                                },
                                                {
                                                    xtype: 'toolbar',
                                                    dock:'top',
                                                    // style:"background-color:#72635E;color:white",
                                                    items: [
                                                        {
                                                            xtype: 'button',
                                                            icon: varjs.config.base_url + 'images/icons/new/export.png',
                                                            text: lang('Export'),
                                                            cls: 'Sfr_BtnGridPaleBlue',
                                                            overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                                            //hidden: m_act_export_excel,
                                                            handler: function () {
                                                                Ext.MessageBox.confirm(lang('Message'), lang('Export data ?'), function (btn) {
                                                                    if (btn == 'yes') {
                                                                        Ext.MessageBox.show({
                                                                            msg: lang('Please wait...'),
                                                                            progressText: lang('Exporting...'),
                                                                            width: 300,
                                                                            wait: true,
                                                                            waitConfig: {
                                                                                interval: 200
                                                                            },
                                                                            icon: 'ext-mb-download', //custom class in msg-box.html
                                                                            animateTarget: 'mb7'
                                                                        });

                                                                        var m_subdistrict = localStorage.getItem('sub')!=null && localStorage.getItem('sub')!=undefined ? localStorage.getItem('sub') : '';
                                                                        var m_village = localStorage.getItem('vil')!=null && localStorage.getItem('vil')!=undefined ? localStorage.getItem('vil') : '';

                                                                        Ext.Ajax.request({
                                                                            url: m_api + '/dboard/store_transaction_supplier_export_excel',
                                                                            method: 'GET',
                                                                            waitMsg: lang('Please Wait'),
                                                                            params: {
                                                                                awal: $('#datepicker1').val(),
                                                                                akhir: $('#datepicker2').val(),
                                                                                district: m_district,
                                                                                subdistrict: m_subdistrict,
                                                                                village: m_village,
                                                                                keyword_filter: Ext.getCmp('DashboardTransactionSupplier-textSearch').getValue(),
                                                                                year_filter: Ext.getCmp('DashboardTransactionSupplier-dropdownYears').getValue(),
                                                                                province_filter: Ext.getCmp('DashboardTransactionSupplier-dropdownProvince').getValue(),
                                                                                district_filter: Ext.getCmp('DashboardTransactionSupplier-dropdownDistrict').getValue(),
                                                                                limit:0,
                                                                                limit:1000000000000,
                                                                            },
                                                                            success: function (data) {
                                                                                Ext.MessageBox.hide();
                                                                                if (!testJSON(data.responseText)) {
                                                                                    Ext.MessageBox.show({
                                                                                        title: 'Failed',
                                                                                        msg: 'Connection Failed',
                                                                                        buttons: Ext.MessageBox.OK,
                                                                                        animateTarget: 'mb9',
                                                                                        icon: 'ext-mb-error'
                                                                                    });
                                                                                    return false;
                                                                                }
                                        
                                                                                var jsonResp = JSON.parse(data.responseText);
                                                                                if (jsonResp.success == true) {
                                                                                    window.location = jsonResp.filenya;
                                                                                } else if (jsonResp.message == 'Empty') {
                                                                                    Ext.MessageBox.show({
                                                                                        title: lang('Success'),
                                                                                        msg: lang(jsonResp.filenya),
                                                                                        buttons: Ext.MessageBox.OK,
                                                                                        animateTarget: 'mb9',
                                                                                        icon: 'ext-mb-info'
                                                                                    });
                                                                                    return false;
                                                                                }
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
                                                                });
                                                            }
                                                        },
                                                        {
                                                            name: 'key',
                                                            id: 'DashboardTransactionSupplier-textSearch',
                                                            xtype: 'textfield',
                                                            baseCls:'Sfr_TxtfieldSearchGrid',
                                                            width: 400,
                                                            emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                                                            listeners: {
                                                                specialkey: submitOnEnterGridTransaction
                                                            }
                                                        },{
                                                            xtype:'tbspacer',
                                                            flex:1
                                                        },
                                                        {
                                                            id: 'DashboardTransactionSupplier-dropdownYears',
                                                            xtype: 'combobox',
                                                            store: cmbyears,
                                                            hidden: false,
                                                            emptyText: lang('Years'),
                                                            // value: (m_grid_filter_farmer_category != "") ? m_grid_filter_farmer_category : 'Mapped',
                                                            displayField: 'label',
                                                            valueField: 'id',
                                                            queryMode: 'local',
                                                            width: 100,
                                                            listeners: {
                                                                change: function (cb, nv, ov) {
                                                                    setFilterLs();
                                                                    Ext.getCmp('DashboardTransactionSupplier-Grid').getStore().loadPage(1);
                                                                }
                                                            }
                                                        },
                                                        {
                                                            id: 'DashboardTransactionSupplier-dropdownProvince',
                                                            xtype: 'combobox',
                                                            store: cmbProvince,
                                                            hidden: false,
                                                            emptyText: lang('All Provinces'),
                                                            // value: (m_grid_filter_farmer_category != "") ? m_grid_filter_farmer_category : 'Mapped',
                                                            displayField: 'name',
                                                            valueField: 'id',
                                                            queryMode: 'local',
                                                            width: 200,
                                                            listeners: {
                                                                change: function (cb, nv, ov) {
                                                                    setFilterLs();
                                                                    Ext.getCmp('DashboardTransactionSupplier-Grid').getStore().loadPage(1);
                                                                }
                                                            }
                                                        },
                                                        {
                                                            id: 'DashboardTransactionSupplier-dropdownDistrict',
                                                            xtype: 'combobox',
                                                            store: cmbDistrict,
                                                            hidden: false,
                                                            emptyText: lang('All District'),
                                                            // value: (m_grid_filter_farmer_category != "") ? m_grid_filter_farmer_category : 'Mapped',
                                                            displayField: 'name',
                                                            valueField: 'id',
                                                            queryMode: 'local',
                                                            width: 200,
                                                            listeners: {
                                                                change: function (cb, nv, ov) {
                                                                    setFilterLs();
                                                                    Ext.getCmp('DashboardTransactionFarmer-Grid').getStore().loadPage(1);
                                                                }
                                                            }
                                                        },
                                                        // {
                                                        //     icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                                        //     tooltip: lang('Refresh'),
                                                        //     cls:'Sfr_BtnGridBlue',
                                                        //     overCls:'Sfr_BtnGridBlue-Hover',
                                                        //     handler: function() {
                                                        //         //reload
                                                        //         setFilterLs();
                                                        //         Ext.getCmp('DashboardTransactionSupplier-Grid').getStore().loadPage(1);
                                                        //     }
                                                        // },{
                                                        //     icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                                        //     tooltip: lang('Set Filter'),
                                                        //     cls:'Sfr_BtnGridBlue',
                                                        //     overCls:'Sfr_BtnGridBlue-Hover',
                                                        //     handler: function() {
                                                        //         //advanced search
                                                        //         var winAdvFilter = Ext.create('Koltiva.view.Dboard.WinAdvancedFilterMill');
                                                        //         if (!winAdvFilter.isVisible()) {
                                                        //             winAdvFilter.center();
                                                        //             winAdvFilter.show();
                                                        //         } else {
                                                        //             winAdvFilter.close();
                                                        //         }
                                                        //     }
                                                        // }
                                                    ]
                                                }
                                            ],
                                            columns: [
                                                { 
                                                    id: 'DashboardTransactionSupplier-Grid-No'
                                                    ,text: 'No'
                                                    ,width:30
                                                    ,xtype: 'rownumberer'
                                                },
                                                {   
                                                    id: 'DashboardTransactionSupplier-Grid-SupplierID'
                                                    ,text: lang('Supplier ID')
                                                    ,dataIndex: 'SupplierID'
                                                    ,width:190 
                                                },
                                                {   
                                                    id: 'DashboardTransactionSupplier-Grid-SupplierName'
                                                    ,text: lang('Supplier Name')
                                                    ,dataIndex: 'SupplierName'
                                                    ,width:190 
                                                },
                                                {   
                                                    id: 'DashboardTransactionSupplier-Grid-District'
                                                    ,text: lang('District')
                                                    ,dataIndex: 'District'
                                                    ,width:180 
                                                },
                                                {   
                                                    id: 'DashboardTransactionSupplier-Grid-SubDistrict'
                                                    ,text: lang('Sub District')
                                                    ,dataIndex: 'SubDistrict'
                                                    ,width:180 
                                                },
                                                {   
                                                    id: 'DashboardTransactionSupplier-Grid-TotalFFBReceived'
                                                    ,text: lang('Total FFB Received (TON)')
                                                    ,dataIndex: 'totalffbreceived'
                                                    ,width:800
                                                    ,renderer: function(v, metaData, record, rowIndex, colIndex, store) {
                                                        var ffb = (parseFloat(v)/1000).toFixed(2);
                                                        return ffb;
                                                    }
                                                }
                                            ],
                                        }
                                    ]
                                    },
                                ]
                            }]
                }]
            }],
            buttons: [{
                    text: lang('Download Excel'),
                    hidden: true,
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-green',
                    handler: function () {
                        var awal = $('#datepicker1').val();
                        var akhir = $('#datepicker2').val();
                        if (awal == '') {
                            Ext.MessageBox.alert('Warning', 'Tanggal awal tidak boleh kosong!!');
                            return;
                        }
                        if (akhir == '') {
                            Ext.MessageBox.alert('Warning', 'Tanggal akhir tidak boleh kosong!!');
                            return;
                        }
                        if (Date.parse(awal) > Date.parse(akhir)) {
                            Ext.MessageBox.alert('Warning', 'Format tanggal salah!');
                        } else {
                            var delta = Date.parse(akhir) - Date.parse(awal);
                            var days = parseInt((delta / 86400 / 1000) + 1);
                            if (days > 7) {
                                Ext.MessageBox.alert('Warning', 'Periode tanggal maksima 7 hari!');
                            } else {
                                
                            }
                        }
                    }
                }]
        }]
    }]
});

$('.widget-download-list .widget-head').on('click', function (event) {
    event.preventDefault();
    /* Act on the event */
    $list = $($(this).parent().find('.widget-list')[0]);
    if ($list.hasClass('expanded')) {
        $list.removeClass('expanded');
        $list.addClass('colapsed');
    } else {
        $list.addClass('expanded');
        $list.removeClass('colapsed');
    }
});
// if (m_prov!='') dataDistrict(m_data,'traceability'); 
function number_formats (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };

    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

// function setBox(awal,akhir){
//     Ext.MessageBox.show({
//         msg: 'Loading, please wait...',
//         progressText: 'Loading...',
//         width:300,
//         wait:true,
//         waitConfig: {interval:200},
//         icon:'ext-mb-download', //custom class in msg-box.html
//         iconHeight: 50,
//         animateTarget: 'mb7'
//     }); 
    
//     Ext.Ajax.request({
//         waitMsg: lang('Please Wait'),
//         url: m_api + '/dboard/dash_get_traceability_mill',
//         method : 'get',
//         params: {  
//             awal: awal,
//             akhir: akhir,
//             // sshSupplychainID : sshSupplychainID,
//             // sshSupplyChildID : sshSupplyChildID
//         },
//         success: function(response, opts){
//             Ext.MessageBox.hide();
//             var obj = Ext.decode(response.responseText); 
            
//             $('#box1').html(number_format(obj.do,0,'.',','));
//             $('#box2').html(number_format(obj.TotalFarmerFFB,0,'.',','));
//             $('#box3').html(number_format(obj.plantation,0,'.',','));
//             $('#box4').html(number_format(obj.VolumeBrutoFarmer,0,'.',','));

//             $('#box5').html(number_format(obj.transaction,0,'.',','));
//             $('#box6').html(number_format(obj.totalReceivedMill,2,'.',','));
//             $('#box7').html(number_format(obj.production,2,'.',','));
//             // $('#box8').html(number_format(box8,2,'.',','));
//             $('#box9').html(number_format(obj.productionAll,2,'.',','));
//             $('#box10').html(number_format(obj.stock,2,'.',','));
//         },

//         failure: function(response, opts){
//             Ext.MessageBox.hide();
//             var obj = Ext.decode(response.responseText);
//             Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
//         }
//     });
    
            
//     var storeTransactionSupplier = Ext.getCmp("DashboardTransactionSupplier-Grid").getStore();
//     storeTransactionSupplier.proxy.extraParams.awal = awal;
//     storeTransactionSupplier.proxy.extraParams.akhir = akhir;

//     var ptextSearch, pAdvRowDateTransaction, pAdvDateTransactionBegin, pAdvDateTransactionEnd;

//     var patchouli_dashboard_ls = JSON.parse(localStorage.getItem('patchouli_dashboard_ls'));
//     if(patchouli_dashboard_ls != null){
//         ptextSearch = patchouli_dashboard_ls.ptextSearch;
//     }else{
//         ptextSearch = "";
//     }

//     var patchouli_adv_ls = JSON.parse(localStorage.getItem('patchouli_adv_ls'));
//     if(patchouli_adv_ls != null){
//         pAdvRowDateTransaction = patchouli_adv_ls.pAdvRowDateTransaction;
//         pAdvDateTransactionBegin = patchouli_adv_ls.pAdvDateTransactionBegin;
//         pAdvDateTransactionEnd = patchouli_adv_ls.pAdvDateTransactionEnd;
//     }else{
//         pAdvRowDateTransaction = "";
//         pAdvDateTransactionBegin = "";
//         pAdvDateTransactionEnd = "";
//     }
    
//     storeTransactionSupplier.proxy.extraParams.textSearch = ptextSearch;
//     storeTransactionSupplier.proxy.extraParams.AdvRowDateTransaction = pAdvRowDateTransaction;
//     storeTransactionSupplier.proxy.extraParams.AdvDateTransactionBegin = pAdvDateTransactionBegin;
//     storeTransactionSupplier.proxy.extraParams.AdvDateTransactionEnd = pAdvDateTransactionEnd;

//     storeTransactionSupplier.load();

//     Ext.getCmp('DashboardTransactionSupplier-textSearch').setValue(ptextSearch);
    
// } 

function setFilterLs(){
    localStorage.setItem('patchouli_dashboard_ls', JSON.stringify({
        ptextSearch: Ext.getCmp('DashboardTransactionSupplier-textSearch').getValue()
    }));

    var storeTransactionSupplier = Ext.getCmp("DashboardTransactionSupplier-Grid").getStore();

    var pdropdownYear       = Ext.getCmp('DashboardTransactionSupplier-dropdownYears').getValue();
    var pdropdownProvince   = Ext.getCmp('DashboardTransactionSupplier-dropdownProvince').getValue();
    var pdropdownDistrict   = Ext.getCmp('DashboardTransactionSupplier-dropdownDistrict').getValue();

    storeTransactionSupplier.proxy.extraParams.dropdownYear = pdropdownYear;
    storeTransactionSupplier.proxy.extraParams.dropdownProvince = pdropdownProvince;
    storeTransactionSupplier.proxy.extraParams.dropdownDistrict = pdropdownDistrict;

    storeTransactionSupplier.load();
}

function testJSON(text){
    try{
        JSON.parse(text);
        return true;
    }
    catch (error){
        return false;
    }
}

function submitOnEnterGridTransaction(field, event) {
    if (event.getKey() == event.ENTER) {
        setFilterLs();

        var storeTransactionSupplier = Ext.getCmp("DashboardTransactionSupplier-Grid").getStore();
        var patchouli_dashboard_ls = JSON.parse(localStorage.getItem('patchouli_dashboard_ls'));
        if(patchouli_dashboard_ls != null){
            ptextSearch = patchouli_dashboard_ls.ptextSearch;
        }else{
            ptextSearch = "";
        }
        
        storeTransactionSupplier.proxy.extraParams.textSearch = ptextSearch;
        storeTransactionSupplier.load();
    }
}

function column(data, div, judul, yJudul, warna, kategori, stack, koma,legen,rotate) {
    stack   = typeof stack !== 'undefined' ? stack : 'normal';
    koma    = typeof koma !== 'undefined' ? koma : 0;
    legen   = typeof legen !== 'undefined' ? legen : false;
    rotate  = typeof rotate !== 'undefined' ? rotate : -45;
    warna   = warna !== null ? warna : ['#95130b','#FFBC65','#99884C','#7F5E33','#CC7C14','#402706','#FFC80C','#FF4F0C'];

    new Highcharts.Chart({
        chart: {
            type: 'column',
            renderTo: div
        },
        title: {
            text: judul
        },
        xAxis: {
            categories: kategori,
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: yJudul
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        legend: {
            enabled: false
        },
        series: [{
            data: data
    
        }]
    });
}

function lines(data,header,div,judul,yJudul){
    console.log(div+' '+judul+' '+yJudul);
    new Highcharts.Chart({
        chart: {
            renderTo:div,
            type: 'line'
        },
        title: {
            text: judul
        },
        xAxis: {
            categories: header
        },
        yAxis: {
            title: {
                text: yJudul
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        tooltip: {
            // pointFormat: '{series.name} : <b>{point.y:,.0f}</b> kg<br>',
            // shared:true,
            // split:false,
            // crosshairs: true
            formatter: function () {
                var s = "<table style='border:1px solid #666;padding:5px'>"
        
                $.each(this.points, function () {
                    s += "<tr><td><li style='list-style-type:square;font-size:15pt;color:"+this.series.color+";padding-left:10px;border-bottom: 1px solid #666;padding-bottom:5px'></li>"+
                        "</td><td style=\"padding:5px;border:1px solid #666\">"+this.series.name+": </td>" +
                          "<td style=\"padding:5px;border:1px solid #666\"><b>"+this.y+" "+satauan+"</b></td></tr>";
                });
                s += "</table>"
                return s;
            },
            shared: true,
            useHTML: true
        },
        series: data
    });
}

var ajaxDataRenderer = function(url) {
    Ext.MessageBox.show({
        msg: lang('Loading'+'...'),
        progressText: lang('Generate data')+'...',
        width: 300,
        wait: true,
        waitConfig: {
        interval: 200
        },
        icon: 'ext-mb-download', //custom class in msg-box.html
        animateTarget: 'mb7'
    });
   $('#wrapper').addClass('cover');

    var m_subdistrict = localStorage.getItem('sub')!=null && localStorage.getItem('sub')!=undefined ? localStorage.getItem('sub') : '';
    var m_village = localStorage.getItem('vil')!=null && localStorage.getItem('vil')!=undefined ? localStorage.getItem('vil') : '';
    
   var s = [];
   m_mill = m_mill == false ? '' : m_mill;
   $.ajax({
        type: "GET",
        url: url,
        data: {
            awal: $('#datepicker1').val(),
            akhir: $('#datepicker2').val(),
            partner:m_partner,
            traceability_partner:m_traceability_partner,
            mill:m_mill,            
            district:m_district,
            subdistrict:m_subdistrict,
            village:m_village,
        },
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {    
            s=r;       
            var box1 = box2 = box3 = box4 = box5 = box6 = box7 = box8 = box9 = box10 = box11 = box12 = box13 = box14 = box15 = box16 = 0;
            if(r){
                console.log(r.data);
                box1 = r.data.total_farmer;
                box2 = r.data.total_dealer;
                box3 = r.data.total_number_of_farmer_selling;
                box4 = r.data.total_number_of_transaction_from_farmers;
                box5 = r.data.total_number_transactions_from_dealer;
                box6 = r.data.traceable_volume_received_at_mill;
                box7 = r.data.total_cpo_production;
                box8 = r.data.total_pk_production;
                
                box9 = r.data.total_number_of_cpo_refinery_transaction;
                box10 = r.data.total_number_of_pk_refinery_transaction;
                box11 = r.data.total_stock_cpo;
                box12 = r.data.total_stock_pk;
                box13 = r.data.total_traceable_cpo_dispatched_to_refinery;
                box14 = r.data.total_traceable_pk_dispatched_to_refinery;
                box15 = r.data.total_traceable_cpo_received_to_refinery;
                box16 = r.data.total_traceable_pk_received_to_refinery;

            }
            $('#box1').html(number_formats(box1,0,'.',','));
            $('#box2').html(number_formats(box2,0,'.',','));
            $('#box3').html(number_formats(box3,0,'.',','));
            $('#box4').html(number_formats(box4,2,'.',','));
            $('#box5').html(number_formats(box5,0,'.',','));
            $('#box6').html(number_formats(box6,2,'.',','));
            $('#box7').html(number_formats(box7,2,'.',','));
            $('#box8').html(number_formats(box8,2,'.',','));

            $('#box9').html(number_formats(box9,0,'.',','));
            $('#box10').html(number_formats(box10,0,'.',','));
            $('#box11').html(number_formats(box11,2,'.',','));
            $('#box12').html(number_formats(box12,2,'.',','));
            $('#box13').html(number_formats(box13,3,'.',','));
            $('#box14').html(number_formats(box14,3,'.',','));
            $('#box15').html(number_formats(box15,3,'.',','));
            $('#box16').html(number_formats(box16,3,'.',','));

            //pie chart source ffb
             $(document).ready(function() {
                var chart = {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                };
                var title = {
                    text: [lang('Source of FFB (TON)')]
                };
                var tooltip = {
                    pointFormat: '<b>{point.y:.2f}TON</b>'
                };
                var colors = ['#4572c4', '#df8244', '#f6c142', '#689ad0', '#a5a5a5'];
                var plotOptions = {
                    pie: {
                        innerSize: 160,
                        depth: 45,
                        showInLegend: true,
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y:.2f}TON',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                };
                var series = [{
                    name: '',
                    colorByPoint: true,
                    data: [{
                        name: lang('Farmer Plasma'),
                        y: r.data.traceable_volume_farmer_plasma
                    }, 
                    {
                        name: lang('Agent / Dealer / Vendor'),
                        y: r.data.traceable_volume_agent_dealer,
                        sliced: true,
                        selected: true
                    }, 
                    {
                        name: lang('Owned Estate'),
                        y: parseFloat(r.data.traceable_volume_owned_estate)
                    }, 
                    {
                        name: lang('External Estate'),
                        y: parseFloat(r.data.traceable_volume_external_estate)
                    }]
                }];

                var json = {};
                json.chart = chart;
                json.title = title;
                json.tooltip = tooltip;
                json.colors = colors;
                json.plotOptions = plotOptions;
                json.series = series;

                $('#source_ffb').highcharts(json);
            });
            //end pie chart source ffb

            $(document).ready(function() {
                var chart = {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                };
                var title = {
                    text: [lang('Traceable Sales (TON)')]
                };
                var tooltip = {
                    pointFormat: '<b>{point.y:.2f}TON</b>'
                };
                var plotOptions = {
                    pie: {
                        innerSize: 160,
                        depth: 45,
                        showInLegend: true,
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y:.2f}TON',
                        }
                    }
                };
                //console.log(r.data.traceable_sales_pie);
                var series = [{
                    name: '',
                    colorByPoint: true,
                    data: r.data.traceable_sales_pie
                }];

                var json = {};
                json.chart = chart;
                json.title = title;
                json.tooltip = tooltip;
                json.plotOptions = plotOptions;
                json.series = series;

                $('#Traceable_Sales').highcharts(json);
            });

            $(document).ready(function() {
                var chart = {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                };
                var title = {
                    text: [lang('FFB Traceability Percentage')]
                };
                var tooltip = {
                    pointFormat: '<b>{point.y:.2f}TON</b>'
                };
                var colors = ['#4572c4', '#df8244','#f6c142'];
                var plotOptions = {
                    pie: {
                        innerSize: 160,
                        depth: 45,
                        showInLegend: true,
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f}% (<b>{point.y}</b> TON)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                };
                var series = [{
                    name: '',
                    colorByPoint: true,
                    data: [{
                        name: lang('Traceable to Farmer'),
                        y: r.data.traceable_to_farmer,
                        sliced: true,
                        selected: true
                    }, {
                        name: lang('Traceable to Agent/Dealer'),
                        y: parseFloat(r.data.traceable_to_agent)
                    },
                    {
                        name: lang('Traceable to Plantation'),
                        y: parseFloat(r.data.traceable_to_plantation)
                    }]
                }];

                var json = {};
                json.chart = chart;
                json.title = title;
                json.tooltip = tooltip;
                json.colors = colors;
                json.plotOptions = plotOptions;
                json.series = series;

                $('#FFB_Traceability_Percentage').highcharts(json);
            });

             //line chart data
             $(document).ready(function() {
                var title = {
                    text: lang('Number of Supplier Transaction per MONTH')
                };
                var subtitle = {
                };
                var xAxis = {
                    categories: r.data.categories_month
                };
                var yAxis = {
                    title: {
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                };   
                var tooltip = {
                    valueSuffix: '',
                    pointFormat: "{series.name} : {point.y}"
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };
                
                var series = r.data.series_transaction;
                
                var json = {};
                json.title = title;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#Number_of_Supplier_Transaction_per_MONTH').highcharts(json);
            });
            //end line chart data

             //line chart data
             $(document).ready(function() {
                var title = {
                    text: lang('Quantity Sold per Supplier')
                };
                var subtitle = {
                };
                var xAxis = {
                    categories: r.data.categories_month
                };
                var yAxis = {
                    title: {
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                };   
                var tooltip = {
                    valueSuffix: '',
                    pointFormat: "{series.name} : {point.y:.2f}TON"
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };

                var series =  r.data.series_volume;
                
                var json = {};
                json.title = title;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#Quantity_Sold_per_Supplier').highcharts(json);
            });
            //end line chart data

            //line chart data
            $(document).ready(function() {
                var title = {
                    text: lang('PalmOil Production')
                };
                var subtitle = {
                };
                var xAxis = {
                    categories: r.data.categories_month
                };
                var yAxis = {
                    title: {
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                };   
                var tooltip = {
                    valueSuffix: '',
                    pointFormat: "{series.name} : {point.y:.3f}"
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };

                var labelNameCpo    = 'CPO';
                var labelNamePk     = 'pk';

                var series =  r.data.series_production;
                
                var json = {};
                json.title = title;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#palmoil_production').highcharts(json);
            });
            //end line chart data

            //line chart data
            $(document).ready(function() {
                var title = {
                    text: lang('PalmOil Production Dispatch')
                };
                var subtitle = {
                };
                var xAxis = {
                    categories: r.data.categories_month
                };
                var yAxis = {
                    title: {
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                };   
                var tooltip = {
                    valueSuffix: '',
                    pointFormat: "{series.name} : {point.y:.3f}"
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };

                var labelNameCpo    = 'CPO';
                var labelNamePk     = 'PK';

                var series =  r.data.series_despatch;
                
                var json = {};
                json.title = title;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#palmoil_dispatch').highcharts(json);
            });
            //end line chart data

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            Ext.getCmp('DashboardTransactionSupplier-Grid').getStore().loadPage(1);   
            Ext.MessageBox.hide();
         }
   });
    Ext.MessageBox.hide();
   return s; 
};
//console.log(s);

// //line chart with number format
// lines(s.grafik.hasil_volume,s.grafik.header,'volume_chart',lang('Volume(Ton)'),lang('Ton'), number_format(s.grafik.hasil_volume,s.grafik.header,2,'.',',') );
// lines(s.grafik.hasil_transaksi,s.grafik.header,'transaction_chart',lang('Reception'),lang('Reception'), number_format(s.grafik.hasil_transaksi,s.grafik.header,2,'.',','));

// //column chart
// column(s.val_supplybase, 'supplybase_chart', lang('Jumlah Supplybase'), lang('Supplybase'), '', s.cat_supplybase, 'total', 0, true);
// column(s.val_plot, 'plot_chart', lang('Jumlah CPs/TPH'), lang('CPs/TPH'), '', s.cat_plot, 'total', 0, true);
// column(s.val_transaction, 'transaction_bar_chart', lang('Jumlah Reception'), lang('Reception'), '', s.cat_transaction, 'total', 0, true);
// column(s.val_volume, 'volume_bar_chart', lang('Jumlah Volume (Ton)'), lang('Volume (Ton)'), '', s.cat_volume, 'total', 0, true);
// plot(s.traceable_volume.data,'traceable_volume',  s.traceable_volume.judul, '2', s.traceable_volume.yjudul, 0);

$(document).ready(function () { 

    setFilter = function()
    {
        var s = ajaxDataRenderer(m_data);
    }

    var s = ajaxDataRenderer(m_data);

})
