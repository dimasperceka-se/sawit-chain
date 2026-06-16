
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
    this.addedPoly      = [];
    this.map;
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
            }
        }        
    });
    this.map = $map_canvas.gmap3('get');
}

Supplychain.prototype.addPoint = function(point){
    var index = $.inArray(point.type+'_'+point.id, this.addedPoints);
    if (index == -1) {
        this.points.push(point);
        this.addedPoints.push(point.type+'_'+point.id);
    } 
};

Supplychain.prototype.removePoint = function(by, key,exclude_id){
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
    } 
};
Supplychain.prototype.removePolyById = function(id){
    for (var i = this.poly.length - 1; i >= 0; i--) {
        if (this.poly[i].id == id || this.poly[i].from_id == id || this.poly[i].to_id == id) {
            this.poly[i].hidden = true;
        }
    }
};
Supplychain.prototype.restorePolyById = function(id){
    for (var i = this.poly.length - 1; i >= 0; i--) {
        if (this.poly[i].id == id || this.poly[i].from_id == id || this.poly[i].to_id == id) {
            this.poly[i].hidden = false;
        }
    }
};

Supplychain.prototype.showDetail = function(id){
    var point = this.getPointById(id);
    this.setPointAttr(point.id, 'detail_shown', true);
    // hide point with same level, except this point
    // this.removePointByLevel(point.level, point.id);
    if (point.detail_fetched == false && supplychain.warehouse!=point.id) {
        get_child(point, function(){
            supplychain.setPointAttr(point.id, 'detail_fetched', true);
            supplychain.reDrawMap(false);
        });
    } else {
        // console.log(point.id);
        this.restorePoint('parent', point.id);
        this.reDrawMap(false);
    }
};

Supplychain.prototype.showProfile = function(id, tipe){
    if (id == 'tba') {
        alert('Can not show farmer profile! (New Farmers)');
    } else {
        if (tipe == 'mill') {
            preview_cetak_surat(m_api+'/mill/cetak_mill_profiles/MillID/'+id);
        } else if(tipe == 'farmer') {
            var point = this.getPointById(id);
            preview_cetak_surat(m_api+'/farmer/cetak_beneficiary_profiles/MemberID/'+point.member_id);
        } else {
            preview_cetak_surat(m_api+'/grower/cetak_agent_profiles/MemberID/'+id);
        }
    }
};

