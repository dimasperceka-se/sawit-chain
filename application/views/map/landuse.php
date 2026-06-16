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
		background-color: #95130b;
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
	    padding: 0 0 20px 20px;
	    border-top: 0;
	}
</style>
<link href="<?=base_url()?>assets/lib/datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet"> 
<link href="<?=base_url()?>css/modules/map/map.css" rel="stylesheet"> 

<script type="text/javascript">
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
<script src="<?php echo base_url() ?>js/modules/map/landuse.js"></script>
<script type="text/javascript">
	// load googlemap
    loadScript("<?php echo base_url() ?>js/geospatial.js");
	loadScript("<?php echo base_url() ?>js/gmap3.js",init_map);

	function extLoading() {
		Ext.MessageBox.show({
			msg: 'Please wait...',
			progressText: 'Generating...',
			width: 300,
			wait: true,
			waitConfig: {
				interval: 200
			},
			icon: 'ext-mb-info', //custom class in msg-box.html
			animateTarget: 'mb9'
		});

		setTimeout(function() {
			fetchObject();
		}, 500);
	}
</script>

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
		<div class="panel-body" style="overflow: auto; max-height: 875px;">
			<div class="panel-group">
				<form action="#">
					<div class="form-group">
						<label class="col-sm-4 control-label" style="margin: 10px auto 0;"><?php echo lang('Filter Berdasarkan') ?></label>
						<div class="col-sm-8">
							<div class="am-radio inline">
								<input type="radio" value="district" checked="" name="filterby" id="filterbydistrict">
								<label for="filterbydistrict"><?php echo lang('District') ?></label>
							</div>
							<div class="am-radio inline">
								<input type="radio" value="farmerid" name="filterby" id="filterbyfarmerid">
								<label for="filterbyfarmerid"><?php echo lang('Actor') ?></label>
							</div>
						</div>
					</div>
					<div class="col-md-12"></div>
					<div class="form-group filterby-district">
						<select class="form-control" id="filter-province" placeholder="Province" style="display: inline; width: 49%;">
							<option value=""><?php echo lang('All Province') ?></option>
	                    </select>
						<select class="form-control" id="filter-district" placeholder="District" style="display: inline; width: 49%;">
							<option value=""><?php echo lang('All District') ?></option>
	                    </select>
		                <div class="spacer text-right" style="margin-top: 5px">
	                      <button type="submit" onmousedown="extLoading();" class="btn btn-space btn-primary filter-search"><?php echo lang('Search') ?></button>
	                    </div>
	                </div>
	                <div class="input-group xs-mb-15 hidden filterby-farmerid">
	                	<input type="text" class="form-control" id="filter-key" placeholder="<?php echo lang('Search Actor ID') ?>">
						<span class="input-group-btn"><button type="button" onmousedown="extLoading();" class="btn btn-primary filter-search" id="filter-search">Search&nbsp;&nbsp;<img id="imgLoad" style="display:none;" src="images/dg/ajax-loader.svg" width="16" /></button></span>
	                </div>
				</form>
			</div>
			<hr/>
			<div id="accordion1" class="panel-group accordion">
				<?php if ($action['act_landuse'] == '1'): ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1" href="#collapseOne" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Landuse') ?></a></h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
						<div class="panel-body" style="padding-right: 0px;">							
							<div id="accordion1A" class="panel-group accordion" style="margin-bottom: 0px; padding-bottom: 0px;">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1A" href="#collapseOneA" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Restricted Area') ?></a></h4>
									</div>
									<div id="collapseOneA" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-restricted">

										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1A" href="#collapseOneB" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Safe Area') ?></a></h4>
									</div>
									<div id="collapseOneB" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-safe">
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1A" href="#collapseOneC" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Buffer Zone') ?></a></h4>
									</div>
									<div id="collapseOneC" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-buffer">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>					
				<?php endif ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1" href="#collapseTwo" class="collapsed" aria-expanded="false"><i class="icon s7-angle-down"></i> <?php echo lang('Administrative Boundary') ?></a></h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
						<div class="panel-body" id="panel-administrative">
							
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1" href="#collapseThree" class="collapsed" aria-expanded="false"><i class="icon s7-angle-down"></i> <?php echo lang('Actors') ?></a></h4>
					</div>
					<div id="collapseThree" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
						<div class="panel-body" id="panel-actors">
						</div>
					</div>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1" href="#collapseZero" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Additional Filters') ?></a></h4>
					</div>
					<div id="collapseZero" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
						<div class="panel-body" style="padding-right: 0px;">							
							<div id="accordion0A" class="panel-group accordion" style="margin-bottom: 0px; padding-bottom: 0px;">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion0A" href="#collapseZeroA" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Farm Age') ?></a></h4>
									</div>
									<div id="collapseZeroA" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-farm-age" style="padding-right: 0;">
											<div class="am-checkbox"><input class="check-age" id="check_age_1" type="checkbox" data-id="age_1" data-name="age_1"><label for="check_age_1"><?php echo lang('1-3 Years : Seedlings Phase'); ?></label></div>
											<div class="am-checkbox"><input class="check-age" id="check_age_4" type="checkbox" data-id="age_4" data-name="age_4"><label for="check_age_4"><?php echo lang('4-6 Years : Young Phase'); ?></label></div>
											<div class="am-checkbox"><input class="check-age" id="check_age_7" type="checkbox" data-id="age_7" data-name="age_7"><label for="check_age_7"><?php echo lang('7-18 Years : Prime Phase'); ?></label></div>
											<div class="am-checkbox"><input class="check-age" id="check_age_19" type="checkbox" data-id="age_19" data-name="age_19"><label for="check_age_19"><?php echo lang('> 19 Years : Old Phase'); ?></label></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion1" href="#collapseFour" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Additional Layers') ?></a></h4>
					</div>
					<div id="collapseFour" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
						<div class="panel-body" style="padding-right: 0px;">							
							<div id="accordion2A" class="panel-group accordion" style="margin-bottom: 0px; padding-bottom: 0px;">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion2A" href="#collapseFourCovid" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Covid 19 Risk') ?></a></h4>
									</div>
									<div id="collapseFourCovid" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-covid_risk">

										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion2A" href="#collapseFourA" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Land Cover Overlay') ?></a></h4>
									</div>
									<div id="collapseFourA" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-land_cover">

										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion2A" href="#collapseFourB" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Animal Habitat') ?></a></h4>
									</div>
									<div id="collapseFourB" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-animal_habitat">
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion2A" href="#collapseFourC" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Mills distance') ?></a></h4>
									</div>
									<div id="collapseFourC" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-mill_distance">
											<div class="am-radio"><input type="radio" name="mill_distance" class="mill_distance" id="mill_dist_0" data-distance="0"><label for="mill_dist_0"><span class="landuse-color" style="color:#f46d43; background-color: #f46d43;">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php echo lang('0 Km Range') ?></label></div>
											<div class="am-radio"><input type="radio" name="mill_distance" class="mill_distance" id="mill_dist_10" data-distance="10"><label for="mill_dist_10"><span class="landuse-color" style="color:#f46d43; background-color: #f46d43;">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php echo lang('10 Km Range') ?></label></div>
											<div class="am-radio"><input type="radio" name="mill_distance" class="mill_distance" id="mill_dist_30" data-distance="30"><label for="mill_dist_30"><span class="landuse-color" style="color:#f46d43; background-color: #f46d43;">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php echo lang('30 Km Range') ?></label></div>
											<div class="am-radio"><input type="radio" name="mill_distance" class="mill_distance" id="mill_dist_50" data-distance="50"><label for="mill_dist_50"><span class="landuse-color" style="color:#f46d43; background-color: #f46d43;">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php echo lang('50 Km Range') ?></label></div>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion2A" href="#collapseFireHotspot" aria-expanded="false" class="collapsed"><i class="icon s7-angle-down"></i> <?php echo lang('Fire Hotspot') ?></a></h4>
									</div>
									<div id="collapseFireHotspot" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
										<div class="panel-body" id="panel-hotspot">
											<div class="col-sm-12">
												<select class="form-control" name="hotspot_satellite" id="hotspot_satellite" placeholder="Satellite">
													<option value=""><?php echo lang('All Satellite') ?></option>
													<option value="Aqua"><?php echo lang('Aqua (MODIS) ') ?></option>
													<option value="Terra"><?php echo lang('Terra (MODIS)') ?></option>
													<option value="1"><?php echo lang('NOAA (VIIRS) ') ?></option>
													<option value="N"><?php echo lang('S-NPP (VIIRS) ') ?></option>
							                    </select>
							                </div>
											<div class="col-sm-6">
												<div class="am-radio"><input type="radio" name="hotspot_timeline" class="hotspot_timeline" id="hotspot_latest" data-timeline="latest" value="latest"><label for="hotspot_latest"> <?php echo lang('Latest') ?></label></div>
											</div>
											<div class="col-sm-6">
												<div class="am-radio"><input type="radio" name="hotspot_timeline" class="hotspot_timeline" id="hotspot_24h" data-timeline="24h" value="24h"><label for="hotspot_24h"> <?php echo lang('24 Hours') ?></label></div>
											</div>
											<div class="col-sm-6">
												<div class="am-radio"><input type="radio" name="hotspot_timeline" class="hotspot_timeline" id="hotspot_48h" data-timeline="48h" value="48h"><label for="hotspot_48h"> <?php echo lang('48 Hours') ?></label></div>
											</div>
											<div class="col-sm-6">
												<div class="am-radio"><input type="radio" name="hotspot_timeline" class="hotspot_timeline" id="hotspot_72h" data-timeline="72h" value="72h"><label for="hotspot_72h"> <?php echo lang('72 Hours') ?></label></div>
											</div>
											<div class="col-sm-9">
												<div data-min-view="2" data-date-format="yyyy-mm-dd" class="input-group date datetimepicker">
													<input size="16" type="text" name="hotspot_date" value="" class="form-control"><span class="input-group-addon btn btn-primary"><i class="icon-th s7-date"></i></span>
												</div>
											</div>
											<div class="col-sm-3" style="padding: 6px;">
												<button class="btn btn-primary pull-right" id="button-hotspot-view">View</button>
											</div>
											<div class="col-sm-12" id="panel-hotspot-trust">
												<hr/>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="map_canvas" style="width:100%; min-height: 600px;"></div>   		

</div>