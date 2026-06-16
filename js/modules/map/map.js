var mode = '';
// var places          = [];
// var district_point  = [];
var bounds          = new google.maps.LatLngBounds();
var province, district, key;
var weather = [];

var $map_canvas     = $('#map_canvas');
var $province       = $('#province');
var $district       = $('#district');
var $date_start     = $('#date-start');
var $date_end       = $('#date-end');

var top_toolbar             = $('#top-toolbar')[0];
var age_toolbar             = $('#age-toolbar')[0];
var category_toolbar        = $('#category-toolbar')[0];
var bottom_toolbar          = $('#bottom-toolbar')[0];

var width           = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var height          = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

var infowindow      = new google.maps.InfoWindow();

$(function () {
    setMapSize();
    $.get(url_province, function (data) {
        var opt = '<option value="">' + lang("Pilih Propinsi") + '</option>';
        $.each(data.data, function (index, val) {
            opt += '<option value="' + val.id + '">' + lang(val.province) + '</option>';
        });
        $province.find('option').remove();
        $province.append(opt);
        $('#supply_province').find('option').remove();
        $('#supply_province').append(opt);
    });
    $('#wrapper').off('change').off('click');
    // handle button search click
    $('#wrapper').on('click', '#btn_search', function () {
        closeInfoBox();
        province = $province.val();
        district = $district.val();
        if (district === '') {
            district = 'undefined'
        }

        key     = $('#key').val();
        status_gps  = $('#status_gps').val();

        if (!province) {
            alert(lang('Please select province!'))
            return false;
        }

        clear_map();

        if (district == 'undefined') {
            get_province_markers();
        } else {
            get_district_markers();
        }
        $('#potential').val('');
        return false;
    });
    $('#btn_view_bank').on('click', function () {
        closeInfoBox();
        bank        = $('#bank').val();
        district    = $district.val();

        if (!bank || !district) {
            alert(lang('Please select District and Bank!'))
            return false;
        }
        get_bank_markers(district, bank);
        setTimeout(function(){get_bank_farmer_info()},500);
        return false;
    });
    $('#wrapper').on('click', '#btn_search_supply', function(event) {
        event.preventDefault();
        closeInfoBox();
        if (!$('#supply_province').val()) {
            alert(lang('Please select province!'))
            return false;
        }
        if (!$date_start.val() && !$date_end.val()) {
            alert(lang('Please select dates!'));
            return false;
        }
        get_supplychain_map();
    });
    $('#wrapper').on('change', '#province', function () {
        if ($(this).val() === '') {
            return false;
        }

        $.get(url_district + '?ProvinceID=' + $(this).val(), function (data) {
            var opt = '<option value="">' + lang("Semua Kabupaten") + '</option>';
            $.each(data.data, function (index, val) {
                opt += '<option value="' + val.id + '">' + val.district + '</option>';
            });
            $district.find('option').remove();
            $district.append(opt);
        });
    });
    $('#wrapper').on('change', '#supply_province', function () {
        if ($(this).val() === '') {
            return false;
        }

        $.get(url_province_partner + '?ProvinceID=' + $(this).val(), function (data) {
            var opt = '';
            $.each(data, function (index, val) {
                opt += '<option value="' + val.id + '">' + val.name + '</option>';
            });
            $('#supply_partner').find('option').remove();
            $('#supply_partner').append(opt);
        });
    });
    $('#wrapper').on('change', '#district', function () {
        if ($(this).val() === '') {
            return false;
        }
    });
    $('#wrapper').on('change', '.skop:not(#all_skop)', function () {
        clickOn($(this));
    });
    $('#check_bank').on('change', function () {
        if ($(this).prop('checked')) {
            $(bank_toolbar).removeClass('hidden');
        } else {
            $(bank_toolbar).addClass('hidden');
        }
    });
    var elem_full = $('#map_canvas')[0];
    $('#wrapper').on('click', '#btn_full', function(event) {
        $('.tipso').tipso('hide');
        if (screenfull.isFullscreen) {
            screenfull.exit();
        } else {
            screenfull.request(elem_full);
        }
        set_tooltip();
    });

    $('#tab_default').on('click', function(event) {
        event.preventDefault();
        init_map_default();
        $('#tabs li').removeClass('active');
        $(this).parent().addClass('active');
    });

    $('#tab_supply').on('click', function(event) {
        event.preventDefault();
        init_map_supply();
        $('#tabs li').removeClass('active');
        $(this).parent().addClass('active');
    });

    $('#tab_bank').on('click', function(event) {
        event.preventDefault();
        init_map_bank();
        $('#tabs li').removeClass('active');
        $(this).parent().addClass('active');
    });

    $('#supply_tab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })

    $('#potential').on('change', function(event) {
        event.preventDefault();
        set_potential($(this).val());
    });

    set_tooltip();
});

$(document).on(screenfull.raw.fullscreenchange, function () {
    setMapSize();
});

function set_tooltip () {
    setTimeout(function(){
        $('.tipso').tipso({
            position: 'top',
            background: '#2574A9',
            useTitle: false,
            speed: 100
        });
    }, 1000);
}

function init_map() {
    var map = '';
    if (act_default) {
        $('#button_tab_default').removeClass('hidden');
        if (map === '') map = 'default';
    }
    if (act_supplychain) {
        $('#button_tab_supply').removeClass('hidden');
        if (map === '') map = 'supply';
    }
    if (act_bank) {
        $('#button_tab_bank').removeClass('hidden');
        if (map === '') map = 'bank';
    }
    eval('init_map_'+map+'()');
}

function init_map_() {
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
        }
    }
    )
}

