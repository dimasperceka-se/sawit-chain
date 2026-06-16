<div class="main-content" style="background: #fff; min-height: 1500px;">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/select2/css/select2.min.css" />
    <link rel=stylesheet href="<?=base_url()?>css/modules/cms/hilight.min.css">
    <link rel=stylesheet href="<?=base_url()?>css/modules/cms/plyr.min.css">
    <link rel=stylesheet href="<?=base_url()?>css/modules/cms/jquery-confirm.css">
    <link rel=stylesheet href="<?=base_url()?>css/modules/cms/cms.css">
    <script>
        var baseUrl = '<?php echo base_url(); ?>';
    </script>
    <div class="cms-container">
        <div class="col-md-9 left-side">
            <div class="top-grid-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="btn btn-add-new" href="#" id="addNew">
                                <i class="fa fa-plus-circle"></i> Add Video
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-8">&nbsp;</div>
                            <div class="col-md-4">
                                <div class="row">
                                    <select class="form-control" id="nf-language-switcher" name="language">
                                        <option value=""></option>
                                        <?php 
                                            $selected = '';
                                            foreach($language_list as $key => $list){ 
                                                if($list['id'] == $language){
                                                    $selected = 'selected="selected"';
                                                } else {
                                                    $selected = '';
                                                }
                                        ?>
                                            <option value="<?php echo $list['id']; ?>" <?php echo $selected; ?>><?php echo $list['label']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-content clearfix" id="main-videos"></div>
            <div class="grid-pagination">
                <nav aria-label="...">
                    <ul class="pagination pagination-md" id="ItemPagination"></ul>
                </nav>
            </div>
        </div>
        <div class="col-md-3 right-side" style="display: none">
            <div class="side-component">
                <h3 class="sidebar-title">Search</h3>
                <div class="sidebar-item search-form">
                    <form action="">
                        <input type="text">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>

                </div><!-- End sidebar search formn-->

                <h3 class="sidebar-title">Categories</h3>
                <div class="sidebar-item categories">
                <ul>
                    <li><a href="#">Company <span>(25)</span></a></li>
                    <li><a href="#">Farmer <span>(12)</span></a></li>
                    <li><a href="#">Trader <span>(5)</span></a></li>
                    <li><a href="#">SME <span>(22)</span></a></li>
                    <li><a href="#">Traceabilty <span>(8)</span></a></li>
                    <li><a href="#">Educaion <span>(14)</span></a></li>
                </ul>

                </div><!-- End sidebar categories-->

                <h3 class="sidebar-title">Tags</h3>
                <div class="sidebar-item tags">
                <ul>
                    <li><a href="#">Cocoa</a></li>
                    <li><a href="#">Production</a></li>
                    <li><a href="#">Export</a></li>
                    <li><a href="#">Indonesia</a></li>
                    <li><a href="#">Unied</a></li>
                    <li><a href="#">Office</a></li>
                </ul>
                </div><!-- End sidebar tags-->
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
   
    <script type="text/javascript">
        <?php foreach ($action as $key => $value): ?>
	        var <?php echo 'm_'.$key ?> = "<?php echo $value ?>";
        <?php endforeach ?>
    </script>
    
    <script type="text/javascript" src="<?php echo base_url() ?>assets/lib/summernote/summernote2.min.js"></script> 
    <script type="text/javascript" src="<?php echo base_url() ?>assets/lib/select2/js/select2.min.js"></script>
    <script src="<?php echo base_url() ?>js/modules/cms/sticky.js"></script>
    <script src="<?php echo base_url() ?>js/modules/cms/pretty.js"></script>
    <script src="<?php echo base_url() ?>js/modules/cms/jquery-confirm.js"></script>
    <script src="<?php echo base_url() ?>js/modules/cms/plyr.js"></script>
    <script src="<?php echo base_url() ?>js/modules/cms/video.js?2"></script>
    <script src="<?php echo base_url() ?>js/functions.js"></script>
    <script type="text/javascript">
        loadView();
        $(document).ready(function() {
            $('#nf-language-switcher').select2({
                width: '100%',
                placeholder: "Select Language"
            });
        });
    </script>
</div>