<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * Region API
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Furqon Ramdhani
 * @link		http://furqonramdhani.com
 */


class Region extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mregion');

    }

    function regionals_get()
    {

        //$users = $this->some_model->getSomething( $this->get('limit') );
        $regionals = $this->Mregion->getRegionals();

        if($regionals)
        {
            $this->response($regionals, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any provinces!'), 404);
        }
    }

    function provinces_get()
    {

        //$users = $this->some_model->getSomething( $this->get('limit') );
        $provinces = $this->Mregion->getAllProvince();

        if($provinces)
        {
            $this->response($provinces, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any provinces!'), 404);
        }
    }

    function province_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $province = $this->Mregion->getProvince($this->get('id'));

        if($province)
        {
            $this->response($province, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Province could not be found'), 404);
        }
    }

    function district_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $district = $this->Mregion->getDistrict($this->get('id'));

        if($district)
        {
            $this->response($district, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Province could not be found'), 404);
        }
    }

    function subdistrict_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $subdistrict = $this->Mregion->getSubDistrict($this->get('id'));

        if($subdistrict)
        {
            $this->response($subdistrict, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'SubDistrict could not be found'), 404);
        }
    }

    function village_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $village= $this->Mregion->getVillage($this->get('id'));

        if($village)
        {
            $this->response($village, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Village could not be found'), 404);
        }
    }


}