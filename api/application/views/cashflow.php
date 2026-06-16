<style>
    
    .x-panel-body-default,table {
        background: #FFF !important;
    }
</style>

<center style="padding:20px;">
    <div style="width: 100%; max-width: 1280px;border:1px solid;padding:6px 8px;margin-bottom: 1px;">LAPORAN ARUS KAS</div>
    <div style="position: relative; margin:0px auto; text-align: center;width:100%;max-width: 1280px;">
        <table border="1" style="border-collapse: collapse;width:100%;max-width: 1280px;">
            <tr>
                <td style="padding:5px; text-align: center; font-size: .9em;font-weight: bold; width: 7%;">TGL</td>
                <td style="padding:5px; text-align: center; font-size: .9em; font-weight: bold;">PERKIRAAN</td>
                <td style="padding:5px; text-align: center; font-size: .9em; font-weight: bold; width: 15%">DEBIT</td>
                <td style="padding:5px; text-align: center; font-size: .9em; font-weight: bold; width: 15%">KREDIT</td>
                <td style="padding:5px; text-align: center; font-size: .9em; font-weight: bold; width: 15%">SALDO</td>
            </tr>
            <?php
                
                foreach($data as $key => $value) {
                    $value['saldo'] = 0;
                    $value['debet'] = 0;
                    $value['kredit'] = 0;
            ?>
                <tr>
                    <td style="padding:5px; text-align: left; font-size: .9em;width: 7%; background-color: #F4F4F4;"><?php echo $value['CoaCode']; ?></td>
                    <td style="padding:5px; text-align: left; font-size: .9em; background-color: #F4F4F4;" colspan="4" ><?php echo $value['CoaTitle']; ?></td>
                </tr>
                <?php 
                    if(count($value['transactions']) > 0) {
                        
                        foreach($value['transactions'] as $akey => $trans) {
                            
                            $value['saldo']  += ($trans['debet'] - $trans['kredit']);
                            $value['debet']  += $trans['debet'];
                            $value['kredit'] += $trans['kredit'];
                            
                ?>
                            <tr>
                                <td style="padding:5px; text-align: center; font-size: .9em;width: 7%;"><?php echo $trans['JournalDate']; ?></td>
                                <td style="padding:5px; text-align: left; font-size: .9em;"><?php echo $trans['JournalMemo']; ?></td>
                                <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo number_format($trans['debet'],0); ?></td>
                                <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo number_format($trans['kredit'],0); ?></td>
                                <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo number_format($value['saldo']); ?></td>
                            </tr>
                <?php
                        }
                    }
                ?>
                <tr>
                    <td style="padding:5px; text-align: center; font-size: .9em;width: 7%;"></td>
                    <td style="padding:5px; text-align: left; font-size: .9em;">Total</td>
                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo number_format($value['debet'],0); ?></td>
                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo number_format($value['kredit'],0); ?></td>
                    <td style="padding:5px; text-align: right; font-size: .9em; width: 15%"><?php echo number_format($value['saldo'],0); ?></td>
                </tr>
            <?php

                }
            ?>
            
        </table>
    </div>
</center>
