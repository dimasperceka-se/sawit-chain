<?php
class MExample extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function saveStatus($latitude, $longitude, $speed){

        $sql = "INSERT INTO ktv_example(latitude, longitude, speed, created_dttm) VALUES ('".$latitude."','".$longitude."','".$speed."',now())";
        $query = $this->db->query($sql);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;

    }

	function readStatus(){

        $sql = "SELECT * FROM ktv_example";
      	$query = $this->db->query($sql);
        $results = $query->result_array();
        return $results;

    }





}
?>