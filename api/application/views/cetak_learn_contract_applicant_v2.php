<?php
/**
 * @Author: nikolius
 * @Date:   2018-04-11 10:57:41
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-04-11 11:08:42
 */
?>
<div class="page">

    <!--- MULAI TABEL UTAMA (BEGIN) -->
    <div id="mainContainer">

        <div id="wrapTabelHeader" style="background-color: #4C81E0;margin-bottom:15px;">
        <table id="tabelHeader" border="0">
            <tr>
                <td width="4%">&nbsp;</td>
                <td><img width="150" style="padding:6px;" id="photoPetani" src="<?php echo base_url().'images/Photo/no-user.jpg' ?>" /></td>
                <td width="75%" id="tdTitleMainTabel">
                    <h2>Surat Pernyataan</h2>
                    <h1>Keikutsertaan Sekolah Lapang</h1>
                </td>
                <td width="110" id="tdBarcode">
                    <div id="wrapQr">
                        <img src="<?php echo base_url() .  $QRCode ?>" width="100" />
                        <!-- <img src="<?php echo base_url() ?>index.php/farmer/qrcode_generator/<?php echo $applicant['DisplayID'];?>/" width="100" /> -->
                        <div style="text-align:center;font-weight: normal;">APPLICANT ID :</div><?php echo $applicant['DisplayID'] ?>
                    </div>
                </td>
                <td width="4%">&nbsp;</td>
            </tr>
        </table>
        </div>

        <table width="100%">
            <tr>
                <td rowspan="2" width="7%" valign="top">
                    <img src="<?php echo base_url() ?>assets/css/learning-contract/icon-fp-basic-data.png" width="40" />
                </td>
                <td class="tdTitleMain" valign="top">
                   BASIC DATA
                </td>
            </tr>
            <tr>
                <td valign="top">

                    <table width="100%" class="tabelValueMain">
                    <tr>
                        <td width="25%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Nama')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['Fullname'];?>&nbsp;</div>
                        </td>
                        <td width="25%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Tgl. Lahir')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['DateOfBirth'];?>&nbsp;</div>
                        </td>
                        <td width="25%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Jenis Kelamin')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['Gender']=='m'?'Laki-laki':'Perempuan'?>&nbsp;</div>
                        </td>
                        <td width="25%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('No Telepon')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['PhoneNumber'];?>&nbsp;</div>
                        </td>
                    </tr>
                    </table>
                    <table width="100%" class="tabelValueMain">
                    <tr>
                        <td width="33%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Alamat')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['Address'];?>&nbsp;</div>
                        </td>
                        <td width="34%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Desa')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['Village'];?>&nbsp;</div>
                        </td>
                        <td width="33%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Kecamatan')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['SubDistrict'];?>&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="33%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Kabupaten')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['District'];?>&nbsp;</div>
                        </td>
                        <td width="34%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Provinsi')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['Province'];?>&nbsp;</div>
                        </td>
                        <td width="33%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Kelompok Tani')) ?></div>
                            <div class="tdValueMain"><?php echo $applicant['GroupName'];?>&nbsp;</div>
                        </td>
                    </tr>
                    </table>

                    <hr style="border-bottom: 1px dotted #4C81E0;" />

                </td>
            </tr>
        </table>

        <table border="0">
            <tr>
                <td width="17%">&nbsp;</td>
                <td><h3 id="titleListTabel">Dengan ini menyatakan :</h3></td>
            </tr>
        </table>
        <table class="tabelListItem" border="0">
        <tr>
            <td class="tdNoKiri" width="6%">1.</td>
            <td>
                Bersedia dengan sungguh-sungguh dan secara proaktif mengikuti kegiatan sekolah lapang yang dilakukan selama 3 (tiga) bulan dan kegiatan lanjutan komponen program lainnya;
            </td>
        </tr>
        <tr>
            <td class="tdNoKiri">2.</td>
            <td>
                Bersedia menerapkan dan mempraktekkan hasil yang di dapat selama kegiatan sekolah lapang di kebun masing-masing baik selama maupun pasca kegiatan sekolah lapang serta berbagi cerita sukses di kebun;
            </td>
        </tr>
        <tr>
            <td class="tdNoKiri">3.</td>
            <td>
                Berkomitmen untuk memperbaiki kebun dan meningkatkan produktivitas minimal 1 ton/ha/tahun;
            </td>
        </tr>
        <tr>
            <td class="tdNoKiri">4.</td>
            <td>
                Bersedia untuk berbagi pengetahuan yang didapat dalam sekolah lapang dengan petani lain di luar sekolah lapang;
            </td>
        </tr>
        <tr>
            <td class="tdNoKiri">5.</td>
            <td>
                Bersedia memberikan informasi progres pelatihan, data kebun, praktek kebun, produktivitas kebun, kendala-kendala selama berkebun serta data petani dan keluarganya;
            </td>
        </tr>
        <tr>
            <td class="tdNoKiri">6.</td>
            <td>
                Bersedia untuk mendukung keperluan dokumentasi program seperti penyediaan foto profil peserta, video kegiatan di kebun, serta kegiatan dokumentasi lainnya dimana data ini dapat dipublikasikan untuk mendukung Sustainable Palmoil Production Program SCPP;
            </td>
        </tr>
        <tr>
            <td class="tdNoKiri">7.</td>
            <td>
                Memberikan persetujuan penggunaan dan publikasi seluruh data dan informasi petani yang didapat selama maupun setelah program pelatihan kepada pihak lain sepanjang digunakan untuk peningkatan kesejahteraan petani dan keberlangsungan manfaat SCPP dalam jangka panjang.
            </td>
        </tr>
        </table>
        <br />

        <table border="0">
        <tr>
            <td width="6%">&nbsp;</td>
            <td colspan="2" class="tdTextTabel">
                Demikian surat pernyataan ini dibuat setelah memperoleh penjelasan sebelumnya, tanpa paksaan dari pihak manapun, serta agar dapat digunakan sebagaimana mestinya.
            </td>
        </tr>
        <tr>
            <td colspan="3" height="30"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td width="40%" class="tdTextTabel">
                Yang membuat pernyataan,
                <div class="txtTanggal"><?=$applicant['District'].', '.SetTanggal(date('Y-m-d'))?><br></div>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3" height="80"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td width="40%" class="tdTextTabel">
                <div class="txtTanggal">NAMA</div>
                <div style="font-weight:bold;width:150px;background-color:#F1F1F1;" class="txtTanggal"><?php echo $applicant['Fullname'];?></div>
            </td>
            <td>&nbsp;</td>
        </tr>
        </table>

    </div>
    <!--- MULAI TABEL UTAMA (END) -->

</div>
<div class="page-break"></div>