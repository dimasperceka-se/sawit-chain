<?php
    /**
     * @author [Sonny Fitriawan]
     * @email [sonny.fitriawan@koltiva.com]
     * @create date 2020-05-12 19:34:24
     * @modify date 2020-05-12 19:34:24
     * @desc [description]
     */
?>
<div class="main-content" style="background: #fff; min-height: 1500px;">
    <style type="text/css"></style>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/select2/css/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/lib/summernote/summernote2.css" />
    <link rel=stylesheet href="<?=base_url()?>css/modules/cms/cms.css">
   
    <div class="news-container">
        <form id="News-Form" method="POST" enctype="multipart/form-data">
            <div class="col-md-9 left-side">
                <div class="top-grid-header">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group back-to-list">
                                <a id="backLink" href="#">
                                    <i class="fa fa-arrow-left"></i> Back to News List
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-md-8">
                                    <!--label for="exampleInputEmail1" class="pull-right">Select Language</label-->
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <select class="form-control" id="nf-language-switcher" name="Language">
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
                <div class="grid-content clearfix">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="properties-title">Title</label>
                                <input type="hidden" name="NewsID">
                                <input type="text" class="form-control form-title validate" placeholder="Write your title here.." name="Title">
                            </div>
                            <div class="form-group">
                                <label class="properties-title">Summary</label>
                                <textarea id="news-summary" class="form-control form-summary validate" name="Summary" placeholder="Write your summary here"></textarea>
                                <label class="properties-title">Main Image</label>
                            </div>
                            <div class="dropzone">
                                <label for="files" class="dropzone-container">
                                    <div class="file-icon" id="FileIcon">+</div>
                                    <div id="PhotoBox"></div>
                                    <div class="dropzone-title">
                                        Drag and drop your news image or <span class='browse'>browse</span> your images
                                    </div>
                                </label>
                                <input id="files" name="file" type="file" class="file-input" />
                                <input id="oldfile" name="OldFile" type="text" class="file-input" />
                            </div>
                            <div class="panel panel-default m-t-20">
                                <label class="properties-title" style="margin-bottom: 10px;">Description</label>
                                <textarea id="news-description" class="description validate" name="Content"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="properties-news col-md-3 right-side">
                <div class="dropdown status-news">
                    <div class="btn-group btn-space dropdown-toggle">
                        <button type="button" class="btn btn-status shadow p-3 mb-5 bg-white rounded" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Save</button>
                        <button type="button" data-toggle="dropdown" class="btn status-dropdown dropdown-toggle" aria-expanded="false"><span class="glyphicon glyphicon-menu-up"></span><span class="sr-only">Toggle Dropdown</span></button>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href="#" id="savePublish">Save & Publish</a></li>
                            <li><a href="#" id="saveAsDraft">Save As Draft</a></li>
                            <li class="divider"></li>
                            <li><a href="#" id="saveUnPublish">Unpublish</a></li>
                        </ul>
                    </div>
                </div>
                <div class="news-access-users">
                    <form action="#">
                        <div class="form-group m-t-10">
                            <label for="news-status" class="properties-title">Status Type</label>
                            <select class="form-control" id="news-status" name="StatusType">
                                <option></option>
                                <option value="Public">Public</option>
                                <option value="Private">Private</option>
                            </select>
                        </div>
                        <div class="form-group m-t-10 partner-access">
                            <label for="news-status" class="properties-title">Partner Access</label>
                            <select class="form-control" id="partner-access" name="PartnerIDImplode[]" multiple="multiple">
                                <?php foreach($partners as $key => $partner): ?>
                                    <option value="<?php echo $partner['id']; ?>"><?php echo $partner['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group m-t-10 partner-access">
                            <label for="news-status" class="properties-title">Role Access</label>
                            <select class="form-control" id="role-access" name="RoleAccess[]" multiple="multiple">
                                <option value="RoleAccessFarmer">Farmer</option>
                                <option value="RoleAccessTrader">Trader</option>
                                <option value="RoleAccessStaff">Staff</option>
                                <option value="RoleAccessRetailer">Retailer</option>
                            </select>
                        </div>
                        <!--div class="form-group m-t-10">
                            <div class="attachment-file">
                                <label class="properties-title">Thumbnails<sup style="color:#d02630;"> (Max file 20 MB)</sup></label>
                                <input class="form-control" type="file" onchange="ValidateSize(this)" accept=".png, .jpg, .jpeg, .giff" name="input_thumbnail" id="input_thumbnail" />
                            </div>
                        </div-->      
                        <div class="form-group m-t-10">
                            <label class="properties-title">Author</label>
                            <div class="input-group xs-mb-15"><span class="input-group-addon glyphicon glyphicon-user"></span>
                                <input readonly type="text" name="PostedBy" placeholder="Name of Author" class="form-control">
                            </div>
                        </div>
                        <div>
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <div class="attachment-file">
                                        <label class="properties-title">Attachments<sup style="color:#d02630;"> (Max file 20 MB)</sup></label>
                                        <input type="file" class="form-control" onchange="ValidateSize(this)" accept=".png, .jpg, .jpeg, .pdf, .giff" name="upload_file1" id="upload_file1" />
                                    </div>
                                </div>
                            </div>
                            <div></div>
                            <div id="moreImageUpload"></div>
                            <div class="clear"></div>
                            <div id="moreImageUploadLink" style="display:none;">
                                <a href="javascript:void(0);" id="attachMore">Attach another file</a>
                            </div>
                        </div>
                        
                        <!-- height max -->
                        <div style="height: 1200px;max-height:100%;">
                        </div>

                    </form>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript">
        <?php foreach ($action as $key => $value): ?>
	        var <?php echo 'm_'.$key ?> = "<?php echo $value ?>";
        <?php endforeach ?>
    </script>
    
    <script type="text/javascript" src="<?php echo base_url() ?>assets/lib/summernote/summernote2.min.js"></script> 
    <script type="text/javascript" src="<?php echo base_url() ?>assets/lib/select2/js/select2.min.js"></script> 
    <script type="text/javascript" src="<?php echo base_url() ?>js/modules/cms/form.js"></script> 
    <script type="text/javascript" src="<?php echo base_url() ?>js/modules/cms/htmlentities.js"></script> 
    <script type="text/javascript" src="<?php echo base_url() ?>js/modules/cms/news_form.js?202211252"></script> 
</div>