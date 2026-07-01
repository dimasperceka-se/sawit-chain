<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Msync_event
 *
 * Versi CodeIgniter dari Koltitrace_Global\SyncController (Laravel).
 * Perbedaan utama: TANPA cache / redis / async (Kafka). Event yang dikirim
 * dari mobile diproses & disimpan langsung (synchronous) ke database:
 *   - mw2_event_json        : penyimpanan raw event JSON
 *   - ktv_survey_result     : hasil survei dalam bentuk JSON (dibaca balik mobile)
 *   - <table_reff>          : mapping kolom per dataElement via tabel mw_mapping
 *   - sys_log_sync_status   : status sync per event
 *   - log_sync_upload       : log payload upload
 *
 * @author  Sawitchain
 */
class Msync_event extends CI_Model
{
    /** dataElement uid entitas/objek (mengikuti SyncController Laravel) */
    const DE_ENTITY = 'ayd6Qh44yVU';

    /** dataElement uid year of reporting */
    const DE_YEAR_OF_REPORTING = 'P5rrZ7i3yWX';

    /** parent program uid assessment */
    const PARENT_ASSESSMENT_UID = 'RNAaFLosjvC';

    /** cache kolom tabel per-request supaya tidak query INFORMATION_SCHEMA berulang */
    private $columnCache = array();

    public function __construct()
    {
        parent::__construct();
    }

    /* =====================================================================
     * LOGIN MOBILE
     * ===================================================================== */

    /**
     * Login mobile berdasarkan username & password.
     * Password sys_user disimpan sebagai md5 (mengikuti konvensi mauth).
     * Saat sukses, token disimpan ke sys_user.UserMobileToken.
     *
     * @return array { success, message?/error?, results? }
     */
    public function login($username, $password)
    {
        // validasi input
        $username = trim((string) $username);
        if ($username === '' || (string) $password === '') {
            return array('success' => false, 'code' => 400, 'error' => 'Username dan password wajib diisi');
        }

        $user = $this->db->select('UserId, UserName, UserPassword, UserRealName, UserLanguage, StatusCode')
            ->where('UserName', $username)
            ->get('sys_user')->row();

        // user tidak ada
        if (! $user) {
            return array('success' => false, 'code' => 401, 'error' => 'Username atau password salah');
        }

        // user non-aktif
        if ($user->StatusCode !== 'active') {
            return array('success' => false, 'code' => 403, 'error' => 'Akun tidak aktif');
        }

        // password salah
        if ($user->UserPassword !== md5($password)) {
            return array('success' => false, 'code' => 401, 'error' => 'Username atau password salah');
        }

        // generate & simpan token
        $token = $this->generateToken($user->UserName);
        $this->db->where('UserId', $user->UserId)->update('sys_user', array('UserMobileToken' => $token));

        return array(
            'success' => true,
            'code'    => 200,
            'message' => 'Login berhasil',
            'results' => array(
                'UserID'   => $user->UserId,
                'UserName' => $user->UserName,
                'RealName' => $user->UserRealName,
                'Language' => $user->UserLanguage,
                'Token'    => $token,
            ),
        );
    }

    private function generateToken($username)
    {
        return hash_pbkdf2('sha512', 'bismillah'.$username.date('Y-m-d H:i:s'), 'NIKOSB_VC', 10, 32);
    }

    /* =====================================================================
     * ENTRY POINT : SYNC DATA DARI MOBILE (pengganti syncEvents + Kafka)
     * ===================================================================== */

    /**
     * Proses semua event dari payload mobile secara synchronous.
     *
     * @param array  $payload  hasil json_decode body (berisi key "events")
     * @param string $sender   header sender
     * @param string $appName  header appName
     * @param string $username username pengirim (header/payload)
     *
     * @return array response siap dikembalikan controller
     */
    public function syncEvents($payload, $sender = '', $appName = '', $username = '')
    {
        if (empty($payload['events']) || ! is_array($payload['events'])) {
            return array('status' => false, 'result' => 'No events to process');
        }

        $rawPayload = json_encode($payload);
        $firstEvent = $payload['events'][0];
        $firstEventUid = isset($firstEvent['event']) ? $firstEvent['event'] : null;
        $firstSyncUid = isset($firstEvent['syncUid']) ? $firstEvent['syncUid'] : null;

        // proses simpan setiap event
        $processed = array();
        $failed = array();

        // echo "<pre>";print_r($payload);echo "</pre>";die;

        $this->db->trans_begin();
        try {
            foreach ($payload['events'] as $event) {
                if (empty($event['event']) || empty($event['program'])) {
                    continue;
                }
                $this->saveEvent($event, $sender, $appName, $username, $rawPayload);
                $processed[] = $event['event'];
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Database transaction failed');
            }
            $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();

            $this->insertLogSyncPublish(
                'publish failed error : '.$e->getMessage(),
                $rawPayload,
                $username,
                'Sync API',
                $firstSyncUid,
                $firstEventUid
            );

            return array(
                'success' => false,
                'status'  => false,
                'result'  => $e->getMessage(),
            );
        }

        $this->insertLogSyncPublish(
            'publish success message : success',
            $rawPayload,
            $username,
            'Sync API',
            $firstSyncUid,
            $firstEventUid
        );

        return array(
            'success'   => true,
            'status'    => true,
            'result'    => 'Sync processed',
            'processed' => $processed,
        );
    }

    /**
     * Simpan satu event ke seluruh tabel target (synchronous).
     */
    private function saveEvent($event, $sender, $appName, $username, $rawPayload)
    {
        $eventUid = $event['event'];
        $programUid = $event['program'];
        $dataValues = isset($event['dataValues']) && is_array($event['dataValues']) ? $event['dataValues'] : array();

        $storedBy = $username;
        if ($storedBy === '' && isset($dataValues[0]['storedBy'])) {
            $storedBy = $dataValues[0]['storedBy'];
        }
        $userId = $this->getUserId($storedBy);
        $now = date('Y-m-d H:i:s');

        // -- a. raw event ke mw2_event_json
        $r = $this->saveEventJson($eventUid, $programUid, json_encode($event), $now);
        $this->logPull($eventUid, 'mw2_event_json', $r);

        // -- b. ktv_survey_result (bentuk JSON)
        $r = $this->saveSurveyResult($event, $dataValues, $userId, $now);
        $this->logPull($eventUid, 'ktv_survey_result', $r);

        // -- c. mapping ke table_reff per dataElement via mw_mapping
        $this->saveToMappedTables($programUid, $eventUid, $dataValues, $userId, $now, $storedBy);

        // -- d. status sync sukses
        $syncUid = isset($event['syncUid']) ? $event['syncUid'] : null;
        $this->insertSyncStatus($eventUid, $syncUid, 'success', '~error~:[]', $now);
        $this->logPull($eventUid, '', array('action' => 'done', 'query' => 'sender'));
    }

