<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


use Aws\S3\S3Client;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

class Awsfileupload
{

    private $awsfileupload;
    private $bucket;
    private $version;
    private $region;

    function __construct($config = array())
    {
        if (!empty($config)) {
            extract($config);

            $sdk = new Aws\Sdk($s3);
            $this->awsfileupload = $sdk->createS3();
            $this->bucket = $s3Bucket;

            $this->version = $s3['version'];
            $this->region = $s3['region'];
        }

        $this->CI = &get_instance();

        log_message('debug', 'S3 Class Initialized');
    }

    public function upload($upFilePath, $upFilename, $module, $type = 'images')
    {
        if (file_exists($upFilePath)) {
            $filename = $this->setHashName($upFilename);
            $tmp_path = $upFilePath;

            //Get Mime type filenya
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $upFilePath);

            //$type hanya boleh (images or documents)
            if($type != 'images') {
                $type = 'documents';
            }

            $upload_config = [
                'bucket'        => $this->bucket,
                'key'           => $this->CI->config->item('CTEnv') . '/' . $type . '/' . $module . '/' . $filename,
                'acl'           => 'public-read',
                'before_initiate' => function (\Aws\Command $command) use ($mime_type) {
                    $command['ContentType'] = $mime_type;
                }
            ];
            $uploader = new MultipartUploader($this->awsfileupload, $tmp_path, $upload_config);

            //Upload
            try {
                $uploader->upload();

                $result['success'] = TRUE;
                $result['filenamepath'] = $type . '/' . $module . '/' . $filename;
                $result['fileurl'] = $this->showFileURL($type,$module, $filename);
            } catch (MultipartUploadException $e) {
                //Show error
                $result['success'] = FALSE;
                $result['message'] = $e->getMessage();
            }

            return $result;
        } else {
            return false;
        }
    }

    public function delete($filepath)
    {
        $path = $this->CI->config->item('CTEnv') . '/' . $filepath;
        return $this->awsfileupload->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $path
        ]);
    }

    public function doesObjectExist($filepath) {
        if (empty($filepath) || $this->awsfileupload === null) {
            return false;
        }
        try {
            $path = $this->CI->config->item('CTEnv') . '/' . $filepath;
            return $this->awsfileupload->doesObjectExist($this->bucket, $path);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getS3Path($path)
    {
        return 's3://' . $this->bucket . '/' . ltrim($path, '/');
    }

    private function setHashName($filename)
    {
        $arrTemp = explode(".", $filename);
        $ext = strtolower(array_values(array_slice($arrTemp, -1))[0]);

        $hasname = hash_pbkdf2("sha512",$arrTemp[0],'NIKOSB_VC',10,32);
        $name = $hasname . strtotime(date('Y-m-d H:i:s'));
        return $name . '.' . $ext;
    }

    public function showFileURL($type, $module, $filename)
    {
        return $this->CI->config->item('CTCDN') . '/' . $type . '/'. $module . '/' . $filename;
    }

    public function getTypeOfFile($filename)
    {
        $arrTemp = explode(".", $filename);
        $extType = strtolower(array_values(array_slice($arrTemp, -1))[0]);
        if ($extType == 'png' || $extType == 'jpeg' || $extType == 'gif' || $extType == 'jpg' || $extType == 'bmp') {
            $type = 'images';
        } elseif (
            $extType == 'webm' || $extType == 'mkv' || $extType == 'flv' || $extType == 'ogv' || $extType == 'avi'
            || $extType == 'mov' || $extType == 'wmv' || $extType == 'mp4' || $extType == 'mpg' || $extType == 'mpeg'
            || $extType == '3gp'
        ) {
            $type = 'videos';
        } else {
            $type = 'documents';
        }
        
        return $type;
    }

    public function doesObjectExistWithEnv($env, $filepath) {
        if (empty($filepath) || $this->awsfileupload === null) {
            return false;
        }
        try {
            $path = $env . '/' . $filepath;
            return $this->awsfileupload->doesObjectExist($this->bucket, $path);
        } catch (\Exception $e) {
            return false;
        }
    }
}
