<?php

class Memail extends CI_Model {

    function readEmailLogs($key, $date, $start, $limit) {
        if ($date){
            $alt_date = explode('T', $date);
            $date = $alt_date[0];
        }
        $sql = "
            SELECT %s
            FROM ktv_email_log
            WHERE (EmailSubject LIKE ? OR EmailTo LIKE ? OR EmailFrom LIKE ?)
                AND DATE(EmailAddTime) LIKE ?
            ORDER BY EmailID %s";
        $query = $this->db->query(sprintf($sql, 'EmailID AS id,EmailSubject,EmailTo,EmailFrom,DATE(EmailAddTime) AS EmailAddTime', 'LIMIT ?,?'), array("%$key%", "%$key%", "%$key%", "%$date%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array("%$key%", "%$key%", "%$key%", "%$date%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readEmailLog($id) {
        $sql = "
            SELECT 
                EmailID AS id,
                EmailSubject,
                EmailTo,
                EmailFrom,
                EmailBody,
                DATE(EmailAddTime) AS EmailAddTime
            FROM ktv_email_log 
            WHERE 
                EmailID=?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        $return['data'] = $result[0];
        return $result[0];
    }

    function getEmailByUserId($userid) {
        $sql = "
            SELECT 
                UserName
            FROM sys_user
            WHERE UserId = ?
            ";
        $query = $this->db->query($sql, array($userid));
        $result = $query->result_array();
        return $result[0]['UserName'];
    }

    function createEmailLog($subject, $to, $from, $body, $userid) {
        $sql = "
            INSERT INTO ktv_email_log(
                EmailSubject,
                EmailTo,
                EmailFrom,
                EmailBody,
                EmailAddUserId,
                EmailAddTime
            ) VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                now()
            )";
        $query = $this->db->query($sql, array($subject, $to, $from, $body, $userid));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateEmailLog($subject, $to, $from, $body, $id, $userid) {
        $sql = "
            UPDATE ktv_email_log 
            SET 
                EmailSubject=?,
                EmailTo=?,
                EmailFrom=?,
                EmailBody=?,
                EmailUpdateUserId=?,
                EmailUpdateTime=now()
            WHERE EmailID=?";
        $query = $this->db->query($sql, array($subject, $to, $from, $body, $userid, $id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function getComboEmail() {
        $sql = "
            SELECT 
                a.`UserName` AS id,
                CONCAT(a.`UserRealName`,' (',a.`UserName`,')') AS label
            FROM 
                sys_user a 
            where a.UserName != '' AND a.`UserRealName` != ''
            ORDER BY a.UserRealName ASC
            ";
        $query = $this->db->query($sql);
        $result['data'] = $query->result_array();
        return $result;
    }

    function deleteEmailLog($id) {
        $sql = "
            DELETE FROM ktv_email_log WHERE EmailID=?";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

}

?>
