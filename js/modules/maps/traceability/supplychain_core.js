
var Supplychain = function() {
    this.province   = '';
    this.start      = '';
    this.end        = '';
    this.partner    = '';
    this.warehouse  = '';
    this.certification    = '1';
    // points
    this.points         = [];
	// removed points
    this.removedPoints         = [];
    // points id (for checking purpose)
    this.addedPoints    = [];
    // polygons
    this.poly           = [];
    // polygons id (for checking purpose)
    this.addedPoly    	= [];
};

Supplychain.prototype.init = function(){
    $map_canvas.gmap3({
        map: {
            options: {
                center: [-2.0836809794977484, 113.63967449468988],
                zoom: 5,
                //mapTypeControl: false,
                panControl: true,
                zoomControl: true,
                //scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                rotateControlOptions: false,
                overviewMapControl: false,
                OverviewMapControlOptions: false,
                scrollwheel: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            ,callback: function (map) {
                if (top_toolbar_supply) {
                    map.controls[google.maps.ControlPosition.TOP_CENTER].push(top_toolbar_supply);
                    setTimeout(function(){
                        $(top_toolbar_supply).removeClass('hidden');
                        init_date ();
                    }, 200)
                }
                if (bottom_toolbar) {
                    map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(bottom_toolbar);
                    setTimeout(function(){
                        $(bottom_toolbar).removeClass('hidden');
                    }, 200)
                }
                // if (map_default_toolbar) {
                //     map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(map_default_toolbar);
                //     setTimeout(function(){
                //         $(map_default_toolbar).removeClass('hidden');
                //     }, 200)
                // }
            }
        }        
    });
}

Supplychain.prototype.addPoint = function(point){
	var index = $.inArray(point.id, this.addedPoints);
	if (index == -1) {
		this.points.push(point);
		this.addedPoints.push(point.id);
	} else {
        // this.points[index].bruto                += point.bruto;
        // this.points[index].netto                += point.netto;
        // this.points[index].transaction_count    += point.transaction_count;
        // this.points[index].supply_count         += point.supply_count;
        // this.points[index].batch_count          += point.batch_count;
	}
};

Supplychain.prototype.removePoint = function(by, key,exclude_id){
    // console.log(by, key,exclude_id);
    switch (by) {
        case 'level':
            this.removePointByLevel(key, exclude_id);
            break;
        case 'parent':
            this.removePointByParent(key, exclude_id);
            break;
    }
};
Supplychain.prototype.removePointByLevel = function(level, exclude_id){
    for (var i = this.points.length - 1; i >= 0; i--) {
        if (this.points[i].level == level && this.points[i].id != exclude_id) {
            this.removePolyById(this.points[i].id);
            this.points[i].hidden = true;
            this.removePointByParent(this.points[i].id);
        }
    }
};
Supplychain.prototype.removePointByParent = function(parent_id, exclude_id){
    // console.log(parent_id);
    for (var i = this.points.length - 1; i >= 0; i--) {
        if (this.points[i].id == parent_id) {
            this.points[i].detail_shown = false;
        }
        if (this.points[i].parent == parent_id && this.points[i].id != exclude_id && this.points[i].id != parent_id) {
            this.removePolyById(this.points[i].id);
            this.points[i].hidden = true;
            this.removePointByParent(this.points[i].id);
        }
    }
};
Supplychain.prototype.restorePoint = function(by, key,exclude_id){
    // console.log(by, key,exclude_id);
    switch (by) {
        case 'level':
            this.restorePointByLevel(key, exclude_id);
            break;
        case 'parent':
            this.restorePointByParent(key, exclude_id);
            break;
    }
};
Supplychain.prototype.restorePointByLevel = function(level, exclude_id){
    for (var i = this.points.length - 1; i >= 0; i--) {
        if (this.points[i].level == level && this.points[i].id != exclude_id) {
            this.restorePolyById(this.points[i].id);
            this.points[i].hidden = false;
            // this.restorePointByParent(this.points[i].id);
        }
    }
};
Supplychain.prototype.restorePointByParent = function(parent_id, exclude_id){
    for (var i = this.points.length - 1; i >= 0; i--) {
        if (this.points[i].parent == parent_id && this.points[i].id != exclude_id) {
            this.restorePolyById(this.points[i].id);
            this.points[i].hidden = false;
        }
    }
};

Supplychain.prototype.addPoly = function(poly){
	var index = $.inArray(poly.id, this.addedPoly);
	if (index == -1) {
		this.poly.push(poly);
		this.addedPoly.push(poly.id);
	} else {
        this.poly[index].survey               += poly.survey;
        this.poly[index].bruto                += poly.bruto;
        this.poly[index].netto                += poly.netto;
        this.poly[index].totalusd             += poly.totalusd;
        this.poly[index].totalidr             += poly.totalidr;
        this.poly[index].balance              += poly.balance;
        this.poly[index].transaction_count    += poly.transaction_count;
        this.poly[index].supply_count         += poly.supply_count;
        this.poly[index].batch_count          += poly.batch_count;		
	}
};
Supplychain.prototype.removePolyById = function(id){
    for (var i = this.poly.length - 1; i >= 0; i--) {
        if (this.poly[i].id == id) {
            this.poly[i].hidden = true;
        }
    }
};
Supplychain.prototype.restorePolyById = function(id){
    for (var i = this.poly.length - 1; i >= 0; i--) {
        if (this.poly[i].id == id) {
            this.poly[i].hidden = false;
        }
    }
};

Supplychain.prototype.showDetail = function(id){
    this.setPointAttr(id, 'detail_shown', true);
    var point = this.getPointById(id);
    // var certification = this.certification;
    // this.removePointByLevel(point.level, point.id);
    //console.log(point);
    get_farmer(id, point, this.partner, this.certification, function(){
        supplychain.reDrawMap(false);
        supplychain.setPointAttr(id, 'detail_fetched', true);
    });
    if (point.detail_fetched !== true) {
        // if (point.type == 'sce' || point.type == 'pedagang' || point.type == 'perwakilan') {
        //     get_detail_trader(id, point, function(){
        //         supplychain.reDrawMap(false);
        //         supplychain.setPointAttr(id, 'detail_fetched', true);
        //     });
        // } else if (point.type == 'cpg') {
            get_farmer(id, point, this.partner, this.certification, function(){
                supplychain.reDrawMap(false);
                supplychain.setPointAttr(id, 'detail_fetched', true);
            });
        // }
    } else {
        // console.log(id);
        this.restorePoint('parent', id);
        this.reDrawMap(false);
    }
};

Supplychain.prototype.showProfile = function(id){
    preview_cetak_surat(m_cetak_beneficiary_profiles+'FarmerID/'+id);
};

Supplychain.prototype.hideDetail = function(id){
    var point = this.getPointById(id);
    this.removePoint('parent', id);
    this.setPointAttr(id, 'detail_shown', false);
    this.restorePointByParent(point.parent);
    // this.restorePointByLevel(point.level);
    this.reDrawMap(false);
};

Supplychain.prototype.getPointById = function(id){
    var point = false;
    $.each(this.points, function(index, val) {
        if (val.id == id) {
            point = val;
            return false;
        } 
    });
    return point;
};

Supplychain.prototype.setPointAttr = function(id, key, value){
    for (var i = this.points.length - 1; i >= 0; i--) {
        if (this.points[i].id == id) {
            this.points[i][key] = value;
        }
    }
};

Supplychain.prototype.reDrawMap = function(fitbounds){
    clear_map();
    closeInfoBox();
    supplychain.renderPoints(fitbounds);
    supplychain.renderPoly();

};

Supplychain.prototype.renderPoints = function(fitbounds){
	fitbounds = typeof fitbounds !== 'undefined' ? fitbounds : true;
    var icon_path = base_url + 'img/maps/';
    // reset bounds
    bounds = new google.maps.LatLngBounds();

    var markers = [];
	$.each(this.points, function(index, val) {
        if (val.hidden !== true) {
    		markers.push({
                latLng: [parseFloat(val.lat), parseFloat(val.lng)],
                data:   val,
                tag:    val.type,
                id:     val.id,
                options:    {
    				icon: icon_path + val.type + '.png'
    			},
    		});
    		// add new bounds
    		var myLatLng = new google.maps.LatLng(parseFloat(val.lat), parseFloat(val.lng));
    		bounds.extend(myLatLng);
        }
	});
	
	$('#map_canvas').gmap3({
		marker: {
			values: markers
	        , events: {
	            click: function (marker, event, context) {
                    profile = '<table border="0" width="100%"><tbody>';
                    if (context.data.level < 5) {
                        if (context.data.detail_shown === true) {
                            profile += '<tr><td colspan="3" style="text-align:center;"><a class="green_btn" style="cursor:pointer;" onclick="supplychain.hideDetail('+context.data.id+')">'+lang('Hide Detail')+'</a></td></tr>';
                        } else {
                            profile += '<tr><td colspan="3" style="text-align:center;"><a class="green_btn" style="cursor:pointer;" onclick="supplychain.showDetail('+context.data.id+')">'+lang('Show Detail')+'</a></td></tr>';
                        }
                    }
                    profile += '</tbody></table>';
                    
                    info = '<ul class="nav nav-pills nav-justified" role="tablist" id="supply_tab">';
                    info += '<li role="presentation" class="active"><a href="#supply_profile" aria-controls="supply_profile" role="tab" data-toggle="tab">'+ lang('Profile') +'</a></li>';
                    info += '<li role="presentation"><a href="#supply_transaction" aria-controls="supply_transaction" role="tab" data-toggle="tab">'+ lang('Transaction') +'</a></li>';
                    info += '<div class="tab-content">';
                    info += '<div role="tabpanel" class="tab-pane active" id="supply_profile">'+profile+'</div>';
                    info += '<div role="tabpanel" class="tab-pane" id="supply_transaction"><div style="margin: 100px;"><img src="img/ajax-loader.gif" style="position: inherit; margin: 0 auto; border: none; width: 40px; height: 40px"></div></div>';
                    info += '</div>';
                    info += '</ul>';

                    var mapObject = $(this).gmap3("get");
                    closeInfoBox();
                    getInfoBoxSupply(context.data.label, info, context.data.type).open(mapObject, marker);
                    getSupplyProfile(context.data.id, context.data.type, context.data.supply_id);
	            }
	        }
		}
	});
	if (fitbounds) {
		$('#map_canvas').gmap3("get")
		.fitBounds(bounds);
	}
};

Supplychain.prototype.renderPoly = function(){
	var color = [];
    color['trader']             = "#72C0D7";
    color['pedagang']           = "#72C0D7";
    color['koperasi']           = "#E7471D";
    color['organisasi_petani']  = "#E7471D";
    color['cpg']                = "#863C0E";
    color['farmer']             = "#95AE5F";
    color['sce']                = "#2FB044";
	// Define a symbol using a predefined path (an arrow)
	// supplied by the Google Maps JavaScript API.
	var lineSymbol = {
		path: google.maps.SymbolPath.FORWARD_OPEN_ARROW
	};
	
	$.each(this.poly, function(index, val) {
        if (val.hidden !== true) {
    		$('#map_canvas').gmap3({
    			polyline: {                    
    				values:[{
    					options:{
    						path: val.path
    					}
    					,data: val
    				}]
    		        ,events: {
    		            /*click: function (poly, event, context) {
    		                var map = $(this).gmap3("get");
    		                var info = '';
    		                infowindow = $(this).gmap3({get: {name: "infowindow"}});
    		                info = '<table border="0" width="100%"><tbody>\
    		                	<tr><td colspan="3"><strong>'+context.data.label+'</strong></td></tr>\
    		                    <tr><td style="width:120px;">' + lang('From') + '</td><td>:&nbsp;</td><td>' + context.data.from + '</td></tr>\
    		                    <tr><td>' + lang('To') + '</td><td>:&nbsp;</td><td>' + context.data.to + '</td></tr>\
    		                    <tr><td>' + lang('Bruto') + '</td><td>:&nbsp;</td><td>' + number_format(context.data.bruto/1000,3,'.',',') +' '+lang('Ton')+ '</td></tr>\
    		                    <tr><td>' + lang('Netto') + '</td><td>:&nbsp;</td><td>' + number_format(context.data.netto/1000,3,'.',',') +' '+lang('Ton')+ '</td></tr>\
    		                    </tbody></table>';

    		                // var mapObject = $(this).gmap3("get");
    		                // closeInfoBox();
    		                // getInfoBoxSupply(context.data.label, info, context.data.to_type).open(mapObject, poly);
    		                if (infowindow) {
    		                    infowindow.close();
    		                } 
    	                    $(this).gmap3({
    	                        infowindow: {
    	                            // anchor: poly,
    	                            options: {
    	                            	content: info
    	                            	,position: event.latLng
    	                            }
    	                        }
    	                    });
    		                
    		            }*/
    		        }
    				,options:{
    					strokeColor: color[val.type],
    					strokeOpacity: 1.0,
    					strokeWeight: 2,
    					icons: [{
    						icon: lineSymbol,
    						offset: '0%',
    						repeat: '100px'
    					}]
    				}			
    			}
    		})
        }
	});

};

Supplychain.prototype.getCpg = function(id){
    // console.log('Get Farmer Group');
	var to = {};
    $.each(this.points, function(index, val) {
        if (val.id == id) {
            to = val;
            supplychain.points[index].is_detail = 1;
            return false;
        } 
    });
    this.removePoint('level',3,id);
	display_detail_trader(id, to);
};

Supplychain.prototype.getFarmer = function(traderid, id){
    // console.log('Get Farmer');
	var to = {};
	$.each(this.points, function(index, val) {
		if (val.id == id) {
			to = val;
            supplychain.points[index].is_detail = 1;
			return false;
		} 
	});
    this.removePoint('level',4,id);
	display_detail_cpg(traderid, id, to);
};

function getInfoBoxSupply(title, content, type) {
    let upperTitle = title.toLowerCase()
                    .split(' ')
                    .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
                    .join(' ');
    return new InfoBox({
        content:
            '<div class="marker_info none" id="marker_info">' +
            '<div class="info info_'+type+'" id="info_supply">'+
            '<h2>'+ lang(upperTitle) +'<span></span></h2>' +
            '<span>'+ content +'</span>' +
            // '<a href="'+ 'item.url_point' + '" class="green_btn">More info</a>' +
            '<span class="arrow"></span>' +
            '</div>' +
            '</div>',
        // disableAutoPan: true,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(30, -195),
        // closeBoxMargin: '50px 200px',
        closeBoxMargin: "20px 3px 2px 2px",
        // closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
        closeBoxURL: base_url+"img/close.gif",
        // closeBoxURL: '',
        isHidden: false,
        pane: 'floatPane',
        enableEventPropagation: true,
    });
}

function get_detail_trader (id, to, callback) {
    var start       = $date_start.val();
    var end         = $date_end.val();
	$.ajax({
		url: url_supplychain_cpg,
		data: {
			traderid: to.supply_id,
			start: start,
			end: end,
		},
	})
	.done(function(data) {
		// console.log("success");
		// console.log(data);
		// return false;
		$.each(data, function(index, val) {
            if (Math.abs(parseFloat(val.latitude)) > 0 && Math.abs(parseFloat(val.latitude)) < 90) {
                var cpg         = {};
                cpg.id          = val.id;
                cpg.supply_id          = val.supply_id;
                cpg.parent      = id;
                cpg.level       = 4;
                cpg.traderid    = id;
                cpg.type        = 'cpg';
                cpg.label       = lang('Farmer Group');
                cpg.name        = val.name;
                cpg.lat         = parseFloat(val.latitude);
                cpg.lng         = parseFloat(val.longitude);
    			cpg.bruto                = parseFloat(val.bruto);
    			cpg.netto                = parseFloat(val.netto);
    			cpg.transaction_count    = parseFloat(val.transaction_count);
    			cpg.supply_count         = parseFloat(val.supply_count);
    			cpg.batch_count          = parseFloat(val.batch_count);
    			supplychain.addPoint(cpg);

    			var cpg_poly = {};
    			cpg_poly.id          = val.id;
    			cpg_poly.type        = 'cpg';
    			cpg_poly.label       = lang('Farmer Group');
    			cpg_poly.from        = val.name;
    			cpg_poly.to          = to.name;
    			cpg_poly.from_type   = 'cpg';
    			cpg_poly.to_type     = 'trader';
    			cpg_poly.bruto                = val.bruto;
    			cpg_poly.netto                = val.netto;
    			cpg_poly.transaction_count    = val.transaction_count;
    			cpg_poly.supply_count         = val.supply_count;
    			cpg_poly.batch_count          = val.batch_count;
    			cpg_poly.path = [
    			    [parseFloat(val.latitude), parseFloat(val.longitude)],
    			    [parseFloat(to.lat), parseFloat(to.lng)]
    			];
    			supplychain.addPoly(cpg_poly);
            }
		});
		// supplychain.reDrawMap(false);
        callback();   
	})
	.fail(function() {
		// console.log("error");
	})
	.always(function() {
		// console.log("complete");
	});	
}

// function get_detail_cpg (traderid, id, to, callback) {
function get_farmer (id, to, partner, certification, callback) {
    var start       = $date_start.val();
    var end         = $date_end.val();
    $.ajax({
        url: url_supplychain_farmer,
        data: {
            partner: partner,
            certification: certification,
            supply_id: to.supply_id,
            start: start,
            end: end,
        },
    })
    .done(function(data) {
        $.each(data, function(index, val) {
            if (Math.abs(parseFloat(val.latitude)) > 0 && Math.abs(parseFloat(val.latitude)) < 90) {
                var farmer     = {};
                farmer.id      = val.id;
                farmer.parent      = id;
                farmer.level       = 5;
                farmer.type    = 'farmer';
                farmer.label   = lang('Farmer');
                farmer.name    = val.name;
                farmer.lat     = parseFloat(val.latitude);
                farmer.lng     = parseFloat(val.longitude);
                farmer.bruto                = parseFloat(val.bruto);
                farmer.netto                = parseFloat(val.netto);
                farmer.transaction_count    = parseFloat(val.transaction_count);
                farmer.supply_count         = parseFloat(val.supply_count);
                farmer.batch_count          = parseFloat(val.batch_count);
                supplychain.addPoint(farmer);

                var farmer_poly = {};
                farmer_poly.id          = val.id;
                farmer_poly.type        = 'farmer';
                farmer_poly.label       = lang('Farmer');
                farmer_poly.from        = val.name;
                farmer_poly.to          = to.name;
                farmer_poly.from_type   = 'farmer';
                farmer_poly.to_type     = 'cpg';
                farmer_poly.bruto                = val.bruto;
                farmer_poly.netto                = val.netto;
                farmer_poly.transaction_count    = val.transaction_count;
                farmer_poly.supply_count         = val.supply_count;
                farmer_poly.batch_count          = val.batch_count;
                farmer_poly.path = [
                    [parseFloat(val.latitude), parseFloat(val.longitude)],
                    [parseFloat(to.lat), parseFloat(to.lng)]
                ];
                supplychain.addPoly(farmer_poly);
            }
        });
        // supplychain.reDrawMap(false);
        callback();
    })
    .fail(function() {
        // console.log("error");
    })
    .always(function() {
        // console.log("complete");
    }); 
}

function getSupplyProfile(id, type, supplychain_id) {
    var url_profile = url_api+'/cargill/geospatial/supply_profile_'+type+'?id='+id+'&start='+supplychain.start+'&end='+supplychain.end+'&partner='+supplychain.partner+'&certification='+supplychain.certification+'&warehouse='+supplychain.warehouse+'&supplychainid='+supplychain_id;
    $.get(url_profile, function(data) {
        if(type=='warehouse'){
            profile = '<table border="0" width="100%"><tbody>';
            profile += '<tr><td width="200px">' + lang('ID') + '</td><td>:&nbsp;</td><td>' + data.id + '</td></tr>';
            profile += '<tr><td>' + lang('Name') + '</td><td>:&nbsp;</td><td>' + data.name + '</td></tr>';
            profile += '<tr><td>' + lang('Batch') + '</td><td>:&nbsp;</td><td>' + data.batch_count + '</td></tr>';
            profile += '<tr><td>' + lang('Bruto') + '</td><td>:&nbsp;</td><td>' + number_format(data.wh_bruto/1000,3,'.',',') +' '+lang('Ton')+ '</td></tr>';
            profile += '<tr><td>' + lang('Netto') + '</td><td>:&nbsp;</td><td>' + number_format(data.wh_netto/1000,3,'.',',') +' '+lang('Ton')+ '</td></tr>';
            profile += '</tbody></table>';
        }else{
            profile = '<table border="0" width="100%"><tbody>';
            profile += '<tr><td width="200px">' + lang('ID') + '</td><td>:&nbsp;</td><td>' + data.id + '</td></tr>';
            profile += '<tr><td>' + lang('Name') + '</td><td>:&nbsp;</td><td>' + data.name + '</td></tr>';
            if (type=='farmer' || type=='sce') profile += '<tr><td>' + lang('CPGid') + '</td><td>:&nbsp;</td><td>' + data.CPGid + '</td></tr>';
            if (type=='farmer' || type=='sce') profile += '<tr><td>' + lang('GroupName') + '</td><td>:&nbsp;</td><td>' + data.GroupName + '</td></tr>';
            if (type=='farmer') profile += '<tr><td>' + lang('Village') + '</td><td>:&nbsp;</td><td>' + data.Village + '</td></tr>';
            if (type=='farmer') profile += '<tr><td>' + lang('Survey Volume') + '</td><td>:&nbsp;</td><td>' + number_format(data.survey/1000,3,'.',',') + ' '+lang('Ton')+ '</td></tr>';
            if (type=='farmer') profile += '<tr><td>' + lang('Sales Quota') + '</td><td>:&nbsp;</td><td>' + number_format(data.quota/1000,3,'.',',') + ' '+lang('Ton')+ '</td></tr>';
            profile += '<tr><td>' + lang('Transaction') + '</td><td>:&nbsp;</td><td>' + data.transaction_count + '</td></tr>';
            //profile += '<tr><td>' + lang('Delivered to Supplier') + '</td><td>:&nbsp;</td><td>' + data.delivered_count + '</td></tr>';
            profile += '<tr><td>' + lang('Batch') + '</td><td>:&nbsp;</td><td>' + data.batch_count + '</td></tr>';
            if (type=='pedagang') profile += '<tr><td>' + lang('Farmers') + '</td><td>:&nbsp;</td><td>' + data.farmer_count + '</td></tr>';
            //if (type=='warehouse') profile += '<tr><td>' + lang('Coop') + '</td><td>:&nbsp;</td><td>' + data.coop_count + '</td></tr>';
            profile += '<tr><td>' + lang('Bruto') + '</td><td>:&nbsp;</td><td>' + number_format(data.bruto/1000,3,'.',',') +' '+lang('Ton')+ '</td></tr>';
            profile += '<tr><td>' + lang('Netto') + '</td><td>:&nbsp;</td><td>' + number_format(data.netto/1000,3,'.',',') +' '+lang('Ton')+ '</td></tr>';
            if (type=='farmer') profile += '<tr><td colspan="3"><center><a style="line-height: 14px;" class="green_btn" onclick="supplychain.showProfile(' + data.id + ')" href="#"> ' + lang('Show Profile') + ' </a></center></td></tr>';

            profile += '</tbody></table>';
        }
        $('#supply_profile').prepend(profile);
    });
    var url_transaction = url_api+'/cargill/geospatial/supply_transaction_'+type+'?id='+id+'&start='+supplychain.start+'&end='+supplychain.end+'&partner='+supplychain.partner+'&certification='+supplychain.certification+'&warehouse='+supplychain.warehouse+'&supplychainid='+supplychain_id;
    $.get(url_transaction, function(data) {
        var bruto = 0;
        var netto = 0;
        var destweight = 0;
        var wh_netto = 0;
        if(type=='warehouse'){
            trans = '<table width="100%" class="table table-condensed table-hover table-bordered table-striped"><thead><tr>'
                        +'<th>No</th>' 
                        +'<th>Delivery Date</th>'
                        +'<th>PO Number</th>'
                        +'<th>From</th>'
                        +'<th>Gross Weight (Kg)</th>'
                        +'<th>Weight Quality (Kg)</th>'
                        +'<th>Destination Weight (Kg)</th>'
                        +'<th>Netto Warehouse (Kg)</th>'
                    +'</tr></thead><tbody>';
            $.each(data, function(index, val) {
                trans += '<tr><td>'+(index+1)+'</td><td>'+val.deliverydate+'</td>'
                    +'</td><td>'+val.po+'</td>'
                    +'</td><td>'+val.batchfrom+'</td>'
                    +'<td style="text-align:right">'+number_format(val.bruto,2,'.',',')+'</td>'
                    +'<td style="text-align:right">'+number_format(val.netto,2,'.',',')+'</td>'
                    +'<td style="text-align:right">'+number_format(val.destweight,2,'.',',')+'</td>'
                    +'<td style="text-align:right">'+number_format(val.wh_netto,2,'.',',')+'</td>'
                    +'</td><tr>'
                bruto += parseFloat(val.bruto);
                netto += parseFloat(val.netto);
                destweight += parseFloat(isNaN(val.destweight) ? 0 :val.destweight);
                wh_netto += parseFloat(isNaN(val.wh_netto) ? 0 :val.wh_netto);
            });
            trans += '<tr><td colspan="3">Total (in Kg)</td><td style="text-align:right"><strong>'+number_format(bruto,2,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(netto,2,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(destweight,2,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(wh_netto,2,'.',',')+'</strong></td></tr>';
            trans += '<tr><td colspan="3">Total (in '+lang('Ton')+ ')</td><td style="text-align:right"><strong>'+number_format(bruto/1000,3,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(netto/1000,3,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(destweight/1000,3,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(wh_netto/1000,3,'.',',')+'</strong></td></tr>';
            trans += '</tbody></table>';
        }else if(type=='trader'){
            trans = '<table width="100%" class="table table-condensed table-hover table-bordered table-striped"><thead><tr>'
                        +'<th>No</th>' 
                        +'<th>Delivery Date</th>'
                        +'<th>PO Number</th>'
                        +'<th>From</th>'
                        +'<th>Gross Weight (Kg)</th>'
                        +'<th>Weight Quality (Kg)</th>'
                        //+'<th>Destination Weight</th>'
                        +'<th>Destination</th>'
                    +'</tr></thead><tbody>';
            $.each(data, function(index, val) {
                trans += '<tr><td>'+(index+1)+'</td><td>'+val.deliverydate+'</td>'
                    +'<td>'+val.po+'</td>'
                    +'<td>'+val.batchfrom+'</td>'
                    +'<td style="text-align:right">'+number_format(val.bruto,2,'.',',')+'</td>'
                    +'<td style="text-align:right">'+number_format(val.netto,2,'.',',')+'</td>'
                    //+'<td style="text-align:right">'+number_format(val.destweight,2,'.',',')+'</td>'
                    +'<td>'+val.destination+'</td>'
                    +'</tr>'
                bruto += parseFloat(isNaN(val.bruto) || val.bruto==null ? 0 :val.bruto);
                netto += parseFloat(isNaN(val.netto) || val.netto==null ? 0 :val.netto);
                destweight += parseFloat(isNaN(val.destweight) || val.destweight==null ? 0 :val.destweight);
            });
            trans += '<tr><td colspan="4">Total (in Kg)</td><td style="text-align:right"><strong>'+number_format(bruto,2,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(netto,2,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(destweight,2,'.',',')+'</strong></td><td style="text-align:right"></td></tr>';
            trans += '<tr><td colspan="4">Total (in '+lang('Ton')+ ')</td><td style="text-align:right"><strong>'+number_format(bruto/1000,3,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(netto/1000,3,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(destweight/1000,3,'.',',')+'</strong></td><td style="text-align:right"></td></tr>';
            trans += '</tbody></table>';
        }else{
            trans = '<table width="100%" class="table table-condensed table-hover table-bordered table-striped"><thead><tr><th>No</th><th>Date</th>'+(type != 'farmer'?'<th>Batch Number</th>':'<th>Trans Number</th>')+'<th>'+lang('Bruto')+' (Kg)</th><th>'+lang('Netto')+' (Kg)</th>'+(type != 'warehouse'?('<th>Dest</th>'):'')+(type == 'warehouse'?('<th>From</th>'):'')+'<th>Status</th>'+'</tr></thead><tbody>';
            $.each(data, function(index, val) {
                trans += '<tr><td>'+(index+1)+'</td><td>'+val.trans_date+'</td>'+(type != 'farmer'?('<td>'+val.batch_number+'</td>'):('<td>'+val.trans_number+'</td>'))+'<td style="text-align:right">'+number_format(val.bruto,2,'.',',')+'</td><td style="text-align:right">'+number_format(val.netto,2,'.',',')+'</td>'+(type != 'warehouse'?('<td>'+val.dest_orgname+'</td>'):'')+(type == 'warehouse'?('<td>'+val.batch_from+'</td>'):'')+'<td>'+val.batch_status+'</td>'+'</tr>';
                bruto += parseFloat(val.bruto);
                netto += parseFloat(val.netto);
            });
            trans += '<tr><td colspan="3">Total (in Kg)</td><td style="text-align:right"><strong>'+number_format(bruto,2,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(netto,2,'.',',')+'</strong></td><td colspan="2"></td></tr>';
            trans += '<tr><td colspan="3">Total (in '+lang('Ton')+ ')</td><td style="text-align:right"><strong>'+number_format(bruto/1000,3,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(netto/1000,3,'.',',')+'</strong></td><td colspan="2"></td></tr>';
            trans += '</tbody></table>';
        }
        $('#supply_transaction').html(trans);
    });
}

if (!Array.prototype.remove) {
    Array.prototype.remove = function(val, all) {
        var i, removedItems = [];
        if (all) {
            for(i = this.length; i--;){
                if (this[i] === val) removedItems.push(this.splice(i, 1));
            }
        }
        //same as before...
        else {  
            i = this.indexOf(val);
            if(i>-1) removedItems = this.splice(i, 1);
        }
        return removedItems;
    };
}