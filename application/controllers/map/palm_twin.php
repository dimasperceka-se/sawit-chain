<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Palm Oil Digital Twin — embeds the standalone geospatial app (separate Node
 * service, see delete-soon/palm-twin) inside the Maps menu via an iframe.
 */
class Palm_twin extends SS_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = array();
        $data['title']    = 'Palm Oil Digital Twin';
        // URL of the standalone palm-twin app. Override with PALM_TWIN_URL env.
        $data['twin_url'] = getenv('PALM_TWIN_URL') ?: 'http://localhost:5003';
        $this->LoadView($data, 'map/palm_twin');
    }
}
