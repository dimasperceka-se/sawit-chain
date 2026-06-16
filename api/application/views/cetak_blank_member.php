<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Member Form</title>

<style type="text/css">

@media all {
    .page-break { display: none; }
}

@media print {
    @page { margin: 0.5cm; padding:0cm; }
    .page-break  { display: block; page-break-before: always; }
    .page-break-after { display: block; page-break-after: always; }

}

body {
    margin:0;
    padding:0;
    line-height: 1.5em;
    font-family: Tahoma, Verdana, Helvetica, Arial;
    font-size: 14px;
    color: #000000;
    background-color: #ffffff;
}
a:link, a:visited { color: #0066CC; text-decoration: none}
a:active, a:hover { color: #008800; text-decoration: underline}

#templatemo_container_wrapper {
    /*background: url(images/templatemo_side_bg.gif) repeat-x;*/
    background: #ffffff;
    margin:1px;
}
#templatemo_container {
    margin: 1px auto;
    /*background: url(images/templatemo_content_bg.gif);*/
    background: #FFFFFF;
}
#templatemo_top {
    clear: left;
    height: 25px;   /* 'padding-top' + 'height' must be equal to the 'background image height' */
    padding-top: 42px;
    padding-left: 30px;
    background: url(images/templatemo_top_bg.gif) no-repeat bottom;
}
#templatemo_header {
    clear: left;
    padding-top: 12px;
    height: 60px;
    text-align: center;
    font-weight: bold;
    font-size: 20px;
    color: #000000;
    /*background: url(images/templatemo_header_bg.gif) no-repeat;*/
}
#templatemo_header2 {
    clear: left;
    padding-top: 12px;
    height: 110px;
    text-align: center;
    font-weight: bold;
    font-size: 20px;
    color: #000000;
    /*background: url(images/templatemo_header_bg.gif) no-repeat;*/
}
#inner_header {
    height: 30px;
    background: url(images/templatemo_header.jpg) no-repeat center center;
}
#templatemo_left_column {
    width: 100%;
    page-break-after: always;
}
#templatemo_right_column {
    float: right;
    width: 216px;
    padding-right: 15px;
}
#templatemo_footer {
    clear: both;
    /*padding-top: 18px;*/
    height: 15px;
    text-align: center;
    font-size: 11px;
    /*background: url(images/templatemo_footer_bg.gif) no-repeat;*/
    color: #ffffff;
}
#templatemo_footer a {
    color: #666666;
}
#templatemo_site_title {
    padding-top: 65px;
    font-weight: bold;
    font-size: 28px;
    color: #000000;
}
#templatemo_site_slogan {
    padding-top: 14px;
    font-weight: bold;
    font-size: 13px;
    color: #AAFFFF;
}
.templatemo_spacer {
    clear: left;
    height: 18px;
}
.templatemo_pic {
    float: left;
    margin-right: 10px;
    margin-bottom: 10px;
    border: 1px solid #000000;
}
.section_box {
    margin: 10px;
    padding: 10px;
    border: 1px dashed #ffffff;
    background: #FFFFFF;
    border: 1px solid #000000;
}
.section_box2 {
    clear: left;
    margin-top: 5px;
    background: #ffffff;
    font-size: 12px;
    font-weight: bold;
}
.section_box3 {
    clear: left;
    margin-top: 10px;
    background: #ffffff;
    font-size: 12px;
}
.text_area {
    padding: 1px;
}
.publish_date {
    clear: both;
    margin-top: 10px;
    color: #999999;
    font-size: 11px;
    font-weight: bold;
}
.title {
    padding-bottom: 12px;
    font-size: 18px;
    font-weight: bold;
    color: #000000;
}
.subtitle {
    padding-bottom: 6px;
    font-size: 14px;
    font-weight: bold;
    color: #666666;
}
.post_title_main {
    padding: 6px;
    padding-left: 10px;
    background: #cccccc;
    font-size: 16px;
    font-weight: bold;
    color: #000000;
}
.post_title {
    padding: 6px;
    padding-left: 10px;
    background: #cccccc;
    font-size: 16px;
    font-weight: bold;
    color: #000000;
}
.templatemo_menu {
    list-style-type: none;
    margin: 10px;
    margin-top: 0px;
    padding: 0px;
    width: 195px;
}
.templatemo_menu li a{
    background: #F4F4F4 url(images/button_default.gif) no-repeat;
    font-size: 13px;
    font-weight: bold;
    color: #000000;
    display: block;
    width: auto;
    margin-bottom: 2px;
    padding: 5px;
    padding-left: 12px;
    text-decoration: none;
}
* html .templatemo_menu li a{
    width: 190px;
}
.templatemo_menu li a:visited, .templatemo_menu li a:active{
    color: #000000;
}
.templatemo_menu li a:hover{
    background: #EEEEEE url(images/button_active.gif) no-repeat;
    color: #FF3333;
}#templatemo_container_wrapper #templatemo_container #templatemo_left_column .text_area .section_box2 .post_title_main strong td {
     color: #FFF;
 }
