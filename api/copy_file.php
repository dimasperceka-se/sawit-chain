<?
$folder = 'backup';
$file = array('summary_garden','nutrisi','ppi','summary_cpg','summary_master_training','summary_kader_training');
$mk = mkdir($folder.'/'.date('Ymd'), 0777);
for ($i=0;$i<sizeof($file);$i++) {
   copy($file[$i].'.xls', $folder.'/'.date('Ymd').'/'.$file[$i].'.xls');
}
?>
