<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function getSettings($OrgID) {

	$ci =& get_instance();
	$ci->load->database();

	$result = [];

	$ci->db->select("org.*
					,o.SupplychainID 
                    ,o.OrgType 
                    ,o.OrgID 
                    ,o.NAME
                    ,o.Address
                    ,dis.District as District
                    ,tt.TransactionType
                    ,bt.SeaweedType
                    ,dest.Destination
                    ,qu.Quality
                    ,pac.Package
                    ,ar.AccessDistrict");
	$ci->db->join("ktv_tc_neo_supplychain_org org", "o.SupplychainID = org.SupplychainID", "left");
	$ci->db->join("ktv_district dis", "dis.DistrictID = o.DistrictID", "left");
	$ci->db->join("(
		            SELECT SupplychainID, count(SupplychainID) TransactionType FROM ktv_tc_neo_supplychain_trans_type GROUP BY SupplychainID
	            ) tt", "tt.SupplychainID = o.SupplychainID", "left");
	$ci->db->join("(
		            SELECT SupplychainID, count(SupplychainID) SeaweedType from ktv_tc_neo_supplychain_seaweed_type GROUP BY SupplychainID
	            ) bt", "bt.SupplychainID=o.SupplychainID", "left");
	$ci->db->join("(
		            SELECT ChildOrgId, count(ChildOrgId) Destination from ktv_tc_neo_supplychain_org_rel group by ChildOrgId
	            ) dest", "dest.ChildOrgId=o.SupplychainID", "left");
	$ci->db->join("(
		            SELECT QualitySupplychainID, COUNT(QualitySupplychainID) Quality from ktv_tc_neo_supplychain_quality group by QualitySupplychainID
	            ) qu", "qu.QualitySupplychainID=o.SupplychainID", "left");
	$ci->db->join("(
                    SELECT PackageSupplychainID, COUNT(PackageSupplychainID) Package from ktv_tc_neo_supplychain_package group by PackageSupplychainID
                ) pac", "pac.PackageSupplychainID=o.SupplychainID", "left");
	$ci->db->join("(
                    SELECT SupplychainID, COUNT(SupplychainID) AccessDistrict from ktv_tc_neo_supplychain_area group by SupplychainID
                ) ar", "ar.SupplychainID=o.SupplychainID", "left");
	$ci->db->join("(
                    SELECT SupplychainID, COUNT(SupplychainID) Staff from view_tc_neo_supplychain_staff group by SupplychainID
                ) staf", "staf.SupplychainID=o.SupplychainID", "left");
	$ci->db->where("org.StatusCode", "active");
	$ci->db->where("o.OrgID", $OrgID);
	$ci->db->order_by("o.SupplychainID");

	$data = $ci->db->get("view_tc_neo_supplychain_org o")->row();

	if (!empty($data)) {
		$SupplychainID = $data->SupplychainID;

		$result['basicData'] = (object)[
			'SupplychainID'  => $data->SupplychainID,
	        'OrgType'		 => $data->OrgType,
	        'OrgID'			 => $data->OrgID,
	        'NAME'			 => $data->NAME,
	        'Address'		 => $data->Address,
	        'District'		 => $data->District,
	        'TransactionType'=> $data->TransactionType,
	        'SeaweedType'	 => $data->SeaweedType,
	        'Destination'	 => $data->Destination,
	        'Quality'		 => $data->Quality,
	        'Package'		 => $data->Package,
	        'AccessDistrict' => $data->AccessDistrict

		];

		$result['settingTransaction'] = (object)[
			'KodeArea'  			  => $data->KodeArea,
			'PembelianFarmer'  		  => $data->PembelianFarmer,
	        'PembelianNonFarmer'      => $data->PembelianNonFarmer,
	        'PembelianFarmerCert'     => $data->PembelianFarmerCert,
	        'PembelianBatch'	      => $data->PembelianBatch,
	        'IsAutoBatch'		      => $data->IsAutoBatch,
	        'LabelFarmerCertified'    => $data->LabelFarmerCertified,
	        'LabelFarmerNonCertified' => $data->LabelFarmerNonCertified,
	        'LabelNonFarmer'          => $data->LabelNonFarmer,
	        'SentEmail'               => $data->SentEmail,
	        'IsSampleReduction'		  => $data->IsSampleReduction,
	        'IsSingleLogin'           => $data->IsSingleLogin,
	        'UsePartnerID'	          => $data->UsePartnerID,
	        'UseFermentation'	      => $data->UseFermentation,
	        'AllowNewFarmer'		  => $data->AllowNewFarmer,
	        'DownloadFarmerTypeID'    => $data->DownloadFarmerTypeID,
	        'AllowMixGrade'		      => $data->AllowMixGrade,
	        'IsCheckSampleDate'       => $data->IsCheckSampleDate,
	        'IsSMS'   		          => $data->IsSMS,
	        "PartnerID"               => $data->PartnerID
		];

		// search transaction type on tab transaction seaweed type

		$ci->db->select("ks.SupplychainID
                	     ,ks.TransTypeID
                	     ,CONCAT(r.TransTypeName, ' - ', r.SupplyType) TransTypeName", FALSE);
		$ci->db->join("ref_tc_neo_trans_type r", "ks.TransTypeID = r.TransTypeID", "left");
		$ci->db->where("r.StatusCode", 'active');
		$ci->db->where("ks.SupplychainID", $SupplychainID);
		$transType = $ci->db->get("ktv_tc_neo_supplychain_trans_type ks")->result();

		// search seaweed type on tab transaction seaweed type

		$ci->db->select("ks.SupplychainID
		                ,ks.SeaweedTypeID
		                ,ks.IsPriceEditable
		                ,r.SeaweedTypeName 
		                ,IF(ks.IsPriceEditable=1,'Yes', 'No') as IsPrice", FALSE);
		$ci->db->join("ref_tc_neo_seaweed_type r", "ks.SeaweedTypeID=r.SeaweedTypeID", "left");
		$ci->db->where("r.StatusCode", 'active');
		$ci->db->where("ks.SupplychainID", $SupplychainID);
		$seaweedType = $ci->db->get("ktv_tc_neo_supplychain_seaweed_type ks")->result();

		$result['transactionSeaweedType'] = (object)[
			'transType'   => $transType,
			'seaweedType' => $seaweedType
		];

		// search delivery seaweed type on tab delivery seaweed type

		$ci->db->select("ks.SupplychainID
                    	,ks.SeaweedTypeID
                    	,r.SeaweedTypeName");
		$ci->db->join("ref_tc_neo_seaweed_type r", "ks.SeaweedTypeID=r.SeaweedTypeID", "left");
		$ci->db->where("r.StatusCode", 'active');
		$ci->db->where("ks.SupplychainID", $SupplychainID);
		$deliverySeaweedType = $ci->db->get("ktv_tc_neo_supplychain_delivery_seaweed_type ks")->result();

		$result['deliverySeaweedType'] = $deliverySeaweedType;

		// search destination on tab destination

		$ci->db->select("ks.RelId
	                    ,ks.ParentOrgId
	                    ,ks.ChildOrgId
	                    ,ks.SeaweedTypeID
	                    ,ks.StartDate
	                    ,ks.EndDate
	                    ,r.SeaweedTypeName
	                    ,ko.OrgType
	                    ,org.Name as Parent
	                    ,kpp.PartnerID
	                    ,kpp.PartnerName");
		$ci->db->join("ref_tc_neo_seaweed_type r", "ks.SeaweedTypeID=r.SeaweedTypeID", "left");
		$ci->db->join("view_tc_neo_supplychain_org org", "org.SupplychainID=ks.ParentOrgId", "left");
		$ci->db->join("ktv_tc_neo_supplychain_org ko", "ko.SupplychainID=ks.ParentOrgId", "left");
		$ci->db->join("ktv_program_partner kpp", "ko.PartnerID=kpp.PartnerID", "left");
		$ci->db->where("r.StatusCode", 'active');
		$ci->db->where("ks.StartDate <=", date('Y-m-d'));
        $ci->db->where("ks.EndDate >=", date('Y-m-d'));
		$ci->db->where("ks.ChildOrgId", $SupplychainID);
		$destination = $ci->db->get("ktv_tc_neo_supplychain_org_rel ks")->result();

		$result['destination'] = $destination;

		// search quality on tab quality

		$ci->db->select("q.QualityID
		                ,q.QualityDateStart
		                ,q.QualityDateEnd
		                ,q.QualitySupplychainID
		                ,qs.StandardID
		                ,qs.StandardName
		                ,qs.IsClaim
		                ,qs.IsReward
		                ,IF(IsClaim='1','Yes', 'No') AS Claim
		                ,IF(IsReward='1','Yes', 'No') AS Reward
		                ,qs.QualityType
		                ,qs.MobileQualityID
		                ,mq.MobileQualityName", FALSE);
		$ci->db->join("ktv_tc_neo_supplychain_quality_standard qs", "q.StandardID=qs.StandardID");
		// $ci->db->join("ref_tc_neo_quality rnq", "qs.StandardName = rnq.RefQualityID");
		$ci->db->join("ref_tc_neo_mobile_quality_type mq", "mq.MobileQualityID=qs.MobileQualityID", "left");
		$ci->db->where("q.QualityDateStart <=", date('Y-m-d'));
        $ci->db->where("q.QualityDateEnd >=", date('Y-m-d'));
		$ci->db->where("q.QualitySupplychainID", $SupplychainID);
		$quality = $ci->db->get("ktv_tc_neo_supplychain_quality q")->result();

		$result['quality'] = $quality;

		// search package on tab package

		$ci->db->select("PackageID
		                ,PackageSupplychainID
		                ,PackageType
		                ,PackageWeight
		                ,PackageCapasity
		                ,DefaultPackage
		                ,StatusCode
		                ,IsTransaction as IsTrans,IsBatch as IsBatc
		                ,IF(IsTransaction='1','Yes', 'No') AS IsTransaction
		                ,IF(IsBatch='1','Yes', 'No') AS IsBatch
		                ,IF(DefaultPackage='1','Yes', 'No') AS DefaultPack
		                ,PackageOrder", FALSE);
		$ci->db->where("StatusCode", 'active');
		$ci->db->where("PackageSupplychainID", $SupplychainID);
		$package = $ci->db->get("ktv_tc_neo_supplychain_package")->result();

		$result['package'] = $package;

		// search access district on tab access district

		$ci->db->select("a.SupplychainID
		                ,a.AreaID
		                ,a.DistrictID
		                ,p.ProvinceID
		                ,d.District as District
		                ,p.Province as Province
		                ,c.CountryName as Country");
		$ci->db->join("ktv_district d", "a.DistrictID=d.DistrictID", "left");
		$ci->db->join("ktv_province p", "d.ProvinceID=p.ProvinceID", "left");
		$ci->db->join("ktv_country c", "p.CountryCode = c.ISO2", "left");
		$ci->db->where("d.StatusCode", 'active');
		$ci->db->where("p.StatusCode", 'active');
		$ci->db->where("a.SupplychainID", $SupplychainID);
		$accessDistrict = $ci->db->get("ktv_tc_neo_supplychain_area a")->result();

		$result['accessDistrict'] = $accessDistrict;

		// search unit on tab unit

		$ci->db->select("ku.SupplychainID
		                ,ku.UnitID
		                ,ku.OrgUnitID
		                ,u.UnitName
		                ,IF(ku.IsDefault='1','Yes', 'No') AS IsDefault", FALSE);
		$ci->db->join("ref_tc_neo_unit u", "ku.UnitID=u.UnitID", "left");
		$ci->db->where("u.StatusCode", 'active');
		$ci->db->where("ku.SupplychainID", $SupplychainID);
		$unit = $ci->db->get("ktv_tc_neo_supplychain_org_unit ku")->result();

		$result['unit'] = $unit;

		// search currency on tab currency

		$ci->db->select("ku.SupplychainID
	                    ,c.CurrencyID
	                    ,ku.OrgCurrencyID
	                    ,c.CurrencyName
	                    ,IF(ku.IsDefault='1','Yes', 'No') AS IsDefault", FALSE);
		$ci->db->join("ref_tc_neo_currency c", "ku.CurrencyID=c.CurrencyID", "left");
		$ci->db->where("c.StatusCode", 'active');
		$ci->db->where("ku.SupplychainID", $SupplychainID);
		$currency = $ci->db->get("ktv_tc_neo_supplychain_org_currency ku")->result();

		$result['currency'] = $currency;
	}

	return $result;

}

function getTransType($SupplychainID, $DestinationID, $PartnerID) {
	$ci =& get_instance();
	$ci->load->database();

	$paramOne = $ci->db->where('SupplychainID', $SupplychainID)
					   ->where('PartnerID', $PartnerID)
					   ->get('view_tc_neo_supplychain_staff')
					   ->row()
					   ->OrgType;
    
    $paramTwo = $ci->db->where('SupplychainID', $DestinationID)
    				   ->where('PartnerID', $PartnerID)
					   ->get('view_tc_neo_supplychain_staff')
					   ->row()
					   ->OrgType;

	$getTransType = $ci->db->where('TransTypeName', $paramOne)
						   ->where('SupplyType', $paramTwo)
						   ->where('PartnerID', $PartnerID)
						   ->get('ref_tc_neo_trans_type')
						   ->row()
						   ->TransTypeID;
    
    return $getTransType;
}

function generateRandomUID($length = 11) {
    $characters 	  = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString 	  = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(1, $charactersLength - 1)];
    }

    return $randomString;
}