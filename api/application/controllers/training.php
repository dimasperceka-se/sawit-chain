n<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cpg extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('cpg/mcpg');
    }

    function RegionIDs_get() {
        $RegionIDs = $this->mcpg->readRegionIDs($this->get('query'),$this->get('start'),$this->get('limit'));
        if($RegionIDs) $this->response($RegionIDs, 200);
        else $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function cetak_get($id) {
         $data['data'] = $this->mcpg->readTraining($id);
         $part = $this->mcpg->readParticipants($id);
         $data['peserta'] = $part['data'];
         $this->load->view('cpg_cetak_hadir', $data);
    }

    function batchs_get() {
        $data = $this->mcpg->readBatchs();
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any RegionIDs!'), 404);
    }

    function cpgcs_get() {
        $cpgs = $this->mcpg->readCpgs($this->get('prov'),$this->get('kab'),$this->get('key'),$this->get('start'),$this->get('limit'));
        if($cpgs) $this->response($cpgs, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function trainings_get() {
        $cpgs = $this->mcpg->readTrainings($this->get('key'));
        if($cpgs) $this->response($cpgs, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function family_trainings_get() {
        $data = $this->mcpg->readFamilyTrainings($this->get('id'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }
    function familys_get() {
        $cpgs = $this->mcpg->readFamily($this->get('key'));
        if($cpgs) $this->response($cpgs, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function participants_get() {
        $cpgs = $this->mcpg->readParticipants($this->get('key'),$this->get('start'),$this->get('limit'));
        if($cpgs) $this->response($cpgs, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function participant_post() {
        if(!$this->post('CpgBatchTrainingID')) $this->response(NULL, 400);
         $cpg = $this->mcpg->createParticipant($this->post('CpgBatchTrainingID'),$this->post('pFarmerID'),
            $this->post('PetaniKakao'),$this->post('FamilyID'),$this->post('WritingAwal'),$this->post('WritingAkhir'),
            $this->post('BallotAwal'),$this->post('BallotAkhir'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be found'), 404);
   }

    function participant_put() {
        if(!$this->put('id')) $this->response(NULL, 400);
        $farmerid = is_numeric($this->put('pFarmerID'))?$this->put('pFarmerID'):$this->put('pFarmerID')
         $cpg = $this->mcpg->updateParticipant($this->put('CpgBatchTrainingID'),$farmerid,
            $this->put('PetaniKakao'),$this->put('FamilyID'),$this->put('WritingAwal'),$this->put('WritingAkhir'),
            $this->put('BallotAwal'),$this->put('BallotAkhir'),$this->put('id'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be found'), 404);
   }

    function farmers_get() {
        $cpgs = $this->mcpg->readFarmers($this->get('cpg'));
        if($cpgs) $this->response($cpgs, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function fasilitators_get() {
        $data = $this->mcpg->readFasilitators();
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function penyuluhs_get() {
        $data = $this->mcpg->readPenyuluhs($this->get('prov'));
        if($data) $this->response($data, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }

    function trainingNames_get() {
        $cpgs = $this->mcpg->readTrainingNames();
        if($cpgs) $this->response($cpgs, 200);
        else $this->response(array('error' => 'Couldn\'t find any cpgs!'), 404);
    }
    function cpgc_get() {
        if(!$this->get('id')) $this->response(NULL, 400);
        $cpg = $this->mcpg->readCpg($this->get('id'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function farmergroups_get()
    {
        $cpgs = $this->Mcpg->readCpgs();
        if($cpgs)
        {
            $this->response($cpgs, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any CPG\'s!'), 404);
        }
    }

    function cpgc_post() {
        if(!$this->post('GroupName')) $this->response(NULL, 400);
        $cpg = $this->mcpg->createCpg($this->post('GroupName'),$this->post('Address'),$this->post('TahunTerbentuk'),
            $this->post('Desa'),$this->post('batch'),$this->post('latitude'),$this->post('longitude'),$this->post('elevation'),
            $this->post('Status'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be found'), 404);
   }

    function training_post() {
        if(!$this->post('CpgTrainingsID')) $this->response(NULL, 400);
         $cpg = $this->mcpg->createTraining($this->post('idd'),$this->post('CpgTrainingsID'),$this->post('ProgramStaffID'),
            $this->post('ExtensionStaffID'),$this->post('KeyFarmerID'),$this->post('DemoplotOwnerID'),
            $this->post('TrainingStart'),$this->post('TrainingEnd'),$this->post('PetaniKakao'),$this->post('FamilyID'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be found'), 404);
   }

    function training_put() {
        if(!$this->put('idt')) $this->response(NULL, 400);
         $cpg = $this->mcpg->updateTraining($this->put('CpgTrainingsID'),$this->put('ProgramStaffID'),
            $this->put('ExtensionStaffID'),$this->put('KeyFarmerID'),$this->put('DemoplotOwnerID'),
            $this->put('TrainingStart'),$this->put('TrainingEnd'),$this->put('PetaniKakao'),$this->put('FamilyID'),
            $this->put('idt'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be found'), 404);
   }

    function farmergroup_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }

        $cpg = $this->Mcpg->readCpg($this->get('id'));

        if($cpg)
        {
            $this->response($cpg, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function cpgc_put() {
        if(!$this->put('id')) $this->response(NULL, 400);
        $cpg = $this->mcpg->updateCpg($this->put('GroupName'),$this->put('Address'),$this->put('TahunTerbentuk'),
            $this->put('Desa'),$this->put('batch'),$this->put('latitude'),$this->put('longitude'),$this->put('elevation'),$this->put('status'),
            $this->put('id'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function farmergroup_post()
    {
        $groupName= $this->post('groupName');
        $tahunTerbentuk = $this->post('tahunTerbentuk');
        $regionID = $this->post('regionID');

        if(!$groupName)
        {
            $this->response(NULL, 400);
        }

        $cpg = $this->Mcpg->createCpg($groupName, $tahunTerbentuk, $regionID);

        if($cpg)
        {
            $this->response($cpg, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be found'), 404);
        }
    }

    function cpgc_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteCpg($this->delete('id'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be delete'), 404);
    }

    function training_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteTraining($this->delete('id'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be delete'), 404);
    }

    function participant_delete() {
        if(!$this->delete('id')) $this->response(NULL, 400);
        $cpg = $this->mcpg->deleteParticipant($this->delete('id'));
        if($cpg) $this->response($cpg, 200);
        else $this->response(array('error' => 'Cpg could not be delete'), 404);
    }

    function farmergroup_delete()
    {
        $cpgID = $this->delete('cpgID');
        // $this->response($id, 200);
        if(!$cpgID)
        {
            $this->response(NULL, 400);
        }

        $cpg = $this->Mcpg->deleteCpg($cpgID);

        if($cpg)
        {
            $this->response($cpg, 200); // 200 being the HTTP response code
        }
        else
        {
            $this->response(array('error' => 'Unit could not be delete'), 404);
        }
    }

}
