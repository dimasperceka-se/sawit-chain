<div class="main-content" >
<style type="text/css">
    .cluster-1{
        background-image:url(<?php echo base_url() ?>img/maps/m1.png);
        line-height:53px;
        width: 53px;
        height: 52px;
    }
    .cluster-2{
        background-image:url(<?php echo base_url() ?>img/maps/m2.png);
        line-height:53px;
        width: 56px;
        height: 55px;
    }
    .cluster-3{
        background-image:url(<?php echo base_url() ?>img/maps/m3.png);
        line-height:66px;
        width: 66px;
        height: 65px;
    }
</style>

<link href="<?=base_url()?>css/modules/map/map.css" rel="stylesheet"> 
<link href="<?=base_url()?>css/tipso.min.css" rel="stylesheet"> 
<!-- <link href="<?=base_url()?>assets/css/tab-round.css" rel="stylesheet">  -->

<div id="top-toolbar" class="row map-toolbar hidden">
    <form action="" class="form-inline" onsubmit="return false;">
        <input type="text" class="form-control-bak" name="key" id="key" style="height: 34px; width:200px;" size="60" placeholder="<?php echo lang('Cari berdasar ID/nama') ?>">
        <select class="form-control-bak" name="province" id="province" style="height: 34px; width:150px;">
            <option value=""><?php echo lang("Pilih Propinsi") ?></option>
        </select>
        <select class="form-control-bak" name="district" id="district" style="height: 34px; width:130px;">
            <option value=""><?php echo lang("Semua Kabupaten") ?></option>
        </select>
        <button id="btn_search" class="btn btn-success" type="button">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <span id="btn_search_text"><?php echo lang('Search') ?></span>
        </button>
    </form>
</div>
<div id="bank-toolbar" class="row map-toolbar hidden" style="margin-right: 5px;">
    <form action="" class="form-inline" style="margin: 0;">
        <select name="bank" id="bank" style="height: 34px; width:100px;">
            <option value=""><?php echo lang("Pilih Bank") ?></option>
        </select>
        <select name="radius" id="radius" style="height: 34px; width:60px;">
            <option value="5">5 Km</option>
            <option value="10">10 Km</option>
            <option value="15">15 Km</option>
            <option value="25">25 Km</option>
        </select>
        <button id="btn_view_bank" class="btn btn-success" type="button">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <span>&nbsp;</span>
        </button>
    </form>
    <div id="bank_info" class="hidden">
        <ul style="list-style: none; margin-left:5px; margin-top: 5px; padding: 0;">
            <li style="font-size: 1.2em;"><span id="info_farmer_1"></span> Farmers Fit the Criteria</li>
            <li style="font-size: 1.2em;"><span id="info_farmer_2"></span> Certified Farmers Fit the Criteria</li>
            <li style="font-size: 1.2em;"><span id="info_farmer_3"></span> Farmers Don't Fit the Criteria</li>
        </ul>
    </div>
</div>
<!-- <div id="weather-toolbar" class="hidden">
    <ul class="list-group">
        <label><li class="list-group-item"><input type="checkbox" id="weather" class="" name="" value="weather"> <img style="width:32px;" src="http://openweathermap.org/img/w/02d.png" alt=""> <?php echo lang('Weather') ?></li></label>
    </ul>
</div> -->
<div id="bottom-toolbar" class="map-toolbar hidden">
    <button id="btn_full" class="btn btn-primary tipso" data-tipso="<?php echo lang('Toggle fullscreen') ?>">
        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
    </button>
</div>
<!-- <div id="map-supply-toolbar" class="map-toolbar hidden">
    <button id="btn_map_supply" class="btn btn-success tipso" data-tipso="<?php echo lang('Switch to supply chain map') ?>">
        <span class="glyphicon glyphicon-random" aria-hidden="true"></span>
    </button>
</div>
<div id="map-default-toolbar" class="map-toolbar hidden">
    <button id="btn_map_default" class="btn btn-success tipso" data-tipso="<?php echo lang('Switch to default map') ?>">
        <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
    </button>
