<?php
class Mfarmer extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function readFarmers(){

        $sql = "SELECT a.FarmerID, a.FarmerGroupID, b.person_nm FarmerName, b.address Address, b.handphone Handphone,b.regional_cd,"
            . " b.gender Gender, b.marital_st MaritalStatus, b.birth_dttm Birthdate,b.education Education, b.photo Photo,"
            . " a.WritingAwal, a.WritingAkhir, a.BallotAwal, a.BallotAkhir, a.Muge, a.KeyFarmer, a.DemoPlot,"
            . "  a.OtherTraining,"
            . " a.CPGmembership, a.OtherTrainingSiapa, a.OtherTrainingTahun, a.OtherTrainingLama, a.DemoPlotLama,"
            . " a.DateCreated, a.DateUpdated, a.LastModifiedBy"
            . " FROM ktv_farmer a"
            . " INNER JOIN ktv_persons b ON b.person_id = a.PersonID"
            . " WHERE a.StatusCode='active'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function readFarmer($id){

        $sql = "SELECT a.FarmerID, a.FarmerGroupID, b.person_nm FarmerName, b.address Address, b.handphone Handphone,b.regional_cd,"
             . " b.gender Gender, b.marital_st MaritalStatus, b.birth_dttm Birthdate,b.education Education, b.photo Photo,"
             . " a.WritingAwal, a.WritingAkhir, a.BallotAwal, a.BallotAkhir, a.Muge, a.KeyFarmer, a.DemoPlot,"
             . " a.AnggotaKerjaKebun, a.BuruhSeasonal, a.BuruhFulltime, a.HarvestYesNo, a.Fermentation, a.FermentationDays,"
             . " a.SunDryingSemen, a.DryingAlat, a.DryingDays, a.CocoaBuyers, a.NoFermentation, a.Sortasi, a.NoSortasi,"
             . " a.LahanKosong, a.SunDryingAspal, a.JemurYesNo, a.TidakJemur, a.SunDryingAlas, a.OtherTraining,"
             . " a.CPGmembership, a.OtherTrainingSiapa, a.OtherTrainingTahun, a.OtherTrainingLama, a.DemoPlotLama,"
             . " a.DateCollection, a.DateCreated, a.DateUpdated, a.LastModifiedBy"
             . " FROM ktv_farmer a"
             . " INNER JOIN ktv_persons b ON b.person_id = a.PersonID"
             . " WHERE FarmerID= '".$id."' and a.StatusCode='active'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function createFarmer($cpgID){

        $farmerID =  $this->_generateFarmerID($cpgID);
        // $farmerID = 1000200077;
        $farmerName = "[ Nama Petani ]";

        $sql2 = "INSERT INTO ktv_persons (person_nm) VALUES ('".$farmerName."')";
        $query2 = $this->db->query($sql2);

        $new_person_id = $this->db->insert_id();

        $sql1 = "INSERT INTO ktv_farmer (FarmerID, PersonID, FarmerGroupID) VALUES ('".$farmerID."','".$new_person_id."','".$cpgID."')";
        $query1 = $this->db->query($sql1);


