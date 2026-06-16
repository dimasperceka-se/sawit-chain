<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Landuse extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('api_gis/mlanduse');
    }

    public function landuse_detection_get(){
        $url    = "http://localhost:8000/koltigis/v1/checkGarden";
        $user   = "harits.balfas@koltiva.com";
        $pass   = "inirahasia1234";


        $curl = curl_init();
        $data = $this->mlanduse->getGarden();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic aGFyaXRzLmJhbGZhc0Brb2x0aXZhLmNvbTppbmlyYWhhc2lhMTIzNA=='
            ),
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($data)
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);
        $response_data = json_decode($response);

        foreach ($response_data as $data) {
            $data = array(
                'MemberID'      => intval($data->ID),
                'PlotNr'        => intval($data->PlotNr),
                'SurveyNr'      => intval($data->SurveyNr),
                'Revision'      => intval($data->Revision),
                'Landuse'       => $data->Landuse,
                'Percentage'    => floatval($data->Percentage)
            );
        
            $this->db->insert('gis_garden_landuse_summary_tmp', $data);
        }

        return $response_data;
    }


}



