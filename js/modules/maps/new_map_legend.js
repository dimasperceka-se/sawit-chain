function loadLegend(params) {
    // Reset Legend
    $info_legend.html('')

    $info_legend.append(layerActors())
    
    $info_legend.append(layerFarmPolygonByStatus())
    $info_legend.append(layerFarmLocationByAge())
    $info_legend.append(layerFarmPolygonByAge())
    
    LIST_PARTNER_LAND_MANAGEMENT.includes(m_partner_id) && $info_legend.append(layerLandManagement())

    TESTING && $info_legend.append(layerLandUseKLHK())
    TESTING && $info_legend.append(layerLandUsePMZ())

    loadKmlLayer()
   
    $info_legend.append(layerFireHotspot())
    $info_legend.append(layerMillDistance())

    cekConnTileLayer['Hansen'] && $info_legend.append(layerForestCoverHansen())
    cekConnTileLayer['KLHK'] && $info_legend.append(layerKLHK())

    cardEvent(params)
}


function cardEvent(params) {
    // Card - Collapse / Expand
        $('.card-title').on('click',function (event) {
            var cardId      = $(this).data('id')
            var isBodyShow  = $(this).context.dataset.show
            
            if (isBodyShow == 'show'){
                $("#" + cardId + " > div:eq(1)").hide()
                $(this).attr('data-show',"hide")
            }else{
                $("#" + cardId + " > div:eq(1)").show()
                $(this).attr('data-show',"show")
            }
        })

    
    // Card - Close
        $('.card-close').on('click',function (event) {
            var cardID      = $(this).data('id')
            var kmlgroup    = $(this).data('kml')
            var tilegroup   = $(this).data('tile')
            var LayerTypeList   =  {
                feature : ["layer-farm-location-by-age","layer-farm-polygon-by-age","layer-farm-polygon-by-status"],
                kml     : ["layer-landuse-restrictedarea", "layer-landuse-safearea", "layer-landuse-bufferzone","layer-landuse-administrativeboundary","layer-landuse-additionallayer"],
                tileset : ["layer-hansen", "layer-klhk"],
                geojson : ["layer-land-management", "layer-landuse-klhk", "layer-landuse-pmz"],
            }

            var items   = {}
            
            items["layer-actors-basic"] = [
                {type:'marker',  tag:'actor-basic-farm-location'},
                {type:'polygon', tag:'actor-basic-farm-area'},
            ]
           
            items["layer-farm-location-by-age"] = [
                {type:'marker',  tag:'farm-location-by-age-3'},
                {type:'marker',  tag:'farm-location-by-age-6'},
                {type:'marker',  tag:'farm-location-by-age-18'},
                {type:'marker',  tag:'farm-location-by-age-19'},
            ]

            items["layer-farm-polygon-by-age"] = [
                {type:'polygon',  tag:'farm-polygon-by-age-3'},
                {type:'polygon',  tag:'farm-polygon-by-age-6'},
                {type:'polygon',  tag:'farm-polygon-by-age-18'},
                {type:'polygon',  tag:'farm-polygon-by-age-19'},
            ]            
            
            items["layer-farm-polygon-by-status"] = [
                {type:'polygon',  tag:'farm-polygon-by-status-new'},
                {type:'polygon',  tag:'farm-polygon-by-status-verified'},
                {type:'polygon',  tag:'farm-polygon-by-status-overlap'},
                {type:'polygon',  tag:'farm-polygon-by-status-retake'},
            ]

            items["layer-land-management"] = [
                {type:'geojson',  tag:'land-management-iup'},
                {type:'geojson',  tag:'land-management-hgu'},
                {type:'geojson',  tag:'land-management-hcv'},
                {type:'geojson',  tag:'land-management-hcs'},
                {type:'geojson',  tag:'land-management-izin'},
            ]

            items["layer-landuse-klhk"] = [
                {type:'geojson',  tag:'landuse-klhk-1001'},
                {type:'geojson',  tag:'landuse-klhk-1002'},
                {type:'geojson',  tag:'landuse-klhk-1003'},
                {type:'geojson',  tag:'landuse-klhk-1004'},
                {type:'geojson',  tag:'landuse-klhk-1005'},
                {type:'geojson',  tag:'landuse-klhk-1007'},
            ]
            
            items["layer-landuse-pmz"] = [
                {type:'geojson',  tag:'landuse-pmz-red'},
                {type:'geojson',  tag:'landuse-pmz-yellow'},
                {type:'geojson',  tag:'landuse-pmz-green'},
            ]

            $('#'+cardID).hide()
            $('#lcp-'+cardID).prop('checked', false)

            if(LayerTypeList.feature.includes(cardID)){
                $.each(items[cardID], function(index, {type,tag}) {
                    var layers = $map_canvas.gmap3({
                        get: { name: type, tag: tag, all: true}
                    });

                    layers && $.each(layers, (idx, layer) =>layer.setMap(null));
                    $('#switch-'+tag).attr('checked', false);
                })
            }

            if(LayerTypeList.kml.includes(cardID)){
                $.each(LayerKML[kmlgroup], function(i,{Name}){
                    var key = Name.replaceAll(".","")
                        key = key.replaceAll(" ","-")
                    var tag = "kml-" + key  

                    kml_layer[tag] = $map_canvas.gmap3({
                        get: { name: 'kmllayer', tag: tag, all:true}
                    });

                    kml_layer[tag] && $.each(kml_layer[tag], (idx, kml_layer) =>kml_layer.setMap(null))

                })
            }

            if(LayerTypeList.tileset.includes(cardID)){
                $.each(URL_TILESET_HANSEN, (i,{order})=>map.overlayMapTypes.setAt(order, null))
                $.each(URL_KAWASAN_HUTAN, (i,{order})=>map.overlayMapTypes.setAt(order, null))
            }

            if(LayerTypeList.geojson.includes(cardID)){
                $.each(items[cardID], function(index, {type,tag}) {
                    map.data.forEach(feature => feature.getProperty('tag') == tag && map.data.overrideStyle(feature,{visible : false}))
                    $('#switch-'+tag).attr('checked', false);
                })

            }


        })

    // Show Hide Feature
        $('.layer-switch').on('change', function(event) {
            var type    = $(this).data('type')
            var tag     = $(this).data('tag')
            var show    = $(this).is(':checked') ? map:null
            var show_gj = $(this).is(':checked') ? true:false
            var name    = $(this).data('name')
            var layer   = $(this).data('layer')
            
            if(type == "geojson"){
                map.data.forEach(feature => feature.getProperty('tag') == tag && map.data.overrideStyle(feature,{visible : show_gj}))
            }else{
                show_hide_feature(type,tag,show,name)
            }

        });  

    // Download 
        $('.export').on('click',function (event) {
            event.preventDefault();
            Ext.MessageBox.show({
                msg: 'Please wait...',
                progressText: 'Generating...',
                width: 300,
                wait: true,
                waitConfig: {
                    interval: 200
                },
                icon: 'ext-mb-info', //custom class in msg-box.html
                animateTarget: 'mb9'
            });
            $.ajax({
                type: "GET",
                url: m_api + '/map/' + $(this).data('api'),
                data: params,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(result) {
                    if (result.filenya) {
                        window.location = result.filenya
                    } else {
                        Ext.Msg.alert('Info', lang('Can not generate file!'));
                    }
                },
                complete: function() {
                    Ext.MessageBox.hide();
                }
            });
        })
    
    // mill_distance 
        $('.mill_distance').on('change', function(event) {
            closeInfoBox();
            hide_circle('mill-circle');
        
            if($(this).data('distance') > 0)
                draw_mills_distance($(this).data('distance')*1000);
        });

    // Fire Hotspot
        $.each(hostpots, function(index, val) {
            var tpl = '<div class="am-checkbox" style="display: none;"><input class="check-'+val.type+'" id="check_'+val.key+'" data-type="'+val.key+'" type="checkbox"><label for="check_'+val.key+'" style="font-size:11px"> <img style="width:15px;margin-bottom:2px;margin-right:4px" src="'+val.icon+'" alt="" > '+val.label+' <span class="object-count"></span></label></div>';
            $('#panel-hotspot-trust').append(tpl);
        });

        $('input[name=hotspot_date]').change(function(event) {
            if ($(this).val()) {
                $('input[name=hotspot_timeline]').removeProp('checked').prop('disabled', 'true');
            } else {
                $('input[name=hotspot_timeline]').removeProp('disabled');
            }
        });
        
        $(".datetimepicker").datetimepicker({
            autoclose: true,
            componentIcon: '.s7-date',
            navIcons:{
                rightIcon: 's7-angle-right',
                leftIcon: 's7-angle-left'
            }
        });

        $('#button-hotspot-view').on('click', function(event) {
            event.preventDefault();
            var satellite = $('input[name=hotspot_satellite]').val();
            var timeline = $('input[name=hotspot_timeline]:checked').val();
            var date = $('input[name=hotspot_date]').val();
    
            if (typeof(timeline) !== 'undefined' || date !== '') {
                getHotspots();
            } else {
                Ext.Msg.alert('Info', lang('Please select timeline or date'));
            }
        });  

        $('.check-marker').on('change', function(event) {
            console.log("here")
            closeInfoBox();
            if ($(this).is(':checked')) {
                show_marker($(this).data('type'));
                show_polygon($(this).data('type'));
            } else {
                hide_marker($(this).data('type'));
                hide_polygon($(this).data('type'));
                if ($(this).data('type') == 'processing') {
                    hide_circle();
                }
            }
        });

}


