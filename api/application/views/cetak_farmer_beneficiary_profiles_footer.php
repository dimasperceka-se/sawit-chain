<?php
/**
 * @Author: nikolius
 * @Date:   2016-04-13 15:43:01
 */
?>

   <script type="text/javascript">
      if (document.location.protocol != 'https:') document.write('\x3Cscript src="<?php echo base_url();?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript">\x3C/script>');
   </script>
   <script type="text/javascript">
      window.jQuery || document.write('\x3Cscript src="<?php echo base_url();?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript">\x3C/script>');
   </script>
   <script src="<?php echo base_url();?>assets/js/print_beneficiary/museutils.js" type="text/javascript"></script>
   <script src="<?php echo base_url();?>assets/js/print_beneficiary/jquery.watch.js" type="text/javascript"></script>

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