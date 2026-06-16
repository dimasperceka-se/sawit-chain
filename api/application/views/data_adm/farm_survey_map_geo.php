<?php
/******************************************
 *  Author : sofyan.salim@koltiva.com 
 *  Created On : 08-11-2021
 *  File : farm_survey_map_geo.php
 *******************************************/
?>
<div id="DistMap" style="width:<?php echo $ContWidth;?>px;height:<?php echo $ContHeight;?>px;margin:3px;"></div>
<script type="text/javascript">
var mapFarmLocCoor = null;
var markersFarmLocCoor = [];
var mapPolyFarmLocPoly = [];
var jml_cek_poly = 0;
var jml_cek_all_poly = 0;
var jml_cek_gar = 0;
var jml_cek_all_gar = 0;
var data_gar = [];

function renderCoordinatesAndPolygon(DataKoor,DataPoly) {
    removeCoordinates();
    removePolygon();
    markersFarmLocCoor = [];
    mapPolyFarmLocPoly = [];
    let imageMarker = '<?php echo base_url() ?>' + 'images/map/farmer.png';
    let bounds = new google.maps.LatLngBounds();
    let maxZoomService = new google.maps.MaxZoomService();

    //========== Koordinat (BEGIN) ==============================//
    for (let i = 0; i < DataKoor.length; i++) {
        let marker = new google.maps.Marker({
            position: new google.maps.LatLng(parseFloat(DataKoor[i].Latitude), parseFloat(DataKoor[i].Longitude)),
            map: mapFarmLocCoor,
            icon: imageMarker
        });

        //extend the bounds to include each marker's position
        bounds.extend(marker.position);

        //now fit the map to the newly inclusive bounds
        mapFarmLocCoor.fitBounds(bounds);
        mapFarmLocCoor.panToBounds(bounds);

        //add ke global variabel
        markersFarmLocCoor.push(marker);
    }
    //========== Koordinat (END) ==============================//

    //========== Polgyon (BEGIN) ==============================//
    //console.log(DataPoly);
    for (let i = 0; i < DataPoly.length; i++) {
        var Polygonnya = new google.maps.Polygon({
            paths: DataPoly[i].TitikPolygon,
            strokeColor: DataPoly[i].ColorCode,
            strokeOpacity: 0.8,
            strokeWeight: 3,
            fillColor: DataPoly[i].ColorCode,
            fillOpacity: 0.35
        });
        Polygonnya.setMap(mapFarmLocCoor);

        //Set Batasan Zoom nya
        for (var incre = 0; incre < DataPoly[i].TitikPolygon.length; incre++) {
            bounds.extend(new google.maps.LatLng(DataPoly[i].TitikPolygon[incre].lat, DataPoly[i].TitikPolygon[incre].lng));
        }

        //add ke global variabel
        mapPolyFarmLocPoly.push(Polygonnya);
    }
    //========== Polgyon (END) ==============================//

    //(Optional) restore the zoom level after the map is done scaling ============ (Begin)
    let listener = google.maps.event.addListener(mapFarmLocCoor, "idle", function () {
        mapFarmLocCoor.fitBounds(bounds);
        mapFarmLocCoor.panToBounds(bounds);

        //set Zoom Level, cek Sorry, no imagery here
        maxZoomService.getMaxZoomAtLatLng(mapFarmLocCoor.getCenter(), function(response) {
            if (response.status !== 'OK') {
                console.log('Error in MaxZoomService');
            } else {
                if(mapFarmLocCoor.getZoom() > response.zoom){
                    mapFarmLocCoor.setZoom(response.zoom);
                }
            }
        });

        google.maps.event.removeListener(listener);
    });
    //(Optional) restore the zoom level after the map is done scaling ============ (End)
}

function showHideCoordinates(isChecked, urutanIndex) {
    //console.log(markersFarmLocCoor);
    if(isChecked == true) {
        markersFarmLocCoor[urutanIndex].setVisible(true);
        jml_cek_gar = jml_cek_gar + 1;
    } else {
        markersFarmLocCoor[urutanIndex].setVisible(false);
        jml_cek_gar = jml_cek_gar - 1;
    }
    if (jml_cek_gar == jml_cek_all_gar) {
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Garden-BtnCheck').setDisabled(true);
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Garden-BtnUncheck').setDisabled(false);
    } else {
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Garden-BtnCheck').setDisabled(false);
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Garden-BtnUncheck').setDisabled(false);
    }
    if (jml_cek_gar == 0){
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Garden-BtnUncheck').setDisabled(true);
    }
}

function removeCoordinates() {
    for (var i = 0; i < markersFarmLocCoor.length; i++) {
        markersFarmLocCoor[i].setMap(null);
    }
}

function showHidePolygon(isChecked, UrutanIndex) {
    //console.log(mapPolyFarmLocPoly);
    if(isChecked == true) {
        mapPolyFarmLocPoly[UrutanIndex].setVisible(true);
        jml_cek_poly = jml_cek_poly + 1;
    } else {
        mapPolyFarmLocPoly[UrutanIndex].setVisible(false);
        jml_cek_poly = jml_cek_poly - 1;
    }
    if (jml_cek_poly == jml_cek_all_poly) {
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Poly-BtnCheck').setDisabled(true);
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Poly-BtnUncheck').setDisabled(false);
    } else {
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Poly-BtnCheck').setDisabled(false);
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Poly-BtnUncheck').setDisabled(false);
    }
    if (jml_cek_poly == 0){
        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView-Poly-BtnUncheck').setDisabled(true);
    }
}

function removePolygon() {
    for (var i = 0; i < mapPolyFarmLocPoly.length; i++) {
        mapPolyFarmLocPoly[i].setMap(null);
    }
}

function InitMap(){
    //Tampilkan Peta Indonesia
    mapFarmLocCoor = new google.maps.Map(document.getElementById('DistMap'), {
        zoom: 5,
        center: new google.maps.LatLng(1.341001, 116.276096),
        mapTypeId: google.maps.MapTypeId.HYBRID
    });
}

$(function () {
    setTimeout(InitMap(), 2000);
});
</script>