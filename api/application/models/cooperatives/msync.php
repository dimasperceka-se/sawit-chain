<?php

class Msync extends CI_Model {
    
    
    function memberType($CoopID) {
        $data = array();
        $txt = null;
        $q = $this->db->get_where('coop_member_type', array('SyncedDate' => null, 'coopID' => $CoopID));
        // echo $this->db->last_query();

        if ($q->num_rows() > 0) {
            $i = 0;
            foreach ($q->result() as $r) {
                // unset($data);

                $data[$i]['typeID'] = $r->typeID;
                $data[$i]['coopID'] = $r->coopID;
                $data[$i]['typeCode'] = $r->typeCode;
                $data[$i]['typeName'] = $r->typeName;
                $data[$i]['typeMaxProfit'] = $r->typeMaxProfit;
                $data[$i]['typeSimPokokAmount'] = $r->typeSimPokokAmount;
                $data[$i]['typeSimWajibAmount'] = $r->typeSimWajibAmount;
                $data[$i]['typeSimWajibPeriod'] = $r->typeSimWajibPeriod;
                $data[$i]['typeSimPokokPeriod'] = $r->typeSimPokokPeriod;
                $data[$i]['RegistrationFee'] = $r->RegistrationFee;
                $data[$i]['CoaRegMemberTypeID'] = $r->CoaRegMemberTypeID;
                $data[$i]['CreatedBy'] = $r->CreatedBy;
                $data[$i]['CreatedDate'] = $r->CreatedDate;
                $data[$i]['UpdatedDate'] = $r->UpdatedDate;
                $i++;
            } //end foreach
        } else {
            return 0;
        }
        // $d = http_build_query($data) . "\n";
        // $d = http_build_query($data, '', '&amp;');
        return json_encode($data);
    }

    function insertMemberType($json) {
        $ret = array();
        $i = 0;
        foreach ($json as $key => $r) {

            $data['typeID'] = $r->typeID;
            $data['coopID'] = $r->coopID;
            $data['typeCode'] = $r->typeCode;
            $data['typeName'] = $r->typeName;
            $data['typeMaxProfit'] = $r->typeMaxProfit;
            $data['typeSimPokokAmount'] = $r->typeSimPokokAmount;
            $data['typeSimWajibAmount'] = $r->typeSimWajibAmount;
            $data['typeSimWajibPeriod'] = $r->typeSimWajibPeriod;
            $data['typeSimPokokPeriod'] = $r->typeSimPokokPeriod;
            $data['RegistrationFee'] = $r->RegistrationFee;
            $data['CoaRegMemberTypeID'] = $r->CoaRegMemberTypeID;
            $data['CreatedBy'] = $r->CreatedBy;
            $data['CreatedDate'] = $r->CreatedDate;
            $data['UpdatedDate'] = $r->UpdatedDate;

            $wer = array('typeID' => $r->typeID, 'coopID' => $r->coopID);
            $q = $this->db->get_where('coop_member_type', $wer);
            if ($q->num_rows() > 0) {
                $this->db->where($wer);
                $this->db->update('coop_member_type', $data);
            } else {
                $this->db->insert('coop_member_type', $data);
            }

            $ret[$i]['typeID'] = $r->typeID;
            $ret[$i]['coopID'] = $r->coopID;
            $ret[$i]['SyncedDate'] = date('Y-m-d');

            $i++;
        }
        return json_encode($ret);
    }

