<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdocument');
    }

    public function list_get()
    {
        $data = $this->mdocument->getDocuments();
        $this->response($data);
    }

}

/* End of file announcement.php */
/* Location: ./application/controllers/announcement.php */