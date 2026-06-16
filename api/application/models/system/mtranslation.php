<?php

class Mtranslation extends CI_Model {

    var $coop;

    function __Construct() {
        parent::__Construct();
        $this->coop = getCoopID();
    }

    function readTranslations($key, $start, $limit) {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                    t.key
            FROM 
            sys_translation t
            WHERE (t.`key` LIKE ? OR t.`text` LIKE ?)
            GROUP BY
            t.key
            ORDER BY t.key
            %s
        ";
        $query = $this->db->query(sprintf($sql, 'LIMIT ?,?'), array("%$key%", "%$key%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        $sql_row = "SELECT FOUND_ROWS() AS total";
        $row = $this->db->query($sql_row, array());
        $total = $row->result_array();
        $result['total'] = $total[0]['total'];
        return $result;
    }

    function readListLang() {
        $sql = "
            SELECT 
              `code`,
              `name` AS `text`,
              `code` AS `dataIndex`,
              '' AS `hidden`,
              '24%' AS `width`
            FROM
              `sys_language`
              ";
        $query = $this->db->query($sql, array());
        $result = $query->result_array();
        return $result;
    }

    function readTranslationByKey($key, $lang) {
        $sql = "
            SELECT 
              a.text
            FROM
              `sys_translation` a 
            WHERE a.`key` = ?
            AND a.`language` = ?
              ";
        $query = $this->db->query($sql, array($key, $lang));
        $result = $query->result_array();
        return $result[0]['text'];
    }

    /* function readTranslationList() {
      $sql = "
      SELECT SQL_CALC_FOUND_ROWS
      `id`,
      `key`,
      `language`,
      `set`,
      `text`
      FROM
      `sys_translation`
      WHERE `key` LIKE ?
      ";
      $query = $this->db->query(sprintf($sql, ''), array());
      $result['data'] = $query->result_array();

      $sql_row = "SELECT FOUND_ROWS() AS total";
      $row = $this->db->query($sql_row, array());
      $total = $row->result_array();
      $result['total'] = $total[0]['total'];

      return $result;
      }
     */

    function readTranslation($id) {
        $sql = "
            SELECT
                  a.`name`,
                  a.`code` AS `dataIndex`,
                  b.`id` AS trans_id,
                  b.`text`
            FROM
                    (SELECT ? AS `key`) r
            LEFT JOIN sys_language AS a ON 1 = 1
            LEFT JOIN sys_translation b ON b.language = a.`code` AND b.`key` = r.`key`
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();

        return $result;
    }

    function cekTranslation($key, $id) {
        $sql = "
        SELECT
          `id`,
          `key`,
          `language`,
          `set`,
          `text`
          FROM
          `sys_translation`
        WHERE `key` = ?
        --where--
        ";
        $where = "";

        if (!empty($id))
            $where = " AND `key` != '$id'";

        $sql = str_replace('--where--', $where, $sql);
        $query = $this->db->query($sql, array($key));

        $result = $query->result_array();

        return $result;
    }

    function createTranslation($params) {
        $sql = "
        INSERT INTO `sys_translation` (
          `key`,
          `language`,
          `set`,
          `text`
        )VALUES(
            ?,
            ?,
            ?,
            ?
        )        
        ";
        $this->db->trans_start();
        $query = $this->db->query($sql, $params);

        if ($query) {
            $results['success'] = true;
            $this->db->trans_commit();
            $results['message'] = "Translation Created.";
        } else {
            $results['success'] = false;
            $this->db->trans_rollback();
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateTranslation($params) {
        $sql = "
            UPDATE 
              `sys_translation` 
            SET
              `key` = ?,
              `language` = ?,
              `set` = ?,
              `text` = ? 
            WHERE `id` = ?
            ";
        $this->db->trans_start();
        $query = $this->db->query($sql, $params);
        if ($query) {
            $results['success'] = true;
            $this->db->trans_commit();
            $results['message'] = "Translation Updated.";
        } else {
            $results['success'] = false;
            $this->db->trans_rollback();
            $results['message'] = "Failed to update Translation";
        }
        return $results;
    }

    function deleteTranslation($id) {
        $sql = "
            DELETE 
            FROM
              `sys_translation` 
            WHERE `key` = ?
        ";
        $this->db->trans_start();
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $this->db->trans_commit();
            $results['message'] = "Translation Deleted.";
        } else {
            $results['success'] = false;
            $this->db->trans_rollback();
            $results['message'] = "Failed to delete Translation";
        }
        return $results;
    }

}

?>
