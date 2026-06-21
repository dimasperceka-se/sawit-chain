<?php
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "SawitChain";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon -->
    <link rel="icon" href="<?php echo base_url('assets/new/img/sawitchain-favicon.png'); ?>" />
    <title><?= $title ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/stroke-7/style.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/jquery.nanoscroller/css/nanoscroller.css"/>
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css"/> -->
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/datetimepicker/css/bootstrap-datetimepicker.min.css"/> -->
    <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel=stylesheet href="<?php echo base_url() ?>assets/plugins/datepicker/css/datepicker.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/style_green.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/custom.css?<?php echo date('YmdHis');?>" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/custom_ext.css" type="text/css"/> <!-- except -->
    <link rel="stylesheet" href="<?php echo base_url() ?>css/sbuttons.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url() ?>css/add.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/jquery.niftymodals/css/component.css"/>
    <style type="text/css">
.cover {
    background : url("<?php echo base_url() ?>img/ajax-loader.gif") no-repeat scroll center center;
}
    .title-additional-new{
        color: #ffffff;
        border-bottom: 1px solid #6d6b6b;
        font-size: 26px;
        font-weight: 300;
        margin: 0 25px 0;
        padding-bottom: 18px;
        padding-top: 25px;
        list-style: none;
    }
    </style>
    <script>
        var lang_arr = <?php echo json_encode($lang); ?>;
    </script>
    <script src="<?php echo base_url() ?>assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/highstock/highstock.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/highchart/highcharts-more.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/highchart/modules/exporting.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/highchart/modules/no-data-to-display.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/highchart/modules/solid-gauge.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/highchart/plugins/grouped-categories.js"></script>
    
    <!-- <script type="text/javascript" src="<?php echo base_url() ?>js/highstock/modules/exporting.js"></script> -->

    <?php $ver = $this->config->item('extjs_version')?>
    <link href="<?php echo base_url() ?>js/<?php echo $ver ?>/resources/css/ext-all-neptune.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo base_url() ?>js/<?php echo $ver ?>/ext-all.js" type="text/javascript" ></script>
    <!-- <script src="<?php echo base_url() ?>js/<?php echo $ver ?>/ext-all-dev.js" type="text/javascript" ></script> -->
    <script src="<?php echo base_url() ?>js/<?php echo $ver ?>/ext-theme-neptune.js" type="text/javascript" ></script>
    <script src="<?php echo base_url() ?>js/app.js" type="text/javascript" ></script>

   <!-- BoxSelect Source -->
   <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>js/plugins/boxselect/extjs-boxselect-all-debug.css" />
   <script type="text/javascript" src="<?php echo base_url() ?>js/plugins/boxselect/extjs-boxselect-debug.js"></script>
   <script type="text/javascript" src="<?php echo base_url() ?>js/plugins/jQueryRotate.js"></script>
   <script src="<?php echo base_url() ?>assets/lib/jquery.niftymodals/js/jquery.modalEffects.js" type="text/javascript"></script>

   <!-- Jquery Datatatbles -->
   <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/datatables/1.13.1/css/jquery.dataTables.css">  
   <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/datatables/plugins/fixedColumns/css/fixedColumns.dataTables.min.css">
   <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/datatables/plugins/buttons/css/buttons.dataTables.min.css">
   <script type="text/javascript" charset="utf8" src="<?php echo base_url() ?>assets/lib/datatables/1.13.1/js/jquery.dataTables.js"></script>
   <script type="text/javascript" charset="utf8" src="<?php echo base_url() ?>assets/lib/datatables/plugins/fixedColumns/js/dataTables.fixedColumns.min.js"></script>
   <script type="text/javascript" charset="utf8" src="<?php echo base_url() ?>assets/lib/datatables/plugins/buttons/js/dataTables.buttons.min.js"></script>

    <script>
