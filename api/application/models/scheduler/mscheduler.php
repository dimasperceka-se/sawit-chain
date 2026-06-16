<?php
        
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Aws\Common\Aws;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

class Mscheduler extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->truncate = "TRUNCATE TABLE %s";
        $this->traceability = "
			INSERT INTO rpt_traceability
			SELECT
				kcf.FarmerID farmer_id,CONCAT('[',kcf.FarmerID,'] ',kcf.FarmerName) farmer_name,kcf.VillageID,IF(kcc.FarmerID IS NULL,'0','1') farmer_iscertified,
				IF(PerwakilanOrgID=0,IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto,(100-(IF(IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture) IS NULL OR IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture)=0,7,IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture))-7))/100*(IF(b.FAQVolumeNetto>b.FFVolumeNetto,b.FAQVolumeNetto,b.FFVolumeNetto))/moisture_netto*wh_netto) farmer_netto,
				#IF(PerwakilanOrgID=0,IF(kspd1.DetailID IS NULL,'0','1'),IF(kspd2.DetailID IS NULL,'0','1')) farmer_ispaid,
				IF(fpay.id IS NOT NULL,'1','0') farmer_ispaid,
				IF(fpay.id IS NOT NULL,fpay.PaymentDate,NULL) farmer_ispaiddate,

				IF(PerwakilanOrgID=0,NULL,ksovb.SupplychainID) 2_supplychainid,IF(PerwakilanOrgID=0,NULL,'perwakilan') 2_orgtype,IF(PerwakilanOrgID=0,NULL,ksovb.OrgID) 2_orgid, IF(PerwakilanOrgID=0,NULL,ksovb.Name) 2_name, d.OrgType 2_destorgtype, d.OrgID 2_destorgid, aa.Name AS 2_destname,
				IF(PerwakilanOrgID=0,NULL,c.SupplyBatchNumber) 2_batchnumber,IF(PerwakilanOrgID=0,NULL,b.SupplyBatchID) 2_batchid,IF(PerwakilanOrgID=0,NULL,c.DestPO) 2_po,IF(PerwakilanOrgID=0,NULL,b.SupplyTransID) 2_transid,
				IF(PerwakilanOrgID=0,NULL,DATE(IFNULL(b.DateTransaction,c.SupplyBatchDate))) 2_date,
				IF(PerwakilanOrgID=0,NULL,IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)) 2_bruto,
				IF(PerwakilanOrgID=0,NULL,IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)) 2_netto,
				#IF(PerwakilanOrgID=0,NULL,IF(kspd3.DetailID IS NULL,'0','1')) 2_ispaid,
				IF(bspay.id IS NOT NULL,'1','0') 2_ispaid,
				IF(bspay.id IS NOT NULL,bspay.PaymentDate,NULL) 2_ispaiddate,
				c.SupplyDestStatus 2_status,

				c.SupplyOrgID 1_supplychainid,d.OrgType 1_orgtype,d.OrgID 1_orgid, ksov.Name 1_name, 'Gudang' 1_destorgtype, wh_orgid 1_destorgid, wh_name 1_destorgname,
				c.SupplyBatchNumber 1_batchnumber,b.SupplyBatchID 1_batchid,c.DestPO 1_po,b.SupplyTransID 1_transid,
				DATE(IFNULL(b.DateTransaction,c.SupplyBatchDate)) 1_date,
				IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) 1_bruto,
				IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) 1_netto,
				#IF(kspd1.DetailID IS NULL,'0','1') 1_ispaid,
				IF(cooppay.id IS NOT NULL,'1','0') 1_ispaid,
				IF(cooppay.id IS NOT NULL,cooppay.PaymentDate,NULL) 1_ispaiddate,
				'Delivered' AS 1_status,

				wh_supplychainid,wh_orgid,wh_name,wh_batchid,wh_po,wh_transid,wh_date,
				IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*wh_bruto wh_bruto,
				IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto wh_netto,
				wh_dollar,wh_kurs,wh_persenfarmer,wh_persenbs,wh_persenkoperasi, a.wh_status, CONCAT('|',wh_orgid,'|') wh_dest, a.IsQuota, '1' AS IsPremium
			FROM
				(
					SELECT
						b.SupplyType wh_supplytype,b.SupplyID wh_supplyid,b.IsQuota, a.SupplyDestStatus wh_status,

						a.SupplyOrgID wh_supplychainid,c.OrgID wh_orgid,kw.WarehouseName wh_name,a.SupplyBatchID wh_batchid,a.DestPO wh_po,
						b.SupplyTransID wh_transid,DATE(b.DateTransaction) wh_date,
						IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) wh_bruto,
						IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) wh_netto,
						USD wh_dollar,IFNULL(KursNominal,Kurs) wh_kurs,PersenPetani wh_persenfarmer,PersenPerwakilan wh_persenbs,
						PersenBuyinUnit wh_persenkoperasi
					FROM
						ktv_supplychain_batch a
						LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
						LEFT JOIN ktv_supplychain_org c ON a.SupplyOrgID=c.SupplychainID
						LEFT JOIN ktv_warehouse kw ON kw.WarehouseID=c.OrgID
						LEFT JOIN ktv_supplychain_premium d ON d.PremiumSupplychainID=c.SupplychainID AND (DATE(b.DateTransaction) BETWEEN PremiumDateStart AND PremiumDateEnd)
						LEFT JOIN ktv_supplychain_kurs e ON e.KursSupplychainID=c.SupplychainID AND (DATE(b.DateTransaction) BETWEEN KursDateStart AND KursDateEnd)
					WHERE c.OrgType='warehouse'
				) a
				LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.wh_supplyid AND a.wh_supplytype='Batch'
				LEFT JOIN ktv_supplychain_org_view ksov ON ksov.SupplychainID=c.SupplyOrgID
				LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
				#LEFT JOIN ktv_supplychain_transaction_dtl bb ON b.SupplyTransID=bb.SupplyTransID
				LEFT JOIN (
					SELECT
						DISTINCT(a.SupplyTransID),
						IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ') Type,
						IF(IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ')='FAQ',a.FAQResult,a.FFResult) Moisture
					FROM
						ktv_supplychain_transaction_quality a
						LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.DetailID=b.DetailID AND a.StandardID=b.StandardID
						LEFT JOIN ktv_supplychain_transaction_dtl c ON a.SupplyTransID=c.SupplyTransID
					WHERE b.`Name` LIKE '%Moisture%'
				) tipe ON tipe.SupplyTransID=b.SupplyTransID
				LEFT JOIN (
					SELECT DISTINCT(SupplyTransID),Type,Moisture FROM ktv_supplychain_transaction_dtl
				) tipe2 ON tipe2.SupplyTransID=b.SupplyTransID
				LEFT JOIN ktv_supplychain_org d ON c.SupplyOrgID=d.SupplychainID
				LEFT JOIN ktv_farmer kcf ON (b.SupplyID = kcf.FarmerID AND (b.SupplyType='Farmer' OR b.SupplyType='FarmerNonCert'))
				#payment
				/*LEFT JOIN (
				   SELECT DetailID,PaymentSupplychainID,PaymentDestID,DetailSupplyTransID
				   FROM ktv_supplychain_payment kspb
				   LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
				   WHERE (PaymentDestType='Organisasi Petani' OR PaymentDestType='Farmer')
				) kspd1 ON kspd1.PaymentSupplychainID=IF(PerwakilanOrgID=0,c.SupplyOrgID,wh_supplychainid) AND kspd1.PaymentDestID=IF(PerwakilanOrgID=0,kcf.FarmerID,c.SupplyOrgID) AND
				   kspd1.DetailSupplyTransID=IF(PerwakilanOrgID=0,b.SupplyTransID,wh_transid)*/
				#end payment
				LEFT JOIN (
					SELECT SUM((100-(IFNULL(b.Moisture,7)-7))/100*(IF(b.Type='FAQ',a.FAQVolumeNetto,a.FFVolumeNetto))) moisture_netto,
						SUM(IF(a.FAQVolumeNetto>0,a.FAQVolumeNetto,a.FFVolumeNetto)) total_netto,
						SUM(IF(a.FAQVolumeBruto>0,a.FAQVolumeBruto,a.FFVolumeBruto)) total_bruto,
						SupplyBatchID
					FROM ktv_supplychain_transaction a
					LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
					GROUP BY SupplyBatchID
				) z ON z.SupplyBatchID=c.SupplyBatchID

				LEFT JOIN ktv_supplychain_org_view ksovb ON ksovb.OrgID=c.PerwakilanOrgID AND ksovb.OrgType='Pedagang'

				#payment
				/*LEFT JOIN (
				   SELECT DetailID,PaymentSupplychainID,PaymentDestID,DetailSupplyTransID
				   FROM ktv_supplychain_payment kspb
				   LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
				   WHERE (PaymentDestType='Farmer')
				) kspd2 ON kspd2.PaymentSupplychainID=c.SupplyOrgID AND kspd2.PaymentDestID=SupplyID AND
				   kspd2.DetailSupplyTransID=b.SupplyTransID
				#end payment
				#payment
				LEFT JOIN (
				   SELECT DetailID,PaymentSupplychainID,PaymentDestID,DetailSupplyTransID
				   FROM ktv_supplychain_payment kspb
				   LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
				   WHERE (PaymentDestType='Pedagang' OR PaymentDestType='sce')
				) kspd3 ON kspd3.PaymentSupplychainID=c.SupplyOrgID AND kspd3.PaymentDestID=ksovb.SupplychainID AND
				   kspd3.DetailSupplyTransID=wh_transid*/
				#end payment
				LEFT JOIN (SELECT * FROM ktv_certification GROUP BY FarmerID,CertificationStart) kcc ON kcf.FarmerID=kcc.FarmerID AND (wh_date BETWEEN CertificationStart AND CertificationEnd)
				LEFT JOIN ktv_supplychain_org_view aa ON aa.SupplychainID=d.OrgID
				#Payment
				LEFT JOIN (
					SELECT b.DetailSupplyTransID AS id, a.PaymentDestType, a.PaymentDate FROM ktv_supplychain_payment a LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID=b.DetailPaymentID
					WHERE a.PaymentDestType='Farmer'
				) fpay ON fpay.id=b.SupplyTransID
				LEFT JOIN (
					SELECT
						DISTINCT(IF(c.SupplyType='Farmer',c.SupplyTransID,IF(e.SupplyType='Farmer',e.SupplyTransID,g.SupplyTransID))) AS id, a.PaymentDestType, a.PaymentDate
					FROM
						ktv_supplychain_payment a
						LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID = b.DetailPaymentID
						LEFT JOIN ktv_supplychain_transaction c ON c.SupplyTransID=b.DetailSupplyTransID
						LEFT JOIN ktv_supplychain_batch d ON d.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
						LEFT JOIN ktv_supplychain_transaction e ON e.SupplyBatchID=d.SupplyBatchID
						LEFT JOIN ktv_supplychain_batch f ON f.SupplyBatchNumber=e.SupplyID  AND e.SupplyType='Batch'
						LEFT JOIN ktv_supplychain_transaction g ON g.SupplyBatchID=f.SupplyBatchID
					WHERE a.PaymentDestType = 'Pedagang' OR a.PaymentDestType='sce'
				) bspay ON bspay.id=b.SupplyTransID
				LEFT JOIN (
					SELECT
						DISTINCT(IF(c.SupplyType='Farmer',c.SupplyTransID,IF(e.SupplyType='Farmer',e.SupplyTransID,g.SupplyTransID))) AS id, a.PaymentDestType, a.PaymentDate
					FROM
						ktv_supplychain_payment a
						LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID = b.DetailPaymentID
						LEFT JOIN ktv_supplychain_transaction c ON c.SupplyTransID=b.DetailSupplyTransID
						LEFT JOIN ktv_supplychain_batch d ON d.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
						LEFT JOIN ktv_supplychain_transaction e ON e.SupplyBatchID=d.SupplyBatchID
						LEFT JOIN ktv_supplychain_batch f ON f.SupplyBatchNumber=e.SupplyID  AND e.SupplyType='Batch'
						LEFT JOIN ktv_supplychain_transaction g ON g.SupplyBatchID=f.SupplyBatchID
					WHERE a.PaymentDestType = 'Organisasi Petani'
				) cooppay ON cooppay.id=b.SupplyTransID
			WHERE
				c.PerwakilanOrgID IS NOT NULL";
        $this->traceability3 = "
			INSERT INTO rpt_traceability

			SELECT
				kcf.FarmerID farmer_id,CONCAT('[',kcf.FarmerID,'] ',kcf.FarmerName) farmer_name,kcf.VillageID,IF(kcc.FarmerID IS NULL,'0','1') farmer_iscertified,
				IFNULL(farmer_netto,(100-(IF(IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture) IS NULL OR IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture)=0,7,IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture))-7))/100*(IF(b.FAQVolumeNetto>b.FFVolumeNetto,b.FAQVolumeNetto,b.FFVolumeNetto))/moisture_netto*wh_netto) farmer_netto,
				#IF(kspd2.DetailID IS NULL,'0','1') farmer_ispaid,
				IF(fpay.id IS NOT NULL,'1','0') farmer_ispaid,
				IF(fpay.id IS NOT NULL,fpay.PaymentDate,NULL) farmer_ispaiddate,

				c.SupplyOrgID 2_supplychainid,IF(c.SupplyOrgID IS NOT NULL,ksov.OrgType,NULL) 2_orgtype,
				ksov.OrgID 2_orgid, ksov.Name 2_name, IF(c.SupplyOrgID IS NULL, NULL, 1_orgtype) 2_destorgtype, IF(c.SupplyOrgID IS NULL, NULL, 1_orgid) 2_destorgid, IF(c.SupplyOrgID IS NULL, NULL, 1_name) 2_destname, c.SupplyBatchNumber 2_batchnumber,
				c.SupplyBatchID 2_batchid,c.DestPO 2_po,b.SupplyTransID 2_transid,
				DATE(IFNULL(b.DateTransaction,c.SupplyBatchDate)) 2_date,
				IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) 2_bruto,
				IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) 2_netto,
				#IF(c.SupplyOrgID IS NOT NULL,IF(kspd3.DetailID IS NULL,'0','1'),NULL) 2_ispaid,
				IF(c.SupplyOrgID IS NULL, NULL, IF(bspay.id IS NOT NULL,'1','0')) 2_ispaid,
				IF(bspay.id IS NOT NULL,bspay.PaymentDate,NULL) 2_ispaiddate,
				IF(c.SupplyOrgID IS NULL, NULL, 'Delivered') AS 2_status,

				1_supplychainid,1_orgtype,1_orgid, 1_name,  'Gudang' AS 1_destorgtype,wh_orgid 1_destorgid, wh_name 1_destname, 1_batchnumber,1_batchid,1_po,1_transid,1_date,
				IFNULL(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*1_bruto,1_bruto) 1_bruto,
				IFNULL(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*1_netto,1_netto) 1_netto,
				#1_ispaid,
				IF(cooppay.id IS NOT NULL,'1','0') 1_ispaid,
				IF(cooppay.id IS NOT NULL,cooppay.PaymentDate,NULL) 1_ispaiddate,
				'Delivered' AS 1_status,

				wh_supplychainid,wh_orgid,wh_name,wh_batchid,wh_po,wh_transid,wh_date,
				IFNULL(IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*wh_bruto,wh_bruto) wh_bruto,
				IFNULL(IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto,wh_netto)  wh_netto,
				wh_dollar,wh_kurs,wh_persenfarmer,wh_persenbs,wh_persenkoperasi,wh_status, CONCAT('|',wh_orgid,'|') wh_dest,a.IsQuota, '1' AS IsPremium
			FROM (
					SELECT
						SupplyType 1_supplytype,SupplyID 1_supplyid,
						IF(SupplyType='Farmer' OR SupplyType='FarmerNonCert',SupplyID,NULL) farmer_id,
						IF(SupplyType='Farmer' OR SupplyType='FarmerNonCert',(100-(IF(IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture) IS NULL OR IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture)=0,7,IF(tipe.SupplyTransID IS NULL,tipe2.Moisture,tipe.Moisture))-7))/100*(IF(b.FAQVolumeNetto>b.FFVolumeNetto,b.FAQVolumeNetto,b.FFVolumeNetto))/moisture_netto*wh_netto,NULL) farmer_netto,
						c.SupplyOrgID 1_supplychainid,IF(d.OrgType='trader','Pedagang',d.OrgType) 1_orgtype,d.OrgID 1_orgid,ksov.Name 1_name,
						c.SupplyBatchNumber 1_batchnumber,b.SupplyBatchID 1_batchid,c.DestPO 1_po,b.SupplyTransID 1_transid,
						DATE(IFNULL(b.DateTransaction,c.SupplyBatchDate)) 1_date,
						IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) 1_bruto,
						IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) 1_netto,
						#IF(kspd1.DetailID IS NULL,'0','1') 1_ispaid,
						wh_supplychainid,wh_orgid,wh_name,wh_batchid,wh_po,wh_transid,wh_date,
						IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto)/total_bruto*wh_bruto wh_bruto,
						IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto)/total_netto*wh_netto wh_netto,
						wh_dollar,wh_kurs,wh_persenfarmer,wh_persenbs,wh_persenkoperasi,a.IsQuota, wh_status
					FROM (
						  	SELECT
								b.SupplyType wh_supplytype,b.SupplyID wh_supplyid,b.IsQuota,

								a.SupplyOrgID wh_supplychainid,c.OrgID wh_orgid,kw.WarehouseName wh_name,a.SupplyBatchID wh_batchid,a.DestPO wh_po,
								b.SupplyTransID wh_transid,DATE(b.DateTransaction) wh_date,
								IF(b.FAQVolumeBruto>0,b.FAQVolumeBruto,b.FFVolumeBruto) wh_bruto,
								IF(b.FAQVolumeNetto>0,b.FAQVolumeNetto,b.FFVolumeNetto) wh_netto,
								USD wh_dollar,IFNULL(KursNominal,Kurs) wh_kurs,PersenPetani wh_persenfarmer,PersenPerwakilan wh_persenbs,
								PersenBuyinUnit wh_persenkoperasi, a.SupplyDestStatus wh_status
						  	FROM ktv_supplychain_batch a
								LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchID=b.SupplyBatchID
								LEFT JOIN ktv_supplychain_org c ON a.SupplyOrgID=c.SupplychainID
								LEFT JOIN ktv_warehouse kw ON kw.WarehouseID=c.OrgID
								LEFT JOIN ktv_supplychain_premium d ON d.PremiumSupplychainID=c.SupplychainID AND (DATE(b.DateTransaction) BETWEEN PremiumDateStart AND PremiumDateEnd)
						  		LEFT JOIN ktv_supplychain_kurs e ON e.KursSupplychainID=c.SupplychainID AND (DATE(b.DateTransaction) BETWEEN KursDateStart AND KursDateEnd)
						  	WHERE c.OrgType='warehouse'
						) a
						LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.wh_supplyid AND a.wh_supplytype='Batch'
						LEFT JOIN ktv_supplychain_org_view ksov ON ksov.SupplychainID=c.SupplyOrgID
						LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
						#LEFT JOIN ktv_supplychain_transaction_dtl bb ON b.SupplyTransID=bb.SupplyTransID
						LEFT JOIN (
							SELECT
								DISTINCT(a.SupplyTransID),
								IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ') Type,
								IF(IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ')='FAQ',a.FAQResult,a.FFResult) Moisture
							FROM
								ktv_supplychain_transaction_quality a
								LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.DetailID=b.DetailID AND a.StandardID=b.StandardID
								LEFT JOIN ktv_supplychain_transaction_dtl c ON a.SupplyTransID=c.SupplyTransID
							WHERE b.`Name` LIKE '%Moisture%'
						) tipe ON tipe.SupplyTransID=b.SupplyTransID
						LEFT JOIN (
							SELECT DISTINCT(SupplyTransID),Type,Moisture FROM ktv_supplychain_transaction_dtl
						) tipe2 ON tipe2.SupplyTransID=b.SupplyTransID
						LEFT JOIN ktv_supplychain_org d ON c.SupplyOrgID=d.SupplychainID
					    /*#payment
					    LEFT JOIN (
					       SELECT DetailID,PaymentSupplychainID,PaymentDestID,DetailSupplyTransID
					       FROM ktv_supplychain_payment kspb
					       LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
					       WHERE (PaymentDestType='Organisasi Petani')
					    ) kspd1 ON kspd1.PaymentSupplychainID=wh_supplychainid AND kspd1.PaymentDestID=c.SupplyOrgID AND
					       kspd1.DetailSupplyTransID=wh_transid
					    #end payment*/
						LEFT JOIN (
							SELECT SUM((100-(IFNULL(b.Moisture,7)-7))/100*(IF(b.Type='FAQ',a.FAQVolumeNetto,a.FFVolumeNetto))) moisture_netto,
								SUM(IF(a.FAQVolumeNetto>0,a.FAQVolumeNetto,a.FFVolumeNetto)) total_netto,
								SUM(IF(a.FAQVolumeBruto>0,a.FAQVolumeBruto,a.FFVolumeBruto)) total_bruto,
								SupplyBatchID
							FROM ktv_supplychain_transaction a
							LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
							GROUP BY SupplyBatchID
						) z ON z.SupplyBatchID=c.SupplyBatchID
					WHERE c.PerwakilanOrgID IS NULL

				) a
				LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchNumber=a.1_supplyid AND a.1_supplytype='Batch'
				LEFT JOIN ktv_supplychain_transaction b ON b.SupplyBatchID=c.SupplyBatchID
				#LEFT JOIN ktv_supplychain_transaction_dtl bb ON b.SupplyTransID=bb.SupplyTransID
				LEFT JOIN (
					SELECT
						DISTINCT(a.SupplyTransID),
						IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ') Type,
						IF(IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ')='FAQ',a.FAQResult,a.FFResult) Moisture
					FROM
						ktv_supplychain_transaction_quality a
						LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.DetailID=b.DetailID AND a.StandardID=b.StandardID
						LEFT JOIN ktv_supplychain_transaction_dtl c ON a.SupplyTransID=c.SupplyTransID
					WHERE b.`Name` LIKE '%Moisture%'
				) tipe ON tipe.SupplyTransID=b.SupplyTransID
				LEFT JOIN (
					SELECT DISTINCT(SupplyTransID),Type,Moisture FROM ktv_supplychain_transaction_dtl
				) tipe2 ON tipe2.SupplyTransID=b.SupplyTransID
				LEFT JOIN ktv_supplychain_org d ON d.SupplychainID = c.SupplyOrgID

				LEFT JOIN ktv_supplychain_org_view ksov ON ksov.SupplychainID=c.SupplyOrgID
				LEFT JOIN ktv_farmer kcf ON IFNULL(farmer_id,b.SupplyID)=kcf.FarmerID
				LEFT JOIN (SELECT * FROM ktv_certification GROUP BY FarmerID,CertificationStart) kcc ON kcf.FarmerID=kcc.FarmerID AND (wh_date BETWEEN CertificationStart AND CertificationEnd)
				/*#payment
				LEFT JOIN (
							SELECT DetailID,PaymentSupplychainID,PaymentDestID,DetailSupplyTransID
							FROM ktv_supplychain_payment kspb
								LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
							WHERE (PaymentDestType='Farmer')
				) kspd2 ON kspd2.PaymentSupplychainID=1_supplychainid AND kspd2.PaymentDestID=kcf.FarmerID AND kspd2.DetailSupplyTransID=IFNULL(b.SupplyTransID,1_transid)
				#end payment
				#payment
				LEFT JOIN (
							SELECT DetailID,PaymentSupplychainID,PaymentDestID,DetailSupplyTransID
							FROM ktv_supplychain_payment kspb
								LEFT JOIN ktv_supplychain_payment_detail kspbd ON kspbd.DetailPaymentID=kspb.PaymentID
							WHERE (PaymentDestType='Pedagang' OR PaymentDestType='sce')
				) kspd3 ON kspd3.PaymentSupplychainID=1_supplychainid AND kspd3.PaymentDestID=ksov.SupplychainID AND kspd3.DetailSupplyTransID=b.SupplyTransID
				#end payment*/
				#PAYMENT
				#Payment
				LEFT JOIN (
					SELECT b.DetailSupplyTransID AS id, a.PaymentDestType, a.PaymentDate FROM ktv_supplychain_payment a LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID=b.DetailPaymentID
					WHERE a.PaymentDestType='Farmer'
				) fpay ON fpay.id=IF(IF(c.SupplyOrgID IS NOT NULL,ksov.OrgType,NULL)='Pedagang' OR IF(c.SupplyOrgID IS NOT NULL,ksov.OrgType,NULL)='sce',b.SupplyTransID,IF(1_orgtype='trader',1_transid,NULL))
				LEFT JOIN (
					SELECT
						DISTINCT(IF(c.SupplyType='Farmer',c.SupplyTransID,IF(e.SupplyType='Farmer',e.SupplyTransID,g.SupplyTransID))) AS id, a.PaymentDestType, a.PaymentDate
					FROM
						ktv_supplychain_payment a
						LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID = b.DetailPaymentID
						LEFT JOIN ktv_supplychain_transaction c ON c.SupplyTransID=b.DetailSupplyTransID
						LEFT JOIN ktv_supplychain_batch d ON d.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
						LEFT JOIN ktv_supplychain_transaction e ON e.SupplyBatchID=d.SupplyBatchID
						LEFT JOIN ktv_supplychain_batch f ON f.SupplyBatchNumber=e.SupplyID  AND e.SupplyType='Batch'
						LEFT JOIN ktv_supplychain_transaction g ON g.SupplyBatchID=f.SupplyBatchID
					WHERE a.PaymentDestType = 'Pedagang' OR a.PaymentDestType='sce'
				) bspay ON bspay.id=IF(IF(c.SupplyOrgID IS NOT NULL,ksov.OrgType,NULL)='Pedagang' OR IF(c.SupplyOrgID IS NOT NULL,ksov.OrgType,NULL)='sce',b.SupplyTransID,IF(1_orgtype='trader',1_transid,NULL))
				LEFT JOIN (
					SELECT
						DISTINCT(IF(c.SupplyType='Farmer',c.SupplyTransID,IF(e.SupplyType='Farmer',e.SupplyTransID,g.SupplyTransID))) AS id, a.PaymentDestType, a.PaymentDate
					FROM
						ktv_supplychain_payment a
						LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID = b.DetailPaymentID
						LEFT JOIN ktv_supplychain_transaction c ON c.SupplyTransID=b.DetailSupplyTransID
						LEFT JOIN ktv_supplychain_batch d ON d.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
						LEFT JOIN ktv_supplychain_transaction e ON e.SupplyBatchID=d.SupplyBatchID
						LEFT JOIN ktv_supplychain_batch f ON f.SupplyBatchNumber=e.SupplyID  AND e.SupplyType='Batch'
						LEFT JOIN ktv_supplychain_transaction g ON g.SupplyBatchID=f.SupplyBatchID
					WHERE a.PaymentDestType = 'Organisasi Petani'
				) cooppay ON cooppay.id=IF(IF(c.SupplyOrgID IS NOT NULL,ksov.OrgType,NULL)='Pedagang' OR IF(c.SupplyOrgID IS NOT NULL,ksov.OrgType,NULL)='sce',b.SupplyTransID,IF(1_orgtype='trader',1_transid,NULL))
				#END PAYMENT
				LEFT JOIN (
					SELECT SUM((100-(IFNULL(b.Moisture,7)-7))/100*(IF(b.Type='FAQ',a.FAQVolumeNetto,a.FFVolumeNetto))) moisture_netto,
					SUM(IF(a.FAQVolumeNetto>0,a.FAQVolumeNetto,a.FFVolumeNetto)) total_netto,
					SUM(IF(a.FAQVolumeBruto>0,a.FAQVolumeBruto,a.FFVolumeBruto)) total_bruto,
					SupplyBatchID
					FROM ktv_supplychain_transaction a
					LEFT JOIN ktv_supplychain_transaction_dtl b ON a.SupplyTransID=b.SupplyTransID
					GROUP BY SupplyBatchID
				) ab ON c.SupplyBatchID=ab.SupplyBatchID";

        $this->traceability4 = "
			INSERT INTO rpt_traceability

			SELECT
				farmer_id, farmer_name, Farmer_villageid, farmer_iscertified, farmer_netto, '0' farmer_ispaid, NULL farmer_ispaiddate,
				IF(1_bs='1',1_supplychainid,NULL) 1_supplychainid,
				IF(1_bs='1',1_orgtype,NULL) 1_orgtype,
				IF(1_bs='1',1_orgid,NULL) 1_orgid,
				IF(1_bs='1',1_name,NULL) 1_name,
				IF(1_bs='1',dd.OrgType,NULL) 1_destorgtype,
				IF(1_bs='1',dd.OrgID,NULL) 1_destorgid,
				IF(1_bs='1',dd.`Name`,NULL) 1_destname,
				IF(1_bs='1',bb.SupplyBatchNumber,NULL) 1_batchnumber,
				IF(1_bs='1',master.SupplyBatchID,NULL) 1_batchid,
				IF(1_bs='1',bb.DestPO,NULL) 1_po,
				IF(1_bs='1',1_transid,NULL) 1_transid,
				IF(1_bs='1',1_date,NULL) 1_date,
				IF(1_bs='1',1_bruto,NULL) 1_bruto,
				IF(1_bs='1',1_netto,NULL) 1_netto,
				'0' 1_ispaid,
				NULL 1_ispaiddate,
				IF(1_bs='1',bb.SupplyDestStatus,NULL) 1_status,
				#####
				IF(1_cooponly='1',1_supplychainid,IF(1_coop='1',bb.SupplyDestOrgID,NULL)) 2_supplychainid,
				IF(1_cooponly='1',1_orgtype,IF(1_coop='1',dd.OrgType,NULL)) 2_orgtype,
				IF(1_cooponly='1',1_orgid,IF(1_coop='1',dd.OrgID,NULL)) 2_orgid,
				IF(1_cooponly='1',1_name,IF(1_coop='1',dd.`Name`,NULL)) 2_name,
				NULL 2_destorgtype,
				NULL 2_destorgid,
				NULL 2_destname,
				IF(1_cooponly='1',bb.SupplyBatchNumber,IF(1_coop='1',ee.SupplyBatchNumber,NULL)) 2_batchnumber,
				IF(1_cooponly='1',master.SupplyBatchID,IF(1_coop='1',ee.SupplyBatchID,NULL)) 2_batchid,
				IF(1_cooponly='1',bb.DestPO,IF(1_coop='1',ee.DestPO,NULL)) 2_po,
				IF(1_cooponly='1',1_transid,IF(1_coop='1',cc.SupplyTransID,NULL)) 2_transid,
				IF(1_cooponly='1',1_date,IF(1_coop='1', SUBSTR(cc.DateTransaction,1,10),NULL)) 2_date,
				IF(1_cooponly='1',1_bruto,IF(1_coop='1',1_bruto,NULL)) 2_bruto,
				IF(1_cooponly='1',1_netto,IF(1_coop='1',1_netto,NULL)) 2_netto,
				IF(1_cooponly='1',0,IF(1_coop='1',0,NULL)) 2_netto,
				NULL 2_paiddate,
				IF(1_cooponly='1',bb.SupplyDestStatus,IF(1_coop='1',ee.SupplyDestStatus,NULL)) 2_status,
				NULL wh_supplychainid,
				NULL wh_orgid,
				NULL wh_name,
				NULL wh_batchid,
				NULL wh_po,
				NULL wh_transid,
				NULL wh_date,
				NULL wh_bruto,
				NULL wh_netto,
				NULL wh_dollar,
				NULL wh_kurs,
				NULL wh_persenfarmer,
				NULL wh_persenbs,
				NULL wh_persenkoperasi,
				NULL wh_status,
				wh_dest,
				'0' IsQuota,
				'0' IsPremium

			FROM
				( SELECT
					#A.SupplyBatchID, A.SupplyDestOrgID, A.SupplyDestStatus,
					#IF(f.SupplyType='Farmer',f.SupplyTransID,IF(h.SupplyType='Farmer',h.SupplyTransID,j.SupplyTransID)) AS farmer_id,
					#k.SupplyTransID, v.OrgType, v.OrgID, v3.OrgType, v3.OrgID, v4.OrgType, v4.OrgID, v5.OrgType, v5.OrgID,
					#a.*,
					#a.SupplyBatchID, v.OrgType, c.SupplyTransID, c.SupplyType, c.SupplyID, d.SupplyBatchID, e.SupplyTransID,
					#f.SupplyTransID, f.SupplyType, f.SupplyID, h.SupplyTransID, h.SupplyType, j.SupplyID, j.SupplyType
					k.SupplyBatchID, k.SupplyTransID 1_transid, SUBSTR(k.DateTransaction,1,10) 1_date,
					k.SupplyID AS farmer_id, CONCAT('[',k.SupplyID,'] ',l.FarmerName) AS farmer_name, l.VillageID AS Farmer_villageid, l.isCertified AS farmer_iscertified, IFNULL(k.FAQVolumeNetto,k.FFVolumeNetto) farmer_netto,
					IF(v5.OrgType='Pedagang' OR v5.OrgType='sce',v5.SupplychainID,IF(v4.OrgType='Pedagang' OR v4.OrgType='sce',v4.SupplychainID,IF(v3.OrgType='Pedagang' OR v3.OrgType='sce',v3.SupplychainID,IF(v.OrgType='Pedagang' OR v.OrgType='sce',v.SupplychainID,NULL)))) AS 1_supplychainid,
					IF(v5.OrgType='Pedagang' OR v5.OrgType='sce',v5.OrgType,IF(v4.OrgType='Pedagang' OR v4.OrgType='sce',v4.OrgType,IF(v3.OrgType='Pedagang' OR v3.OrgType='sce',v3.OrgType,IF(v.OrgType='Pedagang' OR v.OrgType='sce',v.OrgType,NULL)))) AS 1_orgtype,
					IF(v5.OrgType='Pedagang' OR v5.OrgType='sce',v5.OrgID,IF(v4.OrgType='Pedagang' OR v4.OrgType='sce',v4.OrgID,IF(v3.OrgType='Pedagang' OR v3.OrgType='sce',v3.OrgID,IF(v.OrgType='Pedagang' OR v.OrgType='sce',v.OrgID,NULL)))) AS 1_orgid,
					IF(v5.OrgType='Pedagang' OR v5.OrgType='sce',v5.`Name`,IF(v4.OrgType='Pedagang' OR v4.OrgType='sce',v4.`Name`,IF(v3.OrgType='Pedagang' OR v3.OrgType='sce',v3.`Name`,IF(v.OrgType='Pedagang' OR v.OrgType='sce',v.`Name`,NULL)))) AS 1_name,
					IFNULL(k.FAQVolumeBruto,k.FFVolumeBruto) 1_bruto, IFNULL(k.FAQVolumeNetto,k.FFVolumeNetto) 1_netto,
					IF(v5.OrgType='Pedagang' OR v5.OrgType='sce','1',IF(v4.OrgType='Pedagang' OR v4.OrgType='sce','1',IF(v3.OrgType='Pedagang' OR v3.OrgType='sce','1',IF(v.OrgType='Pedagang' OR v.OrgType='sce','1','0')))) AS 1_bs,
					IF(v5.OrgType='Organisasi Petani','1',IF(v4.OrgType='Organisasi Petani','1',IF(v3.OrgType='Organisasi Petani','1',IF(v.OrgType='Organisasi Petani','1','0')))) AS 1_coop,
					IF(IF(v5.OrgType='Pedagang' OR v5.OrgType='sce','1',IF(v4.OrgType='Pedagang' OR v4.OrgType='sce','1',IF(v3.OrgType='Pedagang' OR v3.OrgType='sce','1',IF(v.OrgType='Pedagang' OR v.OrgType='sce','1','0'))))='0' && IF(v5.OrgType='Organisasi Petani','1',IF(v4.OrgType='Organisasi Petani','1',IF(v3.OrgType='Organisasi Petani','1',IF(v.OrgType='Organisasi Petani','1','0'))))='1','1','0') 1_cooponly
				FROM
					ktv_supplychain_batch a
					#untuk cek status dan bukan di Gudang
					LEFT JOIN ktv_supplychain_org_view v ON a.SupplyOrgID=v.SupplychainID
					LEFT JOIN ktv_supplychain_org_view v2 ON a.SupplyDestOrgID=v2.SupplychainID
					#untuk cek apakah farmer atau batch
					LEFT JOIN ktv_supplychain_transaction b ON a.SupplyBatchNumber=b.SupplyID
					LEFT JOIN ktv_supplychain_transaction c ON a.SupplyBatchID=c.SupplyBatchID
					LEFT JOIN ktv_supplychain_batch d ON c.SupplyID=d.SupplyBatchNumber
					LEFT JOIN ktv_supplychain_org_view v3 ON v3.SupplychainID=d.SupplyOrgID
					#jika batch maka dicek turunannya
					LEFT JOIN ktv_supplychain_transaction e ON d.SupplyBatchID=e.SupplyBatchID
					LEFT JOIN ktv_supplychain_transaction f ON (f.SupplyTransID=c.SupplyTransID OR f.SupplyTransID=e.SupplyTransID)
					LEFT JOIN ktv_supplychain_batch g ON g.SupplyBatchNumber=f.SupplyID
					LEFT JOIN ktv_supplychain_org_view v4 ON v4.SupplychainID=g.SupplyOrgID
					LEFT JOIN ktv_supplychain_transaction h ON h.SupplyBatchID=g.SupplyBatchID
					LEFT JOIN ktv_supplychain_batch i ON i.SupplyBatchNumber=h.SupplyID
					LEFT JOIN ktv_supplychain_org_view v5 ON v5.SupplychainID=i.SupplyOrgID
					LEFT JOIN ktv_supplychain_transaction j ON i.SupplyBatchID=j.SupplyBatchID

					LEFT JOIN ktv_supplychain_transaction k ON k.SupplyTransID=IF(f.SupplyType='Farmer',f.SupplyTransID,IF(h.SupplyType='Farmer',h.SupplyTransID,j.SupplyTransID))
					LEFT JOIN ktv_farmer_view l ON k.SupplyID=l.FarmerID
				WHERE
					a.SupplyDestStatus!='Delivered' AND v.OrgType!='Gudang' AND v2.OrgType!='Gudang' AND b.SupplyTransID IS NULL AND f.SupplyTransID IS NOT NULL
					#skip yg lewat haji burhan
					/*AND v3.OrgType!='Pedagang' AND v3.OrgID!='40144'*/ AND v4.OrgType!='Pedagang'
				) master
					LEFT JOIN ktv_supplychain_batch bb ON bb.SupplyBatchID=master.SupplyBatchID
					LEFT JOIN ktv_supplychain_transaction cc ON cc.SupplyID=bb.SupplyBatchNumber
					LEFT JOIN ktv_supplychain_org_view dd ON dd.SupplychainID=bb.SupplyDestOrgID
					LEFT JOIN ktv_supplychain_batch ee ON ee.SupplyBatchID=cc.SupplyBatchID
					LEFT JOIN (
						SELECT
							a.ChildOrgId id, CONCAT('|',REPLACE(GROUP_CONCAT(DISTINCT (IF(b.OrgType='Gudang',b.OrgID,IF(d.OrgType='Gudang',d.OrgID,IF(f.OrgType='Gudang',f.OrgID,NULL))))),',','|'),'|') AS wh_dest
						FROM
							ktv_supplychain_org_rel a
							LEFT JOIN ktv_supplychain_org_view b ON a.ParentOrgId=b.SupplychainID
							LEFT JOIN ktv_supplychain_org_rel c ON c.ChildOrgId=b.SupplychainID
							LEFT JOIN ktv_supplychain_org_view d ON c.ParentOrgId=d.SupplychainID
							LEFT JOIN ktv_supplychain_org_rel e ON e.ChildOrgId=d.SupplychainID
							LEFT JOIN ktv_supplychain_org_view f ON e.ParentOrgId=f.SupplychainID
						WHERE (b.OrgType='Gudang' OR d.OrgType='Gudang' OR f.OrgType='Gudang')
						GROUP BY a.ChildOrgId
					) est ON est.id=IFNULL(IF(1_bs='1',1_supplychainid,NULL),IF(1_cooponly='1',1_supplychainid,IF(1_coop='1',bb.SupplyDestOrgID,NULL)))";

        $this->certified_tahun = "
         SELECT DISTINCT YEAR(ExternalDate) tahun
         FROM ktv_certification
         WHERE YEAR(ExternalDate)>0";

        $this->traceability5 = "
        INSERT INTO rpt_traceability_transaction
		SELECT
			f.FarmerID,
			CONCAT('[',f.FarmerID,'] ', f.FarmerName) FarmerName,
			f.VillageID,
			IF(a.SupplyType='Farmer','1','0') farmer_iscertified,
			0 'Farmer_netto',
			IF(fpay.id IS NOT NULL, 1, 0) farmer_ispaid,
			fpay.PaymentDate farmer_paymentdate,
			fpay.DetailBerat farmer_paymentnetto,
			
			c.SupplyOrgID 1_supplychainid,
			v1.OrgType 1_orgtype,
			v1.OrgID 1_orgid,
			v1.`Name` 1_name,
			vd1.OrgType 1_destorgtype,
			vd1.OrgID 1_destorgid,
			vd1.`Name` 1_destname,
			c.SupplyBatchNumber 1_batchnumber,
			c.SupplyBatchID 1_batchid,
			c.DestPO 1_po,
			a.SupplyTransID 1_transid,
			SUBSTR(a.DateTransaction,1,10) 1_date,
			IF(a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,a.FAQVolumeBruto,a.FFVolumeBruto) 1_bruto,
			IF(a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,IF(a.FAQVolumeNetto IS NOT NULL OR a.FAQVolumeNetto!=0,a.FAQVolumeNetto,a.FAQVolumeBruto),IF(a.FFVolumeNetto IS NOT NULL OR a.FFVolumeNetto!=0,a.FFVolumeNetto,a.FFVolumeBruto)) 1_netto,
			IF(bspay.id IS NOT NULL, 1, 0) bs_ispaid,
			bspay.PaymentDate bs_paymentdate,
			c.SupplyDestStatus 1_status,

			b2.SupplyOrgID 2_supplychainid,
			v2.OrgType 2_orgtype,
			v2.OrgID 2_orgid,
			v2.`Name` 2_name,
			vd2.OrgType 2_destorgtype,
			vd2.OrgID 2_destorgid,
			vd2.`Name` 2_destname,
			b2.SupplyBatchNumber 2_batchnumber,
			b2.SupplyBatchID 2_batchid,
			b2.DestPO 2_po,
			t2.SupplyTransID 2_transid,
			SUBSTR(t2.DateTransaction,1,10) 2_date,
			IF(t2.FAQVolumeBruto IS NOT NULL AND t2.FAQVolumeBruto!=0,t2.FAQVolumeBruto,t2.FFVolumeBruto) 2_bruto,
			IF(t2.FAQVolumeBruto IS NOT NULL AND t2.FAQVolumeBruto!=0,IF(t2.FAQVolumeNetto IS NOT NULL OR t2.FAQVolumeNetto!=0,t2.FAQVolumeNetto,t2.FAQVolumeBruto),IF(t2.FFVolumeNetto IS NOT NULL OR t2.FFVolumeNetto!=0,t2.FFVolumeNetto,t2.FFVolumeBruto)) 2_netto,
			IF(cooppay.id IS NOT NULL, 1, 0) coop_ispaid,
			cooppay.PaymentDate coop_paymentdate,
			b2.SupplyDestStatus,

			b3.SupplyOrgID 3_supplychainid,
			v3.OrgType 3_orgtype,
			v3.OrgID 3_orgid,
			v3.`Name` 3_name,
			b3.SupplyBatchNumber 3_batchnumber,
			b3.SupplyBatchID 3_batchid,
			b3.DestPO 3_po,
			t3.SupplyTransID 3_transid,
			SUBSTR(t3.DateTransaction,1,10) 3_date,
			IF(t3.FAQVolumeBruto IS NOT NULL AND t3.FAQVolumeBruto!=0,t3.FAQVolumeBruto,t3.FFVolumeBruto) 3_bruto,
			IF(t3.FAQVolumeBruto IS NOT NULL AND t3.FAQVolumeBruto!=0,IF(t3.FAQVolumeNetto IS NOT NULL OR t3.FAQVolumeNetto!=0,t3.FAQVolumeNetto,t3.FAQVolumeBruto),IF(t3.FFVolumeNetto IS NOT NULL OR t3.FFVolumeNetto!=0,t3.FFVolumeNetto,t3.FFVolumeBruto)) 2_netto,
			b3.SupplyDestStatus,
			USD wh_dollar,
			IFNULL(KursNominal,Kurs) wh_kurs,
			PersenPetani wh_persenfarmer,
			PersenPerwakilan wh_persenbs,
			PersenBuyinUnit wh_persenkoperasi,
			IF(v1.OrgType='Gudang',CONCAT('|',v1.OrgID,'|'),IF(v2.OrgType='Gudang',CONCAT('|',v2.OrgID,'|'),IF(v3.OrgType='Gudang',CONCAT('|',v3.OrgID,'|'),dest.wh_dest))) wh_dest,
			a.IsQuota,
			IF(v1.OrgType='Gudang','1',IF(v2.OrgType='Gudang','1',IF(v3.OrgType='Gudang','1','0'))) IsPremium,

			IF(a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,'FAQ','FF') tipe, 
			IF(a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,a.FAQVolumeBruto,a.FFVolumeBruto) Bruto,
			IFNULL(dt1.Moisture,0) MoistureBawah,
			IFNULL(b.Moisture,0) MoistureAtas,
			IFNULL(dt2.MoistureBawah,0) MaxMoistureBawah,
			IFNULL(dt2.MoistureAtas,0) MaxMoistureAtas,
			dt1.Standard StandarTrans,
			IF(a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,dt3.FAQValue,dt3.FFValue) StandarSuppltchainOrgID,
			
			
			/*Moisture Fix*/
			IF(
				IF(
					IFNULL(dt1.Moisture,0)!=0,
					IFNULL(dt1.Moisture,0),
					IFNULL(b.Moisture,0)
				)!=0,
				IF(
					IFNULL(dt1.Moisture,0)!=0,
					IFNULL(dt1.Moisture,0),
					IFNULL(b.Moisture,0)
				), 
				IF(
					IFNULL(dt2.MoistureBawah,0)!=0,
					IFNULL(dt2.MoistureBawah,0),
					IF(
						IFNULL(dt2.MoistureAtas,0)!=0,
						IFNULL(dt2.MoistureAtas,0),
						0
					)
				)
			) MoistureFix,
			
			/*Standar Fix*/
			IFNULL(
				dt1.Standard,
				IF(
					a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,
					dt3.FAQValue,
					dt3.FFValue
				)
			) StandardFix,
			
			/*Rumus*/
			IF(
				/*Moisture Fix*/
				IF(
					IF(
						IFNULL(dt1.Moisture,0)!=0,
						IFNULL(dt1.Moisture,0),
						IFNULL(b.Moisture,0)
					)!=0,
					IF(
						IFNULL(dt1.Moisture,0)!=0,
						IFNULL(dt1.Moisture,0),
						IFNULL(b.Moisture,0)
					), 
					IF(
						IFNULL(dt2.MoistureBawah,0)!=0,
						IFNULL(dt2.MoistureBawah,0),
						IF(
							IFNULL(dt2.MoistureAtas,0)!=0,
							IFNULL(dt2.MoistureAtas,0),
							0
						)
					)
				)
				- 
				/*Standar Fix*/
				IFNULL(
					dt1.Standard,
					IF(
						a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,
						dt3.FAQValue,
						dt3.FFValue
					)
				)
				> 
				0,
				IF(
					IF(
						IFNULL(dt1.Moisture,0)!=0,
						IFNULL(dt1.Moisture,0),
						IFNULL(b.Moisture,0)
					)!=0,
					IF(
						IFNULL(dt1.Moisture,0)!=0,
						IFNULL(dt1.Moisture,0),
						IFNULL(b.Moisture,0)
					), 
					IF(
						IFNULL(dt2.MoistureBawah,0)!=0,
						IFNULL(dt2.MoistureBawah,0),
						IF(
							IFNULL(dt2.MoistureAtas,0)!=0,
							IFNULL(dt2.MoistureAtas,0),
							0
						)
					)
				)
				- 
				/*Standar Fix*/
				IFNULL(
					dt1.Standard,
					IF(
						a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,
						dt3.FAQValue,
						dt3.FFValue
					)
				),
				0
			) Rumus,
			
			/*Berat*/
			(
				/*Bruto*/
				IF(
					a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,
					a.FAQVolumeBruto,
					a.FFVolumeBruto
				)
				*
				(
					100
					-
					/*Rumus*/
					IF(
						/*Moisture Fix*/
						IF(
							IF(
								IFNULL(dt1.Moisture,0)!=0,
								IFNULL(dt1.Moisture,0),
								IFNULL(b.Moisture,0)
							)!=0,
							IF(
								IFNULL(dt1.Moisture,0)!=0,
								IFNULL(dt1.Moisture,0),
								IFNULL(b.Moisture,0)
							), 
							IF(
								IFNULL(dt2.MoistureBawah,0)!=0,
								IFNULL(dt2.MoistureBawah,0),
								IF(
									IFNULL(dt2.MoistureAtas,0)!=0,
									IFNULL(dt2.MoistureAtas,0),
									0
								)
							)
						)
						- 
						/*Standar Fix*/
						IFNULL(
							dt1.Standard,
							IF(
								a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,
								dt3.FAQValue,
								dt3.FFValue
							)
						)
						> 
						0,
						IF(
							IF(
								IFNULL(dt1.Moisture,0)!=0,
								IFNULL(dt1.Moisture,0),
								IFNULL(b.Moisture,0)
							)!=0,
							IF(
								IFNULL(dt1.Moisture,0)!=0,
								IFNULL(dt1.Moisture,0),
								IFNULL(b.Moisture,0)
							), 
							IF(
								IFNULL(dt2.MoistureBawah,0)!=0,
								IFNULL(dt2.MoistureBawah,0),
								IF(
									IFNULL(dt2.MoistureAtas,0)!=0,
									IFNULL(dt2.MoistureAtas,0),
									0
								)
							)
						)
						- 
						/*Standar Fix*/
						IFNULL(
							dt1.Standard,
							IF(
								a.FAQVolumeBruto IS NOT NULL AND a.FAQVolumeBruto!=0,
								dt3.FAQValue,
								dt3.FFValue
							)
						),
						0
					)
				)
				/
				100
			) Berat
			
			
		FROM
			ktv_supplychain_transaction a 
			LEFT JOIN (
				SELECT
					a.SupplyTransID, MAX(a.Moisture) Moisture
				FROM
					ktv_supplychain_transaction_dtl a	
					LEFT JOIn ktv_supplychain_transaction b ON a.SupplyTransID=b.SupplyTransID
				WHERE	
					b.SupplyType!='Batch' AND a.FromBatchID IS NULL
				GROUP BY a.SupplyTransID
			) b ON b.SupplyTransID=a.SupplyTransID
			LEFT JOIN (
				SELECT
					DISTINCT(a.SupplyTransID) SupplyTransID,
					IF(IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ')='FAQ',a.FAQResult,a.FFResult) Moisture,
					IF(IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ')='FAQ',a.FAQStandard,a.FFStandard) Standard
				FROM
					ktv_supplychain_transaction_quality a
					LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.DetailID=b.DetailID AND a.StandardID=b.StandardID AND b.`Name` LIKE '%Moisture%'
					LEFT JOIN ktv_supplychain_transaction_dtl c ON a.SupplyTransID=c.SupplyTransID
				WHERE 
					b.`Name` IS NOT NULL
			) dt1 ON dt1.SupplyTransID=a.SupplyTransID
			LEFT JOIN (
				SELECT
					d.SupplyBatchID,
					MAX(IF(IF((a.FFStandard IS NOT NULL AND a.FFStandard!=0) AND (a.FAQStandard IS NULL OR a.FAQStandard!=0),'FF','FAQ')='FAQ',a.FAQResult,a.FFResult)) MoistureBawah,
					MAX(IFNULL(c.Moisture,0)) MoistureAtas
				FROM
					ktv_supplychain_transaction d 
					LEFT JOIN ktv_supplychain_transaction_quality a ON d.SupplyTransID=a.SupplyTransID
					LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.DetailID=b.DetailID AND a.StandardID=b.StandardID AND b.`Name` LIKE '%Moisture%'
					LEFT JOIN ktv_supplychain_transaction_dtl c ON a.SupplyTransID=c.SupplyTransID AND c.FromBatchID IS NULL
				WHERE b.`Name` IS NOT NULL
				GROUP BY d.SupplyBatchID
			) dt2 ON dt2.SupplyBatchID=a.SupplyBatchID
			LEFT JOIN ktv_supplychain_batch c ON c.SupplyBatchID=a.SupplyBatchID
			LEFT JOIN ktv_supplychain_org_view v1 ON v1.SupplychainID=c.SupplyOrgID
			LEFT JOIN ktv_supplychain_org_view vd1 ON vd1.SupplychainID=c.SupplyDestOrgID
			LEFT JOIN (
				SELECT
					org.SupplychainID StandardSupplychainID,
					MAX(a.StandardID) StandardID,
					IFNULL(b.FAQValue,StandardMoistureKarung) FAQValue,
					IFNULL(b.FFValue,StandardMoistureKarung) FFValue
				FROM
					ktv_supplychain_org org 
					LEFT JOIN ktv_supplychain_quality_standard a ON a.StandardSupplychainID=org.SupplychainID
					LEFT JOIN ktv_supplychain_quality_standard_detail b ON a.StandardID=b.StandardID AND b.`Name` LIKE '%Moisture%'
				GROUP BY
					org.SupplychainID
			) dt3 ON dt3.StandardSupplychainID=c.SupplyOrgID
			LEFT JOIN ktv_supplychain_transaction t2 ON t2.SupplyID=c.SupplyBatchNumber
			LEFT JOIN ktv_supplychain_batch b2 ON b2.SupplyBatchID=t2.SupplyBatchID
			LEFT JOIN ktv_supplychain_org_view v2 ON v2.SupplychainID=b2.SupplyOrgID
			LEFT JOIN ktv_supplychain_org_view vd2 ON vd2.SupplychainID=b2.SupplyDestOrgID
			
			LEFT JOIN ktv_supplychain_transaction t3 ON t3.SupplyID=b2.SupplyBatchNumber
			LEFT JOIN ktv_supplychain_batch b3 ON b3.SupplyBatchID=t3.SupplyBatchID
			LEFT JOIN ktv_supplychain_org_view v3 ON v3.SupplychainID=b3.SupplyOrgID
			LEFT JOIN ktv_supplychain_org_view vd3 ON vd3.SupplychainID=b3.SupplyDestOrgID
			LEFT JOIN ktv_farmer f ON f.FarmerID=a.supplyID

			LEFT JOIN ktv_supplychain_premium prem ON prem.PremiumSupplychainID=IF(v1.OrgType='Gudang',v1.SupplychainID,IF(v2.OrgType='Gudang',v2.SupplychainID,IF(v3.OrgType='Gudang',v3.SupplychainID,0))) AND (DATE(a.DateTransaction) BETWEEN PremiumDateStart AND PremiumDateEnd)
			LEFT JOIN ktv_supplychain_kurs krs ON krs.KursSupplychainID=IF(v1.OrgType='Gudang',v1.SupplychainID,IF(v2.OrgType='Gudang',v2.SupplychainID,IF(v3.OrgType='Gudang',v3.SupplychainID,0))) AND (DATE(a.DateTransaction) BETWEEN KursDateStart AND KursDateEnd)
			
			LEFT JOIN (
				SELECT b.DetailSupplyTransID AS id, a.PaymentDestType, a.PaymentDate, b.DetailBerat FROM ktv_supplychain_payment a LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID=b.DetailPaymentID
				WHERE a.PaymentDestType='Farmer'
				GROUP BY b.DetailSupplyTransID
			) fpay ON fpay.id=a.SupplyTransID
			LEFT JOIN (
				SELECT
					DISTINCT(IF(c.SupplyType='Farmer',c.SupplyTransID,IF(e.SupplyType='Farmer',e.SupplyTransID,g.SupplyTransID))) AS id, a.PaymentDestType, a.PaymentDate, b.DetailBerat
				FROM
					ktv_supplychain_payment a
					LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID = b.DetailPaymentID
					LEFT JOIN ktv_supplychain_transaction c ON c.SupplyTransID=b.DetailSupplyTransID
					LEFT JOIN ktv_supplychain_batch d ON d.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
					LEFT JOIN ktv_supplychain_transaction e ON e.SupplyBatchID=d.SupplyBatchID
					LEFT JOIN ktv_supplychain_batch f ON f.SupplyBatchNumber=e.SupplyID  AND e.SupplyType='Batch'
					LEFT JOIN ktv_supplychain_transaction g ON g.SupplyBatchID=f.SupplyBatchID
				WHERE a.PaymentDestType = 'Pedagang' OR a.PaymentDestType='sce'
				GROUP BY IF(c.SupplyType='Farmer',c.SupplyTransID,IF(e.SupplyType='Farmer',e.SupplyTransID,g.SupplyTransID))
			) bspay ON bspay.id=a.SupplyTransID
			LEFT JOIN (
				SELECT
					DISTINCT(IF(c.SupplyType='Farmer',c.SupplyTransID,IF(e.SupplyType='Farmer',e.SupplyTransID,g.SupplyTransID))) AS id, a.PaymentDestType, a.PaymentDate, b.DetailBerat
				FROM
					ktv_supplychain_payment a
					LEFT JOIN ktv_supplychain_payment_detail b ON a.PaymentID = b.DetailPaymentID
					LEFT JOIN ktv_supplychain_transaction c ON c.SupplyTransID=b.DetailSupplyTransID
					LEFT JOIN ktv_supplychain_batch d ON d.SupplyBatchNumber=c.SupplyID AND c.SupplyType='Batch'
					LEFT JOIN ktv_supplychain_transaction e ON e.SupplyBatchID=d.SupplyBatchID
					LEFT JOIN ktv_supplychain_batch f ON f.SupplyBatchNumber=e.SupplyID  AND e.SupplyType='Batch'
					LEFT JOIN ktv_supplychain_transaction g ON g.SupplyBatchID=f.SupplyBatchID
				WHERE a.PaymentDestType = 'Organisasi Petani'
				GROUP BY IF(c.SupplyType='Farmer',c.SupplyTransID,IF(e.SupplyType='Farmer',e.SupplyTransID,g.SupplyTransID))
			) cooppay ON cooppay.id=a.SupplyTransID
			LEFT JOIN (
				SELECT
					a.ChildOrgId id, CONCAT('|',REPLACE(GROUP_CONCAT(DISTINCT (IF(b.OrgType='Gudang',b.OrgID,IF(d.OrgType='Gudang',d.OrgID,IF(f.OrgType='Gudang',f.OrgID,NULL))))),',','|'),'|') AS wh_dest
				FROM
					ktv_supplychain_org_rel a
					LEFT JOIN ktv_supplychain_org_view b ON a.ParentOrgId=b.SupplychainID
					LEFT JOIN ktv_supplychain_org_rel c ON c.ChildOrgId=b.SupplychainID
					LEFT JOIN ktv_supplychain_org_view d ON c.ParentOrgId=d.SupplychainID
					LEFT JOIN ktv_supplychain_org_rel e ON e.ChildOrgId=d.SupplychainID
					LEFT JOIN ktv_supplychain_org_view f ON e.ParentOrgId=f.SupplychainID
				WHERE (b.OrgType='Gudang' OR d.OrgType='Gudang' OR f.OrgType='Gudang')
				GROUP BY a.ChildOrgId
			) dest ON dest.id =c.SupplyOrgID
		WHERE	
			a.SupplyType!='Batch' AND c.SupplyBatchID IS NOT NULL";

        $this->traceability6 = "
		INSERT INTO rpt_traceability
		SELECT
			a.farmer_id,
			a.farmer_name,
			a.Farmer_villageid,
			a.farmer_iscertified,
			IF(
				a.farmer_ispaidnetto IS NOT NULL AND a.farmer_ispaidnetto!=0 AND a.farmer_ispaid!='',
				a.farmer_ispaidnetto,
				CASE
					WHEN
						a.2_orgtype='Gudang'
					THEN
						IF(
							a.2_netto IS NULL OR a.2_netto=0,
							a.1_netto,
							CASE 
								WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.2_netto
								WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.2_netto
								WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
								WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (a.2_netto - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
								ELSE 0
							END
						)
					WHEN
						a.3_orgtype='Gudang'
					THEN
						IF(
								a.3_netto IS NULL OR a.3_netto=0,
								IF(
									a.2_netto IS NULL OR a.2_netto=0,
									a.1_netto,
									CASE 
										WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.2_netto
										WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.2_netto
										WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
										WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (a.2_netto - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
										ELSE 0
									END
								),
								CASE 
									WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.3_netto
									WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.3_netto
									WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
									WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (a.3_netto - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
									ELSE 0
								END
						)
					ELSE
						a.1_netto
				END
			) farmer_netto,
			IF(a.farmer_ispaid='1','1','0') farmer_ispaid,
			a.farmer_ispaiddate,
			/*1*/
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_supplychainid,
				NULL
			) 1_supplychainid,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_orgtype,
				NULL
			) 1_orgtype,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_orgid,
				NULL
			) 1_orgid,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_name,
				NULL
			) 1_name,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_destorgtype,
				NULL
			) 1_destorgtype,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_destorgid,
				NULL
			) 1_destorgid,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_destname,
				NULL
			) 1_destname,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_batchnumber,
				NULL
			) 1_batchnumber,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_batchid,
				NULL
			) 1_batchid,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_po,
				NULL
			) 1_po,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_transid,
				NULL
			) 1_transid,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_date,
				NULL
			) 1_date,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_bruto,
				NULL
			) 1_bruto,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				IF(a.1_netto IS NOT NULL && a.1_netto!=0,a.1_netto,a.1_bruto),
				NULL
			) 1_netto,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_ispaid,
				NULL
			) 1_ispaid,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_ispaiddate,
				NULL
			) 1_ispaiddate,
			IF(
				(a.1_orgtype='Pedagang' OR a.1_orgtype='sce') AND a.2_orgtype!='Gudang',
				a.1_status,
				NULL
			) 1_status,
			/*2*/
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_supplychainid,
				a.2_supplychainid
			) 2_supplychainid,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				IF(
					a.1_orgtype='Organisasi Petani',
					'koperasi',
					a.1_orgtype
				),
				IF(
					a.2_orgtype='Organisasi Petani',
					'koperasi',
					a.2_orgtype
				)
			) 2_orgtype,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_orgid,
				a.2_orgid
			) 2_orgid,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_name,
				a.2_name
			) 2_name,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_destorgtype,
				a.2_destorgtype
			) 2_destorgtype,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_destorgid,
				a.2_destorgid
			) 2_destorgid,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_destname,
				a.2_destname
			) 2_destname,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_batchnumber,
				a.2_batchnumber
			) 2_batchnumber,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_batchid,
				a.2_batchid
			) 2_batchid,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_po,
				a.2_po
			) 2_po,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_transid,
				a.2_transid
			) 2_transid,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_date,
				a.2_date
			) 2_date,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_bruto,
				CASE 
					WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.2_bruto
					WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.2_bruto
					WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
					WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (a.2_bruto - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
					ELSE 0
				END
			) 2_bruto,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				IF(a.1_netto IS NOT NULL && a.1_netto!=0,a.1_netto,a.1_bruto),
				CASE 
					WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * IF(a.2_netto IS NOT NULL && a.2_netto!=0,a.2_netto,a.2_bruto)
					WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * IF(a.2_netto IS NOT NULL && a.2_netto!=0,a.2_netto,a.2_bruto)
					WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
					WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (IF(a.2_netto IS NOT NULL && a.2_netto!=0,a.2_netto,a.2_bruto) - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
					ELSE 0
				END 
			) 2_netto,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				IF(
					a.1_orgtype='Pedagang' OR a.1_orgtype='sce',
					IF(a.1_ispaid='1','1','0'),
					IF(a.2_ispaid='1','1','0')
				),
				IF(
					a.2_orgtype='Pedagang' OR a.2_orgtype='sce',
					IF(a.1_ispaid='1','1','0'),
					IF(a.2_ispaid='1','1','0')
				)
			) 2_ispaid,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				IF(
					a.1_orgtype='Pedagang' OR a.1_orgtype='sce',
					1_ispaiddate,
					2_ispaiddate
				),
				IF(
					a.2_orgtype='Pedagang' OR a.2_orgtype='sce',
					1_ispaiddate,
					2_ispaiddate
				)
			) 2_ispaiddate,
			IF(
				a.2_orgtype='Gudang' OR a.2_supplychainid IS NULL,
				a.1_status,
				a.2_status
			) 2_status,
			CASE
				WHEN a.2_orgtype='Gudang' THEN a.2_supplychainid
				WHEN a.3_orgtype='Gudang' THEN a.3_supplychainid
				ELSE NULL
			END wh_supplychainid,
			CASE
				WHEN a.2_orgtype='Gudang' THEN a.2_orgid
				WHEN a.3_orgtype='Gudang' THEN a.3_orgid
				ELSE NULL
			END wh_orgid,
			CASE
				WHEN a.2_orgtype='Gudang' THEN a.2_name
				WHEN a.3_orgtype='Gudang' THEN a.3_name
				ELSE NULL
			END wh_name,
			CASE
				WHEN a.2_orgtype='Gudang' THEN a.2_batchid
				WHEN a.3_orgtype='Gudang' THEN a.3_batchid
				ELSE NULL
			END wh_batchid,
			CASE
				WHEN a.2_orgtype='Gudang' THEN a.2_po
				WHEN a.3_orgtype='Gudang' THEN a.3_po
				ELSE NULL
			END wh_po,
			CASE
				WHEN a.2_orgtype='Gudang' THEN a.2_transid
				WHEN a.3_orgtype='Gudang' THEN a.3_transid
				ELSE NULL
			END wh_transid,
			CASE
				WHEN a.2_orgtype='Gudang' THEN a.2_date
				WHEN a.3_orgtype='Gudang' THEN a.3_date
				ELSE NULL
			END wh_date,
			CASE
				WHEN 
					a.2_orgtype='Gudang' 
				THEN 
					(CASE 
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.2_bruto
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.2_bruto
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
						WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (a.2_bruto - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
						ELSE 0
					END)
				WHEN 
					a.3_orgtype='Gudang' 
				THEN 
					(CASE 
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.3_bruto
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * a.3_bruto
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
						WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (a.3_bruto - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
						ELSE 0
					END)
				ELSE NULL
			END wh_bruto,
			CASE
				WHEN 
					a.2_orgtype='Gudang' 
				THEN 
					(CASE 
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * IF(a.2_netto IS NOT NULL && a.2_netto!=0,a.2_netto,a.2_bruto)
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * IF(a.2_netto IS NOT NULL && a.2_netto!=0,a.2_netto,a.2_bruto)
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
						WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (IF(a.2_netto IS NOT NULL && a.2_netto!=0,a.2_netto,a.2_bruto) - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
						ELSE 0
					END)
				WHEN 
					a.3_orgtype='Gudang' 
				THEN 
					(CASE 
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * IF(a.3_netto IS NOT NULL && a.3_netto!=0,a.3_netto,a.3_bruto)
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)=0 THEN a.Berat / IF(dt1.BrutoAll IS NOT NULL,dt1.BrutoAll,dt2.BrutoAll) * IF(a.3_netto IS NOT NULL && a.3_netto!=0,a.3_netto,a.3_bruto)
						WHEN a.Rumus=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat
						WHEN a.Rumus!=0 AND IF(dt1.MaxMoisture IS NOT NULL,dt1.MaxMoisture,dt2.MaxMoisture)!=0 AND IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus)!=0 THEN a.Berat / IF(dt1.BrutoRumus IS NOT NULL,dt1.BrutoRumus,dt2.BrutoRumus) * (IF(a.3_netto IS NOT NULL && a.3_netto!=0,a.3_netto,a.3_bruto) - IF(dt1.Bruto0 IS NOT NULL,dt1.Bruto0,dt2.Bruto0))
						ELSE 0
					END)
				ELSE NULL
			END wh_bruto,
			a.wh_dollar,
			a.wh_kurs,
			a.wh_persenfarmer,
			a.wh_persenbs,
			a.wh_persenkoperasi,
			CASE
				WHEN a.2_orgtype='Gudang' THEN a.2_status
				WHEN a.3_orgtype='Gudang' THEN a.3_status
				ELSE NULL
			END wh_status,
			CASE
				WHEN a.2_orgtype='Gudang' THEN CONCAT('|',a.2_orgid,'|')
				WHEN a.3_orgtype='Gudang' THEN CONCAT('|',a.3_orgid,'|')
				ELSE a.wh_dest
			END wh_dest,
			a.IsQuota,
			a.IsPremium
		FROM
			rpt_traceability_transaction a 
			LEFT JOIN (
				SELECT
					MAX(MoistureFix) MaxMoisture,
					SUM(IF(Rumus!=0,Berat,0)) BrutoRumus,
					SUM(IF(Rumus=0,Berat,0)) Bruto0,
					SUM(Berat) BrutoAll,
					CASE WHEN 2_orgtype='Gudang' THEN 2_po
					WHEN 3_orgtype='Gudang' THEN 3_po
					ELSE 1_batchid END po
				FROM
					rpt_traceability_transaction 
				GROUP BY
					CASE WHEN 2_orgtype='Gudang' THEN 2_po
					WHEN 3_orgtype='Gudang' THEN 3_po
					ELSE 1_batchid END
			) dt1 ON dt1.po=a.1_po
			LEFT JOIN (
				SELECT
					1_batchid,
					MAX(MoistureFix) MaxMoisture,
					SUM(IF(Rumus!=0,Berat,0)) BrutoRumus,
					SUM(IF(Rumus=0,Berat,0)) Bruto0,
					SUM(Berat) BrutoAll
				FROM
					rpt_traceability_transaction 
				GROUP BY
					1_batchid
			) dt2 ON dt2.1_batchid=a.1_batchid";
    }

    function generateReportTraceability($table, $data, $userId) {
        $this->db->trans_start();
        $query1 = $this->db->query(sprintf($this->truncate, 'rpt_traceability'));
        $query1 = $this->db->query(sprintf($this->truncate, 'rpt_traceability_transaction'));
        //$query2 = $this->db->query($this->traceability);
        //$query3 = $this->db->query($this->traceability3);
        //$query3 = $this->db->query($this->traceability4);
        $query5 = $this->db->query($this->traceability5);
        if ($query5) {
            $query6 = $this->db->query($this->traceability6);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $results = false;
        else
            $results = true;
        return $results;
    }

    function generateDashMain() {
        $sql_farmer = "INSERT INTO dash_main_farmer
SELECT
    kcf.CPGid
    ,kcf.VillageID
    ,kcf.SubDistrictID
    ,kcf.SubDistrict
    ,COUNT(DISTINCT kcf.FarmerID) AS farmer
    ,SUM(IF(kcf.Birthdate IS NULL OR kcf.Birthdate = '0000-00-00',1,0)) AS farmer_unage
    ,SUM(TIMESTAMPDIFF(YEAR,kcf.Birthdate,CURDATE())) AS age_sum
    ,SUM(IF(kcf.Gender=1,1,0)) AS male
    ,SUM(IF(kcf.Gender=2,1,0)) AS female
    ,SUM(IF(kcf.isCertified=1,1,0)) AS certified
    ,COUNT(gnp.FarmerID) AS gnp
    ,COUNT(gfp.FarmerID) AS gfp
    ,NOW() AS DateUpdated
FROM ktv_farmer_view kcf
LEFT JOIN (
    SELECT a.FarmerID FROM (
        SELECT FarmerID
        FROM ktv_cpg_batch_trainings_farmers kcbtf
        LEFT JOIN ktv_cpg_batch_trainings kcbt ON kcbt.CpgBatchTrainingID= kcbtf.CpgBatchTrainingID
        WHERE CPGtrainingsID=2 AND TrainingStart>0
        UNION ALL
        SELECT FarmerID
        FROM ktv_kader_trainings_participants kktp
        LEFT JOIN ktv_kader_trainings kkt ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
        WHERE CPGtrainingsID=2 AND TrainingStart>0
        ) a
    GROUP BY a.FarmerID
    ) gnp ON gnp.FarmerID=kcf.FarmerID
LEFT JOIN (
    SELECT a.FarmerID FROM (
        SELECT FarmerID
        FROM ktv_cpg_batch_trainings_farmers kcbtf
        LEFT JOIN ktv_cpg_batch_trainings kcbt ON kcbt.CpgBatchTrainingID= kcbtf.CpgBatchTrainingID
        WHERE CPGtrainingsID=8 AND TrainingStart>0
        UNION ALL
        SELECT FarmerID
        FROM ktv_kader_trainings_participants kktp
        LEFT JOIN ktv_kader_trainings kkt ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
        WHERE CPGtrainingsID=8 AND TrainingStart>0
        ) a
    GROUP BY a.FarmerID
    ) gfp ON gfp.FarmerID=kcf.FarmerID
WHERE
    1 = 1
    AND kcf.VillageID IS NOT NULL
    AND kcf.StatusCode = 'active'
    AND kcf.isTrained = 1
GROUP BY
        kcf.CPGid,
        kcf.SubDistrictID;";
        $sql_garden = "INSERT INTO dash_main_garden
SELECT
    kcf.CPGid
    ,kcf.VillageID
    ,kcf.SubDistrictID
    ,kcf.SubDistrict
    ,COUNT(g.FarmerID) AS garden
    ,SUM(g.GardenHaUnCertified) AS GardenHaUnCertified
    ,SUM(IFNULL(PohonTBM,0))+SUM(IFNULL(PohonTM,0))+SUM(IFNULL(PohonRehab,0)) cocoa_tree
    ,SUM(IFNULL(PohonTM,0)) tm_cocoa_tree
    -- ,SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) AS production
    ,SUM(IF(Production > 0, Production, ProductionCalc)) AS production
    ,SUM(IF(GardenHaUnCertified < 1, 1, 0)) AS small
    ,SUM(IF(GardenHaUnCertified >= 1 AND GardenHaUnCertified < 2, 1, 0)) AS MEDIUM
    ,SUM(IF(GardenHaUnCertified >= 2, 1, 0)) AS large
    -- ,SUM(IF(((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/GardenHaUnCertified < 500, 1, 0)) AS unprofessional
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified < 500, 1, 0)) AS unprofessional
    -- ,SUM(IF(((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/GardenHaUnCertified >= 500 AND ((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/GardenHaUnCertified < 1000, 1, 0)) AS progressing
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified >= 500 AND (IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified < 1000, 1, 0)) AS progressing
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified >= 1000, 1, 0)) AS professional
    ,SUM(IF(c.FarmerID,1,0)) AS garden_certified
    ,SUM(IF(c.FarmerID,g.GardenHaUnCertified,0)) AS GardenHaUnCertified_certified
    ,SUM(IF(c.FarmerID,IF(Production > 0, Production, ProductionCalc),0)) AS production_certified
    ,NOW() AS DateUpdated
FROM ktv_farmer_garden_view g
JOIN (SELECT g.FarmerID,g.GardenNr,MAX(g.SurveyNr)SurveyNr FROM ktv_farmer_garden g GROUP BY g.FarmerID,g.GardenNr) z ON z.FarmerID = g.FarmerID AND z.GardenNr = g.GardenNr AND z.SurveyNr = g.SurveyNr
JOIN ktv_farmer_view kcf ON kcf.FarmerID = g.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1 AND kcf.VillageID IS NOT NULL
LEFT JOIN (
SELECT
    c.FarmerID,
    c.GardenNr
FROM ktv_certification c
WHERE
    CURDATE() BETWEEN c.CertificationStart AND c.CertificationEnd
GROUP BY c.FarmerID,
    c.GardenNr
) c ON c.FarmerID = g.FarmerID AND c.GardenNr = g.GardenNr
WHERE
    g.GardenHaUnCertified > 0
GROUP BY
        kcf.CPGid,
        kcf.SubDistrictID";
        $this->db->trans_start();
        // $query1 = $this->db->query('DROP TABLE IF EXISTS dash_main_farmer');
        $query1 = $this->db->query('TRUNCATE TABLE dash_main_farmer');
        $query2 = $this->db->query($sql_farmer);
        // $query1 = $this->db->query('DROP TABLE IF EXISTS dash_main_garden');
        $query1 = $this->db->query('TRUNCATE TABLE dash_main_garden');
        $query2 = $this->db->query($sql_garden);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $results = false;
        else
            $results = true;
        return $results;
    }

    function generateDashMars() {
        $sql_farmer = "INSERT INTO dash_mars_farmer
SELECT
    kcf.CPGid
    ,kcf.VillageID
    ,kcf.SubDistrictID
    ,kcf.SubDistrict
    ,IFNULL(CertificationHolder,'uncertified') AS certification
    ,COUNT(DISTINCT kcf.FarmerID) AS farmer
    ,SUM(IF(kcf.Birthdate IS NULL OR kcf.Birthdate = '0000-00-00',1,0)) AS farmer_unage
    ,SUM(TIMESTAMPDIFF(YEAR,kcf.Birthdate,CURDATE())) AS age_sum
    ,SUM(IF(kcf.Gender=1,1,0)) AS male
    ,SUM(IF(kcf.Gender=2,1,0)) AS female
    ,SUM(IF(kcf.isCertified=1,1,0)) AS certified
    ,COUNT(gnp.FarmerID) AS gnp
    ,COUNT(gfp.FarmerID) AS gfp
    ,SUM(r.wh_netto) AS volume
    ,NOW() AS DateUpdated
FROM ktv_farmer_view kcf
LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
JOIN (
	SELECT
		c.FarmerID, CONCAT(c.CertificationHolderJenis, '_', c.CertificationHolder) AS CertificationHolder
	FROM ktv_certification c
	WHERE
		CURRENT_DATE BETWEEN c.CertificationStart AND c.CertificationEnd
	GROUP BY c.FarmerID
) c ON c.FarmerID = kcf.FarmerID
LEFT JOIN (
    SELECT a.FarmerID FROM (
        SELECT FarmerID
        FROM ktv_cpg_batch_trainings_farmers kcbtf
        LEFT JOIN ktv_cpg_batch_trainings kcbt ON kcbt.CpgBatchTrainingID= kcbtf.CpgBatchTrainingID
        WHERE CPGtrainingsID=2 AND TrainingStart>0
        UNION ALL
        SELECT FarmerID
        FROM ktv_kader_trainings_participants kktp
        LEFT JOIN ktv_kader_trainings kkt ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
        WHERE CPGtrainingsID=2 AND TrainingStart>0
        ) a
    GROUP BY a.FarmerID
    ) gnp ON gnp.FarmerID=kcf.FarmerID
LEFT JOIN (
    SELECT a.FarmerID FROM (
        SELECT FarmerID
        FROM ktv_cpg_batch_trainings_farmers kcbtf
        LEFT JOIN ktv_cpg_batch_trainings kcbt ON kcbt.CpgBatchTrainingID= kcbtf.CpgBatchTrainingID
        WHERE CPGtrainingsID=8 AND TrainingStart>0
        UNION ALL
        SELECT FarmerID
        FROM ktv_kader_trainings_participants kktp
        LEFT JOIN ktv_kader_trainings kkt ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
        WHERE CPGtrainingsID=8 AND TrainingStart>0
        ) a
    GROUP BY a.FarmerID
    ) gfp ON gfp.FarmerID=kcf.FarmerID
LEFT JOIN (
SELECT
	r.farmer_id
	, r.wh_netto
FROM rpt_traceability r
WHERE
	YEAR(r.wh_date) = 2016
) r ON r.farmer_id = kcf.FarmerID
WHERE
    1 = 1
    AND kcf.VillageID IS NOT NULL
    AND kcf.StatusCode = 'active'
    AND kcf.isTrained = 1
    AND kd.DistrictID IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = 9)
GROUP BY
        kcf.CPGid,
        kcf.SubDistrictID,
        certification
";
        $sql_garden = "INSERT INTO dash_mars_garden
SELECT
    kcf.CPGid
    ,kcf.VillageID
    ,kcf.SubDistrictID
    ,kcf.SubDistrict
    ,IFNULL(CertificationHolder,'uncertified') AS certification
    ,COUNT(g.FarmerID) AS garden
    ,SUM(g.GardenHaUnCertified) AS GardenHaUnCertified
    ,SUM(IFNULL(PohonTBM,0))+SUM(IFNULL(PohonTM,0))+SUM(IFNULL(PohonRehab,0)) cocoa_tree
    ,SUM(IFNULL(PohonTM,0)) tm_cocoa_tree
    ,SUM(IF(Production > 0, Production, ProductionCalc)) AS production
    ,SUM(IF(GardenHaUnCertified < 1, 1, 0)) AS small
    ,SUM(IF(GardenHaUnCertified >= 1 AND GardenHaUnCertified < 2, 1, 0)) AS MEDIUM
    ,SUM(IF(GardenHaUnCertified >= 2, 1, 0)) AS large
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified < 500, 1, 0)) AS unprofessional
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified >= 500 AND (IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified < 1000, 1, 0)) AS progressing
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified >= 1000, 1, 0)) AS professional
    ,SUM(IF(c.FarmerID,1,0)) AS garden_certified
    ,SUM(IF(c.FarmerID,g.GardenHaUnCertified,0)) AS GardenHaUnCertified_certified
    ,SUM(IF(c.FarmerID,IF(Production > 0, Production, ProductionCalc),0)) AS production_certified
    ,NOW() AS DateUpdated
