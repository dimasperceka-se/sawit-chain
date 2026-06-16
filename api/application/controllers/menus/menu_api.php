<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Menu_api extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('system/msys_menu');
    }


    public function list_menu_get()
    {
        $data  = $this->msys_menu->readMenus($this->get());
        $this->response($data, 200);
    }

    public function ShowParentMenu_get()
    {
        $data  = $this->msys_menu->getMenuParent();
        $this->response($data, 200);
    }

    public function add_menu_post()
    {
        $data = $this->msys_menu->createMenu($this->post());
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        
    }

    function show_menuById_get() {
        if(!$this->get('MenuId')) $this->response(NULL, 400);
        $rowMenu = $this->msys_menu->getMenuById($this->get('MenuId'));
        if($rowMenu) $this->response($rowMenu, 200);
        else $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

    function delete_menu_delete() {
        if(!$this->delete('MenuId')) $this->response(NULL, 400);
        $deleteMenu = $this->msys_menu->deleteMenu($this->delete('MenuId'));
        if($deleteMenu) $this->response($deleteMenu, 200);
        else $this->response(array('error' => 'Menu could not be delete'), 404);
    }


    
}