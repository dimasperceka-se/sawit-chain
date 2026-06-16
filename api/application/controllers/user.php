<?php defined('BASEPATH') OR exit('No direct script access allowed');

// require APPPATH.'/libraries/REST_Controller.php';

class User extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('muser');
    }

    function data_get() {
        $data = $this->muser->readData();
        if($data) $this->response($data, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function data_post() {
        if(!$this->post('UserName')) $this->response(NULL, 400);
        $add = $this->muser->createData($this->post('UserName'), $this->post('UserRealName'),$this->post('UserActive'));
        if($add) $this->response($add, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_put() {
        if(!$this->put('UserId')) $this->response(NULL, 400);
        $update = $this->muser->updateData($this->post('UserName'), $this->post('UserRealName'),$this->post('UserActive'),
            $this->post('UserUnitId'));
        if($update) $this->response($update, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be found'), 404);
    }

    function unit_delete() {
        if(!$this->deleteData('UserUnitId')) $this->response(NULL, 400);
        $delete = $this->muser->deleteUser($this->deleteData('UserUnitId'));
        if($unit) $this->response($delete, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Unit could not be delete'), 404);
    }

    function group_get() {
        $data = $this->muser->readUnit();
        if($data) $this->response($data, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    public function myprofile_get()
    {
        $this->response($this->muser->getProfile($_SESSION['username']), 200);
    }

    public function myprofile_post()
    {
        $data = $this->post();

        $result = $this->muser->updateProfile($data, $_SESSION['username']);

        $this->response($result, 200);
    }

    public function mypassword_post()
    {
        $this->load->model('staff/mstaffuser_cognito');
        $username = $_SESSION['username'];
        $oldpassword            = $this->post('oldpassword');
        $newpassword            = $this->post('newpassword');
        $newpassword_confirm    = $this->post('newpassword_confirm');

        //pattern password check
        if (preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,100}$/", $newpassword)) {
            $passPattern = true;
        }else{
            $passPattern = false;
        }

        // check old password
        if ($newpassword != $newpassword_confirm) {
            $result = lang('New password and password confirmation doesn\'t match!');
        } elseif ($passPattern == false) {
            $result = lang('New password doesn\'t fit password criteria!');
        } else {

            // update password, cek dl password lamanya bener kaga
            $cekOldPass = $this->mstaffuser_cognito->checkPassword($username,$oldpassword);
            if($cekOldPass == false) {
                $result = lang('Wrong old password');
                $this->response($result, 200);
            }

            $result = $this->mstaffuser_cognito->UserUpdatePassword($username,$newpassword);
            if($result == false) {
                $result = lang('Change password failed!');

                //write log
                $this->muser->writeLogAccess('Change Password',$_SESSION['userid'],'Failed');
            }else{
                //write log
                $this->muser->writeLogAccess('Change Password',$_SESSION['userid'],'Success');
                $this->muser->writeLogChangePass($_SESSION['userId'],md5($newpassword));
                $this->response(true, 200);
            }
        }

        $this->response($result, 200);

        //=======================================================================
    }
}
