<?php

class Minventory extends CI_Model {

    function createInvCategory($namecat,$Description,$SellCoaID,$BuyCoaID,$userid)
    {
        $sql_cek = "select CoopID from ktv_cooperative_staff where UserId=?";
        $query = $this->db->query($sql_cek, array($userid));
        $r = $query->row();

        $data = array(
            'Name' => $namecat,
            'SellCoaID' => $SellCoaID,
            'BuyCoaID' => $BuyCoaID,
            // 'CoopID' => $r->CoopID,
            'Description' => $Description,
            'CreatedBy' => $userid,
            'CreatedDate' => date('Y-m-d'),
        );

        $query = $this->db->insert('ktv_inventory_category', $data);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateInvCategory($id,$namecat,$Description,$SellCoaID,$BuyCoaID,$userid)
    {
        $data = array(
            'Name' => $namecat,
            'SellCoaID' => $SellCoaID,
            'BuyCoaID' => $BuyCoaID,
            'Description' => $Description,
            'UpdatedBy' => $userid,
            'UpdatedDate' => date('Y-m-d'),
        );

        $this->db->where('CategoryID',$id);
        $query = $this->db->update('ktv_inventory_category', $data);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed updating record";
        }
        return $results;
    }

    function deleteInvCategoryData($id)
    {
        $this->db->where('CategoryID',$id);
        $query = $this->db->delete('ktv_inventory_category');
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to deleting record";
        }
        return $results;
    }

    function readDataInvCategories($start, $limit,$key)
    {
          $sql = "select %s
            from ktv_inventory_category a";

         if(strlen($key) > 0){
            $sql .= " WHERE a.Name LIKE ?";
        }

        $query = $this->db->query(sprintf($sql, 'CategoryID as id, Name as namecat, Description', null, 'LIMIT ?,?'), array("%$key%", (int) $start, (int) $limit));

        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(a.CategoryID) as total', null, ''), array("%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readInvCategoryData($id)
    {
        $sql = "select CategoryID as id, Name as namecat, Description, b.CoaID as SellCoaID,b.CoaTitle as CoaTitleSell,b.CoaCode as SellCoaCode,
        c.CoaID as BuyCoaID,c.CoaTitle as CoaTitleBuy,c.CoaCode as BuyCoaCode
            from ktv_inventory_category a
            left join accounting_coa b ON a.SellCoaID = b.CoaID
            left join accounting_coa c ON a.BuyCoaID = c.CoaID
            WHERE CategoryID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
//        return $result[0];
        if ($query) {

            $results['success'] = true;
            $results['data'] = $result[0];
        } else {
            $results['success'] = false;
            $results['data'] = $result[0];
        }
        return $results;
    }

    function createSupplier($code, $namesupplier, $telephone, $fax, $email, $companyaddress, $city, $country, $userid) {
        // $sql_cek = "select CoopID from ktv_cooperative_staff where UserId=?";
        // $query = $this->db->query($sql_cek, array($userid));
        // $r = $query->row();

        $data = array(
            'code' => $code,
            // 'CoopID' => $r->CoopID,
            'namesupplier' => $namesupplier,
            'telephone' => $telephone,
            'fax' => $fax,
            'email' => $email,
            'companyaddress' => $companyaddress,
            'city' => $city,
            'country' => $country,
            'CreatedBy' => $userid,
            'CreatedDate' => date('Y-m-d'),
        );

        $query = $this->db->insert('coop_supplier', $data);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateSupplier($id,$code, $namesupplier, $telephone, $fax, $email, $companyaddress, $city, $country, $userid) {
        $data = array(
            'code' => $code,
            'namesupplier' => $namesupplier,
            'telephone' => $telephone,
            'fax' => $fax,
            'email' => $email,
            'companyaddress' => $companyaddress,
            'city' => $city,
            'country' => $country,
            'UpdatedBy' => $userid,
            'UpdatedDate' => date('Y-m-d')
        );

        $this->db->where('SupplierID',$id);
        $query = $this->db->update('coop_supplier', $data);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed updating record";
        }
        return $results;
    }

    function readSupplierData($id)
    {
         $sql = "select SupplierID as id, code, namesupplier, companyaddress, companyaddress2, telephone, fax, city, email,country
            from coop_supplier a
            WHERE SupplierID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
//        return $result[0];
        if ($query) {

            $results['success'] = true;
            $results['data'] = $result[0];
        } else {
            $results['success'] = false;
            $results['data'] = $result[0];
        }
        return $results;
    }

    function readDataSuppliers($start, $limit, $OrgType=null, $OrgID=null) {
        $sql = "select %s
            from ktv_supplier a";

        $query = $this->db->query(sprintf($sql, 'SupplierID as id, OrgType, OrgID, Name, Address, Phone, Email, VillageID, Note', 'LIMIT ?,?'), array((int) $start, (int) $limit));

        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(a.SupplierID) as total', null, ''));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function insertData($Number, $Name=null, $catinventoryid=null, $Description=null, $cbdijual=null, $cbdibeli=null, $cbpersediaan=null, $nonaktif=null, $incomeaccount=null, $sellingprice=null, $taxbuyid=null, $UnitMeasuresell=null, $cosaccount=null, $Cost=null, $UnitMeasure=null, $nametaxbuy=null, $supplierid=null, $datebuy=null, $Stock=null, $residu=null, $umur=null, $akumulasibeban=null, $bebanberjalan=null, $nilaibuku=null, $bebanperbulan=null, $akumulasiakhir=null, $userid=null, $Images=null, $coaIDAsset=null, $coaIDAkumDepres=null, $coaIDBebanDepres=null,$SerialNumber,$SupplierName=null,$IsRemoved=null,$RemoveReason=null,$EvaluateType=null,$EvaluateSoldPrice=null,$Location=null,$Status=null,$EvaluateReason=null) {

        $d = $datebuy!=null ? explode('-', $datebuy) : array(0=>null,1=>null, 2=>null);

        $q = $this->db->get_where('ktv_cooperative_staff', array('UserId' => $userid))->row();

        $datebuy = isset($datebuy) ? $datebuy : null;

         $data = array(
            'Number' => $Number,
            'Name' => $Name,
            'Description' => $Description,
            // 'isinventory' => $cbpersediaan == 'on' ? true : false,
            // 'issell' => $cbdijual == 'on' ? true : false,
            // 'isbuy' => $cbdibeli == 'on' ? true : false,
            'coaIDAsset' => $coaIDAsset,
            'coaIDAkumDepres' => $coaIDAkumDepres,
            'coaIDBebanDepres' => $coaIDBebanDepres,
            'Stock' => $Stock,
            'Images' => $Images,
            'Cost' => $Cost,
            'UnitMeasure' => $UnitMeasure,
//            'numperunit'=>$idjournal,
//            'minstock'=>$idjournal,
//            'idprimarysupplier'=>$supplierid,
//            'sellingprice' => $sellingprice,
//            'SelingTax' => $taxid,
            // 'UnitMeasuresell' => $UnitMeasuresell,
//            'numperunitsell'=>$idjournal,
            // 'notes' => $Description,
            'YearBuy' => $d[0],
            'MonthBuy' => $d[1],
            'DateBuy' => $datebuy,
            'CategoryID' => $catinventoryid,
            // 'SupplierID' => $supplierid,
            // 'BuyTax' => $taxbuyid,
//            'idunit'=>$idjournal,
            'Residu' => $residu,
            'Umur' => $umur,
            'AkumulasiBeban' => $akumulasibeban,
            'BebanBerjalan' => $bebanberjalan,
            'NilaiBuku' => $nilaibuku,
            'BebanPerBulan' => $bebanperbulan,
            'AkumulasiAkhir' => $akumulasiakhir,
            'SerialNumber' => $SerialNumber,
            'SupplierName' => $SupplierName,
            'IsRemoved' => $IsRemoved,
            'RemoveReason' => $RemoveReason,
            'Location'=>$Location,
            'CreatedDate' => date('Y-m-d'),
            'CoopID' => $q->CoopID,
            'Status'=>$Status,
            'EvaluateReason'=>$EvaluateReason
        );
        $query = $this->db->insert('ktv_inventory', $data);
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function editData($InventoryID, $Number, $Name=null, $catinventoryid=null, $Description=null, $cbdijual=null, $cbdibeli=null, $cbpersediaan=null, $nonaktif=null, $incomeaccount=null, $sellingprice=null, $taxbuyid=null, $UnitMeasuresell=null, $cosaccount=null, $Cost=null, $UnitMeasure=null, $nametaxbuy=null, $supplierid=null, $datebuy=null, $Stock=null, $residu=null, $umur=null, $akumulasibeban=null, $bebanberjalan=null, $nilaibuku=null, $bebanperbulan=null, $akumulasiakhir=null, $userid=null, $Images=null, $coaIDAsset=null, $coaIDAkumDepres=null, $coaIDBebanDepres=null,$SerialNumber,$SupplierName=null,$IsRemoved=null,$RemoveReason=null,$EvaluateType=null,$EvaluateSoldPrice=null,$Location=null,$reEvaluasiBtnOpt=null,$Status=null,$EvaluateReason=null) {

        $d = $datebuy!=null ? explode('-', $datebuy) : array(0=>null,1=>null, 2=>null);

        $datebuy = $datebuy!=null ? $datebuy : null;

        $data = array(
            'Number' => $Number,
            'Name' => $Name,
            'Description' => $Description,
            // 'isinventory' => $cbpersediaan == 'on' ? true : false,
            // 'issell' => $cbdijual == 'on' ? true : false,
            // 'isbuy' => $cbdibeli == 'on' ? true : false,
            'coaIDAsset' => $coaIDAsset,
            'coaIDAkumDepres' => $coaIDAkumDepres,
            'coaIDBebanDepres' => $coaIDBebanDepres,
            'Stock' => $Stock,
            'Cost' => $Cost!=null ? $this->clearNumber($Cost) : null,
            'UnitMeasure' => $UnitMeasure,
            // 'sellingprice' => $sellingprice!=null ? $this->clearNumber($sellingprice) : null,
            // 'UnitMeasuresell' => $UnitMeasuresell,
            // 'notes' => $Description,
            'YearBuy' => $d[0],
            'MonthBuy' => $d[1],
            'DateBuy' => $datebuy,
            'CategoryID' => $catinventoryid,
            // 'SupplierID' => $supplierid,
            // 'BuyTax' => $taxbuyid,
            'Residu' => $residu,
            'Umur' => $umur,
            'AkumulasiBeban' => $akumulasibeban!=null ? $this->clearNumber($akumulasibeban) : null,
            'BebanBerjalan' => $bebanberjalan!=null ? $this->clearNumber($bebanberjalan) : null,
            'NilaiBuku' => $nilaibuku!=null ? $this->clearNumber($nilaibuku) : null,
            'BebanPerBulan' => $bebanperbulan!=null ? $this->clearNumber($bebanperbulan) : null,
            'AkumulasiAkhir' => $akumulasiakhir!=null ? $this->clearNumber($akumulasiakhir) : null,
            'SerialNumber' => $SerialNumber,
            'SupplierName' => $SupplierName,
            'IsRemoved' => $IsRemoved,
            'RemoveReason' => $RemoveReason,
            'EvaluateType' => $reEvaluasiBtnOpt==1 ? $EvaluateType : null,
            'EvaluateSoldPrice' => $this->clearNumber($EvaluateSoldPrice),
            'Location'=>$Location,
            'UpdatedBy' => $userid,
            'UpdatedDate' => date('Y-m-d'),
            'Status'=>$Status,
            'EvaluateReason'=>$EvaluateReason,
            'SyncedDate'=>null
        );


        if ($Images != null) {
            $data['Images'] = $Images;
        }

        $this->db->where('InventoryID', $InventoryID);
        $query = $this->db->update('ktv_inventory', $data);
        $query = true;
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to updating record";
        }
        return $results;
    }

    function clearNumber($n)
    {
        return str_replace(',', '', $n);
    }

    function deleteInventoryData($id)
    {
        $this->db->where('InventoryID', $id);
        $query = $this->db->delete('ktv_inventory');
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to deleting record";
        }
        return $results;
    }

    function readDatas($start, $limit, $userid, $Status=null) {
        $add = null;

        $sql = "select %s
            from ktv_inventory a";

        $sql_cek = "select CoopID from ktv_cooperative_staff where UserId=?";
        $query = $this->db->query($sql_cek, array($userid));
        $cek = $query->result_array();
        if ($Status!=null)
            {
                $sql .= " WHERE a.Status='" . $Status."'";

            } else {
                $sql .= " WHERE a.Status='Active'";
            }

        $query = $this->db->query(sprintf($sql, 'a.InventoryID,a.Number,a.Name,a.Description,a.isinventory,a.issell,a.isbuy,a.coaIDAsset,a.coaIDAkumDepres,a.coaIDBebanDepres,a.Stock,
            a.Images,a.Cost,a.UnitMeasure,a.sellingprice,a.SelingTax,a.notes,a.datebuy,a.CategoryID,a.SupplierID,
            a.BuyTax,a.residu,a.umur,a.akumulasibeban,a.bebanberjalan,a.nilaibuku,a.bebanperbulan,a.akumulasiakhir', $add, 'LIMIT ?,?'), array((int) $start, (int) $limit));
        // echo $this->db->last_query();
        // exit;
        if($query->num_rows()>0)
        {
            $result['data'] = $query->result_array();

            $query = $this->db->query(sprintf($sql, 'count(a.InventoryID) as total', $add, ''));
            $result['total'] = $query->row()->total;
        } else {
            $result['data'] = null;
            $result['total'] = 0;
        }

        return $result;
    }

    function readData($id) {
        $sql = "
            select a.InventoryID,a.Number,a.Name,a.Description,a.isinventory,a.issell,a.isbuy,a.coaIDAsset,a.coaIDAkumDepres,a.coaIDBebanDepres,a.Stock,
            a.Images,a.Cost,a.UnitMeasure,a.sellingprice,a.SelingTax,a.notes,a.datebuy,a.CategoryID,a.SupplierID,
            a.BuyTax,a.residu,a.umur,a.akumulasibeban,a.bebanberjalan,a.nilaibuku,a.bebanperbulan,a.akumulasiakhir,
            b.coaTitle as coaNameAsset,c.coaTitle as coaNameAkumDepres,d.coaTitle as coaNameBebanDepres,a.Images,SerialNumber,Location,SupplierName,Umur,IsRemoved,RemoveReason,Location,EvaluateType,EvaluateSoldPrice,a.Status,a.EvaluateReason,a.EvaluateSoldPrice
            from ktv_inventory a
            left join accounting_coa b ON a.coaIDAsset = b.coaID
            left join accounting_coa c ON a.coaIDAkumDepres = c.coaID
            left join accounting_coa d ON a.coaIDBebanDepres = d.coaID
            WHERE InventoryID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
//        return $result[0];
        if ($query) {
            $this->config->load('coop');

            $results['success'] = true;
            $results['data'] = $result[0];

            if($result[0]['Images']==null)
            {
                $Images = null;
            } else {
                $Images = base_url() . '' . str_replace('./', '', $this->config->item('inventory_image_dir')) . '/' . $result[0]['Images'];
            }

            $results['data']['fotoinventory'] = $Images;
        } else {
            $results['success'] = false;
            $results['data'] = $result[0];
        }
        return $results;
    }

    function deleteSupplierData($id)
    {
        $this->db->where('SupplierID',$id);
        $query = $this->db->delete('coop_supplier');
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to deleting record";
        }
        return $results;
    }

    function getOpnameItem($CoopID)
    {
        $q = $this->db->query("select a.InventoryID,a.Number,a.Name,
                                case when Stock is null then 0
                                else Stock end as Stock,0 as CheckedStock
                                from ktv_inventory a
                                where CoopID = $CoopID");
        return $q->result_array();
    }

    function insertOpname($data,$CoopID,$userid)
    {
        $this->db->trans_begin();

        $item = json_decode($data['data']);
        $dt = explode('-', $data['periode']);

        $d = array(
                'Periode'=>$dt[2].'-'.$dt[1].'-'.$dt[1],
                'Notes'=>$data['notes'],
                'CreatedBy'=>$userid,
                'CreatedDate'=>date('Y-m-d H:m:s')
        );
        $this->db->insert('coop_stock_opname',$d);
        $id = $this->db->insert_id();

        foreach ($item as $key => $value) {
           $dif = $value->Stock-$value->CheckedStock;
           if($dif<0) {
            $dif = $value->CheckedStock-$value->Stock;
           }

           $di = array(
                'OpnameID' => $id,
                'InventoryID' => $value->InventoryID,
                'ActualStock' => $value->Stock,
                'CheckedStock' => $value->CheckedStock,
                'Difference' => $dif
            );
           $this->db->insert('coop_stock_opname_items',$di);

           //update stok di ktv_inventory
           $this->db->where('InventoryID',$value->InventoryID);
           $this->db->update('ktv_inventory',array('Stock'=>$value->CheckedStock));
        }       

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed inserting data";          
        }
        else
        {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success inserting data";  
        }
        return false;
    }

    function dataOpname($CoopID)
    {
        $sql = "select a.OpnameID,a.Periode,a.Notes,a.CreatedDate,TotalAS,TotalCS,(TotalAS-TotalCS) as Difference
                from coop_stock_opname a
                join (select OpnameID,sum(ActualStock) as TotalAS,sum(CheckedStock) as TotalCS
                    from coop_stock_opname_items
                    group by OpnameID) b ON a.OpnameID = b.OpnameID";
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
            $result['data'] = $query->result_array();
            $result['total'] = $query->num_rows();
        } else {
            $result['data'] = null;
            $result['total'] = 0;
        }
        return $result;
    }

}

?>