FROM ktv_farmer_garden_view g
JOIN (SELECT g.FarmerID,g.GardenNr,MAX(g.SurveyNr)SurveyNr FROM ktv_farmer_garden g GROUP BY g.FarmerID,g.GardenNr) z ON z.FarmerID = g.FarmerID AND z.GardenNr = g.GardenNr AND z.SurveyNr = g.SurveyNr
JOIN ktv_farmer_view kcf ON kcf.FarmerID = g.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1 AND kcf.VillageID IS NOT NULL
LEFT JOIN ktv_village kv ON kv.VillageID = kcf.VillageID
LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
JOIN (
	SELECT
	    c.FarmerID
	    , c.GardenNr
	    , CONCAT(c.CertificationHolderJenis, '_', c.CertificationHolder) AS CertificationHolder
	FROM ktv_certification c
	WHERE
	    CURDATE() BETWEEN c.CertificationStart AND c.CertificationEnd
	GROUP BY c.FarmerID, c.GardenNr
) c ON c.FarmerID = g.FarmerID AND c.GardenNr = g.GardenNr
WHERE
    g.GardenHaUnCertified > 0
    AND kd.DistrictID IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = 9)
GROUP BY
        kcf.CPGid,
        kcf.SubDistrictID,
        certification";
        $sql_mars_volume = "
