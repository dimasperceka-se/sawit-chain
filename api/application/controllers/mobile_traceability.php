<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile_Traceability extends REST_Controller {

    public $_output = array('success' => false, 'error' => 'Data is not valid'); //response data

    public function __construct() {
        parent::__construct();
        
        $this->load->model('traceability/mmobile','_model');
        $this->load->model('mauth','_auth');
    }

    public function login_post() {
        
        if($this->post('username')){
            
            $data = $this->post();
            
            $doLogin = $this->_model->doLogin($this->post('username'),  $this->post('password'));
            if($doLogin){
                return $this->response(array('success' => true, 'message' => 'Berhasil Login', 'results' => $doLogin),  200);
            }
        }
        
        return $this->response($this->_output,401);
        
    }
    
    public function logout_post() {
        
        if($this->input->get_request_header('Authorization')){
            
            $doLogout = $this->_model->doLogout($this->input->get_request_header('Authorization'));
            if($doLogout){
                return $this->response(array('success' => true, 'message' => 'Berhasil Logout'),  200);
            }
        }
        
        return $this->response($this->_output,401);
        
    }

    /**
     * Fungsi untuk mengambil data petani yang di assign ke supplychain berdasarkan data district
     * 
     * @author Ardi <ardiantoro@koltiva.com>
     * @param int $sid SupplychainID
     * @param string $district DistrictID (optional)
     * @return void
     */
    public function get_farmer_GET() {
        
        $supplychainid = $this->get('sid');
        $district = $this->get('district');
        $farmers = $this->_model->getFarmerBySupplychainID($supplychainid,$district);
        
        //get garden
        foreach($farmers as $key => $values){
            $farmers[$key]['garden'] = $this->_model->getGardenByFarmer($values['memberid']);
        }

        if(count($farmers) > 0) {
            $this->response(array('success' => true, 'data' => $farmers, 'total' => count($farmers)), 200); 
        }

        $this->response(array('success' => true, 'data' => array(), 'total' => 0), 200); 
    }

    /**
     * Fungsi ambil harga harian sesuai supplychain
     * @param int $sid SupplychainID
     * @param string $dpriceate Harga yang ingin dipanggil
     * @author Ardi <ardiantoro@koltiva.com>
     * @return void
     */
    public function get_price_GET() {

        ini_set('display_errors',1);
        error_reporting(E_ALL);
        $supplychainid = $this->get('sid');
        $pricedate = $this->get('pricedate');

        $price = $this->_model->getPriceBySupplychainID($supplychainid,$pricedate);
        
        if(count($price) > 0) {
            $this->response(array('success' => true, 'data' => $price), 200); 
        }

        $this->response(array('success' => true, 'data' => 0), 200); 
                
    }

    /**
     * Fungsi ambil sisa kuota penjualan biji sertifikasi berdasarkan farmerid
     * @param int $fid FarmerID
     * @author Ardi <ardiantoro@koltiva.com>
     * @return void
     */
    public function check_quota_GET() {
        
        $farmerid = $this->get('fid');
        
        $data = $this->_model->getQuotaByFarmerID($farmerid);
        
        if(count($data) > 0) {
            $this->response(array('success' => true, 'data' => $data), 200); 
        }

        $this->response(array('success' => true, 'data' => 0), 200); 
                
    }

    /**
     * Fungsi posting batch beserta transaksinya
     * @param json $batch payload batch beserta transaksi
     * @author Ardi <ardiantoro@koltiva.com>
     * @return void
     */
    public function submit_batch_POST() {
        
        
        $batch = is_array($this->post('data'))?$this->post('data'):json_decode($this->post('data'),true);
        
        $data = $this->_model->postBatch($batch);
        $dest = $batch['destsupplychainid'];
        
        if(count($data) > 0) {
            
            //create auto-batch on destination
            if($batch['status'] == 'Sent' || $batch['status'] == 'sent') {

                $this->load->model('traceability/mtransaction_new','_newtrans');
                if(!$this->_model->checkBatchNumber($data['SupplyBatchNumber'])) {
                    $this->_newtrans->createAutoBatch($data['SupplyBatchID'], $dest);
                }
                
            }
            $this->response(array('success' => true, 'data' => $data), 200); 
        }

        $this->response(array('success' => true, 'data' => 0), 200); 
                
    }

    /**
     * Fungsi transaksi tanpa batch
     * @param json $trans payload transaksi
     * @author Ardi <ardiantoro@koltiva.com>
     * @return void
     */
    public function submit_transaction_POST() {
        
        $transaction = $this->post('data');
        
        $data = $this->_model->postTransaction($transaction);
        
        if(count($data) > 0) {
            $this->response(array('success' => true, 'data' => $data), 200); 
        }

        $this->response(array('success' => true, 'data' => 0), 200); 
                
    }

    /**
     * Fungsi untuk ambil data pengiriman
     * @param supplychainid $sid
     * @author Ardi <ardiantoro@koltiva.com>
     */
    public function get_destination_GET($supplychainid = false) {
        
        $destinations = $this->_auth->getDestinationBySupplychainID($supplychainid);
        
        if(count($destinations) > 0) {
            $this->response(array('success' => true, 'data' => $destinations, 'total' => count($destinations)), 200); 
        }

        $this->response(array('success' => true, 'data' => array(), 'total' => 0), 200); 
    }

    /**
     * Fungsi tambah kebun per petani
     * @param FarmerID $fid
     * @author Ardi <ardiantoro@koltiva.com>
     */
    public function add_garden_POST($fid){

        $long = $this->post('long');
        $lat = $this->post('lat');
        $num = $this->post('number');
        
        $data = $this->_model->postGarden($fid,$num,$long,$lat);
        
        if($data > 0) {
            $this->response(array('success' => true, 'data' => $data), 200); 
        } else {
            $this->response(array('success' => true, 'data' => $data), 200); 
        }

        $this->response(array('success' => false, 'data' => 0), 200); 
    }

}
