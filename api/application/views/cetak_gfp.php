<?php 
if ($SurveyNr == 0) {
    $title = lang('GFP Baseline');
} elseif ($SurveyNr > 0) {
    $title = lang('GFP Postline');
}
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8"/>
    <title><?php echo $title ?></title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/nutrition/nutrition.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/nutrition/nutrition-media.css" media="print"/>
    <script src="<?php echo base_url(); ?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
</head>
<body>

<div class="page"> <!-- Halaman 1 Start -->


    <!-- LOGO ATAS (BEGIN) -->
    <?php echo $this->load->view('cetak_logo', compact('logos'), TRUE); ?>
    <!-- LOGO ATAS (END) -->


    <?php echo $this->load->view('cetak_title', compact('survey', 'farmer', 'SurveyNr', 'title'), TRUE); ?>
    
    <?php echo $this->load->view('cetak_farmer_detail', compact('farmer'), TRUE); ?>    

    <table class="noBorder" width="100%" style="margin-bottom:6px;">
    <tr>
        <td width="6%">
            <img src="<?php echo base_url()?>assets/css/nutrition/icon-nutrition-garden.png" width="35" />
        </td>
        <td width="94%" style="border-bottom: 1px dashed #23BAB1;">
            <h2 class="judulTabel"><?php echo strtoupper(lang('Survey'))?></h2>
        </td>
    </tr>
    </table>

    
    <table width="100%" cellspacing="2" class="body">
        <tr>
            <td colspan="3" width="75%"><?php echo lang('Apakah Anda/anggota keluarga memiliki satu atau lebih rekening ') ?>

            </td>
            <td class="line" width="25%">
                <input disabled type="radio" <?php echo $survey['Account'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?php echo $survey['Account'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
        </tr>
        <tr>
            <td colspan="2"><?php echo lang('Jenis Rekening') ?>:</td>
            <td colspan="2" class="line" width="50%">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="checkbox" <?php echo $survey['AccountTypeTabungan'] == '1' ? 'checked' : '' ?>><?php echo lang('Rekening Tabungan') ?>
                            <br>
                            <input disabled type="checkbox" <?php echo $survey['AccountTypeDeposito'] == '1' ? 'checked' : '' ?>><?php echo lang('Deposito Berjangka') ?>
                        </td>
                        <td>
                            <input disabled type="checkbox" <?php echo $survey['AccountTypeKoran'] == '1' ? 'checked' : '' ?>><?php echo lang('Rekening Giro') ?>
                            <br>
                            <input disabled type="checkbox" <?php echo $survey['AccountTypeLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Lainnya') ?>
                        </td>
                        </td>
                </table>
            </td>
        </tr>
        <tr>
            <td width="25%"><?php echo lang('Pemilik Rekening') ?></td>
            <td colspan="3" class="line">
                <input disabled type="radio" <?php echo $survey['AccountHolderFarmer'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya') ?>
                <input disabled type="radio" <?php echo $survey['AccountHolderFarmer'] == '2' ? 'checked' : '' ?>><?php echo lang('Bukan (Anak, Suami/Istri)') ?>
                <input disabled type="checkbox" <?= $checked ?>><?php echo lang('Saat ini saya tidak mengetahui datanya') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php echo lang('Jika bukan tuliskan nama') ?>:</td>
            <td colspan="2" class="line"><?php echo $survey['AccountHolderName'] ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php echo lang('Bank') ?>:</td>
            <td colspan="2" class="line"><?php echo $survey['AccountBankName'] ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php echo lang('Cabang') ?>:</td>
            <td colspan="2" class="line"><?php echo $survey['AccountBankBranch'] ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php echo lang('Nomor Rekening') ?>:</td>
            <td colspan="2" class="line"><?php echo $survey['AccountNumber'] ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"><?php echo lang('Apakah anda telah menyetorkan atau menarik uang dalam 12 bulan terakhir') ?></td>
            <td class="line">
                <input disabled type="radio" <?php echo $survey['DepositWithdrawnMoneyLast12m'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?php echo $survey['DepositWithdrawnMoneyLast12m'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"><?php echo lang('Apakah tahu biaya-biaya yang harus dibayar pada rekening saya') ?></td>
            <td class="line">
                <input disabled type="radio" <?php echo $survey['AccountFeesToPay'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?php echo $survey['AccountFeesToPay'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>

        <tr>
            <td colspan="3"><?php echo lang('Apakah tahu suku bunga yang diterima pada rekening saya') ?></td>
            <td class="line">
                <input disabled type="radio" <?php echo $survey['AccountInterestRate'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?php echo $survey['AccountInterestRate'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>

        <tr>
            <td colspan="4"><?php echo lang('Apakah yang Anda lakukan dengan uang Anda, selain dari membelanjakannya untuk kebutuhan sehari-hari') ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="line">
                <input disabled type="checkbox" <?php echo $survey['MoneyUsageHarian'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya hanya memiliki uang untuk kebutuhan sehari-hari') ?><br>
                <input disabled type="checkbox" <?php echo $survey['MoneyUsageTabung'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya tabung sebagian uang saya') ?><br>
                <input disabled type="checkbox" <?php echo $survey['MoneyUsageInvestasi'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya menginvestasikan sebagian uang dalam bisnis saya') ?><br>
                <input disabled type="checkbox" <?php echo $survey['MoneyUsageEmas'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya menyimpan sebagian uang dalam bentuk emas, ternak, bata, dll') ?><br>
                <input disabled type="checkbox" <?php echo $survey['MoneyUsageKonsumsi'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya tidak menabung uang. Bilamana ada uang saya gunakan untuk keperluan konsumsi') ?><br>
            </td>
        <tr>
            <td colspan="4"><?php echo lang('Apakah alasan Anda tidak menabung') ?></td>
        </tr>
        <tr>
            <td colspan="4" class="line">
                <input disabled type="checkbox" <?php echo $survey['NotSavingJauh'] == '1' ? 'checked' : '' ?>><?php echo lang('Lembaga Keuangan terlalu jauh dari tempat saya') ?><br>
                <input disabled type="checkbox" <?php echo $survey['NotSavingTidakBeruang'] == '1' ? 'checked' : '' ?>><?php echo lang('Tidak memiliki uang untuk ditabung') ?><br>
                <input disabled type="checkbox" <?php echo $survey['NotSavingBiayaTinggi'] == '1' ? 'checked' : '' ?>><?php echo lang('Biayanya terlalu tinggi') ?><br>
                <input disabled type="checkbox" <?php echo $survey['NotSavingTidakPercaya'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya tidak mempercaya orang lain') ?><br>
                <input disabled type="checkbox" <?php echo $survey['NotSavingAdaMenabung'] == '1' ? 'checked' : '' ?>><?php echo lang('Anggota keluarga saya sudah ada yang menabung') ?><br>
                <input disabled type="checkbox" <?php echo $survey['NotSavingLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Lainnya/Saya tidak tahu') ?>
            </td>
        </tr>

    </table>
    

    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="50%"><?php echo lang('Page')?> - 1</td>
                <td class="kolomKanan" align="right">
                    <?php echo $title?>
                </td>
            </tr>
        </table>
    </footer>
</div>
<div class="page">
    <table width="100%" border="0" class="table" cellpadding="2" cellspacing="0">
        <tr>
            <td colspan="4"><?php echo lang('Di manakah Anda menabung sekarang') ?>
        <tr>
            <td colspan="4" class="line">
                <table width="100%">
                    <tr>
                        <td style="width: 50%">
                            <input disabled type="checkbox" <?php echo $survey['SavingUnitRumah'] == '1' ? 'checked' : '' ?>><?php echo lang('Di rumah') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['SavingUnitBank'] == '1' ? 'checked' : '' ?>><?php echo lang('Di bank') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['SavingUnitKoperasi'] == '1' ? 'checked' : '' ?>><?php echo lang('Di koperasi ') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['SavingUnitPedagang'] == '1' ? 'checked' : '' ?>><?php echo lang('Di pedagang/tengkulak') ?>
                        </td>
                        <td>
                            <input disabled type="checkbox" <?php echo $survey['SavingUnitArisan'] == '1' ? 'checked' : '' ?>><?php echo lang('Di Arisan / ROSCA / kelompok Petani') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['SavingUnitOrang'] == '1' ? 'checked' : '' ?>><?php echo lang('Dengan orang lain (teman, keluarga, kolektor, lainnya)') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['SavingUnitLembaga'] == '1' ? 'checked' : '' ?>><?php echo lang('Di lembaga lain') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['SavingUnitMeminjamkan'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya meminjamkan uang ke orang lain') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%"> <?php echo lang('Berapa jauh jarak tempat anda menabung dari rumah') ?>:</td>
            <td colspan="2" class="line">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="radio" <?php echo $survey['DistanceSavingLocation'] == '1' ? 'checked' : '' ?>><?php echo lang('Di rumah') ?><br>
                            <input disabled type="radio" <?php echo $survey['DistanceSavingLocation'] == '2' ? 'checked' : '' ?>><?php echo lang('1 sampai 3 km') ?><br>
                            <input disabled type="radio" <?php echo $survey['DistanceSavingLocation'] == '3' ? 'checked' : '' ?>><?php echo lang('Lebih dari 10 km') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?php echo $survey['DistanceSavingLocation'] == '4' ? 'checked' : '' ?>><?php echo lang('kurang dari 1 km') ?><br>
                            <input disabled type="radio" <?php echo $survey['DistanceSavingLocation'] == '5' ? 'checked' : '' ?>><?php echo lang('3 sampai 10 km') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>



        <tr>
            <td colspan="2"> <?php echo lang('Berapakah nilai total tabungan Anda sekarang') ?></td>
            <td colspan="2" class="line">
                <input disabled type="radio" <?php echo $survey['AmountSaving'] == '1' ? 'checked' : '' ?>><?php echo lang('Kurang dari 2 juta') ?><br>
                <input disabled type="radio" <?php echo $survey['AmountSaving'] == '2' ? 'checked' : '' ?>><?php echo lang('2 sampai 10 juta') ?><br>
                <input disabled type="radio" <?php echo $survey['AmountSaving'] == '3' ? 'checked' : '' ?>><?php echo lang('Lebih dari 10 juta') ?>
            </td>
        </tr>
        <tr>
            <td colspan="4"> <?php echo lang('Untuk keperluan apa Anda menabung selain untuk kebutuhan sehari-hari') ?></td>
        </tr>
        <tr>
            <td colspan="4" class="line">
                <table width="100%">
                    <tr>
                        <td style="width: 50%">
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonSekolah'] == '1' ? 'checked' : '' ?>><?php echo lang('Biaya sekolah / Pendidikan') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonRumahTangga'] == '1' ? 'checked' : '' ?>><?php echo lang('Peralatan rumah tangga (kulkas, TV dan lain-lain)') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonSumbangan'] == '1' ? 'checked' : '' ?>><?php echo lang('Sumbangan pemakaman / pernikahan') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonDarurat'] == '1' ? 'checked' : '' ?>><?php echo lang('Keadaan Darurat') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonKesehatan'] == '1' ? 'checked' : '' ?>><?php echo lang('Perawat kesehatan') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonInvestasiKebun'] == '1' ? 'checked' : '' ?>><?php echo lang('Investasi pertanian dan Pemeliharaan kebun kakao') ?>
                        </td>
                        <td>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonInvestasiLain'] == '1' ? 'checked' : '' ?>><?php echo lang('Investasi bisnis lainnya') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonRumah'] == '1' ? 'checked' : '' ?>><?php echo lang('Rumah baru / renovasi rumah') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonLahan'] == '1' ? 'checked' : '' ?>><?php echo lang('Membeli lahan baru untuk bertani') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonKendaraan'] == '1' ? 'checked' : '' ?>><?php echo lang('Motor / Mobil') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonHaji'] == '1' ? 'checked' : '' ?>><?php echo lang('Haji / Umrah') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonPensiun'] == '1' ? 'checked' : '' ?>><?php echo lang('Masa Pensiun') ?><br>
                            <input disabled type="checkbox" <?php echo $survey['FutureReasonLain'] == '1' ? 'checked' : '' ?>><?php echo lang('Lainnya') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4"><?php echo lang('Apa pertimbangan utama Anda menabung dilembaga seperti bank atau koperasi atau orang pribadi')?></td>
        </tr>
        <tr>
            <td colspan="4" class="line">
                <table width="100%">
                    <tr>
                        <td style="width: 50%">
                            <input disabled type="checkbox" <?php echo $survey['ImportantFactorKemanan'] == '1' ? 'checked' : '' ?>><?php echo lang('Keamanan')?><br>
                            <input disabled type="checkbox" <?php echo $survey['ImportantFactorLikuiditas'] == '1' ? 'checked' : '' ?>><?php echo lang('Likuiditas/Mudah diuangkan')?><br>
                            <input disabled type="checkbox" <?php echo $survey['ImportantFactorAksesibilitas'] == '1' ? 'checked' : '' ?>><?php echo lang('Aksesibilitas/Kemudahan akses')?><br>
                            <input disabled type="checkbox" <?php echo $survey['ImportantFactorKepercayaan'] == '1' ? 'checked' : '' ?>><?php echo lang('Kepercayaan')?>
                        </td>
                        <td>
                            <input disabled type="checkbox" <?php echo $survey['ImportantFactorBiaya'] == '1' ? 'checked' : '' ?>><?php echo lang('Biaya')?><br>
                            <input disabled type="checkbox" <?php echo $survey['ImportantFactorBunga'] == '1' ? 'checked' : '' ?>><?php echo lang('Bunga yang akan diterima')?><br>
                            <input disabled type="checkbox" <?php echo $survey['ImportantFactorLain'] == '1' ? 'checked' : '' ?>><?php echo lang('Lain lain')?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4"> <?php echo lang('Apakah Anda memiliki pinjaman saat ini atau sebelumnya') ?>?</td>
        </tr>
        <tr>
            <td colspan="4" class="line">
                <input disabled type="radio" <?php echo $survey['LoanYesNo'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya, saat ini saya memiliki pinjaman') ?><br>
                <input disabled type="radio" <?php echo $survey['LoanYesNo'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak, saya tidak pernah memiliki pinjaman') ?><br>
                <input disabled type="radio" <?php echo $survey['LoanYesNo'] == '3' ? 'checked' : '' ?>><?php echo lang('Saat ini saya tidak memiliki pinjaman, tapi pernah sekali atau beberapa kali meminjam sebelumnya') ?><br>
                <input disabled type="radio" <?php echo $survey['LoanYesNo'] == '4' ? 'checked' : '' ?>><?php echo lang('Saat ini saya memiliki pinjaman, dan pernah sekali atau beberapa kali meminjam sebelumnya') ?>
            </td>
        </tr>


        <tr>
            <td colspan="3"> <?php echo lang('Berapakah jumlah uang yang Anda pinjam dari pinjaman yang sekarang (terakhir) (rupiah)') ?>
            </td>
            <td class="line"><?php echo $survey['AmountCurrentLoan'] ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Berapakah jumlah uang yang masih belum lunas dari pinjaman yang sekarang (terakhir) (rupiah)') ?>
            </td>
            <td class="line" width="20%"><?php echo $survey['AmountOutsCurrentLoan'] ?></td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Dari manakah Anda (pernah) meminjam uang untuk pinjaman sekarang (terakhir)') ?></td>
            <td class="line" colspan="2" width="50%">
                <table width="100%">
                    <tr>
                        <td><input disabled type="radio" <?php echo $survey['LoanUnitTengkulak'] == '1' ? 'checked' : '' ?>><?php echo lang('Tengkulak/Pedagang') ?><br>
                            <input disabled type="radio" <?php echo $survey['LoanUnitKeluarga'] == '1' ? 'checked' : '' ?>><?php echo lang('Keluarga / Teman ') ?><br>
                            <input disabled type="radio" <?php echo $survey['LoanUnitRentenir'] == '1' ? 'checked' : '' ?>><?php echo lang('Rentenir/ Perantara ') ?><br>
                            <input disabled type="radio" <?php echo $survey['LoanUnitBank'] == '1' ? 'checked' : '' ?>><?php echo lang('Bank') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?php echo $survey['LoanUnitKoperasi'] == '1' ? 'checked' : '' ?>><?php echo lang('Koperasi') ?><br>
                            <input disabled type="radio" <?php echo $survey['LoanUnitMasjid'] == '1' ? 'checked' : '' ?>><?php echo lang('BMT') ?><br>
                            <input disabled type="radio" <?php echo $survey['LoanUnitLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Lainnya') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Berapa Anda mendapatkan pinjaman sebelumnya') ?></td>
            <td class="line" colspan="2">
                <input disabled type="radio" <?php echo $survey['PreviousLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Tidak pernah') ?>
                <input disabled type="radio" <?php echo $survey['PreviousLoan'] == '2' ? 'checked' : '' ?>>1
                <input disabled type="radio" <?php echo $survey['PreviousLoan'] == '3' ? 'checked' : '' ?>>2
                <input disabled type="radio" <?php echo $survey['PreviousLoan'] == '4' ? 'checked' : '' ?>><?php echo lang('3 atau lebih') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Apakah Anda memerlukan jaminan untuk pinjaman Anda saat ini (terakhir)') ?>?</td>
            <td class="line">
                <input disabled type="radio" <?php echo $survey['CollateralCurrentLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?php echo $survey['CollateralCurrentLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Apakah mudah memperoleh pinjaman saat ini (terakhir)') ?>?</td>
            <td class="line">
                <input disabled type="radio" <?php echo $survey['EasyCurrentLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?php echo $survey['EasyCurrentLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Berapa lama proses pengajuan hingga pencairan untuk pinjaman Anda saat ini (terakhir)  ') ?></td>
            <td class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="radio" <?php echo $survey['DisburseIntervalCurrentLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Hari yang sama') ?><br>
                            <input disabled type="radio" <?php echo $survey['DisburseIntervalCurrentLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('3 sampai 7 hari') ?><br>
                            <input disabled type="radio" <?php echo $survey['DisburseIntervalCurrentLoan'] == '3' ? 'checked' : '' ?>><?php echo lang('Lebih dari 14 hari') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?php echo $survey['DisburseIntervalCurrentLoan'] == '4' ? 'checked' : '' ?>><?php echo lang('1 sampai 2 hari ') ?><br>
                            <input disabled type="radio" <?php echo $survey['DisburseIntervalCurrentLoan'] == '5' ? 'checked' : '' ?>><?php echo lang('8 sampai 14 hari') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Bagaimana sistem angsuran pinjaman Anda saat ini (terakhir)') ?></td>
            <td class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="radio" <?php echo $survey['RepaymentScheduleCurrentLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Fleksibel') ?><br>
                            <input disabled type="radio" <?php echo $survey['RepaymentScheduleCurrentLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Bulanan') ?><br>
                            <input disabled type="radio" <?php echo $survey['RepaymentScheduleCurrentLoan'] == '3' ? 'checked' : '' ?>><?php echo lang('Lainnya') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?php echo $survey['RepaymentScheduleCurrentLoan'] == '4' ? 'checked' : '' ?>><?php echo lang('Mingguan') ?><br>
                            <input disabled type="radio" <?php echo $survey['RepaymentScheduleCurrentLoan'] == '5' ? 'checked' : '' ?>><?php echo lang('Akhir periode') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <tr>
            <td colspan="4"> <?php echo lang('Ketika Anda selesai melunasi pinjaman sebelumnya, apakah mudah memperoleh pinjaman yang baru') ?>
            </td>
        </tr>
        <tr>
            <td class="line" colspan="4">
                <input disabled type="radio" <?= $v['EasyGetNewLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Belum tahu, masa pinjaman sekarang sedang berjalan') ?><br>
                <input disabled type="radio" <?= $v['EasyGetNewLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Ya, gampang') ?><br>
                <input disabled type="radio" <?= $v['EasyGetNewLoan'] == '3' ? 'checked' : '' ?>><?php echo lang('Ya, tapi sulit') ?><br>
                <input disabled type="radio" <?= $v['EasyGetNewLoan'] == '4' ? 'checked' : '' ?>><?php echo lang('Saya mencoba, namun saya tidak memperoleh pinjaman baru') ?><br>
                <input disabled type="radio" <?= $v['EasyGetNewLoan'] == '5' ? 'checked' : '' ?>><?php echo lang('Saya tidak memerlukan pinjaman yang baru') ?>
            </td>
        </tr>
        

    </table>
    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="50%"><?php echo lang('Page')?> - 2</td>
                <td class="kolomKanan" align="right">
                    <?php echo $title?>
                </td>
            </tr>
        </table>
    </footer>
</div>
<div class="page">
    <table width="100%" border="0" class="table" cellpadding="2" cellspacing="0">
        <tr>
            <td colspan="4"> <?php echo lang('Untuk apakah Anda akan menggunakan pinjaman saat ini (terakhir)') ?></td>
        </tr>
        <tr>
            <td class="line" colspan="4">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanHarian'] == '1' ? 'checked' : '' ?>><?php echo lang('Kebutuhan sehari-hari/ Biaya hidup') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanSekolah'] == '1' ? 'checked' : '' ?>><?php echo lang('Biaya sekolah / Pendidikan') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanRumahTangga'] == '1' ? 'checked' : '' ?>><?php echo lang('Peralatan rumah tangga (kulkas, TV dan lain-lain)') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanSumbangan'] == '1' ? 'checked' : '' ?>><?php echo lang('Sumbangan pemakaman / pernikahan') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanHutang'] == '1' ? 'checked' : '' ?>><?php echo lang('Membayar utang') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanDarurat'] == '1' ? 'checked' : '' ?>><?php echo lang('Keadaan Darurat') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanKesehatan'] == '1' ? 'checked' : '' ?>><?php echo lang('Perawat kesehatan') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanInvestasiKebun'] == '1' ? 'checked' : '' ?>><?php echo lang('Investasi pertanian dan Pemeliharaan kebun kakao') ?>
                        </td>
                        <td>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanInvestasiLain'] == '1' ? 'checked' : '' ?>><?php echo lang('Investasi bisnis lainnya') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanRumah'] == '1' ? 'checked' : '' ?>><?php echo lang('Rumah baru / renovasi rumah') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanLahan'] == '1' ? 'checked' : '' ?>><?php echo lang('Membeli lahan baru untuk bertani') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanKendaraan'] == '1' ? 'checked' : '' ?>><?php echo lang('Motor / Mobil') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanHaji'] == '1' ? 'checked' : '' ?>><?php echo lang('Haji / Umrah') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanPensiun'] == '1' ? 'checked' : '' ?>><?php echo lang('Masa Pensiun') ?><br>
                            <input disabled type="checkbox" <?= $v['UsageCurrentLoanLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Lainnya') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3"> <?php echo lang('Pernahkah Anda melunasi pinjaman lebih awal ') ?></td>
            <td class="line" width="375">
                <input disabled type="radio" <?= $v['TerminatedLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['TerminatedLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="4"> <?php echo lang('Dari manakah Anda(pernah) meminjam uang untuk pinjaman sekarang (terakhir)') ?></td>
        </tr>
        <tr>
            <td class="line" colspan="4">
                <input disabled type="checkbox" <?= $v['MoneyToRepayLoanPenghasilan'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya memiliki penghasilan yang lebih baik (dari kakao atau lainnya) daripada yang diharapkan') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyToRepayLoanPinjaman'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya memperoleh pinjaman baru') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyToRepayLoanTanah'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya menjual sebidang tanah') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyToRepayLoanTernak'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya menjual satu atau beberapa ternak') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyToRepayLoanDeposito'] == '1' ? 'checked' : '' ?>><?php echo lang('Mencairkan deposito berjangka saya') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyToRepayLoanLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Lainnya') ?>
        <tr><br>
            <td colspan="2"> <?php echo lang('Pembagian keuntungan lebih baik daripada pinjaman berbunga') ?></td>
            <td class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td><input disabled type="radio" <?= $v['ProfitSharingLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya setuju') ?> <br>
                            <input disabled type="radio" <?= $v['ProfitSharingLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Saya tidak setuju') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?= $v['ProfitSharingLoan'] == '3' ? 'checked' : '' ?>><?php echo lang('Saya tidak tahu') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Memiliki pinjaman adalah sebuah tanggung jawab yang besar') ?></td>
            <td class="line" colspan="2">
                <input disabled type="radio" <?= $v['ResponsibilityLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya setuju') ?>
                <input disabled type="radio" <?= $v['ResponsibilityLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Saya tidak setuju') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Ketika saya memiliki pinjaman, saya merasa khawatir dengan cara apa saya akan membayarnya') ?>
            </td>
            <td class="line" colspan="2">
                <input disabled type="radio" <?= $v['WorryToRepayLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya setuju') ?>
                <input disabled type="radio" <?= $v['WorryToRepayLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Saya tidak setuju') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Terkadang sulit menutupi semua biaya') ?>:</td>
            <td class="line" colspan="2">
                <input disabled type="radio" <?= $v['DifficultCoverExpenses'] == '1' ? 'checked' : '' ?>><?php echo lang('Terkadang sulit menutupi biaya keluarga maupun usaha') ?><br>
                <input disabled type="radio" <?= $v['DifficultCoverExpenses'] == '2' ? 'checked' : '' ?>><?php echo lang('Terkadang sulit menutupi biaya keluarga') ?><br>
                <input disabled type="radio" <?= $v['DifficultCoverExpenses'] == '3' ? 'checked' : '' ?>><?php echo lang('Terkadang sulit menutupi biaya usaha') ?><br>
                <input disabled type="radio" <?= $v['DifficultCoverExpenses'] == '4' ? 'checked' : '' ?>><?php echo lang('Tidak, biasanya saya dapat menutupi semua biaya') ?>
            </td>
        </tr>
        <tr>
            <td colspan="4"> <?php echo lang('Apa faktor terpenting bagi Anda saat menabung di lembaga seperti bank atau koperasi atau orang pribadi') ?>
            </td>
        </tr>
        <tr>
            <td class="line" colspan="4">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesSewaRumah'] == '1' ? 'checked' : '' ?>><?php echo lang('Sewa Rumah') ?><br>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesKebun'] == '1' ? 'checked' : '' ?>><?php echo lang('Kebutuhan kebun') ?><br>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesMakanan'] == '1' ? 'checked' : '' ?>><?php echo lang('Makanan') ?><br>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesKesehatan'] == '1' ? 'checked' : '' ?>><?php echo lang('Perawatan Kesehatan') ?><br>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesSosial'] == '1' ? 'checked' : '' ?>><?php echo lang('Kewajiban Sosial (pemakaman, pernikahan, dll.)') ?>
                        </td>
                        <td>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesListrik'] == '1' ? 'checked' : '' ?>><?php echo lang('Listrik / Air') ?><br>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesPendidikan'] == '1' ? 'checked' : '' ?>><?php echo lang('Pendidikan / Biaya Sekolah') ?><br>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesSandang'] == '1' ? 'checked' : '' ?>><?php echo lang('Sandang') ?><br>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesAngsuran'] == '1' ? 'checked' : '' ?>><?php echo lang('Angsuran Pinjaman') ?><br>
                            <input disabled type="checkbox" <?= $v['PostponeExpensesLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Lainnya') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Terkadang sulit memberikan uang (kontribusi) dalam kewajiban sosial seperti  pemakaman atau pernikahan') ?>
            </td>
            <td class="line" colspan="2">
                <input disabled type="radio" <?= $v['DifficultSocialContributions'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya setuju') ?>
                <input disabled type="radio" <?= $v['DifficultSocialContributions'] == '2' ? 'checked' : '' ?>><?php echo lang('Saya tidak setuju') ?>
            </td>
        </tr>


        <tr>
            <td colspan="4"> <?php echo lang('Jika Anda butuh uang untuk pengeluaran mendesak, dari mana Anda akan   mengambilnya') ?>
            </td>
        </tr>
        <tr>
            <td class="line" colspan="4">
                <input disabled type="checkbox" <?= $v['MoneyUrgentExpensesTabungan'] == '1' ? 'checked' : '' ?>><?php echo lang('Tabungan') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyUrgentExpensesMeminjamKeluarga'] == '1' ? 'checked' : '' ?>><?php echo lang('Meminjam dari keluarga dan teman') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyUrgentExpensesMeminjamTengkulak'] == '1' ? 'checked' : '' ?>><?php echo lang('Meminjam (tengkulak/ bank)') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyUrgentExpensesMenjual'] == '1' ? 'checked' : '' ?>><?php echo lang('Segera memanen sejumlah kakao dan menjualnya dalam keadaan biji basah') ?><br>
                <input disabled type="checkbox" <?= $v['MoneyUrgentExpensesLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya tidak tahu') ?>
            </td>
        </tr>

        <tr>
            <td colspan="2"> <?php echo lang('Berapa biayanya untuk membeli pupuk tak bersubsidi untuk kebun kakao yang luasnya satu hektar') ?>
            </td>
            <td width="50%" class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="radio" <?= $v['CostUnsubsidizedFertilizer'] == '1' ? 'checked' : '' ?>><?php echo lang('Kurang dari 1 juta') ?><br>
                            <input disabled type="radio" <?= $v['CostUnsubsidizedFertilizer'] == '2' ? 'checked' : '' ?>><?php echo lang('2 sampai 3 juta') ?><br>
                            <input disabled type="radio" <?= $v['CostUnsubsidizedFertilizer'] == '3' ? 'checked' : '' ?>><?php echo lang('lebih dari 4 juta') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?= $v['CostUnsubsidizedFertilizer'] == '4' ? 'checked' : '' ?>><?php echo lang('1 sampai 2 juta') ?><br>
                            <input disabled type="radio" <?= $v['CostUnsubsidizedFertilizer'] == '5' ? 'checked' : '' ?>><?php echo lang('3 sampai 4 juta') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Apakah Anda memiliki pendapatan lain selain dari kebun kakao') ?></td>
            <td class="line" width="20%">
                <input disabled type="radio" <?= $v['OtherInconme'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['OtherInconme'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Apakah Anda memiliki program pensiun') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['PensionPlan'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['PensionPlan'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Penghasilan Anda lainnya adalah') ?></td>
            <td class="line" colspan="2">
                <input disabled type="radio" <?= $v['OtherIncomeRegular'] == '1' ? 'checked' : '' ?>><?php echo lang('Rutin') ?>
                <input disabled type="radio" <?= $v['OtherIncomeRegular'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak Rutin') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Apa sumber penghasilan lain itu') ?></td>
            <td class="line" colspan="2">
                <input disabled type="checkbox" <?= $v['SourceOtherIncomeGajiTetap'] == '1' ? 'checked' : '' ?>><?php echo lang('Gaji dari pekerjaan tetap / paruh waktu') ?><br>
                <input disabled type="checkbox" <?= $v['SourceOtherIncomeGajiPasangan'] == '1' ? 'checked' : '' ?>><?php echo lang('Gaji pasangan (gaji Suami/Istri)') ?><br>
                <input disabled type="checkbox" <?= $v['SourceOtherIncomeUsaha'] == '1' ? 'checked' : '' ?>><?php echo lang('Penghasilan dari usaha lain') ?><br>
                <input disabled type="checkbox" <?= $v['SourceOtherIncomeFamily'] == '1' ? 'checked' : '' ?>><?php echo lang('Saudara/famili yang mengirim uang dari luar negeri') ?><br>
                <input disabled type="checkbox" <?= $v['SourceOtherIncomeLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Pendapatan Lainnya') ?>
            </td>
        </tr>
    </table>
    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="50%"><?php echo lang('Page')?> - 3</td>
                <td class="kolomKanan" align="right">
                    <?php echo $title?>
                </td>
            </tr>
        </table>
    </footer>
</div>
<div class="page">
    <table width="100%" border="0" class="table" cellpadding="2" cellspacing="0">        
        <tr>
            <td colspan="2" style="width: 50%"> <?php echo lang('Berapa besar penghasilan lain itu per bulannya') ?></td>
            <td class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td><input disabled type="radio" <?= $v['AmountOtherIncome'] == '1' ? 'checked' : '' ?>><?php echo lang('Kurang dari 1 juta') ?><br>
                            <input disabled type="radio" <?= $v['AmountOtherIncome'] == '2' ? 'checked' : '' ?>><?php echo lang('2 sampai 3 juta') ?><br>
                            <input disabled type="radio" <?= $v['AmountOtherIncome'] == '3' ? 'checked' : '' ?>><?php echo lang('Lebih dari 5 juta') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?= $v['AmountOtherIncome'] == '4' ? 'checked' : '' ?>><?php echo lang('1 sampai 2 juta') ?><br>
                            <input disabled type="radio" <?= $v['AmountOtherIncome'] == '5' ? 'checked' : '' ?>><?php echo lang('3 sampai 5 juta') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Kakao adalah bisnis yang menguntungkan') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['CocoaProfitableBusiness'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['CocoaProfitableBusiness'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Pinjaman lebih baik daripada tabungan') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['LoanBetterThanSaving'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['LoanBetterThanSaving'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Membeli pupuk nonsubsidi itu menguntungkan') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['UnsubsidizedFertilizerProfitable'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['UnsubsidizedFertilizerProfitable'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Suku bunga yang tinggi bisa diterima, sepanjang anda bisa membayarnya.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['HighInterestRate'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['HighInterestRate'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Jika seorang petani memiliki utang di tengkulak/pedagang, ia hanya bisa menjual kakaonya ke tengkulak ini') ?>
            </td>
            <td class="line">
                <input disabled type="radio" <?= $v['LoanWithTrader'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['LoanWithTrader'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Lebih baik menjual biji kakao dalam keadaan basah daripada kering, karena Anda mendapatkan uang lebih cepat.') ?>
            </td>
            <td class="line">
                <input disabled type="radio" <?= $v['BetterWetDriedBeans'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['BetterWetDriedBeans'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda adalah peminjam yang baik.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['GoodLoanClient'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['GoodLoanClient'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda mempercayai anggota grup/orang di desa.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['TrustGroupMembers'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['TrustGroupMembers'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda mau membayarkan pinjaman anggota kelompok jika ia membutuhkannya.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['RepayLoanGroupMember'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['RepayLoanGroupMember'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda mempercayai bank untuk menyimpan uang tabungan Anda.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['TrustBank'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['TrustBank'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Dengan kebun kakao Anda dapat membayar semua pengeluaran keluarga.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['CocoaFarmPayExpenses'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['CocoaFarmPayExpenses'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda cukup disiplin menabung uang untuk keperluan tertentu.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['DiscipilinedSaveMoney'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['DiscipilinedSaveMoney'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>

        <tr>
            <td colspan="3"> <?php echo lang('Pedagang/tengkulak adalah orang kaya') ?></td>
            <td class="line" width="20%">
                <input disabled type="radio" <?= $v['TradersRich'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['TradersRich'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda punya jaminan untuk ditawarkan ke bank.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['CollateralOfferedBank'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['CollateralOfferedBank'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Saat ini banyak kebun kakao yang akan dijual') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['ManyCocoaFarmSale'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['ManyCocoaFarmSale'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda puas dengan usaha kakao Anda saat ini.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['SatisfiedCocoaBusiness'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['SatisfiedCocoaBusiness'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Membayar beberapa kg kakao sebagai "bunga" lebih baik daripada membayar uang.') ?></td>
            <td class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="radio" <?= $v['PayCocoaBetterInterest'] == '1' ? 'checked' : '' ?>><?php echo lang('Saya setuju') ?><br>
                            <input disabled type="radio" <?= $v['PayCocoaBetterInterest'] == '2' ? 'checked' : '' ?>><?php echo lang('Saya tidak setuju') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?= $v['PayCocoaBetterInterest'] == '3' ? 'checked' : '' ?>><?php echo lang('Saya tidak tahu') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <tr>
            <td colspan="3"> <?php echo lang('Anda membutuhkan pinjaman dan Anda harus memperolehnya.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['NeedLoan'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['NeedLoan'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda memiliki telepon seluler/hp, kartu SIM atau memiliki akses ke telepon selular kalau-kalau Anda membutuhkannya.') ?>
            </td>
            <td class="line">
                <input disabled type="radio" <?= $v['MobilePhone'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['MobilePhone'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"><?php echo lang('Apakah alasan tidak punya telepon') ?></td>
            <td class="line">
                <input disabled type="checkbox" <?= $v['ReasonNotHavePhoneTidakButuh'] == '1' ? 'checked' : '' ?>>
                <?php echo lang('Saya tidak membutuhkannya') ?><br>
                <input disabled type="checkbox" <?= $v['ReasonNotHavePhoneMahal'] == '2' ? 'checked' : '' ?>>
                <?php echo lang('Terlalu mahal') ?><br>
                <input disabled type="checkbox" <?= $v['ReasonNotHavePhoneSinyal'] == '3' ? 'checked' : '' ?>>
                <?php echo lang('Tidak ada jangkauan sinyal') ?><br>
                <input disabled type="checkbox" <?= $v['ReasonNotHavePhoneLainnya'] == '4' ? 'checked' : '' ?>>
                <?php echo lang('Lainnya') ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Anda memiliki pengetahuan untuk melakukan analisis pinjaman.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['LoanAnalysisKnowledge'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['LoanAnalysisKnowledge'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Produk keuangan Islami lebih penting bagi Anda daripada produk-produk keuangan non-islami') ?>:
            </td>
            <td class="line" colspan="2" width="50%">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="radio" <?= $v['IslamicFinancialAwareness'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?><br>
                            <input disabled type="radio" <?= $v['IslamicFinancialAwareness'] == '2' ? 'checked' : '' ?>><?php echo lang('Saya tidak tahu tentang Produk-produk keuangan islami') ?>
                        </td>
                        <td width="30%">
                            <input disabled type="radio" <?= $v['IslamicFinancialAwareness'] == '3' ? 'checked' : '' ?>><?php echo lang('Tidak') ?><br>
                            <input disabled type="radio" <?= $v['IslamicFinancialAwareness'] == '4' ? 'checked' : '' ?>><?php echo lang('Saya tidak tahu dua-duanya tentang produk keuangan islami dan non-islami') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Anda ingin belajar dari SCPP tentang cara menabung uang') ?>.:</td>
            <td class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td><input disabled type="radio" <?= $v['LearnToSaveMoney'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?><br>
                            <input disabled type="radio" <?= $v['LearnToSaveMoney'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak, saya tidak tertarik dengan tabungan') ?>.
                        </td>
                        <td>
                            <input disabled type="radio" <?= $v['LearnToSaveMoney'] == '3' ? 'checked' : '' ?>><?php echo lang('Tidak, Saya sudah tahu tentang tabungan') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Berapa harga kakao per kilogram hari ini') ?></td>
            <td class="line" colspan="2"><?= $v['CocoaPriceToday'] ?></td>
        </tr>

        <tr>
            <td colspan="2"> <?php echo lang('Nilai kebun kakao saya adalah') ?>:</td>
            <td class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td><input disabled type="radio" <?= $v['ValueCocoaFarm'] == '1' ? 'checked' : '' ?>><?php echo lang('Kurang dari 10 juta') ?><br>
                            <input disabled type="radio" <?= $v['ValueCocoaFarm'] == '2' ? 'checked' : '' ?>><?php echo lang('10 sampai 20 juta') ?><br>
                            <input disabled type="radio" <?= $v['ValueCocoaFarm'] == '3' ? 'checked' : '' ?>><?php echo lang('20 sampai 50 juta') ?><br>
                            <input disabled type="radio" <?= $v['ValueCocoaFarm'] == '4' ? 'checked' : '' ?>><?php echo lang('50 sampai 100 juta') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?= $v['ValueCocoaFarm'] == '5' ? 'checked' : '' ?>><?php echo lang('100 sampai 200 juta') ?><br>
                            <input disabled type="radio" <?= $v['ValueCocoaFarm'] == '6' ? 'checked' : '' ?>><?php echo lang('Lebih daripada 200 juta') ?><br>
                            <input disabled type="radio" <?= $v['ValueCocoaFarm'] == '7' ? 'checked' : '' ?>><?php echo lang('Saya tidak tahu') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"> <?php echo lang('Apakah Anda mengetahui apa itu asuransi') ?></td>
            <td class="line" colspan="2">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="radio" <?= $v['InsuranceKnowledge'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                            <input disabled type="radio" <?= $v['InsuranceKnowledge'] == '2' ? 'checked' : '' ?>><?php echo lang('Saya tidak begitu tahu pasti') ?>
                        </td>
                        <td>
                            <input disabled type="radio" <?= $v['InsuranceKnowledge'] == '3' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3"> <?php echo lang('Sekarang Anda punya asuransi / pernah punya sebelumnya.') ?></td>
            <td class="line">
                <input disabled type="radio" <?= $v['PastNowInsurance'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
                <input disabled type="radio" <?= $v['PastNowInsurance'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
            </td>
        </tr>
        <tr>
            <td colspan="4"> <?php echo lang('Saya memiliki / pernah memiliki asuransi untuk') ?></td>
        </tr>
        <tr>
            <td class="line" colspan="4">
                <table width="100%">
                    <tr>
                        <td>
                            <input disabled type="checkbox" <?= $v['InsuranceTypeMotor'] == '1' ? 'checked' : '' ?>><?php echo lang('Motor') ?><br>
                            <input disabled type="checkbox" <?= $v['InsuranceTypePanen'] == '1' ? 'checked' : '' ?>><?php echo lang('Hasil Panen') ?><br>
                            <input disabled type="checkbox" <?= $v['InsuranceTypeBanjir'] == '1' ? 'checked' : '' ?>><?php echo lang('Banjir') ?><br>
                            <input disabled type="checkbox" <?= $v['InsuranceTypeKemarau'] == '1' ? 'checked' : '' ?>><?php echo lang('Kemarau') ?>
                        </td>
                        <td>
                            <input disabled type="checkbox" <?= $v['InsuranceTypeMobil'] == '1' ? 'checked' : '' ?>><?php echo lang('Mobil') ?><br>
                            <input disabled type="checkbox" <?= $v['InsuranceTypeKesehatan'] == '1' ? 'checked' : '' ?>><?php echo lang('Kesehatan') ?><br>
                            <input disabled type="checkbox" <?= $v['InsuranceTypeJiwa'] == '1' ? 'checked' : '' ?>><?php echo lang('Jiwa / Kredit Jiwa') ?><br>
                            <input disabled type="checkbox" <?= $v['InsuranceTypeLainnya'] == '1' ? 'checked' : '' ?>><?php echo lang('Lainnya') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <footer>
        <table class="tabelFooter" border="0" width="100%">
            <tr>
                <td class="kolomKiri" width="50%"><?php echo lang('Page')?> - 4</td>
                <td class="kolomKanan" align="right">
                    <?php echo $title?>
                </td>
            </tr>
        </table>
    </footer>
</div>
</body>
</html>