/*(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-46635047-1', 'cocoatrace.com');
ga('send', 'pageview');
*/
var varjs =
{
    "config": {
        "base_url": "<?php echo base_url(); ?>/",
        "default_currency": "IDR",
        "extjs_version": "<?=$this->config->item('extjs_version')?>"
    }
};

var ktv = { "ktv_session":"<?php echo bin2hex($_SESSION['username']); ?>","ktv_fullname":"<?php echo $_SESSION['realname'] ?>" }

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', varjs.config.base_url+'js/'+varjs.config.extjs_version+'/ux');
Ext.Loader.setPath('Ext.ux.DataView', varjs.config.base_url+'js/'+varjs.config.extjs_version+'/ux/DataView/');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*',
    'Ext.ux.grid.FiltersFeature',
    'Ext.form.Panel',
    'Ext.tab.*',
    'Ext.window.*',
    'Ext.tip.*',
    'Ext.layout.container.Border',
    'Ext.ux.GMapPanel',
    'Ext.util.*',
    'Ext.view.View',
    'Ext.ux.DataView.DragSelector',
    'Ext.ux.DataView.LabelEditor',
    'Ext.ux.NumericField',
    'Ext.ux.form.BoxSelect',
    'Ext.ux.form.field.BoxSelect'
    ]);

 Highcharts.setOptions({
        exporting: {
            url: "<?php echo base_url() ?>highchart_export/index.php"
            ,buttons: {
                contextButton: {
                    menuItems: [{
                        text: 'Export to PNG',
                        onclick: function () {
                            this.exportChart();
                        }
                    }, {
                        text: 'Export to SVG',
                        onclick: function () {
                            this.exportChart({
                                type: 'image/svg+xml'
                            });
                        },
                        separator: false
                    }]
                }
            }
        },
        credits: {
            enabled: false
        },
    });

 /* Custom grid */
 var h = window.innerHeight - 180;
 Ext.override(Ext.grid.GridPanel, {
    autoScroll : true,
    autoHeight : true,
    maxHeight  : h,
    style : 'border:1px solid #CCC;margin-top:4px;',
    cls : 'Sfr_GridNew',
    loadMask : true,
    selType : 'rowmodel',
    enableColumnHide : false,
    initComponent: function () {
        var me = this;
        this.callParent();
    }
});

Ext.override(Ext.menu.Menu, {
    cls : 'Sfr_ConMenu'
});


