<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-07 15:45:59
 */
?>
<?php foreach($member as $key => $member_data){?>
<div id="templatemo_container_wrapper">
    <div class="page">

        <table class="tabelNoBorder" width="100%" border="0" cellpadding="2">
            <tr>
                <td width="100%" align="right" style="vertical-align:middle;">
                    <img src="/assets/new/img/sawitchain-logo.png" style="max-width:90%; max-height:90%; max-width:120px;">
                </td>
            </tr>
        </table>
        <div align="right" class="dateStamp">Jakarta, <?php echo tanggal_dwibahasa(date('Y-m-d'))?></div>

        <br />
        <div id="judulSurat"><?php echo lang('conNo Surat Persetujuan')?></div>

        <div id="containerSurat">
            <p>
                <?php echo lang('Kepada')?> :<br />
                PT Sawitchain<br />
                Jakarta
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
            <br /><br /><br />

            <p>
                <?php echo lang('conNo dengan ini par1')?>
            </p>

            <p>
                <?php echo lang('conNo saya menginformasikan par2')?>
            </p>

            <p>
                <?php echo lang('conNo saya setuju par3')?>
            </p>

            <p>
                <?php echo lang('conNo persetujuan ini par4')?>
            </p>
            <br />

            <p>
                <?php echo lang('conNo persetujuan dibuat par5')?>
            </p>
            <?php
                if($member_data['WillingnesSignature'] != '') {
                    echo '<img src="'.$member_data['WillingnesSignature'].'" alt="" width="200" />';
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