<?php

/**
 * @Author: nikolius
 * @Date:   2017-10-16 17:15:54
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-18 16:57:27
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
ini_set('display_errors',true);
error_reporting(E_ALL);
*/
class Register extends SS_Controller
{
    public function __construct()
    {
        parent::__construct(0);
        $this->load->model('system');
    }

    public function form(){
        $data = array();
        $dataHeader = array();
        $dataFooter = array();

        $data['paramSegment'] = $this->uri->segment(4);
        $paramRegis = $this->uri->segment(4);
        $paramRegis = pack("H*", $paramRegis);
        $arrTmp = explode('@',$paramRegis);
        $RegID = $arrTmp[0];
        $Username = $arrTmp[1];

        //get data information staff
        $dataStaff = $this->system->getDataRegisterStaff($RegID,$Username);

        //susun display Access Area
        $dataStaff['AccessAreaHtml'] = $this->prepAccessAreaHtml($dataStaff['AccessArea']);

        //data combobo form
        $data['comboPropinsi'] = $this->system->getComboPropinsi();

        $data['templateHeader'] = $this->load->view('frontpage/common/header_page', $dataHeader, true);
        $data['templateFooter'] = $this->load->view('frontpage/common/footer_page', $dataFooter, true);

        $data['dataStaff'] = $dataStaff;
        if($dataStaff['Fullname'] != ""){
            $this->load->view('frontpage/register_form', $data);
        }else{
            $this->load->view('frontpage/register_form_not_found', $data);
        }
    }

    private function prepAccessAreaHtml($AccessArea){
        $arrTmp = explode("@",$AccessArea);
        $countData = count($arrTmp);
        $pembagiKolom = ceil($countData / 2);

        if($countData == 1){
            $returnHtml = '<table width="100%">
                                <tr>
                                    <td width="50%" valign="top">
                                        <input checked disabled type="checkbox"> '.$arrTmp[0].'
                                    </td>
                                    <td width="50%" valign="top">&nbsp;</td>
                                </tr>
                            </table>';
        }else{
            //kolom kiri
            $arrKolomKiri = array();
            $increDataKiri = 0;
            for ($i=0; $i < $pembagiKolom; $i++) {
                $arrKolomKiri[] = '<input checked disabled type="checkbox"> '.$arrTmp[$i];
                $increDataKiri++;
            }
            $kolomKiriHtml = implode('<br />',$arrKolomKiri);

            //kolom kanan
            $arrKolomKanan = array();
            for ($i=$increDataKiri; $i < $countData; $i++) {
                $arrKolomKanan[] = '<input checked disabled type="checkbox"> '.$arrTmp[$i];
            }
            $kolomKananHtml = implode('<br />',$arrKolomKanan);

            $returnHtml = '<table width="100%"><tr><td width="50%" valign="top">'.$kolomKiriHtml.'</td><td width="50%" valign="top">'.$kolomKananHtml.'</td></tr></table>';
        }

        return $returnHtml;
    }

    public function ajax_combo_district(){
        $ProvinceID = $this->input->post('ProvinceID');
        $comboDistrict = $this->system->getComboDistrict($ProvinceID);

        $returnHtml = '';
        for ($i=0; $i < count($comboDistrict); $i++) {
            $returnHtml .= '<option value="'.$comboDistrict[$i]['id'].'">'.$comboDistrict[$i]['label'].'</option>';
        }
        echo '<option value="">Select District</option>'.$returnHtml;
    }

    public function submit_register_tos(){
        //assign variabel form
        unset($_SESSION['form_regis']);
        $_SESSION['form_regis'] = $this->input->post();
        echo '1';
    }

    public function register_tor(){
        $data['tor_register_staff'] = '1';
        $this->load->view('tor_view', $data);
    }

    public function submit_register(){
        //tangkap param
        $paramPost = $_SESSION['form_regis'];
        unset($_SESSION['form_regis']);

        $prosesRegister = $this->system->prosesRegisterStaff($paramPost);

        //kasih login ============================================ (begin)
        if($prosesRegister['success'] == true){
            //login
            $_SESSION['username']           = $prosesRegister['userLogin']['UserName'];
            $_SESSION['realname']           = $prosesRegister['userLogin']['UserRealName'];
            $_SESSION['userid']             = $prosesRegister['userLogin']['UserId'];
            $_SESSION['groupid']            = $prosesRegister['userLogin']['GroupId'];
            $_SESSION['ProjID']             = $prosesRegister['userLogin']['ProjID'];
            $_SESSION['unitid']             = $prosesRegister['userLogin']['UnitId'];
            $_SESSION['daerah']             = $prosesRegister['userLogin']['daerah'];
            $_SESSION['province']           = $prosesRegister['userLogin']['province'];
            $_SESSION['PartnerID']          = $prosesRegister['userLogin']['PartnerID'];
            $_SESSION['daerah_access']      = $prosesRegister['userLogin']['daerah_access'];
            $_SESSION['language']           = $prosesRegister['userLogin']['UserLanguage'];
            $_SESSION['official_email']     = $prosesRegister['userLogin']['official_email'];
            $_SESSION['private_email']      = $prosesRegister['userLogin']['private_email'];
            $_SESSION['email']              = $prosesRegister['userLogin']['email'];
            $_SESSION['official_phone']     = $prosesRegister['userLogin']['official_phone'];
            $_SESSION['private_phone']      = $prosesRegister['userLogin']['private_phone'];
            $_SESSION['phone']              = $prosesRegister['userLogin']['phone'];
            $_SESSION['group']              = $prosesRegister['userLogin']['group_name'];
            $_SESSION['partner']            = $prosesRegister['userLogin']['partner_name'];
            $_SESSION['district']           = $prosesRegister['userLogin']['district'];
            $_SESSION['Photo_staff']        = $prosesRegister['userLogin']['Photo_staff'];
            $_SESSION['role']               = $prosesRegister['userLogin']['role'];
            $_SESSION['filter_by']          = $prosesRegister['userLogin']['GroupFilterBy'];
            $_SESSION['is_admin']           = $prosesRegister['userLogin']['UserIsAdmin'];
            $_SESSION['FlagAccess']         = $prosesRegister['userLogin']['FlagAccess'];
        }
        //kasih login ============================================ (end)

        $data['templateHeader'] = $this->load->view('frontpage/common/header_page', $dataHeader, true);
        $data['templateFooter'] = $this->load->view('frontpage/common/footer_page', $dataFooter, true);

        $data['proses'] = $prosesRegister['success'];
        $this->load->view('frontpage/register_form_proc', $data);
    }
}

?>