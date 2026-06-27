<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sync_event
 *
 * API sync data dari mobile (versi CodeIgniter) — port dari
 * Koltitrace_Global\SyncController (Laravel) TANPA cache / redis / async.
 * Event diproses & disimpan langsung (synchronous) ke database.
 *
 * Endpoint:
 *   POST sync-event/sync          -> upload event dari mobile
 *   GET  sync-event/send-to-mobile-> download master data + status sync
 */
class Sync_event extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('data_adm/msync_event');
    }

    /**
     * Login mobile.
     *
     * Body (JSON atau form): { "username": "...", "password": "..." }
     * Response sukses 200: { success:true, message, results:{ UserID, UserName, RealName, Language, Token } }
     * Response gagal      : { success:false, error } (400 input kurang, 401 kredensial salah, 403 non-aktif)
     */
    public function login_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');

        // dukung body JSON mentah
        if ($username === null && $password === null) {
            $body = json_decode(file_get_contents('php://input'), true);
            if (is_array($body)) {
                $username = isset($body['username']) ? $body['username'] : null;
                $password = isset($body['password']) ? $body['password'] : null;
            }
        }

        $result = $this->msync_event->login($username, $password);

        $code = isset($result['code']) ? $result['code'] : ($result['success'] ? 200 : 401);
        unset($result['code']);

        $this->response($result, $code);
    }

    /**
     * Upload data dari mobile (pengganti syncEvents).
     *
     * Body  : JSON { "events": [ { "event", "program", "syncUid", "dataValues":[...] }, ... ] }
     * Header: sender, appName, UserName (opsional)
     */
    public function sync_post()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $body = file_get_contents('php://input');
        $payload = json_decode($body, true);

        if (! is_array($payload) || empty($payload['events'])) {
            $this->response(array(
                'success' => false,
                'status'  => false,
                'result'  => 'Invalid payload: events is required',
            ), 400);

            return;
        }

        $sender = $this->input->get_request_header('sender');
        $appName = $this->input->get_request_header('appName');
        $username = $this->input->get_request_header('UserName');
        if (! $username) {
            $username = isset($payload['username']) ? $payload['username'] : '';
        }
        if (isset($payload['dataValues'][0]['storedBy'])) {
            $username = $payload['dataValues'][0]['storedBy'];
        }

        $result = $this->msync_event->syncEvents($payload, (string) $sender, (string) $appName, (string) $username);

        $this->response($result, 200);
    }

    /**
     * Download master data ke mobile + status sync per event.
     *
     * Query: ProgramUid (wajib, pisah ;), UserName, ExtUid (event uid, pisah ;),
     *        FarmerUID (entity uid), syncUid (pisah ;)
     */
    public function send_to_mobile_get()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $ProgramUid = $this->get('ProgramUid');
        $UserName = $this->get('UserName');
        $ExtUid = $this->get('ExtUid');
        $EntityUID = $this->get('FarmerUID');
        $syncUID = $this->get('syncUid');

        if (! $ProgramUid) {
            $this->response(array('success' => 0, 'error' => 'ProgramUid needed'), 400);

            return;
        }

        $result = $this->msync_event->sendToMobile(array(
            $ProgramUid,
            $UserName,
            $ExtUid,
            $EntityUID,
            $syncUID,
        ));

        $this->response($result, 200);
    }

    /**
     * Generate VIEW send_to_mobile dari mw_mapping sebuah program.
     *
     * Query:
     *   program   uid program        (default QxauNvjcpBw)
     *   view      nama view          (default view_program_farmer)
     *   base      base/anchor table  (default ktv_members)
     *   execute   1 = langsung CREATE OR REPLACE VIEW, selain itu dry-run
     *
     * Contoh:
     *   /sync-event/build-view?program=QxauNvjcpBw&view=view_program_farmer&base=ktv_members&execute=1
     */
    public function build_view_get()
    {
        $program = $this->get('program') ? $this->get('program') : 'QxauNvjcpBw';
        $view = $this->get('view') ? $this->get('view') : 'view_program_farmer';
        $base = $this->get('base') ? $this->get('base') : 'ktv_members';
        $execute = ($this->get('execute') == '1');

        $result = $this->msync_event->buildProgramView($program, $view, $base, $execute);

        $this->response($result, $result['success'] ? 200 : 400);
    }
}
