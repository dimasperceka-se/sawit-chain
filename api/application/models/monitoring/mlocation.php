<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mlocation extends CI_Model {

    public function __construct() {
        parent::__construct();
    }



    private function _getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $unit){

        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344).' km';
        } else if ($unit == "N") {
            return ($miles * 0.8684).' nm';
        } else {
            return $miles.' mi';
        }
    }

    public function getFarmerByUser($start,$user, $district = '') {

      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }

      //get farmer
      $sql = "SELECT SQL_CALC_FOUND_ROWS kcf.FarmerID , kcf.FarmerName ,(YEAR(NOW()) - YEAR(Birthdate)) Age , IF(kcf.Gender = '1' , 'Male' , 'Female') Gender , kcf.HandPhone , (CASE WHEN kcf.Education=1 THEN 'No Schooling' WHEN kcf.Education=2 THEN 'Primary School incomplete' WHEN kcf.Education=3 THEN 'Primary School Completed, Did not Continue' WHEN kcf.Education=4 THEN 'Junior Secondary School completed' WHEN kcf.Education=5 THEN 'Senior/Vocational School completed' WHEN kcf.Education=6 THEN 'Tertiary degree completed' ELSE '' END) Education, kcf.CPGid , kc.GroupName , kv.Village , Subdistrict , kd.District , kp.Province , kcfg.GardenNr GardenNumber , kcfg.GardenHaUnCertified LandSize , ( kcfg.PohonTBM + kcfg.PohonTM + kcfg.PohonRehab) CacaoTrees , kcfg.TahunTanamanCocoa , ( ( kcfg.PanenTrekMonths * kcfg.PanenTrekPanenMonth * kcfg.PanenTrekKg ) +( kcfg.PanenBiasaMonths * kcfg.PanenBiasaPanenMonth * kcfg.PanenBiasaKg ) +( kcfg.PanenRayaMonths * kcfg.PanenRayaPanenMonth * kcfg.PanenRayaKg ) ) Production , ROUND( ( ( kcfg.PanenTrekMonths * kcfg.PanenTrekPanenMonth * kcfg.PanenTrekKg ) +( kcfg.PanenBiasaMonths * kcfg.PanenBiasaPanenMonth * kcfg.PanenBiasaKg ) +( kcfg.PanenRayaMonths * kcfg.PanenRayaPanenMonth * kcfg.PanenRayaKg ) ) / kcfg.GardenHaUnCertified ,2) Productivity , IF( kcfg.PakaiKompos = '1' , 'Yes' , 'No' ) CompostFertilizer , IF( kcfg.TidakMemakaiKimia = '1' , 'Yes' , 'No' ) NonOrganicFertilizer , IF(kcfg.Herbisida = '1' , 'Yes' , 'No') HerbicidePesticide , IF(kcfg.Herbisida = '1' , 'Yes' , 'No') HerbicidePesticide , IF(kcfg.Herbisida = '1' , 'Yes' , 'No') HerbicidePesticide , kcfg.Latitude, kcfg.Longitude FROM ktv_farmer kcf LEFT JOIN ktv_cpg kc ON kc.CPGid = kcf.CPGid LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = kv.SubDistrictID LEFT JOIN ktv_district kd ON kd.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID LEFT JOIN ktv_farmer_garden kcfg ON kcfg.FarmerID = kcf.FarmerID INNER JOIN( SELECT FarmerID , GardenNr GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr ) LatestSurvey ON LatestSurvey.FarmerID = kcfg.FarmerID AND LatestSurvey.GardenNr = kcfg.GardenNr AND LatestSurvey.SurveyNr = kcfg.SurveyNr INNER JOIN ktv_access_staff kas ON kas.DistrictID = kd.DistrictID INNER JOIN sys_user su ON su.UserId = kas.UserId WHERE su.UserName = '".$user."' AND kcf.StatusCode='active' ".$district." LIMIT ".$start.", 500";
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getCompostByUser($start,$user, $district = '') {

      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }
      //get compost
      $sql = "SELECT SQL_CALC_FOUND_ROWS CompostID, IF(ObjType = 'cpg',cpg.GroupName,IF(ObjType = 'farmer',farmer.FarmerName,IF(ObjType = 'koperasi',coop.CoopName,IF(ObjType = 'warehouse',warehouse.WarehouseName,IF(ObjType = 'trader',trader.TraderName,''))))) AS CompostName, ObjType AS CompostType, Established AS DateEstablished, IF(MesinChooper = 1,'Yes','No') AS ChopperMachine, IF(RumahKompos = 1, 'Yes','No') AS CompostHouse FROM ktv_compost LEFT JOIN ktv_cpg cpg ON cpg.CPGid = ktv_compost.ObjID AND ktv_compost.ObjType = 'cpg' LEFT JOIN ktv_cooperatives coop ON coop.CoopID = ktv_compost.ObjID AND ktv_compost.ObjType = 'koperasi' LEFT JOIN ktv_farmer farmer ON farmer.FarmerID = ktv_compost.ObjID AND ktv_compost.ObjType = 'farmer' LEFT JOIN ktv_warehouse warehouse ON warehouse.WarehouseID = ktv_compost.ObjID AND ktv_compost.ObjType = 'warehouse' LEFT JOIN ktv_traders trader ON trader.TraderID = ktv_compost.ObjID AND ktv_compost.ObjType = 'trader' LEFT JOIN ktv_village village ON village.VillageID = cpg.VillageID AND village.VillageID = farmer.VillageID AND village.VillageID = coop.VillageID AND village.VillageID = trader.VillageID AND village.VillageID = warehouse.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID INNER JOIN sys_user su ON su.UserId = akses.UserId WHERE su.UserName = '".$user."' ".$district." LIMIT ".$start.",500";
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getNurseryByUser($start,$user, $district = '') {

      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }
      //get farmer
      $sql = "SELECT SQL_CALC_FOUND_ROWS NurseryID, IF(ObjType = 'cpg',cpg.GroupName,IF(ObjType = 'farmer',farmer.FarmerName,IF(ObjType = 'koperasi',coop.CoopName,IF(ObjType = 'warehouse',warehouse.WarehouseName,IF(ObjType = 'trader',trader.TraderName,''))))) AS NurseryName, ObjType AS NurseryType, Established AS DateEstablished, Kapasitas as Capacity, Panjang as Length, Lebar as Width, 'Area', 'Certification',(SELECT COUNT(NurseryTransactionID) FROM ktv_nursery_transaction WHERE NurseryID = ktv_nursery.NurseryID) as NumberOfSales FROM ktv_nursery LEFT JOIN ktv_cpg cpg ON cpg.CPGid = ktv_nursery.ObjID AND ktv_nursery.ObjType = 'cpg' LEFT JOIN ktv_cooperatives coop ON coop.CoopID = ktv_nursery.ObjID AND ktv_nursery.ObjType = 'koperasi' LEFT JOIN ktv_farmer farmer ON farmer.FarmerID = ktv_nursery.ObjID AND ktv_nursery.ObjType = 'farmer' LEFT JOIN ktv_warehouse warehouse ON warehouse.WarehouseID = ktv_nursery.ObjID AND ktv_nursery.ObjType = 'warehouse' LEFT JOIN ktv_traders trader ON trader.TraderID = ktv_nursery.ObjID AND ktv_nursery.ObjType = 'trader' LEFT JOIN ktv_village village ON village.VillageID = cpg.VillageID AND village.VillageID = farmer.VillageID AND village.VillageID = coop.VillageID AND village.VillageID = trader.VillageID AND village.VillageID = warehouse.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID INNER JOIN sys_user su ON su.UserId = akses.UserId WHERE su.UserName = '".$user."' ".$district." LIMIT ".$start.",500";
      //$results = $this->db->query($sql);var_dump($this->db->_error_message());die;
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getDemoplotByUser($start,$user, $district = '') {

      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }
      //get demoplot
      $sql = "SELECT SQL_CALC_FOUND_ROWS kcf.FarmerID AS FarmerID, kcf.FarmerName AS FarmerName, kcfg.GardenNr AS GardenNr, kcf.VillageID, kv.Village AS VillageName, Subdistrict AS SubDistrictName, kd.District AS DistrictName, kp.Province AS ProvinceName, kcfg.Latitude, kcfg.Longitude, 'Status' FROM ktv_farmer kcf INNER JOIN ktv_cpg_batch_trainings kcbt ON kcbt.DemoplotOwnerID = kcf.FarmerID AND kcbt.CPGtrainingsID=1 LEFT JOIN ktv_cpg kc ON kc.CPGid = kcf.CPGid LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = kv.SubDistrictID LEFT JOIN ktv_district kd ON kd.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID LEFT JOIN ktv_farmer_garden kcfg ON kcfg.FarmerID = kcf.FarmerID INNER JOIN( SELECT FarmerID , GardenNr GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) LatestSurvey ON LatestSurvey.FarmerID = kcfg.FarmerID AND LatestSurvey.GardenNr = kcfg.GardenNr AND LatestSurvey.SurveyNr = kcfg.SurveyNr INNER JOIN ktv_access_staff kas ON kas.DistrictID = kd.DistrictID INNER JOIN sys_user su ON su.UserId = kas.UserId WHERE su.UserName = '".$user."' AND kcf.StatusCode='active' ".$district." LIMIT ".$start.",500 ";
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getCooperativesByUser($start,$user, $district = '') {

      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }

      //get farmer
      $sql = "SELECT SQL_CALC_FOUND_ROWS CoopID,CoopName,Status AS LegalStatus,TahunTerbentuk As DateEstablished,ktv_cooperatives.VillageID,village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName,Phone,Email,(SELECT COUNT(DISTINCT MemberID) FROM coop_member WHERE TypeID IN(SELECT TypeID FROM coop_member_type WHERE CoopID = ktv_cooperatives.CoopID)) AS MemberNumbers FROM ktv_cooperatives LEFT JOIN ktv_village village ON village.VillageID = ktv_cooperatives.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID INNER JOIN sys_user su ON su.UserId = akses.UserId WHERE su.UserName = '".$user."' ".$district." LIMIT ".$start.",500";
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getTraderByUser($start,$user, $district = '') {

      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }
      //get farmer
      $sql = "SELECT SQL_CALC_FOUND_ROWS a.TraderID,a.TraderName,Sex as Gender,Handphone as Phone,Company as CompanyName,CompanyYear as YearEstablished,a.VillageID, a.Longitude, a.Latitude, village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName FROM ktv_traders a LEFT JOIN ktv_village village ON village.VillageID = a.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID INNER JOIN sys_user su ON su.UserId = akses.UserId WHERE su.UserName = '".$user."' ".$district." LIMIT ".$start.",500";
      //var_dump($sql);die;
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getWarehouseByUser($start,$user, $district = '') {

      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }
      //get farmer
      $sql = "SELECT SQL_CALC_FOUND_ROWS WarehouseID,WarehouseName,'LegalStatus',Year AS DateEstablished,a.PartnerID,(SELECT PartnerName FROM ktv_program_partner WHERE PartnerID = a.PartnerID) AS PartnerName,a.VillageID,village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName FROM ktv_warehouse a LEFT JOIN ktv_village village ON village.VillageID = a.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID INNER JOIN sys_user su ON su.UserId = akses.UserId WHERE su.UserName = '".$user."' ".$district." LIMIT ".$start.",500 ";
      //var_dump($sql);die;
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getSceByUser($start,$user, $district = '') {

      //get sce farmer
      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }
      $sql = "SELECT SQL_CALC_FOUND_ROWS a.FarmerID,a.FarmerName,GroupName,village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName FROM sce_farmer b LEFT JOIN ktv_farmer a ON a.FarmerID = b.FarmerID LEFT JOIN ktv_cpg cpg ON cpg.CPGid = a.CPGid LEFT JOIN ktv_village village ON village.VillageID = a.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID INNER JOIN sys_user su ON su.UserId = akses.UserId WHERE su.UserName = '".$user."' '.$district.' LIMIT ".$start.",500";
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getCpgByUser($start,$user, $district = '') {
      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }
      //get cpg
      //$sql = "SELECT SQL_CALC_FOUND_ROWS kcf.FarmerID , kcf.FarmerName ,(YEAR(NOW()) - YEAR(Birthdate)) Age , IF(kcf.Gender = '1' , 'Male' , 'Female') Gender , kcf.HandPhone , (CASE WHEN kcf.Education=1 THEN 'No Schooling' WHEN kcf.Education=2 THEN 'Primary School incomplete' WHEN kcf.Education=3 THEN 'Primary School Completed, Did not Continue' WHEN kcf.Education=4 THEN 'Junior Secondary School completed' WHEN kcf.Education=5 THEN 'Senior/Vocational School completed' WHEN kcf.Education=6 THEN 'Tertiary degree completed' ELSE '' END) Education, kcf.CPGid , kc.GroupName , kv.Village , ks.SubDistrict , kd.District , kp.Province , kcfg.GardenNr GardenNumber , kcfg.GardenHaUnCertified LandSize , ( kcfg.PohonTBM + kcfg.PohonTM + kcfg.PohonRehab) CacaoTrees , kcfg.TahunTanamanCocoa , ( ( kcfg.PanenTrekMonths * kcfg.PanenTrekPanenMonth * kcfg.PanenTrekKg ) +( kcfg.PanenBiasaMonths * kcfg.PanenBiasaPanenMonth * kcfg.PanenBiasaKg ) +( kcfg.PanenRayaMonths * kcfg.PanenRayaPanenMonth * kcfg.PanenRayaKg ) ) Production , ROUND( ( ( kcfg.PanenTrekMonths * kcfg.PanenTrekPanenMonth * kcfg.PanenTrekKg ) +( kcfg.PanenBiasaMonths * kcfg.PanenBiasaPanenMonth * kcfg.PanenBiasaKg ) +( kcfg.PanenRayaMonths * kcfg.PanenRayaPanenMonth * kcfg.PanenRayaKg ) ) / kcfg.GardenHaUnCertified ,2) Productivity , IF( kcfg.PakaiKompos = '1' , 'Yes' , 'No' ) CompostFertilizer , IF( kcfg.TidakMemakaiKimia = '1' , 'Yes' , 'No' ) NonOrganicFertilizer , IF(kcfg.Herbisida = '1' , 'Yes' , 'No') HerbicidePesticide , IF(kcfg.Herbisida = '1' , 'Yes' , 'No') HerbicidePesticide , IF(kcfg.Herbisida = '1' , 'Yes' , 'No') HerbicidePesticide , kcfg.Latitude, kcfg.Longitude FROM ktv_farmer kcf INNER JOIN ktv_cpg_batch_trainings kcbt ON kcbt.DemoplotOwnerID = kcf.FarmerID AND kcbt.CPGtrainingsID=1 LEFT JOIN ktv_cpg kc ON kc.CPGid = kcf.CPGid LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID = kv.SubDistrictID LEFT JOIN ktv_district kd ON kd.DistrictID = ks.DistrictID LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID LEFT JOIN ktv_farmer_garden kcfg ON kcfg.FarmerID = kcf.FarmerID INNER JOIN( SELECT FarmerID , GardenNr GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr ) LatestSurvey ON LatestSurvey.FarmerID = kcfg.FarmerID AND LatestSurvey.GardenNr = kcfg.GardenNr AND LatestSurvey.SurveyNr = kcfg.SurveyNr INNER JOIN ktv_access_staff kas ON kas.DistrictID = kd.DistrictID INNER JOIN sys_user su ON su.UserId = kas.UserId WHERE su.UserName = '".$user."' AND kcf.StatusCode='active'";
      $sql = "SELECT SQL_CALC_FOUND_ROWS CPGid, GroupName, TahunTerbentuk AS DateEstablished, a.VillageID, kv.Village AS VillageName, Subdistrict AS SubDistrictName, kd.District AS DistrictName, kp.Province AS ProvinceName,( SELECT COUNT(FarmerID) FROM ktv_farmer WHERE CPGid = a.CPGid AND StatusCode = 'active') AS MemberNumbers FROM ktv_cpg a LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = kv.SubDistrictID LEFT JOIN ktv_district kd ON kd.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID INNER JOIN ktv_access_staff kas ON kas.DistrictID = kd.DistrictID INNER JOIN sys_user su ON su.UserId = kas.UserId WHERE su.UserName = '".$user."' ".$district." LIMIT ".$start.",500";
      $results = $this->db->query($sql)->result_array();
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }

    public function getLocationByUser($start,$user, $district = '') {

      if(strlen($district)) {
        $district = 'AND subdistrict.DistrictID = "'.$district.'"';
      }

      //get all data
      $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM( ( SELECT kcf.FarmerID AS OBJECT_ID, kcf.FarmerName AS OBJECT_NAME, "Farmer" AS OBJECT_TYPE, kcfg.GardenNr, kcf.Address, kcf.VillageID, kv.Village, SubDistrict SubDistrictName, kd.District DistrictName, kp.Province ProvinceName, kcfg.Latitude, kcfg.Longitude, kcf.StatusCode AS STATUS FROM ktv_farmer kcf LEFT JOIN ktv_cpg kc ON kc.CPGid = kcf.CPGid LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = kv.SubDistrictID LEFT JOIN ktv_district kd ON kd.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID LEFT JOIN ktv_farmer_garden kcfg ON kcfg.FarmerID = kcf.FarmerID INNER JOIN ( SELECT FarmerID, GardenNr GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_farmer_garden GROUP BY FarmerID, GardenNr) LatestSurvey ON LatestSurvey.FarmerID = kcfg.FarmerID AND LatestSurvey.GardenNr = kcfg.GardenNr AND LatestSurvey.SurveyNr = kcfg.SurveyNr INNER JOIN ktv_access_staff kas ON kas.DistrictID = kd.DistrictID INNER JOIN sys_user su ON su.UserId = kas.UserId WHERE su.UserName = "'.$user.'" '.$district.' ) UNION ( SELECT NurseryID OBJECT_ID, IF ( ObjType = "cpg", cpg.GroupName, IF ( ObjType = "farmer", farmer.FarmerName, IF ( ObjType = "koperasi", coop.CoopName, IF ( ObjType = "warehouse", warehouse.WarehouseName, IF ( ObjType = "trader", trader.TraderName, "" ) ) ) ) ) AS OBJECT_NAME, "Nursery" AS OBJECT_TYPE, "1" AS GardenNr, IF ( ObjType = "cpg", cpg.address, IF ( ObjType = "farmer", farmer.address, IF ( ObjType = "koperasi", coop.address, IF ( ObjType = "warehouse", warehouse.address, IF ( ObjType = "trader", trader.address, "" ) ) ) ) ) AS Address, "VillageID" VillageID, "Village" Village, "SubDistrictName" SubDistrictName, "DistrictName" DistrictName, "ProvinceName" ProvinceName, "Latitude" Latitude, "Longitude" Longitude, "Status" AS STATUS FROM ktv_nursery LEFT JOIN ktv_cpg cpg ON cpg.CPGid = ktv_nursery.ObjID AND ktv_nursery.ObjType = "cpg" LEFT JOIN ktv_cooperatives coop ON coop.CoopID = ktv_nursery.ObjID AND ktv_nursery.ObjType = "koperasi" LEFT JOIN ktv_farmer farmer ON farmer.FarmerID = ktv_nursery.ObjID AND ktv_nursery.ObjType = "farmer" LEFT JOIN ktv_warehouse warehouse ON warehouse.WarehouseID = ktv_nursery.ObjID AND ktv_nursery.ObjType = "warehouse" LEFT JOIN ktv_traders trader ON trader.TraderID = ktv_nursery.ObjID AND ktv_nursery.ObjType = "trader" LEFT JOIN ktv_village village ON village.VillageID = cpg.VillageID AND village.VillageID = farmer.VillageID AND village.VillageID = coop.VillageID AND village.VillageID = trader.VillageID AND village.VillageID = warehouse.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID WHERE akses.UserId = "'.$user.'" '.$district.' ) UNION ( SELECT kcf.FarmerID AS OBJECT_ID, kcf.FarmerName AS OBJECT_NAME, "Demoplot" AS OBJECT_TYPE, kcfg.GardenNr AS GardenNr, kcf.Address, kcf.VillageID, kv.Village AS VillageName, Subdistrict AS SubDistrictName, kd.District AS DistrictName, kp.Province AS ProvinceName, kcfg.Latitude, kcfg.Longitude, kcf.StatusCode AS STATUS FROM ktv_farmer kcf INNER JOIN ktv_cpg_batch_trainings kcbt ON kcbt.DemoplotOwnerID = kcf.FarmerID AND kcbt.CPGtrainingsID = 1 LEFT JOIN ktv_cpg kc ON kc.CPGid = kcf.CPGid LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = kv.SubDistrictID LEFT JOIN ktv_district kd ON kd.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID LEFT JOIN ktv_farmer_garden kcfg ON kcfg.FarmerID = kcf.FarmerID INNER JOIN ( SELECT FarmerID, GardenNr GardenNr, MAX(SurveyNr) SurveyNr FROM ktv_farmer_garden GROUP BY FarmerID, GardenNr ) LatestSurvey ON LatestSurvey.FarmerID = kcfg.FarmerID AND LatestSurvey.GardenNr = kcfg.GardenNr AND LatestSurvey.SurveyNr = kcfg.SurveyNr INNER JOIN ktv_access_staff kas ON kas.DistrictID = kd.DistrictID INNER JOIN sys_user su ON su.UserId = kas.UserId WHERE su.UserName = "'.$user.'" '.$district.' ) UNION ( SELECT CoopID AS OBJECT_ID, CoopName AS OBJECT_NAME, "Cooperative" AS OBJECT_TYPE, "" AS GarderNr, ktv_cooperatives.Address, ktv_cooperatives.VillageID, village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName, ktv_cooperatives.Latitude, ktv_cooperatives.Longitude, "Active" AS STATUS FROM ktv_cooperatives LEFT JOIN ktv_village village ON village.VillageID = ktv_cooperatives.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID WHERE akses.UserId = "'.$user.'" '.$district.' ) UNION ( SELECT a.TraderID OBJECT_ID, a.TraderName OBJECT_NAME, "Trader" OBJECT_TYPE, "" GardenNr, a.Address, a.VillageID, a.Longitude, a.Latitude, village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName, "Active" STATUS FROM ktv_traders a LEFT JOIN ktv_village village ON village.VillageID = a.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID WHERE akses.UserId = "'.$user.'" '.$district.' ) UNION ( SELECT WarehouseID OBJECT_ID, WarehouseName OBJECT_NAME, "Warehouse" OBJET_TYPE, "" GardenNr, a.Address, a.VillageID, village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName, a.Latitude, a.Longitude, "Active" STATUS FROM ktv_warehouse a LEFT JOIN ktv_village village ON village.VillageID = a.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID WHERE akses.UserId = "'.$user.'" '.$district.' ) UNION ( SELECT a.FarmerID OBJECT_ID, a.FarmerName OBJECT_NAME, "Sce" OBJECT_TYPE, "" GardenNr, a.Address, a.VillageID, village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName, a.Latitude, a.Longitude, a.StatusCode STATUS FROM sce_farmer b LEFT JOIN ktv_farmer a ON a.FarmerID = b.FarmerID LEFT JOIN ktv_cpg cpg ON cpg.CPGid = a.CPGid LEFT JOIN ktv_village village ON village.VillageID = a.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID WHERE akses.UserId = "'.$user.'" '.$district.' ) UNION ( SELECT a.CPGid OBJECT_ID, a.GroupName OBJECT_NAME, "Cpg" OBJECT_TYPE, "" GardenNr, a.Address, a.VillageID, village.Village AS VillageName, Subdistrict AS SubDistrictName, district.District AS DistrictName, pr.Province AS ProvinceName, a.Latitude, a.Longitude, "Active" STATUS FROM ktv_cpg a LEFT JOIN ktv_village village ON village.VillageID = a.VillageID LEFT JOIN ktv_subdistrict subdistrict ON subdistrict.SubDistrictID = village.SubDistrictID LEFT JOIN ktv_district district ON district.DistrictID = subdistrict.DistrictID LEFT JOIN ktv_province pr ON pr.ProvinceID = district.ProvinceID INNER JOIN ktv_access_staff akses ON akses.DistrictID = subdistrict.DistrictID INNER JOIN sys_user su ON su.UserId = akses.UserId WHERE su.UserName = "'.$user.'" '.$district.' ) ) ALLOFTHEM LIMIT '.$start.', 500';
      //echo($sql);die;
      $results = $this->db->query($sql)->result_array();// var_dump($this->db->_error_message());die;
      $total = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

      return array('details' => $results, 'total' => $total);
    }
}
