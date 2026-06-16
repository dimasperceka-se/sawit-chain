<?php

/**
 * Authentication Model for Mobile
 *
 * @author Yusuf Sutana 
 */
class m_webtransaction extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->helper('common_helper');
    }
    
    public function get_data_transaction($SID,$pSearch, $start, $limit, $sortingField, $sortingDir){ 
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        //sort
        $sorting = json_decode($this->input->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $order = "ORDER BY a.SupplyTransID DESC";
        if ($sorting){
            if ($sortingField = 'MemberName')
                $sortingField = 'MemberName';
            $order = "ORDER BY $sortingField $sortingDir";    
        }
        
        $start = (int) $start;
        $limit = (int) $limit;
        $sqlLimit = " LIMIT {$start},{$limit}";
        
        /* Filter core */
        if($pSearch['ArrFilter'] != '' && $pSearch['ArrFilter'] != 'null'){
            /* pencarian */
            $SupplyType = $this->input->get('TextFilterTransTypeName');
            $SupplyID= $this->input->get('TextFilterTransSupplyID');
            $SupplyKey = $this->input->get('TextFilterMemberName');
            
            $where = "";
            if($SupplyType != ''){

                if($SupplyType == '1'){
                    $TransactionType = 'Farmer';
                } elseif ($SupplyType == '2'){
                    $TransactionType = 'Batch';
                } elseif ($SupplyType == '3'){
                    $TransactionType = 'Nonfarmer';
                } elseif ($SupplyType == '4'){
                    $TransactionType = 'Delivery';
                } else {
                    $TransactionType = '-';
                }

                $where .= "AND a.`SupplyType` = '$TransactionType'";
            } 

            if($SupplyID != ''){
                $where .= " AND a.`SupplyTransID` = '$SupplyID' ";
            } 

            if($SupplyKey != ''){
                $where .= " AND (IF(
                    b.MemberName IS NULL OR b.MemberName = '',
                    IF(
                        m2.MillName IS NULL OR m2.MillName = '',
                        IF(
                            a.MillOther IS NULL OR a.MillOther = '',
                            IF(
                                mem2.MemberName IS NULL OR mem2.MemberName = '',
                                IF(
                                    a.DOOther IS NULL OR a.DOOther = '',
                                    IF(
                                        a.AgentOther IS NULL OR a.AgentOther = '',
                                        'Nonfarmer',
                                        a.AgentOther
                                    ),
                                    a.DOOther
                                ),
                                mem2.MemberName
                            ),
                            a.MillOther
                        ),
                        m2.MillName
                    ),
                    b.MemberName
                ) LIKE '%$SupplyKey%') ";            
            }

            if($pSearch['TextFilterStartDateTransaction'] != '' OR $pSearch['TextFilterEndDateTransaction'] != ''){
                $DateStart = $pSearch['TextFilterStartDateTransaction'];
                $DateEnd   = $pSearch['TextFilterEndDateTransaction'];

                $whereFilterDate = "AND DATE_FORMAT(a.DateTransaction, '%Y-%m-%d') >= '$DateStart' AND DATE_FORMAT(a.DateTransaction, '%Y-%m-%d') <= '$DateEnd'";
            }
        }

        $data = array('data' => array(), 'total' => 0);

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`SupplyTransID`,
                    a.`SupplychainID`,
                    a.`SupplyBatchID`,
                    a.`TransNumber`,
                    a.`InvoiceNumber`,
                    a.`DateTransaction`,
                    CASE
                            WHEN a.SupplybaseCategoryID = 1 THEN 'Farmer Plasma'
                            WHEN a.SupplybaseCategoryID = 2 THEN 'Direct Smallholder'
                            WHEN a.SupplybaseCategoryID = 3 THEN 'Agent / Dealer / Vendor'
                            WHEN a.SupplybaseCategoryID = 4 THEN 'Owner Estate'
                            WHEN a.SupplybaseCategoryID = 5 THEN 'External Estate'
                            ELSE 'Agent / Dealer / Vendor'
                    END AS SupplyType,
                    a.`PlantationNr`,
                    a.`PlantationNr` FarmNumber,
                    a.`VolumeBruto`,
                    a.`VolumeNetto`,
                    a.`VolumeCutting`,
                    a.`PackageID`,
                    a.`Bunches`,
                    a.`FFBCount` PackageNumber,
                    a.`PackageWeight`,
                    a.`DetailTypeID`,
                    a.`TransStatusID`,
                    a.`FarmingTypeID`,
                    a.ContractPrice,
                    a.NetPrice,
                    a.DiscountPrice,
                    a.TotalPayment,
                    a.PaymentReduction,
                    a.PaymentPaid AS PaymentAmount,
                    a.isTraceable,
                    a.`Notes`,
                    a.`ChangeLog`,
                    a.`ChangeBy`,
                    a.`DateCreated`,
                    a.`CreatedBy`,
                    a.`DateUpdated`,
                    a.`LastModifiedBy`, 
                    a.`PaymentStatusID`,
                    CASE
                            WHEN a.PaymentStatusID = 1 THEN 'Paid'
                            WHEN a.PaymentStatusID = 2 THEN 'Waiting Payment'
                            WHEN a.PaymentStatusID = 3 THEN 'Incomplete Payment'
                            WHEN a.PaymentStatusID = 5 THEN 'To be confirm'
                            WHEN a.PaymentStatusID = 99 THEN 'Failed'
                            ELSE 'Not yet paid'
                    END AS PaymentStatus,
                    a.`PaymentMethodID`,
                    a.`uid`,
                    b.`Latitude`,
                    b.`Longitude`,                 
                    c.PackageType,
                    IF(
                        b.MemberName IS NULL OR b.MemberName = '',
                        IF(
                            m2.MillName IS NULL OR m2.MillName = '',
                            IF(
                                a.MillOther IS NULL OR a.MillOther = '',
                                IF(
                                    mem.Name IS NULL OR mem.Name = '',
                                    IF(
                                       kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                        IF(
                                            a.DOOther IS NULL OR a.DOOther = '',
                                            IF(
                                                a.AgentOther IS NULL OR a.AgentOther = '',
                                                'Nonfarmer',
                                                a.AgentOther
                                            ),
                                            a.DOOther
                                        ),
                                        kms.agCompanyName
                                    ),
                                    mem.Name
                                ),
                                a.MillOther
                            ),
                            m2.MillName
                        ),
                        b.MemberName
                    ) SupplierName,
                    e.Name,
                    IFNULL(b.MemberDisplayID, IFNULL(bb.MemberID, '-')) AS MemberDisplayID,
                    IFNULL(
                            IF(
                                b.MemberID <> 0, b.MemberID, 
                                IF(
                                    a.MillID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.MillID,
                                    IF(
                                        a.DOID <> 0 AND (a.DOOther IS NULL OR a.DOOther = ''), a.DOID,
                                        IF(
                                            a.AgentID <> 0 AND (a.AgentOther IS NULL OR a.AgentOther = ''), a.AgentID,
                                            IF(
                                                a.SupplyID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.SupplyID,
                                                'Unregistered Supplier'
                                            )
                                        )
                                    )
                                )
                            ), 'Unregistered Supplier'
                    ) MemberID,
                    IFNULL(
                        IF(
                            a.MillID IS NOT NULL OR a.MillID <> '', 'external',
                            IF(
                                a.MillOther IS NOT NULL OR a.MillID <> '', 'external', 'other'
                            )
                        ), 'other'
                    ) SellerType,
                    e.ObjID,
                    a.MillID,
                    a.MillOther,
                    IF(a.MillOther IS NULL OR a.MillOther = '', '',true) OtherMill,
                    a.DOID,
                    a.DOOther,
                    IF(a.DOOther IS NULL OR a.DOOther = '', '',true) OtherDO,
                    a.AgentID,
                    a.AgentOther,
                    IF(a.AgentOther IS NULL OR a.AgentOther = '', '',true) OtherAgent,
                    a.AgentOtherNIK,
                    a.AgentOtherSurvey,
                    IF(b.isCertified != '', cp.CertProgName,'Not Certified') Certified,
                    a.SupplyType AS SalesType,
                    IF( a.SupplyBatchID IS NULL, 'Open', 'Sent' ) SupplyStatus,
                    CASE WHEN a.SupplyBatchID IS NULL THEN '-'
                    ELSE
                    IFNULL(vso2.`Name`, b.MemberName)
                    END as BatchFrom
                FROM
                    ktv_tc_supplychain_transaction a
                LEFT JOIN
                    ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' 
                LEFT JOIN
                    ktv_members bb on a.SupplyID = bb.MemberID 
                LEFT JOIN
                    ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
                LEFT JOIN
                    ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch' 
                LEFT JOIN
                    view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
                LEFT JOIN
                    ktv_trace_package c on a.PackageID=c.PackageID
                LEFT JOIN
                    ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
                LEFT JOIN
                    ktv_mill m2 on m2.MillID = a.MillID
                LEFT JOIN
                    view_tc_supplychain_org mem on mem.SupplychainID = a.MillID
                LEFT JOIN
                    ktv_members mem2 on mem2.MemberID = a.AgentID
                LEFT JOIN
                    view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
                LEFT JOIN 
                    ktv_members_extension kms on kms.MemberID = vso3.ObjID
                WHERE
                    a.StatusCode = 'active'
                AND
                    a.SupplychainID = ?
                AND
                    d.SupplyBatchID IS NULL
                $where
                $whereFilterDate
                $order
                $sqlLimit
        ";

        $Q = $this->db->query($sql,array($SID));
        
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $val){
                $val = $this->check_isNull($val);
                $val->SupplyID = $val->MemberID;
            }

            if ($_SESSION['PartnerID'] != '14' || $_SESSION['PartnerID'] != '194'){ // bukan WAGS dan mill perak
                $query = $this->db->query('SELECT FOUND_ROWS() AS total');
                $data['data'] = $result;
                $data['total'] = $query->row()->total;
            } else {
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
            }
            
            return $data;
        }

        return $data;
    }

    public function get_data_transaction_excel($SID,$pSearch, $start, $limit, $sortingField, $sortingDir){ 
        
        //sort
        $sorting = json_decode($this->input->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $order = "ORDER BY a.SupplyTransID DESC";
        if ($sorting){
            if ($sortingField = 'MemberName')
                $sortingField = 'MemberName';
            $order = "ORDER BY $sortingField $sortingDir";    
        }
        
        /* Filter core */
        if($pSearch['ArrFilter'] != '' && $pSearch['ArrFilter'] != 'null'){
            /* pencarian */
            $SupplyType = $this->input->get('TextFilterTransTypeName');
            $SupplyID= $this->input->get('TextFilterTransSupplyID');
            $SupplyKey = $this->input->get('TextFilterMemberName');
            
            $where = "";
            if($SupplyType != ''){

                if($SupplyType == '1'){
                    $TransactionType = 'Farmer';
                } elseif ($SupplyType == '2'){
                    $TransactionType = 'Batch';
                } elseif ($SupplyType == '3'){
                    $TransactionType = 'Nonfarmer';
                } elseif ($SupplyType == '4'){
                    $TransactionType = 'Delivery';
                } else {
                    $TransactionType = '-';
                }

                $where .= "AND a.`SupplyType` = '$TransactionType'";
            } 

            if($SupplyID != ''){
                $where .= " AND a.`SupplyTransID` = '$SupplyID' ";
            } 

            if($SupplyKey != ''){
                $where .= " AND (IF(
                    b.MemberName IS NULL OR b.MemberName = '',
                    IF(
                        m2.MillName IS NULL OR m2.MillName = '',
                        IF(
                            a.MillOther IS NULL OR a.MillOther = '',
                            IF(
                                mem2.MemberName IS NULL OR mem2.MemberName = '',
                                IF(
                                    a.DOOther IS NULL OR a.DOOther = '',
                                    IF(
                                        a.AgentOther IS NULL OR a.AgentOther = '',
                                        'Nonfarmer',
                                        a.AgentOther
                                    ),
                                    a.DOOther
                                ),
                                mem2.MemberName
                            ),
                            a.MillOther
                        ),
                        m2.MillName
                    ),
                    b.MemberName
                ) LIKE '%$SupplyKey%') ";            
            }

            if($pSearch['TextFilterStartDateTransaction'] != '' OR $pSearch['TextFilterEndDateTransaction'] != ''){
                $DateStart = $pSearch['TextFilterStartDateTransaction'];
                $DateEnd   = $pSearch['TextFilterEndDateTransaction'];

                $whereFilterDate = "AND DATE_FORMAT(a.DateTransaction, '%Y-%m-%d') >= '$DateStart' AND DATE_FORMAT(a.DateTransaction, '%Y-%m-%d') <= '$DateEnd'";
            }
        }

        $data = array('data' => array(), 'total' => 0);

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`SupplyTransID`,
                    a.`SupplychainID`,
                    a.`SupplyBatchID`,
                    a.`TransNumber`,
                    a.`InvoiceNumber`,
                    a.`DateTransaction`,
                    CASE
                            WHEN a.SupplybaseCategoryID = 1 THEN 'Farmer Plasma'
                            WHEN a.SupplybaseCategoryID = 2 THEN 'Direct Smallholder'
                            WHEN a.SupplybaseCategoryID = 3 THEN 'Agent / Dealer / Vendor'
                            WHEN a.SupplybaseCategoryID = 4 THEN 'Owner Estate'
                            WHEN a.SupplybaseCategoryID = 5 THEN 'External Estate'
                            ELSE 'Agent / Dealer / Vendor'
                    END AS SupplyType,
                    a.`PlantationNr`,
                    a.`PlantationNr` FarmNumber,
                    a.`VolumeBruto`,
                    a.`VolumeNetto`,
                    a.`VolumeCutting`,
                    a.`PackageID`,
                    a.`Bunches`,
                    a.`FFBCount` PackageNumber,
                    a.`PackageWeight`,
                    a.`DetailTypeID`,
                    a.`TransStatusID`,
                    a.`FarmingTypeID`,
                    a.ContractPrice,
                    a.NetPrice,
                    a.DiscountPrice,
                    a.TotalPayment,
                    a.PaymentReduction,
                    a.PaymentPaid AS PaymentAmount,
                    a.isTraceable,
                    a.`Notes`,
                    a.`ChangeLog`,
                    a.`ChangeBy`,
                    a.`DateCreated`,
                    a.`CreatedBy`,
                    a.`DateUpdated`,
                    a.`LastModifiedBy`, 
                    a.`PaymentStatusID`,
                    CASE
                            WHEN a.PaymentStatusID = 1 THEN 'Paid'
                            WHEN a.PaymentStatusID = 2 THEN 'Waiting Payment'
                            WHEN a.PaymentStatusID = 3 THEN 'Incomplete Payment'
                            WHEN a.PaymentStatusID = 5 THEN 'To be confirm'
                            WHEN a.PaymentStatusID = 99 THEN 'Failed'
                            ELSE 'Not yet paid'
                    END AS PaymentStatus,
                    a.`PaymentMethodID`,
                    a.`uid`,
                    b.`Latitude`,
                    b.`Longitude`,                 
                    c.PackageType,
                    IF(
                        b.MemberName IS NULL OR b.MemberName = '',
                        IF(
                            m2.MillName IS NULL OR m2.MillName = '',
                            IF(
                                a.MillOther IS NULL OR a.MillOther = '',
                                IF(
                                    mem.Name IS NULL OR mem.Name = '',
                                    IF(
                                       kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                        IF(
                                            a.DOOther IS NULL OR a.DOOther = '',
                                            IF(
                                                a.AgentOther IS NULL OR a.AgentOther = '',
                                                'Nonfarmer',
                                                a.AgentOther
                                            ),
                                            a.DOOther
                                        ),
                                        kms.agCompanyName
                                    ),
                                    mem.Name
                                ),
                                a.MillOther
                            ),
                            m2.MillName
                        ),
                        b.MemberName
                    ) SupplierName,
                    e.Name,
                    IFNULL(b.MemberDisplayID, IFNULL(bb.MemberID, '-')) AS MemberDisplayID,
                    IFNULL(
                            IF(
                                b.MemberID <> 0, b.MemberID, 
                                IF(
                                    a.MillID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.MillID,
                                    IF(
                                        a.DOID <> 0 AND (a.DOOther IS NULL OR a.DOOther = ''), a.DOID,
                                        IF(
                                            a.AgentID <> 0 AND (a.AgentOther IS NULL OR a.AgentOther = ''), a.AgentID,
                                            IF(
                                                a.SupplyID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.SupplyID,
                                                'Unregistered Supplier'
                                            )
                                        )
                                    )
                                )
                            ), 'Unregistered Supplier'
                    ) MemberID,
                    IFNULL(
                        IF(
                            a.MillID IS NOT NULL OR a.MillID <> '', 'external',
                            IF(
                                a.MillOther IS NOT NULL OR a.MillID <> '', 'external', 'other'
                            )
                        ), 'other'
                    ) SellerType,
                    e.ObjID,
                    a.MillID,
                    a.MillOther,
                    IF(a.MillOther IS NULL OR a.MillOther = '', '',true) OtherMill,
                    a.DOID,
                    a.DOOther,
                    IF(a.DOOther IS NULL OR a.DOOther = '', '',true) OtherDO,
                    a.AgentID,
                    a.AgentOther,
                    IF(a.AgentOther IS NULL OR a.AgentOther = '', '',true) OtherAgent,
                    a.AgentOtherNIK,
                    a.AgentOtherSurvey,
                    IF(b.isCertified != '', cp.CertProgName,'Not Certified') Certified,
                    a.SupplyType AS SalesType,
                    IF( a.SupplyBatchID IS NULL, 'Open', 'Sent' ) SupplyStatus,
                    CASE WHEN a.SupplyBatchID IS NULL THEN '-'
                    ELSE
                    IFNULL(vso2.`Name`, b.MemberName)
                    END as BatchFrom
                FROM
                    ktv_tc_supplychain_transaction a
                LEFT JOIN
                    ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' 
                LEFT JOIN
                    ktv_members bb on a.SupplyID = bb.MemberID 
                LEFT JOIN
                    ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
                LEFT JOIN
                    ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch' 
                LEFT JOIN
                    view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
                LEFT JOIN
                    ktv_trace_package c on a.PackageID=c.PackageID
                LEFT JOIN
                    ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
                LEFT JOIN
                    ktv_mill m2 on m2.MillID = a.MillID
                LEFT JOIN
                    view_tc_supplychain_org mem on mem.SupplychainID = a.MillID
                LEFT JOIN
                    ktv_members mem2 on mem2.MemberID = a.AgentID
                LEFT JOIN
                    view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
                LEFT JOIN 
                    ktv_members_extension kms on kms.MemberID = vso3.ObjID
                WHERE
                    a.StatusCode = 'active'
                AND
                    a.SupplychainID = ?
                AND
                    d.SupplyBatchID IS NULL
                $where
                $whereFilterDate
                $order
        ";

        $Q = $this->db->query($sql,array($SID));
        
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $val){
                $val = $this->check_isNull($val);
                $val->SupplyID = $val->MemberID;
            }

            if ($_SESSION['PartnerID'] != '14' || $_SESSION['PartnerID'] != '194'){ // bukan WAGS dan mill perak
                $query = $this->db->query('SELECT FOUND_ROWS() AS total');
                $data['data'] = $result;
                $data['total'] = $query->row()->total;
            } else {
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
            }
            
            return $data;
        }

        return $data;
    }

    public function get_data_sms_transaction($pSearch, $start, $limit, $sortingField, $sortingDir,$type){ 
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        //sort
        $sorting = json_decode($this->input->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $order = "ORDER BY a.SupplyTransID DESC";
        if ($sorting){
            if ($sortingField = 'MemberName')
                $sortingField = 'MemberName';
            $order = "ORDER BY $sortingField $sortingDir";    
        }
        
        if($type!="export_excel"){
            $limit = "LIMIT". ' '. $this->input->get('limit');
        } else {
            $limit = "";
        }

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        
        $start = (int) $start;
        $limit = (int) $limit;
        $sqlLimit = "LIMIT {$start},{$limit}";

        /* Filter core */
        if($pSearch['ArrFilter'] != '' && $pSearch['ArrFilter'] != 'null'){
            /* pencarian */
            $SupplyType = $this->input->get('TextFilterTransTypeName');
            $SupplyID= $this->input->get('TextFilterTransSupplyID');
            $SupplyKey = $this->input->get('TextFilterMemberName');
            
            $where = "";

            if($SupplyType != ''){

                if($SupplyType == '1'){
                    $TransactionType = 'Farmer';
                } elseif ($SupplyType == '2'){
                    $TransactionType = 'Batch';
                } elseif ($SupplyType == '3'){
                    $TransactionType = 'Nonfarmer';
                } elseif ($SupplyType == '4'){
                    $TransactionType = 'Delivery';
                } else {
                    $TransactionType = '';
                }

                $where .= "AND a.`SupplyType` = '$TransactionType'";
            } 
           
            if($SupplyID != ''){
                $where .= " AND a.`SupplyTransID` = '$SupplyID' ";
            } 

            if($SupplyKey != ''){
                $where .= " AND (IF(
                    b.MemberName IS NULL OR b.MemberName = '',
                    IF(
                        m2.MillName IS NULL OR m2.MillName = '',
                        IF(
                            a.MillOther IS NULL OR a.MillOther = '',
                            IF(
                                mem2.MemberName IS NULL OR mem2.MemberName = '',
                                IF(
                                    a.DOOther IS NULL OR a.DOOther = '',
                                    IF(
                                        a.AgentOther IS NULL OR a.AgentOther = '',
                                        'Nonfarmer',
                                        a.AgentOther
                                    ),
                                    a.DOOther
                                ),
                                mem2.MemberName
                            ),
                            a.MillOther
                        ),
                        m2.MillName
                    ),
                    b.MemberName
                ) LIKE '%$SupplyKey%') ";            
            }
        
            if($pSearch['TextFilterStartDateTransaction'] != '' OR $pSearch['TextFilterEndDateTransaction'] != ''){
                $DateStart = $pSearch['TextFilterStartDateTransaction'];
                $DateEnd   = $pSearch['TextFilterEndDateTransaction'];

                $whereFilterDate = "AND DATE_FORMAT(a.DateTransaction, '%Y-%m-%d') >= '$DateStart' AND DATE_FORMAT(a.DateTransaction, '%Y-%m-%d') <= '$DateEnd'";
            }
            
            if($pSearch['TextFilterProvince'] != '' OR $pSearch['TextFilterDistrict'] != ''){

                $TextFilterProvince = $pSearch['TextFilterProvince'];
                $TextFilterDistrict = $pSearch['TextFilterDistrict'];

                $whereFilterProvince = "AND kp.ProvinceID = '$TextFilterProvince'";
                $whereFilterDistrict = "AND kd.DistrictID = '$TextFilterDistrict'";
            }
        }

        $data = array('data' => array(), 'total' => 0);
       
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`SupplyTransID`,
                    a.`SupplychainID`,
                    a.`SupplyBatchID`,
                    a.`TransNumber`,
                    a.`InvoiceNumber`,
                    a.`DateTransaction`,
                    CASE
                            WHEN a.SupplybaseCategoryID = 1 THEN 'Farmer Plasma'
                            WHEN a.SupplybaseCategoryID = 2 THEN 'Direct Smallholder'
                            WHEN a.SupplybaseCategoryID = 3 THEN 'Agent / Dealer / Vendor'
                            WHEN a.SupplybaseCategoryID = 4 THEN 'Owner Estate'
                            WHEN a.SupplybaseCategoryID = 5 THEN 'External Estate'
                            ELSE 'Agent / Dealer / Vendor'
                    END AS SupplyType,
                    a.`PlantationNr`,
                    a.`PlantationNr` FarmNumber,
                    a.`VolumeBruto`,
                    a.`VolumeNetto`,
                    a.`VolumeCutting`,
                    a.`PackageID`,
                    a.`Bunches`,
                    a.`FFBCount` PackageNumber,
                    a.`PackageWeight`,
                    a.`DetailTypeID`,
                    a.`TransStatusID`,
                    a.`FarmingTypeID`,
                    a.ContractPrice,
                    a.NetPrice,
                    a.DiscountPrice,
                    a.TotalPayment,
                    a.PaymentReduction,
                    a.PaymentPaid AS PaymentAmount,
                    a.isTraceable,
                    a.`Notes`,
                    a.`ChangeLog`,
                    a.`ChangeBy`,
                    a.`DateCreated`,
                    a.`CreatedBy`,
                    a.`DateUpdated`,
                    a.`LastModifiedBy`, 
                    a.`uid`,
                    b.`Latitude`,
                    b.`Longitude`,                 
                    IF(
                        b.MemberName IS NULL OR b.MemberName = '',
                        IF(
                            m2.MillName IS NULL OR m2.MillName = '',
                            IF(
                                a.MillOther IS NULL OR a.MillOther = '',
                                IF(
                                    mem.Name IS NULL OR mem.Name = '',
                                    IF(
                                       kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                        IF(
                                            a.DOOther IS NULL OR a.DOOther = '',
                                            IF(
                                                a.AgentOther IS NULL OR a.AgentOther = '',
                                                'Nonfarmer',
                                                a.AgentOther
                                            ),
                                            a.DOOther
                                        ),
                                        kms.agCompanyName
                                    ),
                                    mem.Name
                                ),
                                a.MillOther
                            ),
                            m2.MillName
                        ),
                        b.MemberName
                    ) SupplierName,
                    e.Name,
                    IFNULL(b.MemberDisplayID, IFNULL(bb.MemberID, '-')) AS MemberDisplayID,
                    IFNULL(
                            IF(
                                b.MemberID <> 0, b.MemberID, 
                                IF(
                                    a.MillID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.MillID,
                                    IF(
                                        a.DOID <> 0 AND (a.DOOther IS NULL OR a.DOOther = ''), a.DOID,
                                        IF(
                                            a.AgentID <> 0 AND (a.AgentOther IS NULL OR a.AgentOther = ''), a.AgentID,
                                            IF(
                                                a.SupplyID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.SupplyID,
                                                'Unregistered Supplier'
                                            )
                                        )
                                    )
                                )
                            ), 'Unregistered Supplier'
                    ) MemberID,
                    IFNULL(
                        IF(
                            a.MillID IS NOT NULL OR a.MillID <> '', 'external',
                            IF(
                                a.MillOther IS NOT NULL OR a.MillID <> '', 'external', 'other'
                            )
                        ), 'other'
                    ) SellerType,
                    e.ObjID,
                    a.MillID,
                    a.MillOther,
                    IF(a.MillOther IS NULL OR a.MillOther = '', '',true) OtherMill,
                    a.DOID,
                    a.DOOther,
                    IF(a.DOOther IS NULL OR a.DOOther = '', '',true) OtherDO,
                    a.AgentID,
                    a.AgentOther,
                    IF(a.AgentOther IS NULL OR a.AgentOther = '', '',true) OtherAgent,
                    a.AgentOtherNIK,
                    a.AgentOtherSurvey,
                    a.SupplyType AS SalesType,
                    IF( a.SupplyBatchID IS NULL, 'Open', 'Sent' ) SupplyStatus,
                    CASE WHEN a.SupplyBatchID IS NULL THEN '-'
                    ELSE
                    IFNULL(vso2.`Name`, b.MemberName)
                    END as BatchFrom,
                    slst.SMSType,
                            IF(slst.SupplyTransID IS NULL, 'not yet sent', 
                        IF(
                            slst.ProviderID=2,
                            IF(slst.response!='' && LENGTH(slst.response) > 5, 'Delivered', 'Undelivered'),
                            CASE
                                WHEN slst.response LIKE '%\"status\":\"0\",%' THEN 'Delivered'
                                ELSE 'Undelivered'
                            END
                        )
                    ) SMSStatus,
                    slst.Handphone,
                    slst.DateCreated AS SendDate,
                    b.groupName AS GroupName,
                    kp.ProvinceID,
                    kp.Province,
                    kd.DistrictID,
                    kd.District,
                    ks.SubDistrictID,
                    ks.SubDistrict,
                    b.VillageID,
                    kv.Village,
                    slst.AutoID,
                    slst.response
                FROM
                    ktv_tc_supplychain_transaction a
                LEFT JOIN
                    ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'
                LEFT JOIN
                    ktv_members bb on a.SupplyID = bb.MemberID 
                LEFT JOIN
                    ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch' 
                LEFT JOIN
                    view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
                LEFT JOIN
                    ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
                LEFT JOIN
                    ktv_mill m2 on m2.MillID = a.MillID
                LEFT JOIN
                    view_tc_supplychain_org mem on mem.SupplychainID = a.MillID
                LEFT JOIN
                    ktv_members mem2 on mem2.MemberID = a.AgentID
                LEFT JOIN
                    view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
                LEFT JOIN 
                    ktv_members_extension kms on kms.MemberID = vso3.ObjID
                LEFT JOIN 
                    sys_log_sms_transaction slst on slst.SupplyTransID = a.SupplyTransID
                LEFT JOIN 
                    ktv_village kv ON kv.VillageID = b.VillageID
                LEFT JOIN 
                    ktv_subdistrict ks ON ks.SubDistrictID = kv.SubDistrictID
                LEFT JOIN 
                    ktv_district kd ON kd.DistrictID = ks.DistrictID
                LEFT JOIN 
                    ktv_province kp ON kp.ProvinceID = kd.ProvinceID
                WHERE
                    a.StatusCode = 'active'
                AND 
                    slst.StatusCode = 'active'
                $where
                $whereFilterProvince
                $whereFilterDistrict
                $whereFilterDate
                $order
                $sqlLimit
        ";

        $Q = $this->db->query($sql,array());

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $val){
                $val = $this->check_isNull($val);
                $val->SupplyID = $val->MemberID;
            }

            if ($_SESSION['PartnerID'] != '14' || $_SESSION['PartnerID'] != '194'){ // bukan WAGS dan mill perak
                $query = $this->db->query('SELECT FOUND_ROWS() AS total');
                $data['data'] = $result;
                $data['total'] = $query->row()->total;
            } else {
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
            }
            
            return $data;
        }

        return $data;
    }

    function resendSMS($post){
        $sql = "SELECT
                    a.*,
                    b.DateTransaction,
                    b.VolumeNetto,
                    c.MemberName,
                    su.UserRealName AS agentName
                FROM
                    sys_log_sms_transaction a
                LEFT JOIN 
                    ktv_tc_supplychain_transaction b ON b.SupplyTransID = a.SupplyTransID
                LEFT JOIN 
                    ktv_members c ON c.MemberID = b.SupplyID
                LEFT JOIN 
                    ktv_tc_supplychain_farmer ktsf ON ktsf.FarmerID = c.MemberID 
                LEFT JOIN 
                    sys_user su ON su.UserId = a.CreatedBy 
                WHERE
                    a.StatusCode = 'active' AND a.AutoID = ?";
        $query = $this->db->query($sql, array($post['AutoID']));
        
        if($query->num_rows() > 0){
            $data = $query->result_array();
            
            $phone = $data[0]['Handphone'];
            if($phone!='' && $phone!='null' && $phone!='undefined' && strlen($phone) > 9){
                if(preg_match("/^0/", $phone)) {
                    $phone = preg_replace('/^0/', '62', $phone);
                } else {
                    if(!preg_match('/^(\+62|62)/',$phone)){
                        $phone = '62'. $phone;
                    } 
                }
                
                if(preg_match("/^0/", $phone)) {
                    $phone = preg_replace('/^0/', '62', $phone);
                } else {
                    if(!preg_match('/^(\+62|62)/',$phone)){
                        $phone = '62'. $phone;
                    } 
                }
                $phone = str_replace(' ', '', $phone);
                $to = $phone;
                $msg = $data[0]['request'];

                $to = str_replace(' ', '', $to);
                $to = str_replace('-', '', $to);
                $to = str_replace('+', '', $to);

                $farmerName         = $data[0]['MemberName'];
                $agentName          = $data[0]['agentName'];
                $dateTransaction    = $data[0]['DateTransaction'];
                $volumeNetto        = $data[0]['VolumeNetto'];

                $ProviderID         = $data[0]['ProviderID'];
                $SupplyTransID      = $data[0]['SupplyTransID'];
                $Createdby          = $data[0]['CreatedBy'];

                $MessageBody = "yth ".$farmerName." anda telah menjual buah untuk ".$agentName." pada tanggal ".$dateTransaction.", dengan berat ".$volumeNetto." kg.";
                
                $msg2 = urlencode($MessageBody);

                $src = "src="."KOLTIVA";
                
                if(strlen($msg2) > 160){
                    $url = "http://smsapiv2.1rstwap.com:8080/smsapi/pages/sendSmsLatinConcat.do?g3p4i=Koltiva&G4PIpw=Koltiva%205M5&$src&dst=$to&msg=$msg2";
                }else{
                    $url = "http://smsapiv2.1rstwap.com:8080/smsapi/pages/sendSmsLatin.do?g3p4i=Koltiva&G4PIpw=Koltiva%205M5&$src&dst=$to&msg=$msg2";
                }

                $url = filter_var($url,FILTER_SANITIZE_URL);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json'
                ));
                curl_setopt($ch, CURLOPT_TIMEOUT, 1); //timeout in seconds
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $request = $url;
                curl_close($ch);
                
                $insert = array(
                    'SupplyTransID' => @$SupplyTransID,
                    'ProviderID' => @$ProviderID,
                    'Handphone' => @$to,
                    'method' => 'GET',
                    'url' => @$url,
                    'request' => @$request,
                    'response' => @$result,
                    'DateCreated' => date('Y-m-d H:i:s'),
                    'CreatedBy' => @$Createdby,
                    'DateUpdated' => date('Y-m-d H:i:s')
                );
                
                $this->db->insert('sys_log_sms_transaction', $insert);

                $return['success'] = "true";
                $return['message'] = lang("The process is complete");
            }else{
                $return['success'] = "false";
                $return['message'] = lang("Phone number not valid");
            }
        }else{
            $return['success'] = "false";
            $return['message'] = lang("Data not found");
        }
        return $return;
    }

    function checkingSMS($post){
        $sql = "SELECT
                a.*,
                b.DateTransaction,
                b.VolumeNetto,
                c.MemberName,
                su.UserRealName AS agentName
            FROM
                sys_log_sms_transaction a
            LEFT JOIN 
                ktv_tc_supplychain_transaction b ON b.SupplyTransID = a.SupplyTransID
            LEFT JOIN 
                ktv_members c ON c.MemberID = b.SupplyID
            LEFT JOIN 
                ktv_tc_supplychain_farmer ktsf ON ktsf.FarmerID = c.MemberID 
            LEFT JOIN 
                sys_user su ON su.UserId = a.CreatedBy 
            WHERE
                a.StatusCode = 'active' AND a.AutoID = ?";
        $query = $this->db->query($sql, array($post['AutoID']));
      
        if($query->num_rows() > 0){
            $data = $query->result_array();
            $phone = $data[0]['Handphone'];
            if($phone!='' && $phone!='null' && $phone!='undefined' && strlen($phone) > 9){
                if(preg_match("/^0/", $phone)) {
                    $phone = preg_replace('/^0/', '62', $phone);
                } else {
                    if(!preg_match('/^(\+62|62)/',$phone)){
                        $phone = '62'. $phone;
                    } 
                }
                
                if(preg_match("/^0/", $phone)) {
                    $phone = preg_replace('/^0/', '62', $phone);
                } else {
                    if(!preg_match('/^(\+62|62)/',$phone)){
                        $phone = '62'. $phone;
                    } 
                }
                $phone = str_replace(' ', '', $phone);
                $to  = $phone;
                $msg = $data[0]['request'];

                $to = str_replace(' ', '', $to);
                $to = str_replace('-', '', $to);
                $to = str_replace('+', '', $to);

                $ProviderID         = $data[0]['ProviderID'];
                $SupplyTransID      = $data[0]['SupplyTransID'];
                $Createdby          = $data[0]['CreatedBy'];

                $SmsLogID           = $data[0]['AutoID'];

                $logResponseSms     = $data[0]['response'];

                $msgID              = $logResponseSms;

                if(strlen($msg2) > 160){
                    $url = "http://smsapiv2.1rstwap.com:8080/smsapi/pages/smsDeliveryStatus.do?msgID=$msgID";
                }else{
                    $url = "http://smsapiv2.1rstwap.com:8080/smsapi/pages/smsDeliveryStatus.do?msgID=$msgID";
                }

                $url = filter_var($url,FILTER_SANITIZE_URL);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json'
                ));
                curl_setopt($ch, CURLOPT_TIMEOUT, 1); //timeout in seconds
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $request = $url;
                curl_close($ch);
                
                $insert = array(
                    'SupplyTransID' => @$SupplyTransID,
                    'ProviderID' => @$ProviderID,
                    'SmsLogID' => @$SmsLogID,
                    'Handphone' => @$to,
                    'method' => 'GET',
                    'url' => @$url,
                    'request' => @$request,
                    'response' => @$result,
                    'DateCreated' => date('Y-m-d H:i:s'),
                    'CreatedBy' => @$Createdby,
                    'DateUpdated' => date('Y-m-d H:i:s')
                );
                
                $this->db->insert('sys_log_sms_transaction_status', $insert);
                
                $this->db->where('AutoID', $SmsLogID);
                $this->db->update('sys_log_sms_transaction', array('DateUpdated' => date('Y-m-d H:i:s'), 'Status' => $result));

                $return['success'] = "true";
                $return['message'] = lang("The process is complete");
            }else{
                $return['success'] = "false";
                $return['message'] = lang("Phone number not valid");
            }
        }else{
            $return['success'] = "false";
            $return['message'] = lang("Data not found");
        }
        return $return;
    }

    public function get_data_user($UserID){ 
        $sql = "SELECT
                    a.SupplychainID
                FROM
                    view_tc_supplychain_staff a
                WHERE
                    a.UserID = '$UserID'
                ORDER BY partnerid DESC
        ";

        $Q = $this->db->query($sql);
        
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $val){
                $SupplychainID = $val->SupplychainID;
            }
           
            $data = $SupplychainID;
            
            return $data;
        }

        return $data;
    }
    

    public function get_agent_seller($SupplychainID){
        $sql = "
            SELECT
                m.MemberID ObjID,
                m.MemberName Name
            FROM
                ktv_tc_supplychain_org a
                LEFT JOIN `ktv_tc_supplychain_org_rel` b ON a.SupplyChainID = b.ParentID
                LEFT JOIN `ktv_tc_supplychain_org` a1 ON a1.SupplyChainID = b.ChildID
                LEFT JOIN ktv_members m ON m.MemberID = a1.ObjID
            WHERE
                a.SupplyChainID = ?;
        ";

        $query = $this->db->query($sql,array($SupplychainID));

        $return = array('success' => true, 
        'message' => 'Data Berhasil Ditampilkan',
        'total' => $query->num_rows(), 
        'data' => $query->result());

        return $return;
    }
    public function get_do_seller($SupplychainID){
        $sql = "
            SELECT 
                a.ChildID as ObjID, 
                c.`Name` as Name
            FROM (`ktv_tc_supplychain_org_rel` a)
            JOIN `view_tc_supplychain_org` b ON `b`.`SupplychainID`=`a`.`ParentID`
            JOIN `ktv_program_partner` p ON `p`.`PartnerID`=`b`.`PartnerID`
            JOIN view_tc_supplychain_org c on c.SupplychainID = a.ChildID
            WHERE `a`.`StatusCode` =  'active'
            AND a.ParentID =  ?
        ";

        $query = $this->db->query($sql,array($SupplychainID));

        $return = array('success' => true, 
        'message' => 'Data Berhasil Ditampilkan',
        'total' => $query->num_rows(), 
        'data' => $query->result());

        return $return;
    }
    public function check_isNull($v){
        foreach($v as $key => $value){
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }
    public function submit($params){
        $result = false;
        $insid = 0;
        $error = ''; 
        
        $data=array();
        foreach ($params as $key => $value) {
            $keyNew = str_replace("Koltiva_view_Traceability_new_Transaction_FormTransaction-", '', $key);
            if($value == "") $value = null;
            $data[$keyNew] = $value;
        }

        try{
            $this->db->trans_begin();

            if($data["SalesType"] == 1){
                $SalesType      = "Farmer";
                $SupplyID       = $data["FarmerID"];             
                $PlantationNr   = $data["PlantationNr"];
            }
            if($data["SalesType"] == 2){
                $SalesType      = "Nonfarmer";
                $SupplyID       = $this->input->request_headers()['Sid'];             
                $PlantationNr   = explode('-', $data["PlantationNrNonFarmer"]);
                $PlantationNr   = $PlantationNr[0];
            }
            if($data["SalesType"] == 3){
                $SalesType      = "Batch";
                $SupplyID       = $this->input->request_headers()['Sid'];
                $PlantationNr   = 1;
                if($data['SellerType'] == "external"){
                    $contentadd = array(
                        "MillID" => (isset($data["Mill"])) ? $data["Mill"] : null,
                        "MillOther" => (isset($data["OtherMill"])) ? $data["OtherMillName"] : null
                    );
                }
                if($data["SellerType"] == "other"){
                    $contentadd["DOID"]        = (isset($data["DO"])) ? $data["DO"] : null;
                    $contentadd["DOOther"]     = (isset($data["OtherDOName"])) ? $data["OtherDOName"] : null;
                    $contentadd["AgentID"]     = (isset($data["Agent"])) ? $data["Agent"] : null;
                    $contentadd["AgentOther"]  = (isset($data["OtherAgentName"])) ? $data["OtherAgentName"] : null;
                    $contentadd["AgentOtherNIK"]     = (isset($data["OtherAgentNin"])) ? $data["OtherAgentNin"] : null;
                    $contentadd["AgentOtherSurvey"]  = (isset($data["OtherAgentSurvey"])) ? $data["OtherAgentSurvey"] : null;
                }
            }
            
            // $isNpwp = $data['Opsinpwp'] == true ? 1 : 0;
            // $IsStamp = $data['OpsiStampdeduction'] == true ? 1 : 0;
            $content = array(
                "SupplychainID"=> $this->input->request_headers()['Sid'],
                "DateTransaction"=> $data["DateTransaction"],
                "SupplyType"=> $SalesType, //Farmer, Batch
                "SupplyID"=> $SupplyID,
                "PlantationNr"=> $PlantationNr,  
                "ContractPrice"=> $this->replacestr($data['ContractPrice']), 
                "VolumeBruto"=> $data['VolumeBruto'],  // Ini disamakan saja isinya
                "VolumeNetto"=> $data['VolumeNetto'], 
                "Bunches"=> $data['Bunches'], 
                //"NetPrice"=> $this->replacestr($data['NetPrice']), 

                "InvoiceNumber"=> $data['InvoiceNumber'], 
                "TotalPayment"=> $this->replacestr($data['TotalPayment']),
                "StatusCode" => 'active' 
            );

            // echo "<pre>";
            // print_r($content);
            // die;
            //print_r($content);die;
            if($data['STID'] !='' ){
               
                /* Update data Transaction */
                $this->db->where('SupplyTransID', @$data['STID']);
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;

                if(isset($contentadd)){
                    $content = (object) array_merge((array) $content, (array) $contentadd);
                }
                $this->db->update('ktv_tc_supplychain_transaction', $content);
                $insid = @$data['STID'];

            }else{
                $content['TransNumber'] = generateTransTraceabilityNumber($content['SupplychainID']); 
                //$content['InvoiceNumber'] = ""; 
                $content['DateCreated'] = date('Y-m-d H:i:s');
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;

                if(isset($contentadd)){
                    $content = (object) array_merge((array) $content, (array) $contentadd);
                }
                $this->db->insert('ktv_tc_supplychain_transaction', $content);
                
                $insid = $this->db->insert_id();
            }
            //echo $this->db->last_query();die;
            /* Quality
            if($data['quality']){
                $this->db->where('SupplyTransID', $insid);
                $this->db->delete('ktv_tc_supplychain_transaction_quality');

                $quality = $data['quality']; 
                $dt = json_decode($quality);
                 
                foreach($dt as $k => $quality){
                   
                    $type =  $this->cek_type($quality->QualityID);
                    if($type == 'combo'){
                    $QS = $this->db->select('ValueQualityID')
                              ->from('ktv_tc_supplychain_quality_value')
                              ->where('QualityID', $quality->QualityID)
                              ->where('Value', $quality->Value)
                              ->where('StatusCode', 'active')
                              ->get()->row();
                   
                    $content_quality = array(
                        'SupplyTransID' => $insid,
                        'QualityID' => $quality->QualityID,
                        'Value' => @$QS->ValueQualityID,
                        'StatusCode' => 'active'
                    );
                    }else{
                         $content_quality = array(
                            'SupplyTransID' => $insid,
                            'QualityID' => $quality->QualityID, 
                            'Value' => $quality->Value,
                            'StatusCode' => 'active'
                        );
                     } 
                    //print_r($content_quality);die;
                    $content_quality['DateCreated'] = date('Y-m-d H:i:s');
                    $content_quality['DateUpdated'] = date('Y-m-d H:i:s');
                    $content_quality['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                    $this->db->insert('ktv_tc_supplychain_transaction_quality', $content_quality);
                }
            }
             */
             
            if (($this->db->trans_status() == false)) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
                $result = false;
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result,'message' => 'Save data success', 'SupplyTransID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
    
    private function cek_type($QualityID)
    { 
            $this->db->where('QualityID', $QualityID);
            $r= $this->db->select('Type')->from('ktv_tc_supplychain_quality')->get()->row();
            return $r->Type;
    }
    
    function replacestr($form)
    {
        return str_replace(',','',$form);
    }

    public function get_data_farmer_certified($MemberID){
        $sql = "SELECT
                cp.CertProgName
            FROM
                ktv_members m
            LEFT JOIN
                ktv_ref_certification_program cp on cp.CertProgID = m.isCertified
            WHERE
                m.MemberID = ?";
        $query = $this->db->query($sql,array($MemberID));

        if($query->num_rows()>0){
            return array("Certification"=> $query->row()->CertProgName);
        }

        return null;
    }

    public function get_data_farmer($PartnerID, $SupplychainID){ 
        $start =  $this->input->get('start');
        $limit =  $this->input->get('limit');
        $query = $this->input->get('query');

        
        $return = array('data' => array(), 'total' => 0);

        if($SupplychainID != ''){
            if($query != ""){
                $where = " AND m.MemberName like ?";
            }
            $sql = "SELECT
                `m`.`MemberID`,
                `m`.`MemberDisplayID`,
                `m`.`MemberName`,
                `m`.`Nin`,
                `m`.`DateOfBirth`,
                `m`.`Gender`,
                `m`.`Address`,
                `m`.`Handphone`,
                `m`.`Photo`,
                `m`.`GapoktanID`,
                `g`.`GapoktanName`,
                `m`.`FarmerGroupID`,
                `h`.`GroupName`,
                `e`.`ProvinceID`,
                `f`.`Province`,
                `e`.`DistrictID`,
                `e`.`District`,
                `d`.`SubDistrictID`,
                `d`.`SubDistrict`,
                `m`.`VillageID`,
                `c`.`Village`,
                `cp`.`CertProgName`
            FROM
                ktv_tc_supplychain_farmer sf
            LEFT JOIN
                ktv_members m on m.MemberID = sf.FarmerID
            LEFT JOIN 
                ktv_ref_certification_program cp ON cp.CertProgID = m.isCertified
            LEFT JOIN 
                `ktv_village` c ON `m`.`VillageID` = `c`.`VillageID`
            LEFT JOIN 
                `ktv_subdistrict` d ON `c`.`SubDistrictID` = `d`.`SubDistrictID`
            LEFT JOIN 
                `ktv_district` e ON `e`.`DistrictID` = `d`.`DistrictID`
            LEFT JOIN 
                `ktv_province` f ON `f`.`ProvinceID` = `e`.`ProvinceID`
            LEFT JOIN 
                `ktv_gapoktan` g ON m.`GapoktanID` = `g`.`GapoktanID`
            LEFT JOIN 
                `ktv_farmer_group` h ON m.`FarmerGroupID` = `h`.`FarmerGroupID`
            LEFT JOIN 
                `ktv_survey_plot` i ON m.`MemberID` = `i`.`MemberID`
            WHERE
                sf.SupplychainID = ?
            AND
                m.StatusCode = 'active'
            $where
            GROUP BY
                m.MemberID";

            $Q = $this->db->query($sql,array($SupplychainID,($query != ''? "%$query%" : '')));
        }else{
            $this->db->select(' b.MemberID, b.MemberDisplayID, b.MemberName, b.Nin, b.DateOfBirth, b.Gender, b.Address, b.Handphone, b.Photo, b.GapoktanID, g.GapoktanName, b.FarmerGroupID, h.GroupName, e.ProvinceID, f.Province, e.DistrictID, e.District, d.SubDistrictID, d.SubDistrict, b.VillageID, c.Village, cp.CertProgName ');
            $this->db->from('ktv_access_partner_member a');
            $this->db->join('ktv_members b', 'a.apmMemberID=b.MemberID');
            $this->db->join('ktv_ref_certification_program cp', 'cp.CertProgID=b.isCertified');
            $this->db->join('ktv_village c', 'b.VillageID=c.VillageID', 'left');
            $this->db->join('ktv_subdistrict d', 'c.SubDistrictID=d.SubDistrictID', 'left');
            $this->db->join('ktv_district e', 'e.DistrictID=d.DistrictID', 'left');
            $this->db->join('ktv_province f', 'f.ProvinceID=e.ProvinceID', 'left');
            $this->db->join('ktv_gapoktan g', 'b.GapoktanID=g.GapoktanID', 'left');
            $this->db->join('ktv_farmer_group h', 'b.FarmerGroupID=h.FarmerGroupID', 'left');
            $this->db->join('ktv_survey_plot i', 'b.MemberID=i.MemberID');
            $this->db->where('a.apmPartnerID', $PartnerID);
            $this->db->where('b.StatusCode', 'active');
            $this->db->group_by('b.MemberID');      
            if($query){
                $this->db->like('MemberName', $query); 
            }
            
            // $this->db->limit($limit, $start); 
            $this->db->order_by('b.MemberID', 'DESC');  
            $Q = $this->db->get();
        }

        /**Paging total data*/
        // $this->db->from('ktv_members');
        // $this->db->where('StatusCode', 'active'); 
        $jmldata = $Q->num_rows(); 
        /*end*/
        
        // echo $this->db->last_query();die; 
        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
                $val->MemberNames = $val->MemberName.'|'.$val->MemberDisplayID;
            }
            $data['data'] = $result; 
            $data['total'] = $jmldata; 
            
            return $data;
        }
        return $data;
    }

    public function GetFarmers($search)
    {
        $data = [];
        
        $SupplychainID  = $_SESSION['SupplychainID'];
        $query          = $search['query'];
        
        if($SupplychainID != ''){
            if($search['query'] != ""){
                $where = " AND m.MemberName like ?";
            }
            $sql = "SELECT
                `a`.`apmMemberID` AS MemberID,
                `m`.`MemberDisplayID`,
                `m`.`MemberName`,
                `m`.`Nin`,
                `m`.`DateOfBirth`,
                `m`.`Gender`,
                `m`.`Address`,
                `m`.`Handphone`,
                `m`.`Photo`,
                `m`.`GapoktanID`,
                `g`.`GapoktanName`,
                `m`.`FarmerGroupID`,
                `h`.`GroupName`,
                `e`.`ProvinceID`,
                `f`.`Province`,
                `e`.`DistrictID`,
                `e`.`District`,
                `d`.`SubDistrictID`,
                `d`.`SubDistrict`,
                `m`.`VillageID`,
                `c`.`Village`,
                `cp`.`CertProgName`,
                `m`.`FarmerCategory`,
                `m`.`Latitude`,
                `m`.`Longitude`,
                IFNULL(`m`.`isCertified`,'-') isCertified
            FROM
                ktv_access_partner_member a
            LEFT JOIN
                ktv_members m on m.MemberID = a.apmMemberID
            LEFT JOIN 
                ktv_tc_supplychain_farmer sf on m.MemberID = sf.FarmerID
            LEFT JOIN 
                ktv_ref_certification_program cp ON cp.CertProgID = m.isCertified
            LEFT JOIN 
                `ktv_village` c ON `m`.`VillageID` = `c`.`VillageID`
            LEFT JOIN 
                `ktv_subdistrict` d ON `c`.`SubDistrictID` = `d`.`SubDistrictID`
            LEFT JOIN 
                `ktv_district` e ON `e`.`DistrictID` = `d`.`DistrictID`
            LEFT JOIN 
                `ktv_province` f ON `f`.`ProvinceID` = `e`.`ProvinceID`
            LEFT JOIN 
                `ktv_gapoktan` g ON m.`GapoktanID` = `g`.`GapoktanID`
            LEFT JOIN 
                `ktv_farmer_group` h ON m.`FarmerGroupID` = `h`.`FarmerGroupID`
            LEFT JOIN 
                `ktv_survey_plot` i ON m.`MemberID` = `i`.`MemberID`
            WHERE
                sf.SupplychainID = ?
            AND 
                NOW()BETWEEN sf.DateStart AND sf.DateEnd
            AND
                m.StatusCode = 'active'
            $where
            GROUP BY
                m.MemberID";
            $data  = $this->db->query($sql,array($SupplychainID,($query != ''? "%$query%" : '')));
        }else{
            $this->db->select(' b.MemberID, b.MemberDisplayID, b.MemberName, b.Nin, b.DateOfBirth, b.Gender, b.Address, b.Handphone, b.Photo, b.GapoktanID, g.GapoktanName, b.FarmerGroupID, h.GroupName, e.ProvinceID, f.Province, e.DistrictID, e.District, d.SubDistrictID, d.SubDistrict, b.VillageID, c.Village, cp.CertProgName ');
            $this->db->from('ktv_access_partner_member a');
            $this->db->join('ktv_members b', 'a.apmMemberID=b.MemberID');
            $this->db->join('ktv_ref_certification_program cp', 'cp.CertProgID=b.isCertified');
            $this->db->join('ktv_village c', 'b.VillageID=c.VillageID', 'left');
            $this->db->join('ktv_subdistrict d', 'c.SubDistrictID=d.SubDistrictID', 'left');
            $this->db->join('ktv_district e', 'e.DistrictID=d.DistrictID', 'left');
            $this->db->join('ktv_province f', 'f.ProvinceID=e.ProvinceID', 'left');
            $this->db->join('ktv_gapoktan g', 'b.GapoktanID=g.GapoktanID', 'left');
            $this->db->join('ktv_farmer_group h', 'b.FarmerGroupID=h.FarmerGroupID', 'left');
            $this->db->join('ktv_survey_plot i', 'b.MemberID=i.MemberID');
            $this->db->where('a.apmPartnerID', $PartnerID);
            $this->db->where('b.StatusCode', 'active');
            $this->db->group_by('b.MemberID');      
            if($search['query']){
                $this->db->like('MemberName', $query); 
            }
            
            $this->db->order_by('b.MemberID', 'DESC');  
            $data = $this->db->get();
        }

        return $data;
    }

    public function get_data_plantation_new($MemberID){
        $return = array('data' => array(), 'total' => 0);

        $checkMember = $this->db->select('a.MemberID')
                ->from('ktv_members a')
                ->where('a.StatusCode', 'active')
                ->where('a.MemberDisplayID', $MemberID)
                ->get();

        if($checkMember->num_rows() > 0) {
            $result = $checkMember->result();
            foreach($result as $key => $val){
                $id = $val->MemberID;
            }

            $data['data'] = $result;
            $data['total'] = $checkMember->num_rows();
        }

        $Q = $this->db->select(' a.PlotNr as PlantationNr, 
                                               a.SurveyNr, 
                                               a.VillageID, 
                                               a.Latitude, 
                                               a.Longitude, 
                                               c.Village ')
                ->from('ktv_survey_plot a')
                //->join('ref_tc_farming_type b', 'a.FarmingType=b.FarmingTypeID', 'left')
                ->join('ktv_village c', 'a.VillageID=c.VillageID', 'left')
                ->where('a.StatusCode', 'active')
                ->where('a.MemberID', $id)
                ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
                //$val->PlantationName = $val->PlantationNr.'|'.$val->FarmingTypeName;
                $val->PlantationName = $val->PlantationNr;
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        

        return $return;
    }

    public function get_data_plantation($MemberID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select(' a.PlotNr as PlantationNr, 
                                               a.SurveyNr, 
                                               a.VillageID, 
                                               a.Latitude, 
                                               a.Longitude, 
                                               c.Village ')
                ->from('ktv_survey_plot a')
                //->join('ref_tc_farming_type b', 'a.FarmingType=b.FarmingTypeID', 'left')
                ->join('ktv_village c', 'a.VillageID=c.VillageID', 'left')
                ->where('a.StatusCode', 'active')
                ->where('a.MemberID', $MemberID)
                ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
                //$val->PlantationName = $val->PlantationNr.'|'.$val->FarmingTypeName;
                $val->PlantationName = $val->PlantationNr;
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        

        return $return;
    }
    public function get_data_package_type($SupplychainID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('PackageID, PackageType, PackageWeight, PackageCapacity')
          ->from('ktv_tc_supplychain_package')
          ->where('SupplychainID', $SupplychainID)
          ->where('StatusCode', 'active')
          ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        return $data;
    }
    public function get_data_quality($SupplychainID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('`QualityID`, `SupplychainID`, `Name`, `Formula`, `Order`, `Type`, `MinValue`, `MaxValue`, `StandardValue`, `IsPrintVisible`')
          ->from('ktv_tc_supplychain_quality')
          ->where('SupplychainID', $SupplychainID)
          ->where('StatusCode', 'active')
          ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }
            $data['data'] = $result;
            $data['total'] = $Q->num_rows();
            return $data;
        }
        return $data;
    }
    public function get_data_quality_value($QualityID){
        $return = array('data' => array(), 'total' => 0);

        $Q = $this->db->select('`ValueQualityID`, `Value`') 
          ->from('ktv_tc_supplychain_quality_value')
          ->where('QualityID', $QualityID)
          ->where('StatusCode', 'active')
          ->get();

        if($Q->num_rows()){
            $result = $Q->result();
            foreach($result as $key => $val){
                $val = $this->check_isNull($val);
            }
            $return['data'] = $result;
            $return['total'] = $Q->num_rows();
            return $return;
        }
        return $return;
    }

    private function getFarming($id){
        $Q = $this->db->get_where('ref_tc_farming_type', array('FarmingTypeID' => $id));

        if($Q->num_rows()){
            return $Q->row()->FarmingTypeName;
        }else{
            return '';
        }
    }
    
    function getTransaksi($SupplyTransID,$SupplychainID)
    {
        $SQL ='select X.agCompanyName as CompanyName, B.Name, C.MemberID, C.MemberDisplayID, C.MemberName as Namapetani, D.GroupName, A.*  from 
                ktv_tc_supplychain_transaction A 
                LEFT JOIN view_tc_supplychain_org B ON A.SupplychainID = B.SupplychainID
                LEFT JOIN ktv_members C ON A.SupplyID = C.MemberID
                LEFT JOIN ktv_farmer_group D ON D.FarmerGroupID = C.FarmerGroupID
                LEFT JOIN ktv_members_extension X ON X.MemberID = C.MemberID
                where A.SupplyTransID = ? and A.SupplychainID = ? ';
        $t = $this->db->query($SQL, array($SupplyTransID, $SupplychainID));   
    
        return $t->row_array();
    }
    
    function getTransaksiQuality($SupplyTransID)
    {
        $SQL ='select B.Name, A.Value from ktv_tc_supplychain_transaction_quality A, ktv_tc_supplychain_quality B
               where A.QualityID = B.QualityID and A.SupplyTransID = ?';
        $t = $this->db->query($SQL, array($SupplyTransID));
        //echo $this->db->last_query();die;
        return $t->result();
    }

    public function check_role_transaction(){
        
        $Q = $this->db->query("select * from ktv_tc_supplychain_org where SupplychainID = '".$_SESSION['SupplychainID']."' AND PartnerID = '".$_SESSION['PartnerID']."' "); 

        $data['AllTab'] = 1;
        $data['Transaction'] = 0;
        $data['Batch'] = 0;
        $data['Sent'] = 0;
        $data['TypeMill'] = 0;

        if($Q->num_rows()){
            $data['AllTab'] = 0;
            $data['Transaction'] = 0;
            $data['Batch'] = 0;
            $data['Sent'] = 0;
            $data['TypeMill'] = 0;

            $content = $Q->row();
            if((int)$content->IsFarmer == 1) $data['Transaction'] = 1;
            if((int)$content->IsBatch == 1) $data['Batch'] = 1;
            if((int)$content->IsSent == 1) $data['Sent'] = 1;
            if((int)$content->ObjType == 'mill') $data['TypeMill'] = 'mill';
        }

        return array('success' => true, 'data' => $data);
    }

    public function SmsDetailFormOpen($AutoID){

        $sql = "SELECT
            a.`SupplyTransID`,
            a.`SupplychainID`,
            a.`SupplyBatchID`,
            a.`TransNumber`,
            a.`DateTransaction`,
            a.`DateCreated`,
            a.`CreatedBy`,
            a.`DateUpdated`,
            a.`LastModifiedBy`, 
            IF(
                b.MemberName IS NULL OR b.MemberName = '',
                IF(
                    m2.MillName IS NULL OR m2.MillName = '',
                    IF(
                        a.MillOther IS NULL OR a.MillOther = '',
                        IF(
                            mem.Name IS NULL OR mem.Name = '',
                            IF(
                                kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                IF(
                                    a.DOOther IS NULL OR a.DOOther = '',
                                    IF(
                                        a.AgentOther IS NULL OR a.AgentOther = '',
                                        'Nonfarmer',
                                        a.AgentOther
                                    ),
                                    a.DOOther
                                ),
                                kms.agCompanyName
                            ),
                            mem.Name
                        ),
                        a.MillOther
                    ),
                    m2.MillName
                ),
                b.MemberName
            ) AS FarmerName,
            vso2.Name AS AgentName,
            slst.SMSType,
            IF(slst.SupplyTransID IS NULL, 'not yet sent', 
                IF(
                    slst.ProviderID=2,
                    IF(slst.response!='' && LENGTH(slst.response) > 5, 'Delivered', 'Undelivered'),
                    CASE
                        WHEN slst.response LIKE '%\"status\":\"0\",%' THEN 'Delivered'
                        ELSE 'Undelivered'
                    END
                )
            ) SMSStatus,
            slst.Handphone,
            slst.DateCreated AS SendDate,
            slst.AutoID,
            slst.Request AS SmsText,
            slst.response
        FROM
            ktv_tc_supplychain_transaction a
        LEFT JOIN
            ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'
        LEFT JOIN
            ktv_members bb on a.SupplyID = bb.MemberID 
        LEFT JOIN
            ktv_mill m2 on m2.MillID = a.MillID
        LEFT JOIN
            view_tc_supplychain_org mem on mem.SupplychainID = a.MillID
        LEFT JOIN
            ktv_members mem2 on mem2.MemberID = a.AgentID
        LEFT JOIN
            view_tc_supplychain_org vso2 on vso2.SupplychainID=a.SupplychainID
        LEFT JOIN
            view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
        LEFT JOIN 
            ktv_members_extension kms on kms.MemberID = vso3.ObjID
        LEFT JOIN 
            sys_log_sms_transaction slst on slst.SupplyTransID = a.SupplyTransID
        WHERE
            a.StatusCode = 'active'
        AND 
            slst.StatusCode = 'active'
        AND 
            slst.AutoID =  ?";
        $query = $this->db->query($sql, array($AutoID));
        // echo '<pre>'.$this->db->last_query();die;
        
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function SmsChecking($AutoID){

       $sql = "SELECT 
                    a.AutoID,
                    b.request,  
                    b.response AS ResponseStatus,
                    b.DateCreated,
                    c.Status
                FROM 
                    sys_log_sms_transaction a
                LEFT JOIN 
                    sys_log_sms_transaction_status b ON a.AutoID = b.SmsLogID
                LEFT JOIN 
                    ref_tc_sms_status c ON c.StatusID = b.response
                WHERE 
                a.AutoID = ?";
        $query = $this->db->query($sql, array($AutoID));
        // echo '<pre>'.$this->db->last_query();die;
        
        $data = $query->row_array();

        if($query->num_rows()){
            $result = $query->result();
            foreach($result as $val){
                $val = $this->check_isNull($val);
                $val->SupplyID = $val->MemberID;
            }

            if ($_SESSION['PartnerID'] != '14' || $_SESSION['PartnerID'] != '194'){ // bukan WAGS dan mill perak
                $query = $this->db->query('SELECT FOUND_ROWS() AS total');
                $data['data'] = $result;
                $data['total'] = $query->row()->total;
            } else {
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
            }
            
            return $data;
        }
    }

    public function TransactionFormOpen($SupplyTransID) 
    {
        $return = array();

         @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_tc_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        if($SupplychainID==''){
            @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_tc_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID; 
        }

        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                a.`SupplyTransID`,
                a.`SupplychainID`,
                a.`SupplyBatchID`,
                a.`TransNumber`,
                a.`InvoiceNumber`,
                a.`DateTransaction`,
                CASE
                        WHEN a.SupplybaseCategoryID = 1 THEN 'Farmer Plasma'
                        WHEN a.SupplybaseCategoryID = 2 THEN 'Direct Smallholder'
                        WHEN a.SupplybaseCategoryID = 3 THEN 'Agent / Dealer / Vendor'
                        WHEN a.SupplybaseCategoryID = 4 THEN 'Owner Estate'
                        WHEN a.SupplybaseCategoryID = 5 THEN 'External Estate'
                        ELSE '-'
                END AS SupplyType,
                a.`PlantationNr`,
                a.`PlantationNr` FarmNumber,
                a.`VolumeBruto`,
                a.`VolumeNetto`,
                a.`VolumeCutting`,
                a.`PackageID`,
                a.`Bunches`,
                a.`FFBCount` PackageNumber,
                a.`PackageWeight`,
                a.`DetailTypeID`,
                a.`TransStatusID`,
                a.`FarmingTypeID`,
                a.ContractPrice,
                a.NetPrice,
                a.DiscountPrice,
                a.TotalPayment,
                a.PaymentReduction,
                a.PaymentPaid,
                a.`Notes`,
                a.`ChangeLog`,
                a.`ChangeBy`,
                a.`DateCreated`,
                a.`CreatedBy`,
                a.`DateUpdated`,
                a.`LastModifiedBy`, 
                a.`PaymentStatusID`,
                pm.`PaymentMethodID`,
                pm.`PaymentMethod`,
                a.`BankCode`,
                a.`BankName`,
                a.`AccountNumber`,
                a.`AccountName`,
                a.`uid`,
                a.`Status`,
                vso3.`PartnerID`,
                b.`Address`,  
                b.BankBranchName,
                b.BankAccNumber,
                b.BankClientID,
                b.FarmerCategory,    
                b.Latitude,
                b.Longitude,
                CASE
                    WHEN b.Gender = 'm' THEN 'Male'
                    WHEN b.Gender = 'f' THEN 'Female'
                    ELSE '-'
                END Gender,
                b.`Address`,     
                kv.Village,          
                CONCAT('Country: ', kc.`CountryName`,', Province: ',kp.Province,', District: ',kd.District,', Sub District: ',IFNULL(sd.SubDistrict,'-'),', Village: ',IFNULL(kv.Village,'-')) AS Region,
                FLOOR(DATEDIFF(CURDATE(), b.DateOfBirth) / 365.25) AS Age,
                c.PackageType,
                IF(
                    b.MemberName IS NULL OR b.MemberName = '',
                    IF(
                        m2.MillName IS NULL OR m2.MillName = '',
                        IF(
                            a.MillOther IS NULL OR a.MillOther = '',
                            IF(
                                mem.Name IS NULL OR mem.Name = '',
                                IF(
                                    kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                    IF(
                                        a.DOOther IS NULL OR a.DOOther = '',
                                        IF(
                                            a.AgentOther IS NULL OR a.AgentOther = '',
                                            'Nonfarmer',
                                            a.AgentOther
                                        ),
                                        a.DOOther
                                    ),
                                    kms.agCompanyName
                                ),
                                mem.Name
                            ),
                            a.MillOther
                        ),
                        m2.MillName
                    ),
                    b.MemberName
                ) MemberName,
                e.Name,
                IFNULL(b.MemberDisplayID, IFNULL(bb.MemberID, '-')) AS MemberDisplayID,
                IFNULL(
                    IF(
                        a.MillID IS NOT NULL OR a.MillID <> '', 'external',
                        IF(
                            a.MillOther IS NOT NULL OR a.MillID <> '', 'external', 'other'
                        )
                    ), 'other'
                ) SellerType,
                e.ObjID,
                a.MillID,
                a.MillOther,
                IF(a.MillOther IS NULL OR a.MillOther = '', '',true) OtherMill,
                a.DOID,
                a.DOOther,
                IF(a.DOOther IS NULL OR a.DOOther = '', '',true) OtherDO,
                a.AgentID,
                a.AgentOther,
                IF(a.AgentOther IS NULL OR a.AgentOther = '', '',true) OtherAgent,
                a.AgentOtherNIK,
                a.AgentOtherSurvey,
                IF(b.isCertified != '', cp.CertProgName,'Not Certified') Certified,
                CASE
                    WHEN a.SupplyType = 'Farmer' THEN '1'
                    WHEN a.SupplyType = 'Nonfarmer' THEN '2'
                    WHEN a.SupplyType = 'Batch' THEN '3'
                    ELSE '-'
                END SalesType,
                IF( a.SupplyBatchID IS NULL, 'Open', 'Sent' ) SupplyStatus,
                CASE WHEN a.SupplyBatchID IS NULL THEN '-'
                ELSE
                IFNULL(vso2.`Name`, b.MemberName)
                END as BatchFrom
            FROM
                ktv_tc_supplychain_transaction a
            LEFT JOIN
                ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'
            LEFT JOIN
                ktv_members bb on a.SupplyID = bb.MemberID 
            LEFT JOIN
                ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
            LEFT JOIN
                ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch'
            LEFT JOIN
                view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
            LEFT JOIN
                ktv_trace_package c on a.PackageID=c.PackageID
            LEFT JOIN
                ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
            LEFT JOIN
                view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
            LEFT JOIN
                ktv_mill m2 on m2.MillID = a.MillID
            LEFT JOIN
                view_tc_supplychain_org mem on mem.SupplychainID = a.MillID
            LEFT JOIN
                ktv_members mem2 on mem2.MemberID = a.AgentID
            LEFT JOIN 
                ktv_village kv ON b.VillageID = kv.VillageID
            LEFT JOIN 
                ktv_subdistrict sd ON kv.SubDistrictID = sd.SubDistrictID
            LEFT JOIN 
                ktv_district kd ON sd.DistrictID = kd.DistrictID
            LEFT JOIN 
                ktv_province kp ON kd.ProvinceID = kp.ProvinceID
            LEFT JOIN 
                ktv_country kc ON kp.CountryCode = kc.ISO2
            LEFT JOIN
                view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
            LEFT JOIN 
                ktv_members_extension kms on kms.MemberID = vso3.ObjID
            LEFT JOIN 
                ref_tc_payment_method pm ON pm.PaymentMethodID = a.PaymentMethodID
            WHERE 1=1
            AND
                a.StatusCode = 'active'
            AND
                a.SupplychainID = '$SupplychainID'
            AND 
                a.SupplyTransID = {$SupplyTransID}
        ";

        $query = $this->db->query($sql);
        // echo '<pre>'.$this->db->last_query();die;
        
        $data = $query->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function GetWeightUnitTransaction($SupplyTransID) 
    {
        $return = array();

        $where = [
            'SupplyTransID' => $SupplyTransID
        ];

        $this->db->select("SupplyTransID
                           ,Bunches
                           ,VolumeBruto
                           ,VolumeNetto
                           ,ContractPrice
                           ,DeductionPercentage
                           ,ContractPrice
                           ,TotalPayment
                         ", FALSE);
        $this->db->where($where);
        $this->db->order_by('SupplyTransID', 'DESC');

        $query = $this->db->get('ktv_tc_supplychain_transaction')->result_array();
        $return['success'] = true;
        $return['data']    = $query;

        return $return;
    }

    public function GetWeightUnitTransactionDetail($SupplyTransID){
        $where = [
            'SupplyTransID' => $SupplyTransID
        ];

        $this->db->where($where);
        $data = $this->db->get('ktv_tc_supplychain_transaction')->row_array();

        //prep variable
        $dataRow = array();
        foreach ($data as $key => $value) {
            $keyNew = "Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-" . $key;
            $dataRow[$keyNew] = $value;
        }

        $return['success'] = true;
        $return['data'] = $dataRow;
        return $return;
    }

    public function InsertTransactionDetail($paramPost){
        $result = false;
        $insid = 0;
        $error = ''; 
        
        $data=array();
        
        try{
            $this->db->trans_begin();
            
            if($paramPost['SalesType'] == 1){
                $SupplyType = 'Farmer';
                $SupplyID = $paramPost['MemberID'];
                $PlantationNr   = $paramPost["PlantationNr"];
            } elseif ($paramPost['SalesType'] == 2){
                $SupplyType = 'NonFarmer';
                $SupplyID = $_SESSION['SupplychainID'];
            } else {
                $SupplyType = 'Batch';
                $SupplyID = $_SESSION['SupplychainID'];
                
                $PlantationNr   = 1;
                if($paramPost['SellerType'] == "external"){
                    $contentadd = array(
                        "MillID" => (isset($paramPost["Mill"])) ? $paramPost["Mill"] : null,
                        "MillOther" => (isset($paramPost["OtherMillName"])) ? $paramPost["OtherMillName"] : null
                    );
                }
                
                if($paramPost["SellerType"] == "other"){
                    $contentadd["DOID"]                 = (isset($paramPost["DO"])) ? $paramPost["DO"] : null;
                    $contentadd["DOOther"]              = (isset($paramPost["OtherDOName"])) ? $paramPost["OtherDOName"] : null;
                    $contentadd["AgentID"]              = (isset($paramPost["Agent"])) ? $paramPost["Agent"] : null;
                    $contentadd["AgentOther"]           = (isset($paramPost["OtherAgentName"])) ? $paramPost["OtherAgentName"] : null;
                    $contentadd["AgentOtherNIK"]        = (isset($paramPost["OtherAgentNin"])) ? $paramPost["OtherAgentNin"] : null;
                    $contentadd["AgentOtherSurvey"]     = (isset($paramPost["OtherAgentSurvey"])) ? $paramPost["OtherAgentSurvey"] : null;
                }
            }
            
            $content = array(
                "SupplychainID"=> $_SESSION['SupplychainID'],
                "DateTransaction"=> $paramPost["DateTransaction"],
                "SupplyType"=> $SupplyType, //Farmer, Batch
                "SupplyID"=> $SupplyID,
                "PlantationNr"=> $PlantationNr,  
                "ContractPrice"=> $this->replacestr($paramPost['ContractPrice']), 
                "VolumeBruto"=> $paramPost['VolumeBruto'],  // Ini disamakan saja isinya
                "VolumeNetto"=> $paramPost['VolumeNetto'], 
                "DeductionWeight"=> $paramPost['DeductionWeight'],
                "DeductionPercentage"=> $paramPost['DeductionPercentage'],
                "TotalPayment" => $paramPost['ContractPrice'] * $paramPost['VolumeNetto'],
                "Bunches"=> $paramPost['Bunches'], 
                "StatusCode" => 'active' 
            );
            
            $content['TransNumber'] = generateTransTraceabilityNumber($content['SupplychainID']); 
            $content['DateCreated'] = date('Y-m-d H:i:s');
            $content['DateUpdated'] = date('Y-m-d H:i:s');
            $content['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
            
            if(isset($contentadd)){
                $content = array_merge((array) $content, (array) $contentadd);
            }
            
            $this->db->insert('ktv_tc_supplychain_transaction', $content);
            
            $insid = $this->db->insert_id();

            if (($this->db->trans_status() == false)) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
                $result = false;
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();

        if($result) {
            return array('success' => $result,'message' => 'Save data success', 'SupplyTransID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }

        return $results;
    }

    public function UpdateTransactionDetail($paramPost){
        $results = array();
        $this->db->trans_begin();
       
        $SupplyTransID = $paramPost['SupplyTransID'];

        unset($paramPost['OpsiDisplay']);
        
        $data['Bunches']                = $paramPost['Bunches'];
        $data['VolumeNetto']            = $paramPost['VolumeNetto'];
        $data['VolumeBruto']            = $paramPost['VolumeBruto'];
        $data['ContractPrice']          = $paramPost['ContractPrice'];
        $data['TotalPayment']           = $paramPost['VolumeNetto'] * $paramPost['ContractPrice'];
        $data['DeductionWeight']        = $paramPost['DeductionWeight'];
        $data['DeductionPercentage']    = $paramPost['DeductionPercentage'];
        $data['LastModifiedBy']         = $_SESSION['userid'];
        $data['DateUpdated']            = date('Y-m-d H:i:s');
        
        $this->db->where('SupplyTransID', $SupplyTransID);
        $query = $this->db->update('ktv_tc_supplychain_transaction', $data);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success']       = true;
            $results['message']       = lang("Data saved");
            $results['SupplyTransID'] = $paramPost['SupplyTransID'];
        }

        return $results;
    }

    public function DeleteTransactionDetail($paramDelete) {
        $results = array();
        $this->db->trans_begin();

        $dataUpdate = array(
                    "StatusCode"     => "nullified",
                    "DateUpdated"    => date("Y-m-d H:i:s"),
                    "LastModifiedBy" => $_SESSION['userid']
                );
        
        $this->db->where('SupplyTransID', $paramDelete['SupplyTransID']);
        $query = $this->db->update('ktv_tc_supplychain_transaction', $dataUpdate);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to delete data");
        } else {
            $this->db->trans_commit();
            $results['success']       = true;
            $results['message']       = lang("Data deleted");
            $results['SupplyTransID'] = $paramDelete['SupplyTransID'];
        }

        return $results;
    }

    public function UpdateTransaction($paramPost){
        // echo "<pre>";
        // var_dump($paramPost);
        // die;

        $results = array();
        $this->db->trans_begin();

        $date_now             = date("Y-m-d H:i:s");
        $userid               = $_SESSION['userid'];
        $TransPaymentDetailID = $paramPost['TransPaymentDetailID'];
        $SupplyTransID        = $paramPost['SupplyTransID'];
        $SupplychainID        = $_SESSION['SupplychainID'];

        $dataTransaction['SupplychainID']     = $SupplychainID;
        $dataTransaction['InvoiceNumber']     = $paramPost['InvoiceNumber'];
        
        try{
            $this->db->trans_begin();
            
            //getuid
            $uid            = $this->getUID();

            //farmer
            if($paramPost["SalesType"] == 1){
                // $SupplyID       = $paramPost['MemberID'];
                $SupplyType     = "Farmer";
                $PlantationNr   = $paramPost["PlantationNr"];
                $FarmerCategory = $paramPost['FarmerCategory'];
                $Latitude       = $paramPost['Latitude'];
                $Longitude      = $paramPost['Longitude'];
               
                $cekFarmUntraceable = array(
                    'FarmerCategory' => $FarmerCategory,
                    'Latitude' => $Latitude,
                    'Longitude' => $Longitude,
                );

                if($cekFarmUntraceable['FarmerCategory'] == 'Unmapped' && $cekFarmUntraceable['Latitude'] == '' && $cekFarmUntraceable['Longitude'] == ''){
                    $isTraceable = 'NO'; 
                }elseif($cekFarmUntraceable['FarmerCategory'] == 'Mapped' && $cekFarmUntraceable['Latitude'] == '' && $cekFarmUntraceable['Longitude'] == ''){
                    $isTraceable = 'NO';
                }elseif($cekFarmUntraceable['FarmerCategory'] == 'Mapped' && $cekFarmUntraceable['Latitude'] != '' && $cekFarmUntraceable['Longitude'] != ''){
                    $isTraceable = 'YES';
                } else {
                    $isTraceable = 'YES';
                }

                if($paramPost['OpsiDisplay'] == 'insert'){
                    $contentUpdate = array(
                        "SupplychainID"=> $_SESSION['SupplychainID'],
                        "DateTransaction"=> $paramPost["DateTransaction"],
                        "SupplyType"=> $SupplyType, //Farmer, Batch
                        // "SupplyID"=> $SupplyID,
                        "PlantationNr"=> $PlantationNr,
                        "InvoiceNumber"=> $dataTransaction['InvoiceNumber'] ,
                        "TotalPayment"=> $this->replacestr($paramPost['TotalPayment']),
                        "isTraceable"=> $isTraceable,
                        //field payment start
                        "PaymentStatusID"   => $paramPost['PaymentStatusID'],
                        "PaymentMethodID"   => $paramPost['PaymentMethodID'],
                        "BankCode"          => $paramPost['BankCode'],
                        "BankName"          => $paramPost['BankName'],
                        "AccountNumber"     => $paramPost['AccountNumber'],
                        "AccountName"       => $paramPost['AccountName'],
                        "uid"               => base64_encode(date('His')).$paramPost['TransNumber'].$_SESSION['SupplychainID'].date('YmdHis'),
                        //field payment end
                        "PaymentReduction" => $this->replacestr($paramPost['PaymentReduction']),
                        "PaymentPaid"      => $this->replacestr($paramPost['PaymentPaid']),
                        "uid"              => $uid
                    );
                } else {
                    
                    @$SupplyIDTrans = $this->db->query("SELECT SupplyTransID FROM ktv_tc_supplychain_transaction WHERE SupplyTransID=?", array($paramPost['SupplyTransID']))->row()->SupplyID;
                    
                    $contentUpdate = array(
                        "SupplychainID"=> $_SESSION['SupplychainID'],
                        "DateTransaction"=> $paramPost["DateTransaction"],
                        "SupplyType"=> $SupplyType, //Farmer, Batch
                        // "SupplyID"=> $SupplyIDTrans,
                        "PlantationNr"=> $PlantationNr,
                        "InvoiceNumber"=> $dataTransaction['InvoiceNumber'] ,
                        "TotalPayment"=> $this->replacestr($paramPost['TotalPayment']),
                        "isTraceable"=> $isTraceable,
                        //field payment start
                        "PaymentStatusID"   => $paramPost['PaymentStatusID'],
                        "PaymentMethodID"   => $paramPost['PaymentMethodID'],
                        "BankCode"          => $paramPost['BankCode'],
                        "BankName"          => $paramPost['BankName'],
                        "AccountNumber"     => $paramPost['AccountNumber'],
                        "AccountName"       => $paramPost['AccountName'],
                        "uid"               => base64_encode(date('His')).$paramPost['TransNumber'].$_SESSION['SupplychainID'].date('YmdHis'),
                        //field payment end
                        "PaymentReduction" => $this->replacestr($paramPost['PaymentReduction']),
                        "PaymentPaid" => $this->replacestr($paramPost['PaymentPaid']),
                        "uid"              => $uid
                    );
                }
            }
        
            //non farmer
            if($paramPost["SalesType"] == 2){
                
                $SupplyID       = $paramPost['MemberID'];
                $SupplyType     = "NonFarmer";
                $PlantationNr   = explode('-', $paramPost["PlantationNrNonFarmer"]);
                $PlantationNr   = $PlantationNr[0];
                
                if($paramPost['OpsiDisplay'] == 'insert'){
                
                    $contentUpdate = array(
                        "SupplychainID"=> $_SESSION['SupplychainID'],
                        "DateTransaction"=> $paramPost["DateTransaction"],
                        "SupplyType"=> $SupplyType, //Farmer, Batch
                        "SupplyID"=> $SupplyID,
                        "PlantationNr"=> $PlantationNr,  
                        "InvoiceNumber"=> $paramPost['InvoiceNumberNonFarmer'] ,
                        "TotalPayment"=> $this->replacestr($paramPost['TotalPaymentNonFarmer']),
                        //field payment start
                        "PaymentStatusID"   => $paramPost['PaymentStatusIDNonFarmer'],
                        "PaymentMethodID"   => $paramPost['PaymentMethodIDNonFarmer'],
                        "BankCode"          => $paramPost['BankCodeNonFarmer'],
                        "BankName"          => $paramPost['BankNameNonFarmer'],
                        "AccountNumber"     => $paramPost['AccountNumberNonFarmer'],
                        "AccountName"       => $paramPost['AccountNameNonFarmer'],
                        "uid"               => base64_encode(date('His')).$paramPost['InvoiceNumberNonFarmer'].$_SESSION['SupplychainID'].date('YmdHis'),
                        //field payment end
                        "PaymentReduction"  => $this->replacestr($paramPost['PaymentReductionNonFarmer']),
                        "PaymentPaid"       => $this->replacestr($paramPost['PaymentPaid']),
                        "uid"               => $uid
                    );
                } else {

                    @$SupplyIDTrans = $this->db->query("SELECT SupplyID FROM ktv_tc_supplychain_transaction WHERE SupplyTransID=?", array($paramPost['SupplyTransID']))->row()->SupplyID;

                    $contentUpdate = array(
                        "SupplychainID"=> $_SESSION['SupplychainID'],
                        "DateTransaction"=> $paramPost["DateTransaction"],
                        "SupplyType"=> $SupplyType, //Farmer, Batch
                        "SupplyID"=> $SupplyIDTrans,
                        "PlantationNr"=> $PlantationNr,  
                        "InvoiceNumber"=> $paramPost['InvoiceNumber'] ,
                        "TotalPayment"=> $this->replacestr($paramPost['TotalPayment']),
                        //field payment start
                        "PaymentStatusID"   => $paramPost['PaymentStatusID'],
                        "PaymentMethodID"   => $paramPost['PaymentMethodID'],
                        "BankCode"          => $paramPost['BankCode'],
                        "BankName"          => $paramPost['BankName'],
                        "AccountNumber"     => $paramPost['AccountNumber'],
                        "AccountName"       => $paramPost['AccountName'],
                        "uid"               => base64_encode(date('His')).$paramPost['TransNumber'].$_SESSION['SupplychainID'].date('YmdHis'),
                        //field payment end
                        "PaymentReduction"  => $this->replacestr($paramPost['PaymentReduction']),
                        "PaymentPaid"       => $this->replacestr($paramPost['PaymentPaid']),
                        "uid"               => $uid
                    );
                }   
            }
            
            //direct batch
            if($paramPost["SalesType"] == 3){
                
                $SupplyID       = $paramPost['MemberID'];
                $SupplyType     = "Batch";
                $PlantationNr   = 1;
                if($paramPost['SellerType'] == "external"){
                    $contentUpdate = array(
                        "MillID" => (isset($paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-Mill"])) ? $paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-Mill"] : null,
                        "MillOther" => (isset($paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-OtherMillName"])) ? $paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-OtherMillName"] : null
                    );
                    if($paramPost["OpsiDisplay"] == "insert"){
                        
                        $contentUpdate = array(
                            "SupplychainID"=> $_SESSION['SupplychainID'],
                            "DateTransaction"=> $paramPost["DateTransaction"],
                            "SupplyType"=> $SupplyType, //Farmer, Batch
                            "SupplyID"=> $SupplyID,
                            "PlantationNr"=> $PlantationNr,  
                            "InvoiceNumber"=> $paramPost['InvoiceNumberDirectBatch'] ,
                            "TotalPayment"=> $this->replacestr($paramPost['TotalPaymentDirectBatch']),
                            //field payment start
                            "PaymentStatusID"   => $paramPost['PaymentStatusIDDirectBatch'],
                            "PaymentMethodID"   => $paramPost['PaymentMethodIDDirectBatch'],
                            "BankCode"          => $paramPost['BankCodeDirectBatch'],
                            "BankName"          => $paramPost['BankNameDirectBatch'],
                            "AccountNumber"     => $paramPost['AccountNumberDirectBatch'],
                            "AccountName"       => $paramPost['AccountNameDirectBatch'],
                            "uid"               => base64_encode(date('His')).$paramPost['InvoiceNumberDirectBatch'].$_SESSION['SupplychainID'].date('YmdHis'),
                            //field payment end
                            "PaymentReduction" => $this->replacestr($paramPost['PaymentReductionDirectBatch']),
                            "PaymentPaid"      => $this->replacestr($paramPost['PaymentPaidDirectBatch']),
                            "uid"              => $uid
                        );
                    } else {
                        
                        if($paramPost['MemberID'] == 'Unregistered Supplier'){
                            $idMillOther = $paramPost['MemberName'];
                            $idMill = "0";
                        }

                        @$SupplyIDMill = $this->db->query("SELECT MillID FROM ktv_tc_supplychain_transaction WHERE SupplyTransID=?", array($paramPost['SupplyTransID']))->row()->MillID;
                        
                        $contentUpdate = array(
                            "SupplychainID"=> $_SESSION['SupplychainID'],
                            "DateTransaction"=> $paramPost["DateTransaction"],
                            "SupplyType"=> $SupplyType, //Farmer, Batch
                            "MillID"=> $SupplyIDMill,
                            "PlantationNr"=> $PlantationNr,  
                            "InvoiceNumber"=> $paramPost['InvoiceNumber'],
                            "TotalPayment"=> $this->replacestr($paramPost['TotalPayment']),
                            //field payment start
                            "PaymentStatusID"   => $paramPost['PaymentStatusID'],
                            "PaymentMethodID"   => $paramPost['PaymentMethodID'],
                            "BankCode"          => $paramPost['BankCode'],
                            "BankName"          => $paramPost['BankName'],
                            "AccountNumber"     => $paramPost['AccountNumber'],
                            "AccountName"       => $paramPost['AccountName'],
                            "uid"               => base64_encode(date('His')).$paramPost['TransNumber'].$_SESSION['SupplychainID'].date('YmdHis'),
                            //field payment end
                            "MillOther" => $idMillOther,
                            "PaymentReduction" => $this->replacestr($paramPost['PaymentReduction']),
                            "PaymentPaid" => $this->replacestr($paramPost['PaymentPaid']),
                            "uid"              => $uid
                        );
                    }
                }

                if($paramPost["SellerType"] == "other"){
                  
                    if($paramPost["OpsiDisplay"] == "insert"){
                        
                        $contentUpdate = array(
                            "DOID" => isset($paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-DO"]) ? $paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-DO"] : null,
                            "AgentID" => isset($paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-Agent"]) ? $paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-Agent"] : null,
                            "DOOther" => isset($paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-OtherDOName"]) ? $paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-OtherDOName"] : null,
                            "AgentOther" => isset($paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-OtherAgentName"]) ? $paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-OtherAgentName"] : null,
                            "AgentOtherNIK" => isset($paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-OtherAgentNin"]) ? $paramPost["Koltiva_view_Traceability_new_Transaction_FormTransaction-OtherAgentNin"] : null,
                            "AgentOtherSurvey" => isset($paramPost["Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentSurvey"]) ? $paramPost["Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentSurvey"] : null,
                            "SupplychainID"=> $_SESSION['SupplychainID'],
                            "DateTransaction"=> $paramPost["DateTransaction"],
                            "SupplyType"=> $SupplyType, //Farmer, Batch
                            "SupplyID"=> $SupplyID,
                            "PlantationNr"=> $PlantationNr,  
                            "InvoiceNumber"=> $paramPost['InvoiceNumberDirectBatch'] ,
                            "TotalPayment"=> $this->replacestr($paramPost['TotalPaymentDirectBatch']),
                            "PaymentReduction" => $this->replacestr($paramPost['PaymentReductionDirectBatch']),
                            "PaymentPaid" => $this->replacestr($paramPost['PaymentPaidDirectBatch']),
                            "uid" => $uid
                        );
                    } else {
                        
                        if($paramPost['MemberID'] == 'Unregistered Supplier'){
                            $DOOther = $paramPost['MemberName'];
                        } else {
                            $DOID = $paramPost['MemberID'];
                        }

                        $contentUpdate = array(
                            "SupplychainID"=> $_SESSION['SupplychainID'],
                            "DateTransaction"=> $paramPost["DateTransaction"],
                            "SupplyType"=> $SupplyType, //Farmer, Batch
                            "SupplyID"=> "0",
                            "PlantationNr"=> $PlantationNr,  
                            "InvoiceNumber"=> $paramPost['InvoiceNumber'] ,
                            "TotalPayment"=> $this->replacestr($paramPost['TotalPayment']),
                            "DOID" => $DOID,
                            "DOOther" => $DOOther,
                            "PaymentReduction" => $this->replacestr($paramPost['PaymentReduction']),
                            "PaymentPaid" => $this->replacestr($paramPost['PaymentPaid']),
                            "uid" => $uid
                        );
                    }
                }
            }
            
            $isNpwp = $data['Opsinpwp'] == true ? 1 : 0;
            $IsStamp = $data['OpsiStampdeduction'] == true ? 1 : 0;

            // var_dump($contentUpdate);
            // die;
            
            if($paramPost['SupplyTransID'] !='' ){
               
                /* Update data Transaction */
                $this->db->where('SupplyTransID', @$paramPost['SupplyTransID']);
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['LastModifiedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;
                
                $this->db->update('ktv_tc_supplychain_transaction', $contentUpdate);
                $insid = @$paramPost['SupplyTransID'];

                // $phone = "+6281382509259";
                
                // $url = "http://smsapiv2.1rstwap.com:8080/smsapi/pages/sendSmsLatinConcat.do?g3p4i=Koltiva&G4PIpw=Koltiva%205M5&src=KOLTIVA&dst=+681382509259&msg=testingsms";
                
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_POST, 0);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // $response = curl_exec($ch);
                // $err = curl_error($ch);  //if you need
                // curl_close ($ch);
                
                // $request = $url;
            
                // $contentSms = array(
                //     "SupplyTransID"=> $insid,
                //     "Handphone"=> $phone,
                //     "ProviderID"=> '2',
                //     "method"=> 'GET',
                //     "url"=> $url, 
                //     "request"=> $request,
                //     "response"=> $response ,
                //     "DateCreated"=> date('Y-m-d H:i:s'),
                //     "SMSType"=> '1',
                // );
                
                // $this->db->insert('sys_log_sms_transaction', $contentSms);
                // $this->db->insert_id();

            }else{
                $content['TransNumber'] = generateTransTraceabilityNumber($content['SupplychainID']); 
                $content['DateCreated'] = date('Y-m-d H:i:s');
                $content['DateUpdated'] = date('Y-m-d H:i:s');
                $content['CreatedBy'] = array_key_exists('userid',$_SESSION)?$_SESSION['userid']:1;

                if(isset($contentadd)){
                    $content = (object) array_merge((array) $content, (array) $contentadd);
                }
               
                $this->db->insert('ktv_tc_supplychain_transaction', $content);
                $insid = $this->db->insert_id();
            }

            if (($this->db->trans_status() == false)) {
                $this->db->trans_rollback();
                $error = $this->db->_error_messages();
                $result = false;
            } else {
                $this->db->trans_commit();
                $result = true;
            }
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $this->db->trans_complete();
        // echo '<pre>'.$this->db->last_query();die;

        if($result) {
            return array('success' => $result,'message' => 'Save data success', 'SupplyTransID' => $insid);
        }else{
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }

        return $results;
    }

    public function get_data_purchase_report($SID,$type=""){ 
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        //sort
        $sorting = json_decode($this->input->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        /* pencarian */
        $SupplyType = $this->input->get('SupplyType');
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');

        $where = "";
        if($SupplyType){
            $where .= "AND a.`SupplyType` = '$SupplyType' ";
        }

        $order = "ORDER BY a.SupplyTransID DESC";
        if ($sorting){
            if ($sortingField = 'SupplierName')
                $sortingField = 'MemberName';
            $order = "ORDER BY $sortingField $sortingDir";    
        }

        if($SupplyStatus){
            //$this->db->where('a.StatusCode', 'active');
        }

        if($SupplyKey){
            
            $where .= " AND (IF(
                b.MemberName IS NULL OR b.MemberName = '',
                IF(
                    m2.MillName IS NULL OR m2.MillName = '',
                    IF(
                        a.MillOther IS NULL OR a.MillOther = '',
                        IF(
                            mem2.MemberName IS NULL OR mem2.MemberName = '',
                            IF(
                                a.DOOther IS NULL OR a.DOOther = '',
                                IF(
                                    a.AgentOther IS NULL OR a.AgentOther = '',
                                    'Nonfarmer',
                                    a.AgentOther
                                ),
                                a.DOOther
                            ),
                            mem2.MemberName
                        ),
                        a.MillOther
                    ),
                    m2.MillName
                ),
                b.MemberName
            ) LIKE '%$SupplyKey%') ";            
        }

        /* Filter core */
        $PID = $this->input->get('PID');
        $STID = $this->input->get('STID');
        $SBID = $this->input->get('SBID');

        if($STID != ""){
            $where .= "AND a.`SupplyTransID` = '$STID' ";
        }        
        
        if($type!="export_excel"){
            $limit = "LIMIT $start, $limit";
        }

        $data = array('data' => array(), 'total' => 0);

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`DateTransaction` AS Tanggal_transaksi,
                    IFNULL(
                        IF(
                            b.MemberDisplayID <> 0, b.MemberDisplayID, 
                            IF(
                                bb.MemberID <> 0, bb.MemberID,
                                IF(
                                    a.MillID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.MillID,
                                    IF(
                                        a.DOID <> 0 AND (a.DOOther IS NULL OR a.DOOther = ''), a.DOID,
                                        IF(
                                            a.AgentID <> 0 AND (a.AgentOther IS NULL OR a.AgentOther = ''), a.AgentID,
                                            IF(
                                                a.SupplyID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.SupplyID,
                                                'Unregistered Supplier'
                                            )
                                        )
                                    )
                                )
                            )
                        ), 'Unregistered Supplier'
                    ) AS ID_pemasok,
                    IFNULL(b.MemberDisplayID, '-') AS FarmerID,
                    IF(
                        b.MemberName IS NULL OR b.MemberName = '',
                        IF(
                            m2.MillName IS NULL OR m2.MillName = '',
                            IF(
                                a.MillOther IS NULL OR a.MillOther = '',
                                IF(
                                    mem.Name IS NULL OR mem.Name = '',
                                    IF(
                                       kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                        IF(
                                            a.DOOther IS NULL OR a.DOOther = '',
                                            IF(
                                                a.AgentOther IS NULL OR a.AgentOther = '',
                                                'Nonfarmer',
                                                a.AgentOther
                                            ),
                                            a.DOOther
                                        ),
                                        kms.agCompanyName
                                    ),
                                    mem.Name
                                ),
                                a.MillOther
                            ),
                            m2.MillName
                        ),
                        b.MemberName
                    ) Nama_Pemasok,
                    IFNULL(a.Bunches,'0') AS Janjang,
                    a.`VolumeBruto` AS Berat_Kotor,
                    IFNULL(a.DeductionPercentage,'0') AS Presentase_pemotongan,
                    a.`VolumeNetto` AS Berat_bersih,
                    a.`ContractPrice` AS Harga_per_kilo,
                    a.`TotalPayment` AS Total,
                    CASE
                    WHEN a.PaymentReduction iS NULL THEN '0'
                    ELSE a.PaymentReduction
                    END AS Pengurangan_pembayaran,
                    CASE
                    WHEN a.`PaymentPaid` iS NULL THEN a.`TotalPayment`
                    ELSE a.`PaymentPaid`
                    END AS Jumlah_pembayaran,
                    IFNULL(a.`isTraceable` ,'-') AS Ketelusuran
                FROM
                    ktv_tc_supplychain_transaction a
                LEFT JOIN
                    ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' 
                LEFT JOIN 
                    ktv_members bb on a.supplyID = bb.MemberID 
                LEFT JOIN
                    ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
                LEFT JOIN
                    ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch'
                LEFT JOIN
                    view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
                LEFT JOIN
                    ktv_trace_package c on a.PackageID=c.PackageID
                LEFT JOIN
                    ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
                LEFT JOIN
                    ktv_mill m2 on m2.MillID = a.MillID
                LEFT JOIN
                    view_tc_supplychain_org mem on mem.SupplychainID = a.MillID
                LEFT JOIN
                    ktv_members mem2 on mem2.MemberID = a.AgentID
                LEFT JOIN
                    view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
                LEFT JOIN 
                    ktv_members_extension kms on kms.MemberID = vso3.ObjID
                WHERE
                    a.StatusCode = 'active'
                AND
                    a.SupplychainID = ?
                AND 
                    d.SupplyBatchID IS NULL
                $where
                ORDER BY a.SupplyTransID DESC
                $limit
        ";

        $Q = $this->db->query($sql,array($SID));

        if($Q->num_rows()){
            $result = $Q->result();

            if ($_SESSION['PartnerID'] != '14' || $_SESSION['PartnerID'] != '194'){ // bukan WAGS dan mill perak
                $query = $this->db->query('SELECT FOUND_ROWS() AS total');
                $data['data'] = $result;
                $data['total'] = $query->row()->total;
            } else {
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
            }
            
            return $data;
        }

        return $data;
    }

    public function get_data_api_purchase_report($SID){ 

        $DateStart = $this->input->get('DateStart');
        $DateEnd = $this->input->get('DateEnd');
        
        $data = array('data' => array(), 'total' => 0);

        $whereFilter = "AND DATE_FORMAT(a.DateTransaction, '%Y-%m-%d') >= '$DateStart' AND DATE_FORMAT(a.DateTransaction, '%Y-%m-%d') <= '$DateEnd'";

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    a.`DateTransaction` AS Tanggal_transaksi,
                    IFNULL(b.`MemberDisplayID`,'-') AS FarmerID,
                    IFNULL(
                        IF(
                            b.MemberDisplayID <> 0, b.MemberDisplayID, 
                            IF(
                                bb.MemberID <> 0, bb.MemberID,
                                IF(
                                    a.MillID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.MillID,
                                    IF(
                                        a.DOID <> 0 AND (a.DOOther IS NULL OR a.DOOther = ''), a.DOID,
                                        IF(
                                            a.AgentID <> 0 AND (a.AgentOther IS NULL OR a.AgentOther = ''), a.AgentID,
                                            IF(
                                                a.SupplyID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.SupplyID,
                                                'Unregistered Supplier'
                                            )
                                        )
                                    )
                                )
                            )
                        ), 'Unregistered Supplier'
                    ) AS ID_pemasok,
                    IF(
                        b.MemberName IS NULL OR b.MemberName = '',
                        IF(
                            m2.MillName IS NULL OR m2.MillName = '',
                            IF(
                                a.MillOther IS NULL OR a.MillOther = '',
                                IF(
                                    mem.Name IS NULL OR mem.Name = '',
                                    IF(
                                        kms.agCompanyName IS NULL OR kms.agCompanyName = '',
                                        IF(
                                            a.DOOther IS NULL OR a.DOOther = '',
                                            IF(
                                                a.AgentOther IS NULL OR a.AgentOther = '',
                                                'Nonfarmer',
                                                a.AgentOther
                                            ),
                                            a.DOOther
                                        ),
                                        kms.agCompanyName
                                    ),
                                    mem.Name
                                ),
                                a.MillOther
                            ),
                            m2.MillName
                        ),
                        b.MemberName
                    ) Nama_Pemasok,
                    IFNULL(a.Bunches,'0') AS Janjang,
                    a.`VolumeBruto` AS Berat_Kotor,
                    IFNULL(a.DeductionPercentage,'0') AS Presentase_pemotongan,
                    a.`VolumeNetto` AS Berat_bersih,
                    a.`ContractPrice` AS Harga_per_kilo,
                    a.`TotalPayment` AS Total,
                    CASE
                    WHEN a.PaymentReduction iS NULL THEN '0'
                    ELSE a.PaymentReduction
                    END AS Pengurangan_pembayaran,
                    CASE
                    WHEN a.`PaymentPaid` iS NULL THEN a.`TotalPayment`
                    ELSE a.`PaymentPaid`
                    END AS Jumlah_pembayaran,
                    IFNULL(a.`isTraceable` ,'-') AS Ketelusuran
                FROM
                    ktv_tc_supplychain_transaction a
                LEFT JOIN
                    ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'
                LEFT JOIN 
                    ktv_members bb on a.supplyID = bb.MemberID 
                LEFT JOIN
                    ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
                LEFT JOIN
                    ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch'
                LEFT JOIN
                    view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
                LEFT JOIN
                    ktv_trace_package c on a.PackageID=c.PackageID
                LEFT JOIN
                    ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
                LEFT JOIN
                    view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
                LEFT JOIN
                    ktv_mill m2 on m2.MillID = a.MillID
                LEFT JOIN
                    view_tc_supplychain_org mem on mem.SupplychainID = a.MillID
                LEFT JOIN
                    ktv_members mem2 on mem2.MemberID = a.AgentID
                LEFT JOIN
                    view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplyID AND a.SupplyType = 'NonFarmer'
                LEFT JOIN 
                    ktv_members_extension kms on kms.MemberID = vso3.ObjID
                WHERE
                    a.SupplychainID = ?
                AND 
                    d.SupplyBatchID IS NULL
                $whereFilter
                AND
                    a.StatusCode = 'active'
                ORDER BY a.SupplyTransID DESC
        ";

        $Q = $this->db->query($sql,array($SID));
        
        if($Q->num_rows()){
            $result = $Q->result();

            if ($_SESSION['PartnerID'] != '14' || $_SESSION['PartnerID'] != '194'){ // bukan WAGS dan mill perak
                $query = $this->db->query('SELECT FOUND_ROWS() AS total');
                $data['data'] = $result;
                $data['total'] = $query->row()->total;
            } else {
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
            }
            
            return $data;
        }

        return $data;
    }


    public function get_data_sales_report($SID,$type=""){ 
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        //sort
        $sorting = json_decode($this->input->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        /* pencarian */
        $SupplyType = $this->input->get('SupplyType');
        $SupplyStatus = $this->input->get('SupplyStatus');
        $SupplyKey = $this->input->get('SupplyKey');

        $where = "";

        $order = "ORDER BY e.SupplyTransID DESC";
        if ($sorting){
            if ($sortingField = 'a.Name')
                $sortingField = 'a.Name';
            $order = "ORDER BY $sortingField $sortingDir";    
        }

        if($SupplyKey){
            
            $where .= " AND a.Name
            ) LIKE '%$SupplyKey%') ";            
        }

        /* Filter core */
        $PID = $this->input->get('PID');
        $STID = $this->input->get('STID');
        $SBID = $this->input->get('SBID');

        if($type!="export_excel"){
            $limit = "LIMIT $start, $limit";
        }

        $DateStart = $this->input->get('DateStart');
        $DateEnd = $this->input->get('DateEnd');

        $data = array('data' => array(), 'total' => 0);

        $sql = "SELECT 
                    SQL_CALC_FOUND_ROWS
                    c.DeliveryDate AS Tanggal_pengiriman,
                    a.Name AS Nama_agen,
                    IFNULL(vo.Name,c.SupplyDestMillOtherName) AS Tujuan_Mill,
                    IFNULL(c.TotalWeight,'0') AS Berat_kotor_pengiriman,
                    IFNULL(c.FinalCapacity,'0') AS Berat_bersih_dijual,
                    CASE
                        WHEN c.PaymentDelivery IS NULL THEN '0'
                        ELSE SUM(c.PaymentDelivery)
                    END AS Total_harga
                FROM 
                    view_tc_supplychain_org a
                LEFT JOIN 
                    ktv_tc_supplychain_delivery c ON c.SupplychainID = a.SupplychainID
                LEFT JOIN 
                    view_tc_supplychain_org vo ON vo.SupplychainID = c.SupplyDestMillOrgID
                LEFT JOIN 
                    ktv_tc_supplychain_delivery_detail d ON d.DeliveryID = c.DeliveryID
                LEFT JOIN 
                    ktv_tc_supplychain_transaction_detail e ON e.DeliveryDetailID = d.DeliveryID
                LEFT JOIN 
                    ktv_tc_supplychain_transaction f ON f.SupplyTransID = e.SupplyTransID
                WHERE c.SupplychainID = ?
                AND c.StatusCode = 'active'
                $where
                GROUP BY c.DeliveryID
                ORDER BY c.DeliveryID DESC
                $limit
        ";

        $Q = $this->db->query($sql,array($SID));
        
        if($Q->num_rows()){
            $result = $Q->result();

            if ($_SESSION['PartnerID'] != '14' || $_SESSION['PartnerID'] != '194'){ // bukan WAGS dan mill perak
                $query = $this->db->query('SELECT FOUND_ROWS() AS total');
                $data['data'] = $result;
                $data['total'] = $query->row()->total;
            } else {
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
            }
            
            return $data;
        }

        return $data;
    }

    public function get_data_api_sales_report($SID){ 

        $DateStart = $this->input->get('DateStart');
        $DateEnd = $this->input->get('DateEnd');

        $data = array('data' => array(), 'total' => 0);

        $whereFilter = "AND DATE_FORMAT(c.DeliveryDate, '%Y-%m-%d') >= '$DateStart' AND DATE_FORMAT(c.DeliveryDate, '%Y-%m-%d') <= '$DateEnd'";

        $sql = "SELECT 
                c.DeliveryDate AS Tanggal_pengiriman,
                a.Name AS Nama_agen,
                IFNULL(vo.Name,c.SupplyDestMillOtherName) AS Tujuan_Mill,
                IFNULL(c.TotalWeight,'0') AS Berat_kotor_pengiriman,
                IFNULL(c.FinalCapacity,'0') AS Berat_bersih_dijual,
                CASE
                    WHEN c.PaymentDelivery IS NULL THEN '0'
                    ELSE FORMAT(SUM(c.PaymentDelivery),0)
                END AS Total_harga
            FROM 
                view_tc_supplychain_org a  
            LEFT JOIN 
                ktv_tc_supplychain_delivery c ON c.SupplychainID = a.SupplychainID
            LEFT JOIN 
                view_tc_supplychain_org vo ON vo.SupplychainID = c.SupplyDestMillOrgID
            LEFT JOIN 
                ktv_tc_supplychain_delivery_detail d ON d.DeliveryID = c.DeliveryID
            LEFT JOIN 
                ktv_tc_supplychain_transaction_detail e ON e.DeliveryDetailID = d.DeliveryID
            LEFT JOIN 
                ktv_tc_supplychain_transaction f ON f.SupplyTransID = e.SupplyTransID
            WHERE  
                c.SupplychainID = ?  
                $whereFilter
            GROUP BY c.DeliveryID
            ORDER BY c.DeliveryID DESC
            " ; 

        $Q = $this->db->query($sql,array($SID));
        // echo $this->db->last_query();die;
        
        if($Q->num_rows()){
            $result = $Q->result();

            $query = $this->db->query('SELECT FOUND_ROWS() AS total');
            $data['data'] = $result;
            $data['total'] = $query->row()->total;   
        }
        
        return $data;
    }

    public function getPaymentInstruction($get){
        $language = $_SESSION['language'];

        $pay = $this->readDetailPayment($get);
        // var_dump($pay);die;
        
        if(intval($pay->PaymentMethodID)==1){
            $instruction = $this->readDetailPayment($pay->PaymentMethodID,$language);
            $PaymentIntrunction = $instruction->data;
        }else{
            $param = array(
                "PaymentMethodID"   => $pay->PaymentMethodID,
                "uid"               => $pay->uid,
                "LanguageID"        => $_SESSION['language']=='Indonesia'?2:1
            );

            $instruction = $this->APIChekPaymentStatus($param);

            $PaymentIntrunction = $instruction->data->PaymentDetail[0]->PaymentInstruction;
            if(empty($PaymentIntrunction)){
                $instruction = $this->ApiPaymentInstruction(1,$language);
                $PaymentIntrunction = $instruction->data;
            }
        }
        
        //return $instruction->data[0]->Content;
        //die;
        $data=array();
        if(!empty($PaymentIntrunction)){

            foreach ($PaymentIntrunction as $key) {
                $data[] = $key;
                if(strtolower($language)=='indonesia'){
                    $data[0]->Content = str_replace('Petani','Pedagang',$key->Content);
                    $data[0]->Content = str_replace('petani','pedagang',$key->Content);
                }else{
                    $data[0]->Content = str_replace('farmer','trader',$key->Content);
                    $data[0]->Content = str_replace('Farmer','Trader',$key->Content);
                }
                
                
            }
        }

        if ((int) $pay->PaymentMethodID == 2) {
            $paymentLogo                = base_url('images/Logo_BRI.png');
            $virtualAccountNameIdentity = "BRIVA";
        } elseif ((int) $pay->PaymentMethodID == 3) {
            $paymentLogo                = base_url('images/Logo_ATM_BERSAMA.png');
            $virtualAccountNameIdentity = "ATM BERSAMA";
        } else {
            $paymentLogo                = "";
            $virtualAccountNameIdentity = "";
        }

        // var_dump($pay);die;

        $return = array(
            'PaymentVia'           => lang("Payment via")." ".$pay->PaymentMethod,
            'PaymentViaLogo'       => $paymentLogo,
            'TransactionNumber'    => $pay->TransNumber,
            'PaymentMethodID'      => $pay->PaymentMethodID,
            'CompanyCode'          => $pay->CompanyCode,
            'TransactionCode'      => $pay->VirtualAccount,
            'VirtualAccount'       => $pay->CompanyCode?$pay->CompanyCode.''.$pay->VirtualAccount:$pay->VirtualAccount,
            'VirtualAccountName'   => $virtualAccountNameIdentity.' '.$pay->BuyingUnit,
            'TotalPayment'         => "IDR. ".number_format($pay->TotalPaymentWithServiceCharge,2),
            'TotalPaymentOri'      => $pay->TotalPaymentWithServiceCharge,
            'data'                 => $data
        );

        return $return;
    }

    public function ComboPaymentMethod(){
        $sql="SELECT
                PaymentMethodID AS id,
                IF(PaymentMethod='BRIVA','Bank Account - BRIVA',IF(PaymentMethod='ATM Bersama', 'Bank Account - ATM BERSAMA', PaymentMethod)) AS label 
            FROM
                ref_tc_payment_method 
            WHERE
                PaymentMethodID IN ( 2, 3 )
        ";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function readDetailPayment($get){
        $sql = "SELECT
                SQL_CALC_FOUND_ROWS
                a.`SupplyTransID`,
                a.`SupplychainID`,
                a.`SupplyBatchID`,
                b.`SupplybaseType`,
                a.`TransNumber`,
                a.`InvoiceNumber`,
                a.`DateTransaction`,
                CASE
                        WHEN a.SupplybaseCategoryID = 1 THEN 'Farmer Plasma'
                        WHEN a.SupplybaseCategoryID = 2 THEN 'Direct Smallholder'
                        WHEN a.SupplybaseCategoryID = 3 THEN 'Agent / Dealer / Vendor'
                        WHEN a.SupplybaseCategoryID = 4 THEN 'Owner Estate'
                        WHEN a.SupplybaseCategoryID = 5 THEN 'External Estate'
                        ELSE '-'
                END AS SupplyType,
                a.`PlantationNr`,
                a.`PlantationNr` FarmNumber,
                a.`VolumeBruto`,
                a.`VolumeNetto`,
                a.`VolumeCutting`,
                a.`PackageID`,
                a.`Bunches`,
                a.`FFBCount` PackageNumber,
                a.`PackageWeight`,
                a.`DetailTypeID`,
                a.`TransStatusID`,
                a.`FarmingTypeID`,
                a.ContractPrice,
                a.NetPrice,
                a.DiscountPrice,
                a.TotalPayment,
                a.PaymentReduction,
                a.PaymentPaid,
                a.`Notes`,
                a.`ChangeLog`,
                a.`ChangeBy`,
                a.`DateCreated`,
                a.`CreatedBy`,
                a.`DateUpdated`,
                a.`LastModifiedBy`, 
                a.`PaymentStatusID`,
                pm.`PaymentMethodID`,
                pm.`PaymentMethod`,
                a.`BankCode`,
                a.`BankName`,
                a.`AccountNumber`,
                a.`AccountName`,
                a.`uid`,
                a.`CompanyCode`,
                a.`VirtualAccount`,
                a.`ServiceCharge`,
                a.`TotalPaymentWithServiceCharge`,
                vso3.`Name` as BuyingUnitName,
                kpp.`PartnerName` as PartnerName,	
                b.FarmerCategory,    
                b.Latitude,
                b.Longitude,
                CASE
                    WHEN b.Gender = 'm' THEN 'Male'
                    WHEN b.Gender = 'f' THEN 'Female'
                    ELSE '-'
                END Gender,
                b.`Address`,  
                b.BankBranchName,
                b.BankAccNumber,
                b.BankClientID,   
                kv.Village,          
                CONCAT('Country: ', kc.`CountryName`,', Province: ',kp.Province,', District: ',kd.District,', Sub District: ',IFNULL(sd.SubDistrict,'-'),', Village: ',IFNULL(kv.Village,'-')) AS Region,
                FLOOR(DATEDIFF(CURDATE(), b.DateOfBirth) / 365.25) AS Age,
                c.PackageType,
                IF(
                    b.MemberName IS NULL OR b.MemberName = '',
                    IF(
                        m2.MillName IS NULL OR m2.MillName = '',
                        IF(
                            a.MillOther IS NULL OR a.MillOther = '',
                            IF(
                                mem.Name IS NULL OR mem.Name = '',
                                IF(
                                    a.DOOther IS NULL OR a.DOOther = '',
                                    IF(
                                        a.AgentOther IS NULL OR a.AgentOther = '',
                                        'Nonfarmer',
                                        a.AgentOther
                                    ),
                                    a.DOOther
                                ),
                                mem.Name
                            ),
                            a.MillOther
                        ),
                        m2.MillName
                    ),
                    b.MemberName
                ) MemberName,
                e.Name,
                b.`MemberDisplayID`,
                IFNULL(
                        IF(
                            b.MemberID <> 0, b.MemberID, 
                            IF(
                                a.MillID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.MillID,
                                IF(
                                    a.DOID <> 0 AND (a.DOOther IS NULL OR a.DOOther = ''), a.DOID,
                                    IF(
                                        a.AgentID <> 0 AND (a.AgentOther IS NULL OR a.AgentOther = ''), a.AgentID,
                                        IF(
                                            a.SupplyID <> 0 AND (a.MillOther IS NULL OR a.MillOther = ''), a.SupplyID,
                                            'Unregistered Supplier'
                                        )
                                    )
                                )
                            )
                        ), 'Unregistered Supplier'
                ) MemberID,
                IFNULL(
                    IF(
                        a.MillID IS NOT NULL OR a.MillID <> '', 'external',
                        IF(
                            a.MillOther IS NOT NULL OR a.MillID <> '', 'external', 'other'
                        )
                    ), 'other'
                ) SellerType,
                e.ObjID,
                a.MillID,
                a.MillOther,
                IF(a.MillOther IS NULL OR a.MillOther = '', '',true) OtherMill,
                a.DOID,
                a.DOOther,
                IF(a.DOOther IS NULL OR a.DOOther = '', '',true) OtherDO,
                a.AgentID,
                a.AgentOther,
                IF(a.AgentOther IS NULL OR a.AgentOther = '', '',true) OtherAgent,
                a.AgentOtherNIK,
                a.AgentOtherSurvey,
                IF(b.isCertified != '', cp.CertProgName,'Not Certified') Certified,
                CASE
                    WHEN a.SupplyType = 'Farmer' THEN '1'
                    WHEN a.SupplyType = 'Nonfarmer' THEN '2'
                    WHEN a.SupplyType = 'Batch' THEN '3'
                    ELSE '-'
                END SalesType,
                IF( a.SupplyBatchID IS NULL, 'Open', 'Sent' ) SupplyStatus,
                CASE WHEN a.SupplyBatchID IS NULL THEN '-'
                ELSE
                IFNULL(vso2.`Name`, b.MemberName)
                END as BatchFrom
            FROM
                ktv_tc_supplychain_transaction a
            LEFT JOIN
                ktv_members b on a.SupplyID = b.MemberID AND a.SupplyType != 'Batch' AND a.SupplyType != 'Nonfarmer'
            LEFT JOIN
                ktv_ref_certification_program cp on cp.CertProgID = b.isCertified
            LEFT JOIN
                ktv_tc_supplychain_batch d on a.SupplyID=d.SupplyBatchID AND a.SupplyType = 'Batch'
            LEFT JOIN
                view_tc_supplychain_org e on d.SupplyOrgID=e.SupplychainID
            LEFT JOIN
                ktv_trace_package c on a.PackageID=c.PackageID
            LEFT JOIN
                ktv_tc_supplychain_batch sb2 on sb2.SupplyBatchID=a.SupplyID AND a.SupplyType='Batch'
            LEFT JOIN
                view_tc_supplychain_org vso2 on vso2.SupplychainID=sb2.SupplyOrgID
            LEFT JOIN
                ktv_mill m2 on m2.MillID = a.MillID
            LEFT JOIN
                view_tc_supplychain_org mem on mem.SupplychainID = a.DOID
            LEFT JOIN
                ktv_members mem2 on mem2.MemberID = a.AgentID
            LEFT JOIN 
                ktv_village kv ON b.VillageID = kv.VillageID
            LEFT JOIN 
                ktv_subdistrict sd ON kv.SubDistrictID = sd.SubDistrictID
            LEFT JOIN 
                ktv_district kd ON sd.DistrictID = kd.DistrictID
            LEFT JOIN 
                ktv_province kp ON kd.ProvinceID = kp.ProvinceID
            LEFT JOIN 
                ktv_country kc ON kp.CountryCode = kc.ISO2
            LEFT JOIN 
                ref_tc_payment_method pm ON pm.PaymentMethodID = a.PaymentMethodID
            LEFT JOIN
                view_tc_supplychain_org vso3 on vso3.SupplychainID = a.SupplychainID
            LEFT JOIN
                ktv_program_partner kpp on kpp.PartnerID = vso3.PartnerID
            WHERE 1=1
            AND
                a.StatusCode = 'active'
            AND 
                a.SupplyTransID = ?
        ";

        $query = $this->db->query($sql, array($get['SupplyTransID']));

        if ($query->num_rows()>0) {
            return $query->row();
        }
        return false;
    }

    private function apiSubmitPayment($data){

        $getAPISettings = dynamicSettingAPIPayment(base_url());
        $AppID = "WeTcCoF0e22FvGtmEe";
        $token = getTokenCognito($_SESSION['userid']);

        $getHeader = array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        );

        $insertToLog = array(
            'url'         => $getAPISettings['url'].'/api/payment/submit-payment',
            'method'      => 'POST',
            'header'      => json_encode($getHeader),
            'payload'     => json_encode(["data" => json_encode($data)]),
            'TimeStart'   => date('Y-m-d H:i:s')
        );

        $queryToLog  = $this->db->insert('sys_log_access_payment_general', $insertToLog);
        $AutoIDToLog = $this->db->insert_id();
      
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $getAPISettings['url'].'/api/payment/submit-payment',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "data": '.json_encode($data).'
        }
        ',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        ),
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        if ($checkError > 0) {
            $response = $checkErrorMsg;
        }

        $sqlUpdateToLog   = "UPDATE sys_log_access_payment_general SET TimeFinish=?, response=? WHERE AutoID=?";
        $queryUpdateToLog = $this->db->query($sqlUpdateToLog, array(date('Y-m-d H:i:s'), $response, $AutoIDToLog));

        return json_decode($response);
    }

    private function ApiPaymentInstruction($PaymentMethodID,$Language){
        $getAPISettings = dynamicSettingAPIPayment(base_url());
        $AppID = "WeTcCoF0e22FvGtmEe";
        $token = getTokenCognito($_SESSION['userid']);

        $getHeader = array(
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'token: '.$token
        );

        $insertToLog = array(
            'url'         => $getAPISettings['url'].'/api/payment/payment-intruction?PaymentMethodID='.$PaymentMethodID.'&Language='.$Language,
            'method'      => 'GET',
            'header'      => json_encode($getHeader),
            'payload'     => json_encode(['PaymentMethodID' => $PaymentMethodID, 'Language' => $Language]),
            'TimeStart'   => date('Y-m-d H:i:s')
        );

        $queryToLog  = $this->db->insert('sys_log_access_payment_general', $insertToLog);
        $AutoIDToLog = $this->db->insert_id();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $getAPISettings['url'].'/api/payment/payment-intruction?PaymentMethodID='.$PaymentMethodID.'&Language='.$Language,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => array('PaymentMethodID' => $PaymentMethodID,'Languange' => $Language),
        CURLOPT_HTTPHEADER => array(
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'token: '.$token
        ),
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        if ($checkError > 0) {
            $response = $checkErrorMsg;
        }

        $sqlUpdateToLog   = "UPDATE sys_log_access_payment_general SET TimeFinish=?, response=? WHERE AutoID=?";
        $queryUpdateToLog = $this->db->query($sqlUpdateToLog, array(date('Y-m-d H:i:s'), $response, $AutoIDToLog));

        return json_decode($response);
    }

    private function APIChekPaymentStatus($param){
        $getAPISettings = dynamicSettingAPIPayment(base_url());
        $AppID = "WeTcCoF0e22FvGtmEe";
        $token = getTokenCognito($_SESSION['userid']);

        $getHeader = array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        );

        $insertToLog = array(
            'url'         => $getAPISettings['url'].'/api/payment/check-payment-status',
            'method'      => 'POST',
            'header'      => json_encode($getHeader),
            'payload'     => json_encode(["data" => json_encode($param)]),
            'TimeStart'   => date('Y-m-d H:i:s')
        );

        $queryToLog  = $this->db->insert('sys_log_access_payment_general', $insertToLog);
        $AutoIDToLog = $this->db->insert_id();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $getAPISettings['url'].'/api/payment/check-payment-status',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "data":'.json_encode($param).'
        }',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        ),
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        if ($checkError > 0) {
            $response = $checkErrorMsg;
        }

        $sqlUpdateToLog   = "UPDATE sys_log_access_payment_general SET TimeFinish=?, response=? WHERE AutoID=?";
        $queryUpdateToLog = $this->db->query($sqlUpdateToLog, array(date('Y-m-d H:i:s'), $response, $AutoIDToLog));

        return json_decode($response);
    }

    private function _postServiceCharge($PaymentMethodID,$totalPaid){
        
        $getAPISettings = dynamicSettingAPIPayment(base_url());

        $AppID = "WeTcCoF0e22FvGtmEe";
        $token = getTokenCognito($_SESSION['userid']);

        $getHeader = array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        );

        $insertToLog = array(
            'url'         => $getAPISettings['url'].'/api/payment/check-service-charge',
            'method'      => 'POST',
            'header'      => json_encode($getHeader),
            'payload'     => json_encode(["data" => json_encode(['PaymentMethodID' => $PaymentMethodID, 'TotalPaid' => $totalPaid])]),
            'TimeStart'   => date('Y-m-d H:i:s')
        );

        $queryToLog  = $this->db->insert('sys_log_access_payment_general', $insertToLog);
        $AutoIDToLog = $this->db->insert_id();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $getAPISettings['url'].'/api/payment/check-service-charge',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{   
            "data": {   
                "PaymentMethodID":'.$PaymentMethodID.',
                "TotalPaid": '.$totalPaid.'    
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'token: '.$token,
            'application-id: '.$AppID,
            'trace-id: '.$getAPISettings['traceKey'],
            'Content-Type: application/json'
        ),
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response      = curl_exec($curl);
        $checkError    = curl_errno($curl);
        $checkErrorMsg = curl_error($curl);

        curl_close($curl);

        $sqlUpdateToLog   = "UPDATE sys_log_access_payment_general SET TimeFinish=?, response=? WHERE AutoID=?";
        $queryUpdateToLog = $this->db->query($sqlUpdateToLog, array(date('Y-m-d H:i:s'), $response, $AutoIDToLog));

        return json_decode($response);
    }

    public function SubmitPayment($paramPost){ 
        // var_dump($paramPost);
        // die;
        $update = $this->UpdateTransaction($paramPost);

        $PaymentPaid = filter_var($paramPost['PaymentPaid'], FILTER_SANITIZE_NUMBER_INT);
        $recpay  = $this->readDetailPayment($paramPost);
        
        $service = $this->_postServiceCharge($paramPost['PaymentMethodID'],$PaymentPaid)->data;
        
        $data = array(
                "uid"               => $recpay->uid,
                "SupplyTransID"     => $recpay->SupplyTransID,
                "PartnerName"       => $recpay->PartnerName,
                "BuyingUnitName"    => $recpay->BuyingUnitName,
                "TransactionDate"   => $recpay->DateTransaction, 
                "TransactionNumber" => $recpay->TransNumber,
                
                "SupplierID"        => $recpay->MemberDisplayID,
                "SupplierName"      => $recpay->MemberName,
                "SupplierType"      => $recpay->SupplybaseType,
                "BankCode"          => $recpay->BankCode,
                "BankName"          => $recpay->BankName,
                "AccountNumber"     => $recpay->AccountNumber,
                "AccountName"       => $recpay->AccountName,
                "AccountEmoney"     => "",
                "AccountUsername"   => "",

                "PaymentMethodID"   => $recpay->PaymentMethodID?$recpay->PaymentMethodID:2,
                "TotalPaid"         => $PaymentPaid,
                "Notes"             => "",
                "FarmerSignature"   => "",
                "FCMToken"          => "0",
                "LanguageID"        => 2,
                "PaymentDetail"     => [
                    array(
                        "uid"                         => $recpay->SupplierID.$recpay->uid,
                        "PaymentMethodID"             => $recpay->PaymentMethodID?$recpay->PaymentMethodID:2, 
                        "ServiceChargeType"           => $service->ServiceChargeType,
                        "ServiceCharge"               => $service->ServiceCharge,
                        "TotalServiceCharge"          => $service->TotalServiceCharge,
                        "TotalPaid"                   => $service->TotalPaid,
                        "TotalPaidWithServiceCharge"  => $service->TotalPaidWithServiceCharge,
                        "EmoneyToken"                 => "",
                        "PIN"                         => "",
                        "DetailNotes"                 => ""
                    )
                ]
        );
    
        $payment = $this->apiSubmitPayment($data);

        $results = array();
        if($payment->success==true){
            $update = array(
                'Status'                        => 'Submitted',
                'PaymentStatusID'               => $payment->data->PaymentStatusID,
                'CompanyCode'                   => $payment->data->PaymentDetail[0]->CompanyCode,
                'VirtualAccount'                => $payment->data->PaymentDetail[0]->VirtualAccount,
                'ServiceCharge'                 => $payment->data->PaymentDetail[0]->TotalServiceCharge,
                'TotalPaymentWithServiceCharge' => $payment->data->PaymentDetail[0]->TotalPaidWithServiceCharge
            );
           
            $this->db->where('SupplyTransID',$recpay->SupplyTransID);
            $this->db->update('ktv_tc_supplychain_transaction',$update);

            $results['success'] = true;
            $results['message'] = "Payment Success";
            $results['PaymentStatusID'] = (string) $payment->data->PaymentStatusID;
            $results['SupplyTransID'] = $recpay->SupplyTransID;
            $results['data'] = $payment->data;
        }else{
            $results['success'] = false;
            $results['message'] = $payment['ErrorMessage'];
            $results['PaymentStatusID'] = $post['PaymentStatusID'];
            $results['SupplyTransID'] = $recpay->SupplyTransID;
            $results['data'] = array();
        }
        
        return $results;
    }

    public function CheckPaymentStatus($get){
        $results =array();
        $param = array(
            "PaymentMethodID" => $get['PaymentMethodID'],
            "uid" => $get['uid'],
            "LanguageID" => $_SESSION['language']=='Indonesia'?2:1
        );
        $PaymentStatus = $this->APIChekPaymentStatus($param);

        if($PaymentStatus->success==true AND  intval($PaymentStatus->data->PaymentStatusID)!=0){
            $ps=$PaymentStatus->data->PaymentDetail[0];
            
            $sql = "SELECT
                        SupplyTransID,
                        PaymentStatusID,
                        CompanyCode,
                        VirtualAccount,
                        TotalPayment,
                        ServiceCharge,
                        TotalPaymentWithServiceCharge,
                        DisburseFee,
                        TotalDisburse,
                        DateCreated
                    FROM
                        ktv_tc_supplychain_transaction
                    WHERE SupplyTransID = ?";
            $check = $this->db->query($sql,array($get['SupplyTransID']))->row();
            if($check->CompanyCode=="" OR $check->VirtualAccount=="" OR $check->ServiceCharge=="" OR $check->DisburseFee=="" OR $check->TotalDisburse==""){
                $update = array(
                    "PaymentStatusID"   => $ps->PaymentStatusID,
                    "PaymentMethodID"   => $ps->PaymentMethodID,
                    "CompanyCode"       => $ps->CompanyCode,
                    "VirtualAccount"    => $ps->VirtualAccount,
                    "ServiceCharge"     => $ps->TotalServiceCharge,
                    "TotalPaymentWithServiceCharge" => $ps->TotalPaidWithServiceCharge,
                    "DisburseFee" => $ps->FeeDisburse,
                    "TotalDisburse" => $ps->TotalDisburse
                );
            }else{
                $update = array(
                    "PaymentStatusID"   => $ps->PaymentStatusID,
                    "PaymentMethodID"   => $ps->PaymentMethodID,
                    "DisburseFee"       => $ps->FeeDisburse,
                    "TotalDisburse"     => $ps->TotalDisburse
                );
            }
            $this->db->where("SupplyTransID",$get['SupplyTransID']);
            $this->db->update("ktv_tc_supplychain_transaction",$update);
            
            $results['success'] = true;
            $results['message'] = "Check Payment Success";
            $results['data'] = $PaymentStatus->data;

        }else{
            $results['success'] = false;
            $results['message'] = "Check Payment Failed";
            $results['data'] = array();
        }

        return $results;
       
    }

    private function getUID($length = 11) {
        $characters = '012345678910';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

?>