INSERT INTO mars_actual_volume
SELECT
	d.District
    ,SUM(r.wh_netto) AS volume
FROM ktv_farmer_view kcf
LEFT JOIN ktv_village v ON v.VillageID = kcf.VillageID
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
JOIN (
	SELECT
		c.FarmerID, CONCAT(c.CertificationHolderJenis, '_', c.CertificationHolder) AS CertificationHolder
	FROM ktv_certification c
	WHERE
		CURRENT_DATE BETWEEN c.CertificationStart AND c.CertificationEnd
	GROUP BY c.FarmerID
) c ON c.FarmerID = kcf.FarmerID
LEFT JOIN (
	SELECT
		r.farmer_id
		, r.wh_netto
	FROM rpt_traceability r
	WHERE
		YEAR(r.wh_date) = 2016
) r ON r.farmer_id = kcf.FarmerID
WHERE
    1 = 1
    AND kcf.VillageID IS NOT NULL
    AND kcf.StatusCode = 'active'
    AND kcf.isTrained = 1
    AND d.DistrictID IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = 9)
GROUP BY
        d.District
        ";
        $this->db->trans_start();
        $query1 = $this->db->query('TRUNCATE TABLE dash_mars_farmer');
        $query2 = $this->db->query($sql_farmer);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
        $query1 = $this->db->query('TRUNCATE TABLE dash_mars_garden');
        $query2 = $this->db->query($sql_garden);
        // $query1 = $this->db->query('TRUNCATE TABLE mars_actual_volume');
        // $query2 = $this->db->query($sql_mars_volume);
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $results = false;
        else
            $results = true;
        // exit;
        return $results;
    }

    function generateDashDemographic() {
        $this->demographic = "
         INSERT INTO dash_demographic
         SELECT kcf.CPGid,kd.ProvinceID,kd.DistrictID,ks.SubDistrictID,kcf.VillageID,?,
            COUNT(DISTINCT kcf.FarmerID) Farmer,SUM(TIMESTAMPDIFF(YEAR,kcf.Birthdate,CURDATE())) age,
            SUM(IF(((Birthdate IS NULL AND kcf.FarmerID IS NOT NULL) OR Birthdate='0000-00-00'),1,0)) unage,
            SUM(IF(kcf.Gender='2',1,0)) Female,SUM(IF(kcf.Gender='1',1,0)) Male,
            SUM(IF(kcf.Education > 2, 1, 0)) PassPrimarySchool,SUM(IF(kcf.Education BETWEEN 1 AND 2, 1, 0)) NotPassPrimarySchool,
            COUNT(DISTINCT d.FarmerID) PpiFarmer,COUNT(`National`) NasionalCount,SUM(`National`) Nasional,SUM(`1.25/day`) AS Below125,SUM(`2.5/day`) AS Below25,
            COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 15 AND 34,kcf.FarmerID,NULL)) AS Young,
            SUM(family) Household,
            COUNT(a.FarmerID) AS Household_count,
			COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 15 AND 24,kcf.FarmerID,NULL)) AS Age1524,
			COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 25 AND 34,kcf.FarmerID,NULL)) AS Age2534,
			COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 35 AND 44,kcf.FarmerID,NULL)) AS Age3544,
			COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) BETWEEN 45 AND 54,kcf.FarmerID,NULL)) AS Age4554,
			COUNT(DISTINCT IF(YEAR(NOW()) - YEAR(Birthdate) >= 55,kcf.FarmerID,NULL)) AS Age55,
			SUM(IF(kcf.Education = '1', 1, 0)) NotSchool,
			SUM(IF(kcf.Education = '2', 1, 0)) PrimarySchoolIncomplete,
			SUM(IF(kcf.Education = '3', 1, 0)) PrimarySchoolcompleted,
			SUM(IF(kcf.Education = '4', 1, 0)) JuniorHighSchool,
			SUM(IF(kcf.Education = '5', 1, 0)) SeniorHighSchool,
			SUM(IF(kcf.Education = '6', 1, 0)) TertiarySchool
            ,SUM(Family) AS FamilySum
            ,SUM(IF(family = 1, 1, 0)) AS Family1
            ,SUM(IF(family = 2, 1, 0)) AS Family2
            ,SUM(IF(family = 3, 1, 0)) AS Family3
            ,SUM(IF(family = 4, 1, 0)) AS Family4
            ,SUM(IF(family = 5, 1, 0)) AS Family5
            ,SUM(IF(family >= 6, 1, 0)) AS Family6,
         	SUM(IF(`CookingFuel`=1,1,0)) AS Firewood,SUM(IF(`CookingFuel`=2,1,0)) AS GasOther,
            SUM(IF(`Refrigerator`=2,1,0)) AS RefrigatorYes,SUM(IF(`Refrigerator`=1,1,0)) AS RefrigratorNo,
            SUM(IF(`Motorcycle`=2,1,0)) AS MotorcycleYes,SUM(IF(`Motorcycle`=1,1,0)) AS MotorcycleNo,
            SUM(IF(kcf.MaritalStatus = 1,1,0)) AS maried,
            SUM(IF(kcf.MaritalStatus = 2,1,0)) AS single,
            SUM(IF(kcf.MaritalStatus = 3,1,0)) AS widow,
            now()
         FROM ktv_farmer_view kcf
         LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
         LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID=kv.SubDistrictID
         LEFT JOIN ktv_district kd ON kd.DistrictID=ks.DistrictID
         LEFT JOIN (
            SELECT COUNT(FamilyID)+1 family, FarmerID
            FROM ktv_family kf
            GROUP BY FarmerID
         ) a ON kcf.FarmerID = a.FarmerID
         LEFT JOIN (
            SELECT cpg.*
            FROM ktv_cpg cpg
            JOIN ktv_cpg_batch_trainings cbt ON cbt.CPGid = cpg.CPGid AND TrainingStart > 0
            GROUP BY cpg.CPGid
         ) b ON b.CPGid = kcf.CPGid
         LEFT JOIN (#296203.90
            SELECT ppi.*
            FROM (SELECT `FarmerID`,MAX(`SurveyNr`) `LatestSurveyNr` FROM ktv_ppiscore2012 GROUP BY `FarmerID`) z
            LEFT JOIN ktv_ppiscore2012 ppi ON ppi.`FarmerID` = z.`FarmerID` AND ppi.SurveyNr=z.LatestSurveyNr
            GROUP BY z.FarmerID
         ) d ON d.FarmerID=kcf.FarmerID
         %s
         WHERE
            kcf.VillageID IS NOT NULL AND kcf.StatusCode='active' AND kcf.isTrained = 1
            %s
         GROUP BY kcf.CPGid,VillageID#,tahun
         ORDER BY kcf.CPGid,VillageID
         #) a";
        $this->db->trans_start();
        $query0 = $this->db->query(sprintf($this->truncate, 'dash_demographic'));
        $query2 = $this->db->query(sprintf($this->demographic, '', ''), array(NULL));
        $query_tahun = $this->db->query($this->certified_tahun, array());
        $tahun = $query_tahun->result_array();
        $left_join = "         left JOIN (#1632
            SELECT FarmerID farid,YEAR(ExternalDate) tahun FROM ktv_certification
            WHERE ExternalDate > '0000-00-00'
            GROUP BY FarmerID,YEAR(ExternalDate)
         ) e ON e.farid=kcf.FarmerID";
        for ($i = 0; $i < sizeof($tahun); $i++) {
            $query2 = $this->db->query(sprintf($this->demographic, $left_join, 'AND farid IS NOT NULL AND tahun=?'), array($tahun[$i]['tahun'], $tahun[$i]['tahun']));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $results = false;
        else
            $results = true;
        return $results;
    }

    // saat ini tidak diperlukan lagi, langsung ke table transaction
    function generateDashGroup() {
        $this->group = "
         INSERT INTO dash_group(`Year`, CPGid, ProvinceID, DistrictID, SubDistrictID, VillageID,
            cpg, ada_pengurus,tidak_ada_pengurus, ketua_m, sekretaris_m, bendahara_m, ketua_f, sekretaris_f, bendahara_f,
            coop_id, coop_ketua_m, coop_wakil_ketua_m, coop_sekretaris_m, coop_wakil_sekretaris_m,
            coop_bendahara_m, coop_wakil_bendahara_m, coop_ketua_f, coop_wakil_ketua_f, coop_sekretaris_f, coop_wakil_sekretaris_f,
            coop_bendahara_f, coop_wakil_bendahara_f, pembibitan, kapasitas,DateUpdated)
          SELECT c.`year`,kcf.CPGid,kd.ProvinceID,kd.DistrictID,ks.SubDistrictID,kcf.VillageID,
            a.CPGid cpg,ada_pengurus,tidak_ada_pengurus, ketua_m, sekretaris_m, bendahara_m,ketua_f, sekretaris_f,bendahara_f,
            CoopID,
              SUM(IF(kcs.`Position` = 'Ketua' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) coop_ketua_m,
              SUM(IF(kcs.`Position` = 'Wakil Ketua' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) coop_wakil_ketua_m,
              SUM(IF(kcs.`Position` = 'Sekretaris' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) coop_sekretaris_m,
              SUM(IF(kcs.`Position` = 'Wakil Sekretaris' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) coop_wakil_sekretaris_m,
              SUM(IF(kcs.`Position` = 'Bendahara' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) coop_bendahara_m,
              SUM(IF(kcs.`Position` = 'Wakil Bendahara' AND (kcf.Gender = 1 OR kcs.`StaffGender` = 1), 1, 0)) coop_wakil_bendahara_m,

              SUM(IF(kcs.`Position` = 'Ketua' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) coop_ketua_f,
              SUM(IF(kcs.`Position` = 'Wakil Ketua' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) coop_wakil_ketua_f,
              SUM(IF(kcs.`Position` = 'Sekretaris' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) coop_sekretaris_f,
              SUM(IF(kcs.`Position` = 'Wakil Sekretaris' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) coop_wakil_sekretaris_f,
              SUM(IF(kcs.`Position` = 'Bendahara' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) coop_bendahara_f,
              SUM(IF(kcs.`Position` = 'Wakil Bendahara' AND (kcf.Gender = 2 OR kcs.`StaffGender` = 2), 1, 0)) coop_wakil_bendahara_f,
              COUNT(NurseryID) pembibitan,SUM(Kapasitas*2) kapasitas, now()
          FROM ktv_farmer kcf
          LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
          LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID=kv.SubDistrictID
          LEFT JOIN ktv_district kd ON kd.DistrictID=ks.DistrictID
          LEFT JOIN (
            SELECT a.FarmerID,Gender kelamin,CPGid FROM (
                SELECT FarmerID
                FROM ktv_cpg_batch_trainings_farmers kcbtf
                LEFT JOIN ktv_cpg_batch_trainings kcbt ON kcbt.CpgBatchTrainingID= kcbtf.CpgBatchTrainingID
                WHERE TrainingStart > 0 AND CPGtrainingsID=1
                UNION ALL
                SELECT FarmerID
                FROM ktv_kader_trainings_participants kktp
                LEFT JOIN ktv_kader_trainings kkt ON kkt.CpgKaderTrainingID = kktp.CpgKaderTrainingID
                WHERE TrainingStart > 0 AND CPGtrainingsID=1
            ) a
            LEFT JOIN ktv_farmer kcf ON kcf.FarmerID=a.FarmerID
            GROUP BY a.FarmerID
          ) b ON b.FarmerID=kcf.FarmerID
          LEFT JOIN (
            SELECT cpg.CPGid,cpg.VillageID vill,TahunTerbentuk,
                 IF(`AdaPengurus` = 1,1,NULL) AS ada_pengurus,
                 IF(`AdaPengurus` != 1,1,NULL) AS tidak_ada_pengurus,
                 IF(`Ketua`,1,NULL) AS ketua,
                 IF(`Ketua` AND fa.`Gender` = 1,1,NULL) AS ketua_m,
                 IF(`Ketua` AND fa.`Gender` = 2,1,NULL) AS ketua_f,
                 IF(`Sekretaris`,1,NULL) AS sekretaris,
                 IF(`Sekretaris` AND fb.`Gender` = 1,1,NULL) AS sekretaris_m,
                 IF(`Sekretaris` AND fb.`Gender` = 2,1,NULL) AS sekretaris_f,
                 IF(`Bendahara`,1,NULL) AS bendahara,
                 IF(`Bendahara` AND fc.`Gender` = 1,1,NULL) AS bendahara_m,
                 IF(`Bendahara` AND fc.`Gender` = 2,1,NULL) AS bendahara_f
            FROM ktv_cpg cpg
            JOIN ktv_cpg_batch_trainings cbt ON cbt.CPGid = cpg.CPGid AND TrainingStart > 0
            LEFT JOIN ktv_farmer fa ON fa.`FarmerID` = Ketua
            LEFT JOIN ktv_farmer fb ON fb.`FarmerID` = Sekretaris
            LEFT JOIN ktv_farmer fc ON fc.`FarmerID` = Bendahara
            GROUP BY cpg.CPGid
          ) a ON a.CPGid = kcf.CPGid
         LEFT JOIN ktv_cooperative_staff kcs ON kcf.`FarmerID` = kcs.`FarmerID` -- or kcf.VillageID=kcs.VillageID
         LEFT JOIN ktv_nursery kcn ON kcf.FarmerID = kcn.Responsible
         LEFT JOIN (
            SELECT
                cpg.`CPGid`,
                COUNT(DISTINCT CpgBatchTrainingID) AS total_training,
                VillageID,
                YEAR(MIN(bt.`TrainingStart`)) AS `year`
            FROM `ktv_cpg_batch_trainings` bt
            LEFT JOIN `ktv_cpg` cpg ON bt.`CPGid` = cpg.`CPGid`
            WHERE VillageID AND bt.`TrainingStart` > 0
            GROUP BY cpg.`CPGid`,VillageID
            ORDER BY CpgBatchTrainingID ASC
         ) c ON c.CPGid=a.CPGid AND vill=c.VillageID
         WHERE kcf.VillageID IS NOT NULL AND kcf.StatusCode='active'
          GROUP BY a.CPGid,kcf.VillageID
          ORDER BY TahunTerbentuk,a.CPGid,kcf.VillageID
         # ;) a;";
        $this->db->trans_start();
        $query1 = $this->db->query(sprintf($this->truncate, 'dash_group'));
        $query2 = $this->db->query($this->group);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $results = false;
        else
            $results = true;
        return $results;
    }

    function generateDashGarden() {
        $this->garden = "
			INSERT INTO dash_farm

          SELECT kcf.CPGid,kd.ProvinceID,kd.DistrictID,ks.SubDistrictID,kcf.VillageID,?,?,
         	SUM(count_garden) count_garden,SUM(cocoa_area) cocoa_farm_area,SUM(production) production,
         	COUNT(b.FarmerID) farmer_area,SUM(pohon_kakao) cocoa_tree,SUM(other_tree) other_tree,SUM(pohon_rehab) pohon_rehab,
         	SUM(pohon_tm)pohon_tm,SUM(pohon_tbm)pohon_tbm,SUM(pohon_lain)pohon_lain,SUM(age) age,SUM(count_age) count_age,

         	-- SUM(Yield500)Yield500,SUM(Yield1000)Yield1000,SUM(Yield2000)Yield2000,SUM(YieldAbove2000)YieldAbove2000,
		    SUM(IF(b.productivity <= 350, 1, 0)) '<=350Farmer',
		    SUM(IF(b.productivity>350 AND b.productivity<=500 , 1, 0)) '>350And<=500Farmer',
		    SUM(IF(b.productivity>500 AND b.productivity<=750 , 1, 0)) '>500And<=750Farmer',
		    SUM(IF(b.productivity>750 AND b.productivity<=1000 , 1, 0)) '>750And<=1000Farmer',
		    SUM(IF(b.productivity>1000 AND b.productivity<=1500 , 1, 0)) '>1000And<=1500Farmer',
		    -- SUM(IF(b.productivity>1500 AND b.productivity<=2000 , 1, 0)) '>1500And<=2000Farmer',
		    SUM(IF(b.productivity > 1500,1, 0)) '>1500Farmer',

         	SUM(Marginal)Marginal,SUM(Micro)Micro,SUM(Small)Small,SUM(`Medium`)`Medium`,SUM(Large)Large,
         	SUM(unprofessional),SUM(progressing),SUM(professional),
         	SUM(`owner`) `owner`,SUM(crop_share) crop_share,SUM(rent) rent,SUM(other) other,
         	SUM(no_land_certificate)no_land_certificate,SUM(notarial_deed_bpn)notarial_deed_bpn,SUM(skkt_camat)skkt_camat,
         	SUM(village_lurah)village_lurah,SUM(farmer_him_herself)farmer_him_herself,SUM(family_member)family_member,
         	SUM(other_person)other_person,SUM(do_not_know)do_not_know,

         	NOW()
          FROM ktv_farmer kcf
          LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
          LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID=kv.SubDistrictID
          LEFT JOIN ktv_district kd ON kd.DistrictID=ks.DistrictID
          LEFT JOIN (
         	SELECT kcfg.FarmerID,COUNT(kcfg.GardenNr) count_garden,SUM(IFNULL(kcfg.GardenHaUnCertified,0)) cocoa_area,
         		SUM(IF(Production > 0, Production, ProductionCalc)) production,
         		-- SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         		-- (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         		-- (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))) production,
         		SUM(IF(Production > 0, Production, ProductionCalc))/(SUM(IFNULL(kcfg.GardenHaUnCertified,0))) productivity,
         		-- SUM((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
         		-- (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
         		-- (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/(SUM(IFNULL(kcfg.GardenHaUnCertified,0))) productivity,
         		SUM(IF(GardenHaUnCertified<0.3,1,0)) Marginal,
         		SUM(IF(GardenHaUnCertified>=0.3 AND GardenHaUnCertified<0.6,1,0)) Micro,
         		SUM(IF(GardenHaUnCertified>=0.6 AND GardenHaUnCertified<1,1,0)) AS Small,
         		SUM(IF(GardenHaUnCertified>=1 AND GardenHaUnCertified<2,1,0)) AS `Medium`,
         		SUM(IF(GardenHaUnCertified>=2,1,0)) AS large
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified < 500, 1, 0)) AS unprofessional
    -- ,SUM(IF(((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/GardenHaUnCertified < 500, 1, 0)) AS unprofessional
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified >= 500 AND (IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified < 1000, 1, 0)) AS progressing
    -- ,SUM(IF(((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/GardenHaUnCertified >= 500 AND ((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/GardenHaUnCertified < 1000, 1, 0)) AS progressing
    ,SUM(IF((IF(Production > 0, Production, ProductionCalc))/GardenHaUnCertified >= 1000, 1, 0)) AS professional
    -- ,SUM(IF(((IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
    --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
    --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0)))/GardenHaUnCertified >= 1000, 1, 0)) AS professional
         		,SUM(IFNULL(PohonRehab,0)) pohon_rehab,SUM(IFNULL(PohonTM,0)) pohon_tm,SUM(IFNULL(PohonTBM,0)) pohon_tbm,
         		SUM(IFNULL(ShadeTreesNr,0)) pohon_lain,
         		SUM(IF(`TahunTanamanCocoa` IS NULL OR `TahunTanamanCocoa` = 0,0,YEAR(CURRENT_DATE)-`TahunTanamanCocoa`)) age,
         		COUNT(IF(`TahunTanamanCocoa` IS NULL OR `TahunTanamanCocoa` = 0,NULL,YEAR(CURRENT_DATE)-`TahunTanamanCocoa`)) count_age,
         		SUM(IFNULL(PohonTBM,0)+IFNULL(PohonTM,0)+IFNULL(PohonRehab,0)) pohon_kakao,
         		SUM(KelapaNr+PinangNr+KaretNr+CengkehNr+SawitNr+ArenNr+PalaNr+KemiriNr+
         			MahoniNr+JatiNr+BitiNr+UruNr+JabonNr+
         			JackFruitNr+PisangNr+RambutanNr+ManggaNr+LangsatNr+DurianNr+AlpukatNr+SukunNr+PepayaNr+ManggaNr+JerukNr+
         			GamalNr+LamtoroNr+PetaiNr+JengkolNr+
         			ShadeLainNr) other_tree
            FROM ktv_farmer_garden_view kcfg
         	LEFT JOIN (SELECT FarmerID,GardenNr,MAX(SurveyNr) survey_nr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) a ON
         		kcfg.FarmerID=a.FarmerID AND kcfg.GardenNr=a.GardenNr and kcfg.SurveyNr = a.survey_nr
         	WHERE GardenHaUnCertified>0 %s
         	GROUP BY kcfg.FarmerID
          ) b ON b.FarmerID=kcf.FarmerID
          LEFT JOIN (
         	SELECT kcfg.FarmerID,
         		SUM(IF(OwnershipCocoa=1,1,0)) AS `owner`,SUM(IF(OwnershipCocoa=2,1,0)) AS crop_share,
         		SUM(IF(OwnershipCocoa=3,1,0)) AS rent,SUM(IF(OwnershipCocoa=4,1,0)) AS other
         		,SUM(IF(LandCertificate=1,1,0)) AS no_land_certificate
         		,SUM(IF(LandCertificate=2,1,0)) AS notarial_deed_bpn
         		,SUM(IF(LandCertificate=3,1,0)) AS skkt_camat
         		,SUM(IF(LandCertificate=4,1,0)) AS village_lurah
         		,SUM(IF(LandOwner=1,1,0)) AS farmer_him_herself
         		,SUM(IF(LandOwner=2,1,0)) AS family_member
         		,SUM(IF(LandOwner=3,1,0)) AS other_person
         		,SUM(IF(LandOwner=4,1,0)) AS do_not_know
         	FROM ktv_farmer_garden kcfg
         	LEFT JOIN (SELECT FarmerID,GardenNr,MAX(SurveyNr) survey_nr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) a ON
         		kcfg.FarmerID=a.FarmerID AND kcfg.GardenNr=a.GardenNr and kcfg.SurveyNr = a.survey_nr
         	WHERE OwnershipCocoa IS NOT NULL AND LandCertificate IS NOT NULL %s #and GardenHaUnCertified>0
         	GROUP BY kcfg.FarmerID
          ) c ON c.FarmerID=kcf.FarmerID
          -- LEFT JOIN (
          --   SELECT a.FarmerID,
          --   COUNT(CASE
          --           WHEN
          --               (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
          --       ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
          --       ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
          --       ))/(GardenHaUncertified))<500
          --   THEN 1 END) AS Yield500,
          --   COUNT(CASE
          --           WHEN
          --               (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
          --       ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
          --       ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
          --       ))/(GardenHaUncertified))>=500 AND (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
          --       ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
          --       ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
          --       ))/(GardenHaUncertified))<1000
          --   THEN 1 END) AS Yield1000,
          --   COUNT(CASE
          --           WHEN
          --               (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
          --       ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
          --       ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
          --       ))/(GardenHaUncertified))>=1000 AND (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
          --       ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
          --       ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
          --       ))/(GardenHaUncertified))<2000
          --   THEN 1 END) AS Yield2000,
          --   COUNT(CASE
          --           WHEN
          --               (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
          --       ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
          --       ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
          --       ))/(GardenHaUncertified))>=2000
          --   THEN 1 END) AS YieldAbove2000
          --   FROM
          --       ktv_farmer_garden kcfg
          --            LEFT JOIN (SELECT FarmerID,GardenNr,MAX(SurveyNr) survey_nr FROM ktv_farmer_garden GROUP BY FarmerID,GardenNr) a ON
          --               kcfg.FarmerID=a.FarmerID AND kcfg.GardenNr=a.GardenNr AND kcfg.SurveyNr = a.survey_nr
          --           INNER JOIN
          --       ktv_farmer_view kcf ON kcf.FarmerID = kcfg.FarmerID
          --       WHERE (((IFNULL(PanenTrekMonths, 0) * IFNULL(PanenTrekPanenMonth, 0) * IFNULL(PanenTrekKg, 0)
          --       ) + (IFNULL(PanenBiasaMonths, 0) * IFNULL(PanenBiasaPanenMonth, 0) * IFNULL(PanenBiasaKg, 0)
          --       ) + (IFNULL(PanenRayaMonths, 0) * IFNULL(PanenRayaPanenMonth, 0) * IFNULL(PanenRayaKg, 0)
          --       ))/(GardenHaUncertified))>0 AND kcf.VillageID AND kcf.StatusCode = 'active' %s
          --       GROUP BY kcfg.FarmerID
          -- ) d ON d.FarmerID=kcf.FarmerID
         LEFT JOIN (#1632
         	SELECT FarmerID farid,YEAR(ExternalDate) tahun FROM ktv_certification
         	WHERE ExternalDate > '0000-00-00' AND ? BETWEEN YEAR(CertificationStart) AND YEAR(CertificationEnd)
         	GROUP BY FarmerID
         ) e ON e.farid=kcf.FarmerID
          WHERE kcf.VillageID IS NOT NULL
            AND kcf.StatusCode='active'
            AND kcf.isTrained=1
            %s
          GROUP BY kcf.CPGid,kcf.VillageID
         #) a";
        $this->db->trans_start();
        $query1 = $this->db->query(sprintf($this->truncate, 'dash_farm'));
        $jenis = array(
            'baseline',
            'postline',
            'latest'
        );
        $where = array(
            'AND kcfg.SurveyNr = 0',
            'AND kcfg.SurveyNr = a.survey_nr AND kcfg.SurveyNr > 0',
            'AND kcfg.SurveyNr = a.survey_nr'
        );
        $query_tahun = $this->db->query($this->certified_tahun, array());
        $tahun = $query_tahun->result_array();
        for ($i = 0; $i < sizeof($jenis); $i++) {
            $query2 = $this->db->query(sprintf($this->garden, $where[$i], $where[$i], $where[$i], ''), array($jenis[$i], null, ''));
            for ($j = 0; $j < sizeof($tahun); $j++) {
                $query2 = $this->db->query(sprintf($this->garden, $where[$i], $where[$i], $where[$i], ' AND farid IS NOT NULL'), array($jenis[$i], $tahun[$j]['tahun'], $tahun[$j]['tahun']));
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $results = false;
        } else {
            $results = true;
        }
        return $results;
    }

    function generateDashAgri() {
        $this->agri = "
         INSERT INTO dash_agri(CPGid, ProvinceID, DistrictID, SubDistrictID, VillageID, Kompos, Hectare, Fertilizer, PestisidaYes, PestisidaNo,
         PestisidaOrganic, PestisidaNonOrganic, ChemicalYes, ChemicalNo, OrganicYes, OrganicNo, ProtectiveYes, ProtectiveNo, BuangKebun, BuangGunakan,
         BuangKubur, BuangBakar, BuangLain, PestisidaRumah, PestisidaKhusus, PestisidaLuar, PestisidaKebun, PestisidaLain, KomposKandang, KomposCair,
         KomposGranula, ApplicationUrea, ApplicationTSP, ApplicationNPK, ApplicationKCl, ApplicationZA, TBMApplication, TMApplication, TRApplication,
         PenyakitKanker, PenyakitBusuk, PenyakitUpas, PenyakitAkar, PenyakitVSD, PenyakitAntraknose, HamaBPK, HamaHelopeltis, HamaBatang, HerbisidaYes,
         HerbisidaNo, InsectisidaYes, InsectisidaNo, FungisidaYes, FungisidaNo, herbicide_paraquat, herbicide_glyphosate, herbicide_24d,
         insecticide_banned, insecticide_watchlist, insecticide_allowed, fungicide_banned, fungicide_watchlist, fungicide_allowed, NOGAP_NOFung,
         NOGAP_Fung, GAP_NOFung, GAP_Fung,DateUpdated)
         SELECT kcf.CPGid,kd.ProvinceID,kd.DistrictID,ks.SubDistrictID,kcf.VillageID,
            SUM(Kompos) Kompos,SUM(Hectare) Hectare,SUM(Fertilizer) Fertilizer,
            SUM(PestisidaYes) PestisidaYes,SUM(PestisidaNo) PestisidaNo,SUM(PestisidaOrganic) PestisidaOrganic,
            SUM(PestisidaNonOrganic) PestisidaNonOrganic,
            SUM(ChemicalYes)ChemicalYes,SUM(ChemicalNo)ChemicalNo,SUM(OrganicYes) OrganicYes,SUM(OrganicNo)OrganicNo,
            SUM(ProtectiveYes)ProtectiveYes,SUM(ProtectiveNo)ProtectiveNo,
            SUM(BuangKebun)BuangKebun,SUM(BuangGunakan)BuangGunakan,SUM(BuangKubur)BuangKubur,SUM(BuangBakar)BuangBakar,
            SUM(BuangLain)BuangLain,
            SUM(PestisidaRumah)PestisidaRumah,SUM(PestisidaKhusus)PestisidaKhusus,SUM(PestisidaLuar)PestisidaLuar,
            SUM(PestisidaKebun)PestisidaKebun,SUM(PestisidaLain)PestisidaLain,
            SUM(KomposKandang)KomposKandang,SUM(KomposCair)KomposCair,SUM(KomposGranula)KomposGranula,
            SUM(ApplicationUrea)ApplicationUrea,SUM(ApplicationTSP)ApplicationTSP,SUM(ApplicationNPK)ApplicationNPK,
            SUM(ApplicationKCl)ApplicationKCl,SUM(ApplicationZA)ApplicationZA,
            SUM(TBMApplication) AS TBMApplication,SUM(TMApplication) AS TMApplication, SUM(TRApplication) AS TRApplication,
            SUM(PenyakitKanker) AS PenyakitKanker,SUM(PenyakitBusuk) AS PenyakitBusuk,
            SUM(PenyakitUpas) AS PenyakitUpas,SUM(PenyakitAkar) AS PenyakitAkar,
            SUM(PenyakitVSD) AS PenyakitVSD,SUM(PenyakitAntraknose) AS PenyakitAntraknose,
            SUM(HamaBPK) AS HamaBPK,SUM(HamaHelopeltis) AS HamaHelopeltis,SUM(HamaBatang) AS HamaBatang,
            SUM(HerbisidaYes) AS HerbisidaYes,SUM(HerbisidaNo) AS HerbisidaNo,
            SUM(InsectisidaYes) AS InsectisidaYes,SUM(InsectisidaNo) AS InsectisidaNo,
            SUM(FungisidaYes) AS FungisidaYes,SUM(FungisidaNo) AS FungisidaNo,
            SUM(herbicide_paraquat)herbicide_paraquat,SUM(herbicide_glyphosate)herbicide_glyphosate,SUM(herbicide_24d)herbicide_24d,
            SUM(insecticide_banned)insecticide_banned,SUM(insecticide_watchlist)insecticide_watchlist,SUM(insecticide_allowed)insecticide_allowed,
            SUM(fungicide_banned)fungicide_banned,SUM(fungicide_watchlist)fungicide_watchlist,SUM(fungicide_allowed)fungicide_allowed,
            SUM(NOGAP_NOFung)NOGAP_NOFung,SUM(NOGAP_Fung)NOGAP_Fung,SUM(GAP_NOFung)GAP_NOFung,SUM(GAP_Fung)GAP_Fung,
            NOW()
         FROM ktv_farmer kcf
         LEFT JOIN ktv_village kv ON kv.VillageID=kcf.VillageID
         LEFT JOIN ktv_subdistrict ks ON ks.SubDistrictID=kv.SubDistrictID
         LEFT JOIN ktv_district kd ON kd.DistrictID=ks.DistrictID
         LEFT JOIN (
            SELECT
                kcfg.FarmerID,
                SUM((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`)) Kompos,
                SUM(`GardenHaUnCertified`) Hectare,
                SUM(((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
                +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000) Fertilizer
            FROM `ktv_farmer_garden_view` kcfg
            INNER JOIN `ktv_farmer` kcf ON (`kcfg`.`FarmerID` = kcf.`FarmerID`)
            WHERE  `GardenHaUnCertified` > 0 AND kcf.StatusCode = 'active'
                AND (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 50
                AND (IF(Production > 0, Production, ProductionCalc))>0
                -- AND (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)>0
                AND (((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))>0
                OR ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)>0
                OR ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)> 0)
                AND ((((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
                +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/`GardenHaUnCertified`) < 12000
                AND `FrequentFertilizationKompos`*`DoseFertilizerKompos` <20
            GROUP BY kcfg.FarmerID
         ) a ON a.FarmerID=kcf.FarmerID
         LEFT JOIN (
            SELECT
                kcfg.FarmerID,
                SUM(IF(Herbisida = 1 OR Insectisida = 1 OR Fungisida = 1,1,0)) AS PestisidaYes,
                SUM(IF(Herbisida = 2 AND Insectisida = 2 AND Fungisida = 2,1,0)) AS PestisidaNo,
                SUM(IF((Insectisida = 1 AND Insectisida11 = 1) OR (Fungisida = 1 AND Fungisida11 = 1),1,0)) AS PestisidaOrganic,
                SUM(IF((Insectisida = 2 OR Insectisida11 = 2) OR (Fungisida = 2 OR Fungisida11 = 2),1,0)) AS PestisidaNonOrganic,
                SUM(IF(TidakMemakaiKimia = 1,1,0)) AS ChemicalYes,SUM(IF(TidakMemakaiKimia = 2,1,0)) AS ChemicalNo,
                SUM(IF(PakaiKompos = 1,1,0)) AS OrganicYes,SUM(IF(PakaiKompos = 2,1,0)) AS OrganicNo,
                SUM(IF(APD = 1,1,0)) AS ProtectiveYes,SUM(IF(APD = 2,1,0)) AS ProtectiveNo,
                SUM(IF(BuangKemasanPestisida = 1,1,0)) AS BuangKebun,SUM(IF(BuangKemasanPestisida = 2,1,0)) AS BuangGunakan,
                SUM(IF(BuangKemasanPestisida = 3,1,0)) AS BuangKubur,SUM(IF(BuangKemasanPestisida = 4,1,0)) AS BuangBakar,
                SUM(IF(BuangKemasanPestisida = 5,1,0)) AS BuangLain,
                SUM(IF(TempatSimpanPestisida = 1,1,0)) AS PestisidaRumah,SUM(IF(TempatSimpanPestisida = 2,1,0)) AS PestisidaKhusus,
                SUM(IF(TempatSimpanPestisida = 3,1,0)) AS PestisidaLuar,SUM(IF(TempatSimpanPestisida = 4,1,0)) AS PestisidaKebun,
                SUM(IF(TempatSimpanPestisida = 5,1,0)) AS PestisidaLain,
                SUM(IF(PenyakitKanker = 1,1,0)) AS PenyakitKanker,SUM(IF(PenyakitBusuk = 1,1,0)) AS PenyakitBusuk,
                SUM(IF(PenyakitUpas = 1,1,0)) AS PenyakitUpas,SUM(IF(PenyakitAkar = 1,1,0)) AS PenyakitAkar,
                SUM(IF(PenyakitVSD = 1,1,0)) AS PenyakitVSD,SUM(IF(PenyakitAntraknose = 1,1,0)) AS PenyakitAntraknose,
                SUM(IF(HamaBPK = 1,1,0)) AS HamaBPK,SUM(IF(HamaHelopeltis = 1,1,0)) AS HamaHelopeltis,
                SUM(IF(HamaBatang = 1,1,0)) AS HamaBatang,
                SUM(IF(Herbisida = 1,1,0)) AS HerbisidaYes,SUM(IF(Herbisida = 2,1,0)) AS HerbisidaNo,
                SUM(IF(Insectisida = 1,1,0)) AS InsectisidaYes,SUM(IF(Insectisida = 2,1,0)) AS InsectisidaNo,
                SUM(IF(Fungisida = 1,1,0)) AS FungisidaYes,SUM(IF(Fungisida = 2,1,0)) AS FungisidaNo,
                SUM(IF(Herbisida1=1 OR Herbisida2=1 OR Herbisida3=1 OR Herbisida4=1 OR Herbisida5=1 OR Herbisida6=1 OR Herbisida7=1 OR
                    Herbisida8=1 OR Herbisida9=1 OR Herbisida10=1 OR Herbisida11=1 OR Herbisida12=1 OR Herbisida13=1 OR
                    Herbisida14=1 OR Herbisida15=1 OR Herbisida16=1 OR Herbisida17=1 OR Herbisida18=1 OR Herbisida19=1 OR
                    Herbisida20=1 OR Herbisida21=1 OR Herbisida22=1 OR Herbisida23=1 OR Herbisida24=1 OR Herbisida25=1 OR
                    Herbisida26=1 OR Herbisida27=1 OR Herbisida28=1 OR Herbisida29=1,1,0)) AS herbicide_all,
                SUM(IF(Herbisida5=1 OR Herbisida9=1 OR Herbisida10=1 OR Herbisida11=1 OR Herbisida12=1 OR Herbisida13=1 OR
                    Herbisida18=1 OR Herbisida25=1 OR Herbisida26=1 OR Herbisida27=1 OR Herbisida28=1 OR
                    Herbisida29=1, 1, 0)) AS herbicide_paraquat,
                SUM(IF(Herbisida1=1 OR Herbisida2=1 OR Herbisida3=1 OR Herbisida4=1 OR Herbisida6=1 OR Herbisida7=1 OR Herbisida8=1 OR
                    Herbisida14=1 OR Herbisida15=1 OR Herbisida16=1 OR Herbisida17=1 OR Herbisida19=1 OR Herbisida20=1 OR
                    Herbisida21=1 OR Herbisida23=1 OR Herbisida24=1,1,0)) AS herbicide_glyphosate,
                SUM(IF(Herbisida14=1 OR Herbisida15=1 OR Herbisida16=1 OR Herbisida21=1 OR Herbisida22=1,1,0)) AS herbicide_24d,
                SUM(IF(Insectisida1=1 OR Insectisida2=1 OR Insectisida3=1 OR Insectisida4=1 OR Insectisida5=1 OR Insectisida6=1 OR
                    Insectisida7=1 OR Insectisida8=1 OR Insectisida9=1 OR Insectisida10=1 OR Insectisida12=1 OR Insectisida13=1 OR
                    Insectisida14=1 OR Insectisida15=1 OR Insectisida16=1 OR Insectisida17=1 OR Insectisida18=1 OR Insectisida19=1 OR
                    Insectisida20=1 OR Insectisida11=1 OR Insectisida21=1 OR Insectisida22=1 OR Insectisida23=1 OR
                    Fungisida9=1,1,0)) AS insecticide_all,
                SUM(IF(Insectisida12=1 OR Insectisida20=1 OR Insectisida21=1 OR Insectisida22=1 OR Insectisida23=1,1,0)) insecticide_banned,
                SUM(IF(Insectisida1=1 OR Insectisida2=1 OR Insectisida5=1 OR Insectisida6=1 OR Insectisida7=1 OR Insectisida8=1 OR
                    Insectisida9=1 OR Insectisida10=1 OR Insectisida15=1 OR Insectisida19=1 OR Fungisida9=1,1,0)) insecticide_watchlist,
                SUM(IF(Insectisida3=1 OR Insectisida4=1 OR Insectisida13=1 OR Insectisida14=1 OR Insectisida16=1 OR Insectisida17=1 OR
                    Insectisida18=1 OR Insectisida11=1,1,0)) AS insecticide_allowed,
                SUM(IF(Fungisida1=1 OR Fungisida2=1 OR Fungisida3=1 OR Fungisida4=1 OR Fungisida5=1 OR Fungisida6=1 OR Fungisida7=1 OR
                    Fungisida10=1 OR Fungisida12=1 OR Fungisida11=1 OR Fungisida13=1,1,0)) AS fungicide_all,
                SUM(IF(Fungisida13=1,1,0)) AS fungicide_banned,
                SUM(IF(Fungisida2=1 OR Fungisida5=1 OR Fungisida6=1 OR Fungisida10=1,1,0)) AS fungicide_watchlist,
                SUM(IF(Fungisida1=1 OR Fungisida3=1 OR Fungisida4=1 OR Fungisida7=1 OR Fungisida12=1 OR Fungisida11=1,1,0)) fungicide_allowed,
                COUNT(DISTINCT IF((CASE WHEN ((HowToCleanSkin = 2 OR HowToCleanSkin = 5 OR HowToCleanSkin = 6 OR HowToCleanSkin = 7) AND
                (HarvestAwal = 1 AND HarvestHama = 1) AND PruningPlants = 1) THEN 1 ELSE 0 END) = 0 AND Insectisida =0 AND Fungisida = 0, kcfg.FarmerID , NULL)) AS NOGAP_NOFung,
                COUNT(DISTINCT IF((CASE WHEN ((HowToCleanSkin = 2 OR HowToCleanSkin = 5 OR HowToCleanSkin = 6 OR HowToCleanSkin = 7) AND
                (HarvestAwal = 1 AND HarvestHama = 1) AND PruningPlants = 1) THEN 1 ELSE 0 END) = 0 AND (Insectisida =1 OR Fungisida = 1), kcfg.FarmerID , NULL)) AS NOGAP_Fung,
                COUNT(DISTINCT IF((CASE WHEN ((HowToCleanSkin = 2 OR HowToCleanSkin = 5 OR HowToCleanSkin = 6 OR HowToCleanSkin = 7) AND
                (HarvestAwal = 1 AND HarvestHama = 1) AND PruningPlants = 1) THEN 1 ELSE 0 END) = 1 AND (Insectisida =0 OR Fungisida = 0), kcfg.FarmerID , NULL)) AS GAP_NOFung,
                COUNT(DISTINCT IF((CASE WHEN ((HowToCleanSkin = 2 OR HowToCleanSkin = 5 OR HowToCleanSkin = 6 OR HowToCleanSkin = 7) AND
                (HarvestAwal = 1 AND HarvestHama = 1) AND PruningPlants = 1) THEN 1 ELSE 0 END) = 1 AND (Insectisida =1 OR Fungisida = 1), kcfg.FarmerID , NULL)) AS GAP_Fung
            FROM`ktv_farmer_garden` kcfg
            JOIN (SELECT kcfg.`FarmerID`,kcfg.`GardenNr`,MAX(kcfg.`SurveyNr`) AS SurveyNr
                FROM `ktv_farmer_garden` kcfg GROUP BY kcfg.`FarmerID`,kcfg.`GardenNr`
                ) r ON kcfg.`FarmerID` = r.FarmerID AND kcfg.`GardenNr` = r.GardenNr AND kcfg.`SurveyNr` = r.SurveyNr
            WHERE
                kcfg.GardenHaUncertified > 0
            GROUP BY kcfg.FarmerID
         ) b ON b.FarmerID=kcf.FarmerID
         LEFT JOIN (
            SELECT kcfg.FarmerID,
                SUM(CASE WHEN FrKomposKandang > 0 THEN 1 ELSE 0 END) AS KomposKandang,
                SUM(CASE WHEN FrKomposCair > 0 THEN 1 ELSE 0 END) AS KomposCair,
                SUM(CASE WHEN FrKomposGranula > 0 THEN 1 ELSE 0 END) AS KomposGranula,
                SUM(CASE WHEN FrUrea > 1 THEN 1 ELSE 0 END) AS ApplicationUrea,
                SUM(CASE WHEN FrTsp > 1 THEN 1 ELSE 0 END) AS ApplicationTSP,
                SUM(CASE WHEN FrNpk > 1 THEN 1 ELSE 0 END) AS ApplicationNPK,
                SUM(CASE WHEN FrKcl > 1 THEN 1 ELSE 0 END) AS ApplicationKCl,
                SUM(CASE WHEN FrZa > 1 THEN 1 ELSE 0 END) AS ApplicationZA,
                SUM(PupukTBM) AS TBMApplication,SUM(PupukTM) AS TMApplication, SUM(PupukTR) AS TRApplication
            FROM ktv_farmer_garden kcfg
            INNER JOIN (SELECT FarmerID,MAX(SurveyNr) LatestSurveyNr FROM ktv_farmer_garden GROUP BY FarmerID) z ON
                z.FarmerID = kcfg.FarmerID AND z.LatestSurveyNr=kcfg.SurveyNr
            WHERE
                kcfg.GardenHaUncertified > 0
            GROUP BY kcfg.FarmerID
         ) c ON c.FarmerID=kcf.FarmerID
         WHERE kcf.VillageID IS NOT NULL AND kcf.StatusCode='active' AND kcf.isTrained = 1
         GROUP BY kcf.CPGid,kcf.VillageID
         #) a";
        $this->db->trans_start();
        $query1 = $this->db->query(sprintf($this->truncate, 'dash_agri'));
        $query2 = $this->db->query($this->agri);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $results = false;
        else
            $results = true;
        return $results;
    }

    function getDataPhoto() {
        $sql = "SELECT a.FarmerID, b.Photo, b.IsActive, b.PhotoID
				FROM
				ktv_farmer a
				LEFT JOIN ktv_photo_history b ON a.FarmerID=b.FarmerID
				WHERE (a.Photo IS NULL OR a.Photo ='') AND b.Photo !='' AND b.IsActive = '0' LIMIT 100";
        $query = $this->db->query($sql);
        //echo $query->num_rows(); exit;
        $sukses = 0;
        $gagal = 0;
        foreach ($query->result() as $row) {
            $FarmerID = $row->FarmerID;
            $Photo = $row->Photo;
            $PhotoID = $row->PhotoID;
            $this->db->trans_start();
            $sql1 = "UPDATE ktv_farmer SET Photo=? WHERE FarmerID=?";
            $this->db->query($sql1, array($Photo, $FarmerID));

            $sql2 = "UPDATE ktv_photo_history SET IsActive='0' WHERE FarmerID=?";
            $this->db->query($sql2, array($FarmerID));

            $sql3 = "UPDATE ktv_photo_history SET IsActive='1' WHERE PhotoID=?";
            $this->db->query($sql3, array($PhotoID));

            $Path = base_url() . 'images/Photo/' . $Photo;
            $sql4 = "INSERT INTO cek_photo (FarmerID,Path,Status) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE Path=?, Status=?";
            $this->db->query($sql4, array($FarmerID, $Path, 'ada', $Path, 'ada'));

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $gagal++;
            } else {
                $sukses++;
            }
        }
        return array(
            'sukses' => $sukses,
            'gagal' => $gagal
        );
    }

    function getDataPhotoBase64() {
        $sql = "SELECT a.FarmerID,a.Photo,a.Photo_base64
				FROM ktv_farmer a
				LEFT JOIN ktv_photo_history b ON b.FarmerID=a.FarmerID AND b.Photo=a.Photo_base64 AND b.IsActive='1'
				WHERE a.Photo!=a.Photo_base64 AND b.FarmerID!=''";
        $query = $this->db->query($sql);
        //echo $query->num_rows(); exit;
        foreach ($query->result() as $row) {
            $FarmerID = $row->FarmerID;
            $Photo = $row->Photo;
            $sql1 = "UPDATE ktv_photo_history SET Photo=? WHERE FarmerID=? AND IsActive='1'";
            $this->db->query($sql1, array($Photo, $FarmerID));
        }
        return true;
    }

    public function generateDashNutrition() {
        $this->db->trans_start(FALSE);
        $sql_gap = <<<SQL
INSERT INTO dash_gnp
SELECT
    kcf.VillageID
    ,kcf.CPGid
    ,kcf.SubDistrictID
    ,kcf.SubDistrict
    ,COUNT(DISTINCT kcf.FarmerID) AS farmer
    ,SUM(IF((s.PetaniKakao = 1 AND kcf.Gender = 1) OR (s.PetaniKakao = 2 AND kf.AnggotaGender = 1), 1, 0)) AS male
    ,SUM(IF((s.PetaniKakao = 1 AND kcf.Gender = 2) OR (s.PetaniKakao = 2 AND kf.AnggotaGender = 2), 1, 0)) AS female
    ,SUM(TIMESTAMPDIFF(YEAR,kcf.Birthdate,CURDATE())) AS sum_farmer_age
    ,NOW() AS DateUpdated
FROM (
    SELECT
        tf.FarmerID,
        tf.PetaniKakao,
        tf.FamilyID
    FROM ktv_cpg_batch_trainings_farmers tf
    JOIN ktv_cpg_batch_trainings bt ON bt.CpgBatchTrainingID = tf.CpgBatchTrainingID AND bt.CPGtrainingsID=2
    UNION ALL
    SELECT
        tf.FarmerID,
        tf.PetaniKakao,
        tf.FamilyID
    FROM ktv_kader_trainings_participants tf
    JOIN ktv_kader_trainings bt ON bt.CpgKaderTrainingID = tf.CpgKaderTrainingID AND bt.CPGtrainingsID=2
) s
JOIN ktv_farmer_view kcf ON s.FarmerID = kcf.FarmerID
LEFT JOIN ktv_family kf ON s.FamilyID = kf.FamilyID
GROUP BY kcf.CPGid,kcf.SubDistrictID;
SQL;
        $this->db->query("TRUNCATE dash_gnp;");
        $result_gap = $this->db->query($sql_gap);
        $sql_nutrition = <<<SQL
INSERT INTO dash_nutrition
SELECT
    kcf.VillageID,
    kcf.CPGid,
    kcf.SubDistrictID,
    kcf.SubDistrict,
    COUNT(kn.FarmerID) AS farmer,
    COUNT(DISTINCT kcf.FarmerID) AS farmer_distinct,
    AVG(IF(Score > 0 AND Score < 10,Score,NULL)) AS IDDS,
    SUM(IF(Score > 0 AND Score < 10,Score,0)) AS score_total,
    COUNT(IF(Score > 0 AND Score < 10,kn.FarmerID, NULL)) farmer_idds,
    SUM(IF(Score > 0 AND Score < 10 AND kcf.Gender='1',Score,0)) AS score_male,
    SUM(IF(Score > 0 AND Score < 10 AND kcf.Gender='1',1,0)) AS male_idds,
    SUM(IF(Score > 0 AND Score < 10 AND kcf.Gender='2',Score,0)) AS score_female,
    SUM(IF(Score > 0 AND Score < 10 AND kcf.Gender='2',1,0)) AS female_idds,
    SUM(IF(IsFamilyGarden = '1' AND KebunPanjang > 0,1,0) + IF(IsCommmercialGarden = '1' AND ComKebunPanjang > 0,1,0)) AS GardenYes,
    AVG(IF(KebunPanjang * KebunLebar <= 100,KebunPanjang * KebunLebar,NULL)) AS avgGardenSizeMod,
    SUM(IF(KebunPanjang * KebunLebar <= 100,KebunPanjang * KebunLebar,IF(KebunPanjang * KebunLebar IS NULL,0,100))) +
    	SUM(IF(ComKebunPanjang * ComKebunLebar <= 10000,ComKebunPanjang * ComKebunLebar,IF(ComKebunPanjang * ComKebunLebar IS NULL,0,10000))) AS sumGardenSizeMod,
    COUNT(IF(KebunPanjang * KebunLebar <= 100,kn.FarmerID,NULL)) AS farmerMod,
    SUM((CASE
        WHEN KebunPanjang * KebunLebar <= 100 THEN KebunPanjang * KebunLebar
        WHEN KebunPanjang IS NULL THEN 0
        ELSE NULL
    END) ) / COUNT(DISTINCT kcf.FarmerID) AS GardenSizeMod_Farmer,
    SUM(KbBayam) AS Spinach, SUM(`KbCabai`) AS Chilli, SUM(`KbKacangPanjang`) AS LongBean, SUM(`KbKangkung`) AS WaterCress, SUM(`KbSawi`) AS Mustard, SUM(`KbTerong`) AS Eggplant, SUM(`KbTomat`) AS Tomato, 
    SUM(`KbKambing`) AS Goat, SUM(`KbSapi`) AS Cow, SUM(`KbBebek`) AS Duck, SUM(`KbAyam`) AS Chicken, SUM(`KbIkan`) AS Fish,SUM(`KbDomba`) AS Sheep, SUM(`KbKerbau`) AS Buffalo, SUM(`KbBabi`) AS Pig, 
	SUM(IF(IsFamilyGarden = '1' AND KebunPanjang > 0,1,0) + IF(IsCommmercialGarden = '1' AND ComKebunPanjang > 0,1,0)) AS established_garden,
    SUM(IF(HaveFishPond = '1',1,0)) AS fish_pond,
    SUM(IF(FishPondArea IS NOT NULL AND FishPondArea != 0, 1, 0)) AS count_farmer_fish_pond_area,
    SUM(IF(FishPondArea IS NOT NULL AND FishPondArea != 0, FishPondArea, 0)) AS sum_fish_pond_area,
    NOW() AS DateUpdated
FROM (
    SELECT
        n.*
    FROM ktv_nutrition n
    JOIN (
        SELECT
            n.FarmerID, MAX(n.SurveyNr) AS SurveyNr
        FROM ktv_nutrition n
        GROUP BY FarmerID
    ) z ON z.FarmerID = n.FarmerID AND z.SurveyNr = n.SurveyNr
    WHERE
        n.FarmerID
) kn
JOIN ktv_farmer_view kcf ON kn.FarmerID = kcf.FarmerID AND kcf.StatusCode = 'active'
GROUP BY kcf.CPGid,kcf.SubDistrictID;
SQL;
        $this->db->query("TRUNCATE dash_nutrition;");
        $result_nutrition = $this->db->query($sql_nutrition);
        $this->db->trans_complete();
        $msg = array();
        if ($result_gap == false) {
            $msg = "GAP Failed";
        }
        if ($result_nutrition == false) {
            $msg = "Nutrition Failed";
        }
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function setFarmerTrained() {
        $this->db->trans_start(FALSE);
        // reset status
        $this->db->query("UPDATE ktv_farmer f SET f.isTrained = 0");
        // set status
        $sql = "UPDATE
ktv_farmer f, (
SELECT
    tf.FarmerID
FROM ktv_cpg_batch_trainings_farmers tf
JOIN ktv_cpg_batch_trainings bt ON bt.CpgBatchTrainingID = tf.CpgBatchTrainingID
WHERE
    bt.CPGtrainingsID = 1
GROUP BY tf.FarmerID
UNION ALL
SELECT
    tp.FarmerID
FROM ktv_kader_trainings_participants tp
JOIN ktv_kader_trainings t ON t.CpgKaderTrainingID = tp.CpgKaderTrainingID
WHERE
    t.CPGtrainingsID = 1
GROUP BY tp.FarmerID
) t
SET f.isTrained = 1
WHERE t.FarmerID = f.FarmerID
        ";
        $this->db->query($sql);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function setFarmerCertified() {
        $this->db->trans_start(FALSE);
        // reset status
        $this->db->query("UPDATE ktv_farmer f SET f.isCertified = 0");
        // set status
        $sql = "UPDATE
ktv_farmer f, (
SELECT
    c.FarmerID
FROM ktv_certification c
WHERE
    1 = 1
    AND ExternalDate>'0000-00-00'
    AND CURRENT_DATE BETWEEN c.CertificationStart AND c.CertificationEnd
GROUP BY FarmerID
) c
SET
    f.isCertified = 1
WHERE c.FarmerID = f.FarmerID
        ";
        $this->db->query($sql);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function setFarmerLoanPass() {
        $this->db->trans_start(FALSE);
        // reset status
        $this->db->query("UPDATE ktv_farmer f SET f.isLoanPassed = 0");
        // set status
        $sql = "UPDATE
    ktv_farmer f,(
    SELECT
        g.FarmerID,
        SUM(GardenHaUncertified) AS GardenHaUncertified,
        SUM(
            IF(Production > 0, Production, ProductionCalc)
        )AS Prod
        -- SUM(
        --     (IFNULL(PanenTrekMonths,0)*IFNULL(PanenTrekPanenMonth,0)*IFNULL(PanenTrekKg,0))+
        --     (IFNULL(PanenBiasaMonths,0)*IFNULL(PanenBiasaPanenMonth,0)*IFNULL(PanenBiasaKg,0))+
        --     (IFNULL(PanenRayaMonths,0)*IFNULL(PanenRayaPanenMonth,0)*IFNULL(PanenRayaKg,0))
        -- )AS Prod
    FROM ktv_farmer_garden_view g
    JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY FarmerID, GardenNr) z ON g.FarmerID = z.FarmerID AND g.GardenNr = z.GardenNr AND g.SurveyNr = z.SurveyNr
    JOIN (
        SELECT
            f.FarmerID, f.LoanYesNo, f.NeedLoan
        FROM ktv_farmer_financial f
        JOIN (SELECT    f.FarmerID, MAX(f.SurveyNr) AS SurveyNr FROM ktv_farmer_financial f GROUP BY f.FarmerID) z ON z.FarmerID = f.FarmerID
        WHERE 1 = 1
            AND f.NeedLoan = 1
    ) fin ON fin.FarmerID = g.FarmerID
    GROUP BY g.FarmerID
    HAVING
        Prod >= 500
        AND GardenHaUncertified >= 0.5
) kcfg
SET f.isLoanPassed = 1
WHERE
    kcfg.FarmerID = f.FarmerID
        ";
        // AND f.LearningContractStatus = 1
        $this->db->query($sql);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function generateDashFinance() {
        $sql = "INSERT INTO dash_finance
SELECT
    CPGid
    ,VillageID
    ,SubDistrict
    ,SubDistrictID
   ,COUNT(DISTINCT kcf.FarmerID) gfp
   ,COUNT(DISTINCT IF(Gender = 1,kcf.FarmerID,NULL)) male
   ,COUNT(DISTINCT IF(Gender = 2,kcf.FarmerID,NULL)) female
   ,SUM(IF(fc.FarmerID,1,0)) AS fin
   ,SUM(IF(Account = 1,1,0)) AS account
   ,SUM(IF(MoneyUsageTabung OR MoneyUsageInvestasi OR MoneyUsageEmas,1,0)) AS saving
   ,SUM(IF(LoanYesNo IN (1,3,4),1,0)) AS loan
   ,SUM(IF(MoneyUsageTabung,1,0)) AS saving_money
   ,SUM(IF(MoneyUsageInvestasi,1,0)) AS saving_invest
   ,SUM(IF(MoneyUsageEmas,1,0)) AS saving_gold
   ,SUM(IF(MoneyUsageHarian OR MoneyUsageKonsumsi,1,0)) AS saving_no
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitBank=1,1,0)) AS loan_yes_current
   ,SUM(IF(LoanYesNo = 2,1,0)) AS loan_no
   ,SUM(IF(LoanYesNo = 3,1,0)) AS loan_yes_past
   ,SUM(IF(LoanYesNo = 1 AND (LoanUnitBank=0 OR LoanUnitBank IS NULL),1,0)) AS loan_yes_past_current
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitKeluarga,1,0)) AS loan_from_family
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitBank,1,0)) AS loan_from_bank
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitTengkulak,1,0)) AS loan_from_trader
   ,SUM(IF(LoanYesNo = 1 AND LoanUnitKoperasi,1,0)) AS loan_from_coops
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanInvestasiKebun,1,0)) AS loan_for_farm
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanInvestasiLain,1,0)) AS loan_for_other
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanSekolah,1,0)) AS loan_for_school
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanHarian,1,0)) AS loan_for_daily
   ,SUM(IF(LoanYesNo = 1 AND UsageCurrentLoanDarurat,1,0)) AS loan_for_emergency
   ,SUM(IF(Account = 1 AND DepositWithdrawnMoneyLast12m = 1,1,0)) AS account_active
   ,SUM(IF(Account = 2,1,0)) AS account_no
   ,SUM(IF(Account = 1 AND DepositWithdrawnMoneyLast12m = 2,1,0)) AS account_inactive
   ,SUM(IF(Account = 1 AND (SavingUnitBank=1 OR SavingUnitKoperasi=1),1,0)) AS product_saving
   ,SUM(IF(Account = 1 AND (LoanUnitBank=1 OR LoanUnitKoperasi=1),1,0)) AS product_loan
   ,SUM(IF(Account = 1 AND ((LoanUnitBank=1 OR LoanUnitKoperasi=1) AND (SavingUnitBank=1 OR SavingUnitKoperasi=1)),1,0)) AS product_saving_loan
   ,SUM(IF(NeedLoan = 1,1,0)) AS need_loan
   ,SUM(IF(NeedLoan = 2,1,0)) AS need_loan_no
   ,SUM(IF(FutureReasonSekolah OR FutureReasonInvestasiKebun OR FutureReasonInvestasiLain OR FutureReasonDarurat OR FutureReasonKesehatan, 1, 0)) AS future_count
   ,SUM(IF(FutureReasonSekolah,1,0)) AS future_school
   ,SUM(IF(FutureReasonInvestasiKebun,1,0)) AS future_invest_farm
   ,SUM(IF(FutureReasonInvestasiLain,1,0)) AS future_invest_other
   ,SUM(IF(FutureReasonDarurat,1,0)) AS future_emergency
   ,SUM(IF(FutureReasonKesehatan,1,0)) AS future_health
   ,SUM(IF(ValueCocoaFarm = 1 ,1,0)) AS value_10
   ,SUM(IF(ValueCocoaFarm = 2 ,1,0)) AS value_10_20
   ,SUM(IF(ValueCocoaFarm = 3 ,1,0)) AS value_20_50
   ,SUM(IF(ValueCocoaFarm = 4 ,1,0)) AS value_50_100
   ,SUM(IF(ValueCocoaFarm = 5 ,1,0)) AS value_100_200
   ,SUM(IF(ValueCocoaFarm = 6 ,1,0)) AS value_200
   ,SUM(IF(ValueCocoaFarm IS NULL OR ValueCocoaFarm = 7  ,1,0)) AS value_0
   ,NOW() AS DateUpdated
FROM (
SELECT
*
FROM (
SELECT
      kcfg.FarmerID,
      kcf.CPGid,
      VillageID,
      SubDistrict,
      SubDistrictID,
      IF(kcfg.PetaniKakao = 1,kcf.`Gender`,kf.`AnggotaGender`) AS Gender
FROM
      `ktv_cpg_batch_trainings_farmers` kcfg
   JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   LEFT JOIN ktv_family kf ON kf.`FamilyID` = kcfg.`FamilyID`
 WHERE VillageID AND kcf.StatusCode = 'active'
   AND CPGtrainingsID=8
GROUP BY kcf.FarmerID
UNION ALL
SELECT
      kcfg.FarmerID,
      kcf.CPGid,
      VillageID,
      SubDistrict,
      SubDistrictID,
      IF(kcfg.PetaniKakao = 1,kcf.`Gender`,kf.`AnggotaGender`) AS Gender
FROM
      `ktv_kader_trainings_participants` kcfg
   JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
   LEFT JOIN ktv_farmer_view kcf ON kcf.FarmerID = kcfg.`FarmerID`
   LEFT JOIN ktv_family kf ON kf.`FamilyID` = kcfg.`FamilyID`
WHERE VillageID AND kcf.StatusCode = 'active'
   AND CPGtrainingsID=8
GROUP BY kcf.FarmerID
) t
GROUP BY FarmerID
) kcf
LEFT JOIN `ktv_farmer_financial` fc ON fc.`FarmerID` = kcf.`FarmerID` AND fc.`SurveyNr` = 0
GROUP BY CPGid,SubDistrict
";
        $this->db->trans_start(FALSE);
        $this->db->query("TRUNCATE TABLE dash_finance;");
        $result = $this->db->query($sql);
        $this->db->trans_complete();
        $msg = array();
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function generateDashSurvey() {

        $this->db->trans_start(FALSE);
        $this->db->query("TRUNCATE TABLE dash_survey_garden");
        $sql = "INSERT INTO `dash_survey_garden`
SELECT
    kcf.CPGid
    ,kcf.VillageID
    ,kcf.SubDistrictID
    ,kcf.SubDistrict
    ,YEAR(kcfg.DateCollection) AS `year`
    ,COUNT(DISTINCT IF(kcfg.baseline = 1, kcfg.FarmerID,NULL)) AS farmer_baseline
    ,COUNT(DISTINCT IF(kcfg.postline = 1, kcfg.FarmerID,NULL)) AS farmer_postline
    ,COUNT(IF(kcfg.baseline = 1, kcfg.FarmerID,NULL)) AS garden_baseline
    ,COUNT(IF(kcfg.postline = 1, kcfg.FarmerID,NULL)) AS garden_postline
    ,SUM(IF(kcfg.baseline = 1,production,0)) AS production_baseline
    ,SUM(IF(kcfg.postline = 1,production,0)) AS production_postline
    ,SUM(IF(kcfg.baseline = 1,ha,0)) AS ha_baseline
    ,SUM(IF(kcfg.postline = 1,ha,0)) AS ha_postline
    ,SUM(IF(kcfg.baseline = 1,tree,0)) AS tree_baseline
    ,SUM(IF(kcfg.postline = 1,tree,0)) AS tree_postline
    ,NOW() AS DateUpdated
FROM (
SELECT
    kcfg.FarmerID, kcfg.GardenNr, kcfg.SurveyNr,z.SurveyNr AS LatestSurveyNr
    ,IF(kcfg.SurveyNr = 0,1,0) AS baseline
    ,IF(kcfg.SurveyNr > 0 AND z.SurveyNr IS NOT NULL,1,0) AS postline
    ,IF(z.SurveyNr IS NOT NULL,1,0) AS latest
    ,kcfg.DateCollection
    ,kcfg.GardenHaUnCertified AS ha
    ,SUM(
      IF(Production > 0, Production, ProductionCalc)
      ) AS production
    ,(SUM(IFNULL(PohonTBM,0))+SUM(IFNULL(PohonTM,0))+SUM(IFNULL(PohonRehab,0))) AS tree
FROM ktv_farmer_garden_view kcfg
LEFT JOIN (SELECT FarmerID, GardenNr, MAX(SurveyNr) AS SurveyNr FROM ktv_farmer_garden GROUP BY FarmerID, GardenNr) z ON kcfg.FarmerID = z.FarmerID AND kcfg.GardenNr = z.GardenNr AND kcfg.SurveyNr = z.SurveyNr
WHERE
    kcfg.GardenHaUnCertified > 0
    AND (kcfg.SurveyNr = 0 OR z.SurveyNr IS NOT NULL)
GROUP BY kcfg.FarmerID, kcfg.GardenNr, kcfg.SurveyNr
HAVING production > 0
) kcfg
JOIN ktv_farmer_view kcf ON kcfg.FarmerID = kcf.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
GROUP BY kcf.CPGid, kcf.SubDistrictID,`year`
        ";
        $result = $this->db->query($sql);
        $this->db->query("TRUNCATE TABLE dash_survey_nutrition");
        $sql = "INSERT INTO dash_survey_nutrition
SELECT
    kcf.CPGid
    ,kcf.VillageID
    ,kcf.SubDistrictID
    ,kcf.SubDistrict
    ,YEAR(kn.InterviewDate) AS `year`
    ,SUM(IF(baseline,1,0)) AS nutrition_baseline
    ,SUM(IF(postline,1,0)) AS nutrition_postline
    ,SUM(IF(baseline AND is_score, Score, 0)) AS score_sum_baseline
    ,SUM(IF(baseline AND is_score, 1, 0)) AS score_count_baseline
    ,SUM(IF(baseline AND is_score AND kcf.Gender = 1, Score, 0)) AS score_male_sum_baseline
    ,SUM(IF(baseline AND is_score AND kcf.Gender = 1, 1, 0)) AS score_male_count_baseline
    ,SUM(IF(baseline AND is_score AND kcf.Gender = 2, Score, 0)) AS score_female_sum_baseline
    ,SUM(IF(baseline AND is_score AND kcf.Gender = 2, 1, 0)) AS score_female_count_baseline
    ,SUM(IF(postline AND is_score, Score, 0)) AS score_sum_postline
    ,SUM(IF(postline AND is_score, 1, 0)) AS score_count_postline
    ,SUM(IF(postline AND is_score AND kcf.Gender = 1, Score, 0)) AS score_male_sum_postline
    ,SUM(IF(postline AND is_score AND kcf.Gender = 1, 1, 0)) AS score_male_count_postline
    ,SUM(IF(postline AND is_score AND kcf.Gender = 2, Score, 0)) AS score_female_sum_postline
    ,SUM(IF(postline AND is_score AND kcf.Gender = 2, 1, 0)) AS score_female_count_postline
    ,SUM(IF(latest AND is_score, Score, 0)) AS score_sum_latest
    ,SUM(IF(latest AND is_score, 1, 0)) AS score_count_latest
    ,SUM(IF(latest AND is_score AND kcf.Gender = 1, Score, 0)) AS score_male_sum_latest
    ,SUM(IF(latest AND is_score AND kcf.Gender = 1, 1, 0)) AS score_male_count_latest
    ,SUM(IF(latest AND is_score AND kcf.Gender = 2, Score, 0)) AS score_female_sum_latest
    ,SUM(IF(latest AND is_score AND kcf.Gender = 2, 1, 0)) AS score_female_count_latest
    ,SUM(IF(baseline AND is_kebun, KebunPanjang*KebunLebar,0)) AS luas_sum_baseline
    ,SUM(IF(baseline AND is_kebun, 1,0)) AS luas_count_baseline
    ,SUM(IF(postline AND is_kebun, KebunPanjang*KebunLebar,0)) AS luas_sum_postline
    ,SUM(IF(postline AND is_kebun, 1,0)) AS luas_count_postline
    ,SUM(IF(latest AND is_kebun, KebunPanjang*KebunLebar,0)) AS luas_sum_latest
    ,SUM(IF(latest AND is_kebun, 1,0)) AS luas_count_latest
    ,NOW() AS DateUpdated
FROM (
SELECT
    kn.FarmerID
    ,kn.SurveyNr
    ,kn.InterviewDate
    ,kn.Score
    ,IF(kn.`Score` > 0 AND kn.`Score` < 10,1,0) AS is_score
    ,kn.KebunPanjang
    ,IF(kn.`KebunPanjang`<=10 AND kn.`KebunLebar`<=10,1,0) AS is_kebun
    ,kn.KebunLebar
    ,IF(kn.SurveyNr = 0,1,0) AS baseline
    ,IF(kn.SurveyNr > 0 AND z.SurveyNr IS NOT NULL,1,0) AS postline
    ,IF(z.SurveyNr IS NOT NULL,1,0) AS latest
FROM ktv_nutrition kn
LEFT JOIN (SELECT FarmerID, MAX(SurveyNr) AS SurveyNr FROM ktv_nutrition GROUP BY FarmerID) z ON z.FarmerID = kn.FarmerID AND z.SurveyNr = kn.SurveyNr
HAVING (baseline OR latest OR postline)
) kn
JOIN ktv_farmer_view kcf  ON kn.`FarmerID` = kcf.`FarmerID` AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
GROUP BY kcf.CPGid ,kcf.SubDistrictID,`year`
        ";
        $result = $this->db->query($sql);
        $this->db->query("TRUNCATE TABLE dash_survey_ppi");
        $sql = "INSERT INTO dash_survey_ppi
SELECT
    kcf.CPGid
    ,kcf.VillageID
    ,kcf.SubDistrictID
    ,kcf.SubDistrict
    ,SUM(IF(baseline,1,0)) AS `count_baseline`
    ,SUM(IF(baseline,`National`,0)) AS `National_sum_baseline`
    ,COUNT(IF(baseline,`National`,NULL)) AS `National_count_baseline`
    ,SUM(IF(baseline,`1.25/day`,0)) AS '1.25_baseline'
    ,SUM(IF(baseline,`2.5/day`,0)) AS '2.5_baseline'
    ,SUM(IF(postline,1,0)) AS `count_postline`
    ,SUM(IF(postline,`National`,0)) AS `National_sum_postline`
    ,COUNT(IF(postline,`National`,NULL)) AS `National_count_postline`
    ,SUM(IF(postline,`1.25/day`,0)) AS '1.25_postline'
    ,SUM(IF(postline,`2.5/day`,0)) AS '2.5_postline'
    ,NOW() AS DateUpdated
FROM (
SELECT
    kp.FarmerID
    ,IF(kp.SurveyNr = 0,1,0) AS baseline
    ,IF(kp.SurveyNr > 0 AND z.SurveyNr IS NOT NULL,1,0) AS postline
    ,IF(z.SurveyNr IS NOT NULL,1,0) AS latest
    ,`National`
    ,`1.25/day`
    ,`2.5/day`
FROM ktv_ppiscore2012 kp
LEFT JOIN (SELECT FarmerID, MAX(SurveyNr) AS SurveyNr FROM ktv_ppiscore2012 GROUP BY FarmerID) z ON kp.FarmerID = z.FarmerID AND kp.SurveyNr = z.SurveyNr
HAVING (baseline OR latest OR postline)
) kp
JOIN ktv_farmer_view kcf ON kcf.FarmerID = kp.FarmerID AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
GROUP BY kcf.CPGid ,kcf.SubDistrictID
        ";
        $result = $this->db->query($sql);
        $this->db->query("TRUNCATE TABLE dash_survey_finance");
        $sql = "INSERT INTO dash_survey_finance
SELECT
    kcf.CPGid
    , kcf.VillageID
    , kcf.SubDistrictID
    , kcf.SubDistrict
	, COUNT(b.FarmerID) AS gfp_baseline
	, COUNT(p.FarmerID) AS gfp_postline
	, SUM(IF(b.Account = 1,1,0)) AS bank_account_baseline
	, SUM(IF(p.Account = 1,1,0)) AS bank_account_postline
	, SUM(IF(b.DepositWithdrawnMoneyLast12m = 1,1,0)) AS saving_baseline
	, SUM(IF(p.DepositWithdrawnMoneyLast12m = 1,1,0)) AS saving_postline
	, NOW() AS DateUpdated
FROM ktv_farmer_view kcf
LEFT JOIN (
	SELECT
		f.FarmerID
		, f.Account
		, f.DepositWithdrawnMoneyLast12m
	FROM ktv_farmer_financial f
	WHERE
		f.SurveyNr = 0
) b ON kcf.FarmerID = b.FarmerID
LEFT JOIN (
	SELECT
		f.FarmerID
		, f.Account
		, f.DepositWithdrawnMoneyLast12m
	FROM ktv_farmer_financial f
	JOIN (
		SELECT f.FarmerID, MAX(f.SurveyNr) AS SurveyNr FROM ktv_farmer_financial f GROUP BY f.FarmerID
	) z ON f.FarmerID = z.FarmerID AND f.SurveyNr = z.SurveyNr AND f.SurveyNr > 0
) p ON kcf.FarmerID = p.FarmerID
WHERE 1 = 1
AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
GROUP BY kcf.CPGid ,kcf.SubDistrictID
        ";
        $result = $this->db->query($sql);
        $this->db->trans_complete();
        $msg = array();
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function generateDashTraining() {
        $sql = "INSERT INTO dash_training
SELECT
   ? AS `type`
   ,`year`
   ,kcf.CPGid
   ,kcf.VillageID
   ,kcf.SubDistrictID
   ,kcf.SubDistrict
   ,COUNT(DISTINCT IF(CPGtrainingsID=1,t.FarmerID,NULL)) gap
   ,COUNT(DISTINCT IF(CPGtrainingsID=2,t.FarmerID,NULL)) gnp
   ,COUNT(DISTINCT IF(CPGtrainingsID=8,t.FarmerID,NULL)) gfp
   ,COUNT(DISTINCT IF(CPGtrainingsID=9,t.FarmerID,NULL)) gep
   ,COUNT(DISTINCT IF(CPGtrainingsID=14,t.FarmerID,NULL)) gbp
   ,COUNT(DISTINCT IF(CPGtrainingsID=15,t.FarmerID,NULL)) agap
   ,COUNT(DISTINCT IF(CPGtrainingsID=18,t.FarmerID,NULL)) gsp
   ,NOW() AS DateUpdated
FROM (
    SELECT *
    FROM (
        --source--
    ) m WHERE FarmerID GROUP BY FarmerID, CPGtrainingsID
) t
JOIN ktv_farmer_view kcf ON kcf.FarmerID = t.FarmerID
WHERE
   VillageID IS NOT NULL
   AND kcf.StatusCode = 'active'
GROUP BY kcf.CPGid, kcf.SubDistrictID,`year`
";
        $source_farmer = "
        SELECT
           kcfg.FarmerID,
           CPGtrainingsID,
           YEAR(t.`TrainingStart`) AS `year`
        FROM
           `ktv_cpg_batch_trainings_farmers` kcfg
        JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
        WHERE TrainingStart > 0
        ";
        $source_kader = "
        SELECT
           kcfg.FarmerID,
           CPGtrainingsID,
           YEAR(t.`TrainingStart`) AS `year`
        FROM
           `ktv_kader_trainings_participants` kcfg
        LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
        WHERE TrainingStart > 0
        ";
        $this->db->trans_start(FALSE);
        $this->db->query("TRUNCATE TABLE dash_training;");
        $result = $this->db->query(str_replace('--source--', $source_farmer, $sql), array('farmer'));
        $result = $this->db->query(str_replace('--source--', $source_kader, $sql), array('kader'));
        $result = $this->db->query(str_replace('--source--', $source_farmer . ' UNION ALL ' . $source_kader, $sql), array('all'));
        $this->db->trans_complete();
        $msg = array();
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function generateDashTrainingMaster() {
        $sql = "INSERT INTO dash_training_master
SELECT
   `type`
   ,`year`
   ,kcf.VillageID
   ,COUNT(DISTINCT kcf.participant) AS `all`
   ,COUNT(DISTINCT IF(CPGtrainingsID=1,kcf.participant,NULL)) gap
   ,COUNT(DISTINCT IF(CPGtrainingsID=1 AND kcf.type='program',kcf.participant,NULL)) gap_program
   ,COUNT(DISTINCT IF(CPGtrainingsID=1 AND kcf.type='private',kcf.participant,NULL)) gap_private
   ,COUNT(DISTINCT IF(CPGtrainingsID=1 AND kcf.type='extension',kcf.participant,NULL)) gap_extension
   ,COUNT(DISTINCT IF(CPGtrainingsID=2,kcf.participant,NULL)) gnp
   ,COUNT(DISTINCT IF(CPGtrainingsID=2 AND kcf.type='program',kcf.participant,NULL)) gnp_program
   ,COUNT(DISTINCT IF(CPGtrainingsID=2 AND kcf.type='private',kcf.participant,NULL)) gnp_private
   ,COUNT(DISTINCT IF(CPGtrainingsID=2 AND kcf.type='extension',kcf.participant,NULL)) gnp_extension
   ,COUNT(DISTINCT IF(CPGtrainingsID=8,kcf.participant,NULL)) gfp
   ,COUNT(DISTINCT IF(CPGtrainingsID=8 AND kcf.type='program',kcf.participant,NULL)) gfp_program
   ,COUNT(DISTINCT IF(CPGtrainingsID=8 AND kcf.type='private',kcf.participant,NULL)) gfp_private
   ,COUNT(DISTINCT IF(CPGtrainingsID=8 AND kcf.type='extension',kcf.participant,NULL)) gfp_extension
   ,COUNT(DISTINCT IF(CPGtrainingsID=9,kcf.participant,NULL)) gep
   ,COUNT(DISTINCT IF(CPGtrainingsID=9 AND kcf.type='program',kcf.participant,NULL)) gep_program
   ,COUNT(DISTINCT IF(CPGtrainingsID=9 AND kcf.type='private',kcf.participant,NULL)) gep_private
   ,COUNT(DISTINCT IF(CPGtrainingsID=9 AND kcf.type='extension',kcf.participant,NULL)) gep_extension
   ,COUNT(DISTINCT IF(CPGtrainingsID=14,kcf.participant,NULL)) gbp
   ,COUNT(DISTINCT IF(CPGtrainingsID=14 AND kcf.type='program',kcf.participant,NULL)) gbp_program
   ,COUNT(DISTINCT IF(CPGtrainingsID=14 AND kcf.type='private',kcf.participant,NULL)) gbp_private
   ,COUNT(DISTINCT IF(CPGtrainingsID=14 AND kcf.type='extension',kcf.participant,NULL)) gbp_extension
   ,COUNT(DISTINCT IF(CPGtrainingsID=15,kcf.participant,NULL)) agap
   ,COUNT(DISTINCT IF(CPGtrainingsID=15 AND kcf.type='program',kcf.participant,NULL)) agap_program
   ,COUNT(DISTINCT IF(CPGtrainingsID=15 AND kcf.type='private',kcf.participant,NULL)) agap_private
   ,COUNT(DISTINCT IF(CPGtrainingsID=15 AND kcf.type='extension',kcf.participant,NULL)) agap_extension
   ,COUNT(DISTINCT IF(CPGtrainingsID=18,kcf.participant,NULL)) gsp
   ,COUNT(DISTINCT IF(CPGtrainingsID=18 AND kcf.type='program',kcf.participant,NULL)) gsp_program
   ,COUNT(DISTINCT IF(CPGtrainingsID=18 AND kcf.type='private',kcf.participant,NULL)) gsp_private
   ,COUNT(DISTINCT IF(CPGtrainingsID=18 AND kcf.type='extension',kcf.participant,NULL)) gsp_extension
   ,COUNT(DISTINCT IF(CPGtrainingsID=17,kcf.participant,NULL)) cst
   ,COUNT(DISTINCT IF(CPGtrainingsID=17 AND Gender = 'm',kcf.participant,NULL)) cst_male
   ,COUNT(DISTINCT IF(CPGtrainingsID=17 AND Gender = 'f',kcf.participant,NULL)) cst_female
   ,NOW() AS DateUpdated
FROM (
    SELECT
       tp.ParticipantPersonID AS participant,
       p.Gender,
       -- p.type,
       s.ObjType AS `type`,
       t.DistrictID AS VillageID,
       CPGtrainingsID,
       YEAR(t.`TrainingStart`) AS `year`
    FROM
       `ktv_master_trainings_participants` tp
    LEFT JOIN `ktv_master_trainings` t ON t.MasterTrainingID = tp.MasterTrainingID
    LEFT JOIN ktv_staffs s ON s.StaffID = tp.ParticipantNewStaffID
    LEFT JOIN ktv_persons p ON p.PersonID = ParticipantPersonID
    WHERE TrainingStart > 0 AND ParticipantPersonID > 0
) kcf
WHERE
   VillageID IS NOT NULL
GROUP BY kcf.VillageID, `type`, `year`;
";
        $this->db->trans_start(FALSE);
        $this->db->query("TRUNCATE TABLE dash_training_master;");
        $result = $this->db->query($sql);
        $this->db->trans_complete();
        $msg = array();
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function generateDashKPI() {
        $sql = "INSERT INTO dash_kpi
SELECT
    v.VillageID
    , sd.SubDistrictID
    , sd.SubDistrict
    , t.gap_basic
    , t.gap_basic_male
    , t.gap_basic_female
    , t.gap_adv
    , t.gap_adv_male
    , t.gap_adv_female
    , t.gnp
    , t.gnp_male
    , t.gnp_female
    , t.gfp
    , t.gfp_male
    , t.gfp_female
    , t.gep
    , t.gep_male
    , t.gep_female
    , t.gbp
    , t.gbp_male
    , t.gbp_female
    , t.gsp
    , t.gsp_male
    , t.gsp_female
    , c.cpg
    , f.farmer
    , f.farmer_male
    , f.farmer_female
    , f.farmer_certified
    , f.farmer_certified_male
    , f.farmer_certified_female
    , NOW()
FROM ktv_village v
LEFT JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
-- LEFT JOIN ktv_district d ON d.DistrictID = sd.DistrictID
-- LEFT JOIN ktv_province p ON p.ProvinceID = d.ProvinceID
LEFT JOIN (
    SELECT
        c.VillageID
        , COUNT(c.CPGid) AS cpg
    FROM ktv_cpg c
    WHERE c.OwnerClientID = 1
    GROUP BY c.VillageID
) c ON c.VillageID = v.VillageID
LEFT JOIN (
    SELECT
        f.VillageID
        , COUNT(DISTINCT f.FarmerID) AS farmer
        , COUNT(DISTINCT IF(f.Gender = 1,f.FarmerID,NULL)) AS farmer_male
        , COUNT(DISTINCT IF(f.Gender = 2,f.FarmerID,NULL)) AS farmer_female
        , COUNT(DISTINCT IF(f.isCertified = 1,f.FarmerID, NULL)) AS farmer_certified
        , COUNT(DISTINCT IF(f.isCertified = 1 AND f.Gender = 1,f.FarmerID, NULL)) AS farmer_certified_male
        , COUNT(DISTINCT IF(f.isCertified = 1 AND f.Gender = 2,f.FarmerID, NULL)) AS farmer_certified_female    
    FROM ktv_farmer f
    WHERE f.StatusCode = 'active'
    GROUP BY f.VillageID
) f ON f.VillageID = v.VillageID
LEFT JOIN (
    SELECT
        f.VillageID
        , c.CPGid
        , COUNT(DISTINCT IF(CPGtrainingsID=1,t.FarmerID,NULL)) gap_basic
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 1 AND t.Gender = 1,t.FarmerID,NULL)) AS gap_basic_male
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 1 AND t.Gender = 2,t.FarmerID,NULL)) AS gap_basic_female
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 15,t.FarmerID,NULL)) AS gap_adv
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 15 AND t.Gender = 1,t.FarmerID,NULL)) AS gap_adv_male
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 15 AND t.Gender = 2,t.FarmerID,NULL)) AS gap_adv_female
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 2,t.FarmerID,NULL)) AS gnp
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 2 AND t.Gender = 1,t.FarmerID,NULL)) AS gnp_male
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 2 AND t.Gender = 2,t.FarmerID,NULL)) AS gnp_female
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 8,t.FarmerID,NULL)) AS gfp
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 8 AND t.Gender = 1,t.FarmerID,NULL)) AS gfp_male
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 8 AND t.Gender = 2,t.FarmerID,NULL)) AS gfp_female
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 9,t.FarmerID,NULL)) AS gep
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 9 AND t.Gender = 1,t.FarmerID,NULL)) AS gep_male
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 9 AND t.Gender = 2,t.FarmerID,NULL)) AS gep_female
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 14 AND (t.subTopic IN (5,50) OR t.subTopic IS NULL),t.FarmerID,NULL)) AS gbp
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 14 AND (t.subTopic IN (5,50) OR t.subTopic IS NULL) AND t.Gender = 1,t.FarmerID,NULL)) AS gbp_male
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 14 AND (t.subTopic IN (5,50) OR t.subTopic IS NULL) AND t.Gender = 2,t.FarmerID,NULL)) AS gbp_female
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 18,t.FarmerID,NULL)) AS gsp
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 18 AND t.Gender = 1,t.FarmerID,NULL)) AS gsp_male
        , COUNT(DISTINCT IF(t.CPGtrainingsID = 18 AND t.Gender = 2,t.FarmerID,NULL)) AS gsp_female
    FROM (
        SELECT
           kcfg.FarmerID
           , CPGtrainingsID
           , IFNULL(kf.AnggotaGender, kcf.Gender) AS gender
           , GROUP_CONCAT(st.SubCpgTrainingsID) AS subTopic
        FROM
           `ktv_cpg_batch_trainings_farmers` kcfg
        JOIN `ktv_cpg_batch_trainings` t ON t.CpgBatchTrainingID= kcfg.`CpgBatchTrainingID`
        LEFT JOIN ktv_cpg_batch_trainings_sub_topics st ON st.CpgBatchTrainingID = kcfg.CpgBatchTrainingID
        LEFT JOIN ktv_farmer kcf ON kcf.FarmerID = kcfg.FarmerID
        LEFT JOIN ktv_family kf ON kf.FamilyID = kcfg.FamilyID
        WHERE TrainingStart > 0
        GROUP BY kcfg.CpgBatchTrainingsFarmerID
        UNION ALL
        SELECT
           kcfg.FarmerID
           , CPGtrainingsID
           , IFNULL(kf.AnggotaGender, kcf.Gender) AS gender
           , GROUP_CONCAT(st.SubCpgTrainingsID) AS subTopic
        FROM
           `ktv_kader_trainings_participants` kcfg
        LEFT JOIN `ktv_kader_trainings` t ON t.`CpgKaderTrainingID` = kcfg.`CpgKaderTrainingID`
        LEFT JOIN ktv_kader_trainings_sub_topics st ON st.CpgKaderTrainingID = kcfg.CpgKaderTrainingID
        LEFT JOIN ktv_farmer kcf ON kcf.FarmerID = kcfg.FarmerID
        LEFT JOIN ktv_family kf ON kf.FamilyID = kcfg.FamilyID
        WHERE TrainingStart > 0
        GROUP BY kcfg.CpgKaderTrainingsFarmerID
    ) t
    JOIN ktv_farmer f ON f.FarmerID = t.FarmerID AND f.StatusCode = 'active'
    JOIN ktv_cpg c ON c.CPGid = f.CPGid AND c.OwnerClientID = 1
    WHERE t.FarmerID
    GROUP BY f.VillageID
) t ON t.VillageID = v.VillageID
GROUP BY v.VillageID
";
        $this->db->trans_start(FALSE);
        $this->db->query("TRUNCATE TABLE dash_kpi;");
        $result = $this->db->query($sql);
        $this->db->trans_complete();
        $msg = array();
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function generateDashBank() {
        $sql = "
INSERT INTO dash_bank
SELECT
	kcf.VillageID
	, kcf.CPGid
	, kcf.SubDistrictID
	, kcf.SubDistrict
	, COUNT(DISTINCT kcf.FarmerID) AS farmer
	, COUNT(DISTINCT IF(kcf.isLoanPassed = 1,kcf.FarmerID,NULL)) AS farmer_loan_pass
	, COUNT(DISTINCT IF(kcf.isLoanPassed = 1 AND d.FarmerID IS NOT NULL,kcf.FarmerID,NULL)) AS farmer_loan_pass_lt10
	, COUNT(DISTINCT IF(kcf.isLoanPassed = 1 AND d.FarmerID IS NULL,kcf.FarmerID,NULL)) AS farmer_loan_pass_mt10
    , COUNT(DISTINCT d.FarmerID) AS farmer_lt_10km
    , NOW() AS DateUpdated
FROM ktv_farmer_view kcf
LEFT JOIN (
SELECT f.FarmerID, f.LoanYesNo FROM ktv_farmer_financial f
INNER JOIN (SELECT z.FarmerID, MAX(z.SurveyNr) SurveyNr FROM ktv_farmer_financial z GROUP BY z.FarmerID) z ON z.FarmerID = f.FarmerID
) fin ON fin.FarmerID = kcf.FarmerID
LEFT JOIN (
SELECT
    f.FarmerID
    , MIN( 6371 * ACOS( COS( RADIANS(bb.BranchLatitude) ) * COS( RADIANS( g.Latitude) )
    * COS( RADIANS(g.Longitude) - RADIANS(bb.BranchLongitude)) + SIN(RADIANS(bb.BranchLatitude))
    * SIN( RADIANS(g.Latitude)))) AS distance,
    f.StatusCode
FROM ktv_farmer_garden g
JOIN (SELECT g.FarmerID, MAX(g.SurveyNr) SurveyNr FROM ktv_farmer_garden g WHERE g.GardenNr = 1 GROUP BY g.FarmerID) z ON z.FarmerID = g.FarmerID
JOIN ktv_farmer_view f ON f.FarmerID = g.FarmerID AND f.StatusCode = 'active'
LEFT JOIN ktv_village kv ON kv.VillageID = f.VillageID
LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
JOIN ktv_bank_branch bb ON bb.BranchDistrictID = kd.DistrictID
GROUP BY FarmerID
HAVING distance < 10
) d ON d.FarmerID = kcf.FarmerID
WHERE
	VillageID > 0
GROUP BY kcf.VillageID, kcf.CPGid
";
        $this->db->trans_start(FALSE);
        $this->db->query("TRUNCATE TABLE dash_bank;");
        $result = $this->db->query($sql);
        $this->db->trans_complete();
        $msg = array();
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function generateDashEnvironment() {
        $sql = "INSERT INTO dash_environment
SELECT
    p.`Province`
    , p.ProvinceID
    , d.`District`
    , d.DistrictID
    , sd.`SubDistrict`
    , sd.SubDistrictID
    , v.Village
    , kcf.VillageID
    , kcf.CPGid
    , SUM(TotalTrees) AS TotalTrees
    , SUM(Hectare) AS Hectare
    , SUM(Production) AS Production
    , SUM(Productivity) AS Productivity
    , SUM(Productivity_Trees) AS Productivity_Trees
    , SUM(Kg_Kompos_Tree) AS Kg_Kompos_Tree
    , SUM(TBM_Kompos) AS TBM_Kompos
    , SUM(TM_Kompos) AS TM_Kompos
    , SUM(TR_Kompos) AS TR_Kompos
    , SUM(Trees_Kompos) AS Trees_Kompos
    , SUM(Kg_Kompos) AS Kg_Kompos
    , SUM(CO2_Kompos) AS CO2_Kompos
    , SUM(TBM_Fertilized) AS TBM_Fertilized
    , SUM(TM_Fertilized) AS TM_Fertilized
    , SUM(TR_Fertilized) AS TR_Fertilized
    , SUM(Trees_Fertilized) AS Trees_Fertilized
    , SUM(G_Urea_Tree) AS G_Urea_Tree
    , SUM(G_NPK_Tree) AS G_NPK_Tree
    , SUM(Kg_Urea) AS Kg_Urea
    , SUM(Kg_NPK) AS Kg_NPK
    , SUM(Kg_Fertilizer) AS Kg_Fertilizer
    , SUM(Kg_Fertilizer_Tree) AS Kg_Fertilizer_Tree
    , SUM(CO2_Urea) AS CO2_Urea
    , SUM(CO2_NPK) AS CO2_NPK
    , SUM(CO2_ZA) AS CO2_ZA
    , SUM(CO2_Total) AS CO2_Total
    , SUM(CO2_Hectare) AS CO2_Hectare
    , SUM(tCO2e_tCocoa) AS tCO2e_tCocoa
    , SUM(C_Stock_Trees) AS C_Stock_Trees
    , SUM(C_Stock) AS C_Stock
    , NOW() AS DateUpdated
FROM (
SELECT
    kcfg.FarmerID
    , `PohonTBM`+`PohonTM`+`PohonRehab` AS TotalTrees
    , `GardenHaUnCertified` AS Hectare
    , IF(Production > 0, Production, ProductionCalc) AS Production
    -- , `PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg` AS Production
    , (IF(Production > 0, Production, ProductionCalc))/`GardenHaUnCertified` AS Productivity
    -- , (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)/`GardenHaUnCertified` AS Productivity
    , (IF(Production > 0, Production, ProductionCalc)) AS Productivity_Trees
    -- , (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)/(`PohonTBM`+`PohonTM`+`PohonRehab`) AS Productivity_Trees
    , `FrequentFertilizationKompos`*`DoseFertilizerKompos` AS Kg_Kompos_Tree
    , `PohonTBM`*`KomposTBM` AS TBM_Kompos
    , `PohonTM`*`KomposTM` AS TM_Kompos
    , `PohonRehab`*`KomposTR` AS TR_Kompos
    , `PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR` AS Trees_Kompos
    , (`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`) AS Kg_Kompos
    , 0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`)) AS CO2_Kompos
    , `PohonTBM`*`PupukTBM` AS TBM_Fertilized
    , `PohonTM`*`PupukTM` AS TM_Fertilized
    , `PohonRehab`*`PupukTR` AS TR_Fertilized
    , `PohonTBM`*`PupukTBM`+`PohonTM`*`PupukTM`+`PohonRehab`*`PupukTR` AS Trees_Fertilized
    , `FrUrea`*`DoUrea` AS G_Urea_Tree
    , `FrNpk`*`DoNpk` AS G_NPK_Tree
    , ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000 AS Kg_Urea
    , ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000 AS Kg_NPK
    , (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000    +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`))/1000 AS Kg_Fertilizer
    , (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000    + ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/(`PohonTBM`*`PupukTBM`+`PohonTM`*`PupukTM`+`PohonRehab`*`PupukTR`) AS Kg_Fertilizer_Tree
    , 2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000) AS CO2_Urea
    , 0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000) AS CO2_NPK
    , 0.91986 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrZa`*`DoZa`)/1000) AS CO2_ZA
    , (2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000))    + (0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)) + (0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))) AS CO2_Total
    , (((2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000))    + (0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)))    + (0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))))/`GardenHaUnCertified` AS CO2_Hectare
    , ((((2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000))    + (0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000))) + (0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))))/(IF(Production > 0, Production, ProductionCalc))) AS tCO2e_tCocoa
    -- , ((((2.014938 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000))    + (0.657045 * (((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000))) + (0.042 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))    + 0.062 * ((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))))/(`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)) AS tCO2e_tCocoa
    , `PohonTM` + `PohonRehab` AS C_Stock_Trees,
    CASE WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 2 THEN (0.47*(0.202*0.03^2.112)* (`PohonTM` + `PohonRehab`))
        WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 3 AND 5 THEN (0.47*(0.202*0.05^2.112)* (`PohonTM` + `PohonRehab`))
        WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 6 AND 10 THEN (0.47*(0.202*0.1^2.112)* (`PohonTM` + `PohonRehab`))
        WHEN (YEAR(NOW()) - `TahunTanamanCocoa`)  BETWEEN 11 AND 15 THEN (0.47*(0.202*0.12^2.112)* (`PohonTM` + `PohonRehab`))
        WHEN (YEAR(NOW()) - `TahunTanamanCocoa`)  BETWEEN  16 AND 20 THEN (0.47*(0.202*0.15^2.112)* (`PohonTM` + `PohonRehab`))
        WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN  21 AND 30 THEN (0.47*(0.202*0.2^2.112)* (`PohonTM` + `PohonRehab`))
        WHEN (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN  31 AND 50 THEN (0.47*(0.202*0.25^2.112)* (`PohonTM` + `PohonRehab`))
     END AS C_Stock
FROM
    `ktv_farmer_garden_view` kcfg
WHERE  `GardenHaUnCertified` >0
    AND (YEAR(NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 50
    AND (IF(Production > 0, Production, ProductionCalc))>0
    -- AND (`PanenBiasaMonths`*`PanenBiasaPanenMonth`*`PanenBiasaKg`+`PanenTrekMonths`*`PanenTrekPanenMonth`*`PanenTrekKg`+`PanenRayaMonths`*`PanenRayaPanenMonth`*`PanenRayaKg`)>0
    AND (((`PohonTBM`*`KomposTBM` + `PohonTM`*`KomposTM` + `PohonRehab`*`KomposTR`) * (`FrequentFertilizationKompos`*`DoseFertilizerKompos`))>0
    OR ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)>0
    OR ((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)> 0)
    AND ((((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrUrea`*`DoUrea`)/1000
    +((`PohonTBM`*`PupukTBM` +`PohonTM`*`PupukTM` +`PohonRehab`*`PupukTR`)*`FrNpk`*`DoNpk`)/1000)/`GardenHaUnCertified`) < 12000
    AND `FrequentFertilizationKompos`*`DoseFertilizerKompos` <20
) r
INNER JOIN `ktv_farmer_view` kcf ON (r.`FarmerID` = kcf.`FarmerID`)
LEFT JOIN ktv_village v ON v.`VillageID` = kcf.`VillageID`
LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`
WHERE
    kcf.StatusCode = 'active' AND kcf.isTrained = 1
GROUP BY
    p.ProvinceID
    , d.DistrictID
    , sd.SubDistrictID
    , v.VillageID
    , kcf.CPGid
";
        $sql_garden = "INSERT INTO dash_environment_garden
SELECT
    p.`Province`
    , p.ProvinceID
    , d.`District`
    , d.DistrictID
    , sd.`SubDistrict`
    , sd.SubDistrictID
    , v.Village
    , kcf.VillageID
    , kcf.CPGid
    , SUM(kcfg.GardenHaUncertified) TotalHa
    , SUM(PohonTBM+PohonTM+PohonRehab) AS CacaoTrees
    , SUM(KelapaNr) AS 'Nr_Coconut'
    , SUM(PinangNr) AS 'Nr_Areca_Palm'
    , SUM(KaretNr) AS 'Nr_Rubber'
    , SUM(CengkehNr) AS 'Nr_Clove'
    , SUM(SawitNr) AS 'Nr_Oil_Palm'
    , SUM(ArenNr) AS 'Nr_Sugar_Palm'
    , SUM(PalaNr) AS 'Nr_Nutmeg'
    , SUM(KemiriNr) AS 'Nr_Hazelnut'
    , SUM(MahoniNr) AS 'Nr_Mahagony'
    , SUM(JatiNr) AS 'Nr_Teak'
    , SUM(BitiNr) AS 'Nr_Vitex'
    , SUM(UruNr) AS 'Nr_Elmerilla'
    , SUM(JabonNr) AS 'Nr_Anthocephalus'
    , SUM(JackFruitNr) AS 'Nr_Jackfruit'
    , SUM(PisangNr) AS 'Nr_Banana'
    , SUM(RambutanNr) AS 'Nr_Rambutan'
    , SUM(ManggaNr) AS 'Nr_Mango'
    , SUM(LangsatNr) AS 'Nr_Langsat'
    , SUM(Duriannr) AS 'Nr_Durian'
    , SUM(AlpukatNr) AS 'Nr_Avocado'
    , SUM(SukunNr) AS 'Nr_Breadfruit'
    , SUM(PepayaNr) AS 'Nr_Papaya'
    , SUM(ManggisNr) AS 'Nr_Mangosteen'
    , SUM(JerukNr) AS 'Nr_Citrus'
    , SUM(GamalNr) AS 'Nr_Gliricidia'
    , SUM(LamtoroNr) AS 'Nr_Leucaena'
    , SUM(PetaiNr) AS 'Nr_Parkia'
    , SUM(JengkolNr) AS 'Nr_Archidendron'
    , SUM(ShadeLainNr) AS 'Nr_Other'
    , SUM(ShadeTreesNr) AS 'Total_Nr_Diversification'
    , SUM(KelapaNr + PinangNr + KaretNr + CengkehNr + SawitNr + ArenNr + PalaNr + KemiriNr + MahoniNr + JatiNr + BitiNr + UruNr + JabonNr
     + JackFruitNr + PisangNr + RambutanNr + ManggaNr + LangsatNr + Duriannr + AlpukatNr + SukunNr + PepayaNr + ManggisNr + JerukNr
     + GamalNr + LamtoroNr + PetaiNr + JengkolNr + ShadeLainNr) AS 'Check_Total_Nr'
    , SUM(KelapaNr+PinangNr+KaretNr+CengkehNr+SawitNr+ArenNr+PalaNr+KemiriNr) 'Tanaman_Produksi_Selain_Kakao'
    , SUM(MahoniNr+JatiNr+BitiNr+UruNr+JabonNr) 'Kayu_Keras'
    , SUM(JackFruitNr+PisangNr+RambutanNr+ManggaNr+LangsatNr+DurianNr+AlpukatNr+SukunNr+PepayaNr+ManggaNr+JerukNr) 'Buah_buahan'
    , SUM(GamalNr+LamtoroNr+PetaiNr+JengkolNr) 'Leguminosa'
    , SUM(ShadeLainNr) 'Lainnya'
    , NOW() AS DateUpdated
FROM ktv_farmer_view kcf
JOIN (SELECT FarmerID,MAX(SurveyNr) survey FROM ktv_farmer_garden GROUP BY FarmerID) kcfgt ON
    kcfgt.FarmerID=kcf.FarmerID
LEFT JOIN ktv_farmer_garden kcfg ON kcfg.FarmerID=kcfgt.FarmerID AND  survey=kcfg.SurveyNr
LEFT JOIN ktv_village v ON v.`VillageID` = kcf.`VillageID`
LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID` = v.`SubDistrictID`
LEFT JOIN ktv_district d ON d.`DistrictID` = sd.`DistrictID`
LEFT JOIN ktv_province p ON p.`ProvinceID` = d.`ProvinceID`
WHERE 1 = 1 AND kcf.StatusCode = 'active' AND kcf.isTrained = 1
GROUP BY
    p.ProvinceID
    , d.DistrictID
    , sd.SubDistrictID
    , v.VillageID
    , kcf.CPGid
		";
        $sql_base = "INSERT INTO dash_environment_base

