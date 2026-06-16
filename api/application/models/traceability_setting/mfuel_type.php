<?php

/**************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Fri June 24 2022
 * File : mfuel_type.php
 ************************************** */
class Mfuel_type extends CI_Model
{
    public function GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir)
    {
        if ($sortingField == "") $sortingField = "FuelTypeName";
        if ($sortingDir == "") $sortingDir = "ASC";

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    GHGFuelTypeID
                    , `FuelTypeName`
                    , FuelTypeCoefficient
                    , `StatusCode`
                FROM
                    ref_tc_ghg_fuel_type
                WHERE 1=1
                    AND `StatusCode` != 'nullified'
                    AND `FuelTypeName` LIKE ?
                ORDER BY `$sortingField` $sortingDir
                LIMIT ?,?
                ";

        $p = array(
            '%' .$pSearch['textSearch'] . '%', $start, $limit
        );

        $query = $this->db->query($sql, $p);
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        if ($sortingDir == "ASC") {
            $sortingInfo = "ascending";
        }
        if ($sortingDir == "DESC") {
            $sortingInfo = "descending";
        }

        $_SESSION['informationGrid'] = '
            <div class="Sfr_BoxInfoDataGrid_Title"><strong>' . number_format($query->row()->total, 0, ".", ",") . '</strong> ' . lang('Data') . '</div>
            <ul class="Sft_UlListInfoDataGrid">
                <li class="Sft_ListInfoDataGrid">
                    <img class="Sft_ListIconInfoDataGrid" src="' . base_url() . '/assets/images/icons/sort.png" width="20" />&nbsp;&nbsp;Sorted by ' . lang($sortingField) . ' ' . $sortingInfo . '
                </li>
            </ul>';

            return $result;
    }

    public function GetFuelTypeData($GHGFuelTypeID)
    {
        $sql = "SELECT
                    `GHGFuelTypeID`
                    , FuelTypeName
                    , FuelTypeCoefficient
                    , StatusCode
                FROM
                    `ref_tc_ghg_fuel_type`
                WHERE 1=1
                    AND `GHGFuelTypeID` = ?
                LIMIT 1";
        
        $data = $this->db->query($sql, array($GHGFuelTypeID))->row_array();

        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $dataRow['GHGFuelTypeID'] = $data['GHGFuelTypeID'];
        $dataRow['FuelTypeName'] = $data['FuelTypeName'];
        $dataRow['FuelTypeCoefficient'] = $data['FuelTypeCoefficient'];
        $dataRow['StatusCode'] = $data['StatusCode'];

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function InsertFuelType($paramPost) {

        $results = array();
        $this->db->trans_begin();

        $sql = "INSERT INTO `ref_tc_ghg_fuel_type` SET
                    `GHGFuelTypeID` = NULL
                    , `FuelTypeName` = ?
                    , FuelTypeCoefficient = ?
                    , `StatusCode` = ?
                    , DateCreated =  NOW()
                    , CreatedBy = ?";

        $p = array(
            $paramPost['FuelTypeName'],
            $paramPost['FuelTypeCoefficient'],
            $paramPost['StatusCode'],
            $_SESSION['userid']
        );

        $query = $this->db->query($sql, $p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        }

        return $results;

    }

    public function UpdateFuelType($paramPost)
    {
        $results = array();
        $this->db->trans_begin();

        $sql = "UPDATE `ref_tc_ghg_fuel_type` SET
                    `FuelTypeName` = ?
                    , FuelTypeCoefficient = ?
                    , `StatusCode` = ?
                    , DateUpdated =  NOW()
                    , LastModifiedBy = ?
                WHERE
                    `GHGFuelTypeID` = ?
                LIMIT 1";

        $p = array(
            $paramPost['FuelTypeName'],
            $paramPost['FuelTypeCoefficient'],
            $paramPost['StatusCode'],
            $_SESSION['userid'],
            $paramPost['GHGFuelTypeID'],
        );

        $query = $this->db->query($sql, $p);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to update data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data updated");
        }

        return $results;
    }

    function delete_fuel_type($GHGFuelTypeID)
    {
        $results = array();
        $this->db->trans_begin();

        $sql = "DELETE FROM ref_tc_ghg_fuel_type WHERE GHGFuelTypeID = ? LIMIT 1";
        $query = $this->db->query($sql, array($GHGFuelTypeID));

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete fuel type");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Fuel type deleted");
        }

        return $results;

    }
}