    /**
     * Catat satu langkah mapping ke mw_pull_log2019 (mengikuti consumer asli),
     * supaya proses mapping terlihat & bisa ditelusuri.
     */
    private function logPull($eventUid, $table, $result)
    {
        if (! is_array($result)) {
            return;
        }
        $action = isset($result['action']) ? $result['action'] : 'unknown';

        // err_msg: 'done' bila sukses, selain itu reason/error
        if ($action === 'insert' || $action === 'update') {
            $err = 'done';
        } elseif ($action === 'error') {
            $err = isset($result['error']) ? $result['error'] : 'error';
        } else { // skip
            $err = isset($result['reason']) ? 'skip:'.$result['reason'] : 'skip';
        }

        $query = isset($result['query']) ? $result['query'] : $action;

        $this->db->insert('mw_pull_log2019', array(
            'eventuid'  => $eventUid,
            'table_reff' => $table,
            'query'     => $query,
            'err_msg'   => $err,
            'date_exec' => date('Y-m-d H:i:s'),
        ));
    }

    /* =====================================================================
     * PENYIMPANAN
     * ===================================================================== */

    private function saveEventJson($eventUid, $programUid, $eventJson, $now)
    {
        $row = array(
            'event_json'   => $eventJson,
            'event_uid'    => $eventUid,
            'program_uid'  => $programUid,
            'date_created' => $now,
        );
        return $this->upsertByUid('mw2_event_json', $row, 'event_uid', $eventUid);
    }

    private function saveSurveyResult($event, $dataValues, $userId, $now)
    {
        // bangun map dataelement_value : { dataElement => value }
        $deValue = array();
        foreach ($dataValues as $dv) {
            if (isset($dv['dataElement'])) {
                $deValue[$dv['dataElement']] = isset($dv['value']) ? $dv['value'] : null;
            }
        }

        $objectUid = isset($deValue[self::DE_ENTITY]) ? $deValue[self::DE_ENTITY] : null;

        $row = array(
            'uid'               => $event['event'],
            'program_uid'       => $event['program'],
            'object_uid'        => $objectUid,
            'dataValues'        => json_encode($dataValues),
            'dataelement_value' => json_encode($deValue),
            'createdby'         => $userId,
            'datecreated'       => isset($event['created']) ? $this->toMysqlDate($event['created'], $now) : $now,
            'dateupdated'       => isset($event['lastUpdated']) ? $this->toMysqlDate($event['lastUpdated'], $now) : $now,
        );

        return $this->upsertByUid('ktv_survey_result', $row, 'uid', $event['event']);
    }

    /**
     * Mapping nilai dataElement ke kolom tabel sesuai mw_mapping (table_reff/field_reff).
     * Mirip getDataElementNameForKafka() + pullMiddlewareData(), tapi synchronous.
     */
    private function saveToMappedTables($programUid, $eventUid, $dataValues, $userId, $now, $storedBy = '')
    {
        // [table => [ {de, field, fn, priority}, ... ]] -- TIDAK di-collapse.
        $mapping = $this->getMappingRows($programUid);
        if (empty($mapping)) {
            return;
        }

        // index dataValues by dataElement
        $values = array();
        foreach ($dataValues as $dv) {
            if (isset($dv['dataElement'])) {
                $values[$dv['dataElement']] = isset($dv['value']) ? $dv['value'] : null;
            }
        }

        // PENTING: beberapa custom_function (setAPMSTA3, setMemberDisplayID) MEMBACA
        // tabel mw_event_values_temp. Isi dulu sebelum memanggil function apa pun.
        $this->populateEventValuesTemp($eventUid, $programUid, $dataValues, $storedBy);

        // Pisahkan tiap baris mapping sesuai aturan priority:
        //  - priority 1            -> ikut INSERT/UPDATE kolom tabel (DML).
        //  - priority >= 2 + fn    -> dijalankan sbg SELECT custom_function() (efek
        //                             samping, mis. setAPMSTA3 -> akses partner + role).
        //  - priority >= 2 tanpa fn -> diperlakukan DML ke tabelnya (fallback).
        // custom_function pada baris DML dipakai utk MENGHITUNG nilai kolom.
        $dmlTables   = array(); // [table => rows]
        $sideEffects = array(); // rows priority>=2 ber-fn (sudah terurut priority dari query)
        foreach ($mapping as $table => $rows) {
            foreach ($rows as $m) {
                if ($m['priority'] >= 2 && $m['fn'] !== null) {
                    $sideEffects[] = $m;
                } else {
                    $dmlTables[$table][] = $m;
                }
            }
        }

        // 1) ktv_members dulu (sumber FK MemberID utk tabel child).
        $memberId = null;
        if (isset($dmlTables['ktv_members'])) {
            $r = $this->upsertMappedTable('ktv_members', $dmlTables['ktv_members'], $values, $eventUid, $programUid, $userId, $now, null);
            $this->logPull($eventUid, 'ktv_members', $r);
            $memberId = $this->resolveMemberId($eventUid);
            unset($dmlTables['ktv_members']);
        }

        // 2) tabel DML lain (priority 1 / priority>=2 tanpa fn) -> inject FK member.
        foreach ($dmlTables as $table => $rows) {
            $r = $this->upsertMappedTable($table, $rows, $values, $eventUid, $programUid, $userId, $now, $memberId);
            $this->logPull($eventUid, $table, $r);
        }

        // 3) side-effect functions (priority>=2) -> SELECT func(eventuid,proguid,deuid,value).
        //    Dijalankan SETELAH baris tabel ada (mis. setAPMSTA3 butuh member by uid).
        foreach ($sideEffects as $m) {
            if (! array_key_exists($m['de'], $values)) {
                $this->logPull($eventUid, $m['fn'], array('action' => 'skip', 'reason' => 'no_dataelement_value'));
                continue;
            }
            $this->applyCustomFunction($eventUid, $programUid, $m['de'], $values[$m['de']], $m['fn']);
            $this->logPull($eventUid, $m['fn'], array('action' => 'function'));
        }
    }

