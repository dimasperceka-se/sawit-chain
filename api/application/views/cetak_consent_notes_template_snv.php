<?php

/**
 * @Author: nikolius
 * @Date:   2017-11-06 10:38:15
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-11-06 10:51:55
 */
?>
<?php foreach($member as $key => $member_data){?>
<div id="templatemo_container_wrapper">
    <div class="page">

        <table class="tabelNoBorder" width="100%" border="0" cellpadding="2">
            <tr>
                <td width="100%" align="right" style="vertical-align:middle;">
                    <img src="https://dptwplzs7m8x9.cloudfront.net/web/logo/koltiva_logo.svg" style="max-width:90%; max-height:90%; max-width:120px;">
                </td>
            </tr>
        </table>
        <div align="right" class="dateStamp">Jakarta, <?php echo tanggal_dwibahasa(date('Y-m-d'))?></div>

        <br />
        <div id="judulSurat"><?php echo lang('conNo Surat Persetujuan')?></div>

        <div id="containerSurat">
            <p>
                <?php echo lang('Kepada')?> :<br />
                PT Koltiva<br />
                South Quarter Tower A, 5th Floor - Unit H, Jln RA. Kartini Kav. 8, Cilandak Barat, Jakarta 12430
            </p>

            <p>
                <?php echo lang('conNo Salam Surat')?>
            </p>
            <br />

            <table class="tabelNoBorder textTabel" width="100%">
            <tr>
                <td width="30%"><?php echo lang('Nama')?></td>
                <td width="3%">:</td>
                <td><?php echo $member_data['MemberName']?></td>
            </tr>
            <tr>
                <td><?php echo lang('Nomor KTP')?></td>
                <td>:</td>
                <td><?php echo $member_data['Nin']?></td>
            </tr>
            <tr>
                <td><?php echo lang('Alamat')?></td>
                <td>:</td>
                <td><?php echo $member_data['Address']?></td>
            </tr>
            </table>
            <br /><br />

            <p>
                <?php echo lang('conNo persetujuan snv part1')?>
            </p>

            <p>
                <?php echo lang('conNo persetujuan snv part2')?>
            </p>

            <p>
                <?php echo lang('conNo persetujuan snv part3')?>
            </p>

            <?php echo lang('conNo persetujuan snv part4')?>

            <br />
            <p>
                <?php echo lang('conNo persetujuan dibuat par5')?>
            </p>
            <?php
                if(file_exists('images/farmer_signature/'.$member_data['ProvinceID'].'/'.$member_data['FarmerSignature'])) {
                    echo '<img src="'.base_url().'images/farmer_signature/'.$member_data['ProvinceID'].'/'.$member_data['FarmerSignature'].'" width="200" />';
                } else {
                    echo '<br /><br /><br /><br /><br />';
                }
            ?>
            <p>
                ----------------------------------------- <br />
                <?php echo $member_data['MemberNameTtd']?><br />
                <?php echo tanggal_dwibahasa(date('Y-m-d'))?>
            </p>

        </div>

        <footer>
            <table border="0" width="100%" class="tabelNoBorder">
                <tr>
                    <td width="50%"></td>
                    <td align="right"><?php echo lang('Page')?> - 1</td>
                </tr>
            </table>
        </footer>
    </div>
    <div class="page-break"></div>
</div>
<?php }?>