<?php

/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Aug 21 2019
 *  File : mmigrations3.php
 *******************************************/
defined('BASEPATH') or exit('No direct script access allowed');

class Mmigrations3 extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function MigrateFarmerPhoto()
    {
        $FilesIncTrue = 0;
        $FilesIncFalse = 0;
        $result = array();
        $this->load->library('awsfileupload');

        //List petani
        $sql = "SELECT
                m.MemberID
                , m.Photo
                , SUBSTR(m.Photo, 1, 22) Path
                , e.ProvinceID
                , m.DateUpdated
                , m.DateCreated
                , m.uid
            FROM
                `ktv_members` m
            INNER JOIN
                ktv_member_role mr on mr.MemberID = m.MemberID
            LEFT JOIN 
                ktv_village c ON m.VillageID = c.VillageID
            LEFT JOIN 
                ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN 
                ktv_district f ON d.DistrictID = f.DistrictID
            LEFT JOIN 
                ktv_province e ON f.ProvinceID = e.ProvinceID
            WHERE
                m.StatusCode = 'active'
            AND
                m.Photo != ''
            AND
                mr.MRoleID = 1
            AND
                SUBSTR(m.Photo, 1, 22) <> 'images/members/farmer/'
            GROUP BY
                m.MemberID
            ORDER BY m.DateCreated DESC
        ";
        $DataFarmer = $this->db->query($sql)->result_array();

        if (isset($DataFarmer)) {
            for ($i = 0; $i < count($DataFarmer); $i++) {
                //Path Foto
                $pathfotodigocean = 'images/member/' . $DataFarmer[$i]['ProvinceID'].'/' . $DataFarmer[$i]['Photo'];
                $path_parts = pathinfo($pathfotodigocean);
                $namafile = $path_parts['basename'];

                //File info
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $pathfotodigocean);
                // echo '<pre>'; print_r($path_parts); exit;

                if ($mime_type != 'inode/x-empty') {
                    $upload = $this->awsfileupload->upload($pathfotodigocean, $namafile, AWSS3_FARMER_PATH, 'images');
                    if ($upload['success'] == true) {
                        //Update
                        $sql = "UPDATE ktv_members a SET
                                    a.`Photo` = ?
                                WHERE
                                    a.`MemberID` = ?
                                LIMIT 1";
                        $p = array(
                            $upload['filenamepath'],
                            $DataFarmer[$i]['MemberID']
                        );
                        $query = $this->db->query($sql, $p);
                        if ($this->db->affected_rows() > 0) {
                            $FilesIncTrue++;
                        }
                    } else {
                        $FilesIncFalse++;
                    }
                } else {
                    $FilesIncFalse++;
                }
            }
        }


        $result['success'] = true;
        $result['message'] = $FilesIncTrue . " files uploaded, $FilesIncFalse failed";
        return $result;
    }

    public function MigrateFarmerPlotPhoto()
    {
        $FilesIncTrue = 0;
        $FilesIncFalse = 0;
        $result = array();
        $this->load->library('awsfileupload');

        //List Plot Petani
        $sql = "SELECT
                sp.MemberID
                , sp.PlotNr
                , sp.SurveyNr
                , e.ProvinceID
                , sp.PhotoOfVisit
                , SUBSTR(sp.PhotoOfVisit,1,19) Path
                , sp.DateCreated
	            , m.MemberDisplayID
            FROM
                ktv_survey_plot sp
            INNER JOIN
                ktv_members m on m.MemberID = sp.MemberID
            LEFT JOIN 
                ktv_village c ON m.VillageID = c.VillageID
            LEFT JOIN 
                ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN 
                ktv_district f ON d.DistrictID = f.DistrictID
            LEFT JOIN 
                ktv_province e ON f.ProvinceID = e.ProvinceID
            WHERE
                sp.StatusCode = 'active'
            AND
                sp.PhotoOfVisit <> ''
            AND
                SUBSTR(sp.PhotoOfVisit,1,19) <> 'images/plot/farmer/'
            ORDER BY
                sp.DateCreated DESC
        ";
        $DataPlotFarmer = $this->db->query($sql)->result_array();

        if (isset($DataPlotFarmer)) {
            for ($i = 0; $i < count($DataPlotFarmer); $i++) {
                //Path Foto
                $pathfotodigocean = 'images/plot_visit/'.$DataPlotFarmer[$i]['ProvinceID'].'/'.$DataPlotFarmer[$i]['MemberDisplayID'].'/'. $DataPlotFarmer[$i]['PhotoOfVisit'];
                $path_parts = pathinfo($pathfotodigocean);
                $namafile = $path_parts['basename'];

                //File info
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $pathfotodigocean);
                // echo '<pre>'; print_r($pathfotodigocean); exit;

                if ($mime_type != 'inode/x-empty') {
                    $upload = $this->awsfileupload->upload($pathfotodigocean, $namafile, AWSS3_FARMER_PLOT_PATH, 'images');
                    if ($upload['success'] == true) {
                        //Update
                        $sql = "UPDATE ktv_survey_plot a SET
                                    a.`PhotoOfVisit` = ?
                                WHERE
                                    a.`MemberID` = ?
                                AND 
                                    a.`PlotNr` = ?
                                AND 
                                    a.`SurveyNr` = ?
                                LIMIT 1";
                        $p = array(
                            $upload['filenamepath'],
                            $DataPlotFarmer[$i]['MemberID'],
                            $DataPlotFarmer[$i]['PlotNr'],
                            $DataPlotFarmer[$i]['SurveyNr']
                        );
                        $query = $this->db->query($sql, $p);
                        if ($this->db->affected_rows() > 0) {
                            $FilesIncTrue++;
                        }
                    } else {
                        $FilesIncFalse++;
                    }
                } else {
                    $FilesIncFalse++;
                }
            }
        }


        $result['success'] = true;
        $result['message'] = $FilesIncTrue . " files uploaded, $FilesIncFalse failed";
        return $result;
    }

    public function MigrateSMEPhoto()
    {
        $FilesIncTrue = 0;
        $FilesIncFalse = 0;
        $result = array();
        $this->load->library('awsfileupload');

        //List SME
        $sql = "SELECT
                m.MemberID
                , m.Photo
                , SUBSTR(m.Photo, 1, 19) Path
                , e.ProvinceID
                , m.DateUpdated
                , m.DateCreated
                , m.uid
            FROM
                `ktv_members` m
            INNER JOIN
                ktv_member_role mr on mr.MemberID = m.MemberID
            LEFT JOIN 
                ktv_village c ON m.VillageID = c.VillageID
            LEFT JOIN 
                ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN 
                ktv_district f ON d.DistrictID = f.DistrictID
            LEFT JOIN 
                ktv_province e ON f.ProvinceID = e.ProvinceID
            WHERE
                m.StatusCode = 'active'
            AND
                m.Photo != ''
            AND
                mr.MRoleID IN (5,6,7,8,9,10,11,12,13,14)
            AND
                SUBSTR(m.Photo, 1, 19) <> 'images/members/sme/'
            GROUP BY
                m.MemberID
            ORDER BY m.DateCreated DESC
        ";
        $DataSME = $this->db->query($sql)->result_array();

        if (isset($DataSME)) {
            for ($i = 0; $i < count($DataSME); $i++) {
                //Path Foto
                $pathfotodigocean = 'images/trader/' . $DataSME[$i]['ProvinceID'].'/' . $DataSME[$i]['Photo'];
                $path_parts = pathinfo($pathfotodigocean);
                $namafile = $path_parts['basename'];

                //File info
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $pathfotodigocean);
                // echo '<pre>'; print_r($mime_type); exit;

                if ($mime_type != 'inode/x-empty') {
                    $upload = $this->awsfileupload->upload($pathfotodigocean, $namafile, AWSS3_SME_PATH, 'images');
                    if ($upload['success'] == true) {
                        //Update
                        $sql = "UPDATE ktv_members a SET
                                    a.`Photo` = ?
                                WHERE
                                    a.`MemberID` = ?
                                LIMIT 1";
                        $p = array(
                            $upload['filenamepath'],
                            $DataSME[$i]['MemberID']
                        );
                        $query = $this->db->query($sql, $p);
                        if ($this->db->affected_rows() > 0) {
                            $FilesIncTrue++;
                        }
                    } else {
                        $FilesIncFalse++;
                    }
                } else {
                    $FilesIncFalse++;
                }
            }
        }


        $result['success'] = true;
        $result['message'] = $FilesIncTrue . " files uploaded, $FilesIncFalse failed";
        return $result;
    }

    public function MigrateMillPhoto()
    {
        $FilesIncTrue = 0;
        $FilesIncFalse = 0;
        $result = array();
        $this->load->library('awsfileupload');

        //List SME
        $sql = "SELECT
                m.MillID
                , m.Photo
                , SUBSTR(m.VillageID,1,2) ProvinceID
                , SUBSTR(m.Photo, 1, 17) Path
                , m.DateCreated
            FROM
                ktv_mill m
            LEFT JOIN 
                ktv_village c ON m.VillageID = c.VillageID
            LEFT JOIN 
                ktv_subdistrict d ON c.SubDistrictID = d.SubDistrictID
            LEFT JOIN 
                ktv_district f ON d.DistrictID = f.DistrictID
            LEFT JOIN 
                ktv_province e ON f.ProvinceID = e.ProvinceID
            WHERE
                m.StatusCode = 'active'
            AND
                m.Photo <> ''
            AND
                SUBSTR(m.Photo, 1, 17) <> 'images/mill/logo/'
            ORDER BY 
                m.DateCreated DESC       
        ";
        $DataMill = $this->db->query($sql)->result_array();

        if (isset($DataMill)) {
            for ($i = 0; $i < count($DataMill); $i++) {
                //Path Foto
                $pathfotodigocean = 'images/mill/' . $DataMill[$i]['ProvinceID'].'/' . $DataMill[$i]['Photo'];
                $path_parts = pathinfo($pathfotodigocean);
                $namafile = $path_parts['basename'];

                //File info
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $pathfotodigocean);
                // echo '<pre>'; print_r($pathfotodigocean); exit;

                if ($mime_type != 'inode/x-empty') {
                    $upload = $this->awsfileupload->upload($pathfotodigocean, $namafile, AWSS3_MILL_LOGO_PATH, 'images');
                    if ($upload['success'] == true) {
                        //Update
                        $sql = "UPDATE ktv_mill a SET
                                    a.`Photo` = ?
                                WHERE
                                    a.`MillID` = ?
                                LIMIT 1";
                        $p = array(
                            $upload['filenamepath'],
                            $DataMill[$i]['MillID']
                        );
                        $query = $this->db->query($sql, $p);
                        if ($this->db->affected_rows() > 0) {
                            $FilesIncTrue++;
                        }
                    } else {
                        $FilesIncFalse++;
                    }
                } else {
                    $FilesIncFalse++;
                }
            }
        }


        $result['success'] = true;
        $result['message'] = $FilesIncTrue . " files uploaded, $FilesIncFalse failed";
        return $result;
    }
}