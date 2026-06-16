<?php
/**
 * @Author: nikolius
 * @Date:   2016-10-13 13:29:58
 */
require_once APPPATH . 'third_party/resize.image.class.php';

ini_set('display_errors',false);
error_reporting(0);

class Image_process extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function resizeOtf_get()
    {
        $resize_image = new Resize_Image;
        $images_dir = FCPATH;

        $VillageID = (int) $this->get('VID');
        $ProvinceID = substr($VillageID,0,2);
        $Gender = $this->get('gen');

        if($this->get('imagenya') != "")
            $image = 'images/member/'.$ProvinceID.'/'.$this->get('imagenya');

        if (!@file_exists($images_dir . $image)) {
            if($Gender == "f"){
                $image = 'images/default_photo/female-farmer.jpg';
            }else{
                $image = 'images/default_photo/male-farmer.jpg';
            }
        }

        if($image == ""){
            $image = 'images/default_photo/male-farmer.jpg';
        }
        //echo '<pre>'; print_r($image); exit;

        $new_width  = (int) $this->get('width');
        $new_height = (int) $this->get('height');

        $resize_image->new_width  = $new_width;
        $resize_image->new_height = $new_height;

        $resize_image->image_to_resize = $images_dir . $image; // Full Path to the file

        $resize_image->ratio = true; // Keep aspect ratio

        $process = $resize_image->resize(); // Output image
    }

    public function resizeOtfByType_get(){
        $resize_image = new Resize_Image;
        $images_dir = FCPATH;

        if($this->get('imagenya') != ""){
            switch ($this->get('tipe')) {
                case 'farmer':
                    $image = 'images/Photo/'.$this->get('imagenya');
                break;
                case 'staff':
                    $image = 'images/staff/'.$this->get('imagenya');
                break;
                case 'other':
                    $image = 'images/photo_responsible/'.$this->get('imagenya');
                break;
            }
        }else{
            $image = 'images/Photo/default-user.png';
        }

        if (!@file_exists($images_dir . $image)) {
            $image = 'images/Photo/default-user.png';
        }

        $new_width  = (int) $this->get('width');
        $new_height = (int) $this->get('height');

        $resize_image->new_width  = $new_width;
        $resize_image->new_height = $new_height;

        $resize_image->image_to_resize = $images_dir . $image; // Full Path to the file

        $resize_image->ratio = true; // Keep aspect ratio

        $process = $resize_image->resize(); // Output image
    }

    public function resizeOtfNursery_get(){
        $resize_image = new Resize_Image;
        $images_dir = FCPATH;

        if($this->get('imagenya') != "")
            $image = 'images/nursery/'.$this->get('imagenya');
        else
            $image = 'images/nursery/no-image.png';

        if (!@file_exists($images_dir . $image)) {
            $image = 'images/nursery/no-image.png';
        }

        $new_width  = (int) $this->get('width');
        $new_height = (int) $this->get('height');

        $resize_image->new_width  = $new_width;
        $resize_image->new_height = $new_height;

        $resize_image->image_to_resize = $images_dir . $image; // Full Path to the file

        $resize_image->ratio = true; // Keep aspect ratio

        $process = $resize_image->resize(); // Output image
    }

    public function resizeOtfLandscape_get(){
        ini_set('display_errors',false);
        error_reporting(0);

        $resize_image = new Resize_Image;
        $images_dir = FCPATH;

        $VillageID = (int) $this->get('VID');
        $ProvinceID = substr($VillageID,0,2);
        $Gender = $this->get('gen');

        switch ($this->get('opsi')) {
            case 'agentBisnisLocation':
                $resize_image->ratio = false; // Keep aspect ratio
                if($this->get('imagenya') != "")
                    $image = 'images/trader_business/'.$ProvinceID.'/'.$this->get('imagenya');

                if (!@file_exists($images_dir . $image)) {
                    $image = 'images/default_photo/agent-location-land.jpg';
                }

                if($this->get('imagenya') == ""){
                    $image = 'images/default_photo/agent-location-land.jpg';
                }
            break;

            case 'fotoTrader':
                $resize_image->ratio = true; // Keep aspect ratio

                if($this->get('imagenya') != "")
                    $image = 'images/trader/'.$ProvinceID.'/'.$this->get('imagenya');

                if($this->get('imagenya') == ""){
                    $image = 'images/default_photo/agent-location-land.jpg';
                }

                if (!@file_exists($images_dir . $image)) {
                    if($Gender == "f"){
                        $image = 'images/default_photo/female-business.jpg';
                    }else{
                        $image = 'images/default_photo/male-business.jpg';
                    }
                }

            break;

            case 'logoMill':
                $resize_image->ratio = true; // Keep aspect ratio

                if($this->get('imagenya') != "")
                    $image = 'images/mill/'.$ProvinceID.'/'.$this->get('imagenya');

                if (!@file_exists($images_dir . $image)) {
                    $image = 'images/default_photo/business-logo.jpg';
                }

                if($this->get('imagenya') == ""){
                    $image = 'images/default_photo/business-logo.jpg';
                }

            break;

            case 'fotoMill':
                $resize_image->ratio = false; // Keep aspect ratio

                if($this->get('imagenya') != "")
                    $image = 'images/mill_location/'.$ProvinceID.'/'.$this->get('imagenya');

                if (!@file_exists($images_dir . $image)) {
                    $image = 'images/default_photo/mill-location-land.jpg';
                }

                if($this->get('imagenya') == ""){
                    $image = 'images/default_photo/mill-location-land.jpg';
                }

            break;
        }

        $new_width  = (int) $this->get('width');
        $new_height = (int) $this->get('height');

        $resize_image->new_width  = $new_width;
        $resize_image->new_height = $new_height;

        $resize_image->image_to_resize = $images_dir . $image; // Full Path to the file

        $process = $resize_image->resize(); // Output image
    }
}
