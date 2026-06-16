<?php
/******************************************
 *  Author 		: sofyan.salim@Koltiva.com
 *  Created On 	: Mon Feb 27 2022
 *  File 		: all_actors_new.php
 *******************************************/

?>
<!-- <link href="<?=base_url()?>css/modules/map/map.css" rel="stylesheet"> -->
<link href="<?=base_url()?>css/modules/map/map_new.css" rel="stylesheet">
<div class="main-content">

	<div id="main-panel">
		<div id="main-panel-max">
			<!-- Header -->
			<div id="main-panel-header">
				<div>Filter</div>
				<span id="panel-filter-control" class="icon s7-angle-left-circle"></span>
			</div>
		
			<!-- Filter -->
			<div id="main-panel-Filter">
				<div class="box-filter">
					<form>
						<div>
							<label><?php echo lang('Province') ?></label>
							<select class="select-box" id="filter-province" >
								<option value=""><?php echo lang('Choose'). ' ' . lang('Province')  ?></option>
							</select>
						</div>

						<div>
							<label><?php echo lang('District') ?></label>
							<select class="select-box" id="filter-district" >
								<option value=""><?php echo lang('All').' '.lang('District')?></option>
							</select>
						</div>
						
						<?php if($_SESSION['PartnerAsParent'] == "No") { //not Partner as parent?>
							<div style="display: none;">
								<label><?php echo lang('Partner') ?></label>
								<select class="select-box" id="filter-partner">
									<option value="<?php echo $_SESSION['PartnerID']?>">Partnernya</option>
								</select>
							</div>
						<?php } else { 									 //Partner as parent?>
							<div>
								<label><?php echo lang('Partner') ?></label>
								<select class="select-box" id="filter-partner"></select>
							</div>
						<?php }?>

						<div style="width:100%;padding:0px">
							<label><?php echo lang('Search') ?></label>
							<div class="box-filter-search">
								<input id="filter-key" type="text">
								<button id='btn-filter' onmousedown="extLoadingSearch()"><?php echo lang('Search') ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>


			<!-- Legend & Analisis -->
			<div id="main-panel-legend">
				<div id="div-button-panel" style="display:none">
					<button id='btn-legend' onclick="showPanelInfo('legend')" class="select"><?php echo lang('Legend')?></button> 
					<button id='btn-analisis' onclick="showPanelInfo('analisis')" ><?php echo lang('Analysis')?></button>
				</div>

				<div id="panel-legend"></div>

				<div id="panel-analisis" style="display:none">
			
				</div>

				<div id="addlayers" class="add-layers" style="display:none">
					<div style="font-size:26px; font-weight:bold;width:100%; text-align:center;color:white;user-select: none;">+</div>
				</div>
			</div>
		</div>
		<div id="main-panel-min" style="display:none;font-weight: bold; font-size: 16px;line-height: 19px;cursor:pointer;position:absolute; top:8px; left:8px">
			<span id="panel-filter-control-min" class="icon s7-angle-right-circle"></span>
		</div>
	</div>


	<!-- container map -->
	<div id="map_canvas" style="width:100%; height:100%;"></div>
	
	<div id="layer-control-panel" class="modal">
		<div id="lcp-content" class="modal-content">
            <div id="lcp-header"class="modal-header">
                <span class="close">&times;</span>
                <h2><?php echo lang('Layer Control Panel')?></h2>
            </div>
            <div id="lcp-body" class="modal-body">

			
			</div>
        </div>
	</div>


	<!-- Title -->
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
	
	<script type="text/javascript">
		// load googlemap
		loadScript("<?php echo base_url() ?>js/geospatial.js");
		loadScript("<?php echo base_url() ?>js/gmap3.js");

		function extLoadingSearch() {
			Ext.MessageBox.show({
				msg: lang('Please wait'),
				progressText: lang('Generating'),
				width: 300,
				wait: true,
				waitConfig: {
					interval: 200
				},
				icon: 'ext-mb-info', //custom class in msg-box.html
				animateTarget: 'mb9'
			});

			setTimeout(function() {
				$("#btn-filter").click();
			}, 500);
		}

		// Legend or Analisis
			var isLegend = true
			function showPanelInfo(info) {
				if(info=="legend"){
					if(isLegend == false){
						$('#btn-legend').addClass("select")
						$('#btn-analisis').removeClass("select")
						
						$('#panel-legend').show();
						$('#panel-analisis').hide();
						isLegend = true
					}
				}else{
					if(isLegend == true){
						$('#btn-legend').removeClass("select")
						$('#btn-analisis').addClass("select")
						
						$('#panel-legend').hide();
						$('#panel-analisis').show();
						isLegend = false
					}
				}    
			} 

			// Modal (Layer Control Panel)
			// Get the modal
				var modal = document.getElementById("layer-control-panel");

			// Get the button that opens the modal
				var btn = document.getElementById("addlayers");

			// Get the <span> element that closes the modal
				var span = document.getElementsByClassName("close")[0];

			// When the user clicks on the button, open the modal
				btn.onclick = function () {
					modal.style.display = "block";
				};

			//  When the user clicks on <span> (x), close the modal
				span.onclick = function () {
					modal.style.display = "none";
				};

			// When the user clicks anywhere outside of the modal, close it
				window.onclick = function (event) {
					if (event.target == modal) {
						modal.style.display = "none";
					}
				};
	</script>
	<script src="<?php echo base_url() ?>js/infobox.js"></script>
	<script src="<?php echo base_url() ?>js/modules/maps/new_map_global_variable.js"></script>
	<script src="<?php echo base_url() ?>js/modules/maps/new_map_main.js"></script>
	<script src="<?php echo base_url() ?>js/modules/maps/new_map_legend.js"></script>
	<script src="<?php echo base_url() ?>js/modules/maps/new_map_events.js"></script>

</div>