<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * Program Partner API
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Furqon Ramdhani
 * @link		http://furqonramdhani.com
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Programpartner extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mprogrampartner');

    }

    function programpartners_get()
    {
        $ProgramPartners = $this->Mprogrampartner->readProgramPartners();
        if($ProgramPartners)
        {
            $this->response($ProgramPartners, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any ProgramPartners!'), 404);
        }
    }


    function programpartner_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $ProgramPartner = $this->Mprogrampartner->readProgramPartner($this->get('id'));

        if($ProgramPartner)
        {
            $this->response($ProgramPartner, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }


    function programpartner_post()
    {
        $partnerName= $this->post('partnerName');
        $partnerIndustry = $this->post('partnerIndustry');
        $partnerFullName = $this->post('partnerFullName');
        $photo= $this->post('photo');


        if(!$partnerName)
        {
            $this->response(NULL, 400);
        }

        $ProgramPartner = $this->Mprogrampartner->createProgramPartner($partnerName, $partnerIndustry, $partnerFullName, $photo);

        if($ProgramPartner)
        {
            $this->response($ProgramPartner, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function programpartner_put()
    {
        $partnerID= $this->post('partnerID');
        $partnerName= $this->post('partnerName');
        $partnerIndustry = $this->post('partnerIndustry');
        $partnerFullName = $this->post('partnerFullName');
        $photo= $this->post('photo');

        if(!$partnerID)
        {
            $this->response(NULL, 400);
        }

        $ProgramPartner = $this->Mprogrampartner->updateProgramPartner($partnerID, $partnerName, $partnerIndustry, $partnerFullName, $photo);

        if($ProgramPartner)
        {
            $this->response($ProgramPartner, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function programpartner_delete()
    {
        $partnerID = $this->delete('partnerID');
        // $this->response($id, 200);
        if(!$partnerID)
        {
            $this->response(NULL, 400);
        }

        $ProgramPartner = $this->Mprogrampartner->deleteProgramPartner($partnerID);

        if($ProgramPartner)
        {
            $this->response($ProgramPartner, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be delete'), 404);
        }
    }

}