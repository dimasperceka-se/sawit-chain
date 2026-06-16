<div class="main-content" style="background: #fff; min-height: 1500px;">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/select2/css/select2.min.css" />
    <link rel=stylesheet href="<?=base_url()?>css/modules/cms/plyr.min.css">
    <link rel=stylesheet href="<?=base_url()?>css/modules/cms/cms.css">
    <script>
        var baseUrl = '<?php echo base_url(); ?>';
    </script>
    <div class="news-container">
        <div class="col-md-9 left-side">
            <div class="top-grid-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group back-to-list">
                            <a id="backLink" href="#">
                                <i class="fa fa-arrow-left"></i> Back to Video List
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6" style="display: none">
                        <div class="form-group">
                            <div class="col-md-8">
                                <!--label for="exampleInputEmail1" class="pull-right">Select Language</label-->
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <select class="form-control" id="nf-language-switcher" name="language">
                                        <option></option>
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
            <div class="grid-content clearfix" id="preview-video"></div>
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
                    <li><a href="#">General <span>(25)</span></a></li>
                    <li><a href="#">Lifestyle <span>(12)</span></a></li>
                    <li><a href="#">Travel <span>(5)</span></a></li>
                    <li><a href="#">Design <span>(22)</span></a></li>
                    <li><a href="#">Creative <span>(8)</span></a></li>
                    <li><a href="#">Educaion <span>(14)</span></a></li>
                </ul>

                </div><!-- End sidebar categories-->

                <h3 class="sidebar-title">Tags</h3>
                <div class="sidebar-item tags">
                <ul>
                    <li><a href="#">App</a></li>
                    <li><a href="#">IT</a></li>
                    <li><a href="#">Business</a></li>
                    <li><a href="#">Business</a></li>
                    <li><a href="#">Mac</a></li>
                    <li><a href="#">Design</a></li>
                    <li><a href="#">Office</a></li>
                    <li><a href="#">Creative</a></li>
                    <li><a href="#">Studio</a></li>
                    <li><a href="#">Smart</a></li>
                    <li><a href="#">Tips</a></li>
                    <li><a href="#">Marketing</a></li>
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
    <script type="text/javascript" src="<?php echo base_url() ?>assets/js/app-form-wysiwyg.js"></script> 
    <script src="<?php echo base_url() ?>js/modules/cms/video.js?2"></script>
    <script type="text/javascript">
        preView();
        $(document).ready(function() {
            //initialize the javascript
            $('#nf-language-switcher').select2({
                width: '100%',
                placeholder: "Select Language"
            });
        });
    </script>
</div>