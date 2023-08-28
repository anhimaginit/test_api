<?php
$origin=isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:$_SERVER['HTTP_HOST'];
header('Access-Control-Allow-Origin: '.$origin);
header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PUT');
header('Access-Control-Allow-Credentials: true');

           
include_once './lib/class.contact.php';
    $Object = new Contact();

    $EXPECTED = array('token','old_password','id','jwt','private_key');

    foreach ($EXPECTED AS $key) {
        if (!empty($_POST[$key])){
            ${$key} = $Object->protect($_POST[$key]);
        } else {
            ${$key} = NULL;
        }
    }

    //--- validate
    $isAuth =$Object->basicAuth($token);
    if(!$isAuth){
        $ret = array('ERROR'=>'Authentication is failed');
    }else{
        $isAuth = $Object->auth($jwt,$private_key);
        $errObj['errorMsg']="Authentication is failed";
        $isAuth['AUTH']=true;
        if($isAuth['AUTH']){
            $id = $Object->checkOldPass($old_password,$id);

            if(is_numeric($id) && !empty($id)){
                $ret = array('check'=>"Success",'ERROR'=>'');
            }else{
                $ret = array('check'=>"Failed",'ERROR'=>'');
            }

        } else {
            $ret = array('id'=>"",'ERROR'=>$errObj['errorMsg']);

        }

    }

    $Object->close_conn();
    echo json_encode($ret);




