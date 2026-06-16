<div id="templatemo_container_wrapper">
    <div class="page">
        <div id="templatemo_container">
            <h3 style="text-align: center;">
                KONTRAK KESEPAKATAN KERJASAMA PROGRAM <?php echo $data['Certification'] ?> SERTIFIKASI <br/>
                ANTARA <br/>
                PEMEGANG SERTIFIKAT DENGAN PRODUSEN (PETANI)
            </h3>

            <table style="width: 100%" class="table">
                <tr>
                    <td style="width: 30%"><strong>Nama Organisasi</strong></td><td style="width: 5px">:</td>
                    <td><strong><?php echo $Coop ?><!-- <?php echo $data['CertificationHolder'] ?> --></strong></td>
                </tr>
                <tr>
                    <td>Di wakili oleh</td><td>:</td>
                    <td>IMS Manager</td>
                </tr>
                <tr>
                    <td>Nama IMS Manager</td><td>:</td>
                    <td><!-- <?php echo $ims['name'] ?> --></td>
                </tr>
                <tr>
                    <td>No. KTP</td><td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Alamat</td><td>:</td>
                    <td><!-- <?php echo $ims['Address'] ?> --></td>
                </tr>
                <tr>
                    <td colspan="3">Dalam hal ini bertindak sebagai pemegang sertifikat</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Nama Petani</strong></td><td>:</td>
                    <td><strong><?php echo $data['FarmerName'] ?></strong></td>
                </tr>
                <tr>
                    <td>ID Petani</td><td>:</td>
                    <td><?php echo $data['FarmerID'] ?></td>
                </tr>
                <tr>
                    <td>No. KTP</td><td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Alamat</td><td>:</td>
                    <td><?php echo $data['Address'] ?></td>
                </tr>
                <tr>
                    <td><strong>Nama Kelompok</strong></td><td>:</td>
                    <td><strong><?php echo $data['GroupName'] ?></strong></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Nama Bank/Koperasi</strong></td><td>:</td>
                    <td><?php echo $bank['BankName'] ?></td>
                </tr>
                <tr>
                    <td>Nomor Rekening Bank/Koperasi</td><td>:</td>
                    <td><?php echo $bank['AccountNumber'] ?></td>
                </tr>
                <tr>
                    <td>Nama Pemegang Rekening</td><td>:</td>
                    <td><?php echo $bank['AccountBeneficiary'] ?></td>
                </tr>
                <tr>
                    <td colspan="3">Dalam hal ini bertindak sebagai produsen (Petani) peserta program Sertifikasi.</td>
                </tr>
            </table>

            <p>Penandatanganan kontrak ini mengikat kedua belah pihak untuk memenuhi persyaratan khusus dalam sertifikasi <?php echo $data['Certification'] ?> dan kedua belah pihak menerima kewajiban sebagai berikut:</p>

            <p><strong>1.Hak dan Kewajiban organisasi sebagai pemegang sertifikat:</strong></p>
            <div style="padding-left: 20px;">
                <p>Mengelola Internal Manajemen System (IMS) dan mengatur operasi yang layak untuk memperoleh dan mempertahankan sertifikasi <?php echo $data['Certification'] ?></p>
                <p>Melaksanakan kegiatan sosialisasi tentang <strong>Code of Conduct</strong> <?php echo $data['Certification'] ?> secara jelas dan transparan kepada Petani/Kelompok tani.</p> 
                <p>Melakukan <strong>Penilaian RESIKO</strong> dan Pelatihan <strong>STANDAR Internal</strong> untuk Kelompok Tani/Petani </p>
                <p>Melaksanakan sebuah program training yang berkelanjutan secara independen atau bekerja sama dengan para ahli eksternal;</p>
                <p>Mempromosikan penerapan praktek-praktek pertanian yang baik (GAP) dan tanggung jawab social (GSP) dan lingkungan (GEP) sebagaimana ditentukan di program <?php echo $data['Certification'] ?></p>
                <p>Mengelola sistem ketelusuran, pencatatan pembelian, pengendalian mutu, penanganan, pengangkutan dan penjualan kakao sertifikasi <?php echo $data['Certification'] ?></p>
                <p>Melaksanakan Audit Internal kepada seluruh anggota petani minimal 1 kali setahun. </p>
                <p>Mengelola informasi Produsen secara rahasia, jujur dan transparan;</p>
                <p>Menunjuk badan sertifikasi (lembaga audit) yang akan melaksanakan inspeksi dan audit eksternal;</p>
            </div>
        </div>
    </div>
    <div class="page-break"></div>
    <div class="page">
        <div id="templatemo_container">        
            <div style="padding-left: 20px;">
                <p>Memastikan bahwa anggota Kelompok Tani/Petani telah mendapatkan informasi mengenai hak-hak buruh dan tidak melarang adanya interaksi dengan pihak-pihak eksternal (misal: LSM, serikat pekerja); </p>
                <p>Melatih semua Kelompok Tani/Petani dan pekerja yang menangani dan menerapkan produk perlindungan tanaman yang beresiko bahaya dalam hal kesehatan dan keselamatan;</p>
                <p>Memastikan Kelompok Tani/Petani memiliki pengetahuan mengenai kesehatan, keselamatan dan risiko lingkungan dalam proses produksi;</p>
                <p>Mentransfer premi sertifikasi tepat waktu melalui transfer bank kepada rekening Petani/Produsen atau rekening bank koperasi;</p>
                <p>Memiliki hak untuk mengakhiri kontrak dengan terlebih dahulu memberikan penjelasan alasan kuat atas pengakhiran kontrak.</p>
            </div>

            <p><strong>2.Hak dan Kewajiban produsen (Petani)</strong></p>
            <div style="padding-left: 20px;">
                <p>Mengetahui dan memenuhi standar internal (yang merupakan bagian dari kontrak);</p>
                <p>Melaksanakan criteria dalam pedoman pelaksanaan <?php echo $data['Certification'] ?> dalam proses produksi dan manajemen kebunnya;</p>
                <p>Mengikuti pelatihan yang berkelanjutan dan mengaplikasikan semua rekomendasi-rekomendasi teknis;</p>
                <p>Memberikan informasi yang akurat kepada inspektur internal maupun eksternal dan memberikan akses kepada mereka ke unit produksi (kebun) dan dokumen-dokumen yang ada;</p>
                <p>Menerima sanksi dari Struktur IMS dan eksternal jika terdapat temuan ketidakpatuhan terhadap CoC dan melaksanakan langkah-langkah perbaikan sebagaimana yang minta oleh IMS;</p>
                <p>Produsen hanya dapat menjual biji sertifikasi <?php echo $data['Certification'] ?> yang bersumber dari kebun kakao bersertifikasi <?php echo $data['Certification'] ?>;</p>
                <p>Menjual biji kakao bersertifikasi <?php echo $data['Certification'] ?> pada unit pembelia yang terdaftar pada pemegang sertifikat;</p>
                <p>Melaporkan segala perubahan atau variasi kondisi produksi di kebun;</p>
                <p>Petani memiliki hak untuk banding atas keputusan yang diambil oleh manajer atau komite persetujuan dan sanksi; </p>
                <p>Petani/Produsen kelompok berhak untuk meminta dan menerima sebuah salinan dokumen-dokumen dan catatan-catatan;</p>
                <p>Petani bersedia untuk melindungi hutan, species yang terancam dan habitat alami dan memperkuat kanekaragaman hayati;</p>
                <p>Premi dibayar secara non-tunai untuk mengurangi risiko penanganan uang tunai dan pembayaran yang lebih efisien. Oleh karena itu petani perlu mempunyai rekening di bank atau rekening tabungan di Koperasi sebagai rekening untuk menerima premi. Jika petani tidak memiliki rekening tersebut, mereka harus membukanya dalam waktu 10 (sepuluh) hari setelah penandatanganan kontrak ini dan menginformasikan kepada pemegang sertifikat (koperasi) tentang nomor rekening yang telah mereka miliki. (Jika pemegang sertifikat tidak diberitahu tentang nomor rekening, itu akan menyebabkan penundaan pembayaran premi);</p>
                <p>Nomor rekening dapat diubah melalui pemberitahuan secara tertulis kepada pemegang sertifikat (Koperasi);</p>
                <p>Memiliki hak untuk mengakhir kontrak dengan terlebih dahulu memberikan penjelasan alasan pengakhiran kontrak.</p>
            </div>
        </div>
    </div>
    <div class="page-break"></div>
    <div class="page">
        <div id="templatemo_container">              
            <p><strong>3.Jangka waktu kontrak</strong></p>
            <p>Kontrak ini memiliki jangka waktu 1 (satu) tahun. Kontrak secara otomatis terbaharui jika tidak ada salah satu pihak yang menghentikannya. Kontrak ini dapat batal dalam kondisi:</p>
            <div style="padding-left: 20px;">
                <p>Pada saat ada ketidak patuhan terhadap persyaratan dalam kontrak ini oleh salah satu pihak;</p>
                <p>Apabila produsen (petani) memutuskan untuk mengundurkan diri secara suka rela dari Program Sertifikasi <?php echo $data['Certification'] ?>.</p>
            </div>

            <p><strong>4.Sanksi-sanksi pada saat muncul ke tidak patuhan terhadap angka control utama</strong></p>
            <div style="padding-left: 20px;">
                <p>Dalam kasus seorang anggota baru, produsen tersebut tidak disahkan;</p>
                <p>Apabila produsen telah disahkan, produsen tersebut akan ditangguhkan dan hasil panennya tidak akan dibeli sebagai produk sertifikasi;</p>
                <p>Penangguhan dapat dibatalkan setelah ada konfirmasi langkah-langkah perbaikan dari sebuah inspeksi baru.</p>
            </div>

            <p>Bila terjadi sebuah penipuan yang tak terbantahkan, sebuah halangan yang disengaja saat inspeksi atau sebuah penolakan untuk mematuhi kontrak, produsen akan <strong>dikeluarkan dari Program Sertifikasi <?php echo $data['Certification'] ?></strong>, baik secara sengaja atau hanya dalam 1 (satu) periode.</p>

            <p>Dengan tanda tangan yang dibubuhkan dibawah, tiap pihak menyatakan menerima semua bagian dan isi dari kontrak ini dan secara penuh akan dipatuhi dan dilaksanakan.</p>

            <table style="width: 100%">
                <tr>
                    <td style="width: 33%">Tempat</td>
                    <td colspan="2">: <?php echo $data['District'] ?></td>
                </tr>
                <tr>
                    <td style="width: 33%">Tanggal</td>
                    <td colspan="2">: <?php echo indonesian_date($ims['ICSDate'], 'j F Y', '') ?></td>
                </tr>
                <tr>
                    <td colspan="3"><center><strong>Di tanda tangani Oleh:</strong></center></td>
                </tr>
                <tr>
                    <td style="width: 33%">
                        <center>
                            <strong>Pemegang Sertifikat</strong>
                            <br/><br/><br/><br/>
                            <strong>(<?php echo $Coop ?>)</strong><br/>
                            <!-- <?php echo $ims['CoopName'] ?> -->
                        </center>
                    </td>
                    <td style="width: 33%"></td>
                    <td style="width: 33%">
                        <center>
                            <strong>Produsen (Petani)</strong>
                            <br/><br/><br/><br/>
                            <strong>(<?php echo $data['FarmerName'] ?>)</strong><br/>
                            <?php echo $data['FarmerID'] ?>
                        </center>
                    </td>
                </tr>
            </table>
            <p>Saya memberi kuasa pemegang sertifikat untuk mentransfer premi sertifikasi ke rekening tersebut di atas</p>

            <p style="text-align: center;">
                <?php echo $data['FarmerName'] ?><br/>
                <?php echo $data['FarmerID'] ?>
            </p>
        </div>  
    </div>
</div>
<div class="page-break-after"></div>