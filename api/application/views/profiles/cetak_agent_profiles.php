<?php

/**
 * @Author: nikolius
 * @Date:   2017-12-29 16:46:38
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-01-17 12:03:53
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
        <br /><br />
        <?php
            if($this->awsfileupload->doesObjectExist($agent['Photo']) == true) {
                $Photo  = $this->config->item('CTCDN')."/".$agent['Photo'];
            }else{
                $Photo  = base_url().'image_process/resizeOtfLandscape?imagenya='.urlencode($agent['Photo']).'&width=300&height=300&opsi=fotoTrader&VID='.$agent['VillageID'].'&gen='.$agent['Gender'];
            }               
        ?>
        <!--- MULAI TABEL UTAMA (BEGIN) -->
        <div id="mainContainer">

            <table id="tableMainContainer" width="100%" height="975">
            <tr>
               	<td valign="top" id="sideColumn">
                <!-- side column (BEGIN) -->

                	<table width="100%" id="tabelDivSide" style="margin-bottom:12px;">
    				<tr>
    					<td align="center">
    						<img id="photoPetani" src="<?=$Photo?>" />
    					</td>
    				</tr>
    				<tr height="20">
    					<td>&nbsp;</td>
    				</tr>
    				<tr>
                        <td>
                            <div class="divSide" id="titleSideColum">
                                <?php echo strtoupper(lang('Business Transactions'));?><br />
                                <span class="smallerHeader">(<?php echo date('Y'); ?>)</span>
                            </div>

                        </td>
                    </tr>
    				</table>

    				<div class="sideTabelHeader"><?php echo strtoupper(lang('FFB Sales'))?></div>
    				<table class="tabelListDataSide" width="100%" border="0" style="margin-bottom:12px;">
    				<tr style="background-color: #2A6EA6;">
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

    				<div class="sideTabelHeader"><?php echo strtoupper(lang('FFB Suppliers'))?></div>
    				<table class="tabelListDataSide" width="100%" border="0" style="margin-bottom:12px;">
    				<tr style="background-color: #2A6EA6;">
                        <th style="width: 20%;">&nbsp;</th>
                        <th style="width: 20%;">Q1</th>
                        <th style="width: 20%;">Q2</th>
                        <th style="width: 20%;">Q3</th>
                        <th style="width: 20%;">Q4</th>
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
                    ?>

                    <div class="divSide" id="titleSideColum" style="margin-bottom: 10px;padding-top: 5px;">
                        <?php echo strtoupper(lang('Traceability Summary'));?>
                    </div>
                    <div class="divSide centerPos" style="margin-left: 12px;">
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
                            <td class="tdValue"><?php echo number_format($tsFarmer) ?></td> 
                            <td class="tdValue"><?php echo number_format($tsTrans) ?></td>
                         </tr>
                         <tr>
                            <td class="tdDesc"><?php echo strtoupper('Farmer') ?></td>
                            <td class="tdDesc"><?php echo strtoupper('Transactions') ?></td>
                         </tr>
                         <tr height="11">
                            <td colspan="2">&nbsp;</td>
                         </tr>
                      </table>
                   </div>

                    <!--
    				<br />
    				<div class="sideValueHeading" style="padding-bottom: 10px;border-bottom: 1px solid white;margin-bottom: 10px;">
    					<?php echo strtoupper(lang('Sales Monitoring Log'))?>
    				</div>

    				<table class="tabelListData" width="100%" border="0">
                    <tr>
                        <th style="width: 35%;">DATE</th>
                        <th style="width: 65%;">STATUS</th>
                    </tr>
                    <tr>
                    	<td>11 Dec 2017</td>
                    	<td>
                    		<label><input type="checkbox">Active</label>
                    		&nbsp;
                    		<label><input type="checkbox">Inactive</label>
                    	</td>
                    </tr>
                    <tr>
                    	<td>12 Dec 2017</td>
                    	<td>
                    		<label><input type="checkbox">Active</label>
                    		&nbsp;
                    		<label><input type="checkbox">Inactive</label>
                    	</td>
                    </tr>
                    <tr>
                    	<td>13 Dec 2017</td>
                    	<td>
                    		<label><input type="checkbox">Active</label>
                    		&nbsp;
                    		<label><input type="checkbox">Inactive</label>
                    	</td>
                    </tr>
                    <tr>
                    	<td>14 Dec 2017</td>
                    	<td>
                    		<label><input type="checkbox">Active</label>
                    		&nbsp;
                    		<label><input type="checkbox">Inactive</label>
                    	</td>
                    </tr>
                    </table>
                    -->

    			<!-- side column (END) -->
    			</td>

    			<td id="mainColumn" valign="top">
                <!-- right column (BEGIN) -->

                	<table width="100%" style="min-height: 50px;">
                    <tr>
                        <td width="80%" id="tdMainTitle" style="border-right: 1px dotted #3E82E6;">
                            <?php echo strtoupper(lang('Palm Oil'));?><br />
                            <?php echo strtoupper(lang('SME Profile'));?>
                        </td>
                        <td width="20%" id="tdBarcode">
                        	<img src="<?php echo base_url() .  $qrcode_pic ?>" width="100" />
                        </td>
                    </tr>
                    </table>

                    <table width="100%" style="margin-top:-10px;">
                        <tr>
                            <td width="33" valign="top">
                               <img src="<?php echo base_url() ?>assets/css/profiles/business-owner-icon.png" width="22" />
                            </td>
                            <td class="tdTitleMain" valign="top">
                               <?php echo strtoupper(lang('Business Owner')) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top: 1px dotted #3789E0;" colspan="2" valign="top">

                            	<table width="100%" class="tabelValueMain">
                                <tr>
                                    <td width="17%">
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('ID')) ?></div>
                                        <div class="tdValueMain"><?php echo strtoupper($agent['MemberDisplayID']) ?></div>
                                    </td>
                                    <td width="30%">
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Nama')) ?></div>
                                        <div class="tdValueMain"><?php echo strtoupper($agent['MemberName']) ?></div>
                                    </td>
                                    <td width="31%">
                                        <?php
                                            $now        = new Datetime('now');
                                            $birthdate  = new Datetime($agent['DateOfBirth']);
                                            $age        = $now->diff($birthdate);
                                        ?>
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('age')) ?></div>
                                        <div class="tdValueMain"><?php echo $age->y ?>&nbsp;</div>
                                    </td>
                                    <td width="22%">
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Gender')) ?></div>
                                        <div class="tdValueMain">
                                         <?php
                                            if($agent['Gender']=='m'){
                                               echo lang('Male');
                                            }else{
                                               echo lang('Female');
                                            }
                                         ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Provinsi')) ?></div>
                                        <div class="tdValueMain"><?php echo strtoupper($agent['Province']) ?>&nbsp;</div>
                                    </td>
                                    <td>
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('District')) ?></div>
                                        <div class="tdValueMain"><?php echo $agent['District'] ?>&nbsp;</div>
                                    </td>
                                    <td>
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Sub District')) ?></div>
                                        <div class="tdValueMain"><?php echo $agent['SubDistrict'] ?>&nbsp;</div>
                                    </td>
                                    <td>
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Village')) ?></div>
                                        <div class="tdValueMain"><?php echo $agent['Village'] ?>&nbsp;</div>
                                    </td>
                                </tr>
                            	</table>

                            </td>
                        </tr>
                    </table>
                    <br />

                    <table width="100%" style="margin-top:-10px;">
                        <tr>
                            <td width="33" valign="top">
                               <img src="<?php echo base_url() ?>assets/css/profiles/business-information-icon.png" width="22" />
                            </td>
                            <td class="tdTitleMain" valign="top">
                               <?php echo strtoupper(lang('Business Information')) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top: 1px dotted #3789E0;" colspan="2" valign="top">

                            	<?php
                            	//agLegalStatusCompany
                            	switch ($agent['agLegalStatusCompany']) {
                            		case '1': $agLegalStatusCompany = lang('Sole Proprietorship'); break;
                            		case '2': $agLegalStatusCompany = lang('Partnership'); break;
                            		case '3': $agLegalStatusCompany = lang('Limited Partnership'); break;
                            		case '4': $agLegalStatusCompany = lang('Limited Liability Company'); break;
                            		case '5': $agLegalStatusCompany = lang('Corporation'); break;
                            		case '6': $agLegalStatusCompany = lang('Cooperative'); break;
                            		case '7': $agLegalStatusCompany = lang('Foundation'); break;
                            		case '8': $agLegalStatusCompany = lang('Association'); break;
                            		case '9': $agLegalStatusCompany = lang('State Owned'); break;
                            	}
                            	?>
                            	<table width="100%" class="tabelValueMain">
                                <tr>
                                    <td width="33%">
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Year Established')) ?></div>
                                        <div class="tdValueMain"><?php echo strtoupper($agent['agYearEstablished']) ?>&nbsp;</div>
                                    </td>
                                    <td width="34%">
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Legal Status')) ?></div>
                                        <div class="tdValueMain"><?php echo strtoupper($agLegalStatusCompany) ?>&nbsp;</div>
                                    </td>
                                    <td width="33%">
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Latitude')) ?></div>
                                        <div class="tdValueMain"><?php echo $agent['Latitude'] ?>&nbsp;</div>
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="2">
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('SME Role')) ?></div>
                                        <div class="tdValueMain"><?php echo strtoupper($agent['RoleLabel']) ?>&nbsp;</div>
                                    </td>
                                    <td>
                                        <div class="tdLabelMain"><?php echo strtoupper(lang('Longitude')) ?></div>
                                        <div class="tdValueMain"><?php echo $agent['Longitude']?>&nbsp;</div>
                                    </td>
                                </tr>
                            	</table>

                            </td>
                        </tr>
                    </table>
                    <br />

                    <table width="100%" style="margin-top:-10px;">
                        <tr>
                            <td valign="top" width="35%">
                            	<img id="photoPetani" src="<?php echo base_url().'image_process/resizeOtfLandscape?imagenya='.urlencode($agent['agBusinessLocation']).'&width=225&height=175&opsi=agentBisnisLocation&VID='.$agent['VillageID']; ?>" />
                            </td>
                            <td id="map_canvas_<?php echo $agent['MemberID'] ?>" valign="top">&nbsp;</td>
                        </tr>
                    </table>

                    <table width="100%" style="margin-top: 16px;">
                    <tr>
                        <td width="57%">
                            <div class="boxInfoKanan">
                                <table width="100%">
                                <tr>
                                    <td width="28%">
                                        <div class="boxInfoKanan_tdValue"><?php echo $staff['NrOfStaff']?></div>
                                        <div class="boxInfoKanan_tdDesc"><?php echo lang('Staff SME')?></div>
                                    </td>
                                    <td width="36%">
                                        <img style="float:left;margin-right: 3px;" src="<?php echo base_url() ?>assets/css/profiles/male-staff.png" width="25">
                                        <div class="boxInfoKanan_tdValue"><?php echo $staff['StaffLaki']?> %</div>
                                        <div class="boxInfoKanan_tdDesc"><?php echo lang('Male')?></div>
                                    </td>
                                    <td width="36%">
                                        <img style="float:left;margin-right: 3px;" src="<?php echo base_url() ?>assets/css/profiles/female-staff.png" width="25">
                                        <div class="boxInfoKanan_tdValue"><?php echo $staff['StaffPerempuan']?> %</div>
                                        <div class="boxInfoKanan_tdDesc"><?php echo lang('Female')?></div>
                                    </td>
                                </tr>
                                </table>
                            </div>
                        </td>
                        <td width="3%">&nbsp;</td>
                        <td width="40%">
                            <div class="boxInfoKanan">
                                <table width="100%">
                                <tr>
                                    <td>
                                        <img style="float:left;margin-right: 10px;" src="<?php echo base_url() ?>assets/css/profiles/vehicle.png" width="75">
                                        <div class="boxInfoKanan_tdValue"><?php echo $NrOfVehicle?></div>
                                        <div class="boxInfoKanan_tdDesc"><?php echo lang('Nr of Vehicles')?></div>
                                    </td>
                                </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </table>

                    <table width="100%" style="margin-top:16px;">
                        <tr>
                            <td width="33" valign="top">
                               <img src="<?php echo base_url() ?>assets/css/profiles/business-information-icon.png" width="22" />
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

		</div>

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
                <img src="<?php echo base_url() ?>assets/css/print_beneficiary/farm-data-icon.png" width="22" />
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
            <th><?php echo strtoupper(lang('Delivered'))?></th>
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
                                <th>'.strtoupper(lang('Delivered')).'</th>
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

<?php if ($agent['Latitude'] != "" && $agent['Longitude'] != ""): ?>

<script type="text/javascript">
   var icon_path = '<?php echo base_url() ?>assets/css/profiles/';
   $('#map_canvas_<?php echo $agent['MemberID'] ?>').gmap3({
        map: {
            options: {
                center: [<?php echo $agent['Latitude'] ?>, <?php echo $agent['Longitude'] ?>],
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
                {latLng:[<?php echo $agent['Latitude'] ?>, <?php echo $agent['Longitude'] ?>], options: {icon: icon_path + "business-owner-icon-map.png"}},
            ],
            options:{
                draggable: false
            }
        }
   });
</script>

<?php else: ?>
    <script type="text/javascript">
        var tpl = "";
        $('#map_canvas_<?php echo $agent['MemberID'] ?>').html('<div style="margin: 90px auto 0px; text-align: center; color: #aaaaaa;">No Map</div>');
    </script>
<?php endif ?>

<!--                HALAMAN MAP (BEGIN)              -->
<?php if($gardens_coordinate_exists == true){?>

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

                <div style="height:400px;width:100%;margin:0;padding:0;" id="map_garden_canvas_<?php echo $agent['MemberID'] ?>"></div>

            </td>
        </tr>
        </table>
    
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
<script type="text/javascript">
    var imageMarker = "<?php echo base_url() ?>assets/css/print_beneficiary/basic-data-icon-map.png";
    var Lokasinya = [
        <?php
        for ($i=0; $i < count($gardens); $i++) {
            echo '{data:"Farm Nr : '.$gardens[$i]['PlotNr'].'",latLng:['.$gardens[$i]['Latitude'].','.$gardens[$i]['Longitude'].'],options: {icon: imageMarker}}';

            if($i != (count($gardens) -1)){
                echo ',';
            }
        }
        ?>
    ];
    console.log(Lokasinya);
    $('#map_garden_canvas_<?php echo $agent['MemberID'] ?>').gmap3({
        map: {
            options: {
                center: [<?php echo $gardens[0]['Latitude'] ?>, <?php echo $gardens[0]['Longitude'] ?>],
                zoom: 12,
                mapTypeControl: true,
                panControl: true,
                zoomControl: true,
                streetViewControl: true,
                rotateControl: true,
                rotateControlOptions: true,
                overviewMapControl: true,
                OverviewMapControlOptions: true,
                scrollwheel: true,
                mapTypeId: google.maps.MapTypeId.HYBRID,
                disableDefaultUI: true
            }
        },
        marker:{
            values:Lokasinya,
            options:{
                draggable: false
            },
            events:{
                mouseover: function(marker, event, context){
                    var map = $(this).gmap3("get"),
                    infowindow = $(this).gmap3({get:{name:"infowindow"}});
                    if (infowindow){
                        infowindow.open(map, marker);
                        infowindow.setContent(context.data);
                    } else {
                        $(this).gmap3({
                            infowindow:{
                                anchor:marker, 
                                options:{content: context.data}
                            }
                        });
                    }
                },
                mouseout: function(){
                    var infowindow = $(this).gmap3({get:{name:"infowindow"}});
                    if (infowindow){
                        infowindow.close();
                    }
                }
            }
        }
    });
</script>
<?php }?>
<!--                HALAMAN MAP (END)              -->