<?php
/**
 * @Author: nikolius
 * @Date:   2017-01-20 11:05:00
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
    <div class="titlePaper"><?php echo $labelTrainingHeader?> untuk Penerima Perorangan</div>
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
        - Kalau fullpage with header and footer, baris data maksimal adalah 13 baris.
        - Kalau dengan header, tanpa footer, baris data maksdimal adalah 19 baris.
        - Kalau tanpa header, dengan footer, baris data maksdimal adalah 17 baris.
        - Kalau tanpa header dan footer, baris data maksimal adalah 24 baris.
    -->
    <table width="100%" border="0" class="tabelListData">
    <tr class="headerNya">
        <th rowspan="2" width="4%">No.</th>
        <th rowspan="2" width="22%">Peserta</th>
        <th colspan="<?php echo count($dataListSrc['dataHeader'])?>">Nama Peralatan / Perlengkapan</th>
        <th rowspan="2" width="15%">TTD</th>
    </tr>
    <tr class="headerNya">
        <?php
        for ($i=0; $i < count($dataListSrc['dataHeader']); $i++) {
            echo '<th>'.$dataListSrc['dataHeader'][$i]['labelHeader'].'</th>';
        }
        ?>
    </tr>

    <?php
    if($dataListSrc['dataList'][0]['Peserta'] != ""){

        $jumlahData = count($dataListSrc['dataList']);
        $htmlGantiHalaman = '</table>
                            </div>

                            <div class="page">
                                <table width="100%" border="0" class="tabelListData">
                                <tr class="headerNya">
                                    <th rowspan="2" width="4%">No.</th>
                                    <th rowspan="2" width="22%">Peserta</th>
                                    <th colspan="'.count($dataListSrc['dataHeader']).'">Nama Peralatan / Perlengkapan</th>
                                    <th rowspan="2" width="15%">TTD</th>
                                </tr>
                                <tr class="headerNya">';
        for ($i=0; $i < count($dataListSrc['dataHeader']); $i++) {
            $htmlGantiHalaman .= '<th>'.$dataListSrc['dataHeader'][$i]['labelHeader'].'</th>';
        }
        $htmlGantiHalaman .= '</tr>';

        /*
        Cari dengan acuan jumlah datanya,  apakah termasuk
            - 1 halaman (dengan header dan footer)
            - 2 halaman (halaman 1 dengan header tanpa footer, dan halaman 2 tanpa header dengan footer)
            - full halaman (halaman pertama dengan header tanpa footer, halaman terakhir tanpa header dengan footer, halaman di antaranya tanpa header dan footer)
        */

        //1 halaman
        if($jumlahData <= 13){
            $printKategori = '1 halaman';
        }else{
            //36 -> 19 + 17
            if($jumlahData > 13 && $jumlahData <= 36){
                $printKategori = '2 halaman';
            }else{
                $printKategori = 'full halaman';
            }
        }

        switch ($printKategori) {
            case '1 halaman':
                for ($i=1; $i <= count($dataListSrc['dataList']); $i++) {
                    echo '<tr height="40"><td style="text-align:center;">'.$i.'.</td><td>'.$dataListSrc['dataList'][$i-1]['Peserta'].'</td>';
                    for ($j=0; $j < count($dataListSrc['dataHeader']); $j++) {
                        if($dataListSrc['dataList'][$i-1]['GoodsID'.$dataListSrc['dataHeader'][$j]['ReceiptGoodsID']] == "1"){
                            $isChecked = 'checked=""';
                        }else{
                            $isChecked = '';
                        }
                        echo '<td align="center"><input type="checkbox" '.$isChecked.' /></td>';
                    }
                    echo '<td></td></tr>';
                }
            break;

            case '2 halaman':
                if($jumlahData <= 19){
                    $dikurangiSatu = true;
                }else{
                    $dikurangiSatu = false;
                }

                for ($i=1; $i <= count($dataListSrc['dataList']); $i++) {
                    echo '<tr height="40"><td style="text-align:center;">'.$i.'.</td><td>'.$dataListSrc['dataList'][$i-1]['Peserta'].'</td>';
                    for ($j=0; $j < count($dataListSrc['dataHeader']); $j++) {
                        if($dataListSrc['dataList'][$i-1]['GoodsID'.$dataListSrc['dataHeader'][$j]['ReceiptGoodsID']] == "1"){
                            $isChecked = 'checked=""';
                        }else{
                            $isChecked = '';
                        }
                        echo '<td align="center"><input type="checkbox" '.$isChecked.' /></td>';
                    }
                    echo '<td></td></tr>';

                    //cek apakah jumlah data nya sampai 37
                    if($i > 13){
                        if($dikurangiSatu == true){
                            if(($jumlahData - 1) == $i){
                                echo $htmlGantiHalaman;
                            }
                        }else{
                            if($i == 19){
                                echo $htmlGantiHalaman;
                            }
                        }
                    }
                }
            break;

            case 'full halaman':
                //cetak halaman 1
                for ($i=1; $i <= 19; $i++) {
                    echo '<tr height="40"><td style="text-align:center;">'.$i.'.</td><td>'.$dataListSrc['dataList'][$i-1]['Peserta'].'</td>';
                    for ($j=0; $j < count($dataListSrc['dataHeader']); $j++) {
                        if($dataListSrc['dataList'][$i-1]['GoodsID'.$dataListSrc['dataHeader'][$j]['ReceiptGoodsID']] == "1"){
                            $isChecked = 'checked=""';
                        }else{
                            $isChecked = '';
                        }
                        echo '<td align="center"><input type="checkbox" '.$isChecked.' /></td>';
                    }
                    echo '<td></td></tr>';
                }
                echo $htmlGantiHalaman;

                //cetak halaman seterusnya
                $increBarisPerHalaman = 0;
                $sisaData = $jumlahData - 19;
                $dikurangiSatu = false;

                for ($i=20; $i <= count($dataListSrc['dataList']); $i++) {
                    echo '<tr height="40"><td style="text-align:center;">'.$i.'.</td><td>'.$dataListSrc['dataList'][$i-1]['Peserta'].'</td>';
                    for ($j=0; $j < count($dataListSrc['dataHeader']); $j++) {
                        if($dataListSrc['dataList'][$i-1]['GoodsID'.$dataListSrc['dataHeader'][$j]['ReceiptGoodsID']] == "1"){
                            $isChecked = 'checked=""';
                        }else{
                            $isChecked = '';
                        }
                        echo '<td align="center"><input type="checkbox" '.$isChecked.' /></td>';
                    }
                    echo '<td></td></tr>';
                    $increBarisPerHalaman++;
                    $sisaData--;

                    if($dikurangiSatu == false){
                        if($increBarisPerHalaman > 17){
                            //cek sisa datanya apakah lebih besar dari 7 baris (24 - 17)
                            if($sisaData < 7){
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

                    if($increBarisPerHalaman == 24){
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
    <!-- TABLE LIST DATA (END) -->
    <br /><br />

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
        <strong style="text-decoration: underline;"><?php echo $dataReceipt['LabelPartGiver']['nama']?></strong><br />
        <strong><?php echo $dataReceipt['LabelPartGiver']['posisi']?></strong>
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
        <strong style="text-decoration: underline;"><?php echo $dataReceipt['LabelPartReceiver']['nama']?></strong><br />
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
            <strong style="text-decoration: underline;"><?php echo $dataReceipt['LabelPartKnownBy']['nama']?> / <?php echo $dataReceipt['LabelPartKnownBy2']['nama']?></strong><br />
            <strong><?php echo $dataReceipt['LabelPartKnownBy']['posisi']?> / <?php echo $dataReceipt['LabelPartKnownBy2']['posisi']?></strong>
        </td>
        <td width="33%" align="center">&nbsp;</td>
    </tr>
    </table>
    <!-- BAGIAN FOOTER (END) -->

</div>

</body>
</html>