    function insertFeedbackMemberType($json) {
        $this->db->trans_begin();

        foreach ($json as $key => $r) {

            $ret['typeID'] = $r->typeID;
            $ret['coopID'] = $r->coopID;
            $this->db->where($ret);
            $this->db->update('coop_member_type', array('SyncedDate' => $r->SyncedDate));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function savingType($CoopID) {
        $data = array();
        $q = $this->db->get_where('coop_saving_type', array('SyncedDate' => null, 'coopID' => $CoopID));

        if ($q->num_rows() > 0) {
            $i = 0;
            foreach ($q->result() as $r) {

                $data[$i]['savingTypeID'] = $r->savingTypeID;
                $data[$i]['savingTypeCode'] = $r->savingTypeCode;
                $data[$i]['savingTypeDefault'] = $r->savingTypeDefault;
                $data[$i]['coopID'] = $r->coopID;
                $data[$i]['CoaID'] = $r->CoaID;
                $data[$i]['savingTypeSHU'] = $r->savingTypeSHU;
                $data[$i]['savingTypeName'] = $r->savingTypeName;
                $data[$i]['savingTypeMinAmount'] = $r->savingTypeMinAmount;
                $data[$i]['savingTypeMinTrans'] = $r->savingTypeMinTrans;
                $data[$i]['savingTypeInterestRate'] = $r->savingTypeInterestRate;
                $data[$i]['savingTypeInterestCalc'] = $r->savingTypeInterestCalc;
                $data[$i]['savingTypeActiveDate'] = $r->savingTypeActiveDate;
                $data[$i]['savingTypeMonthlyFee'] = $r->savingTypeMonthlyFee;
                $data[$i]['savingTypeInterestPayment'] = $r->savingTypeInterestPayment;
                $data[$i]['savingTypeSHUProfit'] = $r->savingTypeSHUProfit;
                $data[$i]['savingTypeStatus'] = $r->savingTypeStatus;
                $data[$i]['savingRemark'] = $r->savingRemark;
                $data[$i]['CreatedBy'] = $r->CreatedBy;
                $data[$i]['CreatedDate'] = $r->CreatedDate;
                $data[$i]['UpdatedBy'] = $r->UpdatedBy;
                $data[$i]['UpdatedDate'] = $r->UpdatedDate;

                $i++;
            } //end foreach
        }

        return json_encode($data);
    }

    function insertSavingType($json) {
        $ret = array();
        $i = 0;
        foreach ($json as $key => $r) {

            $data['savingTypeID'] = $r->savingTypeID;
            $data['savingTypeCode'] = $r->savingTypeCode;
            $data['savingTypeDefault'] = $r->savingTypeDefault;
            $data['coopID'] = $r->coopID;
            $data['CoaID'] = $r->CoaID;
            $data['savingTypeSHU'] = $r->savingTypeSHU;
            $data['savingTypeName'] = $r->savingTypeName;
            $data['savingTypeMinAmount'] = $r->savingTypeMinAmount;
            $data['savingTypeMinTrans'] = $r->savingTypeMinTrans;
            $data['savingTypeInterestRate'] = $r->savingTypeInterestRate;
            $data['savingTypeInterestCalc'] = $r->savingTypeInterestCalc;
            $data['savingTypeActiveDate'] = $r->savingTypeActiveDate;
            $data['savingTypeMonthlyFee'] = $r->savingTypeMonthlyFee;
            $data['savingTypeInterestPayment'] = $r->savingTypeInterestPayment;
            $data['savingTypeSHUProfit'] = $r->savingTypeSHUProfit;
            $data['savingTypeStatus'] = $r->savingTypeStatus;
            $data['savingRemark'] = $r->savingRemark;
            $data['CreatedBy'] = $r->CreatedBy;
            $data['CreatedDate'] = $r->CreatedDate;
            $data['UpdatedBy'] = $r->UpdatedBy;
            $data['UpdatedDate'] = $r->UpdatedDate;

            $wer = array('savingTypeID' => $r->savingTypeID, 'coopID' => $r->coopID);
            $q = $this->db->get_where('coop_saving_type', $wer);
            if ($q->num_rows() > 0) {
                $this->db->where($wer);
                $this->db->update('coop_saving_type', $data);
            } else {
                $this->db->insert('coop_saving_type', $data);
            }

            $ret[$i]['savingTypeID'] = $r->savingTypeID;
            $ret[$i]['coopID'] = $r->coopID;
            $ret[$i]['SyncedDate'] = date('Y-m-d');

            $i++;
        }
        return json_encode($ret);
    }

    function insertFeedbackSavingType($json) {
        $this->db->trans_begin();

        foreach ($json as $key => $r) {

            $ret['savingTypeID'] = $r->savingTypeID;
            $ret['coopID'] = $r->coopID;
            $this->db->where($ret);
            $this->db->update('coop_saving_type', array('SyncedDate' => $r->SyncedDate));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function member($CoopID) {
        $data = array();
        $q = $this->db->get_where('coop_member', array('SyncedDate' => null, 'CoopID' => $CoopID));

        if ($q->num_rows() > 0) {
            $i = 0;
            foreach ($q->result() as $r) {

                $data[$i] = array(
                    'memberID' => $r->memberID,
                    'CoopID' => $r->CoopID,
                    'memberRefID' => $r->memberRefID,
                    'farmerID' => $r->farmerID,
                    'primaryNo' => $r->primaryNo,
                    'registeredDate' => $r->registeredDate,
                    'typeID' => $r->typeID,
                    'name' => $r->name,
                    'identityType' => $r->identityType,
                    'identityNumber' => $r->identityNumber,
                    'gender' => $r->gender,
                    'placeOfBirth' => $r->placeOfBirth,
                    'dateOfBirth' => $r->dateOfBirth,
                    'address' => $r->address,
                    'villageID' => $r->villageID,
                    'phone' => $r->phone,
                    'maritalStatus' => $r->maritalStatus,
                    'education' => $r->education,
                    'job' => $r->job,
                    'status' => $r->status,
                    'remark' => $r->remark,
                    'signature' => $r->signature,
                    'ResignationDate' => $r->ResignationDate,
                    'ResignationReason' => $r->ResignationReason,
                    'familyName' => $r->familyName,
                    'familyRelation' => $r->familyRelation,
                    'familyIdentityType' => $r->familyIdentityType,
                    'familyIdentityNumber' => $r->familyIdentityNumber,
                    'familyAddress' => $r->familyAddress,
                    'familyPhone' => $r->familyPhone,
                    'savingPokok' => $r->savingPokok,
                    'savingWajib' => $r->savingWajib,
                    'uangPangkal' => $r->UangPangkal,
                    'CreatedBy' => $r->CreatedBy,
                    'CreatedDate' => $r->CreatedDate,
                    'UpdatedBy' => $r->UpdatedBy,
                    'UpdatedDate' => $r->UpdatedDate
                );

                $i++;
            }
        }

        return json_encode($data);
    }

    function insertMember($json) {
        $ret = array();
        $i = 0;
        foreach ($json as $key => $r) {

            $data = array(
                'memberID' => $r->memberID,
                'CoopID' => $r->CoopID,
                'memberRefID' => $r->memberRefID,
                'farmerID' => $r->farmerID,
                'primaryNo' => $r->primaryNo,
                'registeredDate' => $r->registeredDate,
                'typeID' => $r->typeID,
                'name' => $r->name,
                'identityType' => $r->identityType,
                'identityNumber' => $r->identityNumber,
                'gender' => $r->gender,
                'placeOfBirth' => $r->placeOfBirth,
                'dateOfBirth' => $r->dateOfBirth,
                'address' => $r->address,
                'villageID' => $r->villageID,
                'phone' => $r->phone,
                'maritalStatus' => $r->maritalStatus,
                'education' => $r->education,
                'job' => $r->job,
                'status' => $r->status,
                'remark' => $r->remark,
                'signature' => $r->signature,
                'ResignationDate' => $r->ResignationDate,
                'ResignationReason' => $r->ResignationReason,
                'familyName' => $r->familyName,
                'familyRelation' => $r->familyRelation,
                'familyIdentityType' => $r->familyIdentityType,
                'familyIdentityNumber' => $r->familyIdentityNumber,
                'familyAddress' => $r->familyAddress,
                'familyPhone' => $r->familyPhone,
                'savingPokok' => $r->savingPokok,
                'savingWajib' => $r->savingWajib,
                'uangPangkal' => $r->uangPangkal,
                'CreatedBy' => $r->CreatedBy,
                'CreatedDate' => $r->CreatedDate,
                'UpdatedBy' => $r->UpdatedBy,
                'UpdatedDate' => $r->UpdatedDate
            );

            $wer = array('memberID' => $r->memberID, 'CoopID' => $r->CoopID);
            $q = $this->db->get_where('coop_member', $wer);
            if ($q->num_rows() > 0) {
                $this->db->where($wer);
                $this->db->update('coop_member', $data);
            } else {
                $this->db->insert('coop_member', $data);
            }

            $ret[$i]['memberID'] = $r->memberID;
            $ret[$i]['CoopID'] = $r->CoopID;
            $ret[$i]['SyncedDate'] = date('Y-m-d');

            $i++;
        }
        return json_encode($ret);
    }

    function insertFeedbackMember($json) {
        $this->db->trans_begin();

        foreach ($json as $key => $r) {

            $ret['memberID'] = $r->memberID;
            $ret['CoopID'] = $r->CoopID;
            $this->db->where($ret);
            $this->db->update('coop_member', array('SyncedDate' => $r->SyncedDate));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function journal($CoopID) {
        $data = array();
        $q = $this->db->get_where('accounting_journal', array('CoopID' => $CoopID, 'SyncedDate' => null));
        if ($q->num_rows() > 0) {
            $x = 0;
            foreach ($q->result() as $r) {
                $data[$x]['JournalID'] = $r->JournalID;
                $data[$x]['CoopID'] = $r->CoopID;
                $data[$x]['JournalTypeCode'] = $r->JournalTypeCode;
                $data[$x]['JournalDate'] = $r->JournalDate;
                $data[$x]['JournalMemo'] = $r->JournalMemo;
                $data[$x]['JournalIsPosted'] = $r->JournalIsPosted;
                $data[$x]['JournalPostedDate'] = $r->JournalPostedDate;
                $data[$x]['JournalCRBY'] = $r->JournalCRBY;
                $data[$x]['JournalCRDT'] = $r->JournalCRDT;
                $data[$x]['JournalUPBY'] = $r->JournalUPBY;
                $data[$x]['JournalUPDT'] = $r->JournalUPDT;

                $qDetail = $this->db->get_where('accounting_journal_detail', array('JournalID' => $r->JournalID, 'CoopID' => $r->CoopID));
                if ($qDetail->num_rows() > 0) {
                    $i = 0;
                    foreach ($qDetail->result() as $rD) {
                        $data[$x]['Detail'][$i]['JournalDetailID'] = $r->JournalDetailID;
                        $data[$x]['Detail'][$i]['JournalID'] = $r->JournalID;
                        $data[$x]['Detail'][$i]['CoopID'] = $r->CoopID;
                        $data[$x]['Detail'][$i]['CoaCode'] = isset($r->CoaCode) ? $r->CoaCode : null;
                        $data[$x]['Detail'][$i]['JournalDetailDesc'] = isset($r->JournalDetailDesc) ? $r->JournalDetailDesc : null;
                        $data[$x]['Detail'][$i]['CurrencyID'] = isset($r->CurrencyID) ? $r->CurrencyID : null;
                        $data[$x]['Detail'][$i]['JournalDetailOrig'] = isset($r->JournalDetailOrig) ? $r->JournalDetailOrig : null;
                        $data[$x]['Detail'][$i]['JournalDetailExRate'] = isset($r->JournalDetailExRate) ? $r->JournalDetailExRate : null;
                        $data[$x]['Detail'][$i]['JournalDetailSum'] = isset($r->JournalDetailSum) ? $r->JournalDetailSum : null;
                        $data[$x]['Detail'][$i]['JournalDetailType'] = isset($r->JournalDetailType) ? $r->JournalDetailType : null;
                        $data[$x]['Detail'][$i]['JournalDetailCRBY'] = isset($r->JournalDetailCRBY) ? $r->JournalDetailCRBY : null;
                        $data[$x]['Detail'][$i]['JournalDetailCRDT'] = isset($r->JournalDetailCRDT) ? $r->JournalDetailCRDT : null;
                        $data[$x]['Detail'][$i]['JournalDetailUPBY'] = isset($r->JournalDetailUPBY) ? $r->JournalDetailUPBY : null;
                        $data[$x]['Detail'][$i]['JournalDetailUPDT'] = $isset($r->JournalDetailUPDT) ? $r->JournalDetailUPDT : null;
                        $i++;
                    }
                }

                $x++;
            }
        }

        return json_encode($data);
    }

    function insertJournal($json) {
        $ret = array();
        $i = 0;
        foreach ($json as $key => $r) {

            $data['JournalID'] = $r->JournalID;
            $data['CoopID'] = $r->CoopID;
            $data['JournalTypeCode'] = $r->JournalTypeCode;
            $data['JournalDate'] = $r->JournalDate;
            $data['JournalMemo'] = $r->JournalMemo;
            $data['JournalIsPosted'] = $r->JournalIsPosted;
            $data['JournalPostedDate'] = $r->JournalPostedDate;
            $data['JournalCRBY'] = $r->JournalCRBY;
            $data['JournalCRDT'] = $r->JournalCRDT;
            $data['JournalUPBY'] = $r->JournalUPBY;
            $data['JournalUPDT'] = $r->JournalUPDT;


            $wer = array('JournalID' => $r->JournalID, 'CoopID' => $r->CoopID);
            $q = $this->db->get_where('accounting_journal', $wer);
            if ($q->num_rows() > 0) {
                $this->db->where($wer);
                $this->db->update('accounting_journal', $data);
            } else {
                $this->db->insert('accounting_journal', $data);
            }

            if (count($data['Detail']) > 0) {
                foreach ($data['Detail'] as $k => $rr) {
                    $detail['JournalDetailID'] = $rr->JournalDetailID;
                    $detail['JournalID'] = $rr->JournalID;
                    $detail['CoopID'] = $rr->CoopID;
                    $detail['CoaCode'] = isset($rr->CoaCode) ? $rr->CoaCode : null;
                    $detail['JournalDetailDesc'] = isset($rr->JournalDetailDesc) ? $rr->JournalDetailDesc : null;
                    $detail['CurrencyID'] = isset($rr->CurrencyID) ? $rr->CurrencyID : null;
                    $detail['JournalDetailOrig'] = isset($rr->JournalDetailOrig) ? $rr->JournalDetailOrig : null;
                    $detail['JournalDetailExRate'] = isset($rr->JournalDetailExRate) ? $rr->JournalDetailExRate : null;
                    $detail['JournalDetailSum'] = isset($rr->JournalDetailSum) ? $rr->JournalDetailSum : null;
                    $detail['JournalDetailType'] = isset($rr->JournalDetailType) ? $rr->JournalDetailType : null;
                    $detail['JournalDetailCRBY'] = isset($rr->JournalDetailCRBY) ? $rr->JournalDetailCRBY : null;
                    $detail['JournalDetailCRDT'] = isset($rr->JournalDetailCRDT) ? $rr->JournalDetailCRDT : null;
                    $detail['JournalDetailUPBY'] = isset($rr->JournalDetailUPBY) ? $rr->JournalDetailUPBY : null;
                    $detail['JournalDetailUPDT'] = $isset($rr->JournalDetailUPDT) ? $rr->JournalDetailUPDT : null;

                    $wer = array('JournalDetailID' => $r->JournalDetailID, 'CoopID' => $r->CoopID);
                    $q = $this->db->get_where('accounting_journal_detail', $wer);
                    if ($q->num_rows() > 0) {
                        $this->db->where($wer);
                        $this->db->update('accounting_journal_detail', $detail);
                    } else {
                        $this->db->insert('accounting_journal_detail', $detail);
                    }
                }
            }



            $ret[$i]['JournalID'] = $r->JournalID;
            $ret[$i]['CoopID'] = $r->CoopID;
            $ret[$i]['SyncedDate'] = date('Y-m-d');

            $i++;
        }
        return json_encode($ret);
    }

    function insertFeedbackJournal($json) {
        $this->db->trans_begin();

        foreach ($json as $key => $r) {

            $ret['JournalID'] = $r->JournalID;
            $ret['CoopID'] = $r->CoopID;
            $this->db->where($ret);
            $this->db->update('accounting_journal', array('SyncedDate' => $r->SyncedDate));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function supplier($CoopID) {
        $data = array();

        $q = $this->db->get_where('ktv_supplier', array('SyncedDate' => null, 'CoopID' => $CoopID));

        if ($q->num_rows() > 0) {
            $i = 0;
            foreach ($q->result() as $r) {

                $data[$i]['SupplierID'] = $r->SupplierID;
                $data[$i]['CoopID'] = $r->CoopID;
                $data[$i]['OrgType'] = $r->OrgType;
                $data[$i]['OrgID'] = $r->OrgID;
                $data[$i]['Name'] = $r->Name;
                $data[$i]['Address'] = $r->Address;
                $data[$i]['Phone'] = $r->Phone;
                $data[$i]['Email'] = $r->Email;
                $data[$i]['VillageID'] = $r->VillageID;
                $data[$i]['Note'] = $r->Note;

                $i++;
            } //end foreach
        }

        return json_encode($data);
    }

    function insertSupplier($json) {
        $ret = array();
        $i = 0;
        foreach ($json as $key => $r) {
            $data['SupplierID'] = $r->SupplierID;
            $data['CoopID'] = $r->CoopID;
            $data['OrgType'] = $r->OrgType;
            $data['OrgID'] = $r->OrgID;
            $data['Name'] = $r->Name;
            $data['Address'] = $r->Address;
            $data['Phone'] = $r->Phone;
            $data['Email'] = $r->Email;
            $data['VillageID'] = $r->VillageID;
            $data['Note'] = $r->Note;

            $wer = array('SupplierID' => $r->SupplierID, 'CoopID' => $r->CoopID);
            $q = $this->db->get_where('ktv_supplier', $wer);
            if ($q->num_rows() > 0) {
                $this->db->where($wer);
                $this->db->update('ktv_supplier', $data);
            } else {
                $this->db->insert('ktv_supplier', $data);
            }

            $ret[$i]['SupplierID'] = $r->SupplierID;
            $ret[$i]['CoopID'] = $r->CoopID;
            $ret[$i]['SyncedDate'] = date('Y-m-d');

            $i++;
        }
        return json_encode($ret);
    }

    function insertFeedbackSupplier($json) {
        $this->db->trans_begin();

        foreach ($json as $key => $r) {

            $ret['SupplierID'] = $r->SupplierID;
            $ret['CoopID'] = $r->CoopID;
            $this->db->where($ret);
            $this->db->update('ktv_supplier', array('SyncedDate' => $r->SyncedDate));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function inventory($CoopID) {
        $DataInventory = array();
        $q = $this->db->get_where('ktv_inventory', array('SyncedDate' => null, 'CoopID' => $CoopID));
        if ($q->num_rows() > 0) {
            $x = 0;
            foreach ($q->result() as $r) {

                $DataInventory[$x] = array(
                    'InventoryID' => $r->InventoryID,
                    'OrgType' => $r->OrgType,
                    'Status' => $r->Status,
                    'OrgID' => $r->OrgID,
                    'CoopID' => $r->CoopID,
                    'JournalID' => $r->JournalID,
                    'Number' => $r->Number,
                    'SerialNumber' => $r->SerialNumber,
                    'Name' => $r->Name,
                    'Description' => $r->Description,
                    'UnitMeasurementID' => $r->UnitMeasurementID,
                    'IsInventory' => $r->IsInventory,
                    'IsSell' => $r->IsSell,
                    'IsBuy' => $r->IsBuy,
                    'IsRemoved' => $r->IsRemoved,
                    'RemoveReason' => $r->RemoveReason,
                    'coaIDAsset' => $r->coaIDAsset,
                    'coaIDAkumDepres' => $r->coaIDAkumDepres,
                    'coaIDBebanDepres' => $r->coaIDBebanDepres,
                    'Stock' => $r->Stock,
                    'Images' => $r->Images,
                    'Cost' => $r->Cost,
                    'UnitMeasure' => $r->UnitMeasure,
                    'MinStock' => $r->MinStock,
                    'SupplierID' => $r->SupplierID,
                    'SupplierName' => $r->SupplierName,
                    'SellingPrice' => $r->SellingPrice,
                    'SelingTax' => $r->SelingTax,
                    'Notes' => $r->Notes,
                    'YearBuy' => $r->YearBuy,
                    'MonthBuy' => $r->MonthBuy,
                    'DateBuy' => $r->DateBuy,
                    'CategoryID' => $r->CategoryID,
                    'BuyTax' => $r->BuyTax,
                    'Location' => $r->Location,
                    'Residu' => $r->Residu,
                    'Umur' => $r->Umur,
                    'AkumulasiBeban' => $r->AkumulasiBeban,
                    'BebanBerjalan' => $r->BebanBerjalan,
                    'NilaiBuku' => $r->NilaiBuku,
                    'BebanPerBulan' => $r->BebanPerBulan,
                    'AkumulasiAkhir' => $r->AkumulasiAkhir,
                    'IsPaket' => $r->IsPaket,
                    'ParentInventoryID' => $r->ParentInventoryID,
                    'ParentConvertion' => $r->ParentConvertion,
                    'EvaluateType' => $r->EvaluateType,
                    'EvaluateReason' => $r->EvaluateReason,
                    'EvaluateSoldPrice' => $r->EvaluateSoldPrice,
                    'CreatedBy' => $r->CreatedBy,
                    'CreatedDate' => $r->CreatedDate,
                    'UpdatedBy' => $r->UpdatedBy,
                    'UpdatedDate' => $r->UpdatedDate
                );


                $qDetail = $this->db->get_where('ktv_inventory_stok', array('CoopID' => $CoopID, 'InventoryID' => $r->InventoryID));

                $i = 0;
                foreach ($qDetail->result() as $rDetail) {
                    $DataInventory[$x]['stok'][$i]['StokID'] = $rDetail->StokID;
                    $DataInventory[$x]['stok'][$i]['InventoryID'] = $rDetail->InventoryID;
                    $DataInventory[$x]['stok'][$i]['CoopID'] = $rDetail->CoopID;
                    $DataInventory[$x]['stok'][$i]['Type'] = $rDetail->Type;
                    $DataInventory[$x]['stok'][$i]['ID'] = $rDetail->ID;
                    $DataInventory[$x]['stok'][$i]['Awal'] = $rDetail->Awal;
                    $DataInventory[$x]['stok'][$i]['Jumlah'] = $rDetail->Jumlah;
                    $DataInventory[$x]['stok'][$i]['Akhir'] = $rDetail->Akhir;
                    $DataInventory[$x]['stok'][$i]['CreatedBy'] = $rDetail->CreatedBy;
                    $i++;
                }
                $x++;
            }
        }


        return json_encode($DataInventory);
    }

    function insertInventory($json) {
        $ret = array();
        $i = 0;
        foreach ($json as $key => $r) {
            $DataInventory = array(
                'InventoryID' => $r->InventoryID,
                'OrgType' => $r->OrgType,
                'Status' => $r->Status,
                'OrgID' => $r->OrgID,
                'CoopID' => $r->CoopID,
                'JournalID' => $r->JournalID,
                'Number' => $r->Number,
                'SerialNumber' => $r->SerialNumber,
                'Name' => $r->Name,
                'Description' => $r->Description,
                'UnitMeasurementID' => $r->UnitMeasurementID,
                'IsInventory' => $r->IsInventory,
                'IsSell' => $r->IsSell,
                'IsBuy' => $r->IsBuy,
                'IsRemoved' => $r->IsRemoved,
                'RemoveReason' => $r->RemoveReason,
                'coaIDAsset' => $r->coaIDAsset,
                'coaIDAkumDepres' => $r->coaIDAkumDepres,
                'coaIDBebanDepres' => $r->coaIDBebanDepres,
                'Stock' => $r->Stock,
                'Images' => $r->Images,
                'Cost' => $r->Cost,
                'UnitMeasure' => $r->UnitMeasure,
                'MinStock' => $r->MinStock,
                'SupplierID' => $r->SupplierID,
                'SupplierName' => $r->SupplierName,
                'SellingPrice' => $r->SellingPrice,
                'SelingTax' => $r->SelingTax,
                'Notes' => $r->Notes,
                'YearBuy' => $r->YearBuy,
                'MonthBuy' => $r->MonthBuy,
                'DateBuy' => $r->DateBuy,
                'CategoryID' => $r->CategoryID,
                'BuyTax' => $r->BuyTax,
                'Location' => $r->Location,
                'Residu' => $r->Residu,
                'Umur' => $r->Umur,
                'AkumulasiBeban' => $r->AkumulasiBeban,
                'BebanBerjalan' => $r->BebanBerjalan,
                'NilaiBuku' => $r->NilaiBuku,
                'BebanPerBulan' => $r->BebanPerBulan,
                'AkumulasiAkhir' => $r->AkumulasiAkhir,
                'IsPaket' => $r->IsPaket,
                'ParentInventoryID' => $r->ParentInventoryID,
                'ParentConvertion' => $r->ParentConvertion,
                'EvaluateType' => $r->EvaluateType,
                'EvaluateReason' => $r->EvaluateReason,
                'EvaluateSoldPrice' => $r->EvaluateSoldPrice,
                'CreatedBy' => $r->CreatedBy,
                'CreatedDate' => $r->CreatedDate,
                'UpdatedBy' => $r->UpdatedBy,
                'UpdatedDate' => $r->UpdatedDate
            );

            $wer = array('InventoryID' => $r->InventoryID, 'CoopID' => $r->CoopID);
            $q = $this->db->get_where('ktv_inventory', $wer);
            if ($q->num_rows() > 0) {
                $this->db->where($wer);
                $this->db->update('ktv_inventory', $data);
            } else {
                $this->db->insert('ktv_inventory', $data);
            }

            if (count($stok) > 0) {
                foreach ($stok as $kk => $rDetail) {
                    $detailStok['StokID'] = $rDetail->StokID;
                    $detailStok['InventoryID'] = $rDetail->InventoryID;
                    $detailStok['CoopID'] = $rDetail->CoopID;
                    $detailStok['Type'] = $rDetail->Type;
                    $detailStok['ID'] = $rDetail->ID;
                    $detailStok['Awal'] = $rDetail->Awal;
                    $detailStok['Jumlah'] = $rDetail->Jumlah;
                    $detailStok['Akhir'] = $rDetail->Akhir;
                    $detailStok['CreatedBy'] = $rDetail->CreatedBy;

                    $wer = array('StokID' => $rDetail->StokID, 'InventoryID' => $r->InventoryID);
                    $q = $this->db->get_where('ktv_inventory_stok', $wer);
                    if ($q->num_rows() > 0) {
                        $this->db->where($wer);
                        $this->db->update('ktv_inventory_stok', $data);
                    } else {
                        $this->db->insert('ktv_inventory_stok', $data);
                    }
                }
            }


            $ret[$i]['InventoryID'] = $r->InventoryID;
            $ret[$i]['CoopID'] = $r->CoopID;
            $ret[$i]['SyncedDate'] = date('Y-m-d');

            $i++;
        }
        return json_encode($ret);
    }

    function insertFeedbackInventory($json) {
        $this->db->trans_begin();

        foreach ($json as $key => $r) {

            $ret['InventoryID'] = $r->InventoryID;
            $ret['CoopID'] = $r->CoopID;
            $this->db->where($ret);
            $this->db->update('ktv_inventory', array('SyncedDate' => $r->SyncedDate));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function purchase($CoopID) {
        $data = array();
        $q = $this->db->get_where('ktv_purchase', array('SyncedDate' => null, 'CoopID' => $CoopID));

        if ($q->num_rows() > 0) {
            $detail = null;
            $x = 0;
            foreach ($q->result() as $r) {

                $data[$x]['PurchaseID'] = $r->PurchaseID;
                $data[$x]['CoopID'] = $r->CoopID;
                $data[$x]['OrgType'] = $r->OrgType;
                $data[$x]['OrgID'] = $r->OrgID;
                $data[$x]['JournalID'] = $r->JournalID;
                $data[$x]['Number'] = $r->Number;
                $data[$x]['SupplierID'] = $r->SupplierID;
                $data[$x]['DueDate'] = $r->DueDate;
                $data[$x]['Date'] = $r->Date;
                $data[$x]['Diskon'] = $r->Diskon;
                $data[$x]['Pajak'] = $r->Pajak;
                $data[$x]['Total'] = $r->Total;
                $data[$x]['Pembayaran'] = $r->Pembayaran;
                $data[$x]['SisaBayar'] = $r->SisaBayar;
                $data[$x]['TipeBayar'] = $r->TipeBayar;
                $data[$x]['DateCreated'] = $r->DateCreated;
                $data[$x]['CreatedBy'] = $r->CreatedBy;
                $data[$x]['DateUpdated'] = $r->DateUpdated;
                $data[$x]['LastModifiedBy'] = $r->LastModifiedBy;

                //DETAIL
                // unset($detail);
                // $detail = array();
                $qDetail = $this->db->get_where('ktv_purchase_detail', array('CoopID' => $r->CoopID, 'PurchaseId' => $r->PurchaseID));
                if ($qDetail->num_rows() > 0) {
                    $i = 0;
                    foreach ($qDetail->result() as $rD) {
                        $data[$x]['Detail'][$i]['DetailId'] = $r->DetailId;
                        $data[$x]['Detail'][$i]['CoopID'] = $r->CoopID;
                        $data[$x]['Detail'][$i]['PurchaseId'] = $r->PurchaseId;
                        $data[$x]['Detail'][$i]['InventoryID'] = $r->InventoryID;
                        $data[$x]['Detail'][$i]['Qty'] = $r->Qty;
                        $data[$x]['Detail'][$i]['Price'] = $r->Price;
                        $i++;
                    }
                }

                $x++;
            }
        }

        return json_encode($data);
    }

    function insertPurchase($json) {
        
    }

    function sale($CoopID) {
        $dataSale = array();

        $q = $this->db->get_where('ktv_sale', array('SyncedDate' => NULL, 'CoopID' => $CoopID));

        if ($q->num_rows() > 0) {
            $detail = null;
            $x = 0;
            foreach ($q->result() as $rSale) {

                $dataSale[$x]['data'] = array(
                    'SaleId' => $rSale->SaleId,
                    'CoopID' => $rSale->CoopID,
                    'OrgType' => $rSale->OrgType,
                    'OrgID' => $rSale->OrgID,
                    'JournalID' => $rSale->JournalID,
                    'Number' => $rSale->Number,
                    'CustomerID' => $rSale->CustomerID,
                    // 'DueDate'=>$rSale->DueDate,
                    'Date' => $rSale->Date,
                    'Diskon' => $rSale->Diskon,
                    'Pajak' => $rSale->Pajak,
                    'Total' => $rSale->Total,
                    'Pembayaran' => $rSale->Pembayaran,
                    'SisaBayar' => $rSale->SisaBayar,
                    // 'TipeBayar'=>$rSale->TipeBayar,
                    'DateCreated' => $rSale->DateCreated,
                    'CreatedBy' => $rSale->CreatedBy,
                    'DateUpdated' => $rSale->DateUpdated,
                    'LastModifiedBy' => $rSale->LastModifiedBy
                );

                //DETAIL
                $dataSale[$x]['Detail'] = null;

                // unset($detail);
                // $detail = array();
                $i = 0;
                $qDetail = $this->db->get_where('ktv_sale_detail', array('CoopID' => $CoopID, 'SaleId' => $rSale->SaleId));
                foreach ($qDetail->result() as $rDetail) {
                    $dataSale[$x]['Detail'][$i]['DetailID'] = $rDetail->DetailID;
                    $dataSale[$x]['Detail'][$i]['CoopID'] = $rDetail->CoopID;
                    $dataSale[$x]['Detail'][$i]['SaleId'] = $rDetail->SaleId;
                    $dataSale[$x]['Detail'][$i]['InventoryID'] = $rDetail->InventoryID;
                    $dataSale[$x]['Detail'][$i]['Qty'] = $rDetail->Qty;
                    $dataSale[$x]['Detail'][$i]['Price'] = $rrDetail->Price;
                    $dataSale[$x]['Detail'][$i]['Problem'] = $rrDetail->Problem;
                    $dataSale[$x]['Detail'][$i]['Solution'] = $rrDetail->Solution;
                    $dataSale[$x]['Detail'][$i]['DateStart'] = $rrDetail->DateStart;
                    $dataSale[$x]['Detail'][$i]['DateEnd'] = $rrDetail->DateEnd;

                    $i++;
                }

                $x++;
            }
        }

        return json_encode($dataSale);
    }

    function saving($CoopID) {
        $data = array();

        $q = $this->db->get_where('coop_member_saving', array('SyncedDate' => null, 'CoopID' => $CoopID));
        if ($q->num_rows() > 0) {
            $x = 0;
            foreach ($q->result() as $r) {
                $data[$x] = array(
                    'memberSavingID' => $r->memberSavingID,
                    'CoopID' => $r->CoopID,
                    'memberID' => $r->memberID,
                    'savingTypeID' => $r->savingTypeID,
                    'memberSavingRegisteredDate' => $r->memberSavingRegisteredDate,
                    'AmountSaving' => $r->AmountSaving,
                    'memberSavingNo' => $r->memberSavingNo,
                    'memberSavingStatus' => $r->memberSavingStatus,
                    'memberSavingRemark' => $r->memberSavingRemark,
                    'CreatedBy' => $r->CreatedBy,
                    'CreatedDate' => $r->CreatedDate,
                    'UpdatedDate' => $r->UpdatedDate
                );
                $x++;
            }
        }

        return json_encode($data);
    }

    function insertSaving($json) {
        $ret = array();
        $i = 0;
        foreach ($json as $key => $r) {

            $data = array(
                'memberSavingID' => $r->memberSavingID,
                'CoopID' => $r->CoopID,
                'memberID' => $r->memberID,
                'savingTypeID' => $r->savingTypeID,
                'memberSavingRegisteredDate' => $r->memberSavingRegisteredDate,
                'AmountSaving' => $r->AmountSaving,
                'memberSavingNo' => $r->memberSavingNo,
                'memberSavingStatus' => $r->memberSavingStatus,
                'memberSavingRemark' => $r->memberSavingRemark,
                'CreatedBy' => $r->CreatedBy,
                'CreatedDate' => $r->CreatedDate,
                'UpdatedDate' => $r->UpdatedDate
            );

            $wer = array('memberSavingID' => $r->memberSavingID, 'coopID' => $r->coopID);
            $q = $this->db->get_where('coop_member_saving', $wer);
            if ($q->num_rows() > 0) {
                $this->db->where($wer);
                $this->db->update('coop_member_saving', $data);
            } else {
                $this->db->insert('coop_member_saving', $data);
            }

            $ret[$i]['memberSavingID'] = $r->memberSavingID;
            $ret[$i]['coopID'] = $r->coopID;
            $ret[$i]['SyncedDate'] = date('Y-m-d');

            $i++;
        }
        return json_encode($ret);
    }

    function insertFeedbackSaving($json) {
        $this->db->trans_begin();

        foreach ($json as $key => $r) {

            $ret['memberSavingID'] = $r->memberSavingID;
            $ret['coopID'] = $r->coopID;
            $this->db->where($ret);
            $this->db->update('coop_member_saving', array('SyncedDate' => $r->SyncedDate));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function transaction($CoopID) {
        $data = array();

        $q = $this->db->get_where('coop_member_transaction', array('SyncedDate' => null, 'CoopID' => $CoopID));
        if ($q->num_rows() > 0) {
            $x = 0;
            foreach ($q->result() as $r) {

                $data[$x] = array(
                    'MemberTransactionID' => $r->MemberTransactionID,
                    'CoopID' => $r->CoopID,
                    'MemberTransactionType' => $r->MemberTransactionType,
                    // 'savingTypeID' => $r->savingTypeID,
                    'MemberTransactionNumber' => $r->MemberTransactionNumber,
                    'MemberTransactionDate' => $r->MemberTransactionDate,
                    'MemberTransactionName' => $r->MemberTransactionName,
                    'MemberTransactionIdentity' => $r->MemberTransactionIdentity,
                    'MemberTransactionAddress' => $r->MemberTransactionAddress,
                    'MemberID' => $r->MemberID,
                    'MemberSavingID' => $r->MemberSavingID,
                    'MemberTransactionAmount' => $r->MemberTransactionAmount,
                    'MemberTransactionCurrentBalance' => $r->MemberTransactionCurrentBalance,
                    'MemberTransactionRemark' => $r->MemberTransactionRemark,
                    'CreatedBy' => $r->CreatedBy,
                    'CreatedDate' => $r->CreatedDate,
                    'UpdatedBy' => $r->UpdatedBy,
                    'UpdatedDate' => $r->UpdatedDate,
                    'ApprovedBy' => $r->ApprovedBy
                );
                $x++;
            }
        }

        return json_encode($data);
    }

    function insertTransaction($json) {
        $ret = array();
        $i = 0;
        foreach ($json as $key => $r) {

            $data = array(
                'MemberTransactionID' => $r->MemberTransactionID,
                'CoopID' => $r->CoopID,
                'MemberTransactionType' => $r->MemberTransactionType,
                // 'savingTypeID' => $r->savingTypeID,
                'MemberTransactionNumber' => $r->MemberTransactionNumber,
                'MemberTransactionDate' => $r->MemberTransactionDate,
                'MemberTransactionName' => $r->MemberTransactionName,
                'MemberTransactionIdentity' => $r->MemberTransactionIdentity,
                'MemberTransactionAddress' => $r->MemberTransactionAddress,
                'MemberID' => $r->MemberID,
                'MemberSavingID' => $r->MemberSavingID,
                'MemberTransactionAmount' => $r->MemberTransactionAmount,
                'MemberTransactionCurrentBalance' => $r->MemberTransactionCurrentBalance,
                'MemberTransactionRemark' => $r->MemberTransactionRemark,
                'CreatedBy' => $r->CreatedBy,
                'CreatedDate' => $r->CreatedDate,
                'UpdatedBy' => $r->UpdatedBy,
                'UpdatedDate' => $r->UpdatedDate,
                'ApprovedBy' => $r->ApprovedBy
            );

            $wer = array('MemberTransactionID' => $r->MemberTransactionID, 'CoopID' => $r->CoopID);
            $q = $this->db->get_where('coop_member_transaction', $wer);
            if ($q->num_rows() > 0) {
                $this->db->where($wer);
                $this->db->update('coop_member_transaction', $data);
            } else {
                $this->db->insert('coop_member_transaction', $data);
            }

            $ret[$i]['MemberTransactionID'] = $r->MemberTransactionID;
            $ret[$i]['CoopID'] = $r->CoopID;
            $ret[$i]['SyncedDate'] = date('Y-m-d');

            $i++;
        }
        return json_encode($ret);
    }

    function insertFeedbackTransaction($json) {
        $this->db->trans_begin();

        foreach ($json as $key => $r) {

            $ret['MemberTransactionID'] = $r->MemberTransactionID;
            $ret['CoopID'] = $r->CoopID;
            $this->db->where($ret);
            $this->db->update('coop_member_type', array('SyncedDate' => $r->SyncedDate));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function exportAllData() {
        $tables = array(
            'coop_approval',
            // 'coop_approval_staff',
            'coop_area_member',
                //'coop_cash_source',
                //'coop_deposit_interest',
                //'coop_documents',
                //'coop_interest_type',
                //'coop_inventory',
                //'coop_inventorycat',
                //'coop_inventorydeprec',
                //'coop_inventorydeprecitem',
                //'coop_loan',
                //'coop_loan_installment',
                //'coop_loan_type',
                //'coop_loan_type_members',
                //'coop_member',
                //'coop_member_loan',
                //'coop_member_saving',
                //'coop_member_transaction',
                //'coop_member_type',
                //'coop_saving_type',
                //'coop_saving_type_members',
                //'coop_stock_opname',
                //'coop_stock_opname_items',
                //'coop_supplier',
                // 'coop_sync',
                // 'coop_sync_farmer',
                //'coop_transactions',
        );

        $result = array(
            'table_count' => count($tables),
            'coop_data' => array(
            /* sample format
              'sample_table' => array(
              'field_count' => 2,
              'fields' => array('id', 'name'),
              'record_count' => 2,
              'records' => array(
              array('id'=>'1', 'name'=> 'sample value'),
              array('id'=>'2', 'name'=> 'sample value'),
              ...
              ),
              ) */
            )
        );

        foreach ($tables as $tbl) {
            $name = $tbl;

            $this->db->from($tbl);
            // $this->db->where('')
            $q = $this->db->get();

            $lf = $q->list_fields();

            $result['coop_data'][$name]['field_count'] = count($lf);
            $result['coop_data'][$name]['fields'] = $lf;
            $result['coop_data'][$name]['records'] = $q->result_array();
            $result['coop_data'][$name]['record_count'] = $q->num_rows();

            $q->free_result();
        }

        return $result;
    }

// end of exportAllData()

    function getAlldata($CoopID) {
        
        $tables = $this->db->list_tables();
        $values = array();
        
        foreach($tables as $keys => $val) {
            
            if (0 === strpos($val, 'coop_')) {
                if ($this->db->field_exists('CoopID', $val)){
                    $Q = $this->db->get_where($val,array('CoopID' => getCoopID()));
                
                    if($Q->num_rows() > 0) { 
                        $result = $Q->result_array();
                        $output = array($val => $result);
                        $values[] = $output;
                    }
                }
            }
        }
        
        return $values;
    }

}

?>