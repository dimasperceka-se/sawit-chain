// [todo]- cek ini untuk icon marker nya
var iconName = {
    "Mill"                      : "warehouse",
    "Agent/Dealer"              : "tier1",
    "DO"                        : "tier2",
    "Farmer With Transactions"  : "farmer_green",
    "Farmer Not Selling"        : "farmer_red"
}

var isDatatable = false

var TraceabilityData = function(){
    this.points             = [];
    this.polylines          = [];
};


// points => PointID,SupplierID,LocationID,Latitude,Longitude,Tipe)
TraceabilityData.prototype.addPoint = function(newPoint){
    (this.points.filter((point)=> point.PointID == newPoint.PointID)).length 
        ? null 
        : this.points.push(newPoint) 
};

TraceabilityData.prototype.addPolyline = function(newLines){
    (this.polylines.filter((lines)=> lines.LineID == newLines.LineID)).length 
        ? null 
        : this.polylines.push(newLines);
    
};

TraceabilityData.prototype.removePointByPointId = function(pointToRemove){
    this.points = this.points.filter((point)=> point.PointID != pointToRemove)
};

TraceabilityData.prototype.renderPoints = function(fitBounds = true){
    // reset bounds
    bounds = new google.maps.LatLngBounds();

    var markers = [];
    this.points.map(val=>{
        markers.push({
            id      : val.PointID,
            latLng  : [parseFloat(val.Latitude), parseFloat(val.Longitude)],
            data    : val,
            tag     : val.Tipe,
            options : {icon: {url: base_url + 'img/maps/' + iconName[val.Tipe] + '_medium.png', anchor  : new google.maps.Point(20, 22)}},     // [todo]- cek sesuaikan icon name nya
            
        })
        
        var myLatLng = new google.maps.LatLng(parseFloat(val.Latitude), parseFloat(val.Longitude));
        bounds.extend(myLatLng);
    })

    $('#map_canvas').gmap3({
        marker: {
            values: markers,
            events: {
                click: function (marker, event, context) {
                    Ext.MessageBox.show({
                        msg: 'Please wait...',
                        progressText: 'Loading...',
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        animateTarget: 'mb9'
                    });
                    // console.log("context",context)
                    var mapObject = $(this).gmap3("get");
                    closeInfoBox(); 
                    
                    $.ajax({
                        type: "GET",
                        url: m_api + '/traceability_api/traceability_maps/info_by_id',  
                        data: {
                            id      : context.data.SupplierID,
                            type    : context.data.Tipe,
                            lat     : context.data.Latitude,
                            long    : context.data.Longitude,
                            LatitudeParent    : context.data.LatitudeParent,
                            LongitudeParent    : context.data.LongitudeParent,

                            WarehouseID: $('#filterWarehouse').val(),
                            Tier1: $('#filterTier1').val(),
                            Tier2: $('#filterTier2').val(),
                            StartDate: $('#startDate').val(),
                            EndDate: $('#endDate').val(),
                            key: $('#filter-key').val(),
                        },
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        async: false,
                        success: function(data) {
                            // console.log(data)
                            getInfoBox(context.data.PointID, context.data.SupplierID, context.data.Latitude, context.data.Longitude, context.data.Tipe, data).open(mapObject, marker)
                            isDatatable = false;
                            Ext.MessageBox.hide();
                        },
                        failure: function(data){
                            Ext.MessageBox.hide();
                        }
                    });          
                }
            }
        }
    })

    if(fitBounds){
        $('#map_canvas').gmap3("get").fitBounds(bounds)
        $('#map_canvas').gmap3("get").setZoom($('#map_canvas').gmap3("get").getZoom()-1)
    }
    
};


