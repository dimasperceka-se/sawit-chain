
$(function(){
	var $main_panel = $('#main-panel')[0];
	var height      = Math.max(window.innerHeight - 70 || 0);
	var infowindow  = new google.maps.InfoWindow();

	function init_map() {
		$map_canvas.gmap3({
			map: {
				options: {
					center: [-4.433497, 119.949203],
					streetViewControl: false,
					rotateControl: false,
					rotateControlOptions: false,
					overviewMapControl: false,
					OverviewMapControlOptions: false,
					scrollwheel: true,
					mapTypeId: google.maps.MapTypeId.HYBRID,
					mapTypeControlOptions: {
						mapTypeIds: ["roadmap", "satellite", "hybrid", "terrain", "silver_map", "dark_map"],
					},
					maxZoom:18
				},
				callback: map => map.controls[google.maps.ControlPosition.LEFT_TOP].push($main_panel)
			},
		});
		map = $map_canvas.gmap3("get");
	
		const silverMapType = new google.maps.StyledMapType(SILVER_MAP_STYLE,{ name: "Silver" })
		const darkMapType = new google.maps.StyledMapType(DARK_MAP_STYLE,{ name: "Dark" })
	
		map.mapTypes.set("silver_map", silverMapType);
		map.mapTypes.set("dark_map", darkMapType);
		$map_canvas.css('height', height);
	}
		
	setTimeout(() => init_map(), 500);

	// hide panel-filter on click 
	$( "#panel-filter-control" ).click(function() {
		$('#main-panel-max').hide();
		$('#main-panel-min').show();
		$('#main-panel').css('width', '30px')

	  });

	// Show panel-filter on click 
	$( "#panel-filter-control-min").click(function() {
		$('#main-panel-max').show();
		$('#main-panel-min').hide();
		$('#main-panel').css('width', '350px')
	  });


	// Filter Funtion for Region (Province, Distrik,) & Partner
	setupRegionFilter()
	
	//Klik Tombol Search ===================================== (Main Begin) ===============================================================//
	$('#btn-filter').on('click', function(event) {
		event.preventDefault();
		const TESTING_DISTRICT = ["1175"]
		TESTING = TESTING_DISTRICT.includes($('#filter-district').val())
        // Cek Province harus terpilih
        if( !$('#filter-province').val() ) {
            Ext.MessageBox.show({
                title: lang('Information'),
                msg: lang('Province must be selected'),
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            return false;
        }  

		$('#addlayers').show();

        // Reset Map & Panel
			clear_map(); //===> Cek lagi untuk native nya
			kml_layer = []
			activeAjaxConnections = 0
        
            // get params
			var params = {
				ProvinceID: $('#filter-province').val(),
				DistrictID: $('#filter-district').val(),
				PartnerID: $('#filter-partner').val(),
				key: $('#filter-key').val()
			}

			setTileLayer()
			get_kml_Layer_list(params)
			loadLegend(params)
			getActors(params)
			getLandManagement(params)
			TESTING && getLandUseGeoServer(params)
			TESTING && getAnalysisData(params)

			// Ext.MessageBox.hide();

			setModalBody(params)
			m_export_map_polygon_excel == 1 ? $(".export").show() : $(".export").hide();
			TESTING ? $("#div-button-panel").show() : $("#div-button-panel").hide();
			
			
			// Add GeoJsonLayer Listener
				addGeoJsonClickListener()
				addGeoJsonMouseOver()
    })

	
	//Klik Tombol Search ===================================== (Main End) ===============================================================//

})

function setupRegionFilter() {
	getProvinces()
    $('#filter-province').on('change', function(event) {
        getDistricts();
    });
    setTimeout(function() {
        $('#filter-province').change();
    }, 1000);
}

function getProvinces(callback) {
    Ext.Ajax.request({
        url: m_api+'/map/province',
        method: 'GET',
        success: function(response){
            $('#filter-province option').remove();
            var provinces = JSON.parse(response.responseText);
            var options   = '<option value="">'+lang('All Province')+'</option>';
            $.each(provinces, function(index, val) {
                options += '<option value="'+val.id+'">'+val.label+'</option>';
            });
            $('#filter-province').append(options);
            callback;
        }
    });
}

function getDistricts() {
    Ext.Ajax.request({
        url: m_api+'/map/district',
        method: 'GET',
        params: {
            ProvinceID: $('#filter-province').val()
        },
        success: function(response){
            $('#filter-district option').remove();
            var options   = '<option value="">'+lang('All District')+'</option>';
            if (response.responseText) {
                var districts = JSON.parse(response.responseText);
                $.each(districts, function(index, val) {
                    options += '<option value="'+val.id+'">'+val.label+'</option>';
                });
            }
            $('#filter-district').append(options);
        }
    });
}

function clear_map(options) {
    var options = options || {};
    closeInfoBox();
    $map_canvas.gmap3({clear: options});
	if (options == {}) map.overlayMapTypes.clear()
	map.data.forEach(function(feature) {
		map.data.remove(feature);
	});
    bounds = new google.maps.LatLngBounds();
}

function get_kml_Layer_list(params) {
	LayerKML = []

	$.ajax({
		type: "GET",
		url: m_api+'/map/kml_layer_list', 
		data: params,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		async: false,
		success: function(response) {
			$.each(LANDUSE_ITEMS, function(index, {cat_id, kml}) {
				if (response.filter(r => r.CategoryID == cat_id).length > 0)  LayerKML[kml] = response.filter(r => r.CategoryID == cat_id)
			})
		}
	})
}

function getActors(params) {
	// Mill
		$.ajax({
			type: "GET",
			url: m_api+'/map/processing',
			data: params,
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			beforeSend: function(xhr) {
				activeAjaxConnections++;
			},
			success: function(response) {
				const tag = "actors-mill"
				const url_icon = ICON_PATH + "mill.png"
	
				$("#count-actors-mill").text(Number(response.length).toLocaleString())
	
				var markerValue = []
	
				$.each(response, function( i, data ) {
					// Farm Location
					data["type"] = 'actors-mill'
					data["layerTitle"] = 'Mill'
	
	
					markerValue.push({
						latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
						data: data,
						tag: tag,
						options: {
							title: data.Name + " \n" + data.DisplayID ,
							icon: {
								url: url_icon, 
								anchor: new google.maps.Point(20, 20),
								scaledSize: new google.maps.Size(30, 30),
							},
							zIndex: 99 ,
							optimized: true 

						},
					});
	
					if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
						var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
						bounds.extend(myLatLng);
					}
				})
				//Zoom ke daerah data koordinat
				$map_canvas.gmap3("get").fitBounds(bounds);
	
				addMarkers(markerValue)
			},
			complete: function() {
				activeAjaxConnections--;
				if (0 == activeAjaxConnections) {
					Ext.MessageBox.hide();
				}
			}
		})

	// Farm Location
		$.ajax({
			type: "GET",
			url: m_api+'/map/farm_location',
			data: params,
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			beforeSend: function(xhr) {
				activeAjaxConnections++;
			},
			success: function(response) {

				const {FarmLocation} = response
				if(FarmLocation){
					const {total_farm} = FarmLocation.Info
				
					let tag = "actors-farm-location"
					let url_icon = ICON_PATH + "farmer-plot-location.png"
					let z_index = 0
	
					$("#count-actors-farm-location").text(Number(total_farm).toLocaleString())
					$("#count-farm-location-by-age-total").text(Number(total_farm).toLocaleString())
	
	
					var markerValue = []
	
	
					$.each(FarmLocation.Data, function( i, data ) {
						// Farm Location
						data["type"] = 'actors-farm-location'
						data["layerTitle"] = 'Farm Location'
	
	
						markerValue.push({
							latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
							data: data,
							tag: tag,
							options: {
								title: data.Name + " \n" + data.MemberDisplayID ,
								icon: {
									url: url_icon, 
									anchor: new google.maps.Point(20, 20)
								},
								zIndex: -1 ,
								optimized: true 
							},
						});
	
						if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
							var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
							bounds.extend(myLatLng);
						}
					})
					//Zoom ke daerah data koordinat
					$map_canvas.gmap3("get").fitBounds(bounds);
	
					addMarkers(markerValue)
	
					// Farm Location by Age -----------------------------------------------------------------------------------------------
	
					var markerValueByAge = []
	
					let count_age_3 = 0
					let count_age_6 = 0
					let count_age_18 = 0
					let count_age_19 = 0
	
	
					$.each(FarmLocation.Data, function( i, data ) {
						// Farm Location
						data["type"] = 'actors-farm-location'
						data["layerTitle"] = 'Farm Location'
	
						const switch_AGE =  parseFloat(data.FarmAge)
						if(switch_AGE <= 3){
							tag = "farm-location-by-age-3"
							url_icon = ICON_PATH + "farmer-plot-location-3.png"
							z_index = 1
							count_age_3++
	
						}else if(switch_AGE <= 6){
							tag = "farm-location-by-age-6"
							url_icon = ICON_PATH + "farmer-plot-location-6.png"
							z_index = 2
							count_age_6++
	
						}else if(switch_AGE <= 18){
							tag = "farm-location-by-age-18"
							url_icon = ICON_PATH + "farmer-plot-location-18.png"
							z_index = 3
							count_age_18++
							
						}else {
							tag = "farm-location-by-age-19"
							url_icon = ICON_PATH + "farmer-plot-location-19.png"
							z_index = 0
							count_age_19++
							
						}
	
						markerValueByAge.push({
							latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
							data: data,
							tag: tag,
							options: {
								title: data.Name + " \n" + data.MemberDisplayID,
								icon: {
									url: url_icon, 
									anchor: new google.maps.Point(20, 20)
								},
								zIndex: z_index ,
								optimized: true 
							},
						});
	
						if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
							var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
							bounds.extend(myLatLng);
						}
					})
					$("#count-farm-location-by-age-3").text(count_age_3.toLocaleString())
					$("#count-farm-location-by-age-6").text(count_age_6.toLocaleString())
					$("#count-farm-location-by-age-18").text(count_age_18.toLocaleString())
					$("#count-farm-location-by-age-19").text(count_age_19.toLocaleString())
					
					addMarkers(markerValueByAge)
				}
			},
			complete: function() {
				activeAjaxConnections--;

				const layer_AGE =[
					{type:'marker',  tag:'farm-location-by-age-3'},
					{type:'marker',  tag:'farm-location-by-age-6'},
					{type:'marker',  tag:'farm-location-by-age-18'},
					{type:'marker',  tag:'farm-location-by-age-19'},
				]

				$.each(layer_AGE, function(index, {type,tag}) {
                    var layers = $map_canvas.gmap3({
                        get: { name: type, tag: tag, all: true}
                    });

                    layers && $.each(layers, (idx, layer) =>layer.setMap(null));
                })

				if (0 == activeAjaxConnections) {
					Ext.MessageBox.hide();
				}
			}
		})

	// Farm Area
		$.ajax({
			type: "GET",
			url: m_api+'/map/farm_area',
			data: params,
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			beforeSend: function(xhr) {
				activeAjaxConnections++;
			},
			success: function(response) {
				let tag = "actors-farm-area"
				let color = 'blue'
				
				$("#count-actors-farm-area").text(Number(response.length).toLocaleString())
				$("#count-farm-polygon-by-age-total").text(Number(response.length).toLocaleString())
				$("#count-farm-polygon-by-status-total").text(Number(response.length).toLocaleString())
				$.each(response, function( i, data ) {
					const {Polygon, ...attribute} = data
					const coord = JSON.parse(Polygon).coordinates[0];

					const area = [];
					$.each(coord, function(i, v) {
						area[i] = [v[1], v[0]];
					});
					
					const id = tag + "-" + data.MemberDisplayID + '-' + data.PlotNr

					draw_polygon(tag, id, area, attribute, color);

				})

				// Farm Polygon By Age ---------------------------------------------------------------------------------

					let count_age_3 = 0
					let count_age_6 = 0
					let count_age_18 = 0
					let count_age_19 = 0

					$.each(response, function( i, data ) {
						const {Polygon, ...attribute} = data
						const coord = JSON.parse(Polygon).coordinates[0];

						const switch_AGE =  parseFloat(data.FarmAge)
						if(switch_AGE <= 3){
							tag = "farm-polygon-by-age-3"
							color = "#FFA500"
							count_age_3++
		
						}else if(switch_AGE <= 6){
							tag = "farm-polygon-by-age-6"
							color = "#E7E719"
							count_age_6++
							
						}else if(switch_AGE <= 18){
							tag = "farm-polygon-by-age-18"
							color = "#305823"
							count_age_18++
							
						}else {
							tag = "farm-polygon-by-age-19"
							color = "#FF0000"
							count_age_19++
							
						}

						const area = [];
						$.each(coord, function(i, v) {
							area[i] = [v[1], v[0]];
						});
						
						const id = tag + "-" + data.MemberDisplayID + '-' + data.PlotNr


						draw_polygon(tag, id, area, attribute, color);

					})
					
					$("#count-farm-polygon-by-age-3").text(count_age_3.toLocaleString())
					$("#count-farm-polygon-by-age-6").text(count_age_6.toLocaleString())
					$("#count-farm-polygon-by-age-18").text(count_age_18.toLocaleString())
					$("#count-farm-polygon-by-age-19").text(count_age_19.toLocaleString())


				// Farm Polygon By Status -------------------------------------------------------------------------------
				
				    // New : Outlined dengan warna #FF6699 & Tanpa Filled
					// Verified : Outlined dengan warna #2929fe  & Filled di transparansi
					// Overlap : Outlined dengan warna #f508cb & Tanpa Filled
					// Retake : Outlined dengan warna #f59308 & Tanpa Filled
					var statusOption={
						new: {color:'#FF6699', fillOpacity:0.2},
						verified: {color:'#ecf39e', fillOpacity:0.2},
						partnerverified: {color:'#ecf39e', fillOpacity:0.2},
						overlap: {color:'#f508cb', fillOpacity:0.2},
						retake: {color:'#f59308', fillOpacity:0.2},
					}

					const statusValue = ["new", "verified", "overlap", "retake", "partnerverified"]

					let count_status_new = 0
					let count_status_verified = 0
					let count_status_overlap = 0
					let count_status_retake = 0

					$.each(response, function( i, data ) {
						const {Polygon, ...attribute} = data
						const coord = JSON.parse(Polygon).coordinates[0];

						const switch_STATUS =  data.StatusCheck
						
						if(statusValue.includes(switch_STATUS)){
							color = statusOption[switch_STATUS].color
						}else{
							color = 'blue'
						}

						switch (switch_STATUS) {
							case 'new':
								tag = 'farm-polygon-by-status-new'
								count_status_new++
								break;

							case 'verified':
								tag = 'farm-polygon-by-status-verified'
								count_status_verified++

								break;

							case 'partnerverified':
								tag = 'farm-polygon-by-status-verified'
								count_status_verified++
								break;

							case 'overlap':
								tag = 'farm-polygon-by-status-overlap'
								count_status_overlap++

								break;

							case 'retake':
								tag = 'farm-polygon-by-status-retake'
								count_status_retake++

								break;
								
							default:
								break;
						}

						const area = [];
						$.each(coord, function(i, v) {
							area[i] = [v[1], v[0]];
						});
						
						const id = tag + "-" + data.MemberDisplayID + '-' + data.PlotNr


						draw_polygon(tag, id, area, attribute, color);

					})
					
					$("#count-farm-polygon-by-status-new").text(count_status_new.toLocaleString())
					$("#count-farm-polygon-by-status-verified").text(count_status_verified.toLocaleString())
					$("#count-farm-polygon-by-status-overlap").text(count_status_overlap.toLocaleString())
					$("#count-farm-polygon-by-status-retake").text(count_status_retake.toLocaleString())

			},
			complete: function() {
				activeAjaxConnections--;

				const layer_to_hide = [
					{type:'polygon',  tag:'farm-polygon-by-age-3'},
					{type:'polygon',  tag:'farm-polygon-by-age-6'},
					{type:'polygon',  tag:'farm-polygon-by-age-18'},
					{type:'polygon',  tag:'farm-polygon-by-age-19'},
					{type:'polygon',  tag:'farm-polygon-by-status-new'},
					{type:'polygon',  tag:'farm-polygon-by-status-verified'},
					{type:'polygon',  tag:'farm-polygon-by-status-overlap'},
					{type:'polygon',  tag:'farm-polygon-by-status-retake'},
				]

				$.each(layer_to_hide, function(index, {type,tag}) {
                    var layers = $map_canvas.gmap3({
                        get: { name: type, tag: tag, all: true}
                    });

                    layers && $.each(layers, (idx, layer) =>layer.setMap(null));
                })
				if (0 == activeAjaxConnections) {
					Ext.MessageBox.hide();
				}
			}
		})

	// SME
	$.ajax({
		type: "GET",
		url: m_api+'/map/sme',
		data: params,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		beforeSend: function(xhr) {
			activeAjaxConnections++;
		},
		success: function(response) {
			const tag = "actors-sme"
			const url_icon = ICON_PATH + "sme.png"

			$("#count-actors-sme").text(Number(response.length).toLocaleString())

			var markerValue = []

			$.each(response, function( i, data ) {
				// Farm Location
				data["type"] = 'actors-sme'
				data["layerTitle"] = 'SME'


				markerValue.push({
					latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
					data: data,
					tag: tag,
					options: {
						title: data.MemberDisplayID + " \n" + data.MemberName ,
						icon: {
							url: url_icon, 
							anchor: new google.maps.Point(20, 20),
						},
						zIndex : 1,
						optimized: true 
					},
				});

				if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
					var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
					bounds.extend(myLatLng);
				}
			})
			//Zoom ke daerah data koordinat
			$map_canvas.gmap3("get").fitBounds(bounds);

			addMarkers(markerValue)
		},
		complete: function() {
			activeAjaxConnections--;
			if (0 == activeAjaxConnections) {
				Ext.MessageBox.hide();
			}
		}
	})

	// SME Plantation
	$.ajax({
		type: "GET",
		url: m_api+'/map/sme_plantation',
		data: params,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		beforeSend: function(xhr) {
			activeAjaxConnections++;
		},
		success: function(response) {
			const tag = "actors-sme-plantation"
			const url_icon = ICON_PATH + "sme-plantation.png"

			$("#count-actors-sme-plantation").text(Number(response.length).toLocaleString())

			var markerValue = []

			$.each(response, function( i, data ) {
				// Farm Location
				data["type"] = 'actors-sme-plantation'
				data["layerTitle"] = 'SME Plantation'


				markerValue.push({
					latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
					data: data,
					tag: tag,
					options: {
						title: data.MemberDisplayID + " \n" + data.MemberName ,
						icon: {
							url: url_icon, 
							anchor: new google.maps.Point(20, 20),
						},
						zIndex : 1,
						optimized: true 
					},
				});

				if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
					var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
					bounds.extend(myLatLng);
				}
			})
			//Zoom ke daerah data koordinat
			$map_canvas.gmap3("get").fitBounds(bounds);

			addMarkers(markerValue)
		},
		complete: function() {
			activeAjaxConnections--;
			if (0 == activeAjaxConnections) {
				Ext.MessageBox.hide();
			}
		}
	})
}