function init_map_default () {
    if (mode == 'default') { return false }
    mode = 'default';
    destroyMap();
    defaultmap = new Defaultmap();
    defaultmap.init();
    categories = defaultmap.categories;
}

function init_map_supply () {
    if (mode == 'supply') { return false }
    mode = 'supply';
    destroyMap();
    supplychain     = new Supplychain();
    supplychain.init();
    setTimeout(function(){
        setMapSize();
    }, 1000);
}

function init_map_bank () {
    if (mode == 'bank') { return false }
    mode = 'bank';
    destroyMap();
    bankmap = new Bankmap();
    bankmap.init();
    categories = bankmap.categories;
    setTimeout(function(){
        setMapSize();
    }, 1000);
}

function get_province_markers() {
    district_point = [];
    $('#btn_search')
        .attr('disabled', 'disabled');
    $('#btn_search_text')
        .text('...');

    var url = '';
    url = url_districtmap;

    api_url = url + '?search=1&ProvinceID=' + province + '&DistrictID=' + district + '&Keyword=' + key + '&Status=' + status_gps;

    var jqxhr = $.ajax(api_url)
        .done(function (data) {
            if (data.total !== 0) {
                set_districtpoint(data);
            } else {
                Ext.Msg.alert('Info', lang('Data not found'));
            }
        })
        .fail(function () {
            // alert("error");
        })
        .always(function () {
            $('#btn_search')
                .removeAttr('disabled');
            $('#btn_search_text')
                .text(lang('Search'));
        });
}

function get_district_markers() {
    places = [];
    $('#btn_search')
        .attr('disabled', 'disabled');
    $('#btn_search_text')
        .text('...');

    var url = '';
    url = url_geospatialbydistrict;

    api_url = url + '?search=1&ProvinceID=' + province + '&DistrictID=' + district + '&Keyword=' + key + '&Status=' + status_gps;

    $.ajax(api_url)
    .done(function (data) {
        // console.log(data);
        if (data.total !== 0) {
            set_point(data);
        } else {
            Ext.Msg.alert('Info', lang('Data not found'));
        }
    })
    .fail(function () {
        alert("error");
    })
    .always(function () {
        $('#btn_search')
        .removeAttr('disabled');
        $('#btn_search_text')
        .text(lang('Search'));
    });
}

function get_bank_markers(district, bank) {

    api_url = url_geospatialbank + '/DistrictID/' + district + '/BankID/' + bank;

    $.ajax(api_url)
    .done(function (data) {
        if (data.total !== 0) {
            set_point_bank(data);
            set_checked('bank', data.length);
        } else {
            Ext.Msg.alert('Info', lang('Data not found'));
        }
    })
    .fail(function () {
    })
    .always(function () {
    });
}

