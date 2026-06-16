<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Verifikasitoken
{

	public function cekValisasiToken($token, $exp=false)
	{
		$CI = & get_instance();
		$CI->config->load('awscognito');
        $config = $CI->config->item('awscog');

        $aws = new Aws\Sdk($config);
        $cognitoClient = $aws->createCognitoIdentityProvider();

        $client = new \pmill\AwsCognito\CognitoClient($cognitoClient);
        $client->setAppClientId($config['app_client_id']);
        $client->setAppClientSecret($config['app_client_secret']);
        $client->setRegion($config['region']);
        $client->setUserPoolId($config['user_pool_id']);

        $jwtPayload = $client->decodeAccessToken($token);

        $expectedIss = sprintf('https://cognito-idp.%s.amazonaws.com/%s', $config['region'], $config['user_pool_id']);

        if ($jwtPayload['iss'] !== $expectedIss) {
            return array('success' => false, 'message' => 'invalid iss !');
            exit;
        }

        if ($jwtPayload['token_use'] !== 'id') {
            return array('success' => false, 'message' => 'invalid token_use !');
            exit;
        }


        if($exp){
            if ($jwtPayload['exp'] < time()) {
                return array('success' => false, 'message' => 'invalid exp !');
                exit;
            }
        }

        return array('success' => true, 'data' => $jwtPayload);
        exit;	
    }

    
}