</div> -->
<div id="category-toolbar" class="hidden">
	<ul class="list-group" style="margin-left: 20px;">
	</ul>	
</div>
<div id="toolbar-supply-filter" class="map-toolbar hidden">
    <form action="" class="form-inline">
        <select class="hidden" name="province" id="supply_province" style="width:150px; height: 34px;">
            <option value=""><?php echo lang("Pilih Propinsi") ?></option>
        </select>
        <select class="hidden" name="partner" id="supply_partner" style="width:150px; height: 34px;">
            <option value=""><?php echo lang("Pilih Partner") ?></option>
        </select>
        <select class="" name="partner" id="supply_warehouse" style="width:150px; height: 34px;">
            <option value=""><?php echo lang("Pilih Mill") ?></option>
        </select>
	    <select class="" name="partner" id="supply_certification" style="display: none;width:100px; height: 34px;">
            <option value=""><?php echo lang("All Transaction") ?></option>
            <option value="1"><?php echo lang("Certified") ?></option>
            <option value="0"><?php echo lang("Not Certified") ?></option>
        </select>
        <input type="text" name="start" id="date-start" class="" style="width:100px; height: 34px;"  placeholder="<?php echo lang('Start') ?>" value="<?php echo date('Y-m-d', strtotime(date('Y-m-d').' -1 year')) ?>">
        <input type="text" name="end" id="date-end" class="" style="width:100px; height: 34px;"  placeholder="<?php echo lang('End') ?>" value="<?php echo date('Y-m-d') ?>">
        
        <button id="btn_search_supply" class="btn btn-success" type="button">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <span id="btn_search_text"><?php echo lang('Search') ?></span>
        </button>
    </form>	
</div>
<ul class="nav nav-tabs" id="tabs">
    <li id="button_tab_default" role="presentation" class="active hidden" style="background-color: #f0f0f0;"><a href="#" style="padding: 8px; color: #449D44;" id="tab_default"><?php echo lang('Main') ?></a></li>
    <!-- <?php //if ($supplychain_access !== false): ?> -->
    <li id="button_tab_supply" role="presentation" class="hidden" style="background-color: #f0f0f0;"><a href="#" style="padding: 8px; color: #449D44;" id="tab_supply"><?php echo lang('Supply Chain') ?></a></li>
    <!-- <?php //endif ?> -->
    <!-- <?php //if ($bank_access): ?>       -->
    <li id="button_tab_bank" role="presentation" class="hidden" style="background-color: #f0f0f0;"><a href="#" style="padding: 8px; color: #449D44;" id="tab_bank"><?php echo lang('Bank') ?></a></li>
    <!-- <?php //endif ?> -->
</ul>
<div id="map_canvas" style="width:100%; min-height: 450px;">
	
</div>   		
<script type="text/javascript">
	<?php foreach ($action as $key => $value): ?>
    <?php if ($key == 'class') continue; ?>
	var <?php echo $key ?> = "<?php echo $value ?>";
	<?php endforeach ?>

	var base_url        = "<?php echo base_url() ?>"

	$(function(){
		$('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
$('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
$('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
	})
</script>
<script src="<?php echo base_url() ?>js/screenfull.min.js"></script>
<script src="<?php echo base_url() ?>js/tipso.min.js"></script>
<script src="<?php echo base_url() ?>js/infobox.js"></script>
<!--<script src="<?php echo base_url() ?>js/modules/map/defaultmap_new.js"></script>-->
<script src="<?php echo base_url() ?>js/modules/map/supplychain_detail_header.js"></script>
<!--<script src="<?php echo base_url() ?>js/modules/map/bankmap.js"></script>-->
<script src="<?php echo base_url() ?>js/modules/map/supplychain_detail.js"></script>
<script type="text/javascript">
	// load googlemap
    loadScript("<?php echo base_url() ?>js/geospatial.js");
	loadScript("<?php echo base_url() ?>js/gmap3.js",init_map);
	// load fullscreen
	loadScript("<?php echo base_url() ?>js/plugins/bootstrap-datepicker.js", init_date);
</script>
<script src="<?php echo base_url() ?>js/modules/map/ext.js"></script>
</div>