<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * @Author: sonny.fitriawan 
 * @Date: 2018-01-09 13:26:09 
 * @Last Modified by: sonny.fitriawan
 * @Last Modified time: 2018-11-22 12:15:57
 */

define("SITE_DIRECTORY",$_SERVER['DOCUMENT_ROOT'] .'/');

define("EXPORTS_DIRECTORY",FCPATH.'exports/');
define("EXPORTS_URL", "http://".$_SERVER['HTTP_HOST'].'/exports/');
define("UPLOADS_DIRECTORY", SITE_DIRECTORY.'/uploads/sign/');
define("UPLOADS_URL", "http://".$_SERVER['HTTP_HOST'].'/uploads/sign/');

class EmailTemplate extends REST_Controller{
    public function __construct() {
        parent::__construct();
        $this->file = $_FILES;
        $this->load->model('email/memail');
        ini_set('zlib.output_compression', true);
    }    

    function createHtmlandZipFile($html=''){

        $todayh = getdate();
        $filename= "email-editor-".$todayh[seconds].$todayh[minutes].$todayh[hours].$todayh[mday]. $todayh[mon].$todayh[year];

        $newHtmlFilename=EXPORTS_DIRECTORY.$filename.'.html';
        $zipFilename=EXPORTS_DIRECTORY.$filename.'.zip';
        $zipFileUrl=EXPORTS_URL.$filename.'.zip';
        $htmlFileUrl=EXPORTS_URL.$filename.'.html';

        //read email template
        $templateContent=file_get_contents(base_url()."balemail-assets/docs/template.html",true);

        //create new document
        $new_content =$html;

        //view in browser link
        $new_content=str_replace('#view_web',$htmlFileUrl,$new_content);


        $content=str_replace('[email-body]',$new_content,$templateContent);
        $fp = fopen($newHtmlFilename,"wb");
        fwrite($fp,$content);
        fclose($fp);

        //create zip document
        $zip = new ZipArchive();

        $zip->open($zipFilename, ZipArchive::CREATE);
        $zip->addFile($newHtmlFilename, 'index.html');
        $zip->close();
        //remove html file
        //unlink($newHtmlFilename);

        $response=array();
        $response['code']=0;
        $response['url']=$zipFileUrl;
        $response['preview_url']=$htmlFileUrl;
        $response['html']=$new_content;

        return $response;
    }

    function browse_get(){
        $dir    = $_SERVER['DOCUMENT_ROOT'].'/api/uploads/ckeditor';
        $files = $this->dirToArray($dir);

        $retFile = array();

        foreach($files as $key => $file){
            $retFile[$key]['image'] = base_url().'uploads/ckeditor/'.$file;
            $retFile[$key]['thumb'] = base_url().'uploads/ckeditor/'.$file;
            $retFile[$key]['folder'] = 'Small';
        }

        $this->response($retFile, 200);
        /*(echo '
            [
                {
                    "image": "/image1_200x150.jpg",
                    "thumb": "/image1_thumb.jpg",
                    "folder": "Small"
                },
                {
                    "image": "/image2_200x150.jpg",
                    "thumb": "/image2_thumb.jpg",
                    "folder": "Small"
                },
            
                {
                    "image": "/image1_full.jpg",
                    "thumb": "/image1_thumb.jpg",
                    "folder": "Large"
                },
                {
                    "image": "/image2_full.jpg",
                    "thumb": "/image2_thumb.jpg",
                    "folder": "Large"
                }
            ]
        ';*/
    }

    function dirToArray($dir) { 
   
        $result = array(); 
     
        $cdir = scandir($dir); 
        foreach ($cdir as $key => $value) 
        { 
           if (!in_array($value,array(".",".."))) 
           { 
              if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
              { 
                 $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
              } 
              else 
              { 
                 $result[] = $value; 
              } 
           } 
        } 
        
        return $result;

    }

    function editor_upload_post(){
        $limited_ext = array(".jpg",".jpeg",".png",".gif",".bmp");
        $limited_type = array("image/jpg","image/jpeg","image/png","image/gif","image/bmp");
        $not_allowed = array(".php", ".exe", ".zip", ".rar", ".js", ".txt", ".css", ".html", ".htm", ".doc", ".docx");

        $nameUpload = strtolower(basename($_FILES['upload']['name']));
        $typeUpload = strtolower($_FILES['upload']['type']);

        if( isset($_FILES['upload']) && strlen($nameUpload) > 1 ) {
            if ( in_array($typeUpload,$limited_type) ) {
                if( $_FILES['upload']['size'] > 0 ) {
                    $check = getimagesize($_FILES["upload"]["tmp_name"]);
                    if( $check !== false && in_array($check['mime'],$limited_type) ) {
                        $notAllowFlag = 0;
                        foreach( $not_allowed as $notAllow ) {
                            $pos = strpos($nameUpload, $notAllow);
                            if( $pos !== false ) {
                                $notAllowFlag = 1;
                            }
                        }
                        if( $notAllowFlag == 0 ) {
                            $ext = strrchr($nameUpload,'.');
                            if ( in_array($ext,$limited_ext) ) {
                                $funcNum    = $_GET['CKEditorFuncNum'] ;
                                // Optional: instance name (might be used to load a specific configuration file or anything else).
                                $CKEditor   = $_GET['CKEditor'] ;
                                // Optional: might be used to provide localized messages.
                                $langCode   = $_GET['langCode'] ;
                                $uploadurl  = base_url() . 'uploads/ckeditor/';
                                $uploaddir  = $_SERVER['DOCUMENT_ROOT'].'/api/uploads/ckeditor/'; //$uploaddir set permission 777 (unix)
        
                                $new_file_name = rand(100000,999999) . $ext;
                                while ( is_file( $uploaddir . $new_file_name) ) {
                                    $new_file_name = rand(100000,999999) . $ext;
                                }

                                $uploadMoveUpFile = filter_var($uploaddir . $new_file_name,FILTER_SANITIZE_STRING);
                                if ( move_uploaded_file($_FILES['upload']['tmp_name'], $uploadMoveUpFile) ) {
                                   $url = $uploadurl . $new_file_name;
                                   $re = "window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', 'Uploaded successfully...');";
                                } else {
                                    $re = 'alert("Unable to upload the file");';
                                }
                            } else {
                                $re = 'alert("Please select an allowed files ( JPG, PNG, GIF, BMP)...");';
                            }
                        } else {
                            $re = 'alert("Please select an allowed files ( JPG, PNG, GIF, BMP)...");';
                        }
                    } else {
                        $re = 'alert("Please select an allowed files ( JPG, PNG, GIF, BMP)...");';
                    }   
                } else {
                    $re = 'alert("File size cannot be null!");';
                }
            } else {
                $re = 'alert("Please select an allowed files ( JPG, PNG, GIF, BMP)...");';
            }
        } else {
            $re = 'alert("Error!");';
        }

        @header('Content-type: text/html; charset=utf-8');
        $re = filter_var($re,FILTER_UNSAFE_RAW);
        echo "<script>$re;</script>";
    }

}