SELECT 
    --    kcf.FarmerName,
       p.`Province`,
       p.ProvinceID,
       d.`District`,
       d.DistrictID,
       sd.`SubDistrict`,
       sd.SubDistrictID,
       kcf.VillageID,
       kcf.CPGid,
	   kcf.FarmerID AS Farmers,
	   COUNT(r.CO2_Total) AS Farmers_Baseline,
	   COUNT(s.CO2_Total) AS Farmers_Postline,
       SUM(r.TotalTrees) AS TotalTrees_Baseline,
       SUM(s.TotalTrees) AS TotalTrees_Postline,
       SUM(r.Hectare) AS Hectare_Baseline,
       SUM(s.Hectare) AS Hectare_Postline,
       SUM(r.Production) AS Production_Baseline,
       SUM(s.Production) AS Production_Postline,
       SUM(r.Productivity) AS Productivity_Baseline,
       SUM(s.Productivity) AS Productivity_Postline,
       SUM(r.Productivity_Trees) AS Productivity_Trees_Baseline,
       SUM(s.Productivity_Trees) AS Productivity_Trees_Postline,
       SUM(r.CO2_Kompos) AS CO2_Kompos_Baseline,
       SUM(s.CO2_Kompos) AS CO2_Kompos_Postline,
       SUM(r.CO2_Urea) AS CO2_Urea_Baseline,
       SUM(s.CO2_Urea) AS CO2_Urea_Postline,
       SUM(r.CO2_NPK) AS CO2_NPK_Baseline,
       SUM(s.CO2_NPK) AS CO2_NPK_Postline,
       SUM(r.CO2_ZA) AS CO2_ZA_Baseline,
       SUM(s.CO2_ZA) AS CO2_ZA_Postline,
       SUM(r.CO2_Total) AS CO2_Total_Baseline,
       SUM(s.CO2_Total) AS CO2_Total_Postline
