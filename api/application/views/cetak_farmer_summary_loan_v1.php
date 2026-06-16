<?php
$now        = new Datetime('now');
$birthdate  = new Datetime($farmer['Birthdate']);
$age        = $now->diff($birthdate);
$productivity = $garden_size['Production']/$garden_size['GardenHaUnCertified'];
if ($productivity < 500) {
    $professionalism = 'Unprofessional';
} elseif ($productivity >= 500 && $productivity <= 1000) {
    $professionalism = 'Progressing';
} elseif ($productivity > 1000) {
    $professionalism = 'Professional';
}
?>
<page size="A4">
<div id="templatemo_container_wrapper">
    <div id="templatemo_container">
        <div id="templatemo_header" class="logos" style="height:100px;">
			<table width="100%" border="0" cellpadding="2">
				<tr>
					<?php if (count($logos['private_sector']) == 1): ?>
						<?php $logo = $logos['private_sector'][0]; ?>
						<?php
							if($logo['Photo']!=''){
								$file = base_url().'images/Photo/'.$logo['Photo'];
								//if (@getimagesize($file)) {
						?>
						<td height="60px" width="20%" align="left" style="vertical-align:middle;">
							<img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:90%; max-height:90%; max-width:120px;">
						</td>
						<?php } //} ?>
						<?php
							if($logo['PhotoProgram']!=''){
								$file = base_url().'images/Photo/'.$logo['PhotoProgram'];
								//if (@getimagesize($file)) {
						?>
						<td height="60px" width="20%" align="left" style="vertical-align:middle;">
							<img src="<?=base_url()?>images/Photo/<?=$logo['PhotoProgram']?>" style="max-width:90%; max-height:90%; max-width:120px;">
						</td>
						<?php } //} ?>
						<?php ?>
					<?php elseif (count($logos['private_sector']) == 2): ?>
						<?php foreach ($logos['private_sector'] as $key => $logo): ?>
							<?php
								if($logo['Photo']!=''){
									$file = base_url().'images/Photo/'.$logo['Photo'];
									//if (@getimagesize($file)) {
							?>
							<td height="60px" align="left" style="vertical-align:middle;">
								<img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:90%; max-height:90%; max-width:120px;">
							</td>
							<?php } //} ?>
						<?php endforeach ?>
					<?php endif ?>
					<?php if (count($logos['donor']) > 0): ?>
						<?php foreach ($logos['donor'] as $key => $logo): ?>
							<?php
								if($logo['Photo']!=''){
									$file = base_url().'images/Photo/'.$logo['Photo'];
									//if (@getimagesize($file)) {
							?>
							<td height="60px" align="left" style="vertical-align:middle;">
								<img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:90%; max-height:90%; max-width:120px;">
							</td>
							<?php } //} ?>
						<?php endforeach ?>
					<?php endif ?>

					<td width="20%" align="right" style="vertical-align:middle;">
						<img src="<?=base_url()?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
					</td>
				</tr>
			</table>
			<table width="100%" border="0" cellpadding="2" style="margin-top:-15px;">
				<tr>
					<td align="center" style="vertical-align:middle;text-decoration:underline;">Farmer Summary – Request for Loan</td>
							</tr>
						</table>
					</div>
        <div id="templatemo_left_column">
            <div class="text_area" align="center">
                <div class="" align="center">
                    <div class="text_area">
                        <table style="width:100%;" cellspacing="0" class="table_bordered">
                            <tr>
                                <td style="width:215px">Name:</td>
                                <td colspan="3"><strong><?php echo $farmer['FarmerName'] ?></strong></td>
                            </tr>
                            <tr>
                                <td style="text-align: center;"><img height="195px" src="<?php echo base_url().'images/Photo/'.$farmer['Photo'] ?>"></td>
                                <td colspan="3" style="background-color:#eee">
                                    <div style="position:relative; width:100%; height: 200px;">
                                        <div id="map_canvas_<?php echo $farmer['FarmerID'] ?>" style="width: 100%; height: 200px; position: absolute !important;"></div>
                                        <div style="position: relative; float: left; width:45%; height: 100%; padding: 10px;">
                                            <table cellspacing="0" class="table_noborder table_map" >
                                                <!-- <tr>
                                                    <td class="no_border" style="width: 100px;">Name:</td>
                                                    <td class="no_border"><?php echo $farmer['FarmerName'] ?></td>
                                                </tr> -->
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
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <br/>
                        <table style="border-collapse: collapse;border-spacing: 0; width: 100%;">
                            <tr>
                                <td class="no_border" style="width: 50%; vertical-align: top; border: 0px solid #000000; padding-right: 4px;">
                        <table style="width:100%;" cellspacing="0" class="table_bordered">
                            <!-- <tr>
                                <td>Province:</td>
                                <td><?php echo $farmer['Provinsi'] ?></td>
                            </tr> -->
                            <!-- <tr>
                                <td>District:</td>
                                <td><?php echo $farmer['Kabupaten'] ?></td>
                            </tr> -->
                            <!-- <tr>
                                <td>Sub District:</td>
                                <td><?php echo $farmer['Kecamatan'] ?></td>
                            </tr> -->
                            <!-- <tr>
                                <td>Village:</td>
                                <td><?php echo $farmer['Desa'] ?></td>
                            </tr> -->
                            <!-- <tr>
                                <td>Birthdate:</td>
                                <td><?php echo $farmer['Birthdate'] ?></td>
                            </tr> -->
                            <!-- <tr>
                                <td>Age: </td>
                                <td><?php echo $age->y ?></td>
                            </tr> -->
                            <tr>
                                <td style="width:50%;">Gender:</td>
                                <td><?php echo $farmer['Gender']=='1'?'Male':'Female' ?></td>
                            </tr>
                            <tr>
                                <td>Marital Status:</td>
                                <td><?php switch ($farmer['MaritalStatus']) {
                                    case '1': $status = 'Married'; break;
                                    case '2': $status = 'Single'; break;
                                    case '3': $status = 'Divorced'; break;
                                }
                                echo $status
                                ?></td>
                            </tr>
                            <tr>
                                <td>Household Members:</td>
                                <td><?php echo count($family)+1 ?></td>
                            </tr>
                            <tr>
                                <td>Phone number:</td>
                                <td><?php echo $farmer['HandPhone'] ?></td>
                            </tr>
                            <tr>
                                <td>Education:</td>
                                <td><?php
                                $education = '';
                                switch ($farmer['Education']) {
                                    case '1': $education = 'No Schooling ' ;break;
                                    case '2': $education = 'Primary School Incomplete' ;break;
                                    case '3': $education = 'Primary School Completed' ;break;
                                    case '4': $education = 'Junior High School' ;break;
                                    case '5': $education = 'Senior High School / Vocational' ;break;
                                    case '5': $education = 'Tertiary Degree' ;break;
                                }
                                echo $education;
                                 ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Need a loan:</td>
                                <td><?php echo $finance['NeedLoan'] == '1'?'Yes':'No' ?></td>
                            </tr>
                            <tr>
                                <td>Loan Experience:</td>
                                <td><?php echo $finance['LoanYesNo'] == '2'?'No':'Yes' ?></td>
                            </tr>
                            <tr>
                                <td>Loan Source:</td>
                                <td><?php
                                if ($finance['LoanYesNo'] !== '2') {
                                    if ($finance['LoanUnitTengkulak']) {
                                        echo 'Trader';
                                    } elseif ($finance['LoanUnitKeluarga']) {
                                        echo 'Family / Friend';
                                    } elseif ($finance['LoanUnitRentenir']) {
                                        echo 'Moneylender / Middlemen';
                                    } elseif ($finance['LoanUnitBank']) {
                                        echo 'Bank';
                                    } elseif ($finance['LoanUnitKoperasi']) {
                                        echo 'Cooperative';
                                    } elseif ($finance['LoanUnitMasjid']) {
                                        echo 'Mosque';
                                    } elseif ($finance['LoanUnitLainnya']) {
                                        echo 'Others';
                                    }
                                } else {
                                    echo 'No';
                                }
                                ?></td>
                            </tr>
                            <tr>
                                <td>Loan Experience from bank:</td>
                                <td><?php echo $finance['LoanUnitBank'] == '1'?'Yes':'No' ?></td>
                            </tr>
                            <tr>
                                <td>How many loans before?</td>
                                <td><?php echo $finance['PreviousLoan'] ?></td>
                            </tr>
                            <tr>
                                <td>Loan Amount:</td>
                                <td><?php echo $finance['AmountOutsCurrentLoan']>0?number_format($finance['AmountOutsCurrentLoan'],0,'.',','):'' ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Saving Account:</td>
                                <td><?php echo $finance['SavingUnitBank'] == '1'?'Yes':'No' ?></td>
                            </tr>
                            <tr>
                                <td>In what bank:</td>
                                <td><?php echo $finance['AccountBankName'] ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Other income:</td>
                                <td><?php echo $finance['OtherIncome'] == '1'?'Yes':'No' ?></td>
                            </tr>
                            <tr>
                                <td>From what?</td>
                                <td><?php
                                    $other_income = array();
                                    if($finance['SourceOtherIncomeGajiTetap'] == '1') { $other_income[] = 'Salary from a full time / part time job'; }
                                    if($finance['SourceOtherIncomeGajiPasangan'] == '1') { $other_income[] = 'Spouse salary'; }
                                    if($finance['SourceOtherIncomeUsaha'] == '1') { $other_income[] = 'Income from other business'; }
                                    if($finance['SourceOtherIncomeFamily'] == '1') { $other_income[] = 'Relatives send money from abroad'; }
                                    if($finance['SourceOtherIncomeLainnya'] == '1') { $other_income[] = 'Other income'; }
                                    echo implode(', ',$other_income);
                                 ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Proposed collateral:</td>
                                <td><?php echo $finance['CollateralOfferedBank'] == '1'?'Cocoa Beans':'' ?></td>
                            </tr>
                            <tr>
                                <td>Future Money Needs:</td>
                                <td><?php
                                    $future = array();
                                    if($finance['FutureReasonSekolah']          == '1') { $future[] = 'School fees / Education'; }
                                    if($finance['FutureReasonRumahTangga']      == '1') { $future[] = 'Household assets (fridge, TV, etc)'; }
                                    if($finance['FutureReasonSumbangan']        == '1') { $future[] = 'Funeral / wedding contributions'; }
                                    if($finance['FutureReasonDarurat']          == '1') { $future[] = 'Emergencies'; }
                                    if($finance['FutureReasonKesehatan']        == '1') { $future[] = 'Health care'; }
                                    if($finance['FutureReasonInvestasiKebun']   == '1') { $future[] = 'Farm investments and Maintenance for my cocoa farm'; }
                                    if($finance['FutureReasonInvestasiLain']    == '1') { $future[] = 'Other business investments'; }
                                    if($finance['FutureReasonRumah']            == '1') { $future[] = 'New house / house renovation'; }
                                    if($finance['FutureReasonLahan']            == '1') { $future[] = 'Buying new land for farming'; }
                                    if($finance['FutureReasonKendaraan']        == '1') { $future[] = 'Motorbike / Car'; }
                                    if($finance['FutureReasonHaji']             == '1') { $future[] = 'Haji / Umrah'; }
                                    if($finance['FutureReasonPensiun']          == '1') { $future[] = 'Retirement'; }
                                    if($finance['FutureReasonLain']             == '1') { $future[] = 'Others'; }
                                    echo implode(', ',$future);
                                 ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="no_border">Notes:</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                        </table>
                                </td>
                                <td class="no_border" style="width: 50%; vertical-align: top;border: 0px solid #000000; padding-left: 4px;">
                        <table style="width:100%;" cellspacing="0" class="table_bordered">
                            <!-- <tr>
                                <td rowspan="2">GPS Coordinates:</td>
                                <td><?php echo $garden['Longitude'] ?> Long</td>
                            </tr>
                            <tr>
                                <td><?php echo $garden['Latitude'] ?> Lat</td>
                            </tr> -->
                            <tr>
                                <td style="width:50%;">Farm distance from house:</td>
                                <td><?php echo $garden['GardenDistance']>0?$garden['GardenDistance'].' m':'' ?></td>
                            </tr>
                            <tr>
                                <td>SCPP ID:</td>
                                <td><?php echo $farmer['FarmerID'] ?></td>
                            </tr>
                            <tr>
                                <td>SCPP Partner:</td>
                                <td><?php echo $partner['PartnerFullName'] ?></td>
                            </tr>
                            <tr>
                                <td>Farmer Group Name:</td>
                                <td><?php echo $farmer['GroupName'] ?></td>
                            </tr>
                            <tr>
                                <td>Total Production (kg):</td>
                                <td><?php echo $garden_size['Production']>0?number_format($garden_size['Production'],0,',','.'):'' ?></td>
                            </tr>
                            <tr>
                                <td>Production per hectare (kg):</td>
                                <td><?php echo ($garden_size['Production']/$garden_size['GardenHaUnCertified']>0)?number_format($garden_size['Production']/$garden_size['GardenHaUnCertified'],0,',','.'):'' ?></td>
                            </tr>
                            <tr>
                                <td>Cocoa Farm Size (ha):</td>
                                <td><?php echo $garden_size['GardenHaUnCertified'] != 0 ? $garden_size['GardenHaUnCertified'] : '' ?></td>
                            </tr>
                            <tr>
                                <td>Other Land Size (ha):</td>
                                <td><?php
                                $other_land_size = 0;
                                if (!empty($otherland)) {
                                    foreach ($otherland as $key => $value) {
                                        $other_land_size += $value['GardenHa'];
                                    }
                                }
                                echo $other_land_size != 0 ? $other_land_size : '';
                                 ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Number of producing cacao trees:</td>
                                <td><?php echo $garden_size['PohonTM']>0?number_format($garden_size['PohonTM'],0,'.',','):'' ?></td>
                            </tr>
                            <tr>
                                <td>Farm Age (years):</td>
                                <td><?php
                                if ($garden['TahunTanamanCocoa']) {
                                    echo date('Y')-$garden['TahunTanamanCocoa'];
                                }
                                ?></td>
                            </tr>
                            <tr>
                                <td>Certified:</td>
                                <td><?php if($garden['isCertification'] == 1) {
                                    echo 'Yes ';
                                    switch ($garden['Certification']) {
                                        case '1': echo 'UTZ'; break;
                                        case '2': echo 'Rainforest'; break;
                                        case '3': echo 'Fairtrade'; break;
                                        case '4': echo 'Organik'; break;
                                    }
                                } else echo 'No' ?></td>
                            </tr>
                            <tr>
                                <td>Trader:</td>
                                <td><?php echo $farmer['is_trader'] == '1'?'Yes':'No' ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Cocoa Price:</td>
                                <td><?php echo ($finance['CocoaPriceToday'] > 0)?number_format($finance['CocoaPriceToday'],0,'.',','):'' ?></td>
                            </tr>
                            <tr>
                                <td>Cash flow from cocoa (ca.):</td>
                                <td><?php echo ($finance['CocoaPriceToday']*$garden_size['Production'] > 0)?number_format($finance['CocoaPriceToday']*$garden_size['Production'],0,'.',','):'' ?></td>
                            </tr>
                            <tr>
                                <td>Value of cocoa farm:</td>
                                <td><?php
                                switch ($finance['ValueCocoaFarm'] ) {
                                    case '1': echo '< 10 million'; break;
                                    case '2': echo '10 - 20 million'; break;
                                    case '3': echo '20 - 50 million'; break;
                                    case '4': echo '50 - 100 million'; break;
                                    case '5': echo '100 - 200 million'; break;
                                    case '6': echo '> 200 million'; break;
                                    case '7': echo 'I don\'t know'; break;
                                }
                                ?></td>
                            </tr>
                            <tr>
                                <td>Land title for cocoa farm:</td>
                                <td><?php
                                switch ($garden['LandCertificate']) {
                                    case '1': echo 'None'; break;
                                    case '2': echo 'Notary Deed/BPN'; break;
                                    case '3': echo 'Sub District'; break;
                                    case '4': echo 'Village/ward'; break;
                                    case '5': echo 'Do not know'; break;
                                }
                                ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Cocoa is a profitable business:</td>
                                <td><?php echo $finance['CocoaProfitableBusiness'] == '1'?'Yes':'No' ?></td>
                            </tr>
                            <tr>
                                <td>Sells beans wet:</td>
                                <td><?php echo $finance['BetterWetDriedBeans'] == '1'?'Yes':'No' ?></td>
                            </tr>
                            <tr>
                                <td>Trainings received:</td>
                                <td><?php
                                if ($training) {
                                    $train = array();
                                    foreach ($training as $key => $value) {
                                        $train[] = $value['CpgTrainings'];
                                    }
                                    echo implode(', ',$train);
                                }
                                ?></td>
                            </tr>
                            <tr>
                                <td class="no_border">&nbsp;</td>
                                <td class="no_border">&nbsp;</td>
                            </tr>
                            <!-- <tr>
                                <td>Professionalism:</td>
                                <td><?php echo $garden_size['LandSize']=='small'?'Unprofessional':'Professional' ?></td>
                            </tr>
                            <tr>
                                <td>Land size:</td>
                                <td><?php echo $garden_size['LandSize'] ?></td>
                            </tr> -->
                        </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</page>
