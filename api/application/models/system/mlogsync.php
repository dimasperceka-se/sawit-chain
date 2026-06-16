<?php
class Mlogsync extends CI_Model {

    function readMenus($get, $opsiLimit = 'limit'){
        $order = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'LogID DESC' : $order[0]['property'];
        $sort = $order[0]['direction']=='' ? '' : $order[0]['direction'];
        $where = "";
        if($get['pDateStart'] != "" && $get['pDateEnd'] != ""){
            $startDate = date('Y-m-d H:i', strtotime($get['pDateStart']));
            $endDate = date('Y-m-d H:i', strtotime($get['pDateEnd']));
            
            $where = " AND DateCreated BETWEEN '$startDate' AND '$endDate'"; 
        }else{
            
        }

        if($get['pDateStart'] == "" && $get['pDateEnd'] == "" && $get['pUserName'] == ""){
            $now = date('Y-m-d H:i:s');
            $end = date_add(date_create($now), date_interval_create_from_date_string('+6 hours'));
            $end2 = date_format($end, 'Y-m-d H:i:s');
            $where .= " AND DateCreated BETWEEN '$now' AND '$end2'";
        }

        if($get['pUserName'] != ""){
            $where .= " AND Username LIKE '%".$get['pUserName']."%'";
        }

        $sql = "SELECT
                    LogID, Payload, Remark, Sender, DateCreated, Username
                FROM
                    log_sync_upload
                WHERE 1=1
                    $where
                ORDER BY $order_by $sort";
        if ($opsiLimit == 'limit'){
            $sql = $sql . " LIMIT ?,?";
            $p = array(intval($get['start']), intval($get['limit']));
        } elseif($opsiLimit == 'no_limit'){
            $sql = $sql;
            $p = array();
        }
        
        $query = $this->db->query($sql,$p);
        $sql_total="SELECT
                        count(*) as total
                    FROM
                        log_sync_upload
                    WHERE 1=1
                        $where";
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

    function readMenusMw2LogProcess($get, $opsiLimit = 'limit'){
        $order = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'a.id DESC' : $order[0]['property'];
        $sort = $order[0]['direction']=='' ? '' : $order[0]['direction'];
        $where = "";
        if($get['pDateStartMw2'] != "" && $get['pDateEndMw2'] != ""){
            $startDate = date('Y-m-d H:i', strtotime($get['pDateStartMw2']));
            $endDate = date('Y-m-d H:i', strtotime($get['pDateEndMw2']));
            
            $where = " AND a.timestamp BETWEEN '$startDate' AND '$endDate'"; 
        }else{
            $now = date('Y-m-d H:i:s');
            $end = date_add(date_create($now), date_interval_create_from_date_string('+6 hours'));
            $end2 = date_format($end, 'Y-m-d H:i:s');
            $where = " AND a.timestamp BETWEEN '$now' AND '$end2'";
        }

        $sql = "SELECT 
                    a.id
                    ,a.log
                    ,a.proc_name
                    /* ,a.eventuid */
                    ,a.timestamp
                FROM
                    mw2_log_process a
                WHERE 1=1
                    $where
                ORDER BY $order_by $sort";
        if ($opsiLimit == 'limit'){
            $sql = $sql . " LIMIT ?,?";
            $p = array(intval($get['start']), intval($get['limit']));
        } elseif($opsiLimit == 'no_limit'){
            $sql = $sql;
            $p = array();
        }

        $query = $this->db->query($sql,$p);
        $sql_total= "SELECT 
                        count(*) as total
                    FROM
                        mw2_log_process a
                    WHERE 1=1
                        $where";
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
    
    function readMenusMw2EventJson($get, $opsiLimit = 'limit'){
        $order = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'a.date_created DESC' : $order[0]['property'];
        $sort = $order[0]['direction']=='' ? '' : $order[0]['direction'];
        $where = "";
        
        if($get['pTextSearch'] != ""){
            $text = $get['pTextSearch'];
            $array=array_map(null, explode(',', $text));
            $array = implode("','",$array);
            $where = " AND a.event_uid IN ('$array')";
        }

        $sql = "SELECT 
                    a.id, a.event_json, a.event_uid, a.program_uid, a.date_created
                FROM
                    mw2_event_json a
                WHERE 1=1
                    $where
                ORDER BY $order_by $sort";
        if ($opsiLimit == 'limit'){
            $sql = $sql . " LIMIT ?,?";
            $p = array(intval($get['start']), intval($get['limit']));
        } elseif($opsiLimit == 'no_limit'){
            $sql = $sql;
            $p = array();
        }

        $query = $this->db->query($sql,$p);
        // echo $this->db->last_query();die();
        $sql_total= "SELECT FOUND_ROWS() AS total";
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

    function readMenusMwPullLog($get, $opsiLimit = 'limit'){
        $order = json_decode(@$get['sort'], true);
        $order_by = $order[0]['property']=='' ? 'a.date_exec DESC' : $order[0]['property'];
        $sort = $order[0]['direction']=='' ? '' : $order[0]['direction'];
        $where = "";
        
        if($get['pTextSearch'] != ""){
            $text = $get['pTextSearch'];
            $array=array_map(null, explode(',', $text));
            $array = implode("','",$array);
            $where = " AND a.eventuid IN ('$array')";
        }

        $sql = "SELECT 
                    a.mw_log_id, a.query, a.err_msg, a.table_reff, a.eventuid,  date_exec
                FROM
                    mw_pull_log2019 a
                WHERE 1=1
                    $where
                ORDER BY $order_by $sort";
        if ($opsiLimit == 'limit'){
            $sql = $sql . " LIMIT ?,200";
            $p = array(intval($get['start']));
        } elseif($opsiLimit == 'no_limit'){
            $sql = $sql;
            $p = array();
        }

        $query = $this->db->query($sql,$p);
        // echo $this->db->last_query();die();
        $sql_total= "SELECT FOUND_ROWS() AS total";
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
}
?>