function hide_marker(tag) {
    var markers = $map_canvas.gmap3({
        get: {
            name: 'marker',
            tag: tag,
            all: true
        }
    });
    $.each(markers, function (idx, marker) {
        marker.setMap(null);
    })
}


function show_marker(tag) {
    var markers = $map_canvas.gmap3({
        get: {
            name: 'marker',
            tag: tag,
            all: true
        }
    });
    $.each(markers, function (idx, marker) {
        marker.setMap(map);
    })
}

function add_markers(latlng) {
    $map_canvas.gmap3({
        marker: {
            // tag: tag_name,
            // data: info,
            values: latlng
            , events: {
                click: function (marker, event, context) {
                    var mapObject = $(this).gmap3("get");
                    closeInfoBox();
                    // if ($.inArray(context.data.type, ['hotspot_high','hotspot_nominal','hotspot_low',]) == -1) {
                        getInfoBox(context).open(mapObject, marker);
                    // }
                }
            }
        }
    });
}


function getHotspots() {
    clear_map({tag: ['hotspot_low','hotspot_nominal','hotspot_high',]});

    var params = {
        satellite: $('select[name=hotspot_satellite]').val(),
        timeline: $('input[name=hotspot_timeline]:checked').val(),
        date: $('input[name=hotspot_date]').val(),
    };
    var activeAjaxConnections = 0;
    var object_count = 0;   
    $.each(hostpots, function(index, val) {
        // console.log(val)
        $('#check_'+val.key).parent().hide();
        $.ajax({
            type: "GET",
            url: val.api,
            data: params,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr) {
                activeAjaxConnections++;
            },
            // async: false,
            success: function(list) {
                if (list) {
                    object_count += list.length;
                    //var list     = JSON.parse(response.responseText);
                    
                    var latLngs  = [];
                    $.each(list, function(index, data) {
                        var tag = val.key;
                        data['type']  = val.key;
                        data['label'] = val.label;
                        data['color'] = val.color;
					    data["layerTitle"] = 'Fire Hotspot'
                        
                        latLngs.push({
                            latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                            data: data,
                            tag: tag,
                            options: {
                                icon: {
                                    url: val.icon,
                                    scaledSize: new google.maps.Size(10, 10)
                                }
                            },
                        });
                    });

                    addMarkers(latLngs);

                    $('#check_'+val.key).prop('checked', 'checked');
                    $('#check_'+val.key).parent().find('.object-count').text('('+list.length+')');
                    $('#check_'+val.key).parent().show();
                }
            },
            complete: function() {
                activeAjaxConnections--;
                if (0 == activeAjaxConnections) {
                    // this was the last Ajax connection, do the thing
                    Ext.MessageBox.hide();
                    if (0 == object_count) {
                        Ext.Msg.alert('Info', lang('No hotspot object found.'));
                    }
                }
            }
        }); 
    });
}


function layerActors() {
    const layerId = "layer-actors"
    const itemPrefix = "actors"
    const actors_basic = [
        {
            key:"farm-location", 
            icon:"farmer-plot-location",
            caption:"Farm Location",
            type:'marker', 
            title:"Farm Location"
        },
        {
            key:"farm-area", 
            icon:"circle-poly-blue",
            caption:"Farm Polygon",
            type:'polygon', 
            title:"Farm Polygon"
        },
        {
            key:"sme", 
            icon:"sme",
            caption:"SME",
            type:'marker', 
            title:"SME"
        },
        {
            key:"sme-plantation", 
            icon:"sme-plantation",
            caption:"SME Plantation",
            type:'marker', 
            title:"SME Plantation"
        },
        {
            key:"mill", 
            icon:"mill",
            caption:"Mill",
            type:'marker', 
            title:"Mill"
        }
    ]
    var Layer = `<div id="${layerId}" class="legend-card">
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Actor')} </span>
                        <div>
                            <img class="layer-title-icon" style="margin-left:5px; display:none" src="${ICON_PATH}kml-download.png" height=14 alt="" id='download-kml-${layerId}' title="Dowload KML">
                            <img class="layer-title-icon export" style="margin-left:5px;" src="${ICON_PATH}xls-download.png" height=14 alt="" id='download-xls-${layerId}' title="Download Excel" data-api="actors_export_excel">
                            <img class="layer-title-icon card-close" style="margin-left:5px; display:none" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`

                    $.each(actors_basic, function(index, val) {
                        const {key,icon,caption,type,title} = val

                        Layer +=`<div class='lcb-layer'>
                                    <img src="${ICON_PATH + icon}.png" alt="" style="height:28px;width:28px" ${(title !="-" )? "title='" + title + "'" :""}>
                                    <div class='lcb-caption'> 
                                        <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer"  ${(title !="-" )? "title='" + title + "'" :""}>${lang(caption)}</label>
                                    </div>    
                                    <label class="switch-layer">
                                        <span id="count-${itemPrefix}-${key}" class="label-count">0</span> &nbsp
                                        <input checked type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="${type}" data-layer="${itemPrefix}" data-tag="${itemPrefix}-${key}"/><i></i>
                                    </label>
                                </div>`
                    })


        Layer +=    `</div></div>` 

    return Layer

}

