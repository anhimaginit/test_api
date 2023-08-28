
<?php
$origin=isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:$_SERVER['HTTP_HOST'];
header('Access-Control-Allow-Origin: '.$origin);
header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PUT');
header('Access-Control-Allow-Credentials: true');
//header('Access-Control-Allow-Headers: Authorization, X-Requested-With');
//header('P3P: CP="NON DSP LAW CUR ADM DEV TAI PSA PSD HIS OUR DEL IND UNI PUR COM NAV INT DEM CNT STA POL HEA PRE LOC IVD SAM IVA OTC"');
//header('Access-Control-Max-Age: 1');

include_once './lib/class.orders.php';
$Object = new Orders();

$EXPECTED = array('token','pageno','pagelength' ,'search_all','jwt','private_key','personal_filter');

foreach ($EXPECTED AS $key){
    if (!empty($_POST[$key])){
        ${$key} = $Object->protect($_POST[$key]);
    }else if (!empty($_GET[$key])) {
        ${$key} = $Object->protect($_GET[$key]);
    }
    else{
        ${$key} = NULL;
    }

}

 $isAuth =$Object->basicAuth($token);

$list = array();
if(!$isAuth){
    $ret = array("list"=>[],"total"=>0);
    $Object->close_conn();
    echo json_encode($ret);
} else {

    $columns =[ 'warranty','payment','total',
        'balance','s_name','b_name','createTime'];

    //$limit = empty($pagelength) ? 0 : $pagelength;
    $limit=1000;
    $offset = empty($pageno) ? 0 : ($pageno-1)*$pagelength;

    $isAuth = $Object->auth($jwt,$private_key);
    //$isAuth['AUTH']=true;
    if($isAuth['AUTH']){
        $role = $_POST['role'];

        $list = $Object->searchOderList($columns,$search_all,$limit,$offset,$role,$private_key,$personal_filter);
        //$total = $Object->OrderTotalRecords($columns,$search_all,$role,$private_key);

        $ret = array('AUTH'=>true,"list"=>$list,"total"=>0);
    }else{
        $ret = array("list"=>[],'AUTH'=>false,'ERROR'=>$isAuth['ERROR']);
    }

    $Object->close_conn();


    echo json_encode($ret);
}