function setTileLayer(){
	// HANSEN
		$.ajax({ 
			type: 'GET', 
			async:false,
			url: 'https://storage.googleapis.com/earthenginepartners-hansen/tiles/Primary_HT_forests_2001/0/0/0.png',  
			success : function (data) { 
				$.each(URL_TILESET_HANSEN, function (i, {key,url}){
					TileLayer['Hansen'][key] = new google.maps.ImageMapType({
						getTileUrl: function(coordinates, zoom) {
							var link = url.replace('{x}', coordinates.x)
							.replace('{y}', coordinates.y)
							.replace('{z}', zoom);
							
							return link
						},
						name: key,
						alt: key,
						opacity: 1
					});
				})
				console.log("Hansen Data - passed")
				cekConnTileLayer['Hansen'] = true
			}, 
			error: function(xhr){
				console.log("Hansen Data - error")
				console.log(xhr.statusText)

			}
		});	
		
	// KLHK 	
		$.ajax({ 
			type: 'GET',
			async:false,
			url: 'https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK_EN/Forest_Area_/MapServer/legend?f=pjson',  
			success : function (data) { 
				$.each(URL_KAWASAN_HUTAN, function (i, {key,url}){
					TileLayer['KLHK'][key] = new google.maps.ImageMapType({
						getTileUrl: function(coordinates, zoom) {
							var link = url.replace('{x}', coordinates.x)
							.replace('{y}', coordinates.y)
							.replace('{z}', zoom);
							
							return link
						},
						name: key,
						alt: key,
						opacity: 1
					});
				})
				console.log("dbgis-KLHK - passed")
				cekConnTileLayer['KLHK'] = true
			}, error: function(xhr, status, error){
				console.log("dbgis-KLHK - error")
				console.log(xhr.statusText)
			}

		});	

}

