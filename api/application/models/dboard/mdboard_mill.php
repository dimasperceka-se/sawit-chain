<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mdboard_mill extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function truncateTable($namaTabel){
        $sql="DELETE FROM `$namaTabel`";
        $query = $this->db->query($sql);
    }

    public function getDisplayMill($mill,$millGroup,$starDate,$EndDate){
        $SQLFilter = '';
        $SQLFilterFFB = '';
        $SQLFilterDispatch = '';
        $SQLFilterPerYear = '';
        $SQLFilterGauge = '';
        $Param = array();
        $ParamFFB = array();
        $ParamDispatch = array();
        $ParamGauge = array();

        if ($starDate != '' && $EndDate != ''){
            // box & pie
            $SQLFilter .= " AND DATE(ktst.`DateTransaction`) BETWEEN ? AND ?";
            $Param[] = $starDate;
            $Param[] = $starDate;
            $Param[] = $EndDate;
            // ffb
            $SQLFilterFFB .= " AND DATE(ktst.`DateTransaction`) BETWEEN ? AND ?";
            $ParamFFB[] = $starDate;
            $ParamFFB[] = $EndDate;
            // box dispatch
            $SQLFilterDispatch .= " AND DATE(ktp.`ShippingDate`) BETWEEN ? AND ?";
            $ParamDispatch[] = $starDate;
            $ParamDispatch[] = $EndDate;
            //gauge
            $SQLFilterGauge .= " AND DATE(c.`ProcessingDate`) BETWEEN ? AND ?";
            $ParamGauge[] = $starDate;
            $ParamGauge[] = $starDate;
            $ParamGauge[] = $EndDate;
            // line
            $SQLFilterPerYear = " AND DATE(ktp.`ProcessingDate`) BETWEEN ? AND ?";
            $ParamPerYear[] = $starDate;
            $ParamPerYear[] = $starDate;
            $ParamPerYear[] = $EndDate;
        } else {
            // default tahun ini
            $ParamPerYear[] = date('Y-m-d');
        }
        
        if ($mill != ''){
            $SQLFilter .= " AND access_partner.`SupplychainID` = ?";
            $SQLFilterDispatch .= " AND access_partner.`SupplychainID` = ?";
            $SQLFilterPerYear .= " AND access_partner.`SupplychainID` = ?";
            $Param[] = $mill;
            $ParamFFB[] = $mill;
            $ParamDispatch[] = $mill;
            $ParamPerYear[] = $mill;
        }
        // hak akses data
        $Param[] = $_SESSION['PartnerID'];
        $ParamFFB[] = $_SESSION['PartnerID'];
        $ParamDispatch[] = $_SESSION['PartnerID'];
        $ParamPerYear[] = $_SESSION['PartnerID'];
        
        $SQL = "SELECT
                    ktsb.`SupplyDestOrgID`
                    , ktst.`SupplychainID`
                    , DATE(ktst.`DateTransaction`) AS `Date`
                    , (SUM(ktsb.`DestWeight`)/1000) AS NumberOfFFBInput
                    , COUNT(DISTINCT ktsb.SupplyBatchID) as NumberOfTransaction
                    , COUNT(DISTINCT CASE WHEN SupplyType = 'Farmer' OR SupplyType = 'NonFarmer' THEN ktst.`SupplyTransID` END) TraceableFarmer 
                    , SUM(IF(ktpp.`ProductID` = 1, ktpp.`ProductVolume` / 10000, 0)) + SUM(IF(ktpp.`ProductID` = 2, ktpp.`ProductVolume` / 10000, 0)) / (SUM(ktsb.`DestWeight`)/1000) AS ProccessingResult
                    , SUM(IF(ktpp.`ProductID` = 1, ktpp.`ProductVolume` / 10000, 0)) AS CPO
                    , SUM(IF(ktpp.`ProductID` = 2, ktpp.`ProductVolume` / 10000, 0)) AS KPO
                    , SUM(IF(ktpp.`ProductID` = 1 AND plot_farmer.`MemberID` IS NOT NULL, ktpp.`ProductVolume` / 1000, 0)) AS CPOTraceable
	                , SUM(IF(ktpp.`ProductID` = 1 AND plot_farmer.`MemberID` IS NULL, ktpp.`ProductVolume`  / 1000, 0)) AS CPONonTraceable
	                , SUM(IF(ktpp.`ProductID` = 2 AND plot_farmer.`MemberID` IS NOT NULL, ktpp.`ProductVolume`  / 1000, 0)) AS KPOTraceable
	                , SUM(IF(ktpp.`ProductID` = 2 AND plot_farmer.`MemberID` IS NULL, ktpp.`ProductVolume`  / 1000, 0)) AS KPONonTraceable
                    , SUM(IF(ktpp.`ProductID` = 1, ktpp.`ProductVolume` / 10000, 0)) + SUM(IF(ktpp.`ProductID` = 2, ktdd.`DespatchVolume` / 1000, 0)) / COUNT(DISTINCT ktpp.`ProcessingID`) AS AverageOfProduction
                FROM
                    ktv_tc_supplychain_batch ktsb
                    LEFT JOIN ktv_tc_supplychain_transaction ktst ON ktst.SupplyBatchID = ktsb.SupplyBatchID 
                    INNER JOIN view_tc_supplychain_org access_partner ON access_partner.`SupplychainID` = ktsb.`SupplyDestOrgID` AND access_partner.`ObjType` = 'mill'
                    LEFT JOIN ktv_members farmer ON farmer.`MemberID` = ktst.`SupplyID` AND ktst.`SupplyType` != 'batch'
                    LEFT JOIN ktv_survey_plot plot_farmer ON plot_farmer.`MemberID` = farmer.`MemberID`
                    LEFT JOIN ktv_survey_plot_sme plot_sme ON plot_sme.`MemberID` = farmer.`MemberID`
                    LEFT JOIN ktv_tc_processing_detail ktpd ON ktpd.`ObjID` = ktst.`SupplyTransID` AND ktpd.`ObjTypeID` = 1
                    LEFT JOIN ktc_tc_processing_product ktpp ON ktpp.`ProcessingID` = ktpd.`ProcessingID`
                    LEFT JOIN ktv_tc_despatch_detail ktdd ON ktdd.`ProcessingProductID` = ktpp.`ProcessingProductID`
                    LEFT JOIN (
                        SELECT 
                            ktpd.`ObjID`,
                            SUM(IF(ktpp.`ProductID` = 1, ktpp.`ProductVolume` / 1000, 0)), + SUM(IF(ktpp.`ProductID` = 2, ktdd.`DespatchVolume` / 100, 0)) / 1000 AS AverageOfProduction
                        FROM ktv_tc_processing ktp
                        LEFT JOIN ktv_tc_processing_detail ktpd ON ktpd.`ProcessingID` = ktp.`ProcessingID` AND ktpd.`ObjTypeID` = 1
                        LEFT JOIN ktc_tc_processing_product ktpp ON ktpp.`ProcessingID` = ktpd.`ProcessingID`
                        LEFT JOIN ktv_tc_despatch_detail ktdd ON ktdd.`ProcessingProductID` = ktpp.`ProcessingProductID`
                        WHERE 1=1
                            AND YEAR(ktp.`ProcessingDate`) = YEAR(?)
                        GROUP BY
                            ktpd.`ObjID`
                    ) sub_aop ON sub_aop.`ObjID` = ktst.`SupplyTransID`
                WHERE 1=1
                    $SQLFilter
                AND access_partner.`PartnerID` = ?
                ";
        $Query = $this->db->query($SQL, $Param);
        $data = $Query->row();
        
        $supplychain = $_SESSION['SupplychainID'];
        $supply = $supplychain!=""? "AND c.SupplychainID=$supplychain" : '';

        $sql = "SELECT
                    a.ProcessingID
                    , a.ProcessingProductID
                    , c.ProcessingNumber
                    , c.ProcessingDate
                    , FORMAT( a.RemainingVolume, 2 ) RemainingVolume
                    , FORMAT( a.ProductVolume, 2 ) ProductVolume
                    , tdd.DespatchVolume PickedVolume
                    , a.ProductID
                    , d.ProductName 
                    , ktcp.ProductPercentage
                FROM
                    ktc_tc_processing_product a
                    LEFT JOIN ktv_tc_processing c ON c.ProcessingID = a.ProcessingID
                    LEFT JOIN ref_tc_processing_product d ON d.ProductID = a.ProductID 
                    LEFT JOIN ktv_tc_despatch_detail  tdd ON tdd.ProcessingProductID = a.ProcessingProductID
                    LEFT JOIN ktv_tc_supplychain_product ktcp ON ktcp.SupplychainID = c.SupplychainID
                WHERE
                    a.StatusCode = 'active'
                    $supply
                AND
                    a.RemainingVolume > 0
                GROUP BY
                    a.ProcessingProductID, a.ProductID"; 
        $query = $this->db->query($sql,$ParamGauge); 
       
        $CPO = 0;
        $PKO = 0;

        if($query->num_rows()>0){
            foreach($query->result() as $row){
                if($row->ProductID == 1){
                    $CPO += $row->RemainingVolume;
                }
                if($row->ProductID == 2){
                    $PKO += $row->RemainingVolume;
                }
            }
        }

        $dataRemaining = $Query->row();
         
        $SQLFFB = "SELECT
                        (SUM(ktsb.`DestWeight`)/1000) AS NumberOfFFBInput
                    FROM
                        #transaction mill
                        ktv_tc_supplychain_batch ktsb
                        JOIN ktv_tc_supplychain_transaction ktst ON ktst.SupplyBatchID = ktsb.SupplyBatchID 
                        #access
                        JOIN view_tc_supplychain_org access_partner ON access_partner.`SupplychainID` = ktsb.`SupplyDestOrgID` AND access_partner.`ObjType` = 'mill'
                    WHERE 1=1
                        $SQLFilterFFB
                        AND access_partner.`PartnerID` = ?
                    ";
        $Query = $this->db->query($SQLFFB, $ParamFFB);
        $dataFFB = $Query->row();

        $SQLDispatch = "SELECT
                            ktp.`SupplychainID`
                            , ktp.`ShippingDate`
                            , COUNT(ktp.`DespatchID`) AS NumberOfDespatch
                        FROM ktv_tc_despatch ktp
                        #access
                        INNER JOIN view_tc_supplychain_org access_partner ON access_partner.`SupplychainID` = ktp.`SupplychainID` AND access_partner.`ObjType` = 'mill'
                        WHERE 1=1
                            $SQLFilterDispatch
                        AND access_partner.`PartnerID` = ?";
        $QueryDispatch = $this->db->query($SQLDispatch, $ParamDispatch);
        $dataDispatch = $QueryDispatch->row();

        $SQLPerYear = "SELECT 
                            ktp.`ProcessingDate`,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 1, ktpp.`ProductVolume` / 1000, 0)) AS CPOJanuary,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 2, ktpp.`ProductVolume` / 1000, 0)) AS CPOFebruary,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 3, ktpp.`ProductVolume` / 1000, 0)) AS CPOMarch,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 4, ktpp.`ProductVolume` / 1000, 0)) AS CPOApril,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 5, ktpp.`ProductVolume` / 1000, 0)) AS CPOMay,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 6, ktpp.`ProductVolume` / 1000, 0)) AS CPOJune,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 7, ktpp.`ProductVolume` / 1000, 0)) AS CPOJuly,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 8, ktpp.`ProductVolume` / 1000, 0)) AS CPOAugust,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 9, ktpp.`ProductVolume` / 1000, 0)) AS CPOSeptember,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 10, ktpp.`ProductVolume` / 1000, 0)) AS CPOOctober,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 11, ktpp.`ProductVolume` / 1000, 0)) AS CPONovember,
                            SUM(IF(ktpp.`ProductID` = 1 AND MONTH(ktp.`ProcessingDate`) = 12, ktpp.`ProductVolume` / 1000, 0)) AS CPODecember,
                            
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 1, ktpp.`ProductVolume` / 1000, 0)) AS KPOJanuary,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 2, ktpp.`ProductVolume` / 1000, 0)) AS KPOFebruary,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 3, ktpp.`ProductVolume` / 1000, 0)) AS KPOMarch,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 4, ktpp.`ProductVolume` / 1000, 0)) AS KPOApril,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 5, ktpp.`ProductVolume` / 1000, 0)) AS KPOMay,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 6, ktpp.`ProductVolume` / 1000, 0)) AS KPOJune,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 7, ktpp.`ProductVolume` / 1000, 0)) AS KPOJuly,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 8, ktpp.`ProductVolume` / 1000, 0)) AS KPOAugust,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 9, ktpp.`ProductVolume` / 1000, 0)) AS KPOSeptember,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 10, ktpp.`ProductVolume` / 1000, 0)) AS KPOOctober,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 11, ktpp.`ProductVolume` / 1000, 0)) AS KPONovember,
                            SUM(IF(ktpp.`ProductID` = 2 AND MONTH(ktp.`ProcessingDate`) = 12, ktpp.`ProductVolume` / 1000, 0)) AS KPODecember
                        FROM
                        ktv_tc_supplychain_batch ktsb
                            INNER JOIN ktv_tc_supplychain_transaction ktst ON ktst.SupplyBatchID = ktsb.SupplyBatchID
                            INNER JOIN view_tc_supplychain_org access_partner ON access_partner.`SupplychainID` = ktsb.`SupplyDestOrgID` AND access_partner.`ObjType` = 'mill'
                            LEFT JOIN ktv_tc_processing_detail ktpd ON ktpd.`ObjID` = ktst.`SupplyTransID` AND ktpd.`ObjTypeID` = 1
                            LEFT JOIN ktv_tc_processing ktp ON ktp.`ProcessingID` = ktpd.`ProcessingID`
                            LEFT JOIN ktc_tc_processing_product ktpp ON ktpp.`ProcessingID` = ktpd.`ProcessingID`
                            LEFT JOIN ktv_tc_despatch_detail ktdd ON ktdd.`ProcessingProductID` = ktpp.`ProcessingProductID`
                            #filter mill
                            #LEFT JOIN view_tc_supplychain_org filter_mill ON filter_mill.`SupplychainID` = ktsb.`SupplyDestOrgID` AND filter_mill.`ObjType` = 'mill'
                        WHERE 1=1
                            AND YEAR(ktp.`ProcessingDate`) = YEAR(?)
                            $SQLFilterPerYear
                            AND access_partner.`PartnerID` = ?
                        ";
        $QueryPerYear = $this->db->query($SQLPerYear, $ParamPerYear);
        $dataPerYear = $QueryPerYear->row();
        
        //validation product cpo
        $this->db->select("a.ProductID, a.StatusCode", false);
        $this->db->from('ktv_tc_supplychain_product a');
        $this->db->where('a.ProductID  =', '1');
        $this->db->where('a.StatusCode !=', 'nullified');
        $s = $this->db->where('a.SupplychainID',  $supplychain )->get();
        $dataProductCPO = $s->row();
        
        if($dataProductCPO->ProductID != ''){
            $valuecpo = 'notempty';
        } else {
            $valuecpo = 'empty';
        }

        //validation product pko
        $this->db->select("a.ProductID, a.StatusCode", false);
        $this->db->from('ktv_tc_supplychain_product a');
        $this->db->where('a.ProductID  =', '2');
        $this->db->where('a.StatusCode !=', 'nullified');
        $s = $this->db->where('a.SupplychainID',  $supplychain )->get();
        $dataProductPKO = $s->row();

        if($dataProductPKO->ProductID != ''){
            $valuepko = 'notempty';
        } else {
            $valuepko = 'empty';
        }
        
        $result['validation']['cpo'] = $valuecpo;
        $result['validation']['pko'] = $valuepko;
       
        //box
        $result['box']['number_of_ffb_input'] = $dataFFB->NumberOfFFBInput;
        $result['box']['number_traceable_farmer'] = $data->TraceableFarmer;
        $result['box']['number_of_transaction'] = $data->NumberOfTransaction;
        $result['box']['number_processing_result'] = $data->ProccessingResult;
        $result['box']['number_total_cpo'] = $data->CPO;
        $result['box']['number_total_pko'] = $data->KPO;
        $result['box']['number_of_dispatch'] = $dataDispatch->NumberOfDespatch;
        $result['box']['number_average_of_production'] = $data->AverageOfProduction;
        //end box

        //new gauge chart
        $result['dataDisplay']['number_gauge_total_cpo'] = (float) $CPO;
       
        $result['dataDisplay']['number_gauge_total_pko'] = (float) $PKO;

        //pie chart
        $result['pie']['number_traceability_cpo'] = (float) $data->CPOTraceable;

        $result['pie']['number_nontraceability_cpo'] = (float) $data->CPONonTraceable;

        $result['pie']['number_traceability_pko'] = (float) $data->KPOTraceable;

        $result['pie']['number_nontraceability_pko'] = (float) $data->KPONonTraceable;
        //end pie chart

        //line chart 
        $result['line']['number_pko_january'] = (float) $dataPerYear->KPOJanuary;
        $result['line']['number_pko_february'] = (float) $dataPerYear->KPOFebruary;
        $result['line']['number_pko_march'] = (float) $dataPerYear->KPOMarch;
        $result['line']['number_pko_april'] = (float) $dataPerYear->KPOApril;
        $result['line']['number_pko_may'] = (float) $dataPerYear->KPOMay;
        $result['line']['number_pko_june'] = (float) $dataPerYear->KPOJune;
        $result['line']['number_pko_july'] = (float) $dataPerYear->KPOJuly;
        $result['line']['number_pko_august'] = (float) $dataPerYear->KPOAugust;
        $result['line']['number_pko_september'] = (float) $dataPerYear->KPOSeptember;
        $result['line']['number_pko_october'] = (float) $dataPerYear->KPOOctober;
        $result['line']['number_pko_november'] = (float) $dataPerYear->KPONovember;
        $result['line']['number_pko_december'] = (float) $dataPerYear->KPODecember;

        $result['line']['number_cpo_january'] = (float) $dataPerYear->CPOJanuary;
        $result['line']['number_cpo_february'] = (float) $dataPerYear->CPOFebruary;
        $result['line']['number_cpo_march'] = (float) $dataPerYear->CPOMarch;
        $result['line']['number_cpo_april'] = (float) $dataPerYear->CPOApril;
        $result['line']['number_cpo_may'] = (float) $dataPerYear->CPOMay;
        $result['line']['number_cpo_june'] = (float) $dataPerYear->CPOJune;
        $result['line']['number_cpo_july'] = (float) $dataPerYear->CPOJuly;
        $result['line']['number_cpo_august'] = (float) $dataPerYear->CPOAugust;
        $result['line']['number_cpo_september'] = (float) $dataPerYear->CPOSeptember;
        $result['line']['number_cpo_october'] = (float) $dataPerYear->CPOOctober;
        $result['line']['number_cpo_november'] = (float) $dataPerYear->CPONovember;
        $result['line']['number_cpo_december'] = (float) $dataPerYear->CPODecember;
        //end line chart

        return $result;
    }

}
?>