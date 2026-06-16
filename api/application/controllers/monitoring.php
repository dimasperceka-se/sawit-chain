<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class monitoring extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('monitoring/mcategory');
        $this->load->model('monitoring/mactivity');        
        $this->load->model('monitoring/mfoto');
        $this->load->model('monitoring/mgallery');        
    }   
    // =============================================================================================================
    // category --- > mcategory
    // =============================================================================================================
        function categoryid_get() { //var_dump($this->get('categoryID'));die;
            
            $categoryID = $this->get('categoryID');
            $typeID     = $this->get('typeID');

            if ($categoryID =='Farmer'){
                $data = $this->mcategory->cat_Farmers($this->get('query'), $this->get('start'), $this->get('limit'));
            }elseif ($categoryID =='Garden'){
                $data = $this->mcategory->cat_Gardens($this->get('query'), $this->get('start'), $this->get('limit'));
            }elseif ($categoryID =='CPG'){
                $data = $this->mcategory->cat_CPG($this->get('query'), $this->get('start'), $this->get('limit'));
            }elseif ($categoryID =='Nursery'){
                if ($typeID == 'Farmer'){
                    $data = $this->mcategory->cat_Farmers($this->get('query'), $this->get('start'), $this->get('limit'));
                }elseif ($typeID =='CPG'){
                    $data = $this->mcategory->cat_CPG($this->get('query'), $this->get('start'), $this->get('limit'));
                }elseif ($typeID =='Coop'){
                    $data = $this->mcategory->cat_Coop($this->get('query'), $this->get('start'), $this->get('limit'));
                }else{
                    $this->response(array('error' => 'Couldn\'t find any data !'), 404);
                }
            }elseif ($categoryID =='Compost'){
                if ($typeID == 'Farmer'){
                    $data = $this->mcategory->cat_Farmers($this->get('query'), $this->get('start'), $this->get('limit'));
                }elseif ($typeID =='CPG'){
                    $data = $this->mcategory->cat_CPG($this->get('query'), $this->get('start'), $this->get('limit'));
                }elseif ($typeID =='Coop'){
                    $data = $this->mcategory->cat_Coop($this->get('query'), $this->get('start'), $this->get('limit'));
                }else{
                    $this->response(array('error' => 'Couldn\'t find any data !'), 404);
                }
            }elseif ($categoryID =='Demoplot'){
                $data = $this->mcategory->cat_Demoplot($this->get('query'), $this->get('start'), $this->get('limit'));
            }elseif ($categoryID =='Coop'){
                $data = $this->mcategory->cat_Coop($this->get('query'), $this->get('start'), $this->get('limit'));
            }elseif ($categoryID =='Warehouse'){
                $data = $this->mcategory->cat_Warehouse($this->get('query'), $this->get('start'), $this->get('limit'));
            }elseif ($categoryID =='Trader'){
                $data = $this->mcategory->cat_Trader($this->get('query'), $this->get('start'), $this->get('limit'));
            }elseif ($categoryID =='SCE'){
                $data = $this->mcategory->cat_SCE($this->get('query'), $this->get('start'), $this->get('limit'));
            }elseif ($categoryID =='Village'){
                $data = $this->mcategory->cat_Village($this->get('query'), $this->get('start'), $this->get('limit'));
            }

            if ($data) $this->response($data, 200);
            else $this->response(array('error' => 'Couldn\'t find any data !'), 404);
        }

    // =============================================================================================================
    // activity --- > mactivity
    // =============================================================================================================
        // GRID
        function activity_grid_get() { 

            if ($this->get('des') =='') {  
                if ($this->get('category_name') =='') {
                    $activity = $this->mactivity->grid_Activity($this->get('start'), $this->get('limit'));     
                } elseif ($this->get('category_name') =='All') {
                     $activity = $this->mactivity->grid_Activity($this->get('start'), $this->get('limit'));     
                } else {
                     $activity = $this->mactivity->filter_Activity($this->get('category_name'), $this->get('start'), $this->get('limit')); 
                } 
            }else {    
                if ($this->get('category_name') =='') {
                    $activity = $this->mactivity->search_Activity($this->get('des'),$this->get('start'), $this->get('limit')); 
                } elseif ($this->get('category_name') =='All') {
                    $activity = $this->mactivity->search_Activity($this->get('des'),$this->get('start'), $this->get('limit')); 
                } else {
                     $activity = $this->mactivity->search_Activitys($this->get('des'),$this->get('category_name'),$this->get('start'), $this->get('limit')); 
                }                
            }           
            if ($activity)
                $this->response($activity, 200);
            else
                $this->response(array('error' => 'Couldn\'t find any Activity!'), 404);
        }
        // GRID
        function activity_grid2_get() { 
                     
            if ($this->get('des') =='') {               
                $activity = $this->mactivity->grid_Activity($this->get('start'), $this->get('limit'));
            }else {                
                $activity = $this->mactivity->search_Activity($this->get('des'),$this->get('start'), $this->get('limit')); 
            } 

            if ($activity)
                $this->response($activity, 200);
            else
                $this->response(array('error' => 'Couldn\'t find any Activity!'), 404);
        }

        // =============================================================================================================
        // Activity CRUD
        // =============================================================================================================
            // CREATE
            function activity_post() {               

                if ($this->post('id') == '') {         
                    $activity = $this->mactivity->create_Activity($this->post('ObjectCategory'), $this->post('ObjectType'), $this->post('ObjectID'), $this->post('ObjectName'), $this->post('VillageID'), $this->post('Description'),$this->post('VisitDate'),$this->post('VisitTime'), $_SESSION['userid']);        
                }else {
                   $activity = $this->mactivity->update_Activity($this->post('id'),$this->post('ObjectCategory'), $this->post('ObjectType'), $this->post('ObjectID'), $this->post('ObjectName'), $this->post('VillageID'), $this->post('Description'),$this->post('VisitDate'),$this->post('VisitTime'), $_SESSION['userid']);        
                }   

                if ($activity)
                    $this->response($activity, 200);
                else
                    $this->response(array('error' => 'Activity could not be created/updated'), 404);
            }
            // READ
            function activity_get() {
                if (!$this->get('id'))
                     $this->response(NULL, 400);
                $activity = $this->mactivity->read_Activity($this->get('id'));
                if ($activity)
                    $this->response($activity, 200);
                else
                    $this->response(array('error' => 'Activity could not be found'), 404);
            }
            // UPDATE
            function activity_put() {
                if (!$this->put('id'))
                    $this->response(NULL, 400);
                    $activity = $this->mactivity->update_Activity($this->put('id'),$this->put('ObjectCategory'), $this->put('ObjectType'), $this->put('ObjectID'),$this->put('Description'),$this->put('VisitDate'),$this->put('VisitTime'), $_SESSION['userid']);        
                if ($activity)
                    $this->response($activity, 200);
                else
                    $this->response(array('error' => 'Activity could not be updated'), 404);
            }
            // DELETE
            function activity_delete() {
                if (!$this->delete('MonitoringID'))
                     $this->response(NULL, 400);
                $activity = $this->mactivity->delete_Activity($this->delete('MonitoringID'));
                if ($activity)
                    $this->response($activity, 200);
                else
                    $this->response(array('error' => 'Activity could not be deleted'), 404);
            }
    
    // =============================================================================================================
    // foto ---> mfoto
    // =============================================================================================================
        // GRID
        function foto_grid_get() { //var_dump($this->get('categoryID'));die;
            $satu = $this->get('mid'); 
            $foto = $this->mfoto->grid_foto($satu,$this->get('start'), $this->get('limit'));
            if ($foto)
                $this->response($foto, 200);
            else
                $this->response(array('error' => 'Couldn\'t find any Photo!'), 404);
        } 
        // GALLERY
        function foto_gallery_get() { //var_dump($this->get('categoryID'));die;             
            $foto = $this->mfoto->gallery_foto($this->get('start'), $this->get('limit'));
            if ($foto)
                $this->response($foto, 200);
            else
                $this->response(array('error' => 'Couldn\'t find any Photo!'), 404);
        } 
        // =============================================================================================================
        // Foto CRUD
        // =============================================================================================================             
            // CREATE
            function foto_post (){ 
                $allowedExts = array(
                    "gif","GIF",
                    "jpeg","JPEG",
                    "jpg","JPG",
                    "png","PNG",
                    "bmp","BMP",
                );
                $temp        = explode(".", $_FILES["foto"]["name"]);
                $extension   = end($temp);

                $infoString = "";
                
                $tmp_name = $_FILES["foto"]["tmp_name"]; 
                $name     = date('Ymdhis').'_'.$_FILES["foto"]["name"];                                
                $type     = $_FILES["foto"]["type"];
                $size     = $_FILES["foto"]["size"];
                $satu     = $this->post('id');
                $title    = $this->post('title');
                $path     = 'images/photo_activity';

                if($_FILES["foto"]["type"] == ""){
                    die("{'success': false, 'error': 'You have not uploaded Photo !'}");
                }

                if($this->post('title') == ""){
                    die("{'success': false, 'error': 'You have not inserted title !'}");
                }
                
                if ((($_FILES["foto"]["type"] == "image/gif") || ($_FILES["foto"]["type"] == "image/jpeg") 
                    || ($_FILES["foto"]["type"] == "image/jpg") || ($_FILES["foto"]["type"] == "image/pjpeg") 
                    || ($_FILES["foto"]["type"] == "image/x-png") || ($_FILES["foto"]["type"] == "image/png")) 
                    && in_array($extension, $allowedExts)) {
                    
                     if ($_FILES["foto"]["error"] > 0) {
                        die("{'success': false, 'error': '" . $_FILES["foto"]["error"] . "'}");
                    } else {
                        $infoString .= " Upload: " . $_FILES["foto"]["name"] . "<br>";
                        $infoString .= " Type: " . $_FILES["foto"]["type"] . "<br>";
                        $infoString .= " Size: " . ($_FILES["foto"]["size"] / 1024) . " kB<br>";
                        $infoString .= " Temp file: " . $_FILES["foto"]["tmp_name"] . "<br>";

                        if (file_exists("images/photo_activity/" . $name)) {
                            die("{'success': false, 'error': '" . $name . " already exists.'}");
                        } else {
                            
                            move_uploaded_file($_FILES["foto"]["tmp_name"], "images/photo_activity/" . $name);
                            
                            $infoString .= " Stored in: " . "images/photo_activity/" . $name;
                            $foto = $this->mfoto->create_foto($satu,$title,$path,$name,$type,$size,$_SESSION['userid']);
                            
                            if ($foto)
                                $this->response($foto, 200);
                            else
                                $this->response(array('error' => 'Photo could not be created'), 404);
                        }
                    }
                } else {
                    die("{'success': false, 'error': 'You have uploaded an invalid photo file type !'}");
                }                        
            }
            // DELETE 
            function foto_delete() {
                if (!$this->delete('id'))
                    $this->response(NULL, 400);
                unlink('images/photo_activity/' . $this->delete('name'));
                $foto = $this->mfoto->delete_foto($this->delete('id'));
                if ($foto)
                    $this->response($foto, 200);
                else
                    $this->response(array('error' => 'Photo could not be deleted'), 404);
            }
            // READ
            function foto_read($id) {
                $data['data'] = $this->mcpg->readTraining($id);
                $part = $this->mcpg->readParticipants($id);
                $data['attendance'] = array();
                foreach ($part['data'] as $key => $value) {
                    $data['attendance'][$key] = $this->mcpg->getFarmerAttendance($id, $value['pFarmerID']);
                }
                $data['peserta'] = $part['data'];
                $data['logo']    = $this->mcpg->readPartnerLogo($id);
                $this->load->view('cpg_cetak_hadir', $data);
            }
        // ============================================================================================================= 

    // =============================================================================================================
    // gallery ---> mgallery activity
    // =============================================================================================================
        // VIEW GRID
        function gallery_get() {
            //var_dump($this->get('key'));die;
            $Category = $this->get('Category'); 
            $Type = $this->get('Type'); 
            $key = $this->get('key'); 
            $foto = $this->mgallery->cari_Gallery($Category,$Type,$key,$this->get('start'), $this->get('limit'));            
            // echo '<pre>'; print_r($foto); echo '</pre>'; exit;
            if ($foto) {
                // $data = array();
                // $dates = array();
                // foreach ($foto['data'] as $key => $value) {
                //     $tgl = date('Y-m-d', strtotime($value['DateCreated']));
                //     if (!in_array($tgl, $dates)) {
                //         $dates[] = $tgl;
                //     }
                // }
                // foreach ($dates as $key => $value) {
                //     $data[$key]['group'] = $value;
                //     foreach ($foto['data'] as $k => $v) {
                //         if (date('Y-m-d', strtotime($v['DateCreated'])) == $value) {
                //             $data[$key]['list'][] = $v;
                //         }
                //     }
                // }
                // // echo '<pre>'; print_r($data); echo '</pre>'; exit;
                // $this->response(array(
                //     'data' => $data,
                //     'total' => $foto['total']
                // ), 200);     
                $this->response($foto, 200);
            } else 
                $this->response(array('error' => 'Photo could not be found'), 404);
            
        }              
        // DELETE 
        function gallery_delete() {
            if (!$this->delete('id'))
                $this->response(NULL, 400);
            unlink('images/photo_activity/' . $this->delete('name'));
            $foto = $this->mfoto->delete_foto($this->delete('id'));
            if ($foto)
                $this->response($foto, 200);
            else
                $this->response(array('error' => 'Photo could not be deleted'), 404);
        }

    // ==========================================================================================================================
    function Provinsis_get() {
        $data = $this->mactivity->readProvinsis($this->get('key'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Kabupatens_get() {
        $data = $this->mactivity->readKabupatens($this->get('key'),$this->get('prov'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Kecamatans_get() {
        $data = $this->mactivity->readKecamatans($this->get('key'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    function Desas_get() {
        $data = $this->mactivity->readDesas($this->get('key'),$this->get('kab'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

}
