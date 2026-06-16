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
		<div class="span12 map-frame" id="map_area" style="margin:0 0 20px; float: left;"></div>
        <div id="info_area" style="width:248px;">
            <table>
                <tr>
                    <td><?php echo lang('ID Petani') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><span id="MemberID"></span></td>
                </tr>
                <tr>
                    <td><?php echo lang('Nama') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><span id="MemberName"></span></td>
                </tr>
                <tr>
                    <td><?php echo lang('Garden Nr') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><span id="PlotNr"></span></td>
                </tr>
                <!-- <tr>
                    <td><?php echo lang('Survey Nr') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><span id="SurveyNr"></span></td>
                </tr> -->
                <tr>
                    <td><?php echo lang('Hektar Survey') ?></td><td>&nbsp;:&nbsp;</td>
                    <td><span id="GardenAreaHa"></span> Ha</td>
                </tr>
                <tr>
                    <td><?php echo lang('Hektar Map') ?></td><td>&nbsp;:&nbsp;</td>
                    <td>
                        <span id="area_hectare"></span> Ha
                    </td>
                </tr>
                <tr id="tr_area">
                    <td><?php echo lang('Polygon') ?></td><td>&nbsp;:&nbsp;</td>
                    <td>
                        <label><input type="checkbox" name="polygon" id="show-polygon" checked="checked"> <?php echo lang('Show') ?></label>
                        <div>
                            <button id="btn-edit-polygon" class="green_btn"><?php echo lang('Edit') ?></button>
                            <button id="btn-save-polygon" style="display:none;"><?php echo lang('Save') ?></button>
                            <button id="btn-cancel" style="display:none;"><?php echo lang('Cancel') ?></button>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <script type='text/javascript'>
        var width   = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        var height  = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

        var api_farmer      = '<?php echo $api_farmer ?>';
        var api_polygon     = '<?php echo $api_polygon ?>';
        var api_area        = '<?php echo $api_area ?>';          
        var area_hectare = 0;
        </script>  
        <script src="<?php echo base_url() ?>js/modules/map/area.js"></script>
