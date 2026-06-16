<?php
if($filter_label != "sce"){
?>
<input type="hidden" name="filter_by" id="filter_by" value="<?php echo $filter_by ?>">
<input type="hidden" name="filter_label" id="filter_label" value="<?php echo $filter_label ?>">
<input type="hidden" name="filter_id" id="filter_id" value="">
                    <li class="dropdown"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle"><span class="icon s7-shuffle"></span></a>
                        <ul class="dropdown-menu am-messages">
                            <li>
                                <div class="title"><?php echo $filter_title ?><span class="badge"><?php echo count($filter_list) ?></span></div>
                                <div class="list">
                                    <div class="am-scroller nano">
                                        <div class="content nano-content">
                                            <ul>
                                                <?php foreach ($filter_list as $key => $value): ?>
                                                <li class=""><a href="#" id="group-link-id" class="filter_list" data-id="<?php echo $value['id'] ?>">
                                                    <!-- <div class="logo"><img src="assets/img/avatar2.jpg"></div> -->
                                                    <div class="user-content"><span class="date"></span><span class="name"><?php echo $value['label'] ?></span><span class="text-content"></span></div>
                                                </a></li>
                                                <?php endforeach ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer"><a href="#"></a></div>
                            </li>
                        </ul>
                    </li>
<script type="text/javascript">
    $(function(){
        $('.filter_list').on('click', function(event) {
            event.preventDefault();
            $(this).parent().parent().find('li').removeClass('active');
            $(this).parent().addClass('active');
            $('#filter_id').val($(this).data('id'));
            $.get("<?php echo site_url('system/profile/change_filter/') ?>"+'/'+$(this).data('id'), function(data) {
                // return false;
                if(data == 'sce_filter_id_change'){
                    window.location = '<?php echo base_url()?>prog_sce/profile';
                } else if(data == 'coopid_change')
                    {
                         window.location = "<?=site_url()?>";
                    }
                /*optional stuff to do after success */
                // console.log('Filter ID changed');
            });
        });
    });
</script>
<?php } elseif ($filter_label == "sce"){ ?>
<style type="text/css">
#topSearchSceText {
    padding: 5px;
    border: 1px solid #DDDDDD;

    /*Applying CSS3 gradient*/
    background: -moz-linear-gradient(center top , #FFFFFF,  #EEEEEE 1px, #FFFFFF 20px);
    background: -webkit-gradient(linear, center top, center bottom, color-stop(0%, #EEEEEE), color-stop(100%, #fff));

    /*Applying CSS 3radius*/
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;

    /*Applying CSS3 box shadow*/
    -moz-box-shadow: 0 0 2px #DDDDDD;
    -webkit-box-shadow: 0 0 2px #DDDDDD;
    box-shadow: 0 0 2px #DDDDDD;
}

.am-top-header
.navbar-collapse
.am-icons-nav > li.dropdown
.am-messages {
    border-radius: 5px;
    left: auto;
    margin-right: -250px;
    padding-bottom: 0;
    right: 50%;
    width: 550px;
}

#tabelAutoComSce {
    width: 100%;
}

#tabelAutoComSce tr th, #tabelAutoComSce tr td {
    padding: 3px;
    border: 1px solid gray;
    font-size: 10px;
}

#tabelAutoComSce tr th {
    background-color: #589C14;
    color: white;
}

#tabelAutoComSce tbody tr:nth-child(even) {
    background-color: #FAFAFA;
}

#tabelAutoComSce tbody tr:hover {
    cursor: pointer;
    background-color: #8CD2AD;
}

.rowlink::before {
    content: "";
    display: block;
    position: absolute;
    left: 0;
    width: 100%;
    height: 1.5em;
}

</style>
<li class="dropdown" style="padding-top:26px;padding-left:5px;padding-right:5px;">
    <table>
    <tr>
        <td><input id="topSearchSceText" placeholder="Search by Farmer ID / Name" type="text" size="25" /></td>
        <td width="25">
            &nbsp;&nbsp;<img src="<?php echo site_url()?>assets/css/loading.gif" width="16" id="topImgLoadingSce" style="display:none;" />
        </td>
    </tr>
    </table>

    <a style="display:none;" id="sceBtnHideAutoCom" href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle">Button Invis</a>

    <ul id="topListAreaSceSearch" style="margin-top:35px;" class="dropdown-menu am-messages">
        <li></li>
    </ul>
</li>
<script type="text/javascript">
$(document).ready(function(){
    $(document).on( "keyup", "#topSearchSceText", function(e) {
        $("#topListAreaSceSearch").hide();
        var textNya = $(this).val();
        //var elClickNya = document.getElementById('sceBtnHideAutoCom');
        //console.log(textNya);

        if(textNya.length > 2) {
            $("#topImgLoadingSce").show();
            //trigger click
            //elClickNya.click();

            $.ajax({
                url : "<?php echo site_url('system/profile/filter_autocom_sce/') ?>",
                data : "textNya="+textNya,
                type: "POST",
                dataType: "json",
                success: function(data, textStatus, jqXHR){
                    if(data.totalNya > 0){
                        //console.log(data);
                        var htmlNya = '<div class="title">SCE<span class="badge">'+data.totalNya+'</div>';
                        htmlNya += '<div class="list"><div class="am-scroller nano"><div class="content nano-content" style="padding:10px;"><table id="tabelAutoComSce"><thead><tr><th>SCE ID</th><th>Farmer</th><th>CPG</th><th>Village</th></tr></thead><tbody>';

                        for(var i = 0; i < data.dataNya.length; i++) {
                            var obj = data.dataNya[i];
                            htmlNya += `
                                <tr>
                                    <td><a data-id="`+obj.id+`" class="filter_list" href="#" class="rowlink">`+obj.id+`</a></td>
                                    <td>`+obj.farmer+`</td>
                                    <td>`+obj.cpg+`</td>
                                    <td>`+obj.desa+`</td>
                                </tr>
                            `;
                        }
                        htmlNya += '</tbody></table></div></div></div>';

                        $("#topListAreaSceSearch li").html(htmlNya);
                    }else{
                        $("#topListAreaSceSearch li").html('<div class="title">SCE<span class="badge">0</div><div class="list"><div class="am-scroller nano"><div class="content nano-content"><ul><li class=""><a href="<?php echo site_url('prog_sce/sce_farmer_add/') ?>" id="group-link-id" class="" data-id=""><div class="user-content" style="padding-left:5px;"><span class="name">Farmer not found - Add New Farmer SCE</span></div></a></li></ul></div></div></div>');
                    }

                    $("#topImgLoadingSce").hide();
                    $("#topListAreaSceSearch").show();
                    //trigger click
                    //elClickNya.click();
                },
                error: function(data, textStatus, jqXHR){
                    $("#topImgLoadingSce").hide();
                }
            });
        }
    });

    $(document).on( "click", ".filter_list", function(event) {
        event.preventDefault();
        $(this).parent().parent().find('li').removeClass('active');
        $(this).parent().addClass('active');
        $('#filter_id').val($(this).data('id'));
        $.get("<?php echo site_url('system/profile/change_filter/') ?>"+'/'+$(this).data('id'), function(data) {
            // return false;
            if(data == 'sce_filter_id_change'){
                window.location = '<?php echo base_url()?>prog_sce/profile';
            } else if(data == 'coopid_change')
                {
                     window.location = "<?=site_url()?>";
                }
            /*optional stuff to do after success */
            // console.log('Filter ID changed');
        });
    });
});
</script>
<?php } ?>