<div class="page-break-after"></div>
<?php if (abs($garden['Latitude']) > 0 && abs($garden['Longitude'])>0): ?>

<script type="text/javascript">
    var icon_path = '<?php echo base_url() ?>' + 'images/map/';
    $('#map_canvas_<?php echo $farmer['FarmerID'] ?>').gmap3({
        map: {
            options: {
                center: [<?php echo $garden['Latitude'] ?>, <?php echo $garden['Longitude'] ?>],
                zoom: 13,
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
                {latLng:[<?php echo $garden['Latitude'] ?>, <?php echo $garden['Longitude'] ?>], data:"<?php echo $farmer['FarmerName'] ?>",options: {
                            icon: icon_path + "farmer.png"
                        }
                    },
            ],
            options:{
                draggable: false
            }
        }
    });
var MAP_STYLE = [
{
    featureType: "road",
    elementType: "all",
    stylers: [
    { visibility: "on" }
    ]
}
];
var styleOptions = {
    name: "Dummy Style"
};
var map = $('#map_canvas_<?php echo $farmer['FarmerID'] ?>').gmap3('get');
var mapType = new google.maps.StyledMapType(MAP_STYLE, styleOptions);
map.mapTypes.set("Dummy Style", mapType);
map.setMapTypeId("Dummy Style");
var origin = new google.maps.LatLng(<?php echo $garden['Latitude'] ?>, <?php echo $garden['Longitude'] ?>),
    destination = new google.maps.LatLng(<?php echo $bank['BranchLatitude'] ?>, <?php echo $bank['BranchLongitude'] ?>),
    service = new google.maps.DistanceMatrixService();

service.getDistanceMatrix(
    {
        origins: [origin],
        destinations: [destination],
        travelMode: google.maps.TravelMode.DRIVING,
        avoidHighways: false,
        avoidTolls: false
    },
    callback
);

function callback(response, status) {
    var orig = document.getElementById("orig"),
        dest = document.getElementById("dest"),
        dist = document.getElementById("dist");

    if(status=="OK") {
        if (response.rows[0].elements[0].status != 'ZERO_RESULTS') {
            $('#bank_distance').val(response.rows[0].elements[0].distance.text);
        };
    } else {
        console.log("Error: " + status);
    }
}
</script>

<?php else: ?>
    <script type="text/javascript">
        var tpl = ""
        $('#map_canvas_<?php echo $farmer['FarmerID'] ?>').html('<div style="margin: 90px auto 0px; text-align: center; color: #aaaaaa;">No Map</div>');
    </script>
<?php endif ?>