TraceabilityData.prototype.renderPolylines = function(){
    this.polylines.map(val=>{
        const polyline_options = {
            strokeColor: '#4ABDE2',
            strokeOpacity: 1.0,
            strokeWeight: 3,
            icons: [{
                icon: {path: google.maps.SymbolPath.FORWARD_OPEN_ARROW},
                offset: '50px',
                repeat: '100px'
            }]
        }

        $('#map_canvas').gmap3({
            polyline: {                    
               values:[{options:{
                   path: [[val.From.Latitude,val.From.Longitude],[val.To.Latitude,val.To.Longitude]]
               }}],
               options:polyline_options
            }
         });
    })
    
}
/*
    Show Detail : menampilkan data (child) dari objek yang diinginkan
    - Ajax -> query data transaksi yang berhubungan dengan objek tersebut
    - add point & polylines -> render  
*/

TraceabilityData.prototype.showDetail= function(SupplierID, lat, long){

    closeInfoBox()
    // $('#map_canvas').gmap3({clear: {}});
    Ext.MessageBox.show({
        msg: 'Please wait...',
        progressText: 'Loading...',
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
        url: m_api+'/traceability_api/traceability_maps/get_relation_farmer',
        data: {
            
            to_id: SupplierID,
            lat: lat,
            long: long,
            WarehouseID: $('#filterWarehouse').val(),
            Tier1: $('#filterTier1').val(),
            Tier2: $('#filterTier2').val(),
            StartDate: $('#startDate').val(),
            EndDate: $('#endDate').val(),
            key: $('#filter-key').val(),
        },
        success: function(data){
            console.log(data)
            // total actor & fill actor span
            // $.each(data.total, function(index, val) {
            //     $(`#total-${index}` ).html(`(${val})`)
            // })

            // Actor (Point)
            $.each(data.actor, function(index, val) {
                var PointID = val.Tipe+ "-" + val.LocationID
                TcData.addPoint({PointID, ...val})
            })
            TcData.renderPoints()

            // Transaction (Line)
            $.each(data.transaction, function(index, val) {
                var LineID = val.From.Tipe + "-" + val.From.LocationID + "-" + val.To.Tipe + "-" + val.To.LocationID
                TcData.addPolyline({LineID, ...val})
            })
            TcData.renderPolylines()
            Ext.MessageBox.hide();
            // console.log(TcData)
        },
        failure: function(data){
            Ext.MessageBox.hide();
        }
    })
}
/*
    hide Detail : menyembunyikan data (child) dari objek yang diinginkan
    - remove points dan polylines nya lalu di render
*/
TraceabilityData.prototype.hideDetail= function(point_id, id, type){
    // console.log(this.polylines)
    $('#map_canvas').gmap3({clear: {}});
    closeInfoBox()
    var removePolylines = this.polylines.filter(line => line.To.SupplierID == id && line.To.Tipe == type)
        // console.log(removePolylines)

    removePolylines.map(childLine=>{

        let removePolylinesChild = this.polylines.filter(nextLine => nextLine.To.SupplierID == childLine.From.SupplierID && nextLine.To.Tipe  == childLine.From.Tipe)
        removePolylinesChild.map(data=>{    
            this.removePointByPointId(data.From.Tipe + "-" + data.From.SupplierID)

            let nextRemovePoint = this.polylines.filter(nextLine => nextLine.To.SupplierID == data.From.SupplierID && nextLine.To.Tipe  == data.From.Tipe)
            nextRemovePoint.length && nextRemovePoint.map(nextPoint => this.removePointByPointId(nextPoint.From.Tipe + "-" + nextPoint.From.SupplierID) )

            this.polylines = this.polylines.filter(line => line.To.SupplierID != data.From.SupplierID || line.To.Tipe != data.From.Tipe)            //**
            this.polylines = this.polylines.filter(line => line.From.SupplierID  != data.From.SupplierID  || line.From.Tipe != data.From.Tipe)        //**

        })

        this.polylines = this.polylines.filter(line => line.To.SupplierID  != childLine.From.SupplierID || line.To.Tipe != childLine.From.Tipe)  
        this.polylines = this.polylines.filter(line => line.From.SupplierID != childLine.From.SupplierID || line.From.Tipe != childLine.From.Tipe)  //**
    })

    removePolylines.map(data=>this.removePointByPointId(data.From.Tipe + "-" + data.From.SupplierID))
    
    this.polylines = this.polylines.filter(line => line.To.SupplierID  != id || line.To.Tipe != type)


    TcData.renderPoints(fitBounds = false)
    TcData.renderPolylines()
}


