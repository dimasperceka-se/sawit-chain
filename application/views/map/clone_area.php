<?php
if($data['CertificationStatus'] == "No"){
    $showHideTr = 'style="display:none;"';
}else{
    $showHideTr = '';
}
?>
        <style type="text/css">
        #info_area {
            padding: 5px 10px;
            float: left;
        }
        #info_area td {
            line-height: 25px;
            vertical-align: top;
        }
        </style>
		<div class="span12 map-frame" id="map_clonal_area" style="margin:0 0 20px; float: left;"></div>
        <div id="info_area" style="width:280px;">
            <table>
                <tr>
                    <td><?php echo lang('Owner Type') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo lang($data['ObjType']) ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Owner Name') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['name'] ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Garden Nr') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['GardenNr'] ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Area') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['Area'] ?> (Ha)</td>
                </tr>
                <tr>
                    <td><?php echo lang('Year Established') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['EstablishedYear'] ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Certification Provider') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['CertificationStatus'] ?></td>
                </tr>
                <tr <?php echo $showHideTr;?>>
                    <td><?php echo lang('Date Applied for Certification') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['DateAppliedCertification'] ?></td>
                </tr>
                <tr <?php echo $showHideTr;?>>
                    <td><?php echo lang('Date Received Certification') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['DateReceivedCertification'] ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Land Ownership Certificate') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['LandCertificate'] ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Cocoa Clone Total') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['TotalClonesNr'] ?></td>
                </tr>
                <tr>
                    <td><?php echo lang('Total of Shade Trees') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><?php echo $data['TotalShadeTreesNr'] ?></td>
                </tr>
            </table>
        </div>

        <script type='text/javascript'>
        var width   = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        var height  = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

        var clonal_area = $.parseJSON('<?php echo $area ?>');
        var area_hectare = 0;
        </script>
        <script src="<?php echo base_url() ?>js/modules/map/clonal_area.js"></script>
