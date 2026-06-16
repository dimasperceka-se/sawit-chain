<?php

class Mloantype extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($start = 0, $limit = 20, $sort = 'journalDate', $dir = 'DESC', $query = array()) {
//        $this->db->select(array(
//                'loanTypeID',
//                'CoopID',
//                'loanTypeCode',
//                'loanTypeName',
//                'loanTypeMinAmount',
//                'loanTypeMaxAmount',
//                'loanTypeMinTenor',
//                'loanTypeMaxTenor',
//                'loanTypeIslamic',
//                'loanTypeProfitType',
//                'coop_loan_type.interestTypeID',
//                'loanTypePenaltyPercent',
//                'loanTypeInterestAmount',
//                'loanTypeInterestDuration',
//                'loanTypeClientSharing',
//                'loanTypeCooperativeSharing',
//                'loanTypeGracePeriod',
//                'loanTypeFee',
//                'loanTypeDuePenalty',
//                'loanTypePenaltyAmount',
//                'cashSourceID',
//                'loanTypeActiveDate',
//                'loanTypeRemark',
//                'createdBy',
//                'createdDate',
//                'updateBy',
//                'updateDate',
//                'active'
//            ), FALSE
//        );
        $query = $this->db->query("SELECT CASE loanTypeCode
                        WHEN 1 THEN 'Islamic Finance'
                        WHEN 2 THEN 'Loan'
                        end as ProductType,
                        CASE loanTypeCode
                        WHEN 1 THEN 
                            CASE loanTypeProfitType
                            WHEN 1 THEN 'Profit Sharing'
                            WHEN 2 THEN 'Fixed Margin' end
                        WHEN 2 THEN loanTypeInterestAmount
                        end as profit, 
                        CASE active
                            WHEN 1 THEN 'active'
                            ELSE 'inactive'
                        end as status,
                        loanTypeID, coop_loan_type.CoopID, loanTypeCode, loanTypeName, loanTypeMinAmount, loanTypeMaxAmount, loanTypeMinTenor, loanTypeMaxTenor, loanTypeIslamic, 
                        loanTypeProfitType, coop_loan_type.interestTypeID, loanTypePenaltyPercent, loanTypeInterestAmount, loanTypeInterestDuration, loanTypeClientSharing,
                         loanTypeCooperativeSharing, loanTypeGracePeriod, loanTypeFee, loanTypeDuePenalty, loanTypePenaltyAmount, cashSourceID, loanTypeActiveDate, loanTypeRemark,
                         createdBy, createdDate, updateBy, updateDate, active,
                         CONCAT(loanTypeMinTenor,' - ',loanTypeMaxTenor) as tenorRange,
                            CONCAT(loanTypeInterestAmount,'% ',
                                CASE loanTypeInterestDuration 
                                    WHEN 1 THEN 'Per Month'
                                    WHEN 2 THEN 'Per Year'
                                    WHEN 3 THEN 'One Time'
                                END)
                             as interestRate,
                         coop_interest_type.interestTypeName
                         from coop_loan_type
                         left join coop_interest_type ON coop_interest_type.interestTypeID=coop_loan_type.interestTypeID where coop_loan_type.CoopID = ".getCoopID());
//        $this->db->from('coop_loan_type');
//        $this->db->join('coop_interest_type', 'coop_interest_type.interestTypeID=coop_loan_type.interestTypeID', 'left');
        
//        $total = $this->db->_compile_select();

//        $total = $this->db->query($total)->num_rows();
        $total =  $query->num_rows();

        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);

//        $Q = $this->db->get();
        $Q =  $query;

        if ($total) {
            return array('data' => $Q->result_array(), 'total' => $total);
        }

