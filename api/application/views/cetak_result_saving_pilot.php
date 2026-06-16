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
				padding-top: 1cm;
				padding-bottom: 0.5cm;
				padding-right: 0.5cm;
				padding-left: 0.5cm;
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
            input {border: 1px solid;background-color: #FFF}
            input:disabled {color: #000000;}
            .line{border: 1px solid black;}
            td{vertical-align:top}
            @media all {
                .header{  font-size: 10pt;font-family: verdana;padding: 0px;margin: 0px;border: 1px solid;font-weight: bold;}
                .body{font-size: 9pt;font-family: verdana;font-weight: normal;padding: 0px;margin: 0px;border: 1px solid; border-top: none;}
                .header_div{width: 100%; height: 20px; margin-bottom: -28px; border-top: 28px solid #cccccc;}
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
			.page-break {
				page-break-before: always;
			}
        </style>
    </head>
    <body>
         <?php
//         print_r($dataform[0]);
            foreach ($dataform as $k => $v) {
//                print_r($v);
                $v = $v[0];
//                $SurveyNr = 
//                echo $v['SurveyNr']."<".$this->uri->segment(6).'<br>';
                if($v['SurveyNr']<$this->uri->segment(6) || $this->uri->segment(2)=='cetak_aff')
                {
                    //tidak mengetahui datanya
                    if($v['AccountNumber']==null && $v['AccountBankBranch']==null && $v['AccountBankName']==null)
                    {
                        $checked = 'checked';
                    } else {
                        $checked = null;
                    }
                ?>
                            <div class="page">
								<table width="100%" border="0" cellpadding="2">
									<tr>								
										<td width="20%" align="left" style="vertical-align:middle;">
											<img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
										</td>
										<?php 								
											for($i=0;$i<count($logos);$i++){
												if($logos[$i]['Photo']!=''){
										?>
											<td height="60px" width="20%" align="left" style="vertical-align:middle;">
												<img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
											</td>
										<?php
												}
											}
										?>
										<td width="20%" align="right" style="vertical-align:middle;">
											<img src="<?= base_url() ?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
										</td>
										<td width="20%" align="right" style="vertical-align:middle;">
											<img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
										</td>
									</tr>
								</table>
                                <table width="100%" cellspacing="0">
                                    <tr>
										<td height="60px" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;"><?php echo lang('SAVING PILOT') ?></td>                                        
                                    </tr>
								</table>

								<div class="header_div"></div>
								<table width="100%" cellspacing="4" class="header">
									<tr><td><?php echo lang('Data Umum') ?></td>
										<td><?php echo lang('PK') ?> - <?= $data['FarmerID'] ?></td>
										<td><?php echo lang('CPG') ?> - <?= $data['FarmerGroupID'] ?></td>
										<td><?php echo lang('Tanggal') ?> : <?= $v['InterviewDate'] ?></td></tr>
								</table>

								<table width="100%" cellspacing="2" class="body">
									<tr><td><?php echo lang('Nama') ?></td><td>
											<input disabled style = "border: 1px solid;" type="text" value="<?= $data['PersonNm'] ?>" class="input" disabled size="32"></td>
										<td><?php echo lang('Kecamatan') ?></td><td>
											<input disabled style = "border: 1px solid;" type="text" value="<?= $data['Kecamatan'] ?>" class="input" disabled size="32"></td></tr>
									<tr><td><?php echo lang('Provinsi') ?></td><td>
											<input disabled style = "border: 1px solid;" type="text" value="<?= $data['Provinsi'] ?>" class="input" disabled size="32"></td>
										<td>Desa</td><td>
											<input disabled style = "border: 1px solid;" type="text" value="<?= $data['Desa'] ?>" class="input" disabled size="32"></td></tr>
									<tr><td><?php echo lang('Kabupaten') ?></td><td>
											<input disabled style = "border: 1px solid;" type="text" value="<?= $data['Kabupaten'] ?>" class="input" disabled size="32"></td>
										<td><?php echo lang('Alamat') ?></td><td>
											<input disabled style = "border: 1px solid;" type="text" value="<?= $data['alamat'] ?>" class="input" disabled size="32"></td></tr>
								</table>

								<div class="header_div"></div>
								<table width="100%" cellspacing="4" class="header" style="border-top:none">
									<tr><td><?php echo lang('Survey') ?></td><td align="right"><?=$v['surveya']?></td></tr>
								</table>

								<table width="100%" cellspacing="10" class="body">
									<tr>
										<td colspan="3" width="75%">Apakah Bapak/Ibu menikah?</td>
										<td class="line" width="25%">
											<input disabled type="radio" <?= $v['MarriedYesNo'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
											<input disabled type="radio" <?= $v['MarriedYesNo'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Berapakah usia Bapak/Ibu? (Tahun)') ?></td>
										<td class="line" width="25%">
											<?= $v['Age'] ?>
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Ada berapa orang yang menjadi tanggungan Bapak/Ibu?') ?></td>
										<td class="line" width="25%">
											<?= $v['FamilyMembers'] ?>
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Berapa Luas Lahan yang ditanami kakao? (Ha)') ?></td>
										<td class="line" width="25%">
											<?= $v['LandSizeHa'] ?>
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Kira-kira berapa penghasilan tahunan Bapak/Ibu dari penjualan biji kakao? (IDR)') ?></td>
										<td class="line" width="25%">
											<?= number_format( $v['AmountCocoaIncome'] , 0 , '.' , ',' ) ?>
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Kira-kira berapa penghasilan Bapak/Ibu per tahun selain dari penjualan biji kakao? (IDR)') ?></td>
										<td class="line" width="25%">
											<?= number_format( $v['AmountOtherIncome'] , 0 , '.' , ',' ) ?>
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Apakah Bapak/Ibu memiliki simpanan di bank/koperasi/bawah bantal/lainnya? (KECUALI arisan)') ?></td>
										<td class="line" width="25%">
											<input disabled type="radio" <?= $v['SavingYesNo'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
											<input disabled type="radio" <?= $v['SavingYesNo'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Jika Ya, berapa besar simpanan Bapak/Ibu?') ?></td>
										<td class="line" width="25%">
											<?php
												if($v['AmountSaving']=='1'){
													echo "IDR 0 - 99,999";
												}else if($v['AmountSaving']=='2'){
													echo "IDR 100,000 - 499,999";
												}else if($v['AmountSaving']=='3'){
													echo "IDR 500,000 - 999,999";
												}else if($v['AmountSaving']=='4'){
													echo "IDR 1,000,000 - 1,999,999";
												}else if($v['AmountSaving']=='5'){
													echo "IDR 2,000,000 - 2,999,999";
												}else if($v['AmountSaving']=='6'){
													echo "IDR 3,000,000 - 3,999,999";
												}else if($v['AmountSaving']=='7'){
													echo "IDR 4,000,000 - 4,999,999";
												}else if($v['AmountSaving']=='8'){
													echo "IDR 5,000,000 - 5,999,999";
												}else if($v['AmountSaving']=='9'){
													echo "IDR 6,000,000 - 6,999,999";
												}else if($v['AmountSaving']=='10'){
													echo "IDR 7,000,000 - 7,999,999";
												}else if($v['AmountSaving']=='11'){
													echo "IDR 8,000,000 - 8,999,999";
												}else if($v['AmountSaving']=='12'){
													echo "IDR 9,000,000 - 9,999,999";
												}else if($v['AmountSaving']=='13'){
													echo "> IDR 10,000,000";
												}
											?>
											
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Apakah Bapak/Ibu pernah menerima pinjaman dari bank?') ?></td>
										<td class="line" width="25%">
											<input disabled type="radio" <?= $v['LoanYesNo'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
											<input disabled type="radio" <?= $v['LoanYesNo'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
										<td>	
									</tr>
									<tr>
										<td colspan="3" width="75%"><?php echo lang('Nomor rekening BRI Bapak/Ibu') ?></td>
										<td class="line" width="25%">
											<?= $v['AccountNumber'] ?>
										<td>	
									</tr>	
								</table>
                            </div>
                            <div class="page-break"></div>
            <?php
                } else {
                    //cetka postline ksoong
                    ?>
                        <div class="page">

							<table width="100%" border="0" cellpadding="2">
									<tr>								
										<td width="20%" align="left" style="vertical-align:middle;">
											<img src="<?= base_url() ?>images/Photo/03. logo swiss-01-resized.png" style="max-width:90%; max-height:90%; max-width:120px; max-height: 55px;">
										</td>
										<?php 								
											for($i=0;$i<count($logos);$i++){
												if($logos[$i]['Photo']!=''){
										?>
											<td height="60px" width="20%" align="left" style="vertical-align:middle;">
												<img src="<?= base_url() ?>images/Photo/<?= $logos[$i]['Photo'] ?>" style="max-width:90%; max-height:90%; max-width:120px;">
											</td>
										<?php
												}
											}
										?>
										<td width="20%" align="right" style="vertical-align:middle;">
											<img src="<?= base_url() ?>images/Photo/20160315105236_SCPP 2015.jpg" style="max-width:90%; max-height:90%; max-width:120px;">
										</td>
										<td width="20%" align="right" style="vertical-align:middle;">
											<img src="<?= base_url() ?>images/swisscontact.png" style="max-width:90%; max-height:90%; max-width:120px;">
										</td>
									</tr>
								</table>
                                <table width="100%" cellspacing="0">
                                    <tr>
										<td height="60px" align="center" style="font-size: 11pt; text-align: center; font-weight: bold;vertical-align:middle;"><?php echo lang('SAVING PILOT') ?></td>                                        
                                    </tr>
								</table>

							<div class="header_div"></div>
							<table width="100%" cellspacing="4" class="header">
								<tr><td><?php echo lang('Data Umum') ?></td>
									<td><?php echo lang('PK') ?> - <?= $data['FarmerID'] ?></td>
									<td><?php echo lang('CPG') ?> - <?= $data['FarmerGroupID'] ?></td>
									<td><?php echo lang('Tanggal') ?> : <?= $v['InterviewDate'] ?></td></tr>
							</table>

							<table width="100%" cellspacing="2" class="body">
								<tr>
									<td><?php echo lang('Nama') ?></td>
									<td><input disabled style = "border: 1px solid;" type="text" value="<?= $data['PersonNm'] ?>" class="input" disabled size="32"></td>
									<td><?php echo lang('Kecamatan') ?></td>
									<td><input disabled style = "border: 1px solid;" type="text" value="<?= $data['Kecamatan'] ?>" class="input" disabled size="32"></td>
								</tr>
								<tr>
									<td><?php echo lang('Provinsi') ?></td>
									<td><input disabled style = "border: 1px solid;" type="text" value="<?= $data['Provinsi'] ?>" class="input" disabled size="32"></td>
									<td>Desa</td>
									<td><input disabled style = "border: 1px solid;" type="text" value="<?= $data['Desa'] ?>" class="input" disabled size="32"></td>
								</tr>
								<tr>
									<td><?php echo lang('Kabupaten') ?></td>
									<td><input disabled style = "border: 1px solid;" type="text" value="<?= $data['Kabupaten'] ?>" class="input" disabled size="32"></td>
									<td><?php echo lang('Tanggal Lahir') ?></td>
									<td><input disabled style = "border: 1px solid;" type="text" value="<?= $data['Birthdate'] ?>" class="input" disabled size="32"></td>
								</tr>
							</table>

							<div class="header_div"></div>
							<table width="100%" cellspacing="4" class="header" style="border-top:none">
								<tr><td>Survey</td><td align="right"><?=$v['surveya']?></td></tr>
							</table>
                                    
                            <table width="100%" cellspacing="10" class="body">
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Apakah Bapak/Ibu menikah?') ?></td>
									<td class="line" width="25%">
										<input disabled type="radio" <?= $detail['MarriedYesNo'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
										<input disabled type="radio" <?= $detail['MarriedYesNo'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Berapakah usia Bapak/Ibu? (Tahun)') ?></td>
									<td class="line" width="25%">
										<?= $detail['Age'] ?>
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Ada berapa orang yang menjadi tanggungan Bapak/Ibu?') ?></td>
									<td class="line" width="25%">
										<?= $detail['FamilyMembers'] ?>
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Berapa Luas Lahan yang ditanami kakao? (Ha)') ?></td>
									<td class="line" width="25%">
										<?= $detail['LandSizeHa'] ?>
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Kira-kira berapa penghasilan tahunan Bapak/Ibu dari penjualan biji kakao? (IDR)') ?></td>
									<td class="line" width="25%">
										<?= number_format( $detail['AmountCocoaIncome'] , 0 , '.' , ',' ) ?>
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Kira-kira berapa penghasilan Bapak/Ibu per tahun selain dari penjualan biji kakao? (IDR)') ?></td>
									<td class="line" width="25%">
										<?= number_format( $detail['AmountOtherIncome'] , 0 , '.' , ',' ) ?>
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Apakah Bapak/Ibu memiliki simpanan di bank/koperasi/bawah bantal/lainnya? (KECUALI arisan)') ?></td>
									<td class="line" width="25%">
										<input disabled type="radio" <?= $detail['SavingYesNo'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
										<input disabled type="radio" <?= $detail['SavingYesNo'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Jika Ya, berapa besar simpanan Bapak/Ibu?') ?></td>
									<td class="line" width="25%">
										<?php
											if($detail['AmountSaving']=='1'){
												echo "IDR 0 - 99,999";
											}else if($detail['AmountSaving']=='2'){
												echo "IDR 100,000 - 499,999";
											}else if($detail['AmountSaving']=='3'){
												echo "IDR 500,000 - 999,999";
											}else if($detail['AmountSaving']=='4'){
												echo "IDR 1,000,000 - 1,999,999";
											}else if($detail['AmountSaving']=='5'){
												echo "IDR 2,000,000 - 2,999,999";
											}else if($detail['AmountSaving']=='6'){
												echo "IDR 3,000,000 - 3,999,999";
											}else if($detail['AmountSaving']=='7'){
												echo "IDR 4,000,000 - 4,999,999";
											}else if($detail['AmountSaving']=='8'){
												echo "IDR 5,000,000 - 5,999,999";
											}else if($detail['AmountSaving']=='9'){
												echo "IDR 6,000,000 - 6,999,999";
											}else if($detail['AmountSaving']=='10'){
												echo "IDR 7,000,000 - 7,999,999";
											}else if($detail['AmountSaving']=='11'){
												echo "IDR 8,000,000 - 8,999,999";
											}else if($detail['AmountSaving']=='12'){
												echo "IDR 9,000,000 - 9,999,999";
											}else if($detail['AmountSaving']=='13'){
												echo "> IDR 10,000,000";
											}
										?>
										
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Apakah Bapak/Ibu pernah menerima pinjaman dari bank?') ?></td>
									<td class="line" width="25%">
										<input disabled type="radio" <?= $detail['LoanYesNo'] == '1' ? 'checked' : '' ?>><?php echo lang('Ya') ?>
										<input disabled type="radio" <?= $detail['LoanYesNo'] == '2' ? 'checked' : '' ?>><?php echo lang('Tidak') ?>
									<td>	
								</tr>
								<tr>
									<td colspan="3" width="75%"><?php echo lang('Nomor rekening BRI Bapak/Ibu') ?></td>
									<td class="line" width="25%">
										<?= $detail['AccountNumber'] ?>
									<td>	
								</tr>	
                            </table>
                        </div>
        <?php
                }
            }
        ?>
    </body>
</html>
