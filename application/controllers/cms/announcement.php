<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 14 2018
 *  File : cms_news.php
 *******************************************/

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Announcement extends SS_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mpartner');
    }

    public function index(){
        $page = 1;
        if(null !== $_GET['page']){
            $page = $_GET['page'];
        } 

        $language = 'English';
        if($_GET['language']){
            $language = $_GET['language'];
        } 
        
        $data['action'] = array(
            'api_url' => $this->config->item('api').'/mod_cms',
            'cdn_url' => $this->config->item('CTCDN').'/',
            'current_page' => $page,
            'language' => $language
        );
        
        $data['language'] = $language;

        $this->LoadView($data, 'cms/announcement');
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
        
        $data['title'] = lang('Announcement');
        $data['partners'] = $this->mpartner->readPartners();
        $data['language'] = $language;

        $data['action'] = array(
            'api_url' => $this->config->item('api').'/mod_cms',
            'cdn_url' => $this->config->item('CTCDN').'/',
            'announ_id' => 0,
            'current_page' => $page,
            'author' => $author
        );

        $this->LoadView($data, 'cms/announcement_form');
    }

    public function update(){
        $author = $_SESSION['realname'];

        $page = 1;
        if($_GET['page']){
            $page = $_GET['page'];
        }

        $language = 'English';
        if($_GET['language']){
            $language = $_GET['language'];
        }

        $data['language'] = $language;
        if($_GET['proccess'] == 'update'){
            $data['title'] = lang('News');
            $data['partners'] = $this->mpartner->readPartners();
            $data['action'] = array(
                'api_url' => $this->config->item('api').'/mod_cms',
                'cdn_url' => $this->config->item('CTCDN').'/',
                'announ_id' => $_GET['announ_id'],
                'current_page' => $page,
                'author' => $author
            );

            $this->LoadView($data, 'cms/announcement_form');
        } else {
            $data['title'] = lang('News');
            $data['action'] = array(
                'api_url' => $this->config->item('api').'/mod_cms',
                'cdn_url' => $this->config->item('CTCDN').'/',
                'announ_id' => $_GET['announ_id'],
                'current_page' => $page
            );

            $this->LoadView($data, 'cms/announcement_preview');
        }
    }
}