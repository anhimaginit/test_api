<?php
$origin=isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:$_SERVER['HTTP_HOST'];
header('Access-Control-Allow-Origin: '.$origin);
header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PUT');
header('Access-Control-Allow-Credentials: true');

    require __DIR__.'/lib//vendor/autoload.php';
    include_once './lib/class.receiveSMS.php';
    $Object = new ReceiveSMS();

    $EXPECTED = array('jwt','private_key');
    
    foreach ($EXPECTED AS $key) {
        if (!empty($_POST[$key])){
            ${$key} = $Object->protect($_POST[$key]);
        }else if (!empty($_GET[$key])) {
            ${$key} = $Object->protect($_GET[$key]);
        } else {
            ${$key} = NULL;
        }
    }

$http_code=200;
//$isAuth =$Object->basicAuth($token);
$isAuth=true;
if(!$isAuth){
    $ret = array('ERROR'=>'Authentication is failed');
    $Object->close_conn();
    echo json_encode($ret);
}else{
    // Configure HTTP basic authorization: BasicAuth
    $config = ClickSend\Configuration::getDefaultConfiguration()
        ->setUsername('bmhillman')
        ->setPassword('5162FF52-2790-E3B0-1756-AD937E76BFC5');
    $apiInstance = new ClickSend\Api\EmailMarketingApi(new GuzzleHttp\Client(),$config);
    $page = 1; // int | Page number
    $limit = 10; // int | Number of records per page

    try {
        $result = $apiInstance->allowedEmailAddressGet($page, $limit);
    //    $result = $apiInstance->smsSendPost($sms_messages);
        $result = json_decode($result,true);
        $http_code = $result['http_code'];
        if($http_code==200){
            $msg_info = $result['data']['data'];

        //    $rsl= $Object->save_smsOutbound($msg_info,$type,$senderID, $receiverID,$original_body,$original_msg_id);
            $ret = array('ERROR'=>'','list'=>$msg_info);
        }

    } catch (Exception $e) {
        $ret = array('ERROR'=>$e->getMessage(),'list'=>array());
        //echo 'Exception when calling SMSApi->smsSendPost: ', $e->getMessage(), PHP_EOL;
    }
    //$ret = array('ERROR'=>'','info'=>$rsl);
     //die();
    $Object->close_conn();
    echo json_encode($ret);
    http_response_code($code);
}

