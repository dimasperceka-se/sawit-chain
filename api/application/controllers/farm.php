<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * Farm API
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Furqon Ramdhani
 * @link		http://furqonramdhani.com
 */

class Farm extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mfarmer');
    }

    function farmer_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $farmer = $this->Mfarmer->readFarmer($this->get('id'));

        if($farmer)
        {
            $this->response($farmer, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    function farmers_get()
    {

        $farmers = $this->Mfarmer->readFarmers();

        if($farmers)
        {
            $this->response($farmers, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any farmers!'), 404);
        }
    }


    function farmer_post()
    {
        // get CPG ID
        $farmerGroupID = $this->post('id');

        if(!$farmerGroupID)
        {
            $this->response(NULL, 400);
        }

        $farmer = $this->Mfarmer->createFarmer($farmerGroupID);

        if($farmer)
        {
            $this->response($farmer, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Farmer could not be create'), 404);
        }
    }

    function farmer_put()
    {
        $farmerID = $this->get('id');
        $personID = $this->get('id');
        $farmerName = $this->get('id');
        $address = $this->get('id');
        $regional_cd = $this->get('id');
        $handphone = $this->get('id');
        $gender = $this->get('id');
        $maritalStatus = $this->get('id');
        $birthDate = $this->get('id');
        $birthPlace = $this->get('id');
        $education = $this->get('id');
        $photo = $this->get('id');
        $WritingAwal = $this->get('id');
        $WritingAkhir = $this->get('id');
        $BallotAwal = $this->get('id');
        $BallotAkhir = $this->get('id');
        $Muge = $this->get('id');
        $KeyFarmer = $this->get('id');
        $DemoPlot = $this->get('id');
        $AnggotaKerjaKebun = $this->get('id');
        $BuruhSeasonal = $this->get('id');
        $BuruhFulltime = $this->get('id');
        $HarvestYesNo = $this->get('id');
        $Fermentation = $this->get('id');
        $FermentationDays = $this->get('id');
        $SunDryingSemen = $this->get('id');
        $DryingAlat = $this->get('id');
        $DryingDays = $this->get('id');
        $CocoaBuyers = $this->get('id');
        $NoFermentation = $this->get('id');
        $Sortasi = $this->get('id');
        $NoSortasi = $this->get('id');
        $LahanKosong = $this->get('id');
        $SunDryingAspal = $this->get('id');
        $JemurYesNo = $this->get('id');
        $TidakJemur = $this->get('id');
        $SunDryingAlas = $this->get('id');
        $OtherTraining = $this->get('id');
        $CPGmembership = $this->get('id');
        $OtherTrainingSiapa = $this->get('id');
        $OtherTrainingTahun = $this->get('id');
        $OtherTrainingLama = $this->get('id');
        $DemoPlotLama = $this->get('id');

        if(!$farmerID)
        {
            $this->response(NULL, 400);
        }

        $farmer = $this->Mfarmer->updateFarmer($farmerID, $personID, $farmerName, $address, $regional_cd, $handphone, $gender, $maritalStatus, $birthDate, $birthPlace, $education, $photo, $WritingAwal, $WritingAkhir, $BallotAwal, $BallotAkhir, $Muge, $KeyFarmer, $DemoPlot, $AnggotaKerjaKebun, $BuruhSeasonal, $BuruhFulltime, $HarvestYesNo,$Fermentation, $FermentationDays, $SunDryingSemen, $DryingAlat, $DryingDays, $CocoaBuyers, $NoFermentation, $Sortasi, $NoSortasi, $LahanKosong, $SunDryingAspal, $JemurYesNo, $TidakJemur, $SunDryingAlas, $OtherTraining, $CPGmembership, $OtherTrainingSiapa, $OtherTrainingTahun, $OtherTrainingLama, $DemoPlotLama);

        if($farmer)
        {
            $this->response($farmer, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function farmer_delete()
    {
        $farmerID = $this->delete('farmerID');
        $personID = $this->delete('personID');

        // $this->response($id, 200);
        if(!$farmerID)
        {
            $this->response(NULL, 400);
        }

        $farmer = $this->Mfarmer->deleteFarmer($farmerID,$personID);

        if($farmer)
        {
            $this->response($farmer, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be delete'), 404);
        }
    }


    public function send_post()
    {
        var_dump($this->request->body);
    }


    public function send_put()
    {
        var_dump($this->put('foo'));
    }
}