// [todo]- cek
function getInfoBox(PointID, id, lat, long, type, data) {
    
    let infoPanel = ""
    let headerPanel = ""
    let infoHeader = ""
    let infoButton = ""
    let infoDetil = ""
    let infoTransaction = ""
    
    // headerPanel
    headerPanel += `<div style="background-color:#95130b; width:100%; height:30px; border-top-left-radius:10px;border-top-right-radius:10px ;">`
    headerPanel += ` <span style="height:30px;line-height:30px;padding-left:10px; font-size:12px; color:white">${type}</span>`
    headerPanel += `</div>`


    // infoHeader
    infoHeader += `<div style="display:flex">`
    infoHeader += ` <img src="${base_url + 'img/maps/' + iconName[type] + '_large.png'}"></img>`
    infoHeader += ` <div style="padding:10px; margin:0px">` 
    infoHeader += `     <div style="font-size:16px; font-weight:bold">  ${data.detail.name}  </div>`
    infoHeader += `     <div>  ID: ${data.detail.id}   </div>`
    infoHeader += ` </div>`
    infoHeader += `</div>`


    // infoButton
    infoButton += `<div style="display:flex">`   
    infoButton += ` <span onclick="showInfoDetil()" id="btn-info-detil" style="background-color:#2BBE72; color:white; border-top-left-radius:5px; border-top-right-radius:5px; color:white; padding:10px; margin-left: 10px; cursor:pointer"> Detail </span>`
    if(data.transaction.data.length > 0 && data.transaction.data!=null && data.transaction.data!=undefined){
        infoButton += ` <span onclick="showInfoTransaction()"  id="btn-info-transaction" style="background-color:#E4E6E7; color:#A1A7AB !important; border-top-left-radius:5px; border-top-right-radius:5px; color:white; padding:10px; margin-left: 5px; cursor:pointer"> Transaction </span>`
    }
    infoButton += `</div>`


    // infoDetil -> di isi dari data.detail

    infoDetil += `<div id="info-detil" style="margin:10px; height:60%; display: flex;flex-direction: column;justify-content:space-between; color:black">`  
    infoDetil += `    <div>`
    infoDetil += `      <table border="0">`
    infoDetil += `        <tbody>`

        var excludeKey = ['type', 'image_url', 'profile_url' ]; // exclude key yang tidak ingin ditampilkan

        for (const [key, value] of Object.entries(data.detail)) {
            if(!excludeKey.includes(key) ) {
                infoDetil += `<tr>`
                infoDetil += `  <td width="100px" style = "text-transform:capitalize;">${lang(key)} </td>`
                infoDetil += `  <td style="background-color:#D4D5D6; padding:4px 8px 4px 8px; border-radius:4px; width:300px "> ${value} </td>`
                infoDetil += `</tr><tr style="height:10px"></tr>`
            }
        }

    infoDetil += `        </tbody>`
    infoDetil += `      </table>`
    infoDetil += `    </div>`

    var directChild = TcData.polylines.filter(line => line.To.Tipe == type && line.To.SupplierID == id).length
    if(type=='Farmer With Transactions' || type=='Farmer Not Selling'){
        var showNone = 'display: none';
    }else{
        var showNone = '';
    }
    if(directChild){
        // HIDE Detail jika directChild != 0
        // infoDetil += `    <span id="detail-button" onClick="TcData.hideDetail('${PointID}','${id}','${type}')" style="background-color: #4A90E2; color:white; margin:10px; padding:10px; border-radius:5px; align-self:flex-end; cursor:pointer; ${showNone}">`
        // infoDetil += `      HIDE DETAIL`   
        // infoDetil += `    </span>` 
    }else{
        // // SHOW Detail jika directChild = 0
        // infoDetil += `    <span id="detail-button" onClick="TcData.showDetail('${id}','${lat}','${long}')" style="background-color: #4A90E2; color:white; margin:10px; padding:10px; border-radius:5px; align-self:flex-end; cursor:pointer; ${showNone}">`
        // infoDetil += `      SHOW DETAIL`   
        // infoDetil += `    </span>`   
    }

    infoDetil += `</div>`   

    // [todo] - cek lagi setelah SQL real
    // setButtonDetail(PointID, id, type,uniqueChild=12)


    // infoTransaction -> di isi dari data.transaction
        // console.log("tableTransHeader: ",tableTransHeader)
    infoTransaction += `<div id="info-transaction" style="display:none;margin:10px">`
        // Datatable
        // id="trans-table"

        // infoTransaction += `    TABEL TRANSAKSI`
    if(data.transaction.data.length > 0 && data.transaction.data!=null && data.transaction.data!=undefined){
        let tableTransHeader = Object.keys(data.transaction.data[0])
        infoTransaction += `<table id="trans-table">`
        infoTransaction += `     <thead>`
        infoTransaction += `         <tr>`
        infoTransaction += `            <th>No</th>`

        tableTransHeader.map((header)=> infoTransaction += `<th style = "text-transform:capitalize;">${header}</th>`)
        
        infoTransaction += `         </tr>`
        infoTransaction += `     </thead>`
        infoTransaction += `     <tbody >`

        data.transaction.data.map((data,index)=>{
            infoTransaction += `     <tr><td>${index+1}</td>`
            tableTransHeader.map((header)=> infoTransaction += `<td>${data[header]}</td>`)
            infoTransaction += `     </tr>`;
        });  

        infoTransaction += `     </tbody >`
        infoTransaction += `</table>`
    }

    infoTransaction += `</div>`


    // infoPanel
    infoPanel += `<div style="background-color:white; width:700px; height:500px; border-radius:10px; box-shadow:1px 1px lightgrey; border: 1px solid #2BBE72">`
    infoPanel += headerPanel
    infoPanel += infoHeader
    infoPanel += infoButton
    infoPanel += infoDetil
    infoPanel += infoTransaction
    infoPanel += `</div>`


    
    return new InfoBox({
        content: infoPanel,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(30, -195),
        closeBoxMargin: "7px 7px 2px 2px",
        closeBoxURL: m_base_url+"img/close.gif",
        isHidden: false,
        pane: 'floatPane',
        enableEventPropagation: true,
    });
};

