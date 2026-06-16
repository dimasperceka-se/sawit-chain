<?php
/**
 * @Author: nikolius
 * @Date:   2017-01-24 11:26:37
 */
$baseurlnya = base_url();
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8"/>
    <title>GAP</title>

    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/farmer/gap.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/farmer/gap-media.css" media="print"/>
    <script src="<?php echo $baseurlnya; ?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
</head>
<body>

<!-- P1A (BEGIN) -->
<?php if($jenis_form == "Form Kosong"){
    $maxDataFamily = 10;
    $maxDataGardenStatus = 8;
    $maxDataOtherLand = 9;
}elseif($jenis_form == "Form Hasil" || $jenis_form == "form_hasil_simple"){
    $maxDataFamily = count($family['data']);
    $maxDataGardenStatus = count($gardenStatus);
    $maxDataOtherLand = count($otherLand);
}
$halaman_P1A = 1;
?>
<!-- P1A (END) -->

<!-- mulai halaman pertama (BEGIN) -->
<div class="page"> <!-- Halaman 1 Start -->

    <!-- LOGO ATAS (BEGIN) -->
    <table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
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
    <!-- LOGO ATAS (END) -->

    <div class="borderHitam">

        <h2 class="judulHalaman"><?php echo lang('Survei Rumah Tangga Petani Coklat')?></h2>
        <h2 class="judulHalaman"><?php echo lang('P1A. Data Dasar (Khusus Sertifikasi)')?></h2>
        <br />

        <div class="borderHitam" style="padding:3px">
            <?php echo lang('Pengantar')?><br />
            <strong><?php echo lang('Selamat Pagi/Siang/Sore. Nama Saya')?> </strong><br />
            <?php echo lang('Kami dari Swisscontact Indonesia ingin melakukan wawancara dengan bapak/ibu untuk mengetahui kondisi pertanian coklat, dalam rangka program pengembangan pertanian coklat di Indonesia. Maka dari itu kami mohon kesediaan bapak/ibu untuk kami wawancara.')?>
        </div>
        <br />

        <table width="100%">
        <tr>
            <td class="bgBiru" width="20%"><?php echo lang('No. Survey')?></td>
            <td width="30%"><?php echo $SurveyNr; ?></td>
            <td class="bgBiru" width="20%"><?php echo lang('Tanggal')?></td>
            <td width="30%"><?php echo ($garden[0]['DateCollection']!='' && $garden[0]['DateCollection']!='0000-00-00' ? substr($garden[0]['DateCollection'],0,10) : '&nbsp;_&nbsp;_/&nbsp;_&nbsp;_/&nbsp;_&nbsp;_'); ?></td>
        </tr>
        <tr height="35">
            <td class="bgBiru"><?php echo lang('Tanda Tangan')?></td>
            <td colspan="3"></td>
        </tr>
        </table>
        <br />

        <table width="100%">
        <tr>
            <td class="bgBiru" width="15%"><?php echo lang('No. ID Kelompok Tani')?></td>
            <td width="15%"><?php echo $farmer['CPGid']?></td>
            <td class="bgBiru" width="15%"><?php echo lang('Nama Kelompok Tani')?></td>
            <td><?php echo $farmer['GroupName']?></td>
            <td class="bgBiru" width="15%"><?php echo lang('No. ID Petani')?></td>
            <td width="15%"><?php echo $farmer['FarmerID']?></td>
        </tr>
        </table>
        <br />
    </div>
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
                    <td width="50%"><?php echo $farmer['FarmerName']?></td>
                    <td style="border-left:1px solid black;"><?php echo lang('Tanda Tangan')?> :</td>
                </tr>
            </table>
        </td>
    </tr>
    <?php
    //tgl lahir
    $tglLahir = explode("-",$farmer['Birthdate']);
    ?>
    <tr>
        <td class="leftValue"><?php echo lang('Tanggal Lahir')?></td>
        <td>
            <table width="100%" height="100%" class="tabelNoBorder" border="0">
            <tr>
                <td width="1%">&nbsp;</td>
                <td width="9%"><?php echo lang('Tanggal')?>: <?php echo $tglLahir[2]?></td>
                <td width="2%">&nbsp;</td>
                <td width="9%"><?php echo lang('Bulan')?>: <?php echo $tglLahir[1]?></td>
                <td width="2%">&nbsp;</td>
                <td width="9%"><?php echo lang('Tahun')?>: <?php echo $tglLahir[0]?></td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Status Perkawinan')?></td>
        <td>
            <?php
            switch ($farmer['MaritalSt']) {
                case '1':
                    $statusMenikah1 = 'checked=""';
                break;
                case '2':
                    $statusMenikah2 = 'checked=""';
                break;
                case '3':
                    $statusMenikah3 = 'checked=""';
                break;
            }
            ?>
            <table width="100%" height="100%" class="tabelNoBorder" border="0">
            <tr>
                <td>
                    <div class="flexBox"><input type="radio" <?php echo $statusMenikah1;?> name="statusMenikah" value="" />1. <?php echo lang('Menikah')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" <?php echo $statusMenikah2;?> name="statusMenikah" value="" />2. <?php echo lang('Lajang')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" <?php echo $statusMenikah3;?> name="statusMenikah" value="" />3. <?php echo lang('Janda/Duda')?></div>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="leftValue" style="vertical-align: top;"><?php echo lang('Pendidikan Terakhir')?></td>
        <td>
            <?php
            switch ($farmer['Education']) {
                case '1':
                    $lastEdu1 = 'checked=""';
                break;
                case '2':
                    $lastEdu2 = 'checked=""';
                break;
                case '3':
                    $lastEdu3 = 'checked=""';
                break;
                case '4':
                    $lastEdu4 = 'checked=""';
                break;
                case '5':
                    $lastEdu5 = 'checked=""';
                break;
                case '6':
                    $lastEdu6 = 'checked=""';
                break;
            }
            ?>
            <table width="100%" height="100%" class="tabelNoBorder" border="0">
            <tr>
                <td width="30%">
                    <div class="flexBox">
                        <input type="radio" <?php echo $lastEdu1?> name="pendidikanTerakhir" value="" />1. <?php echo lang('Tidak Pernah Sekolah')?>
                    </div>
                </td>
                <td width="30%">
                    <div class="flexBox">
                        <input type="radio" <?php echo $lastEdu4?> name="pendidikanTerakhir" value="" />4. <?php echo lang('Tamat SMP')?>
                    </div>
                </td>
                <td width="40%">&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <div class="flexBox">
                        <input type="radio" <?php echo $lastEdu2?> name="pendidikanTerakhir" value="" />2. <?php echo lang('Tidak Tamat SD')?>
                    </div>
                </td>
                <td>
                    <div class="flexBox">
                        <input type="radio" <?php echo $lastEdu5?> name="pendidikanTerakhir" value="" />5. <?php echo lang('Tamat SMA')?>
                    </div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <div class="flexBox">
                        <input type="radio" <?php echo $lastEdu3?> name="pendidikanTerakhir" value="" />3. <?php echo lang('Tamat SD')?>
                    </div>
                </td>
                <td>
                    <div class="flexBox">
                        <input type="radio" <?php echo $lastEdu6?> name="pendidikanTerakhir" value="" />6. <?php echo lang('Tamat Perguruan Tinggi')?>
                    </div>
                </td>
                <td></td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Nama Propinsi')?></td>
        <td><?php echo $farmer['Provinsi']?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Nama Kabupaten')?></td>
        <td><?php echo $farmer['Kabupaten']?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Nama Kecamatan')?></td>
        <td><?php echo $farmer['Kecamatan']?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Nama Desa')?></td>
        <td><?php echo $farmer['Desa']?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Nama Dusun')?></td>
        <td><?php echo $farmer['alamat']?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('RT / RW')?></td>
        <td><?php echo $farmer['RtRw']?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Nomor Handphone')?></td>
        <td><?php echo $farmer['HandPhone']?></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Status petani (ditanyakan saat post-line)')?></td>
        <td>
            <?php
            switch ($farmer['StatusFarmer']) {
                case '1':
                    $statusPetani1 = 'checked=""';
                break;
                case '2':
                    $statusPetani2 = 'checked=""';
                break;
            }

            switch ($farmer['ReasonStatusFarmer']) {
                case '1':
                    $statusPetaniAlasan1 = 'checked=""';
                break;
                case '2':
                    $statusPetaniAlasan2 = 'checked=""';
                break;
                case '3':
                    $statusPetaniAlasan3 = 'checked=""';
                break;
            }
            ?>
            <table width="100%" height="100%" class="tabelNoBorder" border="0">
            <tr>
                <td width="13%">
                    <div class="flexBox"><input type="radio" <?php echo $statusPetani1?> name="statusPetani" value="" />1. <?php echo lang('Ya, Aktif')?></div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="vertical-align:top;">
                    <div class="flexBox"><input type="radio" <?php echo $statusPetani2?> name="statusPetani" value="" />2. <?php echo lang('Tidak')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" <?php echo $statusPetaniAlasan1?> name="statusPetaniNonAktif" value="" />1. <?php echo lang('Meninggal')?></div>
                    <div class="flexBox"><input type="radio" <?php echo $statusPetaniAlasan2?> name="statusPetaniNonAktif" value="" />2. <?php echo lang('Pindah')?></div>
                    <div class="flexBox"><input type="radio" <?php echo $statusPetaniAlasan3?> name="statusPetaniNonAktif" value="" />3. <?php echo lang('Berhenti Bertani')?></div>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="12">
            B. <?php echo lang('Data Keluarga dan Pekerja')?>
        </td>
    </tr>
    <tr>
        <td class="topTabelValue" width="3%">No</td>
        <td class="topTabelValue"><?php echo lang('Nama Anggota Keluarga dan Pekerja')?></td>
        <td width="12%" class="topTabelValue"><?php echo lang('Hubungan')?></td>
        <td width="9%" class="topTabelValue"><?php echo lang('Ikut bekerja di kebun')?></td>
        <td width="20%" class="topTabelValue"><?php echo lang('Jenis kegiatan')?></td>
        <td class="topTabelValue"><?php echo lang('Jumlah Jam Kerja per hari')?></td>
        <td class="topTabelValue"><?php echo lang('Total hari kerja')?></td>
        <td class="topTabelValue"><?php echo lang('Upah (Rp)')?></td>
        <td class="topTabelValue"><?php echo lang('Tahun Lahir')?></td>
        <td class="topTabelValue"><?php echo lang('Usia')?></td>
        <td width="12%" class="topTabelValue"><?php echo lang('Jenis Kelamin')?></td>
        <td width="9%" class="topTabelValue"><?php echo lang('Masih Sekolah')?></td>
    </tr>
    <?php if(count($family['data']) > 0 || count($family['data']) <= 0  ) { ?>
        <?php for($f=0; $f< $maxDataFamily; $f++){ ?>
            <tr class="rataAtas">
                <td align="center"><?php echo $f+1;?></td>
                <td><?php echo $family['data'][$f]['AnggotaName']?></td>
                <td>
                    <?php
                    switch ($family['data'][$f]['HubunganKeluarga']) {
                        case '1':
                            $famHubKel1_1 = 'checked=""';
                        break;
                        case '2':
                            $famHubKel1_2 = 'checked=""';
                        break;
                        case '3':
                            $famHubKel1_3 = 'checked=""';
                        break;
                        case '4':
                            $famHubKel1_4 = 'checked=""';
                        break;
                    }
                    ?>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['HubunganKeluarga']=='1' ? 'checked=""' : ''); ?> name="famHubungan1<?php echo $f ?>" />1. <?php echo lang('Suami/Istri')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['HubunganKeluarga']=='2' ? 'checked=""' : ''); ?> name="famHubungan1<?php echo $f ?>" />2. <?php echo lang('Anak')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['HubunganKeluarga']=='3' ? 'checked=""' : ''); ?> name="famHubungan1<?php echo $f ?>" />3. <?php echo lang('Lain-lain')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['HubunganKeluarga']=='4' ? 'checked=""' : ''); ?> name="famHubungan1<?php echo $f ?>" />4. <?php echo lang('Pekerja')?></div>
                </td>
                <td>
                    <?php
                    switch ($family['data'][$f]['WorkGardenStatus']) {
                        case '1':
                            $workGardenStatus1_1 = 'checked=""';
                        break;
                        case '2':
                            $workGardenStatus1_2 = 'checked=""';
                        break;
                    }
                    ?>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['WorkGardenStatus']=='1' ? 'checked=""' : ''); ?> name="famIkutKerja1<?php echo $f ?>" />1. <?php echo lang('Ya')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['WorkGardenStatus']=='2' ? 'checked=""' : ''); ?> name="famIkutKerja1<?php echo $f ?>" />2. <?php echo lang('Tidak')?></div>
                </td>
                <td>
                    <?php
                    switch ($family['data'][$f]['ActivityType']) {
                        case '1':
                            $activityType1_1 = 'checked=""';
                        break;
                        case '2':
                            $activityType1_2 = 'checked=""';
                        break;
                        case '3':
                            $activityType1_3 = 'checked=""';
                        break;
                        case '4':
                            $activityType1_4 = 'checked=""';
                        break;
                        case '5':
                            $activityType1_5 = 'checked=""';
                        break;
                    }
                    ?>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['ActivityType']=='1' ? 'checked=""' : ''); ?> name="famJenisKeg1<?php echo $f ?>" />1. <?php echo lang('Pengolahan Lahan')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['ActivityType']=='2' ? 'checked=""' : ''); ?> name="famJenisKeg1<?php echo $f ?>" />2. <?php echo lang('Bibit Baru')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['ActivityType']=='3' ? 'checked=""' : ''); ?> name="famJenisKeg1<?php echo $f ?>" />3. <?php echo lang('Pemeliharaan Tanaman')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['ActivityType']=='4' ? 'checked=""' : ''); ?> name="famJenisKeg1<?php echo $f ?>" />4. <?php echo lang('Panen')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['ActivityType']=='5' ? 'checked=""' : ''); ?> name="famJenisKeg1<?php echo $f ?>" />5. <?php echo lang('Paska Panen')?></div>
                </td>
                <td><?php echo $family['data'][$f]['TotalWorkingHrsPerDay']?></td>
                <td><?php echo $family['data'][$f]['TotalWorkingHrs']?></td>
                <td><?php echo number_format($family['data'][$f]['WageAmount'],0,",",".")?></td>
                <td><?php echo $family['data'][$f]['DateOfBirth']?></td>
                <td><?php echo $family['data'][$f]['AnggotaAge']?></td>
                <td>
                    <?php
                    switch ($family['data'][$f]['AnggotaGender']) {
                        case '1':
                            $famAnggotaGender1_1 = 'checked=""';
                        break;
                        case '2':
                            $famAnggotaGender1_2 = 'checked=""';
                        break;
                    }
                    ?>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['AnggotaGender']=='1' ? 'checked=""' : ''); ?> name="famGender1<?php echo $f ?>" />1. <?php echo lang('Laki-laki')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['AnggotaGender']=='2' ? 'checked=""' : ''); ?> name="famGender1<?php echo $f ?>" />2. <?php echo lang('Perempuan')?></div>
                </td>
                <td>
                    <?php
                    switch ($family['data'][$f]['StatusSekolah']) {
                        case '1':
                            $famStatusSekolah1_1 = 'checked=""';
                        break;
                        case '2':
                            $famStatusSekolah1_2 = 'checked=""';
                        break;
                    }
                    ?>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['StatusSekolah']=='1' ? 'checked=""' : ''); ?> name="famSekolah1<?php echo $f ?>" />1. <?php echo lang('Ya')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($family['data'][$f]['StatusSekolah']=='2' ? 'checked=""' : ''); ?> name="famSekolah1<?php echo $f ?>" />2. <?php echo lang('Tidak')?></div>
                </td>
            </tr>
            <?php if( ($f == 2 AND $maxDataFamily > $f+1 ) OR ($f>2 AND ($f-2)%10==0 AND $f+1 < $maxDataFamily) ){ ?>
                    </table>
                    <footer>
                        <?php echo lang('Page').' '.$halaman_P1A; $halaman_P1A++;?>
                    </footer>
                </div>
                <div class="page">
                    <table width="100%">
                        <tr>
                            <td class="tabelHeader" colspan="12">
                                B. <?php echo lang('Data Keluarga dan Pekerja')?>
                            </td>
                        </tr>
                        <tr>
                            <td class="topTabelValue" width="3%">No</td>
                            <td class="topTabelValue"><?php echo lang('Nama Anggota Keluarga dan Pekerja')?></td>
                            <td width="12%" class="topTabelValue"><?php echo lang('Hubungan')?></td>
                            <td width="9%" class="topTabelValue"><?php echo lang('Ikut bekerja di kebun')?></td>
                            <td width="20%" class="topTabelValue"><?php echo lang('Jenis kegiatan')?></td>
                            <td class="topTabelValue"><?php echo lang('Jumlah Jam Kerja per hari')?></td>
                            <td class="topTabelValue"><?php echo lang('Total hari kerja')?></td>
                            <td class="topTabelValue"><?php echo lang('Upah (Rp)')?></td>
                            <td class="topTabelValue"><?php echo lang('Tahun Lahir')?></td>
                            <td class="topTabelValue"><?php echo lang('Usia')?></td>
                            <td width="12%" class="topTabelValue"><?php echo lang('Jenis Kelamin')?></td>
                            <td width="9%" class="topTabelValue"><?php echo lang('Masih Sekolah')?></td>
                        </tr>
            <?php } ?>
        <?php } ?>
    <?php } else { ?>
        <tr class="rataAtas">
            <td align="center" colspan="12"><?php echo lang('Tidak ada data')?></td>
        </tr>
    <?php } ?>
    </table>

    <footer>
        <?php echo lang('Page').' '.$halaman_P1A; $halaman_P1A++;?>
    </footer>
