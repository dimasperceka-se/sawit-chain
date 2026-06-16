	
 
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
            progressText: 'Saving...',
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
            url: m_dash + 'traceability_index_new',
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

            },
            failure: function(response, opts){
                Ext.MessageBox.hide();
                var obj = Ext.decode(response.responseText);
                Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
            }
        });
		
		/* sementara saya matikan dulu, ini grafik*/
        Ext.getCmp('MyPanel').getLoader().load({url: m_dash + 'trace_hub_chartnew?DateStart='+DateStart+'&DateEnd='+DateEnd+'&sshSupplychainID='+sshSupplychainID+'&sshSupplyChildID='+sshSupplyChildID});
		
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
                                                                    + '<div class="row">'
                                                            
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box1">0</div>'
                                                                    + '<div class="desc">' + lang('Traceable Sales (t)') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/NDPE-Labor-right-abuse.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                                    
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box2">0</div>'
                                                                    + '<div class="desc">' + lang('Number of Farmers Selling') + '</div>'
                                                                    + '</div>'
                                                                    + '<div class="icon col-md-4"><img src="' + m_base_url + 'img/general/Average-Number-of-Plantations-Owned-by-Farmers.png" alt=""></div>'
                                                                    + '</div>'
                                                                    + '</div>'
                                                                    
                                                                    + '<div class="col-md-3">'
                                                                    + '<div class="widget widget-tile hvr-fade">'
                                                                    + '<div class="data-info col-md-8">'
                                                                    + '<div class="value" id="box3">-</div>'
                                                                    + '<div class="desc">' + lang('Number of Farmer Transactions') + '</div>'
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
