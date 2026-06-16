<html>
<style>
  body {
    margin: 0;
    padding: 0;
    background-color: #FAFAFA;
    font: 10pt "verdana";
}
* {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}
.page {
    width: 21cm;
    height:27.7cm;
    padding: 1cm;
    margin: 1cm auto;
    border: 1px #D3D3D3 solid;
    border-radius: 5px;
    background: white;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    position: relative;
}
.subpage {
    padding: 1cm;
    border: 5px red solid;
    height: 256mm;
    outline: 2cm #FFEAEA solid;
}

@page {
    size: A4;
    margin: 0;
}
@media print {
    .page {
        margin: 0;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
    }
    .page-break	{ display: block; page-break-before: always; }
}

    h4 {
        text-align: center;
        font-size: 10pt;
        font-family: verdana;
    }

    .title {
        font-size: 10pt;
        font-family: verdana;
        font-weight: normal;
    }

    .table-print {
        font-size: 10pt;
        font-family: verdana;
        font-weight: normal;
        padding: 0px;
        margin: 0px;
        border-top: 1.5px solid #333333;
        border-left: 1.5px solid #333333;
        border-collapse: collapse;
    }

    .table-print th {
        text-align: center;
        border-right: 1.5px solid #333333;
        border-bottom: 1.5px solid #333333;
        padding: 5px;
        margin:0px;


    }

    .table-print td {
        text-align: left;
        border-right: 1.5px solid #333333;
        border-bottom: 1.5px solid #333333;
        padding: 1px;
        margin:0px;
        font-weight: normal;
    }
    td{
      font-size:11pt;
      font-family: Calibri;
    }

    .header {
        font-size: 12pt;
        font-family: Calibri;
        font-weight: bold;
        text-align: center;
        line-height: 5px;
    }
    .content {
        font-size: 11pt;
        font-family: Calibri;
    }
    .table-white td {
        font-size: 11pt;
        font-family: Calibri;
        font-weight: normal;
    }

    .footer {
        position: absolute;
        bottom: 12;
        right: 0;
        float: right;
        height: 35px;
        margin: 0px 10px 0px 0px;
        color: #000;
        text-align: right;
        padding: 20px 30px;
        width: 180px;

        font-size: 10pt;
        font-family: Calibri;
        font-weight: normal;
        font-style: italic;
    }

    .list ul {
        list-style: none;
        margin-left: 0;
        padding-left: 0;
    }

    .list li {
        padding-left: 1em;
        text-indent: -1em;
    }

    .list li:before {
        content: "-";
        padding-right: 5px;
    }
