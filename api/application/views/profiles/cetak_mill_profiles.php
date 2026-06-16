<?php

/**
 * @Author: nikolius
 * @Date:   2018-01-02 17:13:02
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-17 12:10:15
 */
?>
<style type="text/css">
.gm-style-cc:last-child {
    display: none !important;
}
a[title="Report errors in the road map or imagery to Google"] {
    display: none !important;
}
a[href="https://www.google.com/intl/en-US_US/help/terms_maps.html"] {
    display: none !important;
}
</style>

	<div class="page">
    
		<table width="100%" border="0" cellpadding="2">
            <tr>
                <?php
                    for($i=0;$i<count($logos);$i++){
                        if($logos[$i]['Photo']!=''){
                            $Photo  = $logos[$i]['Photo'];
                            ?>
                                <td height="70px" width="20%" align="center" style="vertical-align:middle;">
                                    <img src="<?=$Photo?>" style="max-width:90%; max-height:90%; max-width:120px;" onerror="this.style.display='none'">
                                </td>
                            <?php
                        }
                    }
                ?>
            </tr>
        </table>
        <br /><br />

        <div id="mainContainer"> <!--- MULAI TABEL UTAMA (BEGIN) -->

        <table id="tableMainContainer" width="100%" height="975">
        <tr>
           	<td valign="top" id="sideColumn">
            <!-- left column (BEGIN) -->

	            <table width="100%" id="tabelDivSide" style="margin-bottom:12px;">
				<tr>
					<td align="center">
                            <?php if ($this->awsfileupload->doesObjectExist($mill['Photo']) == true) { ?>
                                    <img id="photoPetani" src="<?php echo $this->config->item('CTCDN')."/".$mill['Photo']; ?>" width="200" height="200"/>
                            <?php } else { ?>
                                    <img id="photoPetani" src="<?php echo base_url().'image_process/resizeOtfLandscape?imagenya='.urlencode($mill['Photo']).'&width=200&height=200&opsi=logoMill&VID='.$mill['VillageID']; ?>" />
                            <?php } ?>
					</td>
				</tr>
				<tr height="20">
					<td>&nbsp;</td>
				</tr>
				<tr>
                    <td>
                        <div class="divSide" id="titleSideColum">
                            <?php echo strtoupper(lang('Business Transaction'));?><br />
                            <span class="smallerHeader">(YEAR)</span>
                        </div>

                    </td>
                </tr>
				</table>

                <div class="sideTabelHeader"><?php echo strtoupper(lang('FFB Sales'))?></div>
                <table class="tabelListDataSide" width="100%" border="0" style="margin-bottom:12px;">
                <tr style="background-color: #AAA7A9;">
                    <th style="width: 20%;">&nbsp;</th>
                    <th style="width: 20%;">Q1</th>
                    <th style="width: 20%;">Q2</th>
                    <th style="width: 20%;">Q3</th>
                    <th style="width: 20%;">Q4</th>
                </tr>
                <tr style="background-color: white;">
                    <td>BATCH</td>
                    <td><?php echo @$ffb[0]['Q1_batch'];?></td>
                    <td><?php echo @$ffb[0]['Q2_batch'];?></td>
                    <td><?php echo @$ffb[0]['Q3_batch'];?></td>
                    <td><?php echo @$ffb[0]['Q4_batch'];?></td>
                </tr>
                <tr style="background-color: white;">
                    <td>TON</td>
                    <td><?php echo @$ffb[0]['Q1_ton'];?></td>
                    <td><?php echo @$ffb[0]['Q2_ton'];?></td>
                    <td><?php echo @$ffb[0]['Q3_ton'];?></td>
                    <td><?php echo @$ffb[0]['Q4_ton'];?></td>
                </tr>
                </table>
                <br />

                <div class="sideTabelHeader"><?php echo strtoupper(lang('FFB Suppliers'))?></div>
                <table class="tabelListDataSide" width="100%" border="0" style="margin-bottom:12px;">
                <tr style="background-color: #AAA7A9;">
                    <th style="width: 20%;">&nbsp;</th>
                    <th style="width: 20%;">Q1</th>
                    <th style="width: 20%;">Q2</th>
                    <th style="width: 20%;">Q3</th>
                    <th style="width: 20%;">Q4</th>
                </tr>
                <tr style="background-color: white;">
                    <td><?php echo strtoupper(lang('Agent'))?></td>
                    <td><?php echo @$ffb[0]['Q1_agent'];?></td>
                    <td><?php echo @$ffb[0]['Q2_agent'];?></td>
                    <td><?php echo @$ffb[0]['Q3_agent'];?></td>
                    <td><?php echo @$ffb[0]['Q4_agent'];?></td>
                </tr>
                <tr style="background-color: white;">
                    <td><?php echo strtoupper(lang('Farmer'))?></td>
                    <td><?php echo @$ffb[0]['Q1_farmer'];?></td>
                    <td><?php echo @$ffb[0]['Q2_farmer'];?></td>
                    <td><?php echo @$ffb[0]['Q3_farmer'];?></td>
                    <td><?php echo @$ffb[0]['Q4_farmer'];?></td>
                </tr>
                <tr style="background-color: white;">
                    <td><?php echo strtoupper(lang('Transaction'))?></td>
                    <td><?php echo @$ffb[0]['Q1_transaction'];?></td>
                    <td><?php echo @$ffb[0]['Q2_transaction'];?></td>
                    <td><?php echo @$ffb[0]['Q3_transaction'];?></td>
                    <td><?php echo @$ffb[0]['Q4_transaction'];?></td>
                </tr>
                </table>
                <br />
                <?php
                    $tsBatch = $ffb[0]['Q1_batch']+$ffb[0]['Q2_batch']+$ffb[0]['Q3_batch']+$ffb[0]['Q4_batch'];
                    $tsNetto = $ffb[0]['Q1_ton']+$ffb[0]['Q2_ton']+$ffb[0]['Q3_ton']+$ffb[0]['Q4_ton'];
                    $tsFarmer = $ffb[0]['Q1_farmer']+$ffb[0]['Q2_farmer']+$ffb[0]['Q3_farmer']+$ffb[0]['Q4_farmer'];
                    $tsTrans = $ffb[0]['Q1_transaction']+$ffb[0]['Q2_transaction']+$ffb[0]['Q3_transaction']+$ffb[0]['Q4_transaction'];
                    $tsAgent = $ffb[0]['Q1_agent']+$ffb[0]['Q2_agent']+$ffb[0]['Q3_agent']+$ffb[0]['Q4_agent'];
                ?>

                <div class="divSide" id="titleSideColum" style="margin-bottom: 10px;padding-top: 5px;">
                    <?php echo strtoupper(lang('Traceability Summary'));?>
                </div>
                <div class="divSide centerPos" style="margin-left: 2px;">
                  <table class="tabelValueSideColumn" width="100%">
                     <tr>
                        <td class="tdValue"><?php echo number_format($tsBatch) ?></td> 
                        <td class="tdValue"><?php echo number_format($tsNetto,2) ?></td>
                     </tr>
                     <tr>
                        <td class="tdDesc"><?php echo strtoupper(lang('Batches')) ?></td>
                        <td class="tdDesc"><?php echo strtoupper(lang('MT/FFB'))?></td>
                     </tr>
                     <tr height="11">
                        <td colspan="2">&nbsp;</td>
                     </tr>
                     <tr>
                        <td class="tdValue" style="font-size:18px;"><?php echo number_format($tsAgent) ?></td>
                        <td class="tdValue" style="font-size:18px;"><?php echo number_format($tsFarmer) ?></td>
                     </tr>
                     <tr>
                        <td class="tdDesc"><?php echo strtoupper('Agent') ?></td>
                        <td class="tdDesc"><?php echo strtoupper('Farmer') ?></td>
                     </tr>
                     <tr>
                        <td colspan="2" class="tdValue"><?php echo number_format($tsTrans) ?></td>
                     </tr>
                     <tr>
                        <td colspan="2" class="tdDesc"><?php echo strtoupper('Transactions') ?></td>
                     </tr>
                  </table>
               </div>

                <!--
				<div class="sideValueHeadingText" style="padding-bottom: 15px;">
					<?php echo number_format($mill['PlasmaFarmer'],0,".",",")?><br />
					<span class="sideValueHeadingText sideHeadingText"><?php echo strtoupper(lang('Nr of Plasma Farmer'))?></span>
				</div>

				<div class="sideValueHeadingText" style="margin-top:20px;padding-bottom: 15px;">
					<?php echo number_format($mill['EstimatedSmallholderFarmer'],0,".",",")?><br />
					<span class="sideValueHeadingText sideHeadingText"><?php echo strtoupper(lang('Nr of Smallholder Farmer'))?></span>
				</div>

				<div class="sideValueHeadingText" style="margin-top:20px;padding-bottom: 15px;border-bottom: 1px solid white;">
					<?php echo number_format($mill['Capacity'],0,".",",")?> TPH<br />
					<span class="sideValueHeadingText sideHeadingText"><?php echo strtoupper(lang('Production Capacity'))?></span>
				</div>
                -->

            <!-- left column (END) -->
        	</td>

        	<td id="mainColumn" valign="top">
            <!-- right column (BEGIN) -->

            	<table width="100%" style="min-height: 50px;">
                <tr>
                    <td width="80%" id="tdMainTitle" style="border-right: 1px dotted #233543;">
                        <?php echo strtoupper(lang('Palm Oil'));?><br />
                        <?php echo strtoupper(lang('Mill Profile'));?>
                    </td>
                    <td width="20%" id="tdBarcode">
                        <img src="<?php echo base_url() .  $qrcode_pic ?>" width="100" />
                    </td>
                </tr>
                </table>

                <table width="100%" style="margin-top:-10px;">
                    <tr>
                        <td width="33" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/profiles/basic-information-mills-icon.png" width="22" />
                        </td>
                        <td class="tdTitleMain" valign="top">
                           <?php echo strtoupper(lang('Basic Information')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px dotted #233543;" colspan="2" valign="top">

                        	<table width="100%" class="tabelValueMain">
                            <tr>
                                <td width="20%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('ID')) ?></div>
                                    <div class="tdValueMain"><?php echo strtoupper($mill['MillDisplayID']) ?></div>
                                </td>
                                <td width="60%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Nama')) ?></div>
                                    <div class="tdValueMain"><?php echo strtoupper($mill['MillName']) ?></div>
                                </td>
                                <td width="20%">
                                    <div class="tdLabelMain" style="font-size: 10px;"><?php echo strtoupper(lang('Year Established')) ?></div>
                                    <div class="tdValueMain"><?php echo strtoupper($mill['Year']) ?>&nbsp;</div>
                                </td>
                            </tr>
                        	</table>

                            <table width="100%" class="tabelValueMain">
                            <tr>
                                <td width="33%">
                                    <?php
                                    switch ($mill['Status']) {
                                        case '1': $legalStatus = lang('Sole Proprietorship'); break;
                                        case '2': $legalStatus = lang('Partnership'); break;
                                        case '3': $legalStatus = lang('Limited Partnership'); break;
                                        case '4': $legalStatus = lang('Limited Liability Company'); break;
                                        case '5': $legalStatus = lang('Corporation'); break;
                                        case '6': $legalStatus = lang('Cooperative'); break;
                                        case '7': $legalStatus = lang('Foundation'); break;
                                        case '8': $legalStatus = lang('Association'); break;
                                        case '9': $legalStatus = lang('State Owned'); break;
                                    }
                                    ?>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Legal Status')) ?></div>
                                    <div class="tdValueMain"><?php echo strtoupper($legalStatus) ?>&nbsp;</div>
                                </td>
                                <td width="34%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Nr of Staff')) ?></div>
                                    <div class="tdValueMain"><?php echo $NrOfStaff ?>&nbsp;</div>
                                </td>
                                <td width="33%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Province')) ?></div>
                                    <div class="tdValueMain"><?php echo $mill['Province'] ?>&nbsp;</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('District')) ?></div>
                                    <div class="tdValueMain"><?php echo $mill['District'] ?>&nbsp;</div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Kecamatan')) ?></div>
                                    <div class="tdValueMain"><?php echo $mill['SubDistrict'] ?>&nbsp;</div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Desa')) ?></div>
                                    <div class="tdValueMain"><?php echo $mill['Village'] ?>&nbsp;</div>
                                </td>
                            </tr>
                            </table>

                        </td>
                    </tr>
                </table>
                <br /><br />

                <table width="100%" style="margin-top:-10px;">
                    <tr>
                        <td width="33" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/profiles/mills-information-mills-icon.png" width="22" />
                        </td>
                        <td class="tdTitleMain" valign="top">
                           <?php echo strtoupper(lang('Mill Location & Capacity')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px dotted #233543;" colspan="2" valign="top">

                            <table width="100%" style="margin-top: 7px;">
                            <tr>
                                <td width="35%" valign="top">
                                    <table width="100%" class="tabelValueMain">
                                    <tr>
                                        <td>
                                            <div class="tdLabelMain"><?php echo strtoupper(lang('Production Capacity'))?> (TBH)</div>
                                            <div class="tdValueMain"><?php echo $mill['Capacity'] ?>&nbsp;</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tdLabelMain"><?php echo strtoupper(lang('Latitude')) ?></div>
                                            <div class="tdValueMain"><?php echo $mill['Latitude'] ?>&nbsp;</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tdLabelMain"><?php echo strtoupper(lang('Longitude')) ?></div>
                                            <div class="tdValueMain"><?php echo $mill['Longitude'] ?>&nbsp;</div>
                                        </td>
                                    </tr>
                                    </table>
                                </td>
                                <td width="65%" valign="top">
                                    <img style="width: 100%;" id="photoPetani" src="<?php echo base_url().'image_process/resizeOtfLandscape?imagenya='.urlencode($mill['LocationPhoto']).'&width=325&height=175&opsi=fotoMill&VID='.$mill['VillageID']; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" height="15"></td>
                            </tr>
                            <tr>
                                <td height="150" colspan="2" id="map_canvas_<?php echo $mill['MillID'] ?>" valign="top">&nbsp;</td>
                            </tr>
                            </table>

                        </td>
                    </tr>
                </table>
                <br />

                <table width="100%" style="margin-top:-4px;">
                    <tr>
                        <td width="33" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/profiles/mills-information-mills-icon.png" width="22" />
                        </td>
                        <td class="tdTitleMain" valign="top">
                           <?php echo strtoupper(lang('Training Participation')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px dotted #3789E0;" colspan="2" valign="top">
                            <table class="tabelListData" width="100%" border="0" style="margin-top: 7px;">
                            <tr>
                                <th style="width: 4%;">NO.</th>
                                <th><?php echo strtoupper(lang('Training'))?></th>
                                <th style="width: 14%;"><?php echo strtoupper(lang('Nr of Staff'))?></th>
                                <th style="width: 14%;"><?php echo strtoupper(lang('Start'))?></th>
                                <th style="width: 14%;"><?php echo strtoupper(lang('Selesai'))?></th>
                            </tr>
                            <?php
                            if($training[0]['Topic'] != ""){
                                for ($i=0; $i < count($training); $i++) {
                                    echo '<tr>
                                        <td align="center">'.($i+1).'.</td>
                                        <td align="left">'.$training[$i]['Topic'].'.</td>
                                        <td align="center">'.$training[$i]['NrOfStaff'].'</td>
                                        <td align="center">'.date('d M Y', strtotime($training[$i]['Start'])).'.</td>
                                        <td align="center">'.date('d M Y', strtotime($training[$i]['End'])).'.</td>
                                    </tr>';
                                }
                            }else{
                                echo '<tr><td colspan="5" align="center">'.lang('No Data').'</td></tr>';
                            }
                            ?>
                            </table>

                        </td>
                    </tr>
                </table>

            <!-- right column (END) -->
        	</td>
        </tr>
    	</table>

    	</div> <!--- MULAI TABEL UTAMA (END) -->

        <footer>
            <table class="tabelFooter" border="0" width="100%">
                <tr>
                    <td class="kolomKiri" width="30%">&nbsp;</td>
                    <td class="kolomKanan" width="70%" align="right">
                        <table width="100%">
                        <tr>
                            <td style="letter-spacing: 0.1em;font-size: 11px" width="60%">Copyright &copy <?php echo date('Y')?> by PT Koltiva</td>
                            <td style="letter-spacing: 0.1em;font-size: 11px" width="40%" align="right">www.koltiva.com</td>
                        </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </footer>

	</div>

    <?php if(count($traceability_details) > 0) { ?>
    <div class="page">

    <table width="100%" border="0" cellpadding="2">
        <tr>
            <?php
                for($i=0;$i<count($logos);$i++){
                    if($logos[$i]['Photo']!=''){
            ?>
                <td height="70px" width="20%" align="center" style="vertical-align:middle;">
                    <img src="<?= base_url() ?>images/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
                </td>
            <?php
                    }
                }
            ?>
        </tr>
    </table>

    <!--- MULAI TABEL UTAMA (BEGIN) -->
    <div id="mainContainer" style="background-image: none !important;">
    <br>
    <table width="100%" style="margin-bottom: 8px;margin-top:-15px;">
        <tr>
            <td width="4%" valign="top">
            <img src="<?php echo base_url() ?>assets/css/profiles/mills-information-mills-icon.png" width="22" />
            </td>
            <td class="tdTitleMain" valign="top">
                <?php echo strtoupper(lang('CURRENT YEAR TRACEABLE AND CERTIFIED COCOA SALES DETAIL'))?>
            </td>
        </tr>
    </table>

    <!-- MAX 20 Baris disini -->
    <table class="tabelListData" width="100%" border="0">
        <tr>
            <th width="5%">No.</th>
            <th><?php echo strtoupper(lang('Bsatch ID'))?></th>
            <th><?php echo strtoupper(lang('Transaction Date'))?></th>
            <th><?php echo strtoupper(lang('Nett Weight'))?> (kg)</th>
            <th><?php echo strtoupper(lang('FFB'))?></th>
            <th><?php echo strtoupper(lang('From'))?></th>
        </tr>
        <?php
            $increData = 1;
            for ($i=0; $i < count($traceability_details); $i++) {
                if($i == 40 OR ($i>40 and ($i-40)%40==0) and $i < count($traceability_details)){
                    //$page++;
                    echo '</table>

                    </div><footer>
                        <table class="tabelFooter" border="0" width="100%">
                            <tr>
                                <td class="kolomKiri" width="30%">&nbsp;</td>
                                <td class="kolomKanan" width="70%" align="right">
                                    <table width="100%">
                                    <tr>
                                        <td style="letter-spacing: 0.1em;font-size: 11px" width="60%">Copyright &copy '.date('Y').' by PT Koltiva</td>
                                        <td style="letter-spacing: 0.1em;font-size: 11px" width="40%" align="right">www.koltiva.com</td>
                                    </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </footer>
                    </div>
                    <div class="page">
                        <table width="100%" border="0" cellpadding="2">
                            <tr>';
                                
                            for($j=0;$j<count($logos);$j++){
                                if($logos[$j]['Photo']!=''){
                                    echo '<td height="70px" width="20%" align="center" style="vertical-align:middle;">
                                    <img src="'.base_url().'images/'.$logos[$j]['Photo'] .'" style="max-width:90%; max-height:90%; max-width:120px;">
                                    </td>';
                        
                                }
                            }
                                
                        echo '</tr>
                        </table>
                        <div id="mainContainer" style="background-image: none !important;">
                        <br>
                        <table width="100%" style="margin-bottom: 8px;margin-top:-15px;">
                            <tr>
                                <td width="4%" valign="top">
                                    <img src="'.base_url() .'assets/css/print_beneficiary/farm-data-icon.png" width="22" />
                                </td>
                                <td class="tdTitleMain" valign="top">
                                    '.strtoupper(lang('CURRENT YEAR TRACEABLE AND CERTIFIED COCOA SALES DETAIL')).'
                                </td>
                            </tr>
                        </table>

                        
                        <table class="tabelListData" width="100%" border="0">
                            <tr>
                                <th width="5%">No.</th>
                                <th>'.strtoupper(lang('Bsatch ID')).'</th>
                                <th>'.strtoupper(lang('Transaction Date')).'</th>
                                <th>'.strtoupper(lang('Nett Weight')).' (kg)</th>
                                <th>'.strtoupper(lang('FFB')).'</th>
                                <th>'.strtoupper(lang('From')).'</th>
                            </tr>
                    ';
                                                
                }
                
                echo '
                    <tr>
                        <td align="center">'.$increData.'</td>
                        <td align="center">'.$traceability_details[$i]['BatchID'].'</td>
                        <td align="center">'.$traceability_details[$i]['DateTransaction'].'</td>
                        <td align="center">'.number_format($traceability_details[$i]['VolumeNetto'],2,".",",").'</td>
                        <td align="center">'.$traceability_details[$i]['FFB'].'</td>
                        <td align="center">'.$traceability_details[$i]['Delivered'].'</td>
                    </tr>
                    ';
                $increData++;
            }
            ?>
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
                        <td style="letter-spacing: 0.1em;font-size: 11px" width="60%">Copyright &copy <?php echo date('Y')?> by PT Koltiva</td>
                        <td style="letter-spacing: 0.1em;font-size: 11px" width="40%" align="right">www.koltiva.com</td>
                    </tr>
                    </table>
                </td>
            </tr>
        </table>
    </footer>
    </div>
    <?php } ?>

<?php if ($mill['Latitude'] != "" && $mill['Longitude'] != ""): ?>

<script type="text/javascript">
   var icon_path = '<?php echo base_url() ?>assets/css/profiles/';
   $('#map_canvas_<?php echo $mill['MillID'] ?>').gmap3({
        map: {
            options: {
                center: [<?php echo $mill['Latitude'] ?>, <?php echo $mill['Longitude'] ?>],
                zoom: 11,
                mapTypeControl: false,
                panControl: false,
                zoomControl: false,
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
                {latLng:[<?php echo $mill['Latitude'] ?>, <?php echo $mill['Longitude'] ?>], options: {icon: icon_path + "mills-information-mills-icon-map.png"}},
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
        $('#map_canvas_<?php echo $mill['MillID'] ?>').html('<div style="margin: 90px auto 0px; text-align: center; color: #aaaaaa;">No Map</div>');
    </script>
<?php endif ?>