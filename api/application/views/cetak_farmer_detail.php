<table class="noBorder" width="100%" style="margin-bottom:6px;">
<tr>
    <td width="6%">
        <img src="<?php echo base_url()?>assets/css/nutrition/icon-farmer-data.png" width="35" />
    </td>
    <td width="94%" style="border-bottom: 1px dashed #23BAB1;">
        <h2 class="judulTabel"><?php echo strtoupper(lang('Farmer Basic Data'))?></h2>
    </td>
</tr>
</table>
<table class="noBorder" width="100%">
<tr>
    <td width="16%">
        <img src="<?php echo base_url().'image_process/resizeOtf?imagenya='.urlencode($farmer['Photo']).'&width=150&height=150'; ?>" />
    </td>
    <td width="22%" style="padding-left:7px;vertical-align: top;">
        <table width="100%">
        <tr>
            <td>
                <span style="font-size:9px;"><?php echo lang('Farmer Name')?></span><br />
                <input type="text" style="width:100%;" value="<?php echo $farmer['FarmerName']?>" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:9px;"><?php echo lang('Province')?></span><br />
                <input type="text" style="width:100%;" value="<?php echo $farmer['Provinsi']?>" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:9px;"><?php echo lang('SubDistrict')?></span><br />
                <input type="text" style="width:100%;" value="<?php echo $farmer['Kecamatan']?>" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:9px;"><?php echo lang('Dusun')?></span><br />
                <input type="text" style="width:100%;" value="<?php echo $farmer['alamat']?>" />
            </td>
        </tr>
        </table>
    </td>
    <td width="20%" style="padding-left:4px;vertical-align: top;">
        <table width="100%">
        <tr>
            <td>
                <?php
                //pemisahan tgl lahir
                $arrTemp = explode("-",$farmer['Birthdate']);
                $tahunL = $arrTemp[0];
                $bulanL = $arrTemp[1];
                $tglL = $arrTemp[2];
                ?>
                <span style="font-size:9px;"><?php echo lang('Tanggal Lahir')?></span><br />
                <input type="text" style="width:40px;" value="<?php echo $tglL?>" />
            </td>
        </tr>
        <tr>
            <td>
                <?php
                $provKode = substr($farmer['VillageID'],0,2);
                $kabKode = substr($farmer['VillageID'],0,4);
                $kecKode = substr($farmer['VillageID'],0,7);
                ?>
                <span style="font-size:9px;"><?php echo lang('Province Code')?></span><br />
                <input type="text" style="width:30px;" value="<?php echo $provKode?>" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:9px;"><?php echo lang('Sub District Code')?></span><br />
                <input type="text" style="width:90px;" value="<?php echo $kecKode?>" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:9px;"><?php echo lang('RT / RW')?></span><br />
                <!-- <input type="text" class="inputSatuan" />&nbsp;|&nbsp;<input type="text" class="inputSatuan" /> -->
                <input type="text" style="width:67px;" value="<?php echo $farmer['RtRw']?>" />
            </td>
        </tr>
        </table>
    </td>
    <td width="42%" style="vertical-align: top;">
        <table width="100%">
        <tr>
            <td width="48%">
                <span style="font-size:9px;"><?php echo lang('Bulan Lahir')?></span><br />
                <input type="text" style="width:40px;" value="<?php echo $bulanL?>" />
            </td>
            <td width="2%"></td>
            <td width="50%">
                <span style="font-size:9px;"><?php echo lang('Tahun Lahir')?></span><br />
                <input type="text" style="width:80px;" value="<?php echo $tahunL?>" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:9px;"><?php echo lang('District')?></span><br />
                <input type="text" style="width:100%" value="<?php echo $farmer['Kabupaten']?>" />
            </td>
            <td></td>
            <td>
                <span style="font-size:9px;"><?php echo lang('District Code')?></span><br />
                <input type="text" style="width:60px;" value="<?php echo $kabKode?>" />
            </td>
        </tr>
        <tr>
            <td>
                <span style="font-size:9px;"><?php echo lang('Village')?></span><br />
                <input type="text" style="width:100%" value="<?php echo $farmer['Desa']?>" />
            </td>
            <td></td>
            <td>
                <span style="font-size:9px;"><?php echo lang('Village Code')?></span><br />
                <input type="text" style="width:110px;" value="<?php echo $farmer['VillageID']?>" />
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3">
                <span style="font-size:9px;"><?php echo lang('Mobile Phone')?></span><br />
                <input type="text" style="width:50%;" value="<?php echo $farmer['HandPhone']?>" />
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>
<br />