function layerFarmLocationByAge() {
    const layerId = "layer-farm-location-by-age"
    const itemPrefix = "farm-location-by-age"
    const actors_basic = [
        {
            key:"3", 
            icon:"farmer-plot-location-3",
            caption:"1-3 Years : Seedlings Phase",
            type:'marker', 
            title:"1-3 Years : Seedlings Phase",
        },
        {
            key:"6", 
            icon:"farmer-plot-location-6",
            caption:"4-6 Years : Young Phase",
            type:'marker', 
            title:"4-6 Years : Young Phase",
        },
        {
            key:"18", 
            icon:"farmer-plot-location-18",
            caption:"7-18 Years : Prime Phase",
            type:'marker', 
            title:"7-18 Years : Prime Phase",
        },
        {
            key:"19", 
            icon:"farmer-plot-location-19",
            caption:"> 19 Years : Old Phase",
            type:'marker', 
            title:"> 19 Years : Old Phase",
        },
    ]
    
    var Layer = `<div id="${layerId}" class="legend-card" style='display:none'>
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Farm Location (Age)')} <span id="count-${itemPrefix}-total" class="label-count-total" style="font-size:11px">0</span></span> 
                        <div>
                            <img class="layer-title-icon" style="margin-left:5px; display:none" src="${ICON_PATH}kml-download.png" height=14 alt="" id='download-kml-${layerId}' title="Dowload KML">
                            <img class="layer-title-icon export" style="margin-left:5px;" src="${ICON_PATH}xls-download.png" height=14 alt="" id='download-xls-${layerId}' title="Download Excel" data-api="farm_location_export_excel">
                            <img class="layer-title-icon card-close" style="margin-left:5px;" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`

                    $.each(actors_basic, function(index, val) {
                        const {key,icon,caption,type,title} = val

                        Layer +=`<div class='lcb-layer'>
                                    <img src="${ICON_PATH + icon}.png" alt="" style="height:28px;width:28px" ${(title !="-" )? "title='" + title + "'" :""}>
                                    <div class='lcb-caption'> 
                                        <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer"  ${(title !="-" )? "title='" + title + "'" :""}>${lang(caption)}</label>
                                    </div>    
                                    <label class="switch-layer">
                                        <span id="count-${itemPrefix}-${key}" class="label-count">0</span> &nbsp
                                        <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="${type}" data-layer="${itemPrefix}" data-tag="${itemPrefix}-${key}"/><i></i>
                                    </label>
                                </div>`
                    })


        Layer +=    `</div></div>` 

    return Layer
}

function layerFarmPolygonByAge() {
    const layerId = "layer-farm-polygon-by-age"
    const itemPrefix = "farm-polygon-by-age"
    const actors_basic = [
        {
            key:"3", 
            icon:"circle-poly-orange",
            caption:"1-3 Years : Seedlings Phase",
            type:'polygon', 
            title:"1-3 Years : Seedlings Phase",
        },
        {
            key:"6", 
            icon:"circle-poly-yellow",
            caption:"4-6 Years : Young Phase",
            type:'polygon', 
            title:"4-6 Years : Young Phase",
        },
        {
            key:"18", 
            icon:"circle-poly-green",
            caption:"7-18 Years : Prime Phase",
            type:'polygon', 
            title:"7-18 Years : Prime Phase",
        },
        {
            key:"19", 
            icon:"circle-poly-red",
            caption:"> 19 Years : Old Phase",
            type:'polygon', 
            title:"> 19 Years : Old Phase",
        },
    ]
    
    var Layer = `<div id="${layerId}" class="legend-card" style='display:none'>
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Farm Polygon (Age)')} <span id="count-${itemPrefix}-total" class="label-count-total" style="font-size:11px">0</span></span> 
                        <div>
                            <img class="layer-title-icon export" style="margin-left:5px;" src="${ICON_PATH}kml-download.png" height=14 alt="" id='download-kml-${layerId}' title="Dowload KML" data-api="farm_polygon_export_kml">
                            <img class="layer-title-icon export" style="margin-left:5px;" src="${ICON_PATH}xls-download.png" height=14 alt="" id='download-xls-${layerId}' title="Download Excel" data-api="farm_polygon_export_excel">
                            <img class="layer-title-icon card-close" style="margin-left:5px;" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`

                    $.each(actors_basic, function(index, val) {
                        const {key,icon,caption,type,title} = val

                        Layer +=`<div class='lcb-layer'>
                                    <img src="${ICON_PATH + icon}.png" alt="" style="height:28px;width:28px" ${(title !="-" )? "title='" + title + "'" :""}>
                                    <div class='lcb-caption'> 
                                        <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer"  ${(title !="-" )? "title='" + title + "'" :""}>${lang(caption)}</label>
                                    </div>    
                                    <label class="switch-layer">
                                        <span id="count-${itemPrefix}-${key}" class="label-count">0</span> &nbsp
                                        <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="${type}" data-layer="${itemPrefix}" data-tag="${itemPrefix}-${key}"/><i></i>
                                    </label>
                                </div>`
                    })


        Layer +=    `</div></div>` 

    return Layer
}

function layerFarmPolygonByStatus() {
    const layerId = "layer-farm-polygon-by-status"
    const itemPrefix = "farm-polygon-by-status"
    const actors_basic = [
        {
            key:"new", 
            icon:"circle-poly-new",
            caption:"New",
            type:'polygon', 
            title:"New",
        },
        {
            key:"verified", 
            icon:"circle-poly-verified",
            caption:"Verified",
            type:'polygon', 
            title:"Verified",
        },
        {
            key:"overlap", 
            icon:"circle-poly-overlap",
            caption:"Overlap",
            type:'polygon', 
            title:"Overlap",
        },
        {
            key:"retake", 
            icon:"circle-poly-retake",
            caption:"Retake",
            type:'polygon', 
            title:"Retake",
        }
    ]
    
    var Layer = `<div id="${layerId}" class="legend-card" style='display:none'>
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Farm Polygon (Status)')} <span id="count-${itemPrefix}-total" class="label-count-total" style="font-size:11px">0</span></span> 
                        <div>
                            <img class="layer-title-icon export" style="margin-left:5px;" src="${ICON_PATH}kml-download.png" height=14 alt="" id='download-kml-${layerId}' title="Dowload KML" data-api="farm_polygon_status_export_kml"> 
                            <img class="layer-title-icon export" style="margin-left:5px;" src="${ICON_PATH}xls-download.png" height=14 alt="" id='download-xls-${layerId}' title="Download Excel" data-api="farm_polygon_export_excel">
                            <img class="layer-title-icon card-close" style="margin-left:5px;" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`

                    $.each(actors_basic, function(index, val) {
                        const {key,icon,caption,type,title} = val

                        Layer +=`<div class='lcb-layer'>
                                    <img src="${ICON_PATH + icon}.png" alt="" style="height:28px;width:28px" ${(title !="-" )? "title='" + title + "'" :""}>
                                    <div class='lcb-caption'> 
                                        <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer"  ${(title !="-" )? "title='" + title + "'" :""}>${lang(caption)}</label>
                                    </div>    
                                    <label class="switch-layer">
                                        <span id="count-${itemPrefix}-${key}" class="label-count">0</span> &nbsp
                                        <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="${type}" data-layer="${itemPrefix}" data-tag="${itemPrefix}-${key}"/><i></i>
                                    </label>
                                </div>`
                    })


        Layer +=    `</div></div>` 

    return Layer
}

function layerLandManagement() {
    const layerId = "layer-land-management"
    const itemPrefix = "land-management"
    const actors_basic = [
        {
            key:"iup", 
            icon:"circle-poly-iup",
            caption:"IUP",
            type:'geojson', 
            title:"Izin Usaha Perkebunan"
        },
        {
            key:"hgu", 
            icon:"circle-poly-hgu",
            caption:"HGU",
            type:'geojson', 
            title:"Hak Guna Usaha"
        },
        {
            key:"hcv", 
            icon:"circle-poly-hcv",
            caption:"HCV",
            type:'geojson', 
            title:"High Conservation Value"
        },
        {
            key:"hcs", 
            icon:"circle-poly-hcs",
            caption:"HCS",
            type:'geojson', 
            title:"High Carbon Stock"
        },
        {
            key:"izin", 
            icon:"circle-poly-izin",
            caption:"Izin Lokasi",
            type:'geojson', 
            title:"Izin Lokasi"
        },
    ]
    var Layer = `<div id="${layerId}" class="legend-card" style='display:none'>
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Land Management')} </span>
                        <div>
                            <img class="layer-title-icon card-close" style="margin-left:5px;" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`

                    $.each(actors_basic, function(index, val) {
                        const {key,icon,caption,type,title} = val

                        Layer +=`<div class='lcb-layer'>
                                    <img src="${ICON_PATH + icon}.png" alt="" style="height:28px;width:28px" ${(title !="-" )? "title='" + title + "'" :""}>
                                    <div class='lcb-caption'> 
                                        <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer"  ${(title !="-" )? "title='" + title + "'" :""}>${lang(caption)}</label>
                                    </div>    
                                    <label class="switch-layer">
                                        <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="${type}" data-layer="${itemPrefix}" data-tag="${itemPrefix}-${key}"/><i></i>
                                    </label>
                                </div>`
                    })


        Layer +=    `</div></div>` 

    return Layer

}


