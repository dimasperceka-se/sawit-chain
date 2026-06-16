<?php
/**
 * @Author: nikolius
 * @Date:   2016-06-03 17:11:28
 */
$baseurlnya = base_url()."api/";
// $baseurlnya = str_replace('http://','https://',$baseurlnya);
if ($js!='') {?>
    <script>
        $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
        <?$key = array_keys($action);
        for ($i=0;$i<sizeof($action);$i++) {?>
            var m_<?php echo $key[$i]?> = <?php echo ($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
            <?}?>
        </script>
<?php
}
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
   <meta charset="utf-8"/>
   <title><?php echo $titleNya;?></title>

   <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/print_beneficiary/print_beneficiary.css"/>
   <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/print_beneficiary/print_beneficiary-media.css" media="print"/>

   <script src="<?php echo $baseurlnya;?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
   <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACXVwWCJen2OZeCAEYdRxP_HEh7CkxOvs"></script>-->
   <!--<script src="<?php echo $baseurlnya;?>assets/js/gmap3.js"></script>-->
</head>
<body>

<style type="text/css">
.gm-style-cc:last-child {
    display: none !important;
    height: 0px !important;
}
a[title="Report errors in the road map or imagery to Google"] {
    display: none !important;
    height: 0px !important;
}
a[href="https://www.google.com/intl/en-US_US/help/terms_maps.html"] {
    display: none !important;
    height: 0px !important;
}
</style>
<div id='row-fluid'>
	<div class="page-head xs-pt-10 xs-pb-10">
        <div class="row">
            <div class="col-md-12">
                &nbsp;&nbsp;
                <div class="btn-group btn-hspace pull-right">
                    <button class="btn btn-primary pull-right" data-original-title=".btn .btn-info" data-placement="top" onclick="window.print()"><?php echo lang('Print Mill Profile') ?></button>
                </div>
                &nbsp;&nbsp;
                <div class="btn-group btn-hspace pull-right">
                    <button class="btn btn-default" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setRange()"><?php echo lang('Show') ?></button>
                </div>
                &nbsp;&nbsp;
                <div class="btn-group btn-hspace pull-right">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentYear"><?php echo $action['year'] ?></span>&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu" id="year">
                    </ul>
                </div>
                &nbsp;&nbsp;
            </div>            
        </div>
    </div>
</div>
<div class="page">
    <div>
        <table width="100%" style="margin-top:-20px;">
            <tr>
                <td width="33" valign="top">
                    <img src="<?php echo base_url() ?>api/assets/css/print_beneficiary/basic-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                    <?php echo strtoupper(lang('SUMMARY SUPPLIER')) ?> | <label id="summary_ttp"></label> % <?=lang("Traceable")?>
                </td>
                <td class="tdTitleMain" valign="top">
                    <label><?php echo strtoupper(lang('Approved By')) ?> |</label> <label id="approved_by"></label>
                </td>
            </tr>
        </table>
        <table class="table" width="100%" border="0">
            <thead>
                <tr>
                    <th><?php echo strtoupper(lang('Supplier Category'))?></th>
                    <th><?php echo strtoupper(lang('Total Supplier'))?></th>
                    <th><?php echo strtoupper(lang('TTP Percentage'))?></th>
                </tr>
            </thead>
            <tbody id="datacategorysupplier">
            </tbody>
        </table>
    </div>
    <div style="margin:0px 17px 0px 17px">
        <!-- Bagian ini untuk counter -->
        <div class="row">
            <div style="width:48%;float:left">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box5"></div>
                        <div class="desc">
                            <?=lang("Mapped Farmer")?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img style="width:50px" src="<?php echo base_url() ?>img/general/petani2.png" alt=""></div>
                </div>
                <table class="table" width="100%" border="0">
                    <tbody>
                        <tr>
                            <td><?=lang("Total Garden")?></td>
                            <td id="jml_kebun"></td>
                        </tr>
                        <tr>
                            <td><?=lang("Garden Area")?> (Ha)</td>
                            <td id="luas_kebun"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="width:48%;float:right">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box4"></div>
                        <div class="desc">
                            <?=lang("Supplier")?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img style="width:50px" src="<?php echo base_url() ?>img/general/petani2.png" alt=""></div>
                </div>
                <table class="table" width="100%" border="0">
                    <tbody>
                        <tr>
                            <td><?=lang("Total Garden")?></td>
                            <td id="jml_kebun_pemasok"></td>
                        </tr>
                        <tr>
                            <td><?=lang("Garden Area")?> (Ha)</td>
                            <td id="luas_kebun_pemasok"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>
        <table width="100%" style="margin-top:-20px;">
            <tr>
                <td width="33" valign="top">
                    <img src="<?php echo base_url() ?>api/assets/css/print_beneficiary/basic-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                    <?php echo strtoupper(lang('Sertifikasi')) ?>
                </td>
            </tr>
        </table>
        <table class="table" width="100%" border="0">
            <thead>
                <tr>
                    <th><?php echo strtoupper(lang('No.'))?></th>
                    <th><?php echo strtoupper(lang('Certification Type'))?></th>
                    <th><?php echo strtoupper(lang('Organizer'))?></th>
                    <th><?php echo strtoupper(lang('Start Year'))?></th>
                    <th><?php echo strtoupper(lang('End Year'))?></th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="5">No Data</td></tr>
            </tbody>
        </table>
    </div>
    <br>
    <div>
        <table width="100%" style="margin-top:-20px;">
            <tr>
                <td width="33" valign="top">
                    <img src="<?php echo base_url() ?>api/assets/css/print_beneficiary/basic-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                    <?php echo strtoupper(lang('Basic Data')) ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" valign="top">
                <table width="100%" class="tabelValueMain" style="margin-top:10px">
                    <tr>
                        <td width="30%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('ID')) ?></div>
                            <div class="tdValueMain" id="MillDisplayID"></div>
                        </td>
                        <td width="40%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Name')) ?></div>
                            <div class="tdValueMain" id="MillName"></div>
                        </td>
                        <td width="30%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Year Established')) ?></div>
                            <div class="tdValueMain" id="Year"></div>
                        </td>
                    </tr>
                </table>
                <table width="100%" class="tabelValueMain" style="margin-top:10px">
                    <tr>
                        <td width="40%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Legal Status')) ?></div>
                            <div class="tdValueMain" id="Status"></div>
                        </td>
                        <td width="20%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Nr of Staff')) ?></div>
                            <div class="tdValueMain" id="staffNr"></div>
                        </td>
                        <td width="40%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Province')) ?></div>
                            <div class="tdValueMain" id="Province"></div>
                        </td>
                    </tr>
                </table>
                <table width="100%" class="tabelValueMain" style="margin-top:10px">
                    <tr>
                        <td width="40%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('District')) ?></div>
                            <div class="tdValueMain" id="District"></div>
                        </td>
                        <td width="20%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Sub District')) ?></div>
                            <div class="tdValueMain" id="Subdistrict"></div>
                        </td>
                        <td width="40%">
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Village')) ?></div>
                            <div class="tdValueMain" id="Village"></div>
                        </td>
                    </tr>
                </table>
            </tr>
        </table>
    </div>
    <br>
    <br>
    <div>
        <table width="100%" style="margin-top:-20px;">
            <tr>
                <td width="33" valign="top">
                    <img src="<?php echo base_url() ?>api/assets/css/print_beneficiary/basic-data-icon.png" width="22" />
                </td>
                <td class="tdTitleMain" valign="top">
                    <?php echo strtoupper(lang('Lokasi & Kapasitas Mill')) ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" valign="top">
                <table width="47%" class="tabelValueMain" style="margin-top:10px;float:left">
                    <tr>
                        <td>
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Production Capacity (TBH)')) ?></div>
                            <div class="tdValueMain" id="ProductionCapacity"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Latitude')) ?></div>
                            <div class="tdValueMain" id="Latitude"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="tdLabelMain"><?php echo strtoupper(lang('Longitude')) ?></div>
                            <div class="tdValueMain" id="Longitude"></div>
                        </td>
                    </tr>
                </table>
                <table width="47%" class="tabelValueMain" style="margin-top:10px;float:right">
                    <tr>
                        <td>
                            <div class="tdLabelMain" id="imageMill"></div>
                        </td>
                    </tr>
                </table>
            </tr>
        </table>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>
<script>
    $('#year').on('click', '.list_year', function(event) {
        console.log($(this).data('name'));
        event.preventDefault();
        var start   = $('#year').val();
        var end     = $('#datepicker2').val();
        $('#currentYear').text($(this).data('name'));
        m_year = $(this).data('id');
    });
    function setRange() {
        var year    = m_year;
        console.log(year);
        if (m_year!=='') {
            url = "<?php echo site_url('mill/publication')?>";
            link(url+'?year='+m_year);
        }
    }
</script>