var currentLocation = window.location.href;

var html = `<div class="main-content">
            <div class="row">     
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
                            <div class="data-info col-md-8">
                                <div class="value" id="box1"></div>
                                <div class="desc">
                                    ${lang('Number of Farmer')}
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="${`${currentLocation}img/general/petani2.png`}" alt=""></div>
                        </div>
                    </div>       
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
                            <div class="data-info col-md-8">
                                <div class="value" id="box2"></div>
                                <div class="desc">
                                    ${lang('Number of Plantations')}
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="${`${currentLocation}img/general/petani2.png`}" alt=""></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
                            <div class="data-info col-md-8">
                                <div class="value" id="box3"></div>
                                <div class="desc">
                                    ${lang('Total number of farmer with transactions')}
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="${`${currentLocation}img/general/petani2.png`}" alt=""></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
                            <div class="data-info col-md-8">
                                <div class="value" id="box4"></div>
                                <div class="desc">
                                    ${lang('Total number of transactions')}
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="${`${currentLocation}img/general/petani2.png`}" alt=""></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
                            <div class="data-info col-md-8">
                                <div class="value" id="box5"></div>
                                <div class="desc">
                                    ${lang('Total FFB Received at Dealer (TON)')}
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="${`${currentLocation}img/general/master-gnp-participant.png`}" alt=""></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
                            <div class="data-info col-md-8">
                                <div class="value" id="box6"></div>
                                <div class="desc">
                                    ${lang('Total FFB Traceable to Plantation received at Dealer (TON)')}
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="${`${currentLocation}img/general/master-gnp-participant.png`}" alt=""></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
                            <div class="data-info col-md-8">
                                <div class="value" id="box7"></div>
                                <div class="desc">
                                    ${lang('Total FFB Sold (TON)')}
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="${`${currentLocation}img/general/petani2.png`}" alt=""></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade" style="border:2px solid #e2e2e2; border-radius: 8px;">
                            <div class="data-info col-md-8">
                                <div class="value" id="box8"></div>
                                <div class="desc">
                                    ${lang('Number of Delivery')}
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="${`${currentLocation}img/general/trader.png`}" alt=""></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
                                <div id="potential_annual"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
                                <div id="traceable_volume"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
                                <div id="number_line_ffb"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="border:2px solid #e2e2e2; border-radius: 8px;">
                                <div id="monthly_ffb"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`

//Store ==================================== (Begin)
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

