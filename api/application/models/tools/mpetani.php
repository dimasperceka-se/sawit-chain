<?php

class Mpetani extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function tool($dataa) {
        /* //1-
          $data = $dataa->sheets[0]['cells'];
          $sql = "
          UPDATE ktv_farmer b
          SET
          b.Address = ?,b.TotalLahan = ?,b.LahanKakao = ?,b.LahanProduksiLain = ?,b.LahanKosong = ?,
          b.KebunKakao = ?,b.Muge = ?, b.LastModifiedBy=1
          WHERE b.OldFarmerID=?";
          for ($i=2;$i<sizeof($data)+1;$i++) {
          $stat = $this->db->query($sql, array($data[$i][9],$data[$i][25],$data[$i][26],$data[$i][27],$data[$i][28],
          $data[$i][29],$data[$i][17],$data[$i][1]));
          if (!$stat) $err .= '|1|'.$data[$i][1].'|';
          }
          /*
          //2-
          $data = $dataa->sheets[0]['cells'];
          $sql = "
          UPDATE ktv_farmer_garden b
          SET b.OwnershipCocoa = ?,b.TahunTanamanCocoa = ?,b.GardenDistance = ?,b.GardenHaUnCertified = ?,
          b.PanenBiasaMonths = ?,b.PanenBiasaPanenMonth = ?,b.PanenBiasaKg = ?,b.PanenTrekMonths = ?,
          b.PanenTrekPanenMonth = ?,b.PanenTrekKg = ?,b.PanenRayaMonths = ?,b.PanenRayaPanenMonth = ?,
          b.PanenRayaKg = ?,b.TimeHarvestBiasa = ?,b.TimeHarvestTrek = ?,b.TimeHarvestRaya = ?,
          b.LandCertificate = ?,b.PohonTBM = ?,b.PohonTM = ?,b.PohonRehab = ?,

          b.GraftedTrees = ?,b.ReplantedTrees = ?,b.RoadCondition = ?,b.TSH858 = ?,
          b.RCC70 = ?,b.RCC71 = ?,b.RCC72 = ?,b.RCC73 = ?,
          b.Hybrid = ?,b.S1 = ?,b.S2 = ?,  b.ICRRI3 = ?,b.ICRRI4 = ?,
          b.ICRRI5 = ?,b.M01 = ?,b.M06 = ?,b.THR = ?,
          b.RCL = ?,b.J45 = ?,b.CloneLain = ?,b.Gamal = ?,

          b.Kelapa = ?,b.Durian = ?,b.Pinang = ?,b.Karet = ?,
          b.JackFruit = ?,b.Lamtoro = ?,b.Mahoni = ?,b.Pisang = ?,
          b.Rambutan = ?,b.Mangga = ?,b.Langsat = ?,b.ShadeLain = ?,
          b.ShadeTreesNr = ?,b.TimeHarvest = ?,b.HarvestAwal = ?,b.HarvestMasak = ?,
          b.HarvestHama = ?,b.PruningPlants = ?,b.FrequentPruning = ?,b.HighPruning = ?,

          b.PruningProtectPlants = ?,b.FrequentPruningProtect = ?,b.CleanSkin = ?,b.HowToCleanSkin = ?,
          b.OrganicKotoran = ?,b.OrganicResidu = ?,b.OrganicMembeli = ?,b.TidakMemakaiOrganic = ?,
          b.Urea = ?,b.TSP = ?,b.NPK = ?,b.KCL = ?,
          b.TidakMemakaiKimia = ?,b.FrequentFertilizationOrganic = ?,b.DoseFertilizerOrganic = ?,b.FrequentFertilizationKimia = ?,
          b.DoseFertilizerKimia = ?,b.PakaiKompos = ?,b.FrequentFertilizationKompos = ?,b.DoseFertilizerKompos = ?,

          b.FrUrea = ?,b.FrTsp = ?,b.FrNpk = ?,b.FrKcl = ?,
          b.DoUrea = ?,b.DoTsp = ?,b.DoNpk = ?,b.DoKcl = ?,
          b.KimiaDana = ?,b.KimiaSupplier = ?,b.KimiaDilatih = ?,b.HamaBPK = ?,
          b.HamaHelopeltis = ?,b.HamaBatang = ?,b.PenyakitKanker = ?,b.PenyakitBusuk = ?,
          b.PenyakitUpas = ?,b.PenyakitAkar = ?,b.PenyakitVSD = ?,b.PenyakitAntraknose = ?,

          b.Herbisida = ?,b.MerekHerbisida = ?,b.FrequentHerbisida = ?,b.DoseHerbisida = ?,
          b.Herbisida1 = ?,b.Herbisida2 = ?,b.Herbisida3 = ?,b.Herbisida4 = ?,
          b.Herbisida5 = ?,b.Herbisida6 = ?,b.Herbisida7 = ?,b.Herbisida8 = ?,
          b.Herbisida9 = ?,b.Herbisida10 = ?,b.Insectisida = ?,b.MerekInsectisida = ?,
          b.FrequentInsectisida = ?,b.DoseInsectisida = ?,b.Insectisida1 = ?,b.Insectisida2 = ?,

          b.Insectisida3 = ?,b.Insectisida4 = ?,b.Insectisida5 = ?,b.Insectisida6 = ?,
          b.Insectisida7 = ?,b.Insectisida8 = ?,b.Insectisida9 = ?,b.Insectisida10 = ?,
          b.Fungisida = ?,b.MerekFungisida = ?,b.FrequentFungisida = ?,b.DoseFungisida = ?,
          b.Fungisida1 = ?,b.Fungisida2 = ?,b.Fungisida3 = ?,b.Fungisida4 = ?,
          b.Fungisida5 = ?,b.Fungisida6 = ?,b.Fungisida7 = ?,b.Fungisida8 = ?,

          b.Fungisida9 = ?,b.Fungisida10 = ?,b.APD = ?,b.TempatSimpanPestisida = ?,
          b.BuangKemasanPestisida = ?,b.TopGraftedTrees = ?,b.GraftedTreesTahun = ?,b.TopGraftedTreesTahun = ?,
          b.ReplantedTreesTahun = ?,b.KomposTBM = ?,b.KomposTM = ?,b.KomposTR = ?,
          b.PupukTBM = ?,b.PupukTM = ?,b.PupukTR = ?,b.LastModifiedBy = 1,b.DateUpdated = NOW()
          WHERE b.OldFarmerID =? and b.GardenNr =? and b.SurveyNr =?";
          for ($i=2;$i<sizeof($data)+1;$i++) {
          $stat = $this->db->query($sql, array($data[$i][12],$data[$i][30],$data[$i][13],$data[$i][14],
          $data[$i][15],$data[$i][16],$data[$i][17],$data[$i][18],
          $data[$i][19],$data[$i][20],$data[$i][21],$data[$i][22],
          $data[$i][22],$data[$i][139],$data[$i][140],$data[$i][141],
          $data[$i][38],$data[$i][24],$data[$i][25],$data[$i][26],

          $data[$i][27],$data[$i][28],$data[$i][34],$data[$i][35],
          $data[$i][36],$data[$i][37],$data[$i][38],$data[$i][39],
          $data[$i][40],$data[$i][41],$data[$i][42],$data[$i][43],$data[$i][44],
          $data[$i][45],$data[$i][46],$data[$i][47],$data[$i][48],
          $data[$i][49],$data[$i][50],$data[$i][51],$data[$i][52],

          $data[$i][53],$data[$i][54],$data[$i][55],$data[$i][56],
          $data[$i][57],$data[$i][58],$data[$i][59],$data[$i][60],
          $data[$i][61],$data[$i][62],$data[$i][63],$data[$i][64],
          $data[$i][65],$data[$i][66],$data[$i][67],$data[$i][67],
          $data[$i][69],$data[$i][70],$data[$i][71],$data[$i][72],

          $data[$i][73],$data[$i][74],$data[$i][75],$data[$i][76],
          $data[$i][77],$data[$i][78],$data[$i][79],$data[$i][80],
          $data[$i][81],$data[$i][82],$data[$i][83],$data[$i][84],
          $data[$i][85],$data[$i][86],$data[$i][87],$data[$i][88],
          $data[$i][89],$data[$i][90],$data[$i][91],$data[$i][92],

          $data[$i][142],$data[$i][143],$data[$i][144],$data[$i][145],
          $data[$i][146],$data[$i][147],$data[$i][148],$data[$i][149],
          $data[$i][150],$data[$i][151],$data[$i][152],$data[$i][154],
          $data[$i][155],$data[$i][156],$data[$i][157],$data[$i][158],
          $data[$i][159],$data[$i][160],$data[$i][161],$data[$i][162],

          $data[$i][93],$data[$i][102],$data[$i][96],$data[$i][99],
          $data[$i][108],$data[$i][109],$data[$i][110],$data[$i][111],
          $data[$i][112],$data[$i][113],$data[$i][114],$data[$i][115],
          $data[$i][116],$data[$i][117],$data[$i][95],$data[$i][103],
          $data[$i][97],$data[$i][100],$data[$i][128],$data[$i][129],

          $data[$i][130],$data[$i][131],$data[$i][132],$data[$i][133],
          $data[$i][134],$data[$i][135],$data[$i][136],$data[$i][137],
          $data[$i][94],$data[$i][104],$data[$i][98],$data[$i][101],
          $data[$i][118],$data[$i][119],$data[$i][120],$data[$i][121],
          $data[$i][122],$data[$i][123],$data[$i][124],$data[$i][125],

          $data[$i][126],$data[$i][127],$data[$i][153],$data[$i][105],
          $data[$i][106],$data[$i][29],$data[$i][31],$data[$i][32],
          $data[$i][33],$data[$i][163],$data[$i][164],$data[$i][165],
          $data[$i][166],$data[$i][167],$data[$i][168],$data[$i][1],
          $data[$i][2],$data[$i][3]));
          if (!$stat) $err .= '|2|'.$data[$i][1].'|';
          }
          /*3-
          $data = $dataa->sheets[0]['cells'];
          $sql = "
          UPDATE ktv_farmer_post_harvest b
          SET b.AnggotaKerjaKebun = ?,b.BuruhSeasonal = ?,b.BuruhFullTime = ?,b.Fermentation = ?,
          b.FermentationDays = ?,b.SunDryingSemen = ?,b.DryingAlat = ?,b.DryingDays = ?,
          b.CocoaBuyers = ?,b.NoFermentation = ?,b.Sortasi = ?,b.NoSortasi = ?,
          b.SunDryingAspal = ?,b.JemurYesNo = ?,b.TidakJemur = ?,b.SunDryingAlas = ?,
          b.LastModifiedBy=1,b.DateUpdated=NOW()
          WHERE b.FarmerID=? AND b.SurveyNr=? ";
          for ($i=2;$i<sizeof($data)+1;$i++) {
          $stat = $this->db->query($sql, array($data[$i][4],$data[$i][5],$data[$i][6],$data[$i][7],
          $data[$i][8],$data[$i][9],$data[$i][10],$data[$i][11],
          $data[$i][12],$data[$i][13],$data[$i][14],$data[$i][15],
          $data[$i][16],$data[$i][17],$data[$i][18],$data[$i][19],
          $data[$i][1],$data[$i][2]));
          if (!$stat) $err .= '|3|'.$data[$i][1].'|';
          }
          ///4-*
          $data = $dataa->sheets[0]['cells'];
          $sql = "
          INSERT INTO ktv_family(FarmerID,OldFarmerID,AnggotaName,HubunganKeluarga,AnggotaAge,AnggotaGender,
          StatusSekolah,DateCreated,DateUpdated,LastModifiedBy)
          SELECT FarmerID,OldFarmerID,?,?,?,?,   ?,now(),now(),1 FROM ktv_farmer WHERE OldFarmerID=?";
          for ($i=2;$i<sizeof($data)+1;$i++) {
          $stat = $this->db->query($sql, array($data[$i][4],$data[$i][3],$data[$i][5],$data[$i][6],
          $data[$i][7],$data[$i][2]));
          if (!$stat) $err .= '|4|'.$data[$i][1].'|';
          }
          ///5- */
        $data = $dataa->sheets[0]['cells'];
        $sql = "
            UPDATE ktv_ppiscore2012 b
            SET b.Householdmembers = ?,b.Schooling = ?,b.Education = ?,b.Employment = ?,
               b.HouseFloor =?,b.ToiletFacility = ?,b.CookingFuel = ?,b.GasCylinder = ?,
               b.Refrigerator = ?,b.MotorCycle = ?,b.LastModifiedBy=1,b.DateUpdated=NOW()
            WHERE b.OldFarmerID =? and b.SurveyNr=?";
        for ($i = 2; $i < sizeof($data) + 1; $i++) {
            $stat = $this->db->query($sql, array($data[$i][4], $data[$i][5], $data[$i][6], $data[$i][7],
                $data[$i][8], $data[$i][9], $data[$i][10], $data[$i][11],
                $data[$i][12], $data[$i][13], $data[$i][1], $data[$i][2]));
            if (!$stat)
                $err .= '|5|' . $data[$i][1] . '|';
        }
        return $err;
    }

    function readDatas($userId) {
        $sql = "
            select FarmerID as id, FarmerName as nama, Address as alamat, Gender as kelamin, Birthdate as lahir,
               DateCollection,DateUpdated,CPGid,VillageID,Address,FarmerName,HandPhone,Gender,MaritalStatus,Birthdate,Education
            from ktv_farmer_temp
            WHERE CreatedBy=?
            ORDER BY FarmerName";
        $query = $this->db->query($sql, array($userId));
        $result['data'] = $query->result_array();
        return $result;
    }

    function readCpgs($prov, $kab) {
        if ($prov != '')
            $add = "and kp.ProvinceID=$prov";
        if ($kab != '')
            $add .= " and District='$kab'";
        $sql = "
            select CPGid id,concat('[',CPGid,'] ',GroupName) label
            from ktv_cpg
            left join ktv_village kv on kv.VillageID = ktv_cpg.VillageID
            left join ktv_subdistrict ksd on ksd.SubDistrictID = kv.SubDistrictID
            left join ktv_district kd on kd.DistrictID = ksd.DistrictID
            left join ktv_province kp on kp.ProvinceID = kd.ProvinceID
            WHERE CPGid>0 $add
            ORDER BY GroupName";
        $query = $this->db->query($sql, array());
        $result['data'] = $query->result_array();
        return $result;
    }

    function readSubdistricts($prov, $kab) {
        if ($prov != '')
            $add = "and c.ProvinceID = $prov";
        if ($kab != '')
            $add .= " and b.DistrictID = $kab ";
        $sql = "
            select a.SubDistrictID id,concat('[',a.SubDistrictID,'] ',a.SubDistrict) label
            from ktv_subdistrict a
            left join ktv_district b on b.DistrictID = a.DistrictID
            left join ktv_province c on c.ProvinceID = b.ProvinceID
            WHERE a.SubDistrictID>0 $add
            ORDER BY SubDistrict";
        $query = $this->db->query($sql, array());
        $result['data'] = $query->result_array();
        return $result;
    }

    function readVillages($prov, $kab, $kec) {
        if ($prov != '')
            $add = "and d.ProvinceID =$prov";
        if ($kab != '')
            $add .= " and b.DistrictID = $kab";
        if ($kec != '')
            $add .= " and b.SubDistrictID= $kec ";
        $sql = "
            select a.VillageID id,concat('[',a.VillageID,'] ',a.Village) label
            from ktv_village a
            left join ktv_subdistrict b on b.SubDistrictID = a.SubDistrictID
            left join ktv_district c on c.DistrictID=b.DistrictID
            left join ktv_province d on d.ProvinceID = c.ProvinceID
            WHERE a.VillageID>0 $add
            ORDER BY a.Village";
        $query = $this->db->query($sql, array());
        $result['data'] = $query->result_array();
        return $result;
    }

    function readDataFarmers($key, $prov, $kab, $cpg) {
        if ($prov == '' OR $prov == 'null')
            $prov = '%%';
        if ($district != '' AND $district != 'null')
            $add = " and District like '$district'";
        if ($cpg != '' and $cpg != 'null')
            $add .= " and CPGid = $cpg";
        $sql = "
            select FarmerID,Photo,Province
            from ktv_farmer a
            left join ktv_village kv on kv.VillageID = a.VillageID
            left join ktv_subdistrict ksd on ksd.SubDistrictID = kv.SubDistrictID
            left join ktv_district kd on kd.DistrictID = ksd.DistrictID
            left join ktv_province kp on kp.ProvinceID = kd.ProvinceID

            where (a.FarmerID=? OR FarmerName like ?) and kp.ProvinceID like ? %s
            ORDER BY FarmerID";
        $query = $this->db->query(sprintf($sql, $add), array($key, '%' . $key . '%', $prov));
        return $query->result_array();
    }

    function updateDataPetani($farmerid, $path, $status) {
        $sql = "
            INSERT INTO cek_photo (FarmerID,Path,Status) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE Path=?, Status=?";
        return $this->db->query($sql, array($farmerid, $path, $status, $path, $status));
    }

    function readDataPetanis($key, $prov, $district, $subdistrict, $village, $start, $limit) {
        if ($prov == '' OR $prov == 'null')
            $prov = " and e.ProvinceID = $prov";
        if ($district != '' AND $district != 'null')
            $add = " and d.DistrictID = $district";
        if ($subdistrict != '' and $subdistrict != 'null')
            $add .= " and c.SubDistrictID = $subdistrict";
        if ($village != '' and $village != 'null')
            $add .= " and b.VillageID = $village";
        $sql = "select %s
            from ktv_members a
            left join ktv_village b on a.VillageID = b.VillageID
            left join ktv_subdistrict c on c.SubDistrictID = b.SubDistrictID
            left join ktv_district d on d.DistrictID = c.DistrictID
            left join ktv_province e on e.ProvinceID = d.ProvinceID
            where (a.MemberDisplayID=? OR a.MemberName like ?) AND a.StatusCode='active' and e.ProvinceID like ?
            %s
            ORDER BY a.MemberName %s";
        $query = $this->db->query(sprintf($sql, "a.MemberDisplayID AS FarmerID ,a.MemberName AS FarmerName,CONCAT(e.ProvinceID,'/',a.Photo) AS Path,IF(a.Gender = 1,'Male','Female') as 'FarmerGender',a.Photo, a.StatusCode", $add, 'LIMIT ?,?'), array($key, '%' . $key . '%', $prov, (int) $start, (int) $limit));
        
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', $add, ''), array($key, '%' . $key . '%', $prov));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function injectFarmer($data, $userId) {
        /* tools*
          $sql = "
          insert into ktv_certification_temp (FarmerID, GardenNr, Certification, CandidateSelection, FirstYear,
          SecondYear, ThirdYear, FourthYear, DateCreated, DateUpdated, CreatedBy, LastModifiedBy)
          values (?,?,?,?,?,   ?,?,?,now(),now(),?,?)";
          for ($i=2;$i<sizeof($data)-2;$i++) {
          $this->db->query($sql, array($data[$i][1],$data[$i][2],$data[$i][3],$data[$i][4].' 00:00:00',$data[$i][5].' 00:00:00',
          null,null,null,$userId,$userId));
          }
          return;
          /*
          $sql = "
          insert into ktv_photo (PhotoFarmerId, PhotoPath)
          values (?,?)";
          //sizeof($data)+1
          for ($i=2;$i<5000;$i++) {
          $exp = explode('\\',$data[$i][2]);
          $del = '';
          for ($j=0;$j<sizeof($exp);$j++) {
          if ($exp[$j]=='12 Photo Petani') {
          $file = str_replace($del,'',$data[$i][2]);
          break;
          }
          $del .= $exp[$j].'\\';
          }
          $this->db->query($sql, array($data[$i][1],$file));
          }
          return;
          /*$sql = "
          UPDATE ktv_farmer
          SET StatusCode='active'
          WHERE FarmerID=?";
          for ($i=2;$i<sizeof($data)+1;$i++) {
          $this->db->query($sql, array($data[$i][3]));
          }
          return; */
//print_r($data);exit;
        for ($i = 1; $i < sizeof($data[4]) + 1; $i++) {
            if (trim($data[4][$i]) != '')
                $ff[] = $data[4][$i];
            if ($data[4][$i] == 'Birthdate')
                $a = $i;
        }
        $field = implode(',', $ff);
        for ($i = 0; $i < sizeof($ff); $i++) {
            $isian[] = '?';
        }
        $isi = implode(',', $isian);
        $sql = "insert into ktv_farmer_temp ($field,DateCreated,CreatedBy) values ($isi,now(),?)";
        $this->db->query("DELETE FROM ktv_farmer_temp WHERE CreatedBy=$userId");

        $result = true;
        for ($i = 5; $i < sizeof($data) + 10; $i++) {
            if ($data[$i][1] != '') {
                for ($j = 1; $j < sizeof($ff) + 1; $j++) {
                    $value[$j] = $data[$i][$j];
                    if ($j == $a and $data[$i][$j] == '')
                        $data[$i][$j] = null;
                }
                $value[] = $userId;
                $result = $result && $this->db->query($sql, $value);
            }
        }
        return $result;
    }

    function _generateFarmerID($district) {
        $sql = "
            SELECT IFNULL(IF(length(max(FarmerId))!=9,concat(substr(FarmerId,1,4),LPAD(substr(max(FarmerId)+1,5,5),5,'0')),max(FarmerID)+1),
               concat(?,'00001')) as id
            FROM ktv_farmer
            WHERE substr(FarmerId,1,4)=substr(?,1,4)";
        $query = $this->db->query($sql, array($district, $district));
        $result = $query->result_array();
        return $result[0]['id'];
    }

    function injectFarmerData($userId) {

        $farmers = array();

        $sql_get = "SELECT FarmerID,kd.DistrictID as district 
                    FROM ktv_farmer_temp
                    LEFT JOIN ktv_village kv ON kv.VillageID = ktv_farmer_temp.VillageID
                    LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
                    LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                    WHERE CreatedBy=?";
        $sql_update = "UPDATE ktv_farmer_temp SET FarmerID=?,DateCreated=now() WHERE FarmerID=?";
        // ktv_farmer has columns that ktv_farmer_temp does not (schema drift), so a
        // bare INSERT ... SELECT * fails with "Column count doesn't match value count".
        // Build an explicit column list from ktv_farmer_temp (its columns are a subset
        // of ktv_farmer); the extra ktv_farmer columns keep their defaults.
        $tempCols = $this->db->list_fields('ktv_farmer_temp');
        $colList  = '`' . implode('`,`', $tempCols) . '`';
        $sql_copy = "INSERT INTO ktv_farmer ($colList) SELECT $colList FROM ktv_farmer_temp WHERE CreatedBy=? and FarmerID=?";
        $sql_update_bithdate = "UPDATE ktv_farmer SET Birthdate=null WHERE FarmerID=? and Birthdate='0000-00-00'";

        $sql_harvest = "
            INSERT INTO ktv_farmer_post_harvest(FarmerID,SurveyNr,DateCreated,CreatedBy)
            VALUES (?,0,now(),?)";
        $sql_garden = "
            INSERT INTO ktv_farmer_garden(FarmerID,GardenNr,SurveyNr,DateCreated,CreatedBy)
            VALUES (?,1,0,now(),?)";
        $sql_ppi = "
            INSERT INTO ktv_ppiscore(FarmerID,SurveyNr,DateCreated,CreatedBy)
            VALUES (?,0,now(),?)";
        $sql_ppi_2012 = "
            INSERT INTO ktv_ppiscore2012(FarmerID,SurveyNr,DateCreated,CreatedBy)
            VALUES (?,0,now(),?)";
        $sql_nutrition = "
            INSERT INTO ktv_nutrition(FarmerID,SurveyNr,DateCreated,CreatedBy)
            VALUES (?,0,now(),?)";
        $query = $this->db->query($sql_get, array($userId));
        $data = $query->result_array();
        for ($i = 0; $i < sizeof($data); $i++) {
            $this->db->trans_start();
            $newId = $this->_generateFarmerID($data[$i]['district']);
            $this->db->query($sql_update, array($newId, $data[$i]['FarmerID']));
            $this->db->query($sql_copy, array($userId, $newId));
            $idf = $newId;
            $this->db->query($sql_update_bithdate, array($idf));
            //$this->db->query($sql_harvest, array($idf,$userid));
            //$this->db->query($sql_garden, array($idf,$userid));
            //$this->db->query($sql_ppi, array($idf,$userid));
            //$this->db->query($sql_ppi_2012, array($idf,$userid));
            //$this->db->query($sql_nutrition, array($idf,$userid));
            $this->db->trans_complete();

            array_push($farmers, $newId);

            if (!$this->db->trans_status()) {
                break;
            }
        }

        if ($this->db->trans_status()) {
            //$this->db->query("DELETE FROM ktv_farmer_temp WHERE CreatedBy=$userId");
        }

        if (count($farmers) > 0) {
            foreach ($farmers as $fid) {
                $this->updateFarmerdhis($fid);
            }
        }

        return $this->db->trans_status();
    }

    function updateFarmerdhis($FarmerID) {
        /**
         * Add Update data to DHIS using FarmerID
         * @author Ardi <ardiantoro@koltiva.com>
         */
        //Load model from dhis module
        $this->load->model('dhis/mdsync', '_dsync');

        //Get farmer data from view view_program_farmer
        $farmers = $this->_dsync->getDataByDistrict(false, true, 'QxauNvjcpBw', $FarmerID);

        //Found? sync the data to dhis
        if ($farmers) {
            $dhis = $this->_dsync->syncDataPerProgram($farmers, 'QxauNvjcpBw');
        }
        //End
    }

    function getDataFoto($id) {
        $sql = "
            SELECT Province,FarmerID
            FROM ktv_farmer
            LEFT JOIN ktv_village kv ON kv.VillageID = ktv_farmer.VillageID
            LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
            LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
            LEFT JOIN ktv_province kp ON kp.ProvinceID = kd.ProvinceID
            WHERE FarmerID=? OR OldFarmerID=?";
        $query = $this->db->query($sql, array(intval($id), intval($id)));
        $result = $query->result_array();
        if (@$result[0]['FarmerID'] != "") {
            $file = 'frm_' . $result[0]['FarmerID'] . '_' . date('YmdHis') . '.jpg';
            $sql_update = "UPDATE ktv_farmer SET Photo=concat(?,'/',?) where FarmerID=?";
            $this->db->query($sql_update, array($result[0]['Province'], $file, $result[0]['FarmerID']));
            $result[0]['File'] = $file;
        } else {
            $result[0]['File'] = '';
        }
        return $result[0];
    }

    public function getPertaniTraining($CpgBatchTrainingID, $offset, $limit) {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
  f.`FarmerID`,
  tf.CpgBatchTrainingsFarmerID,
  f.`FarmerName`,
  v.`Village` AS Village,
  IF(f.Gender = 1,'Male','Female') AS FarmerGender
FROM `ktv_cpg_batch_trainings_farmers` tf
JOIN `ktv_farmer` f ON f.`FarmerID` = tf.`FarmerID`
LEFT JOIN `ktv_village` v ON v.`VillageID` = f.`VillageID`
WHERE
  1 = 1
  AND tf.`CpgBatchTrainingID` = ?
ORDER BY FarmerName
LIMIT ?, ?
      ";
        $query = $this->db->query($sql, array($CpgBatchTrainingID, intval($offset), intval($limit)));
        $total = $this->db->query('SELECT FOUND_ROWS() AS total');
        $total = $total->row_array(0);
        if ($query->num_rows() > 0) {
            return array(
                'data' => $query->result_array(),
                'total' => $total['total'],
            );
        }
    }

    function getDataLearningContract($FarmerID) {
        $sql = "SELECT LearningContractFile FROM ktv_farmer WHERE FarmerID=?";
        $query = $this->db->query($sql, array($FarmerID));
        $result = $query->result_array();
        return $result[0];
    }

    function updateLearningContractFile($FarmerID, $LearningContractFile) {
        $sql_update = "UPDATE ktv_farmer SET LearningContractFile=? WHERE FarmerID=?";
        $this->db->query($sql_update, array($LearningContractFile, $FarmerID));
    }

    function readHistorys($FarmerID, $start, $limit) {
        $sql = "
            SELECT %s
            FROM  ktv_photo_history

            WHERE FarmerID=?
            ORDER BY PhotoID DESC %s";

        $query = $this->db->query(sprintf($sql, "*,IF(IsActive='1','background:red;','') AS IsActive", ' LIMIT ?,?'), array($FarmerID, (int) $start, (int) $limit));

        //echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; //exit;

        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array($FarmerID));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function updatePhotoFarmer($PhotoID, $FarmerID, $Photo) {
        $this->db->trans_start();
        $sql = "UPDATE ktv_farmer SET Photo=?, DateUpdated=NOW(), LastModifiedBy=? WHERE FarmerID=?";
        $query = $this->db->query($sql, array($Photo, $_SESSION['userid'], $FarmerID));
        $sql = "UPDATE ktv_photo_history SET IsActive='0' WHERE PhotoID!=? AND FarmerID=?";
        $query = $this->db->query($sql, array($PhotoID, $FarmerID));
        $sql = "UPDATE ktv_photo_history SET IsActive='1' WHERE PhotoID=?  AND FarmerID=?";
        $query = $this->db->query($sql, array($PhotoID, $FarmerID));
        $this->updateDataPetani($FarmerID, base_url() . 'images/Photo/' . $Photo, 'ada');
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            $results['success'] = true;
            $results['message'] = "record updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update record";
        }
        return $results;
    }

    function deletePhotoHistory($PhotoID, $FarmerID, $Photo) {
        $sql = "SELECT IsActive FROM ktv_photo_history WHERE PhotoID=? AND FarmerID=?";
        $query = $this->db->query($sql, array($PhotoID, $FarmerID));
        $IsActive = $query->row()->IsActive;
        if ($IsActive == '1') {
            $results['success'] = false;
            $results['message'] = "Failed to delete photo";
        } else {
            $sql = "DELETE FROM ktv_photo_history WHERE Photo=? AND FarmerID=? AND PhotoID=?";
            $query = $this->db->query($sql, array($Photo, $FarmerID, $PhotoID));
            if ($query) {
                if (file_exists(APPPATH . '/images/Photo/' . $Photo)) {
                    unlink(APPPATH . '/images/Photo/' . $Photo);
                }
                $results['success'] = true;
                $results['message'] = "record updated.";
            } else {
                $results['success'] = false;
                $results['message'] = "Failed to update record";
            }
        }

        return $results;
    }

    function createPhotoHistory($FarmerID, $Photo, $IsActive = '0') {
        $sql_check = "SELECT FarmerID FROM ktv_farmer WHERE FarmerID=?";
        $query_check = $this->db->query($sql_check, array($FarmerID));
        if ($query_check->num_rows() > 0) {
            if ($IsActive == '1') {
                $sql = "UPDATE ktv_photo_history SET IsActive='0' WHERE FarmerID=?";
                $query = $this->db->query($sql, array($FarmerID));
            }
            $sql = "INSERT INTO ktv_photo_history(FarmerID,Photo,IsActive,DateCreated,CreatedBy) VALUES(?,?,?,NOW(),?)";
            $query = $this->db->query($sql, array($FarmerID, $Photo, $IsActive, $_SESSION['userid']));
            if ($query) {
                $results['success'] = true;
                $results['message'] = "record created.";
            } else {
                $results['success'] = false;
                $results['message'] = "Failed to create record";
            }
            return $results;
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
            return $results;
        }
    }

    public function updateStatusPhotoPetani() {
        $this->db->trans_begin();

        $sql = "SELECT
                a.`FarmerID`
                , a.Photo
            FROM
                ktv_farmer a
            WHERE
                SUBSTR(a.`FarmerID`,1,4) IN (7401,7411,7311,7312)";
        $query = $this->db->query($sql);
        $dataListFarmer = $query->result_array();

        for ($i = 0; $i < count($dataListFarmer); $i++) {
            if ($dataListFarmer[$i]['Photo'] == "") {
                $dataListFarmer[$i]['Photo'] = "takadapathfoto.coy";
            }

            if (file_exists('images/Photo/' . $dataListFarmer[$i]['Photo'])) {
                $dataListFarmer[$i]['StatusPhoto'] = 'ada';
            } else {
                $dataListFarmer[$i]['StatusPhoto'] = 'tidak ada';
            }

            $sql = "UPDATE ktv_farmer a SET a.StatusPhoto = '" . $dataListFarmer[$i]['StatusPhoto'] . "' WHERE a.`FarmerID` = '" . $dataListFarmer[$i]['FarmerID'] . "' LIMIT 1";
            $query = $this->db->query($sql);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }
    public function GetMemberIDByMemberUidId($MemberUidId){
        $sql = "SELECT
                    a.`MemberID`
                    , prov.`ProvinceID`
                    , a.MemberDisplayID
                FROM
                    ktv_members a
                    LEFT JOIN ktv_village vil ON a.`VillageID` = vil.`VillageID`
                    LEFT JOIN ktv_subdistrict subd ON vil.`SubDistrictID` = subd.`SubDistrictID`
                    LEFT JOIN ktv_district dis ON subd.`DistrictID` = dis.`DistrictID`
                    LEFT JOIN ktv_province prov ON dis.`ProvinceID` = prov.`ProvinceID`
                WHERE
                    a.`MemberUID` = ?
                LIMIT 1";
        return $this->db->query($sql,array($MemberUidId))->row_array();
    }
}

?>
