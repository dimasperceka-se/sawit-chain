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

<div id="top-toolbar" class="row map-toolbar hidden">
    <form action="" class="form-inline" onsubmit="return false;">
        <input type="text" class="form-control-bak" name="key" id="key" style="height: 34px; width:200px;" size="60" placeholder="<?php echo lang('Cari berdasar ID/nama') ?>">
        <select class="form-control-bak" name="province" id="province" style="height: 34px; width:150px;">
            <option value=""><?php echo lang("Pilih Propinsi") ?></option>
        </select>
        <select class="form-control-bak" name="district" id="district" style="height: 34px; width:130px;">
            <option value=""><?php echo lang("Semua Kabupaten") ?></option>
        </select>
        <?php if ($_SESSION['groupid'] == 167): ?>
            <select class="form-control-bak" name="status_gps" id="status_gps" style="height: 34px; width:130px;">
                <option value="all"><?php echo lang("Semua") ?></option>
                <option value="new"><?php echo lang("New") ?></option>
                <option value="verified"><?php echo lang("Verified") ?></option>
                <option value="nullified"><?php echo lang("Nullified") ?></option>
            </select>
        <?php endif ?>
        <button id="btn_search" class="btn btn-success" type="button">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <span id="btn_search_text"><?php echo lang('Search') ?></span>
        </button>
    </form>
</div>
<div id="age-toolbar" class="row map-toolbar hidden" style="margin-right: 5px;">
    <form action="" class="form-inline" style="margin: 0;">
        <select name="potential" id="potential" style="height: 34px;">
            <option value=""><?php echo lang('All Plantation Age') ?></option>
            <option value="Seedling"><?php echo lang('Seedling (1-3)') ?></option>
            <option value="Young"><?php echo lang('Young (4-6)') ?></option>
            <option value="Prime"><?php echo lang('Prime (7-18)') ?></option>
            <option value="Old"><?php echo lang('Old ( > 19)') ?></option>
        </select>
    </form>
</div>
<div id="bottom-toolbar" class="map-toolbar hidden">
    <button id="btn_full" class="btn btn-primary tipso" data-tipso="<?php echo lang('Toggle fullscreen') ?>">
        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
    </button>
</div>
<div id="category-toolbar" class="hidden">
	<ul class="list-group" style="margin-left: 20px;">
	</ul>
</div>
<ul class="nav nav-tabs" id="tabs">
    <li id="button_tab_default" role="presentation" class="active hidden" style="background-color: #f0f0f0;"><a href="#" style="padding: 8px; color: #449D44;" id="tab_default"><?php echo lang('Main') ?></a></li>
</ul>
<div id="map_canvas" style="width:100%; min-height: 450px;">

</div>
<script type="text/javascript">
	<?php foreach ($action as $key => $value): ?>
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
<script src="<?php echo base_url() ?>js/modules/map/defaultmap.js"></script>
<script src="<?php echo base_url() ?>js/modules/map/map.js"></script>
<script type="text/javascript">
	// load googlemap
    loadScript("<?php echo base_url() ?>js/geospatial.js");
	loadScript("<?php echo base_url() ?>js/gmap3.js",init_map);
</script>
<script src="<?php echo base_url() ?>js/modules/map/ext.js"></script>
</div>