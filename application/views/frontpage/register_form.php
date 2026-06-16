<?php
/**
 * @Author: nikolius
 * @Date:   2017-10-17 10:01:44
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-18 16:39:05
 */
echo $templateHeader;
?>


<div class="panel panel-sign" style="width:1024px;margin-bottom:100px;">
    <div class="panel-title-sign mt-xl text-right">
        <h2 class="title text-uppercase text-bold m-none" style="background-color:#95130b!important"><i class="fa fa-user mr-xs"></i>Registration Form</h2>
    </div>
    <div class="panel-body" style="border-top-color:#95130b!important;padding:20px!important;">

        <table width="100%">
            <tr>
                <td width="50%" style="padding:5px 20px 5px 5px;vertical-align:top;">

                    <h4 style="margin:0px;padding:0px;">User Data Form</h4><br />

                    <form class="form-horizontal" id="formRegis">
                        <div class="form-group">
                            <label class="col-sm-4 control-label form-label-left">Fullname</label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?php echo $dataStaff['Fullname']?></p>
                            </div>
                        </div>

                        <div class="form-group form-group-margin-top">
                            <label class="col-sm-4 control-label form-label-left">Email</label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?php echo $dataStaff['Email']?></p>
                            </div>
                        </div>

                        <div class="form-group form-group-margin-top">
                            <label class="col-sm-4 control-label form-label-left">Username</label>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?php echo $dataStaff['Username']?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword" class="col-sm-4 control-label">Password</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="password" id="password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="retypePassword" class="col-sm-4 control-label">Retype Password</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="passwordConf">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Birthdate</label>
                            <div class="col-sm-8">
                                <div class='input-group date'>
                                    <input type='text' class="form-control" id="dateTglLahir" name="tglLahir" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="retypePassword" class="col-sm-4 control-label">Gender</label>
                            <div class="col-sm-8">
                                <label class="radio-inline">
                                    <input type="radio" id="inlineradio1" name="gender" value="m"> Male
                                </label>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <label class="radio-inline">
                                    <input type="radio" id="inlineradio2" name="gender" value="f"> Female
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="retypePassword" class="col-sm-4 control-label">Cellphone</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="cellphone">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="retypePassword" class="col-sm-4 control-label">Work Area Province</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="comboPropinsi" name="work_area_province">
                                    <option value="">Select Province</option>
                                    <?php
                                    for ($i=0; $i < count($comboPropinsi); $i++) {
                                        echo '<option value="'.$comboPropinsi[$i]['id'].'">'.$comboPropinsi[$i]['label'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="retypePassword" class="col-sm-4 control-label">Work Area District</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="comboDistrict" name="work_area_district">
                                    <option value="">Select District</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="retypePassword" class="col-sm-4 control-label">Interface Language</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="app_lang">
                                    <option value="">Select Language</option>
                                    <option value="Indonesia">Bahasa</option>
                                    <option value="English">English</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <input type="hidden" name="paramSegment" value="<?php echo $paramSegment?>">
                                <button type="button" id="btnRegister" class="btn btn-primary">Register</button>
                            </div>
                        </div>

                    </form>

                </td>
                <td width="50%" style="padding:5px;vertical-align:top;">

                    <a name="anchorErrorForm">&nbsp;</a>
                    <div class="alert alert-danger errorForm" style="display:none;">
                        <p class="m-none text-semibold h6">Your form is not valid yet</p><br />
                    </div>

                    <div class="alert alert-warning">
                        <p style="margin-bottom:10px;" class="m-none text-semibold h6">Your password must fit these criteria</p><br />
                        <ul>
                            <li>Minimal 8 characters</li>
                            <li>Maximal 14 characters</li>
                            <li>Contains lowercase [a-z], uppercase [A-Z], numbers [0-9], and special characters "!@#$%^&*(){}[]"</li>
                        </ul>
                    </div>

                    <div class="alert alert-info">

                        <h4 style="margin:0px;padding:0px;">Access Information</h4><br />

                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 control-label form-label-left">User Role</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?php echo $dataStaff['UserRole']?></p>
                                </div>
                            </div>

                            <div class="form-group form-group-margin-top">
                                <label class="col-sm-3 control-label form-label-left">Association</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?php echo $dataStaff['ObjLabel'] ?></p>
                                </div>
                            </div>

                            <div class="form-group form-group-margin-top">
                                <label class="col-sm-3 control-label form-label-left">Position</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?php echo $dataStaff['Position'] ?></p>
                                </div>
                            </div>

                            <div class="form-group form-group-margin-top">
                                <label class="col-sm-3 control-label form-label-left">Access Area</label>
                                <div class="col-sm-9">
                                    <?php echo $dataStaff['AccessAreaHtml'] ?>
                                </div>
                            </div>

                        </div>

                    </div>


                </td>
            </tr>
        </table>

        <br /><br />
    </div>
</div>

<script type="text/javascript">
var picker = new Pikaday({
    field: document.getElementById('dateTglLahir'),
    format: 'YYYY-MM-DD',
    yearRange: [1950,2017]
});

$(document).ready(function(){

    $(document).on( "change", "#comboPropinsi", function(e) {
        e.preventDefault();
        $("#comboDistrict").html('');

        //load combo district
        $.ajax({
            url : '<?php echo base_url()?>system/register/ajax_combo_district',
            data : 'ProvinceID='+$(this).val(),
            type: "POST",
            dataType: "html",
            success: function(data, textStatus, jqXHR){
                console.log(data);
                $("#comboDistrict").append(data);
            }
        });
    });

    /*======================================================================== Form =================================================*/

    $.validator.addMethod("checkCriteriaPassword", function (value, element) {
        var passNya = String(value);
        var regexRule=  /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,14}$/;

        if(passNya.match(regexRule)){
            return true;
        }else{
            return false;
        }
    }, 'Your password doesn\'t fit the criteria yet');

    //validasi rule
    $("#formRegis").validate({
        errorLabelContainer: $("div.errorForm"),
        rules: {
            password: {
                required: true,
                checkCriteriaPassword: true,
                minlength: 8,
                maxlength: 14
            },
            passwordConf: {
                required: true,
                minlength: 8,
                maxlength: 14,
                equalTo: "#password"
            },
            tglLahir:{
                required:true
            },
            gender:{
                required:true
            },
            work_area_province: {
                required:true
            },
            work_area_district: {
                required:true
            },
            app_lang: {
                required:true
            }
        },
        messages: {
            password: {
                required: "Password is required",
                minlength: "Your password must be at least 8 characters long",
                maxlength: "Your password must be less than 15 characters long"
            },
            passwordConf: {
                required: "Confirmation password is required",
                minlength: "Your password must be at least 8 characters long",
                maxlength: "Your password must be less than 15 characters long",
                equalTo: "Please enter the same password as above"
            },
            tglLahir:{
                required:"Birthdate is required"
            },
            gender:{
                required:"Gender is required"
            },
            work_area_province: {
                required:"Work Area Province is required"
            },
            work_area_district: {
                required:"Work Area District is required"
            },
            app_lang: {
                required:"Interface Language is required"
            }
        }
    });

    $("#btnRegister").click(function() {
        if ($('#formRegis').valid()){
            console.log('mantap');
            $('.errorForm').hide();

            $.ajax({
                url : '<?php echo base_url()?>system/register/submit_register_tos',
                data : $("#formRegis").serialize(),
                type: "POST",
                dataType: "html",
                success: function(data, textStatus, jqXHR){
                    console.log(data);
                    if(data == "1"){
                        window.location = '<?php echo base_url()?>system/register/register_tor/';
                    }
                }
            });
        }else{
            scrollToAnchor('anchorErrorForm');
        }
    });
});
</script>

<?php echo $templateFooter;?>