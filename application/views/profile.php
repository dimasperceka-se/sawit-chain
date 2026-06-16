<?php
if ($js!='') { ?>

    <script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
    $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?$key = array_keys($action);
    for ($i=0;$i<sizeof($action);$i++) {?>
        var m_<?=$key[$i]?> = <?=($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
        <?}?>
    </script>
    <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>
<?}?>
<script>

$('.upload').hover(function(){
    // alert('msg')
});
</script>
<style type="text/css">
.green-head {
    background: #589C14;
    color: #fff;
}
.parsley-errors-list.filled::after {border-bottom:7px solid red;}
.parsley-errors-list.filled {background-color: red;}
#notoldpassword{display: none;}
#notnewpassword{display: none;}
#notnewpassword_confirm{display: none;}
.upload:hover {
    width: 135px;
    height: 135px;
    position: absolute;
    background: #ccc;
    background-image: url('<?php echo base_url()."images/icons/silk/camera_add.png" ?>');
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center;
    border-radius: 100px;
    opacity: 0.4;
    cursor: pointer;
}
.side-tab{
    position: relative;
    float: right;
    left: 23px;
    width: 110%;
    top: -20px;

}

.upload {
    width: 135px;
    height: 135px;
    position: absolute;
    border-radius: 100px;

}
</style>


<br><br>
<div class="user-profile col-md-2">
    <div class="user-display user-display xs-mt-50">
    <div class="photo"></div>
        <div class="bottom">
            <div class="user-avatar"><span class="status"></span>
            <div class="upload">
                <b style="text-align: center;display: block;margin: 37px;display: none;">Upload Foto</b>
            </div>

                <?php
                    if(IsUrlImageExist($url_awss3.'/'.$profile['profile']['Photo']) == true) {
                        $AvatarUrl = $url_awss3.'/'.$profile['profile']['Photo'];
                    } else {
                        $AvatarUrl = $this->config->item('api_base_url').'images/Photo/default-user.png';
                    }
                ?>
                <img src="<?php echo $AvatarUrl; ?>">
                <h4 style="text-align: center;"><?php echo $_SESSION['realname'] ?></h4>
                <br>

            </div>
        </div>
    </div>
    <div class="side-tab">
        <ul class="nav nav-tabs nav-stacked">
          <li role="presentation" class="active"><a  href="#roles" aria-controls="roles" role="tab" data-toggle="tab"> Roles</a></li>
          <li><a  href="#access" aria-controls="access" role="tab" data-toggle="tab"> Access Area</a></li>
        </ul>
    </div>
</div>

<div class="main-content col-md-10" >
<div class="user-profile">
        <ul class="nav nav-tabs " role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?php echo lang('Personal Information'); ?></a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?php echo lang('Change Password') ?></a></li>
            <li role="presentation"><a href="#Activites" aria-controls="Activites" role="tab" data-toggle="tab"><?php echo lang('History') ?></a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Page Access</a></li>
        </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
            <div class="info-block panel panel-default">
                <div class="panel-group" id="accordion">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                        <?php echo lang('My Account Data'); ?></a>
                      </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse in">
                        <form action="" method='POST' enctype="multipart/form-data" id='uploadImage' style="display: none">
                          <input onchange="upload()" type="file" class="upload-img" name='Photo'>
                          <input type="hidden" value="<?php echo $_SESSION['Photo_staff'] ?>" name="Photo_old">
                          <input type="hidden" value="<?php echo $_SESSION['userid'] ?>" name="user_id_from_profile">
                        </form>
                      <div class="panel-body">
                          <form id="form-profile" class="form-horizontal">
                          <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-user"></span> <?php echo lang('Full Name'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo $profile['profile']['PersonNm']?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-user"></span> <?php echo lang('Username'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo $_SESSION['username']?>">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-gift"> <?php echo lang('Group'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo $profile[0]['GroupName']?>">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-user"></span> <?php echo lang('Birth Date'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo $profile['profile']['BirthDate']?>">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-map-marker"></span> <?php echo lang('Active status'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo $profile[0]['UserActive']?>">
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-user"></span> <?php echo lang('Gender'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo ($profile['profile']['Gender'] == 'm') ? lang('Male') : lang('Female') ?>">
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-phone"></span> <?php echo lang('No Telepon'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo $profile['profile']['OfficialCellPhone'] ?>">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-mail"></span> <?php echo lang('Email'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo $profile['profile']['OfficialEmail'] ?>">
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-phone"></span> <?php echo lang('Unit'); ?></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" disabled="true" value="<?php echo $profile[0]['UnitName']?>">
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><span class="icon s7-global"></span> <?php echo lang('Language'); ?></label>
                                    <div class="col-sm-8">
                                        <select data-placeholder="<?php echo lang('Choose your language') ?>" class="form-control" name="UserLanguage">
                                            <option value=""><?php echo lang('Select') ?></option>
                                            <option value="Indonesia" <?php echo $profile[0]['UserLanguage']=='Indonesia'?'selected="true"':''?>><?php echo lang('Bahasa') ?></option>
                                            <option value="English" <?php echo $profile[0]['UserLanguage']=='English'?'selected="true"':''?>><?php echo lang('English') ?></option>
                                            <option value="Malaysia" <?php echo $profile[0]['UserLanguage']=='Malaysia'?'selected="true"':''?>><?php echo lang('Malay') ?></option>
                                            <option value="Spanish" <?php echo $profile[0]['UserLanguage']=='Spanish'?'selected="true"':''?>><?php echo lang('Spanish') ?></option>
                                            <option value="French" <?php echo $profile[0]['UserLanguage']=='French'?'selected="true"':''?>><?php echo lang('French') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="pull-right">
                                    <button type="button" data-dismiss="modal" class="btn btn-default md-close"><?php echo lang('Cancel') ?></button>
                                    <button type="button" class="btn btn-primary" id="save_profile"><?php echo lang('Save') ?></button>
                                </div>
                                <!-- <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"> <?php echo lang('Notification Status'); ?></label>
                                    <div class="col-sm-10">
                                        <select data-placeholder="<?php echo lang('Choose your notification status') ?>" class="form-control" name="UserNotification">
                                            <option value=""><?php echo lang('Select') ?></option>
                                            <option value="Official Email Only" <?php echo $profile[0]['UserNotification']=='Official Email Only'?'selected="true"':''?>><?php echo lang('Official Email Only') ?></option>
                                            <option value="Official and Private Email" <?php echo $profile[0]['UserNotification']=='Official and Private Email'?'selected="true"':''?>><?php echo lang('Official and Private Email') ?></option>
                                        </select>
                                    </div>
                                </div> -->

                            </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" class='additonal-info' data-parent="#accordion" href="#collapse2">
                        <i class="glyphicon glyphicon-chevron-right"></i>
                        Additional Information</a>
                      </h4>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="roles">
                            <h2>Roles</h2>
                                <hr>
                                <table class="table">
                                    <thead class="green-head">
                                        <th style="width: 30px">No</th>
                                        <th>Roles</th>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;foreach ($profile['additonal']['roles'] as $roles): ?>
                                            <tr>
                                                <td><?php echo $i;$i++ ?></td>
                                                <td><?php echo $roles['GroupName'] ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="access">
                                <h2>Access Area</h2>
                                <hr>
                                <table class="table">
                                    <thead class="green-head">
                                        <th style="width: 30px">No</th>
                                        <th>Area</th>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;foreach ($profile['additonal']['access'] as $access): ?>
                                            <tr>
                                                <td><?php echo $i;$i++ ?></td>
                                                <td><?php echo $access['District'] ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>

                <!--<div class="panel-body">
                <span class="description">I am a web developer and designer based in Montreal - Canada, I like read books, good music and nature.</span>
                    <table class="no-border no-strip skills">
                        <tbody class="no-border-x no-border-y">
                            <tr>
                                <td class="item"><?php echo lang('Last Login') ?>:<span class="icon s7-user"></span></td>
                                <td><?php echo $last_access['Timestamp'] ?></td>
                            </tr>
                            <tr>
                                <td class="item"><?php echo lang('Username'); ?>:<span class="icon s7-portfolio"></span></td>
                                <td><?php echo $_SESSION['username']?></td>
                            </tr>
                            <tr>
                                <td class="item"><?php echo lang('Group'); ?>:<span class="icon s7-gift"></span></td>
                                <td><?php echo $profile[0]['GroupName']?></td>
                            </tr>
                            <tr>
                                <td class="item"><?php echo lang('Unit'); ?>:</td>
                                <td><?php echo $profile[0]['UnitName']?></td>
                            </tr>
                            <tr>
                                <td class="item"><?php echo lang('Active status'); ?>:<span class="icon s7-map-marker"></span></td>
                                <td><?php echo $profile[0]['UserActive']?></td>
                            </tr>
                            <tr>
                                <td class="item"><?php echo lang('Language'); ?>:<span class="icon s7-global"></span></td>
                                <td><?php echo $profile[0]['UserLanguage']?></td>
                            </tr>
                            <tr>
                                <td class="item"><?php echo lang('Notification Status'); ?>:<span class="icon s7-global"></span></td>
                                <td><?php echo $profile[0]['UserNotification']?></td>
                            </tr>
                        </tbody>
                    </table>
                </div> -->
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="profile">
                <div class="row">
                <div class="col-md-8">
                    <div class="widget widget-fullwidth widget-small">
                        <div class="widget-head">
                            <span class="title"><?php echo lang('Change Password') ?></span>
                        </div>
                        <hr>

                        <form id="form-password">
                            <div class="form-group">
                                <label><?php echo lang('Old Password') ?></label>
                                <input type="password" class="form-control" name="oldpassword" id="oldpassword">
                                <ul id="notoldpassword" class="parsley-errors-list filled" id="parsley-id-4"><li class="parsley-required">This field is not valid yet</li></ul>
                            </div>
                            <div class="form-group">
                                <label><?php echo lang('New Password') ?></label>
                                <input type="password" class="form-control" name="newpassword" id="newpassword">
                                <ul id="notnewpassword" class="parsley-errors-list filled" id="parsley-id-4"><li class="parsley-required">This field is not valid yet</li></ul>
                            </div>
                            <div class="form-group">
                                <label><?php echo lang('Retype New Password') ?></label>
                                <input type="password" class="form-control" name="newpassword_confirm" id="newpassword_confirm">
                                <ul id="notnewpassword_confirm" class="parsley-errors-list filled" id="parsley-id-4"><li class="parsley-required">This field is not valid yet</li></ul>
                            </div>
                        </form>

                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-default md-close"><?php echo lang('Cancel') ?></button>
                            <button type="button" class="btn btn-primary" id="save_password"><?php echo lang('Save') ?></button>
                        </div>
                        <!-- bekas form -->
                </div>
                <div class="col-md-4">

                        <div class="alert alert-info" style="color:black;">
                            <p><?php echo lang('Your new password must fit these criteria')?></p>
                            <ul>
                                <li><?php echo lang('Minimal 8 characters')?></li>
                                <li><?php echo lang('Maximal 14 characters')?></li>
                                <li><?php echo lang('Contains lowercase [a-z], uppercase [A-Z], numbers [0-9], and special characters [!@#$%^&*[](){}]')?></li>
                            </ul>
                        </div>
                </div>

                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="Activites">
                <div class="row">
                    <div class="widget widget-fullwidth widget-small">
                        <div class="widget-head">
                            <span class="title"><?php echo lang('Login History') ?></span>
                        </div>
                        <hr>
                        <div class="table-container">
                            <table class="table table-striped table-fw-widget table-hover">
                                <thead>
                                    <tr>
                                        <th width="20%"><?php echo lang('Login Type') ?></th>
                                        <th width="20%"><?php echo lang('Login Time') ?></th>
                                        <th width="20%"><?php echo lang('Status') ?></th>
                                        <th width="20%"><?php echo lang('IP Address') ?></th>
                                        <th width="30%"><?php echo lang('Location') ?></th>
                                    </tr>
                                </thead>
                                <tbody class="no-border-x">
                                <?php if (!empty($login_history)): ?>
                                    <?php foreach ($login_history as $key => $value): ?>
                                        <tr>
                                            <td>Browser</td>
                                            <td><?php echo $value['Timestamp'] ?></td>
                                            <td><?php echo $value['AttempProcess'] ?></td>
                                            <td class="ip-address"><?php echo $value['SessionIP'] ?></td>
                                            <td class="ip-location"></td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div role="tabpanel" class="tab-pane" id="settings">
            <div class="row">
                <div class="col-md-12">
                        <div class="widget widget-fullwidth widget-small">
                            <div class="widget-head">
                                <i class="glyphicon glyphicon-chevron-right"></i>
                                <span class="title"><?php echo lang('Page Activities') ?></span>
                            </div>
                            <hr>
                            <div class="table-container">
                                <table class="table table-striped table-fw-widget table-hover">
                                    <thead class="">
                                        <tr>
                                            <th width="32%"><?php echo lang('Page') ?></th>
                                            <th width="45%"><?php echo lang('Time') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="no-border-x">
                                    <?php if (!empty($last_page_access)): ?>
                                        <?php foreach ($last_page_access as $key => $value): ?>
                                            <tr>
                                                <td><?php if ($value['parent_menu']): ?><?php echo lang($value['parent_menu']) ?> - <?php endif ?><?php echo lang($value['menu']) ?></td>
                                                <td><?php echo $value['Timestamp'] ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
            </div>
          </div>

</div>
</div>


<script>

$(document).ready(function(){
    $('div.side-tab ul li a').click(function(ev) {
        $('#collapse2').collapse('show');
        $('#collapse1').collapse('hide');
        // $('.additonal-info').click();
        // ev.preventdefault();
    })
})

let ipAddres = document.querySelectorAll('td:nth-child(4)');
var dataIp = [];
ipAddres.forEach(x => {
    let ip = new Promise((resolve, reject) => {
        console.log(x.innerText);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'https://ipinfo.io/', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function (oEvent) {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var el = document.createElement('html');
                    el.innerHTML = xhr.responseText;
                    if (typeof el.querySelectorAll('tr td:nth-child(2)')[2] !== 'undefined'){
                        resolve(el.querySelectorAll('tr td:nth-child(2)')[2].innerText.split(',').slice(1).join(', ').trim());
                    } else {
                        resolve('Not Found')
                    }
                    // console.log(el.querySelectorAll('tr td:nth-child(2)')[2].innerText.split(',').slice(1).join(', ').trim())
                } else {
                   resolve(xhr.statusText);
                }
            }
        };
        xhr.send('ip='+x.innerText);
    });
    dataIp.push(ip);
});
// console.log(dataIp)
Promise.all(dataIp).then(ip => {
    // console.log(ip)
    for (var i = 0; i < ip.length; i++) {
        document.querySelectorAll('td:nth-child(5)')[i].innerText = ip[i];
    }
});

$('.upload').click(() => {
    $('.upload-img').click()
})

function upload() {
    $.ajax({
        type: "POST",
        url: '/api/index.php/basic_staff/image_staff',
        data: new FormData($('#uploadImage')[0]),
        processData: false,
        contentType: false,
        success: function (data) {
            location.reload();
        }
    });
}

</script>