        return array('data' => array(), 'total' => 0);
    }
    
    function getLoanTypeMember($id)
    {
        $q = $this->db->get_where('coop_loan_type_members',array('loanTypeID'=>$id));
        $num = $q->num_rows();
        $tm='';
        $i=0;
        foreach ($q->result() as $r)
        {
            $tm.=$r->MemberTypeID;
            $i++;
            
            if($i<$num)
            {
                $tm.=",";
            }
        }
        return $tm;
    }

    public function getById($id) {
        $this->db->select(
                array(
                    '*'
                )
        );
        $this->db->from('coop_loan_type');
        $this->db->join('coop_interest_type','coop_interest_type.interestTypeID=coop_loan_type.interestTypeID','left');
        $this->db->where('loanTypeID', $id);

        $Q = $this->db->get();
        if ($Q->num_rows()) {
            return $Q->row_array();
        }

        return false;
    }
    
    public function add($data) {
        $this->load->helper('date');
        $data['CoopID'] = getCoopID();
        $data['LoanTypeActiveDate'] = isset($data['LoanTypeActiveDate']) ? convertdate($data['LoanTypeActiveDate']) : null;
        // unset($data['LoanTypeActiveDate']);
        // $data['loanTypeActiveDate'] = $loanTypeActiveDate;
        $itemselector = explode(',',$data['itemselector']); //loan_type_member selection
        
        //remove loan_type_member selection from array $data
        unset($data['itemselector']);
        
        if($data['loanTypeCode']==1)
        {
            //islamic
            $data['loanTypeIslamic'] = 1;
        } else {
            $data['loanTypeIslamic'] = 2;
        }
        
        $data['active'] = $data['active']=='on' ? 1 : 0;
        
        $this->db->insert('coop_loan_type',$data);
        $id = $this->db->insert_id();
        
        
        foreach ($itemselector as $value) {
            $this->db->insert('coop_loan_type_members',array(
                'memberTypeID'=>$value,
                'loanTypeID'=>$id
            ));
        }
        
        if($this->db->_error_number()){
            if($this->db->_error_number() == 1062){
                return array('success' => false, 'msg' => 'Record already exist');
            }
            
            return array('success' => false, 'msg' => 'Failed add record');
        }
        
        return array('success' => true, 'msg' => 'Successfully add record');;
    }
    
    public function edit($id,$data) {

        $data['active'] = $data['active'] == 'on' ? 1 : 0;

        $this->db->where('loanTypeID',$id);
        $this->db->update('coop_loan_typex',$data);

        return $data;
        
        if($this->db->_error_number()){
            if($this->db->_error_number() == 1062){
                return array('success' => false, 'msg' => 'Record already exist');
            }
            
            return array('success' => false, 'msg' => 'Failed update record');
        }
        
        return array('success' => true, 'msg' => 'Successfully update record');;
    }
    
    public function editLoanType($data)
    {
        $id = $data['LoanTypeID'];
        $this->load->helper('date');
        $data['CoopID'] = getCoopID();
        $data['loanTypeActiveDate'] = convertdate($data['LoanTypeActiveDate']);
        $itemselector = explode(',',$data['itemselector']); //loan_type_member selection
        
        //remove loan_type_member selection from array $data
        unset($data['itemselector']);
        
        if($data['LoanTypeCode']==1)
        {
            //islamic
            $data['LoanTypeIslamic'] = 1;
        } else {
            $data['LoanTypeIslamic'] = 2;
        }
        
        $data['active'] = $data['active']=='on' ? 1 : 0;
        
        if($data['active']==1)
        {
            $this->db->where('LoanTypeID',$id);
            $this->db->update('coop_loan_type',$data);
        } else {
            //update menjadi inactive
            $this->db->where('LoanTypeID',$id);
            $this->db->update('coop_loan_type',array('active'=>0));
        }
        
        
        //delete dulu trus diisi lagi
        if($data['active']==1)
        {
            $this->db->where('LoanTypeID',$id);
            $this->db->delete('coop_loan_type_members');

            foreach ($itemselector as $value) {
                $this->db->insert('coop_loan_type_members',array(
                    'memberTypeID'=>$value,
                    'LoanTypeID'=>$id
                ));
            }
        }
        
        if($this->db->_error_number()){
            if($this->db->_error_number() == 1062){
                return array('success' => false, 'msg' => 'Record already exist');
            }
            
            return array('success' => false, 'msg' => 'Failed update record');
        }
        
        return array('success' => true, 'msg' => 'Successfully update record');
    }
    
    public function delete($id) {
        $this->db->where('loanTypeID',$id);
        $this->db->delete('coop_loan_type_members');
        
        $this->db->where('loanTypeID',$id);
        $this->db->delete('coop_loan_type');
        
        if($this->db->_error_number()){
            
            return array('success' => false, 'msg' => 'Failed delete record');
        }
        
        return array('success' => true, 'msg' => 'Successfully delete record');;
    }

}

?>