</div> <!-- Halaman 1 End -->
<!-- mulai halaman pertama (END) -->

<!-- mulai halaman kedua (BEGIN) -->
<div class="page">
    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="6">
            C. <?php echo lang('Status Kebun Kakao')?>
        </td>
    </tr>
    <tr>
        <td class="topTabelValue" width="8%"><?php echo lang('No Kebun')?></td>
        <td class="topTabelValue"><?php echo lang('Luas Lahan (Ha)')?></td>
        <td class="topTabelValue"><?php echo lang('Status Kebun')?></td>
        <td class="topTabelValue"><?php echo lang('Status Sertifikasi')?></td>
        <td class="topTabelValue"><?php echo lang('Komoditas Lain')?></td>
        <td class="topTabelValue"><?php echo lang('Luas Lahan (Ha)')?></td>
    </tr>
    <?php if(count($gardenStatus) > 0 || count($gardenStatus) <= 0) { ?>
        <?php for($gs=0; $gs< $maxDataGardenStatus; $gs++){ ?>
            <tr class="rataAtas">
                <td align="center" width="3%"><?php echo $gardenStatus[$gs]['GardenNr']?></td>
                <td><?php echo $gardenStatus[$gs]['luasGarden']?></td>
                <td>
                    <?php
                    switch ($gardenStatus[$gs]['gardenStatus']) {
                        case '1':
                            $garStatusKebun1_1 = 'checked=""';
                        break;
                        case '2':
                            $garStatusKebun1_2 = 'checked=""';
                        break;
                    }

                    switch ($gardenStatus[$gs]['gardenStatusNotActive']) {
                        case '1':
                            $garStatusKebunNon1_1 = 'checked=""';
                        break;
                        case '2':
                            $garStatusKebunNon1_2 = 'checked=""';
                        break;
                        case '3':
                            $garStatusKebunNon1_3 = 'checked=""';
                        break;
                        case '4':
                            $garStatusKebunNon1_4 = 'checked=""';
                        break;
                        case '5':
                            $garStatusKebunNon1_5 = 'checked=""';
                        break;
                        case '6':
                            $garStatusKebunNon1_6 = 'checked=""';
                        break;
                    }
                    ?>
                    <table width="100%" height="100%" class="tabelNoBorder" border="0">
                    <tr>
                        <td width="39%">
                            <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['gardenStatus']=='1' ? 'checked=""' : ''); ?> name="garStatusKebun1<?php echo $gs ?>" value="" />1. <?php echo lang('Ya, Aktif')?></div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">
                            <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['gardenStatus']=='2' ? 'checked=""' : ''); ?> name="garStatusKebun1<?php echo $gs ?>" value="" />2. <?php echo lang('Tidak, Dengan alasan')?></div>
                        </td>
                        <td>
                            <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['gardenStatusNotActive']=='2' ? 'checked=""' : ''); ?> name="garStatusKebunNon1<?php echo $gs ?>" value="" />a. <?php echo lang('Pindah/beralih ke lahan lain')?></div>
                            <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['gardenStatusNotActive']=='3' ? 'checked=""' : ''); ?> name="garStatusKebunNon1<?php echo $gs ?>" value="" />b. <?php echo lang('Beralih ke komoditas lain')?></div>
                            <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['gardenStatusNotActive']=='4' ? 'checked=""' : ''); ?> name="garStatusKebunNon1<?php echo $gs ?><?php echo $gs ?>" value="" />c. <?php echo lang('Lahan dijual')?></div>
                            <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['gardenStatusNotActive']=='5' ? 'checked=""' : ''); ?> name="garStatusKebunNon1<?php echo $gs ?>" value="" />d. <?php echo lang('Diwariskan ke anggota keluarga')?></div>
                            <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['gardenStatusNotActive']=='6' ? 'checked=""' : ''); ?> name="garStatusKebunNon1<?php echo $gs ?>" value="" />e. <?php echo lang('Terkena hal yang tidak terduga/bencana')?></div>
                        </td>
                    </tr>
                    </table>
                </td>
                <td>
                    <?php
                    if($gardenStatus[$gs]['GardenNr'] != ""){
                        if($gardenStatus[$gs]['Certification'] != ""){
                            $garStatusSert1_1 = 'checked=""';
                        }else{
                            $garStatusSert1_2 = 'checked=""';
                        }
                    }
                    ?>
                    <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['GardenNr']!='' ? 'checked=""' : ''); ?> name="garStatusSert1<?php echo $gs ?>" value="" />1. <?php echo lang('Ya')?></div>
                    <div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['GardenNr']=='' ? 'checked=""' : ''); ?> name="garStatusSert1<?php echo $gs ?>" value="" />2. <?php echo lang('Tidak')?></div>
                </td>
                <td>
                    <?php
                    switch ($gardenStatus[$gs]['Commodity']) {
                        case '1':
                            $garKomLain1_1 = 'checked=""';
                            $garKomLainHa1_1 = $gardenStatus[$gs]['CommodityHa'];
                        break;
                        case '2':
                            $garKomLain1_2 = 'checked=""';
                            $garKomLainHa1_2 = $gardenStatus[$gs]['CommodityHa'];
                        break;
                        case '3':
                            $garKomLain1_3 = 'checked=""';
                            $garKomLainHa1_3 = $gardenStatus[$gs]['CommodityHa'];
                        break;
                        case '4':
                            $garKomLain1_4 = 'checked=""';
                            $garKomLainHa1_4 = $gardenStatus[$gs]['CommodityHa'];
                        break;
                        case '5':
                            $garKomLain1_5 = 'checked=""';
                            $garKomLainHa1_5 = $gardenStatus[$gs]['CommodityHa'];
                        break;
                        case '7':
                            $garKomLain1_7 = 'checked=""';
                            $garKomLainHa1_7 = $gardenStatus[$gs]['CommodityHa'];
                        break;
                        case '8':
                            $garKomLain1_8 = 'checked=""';
                            $garKomLainHa1_8 = $gardenStatus[$gs]['CommodityHa'];
                        break;
                        case '9':
                            $garKomLain1_9 = 'checked=""';
                            $garKomLainHa1_9 = $gardenStatus[$gs]['CommodityHa'];
                        break;
                    }
                    ?>
                    <table width="100%" height="100%" class="tabelNoBorder" border="0">
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['Commodity']=='1' ? 'checked=""' : ''); ?> name="garKomLain1<?php echo $gs ?>" value="" />1. <?php echo lang('Jagung')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['Commodity']=='2' ? 'checked=""' : ''); ?> name="garKomLain1<?php echo $gs ?>" value="" />2. <?php echo lang('Sawit')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['Commodity']=='3' ? 'checked=""' : ''); ?> name="garKomLain1<?php echo $gs ?>" value="" />3. <?php echo lang('Karet')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['Commodity']=='4' ? 'checked=""' : ''); ?> name="garKomLain1<?php echo $gs ?>" value="" />4. <?php echo lang('Cengkeh')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['Commodity']=='5' ? 'checked=""' : ''); ?> name="garKomLain1<?php echo $gs ?>" value="" />5. <?php echo lang('Padi')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['Commodity']=='8' ? 'checked=""' : ''); ?> name="garKomLain1<?php echo $gs ?>" value="" />6. <?php echo lang('Buah-buahan')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['Commodity']=='9' ? 'checked=""' : ''); ?> name="garKomLain1<?php echo $gs ?>" value="" />7. <?php echo lang('Kayu-kayuan')?></div></td>
                    </tr>
                    <tr height="25">
                        <td><div class="flexBox"><input type="radio" <?php echo ($gardenStatus[$gs]['Commodity']=='7' ? 'checked=""' : ''); ?> name="garKomLain1<?php echo $gs ?>" value="" />8. <?php echo lang('Lainnya')?></div></td>
                    </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" height="100%" class="tabelNoBorder" border="0">
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($gardenStatus[$gs]['Commodity']=='1' ? $gardenStatus[$gs]['CommodityHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($gardenStatus[$gs]['Commodity']=='2' ? $gardenStatus[$gs]['CommodityHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($gardenStatus[$gs]['Commodity']=='3' ? $gardenStatus[$gs]['CommodityHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($gardenStatus[$gs]['Commodity']=='4' ? $gardenStatus[$gs]['CommodityHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($gardenStatus[$gs]['Commodity']=='5' ? $gardenStatus[$gs]['CommodityHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($gardenStatus[$gs]['Commodity']=='8' ? $gardenStatus[$gs]['CommodityHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($gardenStatus[$gs]['Commodity']=='9' ? $gardenStatus[$gs]['CommodityHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td><?php echo ($gardenStatus[$gs]['Commodity']=='7' ? $gardenStatus[$gs]['CommodityHa'] : ''); ?></td>
                    </tr>
                    </table>
                </td>
            </tr>
            <?php if( ($gs == 3 AND $maxDataGardenStatus > $gs+1 ) OR ($gs>3 AND ($gs-3)%4==0 AND $gs+1 < $maxDataGardenStatus) ){ ?>
                    </table>
                    <footer>
                        <?php if($gs<5){?>
                        <?php echo lang('Page').' '.$halaman_P1A; $halaman_P1A++;?>
                        <?php }else{?>
                        <?php echo lang('Page').' '.$halaman_P1A; $halaman_P1A++;?>
                        <?php }?>
                    </footer>
                </div>
                <div class="page">
                    <table width="100%">
                        <tr>
                            <td class="tabelHeader" colspan="6">
                                C. <?php echo lang('Status Kebun Kakao')?>
                            </td>
                        </tr>
                        <tr>
                            <td class="topTabelValue" width="8%"><?php echo lang('No Kebun')?></td>
                            <td class="topTabelValue"><?php echo lang('Luas Lahan (Ha)')?></td>
                            <td class="topTabelValue"><?php echo lang('Status Kebun')?></td>
                            <td class="topTabelValue"><?php echo lang('Status Sertifikasi')?></td>
                            <td class="topTabelValue"><?php echo lang('Komoditas Lain')?></td>
                            <td class="topTabelValue"><?php echo lang('Luas Lahan (Ha)')?></td>
                        </tr>
            <?php } ?>
        <?php } ?>
    <?php } else { ?>
        <tr class="rataAtas">
            <td align="center" colspan="6"><?php echo lang('Tidak ada data')?></td>
        </tr>
    <?php } ?>

    </table>
    <footer>
        <?php echo lang('Page').' '.$halaman_P1A; $halaman_P1A++;?>
    </footer>
    <br>
</div>
<div class="page">
    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="3">
            D. <?php echo lang('Status Kebun Lainnya')?>
        </td>
    </tr>
    <tr>
        <td class="topTabelValue" width="8%"><?php echo lang('No.')?></td>
        <td class="topTabelValue"><?php echo lang('Komoditi')?></td>
        <td class="topTabelValue"><?php echo lang('Luas Lahan (Ha)')?></td>
    </tr>
    <?php if(count($otherLand) > 0 || count($otherLand) <= 0) { ?>
        <?php for($ol=0; $ol< $maxDataOtherLand; $ol++){ ?>
            <tr class="rataAtas">
                <?php
                if($otherLand[$ol]['Commodity'] != ""){
                    $otherLandNo1 = "1";

                    switch ($otherLand[$ol]['Commodity']) {
                        case '1':
                            $komLain1_1 = 'checked=""';
                            $komLainHa1_1 = $otherLand[$ol]['GardenHa'];
                        break;
                        case '2':
                            $komLain1_2 = 'checked=""';
                            $komLainHa1_2 = $otherLand[$ol]['GardenHa'];
                        break;
                        case '3':
                            $komLain1_3 = 'checked=""';
                            $komLainHa1_3 = $otherLand[$ol]['GardenHa'];
                        break;
                        case '4':
                            $komLain1_4 = 'checked=""';
                            $komLainHa1_4 = $otherLand[$ol]['GardenHa'];
                        break;
                        case '5':
                            $komLain1_5 = 'checked=""';
                            $komLainHa1_5 = $otherLand[$ol]['GardenHa'];
                        break;
                        case '7':
                            $komLain1_7 = 'checked=""';
                            $komLainHa1_7 = $otherLand[$ol]['GardenHa'];
                        break;
                        case '8':
                            $komLain1_8 = 'checked=""';
                            $komLainHa1_8 = $otherLand[$ol]['GardenHa'];
                        break;
                        case '9':
                            $komLain1_9 = 'checked=""';
                            $komLainHa1_9 = $otherLand[$ol]['GardenHa'];
                        break;
                    }
                }
                ?>
                <td align="center"><?php echo $otherLandNo1?></td>
                <td>
                    <table width="100%" height="100%" class="tabelNoBorder" border="0">
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($otherLand[$ol]['Commodity']=='1' ? 'checked=""' : ''); ?> name="komLain1_1<?php echo $ol ?>" value="" />1. <?php echo lang('Jagung')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($otherLand[$ol]['Commodity']=='2' ? 'checked=""' : ''); ?> name="komLain1_2<?php echo $ol ?>" value="" />2. <?php echo lang('Sawit')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($otherLand[$ol]['Commodity']=='3' ? 'checked=""' : ''); ?> name="komLain1_3<?php echo $ol ?>" value="" />3. <?php echo lang('Karet')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($otherLand[$ol]['Commodity']=='4' ? 'checked=""' : ''); ?> name="komLain1_4<?php echo $ol ?>" value="" />4. <?php echo lang('Cengkeh')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($otherLand[$ol]['Commodity']=='5' ? 'checked=""' : ''); ?> name="komLain1_5<?php echo $ol ?>" value="" />5. <?php echo lang('Padi')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($otherLand[$ol]['Commodity']=='8' ? 'checked=""' : ''); ?> name="komLain1_8<?php echo $ol ?>" value="" />6. <?php echo lang('Buah-buahan')?></div></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><div class="flexBox"><input type="radio" <?php echo ($otherLand[$ol]['Commodity']=='9' ? 'checked=""' : ''); ?> name="komLain1_9<?php echo $ol ?>" value="" />7. <?php echo lang('Kayu-kayuan')?></div></td>
                    </tr>
                    <tr height="25">
                        <td><div class="flexBox"><input type="radio" <?php echo ($otherLand[$ol]['Commodity']=='7' ? 'checked=""' : ''); ?> name="komLain1_7<?php echo $ol ?>" value="" />8. <?php echo lang('Lainnya')?></div></td>
                    </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" height="100%" class="tabelNoBorder" border="0">
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($otherLand[$ol]['Commodity']=='1' ? $otherLand[$ol]['GardenHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($otherLand[$ol]['Commodity']=='2' ? $otherLand[$ol]['GardenHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($otherLand[$ol]['Commodity']=='3' ? $otherLand[$ol]['GardenHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($otherLand[$ol]['Commodity']=='4' ? $otherLand[$ol]['GardenHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($otherLand[$ol]['Commodity']=='5' ? $otherLand[$ol]['GardenHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($otherLand[$ol]['Commodity']=='8' ? $otherLand[$ol]['GardenHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td style="border-bottom:1px solid black;"><?php echo ($otherLand[$ol]['Commodity']=='9' ? $otherLand[$ol]['GardenHa'] : ''); ?></td>
                    </tr>
                    <tr height="25">
                        <td><?php echo ($otherLand[$ol]['Commodity']=='7' ? $otherLand[$ol]['GardenHa'] : ''); ?></td>
                    </tr>
                    </table>
                </td>
            </tr>
            <?php if( ($ol == 4 AND $maxDataOtherLand > $ol+1 ) OR ($ol>4 AND ($ol-4)%5==0 AND $ol+1 < $maxDataOtherLand) ){ ?>
                </table>
                <footer>
                    <?php if($gs<5){?>
                    <?php echo lang('Page').' '.$halaman_P1A; $halaman_P1A++;?>
                    <?php }else{?>
                    <?php echo lang('Page').' '.$halaman_P1A; $halaman_P1A++;?>
                    <?php }?>
                </footer>
                </div>
                <div class="page">
                    <table width="100%">
                        <tr>
                            <td class="tabelHeader" colspan="3">
                                D. <?php echo lang('Status Kebun Lainnya')?>
                            </td>
                        </tr>
                        <tr>
                            <td class="topTabelValue" width="8%"><?php echo lang('No.')?></td>
                            <td class="topTabelValue"><?php echo lang('Komoditi')?></td>
                            <td class="topTabelValue"><?php echo lang('Luas Lahan (Ha)')?></td>
                        </tr>
            <?php } ?>
        <?php } ?>

    <?php } else { ?>
        <tr class="rataAtas">
            <td align="center" colspan="3"><?php echo lang('Tidak ada data')?></td>
        </tr>
    <?php } ?>
    </table>

    <footer>
        <?php echo lang('Page').' '.$halaman_P1A; $halaman_P1A++;?>
    </footer>
</div>
<!-- halaman kelima (END) -->

<?php for($g=0; $g < count($garden); $g++) { ?>
<!-- halaman pertama (BEGIN) -->
<div class="page">

    <!-- LOGO ATAS (BEGIN) -->
    <table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
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
    <!-- LOGO ATAS (END) -->

    <h2 class="judulHalaman"><?php echo lang('Survei Rumah Tangga Petani Coklat')?></h2>
    <h2 class="judulHalaman"><?php echo lang('P1B. Data Kebun (Khusus Sertifikasi)')?></h2>
    <br />

    <table width="100%">
    <tr>
        <td class="bgBiru" width="15%"><?php echo lang('No. ID Petani')?></td>
        <td width="15%"><?php echo ($garden[$g]['FarmerID']!='' ? $garden[$g]['FarmerID'] : '&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_'); ?></td>
        <td class="bgBiru" width="15%"><?php echo lang('No Kebun')?></td>
        <td width="15%"><?php echo ($garden[$g]['GardenNr']!='' ? $garden[$g]['GardenNr'] : '&nbsp;_&nbsp;_</td>'); ?>
        <td class="bgBiru" width="15%"><?php echo lang('No Survei')?></td>
        <td width="15%"><?php echo ($garden[$g]['SurveyNr']!='' ? $garden[$g]['SurveyNr'] : '&nbsp;_&nbsp;_'); ?></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            A. <?php echo lang('Lokasi dan luas kebun kakao')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue" width="25%"><?php echo lang('Latitude')?></td>
        <td><?php echo ($garden[$g]['Latitude']!='' ? $garden[$g]['Latitude'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Longitude')?></td>
        <td><?php echo ($garden[$g]['Longitude']!='' ? $garden[$g]['Longitude'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Elevation')?></td>
        <td><?php echo ($garden[$g]['Not']!='Elevation' ? $garden[$g]['Elevation'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> mdpl</td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jarak dari rumah ke kebun kakao')?></td>
        <td><?php echo ($garden[$g]['GardenDistance']!='' ? $garden[$g]['GardenDistance'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> meter</td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Ukuran kebun')?></td>
        <td><?php echo ($garden[$g]['GardenHaUnCertified']!='' ? $garden[$g]['GardenHaUnCertified'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('hektar')?></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            B. <?php echo lang('Kondisi jalan dan status kepemilikan')?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue" width="25%"><?php echo lang('Kondisi jalan ke kebun kakao')?></td>
        <td>
            <table width="100%" height="100%" class="tabelNoBorder" border="0">
            <tr>
                <td width="50%">
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garJalanKeKebun" value="" <?php echo ($garden[$g]['RoadCondition']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Jalan Aspal')?></div>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garJalanKeKebun" value="" <?php echo ($garden[$g]['RoadCondition']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('Jalan Tanah')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garJalanKeKebun" value="" <?php echo ($garden[$g]['RoadCondition']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Jalan Pengerasan')?></div>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garJalanKeKebun" value="" <?php echo ($garden[$g]['RoadCondition']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Tidak Ada Jalan')?></div>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Status kepemilikan tanah')?></td>
        <td>
            <table width="100%" height="100%" class="tabelNoBorder" border="0">
            <tr>
                <td width="50%">
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garStsMilikTanah" value="" <?php echo ($garden[$g]['OwnershipCocoa']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Pemilik Penggarap')?></div>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garStsMilikTanah" value="" <?php echo ($garden[$g]['OwnershipCocoa']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('Petani Penyewa')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garStsMilikTanah" value="" <?php echo ($garden[$g]['OwnershipCocoa']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Petani Bagi Hasil')?></div>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garStsMilikTanah" value="" <?php echo ($garden[$g]['OwnershipCocoa']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Lain-lain')?></div>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Pemilik tanah')?></td>
        <td>
            <table width="100%" height="100%" class="tabelNoBorder" border="0">
            <tr>
                <td width="50%">
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPemilikTanah" value="" <?php echo ($garden[$g]['LandOwner']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Saya Sendiri')?></div>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPemilikTanah" value="" <?php echo ($garden[$g]['LandOwner']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('Orang Lain')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPemilikTanah" value="" <?php echo ($garden[$g]['LandOwner']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Anggota Keluarga')?></div>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPemilikTanah" value="" <?php echo ($garden[$g]['LandOwner']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Tidak Tahu')?></div>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Sertifikat kepemilikan tanah')?></td>
        <td>
            <table width="100%" height="100%" class="tabelNoBorder" border="0">
            <tr>
                <td>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Tidak Ada')?></div>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Desa/Lurah')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Akte Notaris/BPN')?></div>
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='5' ? 'checked=""' : ''); ?> />5. <?php echo lang('Tidak Tahu')?></div>
                </td>
                <td style="vertical-align:top;">
                    <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('SKKT (Camat)')?></div>
                    <div class="flexBox">&nbsp;</div>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="4">
            C. <?php echo lang('Kondisi Tanaman Kakao')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue" width="40%"><?php echo lang('Tahun Tanam Kakao')?></td>
        <td colspan="3">
            <?php echo ($garden[$g]['TahunTanamanCocoa']!='' ? $garden[$g]['TahunTanamanCocoa'] : ''); ?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah Tanaman Belum Menghasilkan')?></td>
        <td colspan="3">
            <?php echo ($garden[$g]['PohonTBM']!='' ? $garden[$g]['PohonTBM'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah Tanaman Menghasilkan')?></td>
        <td colspan="3">
            <?php echo ($garden[$g]['PohonTM']!='' ? $garden[$g]['PohonTM'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah Tanaman Rusak')?></td>
        <td colspan="3">
            <?php echo ($garden[$g]['PohonRehab']!='' ? $garden[$g]['PohonRehab'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah Pohon sambung samping/sambung pucuk tunas air')?></td>
        <td width="20%">
            <?php echo ($garden[$g]['GraftedTrees']!='' ? $garden[$g]['GraftedTrees'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
        <td class="leftValue" width="15%">
            <?php echo lang('Tahun Tanam')?>
        </td>
        <td><?php echo ($garden[$g]['GraftedTreesTahun']!='' ? $garden[$g]['GraftedTreesTahun'] : '&nbsp;_&nbsp;_&nbsp;_&nbsp;_'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah penanaman ulang dari sambung pucuk dan biji')?></td>
        <td>
            <?php echo ($garden[$g]['TopGraftedTrees']!='' ? $garden[$g]['TopGraftedTrees'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
        <td class="leftValue">
            <?php echo lang('Tahun Tanam')?>
        </td>
        <td><?php echo ($garden[$g]['TopGraftedTreesTahun']!='' ? $garden[$g]['TopGraftedTreesTahun'] : '&nbsp;_&nbsp;_&nbsp;_&nbsp;_'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah penanaman ulang sisipan')?></td>
        <td>
            <?php echo ($garden[$g]['ReplantedTrees']!='' ? $garden[$g]['ReplantedTrees'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
        <td class="leftValue">
            <?php echo lang('Tahun Tanam')?>
        </td>
        <td><?php echo ($garden[$g]['ReplantedTreesTahun']!='' ? $garden[$g]['ReplantedTreesTahun'] : '&nbsp;_&nbsp;_&nbsp;_&nbsp;_'); ?></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="6">
            D. <?php echo lang('Jumlah Varietas Tanaman Kakao Tersertifikasi')?>
        </td>
    </tr>
    <tr>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Varietas')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Varietas')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Varietas')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['S1']=='1' ? 'checked=""' : ''); ?> />&nbsp;S1</td>
        <td><?php echo ($garden[$g]['S1Nr']!='' && $garden[$g]['S1Nr']!='0' ? $garden[$g]['S1Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['TSH858']=='1' ? 'checked=""' : ''); ?> />&nbsp;TSH 858</td>
        <td><?php echo ($garden[$g]['TSH858Nr']!='' && $garden[$g]['TSH858Nr']!='0' ? $garden[$g]['TSH858Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['RCC70']=='1' ? 'checked=""' : ''); ?> />&nbsp;RCC 70</td>
        <td><?php echo ($garden[$g]['RCC70Nr']!='' && $garden[$g]['RCC70Nr']!='0' ? $garden[$g]['RCC70Nr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['S2']=='1' ? 'checked=""' : ''); ?> />&nbsp;S2</td>
        <td><?php echo ($garden[$g]['S2Nr']!='' && $garden[$g]['S2Nr']!='0' ? $garden[$g]['S2Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['ICRRI3']=='1' ? 'checked=""' : ''); ?> />&nbsp;ICRRI 3</td>
        <td><?php echo ($garden[$g]['ICRRI3Nr']!='' && $garden[$g]['ICRRI3Nr']!='0' ? $garden[$g]['ICRRI3Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['RCC71']=='1' ? 'checked=""' : ''); ?> />&nbsp;RCC 71</td>
        <td><?php echo ($garden[$g]['RCC71Nr']!='' && $garden[$g]['RCC71Nr']!='0' ? $garden[$g]['RCC71Nr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['J45']=='1' ? 'checked=""' : ''); ?> />&nbsp;45/MCC02</td>
        <td><?php echo ($garden[$g]['J45Nr']!='' && $garden[$g]['J45Nr']!='0' ? $garden[$g]['J45Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['ICRRI4']=='1' ? 'checked=""' : ''); ?> />&nbsp;ICRRI 4</td>
        <td><?php echo ($garden[$g]['ICRRI4Nr']!='' && $garden[$g]['ICRRI4Nr']!='0' ? $garden[$g]['ICRRI4Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['RCC72']=='1' ? 'checked=""' : ''); ?> />&nbsp;RCC 72</td>
        <td><?php echo ($garden[$g]['RCC72Nr']!='' && $garden[$g]['RCC72Nr']!='0' ? $garden[$g]['RCC72Nr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['M01']=='1' ? 'checked=""' : ''); ?> />&nbsp;M01</td>
        <td><?php echo ($garden[$g]['M01Nr']!='' && $garden[$g]['M01Nr']!='0' ? $garden[$g]['M01Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['ICRRI5']=='1' ? 'checked=""' : ''); ?> />&nbsp;ICRRI 5</td>
        <td><?php echo ($garden[$g]['ICRRI5Nr']!='' && $garden[$g]['ICRRI5Nr']!='0' ? $garden[$g]['ICRRI5Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['RCC73']=='1' ? 'checked=""' : ''); ?> />&nbsp;RCC 73</td>
        <td><?php echo ($garden[$g]['RCC73Nr']!='' && $garden[$g]['RCC73Nr']!='0' ? $garden[$g]['RCC73Nr'] : '&nbsp;'); ?></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="6">
            E. <?php echo lang('Jumlah Varietas Tanaman Kakao Non Sertifikasi')?>
        </td>
    </tr>
    <tr>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Varietas')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Varietas')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Varietas')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Lokal']=='1' ? 'checked=""' : ''); ?> />&nbsp;Lokal</td>
        <td><?php echo ($garden[$g]['LokalNr']!='' && $garden[$g]['LokalNr']!='0' ? $garden[$g]['LokalNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['MT']=='1' ? 'checked=""' : ''); ?> />&nbsp;MT</td>
        <td><?php echo ($garden[$g]['MTNr']!='' && $garden[$g]['MTNr']!='0' ? $garden[$g]['MTNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['BB01']=='1' ? 'checked=""' : ''); ?> />&nbsp;BB01</td>
        <td><?php echo ($garden[$g]['BB01Nr']!='' && $garden[$g]['BB01Nr']!='0' ? $garden[$g]['BB01Nr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['RCL']=='1' ? 'checked=""' : ''); ?> />&nbsp;RCL</td>
        <td><?php echo ($garden[$g]['RCLNr']!='' && $garden[$g]['RCLNr']!='0' ? $garden[$g]['RCLNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['M02']=='1' ? 'checked=""' : ''); ?> />&nbsp;M02</td>
        <td><?php echo ($garden[$g]['M02Nr']!='' && $garden[$g]['M02Nr']!='0' ? $garden[$g]['M02Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['BLB']=='1' ? 'checked=""' : ''); ?> />&nbsp;BLB</td>
        <td><?php echo ($garden[$g]['BLBNr']!='' && $garden[$g]['BLBNr']!='0' ? $garden[$g]['BLBNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['THR']=='1' ? 'checked=""' : ''); ?> />&nbsp;THR</td>
        <td><?php echo ($garden[$g]['THRNr']!='' && $garden[$g]['THRNr']!='0' ? $garden[$g]['THRNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['M04']=='1' ? 'checked=""' : ''); ?> />&nbsp;M04</td>
        <td><?php echo ($garden[$g]['M04Nr']!='' && $garden[$g]['M04Nr']!='0' ? $garden[$g]['M04Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['BRT']=='1' ? 'checked=""' : ''); ?> />&nbsp;BRT</td>
        <td><?php echo ($garden[$g]['BRTNr']!='' && $garden[$g]['BRTNr']!='0' ? $garden[$g]['BRTNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['AP']=='1' ? 'checked=""' : ''); ?> />&nbsp;AP</td>
        <td><?php echo ($garden[$g]['APNr']!='' && $garden[$g]['APNr']!='0' ? $garden[$g]['APNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['M06']=='1' ? 'checked=""' : ''); ?> />&nbsp;M06</td>
        <td><?php echo ($garden[$g]['M06Nr']!='' && $garden[$g]['M06Nr']!='0' ? $garden[$g]['M06Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['MHP04']=='1' ? 'checked=""' : ''); ?> />&nbsp;MHP04</td>
        <td><?php echo ($garden[$g]['MHP04Nr']!='' && $garden[$g]['MHP04Nr']!='0' ? $garden[$g]['MHP04Nr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['PR']=='1' ? 'checked=""' : ''); ?> />&nbsp;PR</td>
        <td><?php echo ($garden[$g]['PRNr']!='' && $garden[$g]['PRNr']!='0' ? $garden[$g]['PRNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['MHP03']=='1' ? 'checked=""' : ''); ?> />&nbsp;MHP03</td>
        <td><?php echo ($garden[$g]['MHP03Nr']!='' && $garden[$g]['MHP03Nr']!='0' ? $garden[$g]['MHP03Nr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Scavina']=='1' ? 'checked=""' : ''); ?> />&nbsp;Scavina</td>
        <td><?php echo ($garden[$g]['ScavinaNr']!='' && $garden[$g]['ScavinaNr']!='0' ? $garden[$g]['ScavinaNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue">&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Not']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Lainnya (sebutkan nama varietas)')?></div>
            <div class="flexBox">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div class="flexBox">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
        </td>
        <td colspan="2">
            <div class="flexBox">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div class="flexBox">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div class="flexBox">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('pohon')?></div>
        </td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 1 <?php echo lang('from')?> 8
    </footer>
</div>
<!-- halaman pertama (END) -->

<!-- halaman kedua (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="6">
            F. <?php echo lang('Tanaman Lain dalam Kebun Kakao')?>
        </td>
    </tr>
    <tr>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jenis')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jenis')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jenis')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Kelapa']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Kelapa')?></td>
        <td><?php echo ($garden[$g]['KelapaNr']!='' && $garden[$g]['KelapaNr']!='0' ? $garden[$g]['KelapaNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Mahoni']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Mahoni')?></td>
        <td><?php echo ($garden[$g]['MahoniNr']!='' && $garden[$g]['MahoniNr']!='0' ? $garden[$g]['MahoniNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Nangka']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Nangka')?></td>
        <td><?php echo ($garden[$g]['NangkaNr']!='' && $garden[$g]['NangkaNr']!='0' ? $garden[$g]['NangkaNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Karet']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Karet')?></td>
        <td><?php echo ($garden[$g]['KaretNr']!='' && $garden[$g]['KaretNr']!='0' ? $garden[$g]['KaretNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Jabon']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Jabon')?></td>
        <td><?php echo ($garden[$g]['JabonNr']!='' && $garden[$g]['JabonNr']!='0' ? $garden[$g]['JabonNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Pisang']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Pisang')?></td>
        <td><?php echo ($garden[$g]['PisangNr']!='' && $garden[$g]['PisangNr']!='0' ? $garden[$g]['PisangNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['JambuMente']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Mente')?></td>
        <td><?php echo ($garden[$g]['JambuMenteNr']!='' && $garden[$g]['JambuMenteNr']!='0' ? $garden[$g]['JambuMenteNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Uru']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Uru')?></td>
        <td><?php echo ($garden[$g]['UruNr']!='' && $garden[$g]['UruNr']!='0' ? $garden[$g]['UruNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Rambutan']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Rambutan')?></td>
        <td><?php echo ($garden[$g]['RambutanNr']!='' && $garden[$g]['RambutanNr']!='0' ? $garden[$g]['RambutanNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Sawit']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Sawit')?></td>
        <td><?php echo ($garden[$g]['SawitNr']!='' && $garden[$g]['SawitNr']!='0' ? $garden[$g]['SawitNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Biti']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Biti')?></td>
        <td><?php echo ($garden[$g]['BitiNr']!='' && $garden[$g]['BitiNr']!='0' ? $garden[$g]['BitiNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Mangga']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Mangga')?></td>
        <td><?php echo ($garden[$g]['ManggaNr']!='' && $garden[$g]['ManggaNr']!='0' ? $garden[$g]['ManggaNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Pala']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Pala')?></td>
        <td><?php echo ($garden[$g]['PalaNr']!='' && $garden[$g]['PalaNr']!='0' ? $garden[$g]['PalaNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Jati']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Jati')?></td>
        <td><?php echo ($garden[$g]['JatiNr']!='' && $garden[$g]['JatiNr']!='0' ? $garden[$g]['JatiNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Langsat']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Langsat')?></td>
        <td><?php echo ($garden[$g]['LangsatNr']!='' && $garden[$g]['LangsatNr']!='0' ? $garden[$g]['LangsatNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Kapok']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Kapok')?></td>
        <td><?php echo ($garden[$g]['KapokNr']!='' && $garden[$g]['KapokNr']!='0' ? $garden[$g]['KapokNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Sengon']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Sengon')?></td>
        <td><?php echo ($garden[$g]['SengonNr']!='' && $garden[$g]['SengonNr']!='0' ? $garden[$g]['SengonNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Kedondong']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Kedondong')?></td>
        <td><?php echo ($garden[$g]['KedondongNr']!='' && $garden[$g]['KedondongNr']!='0' ? $garden[$g]['KedondongNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Pinang']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Pinang')?></td>
        <td><?php echo ($garden[$g]['PinangNr']!='' && $garden[$g]['PinangNr']!='0' ? $garden[$g]['PinangNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Gamal']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Gamal')?></td>
        <td><?php echo ($garden[$g]['GamalNr']!='' && $garden[$g]['GamalNr']!='0' ? $garden[$g]['GamalNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Jeruk']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Jeruk')?></td>
        <td><?php echo ($garden[$g]['JerukNr']!='' && $garden[$g]['JerukNr']!='0' ? $garden[$g]['JerukNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Cengkeh']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Cengkeh')?></td>
        <td><?php echo ($garden[$g]['CengkehNr']!='' && $garden[$g]['CengkehNr']!='0' ? $garden[$g]['CengkehNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Petai']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Petai')?></td>
        <td><?php echo ($garden[$g]['PetaiNr']!='' && $garden[$g]['PetaiNr']!='0' ? $garden[$g]['PetaiNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Alpukat']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Alpukat')?></td>
        <td><?php echo ($garden[$g]['AlpukatNr']!='' && $garden[$g]['AlpukatNr']!='0' ? $garden[$g]['AlpukatNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Aren']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Aren')?></td>
        <td><?php echo ($garden[$g]['ArenNr']!='' && $garden[$g]['ArenNr']!='0' ? $garden[$g]['ArenNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Lamtoro']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Lamtoro')?></td>
        <td><?php echo ($garden[$g]['LamtoroNr']!='' && $garden[$g]['LamtoroNr']!='0' ? $garden[$g]['LamtoroNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Sukun']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Sukun')?></td>
        <td><?php echo ($garden[$g]['SukunNr']!='' && $garden[$g]['SukunNr']!='0' ? $garden[$g]['SukunNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Kemiri']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Kemiri')?></td>
        <td><?php echo ($garden[$g]['KemiriNr']!='' && $garden[$g]['KemiriNr']!='0' ? $garden[$g]['KemiriNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Jengkol']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Jengkol')?></td>
        <td><?php echo ($garden[$g]['JengkolNr']!='' && $garden[$g]['JengkolNr']!='0' ? $garden[$g]['JengkolNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Pepaya']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Pepaya')?></td>
        <td><?php echo ($garden[$g]['PepayaNr']!='' && $garden[$g]['PepayaNr']!='0' ? $garden[$g]['PepayaNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Manggis']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Manggis')?></td>
        <td><?php echo ($garden[$g]['ManggisNr']!='' && $garden[$g]['ManggisNr']!='0' ? $garden[$g]['ManggisNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Jambu']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Jambu')?></td>
        <td><?php echo ($garden[$g]['JambuNr']!='' && $garden[$g]['JambuNr']!='0' ? $garden[$g]['JambuNr'] : '&nbsp;'); ?></td>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Cempedak']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Cempedak')?></td>
        <td><?php echo ($garden[$g]['CempedakNr']!='' && $garden[$g]['CempedakNr']!='0' ? $garden[$g]['CempedakNr'] : '&nbsp;'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" <?php echo ($garden[$g]['Durian']=='1' ? 'checked=""' : ''); ?> />&nbsp;<?php echo lang('Durian')?></td>
        <td><?php echo ($garden[$g]['DurianNr']!='' && $garden[$g]['DurianNr']!='0' ? $garden[$g]['DurianNr'] : '&nbsp;'); ?></td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td class="leftValue" colspan="6"><strong><?php echo lang('Tanaman Penaung/Pelindung')?></strong></td>
    </tr>
    <tr>
        <td class="leftValue" colspan="4"><?php echo lang('Total Pohon Penaung/Pelindung')?></td>
        <td colspan="2">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('pohon')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue" colspan="6"><strong><?php echo lang('Mengapa anda menanam pohon pelindung ?')?></strong></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesIncProductivity']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Untuk meningkatkan produktivitas tanaman kakao')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Untuk meningkatkan nilai tanah')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesIncLandValue']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Untuk mendapatkan penghasilan tambahan')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesAddFirewood']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Untuk menambah sumber kayu bakar')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesProtectSoil']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Untuk melindungi tanah')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesAddFodder']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Untuk menambah sumber makanan ternak')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesReducePests']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Untuk mengurangi serangan hama dan penyakit')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesDoNotKnow']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Saya tidak tahu')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesReduceHeat']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Untuk mengurangi suhu panas di kebun')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['ShadeTreesOthers']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Lainnya')?></div></td>
    </tr>
    <tr>
        <td colspan="4" class="leftValue">
            (C) <?php echo lang('Apakah pohon penaung tersebar merata di kebun ?')?>
        </td>
        <td colspan="2">
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPhnPenaungTersebar" <?php echo ($garden[$g]['ShadeTreesSpreadEvently']=='1' ? 'checked=""' : ''); ?>><?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="<?php echo $g ?>garPhnPenaungTersebar" <?php echo ($garden[$g]['ShadeTreesSpreadEvently']=='2' ? 'checked=""' : ''); ?>><?php echo lang('Tidak')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="4" class="leftValue" style="vertical-align:top;">
            (C) <?php echo lang('Darimana anda mendapatkan bibit pohon penaung ?')?>
        </td>
        <td colspan="2">
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garDptBibitPhnPenaung" <?php echo ($garden[$g]['ShadeTreesObtainSeeds']=='1' ? 'checked=""' : ''); ?>>1.<?php echo lang('Kelompok Tani')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garDptBibitPhnPenaung" <?php echo ($garden[$g]['ShadeTreesObtainSeeds']=='2' ? 'checked=""' : ''); ?>>2.<?php echo lang('Koperasi/IMS')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garDptBibitPhnPenaung" <?php echo ($garden[$g]['ShadeTreesObtainSeeds']=='3' ? 'checked=""' : ''); ?>>3.<?php echo lang('Pedagang bibit')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garDptBibitPhnPenaung" <?php echo ($garden[$g]['ShadeTreesObtainSeeds']=='4' ? 'checked=""' : ''); ?>>4.<?php echo lang('Membuat sendiri')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="6" class="leftValue">
            <strong><?php echo lang('Tanaman penutup tanah')?></strong>
        </td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Nuts']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Kacang-kacangan')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Tubers']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Umbi-umbian')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Patchouli']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Nilam')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['CoverCropOthers']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Lain-lain')?></div></td>
    </tr>
    <tr>
        <td colspan="6"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['NoCoverCrop']=='1' ? 'checked=""' : ''); ?> /><?php echo lang('Tidak ada cover crop')?></div></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            G. <?php echo lang('Bibit Kakao (Hanya untuk kebun yang digunakan dalam program sertifikasi UTZ)')?>
        </td>
    </tr>
    <tr>
        <td width="50%" class="leftValue">(C) <?php echo lang('Darimana anda memperoleh bibit saat ini?')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPerolehBibit" <?php echo ($garden[$g]['ObtainSeedsToday']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Supplier yang direkomendasikan IMS')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPerolehBibit" <?php echo ($garden[$g]['ObtainSeedsToday']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Supplier diluar rekomendasi IMS')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPerolehBibit" <?php echo ($garden[$g]['ObtainSeedsToday']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('Membuat bibit sendiri')?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah secara kasat mata bibit anda bebas hama & penyakit ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKasatMtBibitTerlihatHama" <?php echo ($garden[$g]['SeedsFreeFromPests']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKasatMtBibitTerlihatHama" <?php echo ($garden[$g]['SeedsFreeFromPests']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda mengisi lembar catatan perawatan bibit secara rutin ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garIsiLembarCttRutin" <?php echo ($garden[$g]['SeedsFillRoutineMaintenance']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garIsiLembarCttRutin" <?php echo ($garden[$g]['SeedsFillRoutineMaintenance']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Setelah bergabung dengan program sertifikasi UTZ, apakah anda menyimpan catatan, sertifikat atau keterangan tertulis tentang asal bibit kakao anda ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garSimpanCttUtz" <?php echo ($garden[$g]['AfterCertSaveRecordOriginSeeds']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garSimpanCttUtz" <?php echo ($garden[$g]['AfterCertSaveRecordOriginSeeds']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 2 <?php echo lang('from')?> 8
    </footer>
</div>
<!-- halaman kedua (END) -->

<!-- halaman ketiga (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="5">
            H. <?php echo lang('Produksi Kakao/tahun (jual kering)')?>
        </td>
    </tr>
    <tr>
        <td colspan="3" class="leftValue"><?php echo lang('Perkiraan produksi setahun ke depan (tahun kalender/ Jan-Des)')?></td>
        <td colspan="2"><?php echo ($garden[$g]['ProductionNext']!='' ? $garden[$g]['ProductionNext'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> kg</td>
    </tr>
    <tr>
        <td colspan="3" class="leftValue"><?php echo lang('Estimasi produksi setahun yang lalu (tahun kalender/ Jan-Des))')?></td>
        <td colspan="2"><?php echo ($garden[$g]['']!='' ? $garden[$g][''] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> kg</td>
    </tr>
    <?php if($jenis_form!='form_hasil_simple'){ ?>
    <tr>
        <td class="leftValue" colspan="5"><strong><?php echo lang('Detail Produksi per tiga musim terakhir')?></strong></td>
    </tr>
    <tr>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Musim')?></td>
        <td width="20%" style="text-align:center;font-weight:bold;" class="topTabelValue">a. <?php echo lang('Lama Musim (Jumlah Bulan)')?></td>
        <td width="25%" style="text-align:center;font-weight:bold;" class="topTabelValue">b. <?php echo lang('Interval Panen')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue">c. <?php echo lang('kg/panen')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue">d. <?php echo lang('kg/thn')?></td>
    </tr>
    <tr>
        <td class="leftValue"><strong><?php echo lang('Panen trek')?></strong></td>
        <td><?php echo ($garden[$g]['PanenTrekMonths']!='' ? $garden[$g]['PanenTrekMonths'] : ''); ?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen1" <?php echo ($garden[$g]['PanenTrekPanenMonth']=='0' ? 'checked=""' : ''); ?>  >1. <?php echo lang('Tidak Panen')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen1" <?php echo ($garden[$g]['PanenTrekPanenMonth']=='4' ? 'checked=""' : ''); ?>  >2. <?php echo lang('1 kali/minggu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen1" <?php echo ($garden[$g]['PanenTrekPanenMonth']=='2' ? 'checked=""' : ''); ?>  >3. <?php echo lang('1 kali/2 minggu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen1" <?php echo ($garden[$g]['PanenTrekPanenMonth']=='1' ? 'checked=""' : ''); ?>  >4. <?php echo lang('1 kali/bulan')?></div>
        </td>
        <td><?php echo ($garden[$g]['PanenTrekKg']!='' ? $garden[$g]['PanenTrekKg'] : ''); ?></td>
        <td><?php echo ($garden[$g]['PanenTrekKgThn']!='' ? $garden[$g]['PanenTrekKgThn'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><strong><?php echo lang('Panen Biasa')?></strong></td>
        <td><?php echo ($garden[$g]['PanenBiasaMonths']!='' ? $garden[$g]['PanenBiasaMonths'] : ''); ?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen2" <?php echo ($garden[$g]['PanenBiasaPanenMonth']=='0' ? 'checked=""' : ''); ?> >1. <?php echo lang('Tidak Panen')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen2" <?php echo ($garden[$g]['PanenBiasaPanenMonth']=='4' ? 'checked=""' : ''); ?> >2. <?php echo lang('1 kali/minggu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen2" <?php echo ($garden[$g]['PanenBiasaPanenMonth']=='2' ? 'checked=""' : ''); ?> >3. <?php echo lang('1 kali/2 minggu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen2" <?php echo ($garden[$g]['PanenBiasaPanenMonth']=='1' ? 'checked=""' : ''); ?> >4. <?php echo lang('1 kali/bulan')?></div>
        </td>
        <td><?php echo ($garden[$g]['PanenBiasaKg']!='' ? $garden[$g]['PanenBiasaKg'] : ''); ?></td>
        <td><?php echo ($garden[$g]['PanenBiasaKgThn']!='' ? $garden[$g]['PanenBiasaKgThn'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><strong><?php echo lang('Panen Raya')?></strong></td>
        <td><?php echo ($garden[$g]['PanenRayaMonths']!='' ? $garden[$g]['PanenRayaMonths'] : ''); ?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen3" <?php echo ($garden[$g]['PanenRayaPanenMonth']=='0' ? 'checked=""' : ''); ?> >1. <?php echo lang('Tidak Panen')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen3" <?php echo ($garden[$g]['PanenRayaPanenMonth']=='4' ? 'checked=""' : ''); ?> >2. <?php echo lang('1 kali/minggu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen3" <?php echo ($garden[$g]['PanenRayaPanenMonth']=='2' ? 'checked=""' : ''); ?> >3. <?php echo lang('1 kali/2 minggu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garIntPanen3" <?php echo ($garden[$g]['PanenRayaPanenMonth']=='1' ? 'checked=""' : ''); ?> >4. <?php echo lang('1 kali/bulan')?></div>
        </td>
        <td><?php echo ($garden[$g]['PanenRayaKg']!='' ? $garden[$g]['PanenRayaKg'] : ''); ?></td>
        <td><?php echo ($garden[$g]['PanenRayaKgThn']!='' ? $garden[$g]['PanenRayaKgThn'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><strong><?php echo lang('Total Bulan')?></strong></td>
        <td></td>
        <td class="leftValue"><strong><?php echo lang('Total Produksi kakao')?></strong></td>
        <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('kg/kering')?></td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="3" class="leftValue"><?php echo lang('Penjualan dari hasil setahun yang lalu')?></td>
        <td colspan="2"><?php echo ($garden[$g]['SalesLastyear']!='' ? $garden[$g]['SalesLastyear'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?>kg</td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            I. <?php echo lang('Cara panen & Sanitasi Kakao')?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong><?php echo lang('Cara Panen Kakao')?></strong></td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox">
                <input type="checkbox" <?php echo ($garden[$g]['HarvestAwal']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Buah masak awal')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php echo ($garden[$g]['HarvestMasak']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Buah Masak')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php echo ($garden[$g]['HarvestHama']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Buah Terserang H/P')?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue" width="40%"><?php echo lang('Sanitasi – Apa yang anda lakukan pada kulit buah setelah pembelahan ?')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garLakukanPdKulitBuah" <?php echo ($garden[$g]['HowToCleanSkin']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ditumpuk di kebun kakao')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garLakukanPdKulitBuah" <?php echo ($garden[$g]['HowToCleanSkin']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Dikuburkan')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garLakukanPdKulitBuah" <?php echo ($garden[$g]['HowToCleanSkin']=='3' ? 'checked=""' : ''); ?> >3. <?php echo lang('Ditumpuk di luar kebun')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garLakukanPdKulitBuah" <?php echo ($garden[$g]['HowToCleanSkin']=='4' ? 'checked=""' : ''); ?> >4. <?php echo lang('Dibakar')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garLakukanPdKulitBuah" <?php echo ($garden[$g]['HowToCleanSkin']=='5' ? 'checked=""' : ''); ?> >5. <?php echo lang('Ditumpuk & Ditutup dengan plastik')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garLakukanPdKulitBuah" <?php echo ($garden[$g]['HowToCleanSkin']=='6' ? 'checked=""' : ''); ?> >6. <?php echo lang('Ditumbuk jadi pakan ternak')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garLakukanPdKulitBuah" <?php echo ($garden[$g]['HowToCleanSkin']=='7' ? 'checked=""' : ''); ?> >7. <?php echo lang('Diolah menjadi kompos')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garLakukanPdKulitBuah" <?php echo ($garden[$g]['HowToCleanSkin']=='8' ? 'checked=""' : ''); ?> >8. <?php echo lang('Di buang di sungai')?></div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Bagaimana anda menangani limbah organik dan anorganik ?')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garTanganiLimbah" <?php echo ($garden[$g]['HowToDealOrganicAnorganicWaste']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Limbah disimpan dan dibuang hanya pada area-area yang ditentukan')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garTanganiLimbah" <?php echo ($garden[$g]['HowToDealOrganicAnorganicWaste']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Limbah tidak berbahaya digunakan kembali atau didaur ulang manakala mungkin')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garTanganiLimbah" <?php echo ($garden[$g]['HowToDealOrganicAnorganicWaste']=='3' ? 'checked=""' : ''); ?> >3. <?php echo lang('Limbah oganik digunakan sebagai pupuk')?></div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="4">
            J. <?php echo lang('Data Pemangkasan')?>
        </td>
    </tr>
    <tr>
        <td width="60%" class="leftValue"><strong><?php echo lang('Pemangkasan tanaman kakao')?></strong></td>
        <td width="12%" class="leftValue"><strong><?php echo lang('Dilakukan')?></strong></td>
        <td class="leftValue"><strong><?php echo lang('Tinggi Pemangkasan (meter)')?></strong></td>
        <td class="leftValue"><strong><?php echo lang('Frekuensi Pemangkasan (kali/tahun)')?></strong></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pemangkasan tanaman kakao Untuk membentuk struktur yang optimal')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPangkasTnmanKakao1" <?php echo ($garden[$g]['PruningOptStructure']=='1' ? 'checked=""' : ''); ?>  />1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPangkasTnmanKakao1" <?php echo ($garden[$g]['PruningOptStructure']=='2' ? 'checked=""' : ''); ?>  />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo ($garden[$g]['HeightPruningOptStructure']!='' ? $garden[$g]['HeightPruningOptStructure'] : ''); ?></td>
        <td><?php echo ($garden[$g]['FrequentPruningOptStructure']!='' ? $garden[$g]['FrequentPruningOptStructure'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pemangkasan tanaman kakao Pemangkasan tunas atau bagian tanaman yang terinfeksi hama penyakit')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPangkasTnmanKakao2" <?php echo ($garden[$g]['PruningBudInfected']=='1' ? 'checked=""' : ''); ?>  />1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPangkasTnmanKakao2" <?php echo ($garden[$g]['PruningBudInfected']=='2' ? 'checked=""' : ''); ?>  />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo ($garden[$g]['HeightPruningBudInfected']!='' ? $garden[$g]['HeightPruningBudInfected'] : ''); ?></td>
        <td><?php echo ($garden[$g]['FrequentPruningBudInfected']!='' ? $garden[$g]['FrequentPruningBudInfected'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pemangkasan tanaman kakao Pemangkasan berat untuk tanaman yang tidak produktif')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPangkasTnmanKakao2" <?php echo ($garden[$g]['PruningNotProductive']=='1' ? 'checked=""' : ''); ?>  />1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPangkasTnmanKakao2" <?php echo ($garden[$g]['PruningNotProductive']=='2' ? 'checked=""' : ''); ?>  />2. <?php echo lang('Tidak')?></div>
        </td>
        <td><?php echo ($garden[$g]['HeightPruningNotProductive']!='' ? $garden[$g]['HeightPruningNotProductive'] : ''); ?></td>
        <td><?php echo ($garden[$g]['FrequentPruningNotProductive']!='' ? $garden[$g]['FrequentPruningNotProductive'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue" colspan="2">(C) <?php echo lang('Apakah alat-alat yang anda gunakan selalu disucihamakan ?')?></td>
        <td colspan="2">
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garAlat2DisuciHama" <?php echo ($garden[$g]['DisinfectedTools']=='1' ? 'checked=""' : ''); ?> > 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garAlat2DisuciHama" <?php echo ($garden[$g]['DisinfectedTools']=='2' ? 'checked=""' : ''); ?> > 2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue" colspan="2"><?php echo lang('Pemangkasan pohon pelindung')?></td>
        <td colspan="2">
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPangkatPhnLindung" <?php echo ($garden[$g]['PruningProtectPlants']=='1' ? 'checked=""' : ''); ?> > 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPangkatPhnLindung" <?php echo ($garden[$g]['PruningProtectPlants']=='2' ? 'checked=""' : ''); ?> > 2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 3 <?php echo lang('from')?> 8
    </footer>
</div>
<!-- halaman ketiga (END) -->

<!-- halaman keempat (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="3">
            K. <?php echo lang('Pemupukan pakai Kompos dan Organik serta kesuburan tanah')?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><?php echo lang('Apakah anda memakai pupuk kompos dan/atau organik?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garApkPakaiKomposOrganik" <?php echo ($garden[$g]['PakaiKompos']=='1' ? 'checked=""' : ''); ?> > 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garApkPakaiKomposOrganik" <?php echo ($garden[$g]['PakaiKompos']=='2' ? 'checked=""' : ''); ?> > 2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" style="text-align:center;" class="leftValue"><strong><?php echo lang('Jenis Pupuk')?></strong></td>
        <td style="text-align:center;" class="leftValue"><strong><?php echo lang('Frekuensi (kali/tahun)')?></strong></td>
        <td style="text-align:center;" class="leftValue"><strong><?php echo lang('Dosis')?></strong></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Kompos')?></td>
        <td><?php echo ($garden[$g]['FrequentFertilizationKompos']!='' ? $garden[$g]['FrequentFertilizationKompos'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoseFertilizerKompos']!='' ? $garden[$g]['DoseFertilizerKompos'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('kg/pohon/kali')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pupuk kandang')?></td>
        <td><?php echo ($garden[$g]['FrKomposKandang']!='' ? $garden[$g]['FrKomposKandang'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoKomposKandang']!='' ? $garden[$g]['DoKomposKandang'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('kg/pohon/kali')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pupuk cair')?></td>
        <td><?php echo ($garden[$g]['FrKomposCair']!='' ? $garden[$g]['FrKomposCair'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoKomposCair']!='' ? $garden[$g]['DoKomposCair'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('kg/pohon/kali')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pupuk granula/Padat')?></td>
        <td><?php echo ($garden[$g]['FrKomposGranula']!='' ? $garden[$g]['FrKomposGranula'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoKomposGranula']!='' ? $garden[$g]['DoKomposGranula'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('kg/pohon/kali')?></td>
    </tr>
    <tr>
        <td colspan="3" class="leftVa"><strong><?php echo lang('Pohon mana yang dipupuk kompos dan/atau organik')?></strong></td>
    </tr>
    <tr>
        <td colspan="3">
            <div class="flexBox">
                <input type="checkbox" <?php echo ($garden[$g]['PupukTBM']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Tanaman Belum Menghasilkan')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php echo ($garden[$g]['PupukTM']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Tanaman Menghasilkan')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php echo ($garden[$g]['PupukRehab']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Tanaman Rusak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue">(C) <?php echo lang('Apakah pupuk organik selalu tersedia dan mudah diperoleh ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garOrganikSelaluTersedia" <?php echo ($garden[$g]['AvailableOrganicFertilizer']=='1' ? 'checked=""' : ''); ?> > 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garOrganikSelaluTersedia" <?php echo ($garden[$g]['AvailableOrganicFertilizer']=='2' ? 'checked=""' : ''); ?> > 2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue">(C) <?php echo lang('Apakah anda secara rutin memantau kesuburan tanah secara visual ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garRutinMemantauSuburTanah" <?php echo ($garden[$g]['RoutineWatchSoilFertility']=='1' ? 'checked=""' : ''); ?> > 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garRutinMemantauSuburTanah" <?php echo ($garden[$g]['RoutineWatchSoilFertility']=='2' ? 'checked=""' : ''); ?> > 2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="3" class="leftValue"><strong><?php echo lang('Apa yang anda lakukan untuk memperbaiki kesuburan tanah?')?></strong></td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImprovePlantFixNitrogenInSoil']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Menanam tanaman yang dapat memperbaiki unsur nitrogen dalam tanah')?></div>
        </td>
        <td>
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImproveApplyPracticeAgroforestry']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Menerapkan praktek agroforestry')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImproveFertilizingWithAnorganic']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Melakukan pemupukan dengan pupuk buatan/anorganik')?></div>
        </td>
        <td>
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImproveMakeBiopori']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Membuat biopori')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImproveUseCoverCrop']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Menggunakan tanaman penutup tanah (cover crop)')?></div>
        </td>
        <td>
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImproveTerracing']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Membuat terasering')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImproveFertilizingWithOrganic']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Melakukan pemupukan dengan pupuk alami/organik')?></div>
        </td>
        <td>
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImprovePlantingShadeTrees']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Menanam tanaman pelindung')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <div class="flexBox"><input type="checkbox"  <?php echo ($garden[$g]['ImproveDoNothing']=='1' ? 'checked=""' : ''); ?> /> <?php echo lang('Tidak melakukan apa-apa')?></div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="7">
            L. <?php echo lang('Data Pemupukan Non Organik/Kimia')?>
        </td>
    </tr>
    <tr>
        <td colspan="4" class="leftValue"><?php echo lang('Apakah anda di kebun ini memakai pupuk non organik/kimia ?')?></td>
        <td colspan="3">
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garApkhKebunIniPakaiOrganik" <?php echo ($garden[$g]['TidakMemakaiKimia']=='1' ? 'checked=""' : ''); ?> > 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garApkhKebunIniPakaiOrganik" <?php echo ($garden[$g]['TidakMemakaiKimia']=='2' ? 'checked=""' : ''); ?> > 2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">&nbsp;</td>
        <td style="text-align:center;font-weight:bold;" class="leftValue">Urea</td>
        <td style="text-align:center;font-weight:bold;" class="leftValue">Za</td>
        <td style="text-align:center;font-weight:bold;" class="leftValue">TSP</td>
        <td style="text-align:center;font-weight:bold;" class="leftValue">NPK</td>
        <td style="text-align:center;font-weight:bold;" class="leftValue">KCL</td>
        <td style="text-align:center;font-weight:bold;" class="leftValue">Foliar</td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pemakaian Pupuk')?><br />[<?php echo lang('Kali/tahun')?>]</td>
        <td><?php echo ($garden[$g]['FrUrea']!='' ? $garden[$g]['FrUrea'] : ''); ?></td>
        <td><?php echo ($garden[$g]['FrZa']!='' ? $garden[$g]['FrZa'] : ''); ?></td>
        <td><?php echo ($garden[$g]['FrTsp']!='' ? $garden[$g]['FrTsp'] : ''); ?></td>
        <td><?php echo ($garden[$g]['FrNpk']!='' ? $garden[$g]['FrNpk'] : ''); ?></td>
        <td><?php echo ($garden[$g]['FrKcl']!='' ? $garden[$g]['FrKcl'] : ''); ?></td>
        <td><?php echo ($garden[$g]['FrFoliar']!='' ? $garden[$g]['FrFoliar'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Dosis Pupuk')?><br />[<?php echo lang('Gram/pohon/kali')?>]</td>
        <td><?php echo ($garden[$g]['DoUrea']!='' ? $garden[$g]['DoUrea'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoZa']!='' ? $garden[$g]['DoZa'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoTsp']!='' ? $garden[$g]['DoTsp'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoNpk']!='' ? $garden[$g]['DoNpk'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoKcl']!='' ? $garden[$g]['DoKcl'] : ''); ?></td>
        <td><?php echo ($garden[$g]['DoFoliar']!='' ? $garden[$g]['DoFoliar'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Total Pemupukan')?><br />[<?php echo lang('Gram/pohon/tahun')?>]</td>
        <td><?php echo ($garden[$g]['FrUrea']!='' ? $garden[$g]['FrUrea'] : 0) * ($garden[$g]['DoUrea']!='' ? $garden[$g]['DoUrea'] : 0); ?></td>
        <td><?php echo ($garden[$g]['FrZa']!='' ? $garden[$g]['FrZa'] : 0) * ($garden[$g]['DoZa']!='' ? $garden[$g]['DoZa'] : 0); ?></td>
        <td><?php echo ($garden[$g]['FrTsp']!='' ? $garden[$g]['FrTsp'] : 0) * ($garden[$g]['DoTsp']!='' ? $garden[$g]['DoTsp'] : 0); ?></td>
        <td><?php echo ($garden[$g]['FrNpk']!='' ? $garden[$g]['FrNpk'] : 0) * ($garden[$g]['DoNpk']!='' ? $garden[$g]['DoNpk'] : 0); ?></td>
        <td><?php echo ($garden[$g]['FrKcl']!='' ? $garden[$g]['FrKcl'] : 0) * ($garden[$g]['DoKcl']!='' ? $garden[$g]['DoKcl'] : 0); ?></td>
        <td><?php echo ($garden[$g]['FrFoliar']!='' ? $garden[$g]['FrFoliar'] : '') * ($garden[$g]['DoFoliar']!='' ? $garden[$g]['DoFoliar'] : 0); ?></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td colspan="2" class="leftValue"><strong><?php echo lang('Jika Tidak memakai pupuk non organik, kenapa ?')?></strong></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['KimiaDana']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Tidak ada dana')?></div></td>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['KimiaSupplier']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Tidak menemukan supplier')?></div></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['KimiaDilatih']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Belum dilatih')?></div></td>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['KimiaTidakSuka']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Tidak suka menggunakan pupuk kimia')?></div></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['KimiaTidakTersedia']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Pupuk tidak tersedia')?></div></td>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['KimiaLain']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Lain-lain')?></div></td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong><?php echo lang('Pohon mana yang dipupuk tidak organik/kimia')?></strong></td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox">
                <input type="checkbox" <?php echo ($garden[$g]['PupukTBM']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Tanaman Belum Menghasilkan')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php echo ($garden[$g]['PupukTM']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Tanaman Menghasilkan')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?php echo ($garden[$g]['PupukRehab']=='1' ? 'checked=""' : ''); ?> > <?php echo lang('Tanaman Rusak')?>
            </div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            M. <?php echo lang('Data Hama dan Penyakit Utama yang menyerang tanaman kakao')?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong><?php echo lang('Hama Utama Kakao: Apakah tanaman Kakao di kebun ini pernah diserang hama-hama berikut ini dalam setahun terakhir')?></strong></td>
    </tr>
    <tr>
        <td width="50%"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HamaBPK']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Penggerek Buah Kakao')?></div></td>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HamaHelopeltis']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Helopeltis')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HamaBatang']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Penggerek batang atau ranting')?></div></td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong><?php echo lang('Penyakit Utama Kakao: Apakah tanaman Kakao pernah diserang penyakit dalam setahun terakhir')?></strong></td></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['PenyakitKanker']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Kanker Batang')?></div></td>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['PenyakitBusuk']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Busuk Buah')?></div></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['PenyakitUpas']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Jamur Upas')?></div></td>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['PenyakitAkar']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Jamur Akar')?></div></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['PenyakitVSD']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('VSD')?></div></td>
        <td><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['PenyakitAntraknose']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Antraknose')?></div></td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah pemantauan hama dan penyakit rutin anda lakukan di kebun ini?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garApkhMantauKebunRutin" <?php echo ($garden[$g]['RoutineMonitorPestInGarden']=='1' ? 'checked=""' : ''); ?> > 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garApkhMantauKebunRutin" <?php echo ($garden[$g]['RoutineMonitorPestInGarden']=='2' ? 'checked=""' : ''); ?> > 2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    </table>

    <footer>
        <?php echo lang('Page')?> 4 <?php echo lang('from')?> 8
    </footer>
</div>
<!-- halaman keempat (END) -->

<!-- halaman kelima (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="6">
            N. <?php echo lang('Data Pemakaian Pestisida NB: 1 Liter = 1 Kg = 1.000 ml = 1.000 gram')?>
        </td>
    </tr>
    <tr>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Herbisida')?></strong></td>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Insektisida')?></strong></td>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Fungisida')?></strong></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiHerbisida" <?php echo ($garden[$g]['Herbisida']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiHerbisida" <?php echo ($garden[$g]['Herbisida']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?></div>
        </td>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiInsek" <?php echo ($garden[$g]['Insectisida']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiInsek" <?php echo ($garden[$g]['Insectisida']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?></div>
        </td>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiFungi" <?php echo ($garden[$g]['Fungisida']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiFungi" <?php echo ($garden[$g]['Fungisida']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?></div>
        </td>
    </tr>
    <tr>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Herbisida')?></strong></td>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Insektisida')?></strong></td>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Fungisida')?></strong></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida1']=='1' ? 'checked=""' : ''); ?> >Round Up</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida1']=='1' ? 'checked=""' : ''); ?> >Alika</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida1']=='1' ? 'checked=""' : ''); ?> >Nordox</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida3']=='1' ? 'checked=""' : ''); ?> >Pilar Up</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida3']=='1' ? 'checked=""' : ''); ?> >Capture</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida3']=='1' ? 'checked=""' : ''); ?> >Amistar-top</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida5']=='1' ? 'checked=""' : ''); ?> >Gramo-xone</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida5']=='1' ? 'checked=""' : ''); ?> >Regent</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida5']=='1' ? 'checked=""' : ''); ?> >Rhidomil</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida7']=='1' ? 'checked=""' : ''); ?> >Sapurata</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida7']=='1' ? 'checked=""' : ''); ?> >Penalty</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida7']=='1' ? 'checked=""' : ''); ?> >Antracol</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida9']=='1' ? 'checked=""' : ''); ?> >Para Special</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida9']=='1' ? 'checked=""' : ''); ?> >Chlormite</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida9']=='1' ? 'checked=""' : ''); ?> >Cozeb</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida11']=='1' ? 'checked=""' : ''); ?> >Paratop</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida11']=='1' ? 'checked=""' : ''); ?> >Organik</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida12']=='1' ? 'checked=""' : ''); ?> >Rabbat</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida13']=='1' ? 'checked=""' : ''); ?> >Primaxone</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida13']=='1' ? 'checked=""' : ''); ?> >Vigor</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida2']=='1' ? 'checked=""' : ''); ?> >Dithane</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida15']=='1' ? 'checked=""' : ''); ?> >Polado</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida15']=='1' ? 'checked=""' : ''); ?> >Deicer 505</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida4']=='1' ? 'checked=""' : ''); ?> >Scorpio</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida17']=='1' ? 'checked=""' : ''); ?> >Rumat</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida17']=='1' ? 'checked=""' : ''); ?> >Sidame-thrin</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida6']=='1' ? 'checked=""' : ''); ?> >Antila</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida19']=='1' ? 'checked=""' : ''); ?> >Kleenup</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida19']=='1' ? 'checked=""' : ''); ?> >Halona</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida9']=='1' ? 'checked=""' : ''); ?> >Polydor</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida21']=='1' ? 'checked=""' : ''); ?> >Tanistar</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida21']=='1' ? 'checked=""' : ''); ?> >Buldok</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida8']=='1' ? 'checked=""' : ''); ?> >Organik</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida23']=='1' ? 'checked=""' : ''); ?> >Polaris</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida23']=='1' ? 'checked=""' : ''); ?> >Sevin</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Fungisida13']=='1' ? 'checked=""' : ''); ?> >Benhasil</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida25']=='1' ? 'checked=""' : ''); ?> >Herbatop</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida2']=='1' ? 'checked=""' : ''); ?> >Matador</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida27']=='1' ? 'checked=""' : ''); ?> >Pointer</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida4']=='1' ? 'checked=""' : ''); ?> >Bento</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida29']=='1' ? 'checked=""' : ''); ?> >Tamaxon</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida6']=='1' ? 'checked=""' : ''); ?> >Drusban</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida2']=='1' ? 'checked=""' : ''); ?> >Basmilang</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['']=='1' ? 'checked=""' : ''); ?> >Nurelle</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida8']=='1' ? 'checked=""' : ''); ?> >Sun Up</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida10']=='1' ? 'checked=""' : ''); ?> >Decis</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida6']=='1' ? 'checked=""' : ''); ?> >Supremo</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida12']=='1' ? 'checked=""' : ''); ?> >Klensect</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida8']=='1' ? 'checked=""' : ''); ?> >Rambo</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida14']=='1' ? 'checked=""' : ''); ?> >Unicide</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida10']=='1' ? 'checked=""' : ''); ?> >Noxone</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida16']=='1' ? 'checked=""' : ''); ?> >Arrivo</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida12']=='1' ? 'checked=""' : ''); ?> >Bravo-xone</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida18']=='1' ? 'checked=""' : ''); ?> >Bestox</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida14']=='1' ? 'checked=""' : ''); ?> >Bimastar</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida20']=='1' ? 'checked=""' : ''); ?> >Dangke</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida16']=='1' ? 'checked=""' : ''); ?> >Primastar</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Insectisida22']=='1' ? 'checked=""' : ''); ?> >Laser</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida18']=='1' ? 'checked=""' : ''); ?> >Supretox</div></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida20']=='1' ? 'checked=""' : ''); ?> >Prima Up</div></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Herbisida')?></strong></td>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Insektisida')?></strong></td>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Fungisida')?></strong></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida22']=='1' ? 'checked=""' : ''); ?> >DMA</div></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['Herbisida24']=='1' ? 'checked=""' : ''); ?> >Konup</div></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="6"><?php echo lang('Merk Lainnya')?></td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
        <td colspan="2">&nbsp;</td>
        <td colspan="2">&nbsp;</td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            <?php echo lang('Pemakaian Pestisida')?>
        </td>
    </tr>
    <tr>
        <td width="55%" class="leftValue">(C) <?php echo lang('Apakah anda menggunakan pestisida kimia sesuai dengan dosis yang dianjurkan ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest1" <?php echo ($garden[$g]['UseChemicalPesticideDosage']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest1" <?php echo ($garden[$g]['UseChemicalPesticideDosage']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda menerapkan cara alternatif non-kimia untuk mengendalikan hama & penyakit ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest2" <?php echo ($garden[$g]['ApplyAltNonChemicalControlPests']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest2" <?php echo ($garden[$g]['ApplyAltNonChemicalControlPests']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda menggunakan pestisida alami untuk mengendalikan hama dan penyakit ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest3" <?php echo ($garden[$g]['UseOrganicControlPests']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest3" <?php echo ($garden[$g]['UseOrganicControlPests']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda selalu menggunakan pestisida kimia yang memiliki kadar racun terendah ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest4" <?php echo ($garden[$g]['UseChemicalLowestToxicity']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest4" <?php echo ($garden[$g]['UseChemicalLowestToxicity']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah pestisida kimia hanya anda gunakan sebagai pilihan terakhir ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest5" <?php echo ($garden[$g]['UseChemicalLastChoice']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest5" <?php echo ($garden[$g]['UseChemicalLastChoice']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda menerapkan strategi rotasi pada penggunaan pestisida kimia ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest6" <?php echo ($garden[$g]['ApplyRotationStrategy']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest6" <?php echo ($garden[$g]['ApplyRotationStrategy']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda mencatat penggunaan pestisida dan pupuk anorganik ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest7" <?php echo ($garden[$g]['NoticeUseInorganicFertilizer']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest7" <?php echo ($garden[$g]['NoticeUseInorganicFertilizer']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda sudah dilatih untuk menggunakan pestisida dengan tepat dan aman ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest8" <?php echo ($garden[$g]['TrainedUseProperly']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest8" <?php echo ($garden[$g]['TrainedUseProperly']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah ketika anda menyiapkan dan mencampur pestisida dan pupuk cair sesuai dengan petunjuk dosis dan keamanan pada label ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest9" <?php echo ($garden[$g]['MixPesticideLiquidFertilizer']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest9" <?php echo ($garden[$g]['MixPesticideLiquidFertilizer']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    </table>

    <footer>
        <?php echo lang('Page')?> 5 <?php echo lang('from')?> 8
    </footer>
</div>
<!-- halaman kelima (END) -->

<!-- halaman keenam (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            <?php echo lang('Pemakaian Pestisida')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah kelebihan campuran pestisida, pupuk cair atau limbah pencucian tangki dibuang dengan aman sesuai standar internal kelompok ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest10" <?php echo ($garden[$g]['ExcessPesticideDisposedSafely']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest10" <?php echo ($garden[$g]['ExcessPesticideDisposedSafely']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda memberi tanda dilarang masuk atau mematuhi waktu masuk kembali ke kebun setelah penyemprotan pestisida ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest11" <?php echo ($garden[$g]['GiveNoEntrySignAfterSpraying']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest11" <?php echo ($garden[$g]['GiveNoEntrySignAfterSpraying']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda mematuhi jeda waktu pra-panen yang direkomendasikan untuk seluruh pestisida  yang digunakan ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest12" <?php echo ($garden[$g]['AdherePreHarvestInterval']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest12" <?php echo ($garden[$g]['AdherePreHarvestInterval']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah seluruh perlengkapan yang digunakan untuk pemberian pupuk dan pestisida  dalam kondisi yang baik dan berfungsi sebagaimana mestinya ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest13" <?php echo ($garden[$g]['EquipmentGoodCondition']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPemakaiPest13" <?php echo ($garden[$g]['EquipmentGoodCondition']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong>(C) <?php echo lang('Pestisida dan pupuk anorganik disimpan dengan cara ?')?></strong></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['StoreAccordanceOnLabel']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Sesuai dengan petunjuk pada label')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['StoreOriginalPackaging']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Dalam wadah atau kemasan asli')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['StoreIndicationSuitablePlants']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Dengan indikasi jenis tanaman yang sesuai dengan penggunaannya')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['StoreAvoidPossibleSpill']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Terhindar dari kemungkinan tumpah')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['StoreSecuredPlace']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Diamankan ditempat yang tidak bisa diakses anak anak')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['StoreFarFromProducts']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Jauh dari produk yang dipanen, alat-alat, materi kemasan, dan produk-produk makanan')?></div></td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong>(C) <?php echo lang('Bagaimana keadaan fasilitas untuk penanganan, pelarutan dan penyimpanan pestisida dan pupuk anorganik ?')?></strong></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingCleanDry']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Bersih dan kering')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingEnoughVentilationLight']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Cukup ventilasi dan cahaya')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingStructurallySafe']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Secara struktur aman')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingLeakproofedFloor']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Lantai yang kedap suara dan anti rembes')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingFireproofMaterial']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Rak-rak yang bersifat anti-serap dan bermateri tahan api')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingCollectSpillage']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Terdapat sebuah sistem untuk menampung tumpahan')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingClearWarningSign']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Terdapat tanda peringatan yang jelas dan permanen ada di dekat pintu masuk')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingFirstAidInfo']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Terdapat peringatan keselamatan yang kelihatan, lambang-lambang peringatan, gejala keracunan, dan informasi pertolongan pertama untuk setiap produk yang disimpan')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingProcedureEmergency']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Terdapat tata cara keadaan darurat yang jelas')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingAreaCleanEye']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Terdapat area untuk membersihkan mata')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox" <?php echo ($garden[$g]['HandlingAccommodateLiquidStored']=='1' ? 'checked=""' : ''); ?> ><?php echo lang('Fasilitas  diberi pembatas dan mampu menampung 110% dari seluruh volume cair yang disimpan')?></div></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Menggunakan Pakaian Perlindungan Diri (PPD)')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPakaiPPD" <?php echo ($garden[$g]['APD']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPakaiPPD" <?php echo ($garden[$g]['APD']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Dimana anda menyimpan pestisida sebelum dan selama pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSimpanPestPrevPakai"  <?php echo ($garden[$g]['TempatSimpanPestisida']=='1' ? 'checked=""' : ''); ?> />1.<?php echo lang('Di dalam rumah')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSimpanPestPrevPakai"  <?php echo ($garden[$g]['TempatSimpanPestisida']=='2' ? 'checked=""' : ''); ?> />2.<?php echo lang('Tempat khusus pestisida')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSimpanPestPrevPakai"  <?php echo ($garden[$g]['TempatSimpanPestisida']=='3' ? 'checked=""' : ''); ?> />3.<?php echo lang('Di luar rumah (kawasan rumah)')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSimpanPestPrevPakai"  <?php echo ($garden[$g]['TempatSimpanPestisida']=='4' ? 'checked=""' : ''); ?> />4.<?php echo lang('Di rumah kebun')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garSimpanPestPrevPakai"  <?php echo ($garden[$g]['TempatSimpanPestisida']=='5' ? 'checked=""' : ''); ?> />5.<?php echo lang('Lain lain')?></div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Apa yang anda lakukan dengan kemasan pestisida setelah pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garKemasanPestAfterPake" <?php echo ($garden[$g]['BuangKemasanPestisida']=='1' ? 'checked=""' : ''); ?>  />1.<?php echo lang('Di buang sembarangan (di kebun atau sekitar rumah)')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garKemasanPestAfterPake" <?php echo ($garden[$g]['BuangKemasanPestisida']=='2' ? 'checked=""' : ''); ?>  />2.<?php echo lang('Digunakan untuk menyimpan sesuatu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garKemasanPestAfterPake" <?php echo ($garden[$g]['BuangKemasanPestisida']=='3' ? 'checked=""' : ''); ?>  />3.<?php echo lang('Dicuci dengan bersih dan dikubur')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garKemasanPestAfterPake" <?php echo ($garden[$g]['BuangKemasanPestisida']=='4' ? 'checked=""' : ''); ?>  />4.<?php echo lang('Dibakar')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garKemasanPestAfterPake" <?php echo ($garden[$g]['BuangKemasanPestisida']=='5' ? 'checked=""' : ''); ?>  />5.<?php echo lang('Daur Ulang')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garKemasanPestAfterPake" <?php echo ($garden[$g]['BuangKemasanPestisida']=='6' ? 'checked=""' : ''); ?>  />6.<?php echo lang('Lainnya')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue">(C) <?php echo lang('Apakah anda menggunakan pestisida dan pupuk anorganik')?></td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiPestPupukAnor"  <?php echo ($garden[$g]['UsePesticideInorganicFertilizer']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Dalam jarak 5 meter dari badan air musiman maupun permanen yang lebarnya 3 meter atau kurang (atau dalam jarak 2 meter jika kebun tersebut kurang dari 2 ha)')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiPestPupukAnor"  <?php echo ($garden[$g]['UsePesticideInorganicFertilizer']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Dalam jarak 10 meter dari badan air musiman ataupun permanen yang lebarnya lebih dari 3 meter')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiPestPupukAnor"  <?php echo ($garden[$g]['UsePesticideInorganicFertilizer']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('Dalam jarak 15 meter dari mata air')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garPakaiPestPupukAnor"  <?php echo ($garden[$g]['UsePesticideInorganicFertilizer']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Tidak sesuai poin 1,2 dan 3')?></div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader">
            I. <?php echo lang('Komentar tambahan tentang Kebun Kakao')?>
        </td>
    </tr>
    <tr height="50">
        <td><?php echo ($garden[$g]['CommentValid']!='' ? $garden[$g]['CommentValid'] : ''); ?></td>
    </tr>
    </table>

    <footer>
        <?php echo lang('Page')?> 6 <?php echo lang('from')?> 8
    </footer>
</div>
<!-- halaman keenam (END) -->

<!-- halaman ketujuh (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td colspan="2" class="tabelHeader" align="center">
            <?php echo lang('Penerapan Pasca Panen')?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="tabelHeader">
            A. <?php echo lang('Fermentasi')?>
        </td>
    </tr>
    <tr>
        <td width="45%" class="leftValue"><?php echo lang('Apakah anda melakukan Fermentasi biji kakao sebelum menjemur')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garFermenBijiKakaoPrevJemur" <?php echo ($garden[$g]['Fermentation']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garFermenBijiKakaoPrevJemur" <?php echo ($garden[$g]['Fermentation']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jika ya, berapa hari fermentasi biji dilakukan')?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('hari')?></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Jika tidak, mengapa?')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFermenBijiKakaoPrevJemurNo" <?php echo ($garden[$g]['NoFermentation']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Tidak punya cukup waktu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFermenBijiKakaoPrevJemurNo" <?php echo ($garden[$g]['NoFermentation']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak punya alat')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFermenBijiKakaoPrevJemurNo" <?php echo ($garden[$g]['NoFermentation']=='3' ? 'checked=""' : ''); ?> >3. <?php echo lang('Tidak tahu caranya')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFermenBijiKakaoPrevJemurNo" <?php echo ($garden[$g]['NoFermentation']=='4' ? 'checked=""' : ''); ?> >4. <?php echo lang('Tidak menguntungkan')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFermenBijiKakaoPrevJemurNo" <?php echo ($garden[$g]['NoFermentation']=='5' ? 'checked=""' : ''); ?> >5. <?php echo lang('Malas')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFermenBijiKakaoPrevJemurNo" <?php echo ($garden[$g]['NoFermentation']=='6' ? 'checked=""' : ''); ?> >6. <?php echo lang('Lain-lain')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="tabelHeader">
            B. <?php echo lang('Penjemuran')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Apakah anda menjemur biji kakao sebelum menjual')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garJemurBijiPrevJual" <?php echo ($garden[$g]['JemurYesNo']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garJemurBijiPrevJual" <?php echo ($garden[$g]['JemurYesNo']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><?php echo lang('Jika ya')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Berapa hari anda mengeringkan biji kakao')?></td>
        <td><?php echo ($garden[$g]['DryingDays']!='' ? $garden[$g]['DryingDays'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('hari')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pengeringan pada lantai penjemuran')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKeringPadaLantaiJemur" <?php echo ($garden[$g]['SunDryingSemen']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKeringPadaLantaiJemur" <?php echo ($garden[$g]['SunDryingSemen']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pengeringan di atas aspal')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKeringAtasAspal" <?php echo ($garden[$g]['SunDryingAspal']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKeringAtasAspal" <?php echo ($garden[$g]['SunDryingAspal']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pengeringan dengan alat')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKeringPakeAlat" <?php echo ($garden[$g]['DryingAlat']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKeringPakeAlat" <?php echo ($garden[$g]['DryingAlat']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pengeringan menggunakan alas (terpal, plastik, anyaman daun kelapa)')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKeringPakeAlas" <?php echo ($garden[$g]['SunDryingAlas']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKeringPakeAlas" <?php echo ($garden[$g]['SunDryingAlas']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda selalu memastikan jika biji kakao anda dikeringkan dengan cara yang higienis dan terhindar dari pencemaran asap, kotoran, benda asing dll yang dapat mempengaruhi mutu ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKeringDenganHigenis" <?php echo ($garden[$g]['BeanDryHygienic']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKeringDenganHigenis" <?php echo ($garden[$g]['BeanDryHygienic']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Jika tidak, mengapa anda tidak menjemur biji kakao?')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakJemurBiji" <?php echo ($garden[$g]['TidakJemur']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Lebih menguntungkan menjual biji basah')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakJemurBiji" <?php echo ($garden[$g]['TidakJemur']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Lebih mudah dikerjakan')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakJemurBiji" <?php echo ($garden[$g]['TidakJemur']=='3' ? 'checked=""' : ''); ?> >3. <?php echo lang('Lebih cepat memperoleh uang')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakJemurBiji" <?php echo ($garden[$g]['TidakJemur']=='4' ? 'checked=""' : ''); ?> >4. <?php echo lang('Sulit menjemur karena musim hujan')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakJemurBiji" <?php echo ($garden[$g]['TidakJemur']=='5' ? 'checked=""' : ''); ?> >5. <?php echo lang('Tidak cukup waktu and perlu bantuan tenaga kerja')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakJemurBiji" <?php echo ($garden[$g]['TidakJemur']=='6' ? 'checked=""' : ''); ?> >6. <?php echo lang('Lain-lain')?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah biji kakao anda keringkan hingga mencapai kadar kelembaban sesuai standar kelompok ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKeringCapaiKadarLembab" <?php echo ($garden[$g]['DryMoistureStandard']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKeringCapaiKadarLembab" <?php echo ($garden[$g]['DryMoistureStandard']=='1' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda menerapkan langkah-langkah untuk memastikan agar biji kakao tetap kering dan terhindar dari basah selama proses pengangkutan dan penyimpanan ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garLangkah2BijiKakao" <?php echo ($garden[$g]['ImplementBeanRemainDry']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garLangkah2BijiKakao" <?php echo ($garden[$g]['ImplementBeanRemainDry']=='1' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="tabelHeader">
            C. <?php echo lang('Sortasi dan Penjualan')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Apakah anda memisahkan biji berkualitas bagus dan berkualitas jelek/rendah sebelum menjualnya')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPisahBijiKualitas" <?php echo ($garden[$g]['Sortasi']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPisahBijiKualitas" <?php echo ($garden[$g]['Sortasi']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jika tidak, mengapa anda tidak melakukan pemisahan biji')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakPisahBiji" <?php echo ($garden[$g]['NoSortasi']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Tidak ada perbedaan harga')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakPisahBiji" <?php echo ($garden[$g]['NoSortasi']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Terlalu banyak menghabiskan waktu')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakPisahBiji" <?php echo ($garden[$g]['NoSortasi']=='3' ? 'checked=""' : ''); ?> >3. <?php echo lang('Tidak banyak biji berkualitas bagus')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMengapaTidakPisahBiji" <?php echo ($garden[$g]['NoSortasi']=='4' ? 'checked=""' : ''); ?> >4. <?php echo lang('Tidak tahu cara memisahkan biji')?></div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="leftValue"><?php echo lang('Biasanya menjual biji kakao kepada')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMenjualKepada" <?php echo ($garden[$g]['CocoaBuyers']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Pedagang pengumpul di kampung')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMenjualKepada" <?php echo ($garden[$g]['CocoaBuyers']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Pedagang pengumpul di kecamatan')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMenjualKepada" <?php echo ($garden[$g]['CocoaBuyers']=='3' ? 'checked=""' : ''); ?> >3. <?php echo lang('Pedagangan kabupaten/eksportir')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garMenjualKepada" <?php echo ($garden[$g]['CocoaBuyers']=='4' ? 'checked=""' : ''); ?> >4. <?php echo lang('Kelompok petani')?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Apakah anda mengantar kakao sendiri')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garAntarSendiri" <?php echo ($garden[$g]['AntarSendiri']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garAntarSendiri" <?php echo ($garden[$g]['AntarSendiri']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jika ya, berapa jarak dari rumah anda')?></td>
        <td>
            <?php echo ($garden[$g]['Distance']!='' ? $garden[$g]['Distance'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> meter
        </td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 7 <?php echo lang('from')?> 8
    </footer>
</div>
<!-- halaman ketujuh (END) -->

<!-- halaman ke 8 (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td colspan="2" class="tabelHeader">
            <?php echo lang('Modul Tambahan Terkait Sertifikasi')?>
        </td>
    </tr>
    <tr>
        <td width="70%" class="leftValue">(C) <?php echo lang('Apakah anda berpartisipasi dalam usaha untuk memastikan agar semua anak usia sekolah mendapatkan akses pendidikan ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPastikanAnakSekolah" <?php echo ($garden[$g]['ParticipateChildEducation']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPastikanAnakSekolah" <?php echo ($garden[$g]['ParticipateChildEducation']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda mengalami pemotongan upah kerja untuk tujuan disipliner ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPotonganGajiKerja" <?php echo ($garden[$g]['CutWageForDisciplinary']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPotonganGajiKerja" <?php echo ($garden[$g]['CutWageForDisciplinary']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda melakukan pemotongan upah pekerja anda dengan tujuan disipliner ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garBuatPotonganGajiKerja" <?php echo ($garden[$g]['DoCutWageForWorker']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garBuatPotonganGajiKerja" <?php echo ($garden[$g]['DoCutWageForWorker']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah upah anda dibayarkan sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garGajiSesuaiNonDiskrim" <?php echo ($garden[$g]['WagePaidByPerformance']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garGajiSesuaiNonDiskrim" <?php echo ($garden[$g]['WagePaidByPerformance']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda membayar upah pekerja anda sesuai kinerja atau kesepakatan tanpa adanya diskiriminasi ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garBuatGajiSesuaiNonDiskrim" <?php echo ($garden[$g]['PayingWorkerWageByPerformance']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garBuatGajiSesuaiNonDiskrim" <?php echo ($garden[$g]['PayingWorkerWageByPerformance']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Anda memahami bagaimana penanganan pertolongan pertama pada kecelakaan di kebun')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPahamTolongPertama" <?php echo ($garden[$g]['HandlingFirstAidInGarden']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPahamTolongPertama" <?php echo ($garden[$g]['HandlingFirstAidInGarden']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah kotak pertolongan pertama (P3K) tersedia di pusat lokasi produk, pengolahan dan pemeliharaan ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKotakTolongPertama" <?php echo ($garden[$g]['FirstAidKitLocation']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKotakTolongPertama" <?php echo ($garden[$g]['FirstAidKitLocation']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda sudah memastikan para pengurus kelompok, anggota kelompok, dan  anggota kelompok yang termasuk pekerja,  yang berusia di bawah 18 tahun, atau hamil dan sedang menyusui tidak boleh menangani pestisida ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garPestPencegahan" <?php echo ($garden[$g]['WorkerNotHandlePesticide']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garPestPencegahan" <?php echo ($garden[$g]['WorkerNotHandlePesticide']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah staf kelompok, anggota kelompok, dan anggota kelompok yang merupakan pekerja mempunyai akses terhadap air minum yang aman')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garKerjaAirMinumAman" <?php echo ($garden[$g]['WorkerAccessSafeDrinkingWater']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garKerjaAirMinumAman" <?php echo ($garden[$g]['WorkerAccessSafeDrinkingWater']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Di kebun ini terdapat sebuah zona penyangga berisi vegetasi asli setidaknya selebar 5 meter  dipelihara di sepanjang batas badan air musiman dan permanen untuk mengurangi erosi, membatasi pencemaran pestisida dan pupuk, dan melindungi habitat satwa liar. Di lahan yang luasnya kurang dari 2ha, terdapat zona penyangga  dengan lebar setidaknya 2 meter.')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garZonaPenyangga" <?php echo ($garden[$g]['BufferZoneGarden']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garZonaPenyangga" <?php echo ($garden[$g]['BufferZoneGarden']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah lahan anda dibuat dengan membuka hutan pada tahun 2008 atau sesudahnya ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garBukaLahan2008" <?php echo ($garden[$g]['LandOpeningForest']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garBukaLahan2008" <?php echo ($garden[$g]['LandOpeningForest']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Jika lahan anda dibuat dengan membuka hutan, apakah anda memiliki surat kepemilikan secara resmi dari pemerintah ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garBukaLahanSuratResmi" <?php echo ($garden[$g]['LandOpeningForestCertificate']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garBukaLahanSuratResmi" <?php echo ($garden[$g]['LandOpeningForestCertificate']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue">(C) <?php echo lang('Apakah anda melakukan identifikasi dan perlindungan terhadap spesies langka dan terancam punah di sekitar anda ?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="<?php echo $g ?>garLindungSatwaLangka" <?php echo ($garden[$g]['IdentifyProtectRareSpecies']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="<?php echo $g ?>garLindungSatwaLangka" <?php echo ($garden[$g]['IdentifyProtectRareSpecies']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td colspan="2" class="tabelHeader">
            <?php echo lang('Form Audit')?>
        </td>
    </tr>
    <tr>
        <td width="50%" class="leftValue"><?php echo lang('Candidate Selection')?></td>
        <td><?php echo ($garden[$g]['CandidateSelection']!='' ? $garden[$g]['CandidateSelection'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo ucwords(lang('ICS DATE'))?></td>
        <td><?php echo ($garden[$g]['ICSDate']!='' ? $garden[$g]['ICSDate'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Tanggal External Audit')?></td>
        <td><?php echo ($garden[$g]['ExternalDate']!='' ? $garden[$g]['ExternalDate'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Certification Start')?></td>
        <td><?php echo ($garden[$g]['CertificationStart']!='' ? $garden[$g]['CertificationStart'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Certification End')?></td>
        <td><?php echo ($garden[$g]['CertificationEnd']!='' ? $garden[$g]['CertificationEnd'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Tahun')?></td>
        <td><?php echo ($garden[$g]['Year']!='' ? $garden[$g]['Year'] : ''); ?></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Jenis Sertifikasi')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFAJenisSert1" <?php echo ($garden[$g]['Certification']=='1' ? 'checked=""' : ''); ?> >1. <?php echo 'UTZ'?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFAJenisSert2" <?php echo ($garden[$g]['Certification']=='2' ? 'checked=""' : ''); ?> >2. <?php echo 'Rainforest'?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFAJenisSert3" <?php echo ($garden[$g]['Certification']=='3' ? 'checked=""' : ''); ?> >3. <?php echo 'Fairtrade'?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFAJenisSert4" <?php echo ($garden[$g]['Certification']=='4' ? 'checked=""' : ''); ?> >4. <?php echo 'Organik'?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pemegang Sertifikasi')?></td>
        <td><?php echo ($garden[$g]['CertificationHolderName']!='' ? $garden[$g]['CertificationHolderName'] : ''); ?></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Penilaian dari Internal Inspektor')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFANilaiInspek1" <?php echo ($garden[$g]['StatusAuditCertification']=='1' ? 'checked=""' : ''); ?> >1. <?php echo lang('Lolos Audit')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFANilaiInspek2" <?php echo ($garden[$g]['StatusAuditCertification']=='2' ? 'checked=""' : ''); ?> >2. <?php echo lang('Disahkan dengan syarat')?></div>
            <div class="flexBox"><input type="radio" name="<?php echo $g ?>garFANilaiInspek3" <?php echo ($garden[$g]['StatusAuditCertification']=='3' ? 'checked=""' : ''); ?> >3. <?php echo lang('Tidak Lolos Audit')?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Tidak Lolos Audit, Tanggal perbaikan')?></td>
        <td><?php echo ($garden[$g]['DateRevisionAudit']!='0000-00-00' ? $garden[$g]['DateRevisionAudit'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Komentar Audit')?></td>
        <td><?php echo ($garden[$g]['CommentAudit']!='' ? $garden[$g]['CommentAudit'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Rekomendasi Audit')?></td>
        <td><?php echo ($garden[$g]['RecommendationAudit']!='' ? $garden[$g]['RecommendationAudit'] : ''); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Nama Inspektor')?></td>
        <td><?php echo ($garden[$g]['Inspector']!='' ? $garden[$g]['Inspector'] : ''); ?></td>
    </tr>

    </table>
    <br />


    <footer>
        <?php echo lang('Page')?> 8 <?php echo lang('from')?> 8
    </footer>
</div>
<!-- halaman ke 8 (END) -->

<?php } ?>

</body>
</html>