function getLandManagement(params) {
	$.ajax({
		type: "GET",
		url: m_api+'/map/land_management',
		data: params,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		beforeSend: function(xhr) {
			activeAjaxConnections++;
		},
		success: function(response) {
			if(response.length > 0 ){
				let  geo_json = {
					"type": "FeatureCollection",
					"features": []
				}	
							  
				$.each(response,function( i, data ) {
					var feature_data = {
						"type": "Feature",
						"properties": {
							"layer_title"	: "land-management",
						  	"color"			: "#f3e9dc",
						  	"tag"			: 'land-management-iup',
						  	"remark" 		: data.Remark,
						  	"AreaHa" 		: data.AreaHa,
						  	"province_name" : data.Province,
						  	"district_name" : data.District
						},
						"geometry": JSON.parse(data.PolygonGeo)
					  }
					geo_json.features.push(feature_data)
				})
	
				map.data.addGeoJson(geo_json);
				
				map.data.setStyle(function(feature) {
					return {
					  fillColor: feature.getProperty('color'),
					  strokeColor: feature.getProperty('color'),
					  strokeWeight: 1,
					  visible : false
					};
				});
	
			}else{
				$("#div-lcp-layer-land-management").hide()
			}
		},
		error: function (error) {
			$("#div-lcp-layer-land-management").hide()
		},
		complete: function(event,xhr,options) {
			activeAjaxConnections--;
			if (0 == activeAjaxConnections) {
				Ext.MessageBox.hide();
			}
		}
	})
}

