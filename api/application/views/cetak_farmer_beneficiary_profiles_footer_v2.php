<?php
/**
 * @Author: nikolius
 * @Date:   2016-06-03 17:11:40
 */
?>
<script type="text/javascript">
function initMap(){
	<?php
	for ($i=0; $i < count($paramFooterGoogleApi); $i++) {
		if($paramFooterGoogleApi[$i]['gardens_coordinate_exists'] == 1){
			echo 'initMap'.$paramFooterGoogleApi[$i]['MemberID'].'();';
		}
	}

	if ((int) $checkAreaPolygon['count'] > 0) {
		echo 'initMapPolygon'.$checkAreaPolygon['MemberID'].'();';
	}
	?>
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCoaZKDW1hV9HLw9hcO_rLmiQx0Z5P2M3g&callback=initMap"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/libs/gmap3.js"></script>
<!-- <script type="text/javascript" src="<?php //echo base_url(); ?>assets/js/libs/markerwithlabel.js"></script> -->
<script type="text/javascript">
   $(document).ready(function(){
      $(".tabelListData tr:odd").addClass('odd');
      $(".tabelListData tr:even").addClass('even');

      $(".tabelListDataSide tr:odd").addClass('even');
      $(".tabelListDataSide tr:even").addClass('odd');
   });
</script>
</body>
</html>