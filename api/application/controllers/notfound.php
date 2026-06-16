<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @Author: nikolius
 * @Date:   2017-03-07 17:44:36
 */
class Notfound extends CI_Controller {
    public function index()
    {
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>';
        exit;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */