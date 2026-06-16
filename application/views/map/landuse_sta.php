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
	    padding: 0 30px 20px 20px;
	    border-top: 0;
	}
</style>
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
<script src="<?php echo base_url() ?>js/modules/map/landuse_sta.js"></script>
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
						<!-- <div class="panel-body" id="panel-actors"> -->
						<!-- </div> -->
                        <div class="panel panel-default" style="padding-left:20px">
                            <div class="panel-heading">
                                <h4 class="panel-title"><a data-toggle="collapse" data-parent="#collapseThree" href="#collapseMill" class="collapsed" aria-expanded="false"><i class="icon s7-angle-down"></i> <?php echo lang('Mill') ?></a></h4>
                            </div>
                            <div id="collapseMill" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                <div class="panel-body" id="panel-mill">
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" style="padding-left:20px">
                            <div class="panel-heading">
                                <h4 class="panel-title"><a data-toggle="collapse" data-parent="#collapseThree" href="#collapseSME" class="collapsed" aria-expanded="false"><i class="icon s7-angle-down"></i> <?php echo lang('SME') ?></a></h4>
                            </div>
                            <div id="collapseSME" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                <div class="panel-body" id="panel-sme">
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default" style="padding-left:20px">
                            <div class="panel-heading">
                                <h4 class="panel-title"><a data-toggle="collapse" data-parent="#collapseThree" href="#collapseFarmer" class="collapsed" aria-expanded="false"><i class="icon s7-angle-down"></i> <?php echo lang('Farmers') ?></a></h4>
                            </div>
                            <div id="collapseFarmer" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                <div class="panel-body" id="panel-farmer">
                                </div>
                            </div>
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