function set_districtpoint(district_point) {
    var icon_path = base_url + 'img/maps/';
    $map_canvas.gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();

    if (district_point.total > 0) {
        // farmer
        var latLngs = [];
        $.each(categories, function(index, val) {
            data_district = district_point[val.key];
            if (data_district.length > 0) {
                var data_count = 0;
                for (var i = data_district.length - 1; i >= 0; i--) {
                    var data = data_district[i];
                    data_count += parseInt(data.count);
                    if (Math.abs(parseFloat(data.Latitude)) > 90) {
                        continue;
                    }

                    data['type'] = val.key;
                    data['label'] = val.label;
                    latLngs.push({
                        latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                        data: data,
                        tag: val.key,
                        options: {
                            icon: icon_path + val.key + ".png"
                        },
                    });

                    var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
                    bounds.extend(myLatLng);
                }
                set_checked(val.id, data_count);
            } else {
                set_disabled(val.id);
            }
        });

        add_district_markers(latLngs);
        $('#map_canvas').gmap3("get")
            .fitBounds(bounds);
    } else {
        set_disabled(1, 0);
        set_disabled(2, 0);
        set_disabled(3, 0);
    }

}

function set_point(places) {
    var icon_path = base_url + 'img/maps/';
    $map_canvas.gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();
    if (places.total > 0) {
        var bound_count = 0;
        // farmer
        var latLngs = [];

        $.each(categories, function(index, val) {
            var data_places = places[val.key];

            if (data_places.length > 0) {
                var ids = [];
                for (var i = data_places.length - 1; i >= 0; i--) {
                    var data = data_places[i];
                    if (Math.abs(parseFloat(data.Latitude)) > 90) {
                        continue;
                    }
                    // if ($.inArray(data.id, ids) == -1) {
                        ids.push(data.id);
                    // }

                    data['type']  = val.key;
                    data['label'] = val.label;
                    data['color'] = val.color;
                    var tags = [val.key];
                    if (val.key == 'farmer' && data.potential != '') {
                        tags.push(data.potential);
                    }
                    latLngs.push({
                        latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                        data: data,
                        tag: tags,
                        options: {
                            icon: icon_path + val.icon
                        },
                    });

                    if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
                        var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
                        bounds.extend(myLatLng);
                    }
                }
                set_checked(val.id, ids.length);
            } else {
                set_disabled(val.id);
            }
        });

        add_markers(latLngs);
        $('#map_canvas').gmap3("get")
            .fitBounds(bounds);
    } else {
        set_disabled(1, 0);
        set_disabled(2, 0);
        set_disabled(3, 0);
    }
}

function set_point_bank(places) {
    clearMarkerByTag('bank');
    clearCirle();
    var icon_path = base_url + 'img/maps/';
    if (places.length > 0) {
        var latLngs = [];

        for (var i = places.length - 1; i >= 0; i--) {
            var data = places[i];
            if (Math.abs(parseFloat(data.Latitude)) > 90) {
                continue;
            }

            data['type'] = 'bank';
            data['label'] = 'Bank';
            latLngs.push({
                latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                data: data,
                tag: 'bank',
                options: {
                    icon: icon_path + "bank.png"
                },
            });
            draw_circle(parseFloat(data.Latitude), parseFloat(data.Longitude));

            var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
            bounds.extend(myLatLng);
        }

        add_markers(latLngs);
    }
}

function draw_circle (lat, lng) {
    radius = parseInt($('#radius').val())*1000;
    if (!radius) {
        radius = 5000; // meters
    }
    $map_canvas.gmap3({
        circle:{
            tag: 'bank',
            options:{
                center: [lat, lng],
                radius : radius,
                fillColor : "#AE5D8F",
                strokeColor : "#AE5D8F"
            },
        },
    });
}

function set_checked(val, total) {
    $('.skop[value="' + val + '"]')
        .removeProp('disabled')
        .prop('checked', 'checked')
        .next().next('.skop_total').text(' (' + total + ')');
}

function set_disabled(val) {
    $('.skop[value="' + val + '"]')
        .removeProp('checked')
        .prop('disabled', 'disabled')
        .next().next('.skop_total').text(' (0)');
}

