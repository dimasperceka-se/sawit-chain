<?php
    if (!function_exists('move_upload')) {
        function move_upload($fileUpload,$pathAndImageName)
        {

            $fields = array_keys($fileUpload);
            $CI =& get_instance();
            $getPath = explode('/', $pathAndImageName);
            $imageName = $getPath[count($getPath)-1];
            $path = implode('/',array_slice($getPath,0,count($getPath)-1));

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png|xlsx|xls|pdf|zip|mp4';
            $config['file_name']    = $imageName;
            // $config['max_width']  = '1024';
            // $config['max_height']  = '768';

            $CI->load->library('upload', $config);
            if (!$CI->upload->do_upload($fields[0])){
                $error = array('error' => $CI->upload->display_errors());
                return $error;
            } else {
                $data = array('upload_data' => $CI->upload->data());
                return $data;
            }


        }
    }

?>