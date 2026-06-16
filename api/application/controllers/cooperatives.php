<?php defined('BASEPATH') or exit('No direct script access allowed');

class cooperatives extends REST_Controller
{

    public function __construct()
    {
        $this->file = $_FILES;
        parent::__construct();
        $this->load->model('cooperatives/mcooperatives');
        $this->load->model('cooperatives/minventory');
        $this->load->model('coop/msales');
        $this->load->model('coop/mpurchase');
        $this->config->load('coop');
        set_time_limit(0);
        $this->load->library('curl');
    }

    public function coops_get()
    {
        $data = $this->mcooperatives->readDatas($this->get('kec'), $this->get('kab'), $this->get('prov'), $this->get('start'), $this->get('limit'), $this->get('key'), $this->get('textSearch'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function coop_member_input_grid_get(){
        $CoopID = (int) $this->get('CoopID');
        $textSearch = $this->get('textSearch');
        $villageSearch = $this->get('villageSearch');
        $Enumerator = $this->get('Enumerator');

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $result = $this->mcooperatives->getFarmerGroupMemberInputGrid($CoopID,$textSearch,$villageSearch,$this->get('start'), $this->get('limit'), $sortingField, $sortingDir,$Enumerator);
        $this->response($result, 200);
    }

    public function coop_member_input_post(){
        $CoopID = (int) $this->post('CoopID');
        $arrMemberID = json_decode($this->post('MemberID'));

        $result = $this->mcooperatives->inputFarmerGroupMember($arrMemberID,$CoopID);
        $this->response($result, 200);
    }

    public function coop_member_delete(){
        $CoopID = (int) $this->delete('CoopID');
        $MemberID = (int) $this->delete('MemberID');

        $result = $this->mcooperatives->deleteCoopMember($MemberID,$CoopID);
        $this->response($result, 200);
    }

    public function coop_excel_post()
    {
        $arrAdv = array(
            "prov" => $this->post('prov'),
            "advDistrict" => $this->post('parAdvDistrict'),
            "advNama" => $this->post('parAdvNama'),
            "advStatus" => $this->post('parAdvStatus'),
            "advOpTahun" => $this->post('parAdvOpTahun'),
            "advTahun" => $this->post('parAdvTahun'),
            "advTglModiStart" => substr($this->post('parAdvTglModiStart'), 0, 10),
            "advTglModiEnd" => substr($this->post('parAdvTglModiEnd'), 0, 10)
         );
        $dataList = $this->mcooperatives->readDatasExportExcel($arrAdv);

        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel.php';
        require_once 'application/libraries/PHPExcel-1.7.9/Classes/PHPExcel/IOFactory.php';

        $mem_ini = ini_get('memory_limit');
        ini_set('memory_limit', '1048576M');

        //=============== MULAI TULIS EXCEL (BEGIN) ===================================================================//
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PT Koltiva")
            ->setLastModifiedBy("PT Koltiva")
            ->setTitle("List Data Export CocoaTrace")
            ->setSubject("List Data Export CocoaTrace")
            ->setDescription("List Data Export CocoaTrace")
            ->setKeywords("List Data Export CocoaTrace")
            ->setCategory("List Data Export CocoaTrace");

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('List');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //set style
        $styleFont = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            ),
        );

        $styleFontBold = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
        );

        $styleFontBoldTitle = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );

