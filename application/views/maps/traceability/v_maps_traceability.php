<div class="main-content" >
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<style type="text/css">
	.panel-group.accordion .panel .panel-heading a {
		padding: 5px 0px;
	}
	.panel-group.accordion .panel .panel-collapse .panel-body {
		padding-bottom: 10px;
	}
	#panel-filter{
		border-radius: 6px;
	}
	#sidebar-filter {
    	border-radius: 6px;
	}
	#sidebar-button {
    	border-radius: 6px;
		background-color: #2bbe72;
		color: white;
	}
	#filter-heading {
		margin: 0;
		padding-left: 20px;
		padding-right: 20px;
		background-color: #95130b;
		color: white;
    	border-radius: 6px 6px 0px 0;
	}
	#sidebar-filter .form-control {
    	border-radius: 4px;
	}
	#sidebar-filter .panel-title a{
		font-family: 'Roboto', sans-serif;
	    font-weight: bold;
	    font-size: 18px;
	    color: #9b9b9b;
	}
	#filter-search {
		border-radius: 0 4px 4px 0;
	}
	.landuse-color {
		border-radius: 10px;
	}
	.am-checkbox:hover {
		background-color: #d6ecb7;
	}
	.am-checkbox, .am-radio {
		padding-left: 10px;
	}
	.panel-group.accordion .panel .panel-collapse .panel-body {
	    padding: 0 30px 20px 20px;
	    border-top: 0;
	}
	.info_blue_light {
	    background: rgba(99, 185, 204,1);
	}

	.info_blue_light .arrow {
	    border-right-color: rgba(99, 185, 204, 0.9);
	}

	#trans-table > tbody > tr > td {
		color: #000000;
	}

	div.dataTables_filter > label > input {
		color: #000000;
		margin-top:10px;
	}
</style>
<link href="<?=base_url()?>css/modules/map/maps_traceability.css" rel="stylesheet">
<link href="<?=base_url()?>js/plugins/datatables/css/jquery.dataTables_themeroller.css" rel="stylesheet">

