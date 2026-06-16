<div class="main-content" >

<link href="<?php echo base_url()?>css/modules/map/map.css" rel="stylesheet"> 

<div id="toolbar-supply-filter" class="map-toolbar hidden">
    <form action="" class="form-inline">
        <select class="" name="partner" id="supply_warehouse" style="width:150px; height: 34px;">
            <option value=""><?php echo lang("Pilih Mill") ?></option>
        </select>
        <input type="text" name="start" id="date-start" class="" style="width:100px; height: 34px;"  placeholder="<?php echo lang('Start') ?>" value="<?php echo date('Y-m-d', strtotime(date('Y-m-d').' -1 year')) ?>">
        <input type="text" name="end" id="date-end" class="" style="width:100px; height: 34px;"  placeholder="<?php echo lang('End') ?>" value="<?php echo date('Y-m-d') ?>">
        
        <button id="btn_search_supply" class="btn btn-success" type="button">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <span id="btn_search_text"><?php echo lang('Search') ?></span>
        </button>
    </form>	
</div>
<div id="map_canvas" style="width:100%; min-height: 450px;">
	
</div>   		
<script type="text/javascript">
	<?php foreach ($action as $key => $value): ?>
	var m_<?php echo $key ?> = "<?php echo $value ?>";
	<?php endforeach ?>

	var base_url        = "<?php echo base_url() ?>"

	$(function(){
		$('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
	})
</script>
<script src="<?php echo base_url() ?>js/infobox.js"></script>
<script src="<?php echo base_url() ?>js/modules/map/supplychain_header_new.js"></script>
<script src="<?php echo base_url() ?>js/modules/map/supplychain_new.js"></script>
<script type="text/javascript">
	// load googlemap
    loadScript("<?php echo base_url() ?>js/geospatial.js");
	loadScript("<?php echo base_url() ?>js/gmap3.js",init_map_supply);
	// load datepicker
	loadScript("<?php echo base_url() ?>js/plugins/bootstrap-datepicker.js", init_date);
</script>
</div>