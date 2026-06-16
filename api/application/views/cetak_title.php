<div style="background-color:#23BAB1;padding:8px;">
    <table class="noBorder tabelJudul" width="100%">
    <tr>
        <td width="35%" style="padding-right:5px;border-right: 1px dashed white;">
            <table width="100%">
                <tr>
                    <td width="30%"><?php echo strtoupper(lang('Survey Nr'))?></td>
                    <td>
                        <?php
                            if($SurveyNr == "") $SurveyNr = "-";
                        ?>
                        <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $SurveyNr;?>" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo strtoupper(lang('Date'))?></td>
                    <td>
                        <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php if (!empty($survey['InterviewDate'])) echo date('Y-m-d', strtotime($survey['InterviewDate']))?>" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo strtoupper(lang('Interviewer'))?></td>
                    <td>
                        <input type="text" style="width:100%;border: 1px solid white;" />
                    </td>
                </tr>
                <tr>
                    <td><?php echo strtoupper(lang('Job Title'))?></td>
                    <td>
                        <input type="text" style="width:100%;border: 1px solid white;" />
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%" style="vertical-align:top;padding-left:14px;">
            <h2 class="mainTitle"><?php echo $title ?>
            </h2>
            <table width="100%">
                <tr>
                    <td width="45%">
                        <span style="font-size:9.5px;"><?php echo strtoupper(lang('Farmer Group ID'))?></span><br />
                        <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['CPGid']?>" />
                    </td>
                    <td width="55%">
                        <span style="font-size:9.5px;"><?php echo strtoupper(lang('Name Of Farmer Group'))?></span><br />
                        <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['GroupName']?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-size:9.5px;"><?php echo strtoupper(lang('Farmer ID'))?></span><br />
                        <input class="inputTxtStrong" type="text" style="width:100%;border: 1px solid white;" value="<?php echo $farmer['FarmerID']?>" />
                    </td>
                    <td></td>
                </tr>
            </table>
        </td>
        <td width="15%">
            <img src="<?php echo base_url() ?>index.php/farmer/qrcode_generator/<?php echo $farmer['FarmerID'];?>/" style="width:100%;" />
        </td>
    </tr>
    </table>
</div>
<br />