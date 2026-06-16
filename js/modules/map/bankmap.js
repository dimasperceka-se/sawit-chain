
var Bankmap = function() {
    this.categories = [
        {id: 1, key: 'bank_farmer_1', label: lang('Farmer who meets criteria')}, 
        {id: 2, key: 'bank_farmer_2', label: lang('Certified farmer who meets criteria')}, 
        {id: 3, key: 'bank_farmer_3', label: lang('Farmer who doesn’t meet criteria')}, 
        {id: 5, key: 'farmer_organization', label: lang('Organisasi Petani')}, 
        {id: 7, key: 'trader', label: lang('Pedagang')},
    ];
};

Bankmap.prototype.init = function(){
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
                if (category_toolbar) {
                    map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(category_toolbar);
                    setTimeout(function(){
                        $(category_toolbar).removeClass('hidden');
                    }, 200)
                }
                if (bank_toolbar) {
                    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(bank_toolbar);
                    setTimeout(function(){
                        $(bank_toolbar).removeClass('hidden');
                    }, 200)
                }
                if (bottom_toolbar) {
                    map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(bottom_toolbar);
                    setTimeout(function(){
                        $(bottom_toolbar).removeClass('hidden');
                    }, 200)
                }
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
        tpl = '<label><li class="list-group-item"><input type="checkbox" class="skop bank" id="check_bank" name="bank" value="bank"> <img style="width:32px;" src="'+base_url+'img/maps/bank.png" alt=""> '+lang('Bank')+' <span class="skop_total"></span></li></label>';
        $('#category-toolbar ul').append(tpl);
    }, 200);
}