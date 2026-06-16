<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * Staff API
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Furqon Ramdhani
 * @link		http://furqonramdhani.com
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Staff extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mstaff');

    }

    function programstaffs_get()
    {
        $programstaffs = $this->Mstaff->readProgramStaffs();
        if($programstaffs)
        {
            $this->response($programstaffs, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any staff!'), 404);
        }
    }


    function programstaff_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $programstaff = $this->Mstaff->readProgramStaff($this->get('id'));

        if($staff)
        {
            $this->response($programstaff, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Staff could not be found'), 404);
        }
    }


    function programstaff_post()
    {
        $partnerID      = $this->post('partnerID');
        $staffName      = $this->post('staffName');
        $regionalID     = $this->post('regionalID');
        $birthdate      = $this->post('birthdate');
        $birthplace     = $this->post('birthplace');
        $address        = $this->post('address');
        $handphone      = $this->post('handphone');
        $email          = $this->post('email');
        $gender         = $this->post('gender');
        $maritalStatus  = $this->post('maritalStatus');
        $education      = $this->post('education');
        $photo          = $this->post('photo');

        if(!$staffName)
        {
            $this->response(NULL, 400);
        }

        $programstaff = $this->Mstaff->createProgramStaff($partnerID, $staffName, $regionalID, $birthdate, $birthplace, $address, $handphone, $email, $gender, $maritalStatus, $education, $photo);

        if($programstaff)
        {
            $this->response($programstaff, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Staff could not be create'), 404);
        }
    }

    function programstaff_put()
    {
        $staffID        = $this->put('staffID');
        $personID       = $this->put('personID');
        $partnerID      = $this->put('partnerID');
        $staffName      = $this->put('staffName');
        $regionalID     = $this->put('regionalID');
        $birthdate      = $this->put('birthdate');
        $birthplace     = $this->put('birthplace');
        $address        = $this->put('address');
        $handphone      = $this->put('handphone');
        $email          = $this->put('email');
        $gender         = $this->put('gender');
        $maritalStatus  = $this->put('maritalStatus');
        $education      = $this->put('education');
        $photo          = $this->put('photo');


        if(!$staffID)
        {
            $this->response(NULL, 400);
        }

        $programstaff = $this->Mstaff->updateProgramStaff($staffID,$personID, $partnerID, $staffName, $regionalID, $birthdate, $birthplace, $address, $handphone, $email, $gender, $maritalStatus, $education, $photo);

        if($programstaff)
        {
            $this->response($programstaff, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Staff could not be update'), 404);
        }
    }

    function programstaff_delete()
    {
        $staffID = $this->delete('staffID');
        $personID = $this->delete('staffID');

        // $this->response($id, 200);
        if(!$staffID)
        {
            $this->response(NULL, 400);
        }

        $programstaff = $this->Mstaff->deleteProgramStaff($staffID, $personID);

        if($programstaff)
        {
            $this->response($programstaff, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Staff could not be delete'), 404);
        }
    }

}