function add_markers(latlng, tag_name, icon_file, info) {

    $map_canvas.gmap3({
        marker: {
            values: latlng
            , events: {
                click: function (marker, event, context) {
                    if (act_view_detail) {
                        var mapObject = $(this).gmap3("get");
                        closeInfoBox();
                        getInfoBox('point',context).open(mapObject, marker);
                    }
                }
            }
            // , cluster: {
            //     maxZoom: 11,
            //     radius: 200,
            //     // This style will be used for clusters with more than 0 markers
            //     0: {
            //         content: '<div class="cluster cluster-1">CLUSTER_COUNT</div>',
            //         width: 53,
            //         height: 52
            //     },
            //     // This style will be used for clusters with more than 20 markers
            //     200: {
            //         content: '<div class="cluster cluster-2">CLUSTER_COUNT</div>',
            //         width: 56,
            //         height: 55
            //     },
            //     // This style will be used for clusters with more than 50 markers
            //     1000: {
            //         content: '<div class="cluster cluster-3">CLUSTER_COUNT</div>',
            //         width: 66,
            //         height: 65
            //     }
            //     , events: {
            //         click: function (cluster) {
            //             var map = $(this).gmap3("get");
            //             map.setCenter(cluster.main.getPosition());
            //             map.setZoom(map.getZoom() + 1);
            //         }
            //     }
            // }
        }
    });
}

function closeInfoBox() {
    $('div.infoBox').remove();
}

function getInfoBox(type,item) {
    var content = '';
    if (type == 'point') {
        content = get_info_content(item);
    }
    return new InfoBox({
        content: content,
        // disableAutoPan: true,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(30, -195),
        // closeBoxMargin: '50px 200px',
        closeBoxMargin: "20px 3px 2px 2px",
        closeBoxURL: base_url+"img/close.gif",
        // closeBoxURL: '',
        isHidden: false,
        pane: 'floatPane',
        enableEventPropagation: true
    });
}