function layerLandUseKLHK() {
    const layerId = "layer-landuse-klhk"
    const itemPrefix = "landuse-klhk"
    const layerItem = [
        {
            key:"1001", 
            icon:"circle-poly-1001",
            caption:"Protected Forest",
            type:'geojson', 
            title:"Protected Forest"
        },
        {
            key:"1002", 
            icon:"circle-poly-1002",
            caption:"Conservation Forest",
            type:'geojson', 
            title:"Conservation Forest"
        },
        {
            key:"1003", 
            icon:"circle-poly-1003",
            caption:"Fixed Production Forest",
            type:'geojson', 
            title:"Fixed Production Forest"
        },
        {
            key:"1004", 
            icon:"circle-poly-1004",
            caption:"Limited Production Forest",
            type:'geojson', 
            title:"Limited Production Forest"
        },
        {
            key:"1005", 
            icon:"circle-poly-1005",
            caption:"Conversion Production Forest",
            type:'geojson', 
            title:"Conversion Production Forest"
        },
        {
            key:"1007", 
            icon:"circle-poly-1007",
            caption:"Other Usage Area",
            type:'geojson', 
            title:"Other Usage Area"
        },
    ]
    // var Layer = `<div id="${layerId}" class="legend-card" style='display:none'>
    var Layer = `<div id="${layerId}" class="legend-card">
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Landuse KLHK')} </span>
                        <div>
                            <img class="layer-title-icon card-close" style="margin-left:5px;" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`
                    $.each(layerItem, function(index, val) {
                        const {key,icon,caption,type,title} = val

                        Layer +=`<div class='lcb-layer'>
                                    <img src="${ICON_PATH + icon}.png" alt="" style="height:28px;width:28px" ${(title !="-" )? "title='" + title + "'" :""}>
                                    <div class='lcb-caption'> 
                                        <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer"  ${(title !="-" )? "title='" + title + "'" :""}>${lang(caption)}</label>
                                    </div>    
                                    <label class="switch-layer">
                                        <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="${type}" data-layer="${itemPrefix}" data-tag="${itemPrefix}-${key}"/><i></i>
                                    </label>
                                </div>`
                    })
        Layer +=    `</div></div>` 

    return Layer
}

function layerLandUsePMZ() {
    const layerId = "layer-landuse-pmz"
    const itemPrefix = "landuse-pmz"
    const layerItem = [
        {
            key:"red", 
            icon:"circle-poly-pmz-red",
            caption:"Red Zone",
            type:'geojson', 
            title:"Red Zone"
        },
        {
            key:"yellow", 
            icon:"circle-poly-pmz-yellow",
            caption:"Yellow Zone",
            type:'geojson', 
            title:"Yellow Zone"
        },
        {
            key:"green", 
            icon:"circle-poly-pmz-green",
            caption:"Green Zone",
            type:'geojson', 
            title:"Green Zone"
        },
    ]
    // var Layer = `<div id="${layerId}" class="legend-card">
    var Layer = `<div id="${layerId}" class="legend-card" style='display:none'>
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Landuse KLHK')} </span>
                        <div>
                            <img class="layer-title-icon card-close" style="margin-left:5px;" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`
                    $.each(layerItem, function(index, val) {
                        const {key,icon,caption,type,title} = val

                        Layer +=`<div class='lcb-layer'>
                                    <img src="${ICON_PATH + icon}.png" alt="" style="height:28px;width:28px" ${(title !="-" )? "title='" + title + "'" :""}>
                                    <div class='lcb-caption'> 
                                        <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer"  ${(title !="-" )? "title='" + title + "'" :""}>${lang(caption)}</label>
                                    </div>    
                                    <label class="switch-layer">
                                        <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="${type}" data-layer="${itemPrefix}" data-tag="${itemPrefix}-${key}"/><i></i>
                                    </label>
                                </div>`
                    })
        Layer +=    `</div></div>` 

    return Layer
}

function layerMillDistance() {
    const layerId = "layer-mill-distances"
    const itemPrefix = "mill-distances"
    var Layer = `<div id="${layerId}" class="legend-card">
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Mill Distance')} </span>
                        <div>
                            <img class="layer-title-icon" style="margin-left:5px; display:none" src="${ICON_PATH}kml-download.png" height=14 alt="" id='download-kml-${layerId}' title="Dowload KML">
                            <img class="layer-title-icon export" style="margin-left:5px; display:none" src="${ICON_PATH}xls-download.png" height=14 alt="" id='download-xls-${layerId}' title="Download Excel" data-api="farm_location_export_excel">
                            <img class="layer-title-icon card-close" style="margin-left:5px; display:none" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`



        Layer +=`
                    <div class='lcb-layer'><input type="radio" name="mill_distance" class="mill_distance" id="mill_dist_0" data-distance="0" style='margin:8px;'><label for="mill_dist_0" style="font-size:11px; cursor:pointer;margin:0px;">${lang('0 Km Range')}</label></div>
                    <div class='lcb-layer'><input type="radio" name="mill_distance" class="mill_distance" id="mill_dist_10" data-distance="10" style='margin:8px;'><label for="mill_dist_10" style="font-size:11px; cursor:pointer;margin:0px;">${lang('10 Km Range')}</label></div>
                    <div class='lcb-layer'><input type="radio" name="mill_distance" class="mill_distance" id="mill_dist_30" data-distance="30" style='margin:8px;'><label for="mill_dist_30" style="font-size:11px; cursor:pointer;margin:0px;">${lang('30 Km Range')}</label></div>
                    <div class='lcb-layer'><input type="radio" name="mill_distance" class="mill_distance" id="mill_dist_50" data-distance="50" style='margin:8px;'><label for="mill_dist_50" style="font-size:11px; cursor:pointer;margin:0px;">${lang('50 Km Range')}</label></div>
                `


        Layer +=    `</div></div>` 

    return Layer

}

function layerFireHotspot() {
    const layerId = "layer-fire-hotspot"
    const itemPrefix = "fire-hotspot"
    var Layer = `<div id="${layerId}" class="legend-card">
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Fire Hotspot')} </span>
                        <div>
                            <img class="layer-title-icon" style="margin-left:5px; display:none" src="${ICON_PATH}kml-download.png" height=14 alt="" id='download-kml-${layerId}' title="Dowload KML">
                            <img class="layer-title-icon export" style="margin-left:5px; display:none" src="${ICON_PATH}xls-download.png" height=14 alt="" id='download-xls-${layerId}' title="Download Excel" data-api="farm_location_export_excel">
                            <img class="layer-title-icon card-close" style="margin-left:5px; display:none" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}'>
                        </div>
                    </div> 
                    <div class="legend-card-body">`



        Layer +=`
        <div id="panel-hotspot">
        <div class="col-sm-12">
            <select class="form-control" name="hotspot_satellite" id="hotspot_satellite" placeholder="Satellite" style="font-size:11px;padding:0;height:28px;margin-bottom:8px">
                <option value="">${lang('All Satellite')}</option>
                <option value="Aqua">${lang('Aqua (MODIS)')}</option>
                <option value="Terra">${lang('Terra (MODIS)')}</option>
                <option value="1">${lang('NOAA (VIIRS)')}</option>
                <option value="N">${lang('S-NPP (VIIRS)')}</option>
            </select>
        </div>
        <div class="col-sm-6">
            <div><input type="radio" name="hotspot_timeline" class="hotspot_timeline" id="hotspot_latest" data-timeline="latest" value="latest" ><label style="font-size:11px;margin:0" for="hotspot_latest"> ${lang('Latest')}</label></div>
        </div>
        <div class="col-sm-6">
            <div><input type="radio" name="hotspot_timeline" class="hotspot_timeline" id="hotspot_24h" data-timeline="24h" value="24h" ><label style="font-size:11px;margin:0" for="hotspot_24h"> ${lang('24 Hours')}</label></div>
        </div>
        <div class="col-sm-6">
            <div><input type="radio" name="hotspot_timeline" class="hotspot_timeline" id="hotspot_48h" data-timeline="48h" value="48h" ><label style="font-size:11px;margin:0" for="hotspot_48h"> ${lang('48 Hours')}</label></div>
        </div>
        <div class="col-sm-6">
            <div><input type="radio" name="hotspot_timeline" class="hotspot_timeline" id="hotspot_72h" data-timeline="72h" value="72h" ><label style="font-size:11px;margin:0" for="hotspot_72h"> ${lang('72 Hours')}</label></div>
        </div>
        <div class="col-sm-9" style="height:28px;margin-top:8px;">
            <div data-min-view="2" data-date-format="yyyy-mm-dd" class="input-group date datetimepicker" style="margin-right:8px;height:28px">
                <input size="16" type="text" name="hotspot_date" value="" class="form-control" style="padding:0px; padding-left:8px;height:28px; font-size:11px"><span class="input-group-addon btn btn-primary"><i class="icon-th s7-date"></i></span>
            </div>
        </div>
        <div class="col-sm-3" style="padding: 6px;height:28px;margin-top:2px;">
            <button class="pull-right" id="button-hotspot-view" style="border-radius: 4px;background: #95130b;">View</button>
        </div>
        <div class="col-sm-12" id="panel-hotspot-trust">
            <hr/>
        </div>
    </div>

                `


        Layer +=    `</div></div>` 

    return Layer

}


