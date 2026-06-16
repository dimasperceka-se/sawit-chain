<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Apr 09 2019
 *  File : sql_view_excel_xml.php
 *******************************************/
require_once APPPATH."/third_party/ExcelWriterXML/ExcelWriterXML.php";
$xml = new ExcelWriterXML($filename);
$xml->docTitle('Laporan');
$xml->docAuthor('Admin');
$xml->docCompany('PT Koltiva');

$formatTopTitle = $xml->addStyle('StyleHeader');
$formatTopTitle->fontColor('#416aa3');
$formatTopTitle->alignHorizontal('Left');
$formatTopTitle->fontSize(10);
$formatTopTitle->alignVertical('Center');

$formatHeader = $xml->addStyle('StyleHeader');
$formatHeader->fontBold();
$formatHeader->fontColor('#416aa3');
$formatHeader->alignHorizontal('Center');
$formatHeader->border('All','1','#416aa3','Continuous');
$formatHeader->alignWraptext();
$formatHeader->fontSize(8);
$formatHeader->alignVertical('Center');
$formatHeader->bgColor('#dfe8f6');

$formatRows = $xml->addStyle('StyleHeader');
$formatRows->alignHorizontal('Left');
$formatRows->fontColor('#416aa3');
$formatRows->fontSize(8);
$formatRows->border('All','1','#416aa3','Continuous');
$formatRows->alignWraptext();
$formatRows->alignVertical('Center');

$formatRowNumbers = $xml->addStyle('StyleHeader');
$formatRowNumbers->alignHorizontal('Right');
$formatRowNumbers->numberFormat('###,###');
$formatRowNumbers->border('All','1','#416aa3','Continuous');
$formatRowNumbers->fontColor('#416aa3');
$formatRowNumbers->fontSize(8);
$formatRowNumbers->alignVertical('Center');

$formatRowDate = $xml->addStyle('StyleHeader');
$formatRowDate->alignHorizontal('Left');
$formatRowDate->numberFormatDate();
$formatRowDate->border('All','1','#416aa3','Continuous');
$formatRowDate->fontColor('#416aa3');
$formatRowDate->fontSize(8);
$formatRowDate->alignVertical('Center');

$formatRowDateTime = $xml->addStyle('StyleHeader');
$formatRowDateTime->alignHorizontal('Left');
$formatRowDateTime->numberFormatDatetime();
$formatRowDateTime->border('All','1','#416aa3','Continuous');
$formatRowDateTime->fontColor('#416aa3');
$formatRowDateTime->fontSize(8);
$formatRowDateTime->alignVertical('Center');
/** =================================================================== */

$sheet1 = $xml->addSheet('Data');
$colnum=1;
$rownum = 1;

if(isset($dataList[0])){
    // ========================== HEADER COLUMN - BEGIN ========================//
    $colnum = 1;
    $sheet1->writeString($rownum, $colnum, "No.",$formatHeader); 
    $sheet1->columnWidth($colnum,30); 
    $colnum++;
    foreach($dataList[0] as $key => $val) {
        $sheet1->writeString($rownum, $colnum, $key,$formatHeader); 
        $sheet1->columnWidth($colnum,120); 
        $colnum++;
    }
    // ========================== HEADER COLUMN - END ========================//

    // ========================== CONTENT COLUMN - BEGIN ========================//
    $rownum++;
    $no=0;

    foreach($dataList as $key => $arrval) {   
        $colnum = 1;    
        $no++;
        $sheet1->writeNumber($rownum, $colnum, $no,$formatRowNumbers); $colnum++;
        
        foreach($arrval as $key2 => $arrval2) {
            
            switch($key2){
                case 'Nin':
                case 'Handphone':
                case 'Latitude':
                case 'Longitude':
                case 'FarmerID':
                case 'SurveyYear':
                    $sheet1->writeString($rownum, $colnum, $arrval2,$formatRows); $colnum++;
                break;
                default:
                    //Cek tipe data
                    if(CekValidDate($arrval2,'Y-m-d H:i:s') == true){
                        $arrval2 = $sheet1->convertMysqlDatetime($arrval2);
                        $sheet1->writeDateTime($rownum, $colnum, $arrval2,$formatRowDateTime); $colnum++;
                    } elseif (CekValidDate($arrval2,'Y-m-d') == true) {
                        $arrval2 = $sheet1->convertMysqlDate($arrval2);
                        $sheet1->writeDateTime($rownum, $colnum, $arrval2,$formatRowDate); $colnum++;
                    } else {
                        if(is_numeric($arrval2)){
                            $arrval2 = (float) $arrval2;
                            $sheet1->writeNumber($rownum, $colnum, $arrval2,$formatRowNumbers); $colnum++;
                        }else{
                            $sheet1->writeString($rownum, $colnum, $arrval2,$formatRows); $colnum++;
                        }
                    }
                break;
            }

        }
        $rownum++;
    }
    // ========================== CONTENT COLUMN - END ========================//
}

$xml->sendHeaders();
$xml->writeData();
?>