FROM ktv_farmer kcf
LEFT JOIN
    (SELECT kcfg.FarmerID FarmerID,
            kcfg.GardenNr GardenNr,
            MIN(kcfg.SurveyNr) AS SurveyNr,
            SUM(`PohonTBM` + `PohonTM` + `PohonRehab`) AS TotalTrees,
            SUM(`GardenHaUnCertified`) AS Hectare,
            SUM(IF(Production > 0, Production, ProductionCalc)) AS Production,
            -- SUM(`PanenBiasaMonths` * `PanenBiasaPanenMonth` * `PanenBiasaKg` + `PanenTrekMonths` * `PanenTrekPanenMonth` * `PanenTrekKg` + `PanenRayaMonths` * `PanenRayaPanenMonth` * `PanenRayaKg`) AS Production,
            SUM(IF(Production > 0, Production, ProductionCalc)) / SUM(`GardenHaUnCertified`) AS Productivity,
            -- SUM(`PanenBiasaMonths` * `PanenBiasaPanenMonth` * `PanenBiasaKg` + `PanenTrekMonths` * `PanenTrekPanenMonth` * `PanenTrekKg` + `PanenRayaMonths` * `PanenRayaPanenMonth` * `PanenRayaKg`) / SUM(`GardenHaUnCertified`) AS Productivity,
            SUM(IF(Production > 0, Production, ProductionCalc)) / SUM(`PohonTBM` + `PohonTM` + `PohonRehab`) AS Productivity_Trees,
            -- SUM(`PanenBiasaMonths` * `PanenBiasaPanenMonth` * `PanenBiasaKg` + `PanenTrekMonths` * `PanenTrekPanenMonth` * `PanenTrekKg` + `PanenRayaMonths` * `PanenRayaPanenMonth` * `PanenRayaKg`) / SUM(`PohonTBM` + `PohonTM` + `PohonRehab`) AS Productivity_Trees,
            SUM(p.CO2_Kompos) CO2_Kompos,
            SUM(p.CO2_Urea) CO2_Urea,
            SUM(p.CO2_NPK) CO2_NPK,
            SUM(p.CO2_ZA) CO2_ZA,
            SUM(p.CO2_Total) CO2_Total
     FROM `ktv_farmer_garden_view` kcfg
     LEFT JOIN
         (SELECT FarmerID,
                 GardenNr,
                 0.042 * ((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`)) + 0.062 * ((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`)) AS CO2_Kompos,
                 2.014938 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrUrea` * `DoUrea`) / 1000) AS CO2_Urea,
                 0.657045 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrNpk` * `DoNpk`) / 1000) AS CO2_NPK,
                 0.91986 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrZa` * `DoZa`) / 1000) AS CO2_ZA,
                 (2.014938 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrUrea` * `DoUrea`) / 1000)) + (0.657045 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrNpk` * `DoNpk`) / 1000)) + (0.042 * ((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`)) + 0.062 * ((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`))) AS CO2_Total
          FROM `ktv_farmer_garden_view`
          WHERE `GardenHaUnCertified` > 0
              AND (YEAR (NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 50
              AND (IF(Production > 0, Production, ProductionCalc)) > 0
              -- AND (`PanenBiasaMonths` * `PanenBiasaPanenMonth` * `PanenBiasaKg` + `PanenTrekMonths` * `PanenTrekPanenMonth` * `PanenTrekKg` + `PanenRayaMonths` * `PanenRayaPanenMonth` * `PanenRayaKg`) > 0
              AND (((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`)) > 0
                   OR ((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrUrea` * `DoUrea`) > 0
                   OR ((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrNpk` * `DoNpk`) > 0)
              AND ((((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrUrea` * `DoUrea`) / 1000 + ((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrNpk` * `DoNpk`) / 1000) / `GardenHaUnCertified`) < 12000
              AND `FrequentFertilizationKompos` * `DoseFertilizerKompos` < 20
              AND SurveyNr = 0
          GROUP BY FarmerID,
                   GardenNr ) p ON kcfg.FarmerID = p.FarmerID
     AND kcfg.GardenNr = p.GardenNr
     WHERE kcfg.SurveyNr = 0
     GROUP BY kcfg.FarmerID,
              kcfg.GardenNr) r ON kcf.FarmerID = r.FarmerID
