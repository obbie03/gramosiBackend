<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
ini_set('max_execution_time', 9999);

require 'Core/Functions.php';
use Core\Functions;
$f = new Functions('localhost',3306, 'gramosic', 'root', '');

function getcid($uid){
    global $f;
    $cid = $f->selectData('authentication','', "where id='$uid' limit 1")->fetchObject()->c_id;
    return $cid;
}

function addProgram($name){
    global $f;
    $name = strtolower($name);
    $prog = $f->selectData('programmes', '', "where lower(name) = '$name' limit 1");
    if($prog->rowCount() > 0){
        return $prog->fetchObject()->id;
    }
    $id = $f->insertReturnId(array('c_id'=>1, 'name'=>$name), 'programmes');
    return $id;
}

function addCustomer($name, $account, $code){
    global $f;
    $names = explode(" ", $name);
    if(!$f->checkTableValue($account, 'customers', 'account_no')){
        $id = $f->insertReturnId(array('c_id'=>1, 'email'=>$account.'@email.com', 'user_type'=>1, 'password'=>'$2y$10$z4MMdmy.BOzmsJINTEheKe3RxdNEbh7PnivEmPhpOReNhcow8dsZK', 'mode'=>0), 'authentication');
        $f->insertData(array('u_id'=>$id, 'firstname'=>$names[0], 'middlename'=>count($names)>2?$names[1]:'', 'lastname'=>$names[count($names)-1], 'nrc'=>'nrc', 'phonenumber'=>'pn' , 'sex'=>'sex', 'dob'=>''), 'bio_data');
        $f->insertData(array('u_id'=>$id, 'account_no'=>$account, 'code'=>$code), 'customers');
    }
    return true;
}


function addPropertyType($name){
    global $f;
    $prop = $f->selectData('property_type', '', "where lower(name) = '$name' limit 1");
    if($prop->rowCount() > 0){
        return $prop->fetchObject()->id;
    }
    $id = $f->insertReturnId(array('name'=>$name), 'property_type');
    return $id;
}

function addProperty($name, $coa,$type, $street, $town,$location, $plot, $hectares, $value, $improvement, $rate_value, $rate, $payable){
    global $f;  

    $getCoa = $f->selectData('chart_of_account', '', "where account = '$coa' limit 1");
    if($getCoa->rowCount() > 0){
        $data = array(
            'c_id'=>1,
            'type_id'=>$type,
            'coa_id'=>$getCoa->fetchObject()->id,
            'street'=>$street,
            'town'=>$town,
            'name'=>$name,
            'location'=>$location,
            'plot'=>$plot,
            'hectares'=>$hectares,
            'value'=>$value,
            'improvement'=>$improvement,
            'rate_value'=>$rate_value,
            'rate'=>$rate,
            'payable'=>$payable
        );
        $prop = $f->selectData('property', '', "where name = '$name' and town = '$town' and plot = '$plot' and location = '$location' limit 1");
        if($prop->rowCount() > 0){
            return $prop->fetchObject()->id;
        }
        $id = $f->insertReturnId($data, 'property');
        return $id;
    }else{
        return 0;
    }
  
}
