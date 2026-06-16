Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();

    var ProvinceID;
    var DistrictID;
    var Keyword;

   var skop = Ext.create('Ext.data.Store', {
       fields: ['id', 'label'],
       data : [
           {"id":"1", "label":lang("Semua Kategori"), "checked":true},
           {"id":"2", "label":lang("Petani")},
           {"id":"8", "label":lang("Petani Tersertifikasi")},
           {"id":"3", "label":lang("Pembibitan")},
           {"id":"4", "label":lang("Demoplot")},
           {"id":"5", "label":lang("Organisasi Petani")},
           {"id":"6", "label":lang("Gudang")},
           {"id":"7", "label":lang("Pedagang")},
           //{"id":"8", "label":"Unit Pembelian"}
       ]
   });
    var store_Province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','province'],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: m_Province,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_District = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','district'],
        proxy: {
            type: 'ajax',
            url: m_District,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

     var contMap = Ext.create('Ext.panel.Panel', {
          width: '95%',
          minHeight: 800,
          style: 'border:1px solid #CCC;',
          renderTo: 'ext-content',
          layout: 'fit',
          items: [

                // {
                //     xtype: 'container',
                //     flex: 1,
                //     items: [
                    {
                        xtype: 'panel',
                        id: 'contMapView',
                        flex:1,
                        layout: 'fit',
                        items: [
                         {
                            xtype: 'gridpanel',
                            height: 50,
                             width: '90%',
                            header: false,
                            title: lang('My Grid Panel'),
                            columns: [
                                {
                                    xtype: 'gridcolumn',
                                    dataIndex: 'string',
                                    hidden: true,
                                    text: lang('Farmer Name')
                                },
                                {
                                    xtype: 'numbercolumn',
                                    dataIndex: 'number',
                                    hidden: true,
                                    text: lang('Garden Nr')
                                },
                                {
                                    xtype: 'numbercolumn',
                                    dataIndex: 'bool',
                                    hidden: true,
                                    text: lang('Survey Nr')
                                },
                                {
                                    xtype: 'datecolumn',
                                    dataIndex: 'date',
                                    hidden: true,
                                    text: lang('Last Update')
                                }
                            ],
                            dockedItems: [
                                {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            id: 'FarmerKeyword',
                                            width: 287,
                                            //fieldLabel: lang('Farmer'),
                                            emptyText:lang('Cari berdasar ID/nama')
                                        },{
                                             xtype      : 'combo',
                                             store : skop,
                                             id:'skop',
                                             queryMode: 'local',
                                             displayField: 'label',
                                             valueField: 'id',
                                             value:'1'
                                         },{
                                              id: 'province',
                                              name: 'province',
                                              xtype: 'combo',
                                              store: store_Province,
                                              displayField: 'province',
                                              valueField: 'id',
                                              queryMode: 'local',
                                              emptyText:lang('Pilih provinsi'),
                                              listeners: {
                                                  change: function (cb, nv, ov) {
                                                      ProvinceID = nv
                                                      store_District.load({
                                                          params: {
                                                              ProvinceID: nv
                                                          }});
                                                      Ext.getCmp('district').reset();
                                                  }
                                              }
                                        },
                                        {
                                              id: 'district',
                                              name: 'district',
                                              xtype: 'combo',
                                              store: store_District,
                                              displayField: 'district',
                                              valueField: 'id',
                                              queryMode: 'local',
                                              emptyText:lang('Pilih Kabupaten'),
                                              listeners: {
                                                  change: function (cb, nv, ov) {
                                                      DistrictID = nv
                                                  }
                                              }
                                        },
                                        {
                                            xtype: 'button',
                                            text: lang('Search'),
                                            handler:function(){

                                                Keyword = Ext.getCmp('FarmerKeyword').getValue();
                                                if(ProvinceID=='' || ProvinceID==undefined){
                                                    Ext.MessageBox.alert('Warning',lang('Silahkan pilih provinsi dahulu'));
                                                    return;
                                                }
                                                /// start
                                      //  console.log('after render');
                                        var geocoder;
                                       // geocoder = new google.maps.Geocoder();
                                        google.maps.visualRefresh = true;

                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_geospatialByDistrict+'/ProvinceID/'+ProvinceID+'/DistrictID/'+DistrictID+'/Keyword/'+Keyword+'/skop/'+
                                                Ext.getCmp('skop').getValue(),
                                            method : 'GET',
                                            success: function(response, opts){
                                            var result = Ext.decode(response.responseText);

                                            if(result.total>0){

                                            var lastIdx = result.length;
                                            //console.log(result);
                                            if (result.data.length>0) var CenterLatlng = new google.maps.LatLng(result.data[0].Latitude,result.data[0].Longitude);
                                            else if (result.tersertifikasi.length>0) var CenterLatlng = new google.maps.LatLng(result.tersertifikasi[0].Latitude,result.tersertifikasi[0].Longitude);
                                            else if (result.nursery.length>0) var CenterLatlng = new google.maps.LatLng(result.nursery[0].Latitude,result.nursery[0].Longitude);
                                            else if (result.demoplot.length>0) var CenterLatlng = new google.maps.LatLng(result.demoplot[0].Latitude,result.demoplot[0].Longitude);
                                            else if (result.koperasi.length>0) var CenterLatlng = new google.maps.LatLng(result.koperasi[0].Latitude,result.koperasi[0].Longitude);
                                            else if (result.gudang.length>0) var CenterLatlng = new google.maps.LatLng(result.gudang[0].Latitude,result.gudang[0].Longitude);
                                            else if (result.pedagang.length>0) var CenterLatlng = new google.maps.LatLng(result.pedagang[0].Latitude,result.pedagang[0].Longitude);
                                            else if (result.unitbuying.length>0) var CenterLatlng = new google.maps.LatLng(result.unitbuying[0].Latitude,result.unitbuying[0].Longitude);
                                             var mapOptions = {
                                                 zoom: 10,
                                                 center: CenterLatlng,
                                                 mapTypeId: google.maps.MapTypeId.ROADMAP
                                             };

                                              var map = new google.maps.Map(document.getElementById('mymap'), mapOptions);

                                                Ext.each (result.data, function (field, index, fields) {
                                                    //console.log(result.data[index].Longitude)
                                                    var LastLatlng = new google.maps.LatLng(result.data[index].Latitude,result.data[index].Longitude);

                                                        var marker = new google.maps.Marker({
                                                            position: LastLatlng,
                                                            map: map,
                                                            icon: varjs.config.base_url+'images/tree.png',
                                                            title: result.data[index].FarmerName+' - '+lang('kebun')+' '+ result.data[index].GardenNr
                                                        });
                                                        var image_url = m_photo+'/'+result.data[index].Photo.replace(/\\/g,"/");
                                                        var prod
                                                        if (result.data[index].Produktivitas) prod = parseInt(result.data[index].Produktivitas)
                                                        else prod = '-';
                                                        var infowindow = new google.maps.InfoWindow({
                                                           content : '<table height="200px" width="320px" border="0"><tr><td rowspan="5" width="115px"><div style="width:115px;height:110px;overflow:hidden"><img id="photo_'+result.data[index].FarmerID+'" width="100px" src="'+m_photo+'/no-user.jpg'+'" style="padding:5px" align="left"></div></td><td width="80px">'+lang('Farmer ID')+'</td><td>: '+result.data[index].FarmerID+'</td></tr>'+
                                                               '<tr><td>'+lang('Nama')+'</td><td>: '+result.data[index].FarmerName+'</td></tr>'+
                                                               '<tr><td>'+lang('Luas lahan')+'</td><td>: '+result.data[index].GardenHaUnCertified+' Ha</td></tr>'+
                                                               '<tr><td>'+lang('Produksi')+'</td><td>: '+result.data[index].totalProduksi+' Kg</td></tr>'+
                                                               '<tr><td>'+lang('Pohon')+'</td><td>: '+nnumber_format(result.data[index].Pohon)+' '+lang('Pohon')+'</td></tr>'+
                                                               '<tr><td colspan="3">'+lang('Produktivitas')+' '+prod+' ('+lang('Kg/Ha/Tahun')+')</td></tr>'+
                                                               '<tr><td colspan="3">@ '+result.data[index].Area+'</td></tr><tr><td colspan="3" align="center" style="text-align:center"><a href="#" onClick="displayBeforeCetak('+result.data[index].FarmerID+')" class="btn" style="line-height: 14px;"> '+lang('cetak')+' </a></td></tr></table>'
                                                                });
                                                             google.maps.event.addListener(marker, 'click', function() {
                                                               infowindow.open(map,marker);
                                                               imageExists(image_url,'photo_'+result.data[index].FarmerID)
                                                            });
                                                         if (result.total==1) infowindow.open(map,marker);

                                                }, this);

                                                Ext.each (result.tersertifikasi, function (field, index, fields) {
                                                    //console.log(result.tersertifikasi[index].Longitude)
                                                    var LastLatlng = new google.maps.LatLng(result.tersertifikasi[index].Latitude,result.tersertifikasi[index].Longitude);

                                                        var marker = new google.maps.Marker({
                                                            position: LastLatlng,
                                                            map: map,
                                                            icon: varjs.config.base_url+'images/tree.png',
                                                            title: result.tersertifikasi[index].FarmerName+' - '+lang('kebun')+' '+ result.tersertifikasi[index].GardenNr
                                                        });
                                                        var image_url = m_photo+'/'+result.tersertifikasi[index].Photo.replace(/\\/g,"/");
                                                        var prod
                                                        if (result.tersertifikasi[index].Produktivitas) prod = parseInt(result.tersertifikasi[index].Produktivitas)
                                                        else prod = '-';
                                                        var infowindow = new google.maps.InfoWindow({
                                                           content : '<table height="200px" width="320px" border="0"><tr><td rowspan="5" width="115px"><div style="width:115px;height:110px;overflow:hidden"><img id="photo_'+result.tersertifikasi[index].FarmerID+'" width="100px" src="'+m_photo+'/no-user.jpg'+'" style="padding:5px" align="left"></div></td><td width="80px">Farmer ID</td><td>: '+result.tersertifikasi[index].FarmerID+'</td></tr>'+
                                                               '<tr><td>'+lang('Nama')+'</td><td>: '+result.tersertifikasi[index].FarmerName+'</td></tr>'+
                                                               '<tr><td>'+lang('Luas lahan')+'</td><td>: '+result.tersertifikasi[index].GardenHaUnCertified+' Ha</td></tr>'+
                                                               '<tr><td>'+lang('Produksi')+'</td><td>: '+result.tersertifikasi[index].totalProduksi+' Kg</td></tr>'+
                                                               '<tr><td>'+lang('Pohon')+'</td><td>: '+nnumber_format(result.tersertifikasi[index].Pohon)+' '+lang('Pohon')+'</td></tr>'+
                                                               '<tr><td colspan="3">'+lang('Produktivitas')+' '+prod+' ('+lang('Kg/Ha/Tahun')+')</td></tr>'+
                                                               '<tr><td colspan="3">@ '+result.tersertifikasi[index].Area+'</td></tr><tr><td colspan="3" align="center" style="text-align:center"><a href="#" onClick="displayBeforeCetak('+result.tersertifikasi[index].FarmerID+')" class="btn" style="line-height: 14px;"> '+lang('cetak')+' </a></td></tr></table>'
                                                                });
                                                             google.maps.event.addListener(marker, 'click', function() {
                                                               infowindow.open(map,marker);
                                                               imageExists(image_url,'photo_'+result.tersertifikasi[index].FarmerID)
                                                            });
                                                         if (result.total==1) infowindow.open(map,marker);

                                                }, this);

                                                Ext.each (result.nursery, function (field, index, fields) {
                                                   var LastLatlng = new google.maps.LatLng(result.nursery[index].Latitude,result.nursery[index].Longitude);
                                                   var marker = new google.maps.Marker({
                                                      position: LastLatlng,
                                                      map: map,
                                                      icon: varjs.config.base_url+'img/general/pembibitan_24.png',
                                                      title: result.nursery[index].GroupName
                                                   });
                                                   var infowindow = new google.maps.InfoWindow({
                                                      content : '<table height="120px" width="300px" border="0">'+
                                                         '<tr><td colspan="2"><b>Kelompok</b></td></tr>'+
                                                         '<tr><td>ID</td><td>: '+result.nursery[index].CPGid+'</td></tr>'+
                                                         '<tr><td>'+lang('Nama')+'</td><td>: '+result.nursery[index].GroupName+'</td></tr>'+
                                                         '<tr><td colspan="2"><b>Nursery</b></td></tr>'+
                                                         '<tr><td>'+lang('Penanggung Jawab')+'</td><td>: '+result.nursery[index].FarmerName+'</td></tr>'+
                                                         '<tr><td>'+lang('Tanggal Berdiri')+'</td><td>: '+result.nursery[index].Established+'</td></tr>'+
                                                         '<tr><td>'+lang('Luas')+'</td><td>: '+nnumber_format(result.nursery[index].Panjang*result.nursery[index].Lebar)+'</td></tr>'+
                                                         '<tr><td>'+lang('Kapasitas')+'</td><td>: '+nnumber_format(result.nursery[index].Kapasitas)+'</td></tr>'+
                                                         '</table>'});
                                                      google.maps.event.addListener(marker, 'click', function() {
                                                         infowindow.open(map,marker);
                                                      });
                                                      if (result.total==1) infowindow.open(map,marker);
                                                }, this);

                                                Ext.each (result.demoplot, function (field, index, fields) {
                                                   var LastLatlng = new google.maps.LatLng(result.demoplot[index].Latitude,result.demoplot[index].Longitude);
                                                   var marker = new google.maps.Marker({
                                                      position: LastLatlng,
                                                      map: map,
                                                      icon: varjs.config.base_url+'img/general/demoplot_24.png',
                                                      title: result.demoplot[index].GroupName
                                                   });
                                                   var infowindow = new google.maps.InfoWindow({
                                                      content : '<table height="120px" width="300px" border="0">'+
                                                         '<tr><td colspan="2"><b>Kelompok</b></td></tr>'+
                                                         '<tr><td>CPG ID</td><td>: '+result.demoplot[index].CPGid+'</td></tr>'+
                                                         '<tr><td>'+lang('Nama Kelompok')+'</td><td>: '+result.demoplot[index].GroupName+'</td></tr>'+
                                                         '<tr><td colspan="2"><b>demoplot</b></td></tr>'+
                                                         '<tr><td>Farmer ID</td><td>: '+result.demoplot[index].FarmerID+'</td></tr>'+
                                                         '<tr><td>'+lang('Nama Petani')+'</td><td>: '+result.demoplot[index].FarmerName+'</td></tr>'+
                                                         '<tr><td>'+lang('Luas Lahan')+' (Ha)</td><td>: '+nnumber_format(result.demoplot[index].GardenHaUnCertified)+'</td></tr>'+
                                                         '<tr><td>'+lang('Produksi')+' (Kg)</td><td>: '+nnumber_format(result.demoplot[index].totalProduksi)+'</td></tr>'+
                                                         '<tr><td>'+lang('Pohon')+'</td><td>: '+nnumber_format(result.demoplot[index].Pohon)+'</td></tr>'+
                                                         '<tr><td>'+lang('Produktivitas')+' (Kg/Ha/Tahun)</td><td>: '+nnumber_format(result.demoplot[index].Produktivitas)+'</td></tr>'+
                                                         '</table>'});
                                                      google.maps.event.addListener(marker, 'click', function() {
                                                         infowindow.open(map,marker);
                                                      });
                                                      if (result.total==1) infowindow.open(map,marker);
                                                }, this);

                                                Ext.each (result.koperasi, function (field, index, fields) {
                                                   var LastLatlng = new google.maps.LatLng(result.koperasi[index].Latitude,result.koperasi[index].Longitude);
                                                   var marker = new google.maps.Marker({
                                                      position: LastLatlng,
                                                      map: map,
                                                      icon: varjs.config.base_url+'img/general/koperasi_24.png',
                                                      title: result.koperasi[index].GroupName
                                                   });
                                                   var infowindow = new google.maps.InfoWindow({
                                                      content : '<table height="80px" width="300px" border="0">'+
                                                         '<tr><td>'+lang('Nama')+'</td><td>: '+result.koperasi[index].CoopName+'</td></tr>'+
                                                         '<tr><td>'+lang('Desa')+'</td><td>: '+result.koperasi[index].Village+'</td></tr>'+
                                                         '<tr><td>'+lang('Kecamatan')+'</td><td>: '+result.koperasi[index].SubDistrict+'</td></tr>'+
                                                         '<tr><td>'+lang('Ketua')+'</td><td>: '+result.koperasi[index].StaffName+'</td></tr>'+
                                                         '</table>'});
                                                      google.maps.event.addListener(marker, 'click', function() {
                                                         infowindow.open(map,marker);
                                                      });
                                                      if (result.total==1) infowindow.open(map,marker);
                                                }, this);

                                                Ext.each (result.gudang, function (field, index, fields) {
                                                   var LastLatlng = new google.maps.LatLng(result.gudang[index].Latitude,result.gudang[index].Longitude);
                                                   var marker = new google.maps.Marker({
                                                      position: LastLatlng,
                                                      map: map,
                                                      icon: varjs.config.base_url+'img/general/gudang_24.png',
                                                      title: result.gudang[index].GroupName
                                                   });
                                                   var infowindow = new google.maps.InfoWindow({
                                                      content : '<table height="80px" width="300px" border="0">'+
                                                         '<tr><td>'+lang('Nama')+'</td><td>: '+result.gudang[index].CoopName+'</td></tr>'+
                                                         '<tr><td>'+lang('Desa')+'</td><td>: '+result.gudang[index].Village+'</td></tr>'+
                                                         '<tr><td>'+lang('Kecamatan')+'</td><td>: '+result.gudang[index].SubDistrict+'</td></tr>'+
                                                         '<tr><td>'+lang('Ketua')+'</td><td>: '+result.gudang[index].StaffName+'</td></tr>'+
                                                         '</table>'});
                                                      google.maps.event.addListener(marker, 'click', function() {
                                                         infowindow.open(map,marker);
                                                      });
                                                      if (result.total==1) infowindow.open(map,marker);
                                                }, this);

                                                Ext.each (result.pedagang, function (field, index, fields) {
                                                   var LastLatlng = new google.maps.LatLng(result.pedagang[index].Latitude,result.pedagang[index].Longitude);
                                                   var marker = new google.maps.Marker({
                                                      position: LastLatlng,
                                                      map: map,
                                                      icon: varjs.config.base_url+'img/general/trader_24.png',
                                                      title: result.pedagang[index].GroupName
                                                   });
                                                   var infowindow = new google.maps.InfoWindow({
                                                      content : '<table height="80px" width="300px" border="0">'+
                                                         '<tr><td>'+lang('Nama')+'</td><td>: '+result.pedagang[index].CoopName+'</td></tr>'+
                                                         '<tr><td>'+lang('Desa')+'</td><td>: '+result.pedagang[index].Village+'</td></tr>'+
                                                         '<tr><td>'+lang('Kecamatan')+'</td><td>: '+result.pedagang[index].SubDistrict+'</td></tr>'+
                                                         '<tr><td>'+lang('Ketua')+'</td><td>: '+result.pedagang[index].StaffName+'</td></tr>'+
                                                         '</table>'});
                                                      google.maps.event.addListener(marker, 'click', function() {
                                                         infowindow.open(map,marker);
                                                      });
                                                      if (result.total==1) infowindow.open(map,marker);
                                                }, this);

                                                Ext.each (result.unitbuying, function (field, index, fields) {
                                                   var LastLatlng = new google.maps.LatLng(result.unitbuying[index].Latitude,result.unitbuying[index].Longitude);
                                                   var marker = new google.maps.Marker({
                                                      position: LastLatlng,
                                                      map: map,
                                                      icon: varjs.config.base_url+'img/general/unit_pembelian_24.png',
                                                      title: result.unitbuying[index].GroupName
                                                   });
                                                   var infowindow = new google.maps.InfoWindow({
                                                      content : '<table height="80px" width="300px" border="0">'+
                                                         '<tr><td>'+lang('Nama')+'</td><td>: '+result.unitbuying[index].CoopName+'</td></tr>'+
                                                         '<tr><td>'+lang('Desa')+'</td><td>: '+result.unitbuying[index].Village+'</td></tr>'+
                                                         '<tr><td>'+lang('Kecamatan')+'</td><td>: '+result.unitbuying[index].SubDistrict+'</td></tr>'+
                                                         '<tr><td>'+lang('Ketua')+'</td><td>: '+result.unitbuying[index].StaffName+'</td></tr>'+
                                                         '</table>'});
                                                      google.maps.event.addListener(marker, 'click', function() {
                                                         infowindow.open(map,marker);
                                                      });
                                                      if (result.total==1) infowindow.open(map,marker);
                                                }, this);
                                            }  else {
                                                Ext.MessageBox.alert('Warning',lang('Tidak terdapat data geospatial !!'));
                                            }
                                            //end condition

                                            }
                                        });


                                        /// end

                                            }
                                        }
                                    ]
                                }
                            ]
                        },

                        {
                            xtype: 'gmappanel',
                            id: 'mymap',
                            cls: 'reset-box-sizing',
                            zoomLevel: 10,
                            gmapType: 'map',
                            mapConfOpts: ['enableScrollWheelZoom','enableDoubleClickZoom','enableDragging'],
                            mapControls: ['GSmallMapControl','GMapTypeControl'],
                            center: {
                                 geoCodeAddr: 'Jakarta, Indonesia'
                            },
                            listeners :{

                              afterRender:function(){
                                        /// start
                                      //  console.log('after render');
                                        var geocoder;
                                       // geocoder = new google.maps.Geocoder();
                                        google.maps.visualRefresh = true;

                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_geospatial,
                                            method : 'GET',
                                            success: function(response, opts){
                                            var result = Ext.decode(response.responseText);
                                            var lastIdx = result.length;
                                            //console.log(result);
                                            var CenterLatlng = new google.maps.LatLng(result.data[0].Latitude,result.data[0].Longitude);
                                                var mapOptions = {
                                                    zoom: 6,
                                                    center: CenterLatlng,
                                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                                };

                                              var map = new google.maps.Map(document.getElementById('mymap'), mapOptions);

                                                Ext.each (result.data, function (field, index, fields) {
                                                    //console.log(result.data[index].Longitude)
                                                    var LastLatlng = new google.maps.LatLng(result.data[index].Latitude,result.data[index].Longitude);

                                                        var marker = new google.maps.Marker({
                                                            position: LastLatlng,
                                                            map: map,
                                                            icon: varjs.config.base_url+'images/tree.png',
                                                            title: result.data[index].FarmerName+' - kebun '+ result.data[index].GardenNr
                                                        });
                                                        var image_url = m_photo+'/'+result.data[index].Photo.replace(/\\/g,"/");
                                                        var infowindow = new google.maps.InfoWindow({
                                                           content : '<table width="100%" border="0"><tr><td rowspan="5" width="105px"><img width="100px" src="'+image_url+'" style="padding:5px" align="left"></td><td>'+result.data[index].FarmerID+'</td></tr><tr><td>'+result.data[index].FarmerName+'</td></tr><tr><td>'+result.data[index].GardenHaUnCertified+' Ha</td></tr><tr><td>'+result.data[index].totalProduksi+' Kg</td></tr><tr><td>'+result.data[index].Pohon+'</td></tr>'+
                                                               '<tr><td colspan="2">@ '+result.data[index].Area+'</td></tr><tr><td colspan="2" align="center" style="text-align:center"><a href="#" onClick="displayBeforeCetak('+result.data[index].FarmerID+')" class="btn" style="line-height: 14px;"> '+lang('cetak')+' </a></td></tr></table>'
                                                                });
                                                             google.maps.event.addListener(marker, 'click', function() {
                                                            infowindow.open(map,marker);
                                                               imageExists(image_url,'photo_'+result.data[index].FarmerID)
                                                            });
                                                         if (result.total==1) infowindow.open(map,marker);

                                                }, this);


                                            }
                                        });


                                        /// end
                                    }

                            }
                        }]
                    }]

            //     }
            // ]

     });




});
function imageExists(image_url,id){
   $.get(image_url).done(function() {
       document.getElementById(id).src = image_url+'#'+new Date().getTime();
   })
}

    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
   }
