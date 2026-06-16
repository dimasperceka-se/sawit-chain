<?php

class Mbroadcast extends CI_Model {

    public function readBroadcasts($key, $userid, $start = 0, $limit = 50) {
        $PartnerID= $_SESSION['PartnerID'];
        if($PartnerID=='1' || $PartnerID=='37'){
            $p1 = '/*'; $p2 = '*/';
        }else{
            $p1 = ''; $p2 = '';
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.NotifID, IFNULL(b.PartnerName, 'All Partner') Partner, a.NotifMessage, c.UserRealName Creator, a.DateCreated
                FROM ktv_mobile_notification a
                LEFT JOIN ktv_program_partner b ON b.PartnerID=a.PartnerID
                LEFT JOIN sys_user c ON a.CreatedBy=c.UserId
                WHERE a.StatusCode = 'active' AND a.FarmerID IS NULL
                $p1 AND a.PartnerID=? $p2
                AND (b.PartnerName LIKE ? OR a.NotifMessage LIKE ? OR c.UserRealName LIKE ?)
                ORDER BY a.DateCreated DESC
                LIMIT ?, ?";
        $query = $this->db->query($sql, array($PartnerID, "%$key%", "%$key%", "%$key%", intval($start), intval($limit)));
        //echo "<pre>".$this->db->last_query();exit;
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data' => $query->result_array(),
                'total' => $total['total']
            );
        }
        return false;
    }

    function ComboPartner() {
        $sql = "SELECT PartnerID id, PartnerName label FROM ktv_program_partner WHERE StatusCode='active' AND PartnerIndustry IN (2,3,4)";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    public function createBroadcast($PartnerID, $Message, $userid) {
        $sql = "INSERT INTO ktv_mobile_notification (PartnerID, FarmerID, OrgType, NotifMessage, StatusCode, DateCreated, CreatedBy) 
                  VALUES (?, NULL, 'broadcast', ?, 'active', NOW(), ?)";
        $query = $this->db->query($sql, array($PartnerID, $Message, $userid));
        $NotifID = $this->db->insert_id();
        if ($query) {
            $results['success'] = "true";
            $results['message'] = "Record created.";
            $results['NotifID'] = $NotifID;
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    public function updateBroadcast($NotifID, $PartnerID, $Message, $userid) {
        $sql = "UPDATE ktv_mobile_notification SET PartnerID=?, NotifMessage=?, DateUpdated=NOW(), LastModifiedBy=? WHERE NotifID=?";
        $query = $this->db->query($sql, array($PartnerID, $Message, $userid, $NotifID));
        
        if ($query) {
            $results['success'] = "true";
            $results['message'] = "Record updated.";
            $results['NotifID'] = '';
            
        } else {
            $results['success'] = "false";
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    public function deleteBroadcast($userid, $NotifID) {
        $sql = "UPDATE ktv_mobile_notification SET StatusCode='nullified', LastModifiedBy=?, DateUpdated=NOW() WHERE NotifID=?";
        $query = $this->db->query($sql, array($userid, $NotifID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record.";
        }
        return $results;
    }

    public function readBroadcastDetail($NotifID) {
        $sql = "SELECT *, IF(PartnerID=0, '', PartnerID) partnerID FROM ktv_mobile_notification WHERE NotifID=?";
        $query = $this->db->query($sql, array($NotifID));
        $return = $query->result_array();
        return $return[0];
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function readFiles($IssuesID, $FileBundle) {
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.FileID, a.FilePath, a.FileName, a.FileSize, a.DateCreated Creatime, b.UserRealName Creator
                FROM sys_issues_files a
                LEFT JOIN sys_user b ON a.CreatedBy=b.UserId
                WHERE a.StatusCode='active' AND ( a.IssuesID=? OR (a.FileBundle=? AND FileBundle!='') ) ";
        $query = $this->db->query($sql, array($IssuesID, $FileBundle));
        $sql_total = "SELECT FOUND_ROWS() AS total";
        $query_total = $this->db->query($sql_total);
        if ($query->num_rows() > 0) {
            $total = $query_total->row_array(0);
            return array(
                'data' => $query->result_array(),
                'total' => $total['total']
            );
        }
        return false;
    }

    public function uploadFiles($IssuesID, $FileBundle, $UserId) {
        if ($IssuesID == '')
            $IssuesID = NULL;
        $upload_path = './files/ticketing/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '5120'; //10MB
        $this->load->library('upload');
        $config['upload_path'] = $upload_path;
        $config['encrypt_name'] = TRUE;

        $this->upload->initialize($config);
        if (!empty($_FILES['File']['tmp_name'])) {
            if (!$this->upload->do_upload('File')) {
                $results['infos'] = "Warning";
                $results['status'] = "false";
                $results['message'] = $this->upload->display_errors();
            } else {
                $file = $this->upload->data();
                $sql_insert = "INSERT INTO sys_issues_files(IssuesID, FileBundle, FileName, FilePath, FileType, FileExt, FileSize, FileDesc, CreatedBy, StatusCode, DateCreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())";
                $insert = $this->db->query($sql_insert, array($IssuesID, $FileBundle, $file['orig_name'], $file['file_name'], $file['file_type'], $file['file_ext'], $file['file_size'], '', $UserId));
                if ($insert) {
                    $results['infos'] = "Success";
                    $results['status'] = "true";
                    $results['message'] = "Upload success.";
                } else {
                    $results['infos'] = "Warning";
                    $results['status'] = "false";
                    $results['message'] = "Upload failed.";
                }
            }
        } else {
            $results['infos'] = "Warning";
            $results['status'] = "false";
            $results['message'] = "Upload failed. Please check the file. Max file size is 5MB.";
        }
        return $results;
    }

    

    public function getIssues($IssuesID) {
        $sql = "SELECT
                    a.*, b.UserRealName Creator, c.UserRealName Updator, (SELECT COUNT(*) AS Total FROM sys_issues_files aa WHERE aa.IssuesID=a.IssuesID) Files, rp.PositionName
                FROM
                    sys_issues a
                    LEFT JOIN sys_user b ON a.CreatedBy=b.UserId
                    LEFT JOIN sys_user c ON a.LastModifiedBy=c.UserId
                    LEFT JOIN ktv_persons p ON p.UserID=b.UserId
                    LEFT JOIN ktv_staffs s ON s.PersonID=p.PersonID
                    LEFT JOIN ktv_staff_positions sp ON sp.StaffPosStaffID=s.StaffID
                    LEFT JOIN ktv_ref_position_type rp ON rp.PositionID=sp.StaffPosPositionID
                WHERE
                    (a.IssuesID = ? OR a.IssuesParent = ?)
                AND a.StatusCode = 'active' 
                ORDER BY IssuesID DESC";
        $all = $this->db->query($sql, array($IssuesID, $IssuesID));
        $sql = "SELECT i.IssuesID, i.`Subject`, i.IssuesStatus, it.IssuesType, ip.IssuesPriority
                FROM
                    sys_issues i
                    LEFT JOIN sys_issues_type it ON it.IssuesTypeID=i.IssuesTypeID
                    LEFT JOIN sys_issues_priority ip ON ip.IssuesPriorityID=i.IssuesPriorityID
                WHERE i.IssuesID=?";
        $detail = $this->db->query($sql, array($IssuesID))->result_array();
        $sql = "SELECT g.*
                FROM 
                    sys_user u
                    LEFT JOIN sys_user_group ug ON ug.UserGroupUserId=u.UserId
                    LEFT JOIN sys_group g ON g.GroupId=ug.UserGroupGroupId
                WHERE UserId = ? AND g.GroupId IN (1,158)";
        $user = $this->db->query($sql,array($_SESSION['userid']));
        if($user->num_rows() > 0){
            $u = 1;
        }else{
            $u = 0;
        }
        $return = array(
            'all' => $all,
            'close' => $u,
            'detail' => $detail[0]
        );
        return $return;
    }

    public function getFiles($IssuesID) {
        $sql = "SELECT *  FROM sys_issues_files WHERE IssuesID = ? AND StatusCode = 'active'";
        $query = $this->db->query($sql, array($IssuesID));
        return $query;
    }

    
    

    

    public function checkDownload($userid, $File) {
        $files = explode("_", $File);
        $sql = "SELECT * FROM sys_issues_files WHERE IssuesID=? AND FilePath=?";
        $query = $this->db->query($sql, array($files[0], $files[1]));
        if ($query->num_rows() > 0) {
            $sql = "UPDATE sys_issues_files SET Download=(IFNULL(Download,0)+1) WHERE IssuesID=? AND FilePath=?";
            $query = $this->db->query($sql, array($files[0], $files[1]));
            $results['success'] = "true";
            $results['message'] = "Download success";
            $results['url'] = "files/ticketing/" . $files[1];
        } else {
            $results['success'] = "false";
            $results['message'] = "No Files";
            $results['url'] = "";
        }
        return $results;
    }

    public function deleteFile($userid, $FileID, $FilePath) {
        $sql = "DELETE FROM sys_issues_files WHERE FileID=? AND FilePath=?";
        $query = $this->db->query($sql, array($FileID, $FilePath));
        if ($this->db->affected_rows() > 0) {
            if (file_exists('files/ticketing/' . $FilePath)) {
                delete_file('files/ticketing/' . $FilePath);
            }
            $results['success'] = true;
            $results['message'] = "record deleted.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record.";
        }
        return $results;
    }

    public function closeIssue($userid, $IssuesID) {
        $sql = "UPDATE sys_issues SET IssuesUpdated=NOW(), IssuesUpdateBy=?, IssuesStatus=? WHERE IssuesID=?";
        $query = $this->db->query($sql, array($userid, 'Close', $IssuesID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "Record updated.";
            
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function listIssuesType() {
        $sql = "SELECT IssuesTypeID id, IssuesType label FROM sys_issues_type";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    function getTypeDetail($typeid) {
        $sql = "SELECT IssuesTypeID id, IssuesType FROM sys_issues_type WHERE IssuesTypeID = ?";
        $query = $this->db->query($sql, array($typeid));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['IssuesType'];
        }
    }

    function listIssuesPriority() {
        $sql = "SELECT IssuesPriorityID id, IssuesPriority label FROM sys_issues_priority";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    function getPriorityDetail($priorityid) {
        $sql = "SELECT IssuesPriorityID id, IssuesPriority FROM sys_issues_priority WHERE IssuesPriorityID = ?";
        $query = $this->db->query($sql, array($priorityid));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['IssuesPriority'];
        }
    }

    function emailSent($id) {
        die;
        //Kirim emailnya
        require_once 'application/third_party/phpmailer-hr/class.phpmailer.php';
        $this->config->load('email');

        $ObjMail = new PHPMailer();
        $ObjMail->IsSMTP();
//        $ObjMail->SMTPDebug = 2;
        $ObjMail->SMTPSecure = 'tls';
        $ObjMail->SMTPAuth = true;
        $ObjMail->Host = $this->config->item('email_Host');
        $ObjMail->Port = $this->config->item('email_Port');
        $ObjMail->Username = $this->config->item('email_Username');
        $ObjMail->Password = $this->config->item('email_Password');

        $ObjMail->Priority = 0;
        $ObjMail->SetFrom($this->config->item('email_from'), 'Koltiva Support');

        $data = $this->getTicketDetail($id);

        if ($data) {
            foreach ($data as $key => $val) {
                $description[$key]['type'] = $val['type'];
                $description[$key]['description'] = $val['Description'];
                $description[$key]['created'] = $val['created'];
                $description[$key]['datecreated'] = $val['DateCreated'];
                if ($val['type'] == 'parent') {
                    $emailto[] = $val['email'];
                    $type = $val['IssuesType'];
                    $priority = $val['IssuesPriority'];
                    $subject = $val['Subject'];
                    $status = $val['IssuesStatus'];
                    $root_created = $val['created'];
                } else {
                    $emailcc[] = $val['email'];
                }
            }
        }

        $body = '';
        foreach ($description as $key => $val) {
            $body .= "
                <div class='well'>
                    <p><small><b>" . $val['created'] . " - " . date("d/m/Y H:i", strtotime($val['datecreated'])) . "</b></small></p>
                    
                    <p>" . $val['description'] . "</p>
                </div>";
        }

        $ObjMail->Subject = 'Ticket [' . $type . '/' . $priority . ']- ' . $subject;

        $content = 'Whatever you want to insert...';
        $tpl = file_get_contents('files/email/ticket.html');

        if ($status == 'New') {
            $opening = "Anda telah berhasil membuat tiket baru. Berikut detail tiket Anda :";
        } else if ($status == 'Open') {
            $opening = "Tiket yang anda buat telah ditanggapi. Berikut detail tiket Anda :";
        } else if ($status == 'Close') {
            $opening = "Tiket yang anda buat telah ditutup. Berikut detail tiket Anda :";
        }

        $tpl = str_replace('{{root_created}}', $root_created, $tpl);
        $tpl = str_replace('{{opening}}', $opening, $tpl);
        $tpl = str_replace('{{body}}', $body, $tpl);
        $tpl = str_replace('{{status}}', $status, $tpl);

        $ObjMail->Body = $tpl;
        $ObjMail->IsHTML(true);

        $ObjMail->AddAddress('info@koltiva.com');

        foreach ($emailto as $val) {
            $ObjMail->AddAddress($val);
        }
        foreach ($emailcc as $val) {
            $ObjMail->AddCC($val);
        }


//        if (file_exists('files/tmp/' . $namaFileExcelForAttach)) {
//            $ObjMail->AddAttachment('files/tmp/' . $namaFileExcelForAttach);
//        }

        $result = $ObjMail->Send();

        $ObjMail->ClearAddresses();
        $ObjMail->ClearAllRecipients();
        $ObjMail->IsHTML(false);

        //hapus filenya
        if (file_exists('files/tmp/' . $namaFileExcelForAttach)) {
            delete_file('files/tmp/' . $namaFileExcelForAttach);
        }
    }

    function getTicketDetail($id) {
        $sql = "
                SELECT 
                  IF (a.IssuesParent = 0, 'parent', 'child') AS `type`,
                  a.IssuesParent,
                  a.IssuesID,
                  c.IssuesType,
                  b.IssuesPriority,
                  a.Subject,
                  a.Description,
                  a.IssuesStatus,
                  d.UserRealName AS created,
                  a.DateCreated,
                  g.OfficialEmail AS `email` 
                FROM
                  sys_issues a 
                  LEFT JOIN sys_issues_priority b 
                    ON a.IssuesPriorityID = b.IssuesPriorityID 
                  LEFT JOIN sys_issues_type c 
                    ON a.IssuesTypeID = c.IssuesTypeID 
                  LEFT JOIN sys_user d 
                    ON d.UserId = a.IssuesUpdateBy 
                  LEFT JOIN ktv_persons e 
                    ON d.UserId = e.UserID 
                  LEFT JOIN sys_user f 
                    ON f.UserId = a.CreatedBy 
                  LEFT JOIN ktv_persons g 
                    ON g.UserID = f.UserId 
                WHERE (
                    a.IssuesID = ?
                    OR a.IssuesParent = ?
                  ) 
                  AND a.StatusCode = 'active' 
                ORDER BY a.IssuesParent,
                  a.IssuesID 
            ";

        $query = $this->db->query($sql, array($id, $id));
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

}

?>
