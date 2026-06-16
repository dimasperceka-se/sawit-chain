<?php
if ($js!='') { ?>
<script>
    document.getElementById('titlet').innerHTML = '<?=$titlet?>';
    <?$key = array_keys($action);
    for ($i=0;$i<sizeof($action);$i++) {?>
        var m_<?=$key[$i]?> = <?=($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
        <?}?>
    </script>
    <div id="ext-content"></div>
        <div class="row-fluid">
    <div class="span3">
      <div class="title">
            <div class="row-fluid legend-profile">
                <h1>
                  <?=$_SESSION['realname']?>
                </h1>
            </div>
        </div><!-- End .title -->
        <div class="content">
          <div class="row-fluid well well-small">
                  <img class="row-fluid" src="<?=$user_photo?>">
            </div>
            <ul class="nav nav-tabs nav-stacked">
             <li><a href="<?=site_url('/home/updateprofile')?>" onClick="link(this.href);return false"><i class="gicon-user"></i> <?=lang('Update Profile'); ?></a></li>
              <li><a href="<?=site_url('/home/changepassword')?>" onClick="link(this.href);return false"><i class="gicon-wrench"></i> <?=lang('Change Password'); ?></a></li>
              <!-- <li><a href="<?=site_url('/home/message')?>" onClick="link(this.href);return false"><i class="gicon-envelope"></i> <?=lang('Messages'); ?></a></li> -->
              <!-- <li><a href="<?=site_url('/home/notification')?>" onClick="link(this.href);return false"><i class="gicon-exclamation-sign"></i> <?=lang('Notification'); ?></a></li> -->
              <li><a href="<?=site_url('system/login/logout')?>"><i class="gicon-lock"></i> <?=lang('Logout'); ?></a></li>                            

            </ul>
        </div> <!-- End .content -->
    </div><!-- End .span3 -->

  <div class="span9">
     <div class="row-fluid legend-profile profile">
      <div class="row-fluid ">
        <div class="span6 spacer">
          <ul class="unstyled">
            <li class="location"><span class="muted"><i class="icon-map-marker"></i> <?=lang('Partner Name'); ?> :</span> <?=$profile[0]['partner_name']?></li>
            <li class="location"><span class="muted"><i class="icon-map-marker"></i> <?=lang('Partner Full Name'); ?> :</span> <?=$profile[0]['partner_name']?></li>
            <li class="location"><span class="muted"><i class="icon-globe"></i> <?=lang('Area'); ?> :</span> <?=$profile[0]['district']?></li>
          </ul>
        </div>
        <div class="span6 spacer">
          <div class=" pull-right">
            <img width="100px" style="float:right;margin-bottom:20px" src="<?=$partner_photo?>">
          </div>
       </div>
      </div>
    </div> <!-- End .legend -->

        <div class="content spacer">
          <h3><span><?=lang('My Account Data'); ?></span></h3>
          <form class="form-horizontal" id="form-password">
            <table class="table table-condensed" style="font-size:1em;line-height:0px;border:0">
              <thead>
                <tr>
                  <th style="width:30%"><?=lang('Old Password'); ?></th>
                  <td><input type="password" class="row-fluid password form-control" id="password-field" name="oldpassword"></td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th><?=lang('New Password'); ?></th>
                  <td><input type="password" class="row-fluid password form-control" id="password-field" name="newpassword"></td>
                </tr>
                <tr>
                  <th><?=lang('Retype New Password'); ?></th>
                  <td><input type="password" class="row-fluid password form-control" id="password-field" name="newpassword_confirm"></td>
                </tr>
                <tr>
                  <th colspan="2" style="text-align:right;background-color:#eee">
                    <button class="btn btn-primary" type="button" id="btn-submit"><?=lang('Save'); ?></button>
                    <a href="<?=site_url('/home/profile')?>" onClick="link(this.href);return false"><button class="btn btn-secondary" type="button"><?=lang('Cancel'); ?></button></a>
                  </th>
                </div>
              </div>             
              </tbody>
            </table>
          </form>
        </div> <!-- End .content -->
    </div><!-- End .span9 -->
</div><!-- End .row-fluid -->
        <!-- End .row-fluid -->
        <?}
        if ($style!='') {?>
        <style type="text/css">
            <?=$style?>
        </style>
        <?}?>
        <script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
        <?if ($daer!=''){?>
        <script>
            var jj = '<li><a onClick="link(this.href);return false" href="<?=base_url('home/home/demographic')?>?petani=<?=$petani?>"><?=lang('Semua')?></a></li>';
            var judul = lang('Semua');
            for (var i=0;i<s[7].length;i++) {
                jj += '<li><a onClick="link(this.href);return false" href="<?=base_url('home/home/demographic')?>/a/'+s[7][i]['id']+'?petani=<?=$petani?>">'+
                lang(s[7][i]['label'])+'</a></li>';
                if (s[7][i]['id']=='<?=$private?>') judul = lang(s[7][i]['label'])
            }
        document.getElementById('dLabeli').innerHTML = jj;
        document.getElementById('judul').innerHTML = judul;
    </script>
    <?}?>
