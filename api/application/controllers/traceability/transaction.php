<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('traceability/mtransaction_new');
    }

    function supplyid_get() {
        $key = $this->get('query');
        $tipe = $this->get('tipe');
        $start = $this->get('start');
        $limit = $this->get('limit');
        $data = $this->mtransaction_new->readSupplyIDList($key, $tipe, $start, $limit);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
    }

}
