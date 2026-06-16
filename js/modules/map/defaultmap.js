
var Defaultmap = function() {
    this.categories = [
        {id: 1, key: 'farmer', label: lang('Farmer'), icon: 'farmer.png', color: 'green'}, 
        {id: 2, key: 'agent', label: lang('Agent'), icon: 'agent.png', color: 'blue'},
        {id: 3, key: 'mill', label: lang('Mill'), icon: 'mill.png', color: 'brown'}, 
    ];
};

Defaultmap.prototype.init = function(){
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
                if (top_toolbar) {
                    map.controls[google.maps.ControlPosition.TOP_CENTER].push(top_toolbar);
                    setTimeout(function(){
                        $(top_toolbar).removeClass('hidden');
                    }, 200)
                }
                // if (weather_toolbar) {
                //     map.controls[google.maps.ControlPosition.TOP_RIGHT].push(weather_toolbar);
                //     setTimeout(function(){
                //         $(weather_toolbar).removeClass('hidden');
                //     }, 200)
                // }
                if (category_toolbar) {
                    map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(category_toolbar);
                    setTimeout(function(){
                        $(category_toolbar).removeClass('hidden');
                    }, 200)
                }
                if (age_toolbar) {
                    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(age_toolbar);
                    setTimeout(function(){
                        $(age_toolbar).removeClass('hidden');
                    }, 200)
                }
                if (bottom_toolbar) {
                    map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(bottom_toolbar);
                    setTimeout(function(){
                        $(bottom_toolbar).removeClass('hidden');
                    }, 200)
                }
                // if (map_supply_toolbar) {
                //     map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(map_supply_toolbar);
                //     setTimeout(function(){
                //         $(map_supply_toolbar).removeClass('hidden');
                //     }, 200)
                // }
            }
        }        
    });

    var categories = this.categories;
    setTimeout(function(){
        $('#category-toolbar ul').html('');
        $.each(categories, function(index, val) {
            tpl = '<label><li class="list-group-item"><input type="checkbox" class="skop" name="'+val.key+'" value="'+val.id+'"> <img style="width:32px;" src="'+base_url+'img/maps/'+val.key+'.png" alt=""> '+val.label+' <span class="skop_total"></span></li></label>';
            $('#category-toolbar ul').append(tpl);
        });
    }, 200)
}