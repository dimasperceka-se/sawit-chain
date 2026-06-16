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
                    <img src="https://dptwplzs7m8x9.cloudfront.net/web/logo/koltiva_logo.svg" style="max-width:90%; max-height:90%; max-width:120px;">
                </td>
            </tr>
        </table>
        <div align="right" class="dateStamp">Jakarta, <?php echo tanggal_dwibahasa(date('Y-m-d'))?></div>

        <br />
        <div id="judulSurat"><?php echo lang('Smallholder Declaration')?></div>

        <div id="containerSurat">
            <p>
                <?php echo lang('Kepada')?> :<br />
                PT Koltiva<br />
                RA Mampang Office, 6th Floor, Jl. Mampang Prapatan Raya No.66 A - 66 B, Tegal Parang, Mampang Prapatan , Jakarta Selatan, DKI Jakarta 12790
            </p>

            <p>
                <?php echo lang('rspoDoc Salam Surat')?>
            </p>
            <br />

            <table class="tabelNoBorder" width="100%">
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
            <br />

            <p>
                <b><?php echo lang('By signing this Smallholder Declaration, I assert that')?> :</b>
            </p>

            <ul style="list-style-type: none;">
                <li style="margin-bottom:0.5em"><b><?php echo lang('1.	I recognise the importance of sustainable production.')?></b></li>
                <li style="margin-bottom:0.5em"><b><?php echo lang('2.	I will join a farmer group to pursue group certification of the RSPO ISH Standard and comply with the principles and their relevant criteria and indicators.')?></b></li>
                <li style="margin-bottom:0.5em"><b><?php echo lang('3.	I will provide the following information to my and group manager')?>:</b></li>
                <ul style="list-style-type: none;">
                    <li style="margin-bottom:0.5em"><?php echo lang('a.	All land holdings')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('b.	Location (coordinates) of all plots currently planted with oil palm')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('c.	Information on all plots converted and planted with oil palm after 2005 (through use of the simplified combined HCV-HCS approach for Smallholders)')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('d.	Any plots located on steep slopes')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('e.	Any plots located on peat')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('f.	Details on plans for replanting and expansion of oil palm')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('g.	Any existing land disputes')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('h.	Ownership and land use status')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('i.	Source of farm labour.')?></li>
                </ul>
                <li style="margin-bottom:0.5em"><b><?php echo lang('4.	I commit to the following')?>:</b></li>
                <ul style="list-style-type: none;">
                    <li style="margin-bottom:0.5em"><?php echo lang('a.	Continue to progress along the standard and meet the required milestones for progress')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('b.	Participate in trainings as required and actively participate in the group')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('c.	Ensure no forced labour on farm operations and end any existing forced labour.')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('d.	Pay national level minimum wage')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('e.	Respect the rights of workers to file a complaint')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('f.	Provide safe working conditions and facilities')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('g.	No discrimination, harassment or abuse on the farm')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('h.	Ensure no child labour on farm operations and end any existing child labour')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('i.	Not clearing or acquiring land from indigenous peoples, local communities, or other users without their free, prior and informed consent (FPIC), based on a simplified FPIC approach')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('j.	Resolve any existing disputes')?></li>
                </ul>
            </ul>
        </div>
    </div>
    <div class="page-break"></div>
    <div class="page">
        <div id="containerSurat">
            <ul style="list-style-type: none;">
                <ul style="list-style-type: none;">
                    <li style="margin-bottom:0.5em"><?php echo lang('k.	No new planting or no expansion of existing farms in primary forests HCV areas, HCS forests, in riparian areas, or on steep slopes (more than degrees or as in National Interpretation)')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('l.	Protect HCVs and HCS forests through the precautionary practices approach')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('m.	No new planting on peat and replanting on peat only in areas with low risk of flooding and saline intrusion')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('n.	Use of best management practices for oil palm on peat')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('o.	No burning for preparing land or pest control')?></li>
                    <li style="margin-bottom:0.5em"><?php echo lang('p.	Minimise and control erosion.')?></li>
                </ul>
            </ul>

            <p>
                <b><?php echo lang('Smallholder Benefits')?></b>
            </p>

            <p>
                <?php echo lang('By adopting sustainable farming practices and complying to the RSPO ISH Standard, I understand I will have')?> :
            </p>
            <ul style="list-style-type: none;">
                <li style="margin-bottom:0.5em"><?php echo lang('1.	Knowledge on how to optimise productivity and yields by implementing the good and sustainable agricultural practices that I have been trained on;')?></li>
                <li style="margin-bottom:0.5em"><?php echo lang('2.	Knowledge on how to trade and participate in the market for sustainable palm oil and manage my farm professionally and become financially sustainable;')?></li>
                <li style="margin-bottom:0.5em"><?php echo lang('3.	Structure as well as agency to be able to take the necessary steps towards a sustainable livelihood for my family and my community.')?></li>
            </ul>
            <p>
                <?php echo lang('I recognise I will have access to technical support and financial support as well as access to trade in the market for sustainable palm oil offered by the RSPO and its members, to enable me to realise the benefits of sustainable farming practices.')?>
            </p>
            
            <?php
                if($member_data['WillingnesCommitSignature'] != '') {
                    echo '<img src="'.$member_data['WillingnesCommitSignature'].'" alt="" width="200" />';
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
    </div>
</div>
<?php }?>