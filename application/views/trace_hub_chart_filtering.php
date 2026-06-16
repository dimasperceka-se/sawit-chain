 
    <div id='row-fluid'>
        <div class="page-head xs-pt-10 xs-pb-10">
            <div class="row"> 
				     <div class="btn-group btn-hspace pull-right">
                        <button class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setFilter()"><?php echo lang('Cari') ?></button>
                    </div>
					
					<div class="btn-group btn-hspace pull-right">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentBS"><?php echo lang('All Buying Unit') ?></span>&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu" id="bsList">
                        </ul>
                    </div>
					
					<div class="btn-group btn-hspace pull-right">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentCH"><?php echo lang('All Processing') ?></span>&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu"  role="menu" id="chList">
                        </ul>
                    </div>
					
					<div class="btn-group btn-hspace pull-right">
                       <input type="text" id="datepicker1" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $awal?>">
                        &nbsp;&nbsp;<?php echo lang('sampai') ?>&nbsp;&nbsp;
                        <input type="text" id="datepicker2" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $akhir ?>">&nbsp;&nbsp;
                    </div>
					
			</div>
	  </div>
	</div>
</div>
<script language="javascript" type="text/javascript" src="<?php echo base_url()?>js/plugins/bootstrap-datepicker.js"></script>
<script>  
		var checkin = $('#datepicker1').datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function(ev) {
                if (ev.date.valueOf() > checkout.date.valueOf()) {
                    var newDate = new Date(ev.date)
                    newDate.setDate(newDate.getDate() + 1);
                    checkout.setValue(newDate);
                }
                // checkin.hide();
                // $('#enddate')[0].focus();
            }).data('datepicker');
            var checkout = $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd',
                onRender: function(date) {
                    return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
                }
            }).on('changeDate', function(ev) {
                // checkout.hide();
            }).data('datepicker');
		
		
		
          
					
			$('#chList li').remove();   
			$.get(m_api+'/dashboard/store_supplyorg/', function(data) { 
				var li = '<li><a href="#" class="list_ch" data-id="" data-name="'+lang('All Processing')+'">'+lang('All Processing')+'</a></li>';
			    $('#chList').append(li);
				if (data) {
					$.each(data, function(index, val) {
						var li = '<li><a href="#" class="list_ch" data-id="'+val.id+'" data-name="'+val.label+'">'+val.label+'</a></li>';
						$('#chList').append(li);
					});
					if (m_ch) { 
						$('.list_ch[data-id="'+m_ch+'"]').click();
					} 
				}
			});
						
		 $('#chList').on('click', '.list_ch', function(event) {	
			event.preventDefault();	
			patnerID = $(this).data('id');
			
			$('#currentCH').text($(this).data('name'));
            m_ch = $(this).data('id');
			
			$('#bsList li').remove();
			var li = '<li><a href="#" class="list_bs" data-id="" data-name="'+lang('All Buying Unit')+'">'+lang('All Buying Unit')+'</a></li>';
			$('#bsList').append(li);   
			 
			$.get(m_api+'/dashboard/store_supplyorgChild/?patnerID=' + patnerID, function(data) {
				if (data) {
					$.each(data, function(index, val) {
						var li = '<li><a href="#" class="list_bs" data-id="'+val.id+'" data-name="'+val.label+'">'+val.label+'</a></li>';
						$('#bsList').append(li);
					});
					// set previously selected
					if (m_bs) {
						$('.list_bs[data-id="'+m_bs+'"]').click();
					}
				}
			});

			$('#bsList').on('click', '.list_bs', function(event) {
				event.preventDefault();
				$('#currentBS').text($(this).data('name'));
				m_bs = $(this).data('id');
			}); 
 
			
	});
</script>