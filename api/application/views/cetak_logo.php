<table width="100%" class="tabelNoBorder" border="0" cellpadding="2">
   <tr>
      <td width="20%" align="center" style="vertical-align:middle;">
         <img src="<?php echo base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
      </td>
    <?php
      for ($i=0;$i<count($logos);$i++) {
          if ($logos[$i]['Photo']!='') {
              ?>
      <td height="60px" width="20%" align="center" style="vertical-align:middle;">
         <img src="<?php echo base_url() ?>images/Photo/<?php echo $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
      </td>
    <?php

          }
      }
    ?>
   <td width="20%" align="center" style="vertical-align:middle;">
      <img src="<?php echo base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:135px; max-height: 55px;">
   </td>
</tr>
</table>
<br /><br />