    /**
     * Bangun row dari baris mapping (DML), lalu UPSERT by uid.
     * Tiap field: ada custom_function -> nilai = hasil function; tanpa -> normalizeBool.
     * Untuk tabel child (bukan ktv_members) FK MemberID/FarmerID di-inject.
     */
    private function upsertMappedTable($table, $rows, $values, $eventUid, $programUid, $userId, $now, $memberId)
    {
        $row = array();
        foreach ($rows as $m) {
            if (! array_key_exists($m['de'], $values)) {
                continue;
            }
            $val = $this->applyCustomFunction($eventUid, $programUid, $m['de'], $values[$m['de']], $m['fn']);
            $row[$m['field']] = ($m['fn'] !== null) ? $val : $this->normalizeBool($val);
        }

        if (empty($row)) {
            return array('action' => 'skip', 'reason' => 'no_matching_dataelement');
        }

        $cols = $this->tableColumns($table);
        if (! array_key_exists('uid', $cols)) {
            return array('action' => 'skip', 'reason' => 'no_uid_key');
        }

        // FK ke member utk tabel child (ktv_members dpt MemberID dari setMemberID).
        if ($table !== 'ktv_members' && $memberId) {
            if (array_key_exists('MemberID', $cols)) $row['MemberID'] = $memberId;
            if (array_key_exists('FarmerID', $cols)) $row['FarmerID'] = $memberId;
        }
        $row['uid'] = $eventUid;

        $exists = $this->db->where('uid', $eventUid)->count_all_results($table) > 0;
        if (! $exists) {
            // kolom audit/default utk INSERT baru.
            if (array_key_exists('StatusCode', $cols) && ! isset($row['StatusCode'])) $row['StatusCode'] = 'active';
            if (array_key_exists('DateCreated', $cols)) $row['DateCreated'] = $now;
            if (array_key_exists('DateSync', $cols))    $row['DateSync']    = $now;
            if (array_key_exists('CreatedBy', $cols))   $row['CreatedBy']   = $userId;

            // guard NOT NULL: bila ada kolom wajib kosong, skip terkontrol (jangan
            // gagalkan transaksi seluruh event).
            $missing = array();
            foreach ($this->requiredColumns($table) as $req) {
                if ($req === 'uid') continue;
                if (! isset($row[$req]) || $row[$req] === '' || $row[$req] === null) {
                    $missing[] = $req;
                }
            }
            if (! empty($missing)) {
                return array('action' => 'skip', 'reason' => 'missing_required:'.implode(',', $missing));
            }
        }

        return $this->upsertByUid($table, $row, 'uid', $eventUid, true);
    }

    /**
     * Isi mw_event_values_temp dari dataValues event. Beberapa custom_function
     * (setAPMSTA3, setMemberDisplayID) membaca tabel ini (kolom event, dataelement,
     * datavalue, storedby). Idempoten: hapus dulu baris event ini.
     */
    private function populateEventValuesTemp($eventUid, $programUid, $dataValues, $storedBy)
    {
        $this->db->where('event', $eventUid)->delete('mw_event_values_temp');
        foreach ($dataValues as $dv) {
            if (! isset($dv['dataElement'])) {
                continue;
            }
            $this->db->insert('mw_event_values_temp', array(
                'event'       => $eventUid,
                'program'     => $programUid,
                'dataelement' => $dv['dataElement'],
                'datavalue'   => isset($dv['value']) ? $dv['value'] : null,
                'storedby'    => isset($dv['storedBy']) ? $dv['storedBy'] : $storedBy,
            ));
        }
    }

    /**
     * Hitung nilai sebuah field mapping. Bila ada custom_function, panggil SQL
     * function tsb dgn signature seragam (eventuid, proguid, dataelementuid, value).
     * Bila tidak ada, kembalikan value mentah. Aman thd error -> fallback value.
     */
    private function applyCustomFunction($eventUid, $programUid, $deUid, $rawValue, $fn)
    {
        if ($fn === null || $fn === '') {
            return $rawValue;
        }
        // whitelist nama fungsi (cegah injeksi -- nama tak bisa di-parameterkan).
        if (! preg_match('/^[A-Za-z0-9_]+$/', $fn)) {
            log_message('error', 'custom_function nama tak valid: '.$fn);
            return $rawValue;
        }
        try {
            $q = $this->db->query('SELECT `'.$fn.'`(?, ?, ?, ?) AS v',
                array($eventUid, $programUid, $deUid, $rawValue));
            $r = $q ? $q->row() : null;
            return $r ? $r->v : null;
        } catch (Exception $e) {
            log_message('error', 'custom_function '.$fn.' gagal: '.$e->getMessage());
            return $rawValue;
        }
    }

