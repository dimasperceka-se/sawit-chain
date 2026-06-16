<?php
/**
 * @Author: nikolius
 * @Date:   2017-08-10 17:06:33
 */
?>
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
        <div id="judulSurat"><?php echo strtoupper(lang('printProjBg Project Background'))?></div>
        <br />
        <div id="judulSurat"><?php echo lang('printProjBg Survey relating to the development of a Fresh Palm Bunches traceability system for smallholders (“Project”)')?></div>

        <div id="containerSurat">
        <br /><br />
        <table class="tabelNoBorder" width="90%" border="0" cellpadding="2" align="center">
        <tr>
            <td width="25%" valign="top">
                <?php echo lang('printProjBg Survey Operator')?>
            </td>
            <td width="75%">
                <?php echo lang('printProjBg Survey Operator Text')?>
            </td>
        </tr>
        <tr>
            <td height="15" colspan="2"></td>
        </tr>
        <tr>
            <td valign="top">
                <?php echo lang('printProjBg Project Description')?>
            </td>
            <td>
            <?php echo lang('printProjBg Project Description Text')?>
            </td>
        </tr>
        <tr>
            <td height="15" colspan="2"></td>
        </tr>
        <tr>
            <td valign="top">
                <?php echo lang('printProjBg Scope of Survey')?>
            </td>
            <td>
            <?php echo lang('printProjBg Scope of Survey Text')?>
            </td>
        </tr>
        <tr>
            <td height="15" colspan="2"></td>
        </tr>
        <tr>
            <td valign="top">
                <?php echo lang('printProjBg Data which is not going to be shared')?>
            </td>
            <td>
            <?php echo lang('printProjBg Data which is not going to be shared Text')?>
            </td>
        </tr>
        </table>

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