<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Translation extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('system/mtranslation');
    }

    function header_translation_get() {
        $header = array(
            0 => array('code' => '', 'text' => 'ID', 'dataIndex' => 'id', 'hidden' => true, 'width' => '1%'),
            1 => array('code' => '', 'text' => 'Number', 'dataIndex' => 'number', 'hidden' => '', 'xtype' => 'rownumberer', 'width' => '7%'),
            2 => array('code' => '', 'text' => 'Key', 'dataIndex' => 'key', 'hidden' => '', 'width' => '20%')
        );
        $data['header'] = $this->mtranslation->readListLang();
        $data['lang'] = $data['header'];
        $data['header'] = array_merge($header, $data['header']);

        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any header Translations!'), 404);
    }

    function core_translations_get() {
        $key = $this->get('key');
        $data = $this->mtranslation->readTranslations($key, $this->get('start'), $this->get('limit'));

        if (!empty($data['data'])) {
            foreach ($data['data'] as $key_val => $value) {
                $lang = $this->mtranslation->readListLang();
                foreach ($lang as $value_lang) {
                    $data['data'][$key_val][$value_lang['code']] = $this->mtranslation->readTranslationByKey($value['key'], $value_lang['code']);
                    #!empty($text) ? $data['data'][$key_val][$value_lang['code']] = $text : $data['data'][$key_val][$value_lang['code']] = '[' . $value['key'] . ']';
                }
            }
        }
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Translations!'), 404);
    }

    function core_translation_list_lang() {
        $data = $this->mtranslation->readListLang();
        if ($data)
            $this->response($data, 200);
        else
            $this->response(array('error' => 'Couldn\'t find any Languages!'), 404);
    }

    function validate_translation_get() {
        if ($this->get('key')) {
            $result = $this->mtranslation->cekTranslation($this->get('key'), $this->get('id'));
            if ($result)
                $this->response(array('key_data' => 'false'), 200);
            else
                $this->response(array('key_data' => 'true'), 200);
        }
    }

    function core_translation_get() {
        if (!$this->get('id'))
            $this->response(NULL, 400);
        $result = $this->mtranslation->readTranslation($this->get('id'));
        if ($result)
            $this->response(array('success' => true, 'data' => $result), 200);
        else
            $this->response(array('error' => 'Translations could not be found'), 404);
    }

    function core_translation_post() {
        $lang = $this->mtranslation->readListLang();

        foreach ($lang as $value) {
            $params['key'] = $this->post('key');
            $params['language'] = $value['dataIndex'];
            $params['set'] = 'general';
            $params['text'] = $this->post($value['dataIndex']);

            $result = $this->mtranslation->createTranslation($params);
        }

        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array('error' => 'Translations could not be added'), 404);
        }
    }

    function core_translation_put() {
        if (!$this->put('key') || $this->put('key_old') == '') {
            $this->response(NULL, 400);
        }
        $lang = $this->mtranslation->readListLang();
        foreach ($lang as $value) {
            $trans_id = $this->put('trans_id_' . $value['dataIndex']);
            if (!empty($trans_id)) {
                $params['key'] = $this->put('key');
                $params['language'] = $value['dataIndex'];
                $params['set'] = 'general';
                $params['text'] = $this->put($value['dataIndex']);
                $params['id'] = $trans_id;
                $result = $this->mtranslation->updateTranslation($params);
            } else {
                $param['key'] = $this->put('key');
                $param['language'] = $value['dataIndex'];
                $param['set'] = 'general';
                $param['text'] = $this->put($value['dataIndex']);

                $result = $this->mtranslation->createTranslation($param);
            }
        }
        if ($result)
            $this->response($result, 200); // 200 being the HTTP response code
        else
            $this->response(array('error' => 'Translation could not be edited'), 404);
    }

    function core_translation_delete() {
        if (!$this->delete('id'))
            $this->response(NULL, 400);
        $result = $this->mtranslation->deleteTranslation($this->delete('id'));
        if ($result)
            $this->response($result, 200);
        else
            $this->response(array('error' => 'Translation could not be deleted'), 404);
    }

}