        $styleFontBoldHeader = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '8DB4E3'),
            ),
        );
        $styleFontBoldBgRedCenter = array(
            'font'      => array(
                'name' => 'Arial',
                'size' => '9',
                'bold' => true,
            ),
            'fill'      => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C0504D'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleBorderFull = array(
            'borders' => array(
                'left'   => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right'  => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top'    => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        //set width column
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(19);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

        //tulis judul
        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'List Farmer Organization');
        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleFontBoldTitle);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B2:I2');

        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'No');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', lang('Code'));
        $objPHPExcel->getActiveSheet()->setCellValue('D4', lang('Nama'));
        $objPHPExcel->getActiveSheet()->setCellValue('E4', lang('Phone'));
        $objPHPExcel->getActiveSheet()->setCellValue('F4', lang('Email'));
        $objPHPExcel->getActiveSheet()->setCellValue('G4', lang('Tahun Terbentuk'));
        $objPHPExcel->getActiveSheet()->setCellValue('H4', lang('Status'));
        $objPHPExcel->getActiveSheet()->setCellValue('I4', lang('District'));
        $objPHPExcel->getActiveSheet()->getStyle('B4:I4')->applyFromArray($styleFontBoldHeader);
        $objPHPExcel->getActiveSheet()->getStyle('B4:I4')->applyFromArray($styleBorderFull, false);

        $rowStart = 5;
        for ($i=0; $i < count($dataList); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowStart, $i+1);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$rowStart, $dataList[$i]['CoopCode']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$rowStart, $dataList[$i]['CoopName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$rowStart, $dataList[$i]['Phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$rowStart, $dataList[$i]['Email']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$rowStart, $dataList[$i]['TahunTerbentuk']);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$rowStart, $dataList[$i]['Status']);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$rowStart, $dataList[$i]['District']);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowStart.':I'.$rowStart)->applyFromArray($styleFont);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowStart.':I'.$rowStart)->applyFromArray($styleBorderFull, false);
            $rowStart++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('list_farmer_org_cocoatrace.xls');
        ini_set('memory_limit', $mem_ini);
        $this->response(array('success' => true, 'filenya'=>base_url().'list_farmer_org_cocoatrace.xls'), 200);
        exit;
        //=============== MULAI TULIS EXCEL (END) ===================================================================//
    }

    public function coop_get()
    {
        $data = $this->mcooperatives->readData($this->get('CoopID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function coop_member_panel_grid_get(){
        $CoopID = (int) $this->get('CoopID');

        //sort
        $sorting = json_decode($this->get('sort'));
        $sortingField = $sorting[0]->property;
        $sortingDir = $sorting[0]->direction;

        $result = $this->mcooperatives->getCoopMemberPanelGrid($CoopID,$this->get('start'), $this->get('limit'), $sortingField, $sortingDir);
        $this->response($result, 200);
    }

    public function prep_adv_filter_coop_get()
    {
        $opsi = $this->get('opsi');
        switch ($opsi) {
         case 'cmb_year_establish':
            $data = $this->mcooperatives->getCmbYearEstablish();
            if ($data) {
                $this->response($data, 200);
            } else {
                $this->response(array(), 200);
            }
         break;
         case 'cmb_district':

         break;
         default:
            $this->response(array(), 200);
         break;
      }
    }

    public function staffss_get()
    {
        $data = $this->mcooperatives->readStaffs($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function staffs_farmers_get()
    {
        $data = $this->mcooperatives->readStaffsFarmer($this->get('district'), $this->get('query'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function staffs_post()
    {
        if (!$this->post('Status')) {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->createDataStaff($this->post('CoopID'), $this->post('FarmerID'),
            $this->post('StaffName'), $this->post('Position'), null, $this->post('Phone'), $this->post('Email'),
            substr($this->post('StaffBirthday'), 0, 10), $this->post('StaffGender'), $_SESSION['userid'], $this->post('StaffStatus'), $this->post('PaymentStatus'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function staffs_put()
    {
        if (!$this->put('StaffID')) {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->updateDataStaff($this->put('CoopID'), $this->put('FarmerID'),
            $this->put('StaffName'), $this->put('Position'), null, $this->put('Phone'), $this->put('Email'),
            substr($this->put('StaffBirthday'), 0, 10), $this->put('StaffGender'), $_SESSION['userid'], $this->put('StaffID'), $this->put('StaffStatus'), $this->put('PaymentStatus'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function staffs_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->deleteDataStaff($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }
    }

    public function boards_get()
    {
        $data = $this->mcooperatives->readBoardData($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any data!'), 404);
        }
    }

    public function board_post()
    {
        // $this->response($this->post(), 200);

        $data = $this->mcooperatives->createDataBoard($this->post());
        $this->response($data, 200);

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error. Please try again later'), 200);
        }
    }

    public function board_put()
    {
        $data = $this->mcooperatives->updateDataBoard($this->put());
        $this->response($data, 200);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error. Please try again later'), 200);
        }
    }

    public function coop_post()
    {
        $post = $this->post();
        foreach ($post as $k => $v) {
            $k = str_replace("Koltiva_view_Cooperatives_FormMainCooperatives-FormBasicData-","",$k);
            $varPost[$k] = $v;
        }

        if($varPost['CoopID'] != ''){
            $data = $this->mcooperatives->updateCooperatives($varPost);
        }else{
            $data = $this->mcooperatives->insertCooperatives($varPost);
        }
        
        $this->response($data, 200);
    }

    public function coopu_post()
    {
        if (!$this->post('CoopID')) {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->updateData($this->post(), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function coop_delete()
    {
        if (!$this->delete('CoopID')) {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->deleteData($this->delete('CoopID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }
    }

    public function coop_image_post()
    {
        if ($this->file['Photo']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo']['name'];
            $upload = move_upload($this->file, 'images/coop/'.$gambar);
            if (isset($upload['upload_data'])) {
                //@unlink('images/coop/'.$this->post('Photo_old'));
                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }/*else if ($this->file['Photo_cert']['name']!='') {
            $gambar = date('Ymdhis').'_'.$this->file['Photo_cert']['name'];
            //if(move_uploaded_file($this->file['Photo_cert']['tmp_name'], 'images/coop/'.$gambar)) {
                //@unlink('images/coop/'.$this->post('Photo_cert_old'));
                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }*/
    }

    public function icsgroup_get()
    {
        $data = $this->mcooperatives->readIcsGroup($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function icsgroup_post()
    {
        $IcsGroup = $this->mcooperatives->createIcs('Organisasi Petani', $this->post('IcsObjID'), $_SESSION['userid']);
        if ($IcsGroup) {
            $this->response($IcsGroup, 200);
        } else {
            $this->response(array('error' => 'Failed to create record'), 404);
        }
        /*
         * IcsID
            IcsObjID    4
            iCoopName
            icsType
         */
        //$this->response(true, 200);
        //$objId = 1;
        //$IcsGroup = $this->mcooperatives->createIcs($this->post('icsType'),$objId,$_SESSION['userid']);
        //if($IcsGroup) $this->response($IcsGroup, 200);
        //else $this->response(array('error' => 'Failed to create record'), 404);
    }
    public function icsgroup_put()
    {
        if ($this->put('IcsID')=='') {
            $this->response(array('error' => 'Ics ID is empty'), 404);
        }
        $objId = 1;
        $IcsGroup = $this->mcooperatives->updateIcs($this->put('IcsID'), $this->put('icsType'), $objId, $_SESSION['userid']);
        if ($IcsGroup) {
            $this->response($IcsGroup, 200);
        } else {
            $this->response(array('error' => 'Failed to create record'), 404);
        }
    }

    public function icsmember_find_get()
    {
        $data = $this->mcooperatives->searchFarmer(
            $this->get('district'),
            $this->get('province'),
            $this->get('query')
        );
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function icsmember_get()
    {
        if (!$this->get('icsID')) {
            $this->response(null, 400);
        }
        $start = ($this->get('start') < 0)?0:$this->get('start');
        $data = $this->mcooperatives->readIcsMember(
                $this->get('icsID'),
                $this->get('limit'),
                $this->get('page'),
                $start
        );
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function icsmember_post()
    {
        if (($this->post('icsID')=='') && ($this->post('farmerID') == '')) {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->addIcsMember($this->post('icsID'), $this->post('farmerID'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }
    public function icsmember_delete()
    {
        if ($this->delete('id')=='') {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->deleteIcsMember($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function settingdatacoops_get()
    {
        $data = $this->mcooperatives->readSettingCoopDatas($this->get('UserId'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function settingdatacoop_get()
    {
        $data = $this->mcooperatives->readSettingCoopData($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function settingdatacoop_post()
    {
        if (!$this->post('CoopName')) {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->createData($this->post('CoopCode'), $this->post('CoopName'), $this->post('Phone'),
            $this->post('Email'), $this->post('TahunTerbentuk'), $this->post('Status'), $this->post('Desa'),
            $this->post('Address'), $this->post('Latitude'), $this->post('Longitude'), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function settingdatacoopu_post()
    {
        // if(!$this->post('CoopID'))
        // $this->response($this->post(), 200);

        $data = $this->mcooperatives->updateData($this->post(), $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be found'), 404);
        }
    }

    public function getDataInventory_get()
    {
        $data = $this->minventory->readData($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function getDataInventorys_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = 'createddate';
        $dir = 'DESC';

        if ($this->get('sort')) {
            $sort = json_decode($this->get('sort'), true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array();

        $data = $this->minventory->readDatas($start, $limit, $_SESSION['userid'], $this->get('Status'));

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    public function supplier_post()
    {
        if (!$this->post('namesupplier')) {
            $this->response(null, 400);
        }
        $data = $this->minventory->createSupplier(
                $this->post('code'),
                $this->post('namesupplier'),
                $this->post('telephone'),
                $this->post('fax'),
                $this->post('email'),
                $this->post('companyaddress'),
                $this->post('city'),
                $this->post('country'),
                $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Cannot insert data'), 404);
        }
    }

    public function supplier_put()
    {
        //        if(!$this->post('namesupplier')) $this->response(NULL, 400);
        $data = $this->minventory->updateSupplier(
                $this->put('id'),
                $this->put('code'),
                $this->put('namesupplier'),
                $this->put('telephone'),
                $this->put('fax'),
                $this->put('email'),
                $this->put('companyaddress'),
                $this->put('city'),
                $this->put('country'),
                $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Cannot updating data'), 404);
        }
    }

    public function supplier_get()
    {
        $data = $this->minventory->readSupplierData($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function suppliers_get()
    {
        //return $this->response(array('success'=>true,'message'=>'oke'),  200);

        $start = $this->get('start');
        $limit = $this->get('limit');
        $sort = 'CreatedDate';
        $dir = 'DESC';

        if ($this->get('sort')) {
            $sort = json_decode($this->get('sort'), true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array();

        $data = $this->minventory->readDataSuppliers($start, $limit, $this->get('Type'), $this->get('OrgID'));

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    public function supplier_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }
        $data = $this->minventory->deleteSupplierData($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Data could not be delete'), 404);
        }
    }

    public function invcategorys_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $key = $this->get('key');
        $sort = 'CreatedDate';
        $dir = 'DESC';

        if ($this->get('sort')) {
            $sort = json_decode($this->get('sort'), true);
            $dir = $sort[0]['direction'];
            $sort = $sort[0]['property'];
        }

        $filter = array();

        $data = $this->minventory->readDataInvCategories($start, $limit, $key);

        $this->_num = 200;
        $this->_output = array('success' => true, 'data' => $data['data'], 'total' => $data['total']);

        return $this->response($this->_output,  $this->_num);
    }

    public function invcategory_get()
    {
        $data = $this->minventory->readInvCategoryData($this->get('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function invcategory_post()
    {
        if (!$this->post('namecat')) {
            $this->response(null, 400);
        }
        $data = $this->minventory->createInvCategory(
                $this->post('namecat'),
                $this->post('Description'),
                $this->post('SellCoaID'),
                $this->post('BuyCoaID'),
                $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Cannot insert data'), 404);
        }
    }

    public function invcategory_put()
    {
        if (!$this->put('namecat')) {
            $this->response(null, 400);
        }
        $data = $this->minventory->updateInvCategory(
                $this->put('id'),
                $this->put('namecat'),
                $this->put('Description'),
                $this->put('SellCoaID'),
                $this->put('BuyCoaID'),
                $_SESSION['userid']);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Cannot updating data'), 404);
        }
    }

    public function invcategory_delete()
    {
        //        if(!$this->put('namecat')) $this->response(NULL, 400);
        $data = $this->minventory->deleteInvCategoryData($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Cannot deleting data'), 404);
        }
    }


    public function inventoryAdd_post()
    {
        //        if(!$this->post('CoopID')) $this->response(NULL, 400);


        $config['upload_path'] = $this->config->item('inventory_image_dir');
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']    = '1000';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if (! $this->upload->do_upload('images') && $this->upload->display_errors()!='<p>You did not select a file to upload.</p>') {
            $results['success'] = false;
            $results['message'] = $this->upload->display_errors();
            $this->response($results, 200);
        } else {
            //                $data = array('upload_data' => $this->upload->data());
                $datafile = $this->upload->data();
            if (isset($datafile['file_name'])) {
                $pict = $datafile['file_name'];
            } else {
                $pict = null;
            }

            $idsupplier = $this->post('idsupplier')==null ? null : $this->post('idsupplier');

                // $this->minventory->testing($this->post('SerialNumber'));
                // exit;

                $data = $this->minventory->insertData(
        //                $this->post('idinventory'),
                        $this->post('Number'),
                        $this->post('Name'),
                        $this->post('CategoryID'),
                        $this->post('Description'),
                        $this->post('cbdijual'),
                        $this->post('cbdibeli'),
                        $this->post('cbpersediaan'),
                        $this->post('nonaktif'),
                        $this->post('incomeaccount'),
                        $this->post('sellingprice'),
                        $this->post('idbuytax'),
                        $this->post('unitmeasuresell'),
                        $this->post('cosaccount'),
                        $this->post('Cost') != null ? str_replace(',', '', $this->post('Cost')) : null,
                        $this->post('UnitMeasure'),
                        $this->post('nametaxbuy'),
                        $idsupplier,
                        $this->post('DateBuy'),
                        $this->post('Stock'),
                        $this->post('Residu') != null ? str_replace(',', '', $this->post('Residu')) : null,
                        $this->post('Umur'),
                        $this->post('AkumulasiBeban') != null ? str_replace(',', '', $this->post('AkumulasiBeban')) : null,
                        $this->post('BebanBerjalan') != null ? str_replace(',', '', $this->post('BebanBerjalan')) : null,
                        $this->post('NilaiBuku') != null ? str_replace(',', '', $this->post('NilaiBuku')) : null,
                        $this->post('BebanPerBulan') != null ? str_replace(',', '', $this->post('BebanPerBulan')) : null,
                        $this->post('AkumulasiAkhir') != null ? str_replace(',', '', $this->post('AkumulasiAkhir')) : null,
                        $_SESSION['userid'],
                        $pict,
                        $this->post('coaIDAsset'),
                        $this->post('coaIDAkumDepres'),
                        $this->post('coaIDBebanDepres'),
                        $this->post('SerialNumber'),
                        $this->post('SupplierName'),
                        $this->post('IsRemoved'),
                        $this->post('RemoveReason'),
                        $this->post('EvaluateType'),
                        $this->post('EvaluateSoldPrice'),
                        $this->post('Location'),
                        $this->post('Status'),
                        $this->post('EvaluateReason')
                );
            if ($data) {
                $this->response($data, 200);
            } else {
                $this->response(array('error' => 'insert data failed'), 404);
            }
        }
    }

    public function inventoryedit_post()
    {
        $config['upload_path'] = $this->config->item('inventory_image_dir');
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']    = '1000';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if (! $this->upload->do_upload('Images') && $this->upload->display_errors()!='<p>You did not select a file to upload.</p>') {
            $results['success'] = false;
            $results['message'] = $this->upload->display_errors();
            $this->response($results, 200);
        } else {
            //                $data = array('upload_data' => $this->upload->data());
                $datafile = $this->upload->data();
            if (isset($datafile['file_name'])) {
                $pict = $datafile['file_name'];
            } else {
                $pict = null;
            }


            $idsupplier = $this->post('idsupplier')==null ? null : $this->post('idsupplier');

            $data = $this->minventory->editData(
                        $this->post('InventoryID'),
                        $this->post('Number'),
                        $this->post('Name'),
                        $this->post('CategoryID'),
                        $this->post('Description'),
                        $this->post('cbdijual'),
                        $this->post('cbdibeli'),
                        $this->post('cbpersediaan'),
                        $this->post('nonaktif'),
                        $this->post('incomeaccount'),
                        $this->post('sellingprice'),
                        $this->post('idbuytax'),
                        $this->post('unitmeasuresell'),
                        $this->post('cosaccount'),
                       $this->post('Cost') != null ? str_replace(',', '', $this->post('Cost')) : null,
                        $this->post('UnitMeasure'),
                        $this->post('nametaxbuy'),
                        $idsupplier,
                        $this->post('DateBuy'),
                        $this->post('Stock'),
                        $this->post('Residu'),
                        $this->post('Umur'),
                       $this->post('AkumulasiBeban') != null ? str_replace(',', '', $this->post('AkumulasiBeban')) : null,
                        $this->post('BebanBerjalan') != null ? str_replace(',', '', $this->post('BebanBerjalan')) : null,
                        $this->post('NilaiBuku') != null ? str_replace(',', '', $this->post('NilaiBuku')) : null,
                        $this->post('BebanPerBulan') != null ? str_replace(',', '', $this->post('BebanPerBulan')) : null,
                        $this->post('AkumulasiAkhir') != null ? str_replace(',', '', $this->post('AkumulasiAkhir')) : null,
                        $_SESSION['userid'],
                        $pict,
                        $this->post('coaIDAsset'),
                        $this->post('coaIDAkumDepres'),
                        $this->post('coaIDBebanDepres'),
                        $this->post('SerialNumber'),
                        $this->post('SupplierName'),
                        $this->post('IsRemoved'),
                        $this->post('RemoveReason'),
                        $this->post('EvaluateType'),
                        $this->post('EvaluateSoldPrice'),
                        $this->post('Location'),
                        $this->post('reEvaluasiBtnOpt'),
                        $this->post('Status'),
                        $this->post('EvaluateReason')
                );


            if ($data) {
                $this->response($data, 200);
            } else {
                $this->response(array('error' => 'update data failed'), 404);
            }
        }
    }

    public function inventorydelete_delete()
    {
        $data = $this->minventory->deleteInventoryData($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Cannot deleting data'), 404);
        }
    }

    public function countdepreciate_get()
    {
        //hitung depresiasi inventory
        //RUMUS GARIS LURUS : Harga Perolehan (harga beli) / (umur ekonomis x 12 bulan)

        $hargabeli = $this->get('costInventory');
        $residu = $this->get('residu');
        $tahun = $this->get('umurEkonomis');
        $tgl = $this->get('tgl'); //01-11-2015
        $ambilTahun = $this->get('tahun');
//        $tahun = $this->get('tahun');

        $akumulasiPenyusutanAkhir= number_format($this->akumulasiTerakhir($hargabeli, $residu, $tahun, $tgl));

        if ($tahun==0) {
            $tahun=1;
        }

        //tanggal ambil bulan
        $explode = explode("-", $tgl);
        $bulan = intval($explode[1]);
        // $tahun = intval($explode[0]);
        //umur ekonomis dikalikan bulan
        $umur = $tahun*12;
        $tahunAkhir = intval($explode[0])+$tahun;

        //penyusutan tiap tahun
        $penyusutanTahun = ($hargabeli-$residu)/$tahun;
        $penyusutanBulan = ($hargabeli-$residu)/$umur;
        // echo $penyusutanBulan;

        //penyusutan akhir tahun berjalan
        // echo (13-$bulan);
        $penyusutanBerjalan = (13-$bulan)/12*$penyusutanTahun;

        //penyusutan tahun akhir
        // echo $penyusutanTahun;
        $penyusutanAkhir = (12-(13-$bulan))/12*$penyusutanTahun;
        // echo $penyusutanAkhir;

        $akumulasiPenyusutan=0;

        for ($i=$explode[0]; $i <$tahunAkhir+1 ; $i++) {
            $bebanBerjalan = 0;
            if ($i==$explode[0]) {
                //beban penyusutan tahun berjalan awal
                $akumulasiPenyusutan+=$penyusutanBerjalan;
                $bebanBerjalan = $penyusutanBerjalan;
            } elseif ($i==$tahunAkhir) {
                //selesai
                $akumulasiPenyusutan+=$penyusutanAkhir;
                $bebanBerjalan = $penyusutanAkhir;
            } else {
                $akumulasiPenyusutan+=$penyusutanTahun;
                $bebanBerjalan=$penyusutanTahun;
            }

            $hargabeli-=$bebanBerjalan;

            // echo $i.' '.$ambilTahun.'<br>';

            $d = array(
                'tahun'=>$i,
                'penyusutanBulan'=>number_format($penyusutanBulan),
                'bebanBerjalan'=>number_format($bebanBerjalan),
                'akumulasiPenyusutan'=>number_format($akumulasiPenyusutan),
                'nilaiBuku'=>number_format($hargabeli),
                'akumulasiPenyusutanAkhir'=>$akumulasiPenyusutanAkhir
            );



            if ($ambilTahun!=null) {
                if ($i==$ambilTahun) {
                    // print_r($d);
                    // echo '<hr>';
//                    echo json_encode($d);
                    $this->response($d, 200);
                    break;
                }
            } elseif ($hargabeli==0) {
                break;
            } else {
                // print_r($d);
                     // echo '<hr>';
                    $this->response($d, 200);
            }
        }
    }

    public function akumulasiTerakhir($hargabeli, $residu, $tahun, $tgl)
    {
        if ($tahun==0) {
            $tahun=1;
        }

        //tanggal ambil bulan
        $explode = explode("-", $tgl);
        $bulan = intval($explode[1]);
        // $tahun = intval($explode[0]);
        //umur ekonomis dikalikan bulan
        $umur = $tahun*12;
        $tahunAkhir = intval($explode[0])+$tahun;

        //penyusutan tiap tahun
        $penyusutanTahun = ($hargabeli-$residu)/$tahun;
        $penyusutanBulan = ($hargabeli-$residu)/$umur;
        // echo $penyusutanBulan;

        //penyusutan akhir tahun berjalan
        // echo (13-$bulan);
        $penyusutanBerjalan = (13-$bulan)/12*$penyusutanTahun;

        //penyusutan tahun akhir
        // echo $penyusutanTahun;
        $penyusutanAkhir = (12-(13-$bulan))/12*$penyusutanTahun;
        // echo $penyusutanAkhir;

        $ambilTahun = $explode[0]+$tahun;
// echo $ambilTahun;
        $akumulasiPenyusutan=0;
        $akumulasiPenyusutanAkhir=0;
        for ($i=$explode[0]; $i <$tahunAkhir+1 ; $i++) {
            $bebanBerjalan = 0;
            if ($i==$explode[0]) {
                //beban penyusutan tahun berjalan awal
                $akumulasiPenyusutan+=$penyusutanBerjalan;
                $bebanBerjalan = $penyusutanBerjalan;
            } elseif ($i==$tahunAkhir) {
                //selesai
                $akumulasiPenyusutan+=$penyusutanAkhir;
                $bebanBerjalan = $penyusutanAkhir;
            } else {
                $akumulasiPenyusutan+=$penyusutanTahun;
                $bebanBerjalan=$penyusutanTahun;
            }

            $hargabeli-=$bebanBerjalan;
            $akumulasiPenyusutanAkhir+=$akumulasiPenyusutan;
            // echo $akumulasiPenyusutan.'<br>';


            if ($ambilTahun!=null) {
                if ($i==$ambilTahun) {
                    // print_r($d);
                    // echo '<hr>';
                    // echo json_encode($d);
                    return $akumulasiPenyusutan;
                    break;
                }
            } elseif ($hargabeli==0) {
                break;
            } else {
                // print_r($d);
                     // echo '<hr>';
                    return $akumulasiPenyusutan;
            }
        }
    }

    public function documents_get()
    {
        $data = $this->mcooperatives->readDocument($this->get('key'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function document_post()
    {
        $config['upload_path'] = $this->config->item('coop_document');
        $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|csv|xls|xlsx';
        $config['max_size'] = '1000000';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $results['success'] = false;
            $results['message'] = $this->upload->display_errors();
            $this->response($results, 200);
        } else {
            $datafile = $this->upload->data();
            $d = $this->mcooperatives->saveUploadDocCoop($this->post('label'), $this->post('FileCategory'), $datafile, $_SESSION['userid']);
            $this->response($d, 200);
        }
    }

    public function limit_trans_post()
    {
        $ApprovalID = $this->post('ApprovalID');
        if ($ApprovalID==null) {
            $data = $this->mcooperatives->createLimitTrans($this->post(), $_SESSION['userid']);
        } else {
            $data = $this->mcooperatives->updateLimitTrans($this->post(), $_SESSION['userid']);
        }
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function limit_trans_get()
    {
        $data = $this->mcooperatives->readLimitTrans($this->get('id'), $this->get('start'), $this->get('limit'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function customers_get()
    {
        $data = $this->mcooperatives->readCustomer(getCoopID(), $this->get('Type'), $this->get('Name'));
        $this->response($data, 200);
    }

    public function sales_post()
    {
        if ($this->post('SaleId')=='') {
            $data = $this->msales->create($this->post(), $_SESSION['userid'], getCoopID());
        } else {
            $data = $this->msales->update($this->post(), $_SESSION['userid'], getCoopID());
        }
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function sales_put()
    {
        if ($this->put('formtype')=='pelunasan') {
            $data = $this->msales->pelunasan($this->put(), $_SESSION['userid'], getCoopID());
        } else {
            //update data
            $data = true;
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function sales_list_get()
    {
        $data = $this->msales->getData(getCoopID(), $this->get('Awal'), $this->get('Akhir'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function sales_delete()
    {
        $data = $this->msales->deleteData($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function piutang_list_get()
    {
        $data = $this->msales->getPiutangData(getCoopID(), $this->get('Awal'), $this->get('Akhir'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function purchase_post()
    {
        $data = $this->mpurchase->create($this->post(), $_SESSION['userid'], getCoopID());
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function purchase_list_get()
    {
        $data = $this->mpurchase->getData(getCoopID(), $this->get('Awal'), $this->get('Akhir'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function hutang_list_get()
    {
        $data = $this->mpurchase->getHutangData(getCoopID(), $this->get('Awal'), $this->get('Akhir'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function purchase_put()
    {
        if ($this->put('formtype')=='pelunasan') {
            $data = $this->mpurchase->pelunasan($this->put(), $_SESSION['userid'], getCoopID());
        } else {
            //update data
            $data = true;
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function purchase_items_get()
    {
        $data = $this->mpurchase->getDataItem($this->get('PurchaseID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function items_opname_get()
    {
        $data = $this->minventory->getOpnameItem(getCoopID());
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function opname_post()
    {
        $data = $this->minventory->insertOpname($this->post(), getCoopID(), $_SESSION['userid']);
        if ($data['success']) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function opname_list_get()
    {
        $data = $this->minventory->dataOpname(getCoopID());
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function cooperative_list_get()
    {
        $province = $this->get('ProvinceID');
        $district = $this->get('DistrictID');
        $subdistrict = $this->get('SubDistrictID');
        $data = $this->mcooperatives->readCooperative($province, $district, $subdistrict);
        // if($data)
            $this->response($data, 200);
        // else $this->response(array('error' => 'Couldn\'t find any programs!'), 404);
    }

    public function coop_clonal_garden_get()
    {
        $data = $this->mcooperatives->readClonalGarden($this->get('ObjID'), $this->get('ObjType'), $this->get('ClonalID'), $this->get('GardenNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function coop_clonal_garden_area_get()
    {
        $data = $this->mcooperatives->readClonalGardenArea($this->get('ObjType'), $this->get('ObjID'), $this->get('GardenNr'), $this->get('ClonalID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function coop_clonal_garden_post()
    {
        $clonal_garden = $this->mcooperatives->createClonalGarden($this->post());
        if ($clonal_garden) {
            $this->response($clonal_garden, 200);
        } else {
            $this->response(array('error' => 'Clone Garden could not be saved'), 404);
        }
    }

    public function coop_clonal_garden_put()
    {
        $clonal_garden = $this->mcooperatives->updateClonalGarden($this->put());
        $this->response($clonal_garden, 200);
    }
    public function sync_export_get()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $this->load->helper('file');
        $this->load->library('encrypt');
        $this->load->library('zip');

        $this->load->model('cooperatives/msync');

        $coopId = getCoopID();

        $path = date('Ymd');
        $zip = date('Ymd');

        $txt = $this->msync->getAlldata($coopId);
        $this->zip->add_data($path, $this->encrypt->encode(json_encode($txt)));

        // Write the zip file to a folder on your server. Name it "my_backup.zip"
        $this->zip->archive($path.'.zip');

        // Download the file to your desktop. Name it "my_backup.zip"
        $this->zip->download($path.'.zip');
        exit();

        // print_r(json_encode($x));
        // exit;
        // $txt = "";
        // $txt.=$this->msync->memberType($coopId).'-x-'; // 0
        // $txt.=$this->msync->savingType($coopId).'-x-'; // 1
        // $txt.=$this->msync->member($coopId).'-x-'; // 2
        // $txt.=$this->msync->journal($coopId).'-x-'; // 3
        // $txt.=$this->msync->supplier($coopId).'-x-'; // 4
        // $txt.=$this->msync->inventory($coopId).'-x-'; // 5
        // $txt.=$this->msync->purchase($coopId).'-x-'; // 6
        // $txt.=$this->msync->sale($coopId).'-x-'; // 7
        // $txt.=$this->msync->saving($coopId).'-x-'; // 8
        // $txt.=$this->msync->transaction($coopId).'-x-'; // 9
        // echo $txt;
        // print base64_encode($txt);
        // print $content;
        // echo base64_encode($txt);
    }

    public function import_sync_server_post()
    {
        // $this->load->model('cooperatives/msync');
        // var_dump($this->file);
        $field = 'filedatas';
        $db_coop = date('Ymdhis') . '_' . $this->file['filedatas']['name'];

        $config['upload_path'] = './syncdb/';
        $config['allowed_types'] = 'zip';
        $config['max_size'] = '100000';
        $config['file_name']  = $db_coop;

        $this->load->library('upload', $config);

        if (! $this->upload->do_upload($field)) {
            $error = array('success'=>false,'error' => $this->upload->display_errors());
            $this->response($error, 200);
        } else {
            $data = $this->upload->data();
            $xx = $this->readContent($data);
            // exit;
            $error = array('success'=>false,'error' => $this->upload->display_errors());
            $this->response($error, 200);
        }
    }

    public function readContent($data)
    {
        $primaryKeys = array(
            "coop_approval" => array("ApprovalID"),
            "coop_approval_staff" => array(),
            "coop_area_member" => array("CoopAreaMemberID"),
            "coop_cash_source" => array("CashSourceID"),
            "coop_deposit_interest" => array(),
            "coop_documents" => array("DocCoopID"),
            "coop_interest_type" => array(),
            "coop_inventory" => array("IDInventory"),
            "coop_inventorycat" => array("IDInventoryCat"),
            "coop_inventorydeprec" => array("IDDepreciation"),
            "coop_inventorydeprecitem" => array(),
            "coop_loan_installment" => array("LoanInstallmentID"),
            "coop_loan_type" => array("LoanTypeID"),
            "coop_loan_type_members" => array("LoanTypeMemberID"),
            "coop_member" => array("MemberID"),
            "coop_member_loan" => array("MemberLoanID"),
            "coop_member_saving" => array("MemberSavingID"),
            "coop_member_transaction" => array("MemberTransactionID"),
            "coop_member_type" => array("TypeID"),
            "coop_saving_type" => array("SavingTypeID"),
            "coop_saving_type_members" => array("SavingTypeMemberID"),
            "coop_shu" => array("ShuID"),
            "coop_shu_component" => array("ShuComponentID"),
            "coop_shu_distribution" => array(),
            "coop_stock_opname" => array("OpnameID"),
            "coop_stock_opname_items" => array(),
            "coop_supplier" => array("IDSupplier"),
            "coop_sync" => array("SyncID"),
            "coop_sync_farmer" => array(),
            "coop_transactions"  => array("TransactionID")
        );

        $fileName = $data['file_name'];

        $path = getcwd() .'/syncdb/' .$fileName;

        $this->load->helper('file');
        $this->load->library('encrypt');
        $this->load->library('zip');

        $zip = new ZipArchive;
        if ($zip->open($path) === true) {
            $zip->extractTo(getcwd() .'/syncdb/');
            $zip->close();
        } else {
            echo 'failed to open file';
        }

        $content = read_file(getcwd() .'\syncdb\\' .str_replace('.zip', '', $data['client_name']));

        $content = $this->encrypt->decode($content);

        $data = json_decode($content, true);

        extract($primaryKeys);

        foreach ($data as $k => $table) {
            foreach ($table as $kk => $val) {
                if (count($val) > 0) {
                    foreach ($val as $rk => $rval) {
                        foreach ($$kk as $tkey => $primaries) {
                            unset($rval[$primaries]);
                        }

                        //remove any foreign keys
                        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

                        //check for latest updated
                        $check_for_exist = $this->db->get_where($kk, array('uid' => $rval['uid']))->num_rows();

                        if ($check_for_exist > 0) {
                            $this->db->update($kk, $rval);
                        } else {
                            $this->db->insert($kk, $rval);
                        }
                    }
                }
            }
        }
        unlink($path);
        $results['success'] = true;
        $results['message'] = 'Import Success';
        $this->response($results, 200);
    }

        // //menerima hasil file export dari koperasi lokal untuk selanjutnya disimpan di koperasi server
        // $config['upload_path'] = $this->config->item('coop_sync_importexport');
        // $config['allowed_types'] = '*';
        // $this->load->library('upload', $config);



//         if ( ! $this->upload->do_upload('filedata') && $this->upload->display_errors()!='<p>You did not select a file to upload.</p>')
//         {
//                 $results['success'] = false;
//                 $results['message'] = $this->upload->display_errors();
//                 $this->response($results, 200);
//         }
//         else
//         {
// //                $data = array('upload_data' => $this->upload->data());
//                 $datafile = $this->upload->data();
//                 if(isset($datafile['file_name']))
//                 {
//                     $f = $datafile['file_name'];
//                 } else {
//                     $f = null;
//                 }

//                 $content = file_get_contents($config['upload_path'].'/'.$f);
//                 $contentArr = explode('-x-', base64_decode($content));
//                 // echo base64_decode($content);

//                 header("Content-type: text/plain");
//                 header("Content-Disposition: attachment; filename=sync_coop_import_local_".date('YmdH:m:s'));

//                 $txt = "";
//                 $txt.=$this->msync->insertMemberType(json_decode($contentArr[0])).'-x-'; // 0
//                 $txt.=$this->msync->insertSavingType(json_decode($contentArr[1])).'-x-'; // 1
//                 $txt.=$this->msync->insertMember(json_decode($contentArr[2])).'-x-'; // 2
//                 $txt.=$this->msync->insertJournal(json_decode($contentArr[3])).'-x-'; // 3
//                 $txt.=$this->msync->insertSupplier(json_decode($contentArr[4])).'-x-'; // 4
//                 $txt.=$this->msync->insertInventory(json_decode($contentArr[5])).'-x-'; // 5
//                 // $txt.=$this->msync->insertPurchase(json_decode($contentArr[6])).'-x-'; // 6
//                 // $txt.=$this->msync->sale($coopId).'-x-'; // 7
//                 $txt.=$this->msync->insertSaving(json_decode($contentArr[8])).'-x-'; // 8
//                 $txt.=$this->msync->insertTransaction(json_decode($contentArr[9])).'-x-'; // 9
//                 echo $txt;
//                 // print base64_encode($txt);


//                 // print_r(json_decode($contentArr[0]));
//                 // echo count(json_decode($contentArr[0]));

//                 // if($data) $this->response($data, 200);
//                 // else $this->response(array('error' => 'import data failed'), 404);
//         }
    // }

    public function import_sync_local_feedback_post()
    {
        // - dijalankan oleh coop local.
        // - import file feedback hasil dari proses import ke server
        $config['upload_path'] = $this->config->item('coop_sync_importexport');
        $config['allowed_types'] = '*';

        $this->load->library('upload', $config);

        if (! $this->upload->do_upload('filedata') && $this->upload->display_errors()!='<p>You did not select a file to upload.</p>') {
            $results['success'] = false;
            $results['message'] = $this->upload->display_errors();
            $this->response($results, 200);
        } else {
            $datafile = $this->upload->data();
            if (isset($datafile['file_name'])) {
                $f = $datafile['file_name'];
            } else {
                $f = null;
            }

            $content = file_get_contents($config['upload_path'].'/'.$f);
            $contentArr = explode('-x-', base64_decode($content));

                // $txt = "";
                $txt = $this->msync->insertFeedbackMemberType(json_decode($contentArr[0])).'-x-'; // 0
                if (!$txt) {
                    $this->response(array('status' => false), 200);
                    die;
                }

            $txt = $this->msync->insertFeedbackSavingType(json_decode($contentArr[1])).'-x-'; // 1
                if (!$txt) {
                    $this->response(array('status' => false), 200);
                    die;
                }

            $txt = $this->msync->insertFeedbackMember(json_decode($contentArr[2])).'-x-'; // 2
                if (!$txt) {
                    $this->response(array('status' => false), 200);
                    die;
                }

            $txt = $this->msync->insertFeedbackJournal(json_decode($contentArr[3])).'-x-'; // 3
                if (!$txt) {
                    $this->response(array('status' => false), 200);
                    die;
                }

            $txt.=$this->msync->insertFeedbackSupplier(json_decode($contentArr[4])).'-x-'; // 4
                if (!$txt) {
                    $this->response(array('status' => false), 200);
                    die;
                }

            $txt.=$this->msync->insertFeedbackInventory(json_decode($contentArr[5])).'-x-'; // 5
                if (!$txt) {
                    $this->response(array('status' => false), 200);
                    die;
                }

                // $txt.=$this->msync->insertPurchase(json_decode($contentArr[6])).'-x-'; // 6
                // $txt.=$this->msync->sale($coopId).'-x-'; // 7
                $txt.=$this->msync->insertFeedbackSaving(json_decode($contentArr[8])).'-x-'; // 8
                if (!$txt) {
                    $this->response(array('status' => false), 200);
                    die;
                }

            $txt.=$this->msync->insertFeedbackTransaction(json_decode($contentArr[9])).'-x-'; // 9
                if (!$txt) {
                    $this->response(array('status' => false), 200);
                    die;
                }
                // echo $txt;

                $this->response(array('status' => true), 200);
            die;
        }
    }

    public function sync_get($url=null)
    {
        //access : local coop
        $d = $this->mcooperatives->checkSync(getCoopID());
        if ($d['status']) {
            $st = 'Berhasil';
        } else {
            $st = 'Berhasil';
        }
        $txt = "<b>Status Sinkronisasi</b> : $st <br><br>
        <b>Hasil Pembaharuan Data</b>:<br>
        Chart of Account: ".$d['totalCoa']."<br>
        Farmer: ".$d['totalFarmer']."<br>
        Member: ".$d['totalMember']."<br>
        Supplier: ".$d['totalSupplier']."<br>
        Journal: ".$d['totalJurnal']."<br>
        Purchase: ".$d['totalPurchase']."<br>
        Sale: ".$d['totalSale']."<br>
        Saving Member: ".$d['totalSaving']."<br>
        Deposit/Withdrawal: ".$d['totalTransaction']."<br>
        Inventory: ".$d['totalInventory']."<br> ";
        echo $txt;
        return true;
        if (intval($url)==1) {
            echo $txt;
        } else {
            $this->response(array('status'=>$d['status'], 'message'=>$txt), 200);
        }
    }

    public function check_data_sync_get()
    {
        // access : server
        // $dataType = $this->get('dataType');
        $farmer = false;
        $sales = false;
        $purchase = false;


        // $coop = $this->mcooperatives->checkSyncCoop($this->get('CoopID'));

        // $coa = $this->mcooperatives->checkSyncCoa($this->get('CoopID')); //

        // $farmer = $this->mcooperatives->checkSyncDataFarmer($this->get('CoopID'));

        /*
            status :
                false = tidak ada data yg disync
                true = ada data yg disync
        */
        $this->response(array('farmer'=>$farmer, 'sales'=>$sales, 'purchase'=>$purchase), 200);
    }

    public function get_data_sync_get()
    {
        /*
            access : server
            desc : ambil data yang mau disync
        */

        $CoopID = $this->get('CoopID');
        $dataType = $this->get('dataType');

        //farmer
        if ($dataType=='farmer') {
            $q = $this->mcooperatives->GetFarmerDataSync($CoopID, 'farmer');
        } else {
            $q = false;
        }

        $this->response(array('data'=>$q), 200);
    }

    public function feedback_sync_post()
    {
        /*
            - access : server
            - param $type : farmer,purchase,sales
        */
        $type = $this->post('Type');
        $CoopID = $this->post('CoopID');
        $FarmerID = $this->post('FarmerID');
        // $FarmerData = $this->post('FarmerData');
        if ($type=='farmer') {
            // foreach ($FarmerData as $key => $value) {
                $this->db->where(array('CoopID'=>$CoopID, 'FarmerID'=>$FarmerID));
            $this->db->update('coop_sync_farmer', array('SyncedDate'=>gmdate('Y-m-d H:m:s')));
                // $this->response(array('data'=>$this->db->last_query()), 200);
            // }
        }
    }

    public function receive_journal_sync_post()
    {
        /*
            access : servers
        */
        $CoopID = $this->post('CoopID');
        $data = $this->post('data');

        // $this->db->trans_begin();

        $d['JournalID'] = $data['JournalID'];
        $d['JournalTypeCode'] = $data['JournalTypeCode'];
        $d['JournalDate'] = isset($data['JournalDate']) ? $data['JournalDate'] : null;
        $d['JournalMemo'] = isset($data['JournalMemo']) ? $data['JournalMemo'] : null;
        $d['JournalIsPosted'] = isset($data['JournalIsPosted']) ? $data['JournalIsPosted'] : null;
        $d['JournalPostedDate'] = isset($data['JournalPostedDate']) ? $data['JournalPostedDate'] : null;
        $d['JournalCRBY'] = isset($data['JournalCRBY']) ? $data['JournalCRBY'] : null;
        $d['JournalCRDT'] = isset($data['JournalCRDT']) ? $data['JournalCRDT'] : null;
        $d['JournalUPBY'] = isset($data['JournalUPBY']) ? $data['JournalUPBY'] : null;

        $cek = $this->db->get_where('accounting_journal', array('JournalID'=>$d['JournalID'], 'CoopID'=>$CoopID));
        if ($cek->num_rows()>0) {
            $this->db->where(array('JournalID'=>$d['JournalID'], 'CoopID'=>$CoopID));
            $this->db->update('accounting_journal', $d);
        } else {
            $this->db->insert('accounting_journal', $d);
        }
        // $this->response(array('success'=>$this->db->last_query()),200);
        if (isset($data['Detail'])) {
            // $d['detail'] = $data['Detail'];
            foreach ($data['Detail'] as $key => $value) {
                $werDetail = array(
                    'JournalDetailID'=>$value['JournalDetailID'],
                    'CoopID'=>$value['CoopID']
                );

                $cekdetail = $this->db->get_where('accounting_journal_detail', $werDetail);

                if ($cekdetail->num_rows()>0) {
                    $this->db->where($werDetail);
                    $this->db->update('accounting_journal_detail', $value);
                } else {
                    $this->db->insert('accounting_journal_detail', $value);
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
        // $d['JournalUPDT'] = $data['JournalUPDT'];

           // $this->response($data['JournalID'],200);
         // $this->response($d,200);
        // var_dump($this->post('data'));
         // exit;


         // $this->response(array('success'=>true,'data'=>$data),200);
    }

    public function receive_approval_sync_post()
    {
        /*
            access : servers
        */
        $CoopID = $this->post('CoopID');
        $data = $this->post('data');

        // $this->db->trans_begin();

        $d['ApprovalID'] = $data['ApprovalID'];
        $d['CoopID'] = $data['CoopID'];
        $d['Position'] = isset($data['Position']) ? $data['Position'] : null;
        $d['MinTransaction'] = isset($data['MinTransaction']) ? $data['MinTransaction'] : null;
        $d['MaxTransaction'] = isset($data['MaxTransaction']) ? $data['MaxTransaction'] : null;
        $d['Deposit'] = isset($data['Deposit']) ? $data['Deposit'] : null;
        $d['Withdrawal'] = isset($data['Withdrawal']) ? $data['Withdrawal'] : null;
        $d['CreatedBy'] = isset($data['CreatedBy']) ? $data['CreatedBy'] : null;
        $d['CreatedDate'] = isset($data['CreatedDate']) ? $data['CreatedDate'] : null;
        $d['UpdatedBy'] = isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null;
        $d['UpdatedDate'] = isset($data['UpdatedDate']) ? $data['UpdatedDate'] : null;

        $cek = $this->db->get_where('coop_approval', array('ApprovalID'=>$d['ApprovalID'], 'CoopID'=>$CoopID));
        if ($cek->num_rows()>0) {
            $this->db->where(array('ApprovalID'=>$d['ApprovalID'], 'CoopID'=>$CoopID));
            $this->db->update('coop_approval', $d);
        } else {
            $this->db->insert('coop_approval', $d);
        }
        // $this->response(array('success'=>$this->db->last_query()),200);
        if (isset($data['Detail'])) {
            // $d['detail'] = $data['Detail'];
            foreach ($data['Detail'] as $key => $value) {
                $werDetail = array(
                    'ApprovalID'=>$value['ApprovalID'],
                    'CoopID'=>$value['CoopID'],
                    'Detail'=>$value['Detail']
                );

                $cekdetail = $this->db->get_where('coop_approval_staff', $werDetail);

                if ($cekdetail->num_rows()>0) {
                    $this->db->where($werDetail);
                    $this->db->update('coop_approval_staff', $value);
                } else {
                    $this->db->insert('coop_approval_staff', $value);
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_coa_sync_post()
    {
        /*
            receive COA data from local coop to server
            - access : server
        */
        $CoopID = $this->post('CoopID');
        $CoaData = $this->post('CoaData');

        $coaSynced = array();

        $this->db->trans_begin();
        // $i=0;
        // foreach ($CoaData as $key => $value) {
            $data = array(
                    'CoaID'=>$CoaData['CoaID'],
                    'CoopID'=>$CoaData['CoopID'],
                    'CoaCode'=>$CoaData['CoaCode'],
                    'CoaCodeParent'=> isset($CoaData['CoaCodeParent']) ? $CoaData['CoaCodeParent'] : null,
                    'CoaGroupID'=> isset($CoaData['CoaGroupID']) ? $CoaData['CoaGroupID'] : null,
                    'CoaGroupCode'=> isset($CoaData['CoaGroupCode']) ? $CoaData['CoaGroupCode'] : null,
                    'CurrencyID'=> isset($CoaData['CurrencyID']) ? $CoaData['CurrencyID'] : null,
                    'CoaTitle'=> isset($CoaData['CoaTitle']) ? $CoaData['CoaTitle'] : null,
                    'CoaType'=> isset($CoaData['CoaType']) ? $CoaData['CoaType'] : null,
                    'CoaRelated'=> isset($CoaData['CoaRelated']) ?  $CoaData['CoaRelated'] : null,
                    'CoaStatus'=>isset($CoaData['CoaStatus']) ?  $CoaData['CoaStatus'] : null,
                    'CoaForReceived'=> isset($CoaData['CoaForReceived']) ? $CoaData['CoaForReceived'] : null,
                    'CoaForSpent'=> isset($CoaData['CoaForSpent']) ?  $CoaData['CoaForSpent'] : null,
                    'CoaForCash'=> isset($CoaData['CoaForCash']) ?  $CoaData['CoaForCash'] : null,
                    'CoaForNonCash'=> isset($CoaData['CoaForNonCash']) ?  $CoaData['CoaForNonCash'] : null,
                    'CoaOrder'=>isset($CoaData['CoaOrder']) ?  $CoaData['CoaOrder'] : null,
                    'CoaReportDisplay'=>isset($CoaData['CoaReportDisplay']) ?  $CoaData['CoaReportDisplay'] : null
                );

        $q = $this->db->get_where('accounting_coa', array('CoaID'=>$CoaData['CoaID'], 'CoopID'=>$CoopID));
        if ($q->num_rows()>0) {
            $this->db->where(array('CoaID'=>$CoaData['CoaID'], 'CoopID'=>$CoopID));
            $this->db->update('accounting_coa', $data);
        } else {
            $this->db->insert('accounting_coa', $data);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false, 'data'=>null), 200);
        } else {
            $this->db->trans_commit();
            $coaSynced['CoaID'] = $CoaData['CoaID'];
            $coaSynced['CoopID'] = $CoopID;

            $this->response(array('success'=>true, 'data'=>$coaSynced), 200);
        }

            //balance history
            // foreach ($CoaData['Balance'] as $kBalance => $vBalance) {
            //     $dataBalance = array(
            //             'CoaCode'=>$vBalance['CoaCode'],
            //             'CoaBalanceAmount'=>$vBalance['CoaBalanceAmount'],
            //             'DateCreated'=>$vBalance['DateCreated'],
            //             'CoaID'=>$vBalance['CoaID'],
            //             'CoopID'=>$vBalance['CoopID']
            //         );

            //     $qBal = $this->db->get_where('accounting_coa_balance',array('CoaID'=>$CoaData['CoaID'],'CoopID'=>$CoopID,'DateCreated'=>$vBalance['DateCreated']));
            //     if($qBal->num_rows()>0)
            //     {
            //         $this->db->where(array('CoaID'=>$CoaData['CoaID'],'CoopID'=>$CoopID));
            //         $this->db->update('accounting_coa_balance',$data);
            //     } else {
            //         $this->db->insert('accounting_coa_balance',$data);
            //     }
            // }
            // end balance history

            // $i++;
        // }




        // return array('numrows'=>$i,'coaSynced'=>$coaSynced);
        // $this->response(array('coaSynced'=>$coaSynced,'sql'=>$this->db->last_query()), 200);
    }

    public function receive_purchase_sync_post()
    {
        /*
            access : server
            route : coop local -> server
        */
        $data = $this->post('data');
        $CoopID = $this->post('CoopID');

        $this->db->trans_begin();

        $d = array(
            'PurchaseID'=>$data['PurchaseID'],
            'CoopID'=>$data['CoopID'],
            'OrgType'=> isset($data['OrgType']) ? $data['OrgType'] : null,
            'OrgID'=> isset($data['OrgID']) ? $data['OrgID'] : null,
            'JournalID'=> isset($data['JournalID']) ? $data['JournalID'] : null,
            'Number'=> isset($data['Number']) ? $data['Number'] : null,
            'SupplierID'=> isset($data['SupplierID']) ? $data['SupplierID'] : null,
            'DueDate'=> isset($data['DueDate']) ? $data['DueDate'] : null,
            'Date'=> isset($data['Date']) ? $data['Date'] : null,
            'Diskon'=> isset($data['Diskon']) ? $data['Diskon'] : null,
            'Pajak'=> isset($data['Pajak']) ? $data['Pajak'] : null,
            'Total'=> isset($data['Total']) ? $data['Total'] : null,
            'Pembayaran'=> isset($data['Pembayaran']) ? $data['Pembayaran'] : null,
            'SisaBayar'=> isset($data['SisaBayar']) ? $data['SisaBayar'] : null,
            'TipeBayar'=> isset($data['TipeBayar']) ? $data['TipeBayar'] : null,
            'DateCreated'=> isset($data['DateCreated']) ? $data['DateCreated'] : null,
            'CreatedBy'=> isset($data['CreatedBy']) ? $data['CreatedBy'] : null,
            'DateUpdated'=> isset($data['DateUpdated']) ? $data['DateUpdated'] : null,
            'LastModifiedBy'=>isset($data['LastModifiedBy']) ? $data['LastModifiedBy'] : null
        );

        $wer = array('PurchaseID'=>$d['PurchaseID'],'CoopID'=>$CoopID);
        $cek = $this->db->get_where('ktv_purchase', $wer);

        if ($cek->num_rows()>0) {
            $this->db->where($wer);
            $this->db->update('ktv_purchase', $d);
        } else {
            $this->db->insert('ktv_purchase', $d);
        }

        if (isset($data['Detail'])) {
            foreach ($data['detail'] as $key => $value) {
                $werDetail = array('DetailId'=>$value['DetailId'],'CoopID'=>$value['CoopID'],'PurchaseId'=>$value['PurchaseId']);

                $detail = array(
                        'DetailId'=>$value['DetailId'],
                        'CoopID'=>$value['CoopID'],
                        'PurchaseId'=>$value['PurchaseId'],
                        'InventoryID'=>$value['InventoryID'],
                        'Qty'=>$value['Qty'],
                        'Price'=>$value['Price']
                    );

                $cekdetail = $this->db->get_where('ktv_purchase_detail', $werDetail);

                if ($cekdetail->num_rows()>0) {
                    $this->db->where($werDetail);
                    $this->db->update('ktv_purchase_detail', $detail);
                } else {
                    $this->db->insert('ktv_purchase_detail', $detail);
                }
            }
        }


        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_sale_sync_post()
    {
        /*
            access : server
            route : coop local -> server
        */
        $data = $this->post('data');
        $CoopID = $this->post('CoopID');
        // $this->response($data['data']['SaleId'], 200);
        // exit;
        $this->db->trans_begin();

        $dataSale = array(
            'SaleId'=>$data['data']['SaleId'],
            'CoopID'=>$data['data']['CoopID'],
            'OrgType'=> isset($data['data']['OrgType']) ? $data['data']['OrgType'] : null,
            'OrgID'=> isset($data['data']['OrgID']) ? $data['data']['OrgID'] : null,
            'JournalID'=> isset($data['data']['JournalID']) ? $data['data']['JournalID'] : null,
            'Number'=> isset($data['data']['Number']) ? $data['data']['Number'] : null,
            'CustomerID'=> isset($data['data']['CustomerID']) ? $data['data']['CustomerID'] : null,
            // 'DueDate'=> isset($data['data']['OrgType']) ? $data['data']['DueDate'] : null,
            'Date'=> isset($data['data']['Date']) ? $data['data']['Date'] : null,
            'Diskon'=> isset($data['data']['Diskon']) ? $data['data']['Diskon'] : null,
            'Pajak'=> isset($data['data']['Pajak']) ? $$data['data']['Pajak'] : null,
            'Total'=> isset($data['data']['Total']) ? $data['data']['Total'] : null,
            'Pembayaran'=> isset($data['data']['Pembayaran']) ? $data['data']['Pembayaran'] : null,
            'SisaBayar'=> isset($data['data']['SisaBayar']) ? $data['data']['SisaBayar'] : null,
            // 'TipeBayar'=>$rSale->TipeBayar,
            'DateCreated'=> isset($data['data']['DateCreated']) ? $data['data']['DateCreated'] : null,
            'CreatedBy'=> isset($data['data']['CreatedBy']) ? $data['data']['CreatedBy'] : null,
            'DateUpdated'=> isset($data['data']['DateUpdated']) ? $data['data']['DateUpdated'] : null,
            'LastModifiedBy'=> isset($data['data']['LastModifiedBy']) ? $data['data']['LastModifiedBy'] : null
        );

        $wer = array('SaleId'=>$data['data']['SaleId'],'CoopID'=>$CoopID);
        $cek = $this->db->get_where('ktv_sale', $wer);

        if ($cek->num_rows()>0) {
            $this->db->where($wer);
            $this->db->update('ktv_sale', $dataSale);
        } else {
            $this->db->insert('ktv_sale', $dataSale);
        }

        if (isset($data['Detail'])) {
            foreach ($data['Detail'] as $key => $value) {
                $werDetail = array('DetailID'=>$value['DetailId'],'CoopID'=>$value['CoopID'],'SaleId'=>$value['SaleId']);
                $detail = array(
                    'DetailID'=>$value['DetailID'],
                    'CoopID'=>$value['CoopID'],
                    'SaleId'=>$value['SaleId'],
                    'InventoryID'=>$value['InventoryID'],
                    'Qty'=>$value['Qty'],
                    'Price'=>$value['Price'],
                    'Problem'=>$value['Problem'],
                    'Solution'=>$value['Solution'],
                    'DateStart'=>$value['DateStart'],
                    'DateEnd'=>$value['DateEnd']
                );

                $cekdetail = $this->db->get_where('ktv_sale_detail', $werDetail);


                if ($cekdetail->num_rows()>0) {
                    $this->db->where($werDetail);
                    $this->db->update('ktv_sale_detail', $detail);
                } else {
                    $this->db->insert('ktv_sale_detail', $detail);
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_inventory_sync_post()
    {
        $this->db->trans_begin();

        $data = $this->post('data');
        $CoopID = $this->post('CoopID');

        $DataInventory = array(
                'InventoryID' => $data['InventoryID'],
                'OrgType' => $data['OrgType'],
                'Status' => $data['Status'],
                'OrgID' => $data['OrgID'],
                'CoopID' => $data['CoopID'],
                'JournalID' => isset($data['JournalID']) ? $data['JournalID'] : null,
                'Number' => $data['Number'],
                'SerialNumber' => isset($data['JournalID']) ? $data['SerialNumber'] : null,
                'Name' => isset($data['JournalID']) ? $data['Name'] : null,
                'Description' => isset($data['JournalID']) ? $data['Description'] : null,
                'UnitMeasurementID' => isset($data['JournalID']) ? $data['UnitMeasurementID'] : null,
                'IsInventory' => isset($data['JournalID']) ? $data['IsInventory'] : null,
                'IsSell' => isset($data['JournalID']) ? $data['IsSell'] : null,
                'IsBuy' => isset($data['JournalID']) ? $data['IsBuy'] : null,
                'IsRemoved' => isset($data['JournalID']) ? $data['IsRemoved'] : null,
                'RemoveReason' => isset($data['JournalID']) ? $data['RemoveReason'] : null,
                'coaIDAsset' => isset($data['JournalID']) ? $data['coaIDAsset'] : null,
                'coaIDAkumDepres' => isset($data['JournalID']) ? $data['coaIDAkumDepres'] : null,
                'coaIDBebanDepres' => isset($data['JournalID']) ? $data['coaIDBebanDepres'] : null,
                'Stock' => isset($data['JournalID']) ? $data['Stock'] : null,
                'Images'  => isset($data['JournalID']) ? $data['Images'] : null,
                'Cost'  => isset($data['JournalID']) ? $data['Cost'] : null,
                'UnitMeasure'  => isset($data['JournalID']) ? $data['UnitMeasure'] : null,
                'MinStock'  => isset($data['JournalID']) ? $data['MinStock'] : null,
                'SupplierID' => isset($data['JournalID']) ? $data['SupplierID'] : null,
                'SupplierName'  => isset($data['JournalID']) ? $data['SupplierName'] : null,
                'SellingPrice' => isset($data['JournalID']) ? $data['SellingPrice'] : null,
                'SelingTax' => isset($data['JournalID']) ? $data['SelingTax'] : null,
                'Notes' => isset($data['JournalID']) ? $data['Notes'] : null,
                'YearBuy' => isset($data['JournalID']) ? $data['YearBuy'] : null,
                'MonthBuy' => isset($data['JournalID']) ? $data['MonthBuy'] : null,
                'DateBuy' => isset($data['JournalID']) ? $data['DateBuy'] : null,
                'CategoryID' => isset($data['JournalID']) ? $data['CategoryID'] : null,
                'BuyTax'  => isset($data['JournalID']) ? $data['BuyTax'] : null,
                'Location'  => isset($data['JournalID']) ? $data['Location'] : null,
                'Residu'  => isset($data['JournalID']) ? $data['Residu'] : null,
                'Umur' => isset($data['JournalID']) ? $data['Umur'] : null,
                'AkumulasiBeban' => isset($data['JournalID']) ? $data['AkumulasiBeban'] : null,
                'BebanBerjalan' => isset($data['JournalID']) ? $data['BebanBerjalan'] : null,
                'NilaiBuku' => isset($data['JournalID']) ? $data['NilaiBuku'] : null,
                'BebanPerBulan' => isset($data['JournalID']) ? $data['BebanPerBulan'] : null,
                'AkumulasiAkhir' => isset($data['JournalID']) ? $data['AkumulasiAkhir'] : null,
                'IsPaket'  => isset($data['JournalID']) ? $data['IsPaket'] : null,
                'ParentInventoryID' => isset($data['JournalID']) ? $data['ParentInventoryID'] : null,
                'ParentConvertion' =>isset($data['JournalID']) ? $data['ParentConvertion'] : null,
                'EvaluateType' => isset($data['JournalID']) ? $data['EvaluateType'] : null,
                'EvaluateReason' => isset($data['JournalID']) ? $data['EvaluateReason'] : null,
                'EvaluateSoldPrice' => isset($data['JournalID']) ? $data['EvaluateSoldPrice'] : null,
                'CreatedBy' => isset($data['JournalID']) ? $data['CreatedBy'] : null,
                'CreatedDate' => isset($data['JournalID']) ? $data['CreatedDate'] : null,
                'UpdatedBy' => isset($data['JournalID']) ? $data['UpdatedBy'] : null,
                'UpdatedDate' => isset($data['JournalID']) ? $data['UpdatedDate'] : null
        );


        $qcek = $this->db->get_where('ktv_inventory', array('InventoryID'=>$data['InventoryID'], 'CoopID'=>$data['CoopID']));
        if ($qcek->num_rows()>0) {
            $this->db->where(array('InventoryID'=>$data['InventoryID'], 'CoopID'=>$data['CoopID']));
            $this->db->update('ktv_inventory', $DataInventory);
        } else {
            $this->db->insert('ktv_inventory', $DataInventory);
        }


        if (isset($data['stok'])) {
            foreach ($data['stok'] as $key => $value) {
                $werDetail = array('StokID'=>$value['StokID'],'InventoryID'=>$value['InventoryID'],'CoopID'=>$value['CoopID']);

                $detail = array(
                    'StokID'=>$value['StokID'],
                    'InventoryID'=>$value['InventoryID'],
                    'CoopID'=>$value['CoopID'],
                    'Type'=>$value['Type'],
                    'Qty'=>$value['Qty'],
                    'ID'=>$value['ID'],
                    'Awal'=>$value['Awal'],
                    'Jumlah'=>$value['Jumlah'],
                    'Akhir'=>$value['Akhir'],
                    'CreatedBy'=>$value['CreatedBy']
                );
               // $this->db->insert('ktv_inventory_stok',$detail);

               $cekdetail = $this->db->get_where('ktv_purchase_detail', $werDetail);

                if ($cekdetail->num_rows()>0) {
                    $this->db->where($werDetail);
                    $this->db->update('ktv_purchase_detail', $detail);
                } else {
                    $this->db->insert('ktv_purchase_detail', $detail);
                }
            }
        }


        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_membersaving_sync_post()
    {
        $this->db->trans_begin();

        $data = $this->post('data');
        $CoopID = $this->post('CoopID');

        $d = array(
            'memberSavingID' => $data['data']['memberSavingID'],
            'CoopID' => $data['data']['CoopID'],
            'memberID' => $data['data']['memberID'],
            'savingTypeID' => $data['data']['savingTypeID'],
            'memberSavingRegisteredDate' => $data['data']['memberSavingRegisteredDate'],
            'AmountSaving' => $data['data']['AmountSaving'],
            'memberSavingNo' => $data['data']['memberSavingNo'],
            'memberSavingStatus' => $data['data']['memberSavingStatus'],
            'memberSavingRemark' => $data['data']['memberSavingRemark'],
            'CreatedBy' => $data['data']['CreatedBy'],
            'CreatedDate' => $data['data']['CreatedDate'],
            'UpdatedDate' => $data['data']['UpdatedDate']
        );

        $qcek = $this->db->get_where('coop_member_saving', array('memberSavingID'=>$data['data']['memberSavingID'], 'CoopID'=>$data['data']['CoopID']));
        if ($qcek->num_rows()>0) {
            $this->db->where(array('memberSavingID'=>$data['data']['memberSavingID'], 'CoopID'=>$data['data']['CoopID']));
            $this->db->update('coop_member_saving', $d);
        } else {
            $this->db->insert('coop_member_saving', $d);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_supplier_sync_post()
    {
        $this->db->trans_begin();

        $data = $this->post('data');
        $CoopID = $this->post('CoopID');

        $d = array(
            'SupplierID' => $data['SupplierID'],
            'CoopID' => $data['CoopID'],
            'OrgType' => $data['OrgType'],
            'OrgID' => $data['OrgID'],
            'Name' => $data['Name'],
            'Address' => $data['Address'],
            'Phone' => $data['Phone'],
            'Email' => $data['Email'],
            'VillageID' => $data['VillageID'],
            'Note' => $data['Note']
        );

        $wer = array('SupplierID'=>$data['SupplierID'],'CoopID'=>$data['CoopID']);

        $qcek = $this->db->get_where('ktv_supplier', $wer);
        if ($qcek->num_rows()>0) {
            $this->db->where($wer);
            $this->db->update('ktv_supplier', $d);
        } else {
            $this->db->insert('ktv_supplier', $d);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_membertype_sync_post()
    {
        $this->db->trans_begin();

        $data = $this->post('data');

        $d = array(
            'typeID' => $data['typeID'],
            'coopID' => $data['coopID'],
            'typeCode' => isset($data['typeCode']) ? $data['typeCode'] : null,
            'typeName' => isset($data['typeName']) ? $data['typeName'] : null,
            'typeMaxProfit' => isset($data['typeMaxProfit']) ? $data['typeMaxProfit'] : null,
            'typeSimPokokAmount' => isset($data['typeSimPokokAmount']) ? $data['typeSimPokokAmount'] : null,
            'typeSimWajibAmount' => isset($data['typeSimWajibAmount']) ? $data['typeSimWajibAmount'] : null,
            'typeSimWajibPeriod' => isset($data['typeSimWajibPeriod']) ? $data['typeSimWajibPeriod'] : null,
            'typeSimPokokPeriod' => isset($data['typeSimPokokPeriod']) ? $data['typeSimPokokPeriod'] : null,
            'RegistrationFee' => isset($data['RegistrationFee']) ? $data['RegistrationFee'] : null,
            'CoaRegMemberTypeID' => isset($data['CoaRegMemberTypeID']) ? $data['CoaRegMemberTypeID'] : null,
            'CreatedBy' => isset($data['CreatedBy']) ? $data['CreatedBy'] : null,
            'CreatedDate' => isset($data['CreatedDate']) ? $data['CreatedDate'] : null,
            'UpdatedBy' => isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null,
            'UpdatedBy' => isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null,
            'UpdatedDate' => isset($data['UpdatedDate']) ? $data['UpdatedDate'] : null
        );


        $wer = array('typeID'=>$data['typeID'],'coopID'=>$data['coopID']);

        $qcek = $this->db->get_where('coop_member_type', $wer);
        if ($qcek->num_rows()>0) {
            $this->db->where($wer);
            $this->db->update('coop_member_type', $d);
        } else {
            $this->db->insert('coop_member_type', $d);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_membercoop_sync_post()
    {
        $this->db->trans_begin();
        // $this->response(array('adasd'=>$this->post('data')), 200);
        // exit;

        $data = $this->post('data');
        // $this->response($data, 200);
        $d = array(
            'memberID' => $data['memberID'],
            'CoopID' => $data['CoopID'],
            'memberRefID' => isset($data['memberRefID']) ? $data['memberRefID'] : null,
            'farmerID' => isset($data['farmerID']) ? $data['farmerID'] : null,
            'primaryNo' => isset($data['primaryNo']) ? $data['primaryNo'] : null,
            'registeredDate' => isset($data['registeredDate']) ? $data['registeredDate'] : null,
            'typeID' => isset($data['typeID']) ? $data['typeID'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'identityType' => isset($data['identityType']) ? $data['identityType'] : null,
            'identityNumber' => isset($data['identityNumber']) ? $data['identityNumber'] : null,
            'gender' => isset($data['gender']) ? $data['gender'] : null,
            'placeOfBirth' => isset($data['placeOfBirth']) ? $data['placeOfBirth'] : null,
            'address' => isset($data['address']) ? $data['address'] : null,
            'villageID' => isset($data['villageID']) ? $data['villageID'] : null,
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'maritalStatus' => isset($data['maritalStatus']) ? $data['maritalStatus'] : null,
            'education' => isset($data['education']) ? $data['education'] : null,
            'job' => isset($data['job']) ? $data['job'] : null,
            'status' => isset($data['status']) ? $data['status'] : null,
            'remark' => isset($data['remark']) ? $data['remark'] : null,
            'signature' => isset($data['signature']) ? $data['signature'] : null,
            'ResignationDate' => isset($data['ResignationDate']) ? $data['ResignationDate'] : null,
            'ResignationReason' => isset($data['ResignationReason']) ? $data['ResignationReason'] : null,
            'familyName' => isset($data['familyName']) ? $data['familyName'] : null,
            'familyRelation' => isset($data['familyRelation']) ? $data['familyRelation'] : null,
            'familyIdentityType' => isset($data['familyIdentityType']) ? $data['familyIdentityType'] : null,
            'familyIdentityNumber' => isset($data['familyIdentityNumber']) ? $data['familyIdentityNumber'] : null,
            'familyAddress' => isset($data['familyAddress']) ? $data['familyAddress'] : null,
            'familyPhone' => isset($data['familyPhone']) ? $data['familyPhone'] : null,
            'savingPokok' => isset($data['savingPokok']) ? $data['savingPokok'] : null,
            'savingWajib' => isset($data['savingWajib']) ? $data['savingWajib'] : null,
            'uangPangkal' => isset($data['uangPangkal']) ? $data['uangPangkal'] : null,
            'CreatedDate' => isset($data['CreatedDate']) ? $data['CreatedDate'] : null,
            'UpdatedBy' => isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null,
            'UpdatedBy' => isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null,
            'UpdatedDate' => isset($data['UpdatedDate']) ? $data['UpdatedDate'] : null
        );

        // if ($this->db->trans_status() === FALSE)
        //     {
        //             $this->db->trans_rollback();
        //             $this->response(array('success'=>$d), 200);
        //     }  else
        //         {
        //                 $this->db->trans_commit();
        //                 $this->response(array('success'=>$d), 200);
        //         }


         $wer = array('memberID'=>$data['memberID'],'CoopID'=>$data['CoopID']);

        $qcek = $this->db->get_where('coop_member', $wer);
        if ($qcek->num_rows()>0) {
            $this->db->where($wer);
            $this->db->update('coop_member', $d);
        } else {
            $this->db->insert('coop_member', $d);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_member_sync_get()
    {
        $this->db->trans_begin();

        $data = $this->post('data');
        $this->response(array('asda'=>122), 200);
        $d = array(
            'memberID' => $data['memberID'],
            'CoopID' => $data['CoopID'],
            'memberRefID' => isset($data['memberRefID']) ? $data['memberRefID'] : null,
            'farmerID' => isset($data['farmerID']) ? $data['farmerID'] : null,
            'primaryNo' => isset($data['primaryNo']) ? $data['primaryNo'] : null,
            'registeredDate' => isset($data['registeredDate']) ? $data['registeredDate'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'identityType' => isset($data['identityType']) ? $data['identityType'] : null,
            'identityNumber' => isset($data['identityNumber']) ? $data['identityNumber'] : null,
            'gender' => isset($data['gender']) ? $data['gender'] : null,
            'placeOfBirth' => isset($data['placeOfBirth']) ? $data['placeOfBirth'] : null,
            'address' => isset($data['address']) ? $data['address'] : null,
            'villageID' => isset($data['villageID']) ? $data['villageID'] : null,
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'maritalStatus' => isset($data['maritalStatus']) ? $data['maritalStatus'] : null,
            'education' => isset($data['education']) ? $data['education'] : null,
            'job' => isset($data['job']) ? $data['job'] : null,
            'status' => isset($data['status']) ? $data['status'] : null,
            'remark' => isset($data['remark']) ? $data['remark'] : null,
            'signature' => isset($data['signature']) ? $data['signature'] : null,
            'ResignationDate' => isset($data['ResignationDate']) ? $data['ResignationDate'] : null,
            'ResignationReason' => isset($data['ResignationReason']) ? $data['ResignationReason'] : null,
            'familyName' => isset($data['familyName']) ? $data['familyName'] : null,
            'familyRelation' => isset($data['familyRelation']) ? $data['familyRelation'] : null,
            'familyIdentityType' => isset($data['familyIdentityType']) ? $data['familyIdentityType'] : null,
            'familyIdentityNumber' => isset($data['familyIdentityNumber']) ? $data['familyIdentityNumber'] : null,
            'familyAddress' => isset($data['familyAddress']) ? $data['familyAddress'] : null,
            'familyPhone' => isset($data['familyPhone']) ? $data['familyPhone'] : null,
            'uangPangkal' => isset($data['uangPangkal']) ? $data['uangPangkal'] : null,
            'CreatedDate' => isset($data['CreatedDate']) ? $data['CreatedDate'] : null,
            'UpdatedBy' => isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null,
            'UpdatedBy' => isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null,
            'UpdatedDate' => isset($data['UpdatedDate']) ? $data['UpdatedDate'] : null
        );


        $wer = array('memberID'=>$data['memberID'],'CoopID'=>$data['CoopID']);

        $qcek = $this->db->get_where('coop_member', $wer);
        if ($qcek->num_rows()>0) {
            $this->db->where($wer);
            $this->db->update('coop_member', $d);
        } else {
            $this->db->insert('coop_member', $d);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_savingtype_sync_post()
    {
        $this->db->trans_begin();

        $data = $this->post('data');
        $CoopID = $this->post('CoopID');


        $d = array(
            'savingTypeID' => $data['savingTypeID'],
            'savingTypeCode' => $data['savingTypeCode'],
            'savingTypeDefault' => isset($data['savingTypeDefault']) ? $data['savingTypeDefault'] : null,
            'coopID' => isset($data['coopID']) ? $data['coopID'] : null,
            'CoaID' => isset($data['CoaID']) ? $data['CoaID'] : null,
            'savingTypeSHU' => isset($data['savingTypeSHU']) ? $data['savingTypeSHU'] : null,
            'savingTypeName' => isset($data['savingTypeName']) ? $data['savingTypeName'] : null,
            'savingTypeMinAmount' => isset($data['savingTypeMinAmount']) ? $data['savingTypeMinAmount'] : null,
            'savingTypeMinTrans' => isset($data['savingTypeMinTrans']) ? $data['savingTypeMinTrans'] : null,
            'savingTypeInterestRate' => isset($data['savingTypeInterestRate']) ? $data['savingTypeInterestRate'] : null,
            'savingTypeInterestCalc' => isset($data['savingTypeInterestCalc']) ? $data['savingTypeInterestCalc'] : null,
            'savingTypeActiveDate' => isset($data['savingTypeActiveDate']) ? $data['savingTypeActiveDate'] : null,
            'savingTypeMonthlyFee' => isset($data['savingTypeMonthlyFee']) ? $data['savingTypeMonthlyFee'] : null,
            'savingTypeInterestPayment' => isset($data['savingTypeInterestPayment']) ? $data['savingTypeInterestPayment'] : null,
            'savingTypeSHUProfit' => isset($data['savingTypeSHUProfit']) ? $data['savingTypeSHUProfit'] : null,
            'savingTypeStatus' => isset($data['savingTypeStatus']) ? $data['savingTypeStatus'] : null,
            'savingRemark' => isset($data['savingRemark']) ? $data['savingRemark'] : null,
            'CreatedBy' => isset($data['CreatedBy']) ? $data['CreatedBy'] : null,
            'CreatedDate' => isset($data['CreatedDate']) ? $data['CreatedDate'] : null,
            'UpdatedBy' => isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null,
            'UpdatedDate' => isset($data['UpdatedDate']) ? $data['UpdatedDate'] : null
        );

        $wer = array('savingTypeID'=>$data['savingTypeID'],'coopID'=>$data['coopID']);

        $qcek = $this->db->get_where('coop_saving_type', $wer);
        if ($qcek->num_rows()>0) {
            $this->db->where($wer);
            $this->db->update('coop_saving_type', $d);
        } else {
            $this->db->insert('coop_saving_type', $d);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function receive_transaction_sync_post()
    {
        $this->db->trans_begin();

        $data = $this->post('data');
        $CoopID = $this->post('CoopID');

        $d = array(
            'MemberTransactionID' => $data['MemberTransactionID'],
            'CoopID' => $data['CoopID'],
            'MemberTransactionType' => $data['MemberTransactionType'],
            'MemberTransactionNumber' => $data['MemberTransactionNumber'],
            'MemberTransactionDate' => $data['MemberTransactionDate'],
            'MemberTransactionName' => isset($data['MemberTransactionName']) ? $data['MemberTransactionName'] : null,
            'MemberTransactionIdentity' => isset($data['MemberTransactionIdentity']) ? $data['MemberTransactionIdentity'] : null,
            'MemberTransactionAddress' => isset($data['MemberTransactionAddress']) ? $data['MemberTransactionAddress'] : null,
            'MemberID' => isset($data['MemberID']) ? $data['MemberID'] : null,
            'MemberSavingID' => isset($data['MemberSavingID']) ? $data['MemberSavingID'] : null,
            'MemberTransactionAmount' => isset($data['MemberTransactionAmount']) ? $data['MemberTransactionAmount'] : null,
            'MemberTransactionCurrentBalance' => isset($data['MemberTransactionCurrentBalance']) ? $data['MemberTransactionCurrentBalance'] : null,
            'MemberTransactionRemark' => isset($data['MemberTransactionRemark']) ? $data['MemberTransactionRemark'] : null,
            'CreatedBy' => isset($data['CreatedBy']) ? $data['CreatedBy'] : null,
            'CreatedDate' => isset($data['CreatedDate']) ? $data['CreatedDate'] : null,
            'UpdatedBy' => isset($data['UpdatedBy']) ? $data['UpdatedBy'] : null,
            'UpdatedDate' => isset($data['UpdatedDate']) ? $data['UpdatedDate'] : null,
            'ApprovedBy' => isset($data['ApprovedBy']) ? $data['ApprovedBy'] : null
        );

        $qcek = $this->db->get_where('coop_member_transaction', array('MemberTransactionID'=>$data['MemberTransactionID'], 'CoopID'=>$data['CoopID']));

        if ($qcek->num_rows()>0) {
            $this->db->where(array('MemberTransactionID'=>$data['MemberTransactionID'], 'CoopID'=>$data['CoopID']));
            $this->db->update('coop_member_transaction', $d);
        } else {
            $this->db->insert('coop_member_transaction', $d);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->response(array('success'=>false), 200);
        } else {
            $this->db->trans_commit();
            $this->response(array('success'=>true), 200);
        }
    }

    public function area_member_post()
    {
        $this->db->trans_begin();

         // print_r($this->post('Desa'));exit;
         for ($i=0; $i < count($this->post('Desa')); $i++) {
             $data = array(
                    'CoopID'=>getCoopID(),
                    'VillageID'=>$this->post('Desa')[$i],
                    'DateCreated'=>date('Y-m-d H:m:s'),
                    // 'CreatedBy'=>
                );
             $this->db->insert('coop_area_member', $data);
            // print_r($data);
         }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $ret = array('success'=>false);
        } else {
            $this->db->trans_commit();
            $ret = array('success'=>true);
        }

        // $ret = $this->mcooperatives->add_area_member($$this->post());
            $this->response($ret, 200);
    }

    public function area_members_get()
    {
        $ret = $this->mcooperatives->get_area_members($this->get('prov'), $this->get('kab'), $this->get('kec'), $this->get('start'), $this->get('limit'));
        // print_r('expression');exit;
        $this->response($ret, 200);
    }

    public function clean_null($str)
    {
        $v = isset($str) ? $str : null;
        return $v;
    }

    public function activation_member_get()
    {
        $this->load->model('member/mmember');

        $q = $this->db->query("select memberID from coop_member where (status = 'Inactive' OR status is null)");
        if ($q->num_rows()>0) {
            foreach ($q->result() as $r) {
                $this->mmember->updateStatus($r->memberID, 1);
                echo 'memberID: '.$r->memberID.'<br>';
            }
        } else {
        }
    }

    public function unpaid_member_fee_get()
    {
        $no_member = $this->get('id');
        $this->load->model('coop/mtransaction');

        $data = $this->mtransaction->getMemberUnpaidFee($no_member);
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any Cashsource!'), 404);
        }
    }

    public function coop_nursery_list_get()
    {
        $data = $this->mcooperatives->readDataNurseyNumbers($this->get('ObjType'), $this->get('ObjID'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any Transactions!'), 404);
        }
    }

    public function nursery_respon_by_type_get()
    {
        $responsibleType = $this->get('responsibleType');
        $CoopID = $this->get('CoopID');
        $data = $this->mcooperatives->getNurseryResponByType($responsibleType, $CoopID);
        $this->response($data, 200);
    }

    public function nursery_form_photo_post()
    {
        if ($this->file['Photo_idcoop']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['Photo_idcoop']['name'];
            $upload = move_upload($this->file, 'images/nursery/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/nursery/' . $this->post('Photo_old_idcoop'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            }
        }
    }

    public function nursery_form_photo_responsible_post()
    {
        if ($this->file['PhotoResponsible_idcoop']['name'] != '') {
            $gambar = date('Ymdhis') . '_' . $this->file['PhotoResponsible_idcoop']['name'];
            $upload = move_upload($this->file, 'images/photo_responsible/' . $gambar);
            if (isset($upload['upload_data'])) {
                unlink('images/photo_responsible/' . $this->post('Photo_old_responsible_idcoop'));
                $result['success'] = true;
                $result['file']    = $gambar;
                $this->response($result, 200);
            }
        }
    }

    public function coop_nursery_post()
    {
        //var proses (begin)
        foreach ($this->post() as $key => $value) {
            if ($value == "") {
                $varPro[$key] = null;
            } else {
                $varPro[$key] = $value;
            }
        }
        if ($this->post('ResponsibleGender_idcoop') == "") {
            $varPro['ResponsibleGender_idcoop'] = null;
        }
        $varPro['userid'] = $_SESSION['userid'];
        //var proses (end)

        //validasi responsiblenya (begin)
        $valResponsible = true;
        switch ($this->post('ResponsibleType_idcoop')) {
            case 'farmer':
            case 'staff':
                if ($this->post('Responsible_idcoop') == "") {
                    $valResponsible = false;
                }
            break;
            case 'other':
                if ($this->post('ResponsibleName_idcoop') == "" || $this->post('ResponsibleGender_idcoop') == "") {
                    $valResponsible = false;
                }
            break;
        }
        if ($valResponsible == false) {
            $this->response('Responsible information is empty', 400);
        }
        //validasi responsiblenya (end)

        //saring variabel nursery checklist (begin)
        $varPostNurseryChecklist = array();

        $varPostNurseryChecklist['LocationCloseToCommunityNo'] = ($this->post('LocationCloseToCommunityNo') == '') ? null : $this->post('LocationCloseToCommunityNo');
        $varPostNurseryChecklist['LocationCloseToCommunity'] = ($this->post('LocationCloseToCommunity') == '') ? null : $this->post('LocationCloseToCommunity');
        $varPostNurseryChecklist['GoodLandAreaNo'] = ($this->post('GoodLandAreaNo') == '') ? null : $this->post('GoodLandAreaNo');
        $varPostNurseryChecklist['GoodLandArea'] = ($this->post('GoodLandArea') == '') ? null : $this->post('GoodLandArea');
        $varPostNurseryChecklist['LocationNearCocoaFarmNo'] = ($this->post('LocationNearCocoaFarmNo') == '') ? null : $this->post('LocationNearCocoaFarmNo');
        $varPostNurseryChecklist['LocationNearCocoaFarm'] = ($this->post('LocationNearCocoaFarm') == '') ? null : $this->post('LocationNearCocoaFarm');
        $varPostNurseryChecklist['ContinuousWaterSupplyNo'] = ($this->post('ContinuousWaterSupplyNo') == '') ? null : $this->post('ContinuousWaterSupplyNo');
        $varPostNurseryChecklist['ContinuousWaterSupply'] = ($this->post('ContinuousWaterSupply') == '') ? null : $this->post('ContinuousWaterSupply');
        $varPostNurseryChecklist['IrrigationInstalledNo'] = ($this->post('IrrigationInstalledNo') == '') ? null : $this->post('IrrigationInstalledNo');
        $varPostNurseryChecklist['IrrigationInstalled'] = ($this->post('IrrigationInstalled') == '') ? null : $this->post('IrrigationInstalled');
        $varPostNurseryChecklist['UseShadingNetNo'] = ($this->post('UseShadingNetNo') == '') ? null : $this->post('UseShadingNetNo');
        $varPostNurseryChecklist['UseShadingNet'] = ($this->post('UseShadingNet') == '') ? null : $this->post('UseShadingNet');
        $varPostNurseryChecklist['AdequateSupplyTopSoilNo'] = ($this->post('AdequateSupplyTopSoilNo') == '') ? null : $this->post('AdequateSupplyTopSoilNo');
        $varPostNurseryChecklist['AdequateSupplyTopSoil'] = ($this->post('AdequateSupplyTopSoil') == '') ? null : $this->post('AdequateSupplyTopSoil');
        $varPostNurseryChecklist['ImprovedVarietyNo'] = ($this->post('ImprovedVarietyNo') == '') ? null : $this->post('ImprovedVarietyNo');
        $varPostNurseryChecklist['ImprovedVariety'] = ($this->post('ImprovedVariety') == '') ? null : $this->post('ImprovedVariety');
        $varPostNurseryChecklist['ConstructStoringNo'] = ($this->post('ConstructStoringNo') == '') ? null : $this->post('ConstructStoringNo');
        $varPostNurseryChecklist['ConstructStoring'] = ($this->post('ConstructStoring') == '') ? null : $this->post('ConstructStoring');
        $varPostNurseryChecklist['CorrectEquipmentNo'] = ($this->post('CorrectEquipmentNo') == '') ? null : $this->post('CorrectEquipmentNo');
        $varPostNurseryChecklist['CorrectEquipment'] = ($this->post('CorrectEquipment') == '') ? null : $this->post('CorrectEquipment');
        $varPostNurseryChecklist['WindBreakInstalledNo'] = ($this->post('WindBreakInstalledNo') == '') ? null : $this->post('WindBreakInstalledNo');
        $varPostNurseryChecklist['WindBreakInstalled'] = ($this->post('WindBreakInstalled') == '') ? null : $this->post('WindBreakInstalled');
        $varPostNurseryChecklist['SecurityFenceInstalledNo'] = ($this->post('SecurityFenceInstalledNo') == '') ? null : $this->post('SecurityFenceInstalledNo');
        $varPostNurseryChecklist['SecurityFenceInstalled'] = ($this->post('SecurityFenceInstalled') == '') ? null : $this->post('SecurityFenceInstalled');
        $varPostNurseryChecklist['FertilizerUsedNo'] = ($this->post('FertilizerUsedNo') == '') ? null : $this->post('FertilizerUsedNo');
        $varPostNurseryChecklist['FertilizerUsed'] = ($this->post('FertilizerUsed') == '') ? null : $this->post('FertilizerUsed');
        $varPostNurseryChecklist['OperatorAdequateTrainingNo'] = ($this->post('OperatorAdequateTrainingNo') == '') ? null : $this->post('OperatorAdequateTrainingNo');
        $varPostNurseryChecklist['OperatorAdequateTraining'] = ($this->post('OperatorAdequateTraining') == '') ? null : $this->post('OperatorAdequateTraining');
        $varPostNurseryChecklist['AdequateFacilityNo'] = ($this->post('AdequateFacilityNo') == '') ? null : $this->post('AdequateFacilityNo');
        $varPostNurseryChecklist['AdequateFacility'] = ($this->post('AdequateFacility') == '') ? null : $this->post('AdequateFacility');
        $varPostNurseryChecklist['SustainablePestDiseaseNo'] = ($this->post('SustainablePestDiseaseNo') == '') ? null : $this->post('SustainablePestDiseaseNo');
        $varPostNurseryChecklist['SustainablePestDisease'] = ($this->post('SustainablePestDisease') == '') ? null : $this->post('SustainablePestDisease');
        $varPostNurseryChecklist['CloneGradingNo'] = ($this->post('CloneGradingNo') == '') ? null : $this->post('CloneGradingNo');
        $varPostNurseryChecklist['CloneGrading'] = ($this->post('CloneGrading') == '') ? null : $this->post('CloneGrading');
        $varPostNurseryChecklist['SeedlingCullingDoneNo'] = ($this->post('SeedlingCullingDoneNo') == '') ? null : $this->post('SeedlingCullingDoneNo');
        $varPostNurseryChecklist['SeedlingCullingDone'] = ($this->post('SeedlingCullingDone') == '') ? null : $this->post('SeedlingCullingDone');
        $varPostNurseryChecklist['ProperInputSalesRecordNo'] = ($this->post('ProperInputSalesRecordNo') == '') ? null : $this->post('ProperInputSalesRecordNo');
        $varPostNurseryChecklist['ProperInputSalesRecord'] = ($this->post('ProperInputSalesRecord') == '') ? null : $this->post('ProperInputSalesRecord');
        $varPostNurseryChecklist['SeedsPreGerminatedNo'] = ($this->post('SeedsPreGerminatedNo') == '') ? null : $this->post('SeedsPreGerminatedNo');
        $varPostNurseryChecklist['SeedsPreGerminated'] = ($this->post('SeedsPreGerminated') == '') ? null : $this->post('SeedsPreGerminated');
        //saring variabel nursery checklist (end)

        if ($this->post('NurseryID')=='') {
            //cek nursery nr apakah sudah ada
            $cek = $this->mcooperatives->checkNurseryNr($varPro);
            if ($cek == false) {
                $this->response('NurseryNr already existed!', 400);
            }

            $data = $this->mcooperatives->createDataNursery($varPro, $varPostNurseryChecklist);
        } else {
            $data = $this->mcooperatives->updateDataNursery($varPro, $varPostNurseryChecklist);
        }
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'data could not be found'), 404);
        }
    }

    public function coop_nursery_transaction_post()
    {
        $data = $this->mcooperatives->createDataNurseryTransaction($_SESSION['userid']);

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'data could not be found'), 404);
        }
    }

    public function coop_nursery_transaction_delete()
    {
        if (!$this->delete('id')) {
            $this->response(null, 400);
        }
        $data = $this->mcooperatives->deleteTransaction($this->delete('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'data could not be delete'), 404);
        }
    }

    public function coop_nursery_transaction_put()
    {
        $data = $this->mcooperatives->updateDataNurseryTransaction($this->put('id_nursey'), $this->put('Buyer'), $this->put('Volume'), $this->put('Price'), $this->put('DateTransaction'), $_SESSION['userid'], $this->put('id'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'data could not be found'), 404);
        }
    }

    public function coop_nursery_trans_get()
    {
        $data = $this->mcooperatives->readDataNurseryTrans($this->get('id'), $this->get('prov'), $this->get('kab'), $this->get('start'), $this->get('limit'));

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function coop_nursery_polygon_area_get()
    {
        $data = $this->mcooperatives->readNurseryArea($this->get('ObjType'), $this->get('ObjID'), $this->get('NurseryID'), $this->get('NurseryNr'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
        }
    }

    public function coop_dataFormNursery_get()
    {
        $data = $this->mcooperatives->readDataFormNursery($this->get('id'), $this->get('nursery_id'));
        $this->response($data, 200);
//        if ($data)
//            $this->response($data, 200);
//        else
//            $this->response(array('error' => 'Couldn\'t find any datas!'), 404);
    }

    public function deposito_get()
    {
        $this->load->model('coop/msaving');
        $data = $this->msaving->getDataDeposito(getCoopID(), $this->get('Awal'), $this->get('Akhir'));
        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array('error' => 'Error occured. Please try again later'), 200);
        }
    }

    public function count_days($memberSavingRegisteredDate, $lengthDeposito)
    {
        $endDate = date('Y-m-d', strtotime("+ $lengthDeposito MONTHS", strtotime($memberSavingRegisteredDate)));
        // echo $memberSavingRegisteredDate.'-'.$endDate.'<br>';

        $startTimeStamp = strtotime($memberSavingRegisteredDate);
        $endTimeStamp = strtotime($endDate);

        $timeDiff = abs($endTimeStamp - $startTimeStamp);

        $numberDays = $timeDiff/86400;  // 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        // echo $numberDays;
        return $numberDays;
    }

    public function deposit_interest_get($MemberID=null)
    {
        $pajak = 20/100; //pajak 20% - Pajak PPh Pasal 4 Ayat 2.

        $CoopID = getCoopID();

        $sql = "select a.memberSavingID,a.memberID,b.savingTypeName,minAmountDepositLimit,lengthDeposito,memberSavingRegisteredDate,a.MemberSavingID,
                                metodeDeposito,pajakDeposito,bungaDeposito,depositoAutoJurnal,sum(MemberTransactionAmount) as totalDeposito
                                from coop_member_saving a
                                join coop_saving_type b ON a.savingTypeID = b.savingTypeID
                                join coop_member_transaction c ON a.memberSavingID = c.MemberSavingID
                                where TRUE  ";


        if ($MemberID!=null) {
            $sql.=" AND a.memberID = $MemberID AND a.CoopID = ".$CoopID."";
        } else {
            $sql.=" AND a.CoopID = ".$CoopID."";
        }

        $sql.=" AND b.minAmountDepositLimit is not null";

        $q = $this->db->query($sql);

        $jum = 0;
        foreach ($q->result() as $r) {
            if ($r->pajakDeposito==1) {
                //kena pajak 20% - Pajak PPh Pasal 4 Ayat 2.

                if ($r->metodeDeposito==1) {
                    //Metode masa simpan bulanan:
                    //Jumlah simpanan deposito x Bunga x Jangka waktu bulanan / 12 x 80%
                    $bunga = $r->totalDeposito*($r->bungaDeposito/100)*($r->lengthDeposito/12)*(80/100);
                } else {
                    //Metode masa simpan harian:
                    // Jumlah simpanan deposito x Bunga x Jangka waktu harian / 365 x 80%
                    $bunga = $r->totalDeposito*($r->bungaDeposito/100)*($this->count_days($r->memberSavingRegisteredDate, $r->lengthDeposito)*365)*(80/100);
                }
            } else {
                //tidak kena pajak
                if ($r->metodeDeposito==1) {
                    //Metode masa simpan bulanan:
                    //Jumlah simpanan deposito x Bunga x Jangka waktu bulanan / 12
                     $bunga = $r->totalDeposito*($r->bungaDeposito/100)*($r->lengthDeposito/12);
                } else {
                    //Metode masa simpan harian:
                    //Tanpa pajak: Jumlah simpanan deposito x Bunga x Jangka waktu harian / 365
                    $bunga = $r->totalDeposito*($r->bungaDeposito/100)*($this->count_days($r->memberSavingRegisteredDate, $r->lengthDeposito)*365);
                }
            }

            $d = array(
                'CoopID' => $CoopID,
                'MemberSavingID' => $r->MemberSavingID,
                'TotalSaving' => $r->totalDeposito,
                'InterestPercent' => $r->bungaDeposito,
                'Amount' => $bunga,
                // 'CreatedBy' int(11) DEFAULT NULL,
                'CreatedDate' => date('Y-m-d'),
            );

            //cek udah ada apa belum
            $qcek = $this->db->query("select InterestDepositCoopID
                                        from coop_deposit_interest
                                        where CoopID = $CoopID and MemberSavingID = ".$r->MemberSavingID." and (CreatedDate between '".date('Y-m-01')."' and '".date('Y-m-t')."')");
            if ($qcek->num_rows()>0) {
                $this->db->where(array(
                        'CoopID' => $CoopID,
                        'MemberSavingID' => $r->MemberSavingID
                    ));
                $this->db->update('coop_deposit_interest', $d);
            } else {
                $this->db->insert('coop_deposit_interest', $d);
            }


            if ($this->db->affected_rows()>0) {
                $jum++;
            }



            $this->response(array('success'=>true, 'message'=>'Berhasil menghitung bunga deposito sebanyak '.$jum.' Nasabah'), 200);
        }
    }

    public function report_get($opt)
    {
        if ($opt=='neraca') {
            echo 'neraca';
        }
    }

    public function create_journal_get()
    {
        $this->load->library('Jurnal');

        $transactionType = $this->get('transactionType');

        $q = $this->db->get_where('coop_transactions', array('transactionType'=>$transactionType));
        // echo $q->num_rows();
        // var_dump($q);
        // echo $this->db->last_query();
        // $i=0;
        // print_r($q->result());

        foreach ($q->result() as $r) {
            // echo $r->transactionID.'<br>';
            // echo $r->transactionAmount.'<br>';

            $qcoaFrom = $this->db->get_where('accounting_coa', array('CoaCode'=>$r->cashSourceID));
            $qcoaTo = $this->db->get_where('accounting_coa', array('CoaCode'=>$r->CoaCode));

            if ($qcoaFrom->num_rows()>0 && $qcoaTo->num_rows()>0) {
                $qcoaFrom = $qcoaFrom->row();
                $qcoaTo = $qcoaTo->row();
                echo $qcoaFrom->CoaID.','.$qcoaTo->CoaID.'<br>';

                if ($transactionType==2) {
                    print_r($this->jurnal->recordbooking_out($r->transactionAmount, $qcoaFrom->CoaID, $qcoaTo->CoaID, 1, $_SESSION['userid'], $r->transactionRemark));
                } else {
                    print_r($this->jurnal->recordbooking_in($r->transactionAmount, $qcoaFrom->CoaID, $qcoaTo->CoaID, 1, $_SESSION['userid'], $r->transactionRemark));
                }
            } else {
                echo 'coa id tidak ada . transid:'.$r->transactionID.'<br>';
                // exit;
            }

            //
            // echo $i.'<br>';
            // $i++;
            // print_r($this->jurnal->recordbooking_in($r->transactionAmount,$qcoaFrom->CoaID,$qcoaTo->CoaID,1,$_SESSION['userid']));
        }
       // $this->response(array('success'=>true,'message'=>'Berhasil menghitung bunga deposito sebanyak Nasabah'), 200);
    }

    public function exportimport_data_get()
    {
        $this->load->model('cooperatives/msync');

        $data = $this->msync->exportAllData();

        $this->response(array('success'=>true, 'data'=>$data), 200);
    }

    public function trainings_get()
    {
        return $this->response($this->mcooperatives->getTraining($this->get('CoopID')), 200);
    }

    public function training_post()
    {
        $return = array('success' => false, 'message' => '');
        $result = $this->mcooperatives->addTraining($this->post(null));
        if ($result !== false) {
            $return['success']  = true;
            $return['id']       = $this->db->insert_id();
        } else {
            $return['message'] = lang('Failed to create record');
        }

        return $this->response($return, 200);
    }

    public function training_put()
    {
        $return = array('success' => false, 'message' => '');
        $result = $this->mcooperatives->updateTraining($this->put(null));
        if ($result !== false) {
            $return['success']  = true;
        } else {
            $return['message'] = lang('Failed to create record');
        }
        return $this->response($return, 200);
    }

    public function provinces_get()
    {
        $this->response($this->mcooperatives->getProvinces(), 200);
    }

    public function districts_get()
    {
        $this->response($this->mcooperatives->getDistricts($this->get('ProvinceID')), 200);
    }

    public function training_type_get()
    {
        $this->response($this->mcooperatives->getTrainingType(), 200);
    }

    public function service_provider_get()
    {
        $this->response($this->mcooperatives->getServiceProvider(), 200);
    }

    public function participants_get()
    {
        $this->response($this->mcooperatives->getParticipants($this->get('CoopTrainingsID')), 200);
    }

    public function participant_post()
    {
        $return = array('success' => false, 'message' => '');
        $result = $this->mcooperatives->addParticipants($this->post(null));
        if ($result !== false) {
            $return['success']  = true;
        } else {
            $return['message'] = lang('Failed to add participant');
        }

        return $this->response($return, 200);
    }

    public function participant_put()
    {
        $return = array('success' => false, 'message' => '');
        $result = $this->mcooperatives->updateParticipants($this->put(null));
        if ($result !== false) {
            $return['success']  = true;
        } else {
            $return['message'] = lang('Failed to update record');
        }

        return $this->response($return, 200);
    }

    public function coop_member_get()
    {
        $this->response($this->mcooperatives->getCoopMember($this->get('CoopID'), $this->get('CoopTrainingsID')), 200);
    }

    public function participant_checklists_get() {
        $CoopTrainingsID    = $this->get('CoopTrainingsID');
        $MemberID           = $this->get('MemberID');

        $result             = $this->mcooperatives->getParticipantAttendance($CoopTrainingsID, $MemberID);

        $this->response($result, 200);
    }

    public function participant_detail_get() {
        $ParticipantsID = $this->get('ParticipantsID');
        $result = $this->mcooperatives->getParticipantDetail($ParticipantsID);
        $this->response($result, 200);
    }

    public function attendance_post() {
        $CoopTrainingsID    = $this->post('CoopTrainingsID');
        $MemberID           = $this->post('MemberID');
        $data               = $this->post('data');

        foreach ($data as $key => $value) {
            if ($value['Attendance1'] == 'true' || $value['Attendance1'] == 1) {
                $value['Attendance1'] = 1;
            } else {
                $value['Attendance1'] = 0;
            }

            if ($value['Attendance2'] == 'true' || $value['Attendance2'] == 1) {
                $value['Attendance2'] = 1;
            } else {
                $value['Attendance2'] = 0;
            }
            $result = $this->mcooperatives->updateParticipantAttendance($CoopTrainingsID, $MemberID, $value['DayNumber'], $value['Attendance1'], $value['Attendance2'], $value['TrainingDate'] ? date('Y-m-d', strtotime($value['TrainingDate'])) : null);
        }
        $this->response($result);
    }

    function cetak_get() {
        $CoopTrainingsID    = $this->get('CoopTrainingsID');
        $data['data']       = $this->mcooperatives->getTrainingDetail($CoopTrainingsID);
        $data['DayNumber']  = $this->get('DayNumber');
        if (empty($this->get('result'))) {
            $data['peserta'] = $this->mcooperatives->getTrainingParticipant($CoopTrainingsID);
        } else {
            $data['peserta'] = $this->mcooperatives->getTrainingAttendance($CoopTrainingsID, $this->get('DayNumber'));
        }
        // $data['logo'] = $this->mcooperatives->readPartnerLogo($CoopTrainingsID);
        $this->load->view('training_cooperative_cetak_hadir', $data);
    }
}