function get_info_content (context) {
    var content = '';

    //load photo
    if (context.data.Photo !== 'null') {
        var url_load_photo = '';

        if (context.data.type == 'farmer') {
            url_load_photo = url_photo + context.data.VillageID.substring(0,2) + '/' + context.data.Photo;
        }

        if (context.data.type == 'agent') {
            url_load_photo = url_photo_agent + context.data.VillageID.substring(0,2) + '/' + context.data.Photo;
        }

        setTimeout(function() {$("#farmer_photo"+context.data.MemberID).attr("src",url_api_images+'rolling.gif');}, 20);
        $.get(url_load_photo)
        .done(function() {
            console.log('Done');
            image_url = url_load_photo;
            setTimeout(function() {$("#farmer_photo"+context.data.MemberID).attr("src",image_url);}, 100);
        })
        .fail(function() {
            console.log('Fail');
            setTimeout(function() {$("#farmer_photo"+context.data.MemberID).attr("src",url_api_images+'Photo/default-user.png');}, 100);
        })
    }

    if (context.data.type == 'farmer') {
        image_url = url_api_images+'Photo/default-user.png';
        content = '<img id="farmer_photo'+context.data.MemberID+'" align="left" width="100px" style="padding:5px" src="' + image_url + '" id="">';
        content += '<div class="'+context.data.type+' iw-container">';
            content += '<div class="iw-content">';
            content += '<table border="0" width="100%"><tbody>';
            content += '<tr><td width="100px">' + lang('ID Petani') + '</td><td>:</td><td> ' + context.data.MemberDisplayID + '</td></tr>';
            content += '<tr><td>' + lang('Nama') + '</td><td>:</td><td> ' + context.data.MemberName + '</td></tr>';
            content += '<tr><td>' + lang('Plantation Nr') + '</td><td>:</td><td> ' + context.data.PlotNr + '</td></tr>';
            // content += '<tr><td>' + lang('Survey Nr') + '</td><td>:</td><td> ' + context.data.SurveyNr + '</td></tr>';
            content += '<tr><td>' + lang('Luas lahan') + '</td><td>:</td><td> ' + ((context.data.GardenAreaHa) ? context.data.GardenAreaHa : '') + ' Ha</td></tr>';
            // content += '<tr><td>' + lang('Produksi') + '</td><td>:</td><td> ' + ((context.data.totalProduksi) ? context.data.totalProduksi : '') + ' Kg</td></tr>';
            // content += '<tr><td>' + lang('Pohon') + '</td><td>:</td><td> ' + ((context.data.Pohon) ? context.data.Pohon : '') + ' ' + lang('Pohon') + '</td></tr>';
            // content += '<tr><td>' + lang('Produktivitas') + '</td><td>:</td><td> ' + ((context.data.Produktivitas) ? number_format(context.data.Produktivitas,0,',','.') : '') + ' ('+lang('Kg/Ha/Tahun')+')</td></tr>';
            content += '<tr><td colspan="3">@ ' + context.data.Village +', '+ context.data.SubDistrict + '</td></tr>';
        if (context.data.type == 'farmer') {
            content += '<tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="preview_cetak_surat(\''+m_api+'/farmer/cetak_beneficiary_profiles/MemberID/'+context.data.MemberID + '\')" href="#"> ' + lang('Cetak') + ' </a>';
            // preview_cetak_surat(m_api+'/farmer/cetak_beneficiary_profiles/MemberID'+FarmerID);
        }
        // console.log(context.data.is_area);
        if (context.data.is_area == 1) {
            content += '&nbsp;&nbsp;<a style="line-height: 14px;" class="green_btn" onclick="display_area(' + context.data.MemberID + ',' + context.data.PlotNr + ',' + context.data.SurveyNr + ')" href="#"> ' + lang('Polygon') + '</a>';
        }

        content += '</td></tr>';
        content += '</tbody></table>'
        content += '</div>';
        content += '</div>';
        // <tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="btn btn-primary" onclick="displayBeforeCetak('+context.data.MemberID+')" href="#"> '+lang('cetak')+' </a></td></tr>\
    } else if (context.data.type == 'agent') {
        content = '';
        image_url = url_api_images+'Photo/default-user.png';
        content = '<img id="farmer_photo'+context.data.MemberID+'" align="left" width="100px" style="padding:5px" src="' + image_url + '" id="">';
        content += '<div class="'+context.data.type+' iw-container">';
            content += '<div class="iw-content">';
            content += '<table border="0" width="100%"><tbody>';
            content += '<tr><td style="width:100px; vertical-align: top;">' + lang('Name') + '</td><td style="vertical-align: top;">:</td><td> ' + context.data.MemberName + '</td></tr>';
            content += '<tr><td>' + lang('Role') + '</td><td>:</td><td> ' + context.data.RoleName + '</td></tr>';
            content += '<tr><td>' + lang('Sub District') + '</td><td>:</td><td> ' + ((context.data.SubDistrict) ? context.data.SubDistrict : '') + '</td></tr>';
            content += '<tr><td>' + lang('Village') + '</td><td>:</td><td> ' + context.data.Village + '</td></tr>';
            content += '<tr><td>' + lang('Address') + '</td><td>:</td><td> ' + context.data.Address + '</td></tr>';

            content += '<tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="preview_cetak_surat(\''+m_api+'/grower/cetak_agent_profiles/MemberID/'+context.data.MemberID + '\')" href="#"> ' + lang('Cetak') + ' </a>';

            content += '</tbody></table>';
            content += '</div>';
        content += '</div>';
    } else if (context.data.type == 'mill') {
        content = '';
        content += '<div class="'+context.data.type+' iw-container">';
            content += '<div class="iw-content">';
            content += '<table border="0" width="100%"><tbody>';
            content += '<tr><td style="width:100px; vertical-align: top;">' + lang('Name') + '</td><td style="vertical-align: top;">:</td><td> ' + context.data.MillName + '</td></tr>';
            content += '<tr><td>' + lang('Village') + '</td><td>:</td><td> ' + context.data.Village + '</td></tr>';
            content += '<tr><td>' + lang('Sub District') + '</td><td>:</td><td> ' + ((context.data.SubDistrict) ? context.data.SubDistrict : '') + '</td></tr>';
            content += '<tr><td>' + lang('Address') + '</td><td>:</td><td> ' + ((context.data.Address) ? context.data.Address : '') + '</td></tr>';

            content += '<tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="preview_cetak_surat(\''+m_api+'/mill/cetak_mill_profiles/MillID/'+context.data.MillID + '\')" href="#"> ' + lang('Cetak') + ' </a>';

            content += '</tbody></table>';
            content += '</div>';
        content += '</div>';
    }

    var info = '<div class="marker_info none" id="marker_info">' +
            '<div class="info info_'+context.data.color+'" id="info">'+
            '<h2>'+ ((context.data.type == 'farmer' || context.data.type == 'agent') ? '.' : context.data.label) +'<span></span></h2>' +
            '<span>'+ content +'</span>' +
            // '<a href="'+ 'context.url_point' + '" class="green_btn">More info</a>' +
            '<span class="arrow"></span>' +
            '</div>' +
            '</div>';
    return info;
}

