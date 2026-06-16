<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * FarmerGroup API
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Furqon Ramdhani
 * @link		http://furqonramdhani.com
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class FarmerGroup extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mfarmergroup');

    }

    function farmergroup_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $farmer = $this->Mfarmer->getFarmer($this->get('id'));

        if($farmer)
        {
            $this->response($farmer, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Farmer could not be found'), 404);
        }
    }

    function farmergroup_post()
    {
        //$this->some_model->updateUser( $this->get('id') );
        $message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');

        $this->response($message, 200); // 200 being the HTTP response code
    }

    function farmergroup_delete()
    {
        //$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');

        $this->response($message, 200); // 200 being the HTTP response code
    }

    function farmergroups_get()
    {

        //$users = $this->some_model->getSomething( $this->get('limit') );
        $farmers = $this->Mfarmer->getAllFarmer();

        if($farmers)
        {
            $this->response($farmers, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any farmers!'), 404);
        }
    }


    public function farmer_post()
    {
        // var_dump($this->request->body);
        //generate farmer ID

        //save farmer ID , CPG ID (Group ID)


        if($this->post('id') && $$this->post(''))
        {
            $this->response($this->post('id'), 200);
        }
    }


    public function send_put()
    {
        var_dump($this->put('foo'));
    }
}