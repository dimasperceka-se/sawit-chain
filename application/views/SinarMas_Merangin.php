<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <title>Map Development</title>
    <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.css' rel='stylesheet' />
    <style>
        body { margin:0; padding:0; }
        h2, h3 {
            margin: 10px;
            font-size: 1.2em;
        }
        h3 {
            font-size: 1em;
        }
        p {
            font-size: 0.85em;
            margin: 10px;
            text-align: left;
        }
        .map-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.8);
            margin-right: 20px;
            font-family: Arial, sans-serif;
            overflow: auto;
            border-radius: 3px;
        }
        #map {
            position:absolute;
            top:0;
            bottom:0;
            width:100%;
        }
        #features {
            top: 0;
            height: 200px;
            margin-top: 10px;
            width: 300px;
        }
        #legend {
            padding: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.10);
            line-height: 18px;
            height: 100px;
            margin-bottom: 40px;
            width: 250px;
        }
        .legend-key {
            display:inline-block;
            border-radius: 20%;
            width: 10px;
            height: 10px;
            margin-right: 5px;
        }
        #menu {   
        position: absolute;
        background: #fff;
        padding: 10px;
        font-family: 'Open Sans', sans-serif;
    }
    </style>
</head>

<body>
<div id='map'></div>
<div class='map-overlay' id='features'><h2>Farmers & Landuse Details</h2><div id='pd'><p>Hover The Shape</p></div></div>
<div class='map-overlay' id='legend'></div>

<div id='menu'>
    <input id='cjgrpglsn000s2sodgko52nuo' type='radio' name='rtoggle' value='vector' checked='checked'>
    <label for='cjgrpglsn000s2sodgko52nuo'>Vector Base Map</label>
    
    <input id='cjgrpgsj0000n2sp7v8lejhc6' type='radio' name='rtoggle' value='satellite'>
    <label for='cjgrpgsj0000n2sp7v8lejhc6'>Satellite Base Map</label>
</div>

<script>
// define access token
mapboxgl.accessToken = 'pk.eyJ1IjoiZ2lza29sdGl2YSIsImEiOiJjamcxcHl4MG8xampqMnFtd3YxaWMwNHd3In0.DKA8uYs1UCV3-ujGZFRX9g';

//create vector base map style
var map = new mapboxgl.Map({
    container: 'map', // container id
    style: 'mapbox://styles/giskoltiva/cjgrpglsn000s2sodgko52nuo' // map style URL from Mapbox Studio
});

// switching start here
var layerList = document.getElementById('menu');
var inputs = layerList.getElementsByTagName('input');

function switchLayer(layer) {
    var layerId = layer.target.id;
    map.setStyle('mapbox://styles/giskoltiva/' + layerId);
}
for (var i = 0; i < inputs.length; i++) {
    inputs[i].onclick = switchLayer;
}
//switching stop here

// wait for map to load before adjusting it
map.on('load', function() {
   
    // make a pointer cursor
    map.getCanvas().style.cursor = 'default';

    // set map bounds for startb loading map.
    map.fitBounds([[101.759160, -2.504977], [102.842291, -1.77591]]);

    // make a pointer cursor
    map.getCanvas().style.cursor = 'default';

    // define legend names
    var layers = ['Nature Conservation Area', 'Limited Prodution Forest', 'Conversion Production Forest', 'Fixed Production Forest', 'Another Usage Area', 'Oil Palm Farmers', 'Buffer Zone'];
    var colors = ['#eaa69b', '#cef2cd', '#f3d7cf', '#d7e4e5', '#f3f2d7', '#efde13', '#9b0996'];

    // create legend
    for (i=0; i<layers.length; i++) {
        var layer = layers[i];
        var color = colors[i];
        var item = document.createElement('div');
        var key = document.createElement('span');
        key.className = 'legend-key';
        key.style.backgroundColor = color;

        var value = document.createElement('span');
        value.innerHTML = layer;
        item.appendChild(key);
        item.appendChild(value);
        legend.appendChild(item);
    }	

    // change info window on hover
    map.on('click',  function (e) {
        var polygon = map.queryRenderedFeatures(e.point, {
            layers: ['PolygonWithNumber']
        });
        var landuse = map.queryRenderedFeatures(e.point, {
            layers: ['LanduseWithNumber']
        });
        if (polygon.length > 0) {
            document.getElementById('pd').innerHTML = "<p>Farmer ID : " + polygon[0].properties.MEMBERID  + "</p>" +
                "<p>Name : " + polygon[0].properties.MEMBERNAME + "</p>" + "<p>Plantation Nr : " + polygon[0].properties.PLOTNR + "</p>" +
                "<p>Survey Nr : " + polygon[0].properties.SURVEYNR + "</p>" + "<p>Ha Survey : " + polygon[0].properties.AREA_HA + "</p>" +
                "<p>Ha Polygon : " + polygon[0].properties.POLYGON_HA + "</p>";
        } else {
            document.getElementById('pd').innerHTML = "<h3><strong>" + landuse[0].properties.Function + "</strong></h3><p><strong><em>" + landuse[0].properties.Sum_Count + "</strong> farmer per land use </em></p>";
        }
// <<place hover effect modul in here>>
        // hover effect start here 
    // hover source polygon and landuse
    map.addSource("polygon", {
        "type": "geojson",
        "data": "https://dl.dropbox.com/s/w73cx6h7lvvg1gl/Merangin_PolygonWithNumber.json?dl=0"
    });
    map.addSource("landuse", {
        "type": "geojson",
        "data": "https://dl.dropbox.com/s/u06uiolqlzefls4/Merangin_LanduseWithNumber.json?dl=0"
    });
// polygon addlayers
    map.addLayer({
        "id": "polygon-fills",
        "type": "fill",
        "source": "polygon", 
        "layout": {},
        "paint": {
            "fill-color": "#fffe1d",
            "fill-opacity": 0
        }
    });
    map.addLayer({
        "id": "polygon-fills-hover",
        "type": "fill",
        "source": "polygon",
        "layout": {},
        "paint": {
            "fill-color": "#627BC1",
            "fill-opacity": 0.5,
        },
        "filter": ["==", "JOINID", ""]
    });
    // When the user moves their mouse over the polygon-fill layer, we'll update the filter in
    // the polygon-fills-hover layer to only show the matching polygon, thus making a hover effect.
    map.on("mousemove", "polygon-fills", function(e) {
        map.setFilter("polygon-fills-hover", ["==", "JOINID", e.features[0].properties.JOINID]);
    });
    // Reset the polygon-fills-hover layer's filter when the mouse leaves the layer.
    map.on("mouseleave", "polygon-fills", function() {
        map.setFilter("polygon-fills-hover", ["==", "JOINID", ""]);
    });

// polygon addlayers
    map.addLayer({
        "id": "landuse-fills",
        "type": "fill",
        "source": "landuse", 
        "layout": {},
        "paint": {
            "fill-color": "#627BC1",
            "fill-opacity": 0
        }
    });
    map.addLayer({
        "id": "landuse-fills-hover",
        "type": "fill",
        "source": "landuse",
        "layout": {},
        "paint": {
            "fill-color": "#627BC1",
            "fill-opacity": 0.5,
        },
        "filter": ["==", "FID_1", ""]
    });
    map.on("mousemove", "landuse-fills", function(e) {
        map.setFilter("landuse-fills-hover", ["==", "FID_1", e.features[0].properties.FID_1]);
    });
    map.on("mouseleave", "landuse-fills", function() {
        map.setFilter("landuse-fills-hover", ["==", "FID_1", ""]);
    });
// hover effect stop here

    }); 
});

</script>

</body>
</html>
