<?php

/**
 * Authentication Model for Mobile
 *
 * @author Noersa Eka Khustana
 */
class m_collecting extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data_collecting($SID,$PID)
    {
        $return = array('data' => array(), 'total' => 0);

        $sql = "SELECT
                    a.UserID 
                FROM
                    view_tc_supplychain_staff a
                WHERE
                    a.supplychainid = ? AND a.partnerid = ?";

        $getUser = $this->db->query($sql, array($SID,$PID));

        if ($getUser->num_rows() > 0) {
            $result = $getUser->result();
            foreach ($result as $key => $val) {
            
            $CreatedBy = $val->UserID;

            $this->db->select('a.CollectpointID,
                                a.CollectpointUID,
                                a.CollectpointDisplayID, 
                                a.OrgType,
                                a.CollectpointName,
                                a.OrgID,
                                a.PlantationNr,
                                a.CollectpointName,
                                a.VillageID,
                                a.CollectpointAddress,
                                a.Longitude,
                                a.Latitude,
                                a.StatusCode,
                                a.Remarks,
                                a.CreatedBy,
                                a.DateCreated,
                                a.DateUpdated,
                                a.LastModifiedBy,
                                a.DateSync,
                                a.uid,
                                c.SubDistrictID, 
                                d.DistrictID, 
                                e.ProvinceID',FALSE);
            $this->db->from('ktv_collecting_point a');
            $this->db->join('ktv_village b', 'a.VillageID=b.VillageID', 'left');
            $this->db->join('ktv_subdistrict c', 'c.SubDistrictID=b.SubDistrictID', 'left');
            $this->db->join('ktv_district d', 'd.DistrictID=c.DistrictID', 'left');
            $this->db->join('ktv_province e', 'd.ProvinceID=e.ProvinceID', 'left');
            $this->db->where('a.StatusCode', 'active');
            $this->db->where('a.CreatedBy', $CreatedBy);
            $this->db->group_by('a.CollectpointID');

            $Q = $this->db->get();

            if ($Q->num_rows() > 0) {
                $result = $Q->result();
                foreach ($result as $key => $val) {
                    // $val->Photo = is_null($val->Photo) ? "" : 'api/images/member/' . $val->ProvinceID . '/' . $val->Photo;
                    $val = $this->check_isNull($val);
                    $val->member = array();
                    $collecting_id = $val->CollectpointID;
                    
                    $sql = "SELECT
                                a.CollectpointID, 
                                a.MemberID
                            FROM
                                ktv_collecting_point_member a
                            WHERE
                                a.CollectpointID = ?";
    
                    $getMember = $this->db->query($sql, array($collecting_id));
                        
                    if ($getMember->num_rows()) {
                        $d_member = $getMember->result();
                        foreach ($d_member as $k => $v) {
                            $v = $this->check_isNull($v);
                        }
                        $val->member = $d_member;
                    } else {
                        $data = array();
                    }
                }
                $data['data'] = $result;
                $data['total'] = $Q->num_rows();
                return $data;
            } else {
                $data = array();
            }

            return $data;

            }
        }
    }

    private function check_isNull($v)
    {
        foreach ($v as $key => $value) {
            $v->{$key} = is_null($v->{$key}) ? "" : $v->{$key};
        }
        return $v;
    }

    public function submit($data)
    {
        $result = false;
        $insid = 0;
        $display_id = '';
        $error = '';
        $params = array();

        try {

            $this->db->trans_begin();
            $content = array(
                "CollectpointDisplayID" => $data["CollectpointDisplayID"],
                "OrgType" => $data["OrgType"],
                "OrgID" => $data["OrgID"],
                "PlantationNr" => (!isset($data["PlantationNr"]))?'':$data["PlantationNr"],
                "CollectpointName" => $data["CollectpointName"],
                "VillageID" => $data["VillageID"],
                "Latitude" => $data["Latitude"],
                "Longitude" => $data["Longitude"],
                "CollectpointAddress" => $data["CollectpointAddress"],
            );
            
            $collectingData = false;
            if ($data['CollectpointID'] != 0) {
                $collectingData = $this->checkExistingCollecting($data['CollectpointID']);
            }

            if ($collectingData) {
                if (strtotime($data['DateUpdated']) < strtotime($collectingData['DateUpdated'])) {
                    return array(
                        'success' => false,
                        'message' => 'Save data failed',
                        'error' => 'DateUpdated < DateUpdated WEB',
                    );
                }

                $content["CollectpointID"] = $data["CollectpointID"];
                $content['DateUpdated'] = $data['DateUpdated'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
                $this->db->where('CollectpointID', $data['CollectpointID']);
                $this->db->update('ktv_collecting_point', $content);
                $insid = $data['CollectpointID'];

                if ($data['member'] && count($data['member']) > 0) {
                    $this->deleteMemberCollecting($insid);
                    $this->insertMemberCollecting($data['member'], $insid);
                }
            } else {
                $content['DateCreated'] = $data['DateCreated'];
                $content['DateUpdated'] = $data['DateUpdated'];
                $content['CreatedBy'] = $data['CreatedBy'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
                $this->db->insert('ktv_collecting_point', $content);

                $insid = $this->db->insert_id();

                if ($data['member'] && count($data['member']) > 0) {
                    $this->insertMemberCollecting($data['member'], $insid);
                }

            }

            $result = $this->db->trans_complete();
        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        if ($result) {
            return array('success' => $result, 'CollectingID' => $insid);
        } else {
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }

    private function deleteMemberCollecting($insid)
    {
        $result = $this->db->delete('ktv_collecting_point_member', array('CollectpointID' => $insid));

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    private function insertMemberCollecting($data, $insid)
    {
        $result = false;

        if ($data && count($data) > 0) {
            foreach ($data as $key => $val) {
                $this->db->set('CollectpointID', $insid);
                $this->db->set('MemberID', $val['MemberID']);
                $this->db->set('CreatedBy', 1);
                $this->db->set('DateGenerated', date("Y-m-d H:i:s"));
                $result = $this->db->insert('ktv_collecting_point_member');
            }
        }

        if ($result) {
            return true;
        }
        return $result;
    }

    private function insertMemberPartner($member, $VillageID)
    {
        $result = false;
        if ($VillageID) {

            $partner_id = $this->getPartnerID($VillageID);

            if (count($partner_id) > 0) {
                foreach ($partner_id as $key => $val) {
                    $result = $this->insertPartnerMember($member['MemberID'], $val['PartnerID']);
                }
            }

            if ($result) {
                return true;
            }
        }
        return $result;
    }

    private function getPartnerID($village_id)
    {
        $this->db->select('a.DistrictID,a.PartnerID');
        $this->db->from('ktv_district_partner a');
        $this->db->join('ktv_district b', 'a.DistrictID = b.DistrictID', 'left');
        $this->db->join('ktv_subdistrict c', 'c.DistrictID = b.DistrictID', 'left');
        $this->db->join('ktv_village d', 'd.SubDistrictID = c.SubDistrictID', 'left');
        $this->db->where("d.VillageID = $village_id", null, false);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
    }

    private function InsertPartnerMember($member_id, $partner_id)
    {
        $this->db->set('apmPartnerID', $partner_id);
        $this->db->set('apmMemberID', $member_id);
        $this->db->set('CreatedBy', 1);
        $this->db->set('DateCreated', date("Y-m-d H:i:s"));
        $result = $this->db->replace('ktv_access_partner_member');

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    private function checkExistingCollecting($collecting_id)
    {
        $this->db->select('*', false);
        $this->db->from('ktv_collecting_point a');
        $this->db->where("CollectpointID = '$collecting_id'", null, false);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;

    }

    private function checkExistingPlot($member_id, $survey_nr, $plot_nr)
    {
        $this->db->select('*', false);
        $this->db->from('ktv_survey_plot a');
        $this->db->where("MemberID = '$member_id'", null, false);
        $this->db->where("SurveyNr = '$survey_nr'", null, false);
        $this->db->where("PlotNr = '$plot_nr'", null, false);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }
        return false;

    }

    public function submit_plot($data)
    {
        $result = false;
        $insid = 0;
        $display_id = '';
        $error = '';

        try {
            $this->db->trans_begin();
            $content = array(
                "MemberID" => $data["MemberID"],
                "SurveyNr" => $data['SurveyNr'],
                "PlotNr" => $data['PlotNr'],
                "Latitude" => $data['Latitude'],
                "Longitude" => $data['Longitude'],
                "StatusCode" => 'active',
            );

            $plotData = $this->checkExistingPlot($content['MemberID'], $content['SurveyNr'], $content['PlotNr']);

            if ($plotData) {
                if (strtotime($data['DateUpdated']) < strtotime($plotData['DateUpdated'])) {
                    return array(
                        'success' => false,
                        'message' => 'Save data failed',
                        'error' => 'DateUpdated < DateUpdated WEB',
                    );
                }

                $content['DateUpdated'] = $data['DateUpdated'];
                $content['LastModifiedBy'] = $data['LastModifiedBy'];
                $this->db->update('ktv_survey_plot', $content);
                $insid = $data['MemberID'];
            } else {
                $farmerData = $this->checkExistingFarmer($content['MemberID']);
                if ($farmerData) {
                    $content['DateCollection'] = $data['DateCreated'];
                    $content['DateCreated'] = $data['DateCreated'];
                    $content['DateUpdated'] = $data['DateUpdated'];
                    $content['CreatedBy'] = $data['CreatedBy'];
                    $content['LastModifiedBy'] = $data['LastModifiedBy'];
                    $this->db->insert('ktv_survey_plot', $content);
                }

            }

        } catch (Exception $exc) {
            $this->db->trans_rollback();
            $result = false;
        }

        $result = $this->db->trans_complete();

        if ($result) {
            return array('success' => $result);
        } else {
            return array('success' => $result, 'message' => 'Save data failed', 'error' => $error);
        }
    }
}
