<table>
  <tr>
  <?php
    foreach($cols as $col) { ?>
        <td><?php echo $col; ?></td>
  <?php
    }
  ?>
  </tr>
  <?php
    foreach($data as $key => $value) {
      foreach($cols as $colv){ ?>
        <td><?php echo $value[$colv]; ?></td>
  <?php
      }
    }
  ?>
</table>
