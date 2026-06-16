<?php
class Mlanduse extends CI_Model {

    function __construct() {
        parent::__construct();
    }

	public function getGarden()
	{

        $sql="SELECT kspg.MemberID, kspg.PlotNr, kspg.SurveyNr, kspg.Revision, st_asgeojson(kspg.polygon) AS Polygon
                FROM (
                        SELECT x.MemberID, x.PlotNr, x.SurveyNr, x.Revision, x.polygon, ROW_NUMBER() OVER (PARTITION BY x.MemberID, x.PlotNr ORDER BY x.SurveyNr, x.Revision DESC) as rn 
                        FROM ktv_survey_plot_polygon_geo x
                    ) kspg
                    JOIN ktv_survey_plot ksp on ksp.MemberID = kspg.MemberID 
                    JOIN ktv_village kv on kv.VillageID = ksp.VillageID 
                    JOIN ktv_subdistrict ks on ks.SubDistrictID = kv.SubDistrictID 
                    JOIN ktv_district kd on kd.DistrictID = ks.DistrictID 
                where kspg.rn = 1 and kd.DistrictID = 1175 and st_isvalid(kspg.Polygon) = 1
                GROUP BY kspg.MemberID, kspg.PlotNr";

        $query = $this->db->query($sql);
	    
        return $query->result_array();
	}

    
     
}
?>