function getLandUseGeoServer(params) {
	var settings = {
		"url": "https://geoserver.api.kolti.net/geoserver/Koltiva-Internal/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=Koltiva-Internal%3Agis_ext_landuse_klhk&outputFormat=application%2Fjson&CQL_FILTER=gis_id=360111175",
		"method": "GET",
		"timeout": 0,
	};
	  
	$.ajax(settings).done(function (response) {
		var geo_json_klhk = {...response, features:[]}
		var geo_json_pmz  = {...response, features:[]}

		var prefix = {
			klhk : "landuse-klhk",
			pmz  : "landuse-pmz"
		} 
		
		response.features.map(data=>{
			const newData = { ...data, properties:{...data.properties}}
			
			newData.id 						= prefix.klhk + "-" + data.id 
			newData.properties.layer_title	= prefix.klhk
			newData.properties.tag			= prefix.klhk + "-" + data.properties.function_code.substring(0, 4)
			newData.properties.color		= COLOR_LANDUSE_KLHK[data.properties.function_code]
			
			geo_json_klhk.features.push(newData)
		})

		response.features.map(data=>{
			const newData = { ...data, properties:{...data.properties}}
			
			newData.id 						= prefix.pmz + "-" + data.id 
			newData.properties.layer_title	= prefix.pmz
			newData.properties.tag			= prefix.pmz + "-" + PMZ[data.properties.function_code]["tag"]
			newData.properties.color		= PMZ[data.properties.function_code]["color"]
			newData.properties.zone			= PMZ[data.properties.function_code]["zone"]
			
			geo_json_pmz.features.push(newData)
		})

		map.data.addGeoJson(geo_json_klhk)
		map.data.addGeoJson(geo_json_pmz)

		map.data.setStyle(function(feature) {
			if (feature.getProperty('layer_title') == prefix.klhk){
				return {
					fillColor: feature.getProperty('color'),
					strokeColor: feature.getProperty('color'),
					strokeWeight: 1,
					visible : false,
					zIndex:-99
				};
			}

			if (feature.getProperty('layer_title') == prefix.pmz){
				return {
					fillColor: feature.getProperty('color'),
					strokeColor: feature.getProperty('color'),
					strokeWeight: 1,
					visible : false,
					zIndex:-98
				};
			}
		});
		
	});
}

function getAnalysisData(params) {
	// Farm-Intersect-LandUse-KLHK
	$.ajax({
		type: "GET",
		url: m_api+'/map/info_landuse_summary', 
		data: params,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		beforeSend: function(xhr) {
			activeAjaxConnections++;
		},
		success: function(response) {
			FARM_LANDUSE_SUMMARY_DATA = {...response}
			loadAnalisis()
		},
		complete: function() {
			activeAjaxConnections--;
			if (0 == activeAjaxConnections) {
				Ext.MessageBox.hide();
			}
		}
	})
}

