<style type="text/css">
    #category-toolbar {
        left: 0px !important;
    }
    #category-toolbar .list-group, #weather-toolbar .list-group {
        border-radius: 4px;
        background-color: rgba(240, 240, 240, 0.75);
        background-image: -moz-linear-gradient(center bottom , rgba(255, 255, 255, 0.5) 0%, rgba(240, 240, 240, 0.5) 100%);
    }
    #category-toolbar .list-group-item, #weather-toolbar .list-group-item {
        background-color: rgba(240, 240, 240, 0.0) !important;
        padding: 4px 15px;
        color: #555555;
        font-size: 1.3em;
    }
</style>
<link href="<?php echo base_url()?>css/modules/map/map.css" rel="stylesheet">
<div class="main-content">
    <div id="ext-content"></div>
    <div id="category-toolbar" class="hidden">
        <ul class="list-group" style="margin-left: 20px;">
        </ul>
    </div>
    <div id="map_canvas" style="width:100%; min-height: 450px; margin-top: 5px"></div>
    <!-- <div <?php echo $action['act_view_detail']==false?'style="display: none;"':'' ?>>
        <fieldset class="x-fieldset x-fieldset-with-title x-fieldset-with-legend x-fieldset-default">
        <legend class="x-fieldset-header x-fieldset-header-default" id="fieldset-1039-legend"><span id="fieldset-1039-legend-outerCt" style="display:table;" role="presentation"><div id="fieldset-1039-legend-innerCt" style="height: 100%; vertical-align: top; display: table-cell;" class="" role="presentation"><div class="x-component x-fieldset-header-text x-component-default" id="fieldset-1039-legendTitle"><?php echo lang('Farmer List')?></div></div></span></legend>
        <div class="x-fieldset-body" style="min-height: 20px;" >
            <div class="row">
                <div class="col-sm-12">
                    <div class="am-checkbox">
                        <input type="checkbox" name="check_all" id="check_all">
                        <label for="check_all"><?php echo lang('Check All') ?></label>
                    </div>
                </div>
            </div>
            <div class="row" id="farmer_checklist">

            </div>
        </div>
        </fieldset>
    </div> -->
</div>
<script type="text/javascript">
    <?php foreach ($action as $key => $value): ?>
    var m_<?php echo $key ?> = "<?php echo $value ?>";
    <?php endforeach ?>

    var base_url        = "<?php echo base_url() ?>"

    $(function(){
        $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    })
</script>
<script src="<?php echo base_url() ?>js/screenfull.min.js"></script>
<script src="<?php echo base_url() ?>js/infobox.js"></script>
<script src="<?php echo base_url() ?>js/modules/data_adm/polygon.js"></script>
<script type="text/javascript">
    // load googlemap
    loadScript("<?php echo base_url() ?>js/gmap3.js",init_map);
</script>