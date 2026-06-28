<?php
/**
 * @Author: nikolius
 * @Date:   2017-04-07 10:50:56
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/*
ini_set('display_errors',true);
error_reporting(E_ALL);
*/
class Off_data extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('data_adm/moff_data');
    }

    public function main_list_district_get(){
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        $data = $this->moff_data->getMainListDistrict();
        $this->response($data, 200);
    }

    public function main_list_metadata_get(){
        $data = $this->moff_data->getMainListMetadata();
        $this->response($data, 200);
    }

    public function download_metadata_get(){
        //get latest filename metadata
        $metaFile = $this->moff_data->getLatestMetadataFilename();
        $metaFilePath = 'resources/offline_metadata/'.$metaFile;

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".filesize($metaFilePath));
        header("Content-Disposition: attachment; filename=$metaFile");
        readfile($metaFilePath);
        exit;
    }

    public function download_metadata_kcp_get(){
        //get latest filename metadata
        $metaFile = $this->moff_data->getLatestMetadataKCPFilename();
        $metaFilePath = 'files/offline_metadata/'.$metaFile;

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".filesize($metaFilePath));
        header("Content-Disposition: attachment; filename=$metaFile");
        readfile($metaFilePath);
        exit;
    }

    public function generate_metadata_post(){
        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        $basicAuth = 'YWRtaW46S29sdGl2YTIwMTMh';
        /*
        getMetadataOffline-2017.csv - RDD7V4WGean - getMetadataOffline-2017
        getMetadataOffline-2017 optionset_optionvalue.csv - lQjiVvyE4I6 - getMetadataOffline-2017 optionset_optionvalue
        getMetadataOffline-2017 programindicator.csv - CyNQMnKv9tA - getMetadataOffline-2017 programindicator
        getMetadataOffline-2017 programrule.csv - f64r10OBSTv - getMetadataOffline-2017 programrule
        getMetadataOffline-2017 programruleaction.csv - xmP4onhKnD9 - getMetadataOffline-2017 programrule_programruleaction
        getMetadataOffline-2017 programrulevariable.csv - VW6Tt3kDYEu - getMetadataOffline-2017 programrulevariable
        getMetadataOffline-contract.json - LVANAibyGep - getContract
        getMetadataOffline-masterfarmergroups.json - dfgi7aNdX9J - getCollectivaSetting (ambil url nya dengan key : cpg_master_url)
        getMetadataOffline-translation.json - ewzAwvJGxT3 - getTranslation
        */

        if($this->post('opsiDevelopment') == "yes"){
            $namaFolder = 'metadataoffline-devel';
            $metadataFolder = 'files/offline_metadata_devel/';

            //hapus data lama
            @unlink($metadataFolder.$namaFolder.'.zip');

            //buat folder
            mkdir($metadataFolder.$namaFolder);
        }else{
            $namaFolder = 'metadataoffline-'.date('Ymd_His');
            $metadataFolder = 'files/offline_metadata/';
            mkdir($metadataFolder.$namaFolder);
        }

        // getMetadataOffline-2017.csv (begin)
            //curl
            $url = $this->config->item('dhis_url').'api/sqlViews/RDD7V4WGean/data.csv';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic '.$basicAuth
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            //write data
            file_put_contents($metadataFolder.$namaFolder.'/getMetadataOffline-2017.csv', $result);
        // getMetadataOffline-2017.csv (end)

        // getMetadataOffline-2017 optionset_optionvalue.csv (begin)
            //curl
            $url = $this->config->item('dhis_url').'api/sqlViews/lQjiVvyE4I6/data.csv';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic '.$basicAuth
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            //write data
            file_put_contents($metadataFolder.$namaFolder.'/getMetadataOffline-2017 optionset_optionvalue.csv', $result);
        // getMetadataOffline-2017 optionset_optionvalue.csv (end)

        // getMetadataOffline-2017 programindicator.csv (begin)
            //curl
            $url = $this->config->item('dhis_url').'api/sqlViews/CyNQMnKv9tA/data.csv';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic '.$basicAuth
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            //write data
            file_put_contents($metadataFolder.$namaFolder.'/getMetadataOffline-2017 programindicator.csv', $result);
        // getMetadataOffline-2017 programindicator.csv (end)

        // getMetadataOffline-2017 programrule.csv (begin)
            //curl
            $url = $this->config->item('dhis_url').'api/sqlViews/f64r10OBSTv/data.csv';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic '.$basicAuth
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            //write data
            file_put_contents($metadataFolder.$namaFolder.'/getMetadataOffline-2017 programrule.csv', $result);
        // getMetadataOffline-2017 programrule.csv (end)

        // getMetadataOffline-2017 programruleaction.csv (begin)
            //curl
            $url = $this->config->item('dhis_url').'api/sqlViews/xmP4onhKnD9/data.csv';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic '.$basicAuth
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            //write data
            file_put_contents($metadataFolder.$namaFolder.'/getMetadataOffline-2017 programrule_programruleaction.csv', $result);
        // getMetadataOffline-2017 programruleaction.csv (end)

        // getMetadataOffline-2017 programrulevariable.csv (begin)
            //curl
            $url = $this->config->item('dhis_url').'api/sqlViews/VW6Tt3kDYEu/data.csv';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic '.$basicAuth
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            //write data
            file_put_contents($metadataFolder.$namaFolder.'/getMetadataOffline-2017 programrulevariable.csv', $result);
        // getMetadataOffline-2017 programrulevariable.csv (end)

        // getMetadataOffline-contract.json (begin)
            //curl
            $url = $this->config->item('dhis_url').'api/sqlViews/LVANAibyGep/data.json';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic '.$basicAuth
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            //write data
            file_put_contents($metadataFolder.$namaFolder.'/getMetadataOffline-contract.json', $result);
        // getMetadataOffline-contract.json (end)

        // master_region.json (begin)
            $data = $this->moff_data->getMasterRegion();
            $data = json_encode($data);

            file_put_contents($metadataFolder.$namaFolder.'/master_region.json', $data);
        // master_region.json (end)

        // getMetadataOffline-translation.json (begin)
            //curl
            $url = $this->config->item('dhis_url').'api/sqlViews/ewzAwvJGxT3/data.json';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic '.$basicAuth
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            //write data
            file_put_contents($metadataFolder.$namaFolder.'/getMetadataOffline-translation.json', $result);
        // getMetadataOffline-translation.json (end)

        //ngezip (begin)
            // Get real path for our folder
            $rootPath = realpath($metadataFolder.$namaFolder);

            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open($metadataFolder.$namaFolder.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Create recursive directory iterator
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();
        //ngezip (end)

        //hapus file dan update ke tabelnya
        array_map('unlink', glob($metadataFolder.$namaFolder."/*.*"));
        rmdir($metadataFolder.$namaFolder);

        if($this->post('opsiDevelopment') != "yes"){
            $proses = $this->moff_data->updateMetadataFilename($namaFolder.'.zip');
        }

        ini_set('memory_limit', $mem_ini);
        $this->response('File Generated', 200);
    }

    public function generate_post(){
        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        $DhisSqlViewID = $this->post('DhisSqlViewID');
        $DhisSqlViewName = $this->post('DhisSqlViewName');

        $url = $this->config->item('dhis_url').'api/sqlViews/'.$DhisSqlViewID.'/data.csv';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic YWRtaW46S29sdGl2YTIwMTMh'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $this->load->library('zip');
        $namaFile = date('Ymd').'_'.$DhisSqlViewName.'.csv';
        $this->zip->add_data($namaFile, $result);

        // Write the zip file to a folder on your server. Name it "my_backup.zip"
        $this->zip->archive('files/offline_data/'.date('Ymd').'_'.$DhisSqlViewName.'.zip');

        ini_set('memory_limit', $mem_ini);
        $this->response('File Generated', 200);
    }

    public function main_list_subdistrict_get(){
        if($_SESSION['language'] == "Indonesia"){
            $this->load->language('general', 'indonesia');
        }else{
            $this->load->language('general', 'english');
        }

        $data = $this->moff_data->getMainListSubdistrict();
        $this->response($data, 200);
    }

    public function generate_metadata_backend_post(){
        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        $basicAuth = 'YWRtaW46S29sdGl2YTIwMTMh';
        
        if($this->post('opsiDevelopment') == "yes"){
            $namaFolder = 'metadataoffline-devel';
            $metadataFolder = 'files/offline_metadata_devel/';

            //hapus data lama
            @unlink($metadataFolder.$namaFolder.'.zip');

            //buat folder
            mkdir($metadataFolder.$namaFolder);
        }else{
            $namaFolder = 'metadataoffline-'.date('Ymd_His');
            $metadataFolder = 'files/offline_metadata/';
            mkdir($metadataFolder.$namaFolder);
        }

        // getMetadataOffline-2017.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadata('getmetadataoffline','Koltiva');
        
        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017.csv', $result);
        }
        // getMetadataOffline-2017.csv (end)

        // getMetadataOffline-2017 optionset_optionvalue.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadata('optionset_optionvalue','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 optionset_optionvalue.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 optionset_optionvalue.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 optionset_optionvalue.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 optionset_optionvalue.csv', $result);
        }
        // getMetadataOffline-2017 optionset_optionvalue.csv (end)
        
        // getMetadataOffline-2017 programindicator.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadata('programindicator','Koltiva');
        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 programindicator.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 programindicator.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 programindicator.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 programindicator.csv', $result);
        }
        // getMetadataOffline-2017 programindicator.csv (end)

        // getMetadataOffline-2017 programrule.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadata('programrule','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 programrule.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 programrule.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 programrule.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 programrule.csv', $result);
        }
        // getMetadataOffline-2017 programrule.csv (end)

        // getMetadataOffline-2017 programruleaction.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadata('programrule_programruleaction','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 programrule_programruleaction.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 programrule_programruleaction.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 programrule_programruleaction.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 programrule_programruleaction.csv', $result);
        }
        // getMetadataOffline-2017 programruleaction.csv (end)

        // getMetadataOffline-2017 programrulevariable.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadata('programrulevariable','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 programrulevariable.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 programrulevariable.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 programrulevariable.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 programrulevariable.csv', $result);
        }
        // getMetadataOffline-2017 programrulevariable.csv (end)

        // getMetadataOffline-contract.json (begin)
        $result = '';

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-contract.json', $result);

        // getMetadataOffline-translation.json (begin)
        $result = $this->moff_data->callStoredProcedureMetadata('translation','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-translation.json', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-translation.json'))
                delete_file($FolderTampung . '/getMetadataOffline-translation.json');
            file_put_contents($FolderTampung . '/getMetadataOffline-translation.json', $result);
        }
        // getMetadataOffline-translation.json (end)
        
        // master_region.json (begin)
        $data = $this->moff_data->getMasterRegion();
        $data = json_encode($data);

        file_put_contents($metadataFolder.$namaFolder.'/master_region.json', $data);
        // master_region.json (end)

        //ngezip (begin)
            // Get real path for our folder
            $rootPath = realpath($metadataFolder.$namaFolder);

            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open($metadataFolder.$namaFolder.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Create recursive directory iterator
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();
        //ngezip (end)

        //hapus file dan update ke tabelnya
        array_map('unlink', glob($metadataFolder.$namaFolder."/*.*"));
        rmdir($metadataFolder.$namaFolder);

        if($this->post('opsiDevelopment') != "yes"){
            $proses = $this->moff_data->updateMetadataKCPFilename($namaFolder.'.zip');
        }

        ini_set('memory_limit', $mem_ini);
        $this->response('File Generated', 200);
    }

    public function main_list_metadata_backend_get(){
        $data = $this->moff_data->getMainListMetadataBackend();
        $this->response($data, 200);
    }

    public function generate_metadata_fg_post(){
        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        $basicAuth = 'YWRtaW46S29sdGl2YTIwMTMh';
        
        if($this->post('opsiDevelopment') == "yes"){
            $namaFolder = 'metadataoffline-devel';
            $metadataFolder = 'files/offline_metadata_devel/';

            //hapus data lama
            @unlink($metadataFolder.$namaFolder.'.zip');

            //buat folder
            mkdir($metadataFolder.$namaFolder);
        }else{
            $namaFolder = 'metadataoffline-'.date('Ymd_His');
            $metadataFolder = 'files/offline_metadata/';
            mkdir($metadataFolder.$namaFolder);
        }

        // getMetadataOffline-2017.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadataFg('getmetadataoffline','Koltiva');
        
        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017.csv', $result);
        }
        // getMetadataOffline-2017.csv (end)

        // getMetadataOffline-2017 optionset_optionvalue.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadataFg('optionset_optionvalue','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 optionset_optionvalue.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 optionset_optionvalue.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 optionset_optionvalue.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 optionset_optionvalue.csv', $result);
        }
        // getMetadataOffline-2017 optionset_optionvalue.csv (end)
        
        // getMetadataOffline-2017 programindicator.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadataFg('programindicator','Koltiva');
        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 programindicator.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 programindicator.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 programindicator.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 programindicator.csv', $result);
        }
        // getMetadataOffline-2017 programindicator.csv (end)

        // getMetadataOffline-2017 programrule.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadataFg('programrule','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 programrule.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 programrule.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 programrule.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 programrule.csv', $result);
        }
        // getMetadataOffline-2017 programrule.csv (end)

        // getMetadataOffline-2017 programruleaction.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadataFg('programrule_programruleaction','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 programrule_programruleaction.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 programrule_programruleaction.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 programrule_programruleaction.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 programrule_programruleaction.csv', $result);
        }
        // getMetadataOffline-2017 programruleaction.csv (end)

        // getMetadataOffline-2017 programrulevariable.csv (begin)
        $result = $this->moff_data->callStoredProcedureMetadataFg('programrulevariable','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-2017 programrulevariable.csv', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-2017 programrulevariable.csv'))
                delete_file($FolderTampung . '/getMetadataOffline-2017 programrulevariable.csv');
            file_put_contents($FolderTampung . '/getMetadataOffline-2017 programrulevariable.csv', $result);
        }
        // getMetadataOffline-2017 programrulevariable.csv (end)

        // getMetadataOffline-contract.json (begin)
        $result = '';

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-contract.json', $result);

        // getMetadataOffline-translation.json (begin)
        $result = $this->moff_data->callStoredProcedureMetadataFg('translation','Koltiva');

        //write data
        file_put_contents($metadataFolder . $namaFolder . '/getMetadataOffline-translation.json', $result);

        //write ke tampung
        if ($FolderTampung != "") {
            if (file_exists($FolderTampung . '/getMetadataOffline-translation.json'))
                delete_file($FolderTampung . '/getMetadataOffline-translation.json');
            file_put_contents($FolderTampung . '/getMetadataOffline-translation.json', $result);
        }
        // getMetadataOffline-translation.json (end)
        
        // master_region.json (begin)
        $data = $this->moff_data->getMasterRegion();
        $data = json_encode($data);

        file_put_contents($metadataFolder.$namaFolder.'/master_region.json', $data);
        // master_region.json (end)

        //ngezip (begin)
            // Get real path for our folder
            $rootPath = realpath($metadataFolder.$namaFolder);

            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open($metadataFolder.$namaFolder.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Create recursive directory iterator
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();
        //ngezip (end)

        //hapus file dan update ke tabelnya
        array_map('unlink', glob($metadataFolder.$namaFolder."/*.*"));
        rmdir($metadataFolder.$namaFolder);

        if($this->post('opsiDevelopment') != "yes"){
            $proses = $this->moff_data->updateMetadataFGFilename($namaFolder.'.zip');
        }

        ini_set('memory_limit', $mem_ini);
        $this->response('File Generated', 200);
    }

    public function main_list_metadata_fg_get(){
        $data = $this->moff_data->getMainListMetadataFg();
        $this->response($data, 200);
    }

    public function download_metadata_fg_get(){
        //get latest filename metadata
        $metaFile = $this->moff_data->getLatestMetadataFGFilename();
        $metaFilePath = 'files/offline_metadata/'.$metaFile;

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".filesize($metaFilePath));
        header("Content-Disposition: attachment; filename=$metaFile");
        readfile($metaFilePath);
        exit;
    }
}
?>
