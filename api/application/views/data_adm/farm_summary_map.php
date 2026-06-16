<?php
/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Tue Apr 28 2020
 *  File : polygon_viewer_map.php
 *******************************************/
?>
<div id="FarmSummaryMap" style="width:<?php echo $ContWidth;?>px;height:<?php echo $ContHeight;?>px;margin:3px;"></div>
<script type="text/javascript">
    var mapFarmSummary = null;
    var $map_canvas = $('#FarmSummaryMap');
    var bounds      = new google.maps.LatLngBounds();
    var infowindow  = new google.maps.InfoWindow();

    function InitMap(){
        //Tampilkan Peta Indonesia
        mapFarmSummary = new google.maps.Map(document.getElementById('FarmSummaryMap'), {
            zoom: 5,
            center: new google.maps.LatLng(1.341001, 116.276096),
            // mapTypeId: google.maps.MapTypeId.SATELLITE
        });

        google.maps.event.addListener(mapFarmSummary.data,'addfeature',function(e){
            //check for a polygon
            if(e.feature.getGeometry().getType()==='Polygon'){
                //iterate over the paths
                e.feature.getGeometry().getArray().forEach(function(path){
                //iterate over the points in the path
                    path.getArray().forEach(function(latLng){
                        //extend the bounds
                        console.log(latLng)
                        bounds.extend(latLng);
                    });
                });
            }
        });

        var ptextSearch, CmbPolygonStatus;
        var patchouli_farm_summary_ls = JSON.parse(localStorage.getItem('patchouli_farm_summary_ls'));
        if(patchouli_farm_summary_ls != null){
            ptextSearch = patchouli_farm_summary_ls.ptextSearch;
            CmbPolygonStatus = patchouli_farm_summary_ls.CmbPolygonStatus;
        }else{
            ptextSearch = "";
            CmbPolygonStatus = null;

        }

        Ext.Ajax.request({
            url: m_api+'/data_adm/farm_summary/farm_summary_polygon',
            method: 'GET',
            params: {
                prov: m_ProvinceID,
                kab: m_DistrictID,
                kec: m_SubDistrictID,
                textSearch : ptextSearch,
                CmbPolygonStatus : CmbPolygonStatus,
            },
            success: function(list) {
                var farmSummaryData = JSON.parse(list.responseText)
                $.each(farmSummaryData, function(index, data) {
                    console.log(data);
                    console.log(JSON.parse(data.PolyGeoJson));
                    var geoObject = JSON.parse(data.PolyGeoJson);
                    var coord = geoObject.coordinates[0];
                    var area = [];
                    $.each(coord, function(i, v) {
                        var myLatLng = new google.maps.LatLng(parseFloat(v[1]), parseFloat(v[0]));
                        bounds.extend(myLatLng);
                        area[i] = [v[1], v[0]];
                    });
                    draw_polygon("Farm", data.MemberID + '_' + data.PlotNr, area, data);
                })
                
                $map_canvas.gmap3("get").fitBounds(bounds);
            }
        })
    }

    $(function () {
        setTimeout(InitMap(), 2000);
    });

    function draw_polygon(tag, id, area, data, color) {
        var polygonColor = 'blue';
        if (color) {
            polygonColor = color;
        }
        var farmPolygon = $map_canvas.gmap3({
            polygon: {
                tag: tag,
                id: 'polygon_'+id,
                data: data,
                options: {
                    strokeColor: polygonColor,
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: polygonColor,
                    fillOpacity: 0.35,
                    paths: area
                }
                , events: {
                    click: function (object, event, context) {
                        console.log(context.data)
                        var map = $(this).gmap3("get");
                        var content = "";

                        var region = context.data.ProvinceName+`, `+context.data.DistrictName;
                        var location = "";
                        
                        if(context.data.SubDistrictName == '-' && context.data.VillageName == '-') location = '-';
                        if(context.data.SubDistrictName != '-' && context.data.VillageName == '-') location = context.data.SubDistrictName;
                        if(context.data.SubDistrictName != '-' && context.data.VillageName != '-') location = context.data.SubDistrictName+', '+context.data.VillageName;

                        content += '<p><strong>'+lang('Farm Polygon')+'</strong></p>';
                        content += '<table class="table table-condensed table-hover table-bordered table-striped">';
                            content += '<tbody>';
                                content += '<tr><td style="width: 100px;">'+lang('ID')+'</td><td>'+context.data.ID+'</td></tr>';
                                content += '<tr><td>'+lang('Farmer Name')+'</td><td>'+context.data.FarmerName+'</td></tr>';
                                content += '<tr><td>'+lang('Farm Nr')+'</td><td>'+context.data.FarmNr+'</td></tr>';
                                content += '<tr><td>'+lang('Revision')+'</td><td>'+context.data.Revision+'</td></tr>';
                                content += '<tr><td>'+lang('Ha Survey')+'</td><td>'+context.data.AreaHa+' Ha</td></tr>';
                                content += '<tr><td>'+lang('Region')+'</td><td>'+region+'</td></tr>';
                                content += '<tr><td>'+lang('Location')+'</td><td>'+location+'</td></tr>';
                            content += '</tbody>';
                        content += '</table>';
                        infowindow.setContent(content);
                        infowindow.setPosition(event.latLng);
                        infowindow.open(map);
                    }
                }
            },
        });
    }

</script>