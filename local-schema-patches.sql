-- ============================================================
-- Local schema patches for running PalmOilTrace on the demo dump.
--
-- The demo dump (palmoiltrace_demo.sql) is an OLDER schema than the current
-- code, so some queries reference columns/tables that don't exist yet. Each
-- patch below adds a missing object, with the type inferred from how the code
-- uses it (and from sibling columns of the same kind).
--
-- Run ONCE on a freshly imported DB (Oracle MySQL has no ADD COLUMN IF NOT EXISTS):
--   /usr/local/mysql/bin/mysql -h127.0.0.1 -uroot -p<pw> palmoiltrace_demo < local-schema-patches.sql
-- ============================================================

-- Dashboard menu query (SS_Controller::buildMenu -> IFNULL(mm.MenuTypeFlag, mm3.MenuName)).
-- Additive nullable column; NULL preserves the pre-column behavior (falls back to parent name).
ALTER TABLE sys_menu
    ADD COLUMN MenuTypeFlag VARCHAR(150) NULL DEFAULT NULL AFTER MenuParam;

-- Grower main form (mgrower::getMemberBasicDataForm). Yes/No flag, written via
-- "HaveOtherCommodities = ?" from the form post; typed like sibling Have* columns
-- (HaveBPJS / HaveBankAccount are char(1)).
ALTER TABLE ktv_members
    ADD COLUMN HaveOtherCommodities CHAR(1) NULL DEFAULT NULL;

-- Dashboard Traceability Mill (mdboardtraceability::readDataTraceabilityMillBaru) joins
-- ktv_tc_supplychain_delivery_detail ON ... AND StatusCode='active'. Column was missing.
ALTER TABLE ktv_tc_supplychain_delivery_detail
    ADD COLUMN StatusCode ENUM('active','inactive','nullified') NOT NULL DEFAULT 'active';

-- Plot survey detail (mplot_survey detail SELECT) references columns absent from the
-- demo dump. Types inferred from sibling columns + code usage:
--   *LatLong         -> POINT  (read via ST_Latitude/ST_Longitude, like LatLong)
--   photo / location -> VARCHAR(255) (like FarmPhoto / HowObPlantationText / CollectpointAddress)
--   checkbox flags    -> TINYINT (like HowObPlantation / AnyWaterBodies / PlantAround*)
ALTER TABLE ktv_survey_plot
    ADD COLUMN FireVisableLatLong          POINT        NULL,
    ADD COLUMN TPHLocation                 VARCHAR(255) NULL,
    ADD COLUMN AdditionalLocation          VARCHAR(255) NULL,
    ADD COLUMN DeliveryByPhoto             VARCHAR(255) NULL,
    ADD COLUMN DocumentWritten             VARCHAR(255) NULL,
    ADD COLUMN AnyPalmAttackDisease        VARCHAR(255) NULL,
    ADD COLUMN AnyPalmAttack               TINYINT      NULL,
    ADD COLUMN PlanReplanting              TINYINT      NULL,
    ADD COLUMN HowObPlantationConvert      TINYINT      NULL,
    ADD COLUMN HowObPlantationInheritance  TINYINT      NULL,
    ADD COLUMN HowObPlantationOther        TINYINT      NULL,
    ADD COLUMN HowObPlantationPurchased    TINYINT      NULL,
    ADD COLUMN HowObPlantationReceived     TINYINT      NULL;

-- MySQL 8 rejects `DateCollection = ''` (error 1525) on a DATETIME column, and no
-- sql_mode disables it. The app uses '' as the sentinel for baseline/empty-date
-- survey rows (imported as NULL/zero-date). Make it a string so '' is valid+matchable.
ALTER TABLE ktv_survey_plot
    MODIFY COLUMN DateCollection VARCHAR(50) NULL DEFAULT NULL;
UPDATE ktv_survey_plot
    SET DateCollection = ''
    WHERE DateCollection IS NULL
       OR DateCollection IN ('0000-00-00 00:00:00', '0000-00-00');