Supplychain.prototype.hideDetail = function(id){
    var point = this.getPointById(id);
    this.removePoint('parent', id);
    this.setPointAttr(id, 'detail_shown', false);
    // this.restorePointByParent(point.parent);
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
            var icon = icon_path + (val.icon ? val.icon : val.type + '.png');
            markers.push({
                latLng: [parseFloat(val.lat), parseFloat(val.lng)],
                data: val,
                tag: val.type,
                id: val.id,
                options: {
                    icon: icon
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
                    profile += '<tr><td colspan="3" style="text-align:center;"><a class="green_btn" href="#" style="cursor:pointer;" onclick="supplychain.showProfile(\'' + context.data.id + '\', ' + "'" + context.data.type + "'" +')">'+lang('Show Profile')+'</a> ';
                    if (context.data.has_detail !== false) {
                        if (context.data.detail_shown === true) {
                            profile += '<a class="green_btn" style="cursor:pointer;" onclick="supplychain.hideDetail('+context.data.id+')">'+lang('Hide Detail')+'</a>';
                        } else {
                            profile += '<a class="green_btn" style="cursor:pointer;" onclick="supplychain.showDetail('+context.data.id+')">'+lang('Show Details')+'</a>';
                        }
                    }
                    profile += '</td></tr></tbody></table>';
                    
                    info = '<ul class="nav nav-pills nav-justified" role="tablist" id="supply_tab">';
                    info += '<li role="presentation" class="active"><a href="#supply_profile" aria-controls="supply_profile" role="tab" data-toggle="tab">Profile</a></li>';
                    //info += '<li role="presentation"><a href="#supply_transaction" aria-controls="supply_transaction" role="tab" data-toggle="tab">Transaction</a></li>';
                    info += '<div class="tab-content">';
                    info += '<div role="tabpanel" class="tab-pane active" id="supply_profile">'+profile+'</div>';
                    info += '<div role="tabpanel" class="tab-pane" id="supply_transaction"><div style="margin: 100px;"><img src="img/ajax-loader.gif" style="position: inherit; margin: 0 auto; border: none; width: 40px; height: 40px"></div></div>';
                    info += '</div>';
                    info += '</ul>';

                    var mapObject = $(this).gmap3("get");
                    closeInfoBox();
                    getInfoBoxSupply(context.data.label, info, context.data.type).open(mapObject, marker);

                    getSupplyProfile(context.data);
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
    color['do']     = "#72C0D7";
    color['farmer'] = "#95AE5F";
    // Define a symbol using a predefined path (an arrow)
    // supplied by the Google Maps JavaScript API.
    var lineSymbol = {
        path: google.maps.SymbolPath.FORWARD_OPEN_ARROW
    };
    
    $.each(this.poly, function(index, val) {
        if (val.hidden !== true) {
            var Horizontal = (val.path[0][1]>val.path[1][1]) ? false : true;
            var curve = curvedLine({
                LatStart: val.path[0][0],
                LngStart: val.path[0][1],
                LatEnd: val.path[1][0],
                LngEnd: val.path[1][1],
                Horizontal: Horizontal,
                Multiplier: 2
            });
            $('#map_canvas').gmap3({
                polyline: {                    
                    values:[{
                        options:{
                            path: curve
                        }
                        ,data: val
                    }]
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

    function curvedLine(options) { 
        var result = [];
        var defaults = {
            LatStart: null,
            LngStart: null,
            LatEnd: null,
            LngEnd: null,
            Horizontal: true,
            Multiplier: 1,
            Resolution: 0.1,
        }

        var options =  $.extend(defaults, options);

        var o = options;
        
        var LastLat = o.LatStart;
        var LastLng = o.LngStart;
        
        var PartLat;
        var PartLng;

        var Points = new Array();
        var PointsOffset = new Array();
        
        for(point = 0; point <= 1; point += o.Resolution) {
            Points.push(point);
            offset = (0.6 * Math.sin((Math.PI * point / 1)));
            PointsOffset.push(offset);
        }

        var OffsetMultiplier = 0;
        
        if(o.Horizontal == true) {
            var OffsetLength = (o.LngEnd - o.LngStart) * 0.1;
        } else {
            var OffsetLength = (o.LatEnd - o.LatStart) * 0.1;            
        }

        for(var i = 0; i < Points.length; i++) {
            OffsetMultiplier = (OffsetLength * PointsOffset[i]) * o.Multiplier;
            
            if(o.Horizontal == true) {
                PartLat = (o.LatStart + ((o.LatEnd - o.LatStart) * Points[i])) + OffsetMultiplier;
                PartLng = (o.LngStart + ((o.LngEnd - o.LngStart) * Points[i]));
            } else {
                PartLat = (o.LatStart + ((o.LatEnd - o.LatStart) * Points[i]));
                PartLng = (o.LngStart + ((o.LngEnd - o.LngStart) * Points[i])) + OffsetMultiplier;                    
            }
            
            LastLat = PartLat;
            LastLng = PartLng;
            result.push([LastLat, LastLng]);
        }
        return result;
    }

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
    return new InfoBox({
        content:
            '<div class="marker_info none" id="marker_info">' +
            '<div class="info info_warehouse" id="info_supply" style="background: rgba(65, 92, 112, 0.9);">'+
            '<h2>'+ title +'<span></span></h2>' +
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

// function get_detail_cpg (traderid, id, to, callback) {
function get_child (data, callback) {
    var start       = $date_start.val();
    var end         = $date_end.val();
    $.ajax({
        url: m_url_api+'/maps/supplychain_new/supplychain',
        data: {
            id: data.id,
            start: start,
            end: end,
        },
    })
    .done(function(result) {
        $.each(result, function(index, val) {
            if (Math.abs(parseFloat(val['parent_latitude'])) > 0 && Math.abs(parseFloat(val['parent_latitude'])) <= 90) {
                // MILL
                var point_x = {};
                point_x.id             = val['parent_id'];
                point_x.type           = val['parent_type'].toLowerCase().replace(' ', '_');
                point_x.name           = val['parent_name'];
                point_x.lat            = parseFloat(val['parent_latitude']);
                point_x.lng            = parseFloat(val['parent_longitude']);
                // it has been added before
                // supplychain.addPoint(point_x);

                if (val['child_id'] && Math.abs(parseFloat(val['child_latitude'])) > 0 && Math.abs(parseFloat(val['child_latitude'])) <= 90) {
                    // lewat koperasi
                    var point_y            = {};
                    point_y.id             = val['child_id'];
                    point_y.key            = val['child_type']+'_'+val['child_id'];
                    point_y.plantation     = val['child_plantation'];
                    point_y.parent         = point_x.id;
                    point_y.level          = data.level+1;
                    point_y.hidden         = false;
                    point_y.has_detail     = val['child_type'] == 'farmer' ? false : true;
                    point_y.detail_fetched = false;
                    point_y.detail_shown   = false;
                    point_y.type           = val['child_type'].toLowerCase().replace(' ', '_');
                    point_y.label          = lang(val['CategoryName']);
                    point_y.name           = val['child_name'];
                    point_y.lat            = parseFloat(val['child_latitude']);
                    point_y.lng            = parseFloat(val['child_longitude']);
                    point_y.icon           = category[val['SupplybaseCategoryID']].icon;
                    supplychain.addPoint(point_y);

                    var poly       = {};
                    poly.id        = point_y.id+'-'+point_x.id;
                    poly.hidden    = point_y.hidden;
                    poly.type      = point_y.type;
                    poly.label     = lang(val['CategoryName']);
                    poly.from_id   = point_y.id;
                    poly.from      = point_y.name;
                    poly.to_id     = point_x.id;
                    poly.to        = point_x.name;
                    poly.from_type = point_y.type;
                    poly.to_type   = point_x.type;
                    poly.path = [
                        [parseFloat(point_y.lat), parseFloat(point_y.lng)],
                        [parseFloat(point_x.lat), parseFloat(point_x.lng)],
                    ];
                    supplychain.addPoly(poly);

                    point_x = point_y;
                }

            }

        });
        callback();
    })
    .fail(function() {
        // console.log("error");
    })
    .always(function() {
        // console.log("complete");
    }); 
}

function getSupplyProfile(data) {
    var id = data.id;
    if (data.type == 'farmer') {
        id += '_'+data.plantation;
    }
    var url_profile = m_url_api+'/maps/supplychain_new/supply_profile_'+data.type+'?id='+id+'&start='+supplychain.start+'&end='+supplychain.end+'&partner='+supplychain.partner+'&certification='+supplychain.certification+'&warehouse='+supplychain.warehouse+'&parent='+data.parent;
    $.get(url_profile, function(data) {
        profile = '<table border="0" width="100%"><tbody>';
        if (data.type=='farmer') profile += '<tr><td>' + lang('PlotNr') + '</td><td>:&nbsp;</td><td>' + data.PlotNr + '</td></tr>';
        profile += '<tr><td width="200px">' + lang('ID') + '</td><td>:&nbsp;</td><td>' + data.id + '</td></tr>';
        profile += '<tr><td>' + lang('Name') + '</td><td>:&nbsp;</td><td>' + data.name + '</td></tr>';
        if (data.type=='farmer') profile += '<tr><td>' + lang('Village') + '</td><td>:&nbsp;</td><td>' + data.Village + '</td></tr>';
        profile += '<tr><td>' + lang('Transaction') + '</td><td>:&nbsp;</td><td>' + data.transaction_count + '</td></tr>';
        if (data.type!='warehouse') profile += '<tr><td>' + lang('Delivered to Supplier') + '</td><td>:&nbsp;</td><td>' + data.delivered_count + '</td></tr>';
        if (data.type=='pedagang') profile += '<tr><td>' + lang('Farmers') + '</td><td>:&nbsp;</td><td>' + data.farmer_count + '</td></tr>';
        profile += '<tr><td>' + lang('Bruto') + '</td><td>:&nbsp;</td><td>' + number_format(data.bruto/1000,3,'.',',') +' '+lang('Ton')+ '</td></tr>';
        profile += '<tr><td>' + lang('Netto') + '</td><td>:&nbsp;</td><td>' + number_format(data.netto/1000,3,'.',',') +' '+lang('Ton')+ '</td></tr>';
        
        profile += '</tbody></table>';
        $('#supply_profile').prepend(profile);
    });
    /*var url_transaction = m_url_api+'/geospatial_new/supply_transaction_'+type+'?id='+id+'&start='+supplychain.start+'&end='+supplychain.end+'&partner='+supplychain.partner+'&certification='+supplychain.certification+'&warehouse='+supplychain.warehouse+'&parent='+parent;
    $.get(url_transaction, function(data) {
        var bruto = 0;
        var netto = 0;
        trans = '<table width="100%" class="table table-condensed table-hover table-bordered table-striped"><thead><tr><th>No</th><th>Date</th>'+(type != 'farmer'?'<th>Registration ID</th>':'<th>Registration ID</th>')+'<th>'+lang('Bruto')+' (Kg)</th><th>'+lang('Netto')+' (Kg)</th>'+(type != 'warehouse'?('<th>Dest</th>'):'')+(type == 'warehouse'?('<th>From</th>'):'')+'<th>Status</th>'+'</tr></thead><tbody>';
        $.each(data, function(index, val) {
            trans += '<tr><td>'+(index+1)+'</td><td>'+val.trans_date+'</td>'+(type != 'farmer'?('<td>'+val.batch_number+'</td>'):('<td>'+val.trans_number+'</td>'))+'<td style="text-align:right">'+number_format(val.bruto,2,'.',',')+'</td><td style="text-align:right">'+number_format(val.netto,2,'.',',')+'</td>'+(type != 'warehouse'?('<td>'+val.dest_orgname+'</td>'):'')+(type == 'warehouse'?('<td>'+val.batch_from+'</td>'):'')+'<td>'+val.batch_status+'</td>'+'</tr>';
            bruto += parseFloat(val.bruto);
            netto += parseFloat(val.netto);
        });
        trans += '<tr><td colspan="3">Total (in Kg)</td><td style="text-align:right"><strong>'+number_format(bruto,2,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(netto,2,'.',',')+'</strong></td><td colspan="2"></td></tr>';
        trans += '<tr><td colspan="3">Total (in '+lang('Ton')+ ')</td><td style="text-align:right"><strong>'+number_format(bruto/1000,3,'.',',')+'</strong></td><td style="text-align:right"><strong>'+number_format(netto/1000,3,'.',',')+'</strong></td><td colspan="2"></td></tr>';
        trans += '</tbody></table>';
        $('#supply_transaction').html(trans);
    });*/
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