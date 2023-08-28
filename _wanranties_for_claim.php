<?php
$origin=isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:$_SERVER['HTTP_HOST'];
header('Access-Control-Allow-Origin: '.$origin);
header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PUT');
header('Access-Control-Allow-Credentials: true');

include_once './lib/class.warranty.php';
$Object = new Warranty();

$EXPECTED = array('token','search','jwt','private_key');


foreach ($EXPECTED AS $key){
    if (!empty($_POST[$key]))
        ${$key} = $Object->protect($_POST[$key]);
    else
        ${$key} = NULL;
}

$list = array();
$isAuth =$Object->basicAuth($token);

if(!$isAuth){
    $ret = array("list"=>[],"total"=>0,'ERROR'=>'Authentication is failed');
    $Object->close_conn();
    echo json_encode($ret);
}else{
    $isAuth = $Object->auth($jwt,$private_key);
    $isAuth['AUTH']=true;
    if($isAuth['AUTH']){
        //$acl = $isAuth['acl_list'];
        $list = $Object->warranties_filter($search);
        $Object->close_conn();

        $ret = array("list"=>$list,'AUTH'=>true);

    }else{
        $ret = array("list"=>[],'AUTH'=>false,'ERROR'=>$isAuth['ERROR']);
    }


    echo json_encode($ret);
}


