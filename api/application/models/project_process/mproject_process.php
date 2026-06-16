<?php
/**
 * @Author: nikolius
 * @Date:   2017-03-22 15:56:24
 */
class Mproject_process extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getPrintLogoHeader($DistrictID){
        $sql="SELECT
                a.`PhotoPath` AS Photo
            FROM
                ktv_district_partner_logo a
            WHERE
                a.`DistrictID` = ?
                AND a.PhotoPath <> 'koltiva-logo.png'
            ORDER BY a.`PhotoOrder` ASC
            LIMIT 6";
        $query = $this->db->query($sql, array($DistrictID));
        return $query->result_array();
    }

    public function getPrintLogoHeaderFarmer($DistrictID){
        $this->db->from('ktv_mill');
        $this->db->where('PartnerID', $_SESSION['PartnerID']);

        if ($this->db->get()->num_rows() > 0) {
            # code...
            $datas = $this->getPrintLogoHeaderMill($DistrictID);
            $datas2 = $this->getPrintLogoHeaderNonMill($DistrictID);


            $datas = array_merge($datas2,$datas);
        } else {
            # code...
            $datas = $this->getPrintLogoHeaderNonMill($DistrictID);
        }

        return $datas;
    }

    public function getPrintLogoHeaderFarmerNew($MemberID){
        $datas = $this->getPrintLogoHeaderMillPartner($MemberID);
        $datas2 = $this->getPrintLogoHeaderParentAffiliate($MemberID);

        $data = array_merge($datas2,$datas);

        return $data;
    }

    private function getPrintLogoHeaderParentAffiliate($MemberID){   
        $result     = array();
        $result2    = array();
        if($_SESSION['PartnerID'] != 1){
            $sql = "SELECT
                b.PartnerID
                , b.Photo
                , '' Path 
            FROM
                ktv_program_partner a
            LEFT JOIN
                ktv_program_partner b on a.PartnerParentID = b.PartnerID
            WHERE
                a.`PartnerID` = ?
            ORDER BY
                a.`Photo` ASC";
            
            $query = $this->db->query($sql,array($_SESSION['PartnerID']));
            $result = $query->result_array();

            if($result[0]['PartnerID'] != null AND $result[0]['PartnerID'] != ''){
                $PartnerID = $result[0]['PartnerID'];
            }else{
                $PartnerID = $_SESSION['PartnerID'];
            }

            if($query->num_rows()>0){
                foreach($query->result_array() as $i => $row){
                    if($this->awsfileupload->doesObjectExist($result[$i]['Photo']) == true) {
                        $result[$i]['Photo']  = $this->config->item('CTCDN')."/".$result[$i]['Photo'];
                    }else{
                        if(file_exists($result[$i]['Path'].'/'.$result[$i]['Photo'])){
                            $result[$i]['Photo']  = base_url().$result[$i]['Path'].'/'.$result[$i]['Photo'];
                        }
                    }
                }
            }

            $sql2 = "SELECT
                    ap.PartnerID
                    , b.Photo
                    , 'images/Photo' Path
                FROM
                    ktv_affiliate_partner ap
                LEFT JOIN
                    ktv_program_partner b on ap.PartnerID = b.PartnerID
                WHERE
                    ap.PartnerAffiliateID = ?
                AND
                    ap.PartnerID <> 1";
            $query2 = $this->db->query($sql2,array($PartnerID));
            $result2 = $query2->result_array();

            if($query2->num_rows()>0){
                foreach($query2->result_array() as $j => $row2){
                    if($this->awsfileupload->doesObjectExist($result2[$j]['Photo']) == true) {
                        $result2[$j]['Photo']  = $this->config->item('CTCDN')."/".$result2[$j]['Photo'];
                    }else{
                        if(file_exists($result2[$j]['Path'].'/'.$result2[$j]['Photo'])){
                            $result2[$j]['Photo']  = base_url().$result2[$j]['Path'].'/'.$result2[$j]['Photo'];
                        }
                    }
                }
            }

        }
        $data = array_merge($result2,$result);

        return $data;
    }

    private function getPrintLogoHeaderNonMill($DistrictID){
        $sql="SELECT
                a.`PhotoPath` AS Photo
                , 'images/' AS `Path`
            FROM
                ktv_district_partner_logo a
            WHERE
                a.`DistrictID` = ?
            ORDER BY a.`PhotoOrder` ASC
            LIMIT 6";
        $query = $this->db->query($sql, array($DistrictID));
        return $query->result_array();
    }

    private function getPrintLogoHeaderMill($DistrictID){
        // path image-nya berdasarkan ProvinceID
        $this->db->select('ProvinceID');
        $this->db->from('ktv_district');
        $this->db->where('DistrictID', $DistrictID);
        $Province = $this->db->get()->row();

        $sql="SELECT
                a.`Photo` AS Photo
                , CONCAT('images/mill/', ?) AS `Path`
            FROM
                ktv_mill a
            WHERE
                a.`PartnerID` = ?
            ORDER BY MillID
            LIMIT 1";
        $query = $this->db->query($sql, array($Province->ProvinceID, $_SESSION['PartnerID']));
        $datas = $query->result_array();

        // sisipkan logo koltiva
        array_push($datas, array('Photo' => "https://dptwplzs7m8x9.cloudfront.net/web/logo/koltiva_logo.svg", 'Path' => "images/"));

        return $datas;
    }

    public function getPrintLogoHeaderMIllNew($MillID, $DistrictID){
        // path image-nya berdasarkan ProvinceID
        $this->db->select('ProvinceID');
        $this->db->from('ktv_district');
        $this->db->where('DistrictID', $DistrictID);
        $Province = $this->db->get()->row();

        $sql="SELECT
                    IF(kmg.`GroupName` LIKE 'SMART%', 'smart-logo.jpg', km.`Photo`) AS Photo
                    , IF(kmg.`GroupName` LIKE 'SMART%', 'images/', CONCAT('images/mill/', ?)) AS `Path`
                FROM
                    ktv_mill km
                    LEFT JOIN ktv_mill_group kmg ON kmg.`MillGroupID` = km.`MillGroupID`
                WHERE
                    km.`MillID` = ?";
        $query = $this->db->query($sql, array($Province->ProvinceID, $MillID));
        $datas = $query->result_array();

        if($query->num_rows()>0){
            foreach($query->result_array() as $i => $row){
                if($this->awsfileupload->doesObjectExist($datas[$i]['Photo']) == true) {
                    $datas[$i]['Photo']  = $this->config->item('CTCDN')."/".$datas[$i]['Photo'];
                }else{
                    if(file_exists($datas[$i]['Path'].'/'.$datas[$i]['Photo'])){
                        $datas[$i]['Photo']  = base_url().$datas[$i]['Path'].'/'.$datas[$i]['Photo'];
                    }
                }
            }
        }

        // sisipkan logo koltiva
        array_push($datas, array('Photo' => "https://dptwplzs7m8x9.cloudfront.net/web/logo/koltiva_logo.svg", 'Path' => "images/"));

        return $datas;
    }

    public function getPrintLogoHeaderMIllPartner(){
        $this->load->library('awsfileupload');

        // path image-nya berdasarkan ProvinceID
        $datas = array();
        if($_SESSION['PartnerID'] != 1){
            $sql="SELECT
                    pp.Photo
                    , 'images/Photo/' Path
                FROM
                    `ktv_program_partner` pp
                WHERE
                    pp.PartnerID = ?";
            $query = $this->db->query($sql, array($_SESSION['PartnerID']));
            $datas = $query->result_array();

            if($query->num_rows()>0){
                foreach($query->result_array() as $i => $row){
                    if($this->awsfileupload->doesObjectExist($datas[$i]['Photo']) == true) {
                        $datas[$i]['Photo']  = $this->config->item('CTCDN')."/".$datas[$i]['Photo'];
                    }else{
                        if(file_exists($datas[$i]['Path'].'/'.$datas[$i]['Photo'])){
                            $datas[$i]['Photo']  = base_url().$datas[$i]['Path'].'/'.$datas[$i]['Photo'];
                        }
                    }
                }
            }
        }

        // sisipkan logo koltiva
        array_push($datas, array('Photo' => "https://dptwplzs7m8x9.cloudfront.net/web/logo/koltiva_logo.svg", 'Path' => "images/"));
        return $datas;
    }

}
?>