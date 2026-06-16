<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('email/memail');
    }

    //email
    function emaillogs_get() {
        $emails = $this->memail->readEmailLogs($this->get('key'), $this->get('date'), $this->get('start'), $this->get('limit'));
        if ($emails)
            $this->response($emails, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any emails!'), 404);
    }

    function emaillog_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $email = $this->memail->readEmailLog($this->get('id'));
        if ($email)
            $this->response($email, 200);
        else
            $this->response(array('error' => 'Email could not be found'), 404);
    }

    function emaillog_post() {
        if (!$this->post('EmailSubject'))
            $this->response(NULL, 400);

        $email_to = '';
        foreach ($this->post('EmailTo') as $key => $val) {
            $email_to .= $val;
            if ($key + 1 < count($this->post('EmailTo'))) {
                $email_to .= ', ';
            }
        }
        $user_email = $this->memail->getEmailByUserId($_SESSION['userid']);

        $email = $this->memail->createEmailLog($this->post('EmailSubject'), $email_to, $user_email, $this->post('EmailBody'), $_SESSION['userid']);

        if ($email) {
            $this->load->library('email');
            foreach ($this->post('EmailTo') as $val) {
                if (!filter_var($val, FILTER_VALIDATE_EMAIL) === true) {
                    continue;
                }
                $this->email->initialize($this->config->load('email'));
                $send = $this->email
                        ->from('support@koltiva.com')
                        ->to($val)
                        ->subject($this->post('EmailSubject'))
                        ->message($this->post('EmailBody'))
                        ->send();
            }

            $this->response($email, 200);
        } else {
            $this->response(array('error' => 'Email could not be found'), 404);
        }
    }

    function emaillog_put() {
        if (!$this->put('EmailSubject'))
            $this->response(NULL, 400);
        $user_email = $this->memail->getEmailByUserId($_SESSION['userid']);
        $update = $this->memail->updateEmailLog($this->put('EmailSubject'), $this->put('EmailTo'), $user_email, $this->put('EmailBody'), $this->put('id'), $_SESSION['userid']);
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function comboemail_get() {
        $data = $this->memail->getComboEmail();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any email!'), 404);
    }

    Function insertEmailLog($params) {
        $email = $this->memail->createEmailLog($subject, $to, $from, $body, $status, $desc, $userid);
        if ($email)
            $this->response($email, 200);
        else
            $this->response(array('error' => 'Email could not be found'), 404);
    }

}
