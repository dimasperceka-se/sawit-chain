<?php
/**
 * @author [Sonny Fitriawan]
 * @email [sonny.fitriawan@koltiva.com]
 * @create date 2020-05-14 10:45:50
 * @modify date 2020-05-14 10:45:50
 * @desc [description]
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Video extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mpartner');
        $this->load->model('mlanguage');
    }

    public function index(){
        $page = 1;
        if(null !== $_GET['page']){
            $page = $_GET['page'];
        } 
        $language = 'English';
        if(null !== $_GET['language']){
            $language = $_GET['language'];
        } 

        $data['action'] = array(
            'api_url' => $this->config->item('api').'/mod_cms',
            'cdn_url' => $this->config->item('CTCDN').'/',
            'current_page' => $page,
            'language' => $language
        );
        
        $data['language'] = $language;
        $data['language_list'] = $this->mlanguage->readLanguages();

        $this->LoadView($data, 'cms/video');
    }

    public function add(){

        $author = $_SESSION['realname'];

        $page = 1;
        if($_GET['page']){
            $page = $_GET['page'];
        }

        $language = 'English';
        if($_GET['language']){
            $language = $_GET['language'];
        }

        $data['title'] = lang('News');
        $data['partners'] = $this->mpartner->readPartners();
        $data['language'] = $language;
        $data['language_list'] = $this->mlanguage->readLanguages();
        
        $data['action'] = array(
            'api_url' => $this->config->item('api').'/mod_cms',
            'cdn_url' => $this->config->item('CTCDN').'/',
            'video_id' => 0,
            'current_page' => $page,
            'author' => $author
        );

        $this->LoadView($data, 'cms/video_form');
    }

    public function update(){

        $author = $_SESSION['realname'];

        $page = 1;
        if($_GET['language']){
            $page = $_GET['page'];
        }

        $language = 'English';
        if($_GET['language']){
            $language = $_GET['language'];
        }
        $data['language'] = $language;
        $data['language_list'] = $this->mlanguage->readLanguages();

        if($_GET['proccess'] == 'update'){
            $data['title'] = lang('News');
            $data['partners'] = $this->mpartner->readPartners();
            $data['action'] = array(
                'api_url' => $this->config->item('api').'/mod_cms',
                'cdn_url' => $this->config->item('CTCDN').'/',
                'video_id' => $_GET['video_id'],
                'current_page' => $page
            );

            $this->LoadView($data, 'cms/video_form');
        } else {
            $data['title'] = lang('News');
            $data['action'] = array(
                'api_url' => $this->config->item('api').'/mod_cms',
                'cdn_url' => $this->config->item('CTCDN').'/',
                'video_id' => $_GET['video_id'],
                'current_page' => $page
            );

            $this->LoadView($data, 'cms/video_preview');
        }
    }

}