<div class="panel panel-default hidden" id="panel-filter" style="margin-left: 10px;">
	<div id="sidebar-button" style="display: none">
		<div class="panel-heading">
			<div class="tools" id="sidebar-expand"><span class="icon s7-edit"></span></div>
			<!-- <span class="title">Filter</span> -->
		</div>
		<div class="panel-body">
		</div>
	</div>
	<div id="sidebar-filter" style="width: 380px;">
		<div class="panel-heading" id="filter-heading">
			<div class="tools" id="sidebar-collapse"><span class="icon s7-edit"></span></div>
			<span class="title">Filter</span>
		</div>
		<div class="panel-body">
			<div class="panel-group">
				<form>
					<div class="form-group">
						<label style="margin-bottom:5px; font-size:11px"for="filterWarehouse" class="form-label"><?php echo lang('Mill') ?></label>
						<select class="form-control" id="filterWarehouse" placeholder="All" style="display: inline; width: 98%; height:30px; padding:0 10px 0 10px;  font-size:11px">
							<option><?php echo lang('All') ?></option>
	                    </select>
	                </div>
	                <!-- <div class="form-group">
						<label style="margin-bottom:5px; font-size:11px "for="filterTier2" class="form-label"><?php echo lang('DO') ?></label>
						<select class="form-control" id="filterTier2" placeholder="All" style="display: inline; width: 98%; height:30px; padding:0 10px 0 10px;  font-size:11px">
							<option><?php echo lang('All') ?></option>
	                    </select>
	                </div>
	                <div class="form-group">
						<label style="margin-bottom:5px; font-size:11px "for="filterTier1" class="form-label"><?php echo lang('Agent/Dealer') ?></label>
						<select class="form-control" id="filterTier1" placeholder="All" style="display: inline; width: 98%; height:30px; padding:0 10px 0 10px;  font-size:11px">
							<option><?php echo lang('All') ?></option>
	                    </select>
	                </div> -->
	                <!--<div class="form-group">
						<div class="am-checkbox">
							<input class="check-all" id="filter-check-all" type="checkbox" data-id="check_all" data-name="check_all"> <label for="check_all"><?php echo lang('Show farmers that are not selling'); ?></label>
						</div>
	                </div>-->
	                <div class="row">
	                	<div class="col-md-6">
	                		<label style="margin-bottom:5px; font-size:11px "for="startDate" class="form-label"><?php echo lang('From') ?></label>
	                		<input type="text" name="date" id="startDate" class="form-control" style=" height:30px; padding:0 10px 0 10px;  font-size:11px" placeholder="<?php echo lang('') ?>" value="<?php echo date('Y-m-d', strtotime('-1 year')); ?>">
	                	</div>
	                	<div class="col-md-6">
	                		<label style="margin-bottom:5px; font-size:11px "for="endDate" class="form-label"><?php echo lang('To') ?></label>
	                		<input type="text" name="date" id="endDate" class="form-control" style=" height:30px; padding:0 10px 0 10px;  font-size:11px" placeholder="<?php echo lang('') ?>" value="<?php echo date('Y-m-d') ?>">
	                	</div>
	                </div>
	                <div class="input-group xs-mb-15" style="margin-top:20px;">
	                	<input type="text" class="form-control" id="filter-key" style=" height:30px; padding:0 10px 0 10px;  font-size:11px" placeholder="<?php echo lang('Search by Name / ID') ?>">
	                	<span class="input-group-btn"><button type="button" onmousedown="extLoading();" class="btn btn-primary" style=" height:30px; padding:0 10px 0 10px;  font-size:11px" id="filter-search">Search&nbsp;&nbsp;<img id="imgLoad" style="display:none;" src="images/dg/ajax-loader.svg" width="16" /></button></span>
	                </div>
				</form>
			</div>
			
			<p><b>Map Summary</b></p><br>
			<ul class="list-group list-group-flush" style="margin-top:-20px;">
				<li class="list-group-item" style="border-width: 0 0 0px; padding:0px"><img style="width:24px;" src="<?php echo base_url("img/maps/warehouse_small.png"); ?>" alt=""> Mill <span class="pull-right" id="total-Warehouse">(0)</span></li>
				<li class="list-group-item" style="border-width: 0 0 0px; padding:0px"><img style="width:24px;" src="<?php echo base_url("img/maps/tier2_small.png"); ?>" alt="">DO <span class="pull-right" id="total-Tier2Supplier">(0)</span></li>
				<li class="list-group-item" style="border-width: 0 0 0px; padding:0px"><img style="width:24px;" src="<?php echo base_url("img/maps/tier1_small.png"); ?>" alt="">Agent/Dealer <span class="pull-right" id="total-Tier1Supplier">(0)</span></li>
				<li class="list-group-item" style="border-width: 0 0 0px; padding:0px"><img style="width:24px;" src="<?php echo base_url("img/maps/farmer_green_small.png"); ?>" alt=""> Farmer With Transactions <span class="pull-right" id="total-FarmerWithTransactions">(0)</span></li>
			</ul>
		</div>
	</div>
</div>

<div id="map_canvas" style="width:100%; min-height: 600px;"></div>

<script type="text/javascript">
    var base_url = "<?php echo base_url() ?>"

	<?php if (!empty($action)): ?>
		<?php foreach ($action as $key => $value): ?>
		var m_<?php echo $key ?> = "<?php echo $value ?>";
		<?php endforeach ?>
	<?php endif ?>

	$(function(){
		$('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
	})
</script>

<script src="<?php echo base_url() ?>js/infobox.js"></script>


<!-- JS baru sofyan -->
<script src="<?php echo base_url() ?>js/modules/maps/traceability/class_traceablity_data.js"></script>
<script src="<?php echo base_url() ?>js/modules/maps/traceability/maps_traceability_new.js"></script>

<script type="text/javascript">
	// load googlemap
    loadScript("<?php echo base_url() ?>js/geospatial.js");
	loadScript("<?php echo base_url() ?>js/gmap3.js",init_map);
	loadScript("<?php echo base_url() ?>js/plugins/bootstrap-datepicker.js", init_date);
	loadScript("<?php echo base_url() ?>js/plugins/datatables/js/jquery.dataTables.min.js");

	function extLoading() {
		Ext.MessageBox.show({
			msg: 'Please wait...',
			progressText: 'Generating...',
			width: 300,
			wait: true,
			waitConfig: {
				interval: 200
			},
			icon: 'ext-mb-info',
			animateTarget: 'mb9'
		});

		setTimeout(function() {
			$("#filter-search").click();
		}, 500);
	}
</script>
</div>