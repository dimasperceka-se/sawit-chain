<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-13 10:22:32
 */
?>
<div id="templatemo_container_wrapper">
    <div class="page">

        <table width="100%" border="0" cellpadding="2">
           <tr>
              <td width="20%" align="left" style="vertical-align:middle;">
                 <img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
              </td>
            <?php
              for($i=0;$i<count($logos);$i++){
                 if($logos[$i]['Photo']!=''){
            ?>
              <td height="60px" width="20%" align="left" style="vertical-align:middle;">
                 <img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
              </td>
            <?php
                 }
              }
            ?>
           <td width="20%" align="right" style="vertical-align:middle;">
              <img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
           </td>
        </tr>
        </table>
        <br /><br />

        <!--- MULAI TABEL UTAMA (BEGIN) -->
        <div id="mainContainer">

        <table id="tableMainContainer" width="100%">
            <tr>
                <td valign="top" id="sideColumn">
                <!-- side column (BEGIN) -->

                <table width="100%" id="tabelDivSide">
                    <tr>
                        <td align="center">
                           <img id="photoPetani" src="<?php echo base_url().'image_process/resizeOtfByType?imagenya='.urlencode($manager['foto']).'&width=179&height=0&tipe='.$nursery['ResponsibleType']; ?>" />
                        </td>
                     </tr>
                     <tr height="20">
                        <td>&nbsp;</td>
                     </tr>
                     <tr>
                        <td>
                            <div class="divSide" id="titleSideColum">
                              <?php echo strtoupper(lang('PriNur Nursery Transaction'))?>
                           </div>

                           <div class="divSide titleh3SideColumn" style="border-bottom:none;">
                              <?php echo $nursery['displayYearRangeTrans']?>
                           </div>

                            <table class="tabelListDataSideColumn" width="100%" border="0">
                            <tr>
                                <th><?php echo lang('PriNur Sold Seedlings')?></th>
                                <th><?php echo lang('PriNur Unit Price')?></th>
                                <th>Total</th>
                            </tr>
                            <?php
                            foreach ($nursery_transaction as $key => $value){
                                echo '<tr>
                                    <td align="center">'.number_format($value['Volume'], 0, '.', ',').'</td>
                                    <td align="center">'.number_format($value['Price'], 0, '.', ',').'</td>
                                    <td align="right">'.number_format($value['Total'], 0, '.', ',').'</td>
                                </tr>';
                            }
                            ?>
                            </table>

                            <?php if($bahasanya == "english"){?>
                                <p class="sideColumnTextDescription">* The last <?php echo $nursery['countLastTrans']?> transactions from <?php echo $nursery['jumlahTransaksi']?> transactions</p>
                            <?php }else{?>
                                <p class="sideColumnTextDescription">* <?php echo $nursery['countLastTrans']?> transaki terakhir dari <?php echo $nursery['jumlahTransaksi']?> transaksi</p>
                            <?php }?>
                            <div class="sideHorizontalLine" style="margin-bottom:15px;"></div>


                            <table class="tabelSideTotal" width="100%" border="0">
                                <tr class="trValue">
                                    <td width="35%"><?php echo $nursery['volumePerYear']?></td>
                                    <td width="30%"><?php echo $nursery['avrPrice']?></td>
                                    <td width="35%">IDR <?php echo $nursery['milPerYear']?></td>
                                </tr>
                                <tr class="trText">
                                    <td><?php echo lang('PriNur Total Sold Seedlings / Year')?></td>
                                    <td><?php echo lang('PriNur Average Unit Price')?></td>
                                    <td><?php echo lang('PriNur Million / Year')?></td>
                                </tr>
                            </table>

                            <br />

                            <div class="divSide" id="titleSideColum">
                                <?php echo strtoupper(lang('PriNur Nursery Monitoring Log'))?>
                            </div>

                            <table style="margin-top:10px;" class="tabelListDataSideColumn" width="100%" border="0">
                            <tr>
                                <th><?php echo lang('Tanggal')?></th>
                                <th>Status</th>
                            </tr>
                            <?php
                            foreach ($nursery_monitoring as $key => $value){
                                echo '<tr>
                                    <td align="left">'.$value['tanggalNya'].'</td>
                                    <td align="center">'.lang($value['statusNya']).'</td>
                                </tr>';
                            }
                            ?>
                            </table>

                        </td>
                     </tr>
                </table>

                <!-- side column (END) -->
                </td>
                <td id="mainColumn" valign="top">
                <!-- right column (BEGIN) -->

                    <table width="100%" style="min-height: 45px;">
                    <tr>
                        <td width="82%" id="tdMainTitle" style="border-right: 1px dotted #11B1A7;">
                            <?php echo strtoupper(lang('PriNur Nursery Profile'))?>
                        </td>
                        <td width="18%" id="tdBarcode">
                            <img src="<?php echo base_url() ?>index.php/farmer/qrcode_generator/<?php echo $owner['owner_id'];?>/" />
                        </td>
                    </tr>
                    </table>

                    <table width="100%" style="margin-top:-25px;" border="0">
                    <tr>
                        <td width="2%" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/nursery_summary/icon-farmer-data.png" width="25" />
                        </td>
                        <td width="98%" class="tdTitleMain" valign="top">
                           <?php echo strtoupper(lang('PriNur Nursery Owner'))?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top">

                            <table width="100%" class="tabelValueMain">
                            <tr>
                                <td width="17%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Jenis')) ?></div>
                                    <div class="tdValueMain"><?php echo strtoupper($ObjTypeLabel) ?></div>
                                </td>
                                <td width="30%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Owner ID')) ?></div>
                                    <div class="tdValueMain"><?php echo strtoupper($owner['owner_id']) ?></div>
                                </td>
                                <td width="25%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Owner Name')) ?></div>
                                    <div class="tdValueMain"><?php echo $owner['owner_name']; ?></div>
                                </td>
                                <td width="28%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Provinsi')) ?></div>
                                    <div class="tdValueMain"><?php echo $owner['propinsi'] ?>&nbsp;</div>
                                </td>
                            </tr>
                            </table>

                            <table width="100%" class="tabelValueMain" style="margin-bottom:5px;">
                            <tr>
                                <td width="33%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('District')) ?></div>
                                    <div class="tdValueMain"><?php echo $owner['district']; ?>&nbsp;</div>
                                </td>
                                <td width="33%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Sub District')) ?></div>
                                    <div class="tdValueMain"><?php echo $owner['kecamatan']; ?>&nbsp;</div>
                                </td>
                                <td width="34%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Village')) ?></div>
                                    <div class="tdValueMain"><?php echo $owner['desa']; ?>&nbsp;</div>
                                </td>
                            </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td width="2%" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/nursery_summary/icon-farmer-data.png" width="25" />
                        </td>
                        <td width="98%" class="tdTitleMain" valign="top">
                           <?php echo strtoupper(lang('PriNur Nursery Manager'))?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top">
                            <table width="100%" class="tabelValueMain">
                            <tr>
                                <td width="40%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Nama')) ?></div>
                                    <div class="tdValueMain"><?php echo $manager['nama'] ?></div>
                                </td>
                                <td width="10%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('age')) ?></div>
                                    <?php
                                    $now        = new Datetime('now');
                                    $birthdate  = new Datetime($manager['tgl_lahir']);
                                    $age        = $now->diff($birthdate);
                                    $ageLabel = $age->y;
                                    ?>
                                    <div class="tdValueMain"><?php if($ageLabel=="0") echo '-'; else echo $ageLabel; ?></div>
                                </td>
                                <td width="25%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Phone')) ?></div>
                                    <div class="tdValueMain"><?php echo $manager['telp']; ?>&nbsp;</div>
                                </td>
                                <td width="25%">
                                    <?php
                                    switch ($manager['jk']) {
                                        case 'm':
                                            $jk = lang('Male');
                                        break;
                                        case 'f':
                                            $jk = lang('Female');
                                        break;
                                    }
                                    ?>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Gender')) ?></div>
                                    <div class="tdValueMain"><?php echo $jk; ?>&nbsp;</div>
                                </td>
                            </tr>
                            </table>
                        </td>
                    </tr>
                    </table>

                    <table class="tabelRightNurseryCount" width="100%" border="0" style="margin-top:3px;">
                    <tr>
                        <td width="61%">&nbsp;</td>
                        <td align="right" width="34%"><?php echo strtoupper(lang('PriNur Nursery No.'))?>&nbsp;</td>
                        <td align="center" style="background-color: #11B1A7;color:white;" width="5%"><?php echo $nursery['NurseryNr']?></td>
                    </tr>
                    </table>

                    <table width="100%" style="margin-top:-25px;" border="0">
                    <tr>
                        <td width="2%" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/nursery_summary/icon-nursery-information.png" width="25" />
                        </td>
                        <td width="98%" class="tdTitleMain" valign="top">
                           <?php echo strtoupper(lang('PriNur Nursery Information'))?>
                        </td>
                    </tr>
                    </table>

                    <table style="margin-top:4px;" class="tabelListDataSmallerFont" width="100%" border="0">
                    <tr>
                        <th style="vertical-align:bottom;" ><?php echo strtoupper(lang('Tanggal Berdiri'))?></th>
                        <th style="vertical-align:bottom;"><?php echo strtoupper(lang('Certification'))?></th>
                        <th style="vertical-align:bottom;"><?php echo strtoupper(lang('Lahan'))?> (M2)</th>
                        <th style="vertical-align:bottom;"><?php echo strtoupper(lang('Kapasitas'))?></th>
                        <th style="vertical-align:bottom;">LAT</th>
                        <th style="vertical-align:bottom;">LONG</th>
                    </tr>
                    <?php
                        $noUrut = 1;
                        foreach ($nursery_list as $key => $value){
                            if($value['Area'] == ""){
                                $areaNya = $value['pjgLebarM2'];
                            }else{
                                $areaNya = $value['Area'];
                            }

                            echo '<tr>
                                <td>'.$value['fDate'].'</td>
                                <td align="center">'.$value['CertificationStatus'].'</td>
                                <td align="center">'.number_format($areaNya, 0, '.', ',').'</td>
                                <td align="center">'.number_format($value['Kapasitas'], 0, '.', ',').'</td>
                                <td align="center">'.$value['lat'].'</td>
                                <td align="center">'.$value['long'].'</td>
                            </tr>';
                            $noUrut++;
                        }
                    ?>
                    </table>

                    <br />

                    <table class="tabelFotoNursery" width="525" height="100" border="0" style="margin-top:-15px;">
                    <tr>
                        <td width="150" valign="middle" style="background-color:#E9E7E3;">
                            <img id="photoNursery" src="<?php echo base_url().'image_process/resizeOtfNursery?imagenya='.$nursery['Photo'].'&width=150&height=100'; ?>" />
                        </td>
                        <td width="375">
                            <div id="map_canvas" style="width: 100%; height: 100px;"></div>
                        </td>
                    </tr>
                    </table>

                    <br />

                    <table width="100%" style="margin-top:-20px;" border="0">
                    <tr>
                        <td width="2%" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/nursery_summary/icon-nursery-checklist.png" width="25" />
                        </td>
                        <td width="98%" class="tdTitleMain" valign="top">
                           <?php echo strtoupper(lang('PriNur Nursery Checklist'))?>
                        </td>
                    </tr>
                    </table>

                    <table style="margin-top:4px;" class="tabelListDataSmallerFont" width="100%" border="0">
                    <tr>
                        <th width="53%" style="vertical-align:bottom;"><?php echo strtoupper(lang('PriNur Key Quality Attribute'))?> :</th>
                        <th width="18%" style="vertical-align:bottom;" ><?php echo strtoupper(lang('PriNur Yes / No'))?></th>
                        <th width="29%" style="vertical-align:bottom;text-align:left;" ><?php echo strtoupper(lang('PriNur If No, Justification'))?></th>
                    </tr>
                    <tr>
                        <td><?php echo lang('Location with good access to main roads')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['LocationCloseToCommunity']) {
                                case 'Yes':
                                    $checkedTanya1Yes = 'checked="checked"';
                                    $checkedTanya1No = '';
                                break;
                                case 'No':
                                    $checkedTanya1Yes = '';
                                    $checkedTanya1No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya1Yes = '';
                                    $checkedTanya1No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya1" value="1" <?php echo $checkedTanya1Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya1" value="2" <?php echo $checkedTanya1No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['LocationCloseToCommunityNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Flat, well drained and uniform land area')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['GoodLandArea']) {
                                case 'Yes':
                                    $checkedTanya2Yes = 'checked="checked"';
                                    $checkedTanya2No = '';
                                break;
                                case 'No':
                                    $checkedTanya2Yes = '';
                                    $checkedTanya2No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya2Yes = '';
                                    $checkedTanya2No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya2" value="1" <?php echo $checkedTanya2Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya2" value="2" <?php echo $checkedTanya2No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['GoodLandAreaNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Located at least 100 metres from cocoa plantations')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['LocationNearCocoaFarm']) {
                                case 'Yes':
                                    $checkedTanya3Yes = 'checked="checked"';
                                    $checkedTanya3No = '';
                                break;
                                case 'No':
                                    $checkedTanya3Yes = '';
                                    $checkedTanya3No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya3Yes = '';
                                    $checkedTanya3No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya3" value="1" <?php echo $checkedTanya3Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya3" value="2" <?php echo $checkedTanya3No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['LocationNearCocoaFarmNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Continuous water supply available')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['ContinuousWaterSupply']) {
                                case 'Yes':
                                    $checkedTanya4Yes = 'checked="checked"';
                                    $checkedTanya4No = '';
                                break;
                                case 'No':
                                    $checkedTanya4Yes = '';
                                    $checkedTanya4No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya4Yes = '';
                                    $checkedTanya4No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya4" value="1" <?php echo $checkedTanya4Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya4" value="2" <?php echo $checkedTanya4No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['ContinuousWaterSupplyNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Irrigation system installed')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['IrrigationInstalled']) {
                                case 'Yes':
                                    $checkedTanya5Yes = 'checked="checked"';
                                    $checkedTanya5No = '';
                                break;
                                case 'No':
                                    $checkedTanya5Yes = '';
                                    $checkedTanya5No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya5Yes = '';
                                    $checkedTanya5No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya5" value="1" <?php echo $checkedTanya5Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya5" value="2" <?php echo $checkedTanya5No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['IrrigationInstalledNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Use of appropriate shading')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['UseShadingNet']) {
                                case 'Yes':
                                    $checkedTanya6Yes = 'checked="checked"';
                                    $checkedTanya6No = '';
                                break;
                                case 'No':
                                    $checkedTanya6Yes = '';
                                    $checkedTanya6No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya6Yes = '';
                                    $checkedTanya6No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya6" value="1" <?php echo $checkedTanya6Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya6" value="2" <?php echo $checkedTanya6No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['UseShadingNetNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Adequate supply of top soil or substrate for potting mix')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['AdequateSupplyTopSoil']) {
                                case 'Yes':
                                    $checkedTanya7Yes = 'checked="checked"';
                                    $checkedTanya7No = '';
                                break;
                                case 'No':
                                    $checkedTanya7Yes = '';
                                    $checkedTanya7No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya7Yes = '';
                                    $checkedTanya7No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya7" value="1" <?php echo $checkedTanya7Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya7" value="2" <?php echo $checkedTanya7No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['AdequateSupplyTopSoilNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Improved varieties from certified seed and budwood sources')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['ImprovedVariety']) {
                                case 'Yes':
                                    $checkedTanya8Yes = 'checked="checked"';
                                    $checkedTanya8No = '';
                                break;
                                case 'No':
                                    $checkedTanya8Yes = '';
                                    $checkedTanya8No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya8Yes = '';
                                    $checkedTanya8No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya8" value="1" <?php echo $checkedTanya8Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya8" value="2" <?php echo $checkedTanya8No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['ImprovedVarietyNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Correct equipment is available to operator(s)')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['CorrectEquipment']) {
                                case 'Yes':
                                    $checkedTanya9Yes = 'checked="checked"';
                                    $checkedTanya9No = '';
                                break;
                                case 'No':
                                    $checkedTanya9Yes = '';
                                    $checkedTanya9No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya9Yes = '';
                                    $checkedTanya9No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya9" value="1" <?php echo $checkedTanya9Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya9" value="2" <?php echo $checkedTanya9No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['CorrectEquipmentNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Wind break installed (if needed)');?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['WindBreakInstalled']) {
                                case 'Yes':
                                    $checkedTanya10Yes = 'checked="checked"';
                                    $checkedTanya10No = '';
                                break;
                                case 'No':
                                    $checkedTanya10Yes = '';
                                    $checkedTanya10No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya10Yes = '';
                                    $checkedTanya10No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya10" value="1" <?php echo $checkedTanya10Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya10" value="2" <?php echo $checkedTanya10No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['WindBreakInstalledNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Security fence installed (if needed)')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['SecurityFenceInstalled']) {
                                case 'Yes':
                                    $checkedTanya11Yes = 'checked="checked"';
                                    $checkedTanya11No = '';
                                break;
                                case 'No':
                                    $checkedTanya11Yes = '';
                                    $checkedTanya11No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya11Yes = '';
                                    $checkedTanya11No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya11" value="1" <?php echo $checkedTanya11Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya11" value="2" <?php echo $checkedTanya11No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['SecurityFenceInstalledNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Fertilizer used in seedling establishment')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['FertilizerUsed']) {
                                case 'Yes':
                                    $checkedTanya12Yes = 'checked="checked"';
                                    $checkedTanya12No = '';
                                break;
                                case 'No':
                                    $checkedTanya12Yes = '';
                                    $checkedTanya12No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya12Yes = '';
                                    $checkedTanya12No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya12" value="1" <?php echo $checkedTanya12Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya12" value="2" <?php echo $checkedTanya12No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['FertilizerUsedNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Operators possess adequate skills')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['OperatorAdequateTraining']) {
                                case 'Yes':
                                    $checkedTanya13Yes = 'checked="checked"';
                                    $checkedTanya13No = '';
                                break;
                                case 'No':
                                    $checkedTanya13Yes = '';
                                    $checkedTanya13No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya13Yes = '';
                                    $checkedTanya13No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya13" value="1" <?php echo $checkedTanya13Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya13" value="2" <?php echo $checkedTanya13No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['OperatorAdequateTrainingNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Adequate facilities for workers, and requisite safety equipment provided')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['AdequateFacility']) {
                                case 'Yes':
                                    $checkedTanya14Yes = 'checked="checked"';
                                    $checkedTanya14No = '';
                                break;
                                case 'No':
                                    $checkedTanya14Yes = '';
                                    $checkedTanya14No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya14Yes = '';
                                    $checkedTanya14No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya14" value="1" <?php echo $checkedTanya14Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya14" value="2" <?php echo $checkedTanya14No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['AdequateFacilityNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Sustainable and rational pest and disease control')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['SustainablePestDisease']) {
                                case 'Yes':
                                    $checkedTanya15Yes = 'checked="checked"';
                                    $checkedTanya15No = '';
                                break;
                                case 'No':
                                    $checkedTanya15Yes = '';
                                    $checkedTanya15No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya15Yes = '';
                                    $checkedTanya15No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya15" value="1" <?php echo $checkedTanya15Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya15" value="2" <?php echo $checkedTanya15No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['SustainablePestDiseaseNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Seedling culling is done')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['SeedlingCullingDone']) {
                                case 'Yes':
                                    $checkedTanya16Yes = 'checked="checked"';
                                    $checkedTanya16No = '';
                                break;
                                case 'No':
                                    $checkedTanya16Yes = '';
                                    $checkedTanya16No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya16Yes = '';
                                    $checkedTanya16No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya16" value="1" <?php echo $checkedTanya16Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya16" value="2" <?php echo $checkedTanya16No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['SeedlingCullingDoneNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Proper input and sales records are maintained')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['ProperInputSalesRecord']) {
                                case 'Yes':
                                    $checkedTanya17Yes = 'checked="checked"';
                                    $checkedTanya17No = '';
                                break;
                                case 'No':
                                    $checkedTanya17Yes = '';
                                    $checkedTanya17No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya17Yes = '';
                                    $checkedTanya17No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya17" value="1" <?php echo $checkedTanya17Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya17" value="2" <?php echo $checkedTanya17No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['ProperInputSalesRecordNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo lang('Seeds are pre-germinated before planting')?></td>
                        <td align="center" style="line-height: 18px;">
                            <?php
                            switch ($nursery['SeedsPreGerminated']) {
                                case 'Yes':
                                    $checkedTanya18Yes = 'checked="checked"';
                                    $checkedTanya18No = '';
                                break;
                                case 'No':
                                    $checkedTanya18Yes = '';
                                    $checkedTanya18No = 'checked="checked"';
                                break;
                                default:
                                    $checkedTanya18Yes = '';
                                    $checkedTanya18No = '';
                                break;
                            }
                            ?>
                            <input style="vertical-align:middle;" type="radio" name="tanya18" value="1" <?php echo $checkedTanya18Yes;?> />&nbsp;Yes
                            <input style="vertical-align:middle;" type="radio" name="tanya18" value="2" <?php echo $checkedTanya18No;?> />&nbsp;No
                        </td>
                        <td>
                            <input type="text" value="<?php echo $nursery['SeedsPreGerminatedNo']?>" style="width:100%;border:none;" />
                        </td>
                    </tr>
                    </table>

                <!-- right column (END) -->
                </td>
            </tr>
        </table>

        </div>
        <!--- MULAI TABEL UTAMA (END) -->

    </div>
</div>

<?php if (($nursery['Latitude'] != "") && ($nursery['Longitude'] != "")){ ?>
<script type="text/javascript">
    var icon_path = '<?php echo base_url() ?>' + 'images/map/';
    $('#map_canvas').gmap3({
        map: {
            options: {
                center: [<?php echo $nursery['Latitude'] ?>, <?php echo $nursery['Longitude'] ?>],
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
                {latLng:[<?php echo $nursery['Latitude'] ?>, <?php echo $nursery['Longitude'] ?>], options: {icon: icon_path + "nursery.png"}}
            ],
            options:{
                draggable: false
            }
        }
   });
</script>
<?php }else{?>
    <script type="text/javascript">
        $('#map_canvas').html('<span style="font-size:13px;color:#1FA489;font-weight: bold;">No Map</span>');
    </script>
<?php } ?>