var FarmerID,SurveyID;

    var store_CekSurvey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','surveya'],
        proxy: {
            type: 'ajax',
            url: m_CekSurvey,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var DataBeforeCetak = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 470,
        height:70,
        id:'dataBeforeCetak',
        xtype: 'form',
        bodyPadding: 5,
        layout: {
            align: 'stretch',
            type: 'vbox'
        },
        header: false,
        title: lang('My Form'),
        items: [{
                   xtype: 'textfield',
                   id: 'result',
                   name: 'tipe',
                   value: '0',
                   hidden :true
               },
            {
                xtype: 'combobox',
                id: 'survey',
                name: 'id',
                store: store_CekSurvey,
                fieldLabel: lang('Survey'),
                displayField: 'surveya',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function (cb, nv, ov) {
                        SurveyID = nv
                        //console.log(SurveyID);
                    }
                }
            },
            {
                xtype: 'container',
                height:43,
                layout: {
                    align: 'stretch',
                    pack: 'center',
                    padding: 2,
                    type: 'hbox'
                },
                items: [
                    {
                         xtype: 'button',
                         text: lang('Cetak P1'),
                         margin: '5 5 5 2',
                         scale: 'large',
                         ui: 's-button',
                         disabled: false,
                         cls: 's-blue',
                         handler: function() {
                           if (!isNumber(SurveyID)) {alert('Silahkan pilih surveynya');return;}
                          winBeforeCetak.hide();

                              // hasil
                              preview_cetak_surat(m_cetak_result_farmer+'FarmerID/'+FarmerID+'/SurveyID/'+SurveyID);

                         }
                    },
                    {
                         xtype: 'button',
                         text: lang('Cetak N1'),
                         margin: '5px',
                         scale: 'large',
                         ui: 's-button',
                          cls: 's-blue',
                         disabled: false,
                         handler: function() {
                           if (!isNumber(SurveyID)) {alert('Silahkan pilih surveynya');return;}
                          winBeforeCetak.hide();

                              // hasil
                               preview_cetak_surat(m_cetak_result_nutrisi+'FarmerID/'+FarmerID+'/SurveyID/'+SurveyID);

                         }
                    },
                    {
                        xtype: 'button',
                         text: lang('Cetak PPI'),
                         margin: '5px',
                         scale: 'large',
                         ui: 's-button',
                         cls: 's-blue',
                         disabled: false,
                         handler: function() {
                           if (!isNumber(SurveyID)) {alert('Silahkan pilih type dan surveynya');return;}
                           winBeforeCetak.hide();

                              // hasil
                              preview_cetak_surat(m_cetak_result_ppi2012+'FarmerID/'+FarmerID+'/SurveyID/'+SurveyID);

                         }
                    }
                ]
            }
        ]
    });
    var winBeforeCetak = Ext.create('widget.window', {
        id : 'print',
        title: lang('Cetak'),
        closable: true,
        modal:true,
        layout : 'fit',
        closeAction: 'show',
        width: 480,
        height:140,
        items: [DataBeforeCetak]
    });

    function displayBeforeCetak(Farmer){
      FarmerID = Farmer;
         store_CekSurvey.load({
                  params: {
                      FarmerID: FarmerID
                  }}
          );
        if(!winBeforeCetak.isVisible()){
            winBeforeCetak.show();
        } else {
            winBeforeCetak.hide(this, function() {});
            winBeforeCetak.toFront();
        }
    }