/*
    function tambahan:
*/
function showInfoDetil() {
    $('#info-detil').show();
    $('#btn-info-detil').css( "color", "white" )
    $('#btn-info-detil').css( "background-color", "#2BBE72" )

    $('#info-transaction').hide();
    $('#btn-info-transaction').css( "color", "#A1A7AB" )
    $('#btn-info-transaction').css( "background-color", "#E4E6E7" )

}function showInfoTransaction() {
    $('#info-detil').hide();
    $('#btn-info-detil').css( "color", "#A1A7AB" )
    $('#btn-info-detil').css( "background-color", "#E4E6E7" )


    $('#info-transaction').show();
    $('#btn-info-transaction').css( "color", "white" )
    $('#btn-info-transaction').css( "background-color", "#2BBE72" )

    if(!isDatatable) {
        $('#trans-table').DataTable(
            {
                "bFilter": false,
                "bLengthChange": false,
                // "autoWidth":false,
                // "scrollX": true, 
                // "scrollCollapse":true
            }
        )
        isDatatable = true
    } 

    
}

function closeInfoBox() {
    $('div.infoBox').remove();
}

// [todo] - cek lagi setelah SQL real
function setButtonDetail(PointID, id, type, uniqueChild){
    // cek apakah dia origin (paling awal transaksi) => kalau iya maka button di hidden
    console.log("type",type) 


    let child_count = TcData.polylines.filter(line => line.to_type == type && line.to_id == id).length

    if (child_count == parseInt(uniqueChild)) {
        $("#detail-button").attr("onclick",`TcData.hideDetail('${PointID}','${id}','${type}')`);
        $("#detail-button").html(lang('Hide Details'));
    }

}