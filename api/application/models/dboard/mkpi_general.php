<?php
class Mkpi_general extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetComboFilterYearKpiGeneral() {
        $return = array();

        $ArrYear = explode(',',GetSettingValue('kpi_target_filter_year'));
        rsort($ArrYear);

        for ($i=0; $i < count($ArrYear); $i++) {
            $return[$i]['id'] = $ArrYear[$i];
            $return[$i]['label'] = $ArrYear[$i];
        }

        return $return;
    }

    public function GetKpiTargetGeneralMainGrid($FilterYear,$FilterCountry,$FilterProvince,$FilterPartnerID, $FilterDistrictID) {
        $return = array();

        //Hak Akses
        //modified 25-2-2021 restrict district dilepas untuk admin

        if ($_SESSION['is_admin'] == "1"){
            $SqlHakAkses = "";
        } else {
            if ($_SESSION['daerah_access'] != "") {
                $SqlHakAkses = "AND c.`DistrictID` IN ({$_SESSION['daerah_access']})";
            } else {
                $SqlHakAkses = "AND c.`DistrictID` IN ('')";
            }
        }

        if (!empty($FilterCountry)) {
            $FilterCountry = "AND twil.`ISO2` = '$FilterCountry'";
        }

        
        // if($_SESSION['daerah_access'] != "") $SqlHakAkses = "AND c.DistrictID IN ({$_SESSION['daerah_access']})"; else $SqlHakAkses = "AND c.DistrictID IN ('')";

        $sql = "SELECT
                    twil.CountryID
                    , twil.ProvinceID
                    , twil.ProvinceLabel
                    , twil.PartnerID
                    , twil.Year
                    , twil.DistrictID
                    , twil.District
                    , twil.Province
                    , twil.CountryName
                    , kpi.farmer_registered AS PalmOilFarmersRegistered
                    , kpi.plantation_mapped AS PalmOilPlantationsMapped
                    , kpi.consent_signed    AS ConsentLettersSigned
                    , kpi.plant_ha_mapped   AS PalmOilPlantationsArea
                    , kpi.mills_mapped      AS PalmOilMillsMapped
                    , kpi.plantation_polygon_mapped AS PalmOilPlantationsMappedWithPolygon
                    , kpi.agents_mapped AS PalmOilSMEMapped
                    , kpi.plant_polygon_ha_mapped AS PalmOilPlantationsHectareMappedWithPolygon
                    , kpi.farmers_trained AS TrainOrCoachFarmers
                    , kpi.farmers_plantation_registered AS RegisteredPlantation
                    , kpi.farmers_plantation AS RegisteredPlantationHectares
                    , kpi.farmers_active_responsible AS ResponSourcingFarmers
                    , kpi.traceability_transactions AS TraceTransaction
                    , kpi.platform_users AS PlatformUsers
                    , kpi.small_medium_enterprise AS RegisteredSME
                    , kpi.farm_extensions_user AS FarmXUsers
                    , kpi.farm_gate_users AS FarmGateUsers
                    , kpi.farm_retail_users AS FarmRetailUsers
                    , kpi.farm_cloud_users AS FarmCloudUsers
                FROM
                    (
                        SELECT
                            a.`CountryID`
                            , a.`CountryName`
                            , b.`ProvinceID`
                            , b.`Province`
                            , '{$FilterPartnerID}' AS PartnerID
                            , CONCAT(a.`CountryName`,' - ',b.`Province`,' - ',c.`District`) AS ProvinceLabel
                            , '{$FilterYear}' AS `Year`
                            , c.`DistrictID`
                            , c.`District`
                            , a.`ISO2`
                        FROM
                            ktv_country a
                            INNER JOIN ktv_province b ON a.`ISO2` = b.`CountryCode`
                            INNER JOIN ktv_district c ON b.`ProvinceID` = c.`ProvinceID`
                        WHERE 1=1
                            $SqlHakAkses
                        ORDER BY ProvinceLabel
                    ) AS twil
                    LEFT JOIN dash_pro_kpi_target kpi ON 1=1
                        /* AND twil.CountryID = kpi.`CountryID` */
                        /* AND twil.ProvinceID = kpi.`ProvinceID` */
                        AND twil.DistrictID = kpi.`DistrictID`
                        AND twil.PartnerID = kpi.`PartnerID`
                        AND twil.Year = kpi.`Year`
                WHERE 1=1
                    /* AND ( (twil.FilterCountry = {$FilterCountry}) OR (0={$FilterCountry}) ) */
                    $FilterCountry
                    AND ( (twil.ProvinceID = {$FilterProvince}) OR (0={$FilterProvince}) )
                    AND ( (twil.DistrictID = {$FilterDistrictID}) OR (0={$FilterDistrictID}) )
                /*GROUP BY twil.ProvinceID*/";
        $return = $this->db->query($sql)->result_array();
        
        return $return;
    }

    public function GetKpiTargetSawitTerampilMainGrid($FilterYear) {
        $return = array();

        $sql = "SELECT
            a.TargetID
            , cl.ClusterName
            , fb.ProgramName
            , a.Province
            , a.`Year`
            , a.KsMill
            , a.StMill
            , a.FarmerReg
            , a.FarmReg
            , a.Ha
            , a.SocSel
            , a.FarmerSurveyBP
            , a.FarmSurvey
            , a.Polygon
            , a.FarmerCoach
            , a.CoachingSess
            , a.Sms
            , a.IdCard
            , a.FarmX
            , a.FarmG
            , a.FarmR
            , a.FarmC
        FROM
            `ktv_kpi_st_certification_target_ims_district` a
        INNER JOIN
            ktv_first_buyer_program_cluster cl on cl.ClusterID = a.ClusterID
        INNER JOIN
            ktv_first_buyer_program fb on fb.ProgID = a.ProgID
        WHERE
            a.`Year` = ?";
        $return = $this->db->query($sql, array($FilterYear))->result_array();
        
        return $return;
    }

    public function InputKpiTarget($paramPost) {
        $results = array();
        $this->db->trans_begin();

        $where = [
            'DistrictID' => $paramPost['DistrictID'],
            'PartnerID'  => $paramPost['PartnerID'],
            'Year'       => $paramPost['Year']
        ];

        $checkDashProKpiTarget = $this->db->where($where)
                                          ->get('dash_pro_kpi_target');
        $params = [
            'farmer_registered'               => $paramPost['PalmOilFarmersRegistered'],
            'plantation_mapped'               => $paramPost['PalmOilPlantationsMapped'],
            'consent_signed'                  => $paramPost['ConsentLettersSigned'],
            'plant_ha_mapped'                 => $paramPost['PalmOilPlantationsArea'],
            'mills_mapped'                    => $paramPost['PalmOilMillsMapped'],
            'plantation_polygon_mapped'       => $paramPost['PalmOilPlantationsMappedWithPolygon'],
            'agents_mapped'                   => $paramPost['PalmOilSMEMapped'],
            'plant_polygon_ha_mapped'         => $paramPost['PalmOilPlantationsHectareMappedWithPolygon'],
            'farmers_trained'                 => $paramPost['TrainOrCoachFarmers'],
            'farmers_plantation_registered'   => $paramPost['RegisteredPlantation'],
            'farmers_plantation'              => $paramPost['RegisteredPlantationHectares'],
            'farmers_active_responsible'      => $paramPost['ResponSourcingFarmers'],
            'traceability_transactions'       => $paramPost['TraceTransaction'],
            'platform_users'                  => $paramPost['PlatformUsers'],
            'small_medium_enterprise'         => $paramPost['RegisteredSME'],
            'farm_extensions_user'            => $paramPost['FarmXUsers'],
            'farm_gate_users'                 => $paramPost['FarmGateUsers'],
            'farm_retail_users'               => $paramPost['FarmRetailUsers'],
            'farm_cloud_users'                => $paramPost['FarmCloudUsers']
        ];

        if ($checkDashProKpiTarget->num_rows() > 0) {
            $update = $this->db->where($where)
                               ->update('dash_pro_kpi_target', $params);
        } else {
            $params += $where;
            $insert = $this->db->insert('dash_pro_kpi_target', $params);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        }

        return $results;
    }

    public function InputKpiTargetSawitTerampil($paramPost) {

        if($paramPost["TargetID"] == ""){
            $paramPost["DateUpdated"]       = date("Y-m-d H:i:s");
            $paramPost["LastModifiedBy"]    = $_SESSION['userid'];

            $dataExist = $this->CekExistTargetSawitTerampil($paramPost["ClusterID"], $paramPost["ProgID"],$paramPost["Year"]);
            if($dataExist == "exist"){
                $results['success'] = false;
                $results['message'] = lang("Data Already Exist");

                return $results;
            }

            $ClusterData = $this->getClusterData($paramPost["ClusterID"]);
            $paramPost["ProvinceID"]    = $ClusterData["ProvinceID"];
            $paramPost["FirstBuyerID"]  = $ClusterData["FirstBuyerID"];
            $paramPost["Province"]      = $ClusterData["Province"];

            $query = $this->db->insert("ktv_kpi_st_certification_target_ims_district",$paramPost);
        }else{
            $TargetID = $paramPost["TargetID"];
            $paramPost["DateUpdated"]       = date("Y-m-d H:i:s");
            $paramPost["LastModifiedBy"]    = $_SESSION['userid'];

            unset($paramPost["TargetID"]);
            
            $this->db->where("TargetID",$TargetID);
            $query = $this->db->update("ktv_kpi_st_certification_target_ims_district",$paramPost);
        }
        
        if($query){
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        }else{
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        }

        return $results;
    }

    public function getClusterData($ClusterID){
        $sql    = "SELECT
                a.FirstBuyerID
                , a.ProvinceID
                , p.Province
            FROM
                ktv_first_buyer_program_cluster a
            LEFT JOIN
                ktv_province p on p.ProvinceID = a.ProvinceID
            WHERE 
                a.ClusterID = ?";
        $return  = $this->db->query($sql,array($ClusterID))->row_array();

        return $return;
    }

    public function CekExistTargetSawitTerampil($ClusterID, $ProgID, $Year){
        $sql    = "SELECT a.TargetID FROM ktv_kpi_st_certification_target_ims_district a WHERE a.ClusterID = ? AND a.ProgID = ? AND a.Year = ?";
        $query  = $this->db->query($sql,array($ClusterID,$ProgID,$Year));
        if($query->num_rows()>0){
            return "exist";
        }else{
            return "no_exist";
        }
    }

    public function getTargetSawitTerampilForm($TargetID){
        $sql = "SELECT
            a.TargetID
            , a.ClusterID
            , a.ProgID
            , a.Province
            , a.`Year`
            , a.KsMill
            , a.StMill
            , a.FarmerReg
            , a.FarmReg
            , a.Ha
            , a.SocSel
            , a.FarmerSurveyBP
            , a.FarmSurvey
            , a.Polygon
            , a.FarmerCoach
            , a.CoachingSess
            , a.Sms
            , a.IdCard
            , a.FarmX
            , a.FarmG
            , a.FarmR
            , a.FarmC
        FROM
            `ktv_kpi_st_certification_target_ims_district` a
        WHERE
            a.`TargetID` = ?";
        $data = $this->db->query($sql, array($TargetID))->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-".$key;
            $dataRow[$keyNew] = $value;
        }
        
        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

}