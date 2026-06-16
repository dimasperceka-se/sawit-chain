function closeInfoBox(){
    $('div.infoBox').remove()
}

function checkImageExists(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function() {
        callBack(true);
    };
    imageData.onerror = function() {
        callBack(false);
    };
    imageData.src = imageUrl;
}


function addMarkers(values){
    $('#map_canvas').gmap3({
        marker: {
            values
            , events: {
                click: function (marker, event, context) {
                    var mapObject = $(this).gmap3("get");
                    closeInfoBox();
                    getInfoBox(context).open(mapObject, marker);
                    // cek photo di aws
                        if (context.data.Photo != ""){
                            // var cekImage = m_url_awss3 + '/' + context.data.Photo
                            var cekImage = 'https://d1eyponiky2g3w.cloudfront.net/app/' + context.data.Photo

                            checkImageExists(cekImage, function(existsImage) {
                                if (existsImage == true) {
                                    $("#info-photo-"+context.data.MemberDisplayID).attr("src", cekImage)
                                } else {
                                    $("#info-photo-"+context.data.MemberDisplayID).attr("src", m_base_url + 'api/images/Photo/default-user.png')
                                }
                            });
                        }
                },
            }
        }
    });
}


function getInfoBox(context){
    var content = '';

    content = get_info_content(context)

    return new InfoBox({
        content: content,
        maxWidth: 0,
        // pixelOffset: new google.maps.Size(-150, 0),
        closeBoxMargin: "5px 5px 2px 2px",
        closeBoxURL: ICON_PATH +"close.png",
        isHidden: false,
        pane: 'floatPane',
        enableEventPropagation: true
    });

}


