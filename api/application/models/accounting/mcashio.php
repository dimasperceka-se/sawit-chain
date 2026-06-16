<?php

class Mcashio extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getData($start = 0,$limit = 20,$sort = 'journalDate',$dir = 'DESC', $query = array()) {
        $this->db->select(array(
            'journalID',
            'journalTypeCode',
            'journalDate',
            'journalMemo',
            'journalIsPosted',
            'journalPostedDate',
            '(SELECT SUM(journalDetailSum) FROM accounting_journal_detail WHERE journalID = accounting_journal.journalID AND journalDetailType = 2) AS KREDIT',
            '(SELECT SUM(journalDetailSum) FROM accounting_journal_detail WHERE journalID = accounting_journal.journalID AND journalDetailType = 1) AS DEBET'),
            FALSE
        );
        $this->db->from('accounting_journal');
        
        //apply data filters
        if(count($query) > 0){
            if(array_key_exists('journalTypeCode', $query) && strlen($query['journalTypeCode']) > 0){
                $this->db->where('journalTypeCode',$query['journalTypeCode']);
            }
            
            if(array_key_exists('journalIsPosted', $query) && strlen($query['journalIsPosted']) > 0){
                $this->db->where('journalIsPosted',$query['journalIsPosted']);
            }
            
            if(array_key_exists('journalStart', $query) && strlen($query['journalStart']) > 0 && array_key_exists('journalEnd', $query) && strlen($query['journalEnd']) > 0){
                $this->db->where('(journalDate BETWEEN "'.$query['journalStart'].'" AND "'.$query['journalEnd'].'")',NULL,FALSE);
            }
        }
        
        $total = $this->db->_compile_select();
        
        $total = $this->db->query($total)->num_rows();
        
        $this->db->limit($limit,$start);
        $this->db->order_by($sort,$dir);
        
        $Q = $this->db->get();
        
        if($total){
            return array('data' => $Q->result_array(), 'total' => $total);
        }
        
