<?php
/**
 * @Author: nikolius
 * @Date:   2017-01-25 20:26:34
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
    <h2 class="judulHalaman"><?php echo lang('P1B. Data Kebun')?></h2>
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
        <td><?php echo ($garden[$g]['GardenHaUnCertified']!='' ? $garden[$g]['GardenHaUnCertified'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> m<?php echo lang('hektar')?></td>
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
                    <div class="flexBox"><input type="radio" name="garJalanKeKebun" value="" <?php echo ($garden[$g]['RoadCondition']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Jalan Aspal')?></div>
                    <div class="flexBox"><input type="radio" name="garJalanKeKebun" value="" <?php echo ($garden[$g]['RoadCondition']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('Jalan Tanah')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" name="garJalanKeKebun" value="" <?php echo ($garden[$g]['RoadCondition']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Jalan Pengerasan')?></div>
                    <div class="flexBox"><input type="radio" name="garJalanKeKebun" value="" <?php echo ($garden[$g]['RoadCondition']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Tidak Ada Jalan')?></div>
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
                    <div class="flexBox"><input type="radio" name="garStsMilikTanah" value="" <?php echo ($garden[$g]['OwnershipCocoa']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Pemilik Penggarap')?></div>
                    <div class="flexBox"><input type="radio" name="garStsMilikTanah" value="" <?php echo ($garden[$g]['OwnershipCocoa']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('Petani Penyewa')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" name="garStsMilikTanah" value="" <?php echo ($garden[$g]['OwnershipCocoa']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Petani Bagi Hasil')?></div>
                    <div class="flexBox"><input type="radio" name="garStsMilikTanah" value="" <?php echo ($garden[$g]['OwnershipCocoa']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Lain-lain')?></div>
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
                    <div class="flexBox"><input type="radio" name="garPemilikTanah" value="" <?php echo ($garden[$g]['LandOwner']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Saya Sendiri')?></div>
                    <div class="flexBox"><input type="radio" name="garPemilikTanah" value="" <?php echo ($garden[$g]['LandOwner']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('Orang Lain')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" name="garPemilikTanah" value="" <?php echo ($garden[$g]['LandOwner']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Anggota Keluarga')?></div>
                    <div class="flexBox"><input type="radio" name="garPemilikTanah" value="" <?php echo ($garden[$g]['LandOwner']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Tidak Tahu')?></div>
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
                    <div class="flexBox"><input type="radio" name="garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='1' ? 'checked=""' : ''); ?> />1. <?php echo lang('Tidak Ada')?></div>
                    <div class="flexBox"><input type="radio" name="garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='4' ? 'checked=""' : ''); ?> />4. <?php echo lang('Desa/Lurah')?></div>
                </td>
                <td>
                    <div class="flexBox"><input type="radio" name="garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='2' ? 'checked=""' : ''); ?> />2. <?php echo lang('Akte Notaris/BPN')?></div>
                    <div class="flexBox"><input type="radio" name="garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='5' ? 'checked=""' : ''); ?> />5. <?php echo lang('Tidak Tahu')?></div>
                </td>
                <td style="vertical-align:top;">
                    <div class="flexBox"><input type="radio" name="garSertPemilikTanah" value="" <?php echo ($garden[$g]['LandCertificate']=='3' ? 'checked=""' : ''); ?> />3. <?php echo lang('SKKT (Camat)')?></div>
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
            <?php echo ($garden[$g]['TahunTanamanCocoa']!='' ? $garden[$g]['TahunTanamanCocoa'] : '&nbsp;_&nbsp;_&nbsp;_&nbsp;_'); ?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah Tanaman Belum Menghasilkan')?></td>
        <td colspan="3">
            <?php echo ($garden[$g]['Not']!='' ? $garden[$g]['Not'] : ''); ?> <?php echo lang('pohon')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah Tanaman Menghasilkan')?></td>
        <td colspan="3">
            <?php echo ($garden[$g]['Not']!='' ? $garden[$g]['Not'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah Tanaman Rusak')?></td>
        <td colspan="3">
            <?php echo ($garden[$g]['Not']!='' ? $garden[$g]['Not'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
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
            <?php echo ($garden[$g]['ReplantedTrees']!='' ? $garden[$g]['ReplantedTrees'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
        <td class="leftValue">
            <?php echo lang('Tahun Tanam')?>
        </td>
        <td><?php echo ($garden[$g]['ReplantedTreesTahun']!='' ? $garden[$g]['ReplantedTreesTahun'] : '&nbsp;_&nbsp;_&nbsp;_&nbsp;_'); ?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jumlah penanaman ulang sisipan')?></td>
        <td>
            <?php echo ($garden[$g]['ReplantedTrees']!='' ? $garden[$g]['ReplantedTrees'] : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?> <?php echo lang('pohon')?>
        </td>
        <td class="leftValue">
            <?php echo lang('Tahun Tanam')?>
        </td>
        <td><?php echo ($garden[$g]['Not']!='' ? $garden[$g]['Not'] : '&nbsp;_&nbsp;_&nbsp;_&nbsp;_'); ?></td>
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

    <footer>
        <?php echo lang('Page')?> 1 <?php echo lang('from')?> 6
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
    </table>
    <br />

    <table width="100%">
    <tr>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jenis')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jenis')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jenis')?></td>
        <td style="text-align:center;font-weight:bold;" class="topTabelValue"><?php echo lang('Jumlah Pohon')?></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Cengkeh')?></td>
        <td></td>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Petai')?></td>
        <td></td>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Alpukat')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Aren')?></td>
        <td></td>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Lamtoro')?></td>
        <td></td>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Sukun')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Kemiri')?></td>
        <td></td>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Jengkol')?></td>
        <td></td>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Pepaya')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Manggis')?></td>
        <td></td>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Jambu')?></td>
        <td></td>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Cempedak')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><input type="checkbox" />&nbsp;<?php echo lang('Durian')?></td>
        <td></td>
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
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Untuk meningkatkan produktivitas tanaman kakao')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Untuk meningkatkan nilai tanah')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Untuk mendapatkan penghasilan tambahan')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Untuk menambah sumber kayu bakar')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Untuk melindungi tanah')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Untuk menambah sumber makanan ternak')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Untuk mengurangi serangan hama dan penyakit')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Saya tidak tahu')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Untuk mengurangi suhu panas di kebun')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Lainnya')?></div></td>
    </tr>
    <tr>
        <td colspan="6" class="leftValue">
            <strong><?php echo lang('Tanaman penutup tanah')?></strong>
        </td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Kacang-kacangan')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Umbi-umbian')?></div></td>
    </tr>
    <tr>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Nilam')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox" /><?php echo lang('Lain-lain')?></div></td>
    </tr>
    <tr>
        <td colspan="6"><div class="flexBox"><input type="checkbox" /><?php echo lang('Tidak ada cover crop')?></div></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="5">
            G. <?php echo lang('Produksi Kakao/tahun (jual kering)')?>
        </td>
    </tr>
    <tr>
        <td colspan="3" class="leftValue"><?php echo lang('Perkiraan produksi setahun ke depan (tahun kalender/ Jan-Des)')?></td>
        <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;kg</td>
    </tr>
    <tr>
        <td colspan="3" class="leftValue"><?php echo lang('Estimasi produksi setahun yang lalu (tahun kalender/ Jan-Des))')?></td>
        <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;kg</td>
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
        <td></td>
        <td>
            <div class="flexBox"><input type="radio" name="garIntPanen1">1. <?php echo lang('Tidak Panen')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen1">2. <?php echo lang('1 kali/minggu')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen1">3. <?php echo lang('1 kali/2 minggu')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen1">4. <?php echo lang('1 kali/bulan')?></div>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><strong><?php echo lang('Panen Biasa')?></strong></td>
        <td></td>
        <td>
            <div class="flexBox"><input type="radio" name="garIntPanen2">1. <?php echo lang('Tidak Panen')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen2">2. <?php echo lang('1 kali/minggu')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen2">3. <?php echo lang('1 kali/2 minggu')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen2">4. <?php echo lang('1 kali/bulan')?></div>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><strong><?php echo lang('Panen Raya')?></strong></td>
        <td></td>
        <td>
            <div class="flexBox"><input type="radio" name="garIntPanen3">1. <?php echo lang('Tidak Panen')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen3">2. <?php echo lang('1 kali/minggu')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen3">3. <?php echo lang('1 kali/2 minggu')?></div>
            <div class="flexBox"><input type="radio" name="garIntPanen3">4. <?php echo lang('1 kali/bulan')?></div>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><strong><?php echo lang('Total Bulan')?></strong></td>
        <td></td>
        <td class="leftValue"><strong><?php echo lang('Total Produksi kakao')?></strong></td>
        <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('kg/kering')?></td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="2" class="leftValue"><?php echo lang('Penjualan dari hasil setahun yang lalu')?></td>
        <td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;kg</td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 2 <?php echo lang('from')?> 6
    </footer>
</div>
<!-- halaman kedua (END) -->

<!-- halaman ketiga (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            H. <?php echo lang('Cara panen & Sanitasi Kakao')?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong><?php echo lang('Cara Panen Kakao')?></strong></td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox">
                <input type="checkbox"> <?php echo lang('Buah masak awal')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox"> <?php echo lang('Buah Masak')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox"> <?php echo lang('Buah Terserang H/P')?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue" width="40%"><?php echo lang('Sanitasi – Apa yang anda lakukan pada kulit buah setelah pembelahan ?')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garLakukanPdKulitBuah">1. <?php echo lang('Ditumpuk di kebun kakao')?></div>
            <div class="flexBox"><input type="radio" name="garLakukanPdKulitBuah">2. <?php echo lang('Dikuburkan')?></div>
            <div class="flexBox"><input type="radio" name="garLakukanPdKulitBuah">3. <?php echo lang('Ditumpuk di luar kebun')?></div>
            <div class="flexBox"><input type="radio" name="garLakukanPdKulitBuah">4. <?php echo lang('Dibakar')?></div>
            <div class="flexBox"><input type="radio" name="garLakukanPdKulitBuah">5. <?php echo lang('Ditumpuk & Ditutup dengan plastik')?></div>
            <div class="flexBox"><input type="radio" name="garLakukanPdKulitBuah">6. <?php echo lang('Ditumbuk jadi pakan ternak')?></div>
            <div class="flexBox"><input type="radio" name="garLakukanPdKulitBuah">7. <?php echo lang('Diolah menjadi kompos')?></div>
            <div class="flexBox"><input type="radio" name="garLakukanPdKulitBuah">8. <?php echo lang('Di buang di sungai')?></div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Bagaimana anda menangani limbah organik dan anorganik ?')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garTanganiLimbah">1. <?php echo lang('Limbah disimpan dan dibuang hanya pada area-area yang ditentukan')?></div>
            <div class="flexBox"><input type="radio" name="garTanganiLimbah">2. <?php echo lang('Limbah tidak berbahaya digunakan kembali atau didaur ulang manakala mungkin')?></div>
            <div class="flexBox"><input type="radio" name="garTanganiLimbah">3. <?php echo lang('Limbah oganik digunakan sebagai pupuk')?></div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="4">
            I. <?php echo lang('Data Pemangkasan')?>
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
            <div class="flexBox"><input type="radio" name="garPangkasTnmanKakao1" />1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="garPangkasTnmanKakao1" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pemangkasan tanaman kakao Pemangkasan tunas atau bagian tanaman yang terinfeksi hama penyakit')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garPangkasTnmanKakao2" />1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="garPangkasTnmanKakao2" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pemangkasan tanaman kakao Pemangkasan berat untuk tanaman yang tidak produktif')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garPangkasTnmanKakao2" />1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="garPangkasTnmanKakao2" />2. <?php echo lang('Tidak')?></div>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue" colspan="2"><?php echo lang('Pemangkasan pohon pelindung')?></td>
        <td colspan="2">
            <div class="flexBox">
                <input type="radio" name="garPangkatPhnLindung"> 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garPangkatPhnLindung"> 2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="3">
            J. <?php echo lang('Pemupukan pakai Kompos dan Organik serta kesuburan tanah')?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><?php echo lang('Apakah anda memakai pupuk kompos dan/atau organik?')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="garApkPakaiKomposOrganik"> 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garApkPakaiKomposOrganik"> 2. <?php echo lang('Tidak')?>
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
        <td></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('kg/pohon/kali')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pupuk kandang')?></td>
        <td></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('kg/pohon/kali')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pupuk cair')?></td>
        <td></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('kg/pohon/kali')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pupuk granula/Padat')?></td>
        <td></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('kg/pohon/kali')?></td>
    </tr>
    <tr>
        <td colspan="3" class="leftVa"><strong><?php echo lang('Pohon mana yang dipupuk kompos dan/atau organik')?></strong></td>
    </tr>
    <tr>
        <td colspan="3">
            <div class="flexBox">
                <input type="checkbox"> <?php echo lang('Tanaman Belum Menghasilkan')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox"> <?php echo lang('Tanaman Menghasilkan')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox"> <?php echo lang('Tanaman Rusak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="3" class="leftValue"><strong><?php echo lang('Apa yang anda lakukan untuk memperbaiki kesuburan tanah?')?></strong></td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Menanam tanaman yang dapat memperbaiki unsur nitrogen dalam tanah')?></div>
        </td>
        <td>
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Menerapkan praktek agroforestry')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Melakukan pemupukan dengan pupuk buatan/anorganik')?></div>
        </td>
        <td>
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Membuat biopori')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Menggunakan tanaman penutup tanah (cover crop)')?></div>
        </td>
        <td>
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Membuat terasering')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Melakukan pemupukan dengan pupuk alami/organik')?></div>
        </td>
        <td>
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Menanam tanaman pelindung')?></div>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <div class="flexBox"><input type="checkbox" /> <?php echo lang('Tidak melakukan apa-apa')?></div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="7">
            K. <?php echo lang('Data Pemupukan Non Organik/Kimia')?>
        </td>
    </tr>
    <tr>
        <td colspan="4" class="leftValue"><?php echo lang('Apakah anda di kebun ini memakai pupuk non organik/kimia ?')?></td>
        <td colspan="3">
            <div class="flexBox">
                <input type="radio" name="garApkhKebunIniPakaiOrganik"> 1. <?php echo lang('Ya')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garApkhKebunIniPakaiOrganik"> 2. <?php echo lang('Tidak')?>
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
        <td class="leftValue"><?php echo lang('Pemakaian Pestisida')?><br />[<?php echo lang('Kali/tahun')?>]</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Dosis Pesitisida')?><br />[<?php echo lang('Gram/pohon/kali')?>]</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Total Pemupukan')?><br />[<?php echo lang('Gram/pohon/tahun')?>]</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="7" class="leftValue"><strong><?php echo lang('Jika Tidak memakai pupuk non organik, kenapa ?')?></strong></td>
    </tr>
    <tr>
        <td colspan="4"><div class="flexBox"><input type="checkbox"><?php echo lang('Tidak ada dana')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox"><?php echo lang('Tidak menemukan supplier')?></div></td>
    </tr>
    <tr>
        <td colspan="4"><div class="flexBox"><input type="checkbox"><?php echo lang('Belum dilatih')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox"><?php echo lang('Tidak suka menggunakan pupuk kimia')?></div></td>
    </tr>
    <tr>
        <td colspan="4"><div class="flexBox"><input type="checkbox"><?php echo lang('Pupuk tidak tersedia')?></div></td>
        <td colspan="3"><div class="flexBox"><input type="checkbox"><?php echo lang('Lain-lain')?></div></td>
    </tr>
    <tr>
        <td colspan="7" class="leftValue"><strong><?php echo lang('Pohon mana yang dipupuk tidak organik/kimia')?></strong></td>
    </tr>
    <tr>
        <td colspan="7">
            <div class="flexBox">
                <input type="checkbox"> <?php echo lang('Tanaman Belum Menghasilkan')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox"> <?php echo lang('Tanaman Menghasilkan')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox"> <?php echo lang('Tanaman Rusak')?>
            </div>
        </td>
    </tr>
    </table>

    <footer>
        <?php echo lang('Page')?> 3 <?php echo lang('from')?> 6
    </footer>
</div>
<!-- halaman ketiga (END) -->

<!-- halaman keempat (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            L. <?php echo lang('Data Hama dan Penyakit Utama yang menyerang tanaman kakao')?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong><?php echo lang('Hama Utama Kakao: Apakah tanaman Kakao di kebun ini pernah diserang hama-hama berikut ini dalam setahun terakhir')?></strong></td>
    </tr>
    <tr>
        <td width="50%"><div class="flexBox"><input type="checkbox"><?php echo lang('Penggerek Buah Kakao')?></div></td>
        <td><div class="flexBox"><input type="checkbox"><?php echo lang('Helopeltis')?></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox"><?php echo lang('Penggerek batang atau ranting')?></div></td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><strong><?php echo lang('Penyakit Utama Kakao: Apakah tanaman Kakao pernah diserang penyakit dalam setahun terakhir')?></strong></td></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox"><?php echo lang('Kanker Batang')?></div></td>
        <td><div class="flexBox"><input type="checkbox"><?php echo lang('Busuk Buah')?></div></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox"><?php echo lang('Jamur Upas')?></div></td>
        <td><div class="flexBox"><input type="checkbox"><?php echo lang('Jamur Akar')?></div></td>
    </tr>
    <tr>
        <td><div class="flexBox"><input type="checkbox"><?php echo lang('VSD')?></div></td>
        <td><div class="flexBox"><input type="checkbox"><?php echo lang('Antraknose')?></div></td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="6">
            M. <?php echo lang('Data Pemakaian Pestisida NB: 1 Liter = 1 Kg = 1.000 ml = 1.000 gram')?>
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
            <div class="flexBox"><input type="radio" name="garPakaiHerbisida">1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="garPakaiHerbisida">2. <?php echo lang('Tidak')?></div>
        </td>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garPakaiInsek">1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="garPakaiInsek">2. <?php echo lang('Tidak')?></div>
        </td>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garPakaiFungi">1. <?php echo lang('Ya')?></div>
            <div class="flexBox"><input type="radio" name="garPakaiFungi">2. <?php echo lang('Tidak')?></div>
        </td>
    </tr>
    <tr>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Herbisida')?></strong></td>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Insektisida')?></strong></td>
        <td style="text-align:center;" colspan="2" class="leftValue"><strong><?php echo lang('Merk Fungisida')?></strong></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Round Up</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Alika</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Nordox</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Pilar Up</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Capture</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Amistar-top</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Gramo-xone</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Regent</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Rhidomil</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Sapurata</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Penalty</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Antracol</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Para Special</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Chlormite</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Cozeb</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Paratop</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Organik</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Rabbat</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Primaxone</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Vigor</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Dithane</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Polado</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Deicer 505</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Scorpio</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Rumat</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Sidame-thrin</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Antila</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Kleenup</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Halona</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Polydor</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Tanistar</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Buldok</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Organik</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Polaris</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Sevin</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Benhasil</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Herbatop</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Matador</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Pointer</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Bento</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Tamaxon</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Drusban</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Basmilang</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Nurelle</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Sun Up</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Decis</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Supremo</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Klensect</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Rambo</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Unicide</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Noxone</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Arrivo</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Bravo-xone</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Bestox</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Bimastar</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Dangke</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Primastar</div></td>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Laser</div></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Supretox</div></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Prima Up</div></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">DMA</div></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"><div class="flexBox"><input type="checkbox">Konup</div></td>
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

    <footer>
        <?php echo lang('Page')?> 4 <?php echo lang('from')?> 6
    </footer>
</div>
<!-- halaman keempat (END) -->

<!-- halaman kelima (BEGIN) -->
<div class="page">

    <table width="100%">
    <tr>
        <td class="tabelHeader" colspan="2">
            <?php echo lang('Pemakaian Pestisida')?>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Menggunakan Pakaian Perlindungan Diri (PPD)')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="garPakaiPPD">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garPakaiPPD">2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Dimana anda menyimpan pestisida sebelum dan selama pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garSimpanPestPrevPakai" />1.<?php echo lang('Di dalam rumah')?></div>
            <div class="flexBox"><input type="radio" name="garSimpanPestPrevPakai" />2.<?php echo lang('Tempat khusus pestisida')?></div>
            <div class="flexBox"><input type="radio" name="garSimpanPestPrevPakai" />3.<?php echo lang('Di luar rumah (kawasan rumah)')?></div>
            <div class="flexBox"><input type="radio" name="garSimpanPestPrevPakai" />4.<?php echo lang('Di rumah kebun')?></div>
            <div class="flexBox"><input type="radio" name="garSimpanPestPrevPakai" />5.<?php echo lang('Lain lain')?></div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Apa yang anda lakukan dengan kemasan pestisida setelah pemakaian')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garKemasanPestAfterPake" />1.<?php echo lang('Di buang sembarangan (di kebun atau sekitar rumah)')?></div>
            <div class="flexBox"><input type="radio" name="garKemasanPestAfterPake" />2.<?php echo lang('Digunakan untuk menyimpan sesuatu')?></div>
            <div class="flexBox"><input type="radio" name="garKemasanPestAfterPake" />3.<?php echo lang('Dicuci dengan bersih dan dikubur')?></div>
            <div class="flexBox"><input type="radio" name="garKemasanPestAfterPake" />4.<?php echo lang('Dibakar')?></div>
            <div class="flexBox"><input type="radio" name="garKemasanPestAfterPake" />5.<?php echo lang('Daur Ulang')?></div>
            <div class="flexBox"><input type="radio" name="garKemasanPestAfterPake" />6.<?php echo lang('Lainnya')?></div>
        </td>
    </tr>
    </table>
    <br />

    <table width="100%">
    <tr>
        <td class="tabelHeader">
            N. <?php echo lang('Komentar tambahan tentang Kebun Kakao')?>
        </td>
    </tr>
    <tr height="50">
        <td>&nbsp;</td>
    </tr>
    </table>
    <br /><br /><br />

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
                <input type="radio" name="garFermenBijiKakaoPrevJemur">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garFermenBijiKakaoPrevJemur">2. <?php echo lang('Tidak')?>
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
            <div class="flexBox"><input type="radio" name="garFermenBijiKakaoPrevJemurNo">1. <?php echo lang('Tidak punya cukup waktu')?></div>
            <div class="flexBox"><input type="radio" name="garFermenBijiKakaoPrevJemurNo">2. <?php echo lang('Tidak punya alat')?></div>
            <div class="flexBox"><input type="radio" name="garFermenBijiKakaoPrevJemurNo">3. <?php echo lang('Tidak tahu caranya')?></div>
            <div class="flexBox"><input type="radio" name="garFermenBijiKakaoPrevJemurNo">4. <?php echo lang('Tidak menguntungkan')?></div>
            <div class="flexBox"><input type="radio" name="garFermenBijiKakaoPrevJemurNo">5. <?php echo lang('Malas')?></div>
            <div class="flexBox"><input type="radio" name="garFermenBijiKakaoPrevJemurNo">6. <?php echo lang('Lain-lain')?></div>
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
                <input type="radio" name="garJemurBijiPrevJual">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garJemurBijiPrevJual">2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="leftValue"><?php echo lang('Jika ya')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Berapa hari anda mengeringkan biji kakao')?></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo lang('hari')?></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pengeringan pada lantai penjemuran')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="garKeringPadaLantaiJemur">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garKeringPadaLantaiJemur">2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pengeringan di atas aspal')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="garKeringAtasAspal">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garKeringAtasAspal">2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pengeringan dengan alat')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="garKeringPakeAlat">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garKeringPakeAlat">2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pengeringan menggunakan alas (terpal, plastik, anyaman daun kelapa)')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="garKeringPakeAlas">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garKeringPakeAlas">2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Jika tidak, mengapa anda tidak menjemur biji kakao?')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garMengapaTidakJemurBiji">1. <?php echo lang('Lebih menguntungkan menjual biji basah')?></div>
            <div class="flexBox"><input type="radio" name="garMengapaTidakJemurBiji">2. <?php echo lang('Lebih mudah dikerjakan')?></div>
            <div class="flexBox"><input type="radio" name="garMengapaTidakJemurBiji">3. <?php echo lang('Lebih cepat memperoleh uang')?></div>
            <div class="flexBox"><input type="radio" name="garMengapaTidakJemurBiji">4. <?php echo lang('Sulit menjemur karena musim hujan')?></div>
            <div class="flexBox"><input type="radio" name="garMengapaTidakJemurBiji">5. <?php echo lang('Tidak cukup waktu and perlu bantuan tenaga kerja')?></div>
            <div class="flexBox"><input type="radio" name="garMengapaTidakJemurBiji">6. <?php echo lang('Lain-lain')?></div>
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
                <input type="radio" name="garPisahBijiKualitas">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garPisahBijiKualitas">2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jika tidak, mengapa anda tidak melakukan pemisahan biji')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garMengapaTidakPisahBiji">1. <?php echo lang('Tidak ada perbedaan harga')?></div>
            <div class="flexBox"><input type="radio" name="garMengapaTidakPisahBiji">2. <?php echo lang('Terlalu banyak menghabiskan waktu')?></div>
            <div class="flexBox"><input type="radio" name="garMengapaTidakPisahBiji">3. <?php echo lang('Tidak banyak biji berkualitas bagus')?></div>
            <div class="flexBox"><input type="radio" name="garMengapaTidakPisahBiji">4. <?php echo lang('Tidak tahu cara memisahkan biji')?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Biasanya menjual biji kakao kepada')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garMenjualKepada">1. <?php echo lang('Pedagang pengumpul di kampung')?></div>
            <div class="flexBox"><input type="radio" name="garMenjualKepada">2. <?php echo lang('Pedagang pengumpul di kecamatan')?></div>
            <div class="flexBox"><input type="radio" name="garMenjualKepada">3. <?php echo lang('Pedagangan kabupaten/eksportir')?></div>
            <div class="flexBox"><input type="radio" name="garMenjualKepada">4. <?php echo lang('Kelompok petani')?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Apakah anda mengantar kakao sendiri')?></td>
        <td>
            <div class="flexBox">
                <input type="radio" name="garAntarSendiri">1. <?php echo lang('Ya')?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="garAntarSendiri">2. <?php echo lang('Tidak')?>
            </div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Jika ya, berapa jarak dari rumah anda')?></td>
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;meter
        </td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 5 <?php echo lang('from')?> 6
    </footer>
</div>
<!-- halaman kelima (END) -->

<!-- halaman keenam (END) -->
<div class="page">

    <table width="100%">
    <tr>
        <td colspan="2" class="tabelHeader">
            <?php echo lang('Form Audit')?>
        </td>
    </tr>
    <tr>
        <td width="50%" class="leftValue"><?php echo lang('Candidate Selection')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo ucwords(lang('ICS DATE'))?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Tanggal External Audit')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Certification Start')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Certification End')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Tahun')?></td>
        <td></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Jenis Sertifikasi')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garFAJenisSert1">1. <?php echo 'UTZ'?></div>
            <div class="flexBox"><input type="radio" name="garFAJenisSert2">2. <?php echo 'Rainforest'?></div>
            <div class="flexBox"><input type="radio" name="garFAJenisSert3">3. <?php echo 'Fairtrade'?></div>
            <div class="flexBox"><input type="radio" name="garFAJenisSert4">4. <?php echo 'Organik'?></div>
        </td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Pemegang Sertifikasi')?></td>
        <td></td>
    </tr>
    <tr>
        <td style="vertical-align:top;" class="leftValue"><?php echo lang('Penilaian dari Internal Inspektor')?></td>
        <td>
            <div class="flexBox"><input type="radio" name="garFANilaiInspek1">1. <?php echo lang('Lolos Audit')?></div>
            <div class="flexBox"><input type="radio" name="garFANilaiInspek2">2. <?php echo lang('Disahkan dengan syarat')?></div>
            <div class="flexBox"><input type="radio" name="garFANilaiInspek3">3. <?php echo lang('Tidak Lolos Audit')?></div>
        </td>
    </tr>
    <tr>
        <td width="50%" class="leftValue"><?php echo lang('Tidak Lolos Audit, Tanggal perbaikan')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Komentar Audit')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Rekomendasi Audit')?></td>
        <td></td>
    </tr>
    <tr>
        <td class="leftValue"><?php echo lang('Nama Inspektor')?></td>
        <td></td>
    </tr>
    </table>
    <br />

    <footer>
        <?php echo lang('Page')?> 6 <?php echo lang('from')?> 6
    </footer>
</div>
<!-- halaman keenam (END) -->
<?php } ?>

</body>
</html>