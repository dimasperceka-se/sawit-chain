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

<div class="clearfix" id="page"><!-- column -->

   <div class="clearfix" style="width:0px;height:0px;"></div>

   <div class="clearfix colelem" id="pu81-8"><!-- group -->
      <div class="clearfix grpelem" id="u81-8"><!-- content -->
         <p id="u81-2">FARMER SUMMARY</p>
         <p style="margin-bottom: 7px;" id="u81-4">REQUEST FOR LOAN</p>
         <p id="u81-6"><?php echo strtoupper(lang('Farmer Name')) ?> : <?php echo strtoupper($farmer['FarmerName']) ?> - <?php echo $farmer['FarmerID'] ?></p>
      </div>

      <div class="grpelem" id="u85" style="margin-bottom: -20px;"><!-- custom html -->
         <div id="map_canvas_<?php echo $farmer['FarmerID'] ?>" style="width: 100%; height: 110px; position: absolute !important;"></div>
      </div>

      <div class="rounded-corners grpelem" id="u88"><!-- simple frame --></div>
      <img width="111" class="rounded-corners grpelem" id="u88" src="<?php echo base_url().'images/Photo/'.$farmer['Photo'] ?>" style="" />
   </div>

   <div class="clearfix colelem" id="ppu116-4"><!-- group -->

      <div class="clearfix grpelem" id="pu116-4"><!-- column -->

         <div class="clearfix colelem" id="u116-4"><!-- content -->
            <p><?php echo strtoupper(lang('Province')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u114"><!-- group -->
            <div class="clearfix grpelem" id="u269-4"><!-- content -->
               <p><?php echo strtoupper($farmer['Provinsi']) ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u118-4"><!-- content -->
            <p><?php echo strtoupper(lang('District')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u119"><!-- group -->
            <div class="clearfix grpelem" id="u271-4"><!-- content -->
               <p><?php echo strtoupper($farmer['Kabupaten']) ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u120-4"><!-- content -->
            <p><?php echo strtoupper(lang('Kecamatan')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u121"><!-- group -->
            <div class="clearfix grpelem" id="u272-4"><!-- content -->
               <p><?php echo strtoupper($farmer['Kecamatan']) ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u122-4"><!-- content -->
            <p><?php echo strtoupper(lang('Desa')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u123"><!-- group -->
            <div class="clearfix grpelem" id="u273-4"><!-- content -->
               <p><?php echo strtoupper($farmer['Desa']) ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u125-4"><!-- content -->
            <p><?php echo strtoupper(lang('Birthdate')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u124"><!-- group -->
            <div class="clearfix grpelem" id="u274-4"><!-- content -->
               <p><?php echo date('d-m-Y', strtotime($farmer['Birthdate'])) ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u126-4"><!-- content -->
            <p><?php echo strtoupper(lang('age')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u127"><!-- group -->
            <div class="clearfix grpelem" id="u275-4"><!-- content -->
               <p><?php echo $age->y ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="ppu151-4"><!-- group -->

            <div class="clearfix grpelem" id="pu151-4"><!-- column -->
               <div class="clearfix colelem" id="u151-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Gender')) ?></p>
               </div>
               <div class="clearfix colelem" id="u154"><!-- group -->
                  <div class="rounded-corners clearfix grpelem" id="u155"><!-- group -->
                     <div class="rounded-corners clearfix grpelem" id="u156"><!-- group -->
                     <?php if($farmer['Gender']=='1'){?>
                        <div style="background-color:black;" class="rounded-corners grpelem" id="u157"></div>
                     <?php }else{?>
                        <div style="background-color:white;" class="rounded-corners grpelem" id="u157"></div>
                     <?php }?>
                     </div>
                  </div>
               </div>
               <div class="clearfix colelem" id="u153-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Laki-laki')) ?></p>
               </div>
            </div>


            <div style="margin-left: 35px;" class="clearfix grpelem" id="pu151-4"><!-- column -->
               <div class="clearfix colelem" id="u151-4"><!-- content -->
                  <p></p>
               </div>
               <div class="clearfix colelem" id="u154"><!-- group -->
                  <div class="rounded-corners clearfix grpelem" id="u155"><!-- group -->
                     <div class="rounded-corners clearfix grpelem" id="u156"><!-- group -->
                        <?php if($farmer['Gender']=='2'){?>
                           <div style="background-color:black;" class="rounded-corners grpelem" id="u157"></div>
                        <?php }else{?>
                           <div style="background-color:white;" class="rounded-corners grpelem" id="u157"></div>
                        <?php }?>
                     </div>
                  </div>
               </div>
               <div class="clearfix colelem" id="u153-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Female')) ?></p>
               </div>
            </div>

            <div class="grpelem" id="u257"><!-- simple frame --></div>

            <div class="clearfix grpelem" id="pu168-4"><!-- column -->

               <div class="clearfix colelem" id="u168-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Status Perkawinan')) ?></p>
               </div>

               <div class="rounded-corners clearfix colelem" id="u171"><!-- group -->
                  <div class="rounded-corners grpelem" id="u170">

                     <div class="rounded-corners clearfix grpelem" id="u177"><!-- group -->
                        <?php if($farmer['MaritalStatus'] == '2'){?>
                           <div style="background-color: black;margin-left: -1px;margin-top:0px;" class="rounded-corners grpelem" id="u175"><!-- simple frame --></div>
                        <?php }else{?>
                           <div style="background-color: white;margin-left: -1px;margin-top:0px;" class="rounded-corners grpelem" id="u175"><!-- simple frame --></div>
                        <?php }?>
                     </div>

                  </div>
               </div>

               <div class="clearfix colelem" id="u173-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Single'))?></p>
               </div>

               <div class="clearfix colelem" id="u174"><!-- group -->
                  <div class="rounded-corners clearfix grpelem" id="u176"><!-- group -->

                     <div class="rounded-corners clearfix grpelem" id="u177"><!-- group -->
                        <?php if($farmer['MaritalStatus'] == '1'){?>
                           <div style="background-color: black;" class="rounded-corners grpelem" id="u175"><!-- simple frame --></div>
                        <?php }else{?>
                           <div style="background-color: white;" class="rounded-corners grpelem" id="u175"><!-- simple frame --></div>
                        <?php }?>
                     </div>

                  </div>
               </div>

               <div class="clearfix colelem" id="u178-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Menikah'))?></p>
               </div>

               <div style="margin-left: 90px;" class="clearfix colelem" id="u174"><!-- group -->
                  <div class="rounded-corners clearfix grpelem" id="u176"><!-- group -->

                     <div class="rounded-corners clearfix grpelem" id="u177"><!-- group -->
                        <?php if($farmer['MaritalStatus'] == '3'){?>
                           <div style="background-color: black;margin-left: -0.5px;" class="rounded-corners grpelem" id="u175"><!-- simple frame --></div>
                        <?php }else{?>
                           <div style="background-color: white;margin-left: -0.5px;" class="rounded-corners grpelem" id="u175"><!-- simple frame --></div>
                        <?php }?>
                     </div>

                  </div>
               </div>

               <div style="margin-left: 100px;" class="clearfix colelem" id="u178-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Cerai'))?></p>
               </div>

            </div>

         </div>

         <div class="clearfix colelem" id="u184-4"><!-- content -->
            <p><?php echo strtoupper(lang('No Telepon'))?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u183"><!-- group -->
            <div class="clearfix grpelem" id="u277-4"><!-- content -->
               <p><?php echo $farmer['HandPhone'] ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u188-4"><!-- content -->
            <p><?php echo strtoupper(lang('Education'))?></p>
         </div>
         <div class="gradient rounded-corners colelem" id="u187">
            <div class="clearfix grpelem" id="u277-4"><p>
            <?php
               $education = '';
               switch ($farmer['Education']) {
                  case '1': $education = lang('No Schooling') ;break;
                  case '2': $education = lang('Primary School Incomplete') ;break;
                  case '3': $education = lang('Primary School Completed') ;break;
                  case '4': $education = lang('Junior High School') ;break;
                  case '5': $education = lang('Senior High School / Vocational') ;break;
                  case '5': $education = lang('Tertiary Degree') ;break;
               }
               echo strtoupper($education);
            ?>
            </p></div>
         </div>

         <div class="clearfix colelem" id="ppu192-4"><!-- group -->
            <div class="clearfix grpelem" id="pu192-4"><!-- column -->
               <div class="clearfix colelem" id="u192-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Perlu Pinjaman'))?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u191"><!-- group -->
                  <div class="clearfix grpelem" id="u278-4"><!-- content -->
                     <p>
                     <?php echo $finance['NeedLoan'] == '1' ? strtoupper(lang('Ya')) : strtoupper(lang('Tidak')) ?>
                     </p>
                  </div>
               </div>
            </div>
            <div class="grpelem" id="u256"><!-- simple frame --></div>

            <div class="clearfix grpelem" id="pu193-4"><!-- column -->
               <div class="clearfix colelem" id="u193-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Pengalaman Pinjaman'))?></p>
               </div>
               <div class="clearfix colelem" id="pu194"><!-- group -->
                  <div class="gradient rounded-corners clearfix grpelem" id="u194"><!-- group -->
                     <div class="clearfix grpelem" id="u279-4"><!-- content -->
                        <p>
                           <?php echo $finance['LoanYesNo'] == '1' ? strtoupper(lang('Ya')) : strtoupper(lang('Tidak')) ?>
                        </p>
                     </div>
                  </div>
               <div class="grpelem" id="u255"><!-- simple frame --></div>
               </div>
            </div>
            <div class="clearfix grpelem" id="pu196-4"><!-- column -->
               <div class="clearfix colelem" id="u196-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Sumber Pinjaman'))?></p>
               </div>
               <div class="gradient rounded-corners colelem" id="u195">
                  <div class="clearfix grpelem" id="u277-4">
                     <p>
                     <?php
                     if ($finance['LoanYesNo'] !== '2') {
                        if ($finance['LoanUnitTengkulak']) {
                           echo 'TRADER';
                        } elseif ($finance['LoanUnitKeluarga']) {
                           echo strtoupper(lang('Keluarga / Teman'));
                        } elseif ($finance['LoanUnitRentenir']) {
                           echo strtoupper(lang('Rentenir/ Perantara'));
                        } elseif ($finance['LoanUnitBank']) {
                           echo 'BANK';
                        } elseif ($finance['LoanUnitKoperasi']) {
                           echo strtoupper(lang('Cooperative'));
                        } elseif ($finance['LoanUnitMasjid']) {
                           echo strtoupper(lang('Masjid'));
                        } elseif ($finance['LoanUnitLainnya']) {
                           echo strtoupper(lang('Lain-lain'));
                        }
                     } else {
                        echo strtoupper(lang('Tidak'));
                     }
                     ?>
                     </p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="u200-4"><!-- content -->
            <p><?php echo strtoupper(lang('Pengalaman pinjaman dari bank'))?></p>
         </div>
         <div class="clearfix colelem" id="pu199"><!-- group -->
            <div class="gradient rounded-corners clearfix grpelem" id="u199"><!-- group -->
               <div class="clearfix grpelem" id="u281-4"><!-- content -->
                  <p><?php echo $finance['LoanUnitBank'] == '1' ? strtoupper(lang('Ya')) : strtoupper(lang('Tidak'))  ?></p>
               </div>
            </div>
            <div class="grpelem" id="u254"><!-- simple frame --></div>
            <div class="clearfix grpelem" id="pu204-4"><!-- column -->
               <div style="width:120px;" class="clearfix colelem" id="u204-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Besar pinjaman sebelumnya'))?></p>
               </div>
               <div class="gradient rounded-corners colelem" id="u203">
                  <div class="clearfix grpelem" id="u281-4"><!-- content -->
                     <p><?php echo $finance['PreviousLoan'] ?></p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="u207-4"><!-- content -->
            <p><?php echo strtoupper(lang('Jumlah Pinjaman'))?></p>
         </div>
         <div class="gradient rounded-corners colelem" id="u208">
            <div class="clearfix grpelem" id="u281-4"><!-- content -->
               <p><?php echo $finance['AmountOutsCurrentLoan']>0?number_format($finance['AmountOutsCurrentLoan'],0,'.',','):'' ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="ppu210-4"><!-- group -->
            <div class="clearfix grpelem" id="pu210-4"><!-- column -->
               <div class="clearfix colelem" id="u210-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Rekening Tabungan'))?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u209"><!-- group -->
                  <div class="clearfix grpelem" id="u282-4"><!-- content -->
                     <p>
                        <?php echo $finance['SavingUnitBank'] == '1' ? strtoupper(lang('Ya')) : strtoupper(lang('Tidak')) ?>
                     </p>
                  </div>
               </div>
            </div>
            <div class="grpelem" id="u253"><!-- simple frame --></div>
            <div class="clearfix grpelem" id="pu213-4"><!-- column -->
               <div class="clearfix colelem" id="u213-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Di bank mana'))?></p>
               </div>
               <div class="gradient rounded-corners colelem" id="u214">
                  <div class="clearfix grpelem" id="u282-4"><!-- content -->
                     <p>
                        <?php echo strtoupper($finance['AccountBankName']) ?>
                     </p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="ppu216-4"><!-- group -->
            <div class="clearfix grpelem" id="pu216-4"><!-- column -->
               <div class="clearfix colelem" id="u216-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Pendapatan Lainnya')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u215"><!-- group -->
                  <div class="clearfix grpelem" id="u283-4"><!-- content -->
                     <p>
                        <?php echo $finance['OtherIncome'] == '1' ? strtoupper(lang('Ya')) : strtoupper(lang('Tidak')) ?>
                     </p>
                  </div>
               </div>
            </div>
            <div class="grpelem" id="u252"><!-- simple frame --></div>
            <div class="clearfix grpelem" id="pu218-4"><!-- column -->
               <div class="clearfix colelem" id="u218-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Dari mana')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u217"><!-- group -->
                  <div class="clearfix grpelem" id="u284-4"><!-- content -->
                     <p>
                        <?php
                           $other_income = array();
                           if($finance['SourceOtherIncomeGajiTetap'] == '1') { $other_income[] = lang('Gaji dari pekerjaan tetap / paruh waktu'); }
                           if($finance['SourceOtherIncomeGajiPasangan'] == '1') { $other_income[] = lang('Gaji pasangan (gaji Suami/Istri)'); }
                           if($finance['SourceOtherIncomeUsaha'] == '1') { $other_income[] = lang('Penghasilan dari  usaha lain'); }
                           if($finance['SourceOtherIncomeFamily'] == '1') { $other_income[] = lang('Saudara/famili  yang mengirim uang dari luar negeri'); }
                           if($finance['SourceOtherIncomeLainnya'] == '1') { $other_income[] = lang('Pendapatan Lainnya'); }
                           echo strtoupper(implode(', ',$other_income));
                        ?>
                     </p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="u220-4"><!-- content -->
            <p><?php echo strtoupper(lang('Pelatihan yang diterima')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u221"><!-- group -->
            <div class="clearfix grpelem" id="u285-6"><!-- content -->
               <?php
                 if ($training) {
                     $train = array();
                     foreach ($training as $key => $value) {
                         $train[] = $value['CpgTrainings'];
                     }
                     echo strtoupper(implode(', ',$train));
                 }
               ?>
            </div>
         </div>

         <div class="clearfix colelem" id="u225-4"><!-- content -->
            <p><?php echo strtoupper(lang('Future money needs')) ?></p>
         </div>
         <div class="gradient rounded-corners colelem" id="u224">
            <div class="clearfix grpelem" id="u285-6"><!-- content -->
               <?php
                  $future = array();
                  if($finance['FutureReasonSekolah']          == '1') { $future[] = lang('Biaya sekolah / Pendidikan'); }
                  if($finance['FutureReasonRumahTangga']      == '1') { $future[] = lang('Peralatan rumah tangga (kulkas, TV)'); }
                  if($finance['FutureReasonSumbangan']        == '1') { $future[] = lang('Sumbangan pemakaman / pernikahan'); }
                  if($finance['FutureReasonDarurat']          == '1') { $future[] = lang('Emergencies'); }
                  if($finance['FutureReasonKesehatan']        == '1') { $future[] = lang('Health Care'); }
                  if($finance['FutureReasonInvestasiKebun']   == '1') { $future[] = lang('Investasi pertanian dan Pemeliharaan kebun kakao'); }
                  if($finance['FutureReasonInvestasiLain']    == '1') { $future[] = lang('Investasi bisnis lainnya'); }
                  if($finance['FutureReasonRumah']            == '1') { $future[] = lang('Rumah baru / renovasi rumah'); }
                  if($finance['FutureReasonLahan']            == '1') { $future[] = lang('Membeli lahan baru untuk bertani'); }
                  if($finance['FutureReasonKendaraan']        == '1') { $future[] = lang('Motor / Mobil'); }
                  if($finance['FutureReasonHaji']             == '1') { $future[] = lang('Haji / Umrah'); }
                  if($finance['FutureReasonPensiun']          == '1') { $future[] = lang('Masa Pensiun'); }
                  if($finance['FutureReasonLain']             == '1') { $future[] = lang('Lain lain'); }
                  echo strtoupper(implode(', ',$future));
               ?>
            </div>
         </div>

      </div> <!-- Batas akhir div kolom kiri -->

      <div class="grpelem" id="u236"><!-- simple frame --></div>

      <div class="clearfix grpelem" id="pu129-4"><!-- column -->

         <div class="clearfix colelem" id="u129-4"><!-- content -->
            <p><?php echo strtoupper(lang('Koordinat GPS')) ?></p>
         </div>

         <div class="clearfix colelem" id="pu238-4"><!-- group -->
            <div class="clearfix grpelem" id="u238-4"><!-- content -->
               <p>LONG</p>
            </div>
            <div class="gradient rounded-corners clearfix grpelem" id="u128"><!-- group -->
               <div class="clearfix grpelem" id="u286-4"><!-- content -->
                  <p><?php echo $garden['Longitude'];?></p>
               </div>
            </div>

            <div class="grpelem" id="u240"><!-- simple frame --></div>

            <div class="clearfix grpelem" id="u239-4"><!-- content -->
               <p>LAT</p>
            </div>
            <div class="gradient rounded-corners clearfix grpelem" id="u132"><!-- group -->
               <div class="clearfix grpelem" id="u287-4"><!-- content -->
                  <p><?php echo $garden['Latitude'];?></p>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="u131-4"><!-- content -->
            <p><?php echo strtoupper(lang('Jarak dari rumah ke kebun')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u130"><!-- group -->
            <div class="clearfix grpelem" id="u288-4"><!-- content -->
               <p><?php echo $garden['GardenDistance']>0?$garden['GardenDistance'].' m':'' ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="ppu134-4"><!-- group -->
            <div class="clearfix grpelem" id="pu134-4"><!-- column -->
               <div class="clearfix colelem" id="u134-4"><!-- content -->
                  <p>SCPP ID</p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u133"><!-- group -->
                  <div class="clearfix grpelem" id="u289-4"><!-- content -->
                     <p><?php echo $farmer['FarmerID'] ?></p>
                  </div>
               </div>
            </div>

            <div class="grpelem" id="u244"><!-- simple frame --></div>

            <div class="clearfix grpelem" id="pu150-4"><!-- column -->
               <div class="clearfix colelem" id="u150-4"><!-- content -->
                  <p>SCPP PARTNER</p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u137"><!-- group -->
                  <div class="clearfix grpelem" id="u290-4"><!-- content -->
                     <p><?php echo strtoupper($partner['PartnerFullName']) ?></p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="ppu146-4"><!-- group -->
            <div class="clearfix grpelem" id="pu146-4"><!-- column -->
               <div class="clearfix colelem" id="u146-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Total Production (kg)')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u139"><!-- group -->
                  <div class="clearfix grpelem" id="u291-4"><!-- content -->
                     <p><?php echo $garden_size['Production']>0?number_format($garden_size['Production'],0,',','.'):'' ?></p>
                  </div>
               </div>
            </div>

            <div class="grpelem" id="u245"><!-- simple frame --></div>

            <div class="clearfix grpelem" id="pu149-4"><!-- column -->
               <div class="clearfix colelem" id="u149-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Production per hectare (kg)')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u138"><!-- group -->
                  <div class="clearfix grpelem" id="u292-4"><!-- content -->
                     <p><?php echo ($garden_size['Production']/$garden_size['GardenHaUnCertified']>0)?number_format($garden_size['Production']/$garden_size['GardenHaUnCertified'],0,',','.'):'' ?></p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="u147-4"><!-- content -->
            <p><?php echo strtoupper(lang('Ukuran Kebun (ha)')) ?></p>
         </div>
         <div class="clearfix colelem" id="pu143"><!-- group -->
            <div class="gradient rounded-corners clearfix grpelem" id="u143"><!-- group -->
               <div class="clearfix grpelem" id="u293-4"><!-- content -->
                  <p><?php echo $garden_size['GardenHaUnCertified'] != 0 ? $garden_size['GardenHaUnCertified'] : '' ?></p>
               </div>
            </div>
            <div class="grpelem" id="u246"><!-- simple frame --></div>
            <div class="clearfix grpelem" id="pu148-4"><!-- column -->
               <div class="clearfix colelem" id="u148-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Ukuran Tanah Lainnya (ha)')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u142"><!-- group -->
                  <div class="clearfix grpelem" id="u294-4"><!-- content -->
                     <p>
                        <?php
                          $other_land_size = 0;
                          if (!empty($otherland)) {
                              foreach ($otherland as $key => $value) {
                                  $other_land_size += $value['GardenHa'];
                              }
                          }
                          echo $other_land_size != 0 ? $other_land_size : '';
                        ?>
                     </p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="u145-4"><!-- content -->
            <p><?php echo strtoupper(lang('Jumlah hasil produksi pohon coklat')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u144"><!-- group -->
            <div class="clearfix grpelem" id="u295-4"><!-- content -->
               <p><?php echo $garden_size['PohonTM']>0?number_format($garden_size['PohonTM'],0,'.',','):'' ?></p>
            </div>
         </div>
         <div class="clearfix colelem" id="u167-4"><!-- content -->
            <p><?php echo strtoupper(lang('Usia Kebun (tahun)')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u166"><!-- group -->
            <div class="clearfix grpelem" id="u296-4"><!-- content -->
               <p>
                  <?php
                     if ($garden['TahunTanamanCocoa']) {
                        echo date('Y')-$garden['TahunTanamanCocoa'];
                     }
                  ?>
               </p>
            </div>
         </div>

         <div class="clearfix colelem" id="ppu180-4"><!-- group -->
            <div class="clearfix grpelem" id="pu180-4"><!-- column -->
               <div class="clearfix colelem" id="u180-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Tersertifikasi')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u179"><!-- group -->
                  <div class="clearfix grpelem" id="u297-4"><!-- content -->
                     <p>
                        <?php
                        if($garden['isCertification'] == 1) {
                           echo strtoupper(lang('Ya'));
                           switch ($garden['Certification']) {
                               case '1': echo 'UTZ'; break;
                               case '2': echo 'RAINFOREST'; break;
                               case '3': echo 'FAIRTRADE'; break;
                               case '4': echo strtoupper(lang('Organic')); break;
                           }
                        } else echo strtoupper(lang('Tidak'));
                        ?>
                     </p>
                  </div>
               </div>
            </div>
            <div class="grpelem" id="u247"><!-- simple frame --></div>
            <div class="clearfix grpelem" id="pu181-4"><!-- column -->
               <div class="clearfix colelem" id="u181-4"><!-- content -->
                  <p>TRADER</p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u182"><!-- group -->
                  <div class="clearfix grpelem" id="u298-4"><!-- content -->
                     <p><?php echo $farmer['is_trader'] == '1' ? strtoupper(lang('Ya')) : strtoupper(lang('Tidak')) ?></p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="u186-4"><!-- content -->
            <p><?php echo strtoupper(lang('Certification')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u185"><!-- group -->
            <div class="clearfix grpelem" id="u299-4"><!-- content -->
               <p>
                  <?php
                     if($garden['isCertification'] == 1) {
                        switch ($garden['Certification']) {
                           case '1': echo 'UTZ'; break;
                           case '2': echo 'RAINFOREST'; break;
                           case '3': echo 'FAIRTRADE'; break;
                           case '4': echo strtoupper(lang('Organic')); break;
                        }
                     }
                  ?>
               </p>
            </div>
         </div>

         <div class="clearfix colelem" id="u186-4"><!-- content -->
            <p><?php echo strtoupper(lang('Harga Coklat')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u185"><!-- group -->
            <div class="clearfix grpelem" id="u299-4"><!-- content -->
               <p><?php echo ($finance['CocoaPriceToday'] > 0)?number_format($finance['CocoaPriceToday'],0,'.',','):'' ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u189-4"><!-- content -->
            <p><?php echo strtoupper(lang('Cash Flow dari Coklat (ca.)')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u190"><!-- group -->
            <div class="clearfix grpelem" id="u300-4"><!-- content -->
               <p><?php echo ($finance['CocoaPriceToday']*$garden_size['Production'] > 0)?number_format($finance['CocoaPriceToday']*$garden_size['Production'],0,'.',','):'' ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u198-4"><!-- content -->
            <p><?php echo strtoupper(lang('Nilai Jual Kebun Coklat')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u197"><!-- group -->
            <div class="clearfix grpelem" id="u301-4"><!-- content -->
               <p><?php
                 switch ($finance['ValueCocoaFarm'] ) {
                     case '1': echo '< 10 '.strtoupper(lang('Million')); break;
                     case '2': echo '10 - 20 '.strtoupper(lang('Million')); break;
                     case '3': echo '20 - 50 '.strtoupper(lang('Million')); break;
                     case '4': echo '50 - 100 '.strtoupper(lang('Million')); break;
                     case '5': echo '100 - 200 '.strtoupper(lang('Million')); break;
                     case '6': echo '> 200 '.strtoupper(lang('Million')); break;
                     case '7': echo strtoupper(lang("Tidak tahu")); break;
                 }
               ?></p>
            </div>
         </div>

         <div class="clearfix colelem" id="u206-4"><!-- content -->
            <p><?php echo strtoupper(lang('Sertifikat Tanah Kebun')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u205"><!-- group -->
            <div class="clearfix grpelem" id="u302-4"><!-- content -->
               <p>
                  <?php
                     switch ($garden['LandCertificate']) {
                        case '1': echo strtoupper(lang('None')); break;
                        case '2': echo strtoupper(lang('Akte Notaris/BPN')); break;
                        case '3': echo strtoupper(lang('SKKT (Camat)')); break;
                        case '4': echo strtoupper(lang('Desa/Lurah')); break;
                        case '5': echo strtoupper(lang("Tidak tahu")); break;
                     }
                  ?>
               </p>
            </div>
         </div>

         <div class="clearfix colelem" id="pu229"><!-- group -->
            <div class="gradient rounded-corners clearfix grpelem" id="u229"><!-- group -->
               <div class="clearfix grpelem" id="u304-4"><!-- content -->
                  <p><?php echo $finance['BetterWetDriedBeans'] == '1' ? strtoupper(lang('Ya')) : strtoupper(lang('Tidak')) ?></p>
               </div>
            </div>
            <div class="gradient rounded-corners clearfix grpelem" id="u226"><!-- group -->
               <div class="clearfix grpelem" id="u303-4"><!-- content -->
                  <p><?php echo $finance['CocoaProfitableBusiness'] == '1' ? strtoupper(lang('Ya')) : strtoupper(lang('Tidak')) ?></p>
               </div>
            </div>
            <div class="clearfix grpelem" id="u228-4"><!-- content -->
               <p><?php echo strtoupper(lang('Jual Biji Coklat Basah')) ?></p>
            </div>
            <div class="clearfix grpelem" id="u227-4"><!-- content -->
               <p><?php echo strtoupper(lang('Kakao adalah bisnis yang menguntungkan')) ?></p>
            </div>
            <div class="grpelem" id="u251"><!-- simple frame --></div>
         </div>

         <!--
         <div class="clearfix colelem" id="u231-4">
            <p><?php echo strtoupper(lang('kategori')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u230">
            <div class="clearfix grpelem" id="u305-4">
               <p>SMALL</p>
            </div>
         </div>
         -->

         <div class="clearfix colelem" id="u233-4"><!-- content -->
            <p><?php echo strtoupper(lang('Ukuran Tanah')) ?></p>
         </div>
         <div class="clearfix colelem" id="pu307"><!-- group -->
            <div class="gradient rounded-corners clearfix grpelem" id="u307"><!-- group -->
               <div class="clearfix grpelem" id="u309-4"><!-- content -->
                  <p><?php echo strtoupper($garden_size['LandSize']) ?></p>
               </div>
            </div>
            <div class="grpelem" id="u308"><!-- simple frame --></div>
            <div class="clearfix grpelem" id="pu312-4"><!-- column -->
               <div class="clearfix colelem" id="u312-4"><!-- content -->
                  <p><?php echo strtoupper(lang('Professional')) ?></p>
               </div>
               <div class="gradient rounded-corners clearfix colelem" id="u311"><!-- group -->
                  <div class="clearfix grpelem" id="u310-4"><!-- content -->
                     <p><?php echo strtoupper($professionalism) ?></p>
                  </div>
               </div>
            </div>
         </div>

         <div class="clearfix colelem" id="u234-4"><!-- content -->
            <p><?php echo strtoupper(lang('Pengajuan Agunan')) ?></p>
         </div>
         <div class="gradient rounded-corners clearfix colelem" id="u235"><!-- group -->
            <div class="clearfix grpelem" id="u313-4"><!-- content -->
               <p><?php echo $finance['CollateralOfferedBank'] == '1'?'COCOA BEANS':'' ?></p>
            </div>
         </div>
         <div class="clip_frame clearfix colelem" id="u258"><!-- image -->
            <div id="u258_clip">
               <img class="position_content" id="u258_img" src="<?php echo base_url();?>assets/css/farmer_summary_loan/pasted_image_sc.jpg" alt="" width="139" height="60"/>
            </div>
         </div>

      </div> <!-- Batas akhir div kolom kanan -->

   </div> <!-- div tutup id = "ppu116-4" -->

</div>

<?php if($countData != $increData){?>
   <div class="page-break"></div>
   <br />
<?php }?>

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