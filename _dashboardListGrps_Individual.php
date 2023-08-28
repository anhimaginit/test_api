
<?php
$origin=isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:$_SERVER['HTTP_HOST'];
header('Access-Control-Allow-Origin: '.$origin);
header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PUT');
header('Access-Control-Allow-Credentials: true');
//header('Access-Control-Allow-Headers: Authorization, X-Requested-With');
//header('P3P: CP="NON DSP LAW CUR ADM DEV TAI PSA PSD HIS OUR DEL IND UNI PUR COM NAV INT DEM CNT STA POL HEA PRE LOC IVD SAM IVA OTC"');
//header('Access-Control-Max-Age: 1');

include_once './lib/class.group.php';
$Object = new Group();

$EXPECTED = array('token','ID');

foreach ($EXPECTED AS $key){
    if (!empty($_POST[$key]))
        ${$key} = $Object->protect($_POST[$key]);
    else
        ${$key} = NULL;
}

$isAuth =$Object->basicAuth($token);

$list = array();
if(!$isAuth){
    $ret = array("groups"=>[],'AUTH'=>false,'ERROR'=>'Authenticate failed');
    $Object->close_conn();
    echo json_encode($ret);
    return;
}else{
    $role = $_POST['role'];
    //$groups = $Object->groupsByUserID($ID,$role);
    $groups = $Object->list_gr_individual($ID);

    $ret = array("groups"=>$groups,'AUTH'=>true, 'ERROR'=>'');

    $Object->close_conn();
    echo json_encode($ret);
}