        if ($query1 && $query2) {
            $results['success'] = true;
            $results['message'] = "farmer created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to add farmer";
        }
        return $results;

    }

    function _generateFarmerID($cpgID){

        // Prog ID SCPP = 1
        $progID = 1;
        $sql = "SELECT farmer_cpg, farmer_no"
            . " FROM ktv_farmer_seq"
            . " WHERE farmer_cpg = '$cpgID'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0){
            $row = $query->row();
            $farmer_cpg = $row->farmer_cpg;
            $farmer_no  = $row->farmer_no;
        } else {
            $farmer_cpg = $cpgID;
            $farmer_no  = 0;
        }

        $new_farmer_no = sprintf("%05d",$farmer_no+1);
        $sql = "REPLACE INTO ktv_farmer_seq (farmer_cpg,farmer_no) VALUES ('$farmer_cpg','$new_farmer_no')";
        $this->db->query($sql);

        $farmer_cpg = sprintf("%04d", $cpgID);
        $new_farmer_id = "$farmer_cpg$new_farmer_no";
        log_message('debug', var_export($new_farmer_id));
        return $new_farmer_id;

    }

    function updateFarmer($farmerID, $personID, $farmerName, $address, $regional_cd, $handphone, $gender, $maritalStatus, $birthDate, $birthPlace, $education, $photo, $WritingAwal, $WritingAkhir, $BallotAwal, $BallotAkhir, $Muge, $KeyFarmer, $DemoPlot, $AnggotaKerjaKebun, $BuruhSeasonal, $BuruhFulltime, $HarvestYesNo,$Fermentation, $FermentationDays, $SunDryingSemen, $DryingAlat, $DryingDays, $CocoaBuyers, $NoFermentation, $Sortasi, $NoSortasi, $LahanKosong, $SunDryingAspal, $JemurYesNo, $TidakJemur, $SunDryingAlas, $OtherTraining, $CPGmembership, $OtherTrainingSiapa, $OtherTrainingTahun, $OtherTrainingLama, $DemoPlotLama){

        $sql1 ="UPDATE ktv_persons SET person_nm='".$farmerName."',birth_dttm='".$birthDate."',birth_place='".$birthPlace."',"
            . " gender='".$gender."',address='".$address."',regional_cd='".$regional_cd."',handphone='".$handphone."',"
            . " marital_st='".$maritalStatus."',education='".$education."',photo='".$photo."'"
            . " WHERE "
            . " person_id='".$personID."'";
        $query1 = $this->db->query($sql1);

        $sql2 ="UPDATE ktv_farmer "
            . " SET WritingAwal='".$WritingAwal."',WritingAkhir='".$WritingAkhir."',BallotAwal='".$BallotAwal."',"
            . " BallotAkhir='".$BallotAkhir."',Muge='".$Muge."',DemoPlot='".$DemoPlot."',AnggotaKerjaKebun='".$AnggotaKerjaKebun."',"
            . " BuruhSeasonal='".$BuruhSeasonal."',BuruhFulltime='".$BuruhFulltime."',HarvestYesNo='".$HarvestYesNo."'"
            . " Fermentation='".$Fermentation."',FermentationDays='".$FermentationDays."',SunDryingSemen='".$SunDryingSemen."'"
            . " DryingAlat='".$DryingAlat."',DryingDays='".$DryingDays."',CocoaBuyers='".$CocoaBuyers."'"
            . " NoFermentation='".$NoFermentation."',Sortasi='".$Sortasi."',NoSortasi='".$NoSortasi."'"
            . " LahanKosong='".$LahanKosong."',SunDryingAspal='".$SunDryingAspal."',JemusYesNo='".$JemurYesNo."'"
            . " TidakJemur='".$TidakJemur."',SunDryingAlas='".$SunDryingAlas."',OtherTraining='".$OtherTraining."'"
            . " CPGmembership='".$CPGmembership."',OtherTrainingSiapa='".$OtherTrainingSiapa."',OtherTrainingTahun='".$OtherTrainingTahun."'"
            . " OtherTrainingLama='".$OtherTrainingLama."',DemoPlotLama='".$DemoPlotLama."',KeyFarmer='".$KeyFarmer."'"
            . " WHERE "
            . " FarmerID='".$farmerID."'";
        $query2 = $this->db->query($sql2);

        if ($query1 && $query2) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deleteFarmer($farmerID, $personID){

        $sql = "UPDATE ktv_farmer SET StatusCode='nullified' WHERE FarmerID=".$farmerID."";
        $query = $this->db->query($sql);

        $sql = "UPDATE ktv_persons SET status_cd='nullified' WHERE person_id=".$personID."";
        $query = $this->db->query($sql);


        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;

    }

    function sync_all_farmer(){

        $sql = "SELECT FarmerID, FarmerGroupID, FarmerName,  Address, Handphone, Gender, MaritalStatus, Birthdate, Education, Photo, WritingAwal, WritingAkhir, BallotAwal, BallotAkhir, Muge, KeyFarmer, DemoPlot, AnggotaKerjaKebun, BuruhSeasonal, BuruhFulltime, HarvestYesNo, Fermentation, FermentationDays, SunDryingSemen, DryingAlat, DryingDays, CocoaBuyers, NoFermentation, Sortasi, NoSortasi, LahanKosong, SunDryingAspal, JemurYesNo, TidakJemur, SunDryingAlas, OtherTraining, CPGmembership, OtherTrainingSiapa, OtherTrainingTahun, OtherTrainingLama, DemoPlotLama, DateCollection, FarmerGroupFunctionsID, DateCreated, DateUpdated, LastModifiedBy FROM tblcocoafarmer WHERE 1";
        $query = $this->db->query($sql);
        $chart = array();
        foreach ($query->result_array() as $rs)
        {
            $results[] = $rs;
        }
        return $results;
    }

    function count_all_farmer(){

        $sql = "SELECT count(*) jml FROM tblcocoafarmer WHERE 1";
        // $query = $this->db->query($sql);
        // $chart = array();
        // $sql = "SELECT MAX(recno) AS recno FROM gic_msacct_ar";
        $result = mysql_query($sql);
        $r = mysql_fetch_array($result);
        $count = $r['jml'];
        return $count;
    }

    function sync_up_farmer(){

        //indikator
        // $indikator1       = $this->input->post('rb1');
        // $_REQUEST["org_id"];
        $FarmerID =$_REQUEST["FarmerID"];
        $FarmerGroupID =$_REQUEST["FarmerGroupID"];
        $FarmerName=$_REQUEST["FarmerName"];
        $Province=$_REQUEST["Province"];
        $District=$_REQUEST["District"];
        $SubDistrict=$_REQUEST["SubDistrict"];
        $Address=$_REQUEST["Address"];
        $Handphone=$_REQUEST["Handphone"];
        $Gender=$_REQUEST["Gender"];
        $MaritalStatus=$_REQUEST["MaritalStatus"];
        $Birthdate=$_REQUEST["Birthdate"];
        $Education=$_REQUEST["Education"];
        // $Photo=$this->input->post('Photo');
        // $WritingAwal=$this->input->post('WritingAwal');
        // $WritingAkhir=$this->input->post('WritingAkhir');
        // $BallotAwal=$this->input->post('BallotAwal');
        // $BallotAkhir=$this->input->post('BallotAkhir');
        // $Muge=$this->input->post('Muge');
        // $KeyFarmer=$this->input->post('KeyFarmer');
        // $AnggotaKerjaKebun=$this->input->post('AnggotaKerjaKebun');
        // $BuruhSeasonal=$this->input->post('BuruhSeasonal');
        // $BuruhFulltime=$this->input->post('BuruhFulltime');
        // $HarvestYesNo=$this->input->post('HarvestYesNo');
        // $Fermentation=$this->input->post('Fermentation');
        // $FermentationDays=$this->input->post('FermentationDays');
        // $SunDryingSemen=$this->input->post('SunDryingSemen');
        // $DryingAlat=$this->input->post('DryingAlat');
        // $DryingDays=$this->input->post('DryingDays');
        // $CocoaBuyers=$this->input->post('CocoaBuyer');
        // $NoFermentation=$this->input->post('NoFermentation');
        // $Sortasi=$this->input->post('Sortasi');
        // $NoSortasi=$this->input->post('NoSortasi');
        // $LahanKosong=$this->input->post('LahanKosong');
        // $SunDryingAspal=$this->input->post('SunDryingAspal');
        // $JemurYesNo=$this->input->post('JemurYesNo');
        // $TidakJemur=$this->input->post('TidakJemur');
        // $SunDryingAlas=$this->input->post('SunDryingAlas');
        // $OtherTraining=$this->input->post('OtherTraining');
        // $CPGmembership=$this->input->post('CPGmembership');
        // $OtherTrainingSiapa=$this->input->post('OtherTrainingSiapa');
        // $OtherTrainingLama=$this->input->post('OtherTrainingLama');
        // $DemoPlotLama=$this->input->post('DemoPlotLama');
        // $DateCollection=$this->input->post('DateCollection');
        // $FarmerGroupFunctionsID=$this->input->post('FarmerGroupFunctionsID');
        // $DateCreated=$this->input->post('DateCreated');
        // $DateUpdated=$this->input->post('DateUpdated');
        // $LastModifiedBy=$this->input->post('LastModifiedBy');


        $sql  = "INSERT INTO tblcocoafarmer(FarmerID,FarmerGroupID, FarmerName,Province,District,SubDistrict,Address,Handphone,Gender,MaritalStatus,Birthdate,Education)";
        $sql .= "VALUES ('".$FarmerID."','".$FarmerGroupID."', '".$FarmerName."','".$Province."', '".$District."', '".$SubDistrict."','".$Address."', '".$Handphone."','".$Gender."', '".$MaritalStatus."','".$Birthdate."', '".$Education."')";
        $this->db->query($sql);

    }
	
	// family relationship
    function readFamRelation($id){
        $sql = "SELECT HubunganKeluarga FROM ktv_family WHERE FamilyID={$id}";
        $query = $this->db->query($sql);
        return $query->row()->HubunganKeluarga;
    }

}
?>
