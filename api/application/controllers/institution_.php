<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * Institution API
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Furqon Ramdhani
 * @link		http://furqonramdhani.com
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Institution extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Minstitution');

    }

    function institutions_get()
    {
        $institutions = $this->Minstitution->readInstitutions();
        if($institutions)
        {
            $this->response($institutions, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any institutions!'), 404);
        }
    }


    function institution_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $institution = $this->Minstitution->readInstitution($this->get('id'));

        if($institution)
        {
            $this->response($institution, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }


    function institution_post()
    {
        $name = $this->post('institutionName');

        if(!$name)
        {
            $this->response(NULL, 400);
        }

        $institution = $this->Minstitution->createInstitution($name);

        if($institution)
        {
            $this->response($institution, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function institution_put()
    {
        $id = $this->put('institutionId');
        $name = $this->put('institutionName');

        if(!$id)
        {
            $this->response(NULL, 400);
        }

        $institution = $this->Minstitution->updateInstitution($id, $name);

        if($institution)
        {
            $this->response($institution, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function institution_delete()
    {
        $id = $this->delete('institutionId');
        // $this->response($id, 200);
        if(!$id)
        {
            $this->response(NULL, 400);
        }

        $institution = $this->Minstitution->deleteInstitution($id);

        if($institution)
        {
            $this->response($institution, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be delete'), 404);
        }
    }

}