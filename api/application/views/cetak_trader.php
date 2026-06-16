<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8">
	<title>Farmer</title>

<style>
 body {
    margin: 0;
    padding: 0;
    background-color: #FAFAFA;
    font: 9pt "verdana";
}
* {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}
.page {
    width: 21cm;
    height:27.5cm;
    padding: 1.5cm;
    margin: 0.2cm auto;
    border: 1px #D3D3D3 solid;
    border-radius: 5px;
    background: white;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

@page {
    size: A4;
    margin: 0;
}

@media print {
    .page {
        margin: initial;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
        background-color: initial;

        padding-bottom:0;
    }
   .header{border:90px #cccccc;}
}

input {border: 1px solid;background-color: #FFF}
input:disabled {color: #000000;}
.line{border: 1px solid black;}
td{vertical-align:top}
@media all {
   .header{  font-size: 10pt;font-family: verdana;padding: 0px;margin: 0px;border: 1px solid;font-weight: bold;}
   .body{font-size: 9pt;font-family: verdana;font-weight: normal;padding: 0px;margin: 0px;border: 1px solid; border-top: none;}
   .header_div{width: 100%; height: 20px; margin-bottom: -28px; border-top: 28px solid #cccccc;}
}     
</style>
</head>
<body>
<div class="page">
   <table width="100%" cellspacing="0">
      <tr><td height="60"  width="25%" rowspan="2" align="center" style="vertical-align:middle;"><img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:100%; max-height:100%;"></td>
         <td height="60" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;">DATA PEDAGANG</td>
         <td height="60" rowspan="2" width="25%" align="center" style="vertical-align:middle;"><img src="<?=base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;"></td></tr>
   </table>

   <div class="header_div"></div>
  <table width="100%" cellspacing="4" class="header">
   <tr><td>Data Umum Pemilik</td>
      <td align="right">ID Pedagang - <?=$data['TraderID']?></td></tr>
  </table>
   <table width="100%" cellspacing="2" class="body">
   <tr><td width="20%">Nama Pedagang</td>
      <td class="line"><?=$data['TraderName']?></td></tr>
   <tr><td>No Identitas</td>
      <td class="line"><?=$data['IdentityNum']?></td></tr>
   <tr>
     <td>Tanggal Lahir</td>
     <td class="line"><?=$data['Birthdate']?></td>
   </tr>
   <tr>
     <td>Jenis Kelamin</td>
     <td class="line"><input disabled type="radio" <?=$data['Sex']=='1'?'checked':''?>> Laki-laki
     <input disabled type="radio" <?=$data['Sex']=='2'?'checked':''?>> Perempuan</td>
   </tr>
   <tr>
     <td>Handphone</td>
     <td class="line"><?=$data['Handphone']?></td>
   </tr>
   <!--<tr>
     <td>No Telepon</td>
     <td class="line"><?=$data['NoTelp']?></td>
   </tr>-->
   <tr>
     <td>Email</td>
     <td class="line"><?=$data['Email']?></td>
   </tr>
   <tr><td>Pendidikan Terakhir</td>
      <td class="line">
         <table><tr><td><input disabled type="radio" <?=$data['Education']=='1'?'checked':''?>> Tidak Pernah Sekolah</td>
            <td><input disabled type="radio" <?=$data['Education']=='2'?'checked':''?>> Tidak Tamat SD</td></tr>
            <tr><td><input disabled type="radio" <?=$data['Education']=='3'?'checked':''?>> Tamat SD Tidak Melanjutkan</td>
            <td><input disabled type="radio" <?=$data['Education']=='4'?'checked':''?>> Tamat SMP</td></tr>
            <tr><td><input disabled type="radio" <?=$data['Education']=='5'?'checked':''?>> Tamat SMA/SMK</td>
            <td><input disabled type="radio" <?=$data['Education']=='6'?'checked':''?>> Tamat Perguruan Tinggi</td></tr></table>
      </td></tr>
   </table>

   <div class="header_div"></div>
   <table width="100%" cellspacing="4" class="header"><tr><td>Data Umum Usaha</td></tr></table>
   <table width="100%" cellspacing="2" class="body">
   <tr><td width="20%">Nama Perusahaan</td>
      <td class="line"><?=$data['Company']?></td></tr>
   <tr><td>Nama Alias/Singkatan</td>
      <td class="line"><?=$data['CompanyAlias']?></td></tr>
   <tr><td>Provinsi</td>
      <td class="line"><?=$data['Province']?></td></tr>
   <tr><td>Kabupaten</td>
      <td class="line"><?=$data['District']?></td></tr>
   <tr><td>Kecamatan</td>
      <td class="line"><?=$data['SubDistrict']?></td></tr>
   <tr><td>Desa</td>
      <td class="line"><?=$data['Village']?></td></tr>
   <tr><td>Alamat</td>
      <td class="line"><?=$data['Address']?></td></tr>
   <tr><td>Latitude</td>
      <td class="line"><?=$data['Latitude']?></td></tr>
   <tr><td>Longitude</td>
      <td class="line"><?=$data['Longitude']?></td></tr>
<!--   <tr><td>Elevasi</td>
      <td class="line"><?=$data['Elevation']?></td></tr>-->
   <tr><td>No Telp</td>
      <td class="line"><?=$data['NoTelp']?></td></tr>
   <tr><td>Status hukum Perusahaan</td>
      <td class="line">
         <table width="317"><tr><td width="77"><input disabled type="radio" <?=$data['CompanyStatus']=='UD'?'checked':''?>> UD</td>
            <td width="93"><input disabled type="radio" <?=$data['CompanyStatus']=='Firma'?'checked':''?>> Firma</td></tr>
            <tr><td><input disabled type="radio" <?=$data['CompanyStatus']=='CV'?'checked':''?>> CV</td>
            <td><input disabled type="radio" <?=$data['CompanyStatus']=='Koperasi'?'checked':''?>> Koperasi</td></tr>
            <tr><td><input disabled type="radio" <?=$data['CompanyStatus']=='PT'?'checked':''?>> PT</td>
            <td><input disabled type="radio" <?=$data['CompanyStatus']=='Tidak Berbadan Hukum'?'checked':''?>> Tidak Berbadan Hukum</td></tr></table>
      </td></tr>
   </table>

   <div class="header_div"></div>
  <table width="100%" cellspacing="4" class="header"><tr><td>Status Karyawan</td></tr></table>
  <table width="100%" cellspacing="2" class="body">
   <tr><td style="border-bottom:1px solid">Karyawan Tetap</td>
      <td>Jumlah Karyawan</td></tr>
   <tr><td width="375">Laki-laki</td><td class="line"><?=$data['PermanentEmployeeMale']?></td></tr>
   <tr><td>Perempuan</td><td class="line"><?=$data['TemporaryEmployeeMale']?></td></tr>
      <tr>
      <td colspan="2">&nbsp;</td>
      <tr>
      <td style="border-bottom:1px solid">Karyawan Tidak Tetap</td><td><td>
      </tr>
      <tr>
        <td height="16">Laki-laki</td>
        <td class="line"><?=$data['PermanentEmployeeFemale']?></td>
      </tr>
      <tr>
        <td height="16">Perempuan</td>
        <td class="line"><?=$data['TemporaryEmployeeFemale']?></td>
      </tr>
      <tr>
        <td height="16">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="16" style="border-bottom:1px solid">Anggota Keluarga yang Bekerja didalam perusahaan</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="16">Laki-laki</td>
        <td class="line" width="140"><?=$data['FamilyMembersMale']?></td>
    </tr>
      <tr>
      <td height="16">Perempuan</td>
      <td class="line"><?=$data['FamilyMembersFemale']?></td>
    </tr>
   </table>
<!--
</div><div class="page">
   <table width="100%" cellspacing="0">
      <tr><td height="60"  width="25%" rowspan="2" align="center" style="vertical-align:middle;"><img src="<?=base_url()?>images/Photo/<?=$logo['Photo']?>" style="max-width:100%; max-height:100%;"></td>
         <td height="60" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;">DATA PEDAGANG</td>
         <td height="60" rowspan="2" width="25%" align="center" style="vertical-align:middle;"><img src="<?=base_url()?>images/swisscontact.png" style="max-width:100%; max-height:100%;"></td></tr>
   </table>

   <div class="header_div"></div>
   <table width="100%" cellspacing="4" class="header"><tr><td width="30%">Data Staff</td><td align="center">Nama Pedagang : <?=$data['TraderName']?></td>
      <td width="30%" align="right">ID Pedagang : <?=$data['TraderID']?></td></tr></table>
   <table width="100%" cellspacing="0" class="body" style="border-collapse: collapse;">
   <tr><th style = "border: 1px solid;">Nama Staff</th><th style = "border: 1px solid;">Posisi</th><th style = "border: 1px solid;">Handphone</th><th style = "border: 1px solid;">Email</th><th style = "border: 1px solid;">Tanggal Lahir</th><th style = "border: 1px solid;">Jenis Kelamin</th></tr>
   <?for ($i=0;$i<10;$i++) {?>
      <tr><td  width=130 style = "border: 1px solid;"><?=$data['StaffName']?></td>
         <td style = "border: 1px solid;"><input disabled type="radio" <?=$data['Position']=='pemilik'?'checked':''?>> Pemilik<br>
         <input disabled type="radio" <?=$data['Position']=='coordinator'?'checked':''?>> Koordinator<br>
         <input disabled type="radio" <?=$data['Position']=='staff'?'checked':''?>> Staff</td>
         <td style = "border: 1px solid;"><?=$data['PrivateCellphone']?></td>
         <td width=120 style = "border: 1px solid;"><?=$data['PrivateStaffEmail']?></td>
         <td style = "border: 1px solid;"><?=$data['StaffBirth']?></td>
         <td style = "border: 1px solid;"><input disabled type="radio" <?=$data['StaffGender']=='1'?'checked':''?>> Laki-laki<br>
            <input disabled type="radio" <?=$data['StaffGender']=='2'?'checked':''?>> Perempuan</td></tr>
   <?}?>
   </table>

-->
</div>
</body>
</html>