var GridTransactionFarmer = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: [
        'SupplierName'
        ,'MemberDisplayID'
        ,'Province'
        ,'District'
        ,'SubDistrict'
        ,'Village'
        ,'Production'
        ,'VolumeNetto'
    ],
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: m_api + '/dboard/store_transaction_farmer',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners:{
        beforeload: function(store_viewfarmer, operation) {
            var m_district = localStorage.getItem('dis')!=null && localStorage.getItem('dis')!=undefined ? localStorage.getItem('dis') : '';
            var m_kec = localStorage.getItem('kec')!=null && localStorage.getItem('kec')!=undefined ? localStorage.getItem('kec') : '';
            var m_desa = localStorage.getItem('desa')!=null && localStorage.getItem('desa')!=undefined ? localStorage.getItem('desa') : '';

            store_viewfarmer.proxy.extraParams.awal = $('#datepicker1').val();
            store_viewfarmer.proxy.extraParams.akhir = $('#datepicker2').val();
            store_viewfarmer.proxy.extraParams.district = m_district;
            store_viewfarmer.proxy.extraParams.subdistrict = m_kec;
            store_viewfarmer.proxy.extraParams.village = m_desa;

            store_viewfarmer.proxy.extraParams.keyword_filter = Ext.getCmp('DashboardTransactionFarmer-textSearch').getValue();
            store_viewfarmer.proxy.extraParams.year_filter = Ext.getCmp('DashboardTransactionFarmer-dropdownYear').getValue();
            store_viewfarmer.proxy.extraParams.province_filter = Ext.getCmp('DashboardTransactionFarmer-dropdownProvince').getValue();
            store_viewfarmer.proxy.extraParams.district_filter = Ext.getCmp('DashboardTransactionFarmer-dropdownDistrict').getValue();
        },
    }
});
//Store ==================================== (End)

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
                                            html: html
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
                                            store: GridTransactionFarmer,
                                            id: 'DashboardTransactionFarmer-Grid',
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
                                                    id: 'DashboardTransactionFarmer-gridToolbar',
                                                    store: GridTransactionFarmer,
                                                    dock: 'bottom',
                                                    displayInfo: true
                                                },
                                                {
                                                    xtype: 'toolbar',
                                                    dock:'top',
                                                    style:"background-color:#FFFFF;",
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

                                                                        var m_district = localStorage.getItem('dis')!=null && localStorage.getItem('dis')!=undefined ? localStorage.getItem('dis') : '';
                                                                        var m_kec = localStorage.getItem('kec')!=null && localStorage.getItem('kec')!=undefined ? localStorage.getItem('kec') : '';
                                                                        var m_desa = localStorage.getItem('desa')!=null && localStorage.getItem('desa')!=undefined ? localStorage.getItem('desa') : '';

                                                                        Ext.Ajax.request({
                                                                            url: m_api + '/dboard/store_transaction_farmer_export_excel?' 
                                                                                + 'awal=' + $('#datepicker1').val()
                                                                                + '&akhir=' + $('#datepicker2').val()
                                                                                + '&district=' + m_district
                                                                                + '&subdistrict=' + m_kec
                                                                                + '&village=' + m_desa
                                                                                + '&keyword_filter=' + Ext.getCmp('DashboardTransactionFarmer-textSearch').getValue()
                                                                                + '&year_filter=' + Ext.getCmp('DashboardTransactionFarmer-dropdownYear').getValue()
                                                                                + '&province_filter=' + Ext.getCmp('DashboardTransactionFarmer-dropdownProvince').getValue()
                                                                                + '&district_filter=' + Ext.getCmp('DashboardTransactionFarmer-dropdownDistrict').getValue()
                                                                                + '',
                                                                            method: 'GET',
                                                                            waitMsg: lang('Please Wait'),
                                                                            success: function (data) {
                                                                                Ext.MessageBox.hide();
                                                                                var jsonResp = JSON.parse(data.responseText);
                                                                                window.location = jsonResp.filenya;
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
                                                            id: 'DashboardTransactionFarmer-textSearch',
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
                                                            icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                                            text: lang('Reset'),
                                                            cls: 'Sfr_BtnGridBlue',
                                                            overCls: 'Sfr_BtnGridBlue-Hover',
                                                            handler: function () {
                                            
                                                                Ext.getCmp('DashboardTransactionFarmer-textSearch').setValue('');
                                                                Ext.getCmp('DashboardTransactionFarmer-dropdownYear').setValue('');
                                                                Ext.getCmp('DashboardTransactionFarmer-dropdownProvince').setValue('');
                                                                Ext.getCmp('DashboardTransactionFarmer-dropdownDistrict').setValue('');
                                            
                                                                Ext.getCmp('DashboardTransactionFarmer-Grid').getStore().loadPage(1);
                                            
                                                            }
                                                        },
                                                        {
                                                            id: 'DashboardTransactionFarmer-dropdownYear',
                                                            xtype: 'combobox',
                                                            store: cmbyears,
                                                            hidden: false,
                                                            emptyText: lang('All Years'),
                                                            displayField: 'label',
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
                                                        {
                                                            id: 'DashboardTransactionFarmer-dropdownProvince',
                                                            xtype: 'combobox',
                                                            store: cmbProvince,
                                                            hidden: false,
                                                            emptyText: lang('All Provinces'),
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
                                                        {
                                                            id: 'DashboardTransactionFarmer-dropdownDistrict',
                                                            xtype: 'combobox',
                                                            store: cmbDistrict,
                                                            hidden: false,
                                                            emptyText: lang('All District'),
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
                                                        }
                                                    ]
                                                }
                                            ],
                                            columns: [
                                                { 
                                                    id: 'DashboardTransactionFarmer-Grid-No'
                                                    ,text: 'No'
                                                    ,width:50
                                                    ,xtype: 'rownumberer'
                                                },
                                                { 	
                                                    id: 'DashboardTransactionFarmer-Grid-MemberDisplayID'
                                                    ,text: lang('Farmer ID')
                                                    ,dataIndex: 'MemberDisplayID'
                                                    ,flex: 2 
                                                },
                                                { 	
                                                    id: 'DashboardTransactionFarmer-Grid-SupplierName'
                                                    ,text: lang('Farmer Name')
                                                    ,dataIndex: 'SupplierName'
                                                    ,flex: 2
                                                },
                                                { 	
                                                    id: 'DashboardTransactionFarmer-Grid-Province'
                                                    ,text: lang('Province')
                                                    ,dataIndex: 'Province'
                                                    ,flex: 2 
                                                },
                                                { 	
                                                    id: 'DashboardTransactionFarmer-Grid-District'
                                                    ,text: lang('District')
                                                    ,dataIndex: 'District'
                                                    ,flex: 2  
                                                },
                                                { 	
                                                    id: 'DashboardTransactionFarmer-Grid-SubDistrict'
                                                    ,text: lang('Sub District')
                                                    ,dataIndex: 'SubDistrict'
                                                    ,flex: 2 
                                                },
                                                { 	
                                                    id: 'DashboardTransactionFarmer-Grid-Village'
                                                    ,text: lang('Village')
                                                    ,dataIndex: 'Village'
                                                    ,flex: 2 
                                                },
                                                { 	
                                                    id: 'DashboardTransactionFarmer-Grid-EstimatedAnnualProduction'
                                                    ,text: lang('Estimated Annual Production (kg)')
                                                    ,dataIndex: 'Production'
                                                    ,flex: 2 
                                                    ,align : 'right'
                                                    ,renderer: Ext.util.Format.numberRenderer('0,000.00') 
                                                },
                                                { 	
                                                    id: 'DashboardTransactionFarmer-Grid-TotalFFBReceived'
                                                    ,text: lang('Total FFB Received (kg)')
                                                    ,dataIndex: 'VolumeNetto'
                                                    ,flex: 2 
                                                    ,align : 'right'
                                                    ,renderer: Ext.util.Format.numberRenderer('0,000.00') 
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
                        var awal = Ext.getCmp('sshawal').getRawValue();
                        var akhir = Ext.getCmp('sshakhir').getRawValue();
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

function setFilterLs(){
    Ext.getCmp('DashboardTransactionFarmer-Grid').getStore().loadPage(1);
}

function submitOnEnterGridTransaction(field, event) {
    if (event.getKey() == event.ENTER) {
        Ext.getCmp('DashboardTransactionFarmer-Grid').getStore().loadPage(1);    
    }
}

function number_format (number, decimals, dec_point, thousands_sep) {
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

var ajaxDataRenderer = function(url) {
    Ext.MessageBox.show({
        msg: lang('Loading, please wait...'),
        progressText: lang('Loading...'),
        width:300,
        wait:true,
        waitConfig: {interval:200},
        icon:'ext-mb-download', //custom class in msg-box.html
        iconHeight: 50,
        animateTarget: 'mb7'
    });
   $('#wrapper').addClass('cover');

   var s = [];
   m_mill = m_mill == false ? '' : m_mill;
   m_do = m_do == false ? '' : m_do;
    var m_district = localStorage.getItem('dis')!=null && localStorage.getItem('dis')!=undefined ? localStorage.getItem('dis') : '';
    var m_kec = localStorage.getItem('kec')!=null && localStorage.getItem('kec')!=undefined ? localStorage.getItem('kec') : '';
    var m_desa = localStorage.getItem('desa')!=null && localStorage.getItem('desa')!=undefined ? localStorage.getItem('desa') : '';
   $.ajax({
        type: "GET",
        url: url,
        data: {
            awal: $('#datepicker1').val(),
            akhir: $('#datepicker2').val(),
            district: m_district,
            kec: m_kec, 
            desa: m_desa, 
            prov: localStorage.getItem("prov"),
            daer: m_daer,
            partner:m_partner,
            traceability_partner:m_traceability_partner,
            mill:m_mill,
            do:m_do,
            agent:m_agent
        },
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {

            var box1 = box2 = box3 = box4 = box5 = box6 = box7 = box8 = box9 = box10 = 0;
            if(r){
                box1    = r.number_of_farmer;
                box2    = r.number_of_plantations;
                box3    = r.total_number_farmer_with_transaction;
                box4    = r.total_number_of_transactions;
                box5    = r.total_ffb_received_at_dealer;
                box6    = r.total_ffb_traceable_to_plantation_at_dealer;
                box7    = r.total_ffb_sold;
                box8    = r.number_of_delivery;
            }
            $('#box1').html(number_format(box1,0,'.',','));
            $('#box2').html(number_format(box2,0,'.',','));
            $('#box3').html(number_format(box3,0,'.',','));
            $('#box4').html(number_format(box4,0,'.',','));

            $('#box5').html(number_format(box5,2,'.',','));
            $('#box6').html(number_format(box6,2,'.',','));
            $('#box7').html(number_format(box7,2,'.',','));
            $('#box8').html(number_format(box8,0,'.',','));

            //basic chart data potential annual
            $(document).ready(function() {
                var chart = {
                    type: 'column'
                };
                var title = {
                    text: 'Potential Annual Production Compared to Real Sales'   
                };
                var subtitle = {
                };
                var xAxis = {
                    categories: [r.potential_annual.label]
                };
                var yAxis = {
                };   
                var tooltip = {
                    valueSuffix: '',
                    pointFormat: "Value: {point.y:.2f}"
                };
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };

                var PotentialVolume = 'Potential Volume';
                var TotalTraceableVolume    = 'Total Traceable Volume';

                var series =  [{
                    name: PotentialVolume,
                    data: [r.potential_annual.data['0'].data
                    ],
                    color: "#cfd3db"   
                },{
                    name: TotalTraceableVolume,
                    data: [r.potential_annual.data['1'].data
                    ],
                    color: "#f6be73"
                }];
                
                var json = {};
                json.chart = chart;
                json.title = title;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#potential_annual').highcharts(json);
            });
            //end basic chart data
            
            //pie chart ffb traceablity
            $(document).ready(function() {
                var chart = {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                };
                var title = {
                    text: [r.traceable_volume.judul]
                };
                var colors = ['#4572c4', '#f6be73', '#cfd3db', '#c4810e'];
                var plotOptions = {
                    pie: {
                        innerSize: 150,
                        depth: 45,
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                };
                var series = [{
                    name: '',
                    data: [
                        [r.traceable_volume.data['2'].name, r.traceable_volume.data['2'].y],
                        [r.traceable_volume.data['0'].name, r.traceable_volume.data['0'].y],
                        [r.traceable_volume.data['1'].name, r.traceable_volume.data['1'].y]
                    ]     
                }]

                var json = {};
                json.chart = chart;
                json.title = title;
                json.colors = colors;
                json.plotOptions = plotOptions;
                json.series = series;

                $('#traceable_volume').highcharts(json);
            });
            //end pie chart ffb traceablity

            //line chart data
            $(document).ready(function() {
                var title = {
                    text: lang('Monthly FFB Received')   
                };
                var colors = ['#4572c4', '#f6be73', '#cfd3db', '#c4810e'];
                var subtitle = {
                };
                var xAxis = {
                    categories: r.categories_month
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
                    pointFormat: "{series.name}: {point.y:.2f}MT"
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };

                var series =  r.series_ffb_transaction;
                
                var json = {};
                json.title = title;
                json.colors = colors;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#number_line_ffb').highcharts(json);
            });
            //end line chart data

            // //basic chart
            $(document).ready(function() {
                var chart = {
                    type: 'column'
                };
                var title = {
                    text: lang('Monthly FFB Delivery')
                };
                var colors = ['#4572c4', '#f6be73'];
                var subtitle = {
                };
                var xAxis = {
                    categories: r.categories_month
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
                    pointFormat: "{series.name}: {point.y:.2f}MT"
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };

                var series =  r.series_ffb_delivery;
                
                var json = {};
                json.chart = chart;
                json.title = title;
                json.colors = colors;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#monthly_ffb').highcharts(json);
            });
            //end basic chart data

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            Ext.getCmp('DashboardTransactionFarmer-Grid').getStore().loadPage(1);    
            Ext.MessageBox.hide();
         }
   });
   return s; 
};

$(document).ready(function () {	

    setFilter = function()
    {
        var s = ajaxDataRenderer(m_data);
    }

    var s = ajaxDataRenderer(m_data);

})