function printDirectly(ObjTypeNya,CPGid,NurseryNr){
    var urlNya = url_api+'/nursery/cetak_nursery_summary/'+ObjTypeNya+'/'+CPGid+'/'+NurseryNr+'/';
    preview_cetak_surat(urlNya);
}

function add_district_markers(latlng, tag_name, icon_file, info) {
    $map_canvas.gmap3({
        marker: {
            // tag: tag_name,
            // data: info,
            values: latlng
            , events: {
                mouseover: function (marker, event, context) {
                    var map = $(this).gmap3("get"),
                        infowindow = $(this).gmap3({get: {name: "infowindow"}});
                    var info = '';
                    info = '<strong>' + context.data.District + '</strong><br/>' + context.data.label + ' (' + context.data.count + ')';
                    if (infowindow) {
                        infowindow.open(map, marker);
                        infowindow.setContent(info);
                    } else {
                        $(this).gmap3({
                            infowindow: {
                                anchor: marker,
                                options: {content: info}
                            }
                        });
                    }
                },
                mouseout: function () {
                    var infowindow = $(this).gmap3({get: {name: "infowindow"}});
                    if (infowindow) {
                        infowindow.close();
                    }
                },
                click: function (marker, event, context) {
                    district = context.data.DistrictID;
                    $district.val(district);
                    get_district_markers();
                }
            }
        }
    });
}

function get_bank_farmer_info() {
    var bank_markers = $("#map_canvas").gmap3({
        get: {
            name: "marker",
            tag: 'bank',
            all: true
        }
    });
    // console.log(bank_markers);
    var min_distance = parseInt($('#radius').val());
    if (bank_markers) {
        // fit uncertified farmer
        var fit_farmers = [];
        var farmer_1 = $("#map_canvas").gmap3({
            get: {
                name: "marker",
                tag: 'bank_farmer_1',
                all: true
            }
        });
        // console.log(farmer_1);
        if (farmer_1) {
            $.each(farmer_1, function(index, farmer) {
                farmer_lat = farmer.position.lat();
                farmer_lng = farmer.position.lng();
                var key = farmer_lat+'_'+farmer_lng;
                if ($.inArray(key, fit_farmers) == -1) {
                    $.each(bank_markers, function(i, bank) {
                        if (distance(bank.position.lat(),bank.position.lng(),farmer_lat,farmer_lng,'K') <= min_distance) {
                            fit_farmers.push(key);
                            return false;
                        }
                    });
                }
            });
        }
        // console.log(fit_farmers);
        // fit certified farmer
        var fit_farmers_cert = [];
        var farmer_2 = $("#map_canvas").gmap3({
            get: {
                name: "marker",
                tag: 'bank_farmer_2',
                all: true
            }
        });
        // console.log(farmer_2);
        if (farmer_2) {
            $.each(farmer_2, function(index, farmer) {
                farmer_lat = farmer.position.lat();
                farmer_lng = farmer.position.lng();
                var key = farmer_lat+'_'+farmer_lng;
                if ($.inArray(key, fit_farmers_cert) == -1) {
                    $.each(bank_markers, function(i, bank) {
                        if (distance(bank.position.lat(),bank.position.lng(),farmer_lat,farmer_lng,'K') <= min_distance) {
                            fit_farmers_cert.push(key);
                            return false;
                        }
                    });
                }
            });
        }
        // console.log(fit_farmers_cert);
        // unfit farmer
        var unfit_farmers = [];
        var farmer_3 = $("#map_canvas").gmap3({
            get: {
                name: "marker",
                tag: 'bank_farmer_3',
                all: true
            }
        });
        // console.log(farmer_3);
        if (farmer_3) {
            $.each(farmer_3, function(index, farmer) {
                farmer_lat = farmer.position.lat();
                farmer_lng = farmer.position.lng();
                var key = farmer_lat+'_'+farmer_lng;
                if ($.inArray(key, unfit_farmers) == -1) {
                    $.each(bank_markers, function(i, bank) {
                        if (distance(bank.position.lat(),bank.position.lng(),farmer_lat,farmer_lng,'K') <= min_distance) {
                            unfit_farmers.push(key);
                            return false;
                        }
                    });
                }
            });
        }
        // console.log(unfit_farmers);
        $('#info_farmer_1').text(fit_farmers.length);
        $('#info_farmer_2').text(fit_farmers_cert.length);
        $('#info_farmer_3').text(unfit_farmers.length);
        $('#bank_info').removeClass('hidden');
    } else {
        $('#bank_info').addClass('hidden');
    }
}

