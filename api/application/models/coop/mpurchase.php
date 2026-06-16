<?php

class Mpurchase extends CI_Model {

    function create($data,$userid,$coopid)
    {
        $this->db->trans_begin();

        $tgl = explode('/', $data['Date']);

        $this->load->library('jurnal');
        $JournalID = $this->jurnal->purchase($data['memo'],$data['grandtotal'],$data['totalbayar'],$data['sisabayar'],$coopid,$userid);

        if($data['typeBayarRb']==2)
        {
            //kredit
            if(isset($data['duedate']))
            {
                $arr = explode('/', $data['duedate']);
                $duedate = $arr[2].'-'.$arr[1].'-'.$arr[0];
            } else {
                $duedate = null;
            }
        } else {
             $duedate = null;
        }
        

        $datas = array(
                'OrgType'=>$data['OrgType'],
                'OrgID'=>$data['OrgID'],
                'JournalID'=>$JournalID,
                'DueDate'=>$duedate,
                'Number'=>rand(111111111,999999999),                
                'Date'=>$tgl[2].'-'.$tgl[1].'-'.$tgl[0],
                'Pajak'=>$data['pajak'],
                'Diskon'=>$data['diskon'],
                'Total'=>$data['grandtotal'],
                'Pembayaran'=>$data['totalbayar'],
                'SisaBayar'=>$data['sisabayar']
            );

        if($data['SupplierID'])
        {
            $datas['SupplierID'] = $data['SupplierID'];
        }

        if(intval($data['sisabayar'])>0)
        {
            $data['TipeBayar'] = 2; //kredit
        } else {
            $data['TipeBayar'] = 1; //tunai
        }
        $this->db->insert('ktv_purchase',$datas);

        // return $datas;
        $id = $this->db->insert_id();

        $dataGrid = json_decode($data['griditem']);
        // return $data;


        foreach ($dataGrid as $key => $value) {
            // echo $value->InventoryID;
            $this->db->insert('ktv_purchase_detail',array(
                'PurchaseId'=>$id,
                'InventoryID'=>$value->InventoryID,
                'Qty'=>$value->Qty,
                'Price'=>$value->Price
            ));

            //catet riwayatnya di ktv_inventory_stok
            $qGetStok = $this->db->get_where('ktv_inventory',array('InventoryID'=>$value->InventoryID))->row();
            $awal = $qGetStok->Stock == null ? 0 : $qGetStok->Stock;
            $akhir = $awal+$value->Qty;

            $dstok = array(
                  'InventoryID'=>$value->InventoryID,
                  'Type'=>'pembelian',
                  // 'ID` int(11) DEFAULT NULL,
                  'Awal'=>$awal,
                  'Jumlah'=>$value->Qty,
                  'Akhir'=>$akhir,
                  'CreatedBy'=>$userid,
                  'CreatedDate'=>date('Y-m-d'),
                  'SyncedDate'=>null
                );

            //update stock
            $this->db->where('InventoryID',$value->InventoryID);
            $this->db->update('ktv_inventory',array('Stock'=>$akhir));
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

        return $results;
    }

    function getData($coopid,$sd=null,$nd=null)
    {
        $sql = "select a.PurchaseID,a.OrgType,a.OrgID,a.JournalID,a.Number,a.Date,a.Pajak,a.Diskon,a.Total,a.Pembayaran,a.SisaBayar,
        b.Name as SupplierName,c.JournalMemo,a.SupplierID
                from ktv_purchase a
                left join ktv_supplier b ON a.SupplierID = b.SupplierID
                left join accounting_journal c ON c.JournalID = a.JournalID
                where a.OrgID = $coopid";

        if($sd!=null && $nd!=null)
        {
            // $d1 = explode('-', $sd);
            // $d2 = explode('-', $nd);
            $sql.=" and a.Date between '".$sd."' and '".$nd."'";
        }

        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        $result['total'] = $query->num_rows();
        return $result;
    }

    function deleteData($id)
    {
         $this->db->trans_begin();

        $qs = $this->db->get_where('ktv_purchase',array('PurchaseId'=>$id))->row();

        $this->db->where('JournalID',$qs->JournalID);
        $this->db->delete(array('accounting_journal_detail','accounting_journal'));

        $this->db->where(array('PurchaseId'=>$id));
        $this->db->delete(array('ktv_purchase_detail','ktv_purchase'));

         if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed deleting data";
        }
        else
        {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success deleting data";
        }

        return $results;
    }

    function pelunasan($data,$userid,$coopid)
    {
        $this->db->trans_begin();

        $this->db->where('PurchaseID',$data['PurchaseID']);
        $this->db->update('ktv_purchase',array('SisaBayar'=>$data['sisabayar'],'SyncedDate'=>NULL));

        $q = $this->db->query("select a.SupplierID,b.Name
                                from ktv_purchase a
                                join ktv_supplier b ON a.SupplierID = b.SupplierID
                                where PurchaseID = ".$data['PurchaseID']."")->row();

        $memo = 'Pembayaran Hutang Kepada '.$q->Name.' ('.$q->SupplierID.')';

        $this->load->library('jurnal');
        $JournalID = $this->jurnal->purchase_payable($data,$userid,$coopid,$memo);
        // $qP = $this->db->get_where('ktv_purchase',array('PurchaseID'=>));

         if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed updating data";
        }
        else
        {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success updating data";
        }

        return $results;
    }

    function getHutangData($coopid,$sd=null,$nd=null)
    {
        $sql = "select a.PurchaseID,a.OrgType,a.JournalID,a.Number,a.Date,a.Pajak,a.Diskon,a.Total,a.Pembayaran,a.SisaBayar,a.DueDate,
        b.Name as SupplierName,c.JournalMemo,a.SupplierID
                                from ktv_purchase a
                                left join ktv_supplier b ON a.SupplierID = b.SupplierID
                                left join accounting_journal c ON c.JournalID = a.JournalID
                                where a.OrgID = $coopid and a.SisaBayar <> 0";

        if($sd!=null && $nd!=null)
        {
            // $d1 = explode('-', $sd);
            // $d2 = explode('-', $nd);
            $sql.=" and a.Date between '".$sd."' and '".$nd."'";
        }

        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        $result['total'] = $query->num_rows();
        return $result;
    }

    function getDataItem($PurchaseID)
    {
        $sql = "select a.DetailId,a.PurchaseId,a.InventoryID,a.Qty,a.Price,b.Name as name,sum(a.Price*a.Qty) as Total
                from ktv_purchase_detail a
                join ktv_inventory b ON a.InventoryID = b.InventoryID
                where PurchaseId = $PurchaseID
                group by a.DetailId";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();

        $result['total'] = $query->num_rows();
        return $result;
    }
}

?>
