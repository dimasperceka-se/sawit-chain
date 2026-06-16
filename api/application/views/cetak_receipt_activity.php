<?php
/**
 * @Author: nikolius
 * @Date:   2017-01-19 11:49:27
 */
$baseurlnya = base_url();
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8"/>
    <title>Tanda Terima</title>

    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/receipt/receipt.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/receipt/receipt-media.css" media="print"/>
    <script src="<?php echo $baseurlnya; ?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
</head>
<body>

<div class="page" style="">

    <!-- LOGO ATAS (BEGIN) -->
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
    <!-- LOGO ATAS (END) -->

    <div class="titlePaper">Tanda Terima Peralatan/Perlengkapan</div>
    <div class="titlePaper"><?php echo $labelTrainingHeader?> untuk Penerima Kelompok</div>
    <br />

    <!-- TABLE HEADER DETAIL (BEGIN) -->
    <?php
    switch ($dataReceipt['ObjType']) {
        case 'farmergroup':?>
            <table width="100%" border="0" class="tabelHeaderDetail">
            <tr>
                <td style="" width="22%">No. CPG / Batch</td>
                <td style="text-align:center;" width="5%">:</td>
                <td style="" width="73%"><?php echo $dataReceipt['CPGid']?> / <?php echo $dataReceipt['BatchLabel']?></td>
            </tr>
            <tr>
                <td style="">Desa / Kelompok Tani</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo $dataReceipt['VillageName']?> / <?php echo $dataReceipt['GroupName']?></td>
            </tr>
            <tr>
                <td style="">Kecamatan / Kabupaten</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo $dataReceipt['SubDistrict']?> / <?php echo $dataReceipt['District']?></td>
            </tr>
            <tr>
                <td style="">Tanggal Kegiatan</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo tanggal_indo($dataReceipt['TrainingStart'])?> / <?php echo tanggal_indo($dataReceipt['TrainingEnd'])?></td>
            </tr>
            </table>
        <?php break;
        case 'cadre': ?>
            <table width="100%" border="0" class="tabelHeaderDetail">
            <tr>
                <td style="" width="22%">Batch</td>
                <td style="text-align:center;" width="5%">:</td>
                <td style="" width="73%"><?php echo $dataReceipt['BatchLabel']?></td>
            </tr>
            <tr>
                <td style="">Propinsi</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo $dataReceipt['ProvinceLabel']?></td>
            </tr>
            <tr>
                <td style="">Kabupaten</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo $dataReceipt['DistrictLabel']?></td>
            </tr>
            <tr>
                <td style="">Tanggal Kegiatan</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo tanggal_indo($dataReceipt['TrainingStart'])?> / <?php echo tanggal_indo($dataReceipt['TrainingEnd'])?></td>
            </tr>
            </table>
        <?php break;
        case 'master': ?>
            <table width="100%" border="0" class="tabelHeaderDetail">
            <tr>
                <td style="" width="22%">Batch</td>
                <td style="text-align:center;" width="5%">:</td>
                <td style="" width="73%"><?php echo $dataReceipt['BatchLabel']?></td>
            </tr>
            <tr>
                <td style="">Propinsi</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo $dataReceipt['ProvinceLabel']?></td>
            </tr>
            <tr>
                <td style="">Kabupaten</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo $dataReceipt['DistrictLabel']?></td>
            </tr>
            <tr>
                <td style="">Tanggal Kegiatan</td>
                <td style="text-align:center;">:</td>
                <td style=""><?php echo tanggal_indo($dataReceipt['TrainingStart'])?> / <?php echo tanggal_indo($dataReceipt['TrainingEnd'])?></td>
            </tr>
            </table>
        <?php break;
    }
    ?>
    <!-- TABLE HEADER DETAIL (END) -->
    <br />

    <!-- TABLE LIST DATA (BEGIN) -->
    <!--
        - Kalau fullpage with header and footer, baris data maksimal adalah 26 baris.
        - Kalau dengan header, tanpa footer, baris data maksdimal adalah 37 baris.
        - Kalau tanpa header, dengan footer, baris data maksdimal adalah 34 baris.
        - Kalau tanpa header dan footer, baris data maksimal adalah 46 baris.
    -->

    <table width="100%" border="0" class="tabelListData">
    <tr class="headerNya">
        <th width="4%">No.</th>
        <th width="46%">Nama Barang</th>
        <th width="5%">Qty</th>
        <th width="10%">Unit</th>
        <th width="35%">Keterangan</th>
    </tr>
    <?php
    if($dataActGoods[0]['Barang'] != ""){
        $jumlahData = count($dataActGoods);
        $htmlGantiHalaman = '</table>
                            </div>

                            <div class="page">
                                <table width="100%" border="0" class="tabelListData">
                                <tr class="headerNya">
                                    <th width="4%">No.</th>
                                    <th width="46%">Nama Barang</th>
                                    <th width="5%">Qty</th>
                                    <th width="10%">Unit</th>
                                    <th width="35%">Keterangan</th>
                                </tr>';

        /*
        Cari dengan acuan jumlah datanya,  apakah termasuk
            - 1 halaman (dengan header dan footer)
            - 2 halaman (halaman 1 dengan header tanpa footer, dan halaman 2 tanpa header dengan footer)
            - full halaman (halaman pertama dengan header tanpa footer, halaman terakhir tanpa header dengan footer, halaman di antaranya tanpa header dan footer)
        */

        //1 halaman
        if($jumlahData <= 26){
            $printKategori = '1 halaman';
        }else{
            //71 -> 37 + 34
            if($jumlahData > 26 && $jumlahData <= 71){
                $printKategori = '2 halaman';
            }else{
                $printKategori = 'full halaman';
            }
        }

        switch ($printKategori) {
            case '1 halaman':
                for ($i=1; $i <= count($dataActGoods); $i++) {
                    echo '<tr>
                        <td style="text-align:center;">'.$i.'.</td>
                        <td>'.$dataActGoods[$i-1]['Barang'].'</td>
                        <td style="text-align:center;">'.$dataActGoods[$i-1]['Qty'].'</td>
                        <td style="text-align:center;">'.$dataActGoods[$i-1]['Unit'].'</td>
                        <td>'.$dataActGoods[$i-1]['Remark'].'</td>
                    </tr>';
                }
            break;
            case '2 halaman':
                if($jumlahData <= 37){
                    $dikurangiSatu = true;
                }else{
                    $dikurangiSatu = false;
                }

                for ($i=1; $i <= count($dataActGoods); $i++) {
                    echo '<tr>
                        <td style="text-align:center;">'.$i.'.</td>
                        <td>'.$dataActGoods[$i-1]['Barang'].'</td>
                        <td style="text-align:center;">'.$dataActGoods[$i-1]['Qty'].'</td>
                        <td style="text-align:center;">'.$dataActGoods[$i-1]['Unit'].'</td>
                        <td>'.$dataActGoods[$i-1]['Remark'].'</td>
                    </tr>';

                    //cek apakah jumlah data nya sampai 37
                    if($i > 26){
                        if($dikurangiSatu == true){
                            if(($jumlahData - 1) == $i){
                                echo $htmlGantiHalaman;
                            }
                        }else{
                            if($i == 37){
                                echo $htmlGantiHalaman;
                            }
                        }
                    }
                }
            break;
            case 'full halaman':
                //cetak halaman 1
                for ($i=1; $i <= 37; $i++) {
                    echo '<tr>
                        <td style="text-align:center;">'.$i.'.</td>
                        <td>'.$dataActGoods[$i-1]['Barang'].'</td>
                        <td style="text-align:center;">'.$dataActGoods[$i-1]['Qty'].'</td>
                        <td style="text-align:center;">'.$dataActGoods[$i-1]['Unit'].'</td>
                        <td>'.$dataActGoods[$i-1]['Remark'].'</td>
                    </tr>';
                }
                echo $htmlGantiHalaman;

                //cetak halaman seterusnya
                $increBarisPerHalaman = 0;
                $sisaData = $jumlahData - 37;
                $dikurangiSatu = false;

                for ($i=38; $i <= count($dataActGoods); $i++) {
                    echo '<tr>
                        <td style="text-align:center;">'.$i.'.</td>
                        <td>'.$dataActGoods[$i-1]['Barang'].'</td>
                        <td style="text-align:center;">'.$dataActGoods[$i-1]['Qty'].'</td>
                        <td style="text-align:center;">'.$dataActGoods[$i-1]['Unit'].'</td>
                        <td>'.$dataActGoods[$i-1]['Remark'].'</td>
                    </tr>';
                    $increBarisPerHalaman++;
                    $sisaData--;

                    if($dikurangiSatu == false){
                        if($increBarisPerHalaman > 34){
                            //cek sisa datanya apakah lebih besar dari 12 baris (46 - 34)
                            if($sisaData < 12){
                                $dikurangiSatu = true;
                            }
                        }
                    }

                    if($dikurangiSatu == true){
                        if(($jumlahData - 1) == $i){
                            echo $htmlGantiHalaman;
                            $increBarisPerHalaman = 0;
                            $dikurangiSatu = false;
                        }
                    }

                    if($increBarisPerHalaman == 46){
                        echo $htmlGantiHalaman;
                        $increBarisPerHalaman = 0;
                        $dikurangiSatu = false;
                    }

                }
            break;
        }
    }
    ?>
    </table>
    <br /><br />
    <!-- TABLE LIST DATA (END) -->

    <!-- BAGIAN FOOTER (BEGIN) -->
    <div class="labelLokasiTanggal"><?php echo $dataReceipt['Location']?>, <?php echo tanggal_indo(date("Y-m-d"))?></div>
    <table class="tabelTtd" width="100%" border="0">
    <tr>
        <td width="33%" align="center">
        Yang Menyerahkan,
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
        <strong style="text-decoration: underline;"><?php echo $dataReceipt['LabelGiver']['nama']?></strong><br />
        <strong><?php echo $dataReceipt['LabelGiver']['posisi']?></strong>
        </td>
        <td width="34%">&nbsp;</td>
        <td width="33%" align="center">
        Yang Menerima / Perwakilan,
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
        <strong style="text-decoration: underline;"><?php echo $dataReceipt['LabelReceiver']['nama']?></strong><br />
        <strong>Petani Andalan / Perwakilan</strong>
        </td>
    </tr>
    </table>
    <table class="tabelTtd" width="100%" border="0">
    <tr>
        <td width="33%" align="center">&nbsp;</td>
        <td width="34%" align="center">
            Diketahui Oleh,
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
            <strong style="text-decoration: underline;"><?php echo $dataReceipt['LabelKnownBy']['nama']?> / <?php echo $dataReceipt['LabelKnownBy2']['nama']?></strong><br />
            <strong><?php echo $dataReceipt['LabelKnownBy']['posisi']?> / <?php echo $dataReceipt['LabelKnownBy2']['posisi']?></strong>
        </td>
        <td width="33%" align="center">&nbsp;</td>
    </tr>
    </table>
    <!-- BAGIAN FOOTER (END) -->
</div>

</body>
</html>