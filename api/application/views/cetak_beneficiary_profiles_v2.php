<?php
/**
 * @Author: nikolius
 * @Date:   2016-06-03 17:12:10
 */
?>

<style type="text/css">
.gm-style-cc:last-child {
    display: none !important;
    height: 0px !important;
}
a[title="Report errors in the road map or imagery to Google"] {
    display: none !important;
    height: 0px !important;
}
a[href="https://www.google.com/intl/en-US_US/help/terms_maps.html"] {
    display: none !important;
    height: 0px !important;
}

</style>

<div class="page">

    <table width="100%" border="0" cellpadding="2">
        <tr>
            <?php
                for($i=0;$i<count($logos);$i++){
                    if($logos[$i]['Photo']!=''){
                        ?>
                            <td height="70px" width="20%" align="center" style="vertical-align:middle;">
                                <img src="<?=$logos[$i]['Photo']?>" style="max-width:100%; max-height:100%; max-width:150px;">
                            </td>
                        <?php
                    }
                }
            ?>
        </tr>
    </table>

    <!--- MULAI TABEL UTAMA (BEGIN) -->
    <div id="mainContainer">

    <?php
        if($this->awsfileupload->doesObjectExist($member['Photo']) == true) {
            $Photo  = $this->config->item('CTCDN')."/".$member['Photo'];
        }else{
            $Photo  = base_url().'image_process/resizeOtf?imagenya='.urlencode($member['Photo']).'&width=300&height=300&VID='.$member['VillageID'].'&gen='.$member['Gender'];
        }               
    ?>

    <table id="tableMainContainer" width="100%" height="1000">
    <tr>
       <td valign="top" id="sideColumn">
          <!-- side column (BEGIN) -->
          <table width="100%" id="tabelDivSide">
             <tr>
                <td align="center">
                   <img id="photoPetani" src="<?=$Photo?>" />
                </td>
             </tr>
             <tr height="20">
                <td>&nbsp;</td>
             </tr>
             <tr height="20">
                <td>
                   <div class="divSide" id="titleSideColum">
                        <?php echo strtoupper(lang('FARM SURVEYS'));?>
                   </div>

                   <div class="divSide titleh3SideColumn">
                      BASELINE
                   </div>
                   <div class="divSide centerPos" style="border-bottom:1px solid white;">
                      <table class="tabelValueSideColumn" width="100%">
                         <tr>
                            <td class="tdValue" style="font-size:14px;"><?php echo $garden_baseline['luasKebun'] ?></td>
                            <td class="tdValue" style="font-size:14px;"><?php echo $garden_baseline['Yield'] ?></td>
                         </tr>
                         <tr>
                            <td class="tdDesc"><?php echo strtoupper(lang('Farm Size')) ?> (Ha)</td>
                            <td class="tdDesc"><?php echo strtoupper('Average Plantation Yield (MT/Ha)') ?></td>
                         </tr>
                         <tr>
                            <td class="tdValue" style="font-size:14px;"><?php echo $garden_baseline['AnnualProduction'] ?></td>
                            <td class="tdValue" style="font-size:14px;"><?php echo $garden_baseline['TotalTree'] ?></td>
                         </tr>
                         <tr>
                            <td class="tdDesc"><?php echo strtoupper('Annual Production (MT)') ?></td>
                            <td class="tdDesc"><?php echo strtoupper('Nr. of Oil Palm Trees') ?></td>
                         </tr>
                      </table>
                   </div>

                   <div class="divSide titleh3SideColumn" style="margin-top:10px;">
                      <br />LATEST POST-LINE
                   </div>
                   <div class="divSide centerPos">
                        <table class="tabelValueSideColumn" width="100%">
                         <tr>
                            <td class="tdValue" style="font-size:14px;"><?php echo $garden_postline['luasKebun'] ?></td>
                            <td class="tdValue" style="font-size:14px;"><?php echo $garden_postline['Yield'] ?></td>
                         </tr>
                         <tr>
                            <td class="tdDesc"><?php echo strtoupper(lang('Farm Size')) ?> (Ha)</td>
                            <td class="tdDesc"><?php echo strtoupper('Average Plantation Yield (MT/Ha)') ?></td>
                         </tr>
                         <tr>
                            <td class="tdValue" style="font-size:14px;"><?php echo $garden_postline['AnnualProduction'] ?></td>
                            <td class="tdValue" style="font-size:14px;"><?php echo $garden_postline['TotalTree'] ?></td>
                         </tr>
                         <tr>
                            <td class="tdDesc"><?php echo strtoupper('Annual Production (MT)') ?></td>
                            <td class="tdDesc"><?php echo strtoupper('Nr. of Oil Palm Trees') ?></td>
                         </tr>
                        </table>
                   </div>
                   <br />

                   <div class="divSide titleh3SideColumn" style="margin-top:10px;margin-bottom: 5px;">
                      <?php echo strtoupper(lang('Surveys Log'))?>
                   </div>
                   <table class="tabelListDataSide" width="100%" border="0">
                     <tr>
                        <th><?php echo lang('Farm Nr')?></th>
                        <th><?php echo lang('Survey Nr')?></th>
                        <th><?php echo lang('Date')?></th>
                     </tr>
                     <?php if(count($surveys_log) > 0){?>
                    <?php
                        for ($i=0; $i < count($surveys_log); $i++) {
                            echo '
                            <tr>
                                <td align="center">'.$surveys_log[$i]['PlotNr'].'</td>
                                <td align="center">'.$surveys_log[$i]['SurveyNr'].'</td>
                                <td align="center">'.$surveys_log[$i]['tglSurvey'].'</td>
                            </tr>
                            ';
                        }
                    ?>
                    <?php }else{?>
                    <tr>
                        <td colspan="3" align="center"><?php echo lang('No Survey')?></td>
                    </tr>
                    <?php }?>
                    </table>

                </td>
             </tr>
          </table>
          <!-- side column (END) -->
       </td>
       <td id="mainColumn" valign="top">
          <!-- right column (BEGIN) -->

          <table width="100%" style="min-height: 50px;">
          <tr>
             <td width="80%" id="tdMainTitle" style="border-right: 1px dotted #FF733F;">
                <?php echo strtoupper(lang('PALM OIL FARMER PROFILE'));?>
             </td>
             <td width="20%" id="tdBarcode">
                <img src="<?php echo base_url() .  $qrcode_pic ?>" width="100" />
             </td>
          </tr>
          </table>

          <table width="100%" style="margin-top:-20px;">
             <tr>
                <td width="33" valign="top">
                   <img src="<?php echo base_url() ?>assets/css/print_beneficiary/basic-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                   <?php echo strtoupper(lang('Basic Data')) ?>
                </td>
             </tr>
             <tr>
                <td colspan="2" valign="top">

                    <table width="100%" class="tabelValueMain">
                    <tr>
                        <td width="17%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('ID')) ?></div>
                            <div class="tdValueMain"><?php echo strtoupper($member['MemberDisplayID']) ?></div>
                        </td>
                        <td width="30%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Nama')) ?></div>
                            <div class="tdValueMain"><?php echo strtoupper($member['MemberName']) ?></div>
                        </td>
                        <td width="31%">
                            <?php
                                $now        = new Datetime('now');
                                $birthdate  = new Datetime($member['DateOfBirth']);
                                $age        = $now->diff($birthdate);
                            ?>
                            <div class="tdLabelMain"><?php echo strtoupper(lang('age')) ?></div>
                            <div class="tdValueMain"><?php echo $age->y ?>&nbsp;</div>
                        </td>
                        <td width="22%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('No Telepon')) ?></div>
                            <div class="tdValueMain"><?php echo $member['Handphone'] ?>&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="17%" valign="top">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Gender')) ?></div>
                            <div class="tdValueMain">
                             <?php
                                if($member['Gender']=='m'){
                                   echo lang('Male');
                                }else{
                                   echo lang('Female');
                                }
                             ?>
                            </div>
                        </td>
                        <td width="30%" valign="top">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Education')) ?></div>
                            <div class="tdValueMain"><?php
                            switch ($member['Education']) {
                                case '1':
                                    echo lang('No Education');
                                break;
                                case '2':
                                    echo lang('Primary School Incompleted');
                                break;
                                case '3':
                                    echo lang('Primary School Completed');
                                break;
                                case '4':
                                    echo lang('Graduated Middle School');
                                break;
                                case '5':
                                    echo lang('Graduated High School');
                                break;
                                case '6':
                                    echo lang('Graduated College');
                                break;
                                case '7':
                                    echo lang('Magister/S2');
                                break;
                                case '8':
                                    echo lang('Doctor/S3');
                                break;
                                default:
                                    echo '-';
                                break;
                            }
                            ?>&nbsp;</div>
                        </td>
                        <td width="31%" valign="top">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Marital Status')) ?></div>
                            <div class="tdValueMain"><?php
                            switch ($member['MaritalStatus']) {
                                case '1':
                                    echo lang('Married');
                                break;
                                case '2':
                                    echo lang('Single');
                                break;
                                case '3':
                                    echo lang('Janda/Duda');
                                break;
                                default:
                                    echo '-';
                                break;
                            }
                            ?>&nbsp;</div>
                        </td>
                        <td width="22%" valign="top">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Provinsi')) ?></div>
                            <div class="tdValueMain"><?php echo $member['Provinsi'] ?>&nbsp;</div>
                        </td>
                    </tr>
                    </table>

                    <table width="100%" class="tabelValueMain">
                    <tr>
                        <td width="33%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('District')) ?></div>
                            <div class="tdValueMain"><?php echo $member['Kabupaten'] ?>&nbsp;</div>
                        </td>
                        <td width="33%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Sub District')) ?></div>
                            <div class="tdValueMain"><?php echo $member['Kecamatan'] ?>&nbsp;</div>
                        </td>
                        <td width="34%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Village')) ?></div>
                            <div class="tdValueMain"><?php echo $member['Desa'] ?>&nbsp;</div>
                        </td>
                    </tr>
                    </table>

                </td>
             </tr>
          </table>
          <br />

          <table width="100%" style="margin-bottom: 8px;margin-top:-15px;">
             <tr>
                <td width="33" valign="top">
                   <img src="<?php echo base_url() ?>assets/css/print_beneficiary/farm-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                    <?php echo strtoupper(lang('FARM DATA'))?>
                </td>
             </tr>
          </table>

          <table class="tabelListData" width="100%" border="0">
             <tr>
                <th><?php echo strtoupper(lang('Plot'))?></th>
                <th><?php echo strtoupper(lang('Location'))?></th>
                <th><?php echo strtoupper(lang('Ownership Doc'))?></th>
                <!--<th><?php echo strtoupper(lang('Business Model'))?></th>-->
                <th><?php echo strtoupper(lang('Size (Ha)'))?></th>
                <th><?php echo strtoupper(lang('Avg Tree Age'))?></th>
             </tr>
             <?php
                if(count($gardens) > 0){
                   $increList = 1;
                   foreach ($gardens as $key => $garden){
                      switch ($garden['OwnershipDoc']) {
                         case '1':
                            $OwnershipDoc = lang('No Document');
                         break;
                         case '2':
                            $OwnershipDoc = lang('SKT');
                         break;
                         case '3':
                            $OwnershipDoc = lang('SHM / Sertifikat');
                         break;
                         case '4':
                            $OwnershipDoc = lang('HGU');
                         break;
                         case '5':
                            $OwnershipDoc = lang('SKGR');
                         break;
                         case '6':
                            $OwnershipDoc = lang('Other');
                         break;
                         default:
                            $OwnershipDoc = '-';
                         break;
                      }

                      switch ($garden['BusinessModel']) {
                         case '1':
                            $BusinessModel = lang('Independent');
                         break;
                         case '2':
                            $BusinessModel = lang('Independent - Ex Plasma');
                         break;
                         case '3':
                            $BusinessModel = lang('Plasma');
                         break;
                         default:
                            $BusinessModel = '-';
                         break;
                      }

                      switch ($garden['SoilType']) {
                            case '1':
                                $SoilType = lang('Mineral');
                            break;
                            case '2':
                                $SoilType = lang('Peat');
                            break;
                            default:
                                $SoilType = "-";
                            break;
                      }

                      echo '<tr>
                      <td align="center">'.$garden['PlotNr'].'</td>
                      <td align="center">'.$garden['Location'].'</td>
                      <td align="center">'.$OwnershipDoc.'</td>
                      <td align="center">'.number_format($garden['GardenAreaHa'],1,".",",").'</td>
                      <td align="center">'.number_format($garden['AverageAgeTree'],0,".",",").'</td>
                      </tr>';
                      $increList++;
                   }
                }else{
                   echo '<tr><td colspan="5" align="center">No Data</td></tr>';
                }
             ?>
          </table>
          <br />

          <!--
          <div id="map_canvas_<?php echo $member['MemberID'] ?>" style="width: 100%; height: 110px;"></div>
          <br />
          -->

          <table width="100%" style="margin-bottom: 8px;margin-top:-8px;">
             <tr>
                <td width="33" valign="top">
                   <img src="<?php echo base_url() ?>assets/css/print_beneficiary/training-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                   <?php echo strtoupper(lang('Training Participation'))?>
                </td>
             </tr>
          </table>

          <table class="tabelListData" width="100%" border="0">
             <tr>
                <th style="width: 4%;">NO.</th>
                <th><?php echo strtoupper(lang('Training'))?></th>
                <th style="width: 16%;"><?php echo strtoupper(lang('Start'))?></th>
                <th style="width: 16%;"><?php echo strtoupper(lang('Selesai'))?></th>
                <th style="width: 7%;"><?php echo strtoupper(lang('Days'))?></th>
             </tr>
             <?php if ($trainings[0]['CpgTrainings'] != ""){ ?>
                <?php
                $increTrain = 1;
                ?>
                <?php foreach ($trainings as $key => $training): ?>
                   <tr>
                      <td align="center"><?php echo $increTrain ?>.</td>
                      <td>
                        <?php
                            if($training['sub_topic'] != ""){
                                echo $training['CpgAbbre'];
                            }else{
                                echo $training['CpgTrainings'];
                            }
                        ?>
                        <?php if ($training['sub_topic']): ?>
                            - [<?php echo $training['sub_topic'] ?>]
                        <?php endif ?>
                      </td>
                      <td align="center"><?php echo date('d M Y', strtotime($training['TrainingStart'])) ?></td>
                      <td align="center"><?php echo date('d M Y', strtotime($training['TrainingEnd'])) ?></td>
                      <td align="center"><?php echo $training['TrainingDays'] ?></td>
                   </tr>
                   <?php
                   $increTrain++;
                   if($increTrain > 16) break;
                   ?>
                <?php endforeach ?>
             <?php }else{ ?>
                <tr><td colspan="5" align="center">No Data</td></tr>
             <?php }?>
          </table>
          <br />

          <table width="100%" style="margin-bottom: 8px;margin-top:-8px;">
             <tr>
                <td width="33" valign="top">
                   <img src="<?php echo base_url() ?>assets/css/print_beneficiary/farm-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                   <?php echo strtoupper(lang('Traceable FFB Sales Summary'))?>
                </td>
             </tr>
          </table>
          <table class="tabelListData" width="100%" border="0">
             <tr>
                <th><?php echo strtoupper(lang('Year'))?></th>
                <th><?php echo strtoupper(lang('Total Gross'))?> (kg)</th>
                <th><?php echo strtoupper(lang('Total Nett Weight'))?> (kg)</th>
                <th><?php echo strtoupper(lang('Total Income'))?> (Rp)</th>
             </tr>
            <?php
                if($traceability[0]['trans_year'] != ""){
                    foreach($traceability as $k=>$v){
                        echo '<tr>';
                            echo '<td align="center">'.$v['trans_year'].'</td>';
                            echo '<td align="center">'.$v['bruto'].'</td>';
                            echo '<td align="center">'.$v['netto'].'</td>';
                            echo '<td align="center">'.$v['payment'].'</td>';
                        echo '</tr>';
                    }
                }else{
                    echo '<tr><td colspan="4" align="center">No Data</td></tr>';
                }

            ?>
         </table>

          <!-- right column (END) -->
       </td>
    </tr>
    </table>

    </div>
    <!--- MULAI TABEL UTAMA (END) -->

    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="30%">&nbsp;</td>
                <td class="kolomKanan" width="70%" align="right">
                    <table width="100%">
                    <tr>
                        <td style="letter-spacing: 0.1em;font-size: 11px" width="60%">Copyright &copy <?php echo date('Y')?> by PT SawitChain</td>
                        <td style="letter-spacing: 0.1em;font-size: 11px" width="40%" align="right">www.sawitchain.com</td>
                    </tr>
                    </table>
                </td>
            </tr>
        </table>
    </footer>
