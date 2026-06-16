<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Files extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('files/mdocuments');
        $this->load->model('files/mvideo');
        $this->load->model('files/mstory');
        $this->load->model('files/mphoto');
    }

    function documentss_get() {
        $data = $this->mdocuments->readDocuments($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function documents_post() {
        move_upload($this->file, 'files/documents/' . $this->file['file']['name']);
        // move_uploaded_file($this->file['file']['tmp_name'], 'files/documents/' . $this->file['file']['name']);
        $data = $this->mdocuments->createDocuments($this->post('label'), $this->file['file']['name'], $this->file['file']['type'], $this->file['file']['size'], $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    function documents_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        unlink('files/documents/' . $this->delete('name'));
        $data = $this->mdocuments->deleteDocuments($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be delete'), 404);
    }

    function videos_get() {
        $data = $this->mvideo->readVideo($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function video_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $data = $this->mvideo->readDetailVideo($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Video could not be found'), 404);
    }

    function video_post() {
        if ($this->post('id')) {
            if (!$this->post('id'))
                $this->response(NULL, 400);

            if ($this->file['file']['tmp_name']) {
                if (file_exists('files/video/' . $this->post('current_file'))) {
                    unlink('files/video/' . $this->post('current_file'));
                }

                //upload video (begin)
                //hanya boleh mp4
                switch ($this->file['file']['type']) {
                    case 'video/mp4':
                        $validFileUpload = true;
                        break;
                    default:
                        $validFileUpload = false;
                        break;
                }

                if ($validFileUpload == true) {
                    move_uploaded_file($this->file['file']['tmp_name'], 'files/video/' . $this->file['file']['name']);
                } else {
                    $this->response('Invalid File Type', 400);
                }

                //move_upload($this->file, 'files/video/' . $this->file['file']['name']);
                // move_uploaded_file($this->file['file']['tmp_name'], 'files/video/' . $this->file['file']['name']);
                //upload video (end)
                //move_upload($this->file, 'files/video/' . $this->file['file']['name']);
                // move_uploaded_file($this->file['file']['tmp_name'], 'files/video/' . $this->file['file']['name']);
            } else {
                $this->file['file']['name'] = $this->post('current_file');
                $this->file['file']['size'] = $this->post('current_file_size');
            }

            if ($this->file['thumb']['tmp_name']) {
                if (file_exists('files/video/' . $this->post('current_thumbnail'))) {
                    unlink('files/video/' . $this->post('current_thumbnail'));
                }

                //upload video (begin)
                //hanya boleh image
                switch ($this->file['thumb']['type']) {
                    case 'image/jpeg':
                    case 'image/jpg':
                    case 'image/gif':
                    case 'image/png':
                        $validFileUpload = true;
                        break;
                    default:
                        $validFileUpload = false;
                        break;
                }

                if ($validFileUpload == true) {
                    move_uploaded_file($this->file['thumb']['tmp_name'], 'files/video/' . $this->file['thumb']['name']);
                } else {
                    $this->response('Invalid File Type', 400);
                }
                //move_upload($this->file, 'files/video/' . $this->file['thumb']['name']);
                // move_uploaded_file($this->file['thumb']['tmp_name'], 'files/video/' . $this->file['thumb']['name']);
                //upload video (end)
                //move_upload($this->file, 'files/video/' . $this->file['thumb']['name']);
                // move_uploaded_file($this->file['thumb']['tmp_name'], 'files/video/' . $this->file['thumb']['name']);
            } else {

                $this->file['thumb']['name'] = $this->post('current_thumbnail');
            }
            $result = $this->mvideo->updateVideo($this->post('id'), $this->file['file']['name'], $this->post('title'), $this->post('desc'), $this->file['thumb']['name'], $this->file['file']['size'], $_SESSION['userid']);

            if ($result)
                $this->response($result, 200);
            else
                $this->response(array('error' => 'Video could not be updated'), 404);
        } else {
            //upload video (begin)
            //hanya boleh mp4
            switch ($this->file['file']['type']) {
                case 'video/mp4':
                    $validFileUpload = true;
                    break;
                default:
                    $validFileUpload = false;
                    break;
            }

            if ($validFileUpload == true) {
                move_uploaded_file($this->file['file']['tmp_name'], 'files/video/' . $this->file['file']['name']);
            } else {
                $this->response('Invalid File Type', 400);
            }

            //move_upload($this->file, 'files/video/' . $this->file['file']['name']);
            // move_uploaded_file($this->file['file']['tmp_name'], 'files/video/' . $this->file['file']['name']);
            //upload video (end)
            //upload video (begin)
            //hanya boleh image
            switch ($this->file['thumb']['type']) {
                case 'image/jpeg':
                case 'image/jpg':
                case 'image/gif':
                case 'image/png':
                    $validFileUpload = true;
                    break;
                default:
                    $validFileUpload = false;
                    break;
            }

            if ($validFileUpload == true) {
                move_uploaded_file($this->file['thumb']['tmp_name'], 'files/video/' . $this->file['thumb']['name']);
            } else {
                $this->response('Invalid File Type', 400);
            }
            //move_upload($this->file, 'files/video/' . $this->file['thumb']['name']);
            // move_uploaded_file($this->file['thumb']['tmp_name'], 'files/video/' . $this->file['thumb']['name']);
            //upload video (end)

            $data = $this->mvideo->createVideo($this->file['file']['name'], $this->post('title'), $this->post('desc'), $this->file['thumb']['name'], $this->file['file']['size'], $_SESSION['userid']);
            if ($data)
                $this->response($data, 200);
            else
                $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

//    function video_put() {
//        if (!$this->put('id'))
//            $this->response(NULL, 400);
//        if ($this->file['file']['tmp_name']) {
//            if (file_exists($this->put('current_file'))) {
//                unlink('files/video/' . $this->put('current_file'));
//            }
//            move_uploaded_file($this->file['file']['tmp_name'], 'files/video/' . $this->file['file']['name']);
//        } else {
//            $this->file['file']['name'] = $this->put('current_file');
//            $this->file['file']['size'] = $this->put('current_file_size');
//        }
//        if ($this->file['file']['thumb']) {
//            if (file_exists($this->put('current_thumbnail'))) {
//                unlink('files/video/' . $this->put('current_thumbnail'));
//            }
//            move_uploaded_file($this->file['thumb']['tmp_name'], 'files/video/' . $this->file['thumb']['name']);
//        } else {
//
//            $this->file['thumb']['name'] = $this->put('current_thumbnail');
//        }
//        $result = $this->mvideo->updateVideo($this->put('id'), $this->file['file']['name'], $this->put('title'), $this->put('desc'), $this->file['thumb']['name'], $this->file['file']['size'], $_SESSION['userid']);
//        if ($result)
//            $this->response($result, 200);
//        else
//            $this->response(array('error' => 'Video could not be updated'), 404);
//    }

    function video_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        unlink('files/video/' . $this->delete('name'));
        unlink('files/video/' . $this->delete('thumb'));
        $data = $this->mvideo->deleteVideo($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be delete'), 404);
    }

    function playvideo_get() {
        $data['data'] = $this->get('vid');
        $this->load->view('video', $data);
    }

    function storys_get() {
        $data = $this->mstory->readStory($this->get('key'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function story_search_get() {
        $data = $this->mstory->readStorySearch($this->get('query'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function story_post() {
        move_upload($this->file, 'files/story/' . $this->file['file']['name']);
        // move_uploaded_file($this->file['file']['tmp_name'], 'files/story/' . $this->file['file']['name']);
        if ($this->post('StoryID') == '')
            $data = $this->mstory->createStory($this->post('FarmerID'), $this->file['file']['name'], $_SESSION['userid']);
        else
            $data = $this->mstory->updateStory($this->post('StoryID'), $this->file['file']['name'], $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    function story_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        //@unlink('files/story/'.$this->delete('name'));
        $data = $this->mstory->deleteStory($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be delete'), 404);
    }

    public function download_photo_get() {
        $partner = 3; //Unilever
        $data = $this->mphoto->downloadPhoto($partner);

        $path = 'images/member/';
        $name_zip = 'photo' . date('YmdHis');
        $path_zip = 'images/zip/member/';

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($path_zip . $name_zip . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($data) {
            foreach ($data as $key => $val) {
                if (file_exists($path . $val['Path'])) {
                    $zip->addFile($path . $val['Path'], $val['Photo']);
                }
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function download_consent_get() {
        $partner = 3; //Unilever
        $data = $this->mphoto->downloadConsent($partner);

        $path = 'images/consent/';
        $name_zip = 'consent' . date('YmdHis');
        $path_zip = 'images/zip/consent/';

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($path_zip . $name_zip . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($data) {
            foreach ($data as $key => $val) {
                if (file_exists($path . $val['Path'])) {
                    $zip->addFile($path . $val['Path'], $val['Photo']);
                }
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function download_receipt_get() {
        $partner = 3; //Unilever
        $data = $this->mphoto->downloadReceipt($partner);

        $path = 'images/main_buyer_last_receipt/';
        $name_zip = 'receipt' . date('YmdHis');
        $path_zip = 'images/zip/receipt/';

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($path_zip . $name_zip . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($data) {
            foreach ($data as $key => $val) {
                if (file_exists($path . $val['Path'])) {
                    $zip->addFile($path . $val['Path'], $val['Photo']);
                }
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function download_plantation_photo_get() {
        $partner = 3; //Unilever
        $data = $this->mphoto->downloadPlantation($partner);

        $path = 'images/plot_visit/';
        $name_zip = 'plot' . date('YmdHis');
        $path_zip = 'images/zip/plot/';

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($path_zip . $name_zip . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($data) {
            foreach ($data as $key => $val) {
                if (file_exists($path . $val['Path'])) {
                    $zip->addFile($path . $val['Path'], $val['Photo']);
                }
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

}