    /**
     * Simpan/registrasi petani ke ktv_members dari event mobile.
     *
     * Bila petani (uid = eventUid) BELUM ada → INSERT baris baru lengkap dengan
     * identitas yang di-generate server (MemberID/MemberDisplayID), PartnerID
     * milik user pengirim, role Petani (ktv_member_role MRoleID=1), dan hak akses
     * partner (ktv_access_partner_member) — supaya muncul di grid grower web.
     * Bila sudah ada → hanya UPDATE field hasil mapping.
     *
     * Kolom identitas/partner SENGAJA tidak diambil dari mapping karena mw_mapping
     * program ini tidak reliable (mis. VillageName ter-map sekaligus ke PartnerID,
     * MemberDisplayID, dan apmPartnerID).
     */
    private function saveGrowerBaseTable($eventUid, $deMap, $values, $userId, $now)
    {
        // kolom identitas/partner: jangan pernah diambil dari mapping
        $protected = array(
            'MemberID', 'MemberDisplayID', 'MemberUID', 'PartnerID',
            'uid', 'StatusCode', 'DateCollection', 'DateCreated', 'CreatedBy',
        );

        $row = array();
        foreach ($deMap as $deUid => $field) {
            if (in_array($field, $protected, true)) {
                continue;
            }
            if (! array_key_exists($deUid, $values)) {
                continue;
            }
            $row[$field] = $this->normalizeBool($values[$deUid]);
        }
        if (isset($row['Gender'])) {
            $row['Gender'] = $this->normalizeGender($row['Gender']);
        }

        $exists = $this->db->where('uid', $eventUid)->count_all_results('ktv_members') > 0;

        // petani sudah ada → cukup update field mapping (identitas tak disentuh)
        if ($exists) {
            if (empty($row)) {
                return array('action' => 'skip', 'reason' => 'no_matching_dataelement');
            }
            return $this->upsertByUid('ktv_members', $row, 'uid', $eventUid, false);
        }

        // ---- petani BARU : siapkan kolom wajib (NOT NULL) + identitas server ----
        $memberName = isset($row['MemberName']) ? trim((string) $row['MemberName']) : '';
        if ($memberName === '') {
            return array('action' => 'skip', 'reason' => 'missing_member_name');
        }
        if (! isset($row['DateOfBirth']) || $row['DateOfBirth'] === '') {
            // DateOfBirth NOT NULL tanpa default -> tak bisa insert
            return array('action' => 'skip', 'reason' => 'missing_date_of_birth');
        }

        $partnerId = $this->getUserPartnerId($userId);
        if (! $partnerId) {
            return array('action' => 'skip', 'reason' => 'no_partner_for_user');
        }

        if (! isset($row['Gender']) || $row['Gender'] === '') {
            $row['Gender'] = 'm';
        }

        $villageId = isset($row['VillageID']) ? $row['VillageID'] : null;
        $gen = $this->generateMemberId($villageId);

        $row['MemberID']        = $gen['MemberID'];
        $row['MemberDisplayID'] = $gen['MemberDisplayID'];
        $row['MemberUID']       = $eventUid;
        $row['PartnerID']       = $partnerId;
        $row['StatusCode']      = 'active';
        $row['DateCollection']  = $now;
        $row['DateCreated']     = $now;
        $row['DateSync']        = $now;
        $row['CreatedBy']       = $userId;

        $r = $this->upsertByUid('ktv_members', $row, 'uid', $eventUid, true);

        if (isset($r['action']) && $r['action'] === 'insert') {
            // role Petani -> wajib utk INNER JOIN ktv_member_role di grid grower
            $this->db->insert('ktv_member_role', array(
                'MemberID'    => $gen['MemberID'],
                'MRoleID'     => 1,
                'DateCreated' => $now,
                'CreatedBy'   => $userId,
            ));
            // hak akses partner -> member dibuat di ensurePartnerAccess() (dipanggil
            // dari loop mapping) supaya tidak dobel.
        }

        return $r;
    }

    /** MemberID milik petani berdasarkan uid event (FK utk tabel child). */
    private function resolveMemberId($eventUid)
    {
        $row = $this->db->select('MemberID')->where('uid', $eventUid)->get('ktv_members')->row();

        return $row ? $row->MemberID : null;
    }

    /**
     * Pastikan baris hak akses partner->member ada (ktv_access_partner_member),
     * pakai PartnerID milik user pengirim. Wajib utk INNER JOIN hak akses di grid web.
     */
    private function ensurePartnerAccess($memberId, $userId, $now)
    {
        if (! $memberId) {
            return array('action' => 'skip', 'reason' => 'no_member');
        }
        $partnerId = $this->getUserPartnerId($userId);
        if (! $partnerId) {
            return array('action' => 'skip', 'reason' => 'no_partner_for_user');
        }

        $exists = $this->db->where('apmMemberID', $memberId)
            ->where('apmPartnerID', $partnerId)
            ->count_all_results('ktv_access_partner_member') > 0;
        if ($exists) {
            return array('action' => 'skip', 'reason' => 'already_exists');
        }

        $this->db->insert('ktv_access_partner_member', array(
            'apmPartnerID' => $partnerId,
            'apmMemberID'  => $memberId,
            'DateCreated'  => $now,
            'CreatedBy'    => $userId,
        ));

        return array('action' => 'insert', 'fields' => 2);
    }

    /**
     * Generate MemberID (auto-increment manual) & MemberDisplayID, mengikuti
     * konvensi Mgrower::genMemberID (prefix 'F' + 4 digit awal VillageID + nomor
     * urut 9 digit). Dipakai saat registrasi petani baru via sync.
     */
    private function generateMemberId($villageId)
    {
        $r = $this->db->query('SELECT MAX(MemberID) AS m FROM ktv_members')->row();
        $memberId = ($r && $r->m) ? ((int) $r->m + 1) : 1;

        $prefix = 'F'.substr((string) $villageId, 0, 4);
        $display = $prefix.str_pad((string) $memberId, 9, '0', STR_PAD_LEFT);

        return array('MemberID' => $memberId, 'MemberDisplayID' => $display);
    }

    /**
     * PartnerID milik user (mengikuti logika login mcommon): staff private/program
     * pakai ObjID, selain itu pakai PartnerID dari view_tc_supplychain_staff.
     */
    private function getUserPartnerId($userId)
    {
        if (! $userId) {
            return null;
        }
        $sql = "SELECT IF(st.ObjType IN ('private','program'), st.ObjID, vss.PartnerID) AS PartnerID
                FROM sys_user a
                LEFT JOIN ktv_persons p ON p.UserID = a.UserId
                LEFT JOIN ktv_staffs st ON p.PersonID = st.PersonID
                LEFT JOIN view_tc_supplychain_staff vss ON vss.UserID = a.UserId
                WHERE a.UserId = ?
                LIMIT 1";
        $row = $this->db->query($sql, array($userId))->row();

        return ($row && $row->PartnerID) ? $row->PartnerID : null;
    }

    /** Konversi nilai gender mobile (1/2/male/female) ke enum ktv_members('m','f'). */
    private function normalizeGender($value)
    {
        $v = strtolower(trim((string) $value));
        if (in_array($v, array('1', 'm', 'male', 'laki-laki', 'l'), true)) {
            return 'm';
        }
        if (in_array($v, array('2', 'f', 'female', 'perempuan', 'p'), true)) {
            return 'f';
        }

        return 'm';
    }

    /* =====================================================================
     * VALIDASI (port dari SyncController)
     * ===================================================================== */

    public function validateSyncUid($eventUid, $syncUid = null)
    {
        if (! $syncUid) {
            return false;
        }
        $count = $this->db->where('event_uid', $eventUid)
            ->where('sync_uid', $syncUid)
            ->count_all_results('sys_log_sync_status');

        return $count > 0;
    }

