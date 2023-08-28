<?php
$origin=isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:$_SERVER['HTTP_HOST'];
header('Access-Control-Allow-Origin: '.$origin);
header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PUT');
header('Access-Control-Allow-Credentials: true');

    include_once './lib/class.orders.php';
    $Object = new Orders();

    $EXPECTED = array('token','order_id');

    foreach ($EXPECTED AS $key) {
        if (!empty($_POST[$key])){
            ${$key} = $Object->protect($_POST[$key]);
        } else {
            ${$key} = NULL;
        }
    }

    $isAuth =$Object->basicAuth($token);
    if(!$isAuth){
        $ret = array('ERROR'=>'Authentication is failed');
    }else{
        $ret1 = $Object->deleteOder($order_id);

        if($ret1){
            $ret =array("DELETE"=>"SUCCESS");
        }else{
            $ret =array("DELETE"=>"Can't delete the order");
        }
    }

    $Object->close_conn();
    echo json_encode($ret);



