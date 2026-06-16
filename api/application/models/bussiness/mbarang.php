<?php
class Mbarang extends CI_Model
{

    public function readBarangs($sce_id, $start, $limit)
    {
        $sql="SELECT
                SQL_CALC_FOUND_ROWS
                InventoryID,
                ki.OrgType,
                ksov.Name,
                ki.OrgID,
                ki.Name AS NamaBarang,
                Number,
                kic.Name cat,
                Cost,
                SellingPrice,
                Stock
            FROM
                ktv_inventory ki
                LEFT JOIN ktv_inventory_category kic ON kic.CategoryID=ki.CategoryID
                LEFT JOIN ktv_supplychain_org_view ksov ON ksov.OrgID=ki.OrgID AND ksov.OrgID=ki.OrgID
            WHERE
                ki.OrgType = 'sce' AND
                ki.OrgID = ?
            ORDER BY ki.Name ASC
            LIMIT ?,?";
        $query = $this->db->query($sql, array($sce_id,(int) $start,(int) $limit));
        $result['data'] = $query->result_array();

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        return $result;
    }

    public function readBarang($InventoryID)
    {
        $sql="SELECT
                    a.*,
                    GROUP_CONCAT(CONCAT(c.`Number`,' - ',c.`Name`) SEPARATOR ', ') AS ParentName
                FROM
                    ktv_inventory a
                    LEFT JOIN ktv_inventory_paket b ON a.`InventoryID` = b.`ChildInventoryID`
                    LEFT JOIN ktv_inventory c ON b.`InventoryID` = c.`InventoryID`
                WHERE
                    a.InventoryID = ?
                GROUP BY a.`InventoryID`";
        $query          = $this->db->query($sql, array($InventoryID));
        $data           = $query->result_array();
        $result['data'] = $data[0];
        $sql_detail     = "
            select *
            from ktv_inventory_paket
            WHERE InventoryID=?";
        $query            = $this->db->query($sql_detail, array($InventoryID));
        $result['detail'] = $query->result_array();
        return $result;
    }
    public function readBarangBarang($OrgType, $OrgID, $query, $start, $limit)
    {
        $sql = "
            select InventoryID, Name name,Cost,UnitMeasurementID,InventoryID id
            from ktv_inventory
            WHERE OrgType=? and OrgID=? and Name like ?
            limit ?,?";
        $query = $this->db->query($sql, array($OrgType, $OrgID, "%$query%", (int) $start, (int) $limit));
        return $query->result_array();
    }
    public function createBarang($sce_id, $UnitMeasurementID, $Name, $Number, $CategoryID, $SupplierID, $IsSell, $IsPaket, $IsInventory,
        $ParentInventoryID, $ParentConvertion, $Cost, $SellingPrice, $Images) {

        //hilangkan decimal
        $Cost = str_replace(",","",$Cost);
        $SellingPrice = str_replace(",","",$SellingPrice);

        $sql="INSERT INTO `ktv_inventory` SET
              `OrgType` = 'sce',
              `Status` = 'Active',
              `OrgID` = ?,
              `CoopID` = 0,
              `JournalID` = NULL,
              `Number` = ?,
              `SerialNumber` = '',
              `Name` = ?,
              `Description` = '',
              `UnitMeasurementID` = ?,
              `IsInventory` = '0',
              `IsSell` = ?,
              `IsBuy` = NULL,
              `IsRemoved` = '0',
              `RemoveReason` = '',
              `coaIDAsset` = 0,
              `Images` = ?,
              `Cost` = ?,
              `SellingPrice` = ?,
              `UnitMeasure` = '0',
              `SupplierID` = ?,
              SupplierName = '',
              `CategoryID` = ?,
              `IsPaket` = ?,
              `CreatedBy` = ?,
              `CreatedDate` = NOW()
            ";
        $p = array(
            $sce_id,
            $Number,
            $Name,
            $UnitMeasurementID,
            $IsSell,
            $Images,
            $Cost,
            $SellingPrice,
            $SupplierID,
            $CategoryID,
            $IsPaket,
            $_SESSION['userid']
        );
        $query = $this->db->query($sql,$p);

        if ($query) {
            $results['InventoryID'] = $this->db->insert_id();
            $results['success']     = true;
            $results['message']     = "Record created";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    public function updateBarang($UnitMeasurementID,
                $Name,
                $Number,
                $CategoryID,
                $SupplierID,
                $IsSell,
                $IsPaket,
                $IsInventory,
                $Cost,
                $SalePrice,
                $Photo_old,
                $InventoryID) {
        $sql = "
            UPDATE ktv_inventory SET
                UnitMeasurementID=?,
                Name=?,
                Number=?,
                CategoryID=?,
                SupplierID=?,
                IsSell=?,
                IsPaket=?,
                IsInventory=?,
                Cost=?,
                SellingPrice=?,
                Images=?
            WHERE InventoryID=? LIMIT 1";
        $p = array(
            $UnitMeasurementID,
            $Name,
            $Number,
            $CategoryID,
            $SupplierID,
            $IsSell,
            $IsPaket,
            $IsInventory,
            $Cost,
            $SalePrice,
            $Photo_old,
            $InventoryID
        );
        $query = $this->db->query($sql, $p);

        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record Updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    public function deleteBarang($id)
    {
        $this->db->trans_start();
        $sql = "DELETE FROM ktv_inventory_paket WHERE InventoryID=?";
        $query = $this->db->query($sql, array($id));

        //cek apakah ada foto
        $sql="SELECT
                    Images AS foto
                FROM
                    ktv_inventory
                WHERE
                    InventoryID = ?
                LIMIT 1";
        $query = $this->db->query($sql,array($id));
        $dataBarang = $query->row_array();

        $sql   = "DELETE FROM ktv_inventory WHERE InventoryID=?";
        $query = $this->db->query($sql, array($id));
        $this->db->trans_complete();
        if ($this->db->trans_status()) {

            //hapus fotonya
            if($dataBarang['foto'] != ""){
                @unlink('images/Photo/sce/items/' . $dataBarang['foto']);
            }

            $results['success'] = true;
            $results['message'] = "Record deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }
    //detail
    public function readBarangDetail($id)
    {
        /*
        $sql = "
            select ksp.*,concat('[',ki.Number,'] ',ki.Name) name,ksp.Qty*SellingPrice Total,UnitMeasurement dUnitMeasurement,Cost
            from ktv_inventory_paket ksp
            left join ktv_inventory ki on ksp.InventoryID=ki.InventoryID
            WHERE ksp.InventoryID=?";
        */

        $sql="SELECT
                ksp.`PaketID`,
                ksp.`InventoryID`,
                ksp.`ChildInventoryID`,
                ksp.`Qty`,
                CONCAT('[',ki.Number,'] ',ki.Name) AS `name`,
                kiu.`Name` AS dUnitMeasurement,
                ki.`Cost`,
                (ksp.Qty * ki.`SellingPrice`) AS Total
            FROM
                ktv_inventory_paket ksp
                LEFT JOIN ktv_inventory ki ON ksp.`ChildInventoryID` = ki.`InventoryID`
                LEFT JOIN ktv_inventory_unitmeasurement kiu ON ki.`UnitMeasurementID` = kiu.`UnitMeasurementID`
            WHERE
                ksp.InventoryID = ?
            ORDER BY ksp.`PaketID` ASC";
        $query = $this->db->query($sql, array($id));

        return $query->result_array();
    }
    public function createBarangDetail($InventoryID, $ChildInventoryID, $Qty)
    {
        $sql = "
            INSERT INTO ktv_inventory_paket(InventoryID,ChildInventoryID,Qty)
            VALUES (?,?,?)";
        $query = $this->db->query($sql, array($InventoryID, $ChildInventoryID, $Qty));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }
    public function updateBarangDetail($ChildInventoryID, $Qty, $PaketID)
    {
        $sql = "
            UPDATE ktv_inventory_paket
            SET ChildInventoryID=?,Qty=?
            WHERE PaketID=?";
        $query = $this->db->query($sql, array($ChildInventoryID, $Qty, $PaketID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }
    public function deleteBarangDetail($id)
    {
        $sql = "
            DELETE FROM ktv_inventory_paket WHERE PaketID=? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

    public function createBarangSupplierPopup($sce_id, $Name, $Email, $Address, $Phone, $Note, $Provinsi, $Kecamatan, $Kabupaten, $Desa){
        $sql = "
            INSERT INTO ktv_supplier(OrgType,OrgID,Name,Email,Address,Phone,Note,VillageID)
            VALUES ('sce',?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($sce_id, $Name, $Email, $Address, $Phone, $Note, $Desa));
        if ($query) {
            $results['SupplierID'] = (string) $this->db->insert_id();
            $results['success']    = true;
            $results['message']    = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function createBarangSupplier($OrgType, $OrgID, $Name, $Email, $Address, $Phone, $Note, $Provinsi, $Kecamatan,$Kabupaten, $Desa) {
        $sql = "
            INSERT INTO ktv_supplier(OrgType,OrgID,Name,Email,Address,Phone,Note,VillageID)
            VALUES (?,?,?,?,?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType, $OrgID, $Name, $Email, $Address, $Phone, $Note, $Desa));
        if ($query) {
            $results['SupplierID'] = (string) $this->db->insert_id();
            $results['success']    = true;
            $results['message']    = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function createBarangCategoryPopup($sce_id, $Name, $Note){
        $sql = "
            INSERT INTO ktv_inventory_category(OrgType,OrgID,Name,Description)
            VALUES ('sce',?,?,?)";
        $query = $this->db->query($sql, array($sce_id, $Name, $Note));
        if ($query) {
            $results['CategoryID'] = (string) $this->db->insert_id();
            $results['success']    = true;
            $results['message']    = "Record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function createBarangCategory($OrgType, $OrgID, $Name, $Note)
    {
        $sql = "
            INSERT INTO ktv_inventory_category(OrgType,OrgID,Name,Description)
            VALUES (?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType, $OrgID, $Name, $Note));
        if ($query) {
            $results['CategoryID'] = (string) $this->db->insert_id();
            $results['success']    = true;
            $results['message']    = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function getSupplierComboData($sce_id){
        $sql = "
            select SupplierID id,concat('[',SupplierID,'] ',Name) label
            from ktv_supplier
            WHERE OrgType='sce' and OrgID=? ORDER BY `Name`";
        $query = $this->db->query($sql, array($sce_id));
        return $query->result_array();
    }

    public function readSupplier($type, $id)
    {
        $sql = "
            select SupplierID id,concat('[',SupplierID,'] ',Name) label
            from ktv_supplier
            WHERE OrgType=? and OrgID=?";
        $query = $this->db->query($sql, array($type, $id));
        return $query->result_array();
    }

    public function getKategoriComboData($sce_id){
        $sql = "
            select CategoryID id,Name label
            from ktv_inventory_category
            WHERE OrgType='sce' and OrgID=?";
        $query = $this->db->query($sql, array($sce_id));
        return $query->result_array();
    }

    public function readCategory($type, $id)
    {
        $sql = "
            select CategoryID id,Name label
            from ktv_inventory_category
            WHERE OrgType=? and OrgID=?";
        $query = $this->db->query($sql, array($type, $id));
        return $query->result_array();
    }
    public function readUnit($type, $id)
    {
        $sql = "
            select UnitMeasurementID id,Name label
            from ktv_inventory_unitmeasurement
            WHERE OrgType=? and OrgID=?";
        $query = $this->db->query($sql, array($type, $id));
        return $query->result_array();
    }

    public function createBarangUnitPopup($sce_id, $Name, $Note){
        $sql = "
            INSERT INTO ktv_inventory_unitmeasurement(OrgType,OrgID,Name,Description)
            VALUES ('sce',?,?,?)";
        $query = $this->db->query($sql, array($sce_id, $Name, $Note));
        if ($query) {
            $results['UnitMeasurementID'] = (string) $this->db->insert_id();
            $results['success']           = true;
            $results['message']           = "Record created";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function createBarangUnit($OrgType, $OrgID, $Name, $Note)
    {
        $sql = "
            INSERT INTO ktv_inventory_unitmeasurement(OrgType,OrgID,Name,Description)
            VALUES (?,?,?,?)";
        $query = $this->db->query($sql, array($OrgType, $OrgID, $Name, $Note));
        if ($query) {
            $results['UnitMeasurementID'] = (string) $this->db->insert_id();
            $results['success']           = true;
            $results['message']           = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function readBarangComboAutocom($sce_id, $query, $InventoryID,$start,$limit){
        $sql="SELECT
                ki.InventoryID id,
                ki.InventoryID,
                CONCAT('[',ki.Number,'] ',ki.`Name`) label,
                kiu.`Name` AS UnitMeasurement,
                ki.`Cost`
            FROM
                ktv_inventory ki
                LEFT JOIN ktv_inventory_unitmeasurement kiu ON ki.`UnitMeasurementID` = kiu.`UnitMeasurementID`
            WHERE
                ki.OrgType = 'sce' AND ki.OrgID = ? AND
                ki.`Name` LIKE ? AND
                ki.InventoryID != ?
            ORDER BY ki.`Name`
            LIMIT ?,?";
        $query = $this->db->query($sql, array($sce_id,"%$query%",$InventoryID, (int) $start, (int) $limit));
        return $query->result_array();
    }

}
