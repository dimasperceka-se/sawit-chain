<?php
class Mmenu_pull_engine_check extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function GetGridMainPullEngine($start = null, $limit = null, $opsiLimit = 'limit', $sortingField = '', $sortingDir = '') {
        if ($sortingField == "") $sortingField = 'uid';
        if ($sortingDir == "") $sortingDir = 'DESC';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.uid
                    ,a.timecheck_send
                    ,a.timecheck
                    ,a.remark
                    ,a.WorkStatus
                    ,a.LastSendEmail
                FROM
                    `mw_check_pullengine_status` a
                WHERE 1=1";
        if ($opsiLimit == 'limit'){
            $sql = $sql . " ORDER BY `$sortingField` $sortingDir
                            LIMIT ?,?";
            $p = array($start, $limit);
        } elseif($opsiLimit == 'no_limit'){
            $sql = $sql;
            $p = array();
        }
        $query = $this->db->query($sql, $p);

        $datas = $query->result_array();

        $result['data'] = $datas;

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        if ($sortingDir == 'ASC') {
            $sortingInfo = lang('ascending');
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = lang('descending');
        }

        return $result;
    }

    public function GetGridMainSysSetting($start = null, $limit = null, $opsiLimit = 'limit', $sortingField = '', $sortingDir = '') {
        if ($sortingField == "") $sortingField = 'SetID';
        if ($sortingDir == "") $sortingDir = 'DESC';

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    a.SetID
                    ,a.SetKey
                    ,a.SetName
                    ,a.SetValue
                FROM
                    `sys_setting` a
                WHERE 1=1
                AND a.SetKey = 'pullengine_check_email'";
        if ($opsiLimit == 'limit'){
            $sql = $sql . " ORDER BY `$sortingField` $sortingDir
                            LIMIT ?,?";
            $p = array($start, $limit);
        } elseif($opsiLimit == 'no_limit'){
            $sql = $sql;
            $p = array();
        }
        $query = $this->db->query($sql, $p);

        $datas = $query->result_array();

        $result['data'] = $datas;

        $query = $this->db->query('SELECT FOUND_ROWS() AS total');
        $result['total'] = $query->row()->total;

        if ($sortingDir == 'ASC') {
            $sortingInfo = lang('ascending');
        }
        if ($sortingDir == 'DESC') {
            $sortingInfo = lang('descending');
        }

        return $result;
    }

    public function UpdateValueSetting($paramPost)
    {
        $results = array();
        $this->db->trans_begin();

        $SetID    = $paramPost['SetID'];
        $SetValue = $paramPost['SetValue'];
        
        unset($paramPost["SetID"]);

        $this->db->where('SetID', $SetID);
        $query = $this->db->update('sys_setting', ["SetValue" => $SetValue]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = lang("Failed to save data");
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = lang("Data saved");
        }

        return $results;
    }
}