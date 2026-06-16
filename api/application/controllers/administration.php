<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administration extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('administration/mregion');
	}

	public function tree_get()
	{
		$tree['id'] = '';
		$tree['text'] = 'Province';
		$tree['cls'] = 'root';
		$tree['expanded'] = true;
		$tree['children'] = array();

		$province = $this->mregion->getProvince();
		if (!empty($province)) {
			foreach ($province as $key_prov => $prov) {
                $tree['children'][$key_prov]['id']    = $prov['id'];
                $tree['children'][$key_prov]['text']  = $prov['name'];
                $tree['children'][$key_prov]['cls']  = 'province';
                $tree['children'][$key_prov]['expanded']  = false;
				$district = $this->mregion->getDistrict($prov['id']);
				if (!empty($district)) {
					$tree['children'][$key_prov]['leaf'] = false;
					foreach ($district as $key_dist => $dist) {
						$tree['children'][$key_prov]['children'][$key_dist]['id'] = $dist['id'];
						$tree['children'][$key_prov]['children'][$key_dist]['text'] = $dist['name'];
						$tree['children'][$key_prov]['children'][$key_dist]['cls'] = 'district';
						$tree['children'][$key_prov]['children'][$key_dist]['expanded'] = false;
						$subdistrict = $this->mregion->getSubDistrict($dist['id']);
						if (!empty($subdistrict)) {
							$tree['children'][$key_prov]['children'][$key_dist]['leaf'] = false;
							foreach ($subdistrict as $key_subdist => $subdist) {
								$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['id'] = $subdist['id'];
								$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['text'] = $subdist['name'];
								$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['cls'] = 'subdistrict';
								$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['expanded'] = false;
								// $village = $this->mregion->getVillage($subdist['id']);
								// if (!empty($village)) {
								// 	$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['leaf'] = false;
								// 	foreach ($village as $key_vill => $vill) {
								// 		$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['children'][$key_vill]['id'] = $vill['id'];
								// 		$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['children'][$key_vill]['text'] = $vill['name'];
								// 		$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['children'][$key_vill]['cls'] = 'village';
								// 		$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['children'][$key_vill]['leaf'] = true;
								// 	}
								// } else {
									$tree['children'][$key_prov]['children'][$key_dist]['children'][$key_subdist]['leaf'] = true;
								// }
							}
						} else {
							$tree['children'][$key_prov]['children'][$key_dist]['leaf'] = true;
						}
					}
				} else {
					$tree['children'][$key_prov]['leaf'] = true;
				}
			}
		}
		// var_dump($province);exit;

		$this->response(array(
			'text' => '.',
			'children' => $tree
		), 200);
	}

	public function regions_get()
	{
        $type   = $this->get('type');
        $id     = $this->get('id');
        switch ($type) {
        	case 'province':
        		$region = $this->mregion->getDistrict($id);
        		break;
        	case 'district':
        		$region = $this->mregion->getSubDistrict($id);
        		break;
        	case 'subdistrict':
        		$region = $this->mregion->getVillage($id);
        		break;

        	default:
				$region = $this->mregion->getProvince();
        		break;
        }
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>';exit;
		$this->response($region, 200);
	}

	public function region_post()
	{
        $id     = $this->post('id');
        $name   = $this->post('name');
        $parent_id   = $this->post('parent_id');
        $type   = $this->post('type');
        $mode   = $this->post('mode');

        if ($mode == 'add') {
            $func_detail    = "get".ucfirst($type)."Detail";
            $func_add       = "add".ucfirst($type);
        	// check id
        	if ($this->mregion->$func_detail($id) === false) {
        		$result = $this->mregion->$func_add($id, $name, $parent_id);
        	} else {
				$this->response(array('success' => false, 'msg' => "ID already exists"), 200);
        	}
        } else {
        	$id     = $this->post('old_id');
        	$func_update = "update".ucfirst($type);
        	$result = $this->mregion->$func_update($id, $name);
        }
        if ($result) {
        	$this->response(array('success' => true), 200);
        } else {
        	$this->response(array('success' => fasle,'msg' => "Failed to {$mode} data"), 200);
        }
	}

	public function region_delete()
	{
		$id = $this->delete('id');
		$type = $this->delete('type');

		switch ($type) {
			case 'province':
				$child = 'district';
				break;
			case 'district':
				$child = 'subdistrict';
				break;
			case 'subdistrict':
				$child = 'village';
				break;
			default:
				$child = null;
				break;
		}
		$func_child = "get{$child}";
		$func_delete = "delete{$type}";

		if ($child && count($this->mregion->$func_child($id)) > 0) {
			$this->response(array('message' => 'Can not delete data with childs'), 200);
			exit;
		}
		$result = $this->mregion->$func_delete($id);
		if ($result) {
			$this->response(array('success' => true), 200);
		} else {
			$this->response(false, 400);
		}
	}

    public function province_list_get()
    {
        $data = $this->mregion->listProvince();
        $this->response($data, 200);
    }

    public function district_list_get()
    {
        $data = $this->mregion->listDistrict($this->get('ProvinceID'));
        $this->response($data, 200);
    }

    public function subdistrict_list_get()
    {
        $data = $this->mregion->listSubDistrict($this->get('DistrictID'));
        $this->response($data, 200);
    }

}

/* End of file administration.php */
/* Location: ./application/controllers/administration.php */
