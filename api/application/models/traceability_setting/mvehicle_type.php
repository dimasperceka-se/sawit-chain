<?php

/*******************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Tue June 28 2022
 * File : mvehicle_type.php
********************************************/
class Mvehicle_type extends CI_Model
{
    public function GetGridMain($pSearch, $start, $limit, $sortingField, $sortingDir)
    {
        if ($sortingField == "") $sortingField = "VehicleTypeName";
        if ($sortingDir == "") $sortingDir = "ASC";

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    `GHGVehicleTypeID`
                    , VehicleTypeName
                    , FuelConsumption
                    , `StatusCode`
                FROM
                    ref_tc_ghg_vehicle_type
                WHERE 1=1
                    AND `StatusCode` != 'nullified'
                    AND `VehicleTypeName` LIKE ?
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

    public function GetVehicleTypeData($GHGVehicleTypeID)
    {
        $sql = "SELECT
                    `GHGVehicleTypeID`
                    , VehicleTypeName
                    , FuelConsumption
                    , StatusCode
                FROM
                    `ref_tc_ghg_vehicle_type`
                WHERE 1=1
                    AND `GHGVehicleTypeID` = ?
                LIMIT 1";

        $data = $this->db->query($sql, array($GHGVehicleTypeID))->row_array();

        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $dataRow['GHGVehicleTypeID'] = $data['GHGVehicleTypeID'];
        $dataRow['VehicleTypeName'] = $data['VehicleTypeName'];
        $dataRow['FuelConsumption'] = $data['FuelConsumption'];
        $dataRow['StatusCode'] = $data['StatusCode'];

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function InsertVehicleType($paramPost)
    {
        $results = array();
        $this->db->trans_begin();

        $sql = "INSERT INTO `ref_tc_ghg_vehicle_type` SET
                    `GHGVehicleTypeID` = NULL
                    , `VehicleTypeName` = ?
                    , FuelConsumption = ?
                    , `StatusCode` = ?
                    , DateCreated = NOW()
                    , CreatedBy = ?";

        $p = array(
            $paramPost['VehicleTypeName'],
            $paramPost['FuelConsumption'],
            $paramPost['StatusCode'],
            $_SESSION['userid']
        );

        $query = $this->db->query($sql, $p);

        if ($this->db->trans_status() == false) {
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

    public function UpdateVehicleType($paramPost)
    {
        $results = array();
        $this->db->trans_begin();

        $sql = "UPDATE `ref_tc_ghg_vehicle_type` SET
                    `VehicleTypeName` = ?
                    , FuelConsumption = ?
                    , `StatusCode` = ?
                    , DateUpdated = NOW()
                    , LastModifiedBy = ?
                WHERE 1=1
                    AND GHGVehicleTypeID = ?
                LIMIT 1";

        $p = array(
            $paramPost['VehicleTypeName'],
            $paramPost['FuelConsumption'],
            $paramPost['StatusCode'],
            $_SESSION['userid'],
            $paramPost['GHGVehicleTypeID']
        );

        $query = $this->db->query($sql, $p);

        if ($this->db->trans_status() == false) {
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

    function delete_vehicle_type($GHGVehicleTypeID)
    {
        $results = array();
        $this->db->trans_begin();

        $sql = "DELETE FROM ref_tc_ghg_vehicle_type WHERE GHGVehicleTypeID = ? LIMIT 1";
        $query = $this->db->query($sql, array($GHGVehicleTypeID));

        if ($this->db->trans_status() == false) {
            $results['success'] = false;
            $results['message'] = lang("Failed to delete vehicle type");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Vehicle type deleted");
        }

        return $results;
    }
}