function draw_mills_distance(distance) {
    var mills = $map_canvas.gmap3({
        get: {
            name: 'marker',
            tag: 'actors-mill',
            all: true
        }
    });
    $.each(mills, function(index, mill) {
        draw_circle('mill-circle', mill.position.lat(), mill.position.lng(), distance);
    });
}

function draw_circle (tag, lat, lng, radius) {
    if (!radius) {
        radius = 5000; // meters
    }
    $map_canvas.gmap3({
        circle:{
            tag,
            options:{
                center: [lat, lng],
                radius : radius,
                fillColor : "#95130b",
                strokeColor : "#95130b"
            },
        },
    });
}

function hide_circle(tag) {
    var objects = $map_canvas.gmap3({
        get: {
            name: 'circle',
            tag: tag,
            all: true
        }
    });
    if (objects) {
        $.each(objects, function (idx, object) {
            object.setMap(null);
        })   
    }
}

// ----------------------
function loadKmlLayer() {
    var off_layer_testing = ["restrictedarea", "safearea"]

    $.each(LANDUSE_ITEMS, function(index, {kml, prefix, caption, is_show, category}) {
        const layerId = "layer-landuse-" + prefix
        const itemPrefix = "landuse-" + prefix 


        if(TESTING){
            if(off_layer_testing.includes(prefix)) is_show = false
        }

        var Layer = `<div id="${layerId}" class="legend-card">
            <div class="legend-card-title">
                <img src="${ICON_PATH}drag.svg" alt="">
                <span class="card-title" data-id='${layerId}' data-show="show">${lang(caption)}</span> 
                <div>
                <img class="layer-title-icon card-close" style="margin-left:5px" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}' data-kml="${kml}" title="${lang('Hide')}">
                </div>
            </div> 
            <div class="legend-card-body">`
        if(LayerKML[kml]){
            LayerKML[kml].map(({Name, Color})=>{
                var key = Name.replaceAll(".","")
                    key = key.replaceAll(" ","-")
                Layer +=`<div class='lcb-layer' style="display:flex; align-items: flex-start;">
                    <div class='lcb-caption' style="display:flex; align-items: flex-start;padding-top:5px"> 
                        <span class="landuse-span-color" style="margin:0px 7px; background-color:${Color}; border-radius:50%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer">${lang(Name)}</label>
                    </div>    
                    <label class="switch-layer">
                        <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="kml" data-name="${Name}" data-layer="${itemPrefix}" data-tag="kml-${key}"/><i></i>
                    </label>
                </div>`
            })
        
        
            Layer +=    `</div></div>` 
        
            $info_legend.append(Layer)
            is_show ? $('#'+layerId).show() : $('#'+layerId).hide()
        }
    })
}


function layerForestCoverHansen() {
    const layerId = "layer-hansen"
    const itemPrefix = "hansen"

    var Layer = `<div id="${layerId}" class="legend-card">
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">${lang('Global Forest Change')} (<a href="https://glad.earthengine.app/view/global-forest-change#dl=1;old=off;bl=off;lon=20;lat=10;zoom=3;" target="_blank">Hansen, 2020</a>) </span> 
                        <div>
                        <img class="layer-title-icon card-close" style="margin-left:5px" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}' data-tile="Hansen" title="${lang('Hide')}">
                        </div>
                    </div> 
                    <div class="legend-card-body">`
                    
    URL_TILESET_HANSEN.map(({key, caption, group, legend})=>{
        if(legend.type== "single"){

            Layer +=`<div class='lcb-layer' style="display:flex; align-items: flex-start;">
                        <div class='lcb-caption' style="display:flex; align-items: flex-start;padding-top:5px"> 
                            <span class="landuse-span-color" style="margin:0px 7px; background-color:${legend.color}; border-radius:50%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer">${lang(caption)}</label>
                        </div>    
                        <label class="switch-layer" style="display:flex;align-items: flex-start; margin-top:5px">
                            <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="tile" data-name="${group}" data-layer="${itemPrefix}" data-tag="${key}" /><i></i>
                        </label>
                    </div>`
        }else if (legend.type== "triple"){
            Layer +=`<div class='lcb-layer' style='justify-content:flex-start;align-items:start; padding-top:2px'>
                        <div class='lcb-caption' style='display:flex; align-items: flex-start;padding-top:5px'> 
                            <label for="switch-${itemPrefix}-${key}" style="font-size:11px; cursor:pointer;padding-left:28px;">${lang(caption)}</label>
                        </div>    
                        <label class="switch-layer" style="display:flex;align-items: flex-start; margin-top:5px">
                            <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="tile" data-name="${group}" data-layer="${itemPrefix}" data-tag="${key}" /><i></i>
                        </label>
                    </div>
                    <div style='display: flex; padding-left:28px; width:50%;height:13px'>
                        <div class="landuse-span-color" style="margin:0px; flex:1;heigth: 100%; background-color:${legend.color[0]}; border-radius:10px 0 0 10px "></div>
                        <div class="landuse-span-color" style="margin:0px; flex:1;heigth: 100%;background-color:${legend.color[1]}"></div>
                        <div class="landuse-span-color" style="margin:0px; flex:1;heigth: 100%;background-color:${legend.color[2]}; border-radius:0 10px 10px 0"></div>
                    </div>
                    
                    `
        }
    })

    Layer +=    `</div></div>` 

    return Layer
}