LEFT JOIN
    (SELECT kcfg.FarmerID FarmerID,
            kcfg.GardenNr GardenNr,
            MAX(kcfg.SurveyNr) AS SurveyNr,
            SUM(`PohonTBM` + `PohonTM` + `PohonRehab`) AS TotalTrees,
            SUM(`GardenHaUnCertified`) AS Hectare,
            SUM(IF(Production > 0, Production, ProductionCalc)) AS Production,
            -- SUM(`PanenBiasaMonths` * `PanenBiasaPanenMonth` * `PanenBiasaKg` + `PanenTrekMonths` * `PanenTrekPanenMonth` * `PanenTrekKg` + `PanenRayaMonths` * `PanenRayaPanenMonth` * `PanenRayaKg`) AS Production,
            SUM(IF(Production > 0, Production, ProductionCalc)) / SUM(`GardenHaUnCertified`) AS Productivity,
            -- SUM(`PanenBiasaMonths` * `PanenBiasaPanenMonth` * `PanenBiasaKg` + `PanenTrekMonths` * `PanenTrekPanenMonth` * `PanenTrekKg` + `PanenRayaMonths` * `PanenRayaPanenMonth` * `PanenRayaKg`) / SUM(`GardenHaUnCertified`) AS Productivity,
            SUM(IF(Production > 0, Production, ProductionCalc)) AS Productivity_Trees,
            -- SUM(`PanenBiasaMonths` * `PanenBiasaPanenMonth` * `PanenBiasaKg` + `PanenTrekMonths` * `PanenTrekPanenMonth` * `PanenTrekKg` + `PanenRayaMonths` * `PanenRayaPanenMonth` * `PanenRayaKg`) / SUM(`PohonTBM` + `PohonTM` + `PohonRehab`) AS Productivity_Trees,
            SUM(q.CO2_Kompos) CO2_Kompos,
            SUM(q.CO2_Urea) CO2_Urea,
            SUM(q.CO2_NPK) CO2_NPK,
            SUM(q.CO2_ZA) CO2_ZA,
            SUM(q.CO2_Total) CO2_Total
     FROM `ktv_farmer_garden_view` kcfg
     INNER JOIN
         (SELECT FarmerID,
                 GardenNr,
                 MAX(SurveyNr) SurveyNr
          FROM `ktv_farmer_garden`
          GROUP BY FarmerID,
                   GardenNr) latestSurvey ON kcfg.FarmerID = latestSurvey.FarmerID
     AND kcfg.GardenNr = latestSurvey.GardenNr
     AND kcfg.SurveyNr = latestSurvey.SurveyNr
     LEFT JOIN
         (SELECT kcfg2.FarmerID,
                 kcfg2.GardenNr,
                 kcfg2.SurveyNr,
                 0.042 * ((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`)) + 0.062 * ((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`)) AS CO2_Kompos,
                 2.014938 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrUrea` * `DoUrea`) / 1000) AS CO2_Urea,
                 0.657045 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrNpk` * `DoNpk`) / 1000) AS CO2_NPK,
                 0.91986 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrZa` * `DoZa`) / 1000) AS CO2_ZA,
                 (2.014938 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrUrea` * `DoUrea`) / 1000)) + (0.657045 * (((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrNpk` * `DoNpk`) / 1000)) + (0.042 * ((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`)) + 0.062 * ((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`))) AS CO2_Total
          FROM `ktv_farmer_garden_view` kcfg2
          INNER JOIN
              (SELECT FarmerID,
                      GardenNr,
                      MAX(SurveyNr) SurveyNr
               FROM `ktv_farmer_garden`
               GROUP BY FarmerID,
                        GardenNr) latestSurvey ON kcfg2.FarmerID = latestSurvey.FarmerID
          AND kcfg2.GardenNr = latestSurvey.GardenNr
          AND kcfg2.SurveyNr = latestSurvey.SurveyNr
          WHERE `GardenHaUnCertified` > 0
              AND (YEAR (NOW()) - `TahunTanamanCocoa`) BETWEEN 1 AND 50
              AND (IF(Production > 0, Production, ProductionCalc)) > 0
              -- AND (`PanenBiasaMonths` * `PanenBiasaPanenMonth` * `PanenBiasaKg` + `PanenTrekMonths` * `PanenTrekPanenMonth` * `PanenTrekKg` + `PanenRayaMonths` * `PanenRayaPanenMonth` * `PanenRayaKg`) > 0
              AND (((`PohonTBM` * `KomposTBM` + `PohonTM` * `KomposTM` + `PohonRehab` * `KomposTR`) * (`FrequentFertilizationKompos` * `DoseFertilizerKompos`)) > 0
                   OR ((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrUrea` * `DoUrea`) > 0
                   OR ((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrNpk` * `DoNpk`) > 0)
              AND ((((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrUrea` * `DoUrea`) / 1000 + ((`PohonTBM` * `PupukTBM` + `PohonTM` * `PupukTM` + `PohonRehab` * `PupukTR`) * `FrNpk` * `DoNpk`) / 1000) / `GardenHaUnCertified`) < 12000
              AND `FrequentFertilizationKompos` * `DoseFertilizerKompos` < 20
          GROUP BY kcfg2.FarmerID,
                   kcfg2.GardenNr,
                   kcfg2.SurveyNr) q ON kcfg.FarmerID = q.FarmerID
     AND kcfg.GardenNr = q.GardenNr
     AND kcfg.SurveyNr = q.SurveyNr
     WHERE kcfg.SurveyNr > 0
     GROUP BY kcfg.FarmerID,
              kcfg.GardenNr,
              kcfg.SurveyNr) s ON kcf.FarmerID = s.FarmerID