function get_info_content(context){
    var {id,data,tag} = context
    var infoPanel   = ""
    var headerPanel = ""
    var bodyPanel   = ""
    var infoDetil   = ""
    
    var url_file = m_url_awss3 + '/' + data.Photo
    var url_photo = m_base_url + 'api/images/Photo/default-user.png';


      
    var farmArea =  data.AreaHa ? Number(data.AreaHa).toLocaleString() + " " : "-"
    var farmProduction =  data.Production ? Number(data.Production).toLocaleString() + " " : "-"
    var farmAge =  data.FarmAge ? parseInt(data.FarmAge) + " Year" : "-"
    

    var location    = ''
    location        += data.Village
    location        += ", " + data.District
    location        += ", " + data.Province
    
    var display_name = ""
    var display_id = ""
    var detailList  = []
    var url_cetak_surat  = ""

    var switch_type = data["type"].substring(0,7) == "hotspot" ? "hotspot" : data["type"] 

    switch (switch_type) {
        case 'actors-farm-location':
            display_name    = data.Name
            display_id      = data.MemberDisplayID
            url_cetak_surat = m_api + '/farmer/cetak_beneficiary_profiles/MemberID/' + data.ID

            detailList = [
                {label: "Farm Nr"     , value: data.GardenNr},
                {label: "Survey Nr"         , value: data.SurveyNr},
                {label: "Farm Age"          , value: farmAge},
                {label: "Land Area (Ha)"    , value: farmArea},
                {label: "Production"        , value: farmProduction},
                {label: "Address"           , value: location},
            ]
            break;

        case 'actors-sme':
            console.log(data)
            display_name    = data.MemberName
            display_id      = data.MemberDisplayID
            url_cetak_surat = m_api + '/grower/cetak_agent_profiles/MemberID/' + data.MemberID


            detailList = [
                {label: "Role"              , value: data.RoleName},
                {label: "Village"           , value: data.Village},
                {label: "Sub District"      , value: data.SubDistrict},
                {label: "Address"           , value: location},
            ]
            break;

        case 'actors-sme-plantation':
            display_name    = data.MemberName
            display_id      = data.MemberDisplayID
            url_cetak_surat = m_api + '/grower/cetak_agent_profiles/MemberID/' + data.MemberID


            detailList = [
                {label: "Role"              , value: data.RoleName},
                {label: "Farm Nr"           , value: data.GardenNr},
                {label: "Land Area (Ha)"    , value: data.AreaHa},
                {label: "Production (Ton)"  , value: data.Production},
                {label: "Village"           , value: data.Village},
                {label: "Sub District"      , value: data.SubDistrict},
                {label: "Address"           , value: location},
            ]
            break;

        case 'actors-mill':
            display_name    = data.Name
            display_id      = data.DisplayID
            url_cetak_surat = m_api + '/mill/cetak_mill_profiles?MillID=' + data.ID

            detailList = [
                {label: "Village"           , value: data.Village},
                {label: "Sub District"      , value: data.SubDistrict},
                {label: "Address"           , value: location},
            ]
            break;

        case 'hotspot':
            detailList = [
                {label: "Confidence Level"  , value: data.Confidence},
                {label: "Accuired Date"     , value: data.AcqDate},
                {label: "Temperature"       , value: number_format(data.Temperature,2)+ ' &deg;C'},
                {label: "Satellite"         , value: data.SatelliteName},
            ]
            break;

        default:
            detailList  = []
            break;
    } 

    // Header Panel / Info Frame ---------------------------------

    headerPanel += `<div style="background-color:#95130b; width:100%; height:30px; border-top-left-radius:10px;border-top-right-radius:10px ;">`
    headerPanel += `    <span style="height:30px;line-height:30px;padding-left:10px; font-size:12px; color:white">${lang(data["layerTitle"])}</span>`
    headerPanel += `</div>`


    // Body Panel / Info Detil ---------------------------------

    if (switch_type != "hotspot") {
        bodyPanel   += `<div>` 
        bodyPanel   += `    <div style="display:flex">` 
        bodyPanel   += `        <img id="info-photo-${data.MemberDisplayID}" src="${url_photo}" alt="" style="height:65px;width:65px; margin:10px;border-radius:50%">` 
        bodyPanel   += `        <div style="margin:10px">` 
        bodyPanel   += `            <div style="font-size:16px;font-weight:bold;text-transform:capitalize;">${display_name}</div>` 
        bodyPanel   += `            <div style="margin: 2px 0">ID : ${display_id}</div>` 
        bodyPanel   += `    </div>` 
        bodyPanel   += `</div>` 
    }else{
        bodyPanel   += `<div style="margin-top:8px"></div>` 
    }


    bodyPanel   += `<div style="margin:0 10px">`
    bodyPanel   += `    <table border="0" style="font-size:11px">`
    bodyPanel   += `        <tbody>`
    


    
    $.each(detailList, function( i, {label,value} ) {
        bodyPanel += `<tr>`
        bodyPanel += `  <td width="100px" style = "text-transform:capitalize;">${lang(label)} </td>`
        bodyPanel += `  <td style="background-color:#FCEAD1 ; padding:4px 8px 4px 8px; border-radius:4px; width:300px; text-transform:capitalize;"> ${value} </td>`
        bodyPanel += `</tr><tr style="height:10px"></tr>`
    })

    bodyPanel   += `        </tbody>`
    bodyPanel   += `    </table>`
    bodyPanel   += `</div>` 

    if (switch_type != "hotspot") {
        bodyPanel   += `<div style="width:100%; margin-bottom:8px">` 
        bodyPanel   += `    <button onclick="preview_cetak_surat('${url_cetak_surat}')" style="border-radius: 4px; background: #95130b; color:white;border:none; width:60px; height:25px; margin:0 auto;
        display:block;">` 
        bodyPanel   += `    Print` 
        bodyPanel   += `    </button>` 

        bodyPanel   += `</div>` 
    }


    infoPanel += `<div style="background-color:white; width:350px; border-radius:10px; box-shadow:1px 1px lightgrey; border: 1px solid #2BBE72">`
    infoPanel += headerPanel
    infoPanel += bodyPanel
    infoPanel += `</div>`
    
    return infoPanel
}