function layerKLHK() {
    const layerId = "layer-klhk"
    const itemPrefix = "klhk"
    var json_legend = null

    // var Layer = `<div id="${layerId}" class="legend-card">
    var Layer = `<div id="${layerId}" class="legend-card" style="display:none">
                    <div class="legend-card-title">
                        <img src="${ICON_PATH}drag.svg" alt="">
                        <span class="card-title" data-id='${layerId}' data-show="show">Ministry of Forestry - IDN</span> 
                        <div>
                        <img class="layer-title-icon card-close" style="margin-left:5px" src="${ICON_PATH}cancel.png" height=18 alt="" data-id='${layerId}' data-tile="KLHK">
                        </div>
                    </div> 
                    <div class="legend-card-body">`

        URL_KAWASAN_HUTAN.map(({key, caption, group, url_legend})=>{
            $.ajax({
                url: url_legend,
                async: false, 
                dataType: 'json',
                success: function (json) {
                    json_legend = json;
                }
              });
            Layer +=`<div class='lcb-layer'>
                        <div class='lcb-caption'> 
                            <label for="switch-${itemPrefix}-${key}" style="font-size:12px; cursor:pointer"><b>${lang(caption)}</b></label>
                        </div>    
                        <label class="switch-layer">
                            <input type="checkbox" class="layer-switch ${itemPrefix}" id="switch-${itemPrefix}-${key}" data-type="tile" data-name="${group}" data-layer="${itemPrefix}" data-tag="${key}" /><i></i>
                        </label>
                    </div>`
            Layer +=`<div style="margin-left:4px; margin-bottom:10px">`
                $.each(json_legend.layers[0].legend, function(index,data){
                    const {label,imageData } = data
                    Layer +=`<div style="display:flex; align-items: center; margin-bottom:4px; margin-right:20px">
                                <div><img src="data:image/png;base64,${imageData}"></div>
                                <div style="margin:4px">${lang(label)}</div>
                            </div>`
                })

                
            Layer +=`</div>`
        })

    Layer +=    `</div></div>` 

    return Layer
}

// Panel Analisis
function formatNumberPrefix (n) {
    const unitList = ['y', 'z', 'a', 'f', 'p', 'n', 'u', 'm', '', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
    const zeroIndex = 8;
    const nn = n.toExponential(2).split(/e/);
    let u = Math.floor(+nn[1] / 3) + zeroIndex;
    if (u > unitList.length - 1) {
        u = unitList.length - 1;
    } else
    if (u < 0) {
        u = 0;
    }
    let val = parseInt(nn[0] * Math.pow(10, +nn[1] - (u - zeroIndex) * 3) * 100)
    val = parseFloat(val/100)
    // return nn[0] * Math.pow(10, +nn[1] - (u - zeroIndex) * 3) + unitList[u];
    return val + unitList[u];
}


function loadAnalisis() {
    var $info_analisis = $('#panel-analisis')
    $info_analisis.html('')
    $info_analisis.append(cardInfoFarmLanduse())
    loadChartInfoFarmLanduse()
}

function cardInfoFarmLanduse() {
    var Card = {
        prefix  : "info-farm-landuse",
        title   : lang("Farm Landuse Summary")
    }

    var content = `<div id="card-${Card.prefix}" class="legend-card">
                    <div style="margin:4px; font-size:14px; font-weight:bold">
                        <span class="card-title" data-id='card-1' data-show="show">${Card.title} </span> 
                    </div> 
                    <div style="margin:4px; width:270">
                        <div id="chart-${Card.prefix}">chart</div>
                    </div> 
                </div>` 
    return content
}


function loadChartInfoFarmLanduse() {
    var Chart = {
        id          : "chart-info-farm-landuse", 
        title       : "",
        category    : [],
        data        : []
    }

    console.log(FARM_LANDUSE_SUMMARY_DATA)

    $.each(FARM_LANDUSE_SUMMARY_DATA, function(index, val) {
        console.log(val)
        Chart.category.push(val.Landuse)
        Chart.data.push(parseFloat(val.AreaHa))
    })

    const totalArea = Chart.data.reduce((sum, a) => sum + a, 0);

    Highcharts.chart(Chart.id, {
        chart: { type: 'column', zoomType: 'xy', height:200, width:270, spacingBottom:0 },
        title: { text: Chart.title},
        xAxis: { categories: Chart.category, labels:{style:{fontsize:8}} },
        yAxis: {
            title: {
                useHTML: true,
                text: '(Ha)'
            }
        },
        tooltip: {
            // headerFormat:   '<span style="font-size:10px">{point.key}</span><table>',
            // pointFormat:    '<tr><td style="padding:0"> Area: </td>' +
            //                 '<td style="color:{series.color};padding:0"><b>{point.y:.1f} ha</b></td></tr>' +
            //                 '<tr><td style="padding:0"> Percent: </td>' +
            //                 '<td style="color:{series.color};padding:0"><b>{point.y:.1f} ha</b></td></tr>',
            // footerFormat:   '</table>',
            // shared: true,
            // useHTML: true

            shared: true,
            useHTML: true,
            formatter:function(){
                var percent = (this.y / totalArea  * 100).toFixed(2)
                var text =
                `
                <div>
                    <span style="font-size:10px">${this.x.name}</span>
                    <table>
                        <tr>
                            <td style="font-size:10px;color:#bed65c;padding:0"><b> ${formatNumberPrefix(this.points[0].y)} ha (${percent} %)</b></td>
                        </tr>
                    </table>
                <div>
                `
                // <b>${this.x.name}</b><br>
                // Area : <span style="color:#bed65c;font-weight: bold> ${formatNumberPrefix(this.points[0].y)} ha (${percent} %) </span><br>
                return text;
            }

        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Farm Polygon',
            data: Chart.data,
            color:'#bed65c'
        }],
        exporting: {enabled: false}, 
        legend:{ enabled:false },
    });
}


// Modal Layer Control Panel
function setModalBody(params){
    $('#lcp-body').html('')
    $('#lcp-body').append(getModalBodyItems(params))

    $('.custom-control-input').on('click',function (event) {
        var layerId     = $(this).data('layerid')
        var itemPrefix  = $(this).data('itemprefix')
        var kmlgroup    = $(this).data('kml')
        var tilegroup   = $(this).data('tile')
        var LayerTypeList   =  {
            feature : ["layer-farm-location-by-age","layer-farm-polygon-by-age","layer-farm-polygon-by-status"],
            kml     : ["layer-landuse-restrictedarea", "layer-landuse-safearea", "layer-landuse-bufferzone","layer-landuse-administrativeboundary","layer-landuse-additionallayer"],
            tileset : ["layer-hansen", "layer-klhk"],
            geojson : ["layer-land-management", "layer-landuse-klhk", "layer-landuse-pmz"],
        }
        var items   = {}
        
        items["layer-farm-location-by-age"] = [
            {type:'marker',  tag:'farm-location-by-age-3'},
            {type:'marker',  tag:'farm-location-by-age-6'},
            {type:'marker',  tag:'farm-location-by-age-18'},
            {type:'marker',  tag:'farm-location-by-age-19'},
        ]

        items["layer-farm-polygon-by-age"] = [
            {type:'polygon',  tag:'farm-polygon-by-age-3'},
            {type:'polygon',  tag:'farm-polygon-by-age-6'},
            {type:'polygon',  tag:'farm-polygon-by-age-18'},
            {type:'polygon',  tag:'farm-polygon-by-age-19'},
        ]

        items["layer-farm-polygon-by-status"] = [
            {type:'polygon',  tag:'farm-polygon-by-status-new'},
            {type:'polygon',  tag:'farm-polygon-by-status-verified'},
            {type:'polygon',  tag:'farm-polygon-by-status-overlap'},
            {type:'polygon',  tag:'farm-polygon-by-status-retake'},
        ]

        items["layer-landuse-klhk"] = [
            {type:'geojson',  tag:'landuse-klhk-1001'},
            {type:'geojson',  tag:'landuse-klhk-1002'},
            {type:'geojson',  tag:'landuse-klhk-1003'},
            {type:'geojson',  tag:'landuse-klhk-1004'},
            {type:'geojson',  tag:'landuse-klhk-1005'},
            {type:'geojson',  tag:'landuse-klhk-1007'},
        ]
        
        items["layer-landuse-pmz"] = [
            {type:'geojson',  tag:'landuse-pmz-red'},
            {type:'geojson',  tag:'landuse-pmz-yellow'},
            {type:'geojson',  tag:'landuse-pmz-green'},
        ]

        $('#lcp-'+layerId).is( ":checked" ) ? $('#'+layerId).show(): $('#'+layerId).hide()

        if(LayerTypeList.feature.includes(layerId)){
            $.each(items[layerId], function(index, {type,tag}) {
                var layers = $map_canvas.gmap3({
                    get: { name: type, tag: tag, all: true}
                });
                
                layers && $.each(layers, (idx, layer) =>layer.setMap(null));
                $('#switch-'+tag).attr('checked', false);
            })
        }

        if(LayerTypeList.geojson.includes(layerId)){
            $.each(items[layerId], function(index, {type,tag}) {
                map.data.forEach(feature => feature.getProperty('tag') == tag && map.data.overrideStyle(feature,{visible : false}))
                $('#switch-'+tag).attr('checked', false);
            })
        }

        if(LayerTypeList.kml.includes(layerId)){
            $.each(LayerKML[kmlgroup], function(i,{Name}){
                var key = Name.replaceAll(".","")
                    key = key.replaceAll(" ","-")
                var tag = "kml-" + key  

                kml_layer[tag] = $map_canvas.gmap3({
                    get: { name: 'kmllayer', tag: tag, all:true}
                });

                kml_layer[tag] && $.each(kml_layer[tag], (idx, kml_layer) =>{
                                        kml_layer.setMap(null)
                                        $('#switch-'+itemPrefix+"-"+key).prop('checked', false)
                })

            })
        }

        if(LayerTypeList.tileset.includes(layerId)){
            // asal jalan dulu untuk layer-hansen
            $.each(URL_TILESET_HANSEN, (i,{key,order})=> { 
                map.overlayMapTypes.setAt(order, null)
                $('#switch-'+itemPrefix+"-"+key).prop('checked', false)
            })
        }

    })
}

