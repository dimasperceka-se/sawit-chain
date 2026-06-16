/******************************************
 *  Author      : sofyan.salim@koltva.com
 *  Created On  : Thu May 27 2022
 *  File        : new_map_global_variable.js
 *******************************************/

var TESTING = false
var $map_canvas = $('#map_canvas');
var $info_legend = $('#panel-legend');
var map         = null;
var activeAjaxConnections = 0;

var LayerKML 	= [] 
var kml_layer 	= []

var FARM_LANDUSE_SUMMARY_DATA = {}

// var url_kml     = m_url_awss3+"/documents/kml/";
var url_kml     = m_base_url + "api/files/kml/";

var hostpots = [
    { key: 'hotspot_high', type: 'marker', api: m_api+'/map/fire_hotspot?confidence=high', label: lang('High')+' (&gt;= 80%)', icon: m_base_url+'img/maps/red.png', color: 'red' },
    { key: 'hotspot_nominal', type: 'marker', api: m_api+'/map/fire_hotspot?confidence=nominal', label: lang('Medium')+' (30% &dash; 79%)', icon: m_base_url+'img/maps/yellow.png', color: 'yellow' },
    { key: 'hotspot_low', type: 'marker', api: m_api+'/map/fire_hotspot?confidence=low', label: lang('Low')+' (&lt;= 29%)', icon: m_base_url+'img/maps/green.png', color: 'green' },
];

var ICON_PATH   = m_base_url + '/images/icons/maps/';

var TileLayer = []
	TileLayer['Hansen'] = []
	TileLayer['KLHK'] 	= []

	
var cekConnTileLayer = []
	cekConnTileLayer['Hansen'] = false
	cekConnTileLayer['KLHK'] = false

var URL_TILESET_HANSEN = [
	{
		key 	: 'PrimaryForest2001',
		url 	: 'https://storage.googleapis.com/earthenginepartners-hansen/tiles/Primary_HT_forests_2001/{z}/{x}/{y}.png',
		caption	: 'Primary Forest (2001, 30m, Pan-Tropical)',
		group 	: "Hansen",
		legend  : {type: "single", color:'green'},
		order   : 5,
	 
	},
	{
		key 	: 'HansenLost20212020',
		url 	: 'https://storage.googleapis.com/earthenginepartners-hansen/tiles/gfc_v1.8/loss_alpha/{z}/{x}/{y}.png',
		caption	: 'Tree Cover Lost (2001-2020)',
		group 	: "Hansen",
		legend  : {type: "single", color:'red'},
		order   : 6,
	},
	{
		key 	: 'HansenLostYear20212020',
		url 	: 'https://storage.googleapis.com/earthenginepartners-hansen/tiles/gfc_v1.8/loss_year_alpha/{z}/{x}/{y}.png',
		caption	: 'Tree Cover Lost per Year (2001-2020)',
		group 	: "Hansen",
		legend  : {type: "triple", color:["yellow","orange","red"]},
		order   : 7,
	},
]

var URL_KAWASAN_HUTAN = [
	{
		key 		: 'KawasanHutanKLHK',
		// url 		: 'https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK/Kawasan_Hutan/MapServer/tile/{z}/{y}/{x}', 			// Indonesia
		url 		: 'https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK_EN/Forest_Area_/MapServer/tile/{z}/{y}/{x}',		// English
		url_legend 	: "https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK_EN/Forest_Area_/MapServer/legend?f=pjson",			// English
		caption		: 'Forest Area',
		group 		: "KLHK",
		order   	: 1,
	},
	{
		key 		: 'TORA',
		url 		: 'https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK_EN/TORA_5th_Revision/MapServer/tile/{z}/{y}/{x}',		// English
		url_legend 	: "https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK_EN/TORA_5th_Revision/MapServer/legend?f=pjson",			// English
		caption		: 'TORA 5th Revision',
		group 		: "KLHK",
		order   	: 2,
	},
	{
		key 		: 'PIAPS',
		url 		: 'https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK/PIAPS_Revisi_V/MapServer/tile/{z}/{y}/{x}',		// English
		url_legend 	: "https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK/PIAPS_Revisi_V/MapServer/legend?f=pjson",			// English
		caption		: 'PIAPS 5th Revision',
		group 		: "KLHK",
		order   	: 3,
	},
	{
		key 		: 'SocialForest',
		url 		: 'https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK_EN/Social_Forest/MapServer/tile/{z}/{y}/{x}',		// English
		url_legend 	: "https://dbgis.menlhk.go.id/arcgis/rest/services/KLHK_EN/Social_Forest/MapServer/legend?f=pjson",			// English
		caption		: 'Social Forest',
		group 		: "KLHK",
		order   	: 4,
	},
]

var LIST_PARTNER_LAND_MANAGEMENT = ["1", "235"]