function clear_map() {
    district_point = [];
    places = [];
    $('#map_canvas').gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();
}

function clearMarkerByTag (tag) {
    $("#map_canvas").gmap3({
        clear: {
            name: "marker",
            tag: tag
        }
    });
}

function clearCirle () {
    $("#map_canvas").gmap3({
        clear: {
            name: "circle",
        }
    });
}

function clickOn(elm) {
    closeInfoBox();
    var map = $map_canvas.gmap3("get");
    if (elm.is(':checked')) {
        elm.prop('checked', 'checked');
    } else {
        elm.removeProp('checked');
    }

    // set a filter function using the closure data "checkedColors"
    if ($map_canvas.gmap3({get: "clusterer"})) {
        var checked = {};
        $(".skop:not(#all_skop):checked").each(function (i, chk) {
            checked[$(chk).attr('name')] = true;
        });

        $('#map_canvas').gmap3({get: "clusterer"}).filter(function (data) {
            return data.tag in checked;
        });
    } else {
        $(".skop:not(#all_skop)").each(function (idx, chk) {
            var tag = $(chk).attr('name');
            var markers = $('#map_canvas').gmap3({
                get: {
                    name: 'marker',
                    tag: tag,
                    all: true
                }
            });

            $.each(markers, function (idx, elm) {
                elm.setMap($(chk).is(':checked') ? map : null);
            })
        });
    }

}

function set_potential(key) {
    closeInfoBox();
    if (key !== '') {
        // hide all farmers
        hide_markers('farmer');
        // show selected farmer
        total = show_markers(key);
    } else {
        total = show_markers('farmer');
    }
    $('.skop[value="1"]').next().next('.skop_total').text(' (' + total + ')');
}

function hide_markers(tag) {
    var markers = $('#map_canvas').gmap3({
        get: {
            name: 'marker',
            tag: tag,
            all: true
        }
    });
    $.each(markers, function (idx, elm) {
        elm.setMap(null);
    })
}

function show_markers(tag) {
    var map = $map_canvas.gmap3("get");
    var markers = $('#map_canvas').gmap3({
        get: {
            name: 'marker',
            tag: tag,
            all: true
        }
    });
    $.each(markers, function (idx, elm) {
        elm.setMap(map);
    })
    return markers.length;
}

function setMapSize() {
    if (screenfull.isFullscreen) {
        height = screen.height;
    } else {
        height = window.innerHeight - 150;
    }
    $map_canvas.css('height', height);
}
function fixMap(map) {
    if (!map) {
        map = $('#map_canvas').gmap3('get');
    }
    var center = map.getCenter();
    google.maps.event.trigger(map, 'resize');
    map.setCenter(center);
}
function destroyMap() {
    $map_canvas.gmap3({
        clear: {
            name: ['marker', 'line', 'polyline', 'polygon']
        }
    })
    $map_canvas.gmap3('destroy');
}