</style>
<title>KONTRAK SERTIFIKASI</title>
<body>
    <div class="page">
        <div class="header">
            <p>PERJANJIAN KERJASAMA PROGRAM <?php echo $ims['Certification'];?> SERTIFIKASI</p>
            <p>ANTARA</p>
            <p>PEMEGANG SERTIFIKAT <?php echo $ims['Certification'];?> DENGAN PRODUSEN (PETANI)</p>
        </div>
        <br>

        <div class="content">
            <table class="table-white" width="100%" cellspacing="4" cellpadding="4">
                <tr>
                    <td width="30%">Nama Unit Usaha</td>
                    <td>: <?php echo $ims['CertHolderOrgName'];?></td>
                </tr>
                <tr>
                    <td>Diwakili oleh</td>
                    <td>: <?php echo $ims['CertHolderResponsible'];?></td>
                </tr>
                <tr>
                    <td colspan="2">Dalam hal ini bertindak sebagai Pemegang Sertifikat. <br><br></td>
                </tr>
                <tr>
                    <td>Nama Petani</td>
                    <td>: <?php echo $farmer['FarmerName'];?></td>
                </tr>
                <tr>
                    <td>ID Petani</td>
                    <td>: <?php echo $farmer['FarmerID'];?></td>
                </tr>
                <tr>
                    <td>No. KTP</td>
                    <td>: <?php echo $farmer['NoKTP'];?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: <?php echo $farmer['Address'];?></td>
                </tr>
                <tr>
                    <td>Nama Kelompok</td>
                    <td>: <?php echo $farmer['GroupName'];?></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Nama Bank</td>
                    <td>: <?php echo $farmer['BankName'];?></td>
                </tr>
                <tr>
                    <td>Nomor Rekening Bank</td>
                    <td>: <?php echo $farmer['AccountNumber'];?></td>
                </tr>
                <tr>
                    <td>Nama Pemegang Rekening</td>
                    <td>: <?php echo $farmer['AccountBeneficiary'];?></td>
                </tr>
                <tr>
                    <td colspan="2">Dalam hal ini bertindak sebagai Produsen (petani) peserta program sertifikasi.<br></td>
                </tr>
            </table>

            <p>Kedua belah pihak setuju untuk menandatangani kontrak ini yang bertujuan untuk:</p>
            <ol style="margin-top:-8px;">
                <li>Sebagai aturan yang mengikat kedua belah pihak yang bersepakat;</li>
                <li>Melindungi hak dan kewajiban para pihak;</li>
                <li>Sebagai pedoman bagi kedua belah pihak yang bersepakat;</li>
                <li>Memenuhi persyaratan sertifkasi <?php echo $ims['Certification']?>.</li>
            </ol>
            <br>

            <p>Oleh karena itu kedua belah pihak sepakat untuk menerima hak dan kewajiban sebagai berikut:</p>
            <p><b>I. Kewajiban Pemegang Sertifikat sebagai bagian dari sistem manajemen internal (IMS)</b></p>
            <ol style="margin-top:-8px;">
                <li>Berpartisipasi dan berperan aktif dalam sistem manajemen internal dengan pendampingan dari Koltiva;</li>
                <li>Bersama IMS mengelola organisasi dan anggota-anggota kelompok untuk memenuhi dan patuh terhadap seluruh standar-standar <?php echo $ims['Certification']?>;</li>
                <li>Mengelola sistem ketelusuran, pencatatan pembelian, pengendalian mutu, penanganan pasca panen, pengangkutan dan penjualan kakao sertifikasi <?php echo $ims['Certification']?> dengan menggunakan aplikasi Palmoiltrace dan bersedia dilatih serta didampingi oleh Koltiva;</li>
                <li>Bersama IMS melaksanakan Audit Internal kepada seluruh anggota petani minimal 1 kali setahun dengan didampingi oleh Manajer IMS.</li>
            </ol>

        </div>

        <br><br><br><br><br>
        <footer class="footer">Halaman 1 dari 4</footer>
    </div>
    <div class="page-break"></div>

    <div class="page">
        <div class="header">
            <p>PERJANJIAN KERJASAMA PROGRAM SERTIFIKASI</p>
            <hr>
        </div>
        <br>

        <div class="content">

            <p><b>II. Hak dan kewajiban Pemegang Sertifikat kepada anggota kelompok</b></p>
            <ol style="margin-top:-8px;">
                <li>Berkomitmen untuk memberikan dukungan kepada petani anggota kelompok selama 4 tahun;</li>
                <li>Memberikan dukungan dan informasi kepada petani untuk dapat mematuhi dan menjalankan standar-standar sertifikasi <?php echo $ims['Certification']?>;</li>
                <li>Memberikan informasi harga kakao secara jelas dan transparan kepada seluruh anggota kelompok;</li>
                <li>Membeli biji kakao anggota kelompok dengan harga yang kompetitif sesuai dengan kualitas;</li>
                <li>Memberikan informasi premium dan tata cara pembayarannya secara transparan kepada seluruh anggota kelompok.</li>
            </ol>


            <p><b>III. Hak Pemegang Sertifikat</b></p>
            <ol style="margin-top:-8px;">
                <li>Membeli kakao <?php echo $ims['Certification']?> sesuai dengan harga sesuai kualitas yang disepakati;</li>
                <li>Berhak menolak membeli kakao anggota petani apabila terbukti dicampur dengan kakao non-sertifikasi <?php echo $ims['Certification']?>.</li>
            </ol>

            <p><b>IV. Kewajiban Petani sebagai anggota Kelompok Sertifikasi</b></p>
            <ol style="margin-top:-8px;">
                <li>Bersedia untuk mematuhi dan menjalankan pedoman perilaku <?php echo $ims['Certification']?>;</li>
                <li>Bersedia mematuhi dan menjalankan seluruh peraturan IMS terkait ketertelusuran produk kakao;</li>
                <li>Mengikuti sekolah lapang praktek pertanian kakao yang baik dan sertifikasi <?php echo $ims['Certification']?>;</li>
                <li>Bersedia dikunjungi dan diperiksa kebunnya oleh auditor internal dan auditor eksternal;</li>
                <li>Memberikan informasi yang akurat tentang kondisi kebun dan produksi kepada IMS, auditor internal dan auditor eksternal;</li>
                <li>Bersedia menerima sanksi apabila melanggar peraturan IMS atau standar pedoman perilaku <?php echo $ims['Certification']?>;</li>
                <li>Menjamin untuk melakukan penjualan biji kakao dari kebun bersertifikasi kepada mitra pembelian yang ditunjuk oleh pemegang sertifikat;</li>
                <li>Tidak melakukan pencampuran biji sertifikasi dan non-sertifikasi dengan alasan apapun;</li>
                <li>Melakukan pencatatan produksi dan penjualan secara teratur.</li>
            </ol>

            <p><b>V. Hak Petani</b></p>
            <ol style="margin-top:-8px;">
                <li>Menerima dukungan dari pemegang sertifikat melalui Koltiva dan Swisscontact berupa pelatihan dan pendampingan;</li>
                <li>Memiliki hak untuk mengajukan banding terhadap keputusan IMS;</li>
                <li>Memiliki hak untuk meminta salinan dokumen atau catatan-catatan terkait transaksi pembelian atau penjualan kakao <?php echo $ims['Certification']?>;</li>
                <li>Memiliki hak untuk menerima premium sebagai insentif partisipasi dan kepatuhan dalam menjalankan standar sertifikasi <?php echo $ims['Certification']?>.</li>
            </ol>

        </div>

        <br><br>
        <footer class="footer">Halaman 2 dari 4</footer>
    </div>
    <div class="page-break"></div>

    <div class="page">

        <div class="header">
            <p>PERJANJIAN KERJASAMA PROGRAM SERTIFIKASI</p>
            <hr>
        </div>
        <br>

        <div class="content">
            <p><b>VII. Premium</b></p>

            <ol style="margin-top:-8px;">
                <li>Petani sebagai produsen menerima premi sesuai dengan jumlah total biji yang dijual pada unit pembelian yang telah ditentukan oleh pemegang sertifikat;</li>
                <li>Catatan yang digunakan untuk menentukan jumlah besaran premi yang akan diterima petani adalah catatan yang dipegang oleh pemegang sertifikat;</li>
                <li>Apabila terjadi selisih pencatatan antara petani dan pemegang sertifikat, maka catatan yang digunakan adalah catatan pemegang sertifikat;</li>
                <li>
                    Komposisi pembagian premium adalah sebagai berikut:
                    <table width="100%" class="TabelNoBorder">
                    <tr>
                        <td valign="top" width="41%">Petani:</td>
                        <td>
                            60 % atau kurang lebih Rp 600,-/Kg tergantung - nilai tukar yang berlaku
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Pemegang Sertifikat dan Unit Pembelian:</td>
                        <td>40% atau kurang lebih Rp 400,-/kg tergantung - nilai tukar yang berlaku</td>
                    </tr>
                    </table>
                </li>
                <li>Untuk pembagian premium, petani mendapatkan 60% dari seluruh total premium. Bagian dari petani tidak dapat diganggu gugat. Sedangkan sisa 40% akan diberikan kepada Pemegang Sertifikat. Bagian 40% ini dapat dibagi lagi kepada Unit Pembelian dan kolektor di jaringan pemegang sertifikat sesuai dengan kesepakatan.</li>
            </ol>

            <p><b>VIII. Tata cara pembayaran Premium</b></p>
            <ol style="margin-top:-8px;">
                 <li>Premium dapat dibayarkan secara tunai maupun non-tunai ataupun dengan metode lainnya yang telah disepakati Bersama;</li>
                <li>Petani disarankan untuk memiliki rekening bank atau rekening tabungan di bank yang telah ditunjuk dalam kerjasama untuk menerima premium yang akan dibayarkan oleh Pemegang Sertifikat;</li>
                <li>Jika petani tidak memiliki rekening bank maka pembayaran premium dapat dilakukan secara tunai melalui Pemegang Sertifikat dan diawasi oleh IMS. Pembayaran premium dapat pula dilakukan dengan metode lainnya sesuai dengan kesepakatan antara petani dan Pemegang Sertifikat. Dalam hal pembayaran premium diberikan secara non-tunai, kegagalan dalam menyampaikan informasi nomor rekening kepada Pemegang Sertifikat dapat menyebabkan penundaan pembayaran premium;</li>
                <li>Laporan penggunaan premium yang dikelola Pemegang Sertifikat akan dilaporkan secara terbuka kepada petani anggota program sertifikasi setiap tahun pada waktu yang disepakati;</li>
                <li>Nomor rekening bank yang telah disampaikan kepada Pemegang Sertifikat untuk menerima premium dapat diubah melalui pemberitahuan secara tertulis kepada Pemegang Sertifikat. </li>
            </ol>

            <p><b>IX. Jangka waktu Perjanjian</b></p>
            <ol style="margin-top:-8px;">
                <li>
                    Perjanjian ini berlaku selama 1 tahun dan dapat diperbarui setiap tahun. Perjanjian ini dapat batal dalam kondisi:
                    <ul>
                        <li>Pada saat ada ketidakpatuhan terhadap persyaratan dalam perjanjian ini oleh salah satu pihak;</li>
                        <li>Apabila produsen (petani) memutuskan untuk mengundurkan diri secara sukarela dari Program Sertifikasi <?php echo $ims['Certification']?>.</li>
                    </ul>
                </li>
            </ol>

        </div>

        <br><br><br>
        <footer class="footer">Halaman 3 dari 4</footer>
    </div>
    <div class="page-break"></div>

    <div class="page">

        <div class="header">
            <p>PERJANJIAN KERJASAMA PROGRAM SERTIFIKASI</p>
            <hr>
        </div>
        <br>

        <div class="content">
            <p><b>X. Sanksi-sanksi ketidakpatuhan terhadap kontrol poin utama</b></p>
            <ol style="margin-top:-8px;">
                <li>Apabila seorang produsen tidak memenuhi kriteria keanggotaan sesuai standar sertifikasi <?php echo $ims['Certification']?> maka keanggotaan petani tersebut tidak sah;</li>
                <li>Keangggotaan petani yang tidak dapat disahkan, akan ditangguhkan dan hasil produksi kebunnya tidak dapat dibeli sebagai produk sertifikasi;</li>
                <li>Konfirmasi tersebut akan dilakukan oleh IMS melalui inspeksi;</li>
                <li>Bila terjadi sebuah penipuan yang tak terbantahkan, sebuah halangan yang disengaja saat Inspeksi atau sebuah penolakan untuk mematuhi kontrak, produsen akan dikeluarkan dari Program Sertifikasi <?php echo $ims['Certification']?> untuk 1 (satu) periode.</li>
            </ol>

            <br>
            <p>Dengan tanda tangan yang dibubuhkan dibawah, tiap pihak menyatakan menerima, mematuhi dan melaksanakan semua bagian dan isi dari kontrak ini.</p>
            <br>

            <table width="100%" class="TabelNoBorder">
            <tr>
                <td valign="top" width="10%">Tempat</td>
                <td>: ..........................................................</td>
            </tr>
            <tr><td colspan="2" height="12"></td></tr>
            <tr>
                <td valign="top">Tanggal</td>
                <td>: ..........................................................</td>
            </tr>
            </table>

            <br>
            <p><b>Ditanda tangani Oleh:</b></p>
            <br>

            <table width="100%" class="TabelNoBorder">
            <tr>
                <td width="3%">&nbsp;</td>
                <td valign="top" width="60%">
                    <b>Pemegang Sertifikat</b>
                    <br>
                    <?php echo $ims['Certification']?>

                    <br><br><br><br><br><br><br><br>

                    <?php echo $ims['CertHolderResponsible'];?>
                </td>
                <td valign="top">
                    <b>Pemegang Sertifikat</b>

                    <br><br><br><br><br><br><br><br><br>

                    <?php echo $farmer['FarmerName'];?>
                </td>
            </tr>
            </table>

        </div>

        <br><br><br><br><br><br>
        <footer class="footer">Halaman 4 dari 4</footer>

    </div>
    <div class="page-break"></div>

</body>
</html>