        return array('data' => array(), 'total' => 0);
    }
    
    /**
     * fungsi untuk generate General Ledger mulai dari sini
     */
    
    public function get_full_code_code_tree($coa){
        $query = "SELECT t2.coaCode as lev1, t3.coaCode as lev2, t4.coaCode as lev3
                    FROM accounting_coa AS t1
                    LEFT JOIN accounting_coa AS t2 ON t2.coaCodeParent = t1.coaCode
                    LEFT JOIN accounting_coa AS t3 ON t3.coaCodeParent = t2.coaCode
                    LEFT JOIN accounting_coa AS t4 ON t4.coaCodeParent = t3.coaCode
                    WHERE t1.coaCode = '$coa'";
        $q = $this->db->query($query);
        $record[] = $coa;
        foreach($q->result_array() as $row) {
                $record[] = $row['lev1'];
                $record[] = $row['lev2'];
                $record[] = $row['lev3'];
        }
        if($q->result_array()){
            return $record;
        }else{
            return;
        }

    }

    public function getGLBalance($start_date,$end_date,$coa,$status = 3)
    {
        if($status == '1'){
            $post = 'v_coa.journalIsPosted=1 AND ';
        } elseif($status == '2'){
            $post = 'v_coa.journalIsPosted=0 AND ';
        } else {
            $post = '';
        }
        $subquery1 = '';
        $subquery2 = "WHERE cb.journalClosedID=(SELECT journalClosedID 
                                FROM accounting_journal_closed 
                                WHERE journalClosedDate < '".$start_date."' 
                                ORDER BY journalClosedDate DESC LIMIT 0,1)
                                ";
        $single = false;
        if($coa)
        {
            $ss = $this->get_full_code_code_tree($coa);
            foreach ($ss as $key => $link)
            {
                if ($ss[$key] == '')
                {
                    unset($ss[$key]);
                }
            }
            if(count($ss) > 1){
                $single = true;
            }
            $ss = implode('","', $ss);
            $ss = '"'.$ss.'"';
            
            $sub1 = "accounting_coa.coaCode in (".$ss.") ";
            $sub2 = "AND c.coaCode in (".$ss.")  ";

            $subquery1 .= $sub1." AND ";
            $subquery2 .= " ".$sub2."" ;
        }

            $query = "
            SELECT x.*,c.coaCodeParent,
            if((x.coaCode!=''),x.coaCode,c.coaCode) AS COACODE,
            if((x.coaTitle!=''),x.coaTitle,c.coaTitle) AS COATITLE,
            if((x.coaGroupTitle!=''),x.coaGroupTitle,cg.coaGroupTitle) AS COAGROUPTITLE,
            if((x.coaClassTitle!=''),x.coaClassTitle,cc.coaClassTitle) AS COACLASSTITLE,
            if((x.coaClassType!=''),x.coaClassType,cc.coaClassType) AS COACLASSTYPE,
            if((x.coaType!=''),x.coaType,c.coaType) AS COA_TYPE,
            if((x.coaGroupCode!=''),x.coaGroupCode,cg.coaGroupCode) AS COAGROUPCODE
            FROM accounting_coa c
            LEFT JOIN 
            (SELECT v_coa.*
            FROM v_coa
            WHERE ".$post."v_coa.coaCode IN (SELECT accounting_coa.coaCode as coaCode 
            FROM accounting_coa
            LEFT JOIN accounting_coa_balance ON accounting_coa_balance.coaCode=accounting_coa.coaCode 
            WHERE ".$subquery1." accounting_coa_balance.journalClosedID = (SELECT journalClosedID 
            FROM accounting_journal_closed 
            WHERE journalClosedDate < '".$start_date."' 
            ORDER BY journalClosedDate DESC LIMIT 0,1))
            AND DATE_FORMAT(journalDate,'%Y-%m-%d') >= '".$start_date."' 
            AND DATE_FORMAT(journalDate,'%Y-%m-%d') <= '".$end_date."') x ON x.coaCode=c.coaCode 
            LEFT JOIN accounting_coa_balance cb ON cb.coaCode=c.coaCode
            LEFT JOIN accounting_coa_group cg ON cg.coaGroupCode=c.coaGroupCode	
            LEFT JOIN accounting_coa_class cc ON cc.coaClassID=cg.coaClassID	
            ".$subquery2."
            ORDER BY DATE_FORMAT(journalDate, '%Y-%m-%d') ASC, COACODE ASC, c.coaTitle ASC
            ";
            $query = $this->db->query($query);
            
            $data = $query->result_array();


            $Q2 = $this->db->query("
                                SELECT x.*,cb.coaBalanceAmount,c.coaCodeParent,
        if((x.coaCode!=''),x.coaCode,c.coaCode) AS COACODE, 
        if((x.coaTitle!=''),x.coaTitle,c.coaTitle) AS COATITLE,
        if((x.coaGroupTitle!=''),x.coaGroupTitle,cg.coaGroupTitle) AS COAGROUPTITLE,
        if((x.coaClassTitle!=''),x.coaClassTitle,cc.coaClassTitle) AS COACLASSTITLE,
        if((x.coaClassType!=''),x.coaClassType,cc.coaClassType) AS COACLASSTYPE,
        if((x.coaType!=''),x.coaType,c.coaType) AS COA_TYPE,
        if((x.coaGroupCode!=''),x.coaGroupCode,cg.coaGroupCode) AS COAGROUPCODE
        FROM accounting_coa c
        LEFT JOIN 
        (SELECT v_coa.*, SUM(DEBIT) AS TOTAL_DEBIT, SUM(CREDIT) AS TOTAL_CREDIT
        FROM v_coa
        WHERE ".$post."v_coa.coaCode IN (SELECT accounting_coa.coaCode as coaCode 
        FROM accounting_coa 
        LEFT JOIN accounting_coa_balance ON accounting_coa_balance.coaCode=accounting_coa.coaCode 
        WHERE ".$subquery1." accounting_coa_balance.journalClosedID = (SELECT journalClosedID 
        FROM accounting_journal_closed 
        WHERE journalClosedDate < '2010-02-01' 
        ORDER BY journalClosedDate DESC LIMIT 0,1))
        AND DATE_FORMAT(journalDate,'%Y-%m-%d') > (SELECT journalClosedDate
        FROM accounting_journal_closed
        WHERE journalClosedDate < '".$start_date."'
        ORDER BY journalClosedDate DESC
        LIMIT 0,1) 
        AND DATE_FORMAT(journalDate,'%Y-%m-%d') < '".$start_date."'
        GROUP BY v_coa.coaCode) x ON x.coaCode=c.coaCode 
        LEFT JOIN accounting_coa_balance cb ON cb.coaCode=c.coaCode
        LEFT JOIN accounting_coa_group cg ON cg.coaGroupCode=c.coaGroupCode	
        LEFT JOIN accounting_coa_class cc ON cc.coaClassID=cg.coaClassID	
        ".$subquery2."
        ORDER BY DATE_FORMAT(journalDate, '%Y-%m-%d') ASC, COACODE ASC, c.coaTitle ASC");						 

        $data2 = $Q2->result_array();
        if($coa){
            if($single){
                $i=0;
                foreach($data2 as $row2){
                        if(empty($row2['coaCodeParent'])){
                                foreach($data as $row){
                                        if($row2['COACODE']==$row['COACODE'] or $row2['COACODE']==$row['coaCodeParent']){
                                                $row2['data'][$i++]=$row;
                                        }
                                }
                                $data1[]=$row2;				
                        }
                }
            }else{
                $i=0;
                foreach($data2 as $row2){
                        foreach($data as $row){
                                if($row2['COACODE']==$row['COACODE']){
                                        $row2['data'][$i++]=$row; 
                                        //$y[]=$row2['data'];
                                }
                        }
                        $data1[]=$row2;
                }
            }
        }else{
                $i=0;
                foreach($data2 as $row2){
                        if(empty($row2['coaCodeParent'])){
                                foreach($data as $row){
                                        if($row2['COACODE']==$row['COACODE'] or $row2['COACODE']==$row['coaCodeParent']){
                                                $row2['data'][$i++]=$row;
                                        }
                                }
                                $data1[]=$row2;				
                        }
                }
        }
        return $data1;
    }

    public function get_coa_code($start_date,$end_date,$coa)
    {
        
        $query = "SELECT accounting_coa.coaCode as coaCode, coaTitle, coaBalanceAmount, accounting_coa_balance.journalClosedID,accounting_journal_closed.journalClosedDate, if(coaType=1,'DEBIT','CREDIT') AS coaType
                            FROM accounting_coa
                                    LEFT JOIN accounting_coa_balance ON accounting_coa_balance.coaCode=accounting_coa.coaCode
                                    LEFT JOIN accounting_journal_closed ON accounting_journal_closed.journalClosedID=accounting_coa_balance.journalClosedID
                                    WHERE ";
        if($coa)
        {
                $query .= "accounting_coa.coaCode = '".$coa."' AND ";
        }

        $query .= " accounting_coa_balance.journalClosedID = (SELECT journalClosedID
                    FROM accounting_journal_closed
                    WHERE journalClosedDate < '".$start_date."'
                    ORDER BY journalClosedDate DESC
                    LIMIT 0,1)"; 
        
        
        $query = $this->db->query($query);
        
        if($query->num_rows()){
            $data = $query->result_array();
            return $data;
        }
        
        return array();
    }

    public function get_coa_code_balance($start_date,$end_date,$coa_start,$coa_end)
    {
            $this->db->select('journalClosedID');
            $this->db->where('journalClosedDate < ',$start_date);
            $this->db->order_by('journalClosedDate','DESC');
            $this->db->limit(1);
            $q = $this->db->get('accounting_journal_closed');
            $Q = $q->result_array();
            if(!$Q){
                    $this->db->select('journalClosedID');
                    $this->db->order_by('journalClosedDate','ASC');
                    $this->db->limit(1);
                    $q = $this->db->get('accounting_journal_closed');
                    $Q = $q->result_array();
            }
            $this->db->select('accounting_coa.coaCode as coaCode,coaTitle,coaBalanceAmount,coaClassType');
            $this->db->join('accounting_coa_balance','accounting_coa_balance.coaCode=accounting_coa.coaCode','left');
            $this->db->join('accounting_coa_group','accounting_coa_group.COA_GROUP_CODE=accounting_coa.coaGroupCode','left');
            $this->db->join('accounting_coa_class','accounting_coa_class.coaClassID=accounting_coa_group.coaClassID','left');
            if($Q){
                    $this->db->where('journalClosedID',$Q[0]['journalClosedID']);
            }
            if($coa_start && $coa_end)
            {
                    $this->db->where('accounting_coa.coaCode BETWEEN ',$coa_start.' AND '.$coa_end,false);
            }
            $this->db->group_by('accounting_coa.coaCode');
            $this->db->order_by('accounting_coa.coaCode','ASC');
            $q = $this->db->get('accounting_coa');
            $id = $q->result_array();
            return $id;
    }

    public function get_coa_amount($start_date,$end_date,$coa_start,$coa_end)
    {
        $this->db->select('journalClosedDate');
        $this->db->where('journalClosedDate < ',$start_date);
        $this->db->order_by('journalClosedDate','DESC');
        $this->db->limit(1);
        $q = $this->db->get('accounting_journal_closed');
        $Q = $q->result_array();
        
        $this->db->select('accounting_coa.coaCode,sum(if(journalDetailType=1,journalDetailSum,0)) as Debet,
                            SUM(if(journalDetailType=2,journalDetailSum,0)) as Kredit',false);
        $this->db->join('accounting_journal_detail','accounting_journal_detail.coaCode=accounting_coa.coaCode','left');
        $this->db->join('accounting_journal','accounting_journal.journalID=accounting_journal_detail.journalID','left');
        if($Q)
            $this->db->where('date_format(journalDate,\'%Y-%m-%d\') >',$Q[0]['journalClosedDate']);
            $this->db->where('date_format(journalDate,\'%Y-%m-%d\') <',$start_date);
        if($coa_start && $coa_end)
        {
                $this->db->where('accounting_coa.coaCode BETWEEN ',$coa_start.' AND '.$coa_end,false);
        }
        $this->db->order_by('accounting_coa.coaCode','ASC');
        $this->db->group_by('coaCode');
        $q = $this->db->get('accounting_coa');
        $id = $q->result_array();
        return $id;
    }

    public function get_coa_balance($closing_date, $start_date,$coa){
        $sub = '';
        if($coa)
        {
                $sub .= "accounting_coa.coaCode = '".$coa."' AND ";
        }
        $query = "SELECT 	
                        v_coa.coaCode,accounting_coa_balance.coaBalanceAmount,
                        SUM(DEBIT) AS TOTAL_DEBIT,
                        SUM(CREDIT) AS TOTAL_CREDIT
                FROM v_coa
                LEFT JOIN accounting_coa_balance ON accounting_coa_balance.coaCode=v_coa.coaCode
                WHERE v_coa.coaType=1 
                AND journalIsPosted=1 AND
                DATE_FORMAT(journalDate,'%Y-%m-%d') > '".$closing_date."' AND 
                DATE_FORMAT(journalDate,'%Y-%m-%d') < '".$start_date."'  AND
                v_coa.coaCode IN (SELECT accounting_coa.coaCode as coaCode
                            FROM accounting_coa
                                    LEFT JOIN accounting_coa_balance ON accounting_coa_balance.coaCode=accounting_coa.coaCode
                                    LEFT JOIN accounting_journal_closed ON accounting_journal_closed.journalClosedID=accounting_coa_balance.journalClosedID
                                    WHERE ".$sub."
                                    accounting_coa_balance.journalClosedID = (SELECT journalClosedID
                                                                                FROM accounting_journal_closed
                                                                                WHERE journalClosedDate < '".$start_date."'
                                                                                ORDER BY journalClosedDate DESC
                                                                                LIMIT 0,1))
                GROUP BY v_coa.coaCode
                ORDER BY v_coa.coaCode ASC, DATE_FORMAT(journalDate, '%Y-%m-%d') ASC";
        $query = $this->db->query($query);
        //var_dump($this->db->last_query());die;
        if($query->num_rows()){
            $data = $query->result_array();
            return $data;
        }
        return array();
    }

    public function get_data($start_date,$end_date,$coa,$status = 3)
    {
        $sub = '';
        if($status == '1'){
            $post = 'AND journalIsPosted = 1 ';
        } elseif($status == '2'){
            $post = 'AND journalIsPosted = 0 ';
        } else {
            $post = '';
        }
        
        if($coa)
        {
                $sub .= "accounting_coa.coaCode = '".$coa."' AND ";
        }
        $query = "SELECT  
                                DATE_FORMAT(journalDate, '%d-%b-%y') as journalDate, 
                                journalTypeCode, accounting_journal_detail.coaCode as coaCode, 
                                coaTitle, journalMemo, journalDetailSum, 
                                journalDetailType, journalDetailDesc, 
                                if(journalDetailType=1,accounting_journal_detail.journalDetailSum,0) AS DEBIT,
                                if(journalDetailType=2,accounting_journal_detail.journalDetailSum,0) AS CREDIT
                                FROM (accounting_journal_detail) 
                                LEFT JOIN accounting_journal ON accounting_journal.journalID=accounting_journal_detail.journalID 
                                LEFT JOIN accounting_coa ON accounting_coa.coaCode=accounting_journal_detail.coaCode 
                                WHERE accounting_journal_detail.coaCode IN (SELECT accounting_coa.coaCode as COA_CODE
                            FROM accounting_coa
                                    LEFT JOIN accounting_coa_balance ON accounting_coa_balance.coaCode=accounting_coa.coaCode
                                    LEFT JOIN accounting_journal_closed ON accounting_journal_closed.journalClosedID=accounting_coa_balance.journalClosedID
                                    WHERE ".$sub."
                                    accounting_coa_balance.journalClosedID = (SELECT journalClosedID
                                                                                FROM accounting_journal_closed
                                                                                WHERE journalClosedDate < '".$start_date."'
                                                                                ORDER BY journalClosedDate DESC
                                                                                LIMIT 0,1)) 

                                ".$post." AND 
                                DATE_FORMAT(journalDate,'%Y-%m-%d') >= '".$start_date."' AND 
                                DATE_FORMAT(journalDate,'%Y-%m-%d') <= '".$end_date."'
                                ORDER BY DATE_FORMAT(journalDate, '%Y-%m-%d') ASC";	

        $query = $this->db->query($query);
        //var_dump($this->db->_error_message());
        //var_dump($this->db->last_query());die;
        $data = $query->result_array();
        return $data;
    }

    public function juml($start_date,$end_date)
    {
        $this->db->select('accounting_journal_detail.coaCode,coaTitle,journalTypeDesc,journalDetailType,journalDetailSum');
        //$this->db->where('JOURNAL_IS_POSTED',1);
        $this->db->where('DATE_FORMAT(journalPostedDate,"%Y-%m-%d") >=',$start_date);
        $this->db->where('DATE_FORMAT(journalPostedDate,"%Y-%m-%d") <=',$end_date);
        $this->db->join('accounting_coa','accounting_coa.coaCode=accounting_journal_detail.coaCode','left');
        $this->db->join('accounting_journal','accounting_journal.journalID=accounting_journal_detail.journalID','left');
        $this->db->join('accounting_journal_type','accounting_journal_type.journalTypeCode=accounting_journal.journalTypeCode','left');
        $result = $this->db->get('accounting_journal_detail');
        $juml = $result->num_rows();

        return $juml;
    }

    public function data_combo($value,$display,$table) {
        $data = $this->db->get($table);
        foreach($data->result_array() as $row)
                {
                $record[] = array($row[$value],$row[$display]);
                }
        return $record;
    }
    
    public function get_data_balance($date){
        $cond = "DATE_FORMAT(journalClosedDate,'%Y-%m-%d') < '$date'";
        $this->db->select('journalClosedID,journalClosedDate');
        $this->db->where($cond,null,false);
        $this->db->limit(1);
        $this->db->order_by('journalClosedDate','DESC');
        $Q = $this->db->get('accounting_journal_closed');
        $data = $Q->result_array();
        if($data){
            return $data[0];
        }else{
            return false;
        }
    }
    
    public function get_forward_value($end_date, $coa, $data){
        $date_cond = "DATE_FORMAT(journalDate,'%Y-%m-%d') > '".$data['journalClosedDate']."' AND DATE_FORMAT(journalDate,'%Y-%m-%d') < '$end_date'";
        $this->db->select('v_coa.coaCode,accounting_coa.coaCodeParent as coaCodeParent,SUM(DEBIT) AS DEBIT_FORWARD, SUM(CREDIT) AS CREDIT_FORWARD',false);
        $this->db->join('accounting_coa','accounting_coa.coaCode=v_coa.coaCode','left');
        $this->db->where($date_cond,null,false);
        if($coa){
            $ss = $this->get_full_code_code_tree($coa);
            foreach ($ss as $key => $link)
            {
                if ($ss[$key] == '')
                {
                    unset($ss[$key]);
                }
            }
            if(count($ss) > 1){
                $single = true;
            }
            $ss = implode('","', $ss);
            $ss = '"'.$ss.'"';
            $cc = "v_coa.coaCode in (".$ss.")";
            $this->db->where($cc,null,false);
        }
        $this->db->where('journalIsPosted',1);
        $this->db->group_by('v_coa.coaCode');
        $this->db->order_by('v_coa.coaCode');
        $Q = $this->db->get('v_coa');
        $result = $Q->result_array();
        if($coa){
            //init
            $rst['DEBIT_FORWARD'] = 0;
            $rst['CREDIT_FORWARD'] = 0;
            $rst['coaCode'] = $coa;
            
            foreach ($result as $item){
                $rst['coaCode'] = $coa;
                $rst['DEBIT_FORWARD'] += $item['DEBIT_FORWARD'];
                $rst['CREDIT_FORWARD'] += $item['CREDIT_FORWARD'];
            }
            $hasil[] = $rst;
            return $hasil;
        }
        return $result;
    }
    
    public function get_balance_amount($coa, $data){
        if($coa){
            $this->db->select('sum(coaBalanceAmount) as coaBalanceAmount',false);
            $ss = $this->get_full_code_code_tree($coa);
            foreach ($ss as $key => $link)
            {
                if ($ss[$key] == '')
                {
                    unset($ss[$key]);
                }
            }
            if(count($ss) > 1){
                $single = true;
            }
            $ss = implode('","', $ss);
            $ss = '"'.$ss.'"';
            $cc = "coaCode in (".$ss.")";
            $this->db->where($cc,null,false);
        }else{
            $this->db->select('coaCode,coaBalanceAmount');
        }
        $this->db->where('journalClosedID',$data['journalClosedID']);
        $this->db->order_by('coaCode');
        $Q = $this->db->get('accounting_coa_balance');
        $result = $Q->result_array();
        if($result && $coa){
            $result[0]['coaCode'] = $coa;
        }
        return $result;
    }
    /**
     * End of General Ledger
     */
    
    public function getDetail($id, $start = 0,$limit = 20,$sort = 'journalDetailID',$dir = 'DESC') {
        $this->db->select(array(
            'journalDetailID',
            'accounting_journal_detail.coaCode',
            'coaTitle',
            'accounting_journal_detail.journalID',
            'journalDetailDesc',
            'accounting_journal_detail.currencyID',
            'currencyName',
            'journalDetailOrig',
            'journalDetailExRate',
            'journalDetailSum',
            'IF(journalDetailType = 1,"DEBET",IF(journalDetailType = 2,"KREDIT",NULL)) AS journalDetailType'),
            FALSE
        );
        $this->db->from('accounting_journal_detail');
        $this->db->join('accounting_journal','accounting_journal.journalID=accounting_journal_detail.journalID','LEFT');
        $this->db->join('accounting_currency','accounting_currency.currencyID=accounting_journal_detail.currencyID','LEFT');
        $this->db->join('accounting_coa','accounting_coa.coaCode=accounting_journal_detail.coaCode','LEFT');
        $this->db->where('accounting_journal_detail.journalID',$id);
        $total = $this->db->_compile_select();
        
        $total = $this->db->query($total)->num_rows();
        
        $this->db->limit($limit,$start);
        $this->db->order_by($sort,$dir);
        
        $Q = $this->db->get();
        
        if($total){
            $output = $Q->result_array();
            foreach($output as $keys => $values){
                if($values['journalDetailType'] == 'DEBET'){
                    $output[$keys]['DEBET'] = $values['journalDetailSum'];
                }
                if($values['journalDetailType'] == 'KREDIT'){
                    $output[$keys]['KREDIT'] = $values['journalDetailSum'];
                }
            }
            return array('data' => $output, 'total' => $total);
        }
        
        return array('data' => array(), 'total' => 0);
    }

}

?>
