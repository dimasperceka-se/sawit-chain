<style>
    
    .x-panel-body-default,table {
        background: #FFF !important;
    }
</style>
<?php 
    $totalrows_side_left = count($parent_group_aktiva);
    $totalrows_side_right = count($parent_group_kewajiban) + count($parent_group_modal);
    $totalaktiva = 0;
    
    foreach($parent_group_aktiva as $key_aktiva => $aktiva) {
        $totalrows_side_left++;
    }
    
    foreach($parent_group_kewajiban as $key_kewajiban => $kewajiban) {
        $totalrows_side_right++;
    }
    
    foreach($parent_group_modal as $key_modal => $modal) {
        $totalrows_side_right++;
    }
    
    $needrows = $totalrows_side_left - $totalrows_side_right;
    $month = ['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'];
?>
<center style="padding:20px;">
    <div style="width: 100%; max-width: 1280px;border:1px solid;padding:6px 8px;margin-bottom: 1px;">LAPORAN NERACA</div>
    <div style="width: 100%; max-width: 1280px;border:1px solid;padding:5px;">PERIODE <?php echo $month[$start_month-1] . ' - ' . $month[$end_month-1] . ' ' . $year ?></div>
    <div style="position: relative; margin:0px auto; text-align: center;width:100%;max-width: 1280px;">
        <div style="position:absolute;left:0px;top:0px;width:50%;max-width: 640px;margin:0;">
            <table border="1" style="border-collapse: collapse;">
                <tr>
                    <td style="padding:5px; text-align: center;font-weight: bold;" colspan="3">AKTIVA</td>
                </tr>
                <tr>
                    <td style="padding:5px; text-align: center; font-size: .9em;font-weight: bold; width: 5%;">KODE</td>
                    <td style="padding:5px; text-align: center; font-size: .9em; font-weight: bold;">DESKRIPSI</td>
                    <td style="padding:5px; text-align: center; font-size: .9em; font-weight: bold; width: 15%">JUMLAH</td>
                </tr>
                <?php
                    foreach($parent_group_aktiva as $key_aktiva => $aktiva) {
                ?>
                    <tr>
                        <td style="padding:5px; text-align: left; font-size: .9em;width: 5%; background-color: #F4F4F4;"><?php echo $aktiva['CoaGroupCode']; ?></td>
                        <td style="padding:5px; text-align: left; font-size: .9em; background-color: #F4F4F4;" colspan="2"><?php echo $aktiva['CoaGroupTitle']; ?></td>
                    </tr>
                    <?php 
                        if(count($aktiva['children']) > 0){
                            foreach($aktiva['children'] as $akey => $aktivachild){
                                $totalaktiva += $aktivachild['saldo'];
                                $aktivachild['saldo'] = $aktivachild['saldo']<0?'('.number_format(abs($aktivachild['saldo']),2).')':number_format($aktivachild['saldo'],2);
                    ?>
                                <tr>
                                    <td style="padding:5px; text-align: left; font-size: .9em; width: 5%;"><?php echo $aktivachild['CoaGroupCode']; ?></td>
                                    <td style="padding:5px; text-align: left; font-size: .9em;"><?php echo $aktivachild['CoaGroupTitle']; ?></td>
                                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo $aktivachild['saldo']; ?></td>
                                </tr>
                    <?php
                            }
                        }
                    ?>
                    <tr>
                        <td style="padding:5px; text-align: left; font-size: .9em; width: 5%;">&nbsp;</td>
                        <td style="padding:5px; text-align: left; font-size: .9em;">Total <?php echo $aktiva['CoaGroupTitle']; ?></td>
                        <td style="padding:5px; text-align: right; font-size: .9em; width: 15%;"><?php echo number_format($totalaktiva); ?></td>
                    </tr>
                <?php
                        
                    }
                ?>
                <?php
                if($needrows < 0) {
                    for($i = 0; $i < abs($needrows); $i++) {
                ?>
                        <tr>
                            <td style="padding:5px; text-align: left; font-size: .9em; width: 5%;">&nbsp;</td>
                            <td style="padding:5px; text-align: left; font-size: .9em;">&nbsp;</td>
                            <td style="padding:5px; text-align: center; font-size: .9em; width: 15%">&nbsp;</td>
                        </tr>
                <?php
                    }
                }
                ?>
                <tr>
                    <td style="padding:5px; text-align: left; font-size: .9em; width: 5%;">&nbsp;</td>
                    <td style="padding:5px; text-align: left; font-size: .9em; font-weight:bold;">Total</td>
                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%; font-weight:bold;">0</td>
                </tr>
            </table>
        </div>
        <div style="position:absolute;right:0px;top:0px;width:50%;max-width:640px;margin:0;">
            <table border="1" style="border-collapse: collapse;padding:0;margin:0;">
                <tr>
                    <td style="padding:5px; text-align: center;font-weight: bold;" colspan="3">PASIVA</td>
                </tr>
                <tr>
                    <td style="padding:5px; text-align: center; font-size: .9em;font-weight: bold; width: 5%;">KODE</td>
                    <td style="padding:5px; text-align: center; font-size: .9em; font-weight: bold;">DESKRIPSI</td>
                    <td style="padding:5px; text-align: center; font-size: .9em; font-weight: bold; width: 15%">JUMLAH</td>
                </tr>
                <?php //group kewajiban
                    foreach($parent_group_kewajiban as $key_kewajiban => $kewajiban) {
                ?>
                    <tr>
                        <td style="padding:5px; text-align: left; font-size: .9em;width: 5%; background-color: #F4F4F4;"><?php echo $kewajiban['CoaGroupCode']; ?></td>
                        <td style="padding:5px; text-align: left; font-size: .9em; background-color: #F4F4F4;" colspan="2"><?php echo $kewajiban['CoaGroupTitle']; ?></td>
                    </tr>
                    <?php 
                        if(count($kewajiban['children']) > 0){
                            foreach($kewajiban['children'] as $akey => $kewajibanchild){
                                $kewajibanchild['saldo'] = $kewajibanchild['saldo']<0?'('.number_format(abs($kewajibanchild['saldo']),2).')':number_format($kewajibanchild['saldo'],2);
                    ?>
                                <tr>
                                    <td style="padding:5px; text-align: left; font-size: .9em;width: 5%;"><?php echo $kewajibanchild['CoaGroupCode']; ?></td>
                                    <td style="padding:5px; text-align: left; font-size: .9em;"><?php echo $kewajibanchild['CoaGroupTitle']; ?></td>
                                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo $kewajibanchild['saldo']; ?></td>
                                </tr>
                    <?php
                            }
                        }
                    ?>
                <?php        
                    }
                ?>
                <tr>
                    <td style="padding:5px; text-align: left; font-size: .9em; width: 5%;">&nbsp;</td>
                    <td style="padding:5px; text-align: left; font-size: .9em;">Total <?php echo $kewajiban['CoaGroupTitle']; ?></td>
                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%;"><?php echo number_format($totalaktiva); ?></td>
                </tr>
                <?php //modal
                    foreach($parent_group_modal as $key_modal => $modal) {
                ?>
                    <tr>
                        <td style="padding:5px; text-align: left; font-size: .9em;width: 5%; background-color: #F4F4F4;"><?php echo $modal['CoaGroupCode']; ?></td>
                        <td style="padding:5px; text-align: left; font-size: .9em; background-color: #F4F4F4;" colspan="2"><?php echo $modal['CoaGroupTitle']; ?></td>
                    </tr>
                    <?php 
                        if(count($modal['children']) > 0){
                            foreach($modal['children'] as $akey => $modalchild){
                                $modalchild['saldo'] = $modalchild['saldo']<0?'('.number_format(abs($modalchild['saldo']),2).')':number_format($modalchild['saldo']);
                    ?>
                                <tr>
                                    <td style="padding:5px; text-align: left; font-size: .9em;width: 5%;"><?php echo $modalchild['CoaGroupCode']; ?></td>
                                    <td style="padding:5px; text-align: left; font-size: .9em;"><?php echo $modalchild['CoaGroupTitle']; ?></td>
                                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo $modalchild['saldo']; ?></td>
                                </tr>
                    <?php
                            }
                        }
                    ?>
                    <tr>
                        <td style="padding:5px; text-align: left; font-size: .9em; width: 5%;">&nbsp;</td>
                        <td style="padding:5px; text-align: left; font-size: .9em;">Total <?php echo $modal['CoaGroupTitle']; ?></td>
                        <td style="padding:5px; text-align: right; font-size: .9em; width: 15%;"><?php echo number_format($totalaktiva); ?></td>
                    </tr>
                <?php        
                    }
                ?>
                <?php
                if($needrows > 0) {
                    for($i = 0; $i < abs($needrows); $i++) {
                ?>
                        <tr>
                            <td style="padding:5px; text-align: left; font-size: .9em; width: 5%;">&nbsp;</td>
                            <td style="padding:5px; text-align: left; font-size: .9em;">&nbsp;</td>
                            <td style="padding:5px; text-align: center; font-size: .9em; width: 15%">&nbsp;</td>
                        </tr>
                <?php
                    }
                }
                ?>
                <tr>
                    <td style="padding:5px; text-align: left; font-size: .9em; width: 5%;">&nbsp;</td>
                    <td style="padding:5px; text-align: left; font-size: .9em;">Total</td>
                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%;font-weight: bold;"><?php echo number_format($totalaktiva); ?></td>
                </tr>
            </table>
        </div>
    </div>
</center>