LEFT JOIN ktv_village v ON v.`VillageID`=kcf.`VillageID`
LEFT JOIN ktv_subdistrict sd ON sd.`SubDistrictID`=v.`SubDistrictID`
LEFT JOIN ktv_district d ON d.`DistrictID`=sd.`DistrictID`
LEFT JOIN ktv_province p ON p.`ProvinceID`=d.`ProvinceID`
WHERE kcf.StatusCode='active'
GROUP BY 
kcf.FarmerID,
         kcf.VillageID,
         kcf.CPGid
		";
        $this->db->trans_start(FALSE);
        $this->db->query("TRUNCATE TABLE dash_environment;");
        $result = $this->db->query($sql);
        $this->db->query("TRUNCATE TABLE dash_environment_garden;");
        $result = $this->db->query($sql_garden);
        $this->db->query("TRUNCATE TABLE dash_environment_base;");
        $result = $this->db->query($sql_base);
        $this->db->trans_complete();
        $msg = array();
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function getFarmersByDistrict($district = false, $farmerid = false) {
        $this->db->select('FarmerID');
        $this->db->from('ktv_farmer');
        $this->db->where('SUBSTR(FarmerID,1,4) = ' . $district, null, false);
        $Q = $this->db->get();

        if ($Q->num_rows() > 0) {
            return $Q->result_array();
        }

        return false;
    }

    public function updateFarmerQuota() {

        //truncate
        $truncate = 'TRUNCATE ktv_certification_quota';

        $this->db->query($truncate);

        //re-insert
        $sql = 'INSERT INTO ktv_certification_quota SELECT id,CertificationStart,CertificationEnd,batas_atas,sisa,NOW() FROM view_farmer_quota';

        $this->db->query($sql);

        if ($this->db->_error_number) {
            return $this->db->_error_message();
        }

        return array('sucess' => true);
    }

    public function cleanupReqResetPass() {
        $sql = "DELETE FROM sys_user_newpass WHERE (`created` + INTERVAL 1 DAY) <= NOW()";
        return $this->db->query($sql);
    }

    public function setFarmerBatchNumber() {
        $sql_cpg = "
UPDATE 
	ktv_farmer kcf
	, (
		SELECT
			t.FarmerID
			, f.BatchNumber
			, f.TrainingStart
		FROM (
			SELECT
				tf.FarmerID
				, MIN(bt.TrainingStart) AS TrainingStart
			FROM ktv_cpg_batch_trainings_farmers tf
			JOIN ktv_cpg_batch_trainings bt ON bt.CpgBatchTrainingID = tf.CpgBatchTrainingID
			GROUP BY tf.FarmerID
			) t
			JOIN (
			SELECT
				tf.FarmerID,
				b.BatchNumber,
				bt.TrainingStart
			FROM ktv_cpg_batch_trainings_farmers tf
			JOIN ktv_cpg_batch_trainings bt ON bt.CpgBatchTrainingID = tf.CpgBatchTrainingID
			JOIN ktv_cpg_batch b ON b.CpgBatchID = bt.CpgBatchID
			GROUP BY tf.FarmerID, bt.CpgBatchID
			) f ON f.FarmerID = t.FarmerID AND f.TrainingStart = t.TrainingStart
		) c
SET
	kcf.BatchNumber = c.BatchNumber
	, kcf.BatchDate = DATE(c.TrainingStart)
WHERE
	kcf.FarmerID = c.FarmerID
    	";
        $sql_kader = "
UPDATE 
	ktv_farmer kcf
	, (
		SELECT
			t.FarmerID
			, f.BatchNumber
			, f.TrainingStart
		FROM (
		SELECT
			tf.FarmerID
			, MIN(bt.TrainingStart) AS TrainingStart
		FROM ktv_kader_trainings_participants tf
		JOIN ktv_kader_trainings bt ON bt.CpgKaderTrainingID = tf.CpgKaderTrainingID
		GROUP BY tf.FarmerID
		) t
		JOIN (
		SELECT
			tf.FarmerID,
			b.BatchNumber,
			bt.TrainingStart
		FROM ktv_kader_trainings_participants tf
		JOIN ktv_kader_trainings bt ON bt.CpgKaderTrainingID = tf.CpgKaderTrainingID
		JOIN ktv_cpg_batch b ON b.CpgBatchID = bt.CpgBatchID
		GROUP BY tf.FarmerID, bt.CpgBatchID
		) f ON f.FarmerID = t.FarmerID AND f.TrainingStart = t.TrainingStart
		) c
SET
	kcf.BatchNumber = c.BatchNumber
	, kcf.BatchDate = DATE(c.TrainingStart)
WHERE
	kcf.FarmerID = c.FarmerID
	AND (kcf.BatchDate > DATE(c.TrainingStart) OR kcf.BatchDate IS NULL)
    	";
        $this->db->trans_start(FALSE);
        // unset all
        $this->db->query("UPDATE ktv_farmer kcf SET BatchNumber = NULL, BatchDate = NULL");
        // set from cpg
        $this->db->query($sql_cpg);
        // update from kader
        $this->db->query($sql_kader);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function generateDashCargill() {
        $sql = "
SELECT
    kcf.VillageID
    , kcf.CPGid
    , SUM(IF(kcf.type = 'CF',1,0)) AS CF
    , SUM(IF(kcf.type = 'CL',1,0)) AS CL
    , SUM(IF(kcf.type = 'UTZ',1,0)) AS UTZ
    , SUM(IF(kcf.type = 'CF',g.Production,0)) AS CF_Production
    , SUM(IF(kcf.type = 'CL',g.Production,0)) AS CL_Production
    , SUM(IF(kcf.type = 'UTZ',g.Production,0)) AS UTZ_Production
    , SUM(IF(kcf.type = 'CF',g.GardenHaUnCertified,0)) AS CF_GardenHaUnCertified
    , SUM(IF(kcf.type = 'CL',g.GardenHaUnCertified,0)) AS CL_GardenHaUnCertified
    , SUM(IF(kcf.type = 'UTZ',g.GardenHaUnCertified,0)) AS UTZ_GardenHaUnCertified
    , COUNT(n.FarmerID) AS nursery
    , SUM(n.area) AS nursery_area
    , SUM(n.volume) AS nursery_volume
FROM (
    SELECT
        kcf.FarmerID
        , kcf.VillageID
		, kcf.CPGid
        , CASE
            WHEN c.FarmerID IS NOT NULL THEN 'UTZ'
            WHEN c.FarmerID IS NULL AND kcf.BatchNumber < 59 THEN 'CF'
            WHEN c.FarmerID IS NULL AND kcf.BatchNumber >= 59 THEN 'CL'
        END AS `type`
    FROM ktv_farmer kcf
    JOIN ktv_village v ON v.VillageID = kcf.VillageID
    JOIN ktv_subdistrict sd ON sd.SubDistrictID = v.SubDistrictID
    LEFT JOIN (
        SELECT
            c.FarmerID
        FROM ktv_certification c
        WHERE
            c.Certification = 1
            AND CURRENT_DATE BETWEEN c.CertificationStart AND c.CertificationEnd
        GROUP BY c.FarmerID
    ) c ON c.FarmerID = kcf.FarmerID
    WHERE
        1 = 1
--         AND DistrictID IN (SELECT dp.DistrictID FROM ktv_district_partner dp WHERE dp.PartnerID = 9)
    GROUP BY kcf.FarmerID
) kcf
LEFT JOIN (
    SELECT
        g.FarmerID
        , SUM(IF(g.Production > 0, g.Production, g.ProductionCalc)) AS Production
        , SUM(g.GardenHaUnCertified) AS GardenHaUnCertified
    FROM ktv_farmer_garden_view g
    JOIN (SELECT g.FarmerID, g.GardenNr, MAX(g.SurveyNr) AS SurveyNr FROM ktv_farmer_garden g GROUP BY g.FarmerID, g.GardenNr) z ON z.FarmerID = g.FarmerID AND z.GardenNr = g.GardenNr AND z.SurveyNr = g.SurveyNr
    GROUP BY g.FarmerID
) g ON g.FarmerID = kcf.FarmerID
LEFT JOIN (
	SELECT
		n.ObjID AS FarmerID
		, n.Panjang * n.Lebar AS `area`
		, SUM(t.Volume) AS volume
	FROM ktv_nursery n
	LEFT JOIN ktv_nursery_transaction t ON t.NurseryID = n.NurseryID
	WHERE
		n.ObjType = 'farmer'
	GROUP BY n.ObjID
) n ON n.FarmerID = kcf.FarmerID
WHERE
    VillageID > 0
GROUP BY 
	kcf.VillageID
	, kcf.CPGid
    	";
        $this->db->trans_start(FALSE);
        $this->db->query("TRUNCATE TABLE dash_cargill;");
        $result = $this->db->query($sql);
        $this->db->trans_complete();
        $msg = array();
        return array(
            'success' => $this->db->trans_status(),
            'error' => $msg
        );
    }

    public function getPlottedFarmer() {
        $sql = 'SELECT 
            SUM(total.total) as Total
          FROM
            (SELECT 
              COUNT(a.MemberID) AS total 
            FROM
              ktv_members a 
              JOIN ktv_survey_plot b 
                ON a.MemberID = b.MemberID 
              JOIN ktv_member_role c 
                ON c.MemberID = a.MemberID 
            WHERE b.Latitude IS NOT NULL 
              AND b.Longitude IS NOT NULL 
              AND a.StatusCode = "active"
              AND b.StatusCode = "active" 
              AND c.MRoleID = 1
            GROUP BY a.MemberID) total ';
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['Total'];
        }
        return false;
    }

    public function cekConsentLetterFile() {
        $farmer_unchecked = $this->getFarmerUnchecked();
        if (count($farmer_unchecked) > 0) {
            foreach ($farmer_unchecked as $key => $val) {
                $is_exist = $this->checkFile($val['ProvinceID'], $val['LearningContractSign']);
                if ($is_exist) {
                    $status = 'exist';
                } else {
                    $status = 'not exist';
                }
                $this->updateConsentLetterFileStatus($val['MemberID'], $status);
            }
        }
    }

    private function checkFile($ProvinceID, $filename) {
        $path = 'images/consent/' . $ProvinceID . '/' . $filename;
        if (file_exists($path)) {
            return true;
        } else {
            return false;
        }
    }

    private function getFarmerUnchecked() {
        $sql = "SELECT 
                a.MemberID,
                kd.ProvinceID AS ProvinceID,
                a.LearningContractSign,
                a.LearningContractFileStatus 
              FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
					 LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
					 LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
              WHERE (
                  a.LearningContractFileStatus != 'exist' 
                  OR a.LearningContractFileStatus IS NULL
                ) 
                AND a.LearningContractSign IS NOT NULL ";
        $query = $this->db->query($sql);


        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    private function updateConsentLetterFileStatus($memberid, $status) {
        $sql = "UPDATE ktv_members a SET a.LearningContractFileStatus = ? WHERE a.MemberID = ?";
        $query = $this->db->query($sql, array($status, $memberid));
        return true;
    }

    public function cekMisplacedConsentLetterFile() {
        $path = "images/consent";
        $files = scandir($path, 1);
        foreach ($files as $key => $val) {
            $file = explode('.', $val);
            $ProvinceID = $this->getProvinceIDFromMemberUID($file[0]);
            if ($ProvinceID) {
                $this->moveConsentLetterFile($val, $path, $ProvinceID);
            } else {
                $this->moveConsentLetterFile($val, $path, 'temp');
            }
        }
    }

    private function getProvinceIDFromMemberUID($member) {
        $sql = "SELECT 
                kd.ProvinceID AS ProvinceID 
              FROM
                ktv_members a
                LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
					 LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
					 LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
              WHERE a.MemberUID = ? 
                OR a.MemberDisplayID = ?";
        $query = $this->db->query($sql, array($member, $member));

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0]['ProvinceID'];
        }
        return false;
    }

    private function moveConsentLetterFile($file, $path, $folder) {
        $source = $path . '/' . $file;
        $destination = $path . '/' . $folder . '/' . $file;
        if (file_exists($source)) {
            $copy = copy($source, $destination);
            if ($copy) {
                unlink($source);
            }
        }
    }

    public function generatePhotoFile(){
        $farmer = $this->getListPhotoFarmer();
        if (count($farmer) > 0){
            foreach ($farmer as $key => $val){
                echo '<pre>';
                print_r($val['MemberName']);
                echo '</pre>';
                if(!copy('images/member/'. $val['ProvinceID'] .'/'. $val['Photo'], 'images/member/backup/'. $val['MemberDisplayID'] .' - '. $val['MemberName'].'.jpg')){
                    copy('images/member/' . $val['Photo'], 'images/member/backup/'. $val['MemberDisplayID'] .' - '. $val['MemberName'].'.jpg');
                }else{
                    echo '<pre>';
                    print_r($key. ' Copy success');
                    echo '</pre>';
                }
            }
        }
    }

    function getListPhotoFarmer() {
        $sql = "SELECT 
                    a.MemberDisplayID,
                    a.MemberName,
                    kd.ProvinceID AS ProvinceID,
                    a.Photo 
                FROM
                    ktv_members a
                    LEFT JOIN ktv_village kv ON kv.VillageID = a.VillageID
						  LEFT JOIN ktv_subdistrict ksd ON ksd.SubDistrictID = kv.SubDistrictID
						  LEFT JOIN ktv_district kd ON kd.DistrictID = ksd.DistrictID
                    LEFT JOIN ktv_member_role b 
                        ON a.MemberID = b.MemberID 
                WHERE a.Photo IS NOT NULL 
                    AND kd.DistrictID = 1502
                    AND b.MRoleID = 1 ";

        $query = $this->db->query($sql, array());
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        return false;
    }

    public function UpdateGardenStatus() {
        $this->db->trans_start();

        /* ========================================= Insert ke Garden Status (Begin) ==================================== */
        //Farmer
        $sql = "INSERT IGNORE `ktv_survey_plot_status` (
                    `MemberID`,
                    `PlotNr`,
                    `ActiveStatus`,
                    Remark,
                    `DateCreated`,
                    `CreatedBy`
                )
                SELECT
                    t_gar.MemberID
                    , t_gar.PlotNr
                    , '1'
                    , 'Insert dari script penyesuaian garden status'
                    , NOW()
                    , '1'
                FROM (
                    SELECT
                        gar.`MemberID`
                        , gar.`PlotNr`
                    FROM
                        ktv_survey_plot gar
                    WHERE
                        gar.`MemberID` != '0'
                        AND gar.`PlotNr` != '0'
                    GROUP BY gar.`MemberID`, gar.`PlotNr`
                ) AS t_gar
                LEFT JOIN (
                    SELECT
                        gstat.`MemberID`
                        , gstat.`PlotNr`
                        , gstat.`ActiveStatus`
                    FROM
                        `ktv_survey_plot_status` gstat
                    WHERE
                        gstat.`MemberID` != '0'
                        AND gstat.`PlotNr` != '0'
                ) AS t_garstat ON 1=1
                    AND t_gar.MemberID = t_garstat.MemberID
                    AND t_gar.PlotNr = t_garstat.PlotNr
                WHERE
                    t_garstat.MemberID IS NULL";
        $query = $this->db->query($sql);

        //SME
        $sql = "INSERT IGNORE `ktv_survey_plot_status_sme` (
                    `MemberID`,
                    `PlotNr`,
                    `ActiveStatus`,
                    Remark,
                    `DateCreated`,
                    `CreatedBy`
                )
                SELECT
                    t_gar.MemberID
                    , t_gar.PlotNr
                    , '1'
                    , 'Insert dari script penyesuaian garden status'
                    , NOW()
                    , '1'
                FROM (
                    SELECT
                        gar.`MemberID`
                        , gar.`PlotNr`
                    FROM
                        ktv_survey_plot_sme gar
                    WHERE
                        gar.`MemberID` != '0'
                        AND gar.`PlotNr` != '0'
                    GROUP BY gar.`MemberID`, gar.`PlotNr`
                ) AS t_gar
                LEFT JOIN (
                    SELECT
                        gstat.`MemberID`
                        , gstat.`PlotNr`
                        , gstat.`ActiveStatus`
                    FROM
                        `ktv_survey_plot_status_sme` gstat
                    WHERE
                        gstat.`MemberID` != '0'
                        AND gstat.`PlotNr` != '0'
                ) AS t_garstat ON 1=1
                    AND t_gar.MemberID = t_garstat.MemberID
                    AND t_gar.PlotNr = t_garstat.PlotNr
                WHERE
                    t_garstat.MemberID IS NULL";
        $query = $this->db->query($sql);
        /* ========================================= Insert ke Garden Status (End) ==================================== */

        /* ======================================== Update ke Garden Status (Ha dan Production) (Begin) ==================================== */
        //Farmer
        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`GardenAreaHa`
                        , sgar.`GardenAreaPolygon`
                        , sgar.`AnnualProduction`
                    FROM
                        `ktv_survey_plot` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`GardenAreaHa` = gar_lat.GardenAreaHa
                    , tup.`AnnualProduction` = gar_lat.AnnualProduction
                    , tup.GardenAreaPolygon = gar_lat.GardenAreaPolygon
                ";
        $query = $this->db->query($sql);

        //SME
        $sql = "UPDATE ktv_survey_plot_status_sme tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`GardenAreaHa`
                        , sgar.`AnnualProduction`
                        , sgar.GardenAreaPolygon
                    FROM
                        `ktv_survey_plot_sme` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot_sme lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`GardenAreaHa` = gar_lat.GardenAreaHa
                    , tup.`AnnualProduction` = gar_lat.AnnualProduction
                    , tup.GardenAreaPolygon = gar_lat.GardenAreaPolygon
                ";
        $query = $this->db->query($sql);
        /* ========================================= Update ke Garden Status (Ha dan Production) (End) ==================================== */

        /* ========================================= Update ke Garden Status (Lat n Long) (Begin) ==================================== */
        //Farmer
        $sql = "UPDATE ktv_survey_plot_status tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`Latitude`
                        , sgar.`Longitude`
                    FROM
                        `ktv_survey_plot` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.`Latitude` IS NOT NULL
                        AND sgar.`Latitude` != ''
                        AND sgar.`Latitude` != '0'
                        AND sgar.`Longitude` IS NOT NULL
                        AND sgar.`Longitude` != ''
                        AND sgar.`Longitude` != '0'
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`Latitude` = gar_lat.Latitude
                    , tup.`Longitude` = gar_lat.Longitude";
        $query = $this->db->query($sql);

        //SME
        $sql = "UPDATE ktv_survey_plot_status_sme tup
                INNER JOIN (
                    SELECT
                        sgar.`MemberID`
                        , sgar.`PlotNr`
                        , sgar.`SurveyNr`
                        , sgar.`Latitude`
                        , sgar.`Longitude`
                    FROM
                        `ktv_survey_plot_sme` sgar
                        INNER JOIN (
                            SELECT
                                lat_sgar.`MemberID`
                                , lat_sgar.`PlotNr`
                                , MAX(lat_sgar.`SurveyNr`) AS SurveyNr
                            FROM
                                ktv_survey_plot_sme lat_sgar
                            GROUP BY lat_sgar.`MemberID`, lat_sgar.`PlotNr`
                        ) AS sgar_lat ON
                            sgar.MemberID = sgar_lat.MemberID
                            AND sgar.`PlotNr` = sgar_lat.PlotNr
                            AND sgar.`SurveyNr` = sgar_lat.SurveyNr
                    WHERE 1=1
                        AND sgar.`Latitude` IS NOT NULL
                        AND sgar.`Latitude` != ''
                        AND sgar.`Latitude` != '0'
                        AND sgar.`Longitude` IS NOT NULL
                        AND sgar.`Longitude` != ''
                        AND sgar.`Longitude` != '0'
                    GROUP BY sgar_lat.MemberID , sgar_lat.PlotNr
                ) AS gar_lat ON 1=1
                    AND tup.`MemberID` = gar_lat.MemberID
                    AND tup.`PlotNr` = gar_lat.PlotNr
                SET
                    tup.`Latitude` = gar_lat.Latitude
                    , tup.`Longitude` = gar_lat.Longitude";
        $query = $this->db->query($sql);
        /* ========================================= Update ke Garden Status (Lat n Long) (End)   ==================================== */

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $results['success'] = true;
            $results['message'] = "Process Finished";
        } else {
            $results['success'] = false;
            $results['message'] = "Process Failed";
        }
        return $results;
    }

    public function generateFarmerGridAdditionalInfo(){
    	$update = "UPDATE 
				  ktv_members tbl_member 
				  LEFT JOIN (
				    SELECT 
				      GROUP_CONCAT(DISTINCT s_mi.MillName) AS MillName, 
				      s_ma.apmMemberID AS apmMemberID 
				    FROM 
				      ktv_access_partner_member s_ma 
				      INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID 
				      INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID 
				    WHERE 
				      s_par.PartnerIndustry = 3
                      AND s_mi.StatusCode = 'active'
				    GROUP BY 
				      s_ma.apmMemberID
				  ) tbl_mill ON tbl_mill.apmMemberID = tbl_member.MemberID 
				  LEFT JOIN (
				    SELECT 
				      sub_a.`MemberID`, 
				      COUNT(sub_a.PlotNr) AS NrOfPlantation, 
				      SUM(sub_a.GardenAreaHa) AS TotalHectare, 
				      SUM(sub_a.GardenAreaPolygon) AS TotalHectarePolygon,
                      sub_a.Latitude,
                      sub_a.Longitude
				    FROM 
				      ktv_survey_plot sub_a 
				      INNER JOIN (
				        SELECT 
				          p.MemberID, 
				          p.PlotNr, 
				          MAX(p.SurveyNr) AS SurveyNr, 
				          p.DateCollection 
				        FROM 
				          ktv_survey_plot p 
				        WHERE 
				          p.`StatusCode` = 'active' 
				        GROUP BY 
				          p.MemberID, 
				          p.PlotNr
				      ) sub_b ON sub_a.MemberID = sub_b.MemberID 
				      AND sub_a.PlotNr = sub_b.PlotNr 
				      AND sub_a.SurveyNr = sub_b.SurveyNr 
				    WHERE 
				      sub_a.`StatusCode` = 'active' 
				    GROUP BY 
				      sub_a.`MemberID`
				  ) AS splot ON tbl_member.MemberID = splot.MemberID 
				  LEFT JOIN (
				    SELECT 
				      IFNULL(
				        GROUP_CONCAT(
				          CONCAT(
				            'PlotNr:', ksp.PlotNr, '(', ksp.Latitude, 
				            ',', ksp.Longitude, ')'
				          )
				        ), 
				        ''
				      ) AS Garden, 
				      ksp.MemberID,
                      ksp.`Latitude`,
                      ksp.`Longitude`
				    FROM 
				      ktv_survey_plot ksp 
				    GROUP BY 
				      ksp.MemberID
				  ) ksp ON ksp.MemberID = tbl_member.MemberID 
                  INNER JOIN ktv_member_role mr on mr.MemberID = tbl_member.MemberID
                  INNER JOIN ktv_ref_member_role mrol on mrol.MRoleID = mr.MRoleID
				SET 
				  tbl_member.NrOfPlantation = splot.NrOfPlantation, 
				  tbl_member.TotalHectare = splot.TotalHectare, 
				  tbl_member.Latitude = splot.Latitude, 
				  tbl_member.Longitude = splot.Longitude, 
				  tbl_member.TotalHectarePolygon = splot.TotalHectarePolygon, 
				  tbl_member.MillName = tbl_mill.MillName, 
				  tbl_member.Garden = ksp.Garden, 
				  tbl_member.FarmerCategory = 'Mapped', 
				  tbl_member.SchedulerUpdated = NOW() 
				WHERE                   
                ksp.`Latitude` != 0.000000
                AND tbl_member.StatusCode = 'active'
                AND mrol.MRoleType = 'Farmer'
                OR ksp.`Longitude` != 0.000000
                AND tbl_member.StatusCode = 'active'
                AND mrol.MRoleType = 'Farmer'
                OR ksp.`Latitude` IS NOT NULL 
                AND tbl_member.StatusCode = 'active'
                AND mrol.MRoleType = 'Farmer'
                OR ksp.`Longitude` IS NOT NULL
                AND tbl_member.StatusCode = 'active'
                AND mrol.MRoleType = 'Farmer'";
				  
		$this->db->trans_start();
    		$this->db->query($update);
    	$this->db->trans_complete();

    	if ($this->db->trans_status() !== FALSE) {
            $results['success'] = true;
            $results['message'] = "Process Finished";
        } else {
            $results['success'] = false;
            $results['message'] = "Process Failed";
        }
        return $results;
    }

    public function generateFarmerGridAdditionalInfoUnmapped(){
    	$update = "UPDATE 
				  ktv_members tbl_member 
				  LEFT JOIN (
				    SELECT 
				      GROUP_CONCAT(DISTINCT s_mi.MillName) AS MillName, 
				      s_ma.apmMemberID AS apmMemberID 
				    FROM 
				      ktv_access_partner_member s_ma 
				      INNER JOIN ktv_mill s_mi ON s_ma.apmPartnerID = s_mi.PartnerID 
				      INNER JOIN ktv_program_partner s_par ON s_mi.PartnerID = s_par.PartnerID 
				    WHERE 
				      s_par.PartnerIndustry = 3
                      AND s_mi.StatusCode = 'active'
				    GROUP BY 
				      s_ma.apmMemberID
				  ) tbl_mill ON tbl_mill.apmMemberID = tbl_member.MemberID 
				  LEFT JOIN (
				    SELECT 
				      sub_a.`MemberID`, 
				      COUNT(sub_a.PlotNr) AS NrOfPlantation, 
				      SUM(sub_a.GardenAreaHa) AS TotalHectare, 
				      SUM(sub_a.GardenAreaPolygon) AS TotalHectarePolygon,
                      sub_a.Latitude,
                      sub_a.Longitude
				    FROM 
				      ktv_survey_plot sub_a 
				      INNER JOIN (
				        SELECT 
				          p.MemberID, 
				          p.PlotNr, 
				          MAX(p.SurveyNr) AS SurveyNr, 
				          p.DateCollection 
				        FROM 
				          ktv_survey_plot p 
				        WHERE 
				          p.`StatusCode` = 'active' 
				        GROUP BY 
				          p.MemberID, 
				          p.PlotNr
				      ) sub_b ON sub_a.MemberID = sub_b.MemberID 
				      AND sub_a.PlotNr = sub_b.PlotNr 
				      AND sub_a.SurveyNr = sub_b.SurveyNr 
				    WHERE 
				      sub_a.`StatusCode` = 'active' 
				    GROUP BY 
				      sub_a.`MemberID`
				  ) AS splot ON tbl_member.MemberID = splot.MemberID 
				  LEFT JOIN (
				    SELECT 
				      IFNULL(
				        GROUP_CONCAT(
				          CONCAT(
				            'PlotNr:', ksp.PlotNr, '(', ksp.Latitude, 
				            ',', ksp.Longitude, ')'
				          )
				        ), 
				        ''
				      ) AS Garden, 
				      ksp.MemberID,
                      ksp.`Latitude`,
                      ksp.`Longitude`
				    FROM 
				      ktv_survey_plot ksp 
				    GROUP BY 
				      ksp.MemberID
				  ) ksp ON ksp.MemberID = tbl_member.MemberID
                  INNER JOIN ktv_member_role mr on mr.MemberID = tbl_member.MemberID
                  INNER JOIN ktv_ref_member_role mrol on mrol.MRoleID = mr.MRoleID
				SET 
				  tbl_member.NrOfPlantation = splot.NrOfPlantation, 
				  tbl_member.TotalHectare = splot.TotalHectare, 
				  tbl_member.Latitude = splot.Latitude, 
				  tbl_member.Longitude = splot.Longitude, 
				  tbl_member.TotalHectarePolygon = splot.TotalHectarePolygon, 
				  tbl_member.MillName = tbl_mill.MillName, 
				  tbl_member.Garden = ksp.Garden, 
				  tbl_member.FarmerCategory = 'Unmapped', 
				  tbl_member.SchedulerUpdated = NOW() 
				WHERE                  
                    ksp.`Latitude` = 0.000000
                    AND tbl_member.StatusCode = 'active'
                    AND mrol.MRoleType = 'Farmer'
                    OR ksp.`Longitude` = 0.000000
                    AND tbl_member.StatusCode = 'active'
                    AND mrol.MRoleType = 'Farmer'
                    OR ksp.`Latitude` IS NULL 
                    AND tbl_member.StatusCode = 'active'
                    AND mrol.MRoleType = 'Farmer'
                    OR ksp.`Longitude` IS NULL 
                    AND tbl_member.StatusCode = 'active'
                    AND mrol.MRoleType = 'Farmer'";
				  
		$this->db->trans_start();
    		$this->db->query($update);
    	$this->db->trans_complete();

    	if ($this->db->trans_status() !== FALSE) {
            $results['success'] = true;
            $results['message'] = "Process Finished";
        } else {
            $results['success'] = false;
            $results['message'] = "Process Failed";
        }
        return $results;
    }

    public function generateProcessing(){
        $results['success'] = true;
        $results['message'] = "Data saved";

        // return $results;

        //Sementara di Disabled, Karna Digenerate Secara Manual Di Menu Dispatch

        $this->db->trans_start();

        $sql = "SELECT
                sb.SupplyDestMillOrgID SupplychainID
                , vso.ProductionCapacity
                , vso.WorkHour
            FROM
                ktv_tc_supplychain_batch sb
            INNER JOIN
                ktv_tc_supplychain_transaction st on sb.SupplyBatchID = st.SupplyBatchID
            INNER JOIN
                ktv_tc_supplychain_product sp on sp.SupplychainID = sb.SupplyDestMillOrgID
            LEFT JOIN
                ktv_tc_supplychain_org vso on vso.SupplychainID = sb.SupplyDestMillOrgID
            LEFT JOIN
                ktv_tc_processing_detail pd on pd.ObjID = st.SupplyTransID
            WHERE
            -- 	sb.SupplyDestOrgID = '226'
                1=1
            AND
                sb.SupplyBatchStatus = 'Delivered'
            AND
                sb.StatusCode = 'active'
            AND
                st.StatusCode = 'active'
            AND 
                pd.ObjID IS NULL
            AND
                (sb.SupplyDestMillOrgID IS NOT NULL AND sb.SupplyDestMillOrgID <> 0)
            GROUP BY
                sb.SupplyDestMillOrgID";
        
        $query = $this->db->query($sql);
        
        if($query->num_rows()>0){
            foreach($query->result() as $key => $row){
                $process["SupplychainID"]      = $row->SupplychainID;
                $process["ProcessingNumber"]   = time();
                $process["ProcessingDate"]     = date("Y-m-d");
                $process["ProductionCapacity"] = $row->ProductionCapacity;
                $process["WorkHour"]           = $row->WorkHour;
                $process["StatusCode"]         = 'active';
                $process["CreatedBy"]          = 1;
                $process["DateCreated"]        = date("Y-m-d H:i:s");

                $this->db->insert("ktv_tc_processing",$process);
                
                $this->InsertProcessDetail($row->SupplychainID,$this->db->insert_id());
            }
        }

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        return $results;
    }

    public function InsertProcessDetail($SupplychainID,$ProcessingID){

        $detail         = array();
        $product        = array();
        $total_volume   = 0;

        $sql = "SELECT
            1 ObjTypeID,
            st.SupplyTransID ObjID,
            st.VolumeNetto
        FROM
            ktv_tc_supplychain_batch sb
        INNER JOIN 
            ktv_tc_supplychain_transaction st ON sb.SupplyBatchID = st.SupplyBatchID
        LEFT JOIN
            ktv_tc_processing_detail pd on pd.ObjID = st.SupplyTransID 
        WHERE
            sb.SupplyDestMillOrgID = ?
            AND sb.SupplyBatchStatus = 'Delivered' 
            AND sb.StatusCode = 'active' 
            AND st.StatusCode = 'active' 
            AND pd.ObjID IS NULL
        GROUP BY
            st.SupplyTransID";
        $query = $this->db->query($sql,array($SupplychainID));

        if($query->num_rows()>0){
            foreach($query->result() as $key => $row){
                $detail[$key]["ProcessingID"]       = $ProcessingID;
                $detail[$key]["ObjTypeID"]          = $row->ObjTypeID;
                $detail[$key]["ObjID"]              = $row->ObjID;
                $detail[$key]["ProcessingVolume"]   = $row->VolumeNetto;
                $detail[$key]["StatusCode"]         = 'active';
                $detail[$key]["CreatedBy"]          = 1;
                $detail[$key]["DateCreated"]        = date("Y-m-d H:i:s");

                $total_volume = $total_volume+$row->VolumeNetto;
            }
            $this->db->insert_batch("ktv_tc_processing_detail",$detail);
        }

        if($total_volume > 0){
            $sql2 = "SELECT
                    pd.*
                FROM
                    ktv_tc_supplychain_product pd
                WHERE
                    pd.StatusCode='active'
                    AND NOW() BETWEEN pd.StartDate AND pd.EndDate
                    AND pd.SupplychainID = ?";
            $query2 = $this->db->query($sql2,array($SupplychainID));
            
            if($query2->num_rows()>0){
                foreach($query2->result() as $num => $row2){
                    $product[$num]["ProcessingID"]       = $ProcessingID;
                    $product[$num]["ProductID"]          = $row2->ProductID;
                    $product[$num]["ProductPercentage"]  = $row2->ProductPercentage;
                    $product[$num]["ProductVolume"]      = ($total_volume*$row2->ProductPercentage)/100;
                    $product[$num]["RemainingVolume"]    = ($total_volume*$row2->ProductPercentage)/100;
                    $product[$num]["StatusCode"]         = 'active';
                    $product[$num]["CreatedBy"]          = 1;
                    $product[$num]["DateCreated"]        = date("Y-m-d H:i:s");
                }
                $this->db->insert_batch("ktc_tc_processing_product",$product);
            }
        }

        return array("jml_data"=>$query->num_rows(),"total_volume"=>$total_volume,"product"=>$product,"data"=>$detail);
    }

    public function generateProcessingManual($params = NULL){
        // 5-4-2021
        // menambahkan params tambahan dari settingan haveoer yang ada di company profile
        
        $passing = NULL;
        if ($params != NULL) {
        	$passing = [
        		'ProductPercentageCpo' => (float) $params['ProductPercentageCpo'],
        		'ProductPercentagePk'  => (float) $params['ProductPercentagePk'],
        		'HaveOer'			   => (int) $params['HaveOer'],
        		'fromPopUp'			   => json_decode($params['fromPopUp']),
        		'ProductID'			   => (int) $params['ProductID']
        	];
        }
        // var_dump($passing);exit;
        $this->db->trans_begin();

        $SID = $_SESSION['SupplychainID'];

        $sql = $this->db->query("SELECT
            ktsd.SupplyDestMillOrgID AS SupplychainID,
            ktsd.DeliveryID,
            ktp.ProcessingID
        FROM
            ktv_tc_supplychain_delivery ktsd
        LEFT JOIN
            ktv_tc_processing ktp ON ktp.SupplychainID = ktsd.SupplyDestMillOrgID
        WHERE
            ktsd.DeliveryStatusID = '4'
        AND
            ktsd.StatusCode = 'active'
        AND
            (ktsd.SupplyDestMillOrgID IS NOT NULL AND ktsd.SupplyDestMillOrgID <> 0)
        AND 
            ktsd.SupplyDestMillOrgID = '$SID'
        ORDER BY ktsd.DeliveryID DESC");
            
        $chekData = $sql->result_array();
        // echo '<pre>'.$this->db->last_query();die;
        if(!empty($chekData)){
            $this->InsertProcessDetailManual($SID,$passing);
            $this->UpdateReception($row->DeliveryID);
        } else {
            return [
                'success' => false,
                'message' => "NO DATA"
            ];
        }

        if ($this->db->trans_status()) {
            $this->db->trans_commit();
            $results['success'] = true;
            $results['message'] = "Data saved";
        } else {
            $this->db->trans_rollback();
            $results['success'] = false;
            $results['message'] = "Failed to save data";
        }
        
        return $results;
    }

    public function UpdateReception($GenerateDelivery){
    
        $ReceiptDeliveryID =  $GenerateDelivery;
        
        $update['Status'] = '1';

        $this->db->where('Status', '0');
        $this->db->update('ktv_tc_reception', $update);
        $return['data'] = array(
            'ReceiptDeliveryID' => @$ReceiptDeliveryID,
        );
    }

    public function generateProcessingAutomated($params = NULL){
        
        $passing = NULL;
        if ($params != NULL) {
        	$passing = [
        		'ProductPercentageCpo' => (float) $params['ProductPercentageCpo'],
        		'ProductPercentagePk'  => (float) $params['ProductPercentagePk'],
        		'HaveOer'			   => (int) $params['HaveOer'],
        		'fromPopUp'			   => json_decode($params['fromPopUp']),
        		'ProductID'			   => (int) $params['ProductID']
        	];
        }

        $this->db->trans_begin();
        if($passing['ProductID'] == '1'){
            $SID = $_SESSION['SupplychainID'];

            $sql = $this->db->query("SELECT
                1 ObjTypeID,
                std.totalcapacity as VolumeNetto,
                st.SupplyTransID ObjID,
                std.DeliveryDetailID AS DeliveryID,
                ktc.ReceptionID,
                ktsd.SupplyDestMillOrgID,
                ktso.ProductionCapacity,
                ktso.WorkHour,
                ktc.status,
                p.TotalCapacity,
                p.ProcessingID
                FROM
                ktv_tc_reception ktc
                LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktc.ReceiptDeliveryID
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = ktsdd.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                LEFT JOIN ktv_tc_processing p ON p.SupplychainID = ktsd.SupplyDestMillOrgID
                LEFT JOIN ktv_tc_supplychain_transaction_detail std ON std.DeliveryDetailID = ktsd.DeliveryID    
                LEFT JOIN ktv_tc_supplychain_transaction st ON sb.SupplyBatchID = st.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_org ktso on ktso.SupplychainID = ktsd.SupplyDestMillOrgID
                WHERE
                ktsd.SupplyDestMillOrgID = '$SID'
                AND ktsd.DeliveryStatusID = '4'
                AND std.Weight IS NOT NULL
                -- AND p.StatusGenerate IS NULL
                -- GROUP BY ktc.ReceptionID
                ORDER BY p.processingid DESC");
                
            $chekData = $sql->result_array();
            // echo '<pre>'.$this->db->last_query();die;
            if(!empty($chekData)){
                $ProcessingID['ProcessingID'] = $chekData[0];
                
                $id = $ProcessingID['ProcessingID']['ProcessingID']; 
                $sqlCpo = $this->db->query(
                    "SELECT 
                        *
                    FROM 
                        ktv_tc_processing 
                    WHERE
                        ProcessingID = '$id'
                    AND
                        GenerateFrom = 'CPO'");
                
                $chekCpo = $sqlCpo->result_array();
                if(empty($chekCpo)){
                    // echo "belum ada";exit;

                    $this->InsertProcessDetailAutomated($SID,$passing);
                    $this->UpdateReception($row->DeliveryID);
                } else {
                    // echo "udah ada";exit;
                    return [
                        'success' => false,
                        'message' => "NO DATA"
                    ];
                }
            } else {
                
                return [
                    'success' => false,
                    'message' => "NO DATA"
                ];
            }

            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "Data saved";
            } else {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to save data";
            }
            
            return $results;
        } else {
            $SID = $_SESSION['SupplychainID'];

            $sql = $this->db->query("SELECT
                1 ObjTypeID,
                std.totalcapacity as VolumeNetto,
                st.SupplyTransID ObjID,
                std.DeliveryDetailID AS DeliveryID,
                ktc.ReceptionID,
                ktsd.SupplyDestMillOrgID,
                ktso.ProductionCapacity,
                ktso.WorkHour,
                ktc.status,
                p.TotalCapacity,
                p.ProcessingID
                FROM
                ktv_tc_reception ktc
                LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktc.ReceiptDeliveryID
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = ktsdd.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                LEFT JOIN ktv_tc_processing p ON p.SupplychainID = ktsd.SupplyDestMillOrgID
                LEFT JOIN ktv_tc_supplychain_transaction_detail std ON std.DeliveryDetailID = ktsd.DeliveryID    
                LEFT JOIN ktv_tc_supplychain_transaction st ON sb.SupplyBatchID = st.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_org ktso on ktso.SupplychainID = ktsd.SupplyDestMillOrgID
                WHERE
                ktsd.SupplyDestMillOrgID = '$SID'
                AND ktsd.DeliveryStatusID = '4'
                AND std.Weight IS NOT NULL
                -- AND p.StatusGenerate IS NULL
                -- GROUP BY ktc.ReceptionID
                ORDER BY p.processingid DESC");
                
            $chekData = $sql->result_array();

            if(!empty($chekData)){

            $ProcessingID['ProcessingID'] = $chekData[0];
                
            $id = $ProcessingID['ProcessingID']['ProcessingID']; 
            // var_dump($id);exit;
            $sqlPk = $this->db->query(
                "SELECT 
                    *
                FROM 
                    ktv_tc_processing 
                WHERE
                    ProcessingID = '$id'
                AND
                    GenerateFrom = 'PK'");
            
            $chekPk = $sqlPk->result_array();

            if(empty($chekPk)){
                // echo "belum ada";exit;

                $this->InsertProcessDetailAutomated($SID,$passing);
                $this->UpdateReception($row->DeliveryID);
            } else {
                // echo "udah ada";exit;

                return [
                    'success' => false,
                    'message' => "NO DATA"
                ];
            }
               
            } else {
                return [
                        'success' => false,
                        'message' => "NO DATA"
                ];
            }

            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $results['success'] = true;
                $results['message'] = "Data saved";
            } else {
                $this->db->trans_rollback();
                $results['success'] = false;
                $results['message'] = "Failed to save data";
            }
            
            return $results;

        }
    }

    public function InsertProcessDetailManual($SupplychainID,$passing = NULL){
        
        $detail         = array();
        $product        = array();
        $total_volume   = 0;

        $totalcapacity = $this->db->query("SELECT
                1 ObjTypeID,
                std.totalcapacity as VolumeNetto,
                st.SupplyTransID ObjID,
                std.DeliveryDetailID,
                ktc.ReceptionID,
                ktsd.SupplyDestMillOrgID,
                ktso.ProductionCapacity,
                ktso.WorkHour,
                ktc.status
                FROM
                ktv_tc_reception ktc
                LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktc.ReceiptDeliveryID
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = ktsdd.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                LEFT JOIN ktv_tc_processing p ON p.SupplychainID = ktsd.SupplyDestMillOrgID
                LEFT JOIN ktv_tc_supplychain_transaction_detail std ON std.DeliveryDetailID = ktsd.DeliveryID    
                LEFT JOIN ktv_tc_supplychain_transaction st ON sb.SupplyBatchID = st.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_org ktso on ktso.SupplychainID = ktsd.SupplyDestMillOrgID
                WHERE
                ktsd.SupplyDestMillOrgID = '$SupplychainID'
                AND ktsd.DeliveryStatusID = '4'
                AND std.Weight IS NOT NULL
                AND ktc.status = '0'
                -- AND p.StatusGenerate IS NULL
                GROUP BY ktc.ReceptionID
                -- ORDER BY ktc.ReceptionID DESC
                ");
        $getDataCapacity = $totalcapacity->result_array();
        // echo '<pre>'.$this->db->last_query();die;
        if(!empty($getDataCapacity)){
            
            foreach ($getDataCapacity as $key => $value){
                $totalNetto += $value['VolumeNetto'];
            }
           
            $VolumeNetto = $totalNetto;
    
            foreach($getDataCapacity as $key => $row){
                
                if($row){
                    $GenerateFrom = $passing['ProductID'];

                    if($GenerateFrom == 1){
                        $from = 'CPO'; 
                    } else {
                        $from = 'PK';
                    }

                    $process["ProcessingNumber"]   = time();
                    $process["ProcessingDate"]     = date("Y-m-d");
                    $process["SupplychainID"]      = $row['SupplyDestMillOrgID'];
                    $process["ProductionCapacity"] = $row['ProductionCapacity'];
                    $process["WorkHour"]           = $row['WorkHour'];
                    $process["GenerateDelivery"]   = $row['DeliveryDetailID'];
                    $process["GenerateFrom"]       = $from;
                    $process["TotalCapacity"]      = $row['VolumeNetto'];
                    $process["StatusGenerate"]     = '1';
                    $process["StatusCode"]         = 'active';
                    $process["CreatedBy"]          = 1;
                    $process["DateCreated"]        = date("Y-m-d H:i:s");
                    $this->db->insert("ktv_tc_processing",$process);

                    $ProcessingID  = $this->db->insert_id();
                    
                    $detail[$key]["ProcessingID"]       = $ProcessingID;
                    $detail[$key]["ObjTypeID"]          = $row['ObjTypeID'];
                    $detail[$key]["ObjID"]              = $row['ObjID'];
                    $detail[$key]["ProcessingVolume"]   = $VolumeNetto;
                    $detail[$key]["StatusCode"]         = 'active';
                    $detail[$key]["CreatedBy"]          = 1;
                    $detail[$key]["DateCreated"]        = date("Y-m-d H:i:s");

                    $this->db->insert_batch("ktv_tc_processing_detail",$detail);
                }
            }

            $total_volume = $VolumeNetto;

            if($total_volume > 0){
                    
                $sql2 = "SELECT
                        pd.*
                    FROM
                        ktv_tc_supplychain_product pd
                    WHERE
                        pd.StatusCode='active'
                        AND NOW() BETWEEN pd.StartDate AND pd.EndDate
                        AND pd.SupplychainID = ?";
                $query2 = $this->db->query($sql2,array($SupplychainID));
                
                if($query2->num_rows()>0){

                    $getData = $query2->result();
    
                    if (!empty($passing['ProductID'])) {
                        foreach ($getData as $key => $value) {
                            if ((int) $value->ProductID != (int) $passing['ProductID']) {
                                unset($getData[$key]);
                            }
                        }
                    }

                    if ($passing != NULL) {
                        foreach($passing['fromPopUp'] as $key => $value){
                            $setProduction = $value->setProduction;
                        }
                        if ($passing['HaveOer'] == 1) {
                            if ($passing['ProductID'] == "1") {
                                $ProductPercentage = '';
                                
                                $ProductVolume     = (float) $total_volume;
                                $RemainingVolume   = (float) $setProduction;
                                $flagOer           = 1;
                            } else if ($passing['ProductID'] == "2") {
                                $ProductPercentage = '';
                                $ProductVolume     = (float) $total_volume;
                                $RemainingVolume   = (float) $setProduction;
                                $flagOer           = 1;
                            } else {
                                $ProductPercentage = '';
                                $ProductVolume     = (float) $total_volume;
                                $RemainingVolume   = (float) $setProduction;
                                $flagOer           = 1;
                            }
                        } else {
                            $ProductPercentage = '';
                            $ProductVolume     = (float) $total_volume;
                            $RemainingVolume   = (float) $setProduction;
                            $flagOer           = 0;
                        }

                        $product[$num]["ProcessingID"]       = $ProcessingID;
                        $product[$num]["ProductID"]          = $passing['ProductID'];
                        $product[$num]["ProductPercentage"]  = $ProductPercentage;
                        $product[$num]["ProductVolume"]      = $ProductVolume;
                        $product[$num]["RemainingVolume"]    = $RemainingVolume;
                        $product[$num]["flagOer"]            = $flagOer;
                        $product[$num]["StatusCode"]         = 'active';
                        $product[$num]["CreatedBy"]          = 1;
                        $product[$num]["DateCreated"]        = date("Y-m-d H:i:s");
                    }   

                    $this->db->insert_batch("ktc_tc_processing_product",$product);
                    
                    return array("jml_data"=>count($getData),"total_volume"=>$total_volume,"product"=>$product,"data"=>$detail);
                }
            }
        }
    }

    public function InsertProcessDetailAutomated($SupplychainID,$passing = NULL){
    
        $detail         = array();
        $product        = array();
        $total_volume   = 0;

        $totalcapacity = $this->db->query("SELECT
                1 ObjTypeID,
                std.totalcapacity as VolumeNetto,
                st.SupplyTransID ObjID,
                std.DeliveryDetailID,
                ktc.ReceptionID,
                ktsd.SupplyDestMillOrgID,
                ktso.ProductionCapacity,
                ktso.WorkHour,
                ktc.status,
                p.TotalCapacity
                FROM
                ktv_tc_reception ktc
                LEFT JOIN ktv_tc_supplychain_delivery_detail ktsdd ON ktsdd.DeliveryID = ktc.ReceiptDeliveryID
                LEFT JOIN ktv_tc_supplychain_batch sb ON sb.SupplyBatchID = ktsdd.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_delivery ktsd ON ktsd.DeliveryID = ktsdd.DeliveryID
                LEFT JOIN ktv_tc_processing p ON p.SupplychainID = ktsd.SupplyDestMillOrgID
                LEFT JOIN ktv_tc_supplychain_transaction_detail std ON std.DeliveryDetailID = ktsd.DeliveryID    
                LEFT JOIN ktv_tc_supplychain_transaction st ON sb.SupplyBatchID = st.SupplyBatchID
                LEFT JOIN ktv_tc_supplychain_org ktso on ktso.SupplychainID = ktsd.SupplyDestMillOrgID
                WHERE
                ktsd.SupplyDestMillOrgID = '$SupplychainID'
                AND ktsd.DeliveryStatusID = '4'
                AND std.Weight IS NOT NULL
                -- AND p.StatusGenerate IS NULL
                -- GROUP BY ktc.ReceptionID
                ORDER BY p.processingid DESC
                ");
        $getDataCapacity = $totalcapacity->result_array();
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
        // exit;
        if(!empty($getDataCapacity)){
            
            foreach ($getDataCapacity as $key => $value){

                //cek lagi setelah cpo dan pk di generate
                if($value['status'] == '0'){

                    $SID = $_SESSION['SupplychainID'];

                    $sqlReception = $this->db->query(
                        "SELECT 
                           SUM(kttd.TotalCapacity) AS total,
                           ktr.SupplychainID,
                           kttd.SupplyTransID,
                           ktr.ReceiptDeliveryID
                        FROM 
                            ktv_tc_reception ktr
                        LEFT JOIN 
                            ktv_tc_supplychain_transaction_detail kttd ON kttd.DeliveryDetailID = ktr.ReceiptDeliveryID
                        WHERE
                            ktr.SupplychainID = '$SID'
                        AND
                            ktr.status = '0'");
                    
                    $chekReception = $sqlReception->result_array();

                    if(!empty($chekReception)){
                        foreach($chekReception as $key => $data){

                            $totalcapacity      = $data['total'];
                            $SupplychainID      = $data['SupplychainID'];
                            $SupplyTransID      = $data['SupplyTransID'];
                            $ReceiptDeliveryID  = $data['ReceiptDeliveryID'];

                            if($totalcapacity){
                                
                                $GenerateFrom = $passing['ProductID'];

                                if($GenerateFrom == 1){
                                    $from = 'CPO'; 
                                } else {
                                    $from = 'PK';
                                }
                                
                                $total_volume = $totalcapacity;

                                $process["ProcessingNumber"]   = time();
                                $process["ProcessingDate"]     = date("Y-m-d");
                                $process["SupplychainID"]      = $SupplychainID;
                                $process["ProductionCapacity"] = "";
                                $process["WorkHour"]           = "";
                                $process["GenerateDelivery"]   = $ReceiptDeliveryID;
                                $process["GenerateFrom"]       = $from;
                                
                                $process["TotalCapacity"]       = $total_volume;

                                $process["StatusGenerate"]     = '1';
                                $process["StatusCode"]         = 'active';
                                $process["CreatedBy"]          = 1;
                                $process["DateCreated"]        = date("Y-m-d H:i:s");

                                $this->db->insert("ktv_tc_processing",$process);
            
                                $ProcessingID  = $this->db->insert_id();
                                
                                $detail[$key]["ProcessingID"]       = $ProcessingID;
                                $detail[$key]["ObjTypeID"]          = 1;
                                $detail[$key]["ObjID"]              = $SupplyTransID;
                                $detail[$key]["ProcessingVolume"]   = $totalcapacity;
                                $detail[$key]["StatusCode"]         = 'active';
                                $detail[$key]["CreatedBy"]          = 1;
                                $detail[$key]["DateCreated"]        = date("Y-m-d H:i:s");
            
                                $this->db->insert_batch("ktv_tc_processing_detail",$detail);
                            }

                            if($totalcapacity > 0){
                    
                                $sql2 = "SELECT
                                        pd.*
                                    FROM
                                        ktv_tc_supplychain_product pd
                                    WHERE
                                        pd.StatusCode='active'
                                        AND NOW() BETWEEN pd.StartDate AND pd.EndDate
                                        AND pd.SupplychainID = ?";
                                $query2 = $this->db->query($sql2,array($SupplychainID));
                                
                                if($query2->num_rows()>0){
                                    $getData = $query2->result();
                    
                                    if (!empty($passing['ProductID'])) {
                                        foreach ($getData as $key => $value) {
                                            if ((int) $value->ProductID != (int) $passing['ProductID']) {
                                                unset($getData[$key]);
                                            }
                                        }
                                    }
                                    
                                    if ($passing != NULL) {
                                        if ($passing['HaveOer'] == 2) {
                                            if ($passing['ProductID'] == "1") {
                                                $ProductPercentage = (float) $passing['ProductPercentageCpo'];
                                                $ProductVolume     = ((float) $total_volume * $ProductPercentage)/100;
                                                $RemainingVolume   = ((float) $total_volume * $ProductPercentage)/100;
                                                $flagOer           = 2;
                                            } else if ($passing['ProductID'] == "2") {
                                                $ProductPercentage = (float) $passing['ProductPercentagePk'];
                                                $ProductVolume     = ((float) $total_volume * $ProductPercentage)/100;
                                                $RemainingVolume   = ((float) $total_volume * $ProductPercentage)/100;
                                                $flagOer           = 2;
                                            } else {
                                                $ProductPercentage = (float) $row2->ProductPercentage;
                                                $ProductVolume     = ((float) $total_volume * $ProductPercentage)/100;
                                                $RemainingVolume   = ((float) $total_volume * $ProductPercentage)/100;
                                                $flagOer           = 2;
                                            }
                                        } else {
                                            $ProductPercentage = (float) $row2->ProductPercentage;
                                            $ProductVolume     = ((float) $total_volume * $ProductPercentage)/100;
                                            $RemainingVolume   = ((float) $total_volume * $ProductPercentage)/100;
                                            $flagOer           = 0;
                                        }
                
                                        $product[$num]["ProcessingID"]       = $ProcessingID;
                                        $product[$num]["ProductID"]          = $passing['ProductID'];
                                        $product[$num]["ProductPercentage"]  = $ProductPercentage;
                                        $product[$num]["ProductVolume"]      = $ProductVolume;
                                        $product[$num]["RemainingVolume"]    = $RemainingVolume;
                                        $product[$num]["flagOer"]            = $flagOer;
                                        $product[$num]["StatusCode"]         = 'active';
                                        $product[$num]["CreatedBy"]          = 1;
                                        $product[$num]["DateCreated"]        = date("Y-m-d H:i:s");
                                    }   

                                    $this->db->insert_batch("ktc_tc_processing_product",$product);
                                    
                                    return array("jml_data"=>count($getData),"total_volume"=>$total_volume,"product"=>$product,"data"=>$detail);
                                }
                            }
                        }
                    }
                }

                $totalNetto = $value['TotalCapacity'];
            }
    
            foreach($getDataCapacity as $key => $row){

                if($row){
                    $GenerateFrom = $passing['ProductID'];

                    if($GenerateFrom == 1){
                        $from = 'CPO'; 
                    } else {
                        $from = 'PK';
                    }
                    
                    $VolumeNetto = $row['TotalCapacity'];

                    $process["ProcessingNumber"]   = time();
                    $process["ProcessingDate"]     = date("Y-m-d");
                    $process["SupplychainID"]      = $row['SupplyDestMillOrgID'];
                    $process["ProductionCapacity"] = $row['ProductionCapacity'];
                    $process["WorkHour"]           = $row['WorkHour'];
                    $process["GenerateDelivery"]   = $row['DeliveryDetailID'];

                    $process["TotalCapacity"]       = $VolumeNetto;

                    $process["GenerateFrom"]       = $from;
                    $process["StatusGenerate"]     = '1';
                    $process["StatusCode"]         = 'active';
                    $process["CreatedBy"]          = 1;
                    $process["DateCreated"]        = date("Y-m-d H:i:s");

                    $this->db->insert("ktv_tc_processing",$process);

                    $ProcessingID  = $this->db->insert_id();
                    
                    $detail[$key]["ProcessingID"]       = $ProcessingID;
                    $detail[$key]["ObjTypeID"]          = $row['ObjTypeID'];
                    $detail[$key]["ObjID"]              = $row['ObjID'];
                    $detail[$key]["ProcessingVolume"]   = $VolumeNetto;
                    $detail[$key]["StatusCode"]         = 'active';
                    $detail[$key]["CreatedBy"]          = 1;
                    $detail[$key]["DateCreated"]        = date("Y-m-d H:i:s");

                    $this->db->insert_batch("ktv_tc_processing_detail",$detail);

                    if($VolumeNetto > 0){
                            
                        $sql2 = "SELECT
                                pd.*
                            FROM
                                ktv_tc_supplychain_product pd
                            WHERE
                                pd.StatusCode='active'
                                AND NOW() BETWEEN pd.StartDate AND pd.EndDate
                                AND pd.SupplychainID = ?";
                        $query2 = $this->db->query($sql2,array($SupplychainID));
                        
                        if($query2->num_rows()>0){
        
                            $getData = $query2->result();
            
                            if (!empty($passing['ProductID'])) {
                                foreach ($getData as $key => $value) {
                                    if ((int) $value->ProductID != (int) $passing['ProductID']) {
                                        unset($getData[$key]);
                                    }
                                }
                            }
                            
                            if ($passing != NULL) {
        
                                if ($passing['HaveOer'] == 2) {
                                    if ($passing['ProductID'] == "1") {
                                        $ProductPercentage = (float) $passing['ProductPercentageCpo'];
                                        $ProductVolume     = ((float) $VolumeNetto * $ProductPercentage)/100;
                                        $RemainingVolume   = ((float) $VolumeNetto * $ProductPercentage)/100;
                                        $flagOer           = 2;
                                    } else if ($passing['ProductID'] == "2") {
                                        $ProductPercentage = (float) $passing['ProductPercentagePk'];
                                        $ProductVolume     = ((float) $VolumeNetto * $ProductPercentage)/100;
                                        $RemainingVolume   = ((float) $VolumeNetto * $ProductPercentage)/100;
                                        $flagOer           = 2;
                                    } else {
                                        $ProductPercentage = (float) $row2->ProductPercentage;
                                        $ProductVolume     = ((float) $VolumeNetto * $ProductPercentage)/100;
                                        $RemainingVolume   = ((float) $VolumeNetto * $ProductPercentage)/100;
                                        $flagOer           = 2;
                                    }
                                } else {
                                    $ProductPercentage = (float) $row2->ProductPercentage;
                                    $ProductVolume     = ((float) $VolumeNetto * $ProductPercentage)/100;
                                    $RemainingVolume   = ((float) $VolumeNetto * $ProductPercentage)/100;
                                    $flagOer           = 0;
                                }
        
                                $product[$num]["ProcessingID"]       = $ProcessingID;
                                $product[$num]["ProductID"]          = $passing['ProductID'];
                                $product[$num]["ProductPercentage"]  = $ProductPercentage;
                                $product[$num]["ProductVolume"]      = $ProductVolume;
                                $product[$num]["RemainingVolume"]    = $RemainingVolume;
                                $product[$num]["flagOer"]            = $flagOer;
                                $product[$num]["StatusCode"]         = 'active';
                                $product[$num]["CreatedBy"]          = 1;
                                $product[$num]["DateCreated"]        = date("Y-m-d H:i:s");
        
                            }   
                            
                            $this->db->insert_batch("ktc_tc_processing_product",$product);
                            
                            return array("jml_data"=>count($getData),"VolumeNetto"=>$VolumeNetto,"product"=>$product,"data"=>$detail);
                        }
                    }
                }
            }
        }
    }

    public function generateKPIKoltiva(){
        $this->db->trans_start();

        //Select data2 yg diperlukan
        $sql = "SELECT
                far.FarmerCount
                , plan.FarmCount
                , plan.FarmAreaHa
                , 0 TrainOrCoachFarmers
                , 0 ResponsibleSourcing
                , sme.SMECount
            FROM
                (
                    SELECT 'Palmoil' AS Platform
                ) AS plat
            LEFT JOIN (
                SELECT
                    'Palmoil' Platform
                    , COUNT(m.MemberID) FarmerCount
                FROM
                    ktv_members m
                LEFT JOIN
                    ktv_member_role mr on mr.MemberID = m.MemberID
                LEFT JOIN
                    ktv_ref_member_role rm on rm.MRoleID = mr.MRoleID
                WHERE
                    m.StatusCode = 'active'
                AND
                    rm.MRoleType = 'Farmer'
            ) far on far.Platform = plat.Platform
            LEFT JOIN (
                SELECT
                    a.Platform
                    , SUM(a.FarmCount) FarmCount
                    , SUM(a.FarmAreaHa) FarmAreaHa
                FROM
                (
                SELECT
                    'Palmoil' Platform,
                    COUNT(
                    DISTINCT CONCAT( sp.MemberID, sp.PlotNr )) FarmCount,
                    SUM( sp.GardenAreaHa ) FarmAreaHa 
                FROM
                    ktv_survey_plot sp
                    INNER JOIN ktv_members m ON m.MemberID = sp.MemberID
                WHERE
                    m.StatusCode = 'active'
                UNION
                SELECT
                    'Palmoil' Platform,
                    COUNT(
                    DISTINCT CONCAT( sp.MemberID, sp.PlotNr )) FarmCount,
                    SUM( sp.GardenAreaHa ) FarmAreaHa 
                FROM
                    ktv_survey_plot_sme sp
                    INNER JOIN ktv_members m ON m.MemberID = sp.MemberID 
                WHERE
                    m.StatusCode = 'active'
                ) a
                GROUP BY a.Platform
            ) plan on plan.Platform = plat.Platform
            LEFT JOIN (
                SELECT
                    'Palmoil' Platform
                    , COUNT(m.MemberID) SMECount
                FROM
                    ktv_members m
                LEFT JOIN
                    ktv_member_role mr on mr.MemberID = m.MemberID
                LEFT JOIN
                    ktv_ref_member_role rm on rm.MRoleID = mr.MRoleID
                WHERE
                    m.StatusCode = 'active'
                AND
                    rm.MRoleType = 'Agent'
            ) sme on sme.Platform = plat.Platform 
        ";
        $DataMain = $this->db->query($sql)->row_array();

        $sql2 = "SELECT
                    COUNT(a.`UserId`) AS CountWebUser
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                    INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                WHERE 1=1
                    AND a.`StatusCode` = 'active'
                    AND a.`UserActive` = 'Yes'
                    AND b.`StatusCd` = 'active'
                    AND c.`StatusCode` = 'active'";
        $DataSysUser = $this->db->query($sql2)->row_array();

        //FarmX
        $sql = "SELECT
                    COUNT(a.`UserId`) AS FarmXUser
                FROM
                    sys_user a
                    INNER JOIN ktv_persons b ON a.`UserId` = b.`UserID`
                    INNER JOIN ktv_staffs c ON b.`PersonID` = c.`PersonID`
                WHERE 1=1
                    AND a.`StatusCode` = 'active'
                    AND a.`UserActive` = 'Yes'
                    AND b.`StatusCd` = 'active'
                    AND c.`StatusCode` = 'active'
                    AND a.`UserExtId` != ''
                    AND a.`UserExtId` IS NOT NULL";
        $DataFarmX = $this->db->query($sql)->row_array();

        //Transaction

        $sql = "SELECT
                COUNT(st.SupplyTransID) TotalTransaction
            FROM
                ktv_tc_supplychain_transaction st
            WHERE
                st.StatusCode = 'active'";
        $DataTransaction = $this->db->query($sql)->row_array();

        //FarmGate
        $sql = "SELECT
                    COUNT( a.`UserID` ) AS FarmGateUser 
                FROM
                    view_tc_supplychain_staff a
                    INNER JOIN sys_user b ON a.`UserID` = b.`UserId` 
                WHERE
                    1 = 1 
                    AND b.`UserActive` = 'Yes'
                    AND b.UserTorStatus = 1
                    AND b.UserMobileToken IS NOT NULL";
        $DataFarmGate = $this->db->query($sql)->row_array();

        //FarmCloud
        $sql = "SELECT
                    COUNT(a.`FarmerID`) AS FarmCloudUser
                FROM
                    `sys_farmer_user` a
                WHERE 1=1
                    AND a.`StatusUser` = 'Active'";
        $DataFarmCloud = $this->db->query($sql)->row_array();

        //Data Update
        $FarmerCount    = $DataMain['FarmerCount'];
        $TrainCoach     = $DataMain['TrainOrCoachFarmers'];
        $CountFarm      = $DataMain['FarmCount'];
        $SumHa          = $DataMain['FarmAreaHa'];
        $ResponsibleSourcing = $DataMain['ResponsibleSourcing'];
        $CountSme       = $DataMain['SMECount'];
        $CountWebUser   = $DataSysUser['CountWebUser'];
        $FarmXUser      = $DataFarmX['FarmXUser'];
        $FarmGateUser   = $DataFarmGate['FarmGateUser'];
        $FarmRetailUser = 0;
        $FarmCloudUser  = $DataFarmCloud['FarmCloudUser'];
        $CountWebUser   = $CountWebUser - ($FarmXUser + $FarmGateUser);
        $TraceTransaction = $DataTransaction['TotalTransaction'];

        $sql = "UPDATE `dash_kpi_koltiva` SET
                    `RegisteredFarmer` = ?,
                    `TrainOrCoachFarmers` = ?,
                    `RegisteredPlantation` = ?,
                    `RegisteredPlantationHectares` = ?,
                    `ResponSourcingFarmers` = ?,
                    `TraceTransaction` = ?,
                    `PlatformUsers` = ?,
                    `RegisteredSME` = ?,
                    `FarmXUsers` = ?,
                    `FarmGateUsers` = ?,
                    `FarmRetailUsers` = ?,
                    `FarmCloudUsers` = ?,
                    `DateGenerated` = NOW()
                WHERE 
                    `Platform` = 'Palmoil'
                LIMIT 1";
        $p = array(
            $FarmerCount,
            $TrainCoach,
            $CountFarm,
            $SumHa,
            $ResponsibleSourcing,
            $TraceTransaction,
            $CountWebUser,
            $CountSme,
            $FarmXUser,
            $FarmGateUser,
            $FarmRetailUser,
            $FarmCloudUser
        );
        $query = $this->db->query($sql,$p);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $results['success'] = true;
            $results['message'] = "Success";
        }
        return $results;
    }

    public function updatePolygonArea(){
        $this->db->trans_start();
        $sql = "UPDATE ktv_survey_plot_polygon_geo set AreaHa = ST_Area(Polygon)/10000";


        $query = $this->db->query($sql,$p);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $results['success'] = false;
            $results['message'] = "Failed";
        } else {
            $results['success'] = true;
            $results['message'] = "Calculate Area Success";
        }
        return $results;
    }

    public function checkPullEngineStatus(){

        //Prep for Check
        $sql    = "SELECT timecheck_send, uid, timecheck, LastSendEmail FROM mw_check_pullengine_status LIMIT 1";
        $row    = $this->db->query($sql)->row_array();

        $timecheck      = $row["timecheck"];
        $timecheck_send = $row["timecheck_send"];

        $new_time_send  = strtotime(date("Y-m-d H:i:s"));
        $remark         = "Kiriman Data Pada ".$new_time_send;

        //Jika waktu generate dan balikan dari pull engine sama
        if($timecheck_send == $timecheck){
            $datapost["WorkStatus"]     = "Yes";
            $datapost["LastSendEmail"]  = "Not Send Yet";
            $datapost["timecheck_send"] = $new_time_send;

            $this->db->update("mw_check_pullengine_status", $datapost);
        }

        //Jika waktu generate dan tidak ada balikan dari pull engine
        if($timecheck_send != $timecheck){
            $datapost["WorkStatus"]     = "No";
            $datapost["timecheck_send"] = $new_time_send;

            //cek status kirim email, jika belum dikirim proses kirim email
            if($row["LastSendEmail"] == "Not Send Yet"){
                $content    = $this->getContentCheckPullEngine(); // Set email content
                $list_email = $this->getReceipentEmail(); // List penerima email error
                
                $post       = $this->sendEmail($content, $list_email); // Proses kirim email
                $datapost["LastSendEmail"] = $post; // Status kirim email
            }

            $this->db->update("mw_check_pullengine_status", $datapost);
        }

        $post = $this->sendToSync($new_time_send, $remark); //Kirim Sync

        $data["success"] = true;
        $data["message"] = json_decode($post)->result;
        return $data;

    }

    public function getContentCheckPullEngine(){        
        return $this->load->view("email/check_pull_engine_status", '', true);
    }

    public function getReceipentEmail(){
        $sql    = "SELECT SetValue FROM `sys_setting` WHERE SetKey = 'pullengine_check_email'";
        $row    = $this->db->query($sql)->row_array();

        $tmpmail = explode(";", $row["SetValue"]);

        return $tmpmail;
    }

    public function sendEmail($content, $list_email){

        $params = array(
            'credentials'=> array(
                'key' => 'AKIAXV2QEJE4KG254CIV',
                'secret' => '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',
            ),
            'region' => 'us-east-1',
            'version' => 'latest'
        );

        $SesClient = new SesClient($params);
        $sender_email = 'no-reply@email.koltiva.com';
        $recipient_emails = $list_email;
        // Specify a configuration set. If you do not want to use a configuration comment it or delete.
        //$configuration_set = 'ConfigSet';
        $subject = 'Warning delay of Pull Engine System (PalmoilTrace)';
        $html_body = $content;
        $char_set = 'UTF-8';

        try {
            $result = $SesClient->sendEmail([
                'Destination'=> [
                    'ToAddresses'=> $recipient_emails,
                ],
                'ReplyToAddresses'=> $list_email,
                'Source'=> $sender_email,
                'Message'=> [
                    'Body'=> [
                        'Html'=> [
                            'Charset'=> $char_set,
                            'Data'=> $html_body,
                        ]
                    ],
                    'Subject'=> [
                        'Charset'=> $char_set,
                        'Data'=> $subject,
                    ],
                ],
                // If you aren't using a configuration set, comment or delete the following line
                //'ConfigurationSetName' => $configuration_set,
            ]);
            $messageId = $result['MessageId'];
            
            return "Sended";
        } catch (AwsException $e) {
            return "Not Send Yet";
        }
    }

    public function getCollectivaSetting(){
        $sql    = "CALL getCollectivaSettings(null)";
        $query  = $this->db->query($sql)->result_array();

        $keys = array_column($query, 'key_setting');
        $index = array_search('sync_upload_event_url', $keys);

        $urlSync = $query[$index]["value_setting"];


        return $urlSync;
    }

    public function sendToSync($time, $remark){
        $curl       = curl_init();
        $urlSync    = $this->getCollectivaSetting();

        $data[0]["event"]   = "ktvpalm2022";
        $data[0]["action"]  = "TO_UPDATE";
        $data[0]["status"]  = "COMPLETED";
        $data[0]["created"]  = date("Y-m-d H:i:s");
        $data[0]["orgUnit"]  = "";
        $data[0]["program"]  = "fdfE4Og30kW";
        $data[0]["eventDate"]  = date("Y-m-d H:i:s");
        $data[0]["coordinate"]  = array(
            "latitude" => 0,
            "longitude" => 0
        );

        $data[0]["dataValues"]  = [array(
            "value" => $time,
            "storedBy" => "koltiva",
            "fieldName" => "Dataelement 1",
            "dataElement" => "B2TCPGzuSOS"
        ),array(
            "value" => $remark,
            "storedBy" => "koltiva",
            "fieldName" => "Dataelement 2",
            "dataElement" => "dL9TqaFJhOh"
        ),array(
            "value" => "ktvpalm2022",
            "storedBy" => "koltiva",
            "fieldName" => "Pull Engine Check UID_HIDE",
            "dataElement" => "HKXVoL8SkCc"
        )];

        $data[0]["lastUpdated"]  = date("Y-m-d H:i:s");
        $data[0]["programName"]  = "Pull Engine Status Check";
        $data[0]["programStage"]  = "eV4bgR1Ya9k";

        $datapost["events"] = $data;

        $header = array(
            'Content-Type: application/json',
            'sender : mqtt:com.testing.sync.dummy|fcm:com.testing.sync.dummy',
            'appName : FarmX'
        );

        $datacurl[CURLOPT_URL]            = $urlSync;
        $datacurl[CURLOPT_RETURNTRANSFER] = true;
        $datacurl[CURLOPT_ENCODING]       = '';
        $datacurl[CURLOPT_MAXREDIRS]      = 10;
        $datacurl[CURLOPT_TIMEOUT]        = 0;
        $datacurl[CURLOPT_FOLLOWLOCATION] = true;
        $datacurl[CURLOPT_HTTP_VERSION]   = CURL_HTTP_VERSION_1_1;
        $datacurl[CURLOPT_CUSTOMREQUEST]  = 'POST';
        $datacurl[CURLOPT_POSTFIELDS]     = json_encode($datapost);
        $datacurl[CURLOPT_HTTPHEADER]     = $header;

        curl_setopt_array($curl, $datacurl);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

}