<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Broadcast extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('cms/mbroadcast', '_model');
    }

    public function data_get() {
        $data = $this->_model->readBroadcasts($this->get('key'), $_SESSION['userid'], $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }

    function combo_partner_get() {
        $data = $this->_model->ComboPartner();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any first buyer!'), 404);
        }
    }

    public function data_post() {
        //echo "<pre>".print_r($this->post(),1);die;
        $data = $this->_model->createBroadcast($this->post('PartnerID'), $this->post('Message'), $_SESSION['userid']);
        
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function data_put() {
        $data = $this->_model->updateBroadcast($this->put('NotifID'), $this->put('PartnerID'), $this->put('Message'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    function data_delete() {
        if (!$this->delete('NotifID'))
            $this->response(NULL, 400);
        $data = $this->_model->deleteBroadcast($_SESSION['userid'], $this->delete('NotifID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    public function detail_get() {
        $data = $this->_model->readBroadcastDetail($this->get('NotifID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function files_get() {
        $data = $this->_model->readFiles($this->get('IssuesID'), $this->get('FileBundle'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array(), 200);
    }

    public function upload_post() {
        $data = $this->_model->uploadFiles($this->post('IssuesID'), $this->post('FileBundle'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    

    public function issues_post() {
        $userid = $_SESSION['userid'];
        $data = $this->_model->getIssues($this->post('IssuesID'));
        if ($data['all']->num_rows() > 0) {
            $view = '<div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <span class="title"><b>No. ' . $data['detail']['IssuesID'] . '</b> | <b>' . $data['detail']['Subject'] . '</b> <span class="badge" style="color: #FFFFFF">' . $data['detail']['IssuesStatus'] . '</span></span>                  
                                <span class="badge" style="color: #FFFFFF">' . $data['detail']['IssuesType'] . '</span>
                                <span class="badge" style="color: #FFFFFF">' . $data['detail']['IssuesPriority'] . '</span>
                            </div>
                            <div class="panel-body"> 
                                <button class="btn btn-alt4" onclick="BackPanel()">Back</button> 
                                <button class="btn btn-alt4" onclick="ReplayPanel()">Replay</button>';
            if ($data['detail']['IssuesStatus'] != 'Close' && $data['close']==1) {
                $view .= '<button class="btn btn-alt4 pull-right" onclick="ClosePanel()">Close Issued</button>';
            }

            $view .= '<br><br>';

            $creator = "";
            $i = 0;
            foreach ($data['all']->result() as $row) {
                if ($creator == '') {
                    $creator = $row->CreatedBy;
                }
                if ($creator == $row->CreatedBy) {
                    $class = "warning";
                    $icon = "s7-attention";
                } else {
                    $class = "info";
                    $icon = "s7-info";
                }
                $view .= '<div role="alert" class="alert alert-' . $class . ' alert-icon alert-border-color alert-dismissible">';
                $view .= '<div class="icon"><span class="' . $icon . '"></span></div>';
                $view .= '<div class="message">';
                /* if($i==0){
                  //$view .= '<strong><h4>'.$row->Subject.'</strong></h4>';
                  $view .= '<h3><i><b>'.$row->Creator.'</b></i></h3> <h7 class="pull-right" style="margin-top: -20px;"><i><b>'.substr($row->DateCreated,0,16).'</i></h7>';
                  }else{
                  $view .= '<h7><i>Updated by <b>'.$row->Creator.'</b> on '.substr($row->DateCreated,0,16).'</i></h7>';
                  } */
                $view .= '<h5 class="pull-left"><i><b>' . $row->Creator . '</b></i></h5> &nbsp; <span class="badge">' . $row->PositionName . '</span> <h7 class="pull-right"><i><b>' . date('d/m/Y H:i', strtotime(substr($row->DateCreated, 0, 16))) . '</i></h7>';
                $view .= '<hr>';
                $view .= $row->Description;
                if ($row->Files > 0) {

                    $files = $this->_model->getFiles($row->IssuesID);
                    $view .= '<hr>Files : <br>';
                    $view .= '<ul class="">';
                    foreach ($files->result() as $file) {
                        $view .= '<li><a href="javascript:void(0)" onclick="Download(\'' . $file->IssuesID . '_' . $file->FilePath . '\')">' . $file->FileName . '</a></li>';
                    }
                    $view .= '</ul>';
                }
                if ($userid == $row->CreatedBy) {
                    $view .= '<button class="btn btn-sm btn-primary s-red pull-right btn_edits" onclick="DeleteIssue(' . $row->IssuesID . ')">Delete</button>';
                    $view .= '<button class="btn btn-sm btn-success pull-right btn_edits" onclick="UpdateIssue(' . $row->IssuesID . ')" style="margin-right:10px;">Edit</button>';
                }
                $view .= '</div>';
                $view .= '</div>';
                $i++;
            }
            $view .= '</div></div></div>';
        } else {
            $view = '<div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="title"> - </span></div>
                            <div class="panel-body">
                                <div role="alert" class="alert alert-primary alert-icon alert-border-color alert-dismissible">
                                    <div class="icon"><span class="s7-close-circle"></span></div>
                                    <div class="message">
                                        </span></button><strong>No data found!</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
        }
        echo $view;
        exit;
    }

    

    

    

    function download_post() {
        if (!$this->post('File'))
            $this->response(NULL, 400);
        $data = $this->_model->checkDownload($_SESSION['userid'], $this->post('File'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    function file_delete() {
        if (!$this->delete('FileID'))
            $this->response(NULL, 400);
        $data = $this->_model->deleteFile($_SESSION['userid'], $this->delete('FileID'), $this->delete('FilePath'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    function close_post() {
        if (!$this->post('IssuesID'))
            $this->response(NULL, 400);
        $data = $this->_model->closeIssue($_SESSION['userid'], $this->post('IssuesID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    function issues_type_get() {
        $data = $this->_model->listIssuesType();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any first buyer!'), 404);
        }
    }

    function issues_priority_get() {
        $data = $this->_model->listIssuesPriority();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any first buyer!'), 404);
        }
    }

}
