var width           = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var height          = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

var bounds      = new google.maps.LatLngBounds();
var infowindow  = new google.maps.InfoWindow();
var infobox     = getInfoBox();

var $map_canvas     = $('#map_canvas');
var $province       = $('#filter-province');
var $district       = $('#filter-district');
var $subdistrict    = $('#filter-subdistrict');
var $bank           = $('#filter-bank');

var filter_toolbar      = $('#filter-toolbar')[0];
var category_toolbar    = $('#category-toolbar')[0];
var fullscreen_toolbar  = $('#fullscreen-toolbar')[0];

var categories = [
    {id: 1, key: 'farmer_fitted', icon: 'bank_farmer_1', label: lang('Farmer Who Fitted the Criteria')},
    {id: 2, key: 'farmer_approved', icon: 'bank_farmer_2', label: lang('Farmers Whose Loan Application Has Been Approved')},
    {id: 3, key: 'farmer_rejected', icon: 'bank_farmer_3', label: lang('Farmers Whose Loan Application Has Been Rejected')},
    // {id: 1, key: 'farmer', label: lang('Petani')},
    // {id: 2, key: 'farmer_certified', label: lang('Petani Tersertifikasi')},
    // {id: 3, key: 'nursery', label: lang('Pembibitan')},
    // {id: 4, key: 'demoplot', label: lang('Demoplot')},
    // {id: 5, key: 'farmer_organization', label: lang('Organisasi Petani')},
    // {id: 6, key: 'warehouse', label: lang('Gudang')},
    // {id: 7, key: 'trader', label: lang('Pedagang')},
];

$(function () {
    // set map size to fit screen
    setMapSize();
    $.each(categories, function(index, val) {
        tpl = '<label><li class="list-group-item"><input type="checkbox" class="skop" name="'+val.key+'" value="'+val.id+'"> <img style="width:32px;" src="'+base_url+'img/maps/'+val.icon+'.png" alt=""> '+val.label+' <span class="skop_total"></span></li></label>';
        $('#category-toolbar ul').append(tpl);
    });
    // get province data
    $.get(url_province, function (data) {
        var opt = '<option value="">' + lang("Pilih Propinsi") + '</option>';
        $.each(data, function (index, val) {
            opt += '<option value="' + val.id + '">' + lang(val.name) + '</option>';
        });
        $province.find('option').remove();
        $province.append(opt);
        if (user_province) {
            $province.val(user_province).change();
            $province.prop('disabled', 'true');
        }
    });
    $('#wrapper').off('change').off('click');
    // on province change
    $('#wrapper').on('change', '#filter-province', function () {
        if ($(this).val() == '') {
            return false;
        }

        $.get(url_district + '?provinceid=' + $(this).val(), function (data) {
            var opt = '<option value="">' + lang("Semua Kabupaten") + '</option>';
            $.each(data, function (index, val) {
                opt += '<option value="' + val.id + '">' + val.name + '</option>';
            });
            $district.find('option').remove();
            $district.append(opt);
            if (user_district) {
                $district.val(user_district).change();
                $district.prop('disabled', 'true');
            }
        });
    });
    $('#wrapper').on('change', '#filter-district', function () {
        if ($(this).val() == '') {
            return false;
        }

        // $.get(url_subdistrict + '?districtid=' + $(this).val(), function (data) {
        //     var opt = '<option value="">' + lang("Semua Kecamatan") + '</option>';
        //     $.each(data, function (index, val) {
        //         opt += '<option value="' + val.id + '">' + val.name + '</option>';
        //     });
        //     $subdistrict.find('option').remove();
        //     $subdistrict.append(opt);
        // });
        $.get(url_bank_branch + '?DistrictID=' + $(this).val(), function (data) {
            var opt = '<option value="">' + lang("Semua Bank") + '</option>';
            $.each(data.data, function (index, val) {
                opt += '<option value="' + val.id + '">' + val.name + '</option>';
            });
            $bank.find('option').remove();
            $bank.append(opt);
            if (user_branch) {
                $bank.val(user_branch).change();
                $bank.prop('disabled', 'true');
                $('#btn_search').click();
            }
        });
    });
    $('#wrapper').on('change', '#filter-bank', function () {
        if ($(this).val() == '') {
            $('#filter-radius').hide();
        } else {
            $('#filter-radius').show();
        }
    });
    // handle button search click
    $('#wrapper').on('click', '#btn_search', function () {
        closeInfoBox();
        province        = $province.val();
        district        = $district.val();
        // subdistrict     = $subdistrict.val();
        subdistrict     = '';
        bank     = $bank.val();
        if (!province) {
            alert(lang('Please select province!'));
            return false;
        }

        clear_map();
        get_bank_markers(province, district, subdistrict, bank);
        if (bank) {

        }
        return false;
    });
    $('#wrapper').on('change', '.skop:not(#all_skop)', function () {
        clickOn($(this));
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
    set_tooltip();
});

$(document).on(screenfull.raw.fullscreenchange, function () {
    setMapSize();
});

function init_map () {
    destroyMap();
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
                if (filter_toolbar) {
                    map.controls[google.maps.ControlPosition.TOP_CENTER].push(filter_toolbar);
                    setTimeout(function(){
                        $(filter_toolbar).removeClass('hidden');
                    }, 200)
                }
                if (category_toolbar) {
                    map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(category_toolbar);
                    setTimeout(function(){
                        $(category_toolbar).removeClass('hidden');
                    }, 200)
                }
                if (fullscreen_toolbar) {
                    map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(fullscreen_toolbar);
                    setTimeout(function(){
                        $(fullscreen_toolbar).removeClass('hidden');
                    }, 200)
                }
            }
        }
    });
}

