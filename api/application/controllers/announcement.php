<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announcement extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mannouncement');
    }

    public function list_get()
    {
        $data = $this->mannouncement->getAnnoucements();
        $this->response($data, 200);
    }

}

/* End of file announcement.php */
/* Location: ./application/controllers/announcement.php */