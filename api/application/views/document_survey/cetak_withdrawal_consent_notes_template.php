<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-11 15:10:17
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

        <br /><br />
        <div id="judulSurat"><?php echo lang('withConNo Withdrawal of Consent Letter')?></div>
        <br />
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
                <?php echo lang('withConNo par1')?>
            </p>

            <p>
                <?php echo lang('withConNo par2')?>
            </p>
            <br />

            <p>
                <?php echo lang('conNo persetujuan dibuat par5')?>
            </p>
            <?php
                if(file_exists($member_data['WithdrawalConsentSign'])) {
                    echo '<img src="'.$member_data['WithdrawalConsentSign'].'" onerror="this.onerror=null; this.src=\'\'" alt="" width="200" />';
                } else {
                    echo '<br /><br /><br /><br /><br />';
                }
            ?>
            <br /><br /><br /><br /><br />
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