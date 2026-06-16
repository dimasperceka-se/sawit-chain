<?php
class Mstaff extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function readProgramStaffs(){

        $sql ="SELECT a.staff_id, a.partner_id, a.person_id, b.person_nm, b.birth_dttm, b.birthplace, b.address,"
            . " b.handphone, b.email, b.gender, b.marital_st,b.education, b.photo "
            . " FROM ktv_program_staff a "
            . " INNER JOIN ktv_persons b ON a.person_id = b.person_id"
            . " WHERE b.status_cd = 'active'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function readProgramStaff($id){


        $sql ="SELECT a.StaffID, a.PartnerID, a.PersonID, b.person_nm, b.birth_dttm, b.birthplace, b.address,"
            . " b.handphone, b.email, b.gender, b.marital_st,b.education, b.photo "
            . " FROM ktv_program_staff a "
            . " INNER JOIN ktv_persons b ON a.PersonID = b.person_id"
            . " WHERE a.PersonID='".$id."' AND b.status_cd = 'active'";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;
    }

    function createProgramStaff($partnerID, $staffName, $regionalID, $birthdate, $birthplace, $address, $handphone, $email, $gender, $maritalStatus, $education, $photo){

        $sql2 = "INSERT INTO ktv_persons(person_nm, regional_cd, birth_dttm, birthplace, address, handphone, email, gender,marital_st,education,photo, created_dttm, created_by) VALUES ('".$staffName."','".$regionalID."','".$birthdate."','".$birthplace."','".$address."','".$handphone."','".$email."','".$gender."','".$maritalStatus."','".$education."','".$photo."',now(), 1)";
        $query2 = $this->db->query($sql2);

        // get person ID
        $personID = $this->db->insert_id();

        $sql1 = "INSERT INTO ktv_program_staff(partner_id, person_id, created_dttm, created_by) VALUES ('".$partnerID."','".$personID."', now(), 1)";
        $query1 = $this->db->query($sql1);

        if ($query1 && $query2) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;

    }

    function updateProgramStaff($staffID, $personID, $partnerID, $staffName, $regionalID, $birthdate, $birthplace, $address, $handphone, $email, $gender, $maritalStatus, $education, $photo){

        $sql1 ="UPDATE ktv_persons SET person_nm='".$staffName."',regional_cd='".$regionalID."',"
            . " birth_dttm='".$birthdate."',birthplace='".$birthplace."',address='".$address."',handphone='".$handphone."',"
            . " email='".$email."',gender='".$gender."',marital_st='".$maritalStatus."',education='".$education."',"
            . " photo='".$photo."', modified_dttm=now(), modified_by=1"
            . " WHERE "
            . " person_id='".$personID."'";
        $query1 = $this->db->query($sql1);

        $sql2 ="UPDATE ktv_program_staff SET partner_id='".$partnerID."', modified_dttm=now(), modified_by=1"
            . " WHERE "
            . " staff_id='".$staffID."'";
        $query2 = $this->db->query($sql2);

        if ($query1 && $query2) {
            $results['success'] = true;
            $results['message'] = "record update.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;

    }


    function deleteProgramStaff($staffID, $personID){

        $sql1 ="UPDATE ktv_persons SET status_cd='nullified'"
            . " modified_dttm=now(), modified_by=1"
            . " WHERE "
            . " person_id='".$personID."'";
        $query1 = $this->db->query($sql1);

        $sql2 ="UPDATE ktv_program_staff SET status_cd='nullified', modified_dttm=now(), modified_by=1"
            . " WHERE "
            . " staff_id='".$staffID."'";
        $query2 = $this->db->query($sql2);

        if ($query1 && $query2) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;

    }



}
?>