<!DOCTYPE html>
<html class="html" lang="en-US">
 <head>

  <script type="text/javascript">
   if(typeof Muse == "undefined") window.Muse = {}; window.Muse.assets = {"required":["jquery-1.8.3.min.js", "museutils.js", "jquery.watch.js", "index.css"], "outOfDate":[]};
</script>

  <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
  <meta name="generator" content="2015.0.1.310"/>
  <title>Form Pendaftaran</title>
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/site_global_coopmember.css?4052507572"/>
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/index_coopmember.css?3851972549" id="pagesheet"/>
  <!--[if lt IE 9]>
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/iefonts_index_coopmember.css?221592488"/>
  <![endif]-->
  <!-- Other scripts -->
  <script type="text/javascript">
   document.documentElement.className += ' js';
var __adobewebfontsappname__ = "muse";
</script>
  <!-- JS includes -->
  <script type="text/javascript">
   document.write('\x3Cscript src="' + (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//webfonts.creativecloud.com/ubuntu:n7,i4:default;varela-round:n4:default.js" type="text/javascript">\x3C/script>');
</script>
   </head>
 <body>

  <div class="clearfix" id="page"><!-- column -->
   <div class="clearfix colelem" id="pu190"><!-- group -->
    <div class="clip_frame grpelem" id="u190"><!-- image -->
     <img class="block" id="u190_img" src="<?=base_url()?>assets/images/logo%2001.jpg" alt="" width="75" height="73"/>
    </div>
    <div class="clip_frame grpelem" id="u196"><!-- image -->
     <img class="block" id="u196_img" src="<?=base_url()?>assets/images/logo%2002.jpg" alt="" width="114" height="73"/>
    </div>
    <div class="clearfix grpelem" id="u77-10"><!-- content -->
     <p id="u77-2">KOPERASI GABUNGAN GAPOKTAN (KGG)</p>
     <p id="u77-4">KAB. KOLAKA TIMUR</p>
     <p id="u77-6">Sekretariat: Desa Tokai Kecamatan Poli-Polia</p>
     <p id="u77-8">Email: koperasi.kgg@gmail.com</p>
    </div>
   </div>
   <div class="clearfix colelem" id="pu79"><!-- column -->
    <div class="colelem" id="u79"><!-- simple frame --></div>
    <div class="clearfix colelem" id="pu80"><!-- group -->
     <div class="clearfix grpelem" id="u80"><!-- group -->
      <div class="clearfix grpelem" id="u230-4"><!-- content -->
       <p>NO. ANGGOTA</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u232">&nbsp;<span style="font-size:10px;"><?=$d['primaryNo']?></span><!-- simple frame --></div>
     </div>
     <div class="clearfix grpelem" id="u231-4"><!-- content -->
      <p>:</p>
     </div>
    </div>
   </div>
   <div class="clearfix colelem" id="pu87"><!-- column -->
    <div class="colelem" id="u87"><!-- simple frame --></div>
    <div class="clearfix colelem" id="u88-4"><!-- content -->
     <p>DATA ANGGOTA</p>
    </div>
   </div>
   <div class="colelem" id="u115"><!-- simple frame --></div>
   <div class="clearfix colelem" id="pu90"><!-- group -->
    <div class="clearfix grpelem" id="u90"><!-- column -->
     <div class="clearfix colelem" id="pu91-4"><!-- group -->
      <div class="clearfix grpelem" id="u91-4"><!-- content -->
       <p>Nama Lengkap</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u93">&nbsp;<?=$d['name']?></div>
     </div>
     <div class="clearfix colelem" id="pu96-4"><!-- group -->
      <div class="clearfix grpelem" id="u96-4"><!-- content -->
       <p>No. Identitas</p>
      </div>
      <div class="clearfix grpelem" id="u202-4"><!-- content -->
       <p>:</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u98">&nbsp;<?=$d['identityNumber']?></div>
      <div class="clearfix grpelem" id="u204-4"><!-- content -->
       <p>Jenis Kelamin :&nbsp;<?php
       if($d['gender']==1)
       {
        echo 'Laki-laki';
       } else {
        echo 'Perempuan';
       }
       ?></p>
      </div>

     </div>
     <div class="clearfix colelem" id="pu206-6"><!-- group -->
      <div class="clearfix grpelem" id="u206-6"><!-- content -->
       <p>Tempat dan Tanggal</p>
       <p>Lahir</p>
      </div>
      <div class="clearfix grpelem" id="u207-4"><!-- content -->
       <p>:</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u208">&nbsp;<?=$d['placeOfBirth']?> / <?=$d['dateOfBirth']?></div>
     </div>
     <div class="clearfix colelem" id="pu209-4"><!-- group -->
      <div class="clearfix grpelem" id="u209-4"><!-- content -->
       <p>Kabupaten/Kota</p>
      </div>
      <div class="clearfix grpelem" id="u213-4"><!-- content -->
       <p>:</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u214">&nbsp;<?=$d['district']?></div>
     </div>
     <div class="clearfix colelem" id="pu215-4"><!-- group -->
      <div class="clearfix grpelem" id="u215-4"><!-- content -->
       <p>Kecamatan</p>
      </div>
      <div class="clearfix grpelem" id="u216-4"><!-- content -->
       <p>:</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u217">&nbsp;<?=$d['subDistrict']?></div>
     </div>
     <div class="clearfix colelem" id="pu218-4"><!-- group -->
      <div class="clearfix grpelem" id="u218-4"><!-- content -->
       <p>Desa</p>
      </div>
      <div class="clearfix grpelem" id="u219-4"><!-- content -->
       <p>:</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u220">&nbsp;<?=$d['Village']?></div>
     </div>
     <div class="clearfix colelem" id="pu221-4"><!-- group -->
      <div class="clearfix grpelem" id="u221-4"><!-- content -->
       <p>Telepon</p>
      </div>
      <div class="clearfix grpelem" id="u222-4"><!-- content -->
       <p>:</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u223">&nbsp;<?=$d['phone']?></div>
     </div>
     <div class="clearfix colelem" id="pu224-4"><!-- group -->
      <div class="clearfix grpelem" id="u224-4"><!-- content -->
       <p>Pekerjaan</p>
      </div>
      <div class="clearfix grpelem" id="u225-4"><!-- content -->
       <p>:</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u226">&nbsp;<?=$d['job']?></div>
     </div>
     <div class="clearfix colelem" id="pu227-4"><!-- group -->
      <div class="clearfix grpelem" id="u227-4"><!-- content -->
       <p>Status Pernikahan</p>
      </div>
      <!-- <div class="gradient rounded-corners grpelem" id="u143"><!-- simple frame --></div>
      <div class="clearfix grpelem" id="u142-4"><!-- content -->
       <p><?php
          if($d['maritalStatus']==1)
          {
            echo 'Lajang';
          } else if($d['maritalStatus']==2)
          {
            echo 'Menikah';
          } else if($d['maritalStatus']==3)
          {
            echo 'Duda/Janda';
          }
        ?></p>
      </div>


    </div>
    <div class="clearfix grpelem" id="u92-4"><!-- content -->
     <p>:</p>
    </div>
    <div class="clearfix grpelem" id="u228-4"><!-- content -->
     <p>:</p>
    </div>
    <div class="clearfix grpelem" id="u205-4"><!-- content -->
     <p></p>
    </div>
   </div>
   <div class="colelem" id="u148"><!-- simple frame --></div>
   <div class="clearfix colelem" id="u149-4"><!-- content -->
    <p>DATA PETANI BINAAN PROGRAM SCPP</p>
   </div>
   <div class="colelem" id="u150"><!-- simple frame --></div>
   <div class="clearfix colelem" id="u151"><!-- group -->
    <div class="clearfix grpelem" id="pu288-4"><!-- column -->
     <div class="clearfix colelem" id="u288-4"><!-- content -->
      <p>ID Petani SCPP</p>
     </div>
     <div class="clearfix colelem" id="u289-4"><!-- content -->
      <p>Nama Kelompok Tani</p>
     </div>
    </div>
    <div class="clearfix grpelem" id="pu290-4"><!-- column -->
     <div class="clearfix colelem" id="u290-4"><!-- content -->
      <p>:</p>
     </div>
     <div class="clearfix colelem" id="u291-4"><!-- content -->
      <p>:</p>
     </div>
    </div>
    <div class="clearfix grpelem" id="pu292"><!-- column -->
     <div class="gradient rounded-corners colelem" id="u292"><?php if(isset($d['farmerID'])) { echo '&nbsp;'.$d['farmerID']; }?><!-- simple frame --></div>
     <div class="gradient rounded-corners colelem" id="u293">&nbsp;<?=$d['GroupName']?></div>
    </div>
   </div>
   <div class="colelem" id="u257"><!-- simple frame --></div>
   <div class="clearfix colelem" id="u258-4"><!-- content -->
    <p>KETENTUAN KEANGGOTAAN KOPERASI</p>
   </div>
   <div class="clearfix colelem" id="pu259"><!-- group -->
    <div class="grpelem" id="u259"><!-- simple frame --></div>
    <div class="clearfix grpelem" id="u260"><!-- group -->
     <div class="clearfix grpelem" id="pu261-4"><!-- column -->
      <div class="clearfix colelem" id="u261-4"><!-- content -->
       <p>SIMPANAN PERTAMA KEANGGOTAAN BARU</p>
      </div>
      <div class="clearfix colelem" id="pu264-4"><!-- group -->
       <div class="clearfix grpelem" id="u264-4"><!-- content -->
        <p>Simpanan Pokok</p>
       </div>
       <div class="clearfix grpelem" id="u263-4"><!-- content -->
        <p>Rp</p>
       </div>
       <div class="gradient rounded-corners grpelem" id="u262"><span style="float:right;"><?=number_format($d['simpananPokok'])?>&nbsp;</span></div>
       <div class="clearfix grpelem" id="u265-4"><!-- content -->
        <p>Jenis Anggota</p>
       </div>
      </div>
      <div class="clearfix colelem" id="pu266-6"><!-- group -->
       <div class="clearfix grpelem" id="u266-6"><!-- content -->
        <p id="u266-2">Simpanan Wajib</p>
        <p id="u266-4"><span id="u266-3">(Setiap Bulan)</span></p>
       </div>
       <div class="gradient rounded-corners grpelem" id="u267"><span style="float:right;"><?=number_format($d['simpananPokok'])?>&nbsp;</span></div>
      </div>
      <div class="clearfix colelem" id="pu270-4"><!-- group -->
       <div class="clearfix grpelem" id="u270-4"><!-- content -->
        <p>Uang Pangkal</p>
       </div>
       <div class="clearfix grpelem" id="u272-4"><!-- content -->
        <p>Rp</p>
       </div>
       <div class="gradient rounded-corners grpelem" id="u273"><span style="float:right;"><?=number_format($d['simpananPokok'])?>&nbsp;</span></div>
      </div>
      <div class="colelem" id="u277"><!-- simple frame --></div>
      <div class="clearfix colelem" id="pu279-4"><!-- group -->
       <div class="clearfix grpelem" id="u279-4"><!-- content -->
        <p>Jumlah</p>
       </div>
       <div class="clearfix grpelem" id="u280-4"><!-- content -->
        <p>Rp</p>
       </div>
       <div class="gradient rounded-corners grpelem" id="u278"><span style="float:right;"><?=number_format($d['simpananPokok'])?>&nbsp;</span></div>
      </div>
     </div>
     <div class="clearfix grpelem" id="u269-4"><!-- content -->
      <p>: <?=$d['typeName']?></p>
     </div>


    </div>
    <div class="clearfix grpelem" id="u268-4"><!-- content -->
     <p>Rp</p>
    </div>
   </div>
   <div class="clearfix colelem" id="pu174-11"><!-- group -->
    <div class="clearfix grpelem" id="u174-11"><!-- content -->
     <p id="u174-2">DENGAN MENANDATANGANI FORMULIR INI:</p>
     <ol class="list0 nls-None" id="u174-9">
      <li id="u174-4">Saya sepenuhnya bertanggung jawab atas kebenaran informasi yang saya berikan dan tunduk kepada aturan informasi yang saya berikan</li>
      <li id="u174-6">Saya memberikan kuasa kepada koperasi terkait untuk mendebet dan melakukan penyerangan iuran atas nama saya.</li>
      <li id="u174-8">Saya setuju untuk menerima laporan dan informasi lainnya dalam bentuk cetak.</li>
     </ol>
    </div>
    <div class="clearfix grpelem" id="ppu181-4"><!-- column -->
     <div class="clearfix colelem" id="pu181-4"><!-- group -->
      <div class="clearfix grpelem" id="u181-4"><!-- content -->
       <p>Tanggal</p>
      </div>
      <div class="gradient rounded-corners grpelem" id="u182">&nbsp;<?=$regdate[2].'-'.$regdate[1].'-'.$regdate[0]?></div>


     </div>
     <div class="clearfix colelem" id="pu178"><!-- group -->
      <div class="clearfix grpelem" id="u178"><!-- group -->
       <div class="clearfix grpelem" id="u187-4"><!-- content -->
        <p>Petugas</p>
       </div>
      </div>
      <div class="clearfix grpelem" id="u179"><!-- group -->
       <div class="clearfix grpelem" id="u297-4"><!-- content -->
        <p>Pemohon</p>
       </div>
      </div>
     </div>
     <div class="clearfix colelem" id="pu176"><!-- group -->
      <div class="grpelem" id="u176"><!-- simple frame --></div>
      <div class="grpelem" id="u177"><!-- simple frame --></div>
     </div>
    </div>
   </div>
   <div class="clearfix colelem" id="u175-9"><!-- content -->
    <p id="u175-2">DOKUMEN YANG DILAMPIRKAN:</p>
    <ol class="list0 nls-None" id="u175-7">
     <li id="u175-4">Photo Copy KTP</li>
     <li id="u175-6">Photo Copy Kartu Keluarga</li>
    </ol>
   </div>
   <div class="verticalspacer"></div>
  </div>
  <!-- JS includes -->
  <script type="text/javascript">
   if (document.location.protocol != 'https:') document.write('\x3Cscript src="https://code.jquery.com/jquery-1.8.3.min.js" type="text/javascript">\x3C/script>');
</script>
  <script type="text/javascript">
   window.jQuery || document.write('\x3Cscript src="https://code.jquery.com/jquery-1.8.3.min.js" type="text/javascript">\x3C/script>');
</script>
  <script src="<?=base_url()?>assets/js/museutils.js?334180058" type="text/javascript"></script>
  <script src="<?=base_url()?>assets/js/jquery.watch.js?293013060" type="text/javascript"></script>
  <!-- Other scripts -->
  <script type="text/javascript">
   $(document).ready(function() { try {
(function(){var a={},b=function(a){if(a.match(/^rgb/))return a=a.replace(/\s+/g,"").match(/([\d\,]+)/gi)[0].split(","),(parseInt(a[0])<<16)+(parseInt(a[1])<<8)+parseInt(a[2]);if(a.match(/^\#/))return parseInt(a.substr(1),16);return 0};(function(){$('link[type="text/css"]').each(function(){var b=($(this).attr("href")||"").match(/\/?css\/([\w\-]+\.css)\?(\d+)/);b&&b[1]&&b[2]&&(a[b[1]]=b[2])})})();(function(){$("body").append('<div class="version" style="display:none; width:1px; height:1px;"></div>');
for(var c=$(".version"),d=0;d<Muse.assets.required.length;){var f=Muse.assets.required[d],g=f.match(/([\w\-\.]+)\.(\w+)$/),k=g&&g[1]?g[1]:null,g=g&&g[2]?g[2]:null;switch(g.toLowerCase()){case "css":k=k.replace(/\W/gi,"_").replace(/^([^a-z])/gi,"_$1");c.addClass(k);var g=b(c.css("color")),h=b(c.css("background-color"));g!=0||h!=0?(Muse.assets.required.splice(d,1),"undefined"!=typeof a[f]&&(g!=a[f]>>>24||h!=(a[f]&16777215))&&Muse.assets.outOfDate.push(f)):d++;c.removeClass(k);break;case "js":k.match(/^jquery-[\d\.]+/gi)&&
typeof $!="undefined"?Muse.assets.required.splice(d,1):d++;break;default:throw Error("Unsupported file type: "+g);}}c.remove();if(Muse.assets.outOfDate.length||Muse.assets.required.length)c="Some files on the server may be missing or incorrect. Clear browser cache and try again. If the problem persists please contact website author.",(d=location&&location.search&&location.search.match&&location.search.match(/muse_debug/gi))&&Muse.assets.outOfDate.length&&(c+="\nOut of date: "+Muse.assets.outOfDate.join(",")),d&&Muse.assets.required.length&&(c+="\nMissing: "+Muse.assets.required.join(",")),alert(c)})()})();
/* body */
Muse.Utils.transformMarkupToFixBrowserProblemsPreInit();/* body */
Muse.Utils.prepHyperlinks(true);/* body */
Muse.Utils.fullPage('#page');/* 100% height page */
Muse.Utils.showWidgetsWhenReady();/* body */
Muse.Utils.transformMarkupToFixBrowserProblems();/* body */
} catch(e) { if (e && 'function' == typeof e.notify) e.notify(); else Muse.Assert.fail('Error calling selector function:' + e); }});
</script>
   </body>
</html>