#templatemo_container_wrapper #templatemo_container #templatemo_left_column .text_area .section_box2 .post_title_main {
    color: #000000;
}
div {
    color: #000000;
}
</style>

</head>
<body>
<div id="templatemo_container_wrapper">
    <div id="templatemo_container">
        <div id="templatemo_header">
            <table width="100%" cellspacing="0">
                <tr><td height="60" width="200px" align="center" style="vertical-align:middle;"><img src="<?=base_url()?>images/Photo/?>" style="max-width:100%; max-height:100%;"></td>
                    <td height="60" align="center" style="vertical-align:middle;">Form Member<br /><span style="text-decoration:underline;"></span></td>
                    <td height="60" width="200px" align="center" style="vertical-align:middle;"><img src="<?=base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;"></td>
                </tr>
            </table>
        </div>

        <div id="templatemo_left_column">
            <div class="text_area" align="center">
                <div class="section_box2" align="center">
                    <div class="text_area">
                        <br>
                        <table width="100%" cellspacing="0" style="border : 1px solid #000000;">
                            <tr style="background-color: #CCCCCC;padding:2px;">
                                <td width="80%" colspan="2">Member Details</td>
                                <td width="20%" colspan="2"></td>
                            </tr>
                            <tr style="padding:4px;">
                                <td width="10%">
                                </td>
                                <td width="30%">
                                    <input type="checkbox"> SCPP Farmer
                                </td>
                                <td width="10%">PHOTO &nbsp;&nbsp; &nbsp; &nbsp;  &nbsp;&nbsp; &nbsp; &nbsp;  &nbsp;&nbsp; &nbsp; &nbsp;  &nbsp;&nbsp; &nbsp; &nbsp;  SIGNATURE</td>
                            </tr> 
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                    Farmer ID
                                </td>
                                <td width="30%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="10%"></td>
                            </tr>
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                    Member Type
                                </td>
                                <td width="30%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="10%"></td>
                            </tr>
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                   Member No
                                </td>
                                <td width="30%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="10%"></td>
                            </tr>
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                   Name
                                </td>
                                <td width="30%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="10%"></td>
                            </tr>
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                  KTP No
                                </td>
                                <td width="30%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="10%"></td>
                            </tr>
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                   Member No
                                </td>
                                <td width="30%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="10%"></td>
                            </tr>
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                    Gender
                                </td>
                                <td width="30%">
                                    <input type="radio" name="gender"> Male <input type="radio" name="gender"> Female
                                </td>
                                <td width="10%"></td>
                            </tr> 
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                   Place/Date Birth
                                </td>
                                <td width="30%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="13">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="10">
                                </td>
                                <td width="10%"></td>
                            </tr>
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                   Address
                                </td>
                                <td width="30%">
                                    <textarea cols="22" rows="7"></textarea>
                                </td>
                                <td width="10%"></td>
                            </tr>
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                    Vilage ID
                                </td>
                                <td width="0%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="30%"></td>
                            </tr> 
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                    Phone
                                </td>
                                <td width="0%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="30%"></td>
                            </tr>      
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                    Profession
                                </td>
                                <td width="0%">
                                    <input style = "border: 1px solid #999;background-color: #FFF"  type="text" value="" disabled size="35">
                                </td>
                                <td width="30%"></td>
                            </tr>    
                            
                            <tr style="padding:4px;">
                                <td width="10%">
                                    Marital Status
                                </td>
                                <td width="30%">
                                    <input type="radio" name="gender"> Single <input type="radio" name="gender"> Married
                                </td>
                                <td width="10%"></td>
                            </tr>                 
                        </table>
                    </div>
                </div>
                           </div>
        </div>

        <div class="page-break"></div>
        <div class="page-break"></div>


</body>
</html>
