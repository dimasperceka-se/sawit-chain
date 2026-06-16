<?php

class Mlogupload extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getLogUploads($key, $start, $limit) {
        $sql = "
            SELECT
                `logID`,
                `logCategory`,
                `logFileName`,
                `logStatus`,
                `UserExecuted`,
                `UserRealName`,
                `DateExecuted`
            FROM
                `sys_log_upload`
            LEFT JOIN sys_user ON UserId = UserExecuted
            %s
            ORDER BY logID DESC
            %s
        ";

        $where = "WHERE (
                logCategory LIKE (?)
                OR logFileName LIKE (?)
                OR logStatus LIKE (?)
                )
        ";

        $query = $this->db->query(sprintf($sql, $where, 'LIMIT ?,?'), array('%' . $key . '%', '%' . $key . '%', '%' . $key . '%', (int) $start, (int) $limit));
        $result['data'] = $query->result_array();

        $row = $this->db->query(sprintf($sql, $where, ''), array('%' . $key . '%', '%' . $key . '%', '%' . $key . '%'));
        $total_row = $row->result_array();
        $result['total'] = count($total_row);

        return $result;
    }

    function insertLogUpload($log_type, $name, $status, $user_id) {
        $sql = "
            INSERT INTO `sys_log_upload` (
                `logCategory`,
                `logFileName`,
                `logStatus`,
                `UserExecuted`,
                `DateExecuted`
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                NOW()
            )
        ";
        $this->db->query($sql, array($log_type, $name, $status, $user_id));
    }

    public function insertLogUploadPolygonSurveyPlot($fileZip){
        $sql="INSERT INTO `log_survey_plot_polygon_process` SET `filezip` = ?";
        $p = array(
            $fileZip
        );
        $this->db->query($sql, $p);
    }

}

?>
