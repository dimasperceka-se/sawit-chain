<div class="main-content" >
<link href="<?=base_url()?>css/modules/bank/map.css" rel="stylesheet"> 
<link href="<?=base_url()?>css/tipso.min.css" rel="stylesheet"> 

<div id="filter-toolbar" class="row map-toolbar hidden">
    <form action="" class="form-inline">
        <select name="province" id="filter-province" style="height: 34px; width:200px;">
            <option value=""><?php echo lang("Pilih Propinsi") ?></option>
        </select>
        <select name="district" id="filter-district" style="height: 34px; width:150px;">
            <option value=""><?php echo lang("Semua Kabupaten") ?></option>
        </select>
        <!-- <select name="district" id="filter-subdistrict" style="height: 34px; width:150px;">
            <option value=""><?php echo lang("Semua Kecamatan") ?></option>
        </select> -->
        <select name="district" id="filter-bank" style="height: 34px; width:200px;">
            <option value=""><?php echo lang("Semua Bank") ?></option>
        </select>
        <select name="district" id="filter-radius" style="height: 34px; width:80px; display: none;">
            <option value="5000">5 Km</option>
            <option value="10000">10 Km</option>
            <option value="15000">15 Km</option>
            <option value="20000">20 Km</option>
            <option value="25000">25 Km</option>
        </select>
        <button id="btn_search" class="btn btn-success" type="button">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <span id="btn_search_text"><?php echo lang('Search') ?></span>
        </button>
    </form>
</div>
<div id="category-toolbar" class="hidden">
    <ul class="list-group" style="margin-left: 20px;">
    </ul>   
</div>
<div id="fullscreen-toolbar" class="map-toolbar hidden">
    <button id="btn_full" class="btn btn-primary tipso" data-tipso="<?php echo lang('Toggle fullscreen') ?>">
        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
    </button>
</div>
<div id="map_canvas" style="width:100%; min-height: 450px;">
	
</div>   		
<script type="text/javascript">
	<?php foreach ($action as $key => $value): ?>
	var <?php echo $key ?> = "<?php echo $value ?>";
	<?php endforeach ?>

	var base_url        = "<?php echo base_url() ?>"

	$(function(){
		$('#titlet').html('<?php echo $title ?>');
	})
</script>
<script src="<?php echo base_url() ?>js/screenfull.min.js"></script>
<script src="<?php echo base_url() ?>js/tipso.min.js"></script>
<script src="<?php echo base_url() ?>js/infobox.js"></script>
<script src="<?php echo base_url() ?>js/modules/bank/map_functions.js"></script>
<script src="<?php echo base_url() ?>js/modules/bank/map.js"></script>
<script type="text/javascript">
	// load googlemap
	loadScript("<?php echo base_url() ?>js/gmap3.js",init_map);
</script>
</div>