<?php
/**
 * @Author: nikolius
 * @Date:   2016-07-19 10:55:00
 */

$productivity = $garden_size['Production']/$garden_size['GardenHaUnCertified'];
if ($productivity < 500) {
    $professionalism = lang('Unprofessional Farmer');
} elseif ($productivity >= 500 && $productivity <= 1000) {
    $professionalism = lang('Progressing Farmer');
} elseif ($productivity > 1000) {
    $professionalism = lang('Professional Farmer');
}

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
                    <img src="<?= base_url() ?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
                </td>
                <td width="20%" align="right" style="vertical-align:middle;">
                    <img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
                </td>
            </tr>
        </table>
        <br /><br />

        <!--- MULAI TABEL UTAMA (BEGIN) -->
        <div id="mainContainer">
        <table id="tableMainContainer" width="100%" height="800">

            <tr>
                <td valign="top" id="sideColumn">
                <!-- side column (BEGIN) -->
                    <table width="100%" id="tabelDivSide">
                        <tr>
                            <td align="center">
                                <img id="photoPetani" src="<?php echo base_url().'image_process/resizeOtf?imagenya='.urlencode($farmer['Photo']).'&width=190&height=190'; ?>" />
                            </td>
                        </tr>
                        <tr height="20">
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="divSide" id="titleSideColum">
                                    <?php echo strtoupper(lang('PROFESSIONAL STATUS'))?>
                                </div>
                                <div class="divSide titleh3SideColumn">
                                    <span><?php echo ucwords($professionalism);?></span>
                                </div>

                                <br />

                                <div class="divSide" id="titleSideColum">
                                    <?php echo strtoupper(lang('Ukuran Tanah'))?>
                                </div>
                                <div class="divSide titleh3SideColumn">
                                    <span><?php echo ucwords(lang($garden_size['LandSize'])); ?></span>
                                </div>

                                <br />

                                <div class="divSide" id="titleSideColum">
                                    <?php echo lang('FARM DATA')?>
                                </div>

                                <div class="divSide centerPos">
                                    <?php
                                    if($garden_size['GardenHaUnCertified'] == "") $garden_size['GardenHaUnCertified'] = 0;

                                    $other_land_size = 0;
                                    if (!empty($otherland)) {
                                        foreach ($otherland as $key => $value) {
                                            $other_land_size += $value['GardenHa'];
                                        }
                                    }

                                    if ($garden['TahunTanamanCocoa']) {
                                        $farmYear = date('Y')-$garden['TahunTanamanCocoa'];
                                    }else{
                                        $farmYear = 0;
                                    }

                                    ?>
                                    <table class="tabelValueSideColumn" width="100%">
                                    <tr>
                                        <tr>
                                            <td class="tdValue"><?php echo $garden_size['GardenHaUnCertified'] ?> ha</td>
                                            <td class="tdValue"><?php echo $other_land_size ?> ha</td>
                                        </tr>
                                        <tr>
                                            <td class="tdDesc"><?php echo strtoupper(lang('COCOA FARM SIZE'))?></td>
                                            <td class="tdDesc"><?php echo strtoupper(lang('OTHER LAND SIZE'))?></td>
                                        </tr>
                                        <tr height="5"><td colspan="2">&nbsp;</td></tr>
                                        <tr>
                                            <td class="tdValue"><?php echo $farmYear.' '.strtolower(lang('Years'))?> </td>
                                            <td class="tdValue"><?php echo $garden_size['Production']>0?number_format($garden_size['Production'],0,'.',','):'' ?> kg</td>
                                        </tr>
                                        <tr>
                                            <td class="tdDesc"><?php echo strtoupper(lang('FARM AGE'))?></td>
                                            <td class="tdDesc"><?php echo strtoupper(lang('TOTAL PRODUCTION'))?></td>
                                        </tr>
                                        <tr height="5"><td colspan="2">&nbsp;</td></tr>
                                        <tr>
                                            <td colspan="2" class="tdValue"><?php echo $garden_size['PohonTM']>0?number_format($garden_size['PohonTM'],0,'.',','):'' ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="tdDesc"><?php echo lang('NO. PRODUCING CACAO TREES')?></td>
                                        </tr>
                                        <tr height="5"><td colspan="2">&nbsp;</td></tr>
                                        <tr>
                                            <td colspan="2" class="tdValue">Rp. <?php echo ($finance['CocoaPriceToday']*$garden_size['Production'] > 0)?number_format($finance['CocoaPriceToday']*$garden_size['Production'],0,'.',','):'0' ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="tdDesc"><?php echo lang('CASH FLOW FROM COCOA (ca.)')?></td>
                                        </tr>
                                        <tr height="5"><td colspan="2">&nbsp;</td></tr>
                                        <tr>
                                            <td colspan="2" class="tdValue"><?php
                                            switch ($finance['ValueCocoaFarm'] ) {
                                                case '1': echo '< 10 '.strtolower(lang('Million')); break;
                                                case '2': echo '10 - 20 '.strtolower(lang('Million')); break;
                                                case '3': echo '20 - 50 '.strtolower(lang('Million')); break;
                                                case '4': echo '50 - 100 '.strtolower(lang('Million')); break;
                                                case '5': echo '100 - 200 '.strtolower(lang('Million')); break;
                                                case '6': echo '> 200 '.strtolower(lang('Million')); break;
                                                case '7': echo '-'; break;
                                            }
                                            ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="tdDesc"><?php echo strtoupper(lang('Nilai Jual Kebun Coklat'))?>&nbsp;<?php echo strtoupper(lang('dalam rupiah'))?></td>
                                        </tr>
                                    </tr>
                                    </table>
                                </div>

                            </td>
                        </tr>
                    </table>
                <!-- side column (END) -->
                </td>
                <td id="mainColumn" valign="top">
                <!-- right column (BEGIN) -->

                    <table width="100%" style="min-height: 110px;">
                    <tr>
                        <td width="70%" style="border-right: 1px dotted #11B1A7;">
                            <div id="tdSubTitle"><?php echo ucwords(lang('COCOA FARMER PROFILE'))?></div>
                            <div id="tdMainTitle"><?php echo strtoupper(lang('loan request'))?> </div>
                        </td>
                        <td width="30%" id="tdBarcode">
                            <img src="<?php echo base_url() ?>index.php/farmer/qrcode_generator/<?php echo $farmer['FarmerID'];?>/" width="75" />
                            <div style="text-align:center;font-weight: normal;"><?php echo strtoupper(lang('Farmer ID'))?> :</div><?php echo $farmer['FarmerID'] ?>
                        </td>
                    </tr>
                    </table>

                    <table width="100%" style="margin-top:-25px;" border="0">
                    <tr>
                        <td width="7%" valign="top">
                            <img src="<?php echo base_url() ?>assets/css/req_for_loan/icon-fp-basic-data.png" width="30" />
                        </td>
                        <td width="93%" class="tdTitleMain" valign="middle">
                           <?php echo strtoupper(lang('Basic Data')) ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="2">
                            <table width="100%" class="tabelValueMain">
                            <tr>
                                <td width="33%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Farmer Name')) ?></div>
                                    <div class="tdValueMain"><?php echo strtoupper($farmer['FarmerName']) ?></div>
                                </td>
                                <td width="20%">
                                    <?php
                                        $now        = new Datetime('now');
                                        $birthdate  = new Datetime($farmer['Birthdate']);
                                        $age        = $now->diff($birthdate);
                                    ?>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Birthdate')) ?></div>
                                    <div class="tdValueMain">
                                        <?php echo date('d M Y', strtotime($farmer['Birthdate'])) ?>
                                        &nbsp;
                                    </div>
                                </td>
                                <td width="14%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('age')) ?></div>
                                    <div class="tdValueMain"><?php echo $age->y ?>&nbsp;</div>
                                </td>
                                <td width="33%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Gender')) ?></div>
                                    <div class="tdValueMain">
                                    <?php
                                        if($farmer['Gender']=='1'){
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
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('No Telepon')) ?></div>
                                    <div class="tdValueMain"><?php echo $farmer['HandPhone'] ?>&nbsp;</div>
                                </td>
                                <td colspan="2">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Status Perkawinan'))?></div>
                                    <div class="tdValueMain">
                                        <?php switch ($farmer['MaritalStatus']) {
                                            case '1': $status = lang('Menikah'); break;
                                            case '2': $status = 'Single'; break;
                                            case '3': $status = lang('Cerai'); break;
                                        }
                                        echo $status
                                        ?>
                                    &nbsp;</div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo lang('NO. FAMILY MEMBER')?></div>
                                    <div class="tdValueMain"><?php echo count($family)+1 ?>&nbsp;</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tdLabelMain"><?php echo lang('EDUCATION LEVEL')?></div>
                                    <div class="tdValueMain">
                                    <?php
                                        $education = '';
                                        switch ($farmer['Education']) {
                                            case '1': $education = lang('No Schooling');break;
                                            case '2': $education = lang('Primary School Incomplete');break;
                                            case '3': $education = lang('Primary School Completed');break;
                                            case '4': $education = lang('Junior High School');break;
                                            case '5': $education = lang('Senior High School / Vocational');break;
                                            case '5': $education = lang('Tertiary Degree');break;
                                        }
                                        echo $education;
                                    ?>
                                    &nbsp;</div>
                                </td>
                                <td colspan="2">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Farmer Group')); ?></div>
                                    <div class="tdValueMain"><?php echo $farmer['GroupName'] ?>&nbsp;</div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Farmer Cooperative')); ?></div>
                                    <div class="tdValueMain">None&nbsp;</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Provinsi')) ?></div>
                                    <div class="tdValueMain"><?php echo $farmer['Provinsi'] ?>&nbsp;</div>
                                </td>
                                <td colspan="2">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('District')) ?></div>
                                    <div class="tdValueMain"><?php echo $farmer['Kabupaten'] ?>&nbsp;</div>
                                </td>
                                <td>
                                    <!--
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('DISTANCE TO BANK')) ?></div>
                                    <div class="tdValueMain" style="font-size:24px !important;"><?php echo number_format($bank['distance'],0,',','.') ?> km&nbsp;</div>
                                    -->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Bank Branch Name')) ?></div>
                                    <div class="tdValueMain"><?php echo $bank['label']?>&nbsp;</div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('DISTANCE TO BANK')) ?></div>
                                    <div class="tdValueMain" style="font-size:24px !important;"><?php echo number_format($bank['distance'],0,',','.') ?> km&nbsp;</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Sub District')) ?></div>
                                    <div class="tdValueMain"><?php echo $farmer['Kecamatan'] ?>&nbsp;</div>
                                </td>
                                <td colspan="2">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Village')) ?></div>
                                    <div class="tdValueMain"><?php echo $farmer['Desa'] ?>&nbsp;</div>
                                </td>
                            </tr>
                            </table>

                            <div style="width:100%;border-bottom: 1px dotted #4C81E0;margin-bottom:3px;margin-top:-15px;">&nbsp;</div>

                            <table width="100%" class="tabelValueMain">
                            <tr>
                                <td width="27%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Perlu Pinjaman'))?></div>
                                    <div class="tdValueMain"><?php echo $finance['NeedLoan'] == '1'? lang('Ya') : lang('No') ?></div>
                                </td>
                                <td width="33%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('LOAN EXPERIENCE'))?></div>
                                    <div class="tdValueMain"><?php echo $finance['LoanYesNo'] == '2'? lang('None'): lang('Yes') ?></div>
                                </td>
                                <td width="44%">
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Sumber Pinjaman'))?></div>
                                    <div class="tdValueMain">
                                        <?php
                                        if ($finance['LoanYesNo'] !== '2') {
                                            if ($finance['LoanUnitTengkulak']) {
                                                echo lang('Pedagang');
                                            } elseif ($finance['LoanUnitKeluarga']) {
                                                echo lang('Family / Friends');
                                            } elseif ($finance['LoanUnitRentenir']) {
                                                echo lang('Moneylender / Middlemen');
                                            } elseif ($finance['LoanUnitBank']) {
                                                echo lang('Bank');
                                            } elseif ($finance['LoanUnitKoperasi']) {
                                                echo lang('Cooperative');
                                            } elseif ($finance['LoanUnitMasjid']) {
                                                echo lang('Masjid');
                                            } elseif ($finance['LoanUnitLainnya']) {
                                                echo lang('Others');
                                            }
                                        } else {
                                            echo lang('None');
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('SAVINGS ACCOUNT'))?></div>
                                    <div class="tdValueMain"><?php echo $finance['SavingUnitBank'] == '1'? lang('Yes') : lang('None') ?></div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Institution'))?></div>
                                    <div class="tdValueMain"><?php if($finance['AccountBankName'] == "") echo lang('None'); else echo $finance['AccountBankName']; ?></div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Pengalaman pinjaman dari bank'))?></div>
                                    <div class="tdValueMain"><?php echo $finance['LoanUnitBank'] == '1'?lang('Yes'):lang('None') ?></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Pendapatan Lainnya'))?></div>
                                    <div class="tdValueMain"><?php echo $finance['OtherIncome'] == '1'?lang('Yes'):lang('None')?></div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('OTHER INCOME SOURCE'))?></div>
                                    <div class="tdValueMain">
                                        <?php
                                        $other_income = array();
                                        if($finance['SourceOtherIncomeGajiTetap'] == '1') { $other_income[] = lang('Gaji dari pekerjaan tetap / paruh waktu'); }
                                        if($finance['SourceOtherIncomeGajiPasangan'] == '1') { $other_income[] = lang('Gaji pasangan (gaji Suami/Istri)'); }
                                        if($finance['SourceOtherIncomeUsaha'] == '1') { $other_income[] = lang('Penghasilan dari  usaha lain'); }
                                        if($finance['SourceOtherIncomeFamily'] == '1') { $other_income[] = lang('Saudara/famili  yang mengirim uang dari luar negeri'); }
                                        if($finance['SourceOtherIncomeLainnya'] == '1') { $other_income[] = lang('Pendapatan Lainnya'); }
                                        echo implode(', ',$other_income);
                                        ?>
                                    &nbsp;</div>
                                </td>
                                <td>
                                    <div class="tdLabelMain"><?php echo strtoupper(lang('Pengajuan Agunan'))?></div>
                                    <div class="tdValueMain"><?php echo $finance['CollateralOfferedBank'] == '1'?lang('Cocoa Beans'):lang('None') ?>&nbsp;</div>
                                </td>
                            </tr>
                            </table>

                        </td>
                    </tr>
                    </table>
                    <br />


                    <table width="100%" style="margin-bottom:5px;margin-top:-15px;">
                    <tr>
                        <td width="7%" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/req_for_loan/icon-fp-farm-data.png" width="25" />
                        </td>
                        <td class="tdTitleMain" valign="middle">
                           <?php echo strtoupper(lang('FARM DATA'))?>
                        </td>
                    </tr>
                    </table>

                    <table class="tabelListData" width="100%" border="0">
                    <tr>
                        <th><?php echo strtoupper(lang('FarPro GardenNr'))?>.</th>
                        <th><?php echo strtoupper(lang('Land Ownership'))?></th>
                        <th><?php echo strtoupper(lang('Land Certificate'))?></th>
                        <th>LAT</th>
                        <th>LONG</th>
                    </tr>
                    <?php
                        if(count($gardens) > 0){
                           $increList = 1;
                           foreach ($gardens as $key => $gardenList){

                              switch ($gardenList['OwnershipCocoa']) {
                                 case '1':
                                    $landOwn = lang('Pemilik Penggarap');
                                 break;
                                 case '2':
                                    $landOwn = lang('Petani Bagi Hasil');
                                 break;
                                 case '3':
                                    $landOwn = lang('Petani Penyewa');
                                 break;
                                 case '4':
                                    $landOwn = lang('Lain lain');
                                 break;
                                 default:
                                    $landOwn = '-';
                                 break;
                              }

                              switch ($gardenList['LandCertificate']) {
                                 case '1':
                                    $landCert = lang('None');
                                 break;
                                 case '2':
                                    $landCert = lang('Akte Notaris/BPN');
                                 break;
                                 case '3':
                                    $landCert = lang('SKKT (Camat)');
                                 break;
                                 case '4':
                                    $landCert = lang('Desa/Lurah');
                                 break;
                                 case '5':
                                    $landCert = lang('Tidak tahu');
                                 break;
                                 default:
                                    $landCert = '-';
                                 break;
                              }

                              echo '<tr>
                              <td align="center">'.$gardenList['GardenNr'].'</td>
                              <td align="center">'.$landOwn.'</td>
                              <td align="center">'.$landCert.'</td>
                              <td align="center">'.$gardenList['Latitude'].'</td>
                              <td align="center">'.$gardenList['Longitude'].'</td>
                              </tr>';
                              $increList++;
                           }
                        }else{
                           echo '<tr><td colspan="6" align="center">No Data</td></tr>';
                        }
                    ?>
                    </table>

                    <div id="map_canvas_<?php echo $farmer['FarmerID'] ?>" style="width: 100%; height: 110px;"></div>
                    <table width="100%" class="tabelValueMain">
                    <tr>
                        <td width="45%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Jarak dari rumah ke kebun'))?></div>
                        </td>
                        <td>
                            <?php
                                if($garden['GardenDistance'] == "") $garden['GardenDistance'] = 0;
                            ?>
                            <div class="tdValueMain"><?php echo $garden['GardenDistance'];?>&nbsp;m</div>
                        </td>
                    </tr>
                    </table>
                    <br />

                    <table width="100%" style="margin-bottom:5px;margin-top:-15px;">
                    <tr>
                        <td width="7%" valign="top">
                           <img src="<?php echo base_url() ?>assets/css/req_for_loan/icon-fp-training.png" width="30" />
                        </td>
                        <td class="tdTitleMain" valign="middle">
                           <?php echo strtoupper(lang('Training Participation'))?>
                        </td>
                    </tr>
                    </table>

                    <table class="tabelListData" width="100%" border="0">
                    <tr>
                        <th>NO.</th>
                        <th><?php echo strtoupper(lang('Training'))?></th>
                        <th><?php echo strtoupper(lang('Start'))?></th>
                        <th><?php echo strtoupper(lang('Selesai'))?></th>
                        <th><?php echo strtoupper(lang('Days'))?></th>
                        <th><?php echo strtoupper(lang('Jenis'))?></th>
                    </tr>
                    <?php if ($trainings): ?>
                        <?php
                        $increTrain = 1;
                        ?>
                        <?php foreach ($trainings as $key => $training): ?>
                           <tr>
                              <td align="center"><?php echo $training['BatchNumber'] ?>.</td>
                              <td>
                                <?php echo $training['CpgTrainings'] ?>
                                <?php if ($training['sub_topic']): ?>
                                    - [<?php echo $training['sub_topic'] ?>]
                                <?php endif ?>
                              </td>
                              <td align="center"><?php echo date('d M Y', strtotime($training['TrainingStart'])) ?></td>
                              <td align="center"><?php echo date('d M Y', strtotime($training['TrainingEnd'])) ?></td>
                              <td align="center"><?php echo $training['TrainingDays'] ?></td>
                              <td align="center"><?php echo $training['type'] ?></td>
                           </tr>
                           <?php
                           $increTrain++;
                           if($increTrain > 7) break;
                           ?>
                        <?php endforeach ?>
                    <?php endif ?>
                    </table>

                <!-- right column (END) -->
                </td>
            </tr>


        </table>
        </div>
        <!--- MULAI TABEL UTAMA (END) -->

    </div>
    <div class="page-break"></div>

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