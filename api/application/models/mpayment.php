<?php

class Mpayment extends CI_Model {
    
    public function readDataPrePayment($get){
        // @$SupplychainID = $this->db->query("SELECT SupplychainID FROM ktv_supplychain_org WHERE OrgID=?", array($_SESSION['ObjID']))->row()->SupplychainID;
        // if($SupplychainID==''){
        //     @$SupplychainID = $this->db->query("SELECT SupplychainID FROM view_supplychain_staff WHERE UserID=?", array($_SESSION['userid']))->row()->SupplychainID;
        // }
        $PartnerID = getPartnerID($_SESSION['userid']);
        $SupplychainID = getSupplychainID($_SESSION['userid']);
        
        $order = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'scp.DateCreated' : $order[0]['property'];
        $sort = $order[0]['direction']=='' ? 'DESC' : $order[0]['direction'];
        if(@$get['StartDate']!=='' && @$get['EndDate']!=''){
            $date1 = ''; $date2 = '';
        }else{
            $date1 = '/*'; $date2 = '*/';
        }
        $t1 = @$get['TransNumber']=='' ? '/*' : '';
        $t2 = @$get['TransNumber']=='' ? '*/' : '';
        $ts1 = @$get['PaymentStatusID']=='' ? '/*' : '';
        $ts2 = @$get['PaymentStatusID']=='' ? '*/' : '';

        if(intval($SupplychainID)>0){
            $sid1="/*";
            $sid2="*/";
        }else{
            $sid1="/*";
            $sid2="*/";
        }

        $statusPayment1 = "/*";
        $statusPayment2 = "*/";

        if (!empty($get['PaymentStatusID'])) {
            $statusPayment1 = "";
            $statusPayment2 = "";
        }
       
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                scp.CustomPaymentID,
                scp.PaymentMethodID,
                scp.TransactionNumber,
                scp.PartnerID,
                scp.SupplychainID,
                scp.PartnerRecipientID,
                /* kpp.PartnerName, */
                kpp.PartnerName as PartnerName,
                scp.RecipientID,
                kpp2.PartnerName as Recipient,
                /*vso.BankName,*/
                '' as BankName,
                scp.AccountName,
                scp.AccountNumber,
                scp.Amount,
                scp.ServiceCharge,
                scp.TotalPaymentWithServiceCharge,
                scp.DisburseFee,
                scp.TotalDisburse,
                date(scp.DateCreated) Date,
                scp.Status,
                IF(scp.Status='Draft',scp.Status,pms.PaymentStatus) TransStatus,
                scp.uid

                FROM ktv_tc_supplychain_custompayment scp
                LEFT JOIN ref_tc_payment_method_status pms ON pms.PaymentStatusID=scp.PaymentStatusID
                LEFT JOIN ktv_program_partner kpp ON scp.PartnerID=kpp.PartnerID
                LEFT JOIN ktv_program_partner kpp2 ON scp.PartnerRecipientID=kpp2.PartnerID
                /* LEFT JOIN ktv_program_partner kpp ON kpp.PartnerID=vso.PartnerID */
                WHERE 
                scp.StatusCode='active'
                AND (scp.PartnerID =? OR scp.PartnerRecipientID=?)
                $sid1 AND (scp.SupplychainID=? OR scp.RecipientID=?) $sid2
                AND scp.TransactionNumber LIKE ?
                $date1 AND date(scp.DateCreated) BETWEEN ? AND ? $date2
                $statusPayment1 AND scp.PaymentStatusID = ? $statusPayment2
                ORDER BY $order_by $sort
                LIMIT ?,?";
        $query = $this->db->query($sql, array($PartnerID,$PartnerID,$SupplychainID,$SupplychainID, "%".@$get['TransNumber']."%", SUBSTR(@$get['StartDate'],0,10), SUBSTR(@$get['EndDate'],0,10), $get['PaymentStatusID'],intval(@$get['start']), intval(@$get['limit'])));
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        }else{
            return false;
        }
    }

    public function saveTransaction($post){
        $PartnerID     = getPartnerID($_SESSION['userid']);
        // $SupplychainID = getSupplychainID($_SESSION['userid']);
        $SupplychainID = 0;

        $CustPayment = array(
            'PaymentStatusID'   => $post['PaymentStatusID'],
            'PaymentMethodID'   => $post['PaymentMethodID'],
            'PartnerID'         => $PartnerID,
            'SupplychainID'     => $SupplychainID,
            'PartnerRecipientID'=> 1,
            'RecipientID'       => $post['Prepayment-Recipient'],
            'AccountName'       => $post['AccountName'],
            'AccountNumber'     => $post['AccountNumber'],
            'Amount'            => $post['Amount'],
            'Status'            => $post['StatusID'],
        );
        
        $check = $this->db->query("SELECT * FROM ktv_tc_supplychain_custompayment WHERE CustomPaymentID=?", array($post['PrepaymentID']));
        if ($check->num_rows() > 0) {

            $CustPayment['LastModifiedBy'] = $_SESSION['userid'];
            $CustPayment['DateUpdated']    = date('Y-m-d H:i:s');

            $this->db->where('CustomPaymentID',$post['PrepaymentID']);
            $query = $this->db->update('ktv_tc_supplychain_custompayment', $CustPayment);

            $message = "Record Updated";
            $CustPayment['CustomPaymentID'] = $post['PrepaymentID'];
        }else{
            $CustPayment['TransactionNumber'] = $PartnerID.$post['Prepayment-Recipient'].date('YmdHis');
            $CustPayment['uid']               = base64_encode(date('His')).$PartnerID.$post['Prepayment-Recipient'].date('YmdHis');
            $CustPayment['StatusCode'] = 'active';
            $CustPayment['CreatedBy'] = $_SESSION['userid'];
            $CustPayment['DateCreated'] = date('Y-m-d H:i:s');
            $query = $this->db->insert('ktv_tc_supplychain_custompayment', $CustPayment);
            $CustPayment['CustomPaymentID'] = $this->db->insert_id();
            $message = "Record Inserted";

        }
       
        if($query){
            $results['success'] = true;
            $results['message'] = $message;
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        $results['data'] = $CustPayment;
        return $results;
    }

    public function comboRecipient($get){

        $order    = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'label' : $order[0]['property'];
        $sort     = $order[0]['direction']=='' ? 'ASC' : $order[0]['direction'];

        // $PartnerID = getPartnerID($_SESSION['userid']);

        // $sql="  SELECT 
        //             SQL_CALC_FOUND_ROWS
        //             SupplychainID as id,
        //             Name as label,
        //             ObjType as org_type,
        //             Address
        //         FROM view_tc_supplychain_org
        //         WHERE PartnerID=?
                
        //         AND (SupplychainID LIKE ? OR Name LIKE ?)
        //         GROUP BY id
        //         ORDER BY label ASC
        //         LIMIT ?,?
        // ";

        $sql="  SELECT 
                    SQL_CALC_FOUND_ROWS
                    '0' as id,
                    PartnerName as label,
                    '-' as org_type,
                    '-' as Address
                FROM ktv_program_partner
                WHERE PartnerID = ?
                
                AND (PartnerName LIKE ?)
                GROUP BY id
                ORDER BY label ASC
                LIMIT ?,?
        ";
        
       $query = $this->db->query($sql, array(1,"%".@$get['query']."%",intval(@$get['start']), intval(@$get['limit'])));
              
        $sql_total= "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data'      => $query->result_array(),
                'total'     => $total['total']
                );
        } else {
            return false;
        }
    }

    public function getBankAccount($get){
        // $sql="SELECT SupplychainID, Name, BankName, BankCode, AccountNumber, AccountName
        //         FROM view_supplychain_org
        //         WHERE SupplychainID=?";
        // $query = $this->db->query($sql,array($get['SupplychainID']));

        $sql = "SELECT
                  '0' as SupplychainID
                  ,PartnerName as Name
                  ,'-' as BankName
                  ,'-' as BankCode
                  ,'0' as AccountNumber
                  ,PartnerName as AccountName
                FROM ktv_program_partner
                WHERE PartnerID = 1
                ";

        $query = $this->db->query($sql);

        return $query->row_array(); 
    }

    public function readDetailPayment($get){
        // echo '<pre>';
        // var_dump($get);
        // die;
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

    public function DeletePayment($del){
        $data = array(
            'StatusCode' => 'nullified'
        );
        $this->db->where('CustomPaymentID',$del['CustomPaymentID']);
        $query = $this->db->update('ktv_tc_supplychain_custompayment', $data);
        if($query){
            $results['success'] = true;
            $results['message'] = "Record Deleted";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }

        return $results;
    }

    private function _postServiceCharge($PaymentMethodID,$totalPaid){
        
        $getAPISettings = dynamicSettingAPIPayment(base_url());
        $AppID = "1!dM2Wwrwaitt2yPeeaPb0eUnam";
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

        // var_dump($insertToLog);
        // die();

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
        $AppID = "1!dM2Wwrwaitt2yPeeaPb0eUnam";
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
        $AppID = "1!dM2Wwrwaitt2yPeeaPb0eUnam";
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

    public function SubmitPayment($paramPost){
        $recpay  = $this->readDetailPayment($paramPost);
        // echo '<pre>';
        // var_dump($recpay);
        // die;

        // $service = $this->_postServiceCharge($paramPost['PaymentMethodID'],$paramPost['Amount'])->data;
        
        $data = array(
                "uid"               => $recpay->uid,
                "SupplyTransID"     => $recpay->SupplyTransID,
                // "PartnerID"         => $recpay->PartnerID,
                // "BuyingUnitName"    => $recpay->BuyingUnit,
                "TransactionDate"   => $recpay->DateTransaction, 
                "TransactionNumber" => $recpay->TransNumber,
                
                "SupplierID"        => $recpay->MemberDisplayID,
                "SupplierName"      => $recpay->MemberName,
                "SupplierType"      => $recpay->SupplybaseType,
                "BankCode"          => $paramPost['BankCode'],
                "BankName"          => $paramPost['BankName'],
                "AccountNumber"     => $paramPost['AccountNumber'],
                "AccountName"       => $paramPost['AccountName'],
                "AccountEmoney"     => "",
                "AccountUsername"   => "",

                "PaymentMethodID"   => $recpay->PaymentMethodID?$recpay->PaymentMethodID:2,
                "TotalPaid"         => $recpay->PaymentPaid,
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
                        "TotalPaid"                   => $service->PaymentPaid,
                        "TotalPaidWithServiceCharge"  => $service->TotalPaidWithServiceCharge,
                        "EmoneyToken"                 => "",
                        "PIN"                         => "",
                        "DetailNotes"                 => ""
                    )
                ]
        );

        // echo '<pre>';
        // var_dump($data);
        // die;
    
        $payment = $this->apiSubmitPayment($data);

        echo '<pre>';
        var_dump($payment);
        die;

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
           
            $this->db->where('CustomPaymentID',$recpay->CustomPaymentID);
            $this->db->update('ktv_tc_supplychain_custompayment',$update);

            $results['success'] = true;
            $results['message'] = "Payment Success";
            $results['PaymentStatusID'] = (string) $payment->data->PaymentStatusID;
            $results['data'] = $payment->data;
        }else{
            $results['success'] = false;
            $results['message'] = $payment['ErrorMessage'];
            $results['PaymentStatusID'] = $post['PaymentStatusID'];
            $results['data'] = array();
        }
        
        return $results;
    }

    public function getPaymentInstruction($get){
        $language = $_SESSION['language'];

        $pay = $this->readDetailPrepayment($get);
        
        if(intval($pay->PaymentMethodID)==1){
            $instruction = $this->ApiPaymentInstruction($pay->PaymentMethodID,$language);
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

        $return = array(
            'PaymentVia'           => lang("Payment via")." ".$pay->PaymentMethod,
            'PaymentViaLogo'       => $paymentLogo,
            'TransactionNumber'    => $pay->TransactionNumber,
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
                        sc.CustomPaymentID,
                        sc.PaymentStatusID,
                        sc.PaymentMethodID,
                        pu.ObjID AS PaymentUnitId, 
                        sc.CompanyCode,
                        sc.VirtualAccount,
                        sc.Amount,
                        sc.ServiceCharge,
                        sc.TotalPaymentWithServiceCharge,
                        sc.DisburseFee,
                        sc.TotalDisburse,
                        sc.DateCreated
                    FROM
                        ktv_tc_supplychain_custompayment sc
                    LEFT JOIN ref_tc_payment_unit pu ON pu.ObjID = sc.PartnerID
                    WHERE sc.CustomPaymentID = ?";
            $check = $this->db->query($sql,array($get['PrePaymentID']))->row();
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
            $this->db->where("CustomPaymentID",$get['PrePaymentID']);
            $this->db->update("ktv_tc_supplychain_custompayment",$update);

            //check duplicate id payment ktv_tc_balance
            $this->db->where('IDReference', $check->CustomPaymentID);
            $query = $this->db->get('ktv_tc_payment_balance');
            $count_row = $query->num_rows();
        
            if(intval($PaymentStatus->data->PaymentStatusID)==1 && $count_row<2){

                //get last balance
                $sqlpaymentbalance = "SELECT
                                        PaymentBalanceID,
                                        PaymentUnitID,
                                        Credit,
                                        Debit,
                                        Balance
                                    FROM
                                        ktv_tc_payment_balance";

                $checkbalance = $this->db->query($sqlpaymentbalance,array($get['PaymentBalanceID']))->row();

                if($checkbalance == NULL){
                    $balance = 0;
                }else{
                    $balance = $checkbalance->Balance;
                }
            
                $insert = array(
                    "PaymentUnitID"      => $check->PaymentUnitId, //southland
                    "Balance"            => $balance - $check->TotalPaymentWithServiceCharge,
                    "Credit"             => $check->TotalPaymentWithServiceCharge,
                    "Debit"              => 0,
                    "DateTransaction"    => $check->DateCreated,
                    "Remaks"             => "pre payment from soutland to koltiva",
                    "TableReference"     => "ktv_tc_supplychain_custompayment",
                    "IDReference"        => $check->CustomPaymentID
                );
                $query = $this->db->insert('ktv_tc_payment_balance', $insert);

                $checkbalance = $this->db->query($sqlpaymentbalance,array($get['PaymentBalanceID']))->row();

                $insert = array(
                    "PaymentUnitID"      => 1, //koltiva
                    "Credit"             => 0,
                    "Debit"              => $check->TotalPaymentWithServiceCharge,
                    "Balance"            => $balance + $check->TotalPaymentWithServiceCharge,
                    "DateTransaction"    => $check->DateCreated,
                    "Remaks"             => "pre payment from koltiva to koltiva",
                    "TableReference"     => "ktv_tc_supplychain_custompayment",
                    "IDReference"        => $check->CustomPaymentID
                );
                $query = $this->db->insert('ktv_tc_payment_balance', $insert);
            }
            
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

    private function getLanguageID(){

        $query = $this->db->query("SELECT id FROM sys_language WHERE name=?",array($_SESSION['language']))->row();
        return $query->id;
    }

    public function getComboPartner(){
        $a1        = "/*";
        $a2        = "*/";
        $PartnerID = "";

        if ($_SESSION['is_admin'] != 1) {
            if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
                $PartnerID = $_SESSION['PartnerID'];

                $a1 = "";
                $a2 = "";
            } else {
                $PartnerID = $this->db->where('UserID', $_SESSION['userid'])
                                      ->get('view_tc_supplychain_staff')
                                      ->row()
                                      ->PartnerID;

                $a1 = "";
                $a2 = "";
            }
        } else {
            if ($_SESSION['role'] == "Private" || $_SESSION['role'] == "Program") {
                $PartnerID = $_SESSION['PartnerID'];

                $a1 = "";
                $a2 = "";
            }
        }

        $sql="SELECT
                a.`PartnerID` AS id
                , a.`PartnerName` AS label
            FROM
                ktv_program_partner a
            WHERE
                a.`StatusCode` = 'active'
            $a1 AND a.`PartnerID` = ? $a2
            ORDER BY a.`PartnerName` ASC";

        $query = $this->db->query($sql, [$PartnerID]);

        return $query->result_array();
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
}
?>