</div>
<!--                HALAMAN MAP (BEGIN)              -->
<?php if($gardens_coordinate_exists == true){?>

<div class="page">

    <table width="100%" border="0" cellpadding="2">
        <tr>
        <?php
            for($i=0;$i<count($logos);$i++){
                if($logos[$i]['Photo']!=''){
                    if($this->awsfileupload->doesObjectExist($logos[$i]['Photo']) == true) {
                        $Photo  = $this->config->item('CTCDN')."/".$logos[$i]['Photo'];
                        ?>
                            <td height="70px" width="20%" align="center" style="vertical-align:middle;">
                                <img src="<?=$Photo?>" style="max-width:90%; max-height:90%; max-width:120px;">
                            </td>
                        <?php
                    }else{
                        if(file_exists($logos[$i]['Path'].'/'.$logos[$i]['Photo'])){
                            $Photo  = base_url().$logos[$i]['Path'].'/'.$logos[$i]['Photo'];
                            ?>
                                <td height="70px" width="20%" align="center" style="vertical-align:middle;">
                                    <img src="<?=$Photo?>" style="max-width:90%; max-height:90%; max-width:120px;">
                                </td>
                            <?php
                        }
                    }
                }
            }
        ?>
        </tr>
    </table><br /><br />

    <!--- MULAI TABEL UTAMA (BEGIN) -->
    <div id="mainContainer" style="background-image: none !important;">

        <table id="tableMainContainer" width="98%">
        <tr>
            <td id="mainColumn" valign="top" style="width:100%">

                <table width="100%" style="margin-bottom: 8px;margin-top:-15px;">
                     <tr>
                        <td width="33" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/print_beneficiary/farm-data-icon.png" width="22" />
                        </td>
                        <td class="tdTitleMain" valign="top">
                            <?php echo strtoupper(lang('FARM MAP'))?>
                        </td>
                     </tr>
                </table>

                <div style="height:400px;width:100%;margin:0;padding:0;" id="map_canvas_<?php echo $member['MemberID'] ?>"></div>

            </td>
        </tr>
        </table>

        <!-- JS Generate MAP (BEGIN) -->

        <script type="text/javascript">
        function initMap<?php echo $member['MemberID'] ?>() {
            var imageMarker = "<?php echo base_url() ?>assets/css/print_beneficiary/basic-data-icon-map.png";
            var bounds = new google.maps.LatLngBounds();
            var infowindow = new google.maps.InfoWindow();
            var maxZoomService = new google.maps.MaxZoomService();

            var Lokasinya = [
                <?php
                for ($i=0; $i < count($gardens); $i++) {
                    echo '["Farm Nr : '.$gardens[$i]['PlotNr'].'",'.$gardens[$i]['Latitude'].','.$gardens[$i]['Longitude'].','.$gardens[$i]['PlotNr'].']';

                    if($i != (count($gardens) -1)){
                        echo ',';
                    }
                }
                ?>
            ];
            //console.log(Lokasinya);


            var map = new google.maps.Map(document.getElementById('map_canvas_<?php echo $member['MemberID'] ?>'), {
                zoom: 15,
                center: new google.maps.LatLng(<?php echo $gardens[0]['Latitude'] ?>, <?php echo $gardens[0]['Longitude'] ?>),
                mapTypeId: google.maps.MapTypeId.HYBRID
            });

            //Titik Koordinatnya ======================= (Begin)
            for (i = 0; i < Lokasinya.length; i++) {
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(Lokasinya[i][1], Lokasinya[i][2]),
                    map: map,
                    icon: imageMarker
                });

                //extend the bounds to include each marker's position
                bounds.extend(marker.position);

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                      infowindow.setContent(Lokasinya[i][0]);
                      infowindow.open(map, marker);
                    }
                })(marker, i));

                //now fit the map to the newly inclusive bounds
                map.fitBounds(bounds);
                map.panToBounds(bounds);
            }
            //Titik Koordinatnya ======================= (End)

            //Gambar Polygonnya ======================= (BEGIN)
            <?php for ($i=0; $i < count($gardens_polygon); $i++) { ?>
                var Polygonnya<?php echo $gardens_polygon[$i]['PlotNr'] ?> = new google.maps.Polygon({
                  paths: <?php echo $gardens_polygon[$i]['polygon_data'] ?>,
                  strokeColor: '#FF0000',
                  strokeOpacity: 0.8,
                  strokeWeight: 3,
                  fillColor: '#FF0000',
                  fillOpacity: 0.35
                });
                Polygonnya<?php echo $gardens_polygon[$i]['PlotNr'] ?>.setMap(map);

                var garPoly = <?php echo $gardens_polygon[$i]['polygon_data'] ?>;
                for (incre = 0; incre < garPoly.length; incre++) {
                    bounds.extend(new google.maps.LatLng(garPoly[incre].lat, garPoly[incre].lng));
                }
            <?php } ?>
            //Gambar Polygonnya ======================= (End)

            //(optional) restore the zoom level after the map is done scaling
            var listener = google.maps.event.addListener(map, "idle", function () {
                map.fitBounds(bounds);
                map.panToBounds(bounds);

                //set Zoom Level, cek Sorry, no imagery here
                maxZoomService.getMaxZoomAtLatLng(map.getCenter(), function(response) {
                  if (response.status !== 'OK') {
                    infoWindow.setContent('Error in MaxZoomService');
                  } else {
                    if(map.getZoom() > response.zoom){
                        map.setZoom(response.zoom);
                    }
                  }
                });

                google.maps.event.removeListener(listener);
            });

        }
        </script>

        <!-- JS Generate MAP (END) -->
        <?php if(count($traceability_details) > 0) { ?>
        <br>
        <br>
        <table width="100%" style="margin-bottom: 8px;margin-top:-15px;">
            <tr>
                <td width="4%" valign="top">
                    <img src="<?php echo base_url() ?>assets/css/print_beneficiary/farm-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                    <?php echo strtoupper(lang('CURRENT YEAR TRACEABLE AND CERTIFIED PALMOIL SALES DETAIL'))?>
                </td>
            </tr>
        </table>

        <!-- MAX 20 Baris disini -->
        <table class="tabelListData" width="100%" border="0">
            <tr>
                <th width="5%">No.</th>
                <th><?php echo strtoupper(lang('Transaction Date'))?></th>
                <th><?php echo strtoupper(lang('Nett Weight'))?> (kg)</th>
                <th><?php echo strtoupper(lang('FFB'))?></th>
                <th><?php echo strtoupper(lang('Price'))?> Rp/Kg</th>
                <th><?php echo strtoupper(lang('Income'))?> (Rp)</th>
                <th><?php echo strtoupper(lang('BUYER'))?></th>
            </tr>
            <?php
                $increData = 1;
                for ($i=0; $i < count($traceability_details); $i++) {
                    echo '
                    <tr>
                        <td align="center">'.$increData.'</td>
                        <td align="center">'.$traceability_details[$i]['DateTransaction'].'</td>
                        <td align="center">'.number_format($traceability_details[$i]['VolumeNetto'],2,".",",").'</td>
                        <td align="center">'.$traceability_details[$i]['FFB'].'</td>
                        <td align="center">'.number_format($traceability_details[$i]['NetPrice'],0,".",",").'</td>
                        <td align="center">'.number_format($traceability_details[$i]['TotalPayment'],0,".",",").'</td>
                        <td align="center">'.$traceability_details[$i]['Name'].'</td>
                    </tr>
                    ';
                    $increData++;
                }
                ?>
        </table>
        <?php } ?>
    
        <br>
            <br>
            <div style="padding: 0px 50px 0px 50px;">
                <table width="100%" style="margin-bottom: 8px;margin-top:-15px;">
                    <tr>
                        <td width="33" valign="top">
                            <img src="<?php echo base_url() ?>assets/css/print_beneficiary/farm-data-icon.png" width="22" />
                        </td>
                        <td class="tdTitleMain" valign="top">
                            <?php echo strtoupper(lang('FARM DATA')) ?>
                        </td>
                    </tr>
                </table>

                <table class="tabelListData" width="100%" border="0">
                    <tr>
                        <th><?php echo strtoupper(lang('Plot')) ?></th>
                        <th><?php echo strtoupper(lang('Location')) ?></th>
                        <th><?php echo strtoupper(lang('Ownership Doc')) ?></th>
                        <!--<th><?php echo strtoupper(lang('Business Model')) ?></th>-->
                        <th><?php echo strtoupper(lang('Size (Ha)')) ?></th>
                        <th><?php echo strtoupper(lang('Avg Tree Age')) ?></th>
                        <th><?php echo strtoupper(lang('Latitude')) ?></th>
                        <th><?php echo strtoupper(lang('Longitude')) ?></th>
                    </tr>
                    <?php
                    if (count($gardens) > 0) {
                        $increList = 1;
                        foreach ($gardens as $key => $garden) {
                            switch ($garden['OwnershipDoc']) {
                                case '1':
                                    $OwnershipDoc = lang('No Document');
                                    break;
                                case '2':
                                    $OwnershipDoc = lang('SKT');
                                    break;
                                case '3':
                                    $OwnershipDoc = lang('SHM / Sertifikat');
                                    break;
                                case '4':
                                    $OwnershipDoc = lang('HGU');
                                    break;
                                case '5':
                                    $OwnershipDoc = lang('SKGR');
                                    break;
                                case '6':
                                    $OwnershipDoc = lang('Other');
                                    break;
                                default:
                                    $OwnershipDoc = '-';
                                    break;
                            }

                            switch ($garden['BusinessModel']) {
                                case '1':
                                    $BusinessModel = lang('Independent');
                                    break;
                                case '2':
                                    $BusinessModel = lang('Independent - Ex Plasma');
                                    break;
                                case '3':
                                    $BusinessModel = lang('Plasma');
                                    break;
                                default:
                                    $BusinessModel = '-';
                                    break;
                            }

                            switch ($garden['SoilType']) {
                                case '1':
                                    $SoilType = lang('Mineral');
                                    break;
                                case '2':
                                    $SoilType = lang('Peat');
                                    break;
                                default:
                                    $SoilType = "-";
                                    break;
                            }

                            echo '<tr>
                      <td align="center">' . $garden['PlotNr'] . '</td>
                      <td align="center">' . $garden['Location'] . '</td>
                      <td align="center">' . $OwnershipDoc . '</td>
                      <td align="center">' . number_format($garden['GardenAreaHa'], 1, ".", ",") . '</td>
                      <td align="center">' . number_format($garden['AverageAgeTree'], 0, ".", ",") . '</td>
                      <td align="center">' . $garden['Latitude'] . '</td>
                      <td align="center">' . $garden['Longitude'] . '</td>
                      </tr>';
                            $increList++;
                        }
                    } else {
                        echo '<tr><td colspan="5" align="center">No Data</td></tr>';
                    }
                    ?>
                </table>
            </div>
            <br/>
    </div>

    <div id="mainContainerPolygon" style="background-image: none !important;">

        <table id="tableMainContainer" width="98%">
            <tr>
                <td id="mainColumn" valign="top" style="width:100%">

                    <table width="100%" style="margin-bottom: 8px;margin-top:-15px;">
                         <tr>
                            <td width="33" valign="top">
                               <img src="<?php echo base_url() ?>assets/css/print_beneficiary/farm-data-icon.png" width="22" />
                            </td>
                            <td class="tdTitleMain" valign="top">
                                <?php echo strtoupper(lang('POLYGON MAP'))?>
                            </td>
                         </tr>
                    </table>

                    <div style="height:400px;width:100%;margin:0;padding:0;" id="map_polygon_<?php echo $member['MemberID'] ?>" class="gmap3"></div>

                </td>
            </tr>
        </table>

        <!-- JS Generate MAP (BEGIN) -->

        <script type="text/javascript">
            function initMapPolygon<?php echo $member['MemberID'] ?>() {
                function show_polygon_new() {
                    let areasNew = <?php echo $areaPolygon; ?>;
                    for (let i = 0; i <= areasNew.length; i++) {
                        let valuenya = areasNew[i]

                        $("#map_polygon_<?php echo $member['MemberID'] ?>").gmap3({
                            polygon: {
                                options: {
                                    strokeColor: "#FF0000",
                                    strokeOpacity: 0.8,
                                    strokeWeight: 2,
                                    fillColor: "#FF0000",
                                    fillOpacity: 0.35,
                                    paths: [valuenya]
                                }
                            }
                        });
                    }
                }

                function fixMap(map)
                {
                    var center = map.getCenter();
                    google.maps.event.trigger(map, 'resize');
                    map.setCenter(center);
                }

                var earthRadiusMeters   = 6367460.0;
                var metersPerDegree     = 2.0*Math.PI*earthRadiusMeters/360.0;
                var radiansPerDegree    = Math.PI/180.0;

                var area_bounds = null;
                area_bounds = new google.maps.LatLngBounds();

                var map;
                var markers = new Array();
                var mode = 'normal';

                $("#map_polygon_<?php echo $member['MemberID'] ?>").gmap3({
                    map: {
                        options: {
                            center: [<?php echo $centerLatLongPolygon[0]?>,<?php echo $centerLatLongPolygon[1]?>],
                            zoom: 14.5
                        }
                    }
                });

                map = $("#map_polygon_<?php echo $member['MemberID'] ?>").gmap3({get: {name: "map"}});

                show_polygon_new();

                setTimeout(function(){
                    map = $('#map_polygon_<?php echo $member['MemberID'] ?>').gmap3('get');
                    fixMap(map);
                }, 10);
            }
        </script>

        <!-- JS Generate MAP (END) -->
    
        <br>
            <br>
            <div style="padding: 0px 50px 0px 50px;">
                <table width="100%" style="margin-bottom: 8px;margin-top:-15px;">
                    <tr>
                        <td width="33" valign="top">
                            <img src="<?php echo base_url() ?>assets/css/print_beneficiary/farm-data-icon.png" width="22" />
                        </td>
                        <td class="tdTitleMain" valign="top">
                            <?php echo strtoupper(lang('POLYGON DATA')) ?>
                        </td>
                    </tr>
                </table>

                <table class="tabelListData" width="100%" border="0">
                    <tr>
                        <th><?php echo strtoupper(lang('Plot Nr')) ?></th>
                        <th><?php echo strtoupper(lang('Location')) ?></th>
                        <th><?php echo strtoupper(lang('Polygon Size (Ha)')) ?></th>
                    </tr>
                    <?php
                    if (count($polygon_list) > 0) {
                        $increList = 1;
                        foreach ($polygon_list as $key => $value) {
                            echo '<tr>
                                  <td align="center">' . $value['PlotNr'] . '</td>
                                  <td align="center">' . $value['Location'] . '</td>
                                  <td align="center">' . number_format($value['PolygonSize'], 1, ".", ",") . '</td>
                                  </tr>';
                            $increList++;
                        }
                    } else {
                        echo '<tr><td colspan="5" align="center">No Data</td></tr>';
                    }

                    ?>
                </table>
            </div>
            <br/>
    </div>

    <footer>
        <div class="note">
            <div>
                <p><?php echo lang('Note'). ':'; ?></p>
                <ul>
                    <li><?php echo lang('Pemegang dokumen harus menyertakan fotokopi identitas (KTP)'); ?></li>
                    <li><?php echo lang('Pihak perusahaan tidak bertanggung jawab atas penyalahgunaan dokumen oleh pihak manapun'); ?></li>
                    <li><?php echo lang('Dokumen ini bukan merupakan legalitas administrasi pemerintah maupun badan sertifikasi tertentu'); ?></li>
                </ul>
            </div>
        </div>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="30%">&nbsp;</td>
                <td class="kolomKanan" width="70%" align="right">
                    <table width="100%">
                    <tr>
                        <td style="letter-spacing: 0.1em;font-size: 11px" width="60%">Copyright &copy <?php echo date('Y')?> by PT SawitChain</td>
                        <td style="letter-spacing: 0.1em;font-size: 11px" width="40%" align="right">www.sawitchain.com</td>
                    </tr>
                    </table>
                </td>
            </tr>
        </table>
    </footer>

</div>

<?php }?>
<!--                HALAMAN MAP (END)              -->