</script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/gValidation.js"></script>
</head>
<body>
<div class="am-wrapper am-white-header">
    <nav class="navbar navbar-default navbar-fixed-top am-top-header">
        <div class="container-fluid">
            <div class="navbar-header">
                <div class="page-title"><span id="page_title"></span></div>
                <a href="#" class="am-toggle-left-sidebar navbar-toggle collapsed" onclick="return false;"><span class="icon-bar"><span></span><span></span><span></span></span></a><a href="#" class="navbar-brand"></a>
            </div>
            <a href="#" class="am-toggle-right-sidebar"><span class="icon s7-menu2"></span></a><a href="#" data-toggle="collapse" data-target="#am-navbar-collapse" class="am-toggle-top-header-menu collapsed"><span class="icon s7-angle-down"></span></a>

            <div id="am-navbar-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right am-user-nav">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle">
                            <?php
                                $fotoProfile = "";
                                if($_SESSION['Photo_staff'] != ""){
                                    if(IsUrlImageExist($this->config->item('CTCDN').'/'.$_SESSION['Photo_staff']) == true) {
                                        $fotoProfile = $this->config->item('CTCDN').'/'.$_SESSION['Photo_staff'];
                                    } else {
                                        $fotoProfile = $this->config->item('api_base_url').$_SESSION['Photo_staff'];
                                    }
                                }else{
                                    if($_SESSION['Gender'] == "f"){
                                        $fotoProfile = $this->config->item('api_base_url').'images/default_photo/female-business.jpg';
                                    }else{
                                        $fotoProfile = $this->config->item('api_base_url').'images/default_photo/male-business.jpg';
                                    }
                                }
                            ?>
                            <img src="<?php echo $fotoProfile; ?>"><span class="user-name"><?php echo $_SESSION['realname'] ?></span><span class="angle-down s7-angle-down"></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href="<?php echo site_url('home/profile') ?>" onclick="link(this.href); return false;"> <span class="icon s7-user"></span>My profile</a></li>
                            <li><a href="<?php echo site_url('system/login/logout') ?>"> <span class="icon s7-power"></span>Sign Out</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav am-nav-right">
                    <h2 class="xs-mt-10" id="breadcrumb_title"></h2>
                    <ol class="breadcrumb custom-breadcrumb">
                        <li id="first-breadcrumb"></li>
                        <li id="second-breadcrumb"></li>
                    </ol>
                </ul>
                <ul class="nav navbar-nav navbar-right am-icons-nav">
                    <!-- <li class="dropdown"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle"><span class="icon s7-comment"></span></a>
                        <ul class="dropdown-menu am-messages">
                            <li>
                                <div class="title">Messages<span class="badge">3</span></div>
                                <div class="list">
                                    <div class="am-scroller nano">
                                        <div class="content nano-content">
                                            <ul>
                                                <li class="active"><a href="#">
                                                    <div class="logo"><img src="<?php echo base_url() ?>assets/img/avatar2.jpg"></div>
                                                    <div class="user-content"><span class="date">April 25</span><span class="name">Jessica Caruso</span><span class="text-content">Request you to be a part of the same so that we can work...</span></div>
                                                </a></li>
                                                <li><a href="#">
                                                    <div class="logo"><img src="<?php echo base_url() ?>assets/img/avatar3.jpg"></div>
                                                    <div class="user-content"><span class="date">March 18</span><span class="name">Joel King</span><span class="text-content"> We wish to extend the building.</span></div>
                                                </a></li>
                                                <li><a href="#">
                                                    <div class="logo"><img src="<?php echo base_url() ?>assets/img/avatar4.jpg"></div>
                                                    <div class="user-content"><span class="date">January 3</span><span class="name">Claire Sassu</span><span class="text-content"> We the ladies of a block are wiling to join together to set up a catering...</span></div>
                                                </a></li>
                                                <li><a href="#">
                                                    <div class="logo"><img src="<?php echo base_url() ?>assets/img/avatar5.jpg"></div>
                                                    <div class="user-content"><span class="date">January 2</span><span class="name">Emily Carter</span><span class="text-content"> Request you to be a part of the same so that we can work...</span></div>
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer"><a href="#">View all messages</a></div>
                            </li>
                        </ul>
                    </li> -->
                    <?php
                    if($sync_button)
                    {
                         ?>
                         <li style="margin-top:7%;" >
                             <?=$last_synced?>
                        </li>
                        <li class="dropdown"><a href="#"  onclick="popupCenter('<?=$this->config->item('api')."/cooperatives/sync/1"?>', 'myPop1',450,450);" href="javascript:void(0);" data-toggle="dropdown" role="button" aria-expanded="false"><span class="icon s7-refresh-cloud"></span><span class="indicator hidden" id="notif-indicator"></span></a>

                        </li>
                        <script>
                        function popupCenter(url, title, w, h) {
                            var left = (screen.width/2)-(w/2);
                            var top = (screen.height/2)-(h/2);
                            return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
                        }
                        </script>
                        <?php
                    }
                    ?>
                    <li class="dropdown"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle"><span class="icon s7-bell"></span><span class="indicator hidden" id="notif-indicator"></span></a>
                        <ul class="dropdown-menu am-notifications">
                            <li>
                                <div class="title">Notifications<span class="badge" id="NumNotifHeader"><?php echo $NotifNum ?></span></div>
                                <div class="list">
                                    <div class="am-scroller nano">
                                        <div class="content nano-content">
                                            <ul class="ListNotifHeader">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="footer"><a href="#">View all notifications</a></div> -->
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown">

                        <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle" id="usergroup-indicator-header-icon">
                            <span class="icon s7-share"></span><span class="indicator hidden" id="usergroup-indicator-header"></span>
                        </a>
                        <ul class="dropdown-menu am-connections" id="UserGroupPanel" style="height:450px;">

                        <li>
                        <div class="list">
                            <div class="am-scroller nano" style="height:450px;">
                                <div class="content nano-content">
                                    <ul>

                            <!--
                            <li>
                                <div class="title" style="background-color: #B3B3B3;color:white;font-weight:bold;"><?php echo lang('Project') ?></div>
                                <div class="list">
                                    <div class="content">
                                        <ul>
                                            <?php
                                            if($dataUserProject[0]['ProjID'] != ""){
                                                foreach ($dataUserProject as $key => $row) {
                                                    echo '
                                                    <li>
                                                        <div class="logo"><span class="icon s7-users"></span></div>
                                                        <div class="field"><span>'.$row['ProjName'].'</span>
                                                            <div class="pull-right">
                                                                <div class="switch-button switch-button-sm">';
                                                                if($row['ProjID'] == $_SESSION['ProjID']){
                                                                    echo '<input type="checkbox" checked="" ><span><label></label></span>';
                                                                }else{
                                                                    echo '<a href="'.site_url('system/profile/change_project/'.$row['ProjID']).'"><input type="checkbox"><span><label></label></span></a>';
                                                                }
                                                                echo '</div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    ';
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            -->

                            <!-- USER AFFILIATES (Begin)  -->
                            <?php if($_SESSION['UserAff'] != ""){ ?>
                            <li>
                                <div class="title" style="background-color: #B3B3B3;color:white;font-weight: bold;"><?php echo lang('User Affiliates') ?></div>
                                <div class="list">
                                    <div class="content">
                                        <ul>
                                            <?php
                                                $arrUserAff = explode(",",$_SESSION['UserAff']);
                                                $dataComboUserAff = array();
                                                for ($i=0; $i < count($arrUserAff); $i++) {
                                                    $sql = "SELECT UserId,UserRealName,UserName FROM sys_user WHERE UserId='{$arrUserAff[$i]}' AND UserActive='Yes' LIMIT 1";
                                                    $query = $this->db->query($sql);
                                                    $dataRow = $query->row_array();

                                                    $dataComboUserAff[$i]['id'] = $dataRow['UserId'];
                                                    $dataComboUserAff[$i]['label'] = $dataRow['UserRealName'].'<br>('.$dataRow['UserName'].')';

                                                    if($dataRow['UserId'] != ""){
                                                        echo '
                                                        <li>
                                                            <div class="logo"><span class="icon s7-users"></span></div>
                                                            <div class="field"><span style="font-size:9px;">'.$dataComboUserAff[$i]['label'].'</span>
                                                                <div class="pull-right">
                                                                    <div class="switch-button switch-button-sm">';
                                                                    if($_SESSION['userid'] == $dataRow['UserId']){
                                                                        echo '<input type="checkbox" checked="" ><span><label></label></span>';
                                                                    }else{
                                                                        echo '<a href="'.site_url('system/profile/change_user_affiliate/'.$dataComboUserAff[$i]['id']).'"><input type="checkbox"><span><label></label></span></a>';
                                                                    }
                                                                    echo '</div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        ';
                                                    }
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <?php } ?>
                            <!-- USER AFFILIATES (End)  -->

                            <li>
                                <div class="title" style="background-color: #B3B3B3;color:white;font-weight: bold;"><?php echo lang('Group') ?></div>
                                <div class="list">
                                    <div class="content">
                                        <ul>
                                            <?php if ($groups): ?>
                                            <?php foreach ($groups as $key => $group): ?>
                                            <li>
                                                <div class="logo"><span class="icon s7-users"></span></div>
                                                <div class="field"><span style="font-size:9px;"><?php echo $group['name'] ?></span>
                                                    <div class="pull-right">
                                                        <div class="switch-button switch-button-sm">
                                                            <?php if ($current_group['name']==$group['name']): ?>
                                                            <input type="checkbox" checked="" ><span><label></label></span>
                                                            <?php else: ?>
                                                            <a href="<?php echo site_url('system/profile/change_group/'.$group['id']) ?>"><input type="checkbox"><span><label></label></span></a>
                                                            <?php endif ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php endforeach ?>
                                            <?php endif ?>
                                        </ul>
                                    </div>
                                </div>
                            </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        </li>

                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="am-left-sidebar">
        <div class="content">
            <div class="am-logo"></div>
            <ul class="sidebar-elements">
                <?php
                    foreach ($menus as $key => $value) {
                        foreach ($value['child'] as $key2 => $value2) {
                            /* if (empty($value2['child_flag_type'])) {
                                $value2['child_flag_type'] = $value['MenuName'];
                            } */

                            $menus[$key]['childNew'][trim($value2['child_flag_type'])][] = $value2;
                            
                        }
                    }
                ?>
                <?php foreach ($menus as $menu): ?>
                    <?php
                    $href = '#';
                    $onclick = '';
                    if (!empty($menu['MenuModule'])) {
                        $href = site_url($menu['MenuModule']);
                        $onclick = 'link(this.href);';
                    } elseif(!empty($menu['child_module']) AND !empty($menu['child_param'])) {
                        $href = site_url($menu['child_module']).'/index/'.$menu['child_param'];
                        $onclick = 'link(this.href);';
                    }
                    ?>
                    <li class="parent"><a href="<?php echo $href ?>" onclick="<?php echo $onclick ?> return false;"><i class="icon"><img src="<?php echo base_url() ?>img/menu_icons/<?php echo $menu['MenuIcon'] ?>" alt=""></i><span><?php echo lang($menu['MenuName']) ?></span></a>
                        <?php if (!empty($menu['child'])): ?>
                            <ul class="sub-menu">
                            <?php foreach ($menu['childNew'] as $key2 => $child): ?>
                                <li class="title-additional-new"><?= lang($key2); ?></li>
                                    <?php foreach ($child as $key3 => $child2): ?>
                                        <?php
                                            $child2_href = '';
                                            if (!empty($child2['MenuModule'])) {
                                                $child2_href = site_url($child2['MenuModule'].(!empty($child2['MenuParam'])?'/index/'.$child2['MenuParam']:''));
                                            } elseif (!empty($child2['child_module']) AND !empty($child2['child_param'])) {
                                                $child2_href = site_url($child2['child_module']).'/index/'.$child2['child_param'];
                                            }
                                            if($_SESSION["PartnerID"] == "194" OR $_SESSION["PartnerID"] == "14"){
                                                if($child2['MenuName'] == "Farmer Training"){
                                                    $child2['MenuName'] = "Member Training";
                                                }
                                                if($child2['MenuName'] == "Kader Training"){
                                                    $child2['MenuName'] = "Group Training";
                                                }
                                                if($child2['MenuName'] == "Program KPI"){
                                                    $child2['MenuName'] = "Wild Asia Malaysia KPI";
                                                }
                                            }
                                        ?>
                                        <li><a href="<?php echo $child2_href; ?>" onclick="link(this.href); return false;">
                                        <?php if ($child2['MenuIcon']): ?>
                                            <i><img src="<?php echo base_url() ?>img/menu_icons/<?php echo $child2['MenuIcon'] ?>" alt=""></i>
                                        <?php endif ?>
                                        <span style="margin-left: 5px; "><?php echo lang($child2['MenuName']) ?></span></a></li>
                                    <?php endforeach ?>
                            <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
            </ul>
            <!--Sidebar bottom content-->
        </div>
    </div>
    <div class="am-content" id="wrapper" style="min-height:450px">
