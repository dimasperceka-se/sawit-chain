<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Coa extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('accounting/mcoa');
    }

    public function tree_get() {
        $closedDateID = $this->get('closedDate');
        $tree['id'] = '';
        $tree['text'] = 'Chart of Accounts';
        $tree['cls'] = 'root';
        $tree['expanded'] = true;
        $tree['children'] = array();

        $class = $this->mcoa->getCoaClass();
        if (!empty($class)) {
            foreach ($class as $key_cls => $cls) {
                $tree['children'][$key_cls]['id'] = 'class|' . $cls['id'] . '|' . $cls['code'];
                $tree['children'][$key_cls]['text'] = $cls['name'];
                $tree['children'][$key_cls]['cls'] = 'class';
                $tree['children'][$key_cls]['code'] = $cls['code'];
                $tree['children'][$key_cls]['expanded'] = false;
                $group = $this->mcoa->getCoaGroup($cls['id']);
                if (!empty($group)) {
                    $tree['children'][$key_cls]['leaf'] = false;
                    foreach ($group as $key_grp => $grp) {
                        $tree['children'][$key_cls]['children'][$key_grp]['id'] = 'group|' . $grp['id'] . '|' . $grp['code'];
                        $tree['children'][$key_cls]['children'][$key_grp]['text'] = $grp['name'];
                        $tree['children'][$key_cls]['children'][$key_grp]['cls'] = 'group';
                        $tree['children'][$key_cls]['children'][$key_grp]['code'] = $grp['code'];
                        $tree['children'][$key_cls]['children'][$key_grp]['expanded'] = false;
                        $coa = $this->mcoa->getCoa($grp['id'], $closedDateID);
                        if (!empty($coa)) {
                            $tree['children'][$key_cls]['children'][$key_grp]['leaf'] = false;
                            foreach ($coa as $key_coa => $val_coa) {
                                $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['id'] = 'coa|' . $val_coa['id'] . '|' . $val_coa['code'];
                                $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['text'] = $val_coa['name'];
                                $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['cls'] = 'coa';
                                $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['code'] = $val_coa['code'];
                                $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['expanded'] = false;
                                $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['leaf'] = true;

                                $coaChild = $this->mcoa->getCoaChild($val_coa['code'], $closedDateID);
                                if ($coaChild) {
                                    $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['leaf'] = false;
                                    $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['children'] = $this->recursive($val_coa['code'], $closedDateID);
                                } else {

                                    $tree['children'][$key_cls]['children'][$key_grp]['children'][$key_coa]['leaf'] = true;
                                }
                            }
                        } else {
                            $tree['children'][$key_cls]['children'][$key_grp]['leaf'] = true;
                        }
                    }
                } else {
                    $tree['children'][$key_cls]['leaf'] = true;
                }
            }
        }

        $this->response(array(
            'text' => '.',
            'children' => $tree
                ), 200);
    }

    function recursive($code, $closedDateID) {
        $array = array();
        $coaChild = $this->mcoa->getCoaChild($code, $closedDateID);
        if (!empty($coaChild)) {
            $array['leaf'] = false;
            foreach ($coaChild as $key_child => $val_child) {
                $array['children'][$key_child]['id'] = 'coa|' . $coaChild[$key_child]['id'] . '|' . $val_child['code'];
                $array['children'][$key_child]['text'] = $coaChild[$key_child]['name'];
                $array['children'][$key_child]['cls'] = 'coa';
                $array['children'][$key_child]['code'] = $coaChild[$key_child]['code'];
                $array['children'][$key_child]['expanded'] = false;
                $coaGrandChild = $this->mcoa->getCoaChild($coaChild[$key_child]['code']);
                if ($coaGrandChild) {
                    $array['children'][$key_child] = $this->recursive($coaChild[$key_child]['code'], $closedDateID);
                    $array['children'][$key_child]['leaf'] = false;
                } else {
                    $array['children'][$key_child]['leaf'] = true;
                }
            }
        }
        return $array['children'];
    }

    public function fin_coas_get() {
        $type = $this->get('type');
        $UseType = $this->get('UseType');
        $id = $this->get('id');
        $closedDateID = $this->get('closedDate');
        $code = $this->get('code');
        switch ($type) {
            case 'class':
                $coa = $this->mcoa->getCoaGroup($id);
                break;
            case 'group':
                $coa = $this->mcoa->getCoa2($id, $closedDateID, $UseType);
               // foreach ($coa as $key => $value) {
               //     if($key=='CoaStatus')
               //     {
               //      // unset($coa['CoaStatus']);
               //      // $coa['xxxx'] = 
               //      array_push($coa, array('asd'));
               //     }
               // }
                break;
            case 'coa':
                $coa = $this->mcoa->getCoaChild($code, $closedDateID);
                break;
            case 'all':
                $coa = $this->mcoa->getAll();
                break;
            default:
                $coa = $this->mcoa->getCoaClass();
                break;
        }
        $this->response($coa, 200);
    }

    function fin_coa_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $coa = $this->mcoa->readCoa($this->get('id'));
        if ($coa)
            $this->response($coa, 200);
        else
            $this->response(array('error' => 'Coa could not be found'), 404);
    }

    function fin_coaclass_post() {
        $coa = $this->mcoa->createCoaClass($this->post('typeName'), $_SESSION['userid']);

        if ($coa) {
            $this->response($coa, 200);
        } else {
            $this->response(array('error' => 'Coa could not be added'), 404);
        }
    }

    function fin_coagroup_post() {
        $coa = $this->mcoa->createCoaGroup($this->post('code'), $this->post('parent'), $this->post('parent_id'), $this->post('name'), $_SESSION['userid']);

        if ($coa) {
            $this->response($coa, 200);
        } else {
            $this->response(array('error' => 'Coa could not be added'), 404);
        }
    }

    function fin_coa_celledit_put()
    {
        $data = json_decode($this->put('data'));
        $coa = $this->mcoa->editCoa($data, $_SESSION['userid']);

        if ($coa) {
            $this->response($coa, 200);
        } else {
            $this->response(array('error' => 'Coa could not be edit'), 404);
        }
    }

    function fin_coa_post() {
        $type = $this->post('type');
        $mode = $this->post('mode');

        if ($mode == 'add') {
            if ($type == 'class') {
                $coa = $this->mcoa->createCoaClass($this->post('name'), $_SESSION['userid']);
            } elseif ($type == 'group') {
                $coa = $this->mcoa->createCoaGroup($this->post('code'), $this->post('parent'), $this->post('source_id'), $this->post('name'), $_SESSION['userid']);
            } elseif ($type == 'coa') {
                $coa = $this->mcoa->createCoa($this->post('code'), null, $this->post('source_id'), $this->post('name'), $this->post('coaType'), $this->post('journalClosedDate'), $this->post('coaBalanceAmount'), $_SESSION['userid']);
            } elseif ($type == 'coa_parent') {
                $coa = $this->mcoa->createCoa($this->post('code'), $this->post('parentCode'), $this->post('source_id'), $this->post('name'), $this->post('coaType'), $this->post('journalClosedDate'), $this->post('coaBalanceAmount'), $_SESSION['userid']);
            }
        } elseif ($mode == 'edit') {
            if ($type == 'class') {
                $coa = $this->mcoa->updateCoaClass($this->post('name'), $_SESSION['userid'], $this->post('old_id'));
            } elseif ($type == 'group') {
                $coa = $this->mcoa->updateCoaGroup($this->post('code'), $this->post('parent'), $this->post('source_id'), $this->post('name'), $_SESSION['userid'], $this->post('old_id'));
            } elseif ($type == 'coa') {
                $coa = $this->mcoa->updateCoa($this->post('code'), null, $this->post('source_id'), $this->post('name'), $this->post('coaType'), $_SESSION['userid'], $this->post('old_id'));
            } elseif ($type = 'coa_parent') {
                $coa = $this->mcoa->updateCoa($this->post('code'), $this->post('parentCode'), $this->post('source_id'), $this->post('name'), $this->post('coaType'), $_SESSION['userid'], $this->post('old_id'));
            }
        }

        if ($coa) {
            $this->response($coa, 200);
        } else {
            $this->response(array('error' => 'Coa could not be added'), 404);
        }
    }

    function fin_coa_put() {
        $type = $this->post('type');


        $update = $this->mcoa->updateCoa($this->put('typeName'), $_SESSION['userid'], $this->put('id'));
        if ($update)
            $this->response($update, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Coa could not be edited'), 404);
    }

    function fin_coa_delete() {
        $type = $this->delete('type');

        if ($type == 'class') {
            $delete = $this->mcoa->deleteCoaClass($this->delete('id'));
        } elseif ($type == 'group') {
            $delete = $this->mcoa->deleteCoaGroup($this->delete('id'));
        } elseif ($type == 'coa' || $type == 'coa_parent') {
            $delete = $this->mcoa->deleteCoa($this->delete('id'));
        }

        if ($delete)
            $this->response($delete, 200);
        else
            $this->response(array('error' => 'Coa could not be deleted'), 404);
    }

    function combo_closingdate_get() {
        $data = $this->mcoa->getComboJournalClosingDate();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Transaction!'), 404);
    }

}
