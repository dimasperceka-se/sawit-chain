<?php
$baseurlnya = base_url();
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
   <head>
      <meta charset="utf-8"/>
      <title>P1/P2</title>
      <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/farmer-p1-p2/farmer-p1-p2.css"/>
      <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/farmer-p1-p2/farmer-p1-p2-media.css" media="print"/>
      <script src="<?php echo $baseurlnya; ?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
   </head>
   <body>
      <!-- begin page 1-->
      <div class="page">
         <!-- begin borderHitam -->
         <div class="borderHitam">
            <h2 class="judulHalaman"><?php echo lang('Survei Rumah Tangga Petani Sawit')?></h2>
            <h2 class="judulHalaman">P1. <?php echo lang('Data Dasar')?></h2>
            <br />
            <table width="100%">
               <tr>
                  <td class="bgBiru" width="20%"><?php echo lang('Tanggal')?></td>
                  <td width="30%"><?php echo !is_null($basicData->DateCollection) ? date('d, F Y', strtotime($basicData->DateCollection)) : ''; ?></td>
                  <td class="bgBiru" width="15%"><?php echo lang('No. ID Petani')?></td>
                  <td width="15%"><?php echo !is_null($basicData->FarmerUID) ? $basicData->FarmerUID : ''; ?></td>
               </tr>
               <tr>
                  <td class="bgBiru" width="15%"><?php echo lang('No. ID Kelompok Tani')?></td>
                  <td width="15%"><?php echo !is_null($basicData->GapoktanID) ? $basicData->GapoktanID : ''; ?></td>
                  <td class="bgBiru" width="15%"><?php echo lang('Nama Kelompok Tani')?></td>
                  <td><?php echo !is_null($basicData->GapoktanName) ? $basicData->GapoktanName : ''; ?></td>
               </tr>
            </table>
            <br />
         </div>
         <!-- end borderHitam -->
         <br />
         <table width="100%">
            <tr>
               <td class="tabelHeader" colspan="2">
                  A. <?php echo lang('Informasi Umum')?>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Nama Petani')?></td>
               <td height="35">
                  <table width="100%" height=100% class="tabelNoBorder">
                     <tr>
                        <td width="50%"><?php echo !is_null($basicData->MemberName) ? $basicData->MemberName : ''; ?></td>
                        <td style="border-left:1px solid black;"><?php echo lang('Tanda Tangan')?> :</td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nomor Kartu Tanda Penduduk')?></td>
               <td class="td-with-value"><?php echo !is_null($basicData->Nin) ? $basicData->Nin : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Tanggal Lahir')?></td>
               <td>
                  <?php echo (!is_null($basicData->DateOfBirth) && $basicData->DateOfBirth != '00-00-0000' ) ? date('d, F Y', strtotime($basicData->DateOfBirth)) : ''; ?>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Jenis Kelamin')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="gender<?php echo $key ?>" <?php echo (!is_null($basicData->Gender) && $basicData->Gender == 'm') ? 'checked' : ''; ?> />1. <?php echo lang('Laki-laki')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="gender<?php echo $key ?>" <?php echo (!is_null($basicData->Gender) && $basicData->Gender == 'f') ? 'checked' : ''; ?> />2. <?php echo lang('Perempuan')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Status Perkawinan')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="30%">
                           <div class="flexBox"><input type="radio" name="statusMenikah<?php echo $key ?>" value="" <?php echo (!is_null($basicData->MaritalStatus) && $basicData->MaritalStatus == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Menikah')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="statusMenikah<?php echo $key ?>" value="" <?php echo (!is_null($basicData->MaritalStatus) && $basicData->MaritalStatus == 2) ? 'checked' : ''; ?>/>2. <?php echo lang('Lajang')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="statusMenikah<?php echo $key ?>" value="" <?php echo (!is_null($basicData->MaritalStatus) && $basicData->MaritalStatus == 3) ? 'checked' : ''; ?>/>3. <?php echo lang('Janda/Duda')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" style="vertical-align: top;"><?php echo lang('Pendidikan Terakhir')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="30%">
                           <div class="flexBox">
                              <input type="radio" name="pendidikanTerakhir<?php echo $key ?>" value="" <?php echo (!is_null($basicData->Education) && $basicData->Education == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Tidak Pernah Sekolah')?>
                           </div>
                        </td>
                        <td width="30%">
                           <div class="flexBox">
                              <input type="radio" name="pendidikanTerakhir<?php echo $key ?>" value="" <?php echo (!is_null($basicData->Education) && $basicData->Education == 4) ? 'checked' : ''; ?> />4. <?php echo lang('Tamat SMP')?>
                           </div>
                        </td>
                        <td width="40%">&nbsp;</td>
                     </tr>
                     <tr>
                        <td>
                           <div class="flexBox">
                              <input type="radio" name="pendidikanTerakhir<?php echo $key ?>" value="" <?php echo (!is_null($basicData->Education) && $basicData->Education == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak Tamat SD')?>
                           </div>
                        </td>
                        <td>
                           <div class="flexBox">
                              <input type="radio" name="pendidikanTerakhir<?php echo $key ?>" value="" <?php echo (!is_null($basicData->Education) && $basicData->Education == 5) ? 'checked' : ''; ?> />5. <?php echo lang('Tamat SMA')?>
                           </div>
                        </td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>
                           <div class="flexBox">
                              <input type="radio" name="pendidikanTerakhir<?php echo $key ?>" value="" <?php echo (!is_null($basicData->Education) && $basicData->Education == 3) ? 'checked' : ''; ?> />3. <?php echo lang('Tamat SD')?>
                           </div>
                        </td>
                        <td>
                           <div class="flexBox">
                              <input type="radio" name="pendidikanTerakhir<?php echo $key ?>" value="" <?php echo (!is_null($basicData->Education) && $basicData->Education == 6) ? 'checked' : ''; ?> />6. <?php echo lang('Tamat Perguruan Tinggi')?>
                           </div>
                        </td>
                        <td></td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nama Propinsi')?></td>
               <td class="td-with-value"><?php echo (!is_null($basicData->Province)) ? $basicData->Province : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nama Kabupaten')?></td>
               <td class="td-with-value"><?php echo (!is_null($basicData->District)) ? $basicData->District : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nama Kecamatan')?></td>
               <td class="td-with-value"><?php echo (!is_null($basicData->SubDistrict)) ? $basicData->SubDistrict : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nama Desa')?></td>
               <td class="td-with-value"><?php echo (!is_null($basicData->Village)) ? $basicData->Village : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nama Dusun')?></td>
               <td class="td-with-value"><?php echo (!is_null($basicData->Address)) ? $basicData->Address : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nomor Handphone')?></td>
               <td class="td-with-value"><?php echo !is_null($basicData->Handphone) ? $basicData->Handphone : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Akses ke phone printer/smartphone')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="aksesToSmartphone<?php echo $key ?>" value="" <?php echo (!is_null($basicData->AccessToSmartphone) && $basicData->AccessToSmartphone == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="aksesToSmartphone<?php echo $key ?>" value="" <?php echo (!is_null($basicData->AccessToSmartphone) && $basicData->AccessToSmartphone == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah Anda bekerja di perkebunan sendiri')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="WorkInPlot<?php echo $key ?>" value="" <?php echo (!is_null($basicData->WorkInPlot) && $basicData->WorkInPlot == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="WorkInPlot<?php echo $key ?>" value="" <?php echo (!is_null($basicData->WorkInPlot) && $basicData->WorkInPlot == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah para pekerja menggunakan PPE ketika bekerja')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="UseAPD<?php echo $key ?>" value="" <?php echo (!is_null($basicData->UseAPD) && $basicData->UseAPD == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="UseAPD<?php echo $key ?>" value="" <?php echo (!is_null($basicData->UseAPD) && $basicData->UseAPD == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Pernahkah Anda mengalami kecelakaan saat bekerja')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="HadAccident<?php echo $key ?>" value="" <?php echo (!is_null($basicData->HadAccident) && $basicData->HadAccident == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HadAccident<?php echo $key ?>" value="" <?php echo (!is_null($basicData->HadAccident) && $basicData->HadAccident == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Kecelakan apa')?></td>
               <td><?php echo (!is_null($basicData->WhatAccident)) ? $basicData->WhatAccident : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah anda punya BPJS')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="haveBPJS<?php echo $key ?>" value="" <?php echo (!is_null($basicData->HaveBPJS) && $basicData->HaveBPJS == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="haveBPJS<?php echo $key ?>" value="" <?php echo (!is_null($basicData->HaveBPJS) && $basicData->HaveBPJS == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah anggota kelompok tani')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="inGroup<?php echo $key ?>" value="" <?php echo (!is_null($basicData->inGroup) && $basicData->inGroup == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="inGroup<?php echo $key ?>" value="" <?php echo (!is_null($basicData->inGroup) && $basicData->inGroup == 0) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Kelompok Tani')?></td>
               <td><?php echo (!is_null($basicData->groupName)) ? $basicData->groupName : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah anggota gapoktan')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="inGapoktan<?php echo $key ?>" value="" <?php echo (!is_null($basicData->inGapoktan) && $basicData->inGapoktan == 0) ? 'checked' : ''; ?>/>1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="inGapoktan<?php echo $key ?>" value="" <?php echo (!is_null($basicData->inGapoktan) && $basicData->inGapoktan == 0) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nama Gapoktan')?></td>
               <td><?php echo (!is_null($basicData->GapoktanName)) ? $basicData->GapoktanName : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah anggota koperasi')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="inCoop<?php echo $key ?>" value="" <?php echo (!is_null($basicData->inCoop) && $basicData->inCoop == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="inCoop<?php echo $key ?>" value="" <?php echo (!is_null($basicData->inCoop) && $basicData->inCoop == 0) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nama Koperasi')?></td>
               <td class="td-with-value"><?php echo !is_null($basicData->CoopName) ? $basicData->CoopName: ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Anda memiliki berapa kebun kelapa sawit')?></td>
               <td class="td-with-value"><?php echo !is_null($basicData->HowManyPlot) ? $basicData->HowManyPlot: ''; ?></td>
            </tr>
         </table>
         <footer>
            <?php echo lang('Page').' 1';?>
         </footer>
      </div>
      <!-- end page 1-->
      <!-- begin page 2 -->
      <div class="page">
         <br>
         <table width="100%">
             <tr>
                 <td class="tabelHeader" colspan="13">
                     B. <?php echo lang('Data Keluarga')?>
                 </td>
             </tr>
             <tr>
                 <td class="topTabelValue" width="3%">No</td>
                 <td width="15%" class="topTabelValue"><?php echo lang('Nama Anggota Keluarga')?></td>
                 <td width="9%" class="topTabelValue">
                     <?php echo lang('Jenis Kelamin')?><br />
                     <em>
                         <?php echo '1. '.lang('Laki-laki')?><br />
                         <?php echo '2. '.lang('Perempuan')?>
                     </em>
                 </td>
                 <td class="topTabelValue"><?php echo lang('Tahun Lahir')?></td>
                 <td width="10%" class="topTabelValue">
                     <?php echo lang('Hubungan')?>
                     <br />
                     <em>
                         <?php echo '1. '.lang('Suami/Istri')?><br />
                         <?php echo '2. '.lang('Anak')?><br />
                         <?php echo '3. '.lang('Lain-lain')?><br />
                     </em>
                 </td>
                 <td width="9%" class="topTabelValue">
                     <?php echo lang('Masih Sekolah')?><br />
                     <em>
                         <?php echo '1. '.lang('Ya')?><br />
                         <?php echo '2. '.lang('Tidak')?>
                     </em>
                 </td>
                 <td width="9%" class="topTabelValue">
                     <?php echo lang('Ikut bekerja di kebun')?><br />
                     <em>
                     <?php echo '1. '.lang('Ya, Full Time')?><br />
                     <?php echo '2. '.lang('Tidak')?><br />
                     <?php echo '3. '.lang('Ya, Part Time')?>
                     </em>
                 </td>
                 <td width="9%" class="topTabelValue"><?php echo lang('Apa alasan anggota keluarga bekerja diperkebunan')?></td>
                 <td class="topTabelValue"><?php echo lang('Jumlah Jam Kerja per hari')?></td>
                 <td class="topTabelValue"><?php echo lang('Total hari kerja')?></td>
                 <td width="15%" class="topTabelValue">
                     <?php echo lang('Jenis kegiatan')?><br />
                     <em>
                     <?php echo '1. '.lang('Pembibitan')?><br />
                     <?php echo '2. '.lang('Babat')?><br />
                     <?php echo '3. '.lang('Buka Piringan')?><br />
                     <?php echo '4. '.lang('Pemangkasan')?><br />
                     <?php echo '5. '.lang('Pemupukan')?><br />
                     <?php echo '6. '.lang('Pestisida')?><br />
                     <?php echo '7. '.lang('Panen')?><br />
                     <?php echo '8. '.lang('Mengangkut')?><br />
                     </em>
                 </td>
                 <td width="8%" class="topTabelValue"><?php echo lang('Upah Perbulan (Rp)')?></td>
                 <td width="8%" class="topTabelValue"><?php echo lang('Periode Upah')?></td>
             </tr>
             <?php if (count($family) > 0) {
               # code...
               for ($i=0; $i < 10 ; $i++) { 
                  # code...
                  echo "<tr>";
                     echo "<td>".($i+1)."</td>";
                     echo "<td>".$family[$i]['FamLabName']."</td>";
                     echo "<td>".$family[$i]['YearOfBirth']."</td>";
                     echo "<td>".$family[$i]['Gender']."</td>";
                     echo "<td>".lang($family[$i]['FamLabRelation'])."</td>";
                     echo "<td>".lang($family[$i]['InSchool'])."</td>";
                     echo "<td>".lang($family[$i]['WorkingStatus'])."</td>";
                     echo "<td>".lang($family[$i]['ReasonFamilyWork'])."</td>";
                     echo "<td>".lang($family[$i]['TotalWorkingHrsPerDay'])."</td>";
                     echo "<td>".lang($family[$i]['TotalWorkingHrsPerMonth'])."</td>";
                     echo "<td>".lang($family[$i]['ActivityType'])."</td>";
                     echo "<td>".lang($family[$i]['WageCurr'])."</td>";
                     echo "<td>".lang($family[$i]['WagePeriode'])."</td>";
                  echo "</tr>";
                }
             } else {
               # code...
               for ($i=1; $i <= 10 ; $i++) { 
                  # code...
                  echo "<tr>
                          <td>$i.</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                      </tr>";
               }
             }
             ?>
         </table>
         <br>
         <table width="100%">
             <tr>
                 <td class="tabelHeader">
                     C. <?php echo lang('Data Pekerja')?>
                 </td>
             </tr>
             <tr>
               <td>
                  <br>
                  <table>
                     <tr>
                        <td class="leftValue" width="15%"><?php echo lang('Apakah anda punya pekerja (musiman/permanen)')?></td>
                        <td>
                           <table width="100%" height="100%" class="tabelNoBorder" border="0">
                              <tr>
                                 <td width="40%">
                                    <div class="flexBox"><input type="radio" name="doYouHaveWorker<?php echo $key ?>" value="" <?php echo ($memberExtension->labHaveWorkers == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="doYouHaveWorker<?php echo $key ?>" value="" <?php echo ($memberExtension->labHaveWorkers == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td class="leftValue"><?php echo lang('Anda memiliki berapa pekerja')?></td>
                        <td class="td-with-value"><?php echo lang($memberExtension->labHowManyWorker); ?></td>
                     </tr>
                     <tr>
                        <td class="leftValue" width="15%"><?php echo lang('Apakah pekerja anda menggunakan APD')?></td>
                        <td>
                           <table width="100%" height="100%" class="tabelNoBorder" border="0">
                              <tr>
                                 <td width="40%">
                                    <div class="flexBox"><input type="radio" name="workerUsePPE<?php echo $key ?>" value="" <?php echo ($memberExtension->labWorkerUseApd == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="workerUsePPE<?php echo $key ?>" value="" <?php echo ($memberExtension->labWorkerUseApd == 0) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td class="leftValue" width="15%"><?php echo lang('Siapa yang membeli APD tersebut')?></td>
                        <td>
                           <table width="100%" height="100%" class="tabelNoBorder" border="0">
                              <tr>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="whoBuyPPE<?php echo $key ?>" value="" <?php echo ($memberExtension->labWhoBuyApd == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Pemberi kerja')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="whoBuyPPE<?php echo $key ?>" value="" <?php echo ($memberExtension->labWhoBuyApd == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Para pekerja')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="whoBuyPPE<?php echo $key ?>" value="" <?php echo ($memberExtension->labWhoBuyApd == 3) ? 'checked' : ''; ?> />3. <?php echo lang('N/A')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td class="leftValue" width="15%"><?php echo lang('Apakah pekerja anda pernah mengalami kecelakan saat bekerja')?></td>
                        <td>
                           <table width="100%" height="100%" class="tabelNoBorder" border="0">
                              <tr>
                                 <td width="40%">
                                    <div class="flexBox"><input type="radio" name="accident<?php echo $key ?>" value="" <?php echo ($memberExtension->labWorkerHadAccident == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="accident<?php echo $key ?>" value="" <?php echo ($memberExtension->labWorkerHadAccident == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td class="leftValue" width="15%"><?php echo lang('Kecelakaan apa')?></td>
                        <td class="td-with-value"><?php echo $memberExtension->labWhatAccident; ?></td>
                     </tr>
                     <tr>
                        <td class="leftValue" width="15%"><?php echo lang('Apakah pekerja anda memiliki BPJS')?></td>
                        <td>
                           <table width="100%" height="100%" class="tabelNoBorder" border="0">
                              <tr>
                                 <td width="40%">
                                    <div class="flexBox"><input type="radio" name="haveBPJS<?php echo $key ?>" value="" <?php echo ($memberExtension->labWorkerHaveBpjs == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Ya')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="haveBPJS<?php echo $key ?>" value="" <?php echo ($memberExtension->labWorkerHaveBpjs == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Tidak')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td class="leftValue" width="15%"><?php echo lang('Siapa yang membayar BPJS')?></td>
                        <td>
                           <table width="100%" height="100%" class="tabelNoBorder" border="0">
                              <tr>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="whoPayBPJS<?php echo $key ?>" value="" <?php echo ($memberExtension->labWhoPayBpjs == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Pemberi kerja')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="whoPayBPJS<?php echo $key ?>" value="" <?php echo ($memberExtension->labWhoPayBpjs == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Para pekerja')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="whoPayBPJS<?php echo $key ?>" value="" <?php echo ($memberExtension->labWhoPayBpjs == 3) ? 'checked' : ''; ?> />3. <?php echo lang('N/A')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td class="leftValue" width="15%"><?php echo lang('Siapa yang memberikan penjelasan tentang K3 kepada staff anda')?></td>
                        <td>
                           <table width="100%" height="100%" class="tabelNoBorder" border="0">
                              <tr>
                                 <td width="40%">
                                    <div class="flexBox"><input type="radio" name="explainK3<?php echo $key ?>" value="" <?php echo ($memberExtension->labGiveInfoHealthSafety == 1) ? 'checked' : ''; ?> />1. <?php echo lang('Tidak ada penjelasan')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="explainK3<?php echo $key ?>" value="" <?php echo ($memberExtension->labGiveInfoHealthSafety == 2) ? 'checked' : ''; ?> />2. <?php echo lang('Saya jelaskan')?></div>
                                 <td width="13%">
                                 <td>
                                    <div class="flexBox"><input type="radio" name="explainK3<?php echo $key ?>" value="" <?php echo ($memberExtension->labGiveInfoHealthSafety == 3) ? 'checked' : ''; ?> />3. <?php echo lang('Pihak ketiga')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
                  <br>
                  <table>
                     <tr>
                        <td class="topTabelValue" width="3%">No</td>
                        <td width="15%" class="topTabelValue"><?php echo lang('Nama')?></td>
                        <td width="9%" class="topTabelValue">
                           <?php echo lang('Jenis Kelamin')?><br />
                           <em>
                           <?php echo '1. '.lang('Laki-laki')?><br />
                           <?php echo '2. '.lang('Perempuan')?>
                           </em>
                        </td>
                        <td width="9%" class="topTabelValue"><?php echo lang('Umur')?></td>
                        <td width="9%" class="topTabelValue"><?php echo lang('Wage Amount (IDR)')?></td>
                        <td width="9%" class="topTabelValue"><?php echo lang('Wage Period')?></td>
                     </tr>
                     <?php
                        for ($i=0; $i < 10 ; $i++) { 
                           # code...
                           echo "<tr>";
                              echo "<td>".($i+1)."</td>";
                              echo "<td>".$labour[$i]['LaboName']."</td>";
                              if ($labour[$i]['Gender'] == 'm') {
                                 # code...
                                 echo "<td>".lang('Laki-laki')."</td>";
                              } elseif ($labour[$i]['Gender'] == 'f') {
                                 # code...
                                 echo "<td>".lang('Perempuan')."</td>";
                              } else {
                                 echo "<td></td>";
                              }
                              
                              echo "<td>".$labour[$i]['Age']."</td>";
                              echo "<td>".lang($labour[$i]['WageAmount'])."</td>";
                              echo "<td>".lang($labour[$i]['WagePeriod'])."</td>";
                           echo "</tr>";
                         }
                     ?>
                  </table>
               </td>
             </tr>
         </table>
         <footer>
            <?php echo lang('Page').' 2';?>
         </footer>
      </div>
      <!-- end page 2 -->
      <!-- begin page 3-->
      <div class="page">
         <br>
          <table width="100%">
             <tr>
                 <td class="tabelHeader" colspan="6">
                     D. <?php echo lang('Komoditas Lain')?>
                 </td>
             </tr>
             <tr>
               <td class="topTabelValue" width="4%"><?php echo lang('No')?></td>
                 <td class="topTabelValue" width="4%"><?php echo lang('No Kebun')?></td>
                 <td class="topTabelValue" width="8%"><?php echo lang('Komoditas')?></td>
                 <td class="topTabelValue" width="4%"><?php echo lang('Ukuran (Ha)')?></td>
                 <td class="topTabelValue" width="16%"><?php echo lang('Catatan')?></td>
             </tr>
            <?php for($i = 0; $i < 10; $i++){
               echo "<tr>";
                  echo "<td>".($i+1)."</td>";
                  echo "<td>".lang($otherLand[$i]['MemOtherID'])."</td>";
                  echo "<td>".lang($otherLand[$i]['Commodity'])."</td>";
                  echo "<td>".lang($otherLand[$i]['GardenHa'])."</td>";
                  echo "<td>".lang($otherLand[$i]['Remark'])."</td>";
               echo "</tr>";
            } ?>
          </table>
         <br>
         <table width="100%">
             <tr>
                 <td class="tabelHeader" colspan="2">
                     E. <?php echo lang('Household Survey')?>
                 </td>
             </tr>
             <tr>
               <td class="leftValue" width="15%"><?php echo lang('Survey Nr')?></td>
               <td class="td-with-value"><?php echo (!is_null($surveyHousehold->SurveyNr)) ? $surveyHousehold->SurveyNr : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Tanggal Koleksi')?></td>
               <td class="td-with-value"><?php echo (!is_null($surveyHousehold->DateCollection)) ? date('d, F Y', strtotime($surveyHousehold->DateCollection)) : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Berapa jumlah anggota rumah tangga')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HhMember<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhMember) && $surveyHousehold->HhMember == 1) ? 'checked' : '' ?> />1. <?php echo lang('Enam atau lebih')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HhMember<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhMember) && $surveyHousehold->HhMember == 2) ? 'checked' : '' ?> />2. <?php echo lang('Lima')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HhMember<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhMember) && $surveyHousehold->HhMember == 3) ? 'checked' : '' ?> />3. <?php echo lang('Empat')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HhMember<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhMember) && $surveyHousehold->HhMember == 4) ? 'checked' : '' ?> />4. <?php echo lang('Tiga')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HhMember<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhMember) && $surveyHousehold->HhMember == 5) ? 'checked' : '' ?> />5. <?php echo lang('Dua')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HhMember<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhMember) && $surveyHousehold->HhMember == 6) ? 'checked' : '' ?> />6. <?php echo lang('Satu')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah semua anggota rumah tangga yang berusia 6 - 18 tahun masih bersekolah')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HhInSchoolEarlyAge<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhInSchoolEarlyAge) && $surveyHousehold->HhInSchoolEarlyAge == 1) ? 'checked' : '' ?> />1. <?php echo lang('Tidak ada anak usia 6-18 tahun')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HhInSchoolEarlyAge<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhInSchoolEarlyAge) && $surveyHousehold->HhInSchoolEarlyAge == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HhInSchoolEarlyAge<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HhInSchoolEarlyAge) && $surveyHousehold->HhInSchoolEarlyAge == 3) ? 'checked' : '' ?> />3. <?php echo lang('Ya')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah tingkat pendidikan terakhir yang diselesaikan oleh pasangan')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="FemaleEduLevel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->FemaleEduLevel) && $surveyHousehold->FemaleEduLevel == 1) ? 'checked' : '' ?> />1. <?php echo lang('Belum pernah sekolah')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="FemaleEduLevel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->FemaleEduLevel) && $surveyHousehold->FemaleEduLevel == 2) ? 'checked' : '' ?> />2. <?php echo lang('SD/SDLB, Madrasah Ibtidaiyah, atau Paket A')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="FemaleEduLevel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->FemaleEduLevel) && $surveyHousehold->FemaleEduLevel == 3) ? 'checked' : '' ?> />3. <?php echo lang('SMP/SMPLB, Madrasah Tsanawiayh, atau Paket B')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="FemaleEduLevel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->FemaleEduLevel) && $surveyHousehold->FemaleEduLevel == 4) ? 'checked' : '' ?> />4. <?php echo lang('Tidak ada kepala rumah tangga perempuan/istri')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="FemaleEduLevel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->FemaleEduLevel) && $surveyHousehold->FemaleEduLevel == 5) ? 'checked' : '' ?> />5. <?php echo lang('Sekolah Menengah Kejuruan')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="FemaleEduLevel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->FemaleEduLevel) && $surveyHousehold->FemaleEduLevel == 6) ? 'checked' : '' ?> />6. <?php echo lang('SMA/SMALB, Madrasah Aliyah, atau Paket C')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="FemaleEduLevel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->FemaleEduLevel) && $surveyHousehold->FemaleEduLevel == 7) ? 'checked' : '' ?> />7. <?php echo lang('D1, D2, D3/Sarjana Muda, D4, S1, S2, S3')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah status pekerjaan utama dari kepala rumah tangga laki-laki /suami seminggu terakhir')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="MaleMainOccu<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->MaleMainOccu) && $surveyHousehold->MaleMainOccu == 1) ? 'checked' : '' ?> />1. <?php echo lang('Tidak ada kepala rumah tangga laki-laki/suami')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="MaleMainOccu<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->MaleMainOccu) && $surveyHousehold->MaleMainOccu == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak bekerja atau pekerja keluarga/pekerja tidak dibayar')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="MaleMainOccu<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->MaleMainOccu) && $surveyHousehold->MaleMainOccu == 3) ? 'checked' : '' ?> />3. <?php echo lang('Pekerja bebas')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="MaleMainOccu<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->MaleMainOccu) && $surveyHousehold->MaleMainOccu == 4) ? 'checked' : '' ?> />4. <?php echo lang('Berusaha sendiri atau berusaha dibantu buruh tidak tetap/buruh tidak dibayar')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="MaleMainOccu<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->MaleMainOccu) && $surveyHousehold->MaleMainOccu == 5) ? 'checked' : '' ?> />5. <?php echo lang('Buruh/karyawan/pegawai')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="MaleMainOccu<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->MaleMainOccu) && $surveyHousehold->MaleMainOccu == 6) ? 'checked' : '' ?> />6. <?php echo lang('Pemilik bisnis dengan pekerja tetap atau dibayar')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apa jenis lantai rumah tangga anda')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="TypeOfFloor<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->TypeOfFloor) && $surveyHousehold->TypeOfFloor == 1) ? 'checked' : '' ?> />1. <?php echo lang('Tanah atau Bambu')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="TypeOfFloor<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->TypeOfFloor) && $surveyHousehold->TypeOfFloor == 2) ? 'checked' : '' ?> />2. <?php echo lang('Lainnya')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apa jenis toilet / wc dirumah tangga anda')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="TypeOfToilet<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->TypeOfToilet) && $surveyHousehold->TypeOfToilet == 1) ? 'checked' : '' ?> />1. <?php echo lang('Tidak ada atau jamban')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="TypeOfToilet<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->TypeOfToilet) && $surveyHousehold->TypeOfToilet == 2) ? 'checked' : '' ?> />2. <?php echo lang('Ada kloset, tapi tidak tersambung ke septic tank (plengsengan)')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="TypeOfToilet<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->TypeOfToilet) && $surveyHousehold->TypeOfToilet == 3) ? 'checked' : '' ?> />3. <?php echo lang('Leher angsa/ Toilet duduk')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apa jenis bahan bakar utama di rumah anda')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="PrimaryFuel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->PrimaryFuel) && $surveyHousehold->PrimaryFuel == 1) ? 'checked' : '' ?> />1. <?php echo lang('Kayu Bakar, arang atau briket')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="PrimaryFuel<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->PrimaryFuel) && $surveyHousehold->PrimaryFuel == 2) ? 'checked' : '' ?> />2. <?php echo lang('Gas/LPG, minyak tanah, listrik, lainnya atau tidak masak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda memiliki tabung gas 12 kg atau lebih')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="Own12KgGas<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->Own12KgGas) && $surveyHousehold->Own12KgGas == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="Own12KgGas<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->Own12KgGas) && $surveyHousehold->Own12KgGas == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda memiliki kulkas')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnRefri<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnRefri) && $surveyHousehold->OwnRefri == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnRefri<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnRefri) && $surveyHousehold->OwnRefri == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda memiliki sepeda motor atau perahu pribadi')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnMotor<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnMotor) && $surveyHousehold->OwnMotor == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnMotor<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnMotor) && $surveyHousehold->OwnMotor == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda memiliki rekening')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HaveBankAccount<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HaveBankAccount) && $surveyHousehold->HaveBankAccount == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HaveBankAccount<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->HaveBankAccount) && $surveyHousehold->HaveBankAccount == 0) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda menggunakan mobile / sms banking')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="UseMobileBanking<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->UseMobileBanking) && $surveyHousehold->UseMobileBanking == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="UseMobileBanking<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->UseMobileBanking) && $surveyHousehold->UseMobileBanking == 0) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda memiliki mobil pribadi')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnPrivateCar<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnPrivateCar) && $surveyHousehold->OwnPrivateCar == 2) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnPrivateCar<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnPrivateCar) && $surveyHousehold->OwnPrivateCar == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda memiliki listrik PLN')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnGriddedElectricity<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnGriddedElectricity) && $surveyHousehold->OwnGriddedElectricity == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnGriddedElectricity<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnGriddedElectricity) && $surveyHousehold->OwnGriddedElectricity == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda memiliki komputer')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnComputer<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnComputer) && $surveyHousehold->OwnComputer == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnComputer<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnComputer) && $surveyHousehold->OwnComputer == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" width="15%"><?php echo lang('Apakah rumah tangga anda memiliki AC')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnAC<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnAC) && $surveyHousehold->OwnAC == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnAC<?php echo $key ?>" value="" <?php echo (!is_null($surveyHousehold->OwnAC) && $surveyHousehold->OwnAC == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
          <footer>
            <?php echo lang('Page').' 3';?>
         </footer>
      </div>
      <!-- end page 3 -->
      <!-- begin page 3 -->
      <?php
         foreach($surveyPlot as $plot){
      ?>
      <div class="page">
         <div class="borderHitam">
            <h2 class="judulHalaman"><?php echo lang('Survei Rumah Tangga Petani Sawit')?></h2>
            <h2 class="judulHalaman">P2. <?php echo lang('Data Kebun')?></h2>
            <br />
            <table width="50%">
               <tr>
                  <td class="bgBiru" width="20%"><?php echo lang('No Kebun')?></td>
                  <td class="td-with-value"><?php echo (!is_null($plot->PlotNr)) ? $plot->PlotNr : '';  ?></td>
               </tr>
               <tr>
                  <td class="bgBiru" width="20%"><?php echo lang('No Survey')?></td>
                  <td class="td-with-value"><?php echo (!is_null($plot->SurveyNr)) ? $plot->SurveyNr : '';  ?></td>
               </tr>
               <tr>
                  <td class="bgBiru" width="20%"><?php echo lang('Tanggal Koleksi')?></td>
                  <td class="td-with-value"><?php echo (!is_null($plot->DateCollection)) ? date('d, F Y', strtotime($plot->DateCollection)) : '';  ?></td>
               </tr>
            </table>
            <br />
         </div>
         <br>
         <table width="100%">
            <tr>
               <td class="tabelHeader" colspan="2">
                  A. <?php echo lang('Kebun')?>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Provinsi')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->Province)) ? $plot->Province : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Kabupaten')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->District)) ? $plot->District : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Kecamatan')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->SubDistrict)) ? $plot->SubDistrict : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Desa')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->Village)) ? $plot->Village : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Luas Kebun (Ha)')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->GardenAreaHa)) ? $plot->GardenAreaHa : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Area of Garden Polygon (Ha)')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->GardenAreaPolygon)) ? $plot->GardenAreaPolygon : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Latitude')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->Latitude)) ? $plot->Latitude : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Longitude')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->Longitude)) ? $plot->Longitude : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Kepemilikan Tanah')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="LandOwnershipType<?php echo $key ?>" value="" <?php echo (!is_null($plot->LandOwnershipType) && $plot->LandOwnershipType == 1) ? 'checked' : '' ?> />1. <?php echo lang('Milik Sendiri')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="LandOwnershipType<?php echo $key ?>" value="" <?php echo (!is_null($plot->LandOwnershipType) && $plot->LandOwnershipType == 2) ? 'checked' : '' ?> />2. <?php echo lang('Petani Bagi Hasil')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="LandOwnershipType<?php echo $key ?>" value="" <?php echo (!is_null($plot->LandOwnershipType) && $plot->LandOwnershipType == 3) ? 'checked' : '' ?> />3. <?php echo lang('Sewa')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="LandOwnershipType<?php echo $key ?>" value="" <?php echo (!is_null($plot->LandOwnershipType) && $plot->LandOwnershipType == 4) ? 'checked' : '' ?> />4. <?php echo lang('Lainnya')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Kepemilikan Kebun')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnerOfTheGarden<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnerOfTheGarden) && $plot->OwnerOfTheGarden == 1) ? 'checked' : '' ?> />1. <?php echo lang('Saya Sendiri')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnerOfTheGarden<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnerOfTheGarden) && $plot->OwnerOfTheGarden == 2) ? 'checked' : '' ?> />2. <?php echo lang('Anggota Keluarga')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnerOfTheGarden<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnerOfTheGarden) && $plot->OwnerOfTheGarden == 3) ? 'checked' : '' ?> />3. <?php echo lang('Orang Lain')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnerOfTheGarden<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnerOfTheGarden) && $plot->OwnerOfTheGarden == 4) ? 'checked' : '' ?> />4. <?php echo lang('Tidak Diketahui')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Nama')?></td>
               <td>
                  <?php 
                     
                     if ($plot->OwnerOfTheGarden == 1) {
                        # code...
                        echo $basicData->MemberName;
                       } else {
                        # code...
                        echo (!is_null($plot->OwnerOfPlantationNameText)) ? $plot->OwnerOfPlantationNameText : '';
                       }
                         
                  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Lokasi')?></td>
               <td>
                  <?php
                     if ($plot->OwnerOfTheGarden == 1) {
                        # code...
                        echo $plot->SubDistrict.', '.$plot->Village;
                       } else {
                        # code...
                        echo (!is_null($plot->OwnerOfPlantationLocationText)) ? $plot->OwnerOfPlantationLocationText : '';
                       }  
                  ?>
                     
                  </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Telepon')?></td>
               <td><?php echo (!is_null($plot->OwnerOfPlantationPhoneText)) ? $plot->OwnerOfPlantationPhoneText : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Status kepemilikan kebun')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnershipDoc<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnershipDoc) && $plot->OwnershipDoc == 1) ? 'checked' : '' ?> />1. <?php echo lang('Tidak ada dokumen')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnershipDoc<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnershipDoc) && $plot->OwnershipDoc == 2) ? 'checked' : '' ?> />2. <?php echo lang('SKT (Surat Keterangan Tanah)')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnershipDoc<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnershipDoc) && $plot->OwnershipDoc == 3) ? 'checked' : '' ?> />3. <?php echo lang('SHM (Sertifikat Hak Milik) / Certificate')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnershipDoc<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnershipDoc) && $plot->OwnershipDoc == 4) ? 'checked' : '' ?> />4. <?php echo lang('HGU (Hak Guna Usaha)')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnershipDoc<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnershipDoc) && $plot->OwnershipDoc == 5) ? 'checked' : '' ?> />5. <?php echo lang('SKGR (Surat Keterangan Ganti Rugi)')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnershipDoc<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnershipDoc) && $plot->OwnershipDoc == 6) ? 'checked' : '' ?> />6. <?php echo lang('Lainnya').': ';?>&nbsp;&nbsp;<input type="text" name="" value="<?php echo (!is_null($plot->OwnershipDocText)) ? $plot->OwnershipDocText : '';  ?>"></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Do you have witnesses to prove the plot ownership')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="workerUsePPE<?php echo $key ?>" value="" />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="workerUsePPE<?php echo $key ?>" value="" />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Name of The Witness')?></td>
               <td>
                  &nbsp;
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Owner Relationship with The Witness')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="workerUsePPE<?php echo $key ?>" value="" />1. <?php echo lang('Keluarga')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="workerUsePPE<?php echo $key ?>" value="" />2. <?php echo lang('Official Witness')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="workerUsePPE<?php echo $key ?>" value="" />3. <?php echo lang('Institution leader (village, farmer group, religion etc)')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="workerUsePPE<?php echo $key ?>" value="" />4. <?php echo lang('Lainnya')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah dokumen kepemilikan atas nama pemilik kebun')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnerDocIsOwner<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnerDocIsOwner) && $plot->OwnerDocIsOwner == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="OwnerDocIsOwner<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnerDocIsOwner) && $plot->OwnerDocIsOwner == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="OwnerDocIsOwner<?php echo $key ?>" value="" <?php echo (!is_null($plot->OwnerDocIsOwner) && $plot->OwnerDocIsOwner == 3) ? 'checked' : '' ?> />1. <?php echo lang('Tidak Tahu')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah anda memiliki STD-B (Surat Tanda Daftar Budidaya)')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HaveSTDB<?php echo $key ?>" value="" <?php echo (!is_null($plot->HaveSTDB) && $plot->HaveSTDB == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HaveSTDB<?php echo $key ?>" value="" <?php echo (!is_null($plot->HaveSTDB) && $plot->HaveSTDB == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HaveSTDB<?php echo $key ?>" value="" <?php echo (!is_null($plot->HaveSTDB) && $plot->HaveSTDB == 3) ? 'checked' : '' ?> />1. <?php echo lang('Tidak Tahu')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apakah anda memiliki SPPL ( Surat Pernyataan Pengelolaan Lingkungan)')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HaveSPPL<?php echo $key ?>" value="" <?php echo (!is_null($plot->HaveSTDB) && $plot->HaveSPPL == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HaveSPPL<?php echo $key ?>" value="" <?php echo (!is_null($plot->HaveSTDB) && $plot->HaveSPPL == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HaveSPPL<?php echo $key ?>" value="" <?php echo (!is_null($plot->HaveSTDB) && $plot->HaveSPPL == 3) ? 'checked' : '' ?> />1. <?php echo lang('Tidak Tahu')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Model Usaha')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="BusinessModel<?php echo $key ?>" value="" <?php echo (!is_null($plot->BusinessModel) && $plot->BusinessModel == 1) ? 'checked' : '' ?> />1. <?php echo lang('Mandiri')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="BusinessModel<?php echo $key ?>" value="" <?php echo (!is_null($plot->BusinessModel) && $plot->BusinessModel == 2) ? 'checked' : '' ?> />2. <?php echo lang('Mandiri - Ex Plasma')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="BusinessModel<?php echo $key ?>" value="" <?php echo (!is_null($plot->BusinessModel) && $plot->BusinessModel == 3) ? 'checked' : '' ?> />1. <?php echo lang('Plasma (punya kontrak dengan kebun inti)')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Bagaimana asal usul mendapatkan kebun')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HowObPlantation<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowObPlantation) && $plot->HowObPlantation == 1) ? 'checked' : '' ?> />1. <?php echo lang('Warisan')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HowObPlantation<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowObPlantation) && $plot->HowObPlantation == 2) ? 'checked' : '' ?> />2. <?php echo lang('Beli Kebun')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HowObPlantation<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowObPlantation) && $plot->HowObPlantation == 3) ? 'checked' : '' ?> />3. <?php echo lang('Konversi Kebun')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HowObPlantation<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowObPlantation) && $plot->HowObPlantation == 4) ? 'checked' : '' ?> />4. <?php echo lang('Pemberian Pemerintah (Transmigrasi)')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="13%">
                           <div class="flexBox">
                              <input type="radio" name="HowObPlantation<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowObPlantation) && $plot->HowObPlantation == 5) ? 'checked' : '' ?> />5. <?php echo lang('Lainnya').':  ';?>
                              &nbsp;&nbsp;
                              <input type="text" name="" value="<?php echo (is_null($plot->HowObPlantationText)) ? $plot->HowObPlantationText: ''; ?>" />
                           </div>
                        </td>
                        <td>
                           <div class="flexBox"></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Kondisi ketika membangun kebun sawit')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="PlantationConditionEst<?php echo $key ?>" value="" <?php echo (!is_null($plot->PlantationConditionEst) && $plot->PlantationConditionEst == 1) ? 'checked' : '' ?> />1. <?php echo lang('Semak belukar/Bekas ladang')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="PlantationConditionEst<?php echo $key ?>" value="" <?php echo (!is_null($plot->PlantationConditionEst) && $plot->PlantationConditionEst == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tanaman Pangan')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="PlantationConditionEst<?php echo $key ?>" value="" <?php echo (!is_null($plot->PlantationConditionEst) && $plot->PlantationConditionEst == 3) ? 'checked' : '' ?> />3. <?php echo lang('Hutan Bakau')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="PlantationConditionEst<?php echo $key ?>" value="" <?php echo (!is_null($plot->PlantationConditionEst) && $plot->PlantationConditionEst == 4) ? 'checked' : '' ?> />4. <?php echo lang('Kebun lain (karet,kopi,dsb)')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td>
                           <div class="flexBox"><input type="radio" name="PlantationConditionEst<?php echo $key ?>" value="" <?php echo (!is_null($plot->PlantationConditionEst) && $plot->PlantationConditionEst == 5) ? 'checked' : '' ?> />5. <?php echo lang('Kebun Sawit')?></div>
                        </td>
                        <td>
                           <div class="flexBox">
                              <input type="radio" name="PlantationConditionEst<?php echo $key ?>" value="" <?php echo (!is_null($plot->PlantationConditionEst) && $plot->PlantationConditionEst == 6) ? 'checked' : '' ?> />6. <?php echo lang('Hutan');?>
                           </div>
                        </td>
                     </tr>
                     <tr>
                        <td>
                           <div class="flexBox">
                              <input type="radio" name="PlantationConditionEst<?php echo $key ?>" value="" <?php echo (!is_null($plot->PlantationConditionEst) && $plot->PlantationConditionEst == 7) ? 'checked' : '' ?> />7. <?php echo lang('Tidak Tahu').':  ';?>
                           </div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
          <footer>
            <?php echo lang('Page').' 4';?>
         </footer>
      </div>
      <!-- end page 4 -->
      <!-- begin page 5 -->
      <div class="page">
         <table width="100%">
            <tr>
               <td class="leftValue"><?php echo lang('Tahun pertama membuka kebun')?></td>
               <td><?php echo (!is_null($plot->FirstPlantingYear) && $plot->FirstPlantingYear > 0) ? $plot->FirstPlantingYear : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Tahun penanaman kelapa sawit saat ini')?></td>
               <td><?php echo (!is_null($plot->YearPlantingCurrent) && $plot->YearPlantingCurrent > 0) ? $plot->YearPlantingCurrent : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Umur rata-rata pohon sawit dikebun')?></td>
               <td><?php echo (!is_null($plot->AverageAgeTree) && $plot->AverageAgeTree > 0) ? $plot->AverageAgeTree : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Jenis Tanah')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="SoilType<?php echo $key ?>" value="" <?php echo (!is_null($plot->SoilType) && $plot->SoilType == 1) ? 'checked' : '' ?> /><?php echo lang('Mineral')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="SoilType<?php echo $key ?>" value="" <?php echo (!is_null($plot->SoilType) && $plot->SoilType == 2) ? 'checked' : '' ?> /><?php echo lang('Gambut')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="SoilType<?php echo $key ?>" value="" <?php echo (!is_null($plot->SoilType) && $plot->SoilType == 3) ? 'checked' : '' ?> /><?php echo lang('Berpasir')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Type of Topography Plantation')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="TopographyType<?php echo $key ?>" value="" <?php echo (!is_null($plot->TopographyType) && $plot->TopographyType == 1) ? 'checked' : '' ?> /><?php echo lang('Flat')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="TopographyType<?php echo $key ?>" value="" <?php echo (!is_null($plot->TopographyType) && $plot->TopographyType == 2) ? 'checked' : '' ?> /><?php echo lang('Moderat')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="TopographyType<?php echo $key ?>" value="" <?php echo (!is_null($plot->TopographyType) && $plot->TopographyType == 3) ? 'checked' : '' ?> /><?php echo lang('Curam')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Jumlah tanaman belum menghasilkan')?></td>
               <td><?php echo (!is_null($plot->TreeTBM) && $plot->TreeTBM > 0) ? $plot->TreeTBM : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Jumlah tanaman menghasilkan')?></td>
               <td><?php echo (!is_null($plot->TreeTM) && $plot->TreeTM > 0) ? $plot->TreeTM : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Jumlah tanaman rusak')?></td>
               <td><?php echo (!is_null($plot->TreeTR) && $plot->TreeTR > 0) ? $plot->TreeTR : ''; ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Jumlah tanaman')?></td>
               <td><?php echo ($plot->TreeTBM + $plot->TreeTM + $plot->TreeTBM) ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Total number of trees per ha')?></td>
               <td>&nbsp;</td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Jenis Pohon (pilih semua yang berlaku)')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateMarihat)) ? 'checked' : '' ?> />1. <?php echo lang('Marihat').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateMarihatNr)) ? $plot->TypePlantMateMarihatNr : '';  ?>"/></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateDumpy)) ? 'checked' : '' ?> />2. <?php echo lang('Dumpy').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateDumpy)) ? $plot->TypePlantMateDumpyNr : '';  ?>"/></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateLonsum)) ? 'checked' : '' ?> />3. <?php echo lang('Lonsum').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateLonsumNr)) ? $plot->TypePlantMateLonsumNr : '';  ?>"/></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateSimalungun)) ? 'checked' : '' ?> />4. <?php echo lang('Simalungun').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateSimalungunNr)) ? $plot->TypePlantMateSimalungunNr : '';  ?>"/></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateDanimas)) ? 'checked' : '' ?> />5. <?php echo lang('Dami Mas').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateDanimasNr)) ? $plot->TypePlantMateDanimasNr : '';  ?>"/></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateSriwijaya)) ? 'checked' : '' ?> />6. <?php echo lang('Sriwijaya').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateSriwijayaNr)) ? $plot->TypePlantMateSriwijayaNr : '';  ?>"/></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateSocfin) || !is_null($plot->TypePlantMateSocfinNr)) ? 'checked' : '' ?> />7. <?php echo lang('Socfin').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateSocfinNr)) ? $plot->TypePlantMateSocfinNr : '';  ?>"/></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateOther)) ? 'checked' : '' ?> />8. <?php echo lang('Jenis Pohon Lainnya').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateOtherText)) ? $plot->TypePlantMateOtherText : '';  ?>"/></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->TypePlantMateDoNotKnow)) ? 'checked' : '' ?> />9. <?php echo lang('Tidak diketahui').': ';?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->TypePlantMateDoNotKnowNr)) ? $plot->TypePlantMateDoNotKnowNr : '';  ?>"/></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Jumlah jenis pohon kelapa sawit')?></td>
               <td>
                  <?php
                     // $tempJumlahJenisPohonSawit = 0;
                     // if(!is_null($plot->TypePlantMateMarihat))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     // if(!is_null($plot->TypePlantMateDumpy))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     // if(!is_null($plot->TypePlantMateLonsum))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     // if(!is_null($plot->TypePlantMateSimalungun))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     // if(!is_null($plot->TypePlantMateDanimas))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     // if(!is_null($plot->TypePlantMateSriwijaya))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     // if(!is_null($plot->TypePlantMateSocfin))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     // if(!is_null($plot->TypePlantMateOther))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     // if(!is_null($plot->TypePlantMateDoNotKnow))
                     //    $tempJumlahJenisPohonSawit = $tempJumlahJenisPohonSawit + 1;
                     
                     // echo $tempJumlahJenisPohonSawit > 0 ? $tempJumlahJenisPohonSawit: '';
                     
                     echo ($plot->TypePlantMateMarihatNr+$plot->TypePlantMateDumpyNr+$plot->TypePlantMateLonsumNr+$plot->TypePlantMateSimalungunNr+$plot->TypePlantMateDanimasNr+$plot->TypePlantMateSriwijayaNr+$plot->TypePlantMateSocfinNr+$plot->TypePlantMateOtherText+$plot->TypePlantMateDoNotKnowNr);
                  ?>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Kapan musim panen trek untuk sawit di daerah anda ? (pilih semua yang berlaku) (pilih semua yang berlaku)')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonJan)) ? 'checked' : '' ?> />1. <?php echo lang('Januari').': ';?></div>
                        </td>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonMay)) ? 'checked' : '' ?> />5. <?php echo lang('Mei').': ';?></div>
                        </td>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonSep)) ? 'checked' : '' ?> />9. <?php echo lang('September').': ';?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonFeb)) ? 'checked' : '' ?> />2. <?php echo lang('Februari').': ';?></div>
                        </td>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonJun)) ? 'checked' : '' ?> />6. <?php echo lang('Juni').': ';?></div>
                        </td>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonOct)) ? 'checked' : '' ?> />10. <?php echo lang('Oktober').': ';?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonMar)) ? 'checked' : '' ?> />3. <?php echo lang('Maret').': ';?></div>
                        </td>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonJul)) ? 'checked' : '' ?> />7. <?php echo lang('Juli').': ';?></div>
                        </td>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonNov)) ? 'checked' : '' ?> />11. <?php echo lang('November').': ';?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonApr)) ? 'checked' : '' ?> />4. <?php echo lang('April').': ';?></div>
                        </td>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonAug)) ? 'checked' : '' ?> />8. <?php echo lang('Agustus').': ';?></div>
                        </td>
                        <td width="30%">
                           <div class="flexBox"><input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->LeanHarvestSeasonDes)) ? 'checked' : '' ?> />12. <?php echo lang('Desember').': ';?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Rotasi panen (sekali per ... Hari) pada musim raya')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->HarvestRateDaysHighSeason)) ? number_format($plot->HarvestRateDaysHighSeason, 0) : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Rata-rata produksi per kali panen (ton) pada musim panen raya')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->AverageProdHighSeason)) ? number_format($plot->AverageProdHighSeason, 2) : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Lama musim panen raya (jumlah bulan)')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->NrLowSeasonMonths)) ? number_format($plot->NrHighSeasonMonths, 0) : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Total Produksi Panen Raya (ton)')?></td>
               <td class="td-with-value">
                  <?php
                  $HighSeasonProduction = 0;
                  if (!is_null($plot->HighSeasonProduction)) {
                     # code...
                     $HighSeasonProduction = (30/$plot->HarvestRateDaysHighSeason) * $plot->NrLowSeasonMonths * $plot->AverageProdHighSeason;
                     echo number_format($HighSeasonProduction, 2);
                  }
                  ?>
               </td>
            </tr>

            <tr>
               <td class="leftValue"><?php echo lang('Rotasi panen (sekali per ... Hari) pada musim trek')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->HarvestRateDaysLowSeason)) ? number_format($plot->HarvestRateDaysLowSeason, 0) : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Rata-rata produksi per kali panen (ton) pada musim panen trek')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->AverageProdLowSeason)) ? number_format($plot->AverageProdLowSeason, 2) : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Lama musim panen trek (jumlah bulan)')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->NrHighSeasonMonths)) ? number_format($plot->NrLowSeasonMonths, 0) : '';  ?></td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Total Produksi Panen trek (ton)')?></td>
               <td class="td-with-value">
                  <?php
                  $LowSeasonProduction = 0;
                  if (!is_null($plot->LowSeasonProduction)) {
                     # code...
                     $LowSeasonProduction = (30/$plot->HarvestRateDaysLowSeason) * $plot->NrHighSeasonMonths * $plot->AverageProdLowSeason;
                     echo number_format($LowSeasonProduction, 2);
                  }
                  ?>
               </td>
            </tr>

            <tr>
               <td class="leftValue"><?php echo lang('Produksi Tahunan (ton)')?></td>
               <td class="td-with-value">
                  <?php
                     $AnnualProduction = $HighSeasonProduction + $LowSeasonProduction;
                     echo number_format($AnnualProduction, 2);
                  ?>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Produktivitas Perkebunan (ton/ha)')?></td>
               <td class="td-with-value">
                  <?php
                     $PlantationProductivity = $AnnualProduction / $plot->GardenAreaHa;
                     echo number_format($PlantationProductivity, 2);
                  ?>
               </td>
            </tr>

            <tr>
               <td class="leftValue"><?php echo lang('Kepada berapa pembeli yang berbeda Anda menjual TBS dalam setahun terakhir?')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HowManyDiffBuyerSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffBuyerSoldLastYear) && $plot->HowManyDiffBuyerSoldLastYear == 1) ? 'checked' : '' ?> /><?php echo lang('1')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HowManyDiffBuyerSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffBuyerSoldLastYear) && $plot->HowManyDiffBuyerSoldLastYear == 3) ? 'checked' : '' ?> /><?php echo lang('3')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HowManyDiffBuyerSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffBuyerSoldLastYear) && $plot->HowManyDiffBuyerSoldLastYear == 5) ? 'checked' : '' ?> /><?php echo lang('Lebih dari 4')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HowManyDiffBuyerSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffBuyerSoldLastYear) && $plot->HowManyDiffBuyerSoldLastYear == 2) ? 'checked' : '' ?> /><?php echo lang('2')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HowManyDiffBuyerSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffBuyerSoldLastYear) && $plot->HowManyDiffBuyerSoldLastYear == 4) ? 'checked' : '' ?> /><?php echo lang('4')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>

            <tr>
               <td class="leftValue"><?php echo lang('Kepada berapa Pabrik Kelapa Sawit Anda menjual TBS dalam setahun terakhir')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HowManyDiffMillSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffMillSoldLastYear) && $plot->HowManyDiffMillSoldLastYear == 1) ? 'checked' : '' ?> /><?php echo lang('1')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HowManyDiffMillSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffMillSoldLastYear) && $plot->HowManyDiffMillSoldLastYear == 3) ? 'checked' : '' ?> /><?php echo lang('3')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HowManyDiffMillSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffMillSoldLastYear) && $plot->HowManyDiffMillSoldLastYear == 5) ? 'checked' : '' ?> /><?php echo lang('Lebih dari 4')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="HowManyDiffMillSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffMillSoldLastYear) && $plot->HowManyDiffMillSoldLastYear == 2) ? 'checked' : '' ?> /><?php echo lang('2')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="HowManyDiffMillSoldLastYear<?php echo $key ?>" value="" <?php echo (!is_null($plot->HowManyDiffMillSoldLastYear) && $plot->HowManyDiffMillSoldLastYear == 4) ? 'checked' : '' ?> /><?php echo lang('4')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>

            <tr>
               <td class="leftValue"><?php echo lang('Catatan lain tentang kebun')?></td>
               <td class="td-with-value"><?php echo (!is_null($plot->Comment)) ? $plot->Comment : ''; ?></td>
            </tr>
         </table>
          <footer>
            <?php echo lang('Page').' 5';?>
         </footer>
      </div>
      <!-- end page 5 -->
      <!-- begin page 6 -->
      <div class="page">
         <table width="100%">
            <tr>
               <td class="tabelHeader" colspan="2">
                  B. <?php echo lang('Pupuk')?>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Do you use non organic fertilizer')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="FertNonOrganicData<?php echo $key ?>" value="" <?php echo (!is_null($plot->FertNonOrganicData) && $plot->FertNonOrganicData == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="FertNonOrganicData<?php echo $key ?>" value="" <?php echo (!is_null($plot->FertNonOrganicData) && $plot->FertNonOrganicData == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
                  <br>
                  <table width="100%" height="100%">
                     <tr>
                        <td width="30%" class="leftValue">
                           &nbsp;
                        </td>
                        <td width="30%">
                           <?php echo lang('Frequency (times/year)'); ?>
                        </td>
                        <td width="30%">
                           <?php echo lang('Dose (kg/plot/times)'); ?>
                        </td>
                        <td width="15%">
                           <?php echo lang('Unit'); ?>
                        </td>
                        <td width="30%">
                           <?php echo lang('Annual dose (kg/plot/year)'); ?>
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Urea'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertUreaTimesYear > 0) ? $plot->FertUreaTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertUreaDose > 0) ? $plot->FertUreaDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('SS'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertSSTimesYear > 0) ? $plot->FertSSTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertSSDose > 0) ? $plot->FertSSDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('NPK'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertNPKTimesYear > 0) ? $plot->FertNPKTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertNPKDose > 0) ? $plot->FertNPKDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('TSP'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertTSPTimesYear > 0) ? $plot->FertTSPTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertTSPDose > 0) ? $plot->FertTSPDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('CU'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertCUTimesYear > 0) ? $plot->FertCUTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertCUDose > 0) ? $plot->FertCUDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('KCL'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertKCLTimesYear > 0) ? $plot->FertKCLTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertKCLDose > 0) ? $plot->FertKCLDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Borat'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertBoratTimesYear > 0) ? $plot->FertBoratTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertBoratDose > 0) ? $plot->FertBoratDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Dolomite/Lime'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertDolomiteTimesYear > 0) ? $plot->FertDolomiteTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertDolomiteDose > 0) ? $plot->FertDolomiteDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Which tree are fertilized with non organic fertilizers'); ?>
                        </td>
                        <td colspan="4">
                           <table width="100%" height="100%" class="tabelNoBorder">
                              <tr>
                                 <td>
                                    <div class="flexBox">
                                       <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->FertWithNonOrgaTBM)) ? 'checked' : '' ?> />1. <?php echo lang('TBM');?>
                                    </div>
                                 </td>
                                 <td>
                                    <div class="flexBox">
                                       <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->FertWithNonOrgaTR)) ? 'checked' : '' ?> />2. <?php echo lang('TR');?>
                                    </div>
                                 </td>
                                 <td>
                                    <div class="flexBox">
                                       <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->FertWithNonOrgaTM)) ? 'checked' : '' ?> />3. <?php echo lang('TM');?>
                                    </div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Do you use organic fertilizer')?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="FertUseOrganic<?php echo $key ?>" value="" <?php echo (!is_null($plot->FertUseOrganic) && $plot->FertUseOrganic == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="FertUseOrganic<?php echo $key ?>" value="" <?php echo (!is_null($plot->FertUseOrganic) && $plot->FertUseOrganic == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                        </td>
                     </tr>
                  </table>
                  <br>
                  <table width="100%" height="100%">
                     <tr>
                        <td width="30%" class="leftValue">
                           &nbsp;
                        </td>
                        <td width="30%">
                           <?php echo lang('Frequency (times/year)'); ?>
                        </td>
                        <td width="30%">
                           <?php echo lang('Dose (kg/plot/times)'); ?>
                        </td>
                        <td width="15%">
                           <?php echo lang('Unit'); ?>
                        </td>
                        <td width="30%">
                           <?php echo lang('Annual dose (kg/plot/year)'); ?>
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Abu Tandan Sawit'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertPBATimesYear > 0) ? $plot->FertPBATimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertPBATimesDose > 0) ? $plot->FertPBATimesDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Tandan Sawit'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertPBTimesYear > 0) ? $plot->FertPBTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertPBTimesDose > 0) ? $plot->FertPBTimesDose: ''; ?>
                        </td>
                        <td width="15%">
                           <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Kompos dari Tandan Sawit'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertCPBTimesYear > 0) ? $plot->FertCPBTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertCPBTimesDose > 0) ? $plot->FertCPBTimesDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Pupuk Kandang'); ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertManureTimesYear > 0) ? $plot->FertManureTimesYear: ''; ?>
                        </td>
                        <td width="30%">
                           <?php echo ($plot->FertManureTimesDose > 0) ? $plot->FertManureTimesDose: ''; ?>
                        </td>
                        <td width="15%">
                            <?php echo lang('Kg'); ?>
                        </td>
                        <td width="30%">
                           &nbsp;
                        </td>
                     </tr>
                     <tr>
                        <td width="30%" class="leftValue">
                           <?php echo lang('Which tree are fertilized with organic fertilizers'); ?>
                        </td>
                        <td colspan="4">
                           <table width="100%" height="100%" class="tabelNoBorder">
                              <tr>
                                 <td>
                                    <div class="flexBox">
                                       <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->FertWithOrgaTBM)) ? 'checked' : '' ?> />1. <?php echo lang('TBM');?>
                                    </div>
                                 </td>
                                 <td>
                                    <div class="flexBox">
                                       <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->FertWithOrgaTBM)) ? 'checked' : '' ?> />2. <?php echo lang('TR');?>
                                    </div>
                                 </td>
                                 <td>
                                    <div class="flexBox">
                                       <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->FertWithOrgaTBM)) ? 'checked' : '' ?> />3. <?php echo lang('TM');?>
                                    </div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
         <br>
         <table width="100%">
            <tr>
               <td class="tabelHeader" colspan="2">
                  C. <?php echo lang('Control of HPT')?>
               </td>
            </tr>
            <tr>
               <td class="leftValue">&nbsp;</td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder">
                     <tr>
                        <td width="30%"><?php echo lang('Herbisida'); ?></td>
                        <td width="30%"><?php echo lang('Insektisida'); ?></td>
                        <td width="30%"><?php echo lang('Fungisida'); ?></td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue">&nbsp;</td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder">
                     <tr>
                        <td width="30%">
                           <table width="100%" height="100%" class="tabelNoBorder">
                              <tr>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="PeUsingHerbicide<?php echo $key ?>" value="" <?php echo (!is_null($plot->PeUsingHerbicide) && $plot->PeUsingHerbicide == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="PeUsingHerbicide<?php echo $key ?>" value="" <?php echo (!is_null($plot->PeUsingHerbicide) && $plot->PeUsingHerbicide == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                        <td width="30%">
                           <table width="100%" height="100%" class="tabelNoBorder">
                              <tr>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="PeUsingInsecticide<?php echo $key ?>" value="" <?php echo (!is_null($plot->PeUsingInsecticide) && $plot->PeUsingInsecticide == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="PeUsingInsecticide<?php echo $key ?>" value="" <?php echo (!is_null($plot->PeUsingInsecticide) && $plot->PeUsingInsecticide == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                        <td width="30%">
                           <table width="100%" height="100%" class="tabelNoBorder">
                              <tr>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="PeUsingFungicide<?php echo $key ?>" value="" <?php echo (!is_null($plot->PeUsingFungicide) && $plot->PeUsingFungicide == 1) ? 'checked' : '' ?> />1. <?php echo lang('Ya')?></div>
                                 </td>
                                 <td>
                                    <div class="flexBox"><input type="radio" name="PeUsingFungicide<?php echo $key ?>" value="" <?php echo (!is_null($plot->PeUsingFungicide) && $plot->PeUsingFungicide == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tidak')?></div>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Frekuensi Pestisida (Kali/Tahun)'); ?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder">
                     <tr>
                        <td width="30%"><?php echo (!is_null($plot->PeFreqHerbi)) ? number_format($plot->PeFreqHerbi, 0).'  ' : '.....................................' ; ?><?php echo lang('Kali/Tahun'); ?></td>
                        <td width="30%"><?php echo (!is_null($plot->PeFreqInsec)) ? number_format($plot->PeFreqInsec, 0).'  ' : '.....................................' ; ?><?php echo lang('Kali/Tahun'); ?></td>
                        <td width="30%"><?php echo (!is_null($plot->PeFreqFungi)) ? number_format($plot->PeFreqFungi, 0).'  ' : '.....................................' ; ?><?php echo lang('Kali/Tahun'); ?></td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue" style="vertical-align: top"><?php echo lang('Merk'); ?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder">
                     <tr>
                        <td width="30%">
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi1)) ? 'checked' : '' ?> />1. <?php echo lang('Round Up');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi2)) ? 'checked' : '' ?> />2. <?php echo lang('Basmilang');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi3)) ? 'checked' : '' ?> />3. <?php echo lang('Pilar Up');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi4)) ? 'checked' : '' ?> />4. <?php echo lang('Sun Up');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi5)) ? 'checked' : '' ?> />5. <?php echo lang('Gramaxone');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi6)) ? 'checked' : '' ?> />6. <?php echo lang('Supremo');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi7)) ? 'checked' : '' ?> />7. <?php echo lang('Sapurata');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi8)) ? 'checked' : '' ?> />8. <?php echo lang('Rambo');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi9)) ? 'checked' : '' ?> />9. <?php echo lang('Para Special');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi10)) ? 'checked' : '' ?> />10. <?php echo lang('Noxone');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi11)) ? 'checked' : '' ?> />11. <?php echo lang('Paratop');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi12)) ? 'checked' : '' ?> />12. <?php echo lang('Bravoxone');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi13)) ? 'checked' : '' ?> />13. <?php echo lang('Primaxone');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi14)) ? 'checked' : '' ?> />14. <?php echo lang('Bimastar');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi15)) ? 'checked' : '' ?> />15. <?php echo lang('Polado');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi16)) ? 'checked' : '' ?> />16. <?php echo lang('Primastar');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi17)) ? 'checked' : '' ?> />17. <?php echo lang('Rumat');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi18)) ? 'checked' : '' ?> />18. <?php echo lang('Supretox');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi19)) ? 'checked' : '' ?> />19. <?php echo lang('Kleenup');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi20)) ? 'checked' : '' ?> />20. <?php echo lang('Prima Up');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi21)) ? 'checked' : '' ?> />21. <?php echo lang('Tanistar');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi22)) ? 'checked' : '' ?> />22. <?php echo lang('DMA');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi23)) ? 'checked' : '' ?> />23. <?php echo lang('Polaris');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi24)) ? 'checked' : '' ?> />24. <?php echo lang('Konup');?>
                           </div>
                        </td>
                        <td width="30%" style="vertical-align: top">
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec1)) ? 'checked' : '' ?> />1. <?php echo lang('Alika');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec2)) ? 'checked' : '' ?> />2. <?php echo lang('Matador');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec3)) ? 'checked' : '' ?> />3. <?php echo lang('Capture');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec4)) ? 'checked' : '' ?> />4. <?php echo lang('Bento');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec5)) ? 'checked' : '' ?> />5. <?php echo lang('Regent');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec6)) ? 'checked' : '' ?> />6. <?php echo lang('Drusban');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec7)) ? 'checked' : '' ?> />7. <?php echo lang('Penalty');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec8)) ? 'checked' : '' ?> />8. <?php echo lang('Nurelle');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec9)) ? 'checked' : '' ?> />9. <?php echo lang('Chlormite');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec10)) ? 'checked' : '' ?> />10. <?php echo lang('Decis');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec11)) ? 'checked' : '' ?> />11. <?php echo lang('Klensect');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec12)) ? 'checked' : '' ?> />12. <?php echo lang('Vigor');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec13)) ? 'checked' : '' ?> />13. <?php echo lang('Unicide');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec14)) ? 'checked' : '' ?> />14. <?php echo lang('Deicer 505');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec15)) ? 'checked' : '' ?> />15. <?php echo lang('Arrivo');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec16)) ? 'checked' : '' ?> />16. <?php echo lang('Sidamethrin');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec17)) ? 'checked' : '' ?> />17. <?php echo lang('Bestox');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec18)) ? 'checked' : '' ?> />18. <?php echo lang('Halona');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec19)) ? 'checked' : '' ?> />19. <?php echo lang('Dangke');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec20)) ? 'checked' : '' ?> />20. <?php echo lang('Buldok');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec21)) ? 'checked' : '' ?> />21. <?php echo lang('Laser');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec22)) ? 'checked' : '' ?> />22. <?php echo lang('Sevin');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeInsec23)) ? 'checked' : '' ?> />23. <?php echo lang('Organik');?>
                           </div>
                           <div class="flexBox">
                              24. <?php echo lang('Merk Lain').':&nbsp;&nbsp;'; ?><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->PeInsecOther)) ? $plot->PeInsecOther : ''; ?>" size="15" />
                           </div>
                        </td>
                        <td width="30%" style="vertical-align: top">
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi1)) ? 'checked' : '' ?> />1. <?php echo lang('Nordox');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi2)) ? 'checked' : '' ?> />2. <?php echo lang('Dithane');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi3)) ? 'checked' : '' ?> />3. <?php echo lang('Amistartop');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi4)) ? 'checked' : '' ?> />4. <?php echo lang('Scorpio');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi5)) ? 'checked' : '' ?> />5. <?php echo lang('Rhidomill');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi6)) ? 'checked' : '' ?> />6. <?php echo lang('Antila');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi7)) ? 'checked' : '' ?> />7. <?php echo lang('Antracol');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi8)) ? 'checked' : '' ?> />8. <?php echo lang('Polydor');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi9)) ? 'checked' : '' ?> />9. <?php echo lang('Cozeb');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi10)) ? 'checked' : '' ?> />10. <?php echo lang('Rabbat');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi11)) ? 'checked' : '' ?> />11. <?php echo lang('Benhasil');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeFungi12)) ? 'checked' : '' ?> />12. <?php echo lang('Organic');?>
                           </div>
                           <div class="flexBox">
                              13. <?php echo lang('Merk Lain').':&nbsp;&nbsp;'; ?><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->PeFungiOther)) ? $plot->PeFungiOther : ''; ?>" size="15" />
                           </div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
         <footer>
            <?php echo lang('Page').' 6';?>
         </footer>
      </div>
      <!-- end page 6 -->
      <!-- begin page 7 -->
      <div class="page">
      	 <table width="100%">
      	 	<tr>
               <td class="leftValue" style="vertical-align: top"><?php echo lang('Merk'); ?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder">
                     <tr>
                        <td width="30%">
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi25)) ? 'checked' : '' ?> />25. <?php echo lang('Herbatop');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi26)) ? 'checked' : '' ?> />26. <?php echo lang('Mupxone');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi27)) ? 'checked' : '' ?> />27. <?php echo lang('Pointer');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi28)) ? 'checked' : '' ?> />28. <?php echo lang('Senus');?>
                           </div>
                           <div class="flexBox">
                              <input type="checkbox" name="workerUsePPE" value="" <?php echo (!is_null($plot->PeHerbi29)) ? 'checked' : '' ?> />29. <?php echo lang('Tamaxon');?>
                           </div>
                           <div class="flexBox">
                              30. <?php echo lang('Merk Lain').':&nbsp;&nbsp;'; ?><input type="text" name="workerUsePPE" value="<?php echo (!is_null($plot->PeHerbiOther)) ? $plot->PeHerbiOther : ''; ?>" size="15" />
                           </div>
                        </td>
                        <td width="30%" style="vertical-align: top">
                        </td>
                        <td width="30%" style="vertical-align: top">
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
      	 	<tr>
               <td class="leftValue"><?php echo lang('Dimana anda menyimpan pestisida sebelum dan selama pemakaian'); ?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="PestStoreLocation<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestStoreLocation) && $plot->PestStoreLocation == 1) ? 'checked' : '' ?> />1. <?php echo lang('Dirumah')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="PestStoreLocation<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestStoreLocation) && $plot->PestStoreLocation == 2) ? 'checked' : '' ?> />2. <?php echo lang('Tempat khusus pestisida')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="PestStoreLocation<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestStoreLocation) && $plot->PestStoreLocation == 3) ? 'checked' : '' ?> />3. <?php echo lang('Diluar rumah (kawasan rumah)')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="PestStoreLocation<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestStoreLocation) && $plot->PestStoreLocation == 4) ? 'checked' : '' ?> />4. <?php echo lang('On the farm')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%" colspan="2">
                           <div class="flexBox"><input type="radio" name="PestStoreLocation<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestStoreLocation) && $plot->PestStoreLocation == 5) ? 'checked' : '' ?> />5. <?php echo lang('Lainnya')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Apa yang anda lakukan dengan kemasan pestisida setelah pemakaian'); ?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="PestPackageAfterUse<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestPackageAfterUse) && $plot->PestPackageAfterUse == 1) ? 'checked' : '' ?> />1. <?php echo lang('Di buang sembarangan (dikebun atau sekitar rumah)')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="PestPackageAfterUse<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestPackageAfterUse) && $plot->PestPackageAfterUse == 2) ? 'checked' : '' ?> />2. <?php echo lang('Digunakan untuk menyimpan sesuatu')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="PestPackageAfterUse<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestPackageAfterUse) && $plot->PestPackageAfterUse == 3) ? 'checked' : '' ?> />3. <?php echo lang('Rinse, perforate and bury')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="PestPackageAfterUse<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestPackageAfterUse) && $plot->PestPackageAfterUse == 4) ? 'checked' : '' ?> />4. <?php echo lang('Dibakar')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="radio" name="PestPackageAfterUse<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestPackageAfterUse) && $plot->PestPackageAfterUse == 5) ? 'checked' : '' ?> />5. <?php echo lang('Recycle/return to the shop')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="radio" name="PestPackageAfterUse<?php echo $key ?>" value="" <?php echo (!is_null($plot->PestPackageAfterUse) && $plot->PestPackageAfterUse == 6) ? 'checked' : '' ?> />6. <?php echo lang('Lainnya')?></div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
      	 </table>
      	 <br>
         <table width="100%">
            <tr>
               <td class="tabelHeader" colspan="2">
                  D. <?php echo lang('Hama & Penyakit')?>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Hama Utama Tanaman'); ?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainRats)) ? 'checked' : '' ?>  />1. <?php echo lang('Tikus')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainOly)) ? 'checked' : '' ?>  />2. <?php echo lang('Tungau Merah')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainSatora)) ? 'checked' : '' ?>  />3. <?php echo lang('Ulat Api')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainTira)) ? 'checked' : '' ?>  />4. <?php echo lang('Penggerek Tandan Buah/Ngengat')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainRhino)) ? 'checked' : '' ?>  />5. <?php echo lang('Kumbang Tanduk')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainElep)) ? 'checked' : '' ?>  />6. <?php echo lang('Gajah')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainOrgUtan)) ? 'checked' : '' ?>  />7. <?php echo lang('Orang Utan')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainLandak)) ? 'checked' : '' ?>  />8. <?php echo lang('Landak')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td colspan="2">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainBabi)) ? 'checked' : '' ?>  />9. <?php echo lang('Babi')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td colspan="2">
                           <div class="flexBox">
                              <input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->PestMainOther)) ? 'checked' : '' ?>  />10. <?php echo lang('Lainnya').':&nbsp;&nbsp;'?>
                              <input type="text" name="doYouHaveWorker" value="<?php echo (!is_null($plot->PestMainOtherText)) ? $plot->PestMainOtherText : ''; ?>" size="25" />
                           </div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td class="leftValue"><?php echo lang('Penyakit Utama Tanaman'); ?></td>
               <td>
                  <table width="100%" height="100%" class="tabelNoBorder" border="0">
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainBlast)) ? 'checked' : '' ?> />1. <?php echo lang('Penyakit Akar/Busuk Akar Sawit')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainGeno)) ? 'checked' : '' ?> />2. <?php echo lang('Penyakit Busuk Pangkal batang')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainSteam)) ? 'checked' : '' ?> />3. <?php echo lang('Penyakit Busuk Batang Atas')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainBud)) ? 'checked' : '' ?> />4. <?php echo lang('Penyakit Busuk Pangkal Kuncup')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainSpear)) ? 'checked' : '' ?> />5. <?php echo lang('Penyakit Busuk Kuncup')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainYellow)) ? 'checked' : '' ?> />6. <?php echo lang('Penyakit Garis Kuning')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td width="40%">
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainAnt)) ? 'checked' : '' ?> />7. <?php echo lang('Anthracnose')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainCrown)) ? 'checked' : '' ?> />8. <?php echo lang('Penyakit Tajuk');?></div>
                        </td>
                     </tr>
                     <tr>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainViscular)) ? 'checked' : '' ?> />9. <?php echo lang('Penyakit Layu daun')?></div>
                        </td>
                        <td>
                           <div class="flexBox"><input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainBunch)) ? 'checked' : '' ?> />10. <?php echo lang('Penyakit Busuk Pangkal Kuncup')?></div>
                        </td>
                     </tr>
                     <tr>
                        <td colspan="2">
                           <div class="flexBox">
                              <input type="checkbox" name="doYouHaveWorker" value="" <?php echo (!is_null($plot->DisMainOther)) ? 'checked' : '' ?> />11. <?php echo lang('Lainnya').':&nbsp;&nbsp;'?>
                              <input type="text" name="doYouHaveWorker" value="<?php echo (!is_null($plot->DisMainOtherText)) ? $plot->DisMainOtherText : ''; ?>" size="25" />
                           </div>
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>
         <footer>
            <?php echo lang('Page').' 7';?>
         </footer>
      </div>
      <!-- end page 7 -->
      <?php } ?>
   </body>
   <script type="text/javascript">
      $("input[type=radio]").attr('disabled', true);
      $("input[type=checkbox]").attr('disabled', true);
      $("input[type=text]").attr('disabled', true);
   </script>
</html>