var LANDUSE_ITEMS =[
	{
		cat_id		: 2,
		kml     	: "AdministrativeBoundary",
		prefix  	: "administrativeboundary",
		caption 	: "Administrative Boundary",
		is_show		: true,
		category	: "Additional Layer"
	},
	{
		cat_id		: 3,
		kml     	: "RestrictedArea",
		prefix  	: "restrictedarea",
		caption 	: "Restricted Area",
		is_show		: true,
		category	: "Landuse"
	},
	{
		cat_id		: 4,
		kml     	: "SafeArea",
		prefix  	: "safearea",
		caption 	: "Safe Area",
		is_show		: true,
		category	: "Landuse"
	},
	{
		cat_id		: 5,
		kml     	: "LandCover",
		prefix  	: "landcover",
		caption 	: "Intact Forest",
		is_show		: true,
		category	: "Landcover"
	},
	{
		cat_id		: 6,
		kml     	: "AnimalHabitat",
		prefix  	: "animalhabitat",
		caption 	: "Animal Habitat",
		is_show		: true,
		category	: "Additional Layer"
	},
	{
		cat_id		: 7,
		kml     	: "BufferZone",
		prefix  	: "bufferzone",
		caption 	: "Buffer Zone",
		is_show		: true,
		category	: "Landuse"
	},
	{
		cat_id		: 8,
		kml     	: "NaturalAreaBoundaries",
		prefix  	: "naturalareaboundaries",
		caption 	: "Natural Area Boundaries",
		is_show		: true,
		category	: "Landuse"
	},
	{
		cat_id		: 9,
		kml     	: "DemarcationAssociated",
		prefix  	: "demarcationassociated",
		caption 	: "Demarcation Associated",
		is_show		: true,
		category	: "Landuse"
	},
	{
		cat_id		: 10,
		kml     	: "PlantationManagementZone",
		prefix  	: "plantationmanagementzone",
		caption 	: "Plantation Management Zone",
		is_show		: true,
		category	: "Landuse"
	},
	{
		cat_id		: 11,
		kml     	: "GreenZone",
		prefix  	: "greenzone",
		caption 	: "Green Zone",
		is_show		: false,
		category	: "Landuse"
	},
	{
		cat_id		: 12,
		kml     	: "YellowZone",
		prefix  	: "yellowzone",
		caption 	: "Yellow Zone",
		is_show		: false,
		category	: "Landuse"
	},
	{
		cat_id		: 13,
		kml     	: "RedZone",
		prefix  	: "redzone",
		caption 	: "Red Zone",
		is_show		: false,
		category	: "Landuse"
	},
]

var COLOR_LANDUSE_KLHK = {
	"1001" 	: "#02AD00",
	"1002" 	: "#AD3FFF",
	"10022" : "#AD3FFF",
	"10024" : "#AD3FFF",
	"10026" : "#AD3FFF",
	"1003" 	: "#8AF200",
	"1004" 	: "#FFFF00",
	"1005" 	: "#FF5EFF",
	"1007" 	: "#FFFFFF",
}

var PMZ_CATEGORY = {
	"red" 	: {tag:"red",    zone: "Red Zone",    color:"#F63225"} ,
	"yellow": {tag:"yellow", zone: "Yellow Zone", color:"#FCEC1B"} ,
	"green" : {tag:"green",  zone: "Green Zone",  color:"#44E11A"} ,
}

var PMZ = {
	"1001" 	: PMZ_CATEGORY["red"],
	"1002" 	: PMZ_CATEGORY["red"],
	"10022" : PMZ_CATEGORY["red"],
	"10024" : PMZ_CATEGORY["red"],
	"10026" : PMZ_CATEGORY["red"],
	"1003" 	: PMZ_CATEGORY["yellow"],
	"1004" 	: PMZ_CATEGORY["yellow"],
	"1005" 	: PMZ_CATEGORY["yellow"],
	"1007" 	: PMZ_CATEGORY["green"],
}

var SILVER_MAP_STYLE = [
			{
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#f5f5f5"
				}
				]
			},
			{
				"elementType": "labels.icon",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#616161"
				}
				]
			},
			{
				"elementType": "labels.text.stroke",
				"stylers": [
				{
					"color": "#f5f5f5"
				}
				]
			},
			{
				"featureType": "administrative.land_parcel",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#bdbdbd"
				}
				]
			},
			{
				"featureType": "poi",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#eeeeee"
				}
				]
			},
			{
				"featureType": "poi",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#e5e5e5"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9e9e9e"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#ffffff"
				}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#dadada"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#616161"
				}
				]
			},
			{
				"featureType": "road.local",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9e9e9e"
				}
				]
			},
			{
				"featureType": "transit.line",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#e5e5e5"
				}
				]
			},
			{
				"featureType": "transit.station",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#eeeeee"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#c9c9c9"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9e9e9e"
				}
				]
			}
	]
			

var DARK_MAP_STYLE = [
			{
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#212121"
				}
				]
			},
			{
				"elementType": "labels.icon",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"elementType": "labels.text.stroke",
				"stylers": [
				{
					"color": "#212121"
				}
				]
			},
			{
				"featureType": "administrative",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "administrative.country",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9e9e9e"
				}
				]
			},
			{
				"featureType": "administrative.land_parcel",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "administrative.locality",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#bdbdbd"
				}
				]
			},
			{
				"featureType": "poi",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#181818"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#616161"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "labels.text.stroke",
				"stylers": [
				{
					"color": "#1b1b1b"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "geometry.fill",
				"stylers": [
				{
					"color": "#2c2c2c"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#8a8a8a"
				}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#373737"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#3c3c3c"
				}
				]
			},
			{
				"featureType": "road.highway.controlled_access",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#4e4e4e"
				}
				]
			},
			{
				"featureType": "road.local",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#616161"
				}
				]
			},
			{
				"featureType": "transit",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#000000"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#3d3d3d"
				}
				]
			}
    ]