function draw_polygon(tag, id, area, data, color){
    var polygonColor = color ? color : 'blue';
    var infowindow  = new google.maps.InfoWindow();

    $map_canvas.gmap3({
        polygon: {
            tag, id, data,
            options: {
                strokeColor: polygonColor,
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: polygonColor,
                fillOpacity: 0.35,
                paths: area
            }, 
            events: {
                click: function (object, event, context) {
                    var map = $(this).gmap3("get");
                    closeInfoBox()
                    var falseMarker  = new google.maps.Marker({
                        map: map,
                        position: event.latLng,
                        visible: false
                      });
                    
                    var content         = "";
                    var poly_location   = `${context.data.VillageName}, ${context.data.SubDistrictName}, ${context.data.DistrictName}, ${context.data.ProvinceName}, , ${context.data.CountryName}`;
                    var statusCheck = context.data.StatusCheck != 'partnerverified'? context.data.StatusCheck : "Verified by " + context.data.PartnerName

                    content += '<div style="background-color:white; padding:10px">';
                    content +=      '<p><strong>'+lang('Farm Polygon')+'</strong></p>';
                    content +=      '<table class="table table-condensed table-hover table-bordered table-striped" style="font-size:11px">';
                    content +=          '<tbody>';
                    content +=              '<tr><td style="width: 100px;">'+lang('ID')+'</td><td>'+context.data.MemberDisplayID+'</td></tr>';
                    content +=              '<tr><td>'+lang('Farmer Name')+'</td><td>'+context.data.Name+'</td></tr>';
                    content +=              '<tr><td>'+lang('Farm Nr')+'</td><td>'+context.data.PlotNr+'</td></tr>';
                    content +=              '<tr><td>'+lang('Survey Nr')+'</td><td>'+context.data.SurveyNr+'</td></tr>';
                    content +=              '<tr><td>'+lang('Ha Survey')+'</td><td>'+context.data.GardenAreaHa+'</td></tr>';
                    content +=              '<tr><td>'+lang('Ha Polygon')+'</td><td>'+context.data.GardenAreaPolygon+'</td></tr>';
                    content +=              '<tr><td>'+lang('Status')+'</td><td>'+ statusCheck[0].toUpperCase() + statusCheck.slice(1) +'</td></tr>';
                    content +=      '</table>';
                    content += '</div>';
 
                
                    new InfoBox({
                        content: content,
                        maxWidth: 0,
                        closeBoxMargin: "5px 5px 2px 2px",
                        closeBoxURL: ICON_PATH +"close.png",
                        isHidden: false,
                        pane: 'floatPane',
                        enableEventPropagation: true,
                    }).open(map, falseMarker);
                }
            }
        },
    });
}