function getModalBodyItems(params){
    var modal_body_contain = ``


    // Actors
        var modal_body_actor = ``

        modal_body_actor += `<div class="title card-header">`
        modal_body_actor += `   <span class="d-inline-block"><b>${lang('Actors')}</b></span>`
        modal_body_actor += `</div>`

        modal_body_actor += `<div class="card-body">`
        modal_body_actor += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
        modal_body_actor += `       <div class="title">`
        modal_body_actor += `           <span class="d-inline-block float-left">[${lang('Default')}] ${lang('Actors')}</span>`
        modal_body_actor += `       </div>`
        modal_body_actor += `   </div>`

        //  Farm Polygon By Status
        modal_body_actor += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
        modal_body_actor += `       <div class="title">`
        modal_body_actor += `           <span class="d-inline-block float-left">`
        modal_body_actor += `               <div class="custom-control custom-checkbox">`
        modal_body_actor += `                   <input  type="checkbox" class="custom-control-input lcp" 
                                                        id="lcp-layer-farm-polygon-by-status" 
                                                        data-layerid="layer-farm-polygon-by-status" 
                                                        data-itemprefix="farm-polygon-by-status" 
                                                        />`
        modal_body_actor += `                   <label class="custom-control-label" style="font-size:13px"
                                                        for="lcp-layer-farm-polygon-by-status">
                                                        ${lang('Farm Polygon (Status)')}
                                                  </label>`
        modal_body_actor += `               </div>`
        modal_body_actor += `           </span>`
        modal_body_actor += `       </div>`
        modal_body_actor += `   </div>`

        //  Farm Location & Polygon By Age
        modal_body_actor += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
        modal_body_actor += `       <div class="title">`
        modal_body_actor += `           <span class="d-inline-block float-left">`
        modal_body_actor += `               <div class="custom-control custom-checkbox">`
        modal_body_actor += `                   <input  type="checkbox" class="custom-control-input lcp" 
                                                        id="lcp-layer-farm-location-by-age" 
                                                        data-layerid="layer-farm-location-by-age" 
                                                        data-itemprefix="farm-location-by-age" 
                                                        />`
        modal_body_actor += `                   <label class="custom-control-label" style="font-size:13px"
                                                        for="lcp-layer-farm-location-by-age">
                                                        ${lang('Farm Location (Age)')}
                                                  </label>`
        modal_body_actor += `               </div>`
        modal_body_actor += `           </span>`
        modal_body_actor += `       </div>`
        modal_body_actor += `   </div>`

        
        modal_body_actor += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
        modal_body_actor += `       <div class="title">`
        modal_body_actor += `           <span class="d-inline-block float-left">`
        modal_body_actor += `               <div class="custom-control custom-checkbox">`
        modal_body_actor += `                   <input  type="checkbox" class="custom-control-input lcp" 
                                                        id="lcp-layer-farm-polygon-by-age" 
                                                        data-layerid="layer-farm-polygon-by-age" 
                                                        data-itemprefix="farm-polygon-by-age" 
                                                        />`
        modal_body_actor += `                   <label class="custom-control-label" style="font-size:13px"
                                                        for="lcp-layer-farm-polygon-by-age">
                                                        ${lang('Farm Polygon (Age)')}
                                                  </label>`
        modal_body_actor += `               </div>`
        modal_body_actor += `           </span>`
        modal_body_actor += `       </div>`
        modal_body_actor += `   </div>`



        modal_body_actor += `</div>`

    // Landuse
        var modal_body_landuse = ``
        modal_body_landuse += `<div class="title card-header" style = "margin-top:10px">`
        modal_body_landuse += `   <span class="d-inline-block"><b>${lang('Landuse')}</b></span>`
        modal_body_landuse += `</div>`

        if(TESTING){ // TESTING - Landuse KLHK From Geoserver
            modal_body_landuse += `<div class="card-body">`
            modal_body_landuse += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
            modal_body_landuse += `       <div class="title">`
            modal_body_landuse += `           <span class="d-inline-block float-left">`
            modal_body_landuse += `               <div class="custom-control custom-checkbox">`
            modal_body_landuse += `                   <input checked type="checkbox" class="custom-control-input lcp" 
                                                            id="lcp-layer-landuse-klhk" 
                                                            data-layerid="layer-landuse-klhk" 
                                                            data-itemprefix="landuse-klhk"/>`
            modal_body_landuse += `                   <label class="custom-control-label" style="font-size:13px"
                                                            for="lcp-layer-landuse-klhk">
                                                            ${lang('Landuse KLHK')}
                                                    </label>`
            modal_body_landuse += `               </div>`
            modal_body_landuse += `           </span>`
            modal_body_landuse += `       </div>`
            modal_body_landuse += `   </div>`
            modal_body_landuse += `</div>`

            modal_body_landuse += `<div class="card-body">`
            modal_body_landuse += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
            modal_body_landuse += `       <div class="title">`
            modal_body_landuse += `           <span class="d-inline-block float-left">`
            modal_body_landuse += `               <div class="custom-control custom-checkbox">`
            modal_body_landuse += `                   <input type="checkbox" class="custom-control-input lcp" 
                                                            id="lcp-layer-landuse-pmz" 
                                                            data-layerid="layer-landuse-pmz" 
                                                            data-itemprefix="landuse-pmz"/>`
            modal_body_landuse += `                   <label class="custom-control-label" style="font-size:13px"
                                                            for="lcp-layer-landuse-pmz">
                                                            ${lang('Plantation Management Zone (PMZ)')}
                                                    </label>`
            modal_body_landuse += `               </div>`
            modal_body_landuse += `           </span>`
            modal_body_landuse += `       </div>`
            modal_body_landuse += `   </div>`
            modal_body_landuse += `</div>`
        } else {
            $.each(LANDUSE_ITEMS, function(index, {kml, prefix, caption, is_show, category}) {
                if (category == "Landuse") {
                    
                    const layerId = "layer-landuse-" + prefix
                    const itemPrefix = "landuse-" + prefix 

                    if(LayerKML[kml])  {
                        modal_body_landuse += `<div class="card-body">`
                        modal_body_landuse += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
                        modal_body_landuse += `       <div class="title">`
                        modal_body_landuse += `           <span class="d-inline-block float-left">`
                        modal_body_landuse += `               <div class="custom-control custom-checkbox">`
                        modal_body_landuse += `                   <input ${is_show? 'checked' :''} type="checkbox" class="custom-control-input lcp" 
                                                                        id="lcp-${layerId}" 
                                                                        data-layerid="${layerId}" 
                                                                        data-itemprefix="${itemPrefix}" 
                                                                        data-kml="${kml}"/>`
                        modal_body_landuse += `                   <label class="custom-control-label" style="font-size:13px"
                                                                        for="lcp-${layerId}">
                                                                        ${lang(caption)}
                                                                </label>`
                        modal_body_landuse += `               </div>`
                        modal_body_landuse += `           </span>`
                        modal_body_landuse += `       </div>`
                        modal_body_landuse += `   </div>`
                        modal_body_landuse += `</div>`
                    }
                }
            })
        }

        
    // Landcover
        var modal_body_landcover = ``
        modal_body_landcover += `<div class="title card-header" style = "margin-top:10px">`
        modal_body_landcover += `   <span class="d-inline-block"><b>${lang('Landcover')}</b></span>`
        modal_body_landcover += `</div>`

        modal_body_landcover += `<div class="card-body">`
        modal_body_landcover += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
        modal_body_landcover += `       <div class="title">`
        modal_body_landcover += `           <span class="d-inline-block float-left">`
        modal_body_landcover += `               <div class="custom-control custom-checkbox">`
        modal_body_landcover += `                   <input checked type="checkbox" class="custom-control-input lcp" id="lcp-layer-hansen" data-layerid="layer-hansen" data-itemprefix="hansen" data-tile="hansen"/>`
        modal_body_landcover += `                   <label class="custom-control-label" for="lcp-layer-hansen" style="font-size:13px">${lang('Global Forest Change')} (Hansen, 2020)</label>`
        modal_body_landcover += `               </div>`
        modal_body_landcover += `           </span>`
        modal_body_landcover += `       </div>`
        modal_body_landcover += `   </div>`
        modal_body_landcover += `</div>`


        $.each(LANDUSE_ITEMS, function(index, {kml, prefix, caption, is_show, category}) {
            if (category == "Landcover") {
                
                const layerId = "layer-landuse-" + prefix
                const itemPrefix = "landuse-" + prefix 

                if(LayerKML[kml])  {
                    modal_body_landcover += `<div class="card-body">`
                    modal_body_landcover += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
                    modal_body_landcover += `       <div class="title">`
                    modal_body_landcover += `           <span class="d-inline-block float-left">`
                    modal_body_landcover += `               <div class="custom-control custom-checkbox">`
                    modal_body_landcover += `                   <input ${is_show? 'checked' :''} type="checkbox" class="custom-control-input lcp" 
                                                                    id="lcp-${layerId}" 
                                                                    data-layerid="${layerId}" 
                                                                    data-itemprefix="${itemPrefix}" 
                                                                    data-kml="${kml}"/>`
                    modal_body_landcover += `                   <label class="custom-control-label" style="font-size:13px"
                                                                    for="lcp-${layerId}">
                                                                    ${lang(caption)}
                                                            </label>`
                    modal_body_landcover += `               </div>`
                    modal_body_landcover += `           </span>`
                    modal_body_landcover += `       </div>`
                    modal_body_landcover += `   </div>`
                    modal_body_landcover += `</div>`
                }
            }
        })
        

    // Additional Layer 
        var modal_body_additional = ``
        modal_body_additional += `<div class="title card-header" style = "margin-top:10px">`
        modal_body_additional += `   <span class="d-inline-block"><b>${lang('Additional Layers')}</b></span>`
        modal_body_additional += `</div>`

        $.each(LANDUSE_ITEMS, function(index, {kml, prefix, caption, is_show, category}) {
            if (category == "Additional Layer") {
                
                const layerId = "layer-landuse-" + prefix
                const itemPrefix = "landuse-" + prefix 

                if(LayerKML[kml])  {
                    modal_body_additional += `<div class="card-body">`
                    modal_body_additional += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
                    modal_body_additional += `       <div class="title">`
                    modal_body_additional += `           <span class="d-inline-block float-left">`
                    modal_body_additional += `               <div class="custom-control custom-checkbox">`
                    modal_body_additional += `                   <input ${is_show? 'checked' :''} type="checkbox" class="custom-control-input lcp" 
                                                                    id="lcp-${layerId}" 
                                                                    data-layerid="${layerId}" 
                                                                    data-itemprefix="${itemPrefix}" 
                                                                    data-kml="${kml}"/>`
                    modal_body_additional += `                   <label class="custom-control-label" style="font-size:13px"
                                                                    for="lcp-${layerId}">
                                                                    ${lang(caption)}
                                                            </label>`
                    modal_body_additional += `               </div>`
                    modal_body_additional += `           </span>`
                    modal_body_additional += `       </div>`
                    modal_body_additional += `   </div>`
                    modal_body_additional += `</div>`
                }
            }
        })

        if(LIST_PARTNER_LAND_MANAGEMENT.includes(m_partner_id))  { 
            modal_body_additional += `<div class="card-body" id="div-lcp-layer-land-management">`
            modal_body_additional += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
            modal_body_additional += `       <div class="title">`
            modal_body_additional += `           <span class="d-inline-block float-left">`
            modal_body_additional += `               <div class="custom-control custom-checkbox">`
            modal_body_additional += `                   <input type="checkbox" class="custom-control-input lcp" id="lcp-layer-land-management" data-layerid="layer-land-management" data-itemprefix="land-management" data-tile="land-management"/>`
            modal_body_additional += `                   <label class="custom-control-label" for="lcp-layer-land-management" style="font-size:13px">${lang('Land Management')}</label>`
            modal_body_additional += `               </div>`
            modal_body_additional += `           </span>`
            modal_body_additional += `       </div>`
            modal_body_additional += `   </div>`
            modal_body_additional += `</div>`
        }


    // External Data  
        // KLHK
        var modal_external_data = ``
        if (cekConnTileLayer['KLHK']) {
                
            modal_external_data += `<div class="title card-header" style = "margin-top:10px">`
            modal_external_data += `   <span class="d-inline-block"><b>External Data</b></span>`
            modal_external_data += `</div>`

            modal_external_data += `<div class="card-body">`
            modal_external_data += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
            modal_external_data += `       <div class="title">`
            modal_external_data += `           <span class="d-inline-block float-left">`
            modal_external_data += `               <div class="custom-control custom-checkbox">`
            modal_external_data += `                   <input type="checkbox" class="custom-control-input lcp" id="lcp-layer-klhk" data-layerid="layer-klhk" data-itemprefix="klhk" data-tile="klhk"/>`
            modal_external_data += `                   <label class="custom-control-label" for="lcp-layer-klhk" style="font-size:13px">Ministry of Environment and Forestry - IDN</label>`
            modal_external_data += `               </div>`
            modal_external_data += `           </span>`
            modal_external_data += `       </div>`
            modal_external_data += `   </div>`
            modal_external_data += `</div>`
        }else{
            modal_external_data += `<div class="title card-header" style = "margin-top:10px">`
            modal_external_data += `   <span class="d-inline-block"><b>External Data</b></span>`
            modal_external_data += `</div>`

            modal_external_data += `<div class="card-body">`
            modal_external_data += `   <div class="d-flex justify-content-between align-items-center mb-1 actors">`
            modal_external_data += `       <div class="title">`
            modal_external_data += `           <span class="d-inline-block float-left">`
            modal_external_data += `               <div class="custom-control custom-checkbox">`
            modal_external_data += `                   <label class="custom-control-label"  style="font-size:13px;color:red">Ministry of Environment and Forestry - IDN [503-UNAVAILABLE !]</label>`
            modal_external_data += `               </div>`
            modal_external_data += `           </span>`
            modal_external_data += `       </div>`
            modal_external_data += `   </div>`
            modal_external_data += `</div>`
        }


    modal_body_contain = modal_body_actor + modal_body_landuse + modal_body_landcover + modal_body_additional + modal_external_data
    return modal_body_contain
}