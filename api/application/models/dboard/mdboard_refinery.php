<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdboard_refinery extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function getDisplayRefinery($mill,$idMill,$starDate,$EndDate){
        
        $SQLFilter = '';
        $Param = array();
        $Params = array();
        if ($starDate != '' && $EndDate != ''){
            $SQLFilter .= " AND DATE(td.`ShippingDate`) BETWEEN ? AND ?";
            $Param[] = $starDate;
            $Param[] = $starDate;
            $Param[] = $EndDate;
        } else {
            $Param[] = date('Y-m-d');
        }
        if ($mill != ''){
            $SQLFilter .= " AND filter_mill.`ObjID` = ?";
            $Param[] = $mill;
        }
        if ($idMill != '') {
            $SQLFilter .= " AND km.`MillID` = ?";
            $Param[] = $idMill;
        }
        $Param[] = $_SESSION['PartnerID'];

        $Params[] = $_SESSION['SupplychainID'];

        $SQL = "SELECT
                SUM(tdd.`DespatchVolume` / 1000) AS TotalInputOil,
                SUM(IF(tdd.`ProductID` = 1, tdd.`DespatchVolume` /1000, 0)) AS TotalCPO,
                SUM(IF(tdd.`ProductID` = 2, tdd.`DespatchVolume` /1000, 0)) AS TotalPKO,
                COUNT(DISTINCT td.`SupplychainID`) AS NumberOfMill,
                COUNT(DISTINCT tdd.`DespatchDetailID`) AS NumberOfTransaction,
                SUM(tdd.`DespatchVolume` / 1000) / COUNT(DISTINCT tdd.`DespatchDetailID`) AS AverageOfProduction,
                SUM(tdd.`DespatchVolume` / 1000) + SUM(td.`DestpatchNetto` / 1000) AS ProccessingResult,
                vso.Name AS MillName
            FROM
                ktv_tc_despatch td
            LEFT JOIN
                view_tc_supplychain_org vso on vso.SupplychainID = td.SupplychainID
            LEFT JOIN
                ktv_tc_reception tr on tr.DespatchID = td.DespatchID
            LEFT JOIN
                ktv_tc_despatch_detail tdd on tdd.DespatchID = td.DespatchID
            INNER JOIN
                view_tc_supplychain_org vso2 on vso2.SupplychainID = td.DestinationID AND vso2.ObjType = 'refinery'
            #filter mill
            LEFT JOIN view_tc_supplychain_org filter_mill ON filter_mill.`SupplychainID` = td.`SupplychainID` AND filter_mill.`ObjType`= 'mill'
            LEFT JOIN ktv_mill km ON km.`MillID` = filter_mill.`ObjID`
            LEFT JOIN (
                        SELECT 
                            ktr.`SupplychainID` ,
                            SUM(tdd.`DespatchVolume`) AS AverageOfProduction
                        FROM ktv_tc_reception ktr
                        LEFT JOIN ktv_tc_reception_detail ktrd ON ktrd.`ReceptionID` = ktr.`ReceptionID`
                        LEFT JOIN ktv_tc_despatch_detail tdd on tdd.DespatchID = ktr.DespatchID
                        WHERE 
                            YEAR(ktr.`ReceptionDate`) = YEAR(?) # dari filter user
                        GROUP BY
                            ktr.`SupplychainID`
                    ) sub_aop ON sub_aop.`SupplychainID` = td.`DestinationID`
            WHERE 1 = 1
                $SQLFilter
                AND td.StatusCode = 'active'
                AND td.DestpatchStatusID = 5
                AND vso2.PartnerID = ?";

        $Query = $this->db->query($SQL, $Param);

        $data = $Query->row();

        $SQLInputAndTrans = "SELECT 
                            IF(MONTH(td.`ShippingDate`) = 1, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilJanuary, 
                            IF(MONTH(td.`ShippingDate`) = 2, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilFebruary,
                            IF(MONTH(td.`ShippingDate`) = 3, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilMarch,
                            IF(MONTH(td.`ShippingDate`) = 4, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilApril,
                            IF(MONTH(td.`ShippingDate`) = 5, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilMay,
                            IF(MONTH(td.`ShippingDate`) = 6, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilJune,
                            IF(MONTH(td.`ShippingDate`) = 7, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilJuly,
                            IF(MONTH(td.`ShippingDate`) = 8, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilAugust,
                            IF(MONTH(td.`ShippingDate`) = 9, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilSeptember,
                            IF(MONTH(td.`ShippingDate`) = 10, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilOctober,
                            IF(MONTH(td.`ShippingDate`) = 11, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilNovember,
                            IF(MONTH(td.`ShippingDate`) = 12, COUNT(DISTINCT ktdd.`DespatchID`), 0) AS NumberOfOilDecember,
                            
                            IF(MONTH(td.`ShippingDate`) = 1, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransJanuary,
                            IF(MONTH(td.`ShippingDate`) = 2, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransFebruary,
                            IF(MONTH(td.`ShippingDate`) = 3, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransMarch,
                            IF(MONTH(td.`ShippingDate`) = 4, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransApril,
                            IF(MONTH(td.`ShippingDate`) = 5, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransMay,
                            IF(MONTH(td.`ShippingDate`) = 6, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransJune,
                            IF(MONTH(td.`ShippingDate`) = 7, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransJuly,
                            IF(MONTH(td.`ShippingDate`) = 8, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransAugust,
                            IF(MONTH(td.`ShippingDate`) = 9, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransSeptember,
                            IF(MONTH(td.`ShippingDate`) = 10, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransOctober,
                            IF(MONTH(td.`ShippingDate`) = 11, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransNovember,
                            IF(MONTH(td.`ShippingDate`) = 12, COUNT(DISTINCT ktdd.`DespatchDetailID`), 0) AS NumberOfTransDecember
                        FROM
                            ktv_tc_despatch td
                            INNER JOIN view_tc_supplychain_org access_partner ON access_partner.`SupplychainID` = td.`DestinationID` AND access_partner.`ObjType` = 'refinery'
                            LEFT JOIN ktv_tc_despatch_detail ktdd ON ktdd.`DespatchID` = td.`DespatchID`
                            LEFT JOIN view_tc_supplychain_org filter_mill ON filter_mill.`SupplychainID` = td.`SupplychainID` AND filter_mill.`ObjType` = 'mill'
                            LEFT JOIN ktv_mill km ON km.`MillID` = filter_mill.`ObjID`
                        WHERE 1=1
                            AND YEAR(td.`ShippingDate`) = YEAR(?)
                            $SQLFilter
                            AND access_partner.`PartnerID` = ?
                        GROUP BY
                            MONTH(td.`ShippingDate`)";
        $QueryInputAndTrans = $this->db->query($SQLInputAndTrans, $Param);
        $dataInputAndTrans = $QueryInputAndTrans->row();

        $SQLOilProduction = "SELECT 
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 1, ktdd.`DespatchVolume` / 1000, 0)) AS CPOJanuary,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 2, ktdd.`DespatchVolume` / 1000, 0)) AS CPOFebruary,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 3, ktdd.`DespatchVolume` / 1000, 0)) AS CPOMarch,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 4, ktdd.`DespatchVolume` / 1000, 0)) AS CPOApril,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 5, ktdd.`DespatchVolume` / 1000, 0)) AS CPOMay,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 6, ktdd.`DespatchVolume` / 1000, 0)) AS CPOJune,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 7, ktdd.`DespatchVolume` / 1000, 0)) AS CPOJuly,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 8, ktdd.`DespatchVolume` / 1000, 0)) AS CPOAugust,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 9, ktdd.`DespatchVolume` / 1000, 0)) AS CPOSeptember,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 10, ktdd.`DespatchVolume` / 1000, 0)) AS CPOOctober,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 11, ktdd.`DespatchVolume` / 1000, 0)) AS CPONovember,
                            SUM(IF(ktdd.`ProductID` = 1 AND MONTH(td.`ShippingDate`) = 12, ktdd.`DespatchVolume` / 1000, 0)) AS CPODecember,
                            
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 1, ktdd.`DespatchVolume` / 1000, 0)) AS KPOJanuary,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 2, ktdd.`DespatchVolume` / 1000, 0)) AS KPOFebruary,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 3, ktdd.`DespatchVolume` / 1000, 0)) AS KPOMarch,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 4, ktdd.`DespatchVolume` / 1000, 0)) AS KPOApril,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 5, ktdd.`DespatchVolume` / 1000, 0)) AS KPOMay,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 6, ktdd.`DespatchVolume` / 1000, 0)) AS KPOJune,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 7, ktdd.`DespatchVolume` / 1000, 0)) AS KPOJuly,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 8, ktdd.`DespatchVolume` / 1000, 0)) AS KPOAugust,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 9, ktdd.`DespatchVolume` / 1000, 0)) AS KPOSeptember,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 10, ktdd.`DespatchVolume` / 1000, 0)) AS KPOOctober,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 11, ktdd.`DespatchVolume` / 1000, 0)) AS KPONovember,
                            SUM(IF(ktdd.`ProductID` = 2 AND MONTH(td.`ShippingDate`) = 12, ktdd.`DespatchVolume` / 1000, 0)) AS KPODecember
                        FROM
                            ktv_tc_despatch td
                            INNER JOIN view_tc_supplychain_org access_partner ON access_partner.`SupplychainID` = td.`DestinationID` AND access_partner.`ObjType` = 'refinery'
                            LEFT JOIN ktv_tc_despatch_detail ktdd ON ktdd.`DespatchID` = td.`DespatchID`
                            LEFT JOIN view_tc_supplychain_org filter_mill ON filter_mill.`SupplychainID` = td.`SupplychainID` AND filter_mill.`ObjType` = 'mill'
                            LEFT JOIN ktv_mill km ON km.`MillID` = filter_mill.`ObjID`
                        WHERE 1=1
                            AND YEAR(td.`ShippingDate`) = YEAR(?)
                            $SQLFilter
                            AND td.StatusCode = 'active'
                            AND td.DestpatchStatusID = 5
                            AND access_partner.`PartnerID` = ?
                        GROUP BY
                            MONTH(td.`ShippingDate`)";
        $QueryOilProduction = $this->db->query($SQLOilProduction, $Param);
        $dataOilProduction = $QueryOilProduction->row();

        $sqlMill = "SELECT
                    m.MillID id
                    , m.MillName MillName
                FROM
                    ktv_tc_supplychain_org_rel orel
                LEFT JOIN
                    view_tc_supplychain_org vso on vso.SupplychainID = orel.ChildID
                LEFT JOIN
                    ktv_mill m on m.MillID = vso.ObjID
                WHERE
                    m.StatusCode = 'active'
                    AND 
                    orel.ParentID = ?
                ";
        $QueryMillTransaction = $this->db->query($sqlMill, $Params);
        
        $millName     = array();
        $transaction  = array();
        if($QueryMillTransaction->num_rows()>0){
            foreach($QueryMillTransaction->result() as $key => $row){
                $millName = $row->MillName;
            }
        }

        $result['dataDisplay']['dataMillName']  = $row->MillName;
        
        $SQLTransaction = "SELECT
                                COUNT(DISTINCT dp.`DespatchNumber`) AS TransactionMillRefinery
                            FROM
                                `ktv_tc_despatch_detail` a
                                LEFT JOIN ref_tc_processing_product b ON b.ProductID = a.ProductID
                                LEFT JOIN ktc_tc_processing_product tsp ON tsp.ProcessingProductID = a.ProcessingProductID
                                LEFT JOIN ktv_tc_processing tp ON tp.ProcessingID = tsp.ProcessingID
                                LEFT JOIN ktv_tc_despatch dp ON dp.DespatchID = a.DespatchID
                                LEFT JOIN view_tc_supplychain_org vso ON vso.SupplychainID = dp.SupplychainID 
                                INNER JOIN ktv_tc_reception tr on tr.DespatchID = a.DespatchID
                            WHERE
                                dp.ShippingDate = ?
                            AND 
                                dp.DestinationID = '". $_SESSION['SupplychainID'] ."'
                            ";
        
        $queryTransaction = $this->db->query($SQLTransaction, $Param);
        
        $dataTranscation = $queryTransaction->row();
        //box
        $result['box']['number_off_total_input_oil'] = $data->TotalInputOil;
        $result['box']['number_of_total_cpo'] = $data->TotalCPO;
        $result['box']['number_of_total_pko'] = $data->TotalPKO;
        $result['box']['number_of_refinery'] = $data->NumberOfMill;
        $result['box']['number_off_avarange_production'] = $data->AverageOfProduction;
        $result['box']['number_of_transaction'] = $data->NumberOfTransaction;
        $result['box']['number_of_processing_result'] = $data->ProccessingResult;
        $result['box']['number_of_oil_result'] = $data->OilResult;
        $result['box']['name'] = $data->MillName;


        $result['dataDisplay']['TransactionMillRefinery']  = $dataTranscation->TransactionMillRefinery;
        //end box

        //gauge chart
        $result['dataTarget']['number_gauge_total_cpo'] = (float) $data->CPORecived + $data->CPORemaining;
        $result['dataDisplay']['number_gauge_total_cpo'] = (float) $data->CPORemaining;

        $result['dataTarget']['number_gauge_total_pko'] = (float) $data->PKORecived + $data->PKORemaining;
        $result['dataDisplay']['number_gauge_total_pko'] = (float) $data->PKORemaining;
        //end gauge chart

        //line chart 
        $result['line']['number_oil_january'] = (float) $dataInputAndTrans->NumberOfOilJanuary;
        $result['line']['number_oil_february'] = (float) $dataInputAndTrans->NumberOfOilFebruary;
        $result['line']['number_oil_march'] = (float) $dataInputAndTrans->NumberOfOilMarch;
        $result['line']['number_oil_april'] = (float) $dataInputAndTrans->NumberOfOilApril;
        $result['line']['number_oil_may'] = (float) $dataInputAndTrans->NumberOfOilMay;
        $result['line']['number_oil_june'] = (float) $dataInputAndTrans->NumberOfOilJune;
        $result['line']['number_oil_july'] = (float) $dataInputAndTrans->NumberOfOilJuly;
        $result['line']['number_oil_august'] = (float) $dataInputAndTrans->NumberOfOilAugust;
        $result['line']['number_oil_september'] = (float) $dataInputAndTrans->NumberOfOilSeptember;
        $result['line']['number_oil_october'] = (float) $dataInputAndTrans->NumberOfOilOctober;
        $result['line']['number_oil_november'] = (float) $dataInputAndTrans->NumberOfOilNovember;
        $result['line']['number_oil_december'] = (float) $dataInputAndTrans->NumberOfOilDecember;

        $result['line']['number_refinery_january'] = (int) $dataInputAndTrans->NumberOfTransJanuary;
        $result['line']['number_refinery_february'] = (int) $dataInputAndTrans->NumberOfTransFebruary;
        $result['line']['number_refinery_march'] = (int) $dataInputAndTrans->NumberOfTransMarch;
        $result['line']['number_refinery_april'] = (int) $dataInputAndTrans->NumberOfTransApril;
        $result['line']['number_refinery_may'] = (int) $dataInputAndTrans->NumberOfTransMay;
        $result['line']['number_refinery_june'] = (int) $dataInputAndTrans->NumberOfTransJune;
        $result['line']['number_refinery_july'] = (int) $dataInputAndTrans->NumberOfTransJuly;
        $result['line']['number_refinery_august'] = (int) $dataInputAndTrans->NumberOfTransAugust;
        $result['line']['number_refinery_september'] = (int) $dataInputAndTrans->NumberOfTransSeptember;
        $result['line']['number_refinery_october'] = (int) $dataInputAndTrans->NumberOfTransOctober;
        $result['line']['number_refinery_november'] = (int) $dataInputAndTrans->NumberOfTransNovember;
        $result['line']['number_refinery_december'] = (int) $dataInputAndTrans->NumberOfTransDecember;
        //end line chart

        //line chart 
        $result['line']['number_pko_january'] = (float) $dataOilProduction->KPOJanuary;
        $result['line']['number_pko_february'] = (float) $dataOilProduction->KPOFebruary;
        $result['line']['number_pko_march'] = (float) $dataOilProduction->KPOMarch;
        $result['line']['number_pko_april'] = (float) $dataOilProduction->KPOApril;
        $result['line']['number_pko_may'] = (float) $dataOilProduction->KPOMay;
        $result['line']['number_pko_june'] = (float) $dataOilProduction->KPOJune;
        $result['line']['number_pko_july'] = (float) $dataOilProduction->KPOJuly;
        $result['line']['number_pko_august'] = (float) $dataOilProduction->KPOAugust;
        $result['line']['number_pko_september'] = (float) $dataOilProduction->KPOSeptember;
        $result['line']['number_pko_october'] = (float) $dataOilProduction->KPOOctober;
        $result['line']['number_pko_november'] = (float) $dataOilProduction->KPONovember;
        $result['line']['number_pko_december'] = (float) $dataOilProduction->KPODecember;

        $result['line']['number_cpo_january'] = (float) $dataOilProduction->CPOJanuary;
        $result['line']['number_cpo_february'] = (float) $dataOilProduction->CPOFebruary;
        $result['line']['number_cpo_march'] = (float) $dataOilProduction->CPOMarch;
        $result['line']['number_cpo_april'] = (float) $dataOilProduction->CPOApril;
        $result['line']['number_cpo_may'] = (float) $dataOilProduction->CPOMay;
        $result['line']['number_cpo_june'] = (float) $dataOilProduction->CPOJune;
        $result['line']['number_cpo_july'] = (float) $dataOilProduction->CPOJuly;
        $result['line']['number_cpo_august'] = (float) $dataOilProduction->CPOAugust;
        $result['line']['number_cpo_september'] = (float) $dataOilProduction->CPOSeptember;
        $result['line']['number_cpo_october'] = (float) $dataOilProduction->CPOOctober;
        $result['line']['number_cpo_november'] = (float) $dataOilProduction->CPONovember;
        $result['line']['number_cpo_december'] = (float) $dataOilProduction->CPODecember;
        //end line chart

        return $result;
    }

}
?>