function show_hide_feature(type, tag, show, name){
    if(type == 'marker' || type == 'polygon' ){
        var features = $map_canvas.gmap3({get: {name: type, tag: tag, all: true}});
        features && $.each(features, (idx, feature) =>feature.setMap(show));
    } else if (type == 'kml'){
        kml_layer[tag] = $map_canvas.gmap3({
                            get: {
                                name: 'kmllayer',
                                tag: tag,
                                all:true
                            }
                        });
        if (show) {
            if(kml_layer[tag].length > 0) {
                $.each(kml_layer[tag], function (idx, kml_layer) {
                    kml_layer.setMap(map);
                })
            } else {
                Ext.MessageBox.show({
                    msg: lang('Please wait'),
                    progressText: lang('Generating'),
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200},
                    icon: 'ext-mb-info',
                    animateTarget: 'mb9'
                });
                Ext.Ajax.request({
                    url: m_api + '/map/show_kml',
                    method: 'GET',
                    params: {
                        Name: name,
                        ProvinceID: $('#filter-province').val(),
                        DistrictID: $('#filter-district').val()
                    },
                    success: function(response){
                        Ext.MessageBox.hide();
                        if (response.responseText) {
                            let data = JSON.parse(response.responseText);
                            $.each(data, function (idx, DataLayer) {
                                $map_canvas.gmap3({
                                    kmllayer:{
                                        options:{url: url_kml + DataLayer.FileName},
                                        tag: tag,
                                        id: tag + DataLayer.ID
                                    },
                                });
                            });
                        } else {
                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: lang('Sorry, There are no KML file for this layer'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    },
                    failure: function(rp, o) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: lang('Information'),
                            msg: lang('Sorry, There are no KML file for this layer'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                });
            }
        }else {
            kml_layer[tag] && $.each(kml_layer[tag], (idx, kml_layer) =>kml_layer.setMap(null))
        }
    } else if (type == 'tile'){
        switch (name) {
            case 'Hansen':
                var tile_layer = URL_TILESET_HANSEN.filter(url => url.key == tag)
                console.log(tile_layer)
                map.overlayMapTypes.setAt(tile_layer[0]["order"], show? TileLayer[name][tag] : null);
                break;
            case 'KLHK':
                var tile_layer = URL_KAWASAN_HUTAN.filter(url => url.key == tag)
                map.overlayMapTypes.setAt(tile_layer[0]["order"], show? TileLayer[name][tag] : null);
                break;
            default:
                break;
        }
    }

}

function addGeoJsonClickListener(){

	map.data.addListener('click', function(event) {
		closeInfoBox()
		var falseMarker  = new google.maps.Marker({
			map: map,
			position: event.latLng,
			visible: false,
		});

		var content = getGeoJsonContent(event.feature);

		new InfoBox({
			content: content,
			maxWidth: 0,
			closeBoxMargin: "5px 5px 2px 2px",
			closeBoxURL: ICON_PATH +"close.png",
			isHidden: false,
			pane: 'floatPane',
			enableEventPropagation: true,
		}).open(map, falseMarker);
	})
}

function getGeoJsonContent(feature){
    var content = ""
    var layer_title = feature.getProperty('layer_title')
    // console.log(feature)
    var layerItem = {
        "land-management": {
            "title" : lang("IUP"),
            "table" : [
                {
                    "key"   : lang("FieldNr"),
                    "value" : feature.getProperty('remark')
                },
                {
                    "key"   : lang("Area"),
                    "value" : Number(feature.getProperty('AreaHa')).toLocaleString() + ' ha'
                },
                {
                    "key"   : lang("Location"),
                    "value" : feature.getProperty('district_name') + ', ' + feature.getProperty('province_name') 
                },

            ]
        },
        "landuse-klhk": {
            "title" : lang("Landuse KLHK"),
            "table" : [
                {
                    "key"   : lang("Decree"),
                    "value" : feature.getProperty('reg_decree')
                },
                {
                    "key"   : lang("Category"),
                    "value" : feature.getProperty('category')
                },
                {
                    "key"   : lang("Landuse"),
                    "value" : feature.getProperty('landuse')
                },

            ]
        },
        "landuse-pmz": {
            "title" : lang("Plantation Management Zone"),
            "table" : [
                {
                    "key"   : lang("Zone"),
                    "value" : feature.getProperty('zone')
                },
                {
                    "key"   : lang("Category"),
                    "value" : feature.getProperty('category')
                },
                {
                    "key"   : lang("Landuse"),
                    "value" : feature.getProperty('landuse')
                },

            ]
        }
    }

    
    content += '<div style="background-color:white; padding:10px; min-width:200px">';
    content +=      '<p><strong>'+ layerItem[layer_title].title +'</strong></p>';
    content +=      '<table class="table table-condensed table-hover table-bordered table-striped" style="font-size:11px">';
    content +=          '<tbody>';

    $.each(layerItem[layer_title].table, function(index, {key,value}) {
        content +=          '<tr><td>'+ key +'</td><td>'+ value +'</td></tr>';
    })

    content +=          '</tbody>';
    content +=      '</table>';
    content += '</div>';

    return content
}

function addGeoJsonMouseOver(){
    map.data.addListener('mouseover', event =>  map.data.overrideStyle(event.feature, {strokeWeight: 3, fillColor:"#95130b"}))
    map.data.addListener('mouseout',  event =>  map.data.overrideStyle(event.feature, {strokeWeight: 1, fillColor:event.feature.getProperty('color')}))
}

