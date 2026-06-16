<?php

class Msales extends CI_Model {

    function create($data,$userid,$coopid)
    {
        $this->db->trans_begin();

        $tgl = explode('/', $data['Date']);

        if($data['CustomerTypeID']==1)
        {
            //MEMBER
            //koperasi
            $qcek = $this->db->get_where('ktv_customer',array('MemberID'=>$data['CustomerID']));
            if($qcek->num_rows()>0)
            {
                $rcek = $qcek->row();
                $CustomerID = $rcek->CustomerID;
            } else {
                //get data member coop
                $qMemberCoop = $this->db->get_where('coop_member',array('MemberID'=>$data['CustomerID']))->row();
                $this->db->insert('ktv_customer',array(
                        'MemberID'=>$data['CustomerID'],
                        'Name'=>$qMemberCoop->name
                    ));
                $CustomerID = $this->db->insert_id();
            }
        }

        $this->load->library('jurnal');
        $JournalID = $this->jurnal->sale($data['memo'],$data['totalbayar'],$data['sisabayar'],$coopid,$userid);

        $datas = array(
                'OrgType'=>$data['searchOrgType'],
                'OrgID'=>$data['searchOrgID'],
                // 'CoopID'=>$coopid,
                'JournalID'=>$JournalID,
                'Number'=>rand(111111111,999999999),
                'CustomerID'=>$CustomerID,
                'Date'=>$tgl[2].'-'.$tgl[1].'-'.$tgl[0],
                'Pajak'=>$data['pajak'],
                'Diskon'=>$data['diskon'],
                'Total'=>$data['grandtotal'],
                'Pembayaran'=>$data['totalbayar'],
                'SisaBayar'=>$data['sisabayar']
            );
        $this->db->insert('ktv_sale',$datas);

        $id = $this->db->insert_id();

        $dataGrid = json_decode($data['griditem']);
        // return $data;


        foreach ($dataGrid as $key => $value) {
            // echo $value->InventoryID;
            $this->db->insert('ktv_sale_detail',array(
                'SaleID'=>$id,
                'InventoryID'=>$value->InventoryID,
                'Qty'=>$value->Qty,
                'Price'=>$value->Price
            ));

              //catet riwayatnya di ktv_inventory_stok
            $qGetStok = $this->db->get_where('ktv_inventory',array('InventoryID'=>$value->InventoryID))->row();
            $awal = $qGetStok->Stock == null ? 0 : $qGetStok->Stock;
            $akhir = $awal-$value->Qty;

            $dstok = array(
                  'InventoryID'=>$value->InventoryID,
                  'Type'=>'pembelian',
                  // 'ID` int(11) DEFAULT NULL,
                  'Awal'=>$awal,
                  'Jumlah'=>$value->Qty,
                  'Akhir'=>$akhir,
                  'CreatedBy'=>$userid,
                  'CreatedDate'=>date('Y-m-d')
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
        $sql = "select a.SaleId,a.OrgType,a.JournalID,a.Number,a.Date,a.Pajak,a.Diskon,a.Total,a.Pembayaran,a.SisaBayar,b.name as CustomerName,d.JournalMemo
                                from ktv_sale a
                                join ktv_customer b ON a.CustomerID = b.CustomerID
                                left join coop_member c ON b.MemberID = c.memberID
                                left join accounting_journal d ON d.JournalID = a.JournalID
                                left join coop_member_type e ON c.typeID = e.typeID 
                                where d.CoopID = $coopid";

        if($sd!=null && $nd!=null)
        {
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

        $qs = $this->db->get_where('ktv_sales',array('SaleId'=>$id))->row();

        $this->db->where('JournalID',$qs->JournalID);
        $this->db->delete(array('accounting_journal_detail','accounting_journal'));

        $this->db->where(array('SaleId'=>$id));
        $this->db->delete(array('ktv_sale_detail','ktv_sale'));

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

        $this->db->where('SaleId',$data['SaleID']);
        $this->db->update('ktv_sale',array(
                'SisaBayar'=>$data['sisabayar'],
                'DateUpdated'=>date('Y-m-d H:m:s'),
                'LastModifiedBy'=>$userid
            ));

        $this->db->select('CustomerId');
        $qcek = $this->db->get_where('ktv_sale',array('SaleId'=>$data['SaleID']))->row();

        $this->db->select('Name,CustomerId');
        $qcus = $this->db->get_where('ktv_customer',array('CustomerId'=>$qcek->CustomerId))->row(0);
        $memo = 'Penerimaan Piutang Dari '.$qcus->Name.' ('.$qcus->CustomerId.')';

        $this->load->library('jurnal');
        $JournalID = $this->jurnal->sale_receivable($data['pelunasan'],$userid,$coopid,$memo);

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

    function getPiutangData($coopid,$sd=null,$nd=null)
    {
         $sql = "select a.SaleId,a.OrgType,a.JournalID,a.Number,a.Date,a.Pajak,a.Diskon,a.Total,a.Pembayaran,a.SisaBayar,
                    b.name as CustomerName,c.JournalMemo,b.MemberID 
                    from ktv_sale a 
                    join ktv_customer bb ON a.CustomerID = bb.CustomerID
                    left join coop_member b ON bb.MemberID = b.memberID 
                    left join accounting_journal c ON c.JournalID = a.JournalID
                    left join coop_member_type d ON b.typeID = d.typeID 
                                where d.CoopID = $coopid and a.SisaBayar <> 0";

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

}

?>
