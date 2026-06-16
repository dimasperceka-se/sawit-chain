<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sync extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('sync/msync');
        $this->load->model('cpg/mcpg');
        $this->load->model('sync/mdashboard');
    }

    function login_get() {
        $username = $this->get('username');
        $password = $this->get('password');
        $login = $this->msync->readLogin($username, $password);
        if ($login) {
            $this->response($login, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Username\'s!'), 404);
        }
    }

    function farmers_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $farmers = $this->msync->readFarmers($provID, $districtID);

        if ($farmers) {
            $this->response($farmers, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any farmers!'), 404);
        }
    }

    function farmer_get() {
        $farmers = $this->msync->readFarmer($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($farmers) {
            $this->response($farmers, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any farmers!'), 404);
        }
    }

    function farmergroups_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $cpgs = $this->msync->readCpgs($provID, $districtID);
        if ($cpgs) {
            $this->response($cpgs, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any CPG\'s!'), 404);
        }
    }

    function farmergroup_get() {

        $cpgs = $this->msync->readCpg($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));
        if ($cpgs) {
            $this->response($cpgs, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any CPG\'s!'), 404);
        }
    }

    function farmergroup_post() {
        if (!$this->post('GroupName'))
            $this->response(NULL, 400);
        $cpg = $this->msync->createCpg($this->post('GroupName'), $this->post('Address'), $this->post('TahunTerbentuk'), $this->post('RegionID'), $this->post('Latitude'), $this->post('Longitude'), $this->post('Elevation'), 'active', $this->post('DateCreated'), $this->post('CreatedBy'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function farmergroup_put() {
        if (!$this->put('id'))
            $this->response(NULL, 400);
        $cpg = $this->msync->updateCpg($this->put('GroupName'), $this->put('Address'), $this->put('TahunTerbentuk'), $this->put('RegionID'), $this->put('Latitude'), $this->put('Longitude'), $this->put('Elevation'), 'active', $this->put('id'), $this->put('LastModifiedBy'), $this->put('DateUpdated'));
        if ($cpg)
            $this->response($cpg, 200);
        else
            $this->response(array('error' => 'Cpg could not be found'), 404);
    }

    function farmer_post() {
        if (!$this->post('FarmerName'))
            $this->response(NULL, 400);
        $farmer = $this->msync->createFarmer($this->post('FarmerName'), $this->post('Birthdate'), $this->post('Gender'), $this->post('Address'), $this->post('VillageID'), $this->post('MaritalStatus'), $this->post('Education'), $this->post('Handphone'), $this->post('CPGid'), $this->post('LahanKosong'), $this->post('Muge'), $this->post('Photo'), $this->post('LahanKakao'), $this->post('LahanProduksiLain'), $this->post('TotalLahan'), $this->post('KebunKakao'), $this->post('DateUpdated'), $this->post('LastModifiedBy'), $this->post('DateCollection'), $this->post('CreatedBy'), $this->post('DateCreated'));
        if ($farmer)
            $this->response($farmer, 200);
        else
            $this->response(array('error' => 'Farmer could not be found'), 404);
    }

    function farmer_put() {
        if (!$this->put('FarmerID'))
            $this->response(NULL, 400);
        $farmer = $this->msync->updateFarmer($this->put('FarmerID'), $this->put('FarmerName'), $this->put('Birthdate'), $this->put('Gender'), $this->put('Address'), $this->put('VillageID'), $this->put('MaritalStatus'), $this->put('Education'), $this->put('Handphone'), $this->put('CPGid'), $this->put('LahanKosong'), $this->put('Muge'), $this->put('Photo'), $this->put('LahanKakao'), $this->put('LahanProduksiLain'), $this->put('TotalLahan'), $this->put('KebunKakao'), $this->put('DateUpdated'), $this->put('LastModifiedBy'), $this->put('DateCollection'), $this->put('CreatedBy'), $this->put('DateCreated'));
        if ($farmer)
            $this->response($farmer, 200);
        else
            $this->response(array('error' => 'Farmer could not be found'), 404);
    }

    function farmergarden_post() {
        //if(!$this->post('name')) $this->response(NULL, 400);
        $garden = $this->msync->createGarden($this->post('FarmerID'), $this->post('GardenNr'), $this->post('DateCollection'), $this->post('Latitude'), $this->post('Longitude'), $this->post('Elevation'), $this->post('OwnershipCocoa'), $this->post('TahunTanamanCocoa'), $this->post('GardenDistance'), $this->post('GardenHaUnCertified'), ($this->post('Production') == '') ? null : $this->post('Production'), $this->post('PanenBiasaMonths'), $this->post('PanenBiasaPanenMonth'), $this->post('PanenBiasaKg'), $this->post('PanenTrekMonths'), $this->post('PanenTrekPanenMonth'), $this->post('PanenTrekKg'), $this->post('PanenRayaMonths'), $this->post('PanenRayaPanenMonth'), $this->post('PanenRayaKg'), $this->post('TimeHarvestBiasa'), $this->post('TimeHarvestTrek'), $this->post('TimeHarvestRaya'), $this->post('LandCertificate'), $this->post('PohonTBM'), $this->post('PohonTM'), $this->post('PohonRehab'), $this->post('GraftedTrees'), $this->post('ReplantedTrees'), $this->post('RoadCondition'), $this->post('Comment'), $this->post('TSH858'), $this->post('RCC70'), $this->post('RCC71'), $this->post('RCC72'), $this->post('RCC73'), $this->post('Hybrid'), $this->post('S1'), $this->post('S2'), $this->post('S3'), $this->post('ICRRI3'), $this->post('ICRRI4'), $this->post('ICRRI5'), $this->post('CloneLain'), $this->post('Gamal'), $this->post('Kelapa'), $this->post('Durian'), $this->post('Pinang'), $this->post('Karet'), $this->post('JackFruit'), $this->post('Lamtoro'), $this->post('Mahoni'), $this->post('Pisang'), $this->post('Rambutan'), $this->post('Mangga'), $this->post('Langsat'), $this->post('ShadeLain'), $this->post('ShadeTreesNr'), $this->post('TimeHarvest'), $this->post('HarvestAwal'), $this->post('HarvestMasak'), $this->post('HarvestHama'), $this->post('PruningPlants'), $this->post('FrequentPruning'), $this->post('HighPruning'), $this->post('PruningProtectPlants'), $this->post('FrequentPruningProtect'), $this->post('CleanSkin'), $this->post('HowToCleanSkin'), $this->post('OrganicKotoran'), $this->post('OrganicResidu'), $this->post('OrganicMembeli'), $this->post('TidakMemakaiOrganic'), $this->post('Urea'), $this->post('TSP'), $this->post('NPK'), $this->post('KCL'), $this->post('TidakMemakaiKimia'), $this->post('FrequentFertilizationOrganic'), $this->post('DoseFertilizerOrganic'), $this->post('FrequentFertilizationKimia'), $this->post('DoseFertilizerKimia'), $this->post('PakaiKompos'), $this->post('FrequentFertilizationKompos'), $this->post('DoseFertilizerKompos'), $this->post('FrUrea'), $this->post('FrTsp'), $this->post('FrNpk'), $this->post('FrKcl'), $this->post('DoUrea'), $this->post('DoTsp'), $this->post('DoNpk'), $this->post('DoKcl'), $this->post('KimiaDana'), $this->post('KimiaSupplier'), $this->post('KimiaDilatih'), $this->post('HamaBPK'), $this->post('HamaHelopeltis'), $this->post('HamaBatang'), $this->post('PenyakitKanker'), $this->post('PenyakitBusuk'), $this->post('PenyakitUpas'), $this->post('PenyakitAkar'), $this->post('PenyakitVSD'), $this->post('PenyakitAntraknose'), $this->post('Herbisida'), $this->post('MerekHerbisida'), $this->post('FrequentHerbisida'), $this->post('DoseHerbisida'), $this->post('Herbisida1'), $this->post('Herbisida2'), $this->post('Herbisida3'), $this->post('Herbisida4'), $this->post('Herbisida5'), $this->post('Herbisida6'), $this->post('Herbisida7'), $this->post('Herbisida8'), $this->post('Herbisida9'), $this->post('Herbisida10'), $this->post('Insectisida'), $this->post('MerekInsectisida'), $this->post('FrequentInsectisida'), $this->post('DoseInsectisida'), $this->post('Insectisida1'), $this->post('Insectisida2'), $this->post('Insectisida3'), $this->post('Insectisida4'), $this->post('Insectisida5'), $this->post('Insectisida6'), $this->post('Insectisida7'), $this->post('Insectisida8'), $this->post('Insectisida9'), $this->post('Insectisida10'), $this->post('Insectisida11'), $this->post('Fungisida'), $this->post('MerekFungisida'), $this->post('FrequentFungisida'), $this->post('DoseFungisida'), $this->post('Fungisida1'), $this->post('Fungisida2'), $this->post('Fungisida3'), $this->post('Fungisida4'), $this->post('Fungisida5'), $this->post('Fungisida6'), $this->post('Fungisida7'), $this->post('Fungisida8'), $this->post('Fungisida9'), $this->post('Fungisida10'), $this->post('Fungisida11'), $this->post('APD'), $this->post('TempatSimpanPestisida'), $this->post('BuangKemasanPestisida'), $this->post('TopGraftedTrees'), $this->post('GraftedTreesTahun'), $this->post('TopGraftedTreesTahun'), $this->post('ReplantedTreesTahun'), $this->post('M01'), $this->post('M06'), $this->post('THR'), $this->post('RCL'), $this->post('J45'), $this->post('KomposTBM'), $this->post('KomposTM'), $this->post('KomposTR'), $this->post('PupukTBM'), $this->post('PupukTM'), $this->post('PupukTR'), $this->post('SurveyNr'), $this->post('DateCreated'), $this->post('DateUpdated'), $this->post('CreatedBy'), $this->post('LastModifiedBy'), $this->post('TSH858Nr'), $this->post('RCC70Nr'), $this->post('RCC71Nr'), $this->post('RCC72Nr'), $this->post('RCC73Nr'), $this->post('LokalNr'), $this->post('S1Nr'), $this->post('S2Nr'), $this->post('ICRRI3Nr'), $this->post('ICRRI4Nr'), $this->post('ICRRI5Nr'), $this->post('M01Nr'), $this->post('M06Nr'), $this->post('THRNr'), $this->post('RCLNr'), $this->post('J45Nr'), $this->post('CloneLainNr'), $this->post('Sukun'), $this->post('Jengkol'), $this->post('Petai'), $this->post('Jabon'), $this->post('Uru'), $this->post('Biti'), $this->post('Jati'), $this->post('Jeruk'), $this->post('Manggis'), $this->post('Pepaya'), $this->post('Alpukat'), $this->post('Kemiri'), $this->post('Pala'), $this->post('Aren'), $this->post('Sawit'), $this->post('Cengkeh'), $this->post('GamalNr'), $this->post('KelapaNr'), $this->post('DurianNr'), $this->post('PinangNr'), $this->post('KaretNr'), $this->post('JackFruitNr'), $this->post('LamtoroNr'), $this->post('MahoniNr'), $this->post('PisangNr'), $this->post('RambutanNr'), $this->post('CengkehNr'), $this->post('SawitNr'), $this->post('ArenNr'), $this->post('ManggaNr'), $this->post('LangsatNr'), $this->post('PalaNr'), $this->post('KemiriNr'), $this->post('AlpukatNr'), $this->post('SukunNr'), $this->post('PepayaNr'), $this->post('ManggisNr'), $this->post('JerukNr'), $this->post('JatiNr'), $this->post('BitiNr'), $this->post('UruNr'), $this->post('JabonNr'), $this->post('PetaiNr'), $this->post('JengkolNr'), $this->post('ShadeLainNr'), $this->post('FrLain'), $this->post('DoLain'), $this->post('KimiaTidakSuka'), $this->post('KimiaLain'), $this->post('Comment'));
        if ($garden)
            $this->response($garden, 200);
        else
            $this->response(array('error' => 'Garden could not be found'), 404);
    }

    function farmergardens_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $farmergardens = $this->msync->readFarmerGardens($provID, $districtID);
        if ($farmergardens) {
            $this->response($farmergardens, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Garden\'s!'), 404);
        }
    }

    function farmergarden_get() {
        $farmergarden = $this->msync->readFarmerGarden($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($farmergarden) {
            $this->response($farmergarden, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any garden!'), 404);
        }
    }

    function gardenstatus_get() {
        $gardenstatus = $this->msync->readGardenStatus($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($gardenstatus) {
            $this->response($gardenstatus, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any garden!'), 404);
        }
    }

    function otherland_get() {
        $otherland = $this->msync->readOtherLand($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($otherland) {
            $this->response($otherland, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any other land!'), 404);
        }
    }

    function role_get() {
        $role = $this->msync->readRole();
        if ($role) {
            $this->response($role, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any role!'), 404);
        }
    }

    function userrole_get() {
        $userrole = $this->msync->readUserRole();
        if ($userrole) {
            $this->response($userrole, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any user role!'), 404);
        }
    }

    function province_get() {
        $province = $this->msync->readProvinces();
        if ($province) {
            $this->response($province, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any province!'), 404);
        }
    }

    function district_get() {
        $district = $this->msync->readDistricts();
        if ($district) {
            $this->response($district, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any district!'), 404);
        }
    }

    function subdistrict_get() {
        $subdistrict = $this->msync->readSubdistricts();
        if ($subdistrict) {
            $this->response($subdistrict, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any subdistrict!'), 404);
        }
    }

    function village_get() {
        $village = $this->msync->readVillages($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($village) {
            $this->response($village, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any village!'), 404);
        }
    }

    function villagecrop_get() {
        $villagecrop = $this->msync->readVillageCrops($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($villagecrop) {
            $this->response($villagecrop, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any village crops!'), 404);
        }
    }

    function villageinfrastructure_get() {
        $villageinfrastructure = $this->msync->readVillageInfrastructures($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($villageinfrastructure) {
            $this->response($villageinfrastructure, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any village infrastructures!'), 404);
        }
    }

    function trainings_attendance_get() {
        $trainings_attendance = $this->msync->readTrainingAttendance($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($trainings_attendance) {
            $this->response($trainings_attendance, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any trainings attendance!'), 404);
        }
    }

    function financial_get() {
        $financial = $this->msync->readFarmerFinancial($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($financial) {
            $this->response($financial, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any financial data!'), 404);
        }
    }

    function certification_get() {
        $certification = $this->msync->readCertification($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($certification) {
            $this->response($certification, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any certification data!'), 404);
        }
    }

    function certificationauditlog_get() {
        $certification = $this->msync->readCertificationAuditLog($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($certification) {
            $this->response($certification, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any certification data!'), 404);
        }
    }

    function farmergarden_put() {
        //if(!$this->post('name')) $this->response(NULL, 400);
        $garden = $this->msync->updateGarden($this->put('FarmerID'), $this->put('GardenNr'), $this->put('DateCollection'), $this->put('Latitude'), $this->put('Longitude'), $this->put('Elevation'), $this->put('OwnershipCocoa'), $this->put('TahunTanamanCocoa'), $this->put('GardenDistance'), $this->put('GardenHaUnCertified'), $this->put('Production'), $this->put('PanenBiasaMonths'), $this->put('PanenBiasaPanenMonth'), $this->put('PanenBiasaKg'), $this->put('PanenTrekMonths'), $this->put('PanenTrekPanenMonth'), $this->put('PanenTrekKg'), $this->put('PanenRayaMonths'), $this->put('PanenRayaPanenMonth'), $this->put('PanenRayaKg'), $this->put('TimeHarvestBiasa'), $this->put('TimeHarvestTrek'), $this->put('TimeHarvestRaya'), $this->put('LandCertificate'), $this->put('PohonTBM'), $this->put('PohonTM'), $this->put('PohonRehab'), $this->put('GraftedTrees'), $this->put('ReplantedTrees'), $this->put('RoadCondition'), $this->put('Comment'), $this->put('TSH858'), $this->put('RCC70'), $this->put('RCC71'), $this->put('RCC72'), $this->put('RCC73'), $this->put('Hybrid'), $this->put('S1'), $this->put('S2'), $this->put('S3'), $this->put('ICRRI3'), $this->put('ICRRI4'), $this->put('ICRRI5'), $this->put('CloneLain'), $this->put('Gamal'), $this->put('Kelapa'), $this->put('Durian'), $this->put('Pinang'), $this->put('Karet'), $this->put('JackFruit'), $this->put('Lamtoro'), $this->put('Mahoni'), $this->put('Pisang'), $this->put('Rambutan'), $this->put('Mangga'), $this->put('Langsat'), $this->put('ShadeLain'), $this->put('ShadeTreesNr'), $this->put('TimeHarvest'), $this->put('HarvestAwal'), $this->put('HarvestMasak'), $this->put('HarvestHama'), $this->put('PruningPlants'), $this->put('FrequentPruning'), $this->put('HighPruning'), $this->put('PruningProtectPlants'), $this->put('FrequentPruningProtect'), $this->put('CleanSkin'), $this->put('HowToCleanSkin'), $this->put('OrganicKotoran'), $this->put('OrganicResidu'), $this->put('OrganicMembeli'), $this->put('TidakMemakaiOrganic'), $this->put('Urea'), $this->put('TSP'), $this->put('NPK'), $this->put('KCL'), $this->put('TidakMemakaiKimia'), $this->put('FrequentFertilizationOrganic'), $this->put('DoseFertilizerOrganic'), $this->put('FrequentFertilizationKimia'), $this->put('DoseFertilizerKimia'), $this->put('PakaiKompos'), $this->put('FrequentFertilizationKompos'), $this->put('DoseFertilizerKompos'), $this->put('FrUrea'), $this->put('FrTsp'), $this->put('FrNpk'), $this->put('FrKcl'), $this->put('DoUrea'), $this->put('DoTsp'), $this->put('DoNpk'), $this->put('DoKcl'), $this->put('KimiaDana'), $this->put('KimiaSupplier'), $this->put('KimiaDilatih'), $this->put('HamaBPK'), $this->put('HamaHelopeltis'), $this->put('HamaBatang'), $this->put('PenyakitKanker'), $this->put('PenyakitBusuk'), $this->put('PenyakitUpas'), $this->put('PenyakitAkar'), $this->put('PenyakitVSD'), $this->put('PenyakitAntraknose'), $this->put('Herbisida'), $this->put('MerekHerbisida'), $this->put('FrequentHerbisida'), $this->put('DoseHerbisida'), $this->put('Herbisida1'), $this->put('Herbisida2'), $this->put('Herbisida3'), $this->put('Herbisida4'), $this->put('Herbisida5'), $this->put('Herbisida6'), $this->put('Herbisida7'), $this->put('Herbisida8'), $this->put('Herbisida9'), $this->put('Herbisida10'), $this->put('Insectisida'), $this->put('MerekInsectisida'), $this->put('FrequentInsectisida'), $this->put('DoseInsectisida'), $this->put('Insectisida1'), $this->put('Insectisida2'), $this->put('Insectisida3'), $this->put('Insectisida4'), $this->put('Insectisida5'), $this->put('Insectisida6'), $this->put('Insectisida7'), $this->put('Insectisida8'), $this->put('Insectisida9'), $this->put('Insectisida10'), $this->put('Insectisida11'), $this->put('Fungisida'), $this->put('MerekFungisida'), $this->put('FrequentFungisida'), $this->put('DoseFungisida'), $this->put('Fungisida1'), $this->put('Fungisida2'), $this->put('Fungisida3'), $this->put('Fungisida4'), $this->put('Fungisida5'), $this->put('Fungisida6'), $this->put('Fungisida7'), $this->put('Fungisida8'), $this->put('Fungisida9'), $this->put('Fungisida10'), $this->put('Fungisida11'), $this->put('APD'), $this->put('TempatSimpanPestisida'), $this->put('BuangKemasanPestisida'), $this->put('TopGraftedTrees'), $this->put('GraftedTreesTahun'), $this->put('TopGraftedTreesTahun'), $this->put('ReplantedTreesTahun'), $this->put('M01'), $this->put('M06'), $this->put('THR'), $this->put('RCL'), $this->put('J45'), $this->put('KomposTBM'), $this->put('KomposTM'), $this->put('KomposTR'), $this->put('PupukTBM'), $this->put('PupukTM'), $this->put('PupukTR'), $this->put('SurveyNr'), $this->put('DateCreated'), $this->put('DateUpdated'), $this->put('CreatedBy'), $this->put('LastModifiedBy'), $this->put('TSH858Nr'), $this->put('RCC70Nr'), $this->put('RCC71Nr'), $this->put('RCC72Nr'), $this->put('RCC73Nr'), $this->put('LokalNr'), $this->put('S1Nr'), $this->put('S2Nr'), $this->put('ICRRI3Nr'), $this->put('ICRRI4Nr'), $this->put('ICRRI5Nr'), $this->put('M01Nr'), $this->put('M06Nr'), $this->put('THRNr'), $this->put('RCLNr'), $this->put('J45Nr'), $this->put('CloneLainNr'), $this->put('Sukun'), $this->put('Jengkol'), $this->put('Petai'), $this->put('Jabon'), $this->put('Uru'), $this->put('Biti'), $this->put('Jati'), $this->put('Jeruk'), $this->put('Manggis'), $this->put('Pepaya'), $this->put('Alpukat'), $this->put('Kemiri'), $this->put('Pala'), $this->put('Aren'), $this->put('Sawit'), $this->put('Cengkeh'), $this->put('GamalNr'), $this->put('KelapaNr'), $this->put('DurianNr'), $this->put('PinangNr'), $this->put('KaretNr'), $this->put('JackFruitNr'), $this->put('LamtoroNr'), $this->put('MahoniNr'), $this->put('PisangNr'), $this->put('RambutanNr'), $this->put('CengkehNr'), $this->put('SawitNr'), $this->put('ArenNr'), $this->put('ManggaNr'), $this->put('LangsatNr'), $this->put('PalaNr'), $this->put('KemiriNr'), $this->put('AlpukatNr'), $this->put('SukunNr'), $this->put('PepayaNr'), $this->put('ManggisNr'), $this->put('JerukNr'), $this->put('JatiNr'), $this->put('BitiNr'), $this->put('UruNr'), $this->put('JabonNr'), $this->put('PetaiNr'), $this->put('JengkolNr'), $this->put('ShadeLainNr'), $this->put('FrLain'), $this->put('DoLain'), $this->put('KimiaTidakSuka'), $this->put('KimiaLain'), $this->put('Comment'));
        if ($garden)
            $this->response($garden, 200);
        else
            $this->response(array('error' => 'Garden could not be found'), 404);
    }

    function postharvests_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $postharvests = $this->msync->readPostHarvests($provID, $districtID);
        if ($postharvests) {
            $this->response($postharvests, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Data\'s!'), 404);
        }
    }

    function postharvest_get() {
        $postharvest = $this->msync->readPostHarvest($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($postharvest) {
            $this->response($postharvest, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any post harvest!'), 404);
        }
    }

    function postharvest_post() {
        if (!$this->post('FarmerID'))
            $this->response(NULL, 400);
        $postharvest = $this->msync->createHarvest($this->post('SurveyNr'), $this->post('DateCollection'), $this->post('AdaProduksi'), $this->post('AnggotaKerjaKebun'), $this->post('BuruhSeasonal'), $this->post('BuruhSeasonalRupiah'), $this->post('BuruhSeasonalPersen'), $this->post('BuruhFulltime'), $this->post('BuruhFulltimeRupiah'), $this->post('BuruhFulltimePersen'), $this->post('Fermentation'), $this->post('FermentationDays'), $this->post('SunDryingSemen'), $this->post('DryingAlat'), $this->post('DryingDays'), $this->post('CocoaBuyers'), $this->post('NoFermentation'), $this->post('Sortasi'), $this->post('NoSortasi'), $this->post('SunDryingAspal'), $this->post('JemurYesNo'), $this->post('TidakJemur'), $this->post('SunDryingAlas'), $this->post('Distance'), $this->post('AntarSendiri'), $this->post('Comment'), $this->post('DateCreated'), $this->post('DateUpdated'), $this->post('CreatedBy'), $this->post('LastModifiedBy'), $this->post('FarmerID'));
        if ($postharvest)
            $this->response($postharvest, 200);
        else
            $this->response(array('error' => 'Post Harvest could not be found'), 404);
    }

    function postharvest_put() {
        if (!$this->put('FarmerID'))
            $this->response(NULL, 400);
        $postharvest = $this->msync->updateHarvest($this->put('SurveyNr'), $this->put('DateCollection'), $this->put('AdaProduksi'), $this->put('AnggotaKerjaKebun'), $this->put('BuruhSeasonal'), $this->put('BuruhSeasonalRupiah'), $this->put('BuruhSeasonalPersen'), $this->put('BuruhFulltime'), $this->put('BuruhFulltimeRupiah'), $this->put('BuruhFulltimePersen'), $this->put('Fermentation'), $this->put('FermentationDays'), $this->put('SunDryingSemen'), $this->put('DryingAlat'), $this->put('DryingDays'), $this->put('CocoaBuyers'), $this->put('NoFermentation'), $this->put('Sortasi'), $this->put('NoSortasi'), $this->put('SunDryingAspal'), $this->put('JemurYesNo'), $this->put('TidakJemur'), $this->put('SunDryingAlas'), $this->put('Distance'), $this->put('AntarSendiri'), $this->put('Comment'), $this->put('DateCreated'), $this->put('DateUpdated'), $this->put('CreatedBy'), $this->put('LastModifiedBy'), $this->put('FarmerID'));
        if ($postharvest)
            $this->response($postharvest, 200);
        else
            $this->response(array('error' => 'Post Harvest could not be found'), 404);
    }

    function nutritions_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $nutritions = $this->msync->readNutritions($provID, $districtID);
        if ($nutritions) {
            $this->response($nutritions, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Data\'s!'), 404);
        }
    }

    function nutrition_get() {
        $nutrition = $this->msync->readNutrition($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($nutrition) {
            $this->response($nutrition, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any nutrition!'), 404);
        }
    }

    function nutrition_post() {
        $data = $this->msync->createNutrition($this->post('SurveyNr'), $this->post('InterviewDate'), $this->post('KebunPanjang'), $this->post('KebunLebar'), $this->post('KbBayam'), $this->post('KbCabai'), $this->post('KbKacangPanjang'), $this->post('KbKangkung'), $this->post('KbSawi'), $this->post('KbTerong'), $this->post('KbTomat'), $this->post('KbKambing'), $this->post('KbSapi'), $this->post('KbBebek'), $this->post('KbAyam'), $this->post('KbIkan'), $this->post('aSagu'), $this->post('aNasi'), $this->post('aMie'), $this->post('aJagung'), $this->post('aRoti'), $this->post('bUbiJalarKuning'), $this->post('bSingkongKuning'), $this->post('bWortel'), $this->post('bLabu'), $this->post('cUbiJalarPutih'), $this->post('cSingkongPutih'), $this->post('cTalas'), $this->post('cKentang'), $this->post('dBayam'), $this->post('dDaunMelinjo'), $this->post('dDaunPepaya'), $this->post('dDaunSingkong'), $this->post('dKangkung'), $this->post('dSawi'), $this->post('eKacangPanjang'), $this->post('eTomat'), $this->post('eTerong'), $this->post('fJambuMerah'), $this->post('fMangga'), $this->post('fPepaya'), $this->post('gJambuAir'), $this->post('gKelapa'), $this->post('gPisang'), $this->post('gRambutan'), $this->post('gSemangka'), $this->post('gSalak'), $this->post('hJeroan'), $this->post('hHati'), $this->post('iAyam'), $this->post('iBebek'), $this->post('iKambing'), $this->post('iKerbau'), $this->post('iSapi'), $this->post('iLainnya'), $this->post('jAyam'), $this->post('jBebek'), $this->post('jEntok'), $this->post('jPuyuh'), $this->post('kCumiCumi'), $this->post('kIkan'), $this->post('kIkanTeri'), $this->post('kKepiting'), $this->post('kKerang'), $this->post('kUdang'), $this->post('lAirTahuSusuKedelai'), $this->post('lSausKacang'), $this->post('lTahu'), $this->post('lTempe'), $this->post('mKeju'), $this->post('mSusu'), $this->post('nMinyakGoreng'), $this->post('nMentega'), $this->post('nSantan'), $this->post('Score'), $this->post('FarmerID'), $this->post('DateCreated'), $this->post('DateUpdated'), $this->post('CreatedBy'), $this->post('LastModifiedBy'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Farmer could not be found'), 404);
    }

    function nutrition_put() {
        $data = $this->msync->updateNutrition($this->put('SurveyNr'), $this->put('InterviewDate'), $this->put('KebunPanjang'), $this->put('KebunLebar'), $this->put('KbBayam'), $this->put('KbCabai'), $this->put('KbKacangPanjang'), $this->put('KbKangkung'), $this->put('KbSawi'), $this->put('KbTerong'), $this->put('KbTomat'), $this->put('KbKambing'), $this->put('KbSapi'), $this->put('KbBebek'), $this->put('KbAyam'), $this->put('KbIkan'), $this->put('aSagu'), $this->put('aNasi'), $this->put('aMie'), $this->put('aJagung'), $this->put('aRoti'), $this->put('bUbiJalarKuning'), $this->put('bSingkongKuning'), $this->put('bWortel'), $this->put('bLabu'), $this->put('cUbiJalarPutih'), $this->put('cSingkongPutih'), $this->put('cTalas'), $this->put('cKentang'), $this->put('dBayam'), $this->put('dDaunMelinjo'), $this->put('dDaunPepaya'), $this->put('dDaunSingkong'), $this->put('dKangkung'), $this->put('dSawi'), $this->put('eKacangPanjang'), $this->put('eTomat'), $this->put('eTerong'), $this->put('fJambuMerah'), $this->put('fMangga'), $this->put('fPepaya'), $this->put('gJambuAir'), $this->put('gKelapa'), $this->put('gPisang'), $this->put('gRambutan'), $this->put('gSemangka'), $this->put('gSalak'), $this->put('hJeroan'), $this->put('hHati'), $this->put('iAyam'), $this->put('iBebek'), $this->put('iKambing'), $this->put('iKerbau'), $this->put('iSapi'), $this->put('iLainnya'), $this->put('jAyam'), $this->put('jBebek'), $this->put('jEntok'), $this->put('jPuyuh'), $this->put('kCumiCumi'), $this->put('kIkan'), $this->put('kIkanTeri'), $this->put('kKepiting'), $this->put('kKerang'), $this->put('kUdang'), $this->put('lAirTahuSusuKedelai'), $this->put('lSausKacang'), $this->put('lTahu'), $this->put('lTempe'), $this->put('mKeju'), $this->put('mSusu'), $this->put('nMinyakGoreng'), $this->put('nMentega'), $this->put('nSantan'), $this->put('Score'), $this->put('FarmerID'), $this->put('DateCreated'), $this->put('DateUpdated'), $this->put('CreatedBy'), $this->put('LastModifiedBy'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Farmer could not be found'), 404);
    }

    function ppiscorecards_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $ppiscorecards = $this->msync->readPPIScorecards($provID, $districtID);
        if ($ppiscorecards) {
            $this->response($ppiscorecards, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Data\'s!'), 404);
        }
    }

    function ppiscorecard_get() {
        $ppiscorecard = $this->msync->readPPIScorecard($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));

        if ($ppiscorecard) {
            $this->response($ppiscorecard, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any ppiscorecard!'), 404);
        }
    }

    function ppiscorecard_post() {
        if (!$this->post('FarmerID'))
            $this->response(NULL, 400);
        $ppiscorecard = $this->msync->createPPIScorecard($this->post('SurveyNr'), $this->post('InterviewDate'), $this->post('Householdmembers'), $this->post('Schooling'), $this->post('Education'), $this->post('Employment'), $this->post('HouseFloor'), $this->post('ToiletFacility'), $this->post('CookingFuel'), $this->post('GasCylinder'), $this->post('Refrigerator'), $this->post('Motorcycle'), $this->post('DateCreated'), $this->post('DateUpdated'), $this->post('CreatedBy'), $this->post('LastModifiedBy'), $this->post('FarmerID'));
        if ($ppiscorecard)
            $this->response($ppiscorecard, 200);
        else
            $this->response(array('error' => 'PPI could not be found'), 404);
    }

    function ppiscorecard_put() {
        if (!$this->put('FarmerID'))
            $this->response(NULL, 400);
        $ppiscorecard = $this->msync->updatePPIScorecard($this->put('SurveyNr'), $this->put('InterviewDate'), $this->put('Householdmembers'), $this->put('Schooling'), $this->put('Education'), $this->put('Employment'), $this->put('HouseFloor'), $this->put('ToiletFacility'), $this->put('CookingFuel'), $this->put('GasCylinder'), $this->put('Refrigerator'), $this->put('Motorcycle'), $this->put('DateCreated'), $this->put('DateUpdated'), $this->put('CreatedBy'), $this->put('LastModifiedBy'), $this->put('FarmerID'));
        if ($ppiscorecard)
            $this->response($ppiscorecard, 200);
        else
            $this->response(array('error' => 'PPI could not be found'), 404);
    }

    //deprecated
    function regionals_get() {
        $regions = $this->msync->readRegionals();
        if ($regions) {
            $this->response($regions, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Region\'s!'), 404);
        }
    }

    function areas_get() {
        $areas = $this->msync->readAreas($this->get('provID'));
        if ($areas) {
            $this->response($areas, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Area\'s!'), 404);
        }
    }

    function batchtrainings_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $batchtrainings = $this->msync->readCpgBatchTrainings($provID, $districtID);
        if ($batchtrainings) {
            $this->response($batchtrainings, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Batch Training\'s!'), 404);
        }
    }

    function batchtraining_get() {

        $batchtrainings = $this->msync->readCpgBatchTraining($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));
        if ($batchtrainings) {
            $this->response($batchtrainings, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Batch Training\'s!'), 404);
        }
    }

    function batchtrainingfarmers_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $batchtrainingfarmers = $this->msync->readCpgBatchTrainingFarmers($provID, $districtID);
        if ($batchtrainingfarmers) {
            $this->response($batchtrainingfarmers, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Participant\'s!'), 404);
        }
    }

    function batchtrainingfarmer_get() {

        $batchtrainingfarmers = $this->msync->readCpgBatchTrainingFarmer($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));
        if ($batchtrainingfarmers) {
            $this->response($batchtrainingfarmers, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Participant\'s!'), 404);
        }
    }

    function savingpilot_get() {
        $savingpilot = $this->msync->readSavingPilot($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));
        if ($savingpilot) {
            $this->response($savingpilot, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Saving pilot\'s!'), 404);
        }
    }

    function gardenpolygondetail_get() {
        $gardenpolygondetail = $this->msync->readGardenPolygonDetail($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));
        if ($gardenpolygondetail) {
            $this->response($gardenpolygondetail, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Saving pilot\'s!'), 404);
        }
    }

    function gardenpolygon_get() {
        $gardenpolygondetail = $this->msync->readGardenPolygon($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));
        if ($gardenpolygondetail) {
            $this->response($gardenpolygondetail, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Saving pilot\'s!'), 404);
        }
    }

    function accessstaff_get() {
        $accessstaff = $this->msync->readAccessStaff();
        if ($accessstaff) {
            $this->response($accessstaff, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Access staff\'s!'), 404);
        }
    }

    function icsmember_get() {
        $icsmember = $this->msync->readIcsMember($this->get('LastDownloadDateUpdated'));
        if ($icsmember) {
            $this->response($icsmember, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Member !'), 404);
        }
    }

    function batchtrainingfarmer_post() {
        if (!$this->post('FarmerID'))
            $this->response(NULL, 400);
        $batchtrainingfarmers = $this->msync->createParticipant($this->post('CpgBatchTrainingID'), $this->post('FarmerID'), $this->post('PetaniKakao'), $this->post('FamilyID'), $this->post('WritingAwal'), $this->post('WritingAkhir'), $this->post('BallotAwal'), $this->post('BallotAkhir'), $this->post('DateCreated'), $this->post('CreatedBy'));
        if ($batchtrainingfarmers)
            $this->response($batchtrainingfarmers, 200);
        else
            $this->response(array('error' => ' Something error!'), 404);
    }

    function families_get() {
        $provID = $this->get('provID');
        $districtID = $this->get('districtID');
        $families = $this->msync->readFamilies($provID, $districtID);
        if ($families) {
            $this->response($families, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Family\'s!'), 404);
        }
    }

    function family_get() {

        $families = $this->msync->readFamily($this->get('Province'), $this->get('District'), $this->get('LastDownloadDateUpdated'));
        if ($families) {
            $this->response($families, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Family\'s!'), 404);
        }
    }

    function family_post() {
        if (!$this->post('AnggotaName'))
            $this->response(NULL, 400);
        $data = $this->msync->createFamily($this->post('FarmerID'), $this->post('AnggotaName'), $this->post('HubunganKeluarga'), $this->post('AnggotaAge'), $this->post('AnggotaGender'), $this->post('StatusSekolah'), $this->post('DateCreated'), $this->post('DateUpdated'), $this->post('LastModifiedBy'));
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Farmer could not be found'), 404);
    }

    function syncupdate_post() {
        $data = json_decode($this->post('data'), true);

//      local debugging
//        $data = $this->post('data');
//        $this->response($this->msync->syncUpdateData($data), 200);
//        exit;

        $sync_update_data = $this->msync->syncUpdateData($data);
        if ($sync_update_data) {

            // send email notification
            $user_id = $this->post('user_id');
            $user = $this->msync->getDetailUser($user_id);
            $date = date('Y-m-d H:i');
            if ($user) {
                $str = '';
                if ($user['gender'] == 'm') {
                    $gender = "Bapak";
                } else {
                    $gender = "Ibu";
                }
                $str .= 'Yth. ' . $gender . ' ' . $user['name'] . ',<br/><br/>';
                $str .= 'Anda telah melakukan sinkronisasi data pada tanggal ' . $date . '<br/> Berikut rincian data sinkronisasi : <br/>';
                foreach ($data as $key => $val) {
                    print_r($val);
                    $label = $detail = '';
                    if ($key == 'ktv_village') {
                        $label = 'Data Desa';
                    } else if ($key == 'ktv_village_crop') {
                        $label = 'Data Komoditas Desa';
                    } else if ($key == 'ktv_village_infrastructure') {
                        $label = 'Data Infrastruktur Desa';
                    } else if ($key == 'ktv_farmer') {
                        $label = 'Data Petani';
                    } else if ($key == 'ktv_farmer_garden') {
                        $label = 'Data Kebun';
                    } else if ($key == 'ktv_family') {
                        $label = 'Data Keluarga Petani';
                    } else if ($key == 'ktv_farmer_garden_status') {
                        $label = 'Data Status Kebun';
                    } else if ($key == 'ktv_farmer_other_land') {
                        $label = 'Data Kebun Selain Kakao';
                    } else if ($key == 'ktv_ppiscore2012') {
                        $label = 'Data PPI';
                    } else if ($key == 'ktv_nutrition') {
                        $label = 'Data Nutrisi';
                    } else if ($key == 'ktv_farmer_post_harvest') {
                        $label = 'Data Pasca Panen';
                    } else if ($key == 'ktv_farmer_financial') {
                        $label = 'Data Finansial';
                    } else if ($key == 'ktv_certification') {
                        $label = 'Data Sertifikasi';
                    } else if ($key == 'ktv_certification_audit_log') {
                        $label = 'Data Audit Sertifikasi';
                    } else if ($key == 'ktv_certification_signature') {
                        $label = 'Data Tanda Tangan Sertifikasi';
                    } else if ($key == 'ktv_saving_pilot') {
                        $label = 'Data Saving Pilot';
                    } else if ($key == 'ktv_cpg_batch_trainings_attendance') {
                        $label = 'Data Kehadiran Pelatihan';
                    } else if ($key == 'ktv_farmer_garden_area') {
                        $label = 'Data Polygon';
                    } else if ($key == 'ktv_farmer_garden_area_detail') {
                        $label = 'Data Detail Polygon';
                    } else {
                        $label = 'Data Lain';
                    }
                    $str .= $label . ' = ' . count($data[$key]) . ' data<br/>';
                    if ($detail !== '') {
                        $str .= '';
                    }
                }
                $str .= "<br/>Harap tidak membalas email ini karena terkirim secara otomatis oleh sistem<br/>";
                $str .= "<br/>Demikian disampaikan, atas perhatian $gender kami ucapkan terima kasih.<br/>";
                $str .= "<br/>Salam Hangat,<br/>";
                $str .= "<br/><br/>";
                $str .= "<br/>&copy; Cocoatrace.<br/>";
                
                if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL) === false) {
                    $this->load->library('email');
                    $this->email->initialize($this->config->load('email'));
                    $this->email->from('support@koltiva.com', 'Koltiva Support');
                    $this->email->to($user['email']);
                    $this->email->cc('info@koltiva.com');
                    $this->email->subject($user['name'] . ' - Sync : ' . $date);
                    $this->email->message($str);
                    $this->email->send();
                }
            }
            $this->msync->insertSyncLog($this->post('user_id'), $date, $str);
            $this->response($sync_update_data, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t update data!'), 404);
        }
    }

    function user_get() {

        $user = $this->msync->readUser($this->get('LastDownloadDateUpdated'));
        if ($user) {
            $this->response($user, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any user\'s!'), 404);
        }
    }

    function person_get() {

        $person = $this->msync->readPerson();
        if ($person) {
            $this->response($person, 200);
        } else {
            $this->response(array('error' => 'Couldn\'t find any person\'s!'), 404);
        }
    }

    function programstaff_get() {

        $programstaff = $this->msync->readProgramStaff($this->get('LastDownloadDateUpdated'));
        if ($programstaff) {
            $this->response($programstaff, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any programstaff\'s!'), 404);
        }
    }

    function privatestaff_get() {

        $PrivateStaff = $this->msync->readPrivateStaff($this->get('LastDownloadDateUpdated'));
        if ($PrivateStaff) {
            $this->response($PrivateStaff, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any PrivateStaff\'s!'), 404);
        }
    }

    function programpartner_get() {

        $ProgramPartner = $this->msync->readProgramPartner();
        if ($ProgramPartner) {
            $this->response($ProgramPartner, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any ProgramPartner\'s!'), 404);
        }
    }

    function districtpartner_get() {

        $DistrictPartner = $this->msync->readDistrictPartner();
        if ($DistrictPartner) {
            $this->response($DistrictPartner, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any District Partner\'s!'), 404);
        }
    }

    function traderstaff_get() {

        $traderstaff = $this->msync->readTraderStaff($this->get('LastDownloadDateUpdated'));
        if ($traderstaff) {
            $this->response($traderstaff, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any traderstaff\'s!'), 404);
        }
    }

    function traders_get() {

        $traders = $this->msync->readTraders($this->get('LastDownloadDateUpdated'));
        if ($traders) {
            $this->response($traders, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any traders\'s!'), 404);
        }
    }

    function surveys_get() {
        $survey = $this->msync->readSurveys();
        if ($survey) {
            $this->response($survey, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Survey\'s!'), 404);
        }
    }

    function survey_get() {

        $survey = $this->msync->readSurvey();
        if ($survey) {
            $this->response($survey, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Family\'s!'), 404);
        }
    }

    function dashboards_get() {

        $dashboard = $this->mdashboard->readDashboards();
        if ($dashboard) {
            $this->response($dashboard, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Data\'s!'), 404);
        }
    }

    function fixtablefarmer_get() {
        $fix = $this->msync->fixTableFarmer();
        if ($fix) {
            $this->response($fix, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t fix table\'s!'), 404);
        }
    }

    function activitygarden_get() {

        $activitygarden = $this->msync->readActivityGarden($this->get('UserID'), $this->get('DateCollection'));
        if ($activitygarden) {
            $this->response($activitygarden, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Activity\'s!'), 404);
        }
    }

    function activitypostharvest_get() {

        $activitypostharvest = $this->msync->readActivityPostHarvest($this->get('UserID'), $this->get('DateCollection'));
        if ($activitypostharvest) {
            $this->response($activitypostharvest, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Activity\'s!'), 404);
        }
    }

    function activityppi_get() {

        $activityppi = $this->msync->readActivityPPI($this->get('UserID'), $this->get('DateCollection'));
        if ($activityppi) {
            $this->response($activityppi, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Activity\'s!'), 404);
        }
    }

    function activityfarmer_get() {

        $activity = $this->msync->readActivityFarmer($this->get('UserID'), $this->get('DateCollection'));
        if ($activity) {
            $this->response($activity, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Activity\'s!'), 404);
        }
    }

    function activityfamily_get() {

        $activity = $this->msync->readActivityFamily($this->get('UserID'), $this->get('DateCollection'));
        if ($activity) {
            $this->response($activity, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Activity\'s!'), 404);
        }
    }

    function activitynutrition_get() {

        $activitynutrition = $this->msync->readActivityNutrition($this->get('UserID'), $this->get('DateCollection'));
        if ($activitynutrition) {
            $this->response($activitynutrition, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Activity\'s!'), 404);
        }
    }

    function allactivity_get() {

        $allactivity = $this->msync->readAllActivity($this->get('UserID'), $this->get('DateCollection'));
        if ($allactivity) {
            $this->response($allactivity, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Activity\'s!'), 404);
        }
    }

    function farmersummary_get() {

        $farmersummary = $this->msync->readFarmerSummary($this->get('FarmerID'));
        if ($farmersummary) {
            $this->response($farmersummary, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Couldn\'t find any Data\'s!'), 404);
        }
    }

    function farmerphoto_post() {
        if ($_FILES['Photo']['name'] != '') {
            log_message('debug', 'path : ' . $this->post('path'));
            log_message('debug', 'photo name : ' . $_FILES['Photo']['name']);
            $gambar = $_FILES['Photo']['name'];
            $server_path = '';
            if ($this->post('server') == 'demo.cocoatrace.com') {
                $server_path = '/var/www/cocoatraceapp_dev/api/';
            } else {
                $server_path = '/var/www/cocoatraceapp/api/';
            }
            $upload = move_upload($_FILES, $server_path . 'images/Photo/' . $this->post('path') . '/' . $gambar);
            if (isset($upload['upload_data'])) {

                $result['success'] = true;
                $result['file'] = $gambar;
                $this->response($result, 200);
            }
        }
    }

    function farmerfile_post() {
        if ($_FILES['File']['name'] != '') {
            log_message('debug', 'file : ' . $this->post('path'));
            log_message('debug', 'file name : ' . $_FILES['File']['name']);
            $file = $_FILES['File']['name'];
            $server_path = '';
            if ($this->post('server') == 'demo.cocoatrace.com') {
                $server_path = '/var/www/cocoatraceapp_dev/api/';
            } else if ($this->post('server') == 'app.cocoatrace.com') {
                $server_path = '/var/www/cocoatraceapp/api/';
            } else {
                $server_path = '/var/www/cocoatraceapp_devel/api/';
            }
            $upload = move_upload($_FILES, $server_path . 'files/upload/' . $file);
            if (isset($upload['upload_data'])) {
                $result['success'] = true;
                $result['file'] = $file;
                $this->response($result, 200);
            }
        }
    }

    /**
     * Fungsi-fungsi untuk syncronize traceability app
     * @author Ardi <ardiantoro@koltiva.com>
     */
    public function traceabilityupdate_post() {

        $data = json_decode($this->post('data'), true);
        $a = $this->msync->sync_traceability($data);
        $this->response($a, 200);
    }

    public function traceabilitydownload_post() {

        $data = json_decode($this->post('data'), true);
        $unit = $this->post('uid');
        $last = $this->post('lastupdated');
        $this->response($this->msync->sync_traceability_download($data, $unit, $last), 200);
    }

    public function traceabilitydownloadfarmer_post() {
        //ini_set('display_errors',true);
        //error_reporting(E_ALL);
        
        $supply = $this->post('sid');
        $last = $this->post('lastupdated');
        $data = $this->msync->sync_traceability_download_farmer($supply, $last);
        $this->response($data, 200);
    }

    public function traceabilitydownloadvillage_post() {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        $partner = $this->post('pid');
        $last = $this->post('lastupdated');
        $this->response($this->msync->sync_traceability_download_village($partner, $last), 200);
    }

    public function traceabilitydownloadcpg_post() {

        $partner = $this->post('pid');
        $last = $this->post('lastupdated');
        $this->response($this->msync->sync_traceability_download_cpg($partner, $last), 200);
    }

    public function traceabilitydownloadsetting_post() {

        $partner = $this->post('pid');
        $supplychain = $this->post('sid');
        $unit = $this->post('uid');
        $last = $this->post('lastupdated');

        $this->response($this->msync->sync_traceability_download_setting($partner, $unit, $supplychain, $last), 200);
    }

    public function testjson_post() {

        //deklarasi
        $data = $this->post();
        //var_dump($data);die;
        //modifikasi
        jsonParser($data, array(
            'ktv_farmer',
            'ktv_family'
                ), array(
            'VillageHeadName',
            'VillageHeadGender'
        ));
        //die;
        //return
        $this->response(array('success' => true, 'data' => $data), 200);
    }

    public function tes_kirim_email_get() {
        $this->load->library('email');
        $this->email->initialize($this->config->load('email'));
        $this->email->from('your@example.com', 'Your Name');
        $this->email->to('mawwat.udi@gmail.com');
        $this->email->cc('noersa.eka@gmail.com');
        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');
        $this->email->send();
        echo $this->email->print_debugger();
    }

    /**
     * Download batch from web -> mobile sent to supplychainid
     * @author Ardi <ardiantoro@koltiva.com>
     * @api traceability/download-batch
     * @param int $supplychain SupplyChainID
     */
    public function getbatch_get($supplychain)
    {
        $return = array('success' => true, 'data' => array());
        $data = $this->msync->sync_traceability_download_batch($supplychain);
        
        if($data) {
            $return = $data;
        }
        
        $this->response($return, 200);
    }

}
