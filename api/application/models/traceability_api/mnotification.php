<?php

/**
 * Authentication Model for Mobile
 *
 * @author Eka <noersa.eka@koltiva.com>
 */
class Mnotification extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
    }

    public function _getNotification($userid)
    {
        $sql = "SELECT * FROM view_tc_supplychain_staff WHERE UserID=?";
        $user = $this->db->query($sql, array($userid));
        if ($user->num_rows() > 0) {
            $user = $user->result_array();
            $SupplychainID = $user[0]['SupplychainID'];
            $PartnerID = $user[0]['PartnerID'];
            $sql = "SELECT
                        mn.NotifID AS NotifID,
                        mn.PartnerID AS PartnerID,
                        mn.OrgID AS OrgID,
                        mn.OrgType AS OrgType,
                        mn.NotifTitle AS NotifTitle,
                        mn.NotifMessage AS NotifMessage,
                        mn.DateCreated AS DateCreated
                    FROM
                        ktv_mobile_notification mn
                    WHERE
                        mn.StatusCode='active' AND mn.OrgID=? AND mn.OrgType='SupplychainID'
                    UNION
                        SELECT
                        AnnID AS NotifID,
                        '' AS PartnerID,
                        c.ObjID AS OrgID,
                        c.ObjType AS OrgType,
                        b.Title AS NotifTitle,
                        b.Content AS NotifMessage,
                        b.DateCreated
                    FROM
                        cms_announcement b
                        LEFT JOIN cms_access c ON b.AnnID = c.ObjID
                        AND c.ObjType = 'announcement'
                        AND b.StatusType = 'private'
                    WHERE
                        1 = 1
                    AND
                    IF
                        ( b.StatusType = 'private', c.RoleAccessTrader = 1 AND ? IN ( c.PartnerIDImplode ), 1 = 1 )
                        ";
            $Q = $this->db->query($sql, array($SupplychainID, $PartnerID));
            if ($Q->num_rows() > 0) {
                $results = $Q->result_array();
                return array_values($results);
            } else {
                return array();
            }
        } else {
            return array();
        }

    }

}
