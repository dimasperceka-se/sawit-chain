<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mupload_farm_polygon extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}


	public function getPartnerName($PartnerID){
		$query = $this->db->select('PartnerName')->get_where('ktv_program_partner', array('PartnerID' => $PartnerID), 1);
		if ($query->num_rows() > 0) {
			return $query->row_array(0)["PartnerName"];
		}
		return false;
	}


	public function getMemberData($MemberDisplayID)
	{
		$query = $this->db->select('MemberID,MemberName')->get_where('ktv_members', array('MemberDisplayID' => $MemberDisplayID), 1);
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
	}


	public function cekPlotNr($MemberID, $PlotNr){
		$sql = "SELECT ksp.MemberID, ksp.PlotNr
					FROM ktv_survey_plot as ksp
					WHERE ksp.MemberID = {$MemberID}
						AND ksp.PlotNr = {$PlotNr}
				";
		$query = $this->db->query($sql);
	
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
	
		return false;
	}


	public function cekSurveyNr($MemberID, $PlotNr, $SurveyNr)
	{
		$sql = "SELECT MAX(ksppg.Revision) as `LastRevision`
				FROM ktv_survey_plot_polygon_geo as ksppg
				WHERE ksppg.MemberID = {$MemberID}
					AND ksppg.PlotNr = {$PlotNr}
					AND ksppg.SurveyNr = {$SurveyNr}
			";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row_array(0);
		}
		return false;
	}


	public function importKMLtmp($file) {
		$this->load->helper('file');
		$kml = read_file($file);
		if (!empty($kml)) {
			$this->db->query("DELETE FROM ktv_upload_farm_polygon_tmp WHERE CreatedBy = {$_SESSION['userid']}");
	
			$places_xml = simplexml_load_string($kml);
			$errors = array();
			$success = array();
			if ($places_xml) {
				foreach ($places_xml->Document->Folder->Placemark as $key => $value) {
	
					$MemberDisplayID_key = null;
					$MemberName_key = null;
					$PlotNr_key = null;
					$SurveyNr_key = null;
					$StatusCheck_key = null;
					
					$MemberID 		= "";
					$MemberName		= "";
					$PlotNr			= 0;
					$SurveyNr		= 0;
					$StatusCheck  	= 'new';  
					$Polygon		= 'null';
					$Lat			= 'null';
					$Lng			= 'null';  
					$CenterLatLong  = 'null';  
					$Valid			= 1;
					$AreaHa			= 'null';
					$Remark			= "-";
					$DateCreated	= date("Y-m-d H:i:s");
					$CreatedBy		= $_SESSION["userid"];
					$Revision 		= 1;
					$PartnerName = $this->getPartnerName($_SESSION['PartnerID']);
	
	
					// Cek Coordinat & convert to polygon string
						$coordinates = trim(strval($value->Polygon->outerBoundaryIs->LinearRing->coordinates));
	
						$coor_arr   = explode(' ', $coordinates);
						if ($coor_arr[count($coor_arr)-1] != $coor_arr[0]){
							$coor_arr[] = $coor_arr[0];
						} 
						for ($i = 0; $i < count($coor_arr); $i++) {

							// Handle Z -> remove Z jika ada Z di Polygon
								$coor_xy_str = $coor_arr[$i];
								$coor_xy_arr = explode(",",$coor_xy_str);
								$coor_xy_arr = [$coor_xy_arr[0],$coor_xy_arr[1]];
								$coor_xy_str = implode(",", $coor_xy_arr);
							
							$coor_arr[$i] = str_replace(","," ",$coor_xy_str);
						}
	
						$PolygonStr = 'POLYGON ((' . implode(", ",$coor_arr). '))' ;
						
						$Polygon 		= "ST_GeomFromText('{$PolygonStr}', 4326, 'axis-order=long-lat')";
						$Lat 			= "ST_Y(ST_Centroid(ST_GeomFromText('{$PolygonStr}')))";
						$Lng 			= "ST_X(ST_Centroid(ST_GeomFromText('{$PolygonStr}')))";
						// $AreaHa			= "ST_Area(ST_GeomFromText('{$PolygonStr}', 4326, 'axis-order=long-lat'))/10000";
						$AreaHa = $value->POLYGON_HA;
	
					
					// GET Data FarmerID, FarmNr, SurveyNr, StatusCheck From KML
						for ($i = 0; $i < count($value->ExtendedData->SchemaData->SimpleData); $i++) {
							$v = reset($value->ExtendedData->SchemaData->SimpleData[$i]);
							if (strtoupper($v['name']) == 'ID') {
								$MemberDisplayID_key = $i;
							}
							if (strtoupper($v['name']) == 'NAME') {
								$MemberName_key = $i;
							}
							if (strtoupper($v['name']) == 'FARM_NR') {
								$PlotNr_key = $i;
							}
							if (strtoupper($v['name']) == 'SURVEY_NR') {
								$SurveyNr_key = $i;
							}
							if (strtoupper($v['name']) == 'STAT_POLY') {
								$StatusCheck_key = $i;
							}
							if (strtoupper($v['name']) == 'POLYGON_HA') {
								$PolygonHa = $i;
							}
	
							$MemberDisplayID    = strval($value->ExtendedData->SchemaData->SimpleData[$MemberDisplayID_key]);
							$MemberName    		= strval($value->ExtendedData->SchemaData->SimpleData[$MemberName_key]);
							$PlotNr    			= intval($value->ExtendedData->SchemaData->SimpleData[$PlotNr_key]);
							$SurveyNr    		= intval($value->ExtendedData->SchemaData->SimpleData[$SurveyNr_key]);
							$StatusCheck    	= strtolower($value->ExtendedData->SchemaData->SimpleData[$StatusCheck_key]);
							$PolygonHa    		= $value->ExtendedData->SchemaData->SimpleData[$PolygonHa];
						}
	
						$MemberData = $this->getMemberData($MemberDisplayID);
					
					// Cek Valid Data 
						if($StatusCheck_key !=null){
							if($MemberData){
								$MemberID = $MemberData['MemberID'];
								$MemberName = $MemberData["MemberName"];
								$cekPlotNr = $this->cekPlotNr($MemberID, $PlotNr);
								if($cekPlotNr){
									$cekSurveyNr = $this->cekSurveyNr($MemberID, $PlotNr, $SurveyNr );
									if ($cekSurveyNr["LastRevision"] !="") {
										$Revision 	= intval($cekSurveyNr["LastRevision"]) + 1;
									} else {
										if($SurveyNr != 20 ) $Valid = 0;
										$Remark		= "SurveyNr Not Exist";
									}
								}else{
									$Valid		= 0;
									$Remark		= "FarmNr Not Exist";
								}
							} else {
								$Valid		= 0;
								$Remark		= "Farmer Not Exist";
							}
						} else {
							$Valid		= 0;
							$Remark		= "[ STAT_POLY ] Empty";
						}
							
					// Insert Into Database
						$sql = "INSERT INTO ktv_upload_farm_polygon_tmp
									(	
										`MemberDisplayID`,
										`MemberName`,
										`PlotNr`,
										`SurveyNr`,
										`Revision`,
										`StatusCheck`,
										`PartnerName`,
										`Polygon`,
										`Lat`,
										`Lng`,
										`AreaHa`,
										`Valid`,
										`Remark`,
										`DateCreated`,
										`CreatedBy`
									)
								VALUES
									(
										'{$MemberDisplayID}',
										'{$MemberName}',
										{$PlotNr},
										{$SurveyNr},
										{$Revision}, 
										'{$StatusCheck}', 
										'{$PartnerName}', 
										{$Polygon},
										{$Lat},
										{$Lng},
										{$PolygonHa},
										{$Valid},
										'{$Remark}',
										'{$DateCreated}',
										{$CreatedBy}
									)";
						$this->db->query($sql);
	
	
						$sql = "SELECT a.Lat, a.Lng FROM ktv_upload_farm_polygon_tmp a
						WHERE 1=1
							AND a.MemberDisplayID 	= '{$MemberDisplayID}'
							AND a.PlotNr 			= {$PlotNr}
							AND a.SurveyNr 			= {$SurveyNr}
							AND a.CreatedBy 		= {$CreatedBy}
						";
						
						$tmp  = $this->db->query($sql)->result_array();
						
						$f_Lat = floatval($tmp[0]["Lat"]);
						$f_Lng = floatval($tmp[0]["Lng"]);
	
						$sql = "UPDATE ktv_upload_farm_polygon_tmp a
								SET a.CenterLatLong = ST_GeomFromText('POINT({$f_Lng} {$f_Lat})', 4326, 'axis-order=long-lat')
								WHERE 1=1
									AND a.MemberDisplayID 	= '{$MemberDisplayID}'
									AND a.PlotNr 			= {$PlotNr}
									AND a.SurveyNr 			= {$SurveyNr}
									AND a.CreatedBy 		= {$CreatedBy}
								";
						$this->db->query($sql);
				}
			}
		}
	}


	public function getFarmPolygon(){
		$sql = "SELECT 
					  a.`MemberDisplayID`
					   , a.`MemberName`
					   , a.`PlotNr`
					   , a.`SurveyNr`
					   , a.`Revision`
					   , a.`StatusCheck`
					   , a.`Lat`
					   , a.`Lng`
					   , ST_ASGEOJSON(a.Polygon) as `Polygon`
					   , a.`AreaHa`
					   , a.`Valid`
					   , a.`Remark`
				FROM ktv_upload_farm_polygon_tmp a
				WHERE CreatedBy = ?
				";
		$query = $this->db->query($sql, array($_SESSION['userid']));
	
		if ($query->num_rows() > 0) {
			$return['data'] = $query->result_array();
			$query = $this->db->query("SELECT FOUND_ROWS() AS total");
			$return['total'] = $query->row_array(0)['total'];
			return $return;
		}
		return true;
	}


	public function farmPolygonClearData(){
		return $this->db->query("DELETE FROM ktv_upload_farm_polygon_tmp WHERE CreatedBy=?",  array($_SESSION['userid']));
	}

	public function cekFarmPolygon()
	{
		$sql = "SELECT a.*
				FROM ktv_upload_farm_polygon_tmp a
					JOIN ktv_members m ON m.MemberDisplayID = a.MemberDisplayID
					JOIN ktv_survey_plot_status p ON m.MemberID = p.MemberID AND a.PlotNr = p.PlotNr
				WHERE a.CreatedBy = ?";
	
		$query = $this->db->query($sql, array($_SESSION['userid']));
		
		if ($query->num_rows() > 0) {
			return true;
		}
	
		return false;
	}


	public function updateFarmPolygon()
    {
        $this->db->trans_start(FALSE);
            
        // insert data to ktv_survey_plot_polygon_geo
            
            $sql = "INSERT INTO `ktv_survey_plot_polygon_geo` (
                `MemberID`
                , `PlotNr`
                , `SurveyNr`
                , `Revision`
                , `Polygon`
                , `CenterLatLong`
                , `AreaHa`
                , `PartnerName`
                , `StatusCheck`
                , `DateCreated`
                , `CreatedBy`
            )
            SELECT
                m.`MemberID`
                , a.`PlotNr`
                , a.`SurveyNr`
                , a.`Revision`
                , a.`Polygon`
                , a.`CenterLatLong`
                , a.`AreaHa`
                , a.`PartnerName`
                , a.`StatusCheck`
                , a.`DateCreated`
                , a.`CreatedBy`
            FROM
                ktv_upload_farm_polygon_tmp a
                JOIN ktv_members m ON m.MemberDisplayID = a.MemberDisplayID 
            WHERE 1=1
                AND a.`CreatedBy` = {$_SESSION['userid']}
                AND a.Valid = 1";

            $query = $this->db->query($sql);

        // Update data to ktv_survey_plot_status
            $sql = "UPDATE ktv_survey_plot_status ksps
                    JOIN ktv_members km ON km.MemberID = ksps.MemberID 
                    INNER JOIN ktv_upload_farm_client_tmp kufc on 1=1
                        AND kufc.MemberDisplayID = km.MemberDisplayID
                        AND kufc.PlotNr = ksps.PlotNr
                    SET ksps.GardenAreaPolygon = kufc.AreaHa
                        , ksps.Latitude = kufc.Lat
                        , ksps.Longitude = kufc.Lng
                        , ksps.LatLong = kufc.CenterLatLong
                        , ksps.DateUpdated = kufc.DateCreated
                        , ksps.LastModifiedBy = kufc.CreatedBy
                    WHERE 1=1
                        AND kufc.`CreatedBy` = {$_SESSION['userid']}
                        AND kufc.Valid = 1";
            $query = $this->db->query($sql);

            
            $sql = "UPDATE ktv_survey_plot ksps
                    JOIN ktv_members km ON km.MemberID = ksps.MemberID 
                    INNER JOIN ktv_upload_farm_client_tmp kufc on 1=1
                        AND kufc.MemberDisplayID = km.MemberDisplayID
                        AND kufc.PlotNr = ksps.PlotNr
                    SET ksps.GardenAreaPolygon = kufc.AreaHa
                        , ksps.Latitude = kufc.Lat
                        , ksps.Longitude = kufc.Lng
                        , ksps.LatLong = kufc.CenterLatLong
                        , ksps.DateUpdated = kufc.DateCreated
                        , ksps.LastModifiedBy = kufc.CreatedBy
                    WHERE 1=1
                        AND kufc.`CreatedBy` = {$_SESSION['userid']}
                        AND kufc.Valid = 1";
            $query = $this->db->query($sql);
        
        // Delete Temporary
            
            $this->db->delete('ktv_upload_farm_polygon_tmp', ['CreatedBy' => $_SESSION['userid']]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}
/* End of file mupload_farm_polygon.php */
/* Location: ./api/application/models/data_adm/Mupload_farm_polygon.php */