    public function validateCampaign($data)
    {
        $result = array('status' => false, 'message' => '');

        $event = $data['events'][0];
        $programId = isset($event['program']) ? $event['program'] : null;
        $campaignId = isset($event['campaignId']) ? $event['campaignId'] : null;
        $dataValue = isset($event['dataValues']) ? $event['dataValues'] : array();
        $userName = isset($dataValue[0]['storedBy']) ? $dataValue[0]['storedBy'] : null;

        if (! $campaignId) {
            return $result;
        }

        $campaign = $this->db->select('Frequency, StartDate, EndDate')
            ->where('CampaignManagementID', $campaignId)
            ->get('campaign_management')->row();

        if (! $campaign || ! $campaign->Frequency) {
            return $result;
        }

        $today = date('Y-m-d');
        $start = date('Y-m-d', strtotime($campaign->StartDate));
        $end = date('Y-m-d', strtotime($campaign->EndDate));

        // hanya validasi jika campaign sedang aktif
        if ($today < $start || $today > $end) {
            return $result;
        }

        $user = $this->db->select('UserId')->where('UserName', $userName)->get('sys_user')->row();
        if (! $user) {
            return $result;
        }

        $count = $this->db->where('program_uid', $programId)
            ->where('createdby', $user->UserId)
            ->where('datecreated >=', $campaign->StartDate)
            ->where('datecreated <=', $campaign->EndDate)
            ->count_all_results('ktv_survey_result');

        if ($count > $campaign->Frequency) {
            $result['status'] = true;
            $result['message'] = "Sorry, but you've reached the maximum allowed frequency for inputting assessments on this entity. ".
                "If you're facing any difficulties specific to this assessment or have inquiries, please reach out to our reliable helpdesk team. Thank you for your understanding.";
        }

        return $result;
    }

