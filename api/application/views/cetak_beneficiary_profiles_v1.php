<?php 
$now        = new Datetime('now');
$birthdate  = new Datetime($farmer['Birthdate']);
$age        = $now->diff($birthdate);
// $productivity = $garden_size['Production']/$garden_size['GardenHaUnCertified'];
// if ($productivity < 500) {
//     $professionalism = 'Unprofessional';
// } elseif ($productivity >= 500 && $productivity <= 1000) {
//     $professionalism = 'Progressing';
// } elseif ($productivity > 1000) {
//     $professionalism = 'Professional';
// }
?>
<page size="A4">
<div id="templatemo_container_wrapper">
    <div id="templatemo_container">
        <div id="templatemo_header" class="logos" style="/*height:100px;*/">
			<table width="100%" border="0" cellpadding="2" style="margin-top:15px;">
				<tr>
					<td align="center" style="vertical-align:middle;text-decoration:underline;">Beneficiary Profiles</td>
                </tr>
            </table>
        </div>
        <div id="templatemo_left_column">
            <div class="text_area" align="center">
                <div class="" align="center">
                    <div class="text_area">
                        <table style="width:100%;" cellspacing="0" class="table_bordered">
                            <tr>
                                <td style="text-align: center; width: 25%"><img height="195px" src="<?php echo base_url().'images/Photo/'.$farmer['Photo'] ?>"></td>
                                <td colspan="3" style="background-color:#eee">
                                    <div style="position:relative; width:100%; height: 200px;">
                                        <div id="map_canvas_<?php echo $farmer['FarmerID'] ?>" style="width: 100%; height: 200px; position: absolute !important;"></div>
                                        <!-- <div style="position: relative; float: left; width:45%; height: 100%; padding: 10px;">
                                            <table cellspacing="0" class="table_noborder table_map" >
                                                <tr>
                                                    <td class="no_border" style="width: 100px;">Name:</td>
                                                    <td class="no_border"><?php echo $farmer['FarmerName'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">Professionalism:</td>
                                                    <td class="no_border"><?php echo $professionalism ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">Land Size:</td>
                                                    <td class="no_border"><?php echo $garden_size['LandSize'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">Birth Date:</td>
                                                    <td class="no_border"><?php echo date('d-m-Y', strtotime($farmer['Birthdate'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">Age:</td>
                                                    <td class="no_border"><?php echo $age->y ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">GPS:</td>
                                                    <td class="no_border">
                                                        <?php if (abs($garden['Longitude'])>0 && abs($garden['Latitude'])>0): ?>
                                                            <?php echo $garden['Longitude'] ?> Long <br/><?php echo $garden['Latitude'] ?> Lat
                                                        <?php else: ?>
                                                            No GPS Data
                                                        <?php endif ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div> 
                                        <div style="position: relative; float: right; width:40%; height: 100%; padding: 10px;">
                                            <table cellspacing="0" class="table_noborder table_map">
                                                <tr>
                                                    <td class="no_border" style="width: 100px;">Province:</td>
                                                    <td class="no_border"><?php echo $farmer['Provinsi'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">District:</td>
                                                    <td class="no_border"><?php echo $farmer['Kabupaten'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">Sub District:</td>
                                                    <td class="no_border"><?php echo $farmer['Kecamatan'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">Village:</td>
                                                    <td class="no_border"><?php echo $farmer['Desa'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="no_border">Distance to Bank:</td>
                                                    <td class="no_border"><span id="bank_distance"><?php echo number_format($bank['distance'],0,',','.') ?></span> km</td>
                                                </tr>
                                            </table>
                                        </div>
                                        -->
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <br/>
                        <h3 style="text-align: left;"><?php echo lang('Basic Data') ?></h3>
                        <table style="width:100%;" cellspacing="0" class="table_noborder">
                            <tr>
                                <td style="width: 25%"><?php echo lang('Farmer ID') ?></td>
                                <td style="width: 25%">: <?php echo $farmer['FarmerID'] ?></td>
                                <td style="width: 25%"><?php echo lang('District') ?></td>
                                <td style="width: 25%">: <?php echo $farmer['Kabupaten'] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Farmer Name') ?></td>
                                <td>: <?php echo $farmer['FarmerName'] ?></td>
                                <td><?php echo lang('Sub District') ?></td>
                                <td>: <?php echo $farmer['Kecamatan'] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Province') ?></td>
                                <td>: <?php echo $farmer['Provinsi'] ?></td>
                                <td><?php echo lang('Village') ?></td>
                                <td>: <?php echo $farmer['Desa'] ?></td>
                            </tr>
                        </table>
                        <h3 style="text-align: left;"><?php echo lang('Membership Data') ?></h3>
                        <table style="width:100%;" cellspacing="0" class="table_noborder">
                            <tr>
                                <td style="width: 25%"><?php echo lang('Group Name') ?></td>
                                <td style="width: 75%">: <?php echo $farmer['CPGid'] ?> - <?php echo $farmer['GroupName'] ?></td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Cooperatives Name') ?></td>
                                <td>: <?php echo $cooperative['CoopName'] ?></td>
                            </tr>
                        </table>
                        <h3 style="text-align: left;"><?php echo lang('Additional Data') ?></h3>
                        <table style="width:100%;" cellspacing="0" class="table_noborder">
                            <tr>
                                <td style="width: 25%"><?php echo lang('Age') ?></td>
                                <td style="width: 25%">: <?php echo $age->y ?> <?php echo lang('yrs old') ?></td>
                                <td style="width: 25%"><?php echo lang('Family Status') ?></td>
                                <td style="width: 25%">: <?php echo !empty($family)?lang('Have'):lang('No') ?></td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Gender') ?></td>
                                <td>: <?php echo $farmer['Gender']=='1'?lang('Male'):lang('Female') ?></td>
                                <td><?php echo lang('Nr of Family Number') ?></td>
                                <td>: <?php 
                                $count = 0;
                                if (!empty($family)) {
                                    foreach ($family as $key => $value) {
                                        if ($value['HubunganKeluarga'] == '2') {
                                            $count++;
                                        }
                                    }
                                    echo $count;
                                }
                                ?></td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Phone Number') ?></td>
                                <td>: <?php echo $farmer['HandPhone'] ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php echo lang('Latest Education') ?></td>
                                <td colspan="3">: <?php 
                                $education = '';
                                switch ($farmer['Education']) {
                                    case '1': $education = lang('No Schooling') ;break;
                                    case '2': $education = lang('Primary School Incomplete') ;break;
                                    case '3': $education = lang('Primary School Completed') ;break;
                                    case '4': $education = lang('Junior High School') ;break;
                                    case '5': $education = lang('Senior High School / Vocational') ;break;
                                    case '5': $education = lang('Tertiary Degree') ;break;
                                }
                                echo $education;
                                 ?></td>
                            </tr>
                        </table>
                        <h3 style="text-align: left;"><?php echo lang('Crops Data') ?></h3>
                        <table style="width:100%;" cellspacing="0" class="table_noborder">
                            <tr>
                                <td style="width: 25%"><?php echo lang('Nr of Cocoa Farms') ?></td>
                                <td style="width: 25%">: <?php echo count($gardens) ?></td>
                                <td style="width: 25%"></td>
                                <td style="width: 25%"></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo lang('Baseline') ?></strong></td>
                                <td></td>
                                <td><strong><?php echo lang('Postline') ?></strong></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Total Hectare') ?></td>
                                <td>: <?php echo $baseline['Hectare'] ?></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Total Hectare') ?></td>
                                <td>: <?php echo $postline['Hectare'] ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Total Production') ?></td>
                                <td>: <?php echo $baseline['Production'] ?></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Total Production') ?></td>
                                <td>: <?php echo $postline['Production'] ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Total Cacao Trees') ?></td>
                                <td>: <?php echo $baseline['Tree'] ?></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Total Cacao Trees') ?></td>
                                <td>: <?php echo $postline['Tree'] ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Average Yield/Hectare') ?></td>
                                <td>: <?php echo number_format($baseline['Production']/$baseline['Hectare'],0,',','.') ?></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('Average Yield/Hectare') ?></td>
                                <td>: <?php echo number_format($postline['Production']/$postline['Hectare'],0,',','.') ?></td>
                            </tr>
                        </table>
                        <h3 style="text-align: left;"><?php echo lang('List of Trainings Participated Data') ?></h3>
                        <table style="width:100%;" cellspacing="0" class="table_bordered">
                            <tr>
                                <td style="text-align: center; width: 80px;"><strong><?php echo lang('Batch') ?></strong></td>
                                <td style="text-align: center; "><strong><?php echo lang('Trainings') ?></strong></td>
                                <td style="text-align: center; width: 80px;"><strong><?php echo lang('Start') ?></strong></td>
                                <td style="text-align: center; width: 80px;"><strong><?php echo lang('End') ?></strong></td>
                                <td style="text-align: center; width: 80px;"><strong><?php echo lang('Days') ?></strong></td>
                                <td style="text-align: center; width: 80px;"><strong><?php echo lang('Type') ?></strong></td>
                            </tr>
                            <?php if ($trainings): ?>
                                <?php foreach ($trainings as $key => $training): ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $training['BatchNumber'] ?></td>
                                        <td><?php echo $training['CpgTrainings'] ?></td>
                                        <td style="text-align: center;"><?php echo date('Y-m-d', strtotime($training['TrainingStart'])) ?></td>
                                        <td style="text-align: center;"><?php echo date('Y-m-d', strtotime($training['TrainingEnd'])) ?></td>
                                        <td style="text-align: center;"><?php echo $training['TrainingDays'] ?></td>
                                        <td style="text-align: center;"><?php echo $training['type'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</page>
<div class="page-break-after"></div>
<?php if (!empty($gardens)): ?>

<script type="text/javascript">
    var icon_path = '<?php echo base_url() ?>' + 'images/map/';
    $('#map_canvas_<?php echo $farmer['FarmerID'] ?>').gmap3({
        map: {
            options: {
                center: [<?php echo $gardens[0]['Latitude'] ?>, <?php echo $gardens[0]['Longitude'] ?>],
                zoom: 11,
                mapTypeControl: false,
                panControl: false,
                zoomControl: false,
                //scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                rotateControlOptions: false,
                overviewMapControl: false,
                OverviewMapControlOptions: false,
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: true
            }
        },
        marker:{
            values:[
                <?php foreach ($gardens as $key => $garden): ?>
                {latLng:[<?php echo $garden['Latitude'] ?>, <?php echo $garden['Longitude'] ?>], options: {icon: icon_path + "farmer.png"}},                    
                <?php endforeach ?>
            ],
            options:{
                draggable: false
            }
        }
    });
</script>    

<?php else: ?>
    <script type="text/javascript">
        var tpl = ""
        $('#map_canvas_<?php echo $farmer['FarmerID'] ?>').html('<div style="margin: 90px auto 0px; text-align: center; color: #aaaaaa;">No Map</div>');
    </script>
<?php endif ?>