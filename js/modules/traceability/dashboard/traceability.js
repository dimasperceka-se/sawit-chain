	
 
    numberWithCommass = function(x)
	{
		x = x == null ? 0 : x;
        var parts = x.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
    
    function setBox(DateStart,DateEnd,sshSupplychainID,sshSupplyChildID){
        Ext.MessageBox.show({
            msg: 'Loading, please wait...',
            progressText: 'Loading...',
            width:300,
            wait:true,
            waitConfig: {interval:200},
            icon:'ext-mb-download', //custom class in msg-box.html
            iconHeight: 50,
            animateTarget: 'mb7'
        }); 
		//console.log(DateStart) 
        Ext.Ajax.request({
            waitMsg: lang('Please Wait'),
            url: m_dash + 'traceability_new',
            method : 'get',
            params: {  
                DateStart: DateStart,
                DateEnd: DateEnd,
				sshSupplychainID : sshSupplychainID,
				sshSupplyChildID : sshSupplyChildID
            },
            success: function(response, opts){
                Ext.MessageBox.hide();
                var obj = Ext.decode(response.responseText); 
                //switch(obj.success){
                $('#box1').html(numberWithCommass(obj.traceable_sales));
                $('#box2').html(obj.number_of_farmer ); 
                $('#box3').html(numberWithCommass(obj.number_of_transaction));
                $('#box4').html(obj.number_of_agregator_1 );
				$('#box5').html(obj.number_of_processing );
                $('#box_traceable_bu').html(obj.number_of_traceable_bu );
                $('#box_traceable_processing').html(obj.number_of_traceable_processing );
                $('#box_traceable_received').html(obj.number_of_traceable_received );
                
                column_one([
                    {name:lang('Potential Volume'),data:[parseFloat(obj.charts.potential.potential)]}, 
                    {name:lang('Total Traceable Volume'),data:[parseFloat(obj.charts.potential.total)]}
                ], 'chart_potential_annual_production', lang('Potential Annual Production Compared to Real Sales'), '', null, [lang('Volume')], 'normal',2,true);

                plot([
                    {name:lang('Buying Unit'), y:parseFloat(obj.charts.volume.buying_unit)},
                    {name:lang('Processing'), y:parseFloat(obj.charts.volume.processing)},
                    {name:lang('Continental'), y:parseFloat(obj.charts.volume.continental)},
                ],'chart_traceable_volume', lang('Traceable Volume at Different levels (t)'),'2',lang('Volume'),2);

                if (obj.charts.volume_sold) {
                    var cat  = [];
                    var data = [];
                    $.each(obj.charts.volume_sold, function(index, val) {
                        cat[index]  = val.label;
                        data[index] = parseInt(val.value);
                    });
                    line([
                        {name:lang(''),data:data}, 
                    ], 'chart_volume_sold', lang('Volume Sold by Farmers'), '', null, cat, 'normal',1,false);
                }
                if (obj.charts.price) {
                    var cat  = [];
                    var data = [];
                    $.each(obj.charts.price, function(index, val) {
                        cat[index]  = val.label;
                        data[index] = parseInt(val.value);
                    });
                    line([
                        {name:lang(''),data:data}, 
                    ], 'chart_price_kg', lang('Price per Kg'), '', null, cat, 'normal',0,false);
                }
                if (obj.charts.volume_delivered) {
                    var cat  = [];
                    var data = [];
                    $.each(obj.charts.volume_delivered, function(index, val) {
                        cat[index]  = val.label;
                        data[index] = parseInt(val.value);
                    });
                    line([
                        {name:lang(''),data:data}, 
                    ], 'chart_volume_delivered', lang('Volume Delivered to Processing'), '', null, cat, 'normal',1,false);
                }
                if (obj.charts.volume_shipped) {
                    var cat  = [];
                    var data = [];
                    $.each(obj.charts.volume_shipped, function(index, val) {
                        cat[index]  = val.label;
                        data[index] = parseInt(val.value);
                    });
                    line([
                        {name:lang(''),data:data}, 
                    ], 'chart_volume_shipped', lang('Volume Shipped to Continental'), '', null, cat, 'normal',1,false);
                }
            },
            failure: function(response, opts){
                Ext.MessageBox.hide();
                var obj = Ext.decode(response.responseText);
                Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
            }
        });
		
		/* sementara saya matikan dulu, ini grafik*/
        Ext.getCmp('MyPanel').getLoader().load({url: m_dash + 'trace_hub_chart?DateStart='+DateStart+'&DateEnd='+DateEnd+'&sshSupplychainID='+sshSupplychainID+'&sshSupplyChildID='+sshSupplyChildID});
		
    } 
  
    setFilter = function()
	{
	   var start = $("#datepicker1").val();
	   var end = $("#datepicker2").val();
	   setBox(start,end,m_ch,m_bs); //m_bs tau m_ch dapat nya dari filtering HTML
	}
	
    var MainSupplyOrg = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_dash + 'store_supplyorg',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
	
	 var MainSupplyOrgChild = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_dash + 'store_supplyorgChild',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
	
    var tab = Ext.create('Ext.Panel', {
        renderTo: 'ext-content',
        height: 2500,
        frame: false,
        items: [{
                xtype: 'panel',
                border: false,
                id: 'sshFilter',
                items: []
            }, {
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
                                        items: [{
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
                                                                html: '<div class="main-content" >'
                                                                    // boxes
                                                                    + '<div class="row">'

                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box1">0</div>'
                                                                    + '<div class="desc">' + lang('Total Traceable Volume (t)') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/icn_supplychaintracebility-traceable-sales.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                                    
                                                                    
                                                                    
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box2">0</div>'
                                                                    + '<div class="desc">' + lang('Total Number of Farmers Selling') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/cpg2.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                                    
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box3">-</div>'
                                                                    + '<div class="desc">' + lang('Total Number of Farmer Transactions') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/petani2.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                            
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box4">0</div>'
                                                                    + '<div class="desc">' + lang('Buying Unit') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/trader.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'

                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box5">0</div>'
                                                                    + '<div class="desc">' + lang('Processing') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/trader.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                            
                                                                    
                                                            
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box_traceable_bu">0</div>'
                                                                    + '<div class="desc">' + lang('Traceable Volume at the Buying Unit Level (t)') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/master-gnp-participant.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                            
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box_traceable_processing">0</div>'
                                                                    + '<div class="desc">' + lang('Traceable Volume at the Processing Level (t)') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/WAREHOUSE.PNG" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                            
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box_traceable_received">0</div>'
                                                                    + '<div class="desc">' + lang('Traceable Volume Received by Continental (t)') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/provinsi.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                                     
                                                                    + '</div>'
                                                                    // chart
                                                                    + '<div class="row">'
                                                                        + '<div class="col-md-6 xs-mt-20">'
                                                                            + '<div class="box gradient">'
                                                                                + '<div class="content row-fluid" style="border:1px solid lightgray;">'
                                                                                    + '<div id="chart_potential_annual_production"></div>'
                                                                                + '</div>'
                                                                            + '</div>'
                                                                        + '</div>'
                                                                        + '<div class="col-md-6 xs-mt-20">'
                                                                            + '<div class="box gradient">'
                                                                                + '<div class="content row-fluid" style="border:1px solid lightgray;">'
                                                                                    + '<div id="chart_traceable_volume"></div>'
                                                                                + '</div>'
                                                                            + '</div>'
                                                                        + '</div>'
                                                                        + '<div class="col-md-6 xs-mt-20">'
                                                                            + '<div class="box gradient">'
                                                                                + '<div class="content row-fluid" style="border:1px solid lightgray;">'
                                                                                    + '<div id="chart_volume_sold"></div>'
                                                                                + '</div>'
                                                                            + '</div>'
                                                                        + '</div>'
                                                                        + '<div class="col-md-6 xs-mt-20">'
                                                                            + '<div class="box gradient">'
                                                                                + '<div class="content row-fluid" style="border:1px solid lightgray;">'
                                                                                    + '<div id="chart_price_kg"></div>'
                                                                                + '</div>'
                                                                            + '</div>'
                                                                        + '</div>'
                                                                        + '<div class="col-md-6 xs-mt-20">'
                                                                            + '<div class="box gradient">'
                                                                                + '<div class="content row-fluid" style="border:1px solid lightgray;">'
                                                                                    + '<div id="chart_volume_delivered"></div>'
                                                                                + '</div>'
                                                                            + '</div>'
                                                                        + '</div>'
                                                                        + '<div class="col-md-6 xs-mt-20">'
                                                                            + '<div class="box gradient">'
                                                                                + '<div class="content row-fluid" style="border:1px solid lightgray;">'
                                                                                    + '<div id="chart_volume_shipped"></div>'
                                                                                + '</div>'
                                                                            + '</div>'
                                                                        + '</div>'

                                                                    + '</div>'

                                                                    + '</div>'
                                                            }]
                                                    }, {
                                                        columnWidth: 1,
                                                        layout: 'form',
                                                        padding: 5,
                                                        border: false,
                                                        hidden:true,
                                                        items: [{
                                                            xtype: 'panel',
                                                            id: 'MyPanel',
                                                            cls: 'MyPanel',
                                                            width: '100%',
                                                            loader: {
                                                                url: '',
                                                                scripts: true,
                                                                autoLoad: false,
                                                            }
                                                        }]
                                                    }]
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
                                    var DateStart = Ext.getCmp('sshDateStart').getRawValue();
                                    var DateEnd = Ext.getCmp('sshDateEnd').getRawValue();
                                    if (DateStart == '') {
                                        Ext.MessageBox.alert('Warning', 'Tanggal awal tidak boleh kosong!!');
                                        return;
                                    }
                                    if (DateEnd == '') {
                                        Ext.MessageBox.alert('Warning', 'Tanggal akhir tidak boleh kosong!!');
                                        return;
                                    }
                                    if (Date.parse(DateStart) > Date.parse(DateEnd)) {
                                        Ext.MessageBox.alert('Warning', 'Format tanggal salah!');
                                    } else {
                                        var delta = Date.parse(DateEnd) - Date.parse(DateStart);
                                        var days = parseInt((delta / 86400 / 1000) + 1);
                                        if (days > 7) {
                                            Ext.MessageBox.alert('Warning', 'Periode tanggal maksima 7 hari!');
                                        } else {
                                            
                                        }
                                    }
                                }
                            }]
                    }]
            } ]
    });
   
   ///diawal load data   
   setBox(m_awal,m_akhir,m_ch,m_bs);