    public function validateYearOfReporting($data)
    {
        try {
            $event = $data['events'][0];
            if (empty($event['action'])) {
                return false;
            }

            $program = $this->db->select('parentuid, validateYearOfReporting')
                ->where('uid', $event['program'])
                ->where('validateYearOfReporting', 1)
                ->limit(1)
                ->get('mw_program')->row();

            if (! $program) {
                return false;
            }

            $isParent = ($program->parentuid == self::PARENT_ASSESSMENT_UID);
            if (! $isParent) {
                // skip dulu untuk child (mengikuti SyncController)
                return false;
            }

            $year = $this->findDataValue($event['dataValues'], self::DE_YEAR_OF_REPORTING);
            $entity = $this->findDataValue($event['dataValues'], self::DE_ENTITY);

            if ($year === null) {
                return false;
            }

            $sql = "SELECT uid FROM ktv_survey_result
                    WHERE program_uid = ?
                      AND dataelement_value->>'$.".self::DE_YEAR_OF_REPORTING."' = ?
                      AND uid != ?
                      AND object_uid = ?";
            $q = $this->db->query($sql, array($event['program'], $year, $event['event'], $entity));

            if ($q->num_rows() > 0) {
                return $year;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /* =====================================================================
     * SEND TO MOBILE (download master data + status sync)
     * ===================================================================== */

    /**
     * Versi CI dari send_to_mobile_v2.
     * Membaca dari view send_to_mobile_mapping (use_json=0) atau dari
     * ktv_survey_result (use_json=1), lalu menempelkan status sync per event.
     *
     * @param array $args [ProgramUid, UserName, ExtUid, FarmerUID, syncUid]
     */
    public function sendToMobile($args)
    {
        list($ProgramUid, $UserName, $ExtUid, $EntityUID, $syncUID) = array_pad($args, 5, '');

        $result = array(
            'success'       => 1,
            'UserName'      => $UserName,
            'lastTimestamp' => time(),
            'lastDttm'      => date('Y-m-d H:i:s'),
        );

        $arrExtUid = ($ExtUid !== '') ? array_filter(explode(';', $ExtUid)) : array();
        $syncUIDs = ($syncUID !== '') ? array_filter(explode(';', $syncUID)) : array();

        $arrProgramUid = array_filter(explode(';', $ProgramUid));
        if (empty($arrProgramUid)) {
            return array('success' => 0, 'error' => 'ProgramUid needed');
        }

        $this->db->select('uid, programid, send_to_mobile_mapping, use_json')
            ->from('mw_program')
            ->where('Status', 1)
            ->where_in('uid', $arrProgramUid);
        $programs = $this->db->get()->result();

        foreach ($programs as $program) {
            $result['rows'][$program->uid] = array();
            $referenceTable = $program->send_to_mobile_mapping ? $program->send_to_mobile_mapping : '';
            $useJson = ((int) $program->use_json === 1);

            try {
                if (strlen($referenceTable) > 0 && ! $useJson) {
                    $result['rows'][$program->uid] = $this->getDataFromView(
                        $referenceTable, $arrExtUid, $EntityUID, $syncUIDs
                    );
                } else {
                    $result['rows'][$program->uid] = $this->getDataFromJson(
                        $program->uid, $arrExtUid, $EntityUID, $syncUIDs
                    );
                }
            } catch (Exception $e) {
                // do nothing, lanjutkan program berikutnya
            }
        }

        return $result;
    }

    private function getDataFromView($view, $events, $entityUid, $syncUIDs)
    {
        $sub = $this->buildSyncStatusSubquery($syncUIDs);

        $sql = "SELECT a.*,
                    (SELECT `status` FROM sys_log_sync_status WHERE event_uid = a.uid $sub) AS status_sync,
                    (SELECT `error`  FROM sys_log_sync_status WHERE event_uid = a.uid $sub) AS sync_error
                FROM ".$this->db->escape_str($view).' AS a WHERE 1=1';

        if (count($events) > 0) {
            $sql .= " AND a.uid IN ('".implode("','", $this->escapeArr($events))."')";
        }
        if ($entityUid) {
            $sql .= " AND a.uid = ".$this->db->escape($entityUid);
        }
        $sql .= ' GROUP BY a.uid';

        return $this->finalizeRows($this->db->query($sql)->result_array());
    }

    private function getDataFromJson($programUid, $events, $entityUid, $syncUIDs)
    {
        $sub = $this->buildSyncStatusSubquery($syncUIDs);

        $sql = "SELECT
                    a.result_id id,
                    a.uid,
                    a.program_uid,
                    u.UserName AS username,
                    a.datecreated created,
                    a.dateupdated lastupdate,
                    a.datecreated eventdate,
                    p.uid program,
                    ps.uid programstage,
                    a.dataValues,
                    (SELECT `status` FROM sys_log_sync_status WHERE event_uid = a.uid $sub) AS status_sync,
                    (SELECT `error`  FROM sys_log_sync_status WHERE event_uid = a.uid $sub) AS sync_error
                FROM ktv_survey_result a
                JOIN mw_program p ON p.uid = a.program_uid
                LEFT JOIN mw_programstage ps ON ps.programid = p.programid
                LEFT JOIN sys_user u ON u.UserId = a.createdby
                LEFT JOIN nf_entity_master su ON su.uid = a.object_uid
                WHERE a.program_uid = ?";

        $params = array($programUid);

        if (count($events) > 0) {
            $sql .= " AND a.uid IN ('".implode("','", $this->escapeArr($events))."')";
        }
        if ($entityUid) {
            $sql .= ' AND su.uid = '.$this->db->escape($entityUid);
        }
        $sql .= ' GROUP BY a.uid';

        return $this->finalizeRows($this->db->query($sql, $params)->result_array());
    }

    private function buildSyncStatusSubquery($syncUIDs)
    {
        if (count($syncUIDs) > 0) {
            return " AND sync_uid IN ('".implode("','", $this->escapeArr($syncUIDs))."') ORDER BY date_created DESC LIMIT 1";
        }

        return ' ORDER BY date_created DESC LIMIT 1';
    }

    /**
     * Bersihkan null + set default status_sync (mirip SyncController V2).
     */
    private function finalizeRows($rows)
    {
        foreach ($rows as &$row) {
            // status default in_process bila belum ada log
            if (! isset($row['status_sync']) || $row['status_sync'] === null || $row['status_sync'] === '') {
                $row['status_sync'] = 'in_process';
                $row['sync_error'] = '';
            } elseif ($row['status_sync'] === 'send to kafka') {
                $row['status_sync'] = 'success';
            }

            // remove null values
            $row = array_filter($row, function ($v) {
                return $v !== null && $v !== '';
            });
        }

        return $rows;
    }

    /* =====================================================================
     * LOGGING
     * ===================================================================== */

    private function insertSyncStatus($eventUid, $syncUid, $status, $error, $now)
    {
        $this->db->insert('sys_log_sync_status', array(
            'id'           => $this->shortUuid(),
            'event_uid'    => $eventUid,
            'sync_uid'     => $syncUid,
            'status'       => $status,
            'error'        => $error,
            'date_created' => $now,
        ));
    }

    public function insertLogSyncPublish($remark, $payload, $username, $sender, $syncUid, $eventUid)
    {
        $this->db->insert('log_sync_upload', array(
            'LogID'       => $this->shortUuid(),
            'Sender'      => $sender,
            'Payload'     => $payload,
            'Remark'      => $remark,
            'DateCreated' => date('Y-m-d H:i:s'),
            'Username'    => $username,
            'CreatedBy'   => $this->getUserId($username),
            'Syncuid'     => $syncUid,
            'EventUID'    => $eventUid,
        ));
    }

    /* =====================================================================
     * GENERATOR VIEW send_to_mobile DARI mw_mapping
     * ===================================================================== */

    /**
     * Bangun (dan opsional create) sebuah VIEW dari mapping mw_mapping milik
     * sebuah program. Tiap kolom field_reff di-alias ke dataelement_uid agar
     * sesuai format yang dibaca mobile (send_to_mobile_mapping).
     *
     * Base table jadi anchor (default ktv_members), reference table lain
     * di-LEFT JOIN otomatis lewat kolom yang sama (uid / *ID).
     *
     * @param string $programUid uid program (mis. 'QxauNvjcpBw')
     * @param string $viewName   nama view (mis. 'view_program_farmer')
     * @param string $baseTable  tabel anchor (mis. 'ktv_members')
     * @param bool   $execute    true = langsung CREATE OR REPLACE VIEW
     *
     * @return array { success, sql, joined_tables, skipped_tables, message }
     */
    public function buildProgramView($programUid, $viewName, $baseTable = 'ktv_members', $execute = false)
    {
        $mapping = $this->getMapping($programUid); // [table => [de_uid => field_reff]]
        if (empty($mapping)) {
            return array('success' => false, 'message' => 'Tidak ada mapping untuk program '.$programUid);
        }

        $baseCols = $this->tableColumns($baseTable);
        if (empty($baseCols)) {
            return array('success' => false, 'message' => 'Base table tidak ditemukan: '.$baseTable);
        }

        // tentukan kolom id base (MemberID / SupplierID / kolom *ID pertama)
        $idCol = $this->guessIdColumn($baseCols);
        $hasUid = array_key_exists('uid', $baseCols);

        // urutkan: base table dulu, lalu sisanya
        $tables = array_keys($mapping);
        usort($tables, function ($a, $b) use ($baseTable) {
            if ($a === $baseTable) {
                return -1;
            }
            if ($b === $baseTable) {
                return 1;
            }

            return strcmp($a, $b);
        });

        $aliasMap = array();      // table => alias (t0, t1, ...)
        $selects = array();
        $joins = array();
        $joined = array();
        $skipped = array();
        $usedAlias = array();     // dataelement alias yang sudah dipakai
        $i = 0;

        foreach ($tables as $table) {
            $cols = ($table === $baseTable) ? $baseCols : $this->tableColumns($table);
            if (empty($cols)) {
                $skipped[$table] = 'tabel tidak ditemukan';
                continue;
            }

            $alias = 't'.$i;

            if ($table === $baseTable) {
                $aliasMap[$table] = $alias;
                $joined[] = $table;
                // kolom identitas
                if ($idCol) {
                    $selects[] = '`'.$alias.'`.`'.$idCol.'` AS `id`';
                }
                if ($hasUid) {
                    $selects[] = '`'.$alias.'`.`uid` AS `uid`';
                }
                $i++;
            } else {
                // cari kolom join yang sama dengan base
                $joinCol = $this->guessJoinColumn($baseCols, $cols);
                if (! $joinCol) {
                    $skipped[$table] = 'tidak ada kolom join yang cocok dengan '.$baseTable;
                    continue;
                }
                $aliasMap[$table] = $alias;
                $joined[] = $table;
                $joins[] = 'LEFT JOIN `'.$table.'` AS `'.$alias.'` ON `'.$alias.'`.`'.$joinCol.'` = `'.$aliasMap[$baseTable].'`.`'.$joinCol.'`';
                $i++;
            }

            // kolom field_reff -> alias dataelement_uid
            foreach ($mapping[$table] as $deUid => $field) {
                if (! array_key_exists($field, $cols)) {
                    continue; // field tidak ada di tabel, lewati
                }
                $aliasName = $deUid;
                $n = 2;
                while (isset($usedAlias[$aliasName])) {
                    $aliasName = $deUid.'_'.$n;
                    $n++;
                }
                $usedAlias[$aliasName] = true;
                $selects[] = '`'.$alias.'`.`'.$field.'` AS `'.$aliasName.'`';
            }
        }

        if (empty($aliasMap[$baseTable])) {
            return array('success' => false, 'message' => 'Base table '.$baseTable.' tidak ada di mapping program '.$programUid);
        }

        $sql = 'CREATE OR REPLACE VIEW `'.$viewName.'` AS'.PHP_EOL
            .'SELECT'.PHP_EOL.'    '.implode(','.PHP_EOL.'    ', $selects).PHP_EOL
            .'FROM `'.$baseTable.'` AS `'.$aliasMap[$baseTable].'`'.PHP_EOL
            .(empty($joins) ? '' : implode(PHP_EOL, $joins));

        $result = array(
            'success'        => true,
            'sql'            => $sql,
            'joined_tables'  => $joined,
            'skipped_tables' => $skipped,
        );

        if ($execute) {
            try {
                $this->db->query($sql);
                $result['message'] = 'View '.$viewName.' berhasil dibuat';
            } catch (Exception $e) {
                $result['success'] = false;
                $result['message'] = 'Gagal create view: '.$e->getMessage();
            }
        } else {
            $result['message'] = 'Dry-run (view belum dibuat). Set execute=1 untuk membuat.';
        }

        return $result;
    }

    /** tebak kolom id : *ID pertama (MemberID, SupplierID, dll) */
    private function guessIdColumn($cols)
    {
        foreach ($cols as $name => $type) {
            if (preg_match('/ID$/', $name)) {
                return $name;
            }
        }

        return null;
    }

    /** tebak kolom join antara base & ref : prioritas uid, lalu kolom *ID yang sama */
    private function guessJoinColumn($baseCols, $refCols)
    {
        if (array_key_exists('uid', $baseCols) && array_key_exists('uid', $refCols)) {
            return 'uid';
        }
        // kolom *ID yang ada di kedua tabel
        foreach ($baseCols as $name => $type) {
            if (preg_match('/ID$/', $name) && array_key_exists($name, $refCols)) {
                return $name;
            }
        }
        // fallback: kolom apa pun yang sama (selain kolom umum non-key)
        $ignore = array('DateCreated', 'DateUpdated', 'CreatedBy', 'LastModifiedBy', 'StatusCode', 'uid');
        foreach ($baseCols as $name => $type) {
            if (array_key_exists($name, $refCols) && ! in_array($name, $ignore, true)) {
                return $name;
            }
        }

        return null;
    }

    /* =====================================================================
     * HELPER
     * ===================================================================== */

    /** mapping mw_mapping : [table_reff => [dataelement_uid => field_reff]] */
    private function getMapping($programUid)
    {
        // Bila satu dataElement ter-map ke >1 field_reff pada tabel yang sama,
        // hanya satu yang bisa menang (struktur [table][de_uid] = field). Pilih
        // baris yang custom_function-nya KOSONG (penulisan kolom langsung) supaya
        // menang atas baris berfungsi (mis. getPartnerID/setMemberDisplayID) yang
        // menargetkan kolom identitas/turunan -- kolom itu sudah diisi server secara
        // native. ORDER memastikan baris custom_function-kosong ditulis terakhir
        // sehingga menimpa (last-wins). Tanpa ini, mis. Village (VillageID) kalah
        // oleh PartnerID/MemberDisplayID yang protected -> village tak pernah masuk.
        $sql = 'SELECT table_reff, dataelement_uid, field_reff
                FROM mw_mapping
                WHERE program_uid = ?
                  AND table_reff IS NOT NULL AND table_reff <> \'\'
                  AND field_reff IS NOT NULL AND field_reff <> \'\'
                ORDER BY (custom_function IS NULL OR custom_function = \'\') ASC, mw_mapping_id ASC';
        $rows = $this->db->query($sql, array($programUid))->result();

        $map = array();
        foreach ($rows as $r) {
            $map[$r->table_reff][$r->dataelement_uid] = $r->field_reff;
        }

        return $map;
    }

    /**
     * Mapping mw_mapping LENGKAP (tidak di-collapse): satu dataElement boleh
     * map ke banyak field/baris. Terurut priority supaya baris DML (priority 1)
     * diproses sebelum side-effect (priority >= 2).
     * Return: [table_reff => [ ['de'=>.., 'field'=>.., 'fn'=>..|null, 'priority'=>int], ... ]]
     */
    private function getMappingRows($programUid)
    {
        $sql = 'SELECT table_reff, dataelement_uid, field_reff, custom_function, priority
                FROM mw_mapping
                WHERE program_uid = ?
                  AND table_reff IS NOT NULL AND table_reff <> \'\'
                  AND field_reff IS NOT NULL AND field_reff <> \'\'
                ORDER BY priority ASC, mw_mapping_id ASC';
        $rows = $this->db->query($sql, array($programUid))->result();

        $map = array();
        foreach ($rows as $r) {
            $fn = ($r->custom_function !== null && $r->custom_function !== '') ? $r->custom_function : null;
            $map[$r->table_reff][] = array(
                'de'       => $r->dataelement_uid,
                'field'    => $r->field_reff,
                'fn'       => $fn,
                'priority' => (int) $r->priority,
            );
        }

        return $map;
    }

    private function getUserId($username)
    {
        if (! $username) {
            return null;
        }
        $row = $this->db->select('UserId')->where('UserName', $username)->get('sys_user')->row();

        return $row ? $row->UserId : null;
    }

    /**
     * Upsert berdasarkan kolom unik (uid). Aman terhadap perbedaan skema:
     *  - kolom yang tidak ada di tabel dibuang
     *  - nilai non-numeric tidak ditulis ke kolom numeric (cegah error 1366),
     *    mis. uid string masuk ke kolom integer MemberID
     *  - nilai kosong tidak ditulis ke kolom numeric / tanggal
     */
    private function upsertByUid($table, $row, $keyColumn, $keyValue, $allowInsert = true)
    {
        $columns = $this->tableColumns($table); // [name => data_type]
        if (empty($columns)) {
            return array('action' => 'skip', 'reason' => 'table_not_found');
        }

        if (! array_key_exists($keyColumn, $columns)) {
            return array('action' => 'skip', 'reason' => 'no_key_column');
        }

        $payload = array();   // kolom biasa (escaped otomatis)
        $geom = array();      // kolom geometry -> ST_GeomFromText(...)
        foreach ($row as $col => $val) {
            if (! array_key_exists($col, $columns) || $col === $keyColumn) {
                continue;
            }

            $type = $columns[$col];

            if ($this->isGeometryType($type)) {
                if (is_string($val) && $this->looksLikeWkt($val)) {
                    $geom[$col] = $val;
                }
                continue; // value bukan WKT valid -> lewati
            }

            if (! $this->isValueCompatible($type, $val)) {
                continue; // tipe tidak cocok, lewati kolom ini
            }

            $payload[$col] = $val;
        }

        // tidak ada field bermakna selain kolom kunci -> tidak perlu tulis apa pun
        if (empty($payload) && empty($geom)) {
            return array('action' => 'skip', 'reason' => 'no_field');
        }

        $exists = $this->db->where($keyColumn, $keyValue)
            ->count_all_results($table) > 0;

        if (! $exists && ! $allowInsert) {
            // baris belum ada & insert tidak diizinkan (tabel relasional via mapping):
            // dilewati karena pembuatan baris baru butuh resolusi PK/FK milik consumer asli
            return array('action' => 'skip', 'reason' => 'row_not_exists');
        }

        foreach ($payload as $col => $val) {
            $this->db->set($col, $val);
        }
        foreach ($geom as $col => $wkt) {
            $this->db->set($col, 'ST_GeomFromText('.$this->db->escape($wkt).')', false);
        }

        try {
            if ($exists) {
                $this->db->where($keyColumn, $keyValue)->update($table);

                return array('action' => 'update', 'fields' => count($payload) + count($geom));
            }
            $this->db->set($keyColumn, $keyValue);
            $this->db->insert($table);

            return array('action' => 'insert', 'fields' => count($payload) + count($geom));
        } catch (Exception $e) {
            return array('action' => 'error', 'error' => $e->getMessage());
        }
    }

    private function isGeometryType($type)
    {
        $geomTypes = array('geometry', 'point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon', 'geometrycollection');

        return in_array(strtolower($type), $geomTypes, true);
    }

    private function looksLikeWkt($value)
    {
        return (bool) preg_match('/^\s*(POINT|LINESTRING|POLYGON|MULTIPOINT|MULTILINESTRING|MULTIPOLYGON|GEOMETRYCOLLECTION)\s*\(/i', $value);
    }

    /**
     * Cek apakah nilai cocok dengan tipe kolom MySQL.
     */
    private function isValueCompatible($type, $value)
    {
        if ($value === null) {
            return true; // biarkan NULL
        }

        $type = strtolower($type);
        $numericTypes = array('int', 'tinyint', 'smallint', 'mediumint', 'bigint', 'decimal', 'float', 'double', 'numeric', 'real', 'bit');
        $dateTypes = array('date', 'datetime', 'timestamp', 'time', 'year');

        if (in_array($type, $numericTypes, true)) {
            return $value !== '' && is_numeric($value);
        }

        if (in_array($type, $dateTypes, true)) {
            return $value !== '' && strtotime($value) !== false;
        }

        return true; // varchar/text/json/dll : terima
    }

    private function tableColumns($table)
    {
        if (isset($this->columnCache[$table])) {
            return $this->columnCache[$table];
        }

        $sql = 'SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?';
        $rows = $this->db->query($sql, array($table))->result_array();

        $cols = array();
        foreach ($rows as $r) {
            $cols[$r['COLUMN_NAME']] = $r['DATA_TYPE'];
        }

        $this->columnCache[$table] = $cols;

        return $cols;
    }

    /** cache kolom wajib (NOT NULL, tanpa default, bukan auto_increment) per tabel */
    private $requiredCache = array();

    /**
     * Daftar kolom yang WAJIB diisi saat INSERT (NOT NULL, tanpa default,
     * bukan auto_increment). Dipakai guard generic upsert supaya insert yang
     * pasti gagal (NOT NULL kosong) di-skip terkontrol — bukan menggagalkan
     * transaksi seluruh event.
     */
    private function requiredColumns($table)
    {
        if (isset($this->requiredCache[$table])) {
            return $this->requiredCache[$table];
        }

        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                  AND IS_NULLABLE = 'NO'
                  AND COLUMN_DEFAULT IS NULL
                  AND EXTRA NOT LIKE '%auto_increment%'";
        $rows = $this->db->query($sql, array($table))->result_array();

        $req = array();
        foreach ($rows as $r) {
            $req[] = $r['COLUMN_NAME'];
        }

        $this->requiredCache[$table] = $req;

        return $req;
    }

    private function findDataValue($dataValues, $deUid)
    {
        if (! is_array($dataValues)) {
            return null;
        }
        foreach ($dataValues as $dv) {
            if (isset($dv['dataElement']) && $dv['dataElement'] === $deUid) {
                return isset($dv['value']) ? $dv['value'] : null;
            }
        }

        return null;
    }

    private function normalizeBool($value)
    {
        if ($value === 'true' || $value === 'TRUE' || $value === true) {
            return '1';
        }
        if ($value === 'false' || $value === 'FALSE' || $value === false) {
            return '2';
        }

        return $value;
    }

    private function toMysqlDate($value, $fallback)
    {
        $ts = strtotime($value);

        return $ts ? date('Y-m-d H:i:s', $ts) : $fallback;
    }

    private function shortUuid()
    {
        $row = $this->db->query('SELECT UUID_SHORT() AS id')->row();

        return $row ? $row->id : null;
    }

    private function escapeArr($arr)
    {
        $out = array();
        foreach ($arr as $v) {
            $out[] = $this->db->escape_str($v);
        }

        return $out;
    }
}