function get_bank_markers(province, district, subdistrict, bank) {
    places = [];
    $('#btn_search')
        .attr('disabled', 'disabled');
    $('#btn_search_text')
        .text('...');

    api_url = url_geospatial_bank + '?_search&ProvinceID=' + province + '&DistrictID=' + district + '&SubDistrictID=' + subdistrict + '&BranchID=' + bank;

    var jqxhr = $.ajax(api_url)
        .done(function (data) {
            if (data.total != 0) {
                set_point_bank(data);
                if (bank) {
                    radius = parseInt($('#filter-radius').val());
                    draw_circle(data[0].lat, data[0].lng, radius);
                    get_point_radius(data[0].lat, data[0].lng, radius);
                }
            } else {
                Ext.Msg.alert('Info', 'Data not found');
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

function set_point_bank (banks) {
    if (banks.length > 0) {
        var latLngs = [];
        $.each(banks, function(index, val) {
            // check if latitude is valid (kadang kebalik lat lng)
            if (Math.abs(parseFloat(val.lat)) > 90) {
                return false;
            }
            var data = [];
            data['id']      = val.id;
            data['type']    = val.type;
            data['label']   = val.name;
            latLngs.push({
                latLng: [parseFloat(val.lat), parseFloat(val.lng)],
                data: data,
                tag: val.type,
                // options: {
                //     icon: icon_path + val.type + ".png"
                // },
            });
            // add to bounds
            var myLatLng = new google.maps.LatLng(parseFloat(val.lat), parseFloat(val.lng));
            bounds.extend(myLatLng);
        })
        add_markers(latLngs);
        $('#map_canvas').gmap3("get").fitBounds(bounds);
    }
}

function draw_circle (lat, lng, radius) {
    if (!radius) {
        radius = 20000; // meters
    };
    $map_canvas.gmap3({
        circle:{
            options:{
                center: [lat, lng],
                radius : radius,
                fillColor : "#008BB2",
                strokeColor : "#005BB7"
            },
        },
        map: {
            options: {
                zoom: 11,
            }
        }
    });
}

function get_point_radius (lat, lng, radius) {
    var icon_path = base_url + 'img/maps/';
    if (!radius) {
        radius = 20000; // meters
    };


    $.each(categories, function(index, val) {
        $.ajax({
            url: url_geospatial_+val.key,
            data: {
                lat: lat,
                lng: lng,
                radius: radius,
            },
        })
        .done(function(response) {
            // console.log(val.key + " success");
            if (response.length > 0) {
                var latLngs = [];
                for (var i = response.length - 1; i >= 0; i--) {
                    var data = response[i];
                    // check if latitude is valid (kadang kebalik lat lng)
                    if (Math.abs(parseFloat(data.lat)) > 90) {
                        continue;
                    }

                    // data['id']      = val.id;
                    data['type']    = val.key;
                    data['distance']    = data.distance;
                    // data['label']   = val.name;
                    latLngs.push({
                        latLng: [parseFloat(data.lat), parseFloat(data.lng)],
                        data: data,
                        tag: val.key,
                        options: {
                            icon: icon_path + val.icon + ".png"
                        },
                    });

                    var myLatLng = new google.maps.LatLng(parseFloat(data.lat), parseFloat(data.lng));
                    bounds.extend(myLatLng);
                }
                add_markers(latLngs);
                $('#map_canvas').gmap3("get")
                    .fitBounds(bounds);
                set_checked(val.id, response.length);
            } else {
                set_disabled(val.id);
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            // console.log("complete");
        });


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

function add_markers(latlng) {
    $map_canvas.gmap3({
        marker: {
            // tag: tag_name,
            // data: info,
            values: latlng
            , events: {
                click: function (marker, event, context) {
                    var mapObject = $(this).gmap3("get");
                    infobox.setContent('');
                    infobox.close();
                    infobox.open(mapObject, marker);
                    set_info_content(infobox, context.data);
                }
            }
            // , cluster: {
            //     maxZoom: 11,
            //     radius: 200,
            //     0: {
            //         content: '<div class="cluster cluster-1">CLUSTER_COUNT</div>',
            //         width: 53,
            //         height: 52
            //     },
            //     200: {
            //         content: '<div class="cluster cluster-2">CLUSTER_COUNT</div>',
            //         width: 56,
            //         height: 55
            //     },
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
function closeInfoBox() {
    $('div.infoBox').remove();
};

function getInfoBox() {
    var content = '';
    return new InfoBox({
        content: content,
        // disableAutoPan: true,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(30, -195),
        // closeBoxMargin: '50px 200px',
        closeBoxMargin: "12px 3px 2px 2px",
        closeBoxURL: base_url+"img/close.gif",
        // closeBoxURL: '',
        isHidden: false,
        pane: 'floatPane',
        enableEventPropagation: true
    });
};

function set_info_content (infobox, data) {
    var url = '';
    if(data.type == 'farmer_fitted' || data.type == 'farmer_approved' || data.type == 'farmer_rejected'){
        url = m_api+'/bank/detail_farmer/FarmerID/'+data.FarmerID+'/GardenNr/'+data.GardenNr+'/SurveyNr/'+data.SurveyNr;
    } else {
        url = m_api+'/bank/detail_'+data.type+'/id/'+data.id;
    }

    $.ajax({
        url: url,
    })
    .done(function(result) {
        content = get_info_content(data,result);
        infobox.setContent(content);
    })
    .fail(function() {
        // console.log("error");
    })
    .always(function() {
        // console.log("complete");
    });
}


function get_info_content (context, data) {
    var content = '';
    if (context.type == 'bank') {
        content = '\
        <div class="'+context.type+' iw-container">\
            <div class="iw-content">\
            <table border="0" width="320px"><tbody>\
            <tr><td width="100px">' + lang('Bank') + '</td><td>:</td><td> ' + data.bank + '</td></tr>\
            <tr><td>' + lang('Branch') + '</td><td>:</td><td> ' + data.name + '</td></tr>\
            <tr><td>' + lang('Province') + '</td><td>:</td><td> ' + data.Province + '</td></tr>\
            <tr><td>' + lang('District') + '</td><td>:</td><td> ' + data.District + '</td></tr>\
            <tr><td>' + lang('SubDistrict') + '</td><td>:</td><td> ' + data.SubDistrict + '</td></tr>\
            <tr><td>' + lang('Village') + '</td><td>:</td><td> ' + data.Village + '</td></tr>\
            <tr><td>' + lang('Address') + '</td><td>:</td><td> ' + data.address + '</td></tr>\
            <tr><td>' + lang('Phone') + '</td><td>:</td><td> ' + data.phone + '</td></tr>\
            ';
        content += '</td></tr>';
        content += '</tbody></table>\
            </div>\
        </div>\
        ';
        // <tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="btn btn-primary" onclick="displayBeforeCetak('+data.FarmerID+')" href="#"> '+lang('cetak')+' </a></td></tr>\
    } if (context.type == 'farmer_fitted' || context.type == 'farmer_approved' || context.type == 'farmer_rejected') {
        image_url = url_photo + data.Photo;
        content = '\
        <img align="left" width="100px" style="padding:5px" src="' + image_url + '" id="">\
        <div class="'+context.type+' iw-container">\
            <div class="iw-content">\
            <table border="0" width="320px"><tbody>\
            <tr><td width="100px">' + lang('ID Petani') + '</td><td>:</td><td> ' + data.FarmerID + '</td></tr>\
            <tr><td>' + lang('Nama') + '</td><td>:</td><td> ' + data.FarmerName + '</td></tr>\
            <tr><td>' + lang('Plantation Nr') + '</td><td>:</td><td> ' + data.GardenNr + '</td></tr>\
            <tr><td>' + lang('Survey Nr') + '</td><td>:</td><td> ' + data.SurveyNr + '</td></tr>\
            <tr><td>' + lang('Jarak') + '</td><td>:</td><td> ' + number_format(context.distance,1,',','.') + ' Km</td></tr>\
            <tr><td>' + lang('Luas lahan') + '</td><td>:</td><td> ' + ((data.GardenHaUnCertified) ? data.GardenHaUnCertified : '') + ' Ha</td></tr>\
            <tr><td>' + lang('Produksi') + '</td><td>:</td><td> ' + ((data.Produksi) ? data.Produksi : '') + ' Kg</td></tr>\
            <tr><td>' + lang('Pohon') + '</td><td>:</td><td> ' + ((data.Pohon) ? data.Pohon : '') + ' ' + lang('pohon') + '</td></tr>\
            <tr><td>' + lang('Produktivitas') + '</td><td>:</td><td> ' + ((data.Produktivitas) ? number_format(data.Produktivitas,0,',','.') : '') + ' ('+lang('Kg/Ha/Tahun')+')</td></tr>\
            <tr><td colspan="3">@ ' + data.Village +', '+ data.SubDistrict + '</td></tr>\
            ';

        content += '</td></tr>';
        content += '</tbody></table>\
            </div>\
        </div>\
        ';
        // <tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="btn btn-primary" onclick="displayBeforeCetak('+data.FarmerID+')" href="#"> '+lang('cetak')+' </a></td></tr>\
    } else if (context.type == 'demoplot') {
        content = '\
        <div class="demoplot iw-container">\
            <div class="iw-content">\
            <table border="0" width="100%" height="130px"><tbody>\
            <tr><td colspan="3"><b>' + lang('Kelompok') + '</b></td></tr>\
            <tr><td width="100px">' + lang('CPG ID') + '</td><td>:</td><td> ' + data.CPGid + '</td></tr>\
            <tr><td>' + lang('Nama Kelompok') + '</td><td>:</td><td> ' + data.GroupName + '</td></tr>\
            <tr><td colspan="3"><b>' + lang('demoplot') + '</b></td></tr>\
            <tr><td width="100px">' + lang('Farmer ID') + '</td><td>:</td><td> ' + data.FarmerID + '</td></tr>\
            <tr><td>' + lang('Nama') + '</td><td>:</td><td> ' + data.FarmerName + '</td></tr>\
            <tr><td>' + lang('Plantation Nr') + '</td><td>:</td><td> ' + data.GardenNr + '</td></tr>\
            <tr><td>' + lang('Jarak') + '</td><td>:</td><td> ' + number_format(context.distance,1,',','.') + ' Km</td></tr>\
            <tr><td>' + lang('Luas lahan') + '</td><td>:</td><td> ' + ((data.GardenHaUnCertified) ? data.GardenHaUnCertified : '') + ' Ha</td></tr>\
            <tr><td>' + lang('Produksi') + '</td><td>:</td><td> ' + ((data.totalProduksi) ? data.totalProduksi : '') + ' Kg</td></tr>\
            <tr><td>' + lang('Pohon') + '</td><td>:</td><td> ' + ((data.Pohon) ? data.Pohon : '') + ' ' + lang('pohon') + '</td></tr>\
            <tr><td>' + lang('Produktivitas') + '</td><td>:</td><td> ' + ((data.Produktivitas) ? number_format(data.Produktivitas,0,',','.') : '') + ' (' + lang('Kg/Ha/Tahun') + ')</td></tr>\
            </tbody></table>\
            </div>\
        </div>\
        ';
    } else if (context.type == 'nursery') {
        content = '\
        <div class="nursery iw-container">\
            <div class="iw-content">\
            <table border="0" width="320px" height="130px"><tbody>\
            <tr><td width="100px">' + lang('CPG ID') + '</td><td>:</td><td> ' + data.CPGid + '</td></tr>\
            <tr><td>' + lang('Nama Kelompok') + '</td><td>:</td><td> ' + data.GroupName + '</td></tr>\
            <tr><td>' + lang('Farmer Name') + '</td><td>:</td><td> ' + data.FarmerName + '</td></tr>\
            <tr><td>' + lang('Jarak') + '</td><td>:</td><td> ' + number_format(context.distance,1,',','.') + ' Km</td></tr>\
            <tr><td>' + lang('Established') + '</td><td>:</td><td> ' + data.Established + '</td></tr>\
            <tr><td>' + lang('Panjang') + '</td><td>:</td><td> ' + data.Panjang + ' M</td></tr>\
            <tr><td>' + lang('Lebar') + '</td><td>:</td><td> ' + data.Lebar + ' ' + ' M</td></tr>\
            <tr><td>' + lang('Capacity') + '</td><td>:</td><td> ' + data.Kapasitas + '</td></tr>\
            </tbody></table>\
            </div>\
        </div>\
        ';
    } else if (context.type == 'warehouse' || context.type == 'farmer_organization' || context.type == 'trader') {
        content = '\
        <div class="'+context.type+' iw-container">\
            <div class="iw-content">\
            <table border="0" width="320px"><tbody>\
            <tr><td style="width:100px; vertical-align: top;">' + lang('Name') + '</td><td style="vertical-align: top;">:</td><td> ' + data.CoopName + '</td></tr>\
            <tr><td>' + lang('Jarak') + '</td><td>:</td><td> ' + number_format(context.distance,1,',','.') + ' Km</td></tr>\
            <tr><td>' + lang('Village') + '</td><td>:</td><td> ' + data.Village + '</td></tr>\
            <tr><td>' + lang('Sub District') + '</td><td>:</td><td> ' + ((data.SubDistrict) ? data.SubDistrict : '') + '</td></tr>\
            <tr><td>' + lang('Staff Name') + '</td><td>:</td><td> ' + ((data.StaffName) ? data.StaffName : '') + '</td></tr>\
            </tbody></table>\
            </div>\
        </div>\
        ';
    }

    var info = '<div class="marker_info none" id="marker_info">' +
            '<div class="info info_'+context.type+'" id="info">'+
            '<h2 style="text-transform: capitalize;">'+ ((context.type == 'farmer_fitted' || context.type == 'farmer_approved' || context.type == 'farmer_rejected') ? 'X' : lang(context.type)) +'<span></span></h2>' +
            '<span>'+ content +'</span>' +
            // '<a href="'+ 'data.url_point' + '" class="green_btn">More info</a>' +
            '<span class="arrow"></span>' +
            '</div>' +
            '</div>';
    return info;
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
    ;
}