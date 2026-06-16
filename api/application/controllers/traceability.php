<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Traceability extends REST_Controller {

    public function __construct() {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('traceability/mdata');
        $this->load->model('traceability/mtransaction');
        $this->load->model('traceability/mtraceability');
    }

    function data_get($type) {
        if ($this->get('id') == '') {
            $data = $this->mdata->readDatas($_SESSION['userid'], $this->get('type'), $this->get('key'), $this->get('prov'),
               $this->get('kab'), $this->get('start'), $this->get('limit'));
        } else {
            $data = $this->mdata->readData($this->get('id'));
        }
        //var_dump($this->db->last_query());die;
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function data_warehouse_get() {
        $data = $this->mdata->readDataComboWarehouse();
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
	function data_template_supplychain_get() {
        $data = $this->mtraceability->readDataTemplateSupplychain();
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function data_bu_get() {
        $data = $this->mdata->readDataComboBu($this->get('wh'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function datawarehouse_get($type) {

        if ($this->get('id') == '') {
            $data = $this->mdata->readDatasWarehouse($_SESSION['userid'], $type, $this->get('key'), $this->get('prov'), $this->get('kab'), $this->get('start'), $this->get('limit'));
        } else {
            $data = $this->mdata->readDataWarehouse($type, $this->get('id'));
        }
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function objids_get() {
      $data = $this->mdata->readDataObjIds($this->get('type'),$this->get('id'));
      if ($data) $this->response($data, 200);
      else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function data_image_post() {
        if ($this->file['Photo']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/Photo_traceability/' . $gambar);
            if (isset($upload['upload_data'])) {
                @unlink('images/Photo_traceability/' . $this->post('Photo_old'));
                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function DECtoDMS($dec) {
        $vars = explode(".", $dec);
        $deg = $vars[0];
        $tempma = "0." . $vars[1];

        $tempma = $tempma * 3600;
        $min = floor($tempma / 60);
        $sec = $tempma - ($min * 60);

        return array($deg, $min, $sec);
    }

    function data_post() {
      if ($this->post('SupplychainID')=='')
         $data = $this->mdata->createData($this->post('ObjID'),$this->post('ObjType'), $_SESSION['userid']);
      else $data = $this->mdata->updateData($this->post('ObjID'),$this->post('IsGenerateBuyingUnitBatch'),$this->post('ObjType'), $this->post('PerwakilanKoperasi'),
         $this->post('MekanismeMoisture'),
         $this->post('MekanismeReward'),$this->post('PembelianNonFarmer'),$this->post('PembelianFarmer'),$this->post('PembelianFarmerCert'),
         $this->post('PembelianBatch'),$this->post('TanpaKualitas'),$this->post('KalkulasiPremium'),$this->post('LabelKarung'),
         $this->post('IsFakturNumber'),$this->post('IstilahFarmer'),$this->post('PemisahanBatch'),
         $_SESSION['userid'],$this->post('SupplychainID'),$this->post('Kab'),
         $this->post('IsFF'),$this->post('IsFAQ'),$this->post('IsMoistureKarung'),$this->post('IsGeneratePacking'),
         $this->post('GenerateWeightOrPackage'),$this->post('IsSuratJalan'),$this->post('FormulaNettoKarung'),
         $this->post('StandardMoistureKarung'),
         $this->post('FormulaNettoPrice'),$this->post('FormulaNettoAkhir'),
         $this->post('LabelFarmerCertified'),$this->post('LabelFarmerNonCertified'),$this->post('LabelNonFarmer'),$this->post('LabelFAQ'),$this->post('LabelFF'),
         $this->post('IsDriver'),$this->post('IsVehicleType'),$this->post('IsDriverPosition'),$this->post('IsDriverAddress'),$this->post('IsPoliceNumber'),$this->post('IsGeneratePo'),$this->post('IsLockDestWeigh'),$this->post('IsAutoBatch'),$this->post('SentEmail'));
      if ($data) $this->response($data, 200);
      else $this->response(array('error' => 'data could not be found'), 404);
    }

    function datawarehouse_post($type)
    {
//         if ($this->file['Photo']['name'] != '') {
//            $gambar = date('Ymdhis') . '_' . $this->file['Photo']['name'];
//            move_uploaded_file($this->file['Photo']['tmp_name'], 'images/Photo_traceability/' . $gambar);
//        } else
//            $gambar = $this->post('Photo_old');
//        $lat = $this->DECtoDMS($this->post('LatSec'));
//        $long = $this->DECtoDMS($this->post('LongSec'));
//        $gambar=null;
        if ($this->post('WarehouseID') == '') {
//            echo 'asd'.$data;
            $data = $this->mdata->createDataWarehouse($type, $this->post('LatSec'),$this->post('LongSec'), $this->post('Elevation'), $_SESSION['userid']);
//
        } else {
            $data = $this->mdata->updateDataWarehouse($type,  $this->post('LatSec'),$this->post('LongSec'), $this->post('Elevation'), $_SESSION['userid']);
        }

        if ($data)
        {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'data could not be found'), 404);
            }
    }

    function data_delete($type) {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $data = $this->mdata->deleteData($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be delete'), 404);
    }

    function partner_get() {
        $data = $this->mdata->readPartners();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    //staff
    function staff_get() {
        $data = $this->mdata->readStaffs($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function staff_farmers_get() {
        $data = $this->mdata->readStaffFarmers(substr($this->get('VillageID'), 0, 4), $this->get('query'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function staff_post() {
        if (!$this->post('StaffName'))
            $this->response(NULL, 400);
        $data = $this->mdata->createStaff($_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function staff_put() {
        if (!$this->put('StaffName'))
            $this->response(NULL, 400);
        $data = $this->mdata->updateStaff($this->put('WarehouseID'), $this->put('StaffName'), $this->put('Email'),
            $this->put('Phone'), $this->put('Position'), $this->put('StaffBirth'), $this->put('StaffGender'),
            $this->put('StaffBirth'), $_SESSION['userid'], $this->put('StaffID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function staff_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $data = $this->mdata->deleteStaff($this->delete('id'), $this->delete('userid'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be delete'), 404);
    }

    //end staff
    //quality standard
    function quality_standards_get() {
        $data = $this->mdata->readQualityStandards($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function quality_standard_get() {
        $data = $this->mdata->readQualityStandard($this->get('StandardID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function quality_standard_combo_get() {
        $data = $this->mdata->readQualityStandardCombos($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function quality_standard_post() {
        if (!$this->post('StandardName'))
            $this->response(NULL, 400);
            $data = $this->mdata->createQualityStandard($this->post('SupplychainID'), $this->post('StandardName'),
            $this->post('IsReward'),$this->post('IsClaim'),$_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_standard_put() {
        if (!$this->put('StandardName'))
            $this->response(NULL, 400);
            $data = $this->mdata->updateQualityStandard($this->put('StandardName'), $this->put('IsReward'),
                    $this->put('IsClaim'),$_SESSION['userid'],$this->put('StandardID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_standard_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $data = $this->mdata->deleteQualityStandard($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be delete'), 404);
    }

    //end quality
    //premium
    function premiums_get() {
        $data = $this->mdata->readPremiums($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function premium_get() {
        $data = $this->mdata->readPremium($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function premium_post() {
        if (!$this->post('PremiumDateStart')) $this->response(NULL, 400);
        $data = $this->mdata->createPremium($this->post('PremiumSupplychainID'), $this->post('PremiumDateStart'),
            $this->post('PremiumDateEnd'), $this->post('PersenPetani'), $this->post('PersenBuyinUnit'), $this->post('PersenPerwakilan'),$this->post('USD'),
            $this->post('Kurs'), $this->post('Rupiah'), $_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function premium_put() {
        if (!$this->put('PremiumDateStart')) $this->response(NULL, 400);
        $data = $this->mdata->updatePremium($this->put('PremiumSupplychainID'), $this->put('PremiumDateStart'),
            $this->put('PremiumDateEnd'), $this->put('PersenPetani'), $this->put('PersenBuyinUnit'),$this->put('PersenPerwakilan'), $this->put('USD'),
            $this->put('Kurs'), $this->put('Rupiah'), $_SESSION['userid'], $this->put('PremiumID'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function premium_delete() {
        if (!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mdata->deletePremium($this->delete('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }

    //end premium
    //quality
    function quality_get() {
        $data = $this->mdata->readQualitys($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function quality_post() {
        if (!$this->post('QualityDateStart')) $this->response(NULL, 400);
        $data = $this->mdata->createQuality($this->post('QualitySupplychainID'), $this->post('QualityDateStart'),
         $this->post('QualityDateEnd'), $this->post('StandardID'),$_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_put() {
        if (!$this->put('QualityDateStart')) $this->response(NULL, 400);
        $data = $this->mdata->updateQuality($this->put('QualitySupplychainID'), $this->put('QualityDateStart'),
            $this->put('QualityDateEnd'), $this->put('StandardID'),$_SESSION['userid'], $this->put('QualityID'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_delete() {
        if (!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mdata->deleteQuality($this->delete('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }

    //end quality
    //quality var
    function quality_var_get() {
        $data = $this->mdata->readQualityVars($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function quality_var_post() {
        if (!$this->post('Name'))  $this->response(NULL, 400);
        $data = $this->mdata->createQualityVar($this->post('VariableID'), $this->post('SupplychainID'), $this->post('Name'),
            $this->post('Formula'), $this->post('Code'), $this->post('Order'), $_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_var_put() {
        if (!$this->put('Name'))
            $this->response(NULL, 400);
        $data = $this->mdata->updateQualityVar($this->put('Name'), $this->put('Formula'), $this->put('Code'),
            $this->put('Order'), $_SESSION['userid'], $this->put('VariableID'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function quality_var_delete() {
        if (!$this->delete('VariableID')) $this->response(NULL, 400);
        $data = $this->mdata->deleteQualityVar($this->delete('VariableID'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }

    //end quality var
    //reward
    function reward_get() {
        $data = $this->mdata->readRewards($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function reward_post() {
        if (!$this->post('RewardDate'))
            $this->response(NULL, 400);
        $data = $this->mdata->createReward($this->post('RewardSupplychainID'), $this->post('RewardDate'), $this->post('FFMoisture'), $this->post('FFBeanCount'), $this->post('FFWaste'), $this->post('FFMouldy'), $this->post('FFInsect'), $this->post('FFSlaty'), $this->post('FAQMoisture'), $this->post('FAQBeanCount'), $this->post('FAQWaste'), $this->post('FAQMouldy'), $this->post('FAQInsect'), $this->post('FAQSlaty'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function reward_put() {
        if (!$this->put('RewardDate'))
            $this->response(NULL, 400);
        $data = $this->mdata->updateReward($this->put('RewardSupplychainID'), $this->put('RewardDate'), $this->put('FFMoisture'), $this->put('FFBeanCount'), $this->put('FFWaste'), $this->put('FFMouldy'), $this->put('FFInsect'), $this->put('FFSlaty'), $this->put('FAQMoisture'), $this->put('FAQBeanCount'), $this->put('FAQWaste'), $this->put('FAQMouldy'), $this->put('FAQInsect'), $this->put('FAQSlaty'), $_SESSION['userid'], $this->put('RewardID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function reward_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $data = $this->mdata->deleteReward($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be delete'), 404);
    }

    //end reward
    //price
    function price_get() {
        $data = $this->mdata->readPrices($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function price_post() {
        if (!$this->post('PriceDateStart'))
            $this->response(NULL, 400);
        $data = $this->mdata->createPrice($this->post('PriceSupplychainID'), $this->post('PriceDateStart'),
            $this->post('PriceDateEnd'), $this->post('FFPrice'), $this->post('FAQPrice'), $this->post('District'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function price_put() {
        if (!$this->put('PriceDateStart'))
            $this->response(NULL, 400);
        $data = $this->mdata->updatePrice($this->put('PriceSupplychainID'), $this->put('PriceDateStart'),
            $this->put('PriceDateEnd'), $this->put('FFPrice'), $this->put('FAQPrice'), $this->put('District'), $_SESSION['userid'], $this->put('PriceID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function price_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $data = $this->mdata->deletePrice($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be delete'), 404);
    }
    //end price

    //kurs
    function kurs_get() {
        $data = $this->mdata->readKurss($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function kurs_post() {
        if (!$this->post('KursDateStart')) $this->response(NULL, 400);
        $data = $this->mdata->createKurs($this->post('KursSupplychainID'), $this->post('KursDateStart'),
            $this->post('KursDateEnd'), $this->post('KursNominal'),$_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function kurs_put() {
        if (!$this->put('KursDateStart')) $this->response(NULL, 400);
        $data = $this->mdata->updateKurs($this->put('KursSupplychainID'), $this->put('KursDateStart'),
            $this->put('KursDateEnd'), $this->put('KursNominal'), $_SESSION['userid'], $this->put('KursID'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function kurs_delete() {
        if (!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mdata->deleteKurs($this->delete('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
    //end kurs

    //package
    function package_get() {
        $data = $this->mdata->readPackages($this->get('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function package_post() {
        if (!$this->post('PackageType'))
            $this->response(NULL, 400);
        $data = $this->mdata->createPackage($this->post('PackageSupplychainID'), $this->post('PackageType'),
            $this->post('PackageWeight'), $this->post('PackageCapasity'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function package_put() {
        if (!$this->put('PackageType'))
            $this->response(NULL, 400);
        $data = $this->mdata->updatePackage($this->put('PackageSupplychainID'), $this->put('PackageType'),
            $this->put('PackageWeight'), $this->put('PackageCapasity'), $_SESSION['userid'], $this->put('PackageID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function package_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $data = $this->mdata->deletePackage($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be delete'), 404);
    }

    //end package
    //transaction
    function transaction_ff_get() {
        if ($this->post('unitfrom') == '')
            $org = $this->get('id');
        else
            $org = $this->get('unitfrom');
        $data = $this->mtransaction->readDataFf($org);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'data could not be found'), 404);
    }

    function transaction_reward_get() {
        if ($this->post('unitfrom') == '')
            $org = $this->get('id');
        else
            $org = $this->get('unitfrom');
        $data = $this->mtransaction->readDataReward($org);
         $this->response($data, 200);
    }

    function transactions_get() {
        $data = $this->mtransaction->readDatas($_SESSION['userid'], $this->get('key'), $this->get('tgl'),
            $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transactions_detail_get() {
        $data = $this->mtransaction->readDatasDetail($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_detail_delete() {
        $data = $this->mtransaction->deleteDataDetail($this->delete('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }
    function transaction_batch_sent_get() {
        $data = $this->mtransaction->readBatchSent($this->get('query'), $_SESSION['userid'],$this->get('start'),$this->get('limit'));
		//**//
		$this->response($data, 200);
		//**//
        //**//if ($data) $this->response($data, 200); // 200 being the HTTP response code
        //**//else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transactions_farmer_get() {
        $data = $this->mtransaction->readDatasFarmer($this->get('id'),$this->get('frombatchid'),$this->get('tipe'));
        $this->response($data, 200); // 200 being the HTTP response code
    }

    function transactions_package_get() {
        if ($this->get('unitfrom') == '')
            $org = $this->get('id');
        else
            $org = $this->get('unitfrom');
        $data = $this->mtransaction->readPackages($org);
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_trans_post() {
        if ($this->post('FarmerID') != '') $SupplyID = $this->post('FarmerID');
        else $SupplyID = $this->post('BatchID');

        $type = $this->post('SupplyType');
        if ($type=='Batch') $SupplyID = ($SupplyID==''?NULL:$SupplyID);
        elseif (trim($SupplyID)=='') {
            if ($this->post('NonFarmerID') != '') {
               $SupplyID = $this->post('NonFarmerID');
               $this->mtransaction->updateNonFarmer($this->post('NonFarmerName'), $this->post('NonIdentity'),
                  $this->post('NonVillageID'), $this->post('NonBirthdate'), $this->post('FarmerDescription'),
                  $this->post('NonFarmerID'));
            } else $SupplyID = $this->mtransaction->createNonFarmer($this->post('NonFarmerName'), $this->post('NonIdentity'),
               $this->post('NonVillageID'), $this->post('NonBirthdate'), $this->post('FarmerDescription'));
            $type = 'NonFarmer';
        }
         $data = $this->mtransaction->createTransaction($this->post('SupplyBatchID'), $this->post('DateTransaction'),
            $type, $SupplyID,
            $this->post('FFVolumeBrutoTrans'), null,null,$this->post('FFVolumeNettoTrans'),
            $this->post('FAQVolumeBrutoTrans'), null,null,$this->post('FAQVolumeNettoTrans'),
            null,null,$this->post('FFReward'), $this->post('FFContractPrice'), $this->post('FFNetPrice'), $this->post('FFTotalPayment'),
            null,null,$this->post('FAQReward'), $this->post('FAQContractPrice'), $this->post('FAQNetPrice'), $this->post('FAQTotalPayment'),
               $this->post('DpTotalPayment'), $this->post('StatusCode'),
            $this->post('WeightBy'), $this->post('SupervisorID'), $this->post('AdminID'), $this->post('VehicleNo'),
            $_SESSION['userid'],$this->post('FakturNumber'),$this->post('FFBeratBersihSetara'),$this->post('FFVerifikasi'),
               $this->post('FAQBeratBersihSetara'),$this->post('FAQVerifikasi'),
            $this->post('standardStandardNama'),$this->post('detail'),$this->post('standardStandardNama'),
               $this->post('resultStandardNama'),$this->post('rewardStandardNama'),$this->post('frombatchid'));
         if ($type = 'NonFarmer') $data['NonFarmerID'] = $SupplyID;
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_trans_put() {
		$type = $this->put('SupplyType');
        if ($this->put('FarmerID') != '') $SupplyID = $this->put('FarmerID');
        elseif ($this->put('NonFarmerID') != '') {
            $SupplyID = $this->put('NonFarmerID');
            $type = 'NonFarmer';
        } else $SupplyID = $this->put('BatchID');
        $data = $this->mtransaction->updateTransaction($this->put('SupplyBatchID'), $this->put('DateTransaction'), $type, $SupplyID,
            $this->put('FFVolumeBrutoTrans'), $this->put('FFPackageTrans'), $this->put('FFMoistureTrans'), $this->put('FFVolumeNettoTrans'),
            $this->put('FAQVolumeBrutoTrans'), $this->put('FAQPackageTrans'), $this->put('FAQMoistureTrans'), $this->put('FAQVolumeNettoTrans'),
            null,null,$this->put('FFReward'), $this->put('FFContractPrice'), $this->put('FFNetPrice'), $this->put('FFTotalPayment'),
            null,null,$this->put('FAQReward'), $this->put('FAQContractPrice'), $this->put('FAQNetPrice'), $this->put('FAQTotalPayment'),
               $this->put('DpTotalPayment'), $this->put('StatusCode'),
            $this->put('WeightBy'), $this->put('SupervisorID'), $this->put('AdminID'), $this->put('VehicleNo'),
               $_SESSION['userid'], $this->put('SupplyTransID'),
            $this->put('FakturNumber'),$this->put('FFBeratBersihSetara'),$this->put('FFVerifikasi'),
               $this->put('FAQBeratBersihSetara'),$this->put('FAQVerifikasi'),
            $this->put('standardStandardNama'),$this->put('detail'),$this->put('standardStandardNama'),$this->put('resultStandardNama'),
               $this->put('rewardStandardNama'),
			//**//
			$this->put('QDetailID'),$this->put('QStandardID'),
			$this->put('QFAQResult'),$this->put('QFAQStandard'),$this->put('QFAQReward'),
			$this->put('QFFResult'),$this->put('QFFStandard'),$this->put('QFFReward'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_dest_get() {
        $data = $this->mtransaction->readDest($this->get('query'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_data_trans_get() {
        $data = $this->mtransaction->readTransData($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_farmer_delete() {
        $data = $this->mtransaction->deleteDataFarmer($this->delete('id'),$this->delete('SupplyTransID'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_farmer_post() {
        $packageId = explode('-', $this->post('PackageType'));
        $data = $this->mtransaction->createDataFarmer($this->post('SupplyTransID'), $packageId[0], $this->post('Type'),
            $this->post('Weight'),$this->post('MoistureStandard'),$this->post('Moisture'),$this->post('Netto'), $_SESSION['userid'],
            $this->post('FarmerID'),$this->post('berat'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_farmer_put() {
        $packageId = explode('-', $this->put('PackageType'));
        if ($packageId[1] != '')
            $PackageID = $packageId[0];
        else
            $PackageID = $this->put('PackageID');
        $data = $this->mtransaction->updateDataFarmer($this->put('SupplyTransID'), $PackageID, $this->put('Type'), $this->put('Weight'),
            $this->put('MoistureStandard'),$this->put('Moisture'),$this->put('Netto'),$_SESSION['userid'], $this->put('DetailID'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_number_get() {
        $data = $this->mtransaction->readNumber($_SESSION['userid']);
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

	function transaction_check_batch_get() {
        $check = $this->mtransaction->checkDataBatch($this->get('id'), $this->get('parentID'), $this->get('orgid'));
		$i = 0;
		foreach($check as $k=>$v){
			$hasil[$i]=$v['hasil'];
			$i++;
		}
		if (in_array("Cert", $hasil)) {
			$cert = "1";
		}else{
			$cert = "0";
		}
		if (in_array("NonCert", $hasil)) {
			$noncert = "1";
		}else{
			$noncert = "0";
		}
		if($cert=="1" && $noncert=="1"){
			$data = array(
				'statusnya'=>'beda'
			);
		}else{
			$data = array(
				'statusnya'=>'sama'
			);
		}
        $this->response($data, 200); // 200 being the HTTP response code
        //else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

	function transaction_check_farmer_batch_get() {
        $check = $this->mtransaction->checkDataBatch($this->get('id'), $this->get('parentID'), $this->get('orgid'));
		if(@$check[0]['hasil']!=$this->get('isCert') && @$check[0]['hasil']!="" && @$check[0]['PemisahanBatch']=="1"){
			$data = array(
				'statusnya'=>'beda'
			);
		}else{
			$data = array(
				'statusnya'=>'sama'
			);
		}
        $this->response($data, 200); // 200 being the HTTP response code
        //else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_batch_get() {
        $data = $this->mtransaction->readDataBatch($this->get('id'), $this->get('orgid'), "'Sent','Delivered'");
        $this->response($data, 200); // 200 being the HTTP response code
        //else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_batch_view_get() {
        $data = $this->mtransaction->readDataBatch($this->get('id'), $this->get('orgid'), '%%');
        $this->response($data, 200); // 200 being the HTTP response code
        //else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_add_batch_get() {
        $data = $this->mtransaction->readDataAddBatch($_SESSION['userid'],$this->get('orgid'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_batch_post() {
        $no = $this->mtransaction->readNumber($_SESSION['userid']);
        $status = 'Open';
        if ($this->post('unitfrom') == '') {
            $org = $this->post('SupplychainID');
            $dest = $this->post('SupplyDestOrgID');
        } else {
            $org = $this->post('unitfrom');
            $dest = $this->post('id');
            $no = $this->mtransaction->readNumber(0,$this->post('unitfrom'));
        }
        $data = $this->mtransaction->createDataBatch($org, $dest, $status, $no['number'], $this->post('SupplyBatchDate'),
            $this->post('VolumeBruto'), $this->post('VolumeNetto'),$this->post('PerwakilanOrgID'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_batch_put() {
        if ($this->put('unitfrom') == '') {
            $org = $this->put('SupplychainID');
            $dest = $this->put('SupplyDestOrgID');
            $status = 'Close Batch';
        } else {
            $org = $this->put('unitfrom');
            $unit = $this->put('unitto');
            $unitto = explode(' - ',$unit);
            $dest = $unitto[0];
            $status = 'Sent';
        }
        $data = $this->mtransaction->updateDataBatch($org, $dest, $status, $this->put('SupplyBatchNumber'),
            $this->put('SupplyBatchDate'), $this->put('VolumeBruto'), $this->put('VolumeNetto'), $_SESSION['userid'],
            $this->put('SupplyBatchID'),
            $this->put('DestPO'),$this->put('DestWeight'),$this->put('DestJumlahKarung'),$this->put('DestICS'),$this->put('DestDriver'),$this->put('DestDriverJabatan'),$this->put('DestDriverAddress'),$this->put('DestNoPolisi'),$this->put('DestTransport'),
            $this->put('PerwakilanOrgID'),$this->put('DeliveryDate'),$this->put('SupplyBatchResponsible'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_close_batch_put() {
        $data = $this->mtransaction->closeBatch($this->put('VolumeBruto'),$this->put('VolumeNetto'),$this->put('SupplyBatchID'),$this->put('IsGeneratePo'),$this->put('SupplychainID'));
        if ($data) $this->response($data, 200); // 200 being the HTTP response code
        else $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_batch_delete() {
        $data = $this->mtransaction->deleteDataBatch($this->delete('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_get() {
        $data = $this->mtransaction->readData($this->get('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function transaction_farmers_get() {
        $data = $this->mtransaction->readFarmers($_SESSION['userid'], $this->get('query'), $this->get('start'), $this->get('limit'),
            $this->get('noncert'));
      $this->response($data, 200);
    }
    function transaction_non_farmers_get() {
        $data = $this->mtransaction->readNonFarmers($_SESSION['userid'], $this->get('query'), $this->get('start'), $this->get('limit'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    function transaction_village_get() {
        $data = $this->mtransaction->readNonFarmerVillage($_SESSION['userid']);
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function transaction_unitfrom_get() {
        $data = $this->mtransaction->readUnitFrom($this->get('query'), $_SESSION['userid']);
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    //relasi
    function relasis_get() {
        $data = $this->mdata->readRelasis($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function relasi_get() {
        $data = $this->mdata->readRelasi($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function relasi_child_get() {
        $data = $this->mdata->readRelasiChild($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function relasi_post() {
        if (!$this->post('NoKontrak')) $this->response(NULL, 400);
        $data = $this->mdata->createRelasi($this->post('ParentOrgId'), $this->post('ChildOrgId'), $this->post('NoKontrak'),
            $this->post('StartDate'), $this->post('EndDate'), $this->post('File'), $this->post('Description'),$_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function relasiu_post() {
        if (!$this->post('NoKontrak')) $this->response(NULL, 400);
        $data = $this->mdata->updateRelasi($this->post('ParentOrgId'), $this->post('ChildOrgId'), $this->post('NoKontrak'),
            $this->post('StartDate'), $this->post('EndDate'), $this->post('File'), $this->post('Description'),
            $_SESSION['userid'], $this->post('RelId'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function relasi_delete() {
        if (!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mdata->deleteRelasi($this->delete('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }

    function cetak_get($bu,$awal,$akhir,$jenis) {
         $data = $this->mdata->getTracebility($bu,$awal,$akhir,$jenis);
         $data['awal'] = $awal;
         $data['akhir'] = $akhir;
         //print_r($data);exit;
         if ($data['data']['jenis']=='Gudang') $this->load->view('tracebility_koperasi_cetak', $data);
         else $this->load->view('tracebility_petani_cetak', $data);
         //if ($data['data']['jenis']=='Organisasi Petani')
    }

    function cetak_detail_get($bu,$awal,$akhir,$jenis) {
         $data = $this->mdata->getTracebility($bu,$awal,$akhir,$jenis,'detail');
         $data['awal'] = $awal;
         $data['akhir'] = $akhir;
         //print_r($data);exit;
         if ($data['data']['jenis']=='Gudang') $this->load->view('tracebility_koperasi_detail_cetak', $data);
         else $this->load->view('tracebility_petani_cetak', $data);
    }
    function cetak_perpetani_get($bu,$awal,$akhir,$jenis) {
         $data = $this->mdata->getTracebilityPerPetani($bu,$awal,$akhir,$jenis,'detail');
         $data['awal'] = $awal;
         $data['akhir'] = $akhir;
         //print_r($data);exit;
         //if ($data['data']['jenis']=='Gudang') $this->load->view('tracebility_gudang_perpetani_cetak', $data);
         //else $this->load->view('tracebility_koperasi_perpetani_cetak', $data);
         $this->load->view('tracebility_koperasi_perpetani_cetak', $data);
    }
    //end relasi

    //perwakilan
    function perwakilans_get() {
        $data = $this->mdata->readPerwakilans($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function perwakilan_post() {
        if (!$this->post('TraderName')) $this->response(NULL, 400);
        $data = $this->mdata->createPerwakilan($this->post('ParentOrgId'), $this->post('ChildOrgId'), $this->post('TraderName'),
            $this->post('Village'), $this->post('Address'), $this->post('Handphone'),$_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function perwakilanu_post() {
        if (!$this->post('TraderName')) $this->response(NULL, 400);
        $data = $this->mdata->updatePerwakilan($this->post('ParentOrgId'), $this->post('ChildOrgId'), $this->post('TraderName'),
            $this->post('Village'), $this->post('Address'), $this->post('Handphone'),
            $_SESSION['userid'], $this->post('TraderID'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function perwakilan_delete() {
        if (!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mdata->deletePerwakilan($this->delete('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }
    //end perwakilan

   //IMPORT
    function import_upload_post() {
        $file = $this->file['file']['tmp_name'];
        $name = $this->file['file']['name'];
        /*if (substr($name,strlen($name)-3,3)=='xls') {
            require_once 'application/libraries/excel_reader/excel_reader2.php';
            $data = new Spreadsheet_Excel_Reader($file);
            $eData[0] = $data->sheets[0]['cells'];
            $eData[1] = $data->sheets[1]['cells'];
         } elseif (substr($name,strlen($name)-4,4)=='xlsx') {
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
            $objectReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objectReader->setReadDataOnly(true);
            $objPHPExcel = $objectReader->load($file);
            $objPHPExcel->setActiveSheetIndex(0);
            $eData = $objPHPExcel->getActiveSheet()->toArray();
            for ($i=0;$i<sizeof($eData);$i++) {
               for ($j=0;$j<sizeof($eData[0]);$j++) {
                  $data[$i+1][$j+1] = $eData[$i][$j];
               }
            }
            $eData[0] = $data;
            $objPHPExcel->setActiveSheetIndex(1);
            $eData1 = $objPHPExcel->getActiveSheet()->toArray();
            for ($i=0;$i<sizeof($eData1);$i++) {
               for ($j=0;$j<sizeof($eData1[0]);$j++) {
                  $data1[$i+1][$j+1] = $eData1[$i][$j];
               }
            }
            $eData[1] = $data1;
         }*/
         if (substr($name,strlen($name)-3,3)=='xls' OR substr($name,strlen($name)-4,4)=='xlsx') {
            $eData = $this->import($file,false);
         }
         //echo $this->post('BuyingUnit');exit;
         //print_r($eData);exit;
        if ($this->post('BuyingUnit')=='-- Buying Unit --' and $this->post('Warehouse')=='27'){
            $result = $this->mtraceability->injectDataBT($this->post('Warehouse'),$this->mtransaction->readNumber('',$this->post('Warehouse')),$eData,$_SESSION['userid']);
        }else if ($this->post('DistrictTrader')=='1' and $this->post('Warehouse')=='5'){
            $result = $this->mtraceability->injectDataCargillDT($this->post('Warehouse'),$eData,$_SESSION['userid']);
        }else{
            $result = $this->mtraceability->injectData($this->post('BuyingUnit'),$this->post('Warehouse'),$this->mtransaction->readNumber('',$this->post('BuyingUnit')),$this->mtransaction->readNumber('',$this->post('Warehouse')),$eData,$_SESSION['userid']);
        }   
         if ($result['name']!='') $result['success'] = 'false';
         elseif ($result['name_sertifikasi']!='') $result['success'] = 'false_sertifikasi';
		 elseif ($result['check_nopo']!='') $result['success'] = 'false_nopo';
         elseif ($result['check_date']!='') $result['success'] = 'false_date';
         elseif ($result['check_transdate']!='') $result['success'] = 'false_transdate';
         else $result['success'] = 'true';
        $this->response($result, 200);
    }

   public function import($inputFileName, $cell_exist_only = true) {
      require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
      require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
        $data = array();
        $inputFileType  = PHPExcel_IOFactory::identify($inputFileName);
        $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel    = $objReader->load($inputFileName);
        $a = 0;
        foreach($objPHPExcel->getWorksheetIterator() as $worksheet){
            $data[$a];
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells($cell_exist_only);
                foreach ($cellIterator as $cell) {
                    if (!is_null($cell)) {
                        $row_data[] = $cell->getFormattedValue();
                        // $row_data[] = $cell->getCalculatedValue();
                    }
                }
                if (!empty($row_data)) {
                    $data[$a][] = $row_data;
                    unset($row_data);
                }
            }
            $a++;
        }
        return $data;
    }

    function transactions_packing_get() {
        $data = $this->mtransaction->readDatasPackingList($this->get('id'));
        $this->response($data, 200); // 200 being the HTTP response code
    }
    function transaction_packing_post() {
      if($this->post('type') == 'Transaction'){
        $data = $this->mtransaction->createDataPackingListPerTransaction($this->post('BatchID'), $_SESSION['userid'],
           $this->post('destid'),$this->post('unitto'));
           if ($data) $this->response($data, 200);
           else $this->response(array('error' => 'data could not be found'), 404);
      } else {
        if ($this->post('jumlah')!='')
           $data = $this->mtransaction->createDataPackingList($this->post('BatchID'),$this->post('jumlah'), $_SESSION['userid'],
              $this->post('destid'),$this->post('unitto'));
        elseif ($this->post('DestWeightPerKarung')!='')
           $data = $this->mtransaction->createDataPackingListKarung($this->post('BatchID'),$this->post('DestWeight'),
              $this->post('DestWeightPerKarung'), $_SESSION['userid'],$this->post('destid'),$this->post('unitto'));
        elseif ($this->post('DestJumlahKarung')!='')
           $data = $this->mtransaction->createDataPackingListByKarung($this->post('BatchID'),$this->post('DestWeight'),
              $this->post('DestJumlahKarung'), $_SESSION['userid'],$this->post('destid'),$this->post('unitto'),$this->post('Gelondongan'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
      }
    }
    function transaction_packing_put() {
      if ($this->put('id')!='')
         $data = $this->mtransaction->updateDataPackingList($this->put('jumlah'), $this->put('id'), $_SESSION['userid']);
      if ($data) $this->response($data, 200);
      else $this->response(array('error' => 'data could not be found'), 404);
    }
    function transaction_packing_delete() {
      if ($this->delete('id')!='')
         $data = $this->mtransaction->deleteDataPackingList($this->delete('id'));
      if ($data) $this->response($data, 200);
      else $this->response(array('error' => 'data could not be found'), 404);
    }

    function premium_cetak_koperasi_get($start, $end, $jenis, $provinsi, $warehouse, $sert, $format = '')
    {
        // echo "<pre>"; print_r(compact('bu','start','end','jenis','format')); echo "</pre>"; exit;
        // $data['detail'] = $this->mdata->getPremiumKoperasi($start,$end,$jenis,$userid);
		$data['provinsi'] = $provinsi;
        $data['detail'] = $this->mdata->getDetailsKoperasi(
            $start,
            $end,
            $jenis,
            $provinsi,
            $warehouse,
            $sert
        );
        $data['logo'] = $this->mdata->getReportLogo($start,$warehouse);
        $data['premium'] = $this->mdata->getPremiumByOrg($warehouse,$start);
        $usd    = $data['premium']['PersenBuyinUnit']/100*$data['premium']['USD'];
        $rp     = $usd*$data['premium']['Kurs'];
        $data['usd']    = $usd;
        $data['rp']     = $rp;
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        // echo "<pre>"; print_r($data); echo "</pre>"; exit;
        $data['jenis'] = $jenis;
         // echo '<pre>'; print_r($data); echo '</pre>';exit;
         // echo '<pre>'; print_r($data); echo '</pre>';exit;
         $data['awal'] = $start;
         $data['akhir'] = $end;
        if ($format == 'excel') {
            //**//
            $this->load->library('Excel', null, 'PHPExcel');
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
            $object = new PHPExcel();

            // Set properties
            $object->getProperties()->setCreator("Koltiva Cocoatrace")
                           ->setLastModifiedBy("Koltiva Cocoatrace")
                           ->setCategory("Koltiva Cocoatrace");
            // Add some data
            $object->getActiveSheet()->getColumnDimension('A')->setWidth(50);
            $object->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('L')->setWidth(25);
            $object->getActiveSheet()->mergeCells('A1:L1');
            $object->getActiveSheet()->mergeCells('A2:L2');
            $style_center = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );

            $object->getActiveSheet()->getStyle("A1:L4")->applyFromArray($style_center);
            $style_border = array(
                  'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                      )
                  )
              );
            $object->getActiveSheet()->getStyle("A4:L4")->applyFromArray($style_border);
            $object->getActiveSheet()->getStyle("A1:L4")->getFont()->setBold(true);
            $object->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'Laporan Penjualan per Koperasi')
                        ->setCellValue('A2', 'Premium : IDR '.number_format($rp,0,'.',',').' | USD '.$usd)
                        ->setCellValue('A4', 'Koperasi')
                        ->setCellValue('B4', 'Survey Volume (Kg)')
                        ->setCellValue('C4', 'Quota (Survey+10%)')
                        ->setCellValue('D4', 'Bruto (Kg)')
                        ->setCellValue('E4', 'Netto (Kg)')
                        ->setCellValue('F4', 'Balance (Kg)')
                        ->setCellValue('G4', 'Total (IDR)')
                        ->setCellValue('H4', 'Total (USD)')
                        ->setCellValue('I4', 'Paid (Kg)')
                        ->setCellValue('J4', 'Unpaid (Kg)')
                        ->setCellValue('K4', 'Paid (USD)')
                        ->setCellValue('L4', 'Unpaid (USD)');
            $counter=5;
            foreach ($data['detail'] as $key => $value) {
                $object->getActiveSheet()->getStyle("B$counter:L$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle("A$counter:L$counter")->applyFromArray($style_border);
                $object->getActiveSheet()->setCellValue('A'.$counter, $data['detail'][$key]['name']);
                $object->getActiveSheet()->setCellValue('B'.$counter, number_format($data['detail'][$key]['survey'],2,'.',''));
                $object->getActiveSheet()->setCellValue('C'.$counter, number_format($data['detail'][$key]['quota'],2,'.',''));
                $object->getActiveSheet()->setCellValue('D'.$counter, number_format($data['detail'][$key]['bruto'],2,'.',''));
                $object->getActiveSheet()->setCellValue('E'.$counter, number_format($data['detail'][$key]['netto'],2,'.',''));
                $object->getActiveSheet()->setCellValue('F'.$counter, number_format($data['detail'][$key]['balance'],2,'.',''));
                $object->getActiveSheet()->setCellValue('G'.$counter, number_format($data['detail'][$key]['totalidr'],2,'.',''));
                $object->getActiveSheet()->setCellValue('H'.$counter, number_format($data['detail'][$key]['totalusd'],2,'.',''));
                $object->getActiveSheet()->setCellValue('I'.$counter, number_format($data['detail'][$key]['paidkg'],2,'.',''));
                $object->getActiveSheet()->setCellValue('J'.$counter, number_format($data['detail'][$key]['unpaidkg'],2,'.',''));
                $object->getActiveSheet()->setCellValue('K'.$counter, number_format($data['detail'][$key]['paidusd'],2,'.',''));
                $object->getActiveSheet()->setCellValue('L'.$counter, number_format($data['detail'][$key]['unpaidusd'],2,'.',''));
                $counter++;
            }

            $konter = $counter;
            $konter++; 
            $object->getActiveSheet()->setCellValue('A'.$konter, "Total");
            $object->getActiveSheet()->getStyle("B$konter:L$konter")->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->setCellValue('B'.$konter, "=SUM(B5:B$counter)");
            $object->getActiveSheet()->setCellValue('C'.$konter, "=SUM(C5:C$counter)");
            $object->getActiveSheet()->setCellValue('D'.$konter, "=SUM(D5:D$counter)");
            $object->getActiveSheet()->setCellValue('E'.$konter, "=SUM(E5:E$counter)");
            $object->getActiveSheet()->setCellValue('F'.$konter, "=SUM(F5:F$counter)");
            $object->getActiveSheet()->setCellValue('G'.$konter, "=SUM(G5:G$counter)");
            $object->getActiveSheet()->setCellValue('H'.$konter, "=SUM(H5:H$counter)");
            $object->getActiveSheet()->setCellValue('I'.$konter, "=SUM(I5:I$counter)");
            $object->getActiveSheet()->setCellValue('J'.$konter, "=SUM(J5:J$counter)");
            $object->getActiveSheet()->setCellValue('K'.$konter, "=SUM(K5:K$counter)");
            $object->getActiveSheet()->setCellValue('L'.$konter, "=SUM(L5:L$counter)");

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $object->setActiveSheetIndex(0);
            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="list_koperasi.xlsx');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $objWriter->save('php://output');
            exit;
            //**//
        } elseif ($format == 'pdf') {
            $this->load->view('premium_koperasi_cetak', $data);
            $html = $this->output->get_output();
            // echo $html; exit;

            // // Load library
            // $this->load->library('dompdf_gen');
            // // Convert to PDF
            // $this->dompdf->load_html($html);
            // $this->dompdf->render();
            // $this->dompdf->stream("list_koperasi.pdf");

            $this->load->library('Pdf', null, 'mPDF');
            // $this->mPDF->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822)); // Add a footer for good measure <img src="https://davidsimpson.me/wp-includes/images/smilies/icon_wink.gif" alt=";)" class="wp-smiley">
            $this->mPDF->WriteHTML($html); // write the HTML into the PDF
            $this->mPDF->Output('list_koperasi.pdf','D'); // save to file because we can

            exit;
        }
         //print_r($data);exit;
        $this->load->view('premium_koperasi_cetak', $data);
    }

    function premium_cetak_bu_get($start, $end, $jenis, $provinsi, $warehouse, $sert, $format = '') {
         // $data['detail'] = $this->mdata->getPremiumBu($start,$end,$jenis);

        $data['detail'] = $this->mdata->getPremiumBuv(
            $start,
            $end,
            $jenis,
            $provinsi,
            $warehouse,
            $sert
        );
        $data['logo'] = $this->mdata->getReportLogo($start,$warehouse);
        $data['jenis'] = $jenis;
        $data['premium'] = $this->mdata->getPremiumByOrg($warehouse,$start);
        $usd    = $data['premium']['PersenPerwakilan']/100*$data['premium']['USD'];
        $rp     = $usd*$data['premium']['Kurs'];
        $data['usd']    = $usd;
        $data['rp']     = $rp;
        // echo '<pre>'; print_r($data['detail']); echo '</pre>';exit;

         $data['awal'] = $start;
         $data['akhir'] = $end;
        if ($format == 'excel') {
            //**//
            $this->load->library('Excel', null, 'PHPExcel');
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
            $object = new PHPExcel();

            // Set properties
            $object->getProperties()->setCreator("Koltiva Cocoatrace")
                           ->setLastModifiedBy("Koltiva Cocoatrace")
                           ->setCategory("Koltiva Cocoatrace");
            // Add some data
            $object->getActiveSheet()->getColumnDimension('A')->setWidth(50);
            $object->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('L')->setWidth(25);
            $object->getActiveSheet()->mergeCells('A1:L1');
            $object->getActiveSheet()->mergeCells('A2:L2');
            $object->getActiveSheet()->mergeCells('A5:L5');
            $style_center = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );

            $object->getActiveSheet()->getStyle("A1:L4")->applyFromArray($style_center);
            $style_border = array(
                  'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                      )
                  )
              );
            $object->getActiveSheet()->getStyle("A4:L4")->applyFromArray($style_border);
            $object->getActiveSheet()->getStyle("A5")->applyFromArray($style_border);
            $object->getActiveSheet()->getStyle("A1:L4")->getFont()->setBold(true);
            $object->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
            $object->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'Laporan Penjualan per Buying Unit')
                        ->setCellValue('A2', 'Premium : IDR '.number_format($rp,0,'.',',').' | USD '.$usd)
                        ->setCellValue('A4', 'Buying Unit')
                        ->setCellValue('B4', 'Survey Volume (Kg)')
                        ->setCellValue('C4', 'Quota (Survey+10%)')
                        ->setCellValue('D4', 'Bruto (Kg)')
                        ->setCellValue('E4', 'Netto (Kg)')
                        ->setCellValue('F4', 'Balance (Kg)')
                        ->setCellValue('G4', 'Total (IDR)')
                        ->setCellValue('H4', 'Total (USD)')
                        ->setCellValue('I4', 'Paid (Kg)')
                        ->setCellValue('J4', 'Unpaid (Kg)')
                        ->setCellValue('K4', 'Paid (USD)')
                        ->setCellValue('L4', 'Unpaid (USD)')
                        ->setCellValue('A5', $data['detail'][0]['name_b']);
            $counter=6;
            foreach ($data['detail'] as $key => $value) {
                $object->getActiveSheet()->getStyle("B$counter:L$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle("A$counter:L$counter")->applyFromArray($style_border);
                $object->getActiveSheet()->setCellValue('A'.$counter, $data['detail'][$key]['name']);
                $object->getActiveSheet()->setCellValue('B'.$counter, number_format($data['detail'][$key]['survey'],2,'.',''));
                $object->getActiveSheet()->setCellValue('C'.$counter, number_format($data['detail'][$key]['quota'],2,'.',''));
                $object->getActiveSheet()->setCellValue('D'.$counter, number_format($data['detail'][$key]['bruto'],2,'.',''));
                $object->getActiveSheet()->setCellValue('E'.$counter, number_format($data['detail'][$key]['netto'],4,'.',''));
                $object->getActiveSheet()->setCellValue('F'.$counter, number_format($data['detail'][$key]['balance'],2,'.',''));
                $object->getActiveSheet()->setCellValue('G'.$counter, number_format($data['detail'][$key]['totalidr'],2,'.',''));
                $object->getActiveSheet()->setCellValue('H'.$counter, number_format($data['detail'][$key]['totalusd'],2,'.',''));
                $object->getActiveSheet()->setCellValue('I'.$counter, number_format($data['detail'][$key]['paidkg'],2,'.',''));
                $object->getActiveSheet()->setCellValue('J'.$counter, number_format($data['detail'][$key]['unpaidkg'],2,'.',''));
                $object->getActiveSheet()->setCellValue('K'.$counter, number_format($data['detail'][$key]['paidusd'],2,'.',''));
                $object->getActiveSheet()->setCellValue('L'.$counter, number_format($data['detail'][$key]['unpaidusd'],2,'.',''));
                $counter++;
            }

            $konter = $counter;
            $konter++; 
            $object->getActiveSheet()->setCellValue('A'.$konter, "Total");
            $object->getActiveSheet()->getStyle("B$konter:L$konter")->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->setCellValue('B'.$konter, "=SUM(B5:B$counter)");
            $object->getActiveSheet()->setCellValue('C'.$konter, "=SUM(C5:C$counter)");
            $object->getActiveSheet()->setCellValue('D'.$konter, "=SUM(D5:D$counter)");
            $object->getActiveSheet()->setCellValue('E'.$konter, "=SUM(E5:E$counter)");
            $object->getActiveSheet()->setCellValue('F'.$konter, "=SUM(F5:F$counter)");
            $object->getActiveSheet()->setCellValue('G'.$konter, "=SUM(G5:G$counter)");
            $object->getActiveSheet()->setCellValue('H'.$konter, "=SUM(H5:H$counter)");
            $object->getActiveSheet()->setCellValue('I'.$konter, "=SUM(I5:I$counter)");
            $object->getActiveSheet()->setCellValue('J'.$konter, "=SUM(J5:J$counter)");
            $object->getActiveSheet()->setCellValue('K'.$konter, "=SUM(K5:K$counter)");
            $object->getActiveSheet()->setCellValue('L'.$konter, "=SUM(L5:L$counter)");
            // Rename sheet
            //$object->getActiveSheet()->setTitle('Laporan Penjualan per Buying Unit');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $object->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="list_buying_unit.xlsx');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $objWriter->save('php://output');
            exit;
            //**//
        } elseif ($format == 'pdf') {
            $this->load->view('premium_bu_cetak', $data);
            $html = $this->output->get_output();
            // echo $html; exit;

            // Load library
            $this->load->library('dompdf_gen');

            // Convert to PDF
            $this->dompdf->load_html($html);
            $this->dompdf->render();
            $this->dompdf->stream("list_buying_unit.pdf");
        }
         //print_r($data);exit;
         $this->load->view('premium_bu_cetak', $data);
    }

    private function process_farmers_data_bu($farmers)
    {
        // echo "<pre>"; print_r($farmers); echo "</pre>"; exit;
        $data       = array();
        $bu         = array();
        $farmer     = array();
        $NamaPerwakilan = '';
        $FarmerID = '';
        $PerwakilanOrgID = '';
        foreach ($farmers as $key => $value) {
            if ($NamaPerwakilan != $value['orgname']) {
                $bu[$value['orgname']] = array();
                $NamaPerwakilan = $value['orgname'];
            }
        }
        foreach ($farmers as $key => $value) {
            if ($FarmerID != $value['id'].'-'.$value['orgid'] ) {
                $farmer[] = array(
                    'farmer'          => $value['farmer'],
                    'orgname'   => $value['orgname'],
                );
                $FarmerID = $value['id'].'-'.$value['orgid'];
            }
        }
        foreach ($bu as $key => $value) {
            foreach ($farmer as $k => $v) {
                if ($key = $v['orgname']) {
                    $bu[$key][$v['farmer']] = array();
                }
            }
        }
        foreach ($farmers as $key => $value) {
            $bu[$value['orgname']][$value['farmer']][] = $value;
        }

        return $bu;
    }

    private function process_farmers_data_cpg($farmers)
    {
        $data               = array();
        $cpgs               = array();
        // get all cpgs
        foreach ($farmers as $key => $value) {
            if (!in_array($value['cpg'], $cpgs)) {
                $cpgs[] = $value['cpg'];
                $data[$value['cpg']] = array();
            }
        }
        $cpgs = $data;
        // group farmer to cpgs
        foreach ($farmers as $key => $value) {
            if (!in_array($value['id'], $cpgs[$value['cpg']])) {
                $cpgs[$value['cpg']][] = $value['id'];
                $data[$value['cpg']][$value['farmer']] = array();
            }
        }
        //
        foreach ($farmers as $key => $value) {
            $data[$value['cpg']][$value['farmer']][] = $value;
        }

        return $data;
    }

    function premium_cetak_detail_get($type, $start, $end, $jenis, $provinsi, $warehouse, $buyingunit, $sert, $all = '', $format = '')
    {
         // $data['detail'] = $this->mdata->getPremiumPetanii($start,$end,$jenis);
        set_time_limit(0);
        if($all=='true'){
            $buyingunit = 0;
        }
        $data['awal'] = $start;
        $data['akhir'] = $end;
        $data['details'] = $this->mdata->getDetailsPetani(
            $start,
            $end,
            $jenis,
            $buyingunit,
            $provinsi,
            $warehouse,
            $sert,
            0,
            100000,
            $type,$format
        );
        $data['logo'] = $this->mdata->getReportLogo($start,$warehouse);
        //print_r($data);exit;
        if (substr($type,-3,3) == 'cpg') {
            $data['detail'] = $this->process_farmers_data_cpg($data['details']['data']);
        } else {
            $data['detail'] = $this->process_farmers_data_bu($data['details']['data']);
        }

        $data['premium'] = $this->mdata->getPremiumByOrg($warehouse,$start);
        $usd    = $data['premium']['PersenPetani']/100*$data['premium']['USD'];
        $rp     = $usd*$data['premium']['Kurs'];
        $data['usd']    = $usd;
        $data['rp']     = $rp;

        if ($format == 'excel') {
            set_time_limit(0);
            //echo "<pre>".print_r($data['details'],1)."</pre>";exit;
            //**//
            switch ($type) {
                case 'details':
                    $filename                   = 'list_farmers_details.xlsx';
                    $title                      = 'Rincian';
                    $header                     = 'Rincian Transaksi Premium per Petani';
                    break;
                case 'summary':
                    $filename                   = 'list_farmers_summary.xlsx';
                    $title                      = 'Rekapitulasi';
                    $header                     = 'Rekapitulasi Premium per Petani';
                    break;
                case 'detailscpg':
                    $filename                   = 'list_cpg_details.xlsx';
                    $title                      = 'Rincian';
                    $header                     = 'Rincian Transaksi Premium per Petani per CPG';
                    break;
                case 'summarycpg':
                    $filename                   = 'list_cpg_summary.xlsx';
                    $title                      = 'Rekapitulasi';
                    $header                     = 'Rekapitulasi Premium per CPG';
                    break;

                default:
                    $filename                   = 'list_farmers_details.xlsx';
                    $title                      = 'Rincian';
                    $header                     = 'Rincian Transaksi Premium per Petani';
                    break;
            }
            $this->load->library('Excel', null, 'PHPExcel');
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
            $object = new PHPExcel();

            // Set properties
            $object->getProperties()->setCreator("Koltiva Cocoatrace")
                           ->setLastModifiedBy("Koltiva Cocoatrace")
                           ->setCategory("Koltiva Cocoatrace");
            // Add some data
            $object->getActiveSheet()->getColumnDimension('A')->setWidth(50);
            $object->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('L')->setWidth(25);
            $object->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $object->getActiveSheet()->mergeCells('A1:M1');
            $object->getActiveSheet()->mergeCells('A2:M2');
            $object->getActiveSheet()->mergeCells('A5:M5');
            $style_center = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );

            $object->getActiveSheet()->getStyle("A1:M4")->applyFromArray($style_center);
            $style_border = array(
                  'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                      )
                  )
              );
            $style_background = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF00')
                )
            );
            $object->getActiveSheet()->getStyle("A4:M4")->applyFromArray($style_border);
            $object->getActiveSheet()->getStyle("A5")->applyFromArray($style_border);
            $object->getActiveSheet()->getStyle("A1:M4")->getFont()->setBold(true);
            $object->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
            $object->setActiveSheetIndex(0)
                        ->setCellValue('A1', $header)
                        ->setCellValue('A2', 'Premium : IDR '.number_format($rp,0,'.',',').' | USD '.$usd)
                        ->setCellValue('A4', 'Name')
                        ->setCellValue('B4', 'Survey Volume (Kg)')
                        ->setCellValue('C4', 'Quota (Survey+10%)')
                        ->setCellValue('D4', 'Bruto (Kg)')
                        ->setCellValue('E4', 'Netto (Kg)')
                        ->setCellValue('F4', 'Balance (Kg)')
                        ->setCellValue('G4', 'Total (IDR)')
                        ->setCellValue('H4', 'Total (USD)')
                        ->setCellValue('I4', 'Paid (Kg)')
                        ->setCellValue('J4', 'Unpaid (Kg)')
                        ->setCellValue('K4', 'Paid (USD)')
                        ->setCellValue('L4', 'Unpaid (USD)')
                        ->setCellValue('M4', 'No. PO')
                        ->setCellValue('A5', $data['detail'][0]['name_b']);
            $counter=5;
            $group_by = '';
            $group_by_details = '';
            $quota = '';
            foreach ($data['details']['data'] as $key => $value) {
                //$object->getActiveSheet()->getStyle("B$counter:L$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                //$object->getActiveSheet()->getStyle("A$counter:L$counter")->applyFromArray($style_border);
                if($type=="summary"){
                    $check = $data['details']['data'][$key]['ids'];
                    $name = $data['details']['data'][$key]['name'];
                    if($group_by!=$check){ //baru
                        $counter++;
                        $group_by = $check;
                        $bruto = $data['details']['data'][$key]['bruto'];
                        $netto = $data['details']['data'][$key]['netto'];
                        $balance = $data['details']['data'][$key]['balance'];
                        $totalidr = $data['details']['data'][$key]['totalidr'];
                        $totalusd = $data['details']['data'][$key]['totalusd'];
                        $paidkg = $data['details']['data'][$key]['paidkg'];
                        $unpaidkg = $data['details']['data'][$key]['unpaidkg'];
                        $paidusd = $data['details']['data'][$key]['paidusd'];
                        $unpaidusd = $data['details']['data'][$key]['unpaidusd'];
                    }else{ //jumlahkan
                        $bruto = $bruto + $data['details']['data'][$key]['bruto'];
                        $netto = $netto + $data['details']['data'][$key]['netto'];
                        $balance = $balance - $data['details']['data'][$key]['netto'];
                        $totalidr = $totalidr + $data['details']['data'][$key]['totalidr'];
                        $totalusd = $totalusd + $data['details']['data'][$key]['totalusd'];
                        $paidkg = $paidkg + $data['details']['data'][$key]['paidkg'];
                        $unpaidkg = $unpaidkg + $data['details']['data'][$key]['unpaidkg'];
                        $paidusd = $paidusd + $data['details']['data'][$key]['paidusd'];
                        $unpaidusd = $unpaidusd + $data['details']['data'][$key]['unpaidusd'];
                    }
                }else if($type=="details"){
                    $check = $data['details']['data'][$key]['ids'];
                    $name = $data['details']['data'][$key]['name'];
                    if($group_by!=$check){ //baru
                        $counter++;
                        $name = $data['details']['data'][$key]['name'];
                        //$object->getActiveSheet()->mergeCells("A$counter:L$counter");
                        if($type=='details' || $type=='detailscpg'){
                            $name2 = $name.', Survey Volume: '.number_format($data['details']['data'][$key]['quota'],2,'.','').' Kg';
                        }
                        $object->getActiveSheet()->setCellValue('A'.$counter, $name2);
                        $group_by = $check;
                        $balance = $data['details']['data'][$key]['balance'];
                    }else{
                        $balance = @$balance - $data['details']['data'][$key]['netto'];
                    }
                    $name = $data['details']['data'][$key]['datetransaction'];
                    $bruto = $data['details']['data'][$key]['bruto'];
                    $netto = $data['details']['data'][$key]['netto'];
                    $balance = @$balance;
                    $totalidr = $data['details']['data'][$key]['totalidr'];
                    $totalusd = $data['details']['data'][$key]['totalusd'];
                    $paidkg = $data['details']['data'][$key]['paidkg'];
                    $unpaidkg = $data['details']['data'][$key]['unpaidkg'];
                    $paidusd = $data['details']['data'][$key]['paidusd'];
                    $unpaidusd = $data['details']['data'][$key]['unpaidusd'];
                    $counter++;
                }else if($type=="summarycpg"){
                    $check = $data['details']['data'][$key]['cpg_id'];
                    $name = $data['details']['data'][$key]['cpg_name'];
                    if($group_by!=$check){ //baru
                        $counter++;
                        $group_by = $check;
                        $bruto = $data['details']['data'][$key]['bruto'];
                        $netto = $data['details']['data'][$key]['netto'];
                        $balance = $data['details']['data'][$key]['balance'];
                        $totalidr = $data['details']['data'][$key]['totalidr'];
                        $totalusd = $data['details']['data'][$key]['totalusd'];
                        $paidkg = $data['details']['data'][$key]['paidkg'];
                        $unpaidkg = $data['details']['data'][$key]['unpaidkg'];
                        $paidusd = $data['details']['data'][$key]['paidusd'];
                        $unpaidusd = $data['details']['data'][$key]['unpaidusd'];
                    }else{ //jumlahkan
                        $bruto = $bruto + $data['details']['data'][$key]['bruto'];
                        $netto = $netto + $data['details']['data'][$key]['netto'];
                        $balance = $balance - $data['details']['data'][$key]['netto'];
                        $totalidr = $totalidr + $data['details']['data'][$key]['totalidr'];
                        $totalusd = $totalusd + $data['details']['data'][$key]['totalusd'];
                        $paidkg = $paidkg + $data['details']['data'][$key]['paidkg'];
                        $unpaidkg = $unpaidkg + $data['details']['data'][$key]['unpaidkg'];
                        $paidusd = $paidusd + $data['details']['data'][$key]['paidusd'];
                        $unpaidusd = $unpaidusd + $data['details']['data'][$key]['unpaidusd'];
                    }
                }else if($type=="detailscpg"){
                    $check = $data['details']['data'][$key]['cpg_id'];
                    if($group_by!=$check){ //baru
                        $counter++;
                        $name = $data['details']['data'][$key]['cpg_name'];
                        //$object->getActiveSheet()->mergeCells("A$counter:L$counter");
                        $object->getActiveSheet()->setCellValue('A'.$counter, $name);
                        $group_by = $check;
                    }
                    $check_details = $data['details']['data'][$key]['ids'];
                    $name = $data['details']['data'][$key]['name'];
                    if($group_by_details!=$check_details){ //baru
                        $counter++;
                        $group_by_details = $check_details;
                        $bruto = $data['details']['data'][$key]['bruto'];
                        $netto = $data['details']['data'][$key]['netto'];
                        $balance = $data['details']['data'][$key]['balance'];
                        $totalidr = $data['details']['data'][$key]['totalidr'];
                        $totalusd = $data['details']['data'][$key]['totalusd'];
                        $paidkg = $data['details']['data'][$key]['paidkg'];
                        $unpaidkg = $data['details']['data'][$key]['unpaidkg'];
                        $paidusd = $data['details']['data'][$key]['paidusd'];
                        $unpaidusd = $data['details']['data'][$key]['unpaidusd'];
                    }else{ //jumlahkan
                        $bruto = $bruto + $data['details']['data'][$key]['bruto'];
                        $netto = $netto + $data['details']['data'][$key]['netto'];
                        $balance = $balance - $data['details']['data'][$key]['netto'];
                        $totalidr = $totalidr + $data['details']['data'][$key]['totalidr'];
                        $totalusd = $totalusd + $data['details']['data'][$key]['totalusd'];
                        $paidkg = $paidkg + $data['details']['data'][$key]['paidkg'];
                        $unpaidkg = $unpaidkg + $data['details']['data'][$key]['unpaidkg'];
                        $paidusd = $paidusd + $data['details']['data'][$key]['paidusd'];
                        $unpaidusd = $unpaidusd + $data['details']['data'][$key]['unpaidusd'];
                    }
                }

                if(number_format($balance,2,'.','') < 0){
                    $object->getActiveSheet()->getStyle("A$counter:M$counter")->applyFromArray($style_background);
                }
                
                $object->getActiveSheet()->setCellValue('A'.$counter, $name);
                $object->getActiveSheet()->setCellValue('B'.$counter, number_format($data['details']['data'][$key]['survey'],2,'.',''));
                $object->getActiveSheet()->setCellValue('C'.$counter, number_format($data['details']['data'][$key]['quota'],2,'.',''));
                $object->getActiveSheet()->setCellValue('D'.$counter, number_format($bruto,2,'.',''));
                $object->getActiveSheet()->setCellValue('E'.$counter, number_format($netto,4,'.',''));
                $object->getActiveSheet()->setCellValue('F'.$counter, number_format($balance,2,'.',''));
                $object->getActiveSheet()->setCellValue('G'.$counter, number_format($totalidr,2,'.',''));
                $object->getActiveSheet()->setCellValue('H'.$counter, number_format($totalusd,2,'.',''));
                $object->getActiveSheet()->setCellValue('I'.$counter, number_format($paidkg,2,'.',''));
                $object->getActiveSheet()->setCellValue('J'.$counter, number_format($unpaidkg,2,'.',''));
                $object->getActiveSheet()->setCellValue('K'.$counter, number_format($paidusd,2,'.',''));
                $object->getActiveSheet()->setCellValue('L'.$counter, number_format($unpaidusd,2,'.',''));
                $object->getActiveSheet()->setCellValue('M'.$counter, $data['details']['data'][$key]['nopo']);
            }
            if($type=='details'){
                $object->getActiveSheet()->getColumnDimension('B')->setVisible(FALSE);
                $object->getActiveSheet()->getColumnDimension('C')->setVisible(FALSE);
            }
            $object->getActiveSheet()->getColumnDimension('M')->setVisible(FALSE);
            $object->getActiveSheet()->getStyle("B6:L$counter")->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle("A6:M$counter")->applyFromArray($style_border);

            $konter = $counter;
            $konter = $konter+2; 
            $object->getActiveSheet()->setCellValue('A'.$konter, "Total");
            $object->getActiveSheet()->getStyle("B$konter:L$konter")->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->setCellValue('B'.$konter, "=SUM(B5:B$counter)");
            $object->getActiveSheet()->setCellValue('C'.$konter, "=SUM(C5:C$counter)");
            $object->getActiveSheet()->setCellValue('D'.$konter, "=SUM(D5:D$counter)");
            $object->getActiveSheet()->setCellValue('E'.$konter, "=SUM(E5:E$counter)");
            $object->getActiveSheet()->setCellValue('F'.$konter, "=SUM(F5:F$counter)");
            $object->getActiveSheet()->setCellValue('G'.$konter, "=SUM(G5:G$counter)");
            $object->getActiveSheet()->setCellValue('H'.$konter, "=SUM(H5:H$counter)");
            $object->getActiveSheet()->setCellValue('I'.$konter, "=SUM(I5:I$counter)");
            $object->getActiveSheet()->setCellValue('J'.$konter, "=SUM(J5:J$counter)");
            $object->getActiveSheet()->setCellValue('K'.$konter, "=SUM(K5:K$counter)");
            $object->getActiveSheet()->setCellValue('L'.$konter, "=SUM(L5:L$counter)");
            // Rename sheet
            //$object->getActiveSheet()->setTitle('Laporan Penjualan per Buying Unit');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $object->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename);
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $objWriter->save('php://output');
            exit;
            //**//

            $this->load->library('Excel', null, 'PHPExcel');
            switch ($type) {
                case 'details':
                    $filename                   = 'list_farmers_details.xls';
                    $sheet_data['title']        = 'Rincian';
                    $sheet_data['header'][]     = 'Rincian Transaksi Premium per Petani';
                    break;
                case 'summary':
                    $filename                   = 'list_farmers_summary.xls';
                    $sheet_data['title']        = 'Rekapitulasi';
                    $sheet_data['header'][]     = 'Rekapitulasi Premium per Petani';
                    break;
                case 'detailscpg':
                    $filename                   = 'list_farmers_details.xls';
                    $sheet_data['title']        = 'Rincian';
                    $sheet_data['header'][]     = 'Rincian Transaksi Premium per Petani per CPG';
                    break;
                case 'summarycpg':
                    $filename                   = 'list_farmers_summary.xls';
                    $sheet_data['title']        = 'Rekapitulasi';
                    $sheet_data['header'][]     = 'Rekapitulasi Premium per Petani per CPG';
                    break;

                default:
                    $filename                   = 'list_farmers_details.xls';
                    $sheet_data['title']        = 'Rincian';
                    $sheet_data['header'][]     = 'Rincian Transaksi Premium per Petani';
                    break;
            }
            $this->PHPExcel->filename($filename);

            // data modification
            // foreach ($data['detail'] as $key => $value) {
            //     $premium = $value['PersenPerwakilan']/100*$value['Rupiah'];

            //     $data['detail'][$key]['bruto'] = number_format($data['detail'][$key]['bruto'],2,',','.');
            //     $data['detail'][$key]['netto'] = number_format($data['detail'][$key]['netto'],2,',','.');
            //     $data['detail'][$key]['premium']    = number_format($premium,2,',','.');
            //     $data['detail'][$key]['total']      = number_format($value['netto']*$premium/1000,2,',','.');
            // }
            // // sheet
            $sheet_data['header'][]  = 'Premium : IDR '.number_format($rp,0,'.',',').' | USD '.$usd;
            if ($type == 'details' OR $type == 'detailscpg') {
                $sheet_data['cols'][] = array(
                    'name' => 'Name',
                    'data' => 'datetransaction',
                    'size' => 15,
                    'align'=> 'left',
                    'wrap' => true,
                    // 'type' => 'text',
                );
            } elseif ($type == 'summary' OR $type == 'summarycpg') {
                $sheet_data['cols'][] = array(
                    'name' => 'Name',
                    'data' => 'farmer',
                    'size' => 40,
                    'align'=> 'left',
                    // 'wrap' => true,
                    // 'type' => 'text',
                );
            }
            $sheet_data['cols']      = array_merge($sheet_data['cols'],array(
                // array(
                //     'name' => 'Header Column',   // Teks header table
                //     'data' => 'data_key',        // index key dari data
                //     'size' => 5,                 // size
                //     'align' => 'center'          // horizontal alignment
                //     'wrap' => true,              // wrap if too long
                //     'type' => 'text',            // set type to text, misal untuk menampilkan nomor telp 08637263872
                // ),
                // array(
                //     'name' => 'No',
                //     'data' => 'no',
                //     'size' => 5,
                //     'align' => 'center'
                // ),
                // array(
                //     'name' => 'Name',
                //     'data' => 'farmer',
                //     'size' => 40,
                //     'align'=> 'left',
                //     'wrap' => true,
                //     // 'type' => 'text',
                // ),
                array(
                    'name' => 'Survey Volume (Kg)',
                    'data' => 'survey',
                    'size' => 15,
                    'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                ),
                array(
                    'name' => 'Quota (Survey + 10%)',
                    'data' => 'quota',
                    'size' => 15,
                    'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                ),
                array(
                    'name' => 'Bruto (Kg)',
                    'data' => 'bruto',
                    'size' => 15,
                    'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                ),
                array(
                    'name' => 'Netto (Kg)',
                    'data' => 'netto',
                    'size' => 15,
                    'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                ),
                array(
                    'name' => 'Balance (Kg)',
                    'data' => 'balance',
                    'size' => 15,
                    'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                ),
            ));
            if ($type == 'detailscpg') {
                $sheet_data['cols'][] = array(
                    'name' => 'Perwakilan',
                    'data' => 'orgname',
                    'size' => 15,
                    // 'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                );
                $sheet_data['cols'][] = array(
                    'name' => 'Batch (PK)',
                    'data' => 'batch',
                    'size' => 10,
                    // 'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                );
            }
            $sheet_data['cols']      = array_merge($sheet_data['cols'],array(
                array(
                    'name' => 'TOTAL (IDR)',
                    'data' => 'totalidr',
                    'size' => 20,
                    'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                ),
                array(
                    'name' => 'TOTAL (USD)',
                    'data' => 'totalusd',
                    'size' => 12,
                    'align'=> 'right',
                    // 'wrap' => true,
                    // 'type' => 'text',
                ),array(
                    'name' => 'PAID (Kg)',
                    'data' => 'paidkg',
                    'size' => 12,
                    'align'=> 'right',
                    'wrap' => true,
                    // 'type' => 'text',

                ),array(
                    'name' => 'UNPAID (Kg)',
                    'data' => 'unpaidkg',
                    'size' => 12,
                    'align'=> 'right',
                    'wrap' => true,
                    // 'type' => 'text',

                ),array(
                    'name' => 'PAID (USD)',
                    'data' => 'paidusd',
                    'size' => 12,
                    'align'=> 'right',
                    'wrap' => true,
                    // 'type' => 'text',

                ),array(
                    'name' => 'UNPAID (USD)',
                    'data' => 'unpaidusd',
                    'size' => 12,
                    'align'=> 'right',
                    'wrap' => true,
                    // 'type' => 'text',

                )
            ));
            $sheet_data['data']      = $data['detail'];

            // $path = $this->PHPExcel->create(compact('sheet'), '');
            // header('Location: '.base_url().'list_buying_unit.xls');
            // exit;

            $last_row = 1;

            $sheet = $this->PHPExcel->getActiveSheet();
            $sheet->setTitle($sheet_data['title']);
            if (!empty($sheet_data['header'])) {
                foreach ($sheet_data['header'] as $header) {
                    $sheet
                        ->mergeCellsByColumnAndRow(0, $last_row, count($sheet_data['cols'])-1, $last_row)
                        ->setCellValueByColumnAndRow(0, $last_row, $header)->getStyleByColumnAndRow(0, $last_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyleByColumnAndRow(0, $last_row)->getFont()->setBold(true);
                    $last_row++;
                }
                $last_row++;
            }

            $first_table_row = $last_row;
            // set table header
            $last_col = 0;
            foreach ($sheet_data['cols'] as $col) {
                $sheet->getStyleByColumnAndRow($last_col, $last_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->setCellValueByColumnAndRow($last_col, $last_row, $col['name']);
                if ($col['size'])
                    $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($last_col))->setWidth($col['size']);
                $sheet->getStyleByColumnAndRow($last_col, $last_row)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyleByColumnAndRow($last_col, $last_row)->getFont()->setBold(true);
                $last_col++;
            }

            // set table data
            $first_data_row = $last_row + 1;
            $no = 1;

            if (!empty($sheet_data['data'])) {

                    foreach ($sheet_data['data'] as $bu => $farmers) {
                        $last_row++;
                        $last_col = 0;
                        $sheet
                            ->mergeCellsByColumnAndRow(0, $last_row, count($sheet_data['cols'])-1, $last_row)
                            ->setCellValueByColumnAndRow($last_col, $last_row, $bu);
                        $sheet->getStyleByColumnAndRow(0, $last_row)->getFont()->setBold(true);
                        $last_row++;

                        if ($type == 'details' OR $type == 'detailscpg') {

                            foreach ($farmers as $name => $farmer) {
                                $last_col = 0;
                                $sheet
                                ->mergeCellsByColumnAndRow(0, $last_row, count($sheet_data['cols'])-1, $last_row)
                                ->setCellValueByColumnAndRow($last_col, $last_row, $name);
                                // $sheet->getStyleByColumnAndRow(0, $last_row)->getFont()->setBold(true);
                                $last_row++;

                                foreach ($farmer as $key => $val) {
                                // modify data
                                    // foreach ($val as $k => $v) {
                                    //     if (is_numeric($v)) {
                                    //         $val[$k] = number_format($v,2,',','.');
                                    //     }
                                    // }

                                    if ($key == 0) {
                                        $Balance = $val['balance'];
                                    } else {
                                        $Balance -= $val['netto'];
                                    }
                                    $val['balance'] = $Balance;

                                    $last_col = 0;
                                    // parsing data
                                    foreach ($sheet_data['cols'] as $col) {
                                        if (!empty($col['type']) and $col['type'] == 'text')
                                            $sheet->setCellValueExplicitByColumnAndRow($last_col, $last_row, $val[$col['data']]);
                                        else {
                                            if (is_numeric($val[$col['data']])) {
                                                $val[$col['data']] = number_format($val[$col['data']],2,',','.');
                                            }
                                            $sheet->setCellValueByColumnAndRow($last_col, $last_row, $val[$col['data']]);
                                        }
                                        $last_col++;
                                    }
                                    $no++;
                                    $last_row++;
                                }
                            }
                        } elseif ($type == 'summary' OR $type == 'summarycpg') {
                            foreach ($farmers as $name => $farmer) {
                                $last_col = 0;
                                $val = array(
                                    'farmer'        => $name,
                                    'survey'        => $farmer[0]['survey'],
                                    'quota'        => $farmer[0]['quota'],
                                    'bruto'         => 0,
                                    'netto'         => 0,
                                    'balance'       => $farmer[0]['balance'],
                                    'totalidr'      => 0,
                                    'totalusd'      => 0,
                                    'paidkg'      => 0,
                                    'unpaidkg'      => 0,
                                    'paidusd'      => 0,
                                    'unpaidusd'      => 0,
                                );
                                // acumulate data
                                foreach ($farmer as $key => $value) {
                                    $val['bruto']       += $value['bruto'];
                                    $val['netto']       += $value['netto'];
                                    // $val['Balance']     += $value['Balance'];
                                    $val['totalidr']    += $value['totalidr'];
                                    $val['totalusd']    += $value['totalusd'];
                                    $val['paidkg']    += $value['paidkg'];
                                    $val['unpaidkg']    += $value['unpaidkg'];
                                    $val['paidusd']    += $value['paidusd'];
                                    $val['unpaidusd']    += $value['unpaidusd'];
                                }
                                $val['balance'] -= $val['netto'];
                                $val['balance'] += $farmer[0]['netto'];
                                // parsing data
                                foreach ($sheet_data['cols'] as $col) {
                                    if (!empty($col['type']) and $col['type'] == 'text')
                                        $sheet->setCellValueExplicitByColumnAndRow($last_col, $last_row, $val[$col['data']]);
                                    else {
                                        if (is_numeric($val[$col['data']])) {
                                            $val[$col['data']] = number_format($val[$col['data']],2,',','.');
                                        }
                                        $sheet->setCellValueByColumnAndRow($last_col, $last_row, $val[$col['data']]);
                                    }
                                    $last_col++;
                                }
                                $no++;
                                $last_row++;
                            }
                        }

                    }
                // format collumns
                $last_col = 0;
                foreach ($sheet_data['cols'] as $col) {
                    $col_string = PHPExcel_Cell::stringFromColumnIndex($last_col);
                    $sheet->getStyle($col_string.$first_data_row.':'.$col_string.$last_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                    if (!empty($col['align'])) {
                        switch ($col['align']) {
                            case 'center':
                            $align = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                            break;
                            case 'left':
                            $align = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                            break;
                            case 'right':
                            $align = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                            break;
                            case 'justify':
                            $align = PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY;
                            break;
                        }
                        $sheet->getStyle($col_string.$first_data_row.':'.$col_string.$last_row)->getAlignment()->setHorizontal($align);
                    }
                    if (!empty($col['wrap']) and $col['wrap'] == true) {
                        $sheet->getStyle($col_string.$first_data_row.':'.$col_string.$last_row)->getAlignment()->setWrapText(true);
                    }
                    $last_col++;
                }
            }
            $last_data_row = $last_row;

            $border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
            $sheet->getStyle('A' . $first_table_row . ':' . (PHPExcel_Cell::stringFromColumnIndex(count($sheet_data['cols']) - 1)) . ($last_data_row))->applyFromArray($border);

            $this->PHPExcel->save();
            exit;
        }
        $data['type'] = $type;
         //print_r($data);exit;
        if ($type == 'details') {
            $this->load->view('premium_farmer_detail_cetak', $data);
        } elseif ($type == 'detailscpg') {
            $this->load->view('premium_farmer_detailcpg_cetak', $data);
        } elseif ($type == 'summary' OR $type == 'summarycpg') {
            if($type=='summary'){
                $data['judul'] = 'Summary Transaction per Farmer';
            }else{
                $data['judul'] = 'Summary Transaction Farmer per CPG';
            }
            $this->load->view('premium_farmer_summary_cetak', $data);
        }
    }

   //Laporan Premium
    function tpremiums_get() {
        $data = $this->mdata->readTransPremiums($this->get('jenis'),$this->get('district'),$this->get('key'),
            $this->get('awal'),$this->get('akhir'),$this->get('cpg'),$this->get('paid'),$this->get('start'),$this->get('limit'));
        $this->response($data, 200);
    }

    function tpremium_get() {
        $data = $this->mdata->readTransPremium($this->get('id'),$this->get('jenis'),$this->get('awal'),$this->get('akhir'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function tpremium_district_get() {
        $data = $this->mdata->readDistrict();
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    /**
     * Tambahan filter by CPG
     */
    function tpremium_cpg_get() {
        $data = $this->mdata->readCPG($this->get('district'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    /**
     * Menghitung detail pembayaran premium menggunakan table rpt_traceability
     * @author Ardi <ardiantoro@koltiva.com>
     */
    function tpremiums_detail_get() {
        $data = false;
        $data = $this->mdata->readTransPremiumDetailsRpt($this->get('id'),$this->get('jenis'),$this->get('district'),
                $this->get('awal'),$this->get('akhir'));
        if ($data) { $this->response($data, 200); }
        else { $this->response(array('error' => 'Couldn\'t find any datas!'), 404); }
    }

    function tpremiums_detail_old_get() {
        $data = $this->mdata->readTransPremiumDetails($this->get('id'),$this->get('jenis'),$this->get('district'),
            $this->get('awal'),$this->get('akhir'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function tpremiums_kuitansi_get() {
        $data = $this->mdata->readTransPremiumKuitansis($this->get('id'),$this->get('jenis'),$this->get('awal'),$this->get('akhir'));
        if ($data)
        $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function tpremium_post() {
        if (!$this->post('id')) $this->response(NULL, 400);
        $data = $this->mdata->createTransPremium($this->post('id'), $this->post('pemberi_id'),
            $this->post('penerima_type'), $this->post('penerima_id'), $this->post('bruto'), $this->post('netto'),
            $this->post('premium'), $_SESSION['userid'],$this->post('berat'),$this->post('premi'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function tpremium_put() {
        if (!$this->post('PremiumDateStart')) $this->response(NULL, 400);
        $data = $this->mdata->createPremium($this->post('PremiumSupplychainID'), $this->post('PremiumDateStart'),
            $this->post('PremiumDateEnd'), $this->post('PersenPetani'), $this->post('PersenBuyinUnit'), $this->post('PersenPerwakilan'),$this->post('USD'),
            $this->post('Kurs'), $this->post('Rupiah'), $_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function kuitansi_massal_cetak_get($jenis,$nama,$cpg,$awal,$akhir) {
         $data = $this->mdata->getKuitansiNomorBySearch($nama,$cpg,$awal,$akhir);
//         print_r($data);exit;
         for ($i=0;$i<sizeof($data);$i++) {
            if ($jenis=='biasa') $this->tpremium_cetak_get($data[$i]['PaymentNumber']);
            else $this->tpremium_rincian_cetak_get($data[$i]['PaymentNumber']);
         }
    }
    function tpremium_cetak_get($nomor) {
         $data = $this->mdata->getKuitansiByNomor($nomor);
         $this->load->view('kuitansi_premium_cetak', $data);
    }
    function tpremium_rincian_cetak_get($nomor) {
         $data = $this->mdata->getKuitansiByNomor($nomor);
         $this->load->view('kuitansi_premium_rincian_cetak', $data);
    }
    // pedagang (BU)
    function viewreport_get(){
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        $data = $this->mdata->getPremiumBuv(
            $dStart[0],
            $dEnd[0],
            $this->get('jenis'),
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('sert'),
            $this->get('coopID')
        );
        // echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; exit;
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
        //$this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    // farmer
    function viewreportf_get(){
        $dStart = explode('T', $this->get('startd'));
        $dEnd = explode('T', $this->get('end'));
        // $data['detail'] = $this->mdata->getPremiumPetanii($start,$end,$jenis);
        //$data = $this->mdata->getDetailsPetani(
        $data = $this->mdata->getDetailsPetani(
            $dStart[0],
            $dEnd[0],
            $this->get('jenis'),
            $this->get('buid'),
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('sert'),
            $this->get('start'),
            $this->get('limit')
        );
        $tmpData = array();
        $resData = array();
        foreach($data['data'] as $index => $record){
            if(key_exists($record['ids'], $tmpData)){
                $minAr  = min(array_keys($tmpData[$record['ids']]));
                $ar     = $tmpData[$record['ids']][$minAr];
                $tmp    = (int) $ar["balance"] - $record['netto'];
                $tmpData[$record['ids']][$tmp] = array(
                    "balance"=>$ar['balance'] - $record['netto']
                );
                $balance = $ar['balance'] - $record['netto'];
            } else {
                $tmpData[$record['ids']][$record['survey']] = array(
                    "balance"=>$record['quota'] - $record['netto']
                );
                $balance = $record['quota'] - $record['netto'];
            }
            $data['data'][$index]['balance'] = $balance;
        }
        $retData = array();
        if(count($data['data'])) {
            $retData = array();
            $retData['data'] = $data['data'];
            $retData['total'] = $data['total'];
        }else {
            //$data = array();
            $retData['data'] = array();
            $retData['total'] = 0;
        }
        //echo "<pre>".print_r($retData,1);exit;
        if($this->get('tot')=='1'){
            $surveys = 0;
            $quotas = 0;
            $brutos = 0;
            $nettos = 0;
            $totalidrs = 0;
            $totalusds = 0;
            $paidkgs = 0;
            $unpaidkgs = 0;
            $paidusds = 0;
            $unpaidusds = 0;
            $balances = 0;
            $farmers = 0;
            foreach ($retData['data'] as $key => $val) {
                if($farmers!=$val['ids']){
                    $balances = $balances + $val['balance'];
                    $surveys = $surveys + $val['survey'];
                    $quotas = $quotas + $val['quota'];
                    $farmers = $val['ids'];    
                }
                
                $brutos = $brutos + $val['bruto'];
                $nettos = $nettos + $val['netto'];
                $totalidrs = $totalidrs + $val['totalidr'];
                $totalusds = $totalusds + $val['totalusd'];
                $paidkgs = $paidkgs + $val['paidkg'];
                $unpaidkgs = $unpaidkgs + $val['unpaidkg'];
                $paidusds = $paidusds + $val['paidusd'];
                $unpaidusds = $unpaidusds + $val['unpaidusd'];
            }
            unset($retData['data']);
            $retData['data'][0]['nopo'] = '';
            $retData['data'][0]['datetransaction'] = 'Total Farmer Details';
            $retData['data'][0]['ids'] = '';
            $retData['data'][0]['farmer'] = '';
            $retData['data'][0]['name'] = '';
            $retData['data'][0]['survey'] = $surveys;
            $retData['data'][0]['bruto'] = $brutos;
            $retData['data'][0]['quota'] = $quotas;
            $retData['data'][0]['netto'] = $nettos;
            $retData['data'][0]['totalusd'] = $totalusds;
            $retData['data'][0]['totalidr'] = $totalidrs;
            $retData['data'][0]['balance'] = '';//$balances;
            $retData['data'][0]['orgid'] = '';
            $retData['data'][0]['batch'] = '';
            $retData['data'][0]['paidkg'] = $paidkgs;
            $retData['data'][0]['unpaidkg'] = $unpaidkgs;
            $retData['data'][0]['paidusd'] = $paidusds;
            $retData['data'][0]['unpaidusd'] = $unpaidusds;
            $retData['data'][0]['cpg_id'] = '';
            $retData['data'][0]['cpg_name'] = '';
        }
        $this->response($retData, 200);
        //else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }
    // koperasi
    function viewreportc_get(){
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        // $data['detail'] = $this->mdata->getPremiumKoperasi($start,$end,$jenis);
        $data = $this->mdata->getDetailsKoperasi(
            $dStart[0],
            $dEnd[0],
            $this->get('jenis'),
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('sert')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    // cetak rekap traceability
    function rekap_penjualan_cetak_get($farmerId,$partner,$jenis) {
        $jenis = str_replace('%20', ' ', $jenis);
        $data = $this->mdata->getRekapPenjualan($farmerId,$partner,$jenis);
		$this->load->model('farmer/mfarmer');
		$data['logos'] = $this->mfarmer->readPartnerLogo($farmerId,$partner);
        $this->load->view('cetak_rekap_traceability', $data);
    }

    function premium_org_get($orgid = '',$date='')
    {
        $orgid = $this->get('orgid');
        $data = $this->mdata->getPremiumByOrg($orgid,$this->get('date'));
        if ($data) {
            $this->response($data);
        } else {
            $this->response(array(
                'PersenPetani'          => 0,
                'PersenBuyinUnit'       => 0,
                'PersenPerwakilan'      => 0,
                'USD'                   => 0,
                'Kurs'                  => 0,
            ), 200);
        }
    }


   //Invoice
    function invoices_get() {
        $data = $this->mdata->readInvoices($this->get('key'),$this->get('awal'),$this->get('akhir'));
        $this->response($data, 200);
    }

    function invoice_get() {
        $data = $this->mdata->readInvoice($this->get('id'),$this->get('jenis'),$this->get('awal'),$this->get('akhir'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function invoices_detail_get() {
        $data = $this->mdata->readInvoiceDetails($this->get('id'),$this->get('awal'),$this->get('akhir'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function invoices_invoice_get() {
        $data = $this->mdata->readInvoiceInvoices($this->get('id'),$this->get('awal'),$this->get('akhir'));
        $this->response($data, 200);
    }

    function invoice_bank_get() {
        $data = $this->mdata->readInvoiceBanks($this->get('id'));
        $this->response($data, 200);
    }

    function invoice_post() {
        if (!$this->post('id')) $this->response(NULL, 400);
        $data = $this->mdata->createInvoice($this->post('id'), $this->post('pemberi_id'), $this->post('penerima_id'),
            $this->post('berat'),$this->post('total'),$this->post('karung'), $this->post('bank_id'),$this->post('bank_rekening'),
            $this->post('bank_an'),$this->post('berats'),$this->post('hargas'),$this->post('karungs'), $_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function invoice_bayar_post() {
        if (!$this->post('nomor')) $this->response(NULL, 400);
        $data = $this->mdata->bayarInvoice($this->post('nomor'), $_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function invoice_put() {
        if (!$this->post('PremiumDateStart')) $this->response(NULL, 400);
        $data = $this->mdata->createPremium($this->post('PremiumSupplychainID'), $this->post('PremiumDateStart'),
            $this->post('PremiumDateEnd'), $this->post('PersenPetani'), $this->post('PersenBuyinUnit'), $this->post('PersenPerwakilan'),$this->post('USD'),
            $this->post('Kurs'), $this->post('Rupiah'), $_SESSION['userid']);
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function invoice_massal_cetak_get($jenis,$nama,$cpg,$awal,$akhir) {
         $data = $this->mdata->getInvoiceNomorBySearch($nama,$cpg,$awal,$akhir);
//         print_r($data);exit;
         for ($i=0;$i<sizeof($data);$i++) {
            if ($jenis=='biasa') $this->invoice_cetak_get($data[$i]['PaymentNumber']);
            else $this->invoice_rincian_cetak_get($data[$i]['PaymentNumber']);
         }
    }
    function invoice_cetak_get($nomor) {
         $data = $this->mdata->getInvoiceByNomor($nomor);
         $this->load->view('invoice_cetak', $data);
    }
    function invoice_rincian_cetak_get($nomor) {
         $data = $this->mdata->getInvoiceByNomor($nomor);
         $this->load->view('invoice_rincian_cetak', $data);
    }

//area
    function areas_get() {
        if(!$this->get('SupplychainID')) $this->response(NULL, 400);
        $data = $this->mdata->readDatasArea($this->get('SupplychainID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function area_post() {
        if(!$this->post('DistrictID')) $this->response(NULL, 400);
        $data = $this->mdata->createDataArea($this->post('SupplychainID'),$this->post('DistrictID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function area_put() {
        if(!$this->put('AreaID')) $this->response(NULL, 400);
        $data = $this->mdata->updateDataArea($this->put('DistrictID')==''?$this->put('District'):$this->put('DistrictID'),
            $this->put('AreaID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }
    function area_delete() {
        if(!$this->delete('AreaID')) $this->response(NULL, 400);
        $data = $this->mdata->deleteDataArea($this->delete('AreaID'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }

    function transaction_setquality_get($supplychainid,$transid='') {
        $data = $this->mdata->readSetQuality($supplychainid,$transid);
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    //quality
    function var_quality_get() {
        $data = $this->mdata->readVarQualitys($this->get('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function var_quality_post() {
        if (!$this->post('Name')) $this->response(NULL, 400);
        $data = $this->mdata->createVarQuality($this->post('StandardID'), $this->post('Name'),$this->post('Order'),
            $this->post('FAQFormula'), $this->post('FFFormula'),$this->post('FAQValue'),$this->post('FFValue'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function var_quality_put() {
        if (!$this->put('Name')) $this->response(NULL, 400);
        $data = $this->mdata->updateVarQuality($this->put('Name'), $this->put('Order'), $this->put('FAQFormula'),
            $this->put('FFFormula'),$this->put('FAQValue'), $this->put('FFValue'), $this->put('DetailID'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be found'), 404);
    }

    function var_quality_delete() {
        if (!$this->delete('id')) $this->response(NULL, 400);
        $data = $this->mdata->deleteVarQuality($this->delete('id'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'data could not be delete'), 404);
    }

	function transaction_packing_check_post() {
        $data = $this->mtransaction->checkDatasPackingList($this->post('id'),$this->post('SupplychainID'));
        $this->response($data, 200); // 200 being the HTTP response code
    }

	function transaction_check_farmer_quota_get(){
		$data = $this->mtransaction->readFarmers($_SESSION['userid'], $this->get('query'), $this->get('start'), $this->get('limit'),
            $this->get('noncert'));
		$batas_atas = $data['data'][0]['batas_atas'];
        $batch = $this->mtransaction->readBatchQuota($this->get('SupplyBatchID'));
        if($batch['SupplyDestOrgQuota']=='0' && $batch['SupplyOrgQuota']=='0'){
            $jual = 0;
        }else{
            $jual = $data['data'][0]['jual'];
        }
		$sisa = number_format($batas_atas,2,",","") - number_format($jual,2,",","");
		$detail = $this->mtransaction->readDetailTransaction($this->get('SupplyTransID'));


		$hasil = number_format($sisa,2,",","") - number_format($detail,2,",","") - number_format($this->get('Weight'),2,",","") + number_format($this->get('defaultWeight'),2,",","");
		$maks = number_format($sisa,2,",","") - number_format($detail,2,",","") + number_format($this->get('defaultWeight'),2,",","");
		$data['data'][0]['hasil'] = $hasil;
		$data['data'][0]['maks'] = $maks;

        if($this->get('query')=='' && $this->get('SupplyBatchID')!="" && $this->get('SupplyTransID')!=""){ //skip yg penghitungan berat penjualan dari BS ke Coop krn gk hitung quota petani lagi
            $data['data'][0]['hasil'] = number_format(100000,2,",","");
            $data['data'][0]['maks'] = '-';            
        }


		$this->response($data['data'][0], 200);
	}

    //end quality

    function del_delete($id) {

        //di cek dulu yak, udah dipake di transaksi ato belum
        $check = $this->mdata->check_for_usage($id);

        //kalo belum ada yang pake, langsung hapus aja
        if($check) {
            $del = $this->mdata->delete_supplychain($id);

            if($del){
                $this->response(array('success' => true),200);
            }
        } else {
            $this->response(array('success' => false, 'error' => 'Data sudah digunakan'),200);
        }
    }

    function viewreportw_get(){
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        // $data['detail'] = $this->mdata->getPremiumKoperasi($start,$end,$jenis);
        $data = $this->mdata->getReportTraceabilityWarehouse(
            $dStart[0],
            $dEnd[0],
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('sert')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }

    function viewreportwtrans_get(){
        $dStart = explode('T', $this->get('startd'));
        $dEnd = explode('T', $this->get('end'));
        // $data['detail'] = $this->mdata->getPremiumKoperasi($start,$end,$jenis);
        $data = $this->mdata->getReportTraceabilityWarehouseTrans(
            $dStart[0],
            $dEnd[0],
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('sert'),
            $this->get('start'),
            $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    
    function viewreportcooptrans_get(){
        $dStart = explode('T', $this->get('startd'));
        $dEnd = explode('T', $this->get('end'));
        // $data['detail'] = $this->mdata->getPremiumKoperasi($start,$end,$jenis);
        $data = $this->mdata->getReportTraceabilityCoopTrans(
            $dStart[0],
            $dEnd[0],
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('coop'),
            $this->get('sert'),
            $this->get('start'),
            $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }

    function viewreportcoop_get(){
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        // $data['detail'] = $this->mdata->getPremiumKoperasi($start,$end,$jenis);
        $data = $this->mdata->getReportTraceabilityCoop(
            $dStart[0],
            $dEnd[0],
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('sert'),
            $this->get('coop')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    
    function viewreportbutrans_get(){
        $dStart = explode('T', $this->get('startd'));
        $dEnd = explode('T', $this->get('end'));
        $data = $this->mdata->getReportTraceabilityBuTrans(
            $dStart[0],
            $dEnd[0],
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('coop'),
            $this->get('bu'),
            $this->get('sert'),
            $this->get('start'),
            $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }

    function viewreportbu_get(){
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        // $data['detail'] = $this->mdata->getPremiumKoperasi($start,$end,$jenis);
        $data = $this->mdata->getReportTraceabilityBu(
            $dStart[0],
            $dEnd[0],
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('sert'),
            $this->get('coop'),
            $this->get('bu')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    
    function viewreportfarmertrans_get(){
        $dStart = explode('T', $this->get('startd'));
        $dEnd = explode('T', $this->get('end'));
        $data = $this->mdata->getReportTraceabilityFarmerTrans(
            $dStart[0],
            $dEnd[0],
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('coop'),
            $this->get('bu'),
            $this->get('farmer'),
            $this->get('sert'),
            $this->get('start'),
            $this->get('limit')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }

    function viewreportfarmer_get(){
        $dStart = explode('T', $this->get('start'));
        $dEnd = explode('T', $this->get('end'));
        // $data['detail'] = $this->mdata->getPremiumKoperasi($start,$end,$jenis);
        $data = $this->mdata->getReportTraceabilityFarmer(
            $dStart[0],
            $dEnd[0],
            $this->get('provinsi'),
            $this->get('warehouse'),
            $this->get('sert'),
            $this->get('coop'),
            $this->get('bu'),
            $this->get('farmer')
        );
        if($data) $this->response($data, 200);
        else {
            $data = array();
            $this->response($data, 200);
        }
    }
    
    function print_report_get()
    {
        $data['jenis'] = $this->get('jenisReport');
        $data['logo'] = $this->mdata->getReportLogo($this->get('start'),$this->get('wh'));
        if($this->get('jenisReport')=='wh'){
            $data['title'] = "Report Traceability<br>[Warehouse - ".$this->get('whname')."]";
            $details = $this->mdata->getReportTraceabilityWarehouseTrans(
                $this->get('start'),
                $this->get('end'),
                $this->get('prov'),
                $this->get('wh'),
                $this->get('sert')
            );
        }else if($this->get('jenisReport')=='coop'){
            $data['title'] = "Report Traceability<br>[Cooperative]";
            $details = $this->mdata->getReportTraceabilityCoopTrans(
                $this->get('start'),
                $this->get('end'),
                $this->get('prov'),
                $this->get('wh'),
                $this->get('coop'),
                $this->get('sert')
            );
            $surveys = $this->mdata->getReportTraceabilityCoop(
                $this->get('start'),
                $this->get('end'),
                $this->get('prov'),
                $this->get('wh'),
                $this->get('sert'),
                $this->get('coop')
            );
            
        }else if($this->get('jenisReport')=='bu'){
            $data['title'] = "Report Traceability<br>[Buying Unit (Trader / SCE)]";
            $details = $this->mdata->getReportTraceabilityBuTrans(
                $this->get('start'),
                $this->get('end'),
                $this->get('prov'),
                $this->get('wh'),
                $this->get('coop'),
                $this->get('bu'),
                $this->get('sert')
            );
            $surveys = $this->mdata->getReportTraceabilityBu(
                $this->get('start'),
                $this->get('end'),
                $this->get('prov'),
                $this->get('wh'),
                $this->get('sert'),
                $this->get('coop'),
                $this->get('bu')
            );
        }else if($this->get('jenisReport')=='farmer'){
            $data['title'] = "Report Traceability<br>[Farmer Details]";
            $details = $this->mdata->getReportTraceabilityFarmerTrans(
                $this->get('start'),
                $this->get('end'),
                $this->get('prov'),
                $this->get('wh'),
                $this->get('coop'),
                $this->get('bu'),
                $this->get('farmer'),
                $this->get('sert')
            );
        }
        if($this->get('tipe')=='Excel'){
            $this->load->library('Excel', null, 'PHPExcel');
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
            require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';
            $object = new PHPExcel();

            // Set properties
            $object->getProperties()->setCreator("Koltiva Cocoatrace")
                           ->setLastModifiedBy("Koltiva Cocoatrace")
                           ->setCategory("Koltiva Cocoatrace");
            // Add some data
            
            $style_center = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );

            
            $style_border = array(
                  'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                      )
                  )
              );
            $title = str_replace("<br>", " ", $data['title']);
            $counter=6;
            if($this->get('jenisReport')=='wh'){
                $object->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $object->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                /*$object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('L')->setWidth(25);*/
                $object->getActiveSheet()->mergeCells('A1:E1');
                $object->getActiveSheet()->mergeCells('A2:E2');
                $object->getActiveSheet()->mergeCells('A5:E5');
                $object->getActiveSheet()->getStyle("A1:E4")->applyFromArray($style_center);
                $object->getActiveSheet()->getStyle("A4:E4")->applyFromArray($style_border);
                $object->getActiveSheet()->getStyle("A5")->applyFromArray($style_border);
                $object->getActiveSheet()->getStyle("A1:E4")->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
                $object->setActiveSheetIndex(0)->setCellValue('A1', $title);
                $object->setActiveSheetIndex(0)->setCellValue('A4', 'Transaction Date');
                $object->setActiveSheetIndex(0)->setCellValue('B4', 'PO Number');
                $object->setActiveSheetIndex(0)->setCellValue('C4', 'Batch Number');
                $object->setActiveSheetIndex(0)->setCellValue('D4', 'Gross (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('E4', 'Netto (Kg)');
                foreach ($details['data'] as $key => $value) {
                    $object->getActiveSheet()->getStyle("B$counter:E$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                    $object->getActiveSheet()->getStyle("A$counter:E$counter")->applyFromArray($style_border);
                    $object->getActiveSheet()->setCellValue('A'.$counter, $details['data'][$key]['datetransaction']);
                    $object->getActiveSheet()->setCellValue('B'.$counter, $details['data'][$key]['po']);
                    $object->getActiveSheet()->setCellValue('C'.$counter, $details['data'][$key]['batchnumber']);
                    $object->getActiveSheet()->setCellValue('D'.$counter, number_format($details['data'][$key]['bruto'],2,'.',''));
                    $object->getActiveSheet()->setCellValue('E'.$counter, number_format($details['data'][$key]['netto'],2,'.',''));
                    $counter++;
                }
                $konter = $counter;
                $konter++; 
                $object->getActiveSheet()->setCellValue('A'.$konter, "Total");
                $object->getActiveSheet()->getStyle("B$konter:E$konter")->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->setCellValue('D'.$konter, "=SUM(D5:D$counter)");
                $object->getActiveSheet()->setCellValue('E'.$konter, "=SUM(E5:E$counter)");
            }else if($this->get('jenisReport')=='coop'){
                $object->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $object->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                /*$object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('L')->setWidth(25);*/
                $object->getActiveSheet()->mergeCells('A1:I1');
                $object->getActiveSheet()->mergeCells('A2:I2');
                $object->getActiveSheet()->mergeCells('A5:I5');
                $object->getActiveSheet()->getStyle("A1:I4")->applyFromArray($style_center);
                $object->getActiveSheet()->getStyle("A4:I4")->applyFromArray($style_border);
                $object->getActiveSheet()->getStyle("A5")->applyFromArray($style_border);
                $object->getActiveSheet()->getStyle("A1:I4")->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
                $object->setActiveSheetIndex(0)->setCellValue('A1', $title);
                $object->setActiveSheetIndex(0)->setCellValue('A4', 'Transaction Date');
                $object->setActiveSheetIndex(0)->setCellValue('B4', 'Survey Volume (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('C4', 'Quota (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('D4', 'PO Number');
                $object->setActiveSheetIndex(0)->setCellValue('E4', 'Batch Number');
                $object->setActiveSheetIndex(0)->setCellValue('F4', 'Batch Status');
                $object->setActiveSheetIndex(0)->setCellValue('G4', 'Destination');
                $object->setActiveSheetIndex(0)->setCellValue('H4', 'Gross (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('I4', 'Netto (Kg)');
                $name = "";
                $j = 0;
                foreach ($details['data'] as $key => $value) {
                    $object->getActiveSheet()->getStyle("B$counter:I$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                    $object->getActiveSheet()->getStyle("A$counter:I$counter")->applyFromArray($style_border);
                    if($name!=$details['data'][$key]['name']){
                        $counter++;
                        $object->getActiveSheet()->getStyle("B$counter:I$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                        $object->getActiveSheet()->getStyle("A$counter:I$counter")->applyFromArray($style_border);
                        $object->getActiveSheet()->getStyle("A$counter:I$counter")->applyFromArray($style_border);
                        $name = $details['data'][$key]['name'];
                        $object->getActiveSheet()->setCellValue('A'.$counter, $details['data'][$key]['name']);
                        $object->getActiveSheet()->setCellValue('B'.$counter, number_format($surveys[$j]['survey'],2,'.',''));
                        $object->getActiveSheet()->setCellValue('C'.$counter, number_format($surveys[$j]['quota'],2,'.',''));
                        $object->getActiveSheet()->getStyle("A$counter:C$counter")->getFont()->setBold(true);
                        $counter++;
                        $j++;
                        $object->getActiveSheet()->getStyle("A$counter:I$counter")->applyFromArray($style_border);
                    }
                    $object->getActiveSheet()->setCellValue('A'.$counter, $details['data'][$key]['datetransaction']);
                    //**//
                    //**//
                    $object->getActiveSheet()->setCellValue('D'.$counter, $details['data'][$key]['po']);
                    $object->getActiveSheet()->setCellValue('E'.$counter, $details['data'][$key]['batchnumber']);
                    $object->getActiveSheet()->setCellValue('F'.$counter, $details['data'][$key]['batchstatus']);
                    $object->getActiveSheet()->setCellValue('G'.$counter, $details['data'][$key]['destination']);
                    $object->getActiveSheet()->setCellValue('H'.$counter, number_format($details['data'][$key]['bruto'],2,'.',''));
                    $object->getActiveSheet()->setCellValue('I'.$counter, number_format($details['data'][$key]['netto'],2,'.',''));
                    $counter++;
                }
                $konter = $counter;
                $konter++; 
                $object->getActiveSheet()->setCellValue('A'.$konter, "Total");
                $object->getActiveSheet()->getStyle("B$konter:I$konter")->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->setCellValue('B'.$konter, "=SUM(B5:B$counter)");
                $object->getActiveSheet()->setCellValue('C'.$konter, "=SUM(C5:C$counter)");
                $object->getActiveSheet()->setCellValue('H'.$konter, "=SUM(H5:H$counter)");
                $object->getActiveSheet()->setCellValue('I'.$konter, "=SUM(I5:I$counter)");
            }else if($this->get('jenisReport')=='bu'){
                $object->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $object->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                /*$object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('L')->setWidth(25);*/
                $object->getActiveSheet()->mergeCells('A1:I1');
                $object->getActiveSheet()->mergeCells('A2:I2');
                $object->getActiveSheet()->mergeCells('A5:I5');
                $object->getActiveSheet()->getStyle("A1:I4")->applyFromArray($style_center);
                $object->getActiveSheet()->getStyle("A4:I4")->applyFromArray($style_border);
                $object->getActiveSheet()->getStyle("A5")->applyFromArray($style_border);
                $object->getActiveSheet()->getStyle("A1:I4")->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
                $object->setActiveSheetIndex(0)->setCellValue('A1', $title);
                $object->setActiveSheetIndex(0)->setCellValue('A4', 'Transaction Date');
                $object->setActiveSheetIndex(0)->setCellValue('B4', 'Survey Volume (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('C4', 'Quota (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('D4', 'PO Number');
                $object->setActiveSheetIndex(0)->setCellValue('E4', 'Batch Number');
                $object->setActiveSheetIndex(0)->setCellValue('F4', 'Batch Status');
                $object->setActiveSheetIndex(0)->setCellValue('G4', 'Destination');
                $object->setActiveSheetIndex(0)->setCellValue('H4', 'Gross (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('I4', 'Netto (Kg)');
                $name = "";
                $j = 0;
                foreach ($details['data'] as $key => $value) {
                    $object->getActiveSheet()->getStyle("B$counter:I$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                    $object->getActiveSheet()->getStyle("A$counter:I$counter")->applyFromArray($style_border);
                    if($name!=$details['data'][$key]['name']){
                        $counter++;
                        $object->getActiveSheet()->getStyle("B$counter:I$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                        $object->getActiveSheet()->getStyle("A$counter:I$counter")->applyFromArray($style_border);
                        $object->getActiveSheet()->getStyle("A$counter:I$counter")->applyFromArray($style_border);
                        $name = $details['data'][$key]['name'];
                        $object->getActiveSheet()->setCellValue('A'.$counter, $details['data'][$key]['name']);
                        $object->getActiveSheet()->setCellValue('B'.$counter, number_format($surveys[$j]['survey'],2,'.',''));
                        $object->getActiveSheet()->setCellValue('C'.$counter, number_format($surveys[$j]['quota'],2,'.',''));
                        $object->getActiveSheet()->getStyle("A$counter:C$counter")->getFont()->setBold(true);
                        $counter++;
                        $j++;
                        $object->getActiveSheet()->getStyle("A$counter:I$counter")->applyFromArray($style_border);
                    }
                    $object->getActiveSheet()->setCellValue('A'.$counter, $details['data'][$key]['datetransaction']);
                    //**//
                    //**//
                    $object->getActiveSheet()->setCellValue('D'.$counter, $details['data'][$key]['po']);
                    $object->getActiveSheet()->setCellValue('E'.$counter, $details['data'][$key]['batchnumber']);
                    $object->getActiveSheet()->setCellValue('F'.$counter, $details['data'][$key]['batchstatus']);
                    $object->getActiveSheet()->setCellValue('G'.$counter, $details['data'][$key]['destination']);
                    $object->getActiveSheet()->setCellValue('H'.$counter, number_format($details['data'][$key]['bruto'],2,'.',''));
                    $object->getActiveSheet()->setCellValue('I'.$counter, number_format($details['data'][$key]['netto'],2,'.',''));
                    $counter++;
                }
                $konter = $counter;
                $konter++; 
                $object->getActiveSheet()->setCellValue('A'.$konter, "Total");
                $object->getActiveSheet()->getStyle("B$konter:I$konter")->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->setCellValue('B'.$konter, "=SUM(B5:B$counter)");
                $object->getActiveSheet()->setCellValue('C'.$konter, "=SUM(C5:C$counter)");
                $object->getActiveSheet()->setCellValue('H'.$konter, "=SUM(H5:H$counter)");
                $object->getActiveSheet()->setCellValue('I'.$konter, "=SUM(I5:I$counter)");
            }else if($this->get('jenisReport')=='farmer'){
                $object->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $object->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                $object->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                $object->getActiveSheet()->mergeCells('A1:K1');
                $object->getActiveSheet()->mergeCells('A2:K2');
                $object->getActiveSheet()->mergeCells('A5:K5');
                $object->getActiveSheet()->getStyle("A1:K4")->applyFromArray($style_center);
                $object->getActiveSheet()->getStyle("A4:K4")->applyFromArray($style_border);
                $object->getActiveSheet()->getStyle("A5")->applyFromArray($style_border);
                $object->getActiveSheet()->getStyle("A1:K4")->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
                $object->setActiveSheetIndex(0)->setCellValue('A1', $title);
                $object->setActiveSheetIndex(0)->setCellValue('A4', 'Transaction Date');
                $object->setActiveSheetIndex(0)->setCellValue('B4', 'Survey Volume (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('C4', 'Quota (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('D4', 'Farmer Group');
                $object->setActiveSheetIndex(0)->setCellValue('E4', 'Village');
                $object->setActiveSheetIndex(0)->setCellValue('F4', 'PO Number');
                $object->setActiveSheetIndex(0)->setCellValue('G4', 'Batch Number');
                $object->setActiveSheetIndex(0)->setCellValue('H4', 'Batch Status');
                $object->setActiveSheetIndex(0)->setCellValue('I4', 'Destination');
                $object->setActiveSheetIndex(0)->setCellValue('J4', 'Gross (Kg)');
                $object->setActiveSheetIndex(0)->setCellValue('K4', 'Netto (Kg)');
                $name = "";
                foreach ($details['data'] as $key => $value) {
                    $object->getActiveSheet()->getStyle("B$counter:K$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                    $object->getActiveSheet()->getStyle("A$counter:K$counter")->applyFromArray($style_border);
                    if($name!=$details['data'][$key]['name']){
                        $counter++;
                        $object->getActiveSheet()->getStyle("B$counter:K$counter")->getNumberFormat()->setFormatCode('#,##0.00');
                        $object->getActiveSheet()->getStyle("A$counter:K$counter")->applyFromArray($style_border);
                        $object->getActiveSheet()->getStyle("A$counter:K$counter")->applyFromArray($style_border);
                        $name = $details['data'][$key]['name'];
                        $object->getActiveSheet()->setCellValue('A'.$counter, $details['data'][$key]['name']);
                        $object->getActiveSheet()->setCellValue('B'.$counter, number_format($details['data'][$key]['survey'],2,'.',''));
                        $object->getActiveSheet()->setCellValue('C'.$counter, number_format($details['data'][$key]['quota'],2,'.',''));
                        $object->getActiveSheet()->setCellValue('D'.$counter, $details['data'][$key]['cpg']);
                        $object->getActiveSheet()->setCellValue('E'.$counter, $details['data'][$key]['village']);
                        $object->getActiveSheet()->getStyle("A$counter:E$counter")->getFont()->setBold(true);
                        $counter++;
                        $object->getActiveSheet()->getStyle("A$counter:K$counter")->applyFromArray($style_border);
                    }
                    $object->getActiveSheet()->setCellValue('A'.$counter, $details['data'][$key]['datetransaction']);
                    //**//
                    //**//
                    $object->getActiveSheet()->setCellValue('F'.$counter, $details['data'][$key]['po']);
                    $object->getActiveSheet()->setCellValue('G'.$counter, $details['data'][$key]['batchnumber']);
                    $object->getActiveSheet()->setCellValue('H'.$counter, $details['data'][$key]['batchstatus']);
                    $object->getActiveSheet()->setCellValue('I'.$counter, $details['data'][$key]['destination']);
                    $object->getActiveSheet()->setCellValue('J'.$counter, number_format($details['data'][$key]['bruto'],2,'.',''));
                    $object->getActiveSheet()->setCellValue('K'.$counter, number_format($details['data'][$key]['netto'],2,'.',''));
                    $counter++;
                }
                $konter = $counter;
                $konter++; 
                $object->getActiveSheet()->setCellValue('A'.$konter, "Total");
                $object->getActiveSheet()->getStyle("B$konter:K$konter")->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->setCellValue('B'.$konter, "=SUM(B5:B$counter)");
                $object->getActiveSheet()->setCellValue('C'.$konter, "=SUM(C5:C$counter)");
                $object->getActiveSheet()->setCellValue('J'.$konter, "=SUM(J5:J$counter)");
                $object->getActiveSheet()->setCellValue('K'.$konter, "=SUM(K5:K$counter)");
            }
                        
            
            /**/

            
            // Rename sheet
            //$object->getActiveSheet()->setTitle('Laporan Penjualan per Buying Unit');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $object->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="report_traceability_'.$this->get('jenisReport').'.xlsx');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
        $data['details'] = $details['data'];
        $this->load->view('traceability_preview_cetak', $data);
        
    }
    
    function transaction_files_get(){
        $data = $this->mtransaction->readUploadFiles($this->get('batchid'));
        $this->response($data, 200);
    }
    
    function transaction_files_post(){
        if (!empty($_FILES)) {
            $upload_path = './files/supplychain/';
            $config['allowed_types'] = 'gif|jpg|png|pdf';
            $config['max_size'] = '1024';

            $this->load->library('upload');

            $config['upload_path'] = $upload_path;
            if(@$_FILES['File']['type']=='image/jpeg'){
                $tipe = ".jpg";
            }else if(@$_FILES['File']['type']=='application/pdf'){
                $tipe = ".pdf";
            }
            
            $config['file_name'] = date('YmdHis').'_'.$this->post('UploadFilesSupplyBatchID').$tipe;
            $this->upload->initialize($config);
            if (!empty($_FILES['File']['tmp_name'])) {
                if ( ! $this->upload->do_upload('File')){
                    $status = 'Warning';
                    $message = $this->upload->display_errors();
                } else {
                    $result = $this->mtransaction->insertUploadFiles($this->post('UploadFilesSupplyBatchID'),$config['file_name'],@$_FILES['File']['name'],@$_FILES['File']['type'],$_SESSION['userid']);
                    if($result){
                        $status = 'Success';
                        $message = "Upload file success";
                    }else{
                        $status = 'Error';
                        $message = "Could not connect to the database. Retry later";
                    }
                }
            }
            $data = array('status'=>$status,'message'=>$message);
            $this->response($data, 200);
        }
    }
    
    function transaction_files_delete() {
        $data = $this->mtransaction->deleteUploadFiles($this->delete('id'));
        if ($data)
            $this->response($data, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Couldn\'t find any units!'), 404);
    }

    function supplychain_area_get(){
        $data = $this->mdata->getSupplychainArea($this->get('SupplychainID'));
        if ($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    function supplychain_area_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $data = $this->mdata->deleteArea($this->delete('id'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }

    public function supplychain_province_get(){
        $data = $this->mdata->listProvinces();
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }
    
    public function supplychain_district_get(){
        $data = $this->mdata->listDistricts($this->get('ProvinceID'), $this->get('SupplychainID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any roles!'), 404);
        }
    }

    public function supplychain_area_post() {
        $data = $this->mdata->addArea($this->post('SupplychainID'), $this->post('DistrictID'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Data could not be found'), 404);
    }
}
