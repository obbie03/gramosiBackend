<?php
include('connector.php');

ini_set('post_max_size', '100M');
ini_set('upload_max_filesize', '100M');
ini_set('max_input_vars', '5000');
ini_set('memory_limit', '128M');

if(isset($_POST['upType'])){
    $check = true;
    if(strpos(strtolower($_POST['upType']), 'coa') != false){
        foreach($_POST['upData'] as $data){
            $pid = addProgram($data['Programme']);
            $cols = array('c_id', 'name', 'programme', 'account', 'amount', 'type');
            if(!$f->checkTableValue($data['Code'], 'chart_of_account', 'account')){
                $vals = array(1, $data['Item'],$pid, $data['Code'], '0', $_POST['upType']);
                $com = array_combine($cols, $vals);
                $check = $f->insertData($com, 'chart_of_account');
            }
        }
    }

    if($_POST['upType'] == 'Customers'){
        foreach($_POST['upData'] as $data){
           $check = addCustomer($data['name'], $data['Account_number'], $data['code']);
        }
    }
    if($_POST['upType'] == 'valuation'){
        foreach($_POST['upData'] as $data){
           $type = addPropertyType($data['property_type']);
           $pid = addProperty($data['property_name'], $data['coa_no'],$type, $data['street'], $data['Town'],$data['location'], 
           $data['stand_plot_no'], $data['area_hectares'], $data['value_of_land'], $data['value_of_improvement'], $data['total_rateable_value'], $data['rate'], $data['rates_payable']);
           $account = $data['account_no'];
           if($pid != 0){
            $user = $f->selectData('customers', '', "where account_no = '$account' limit 1");
            if($user->rowCount() > 0){
             $uid = $user->fetchObject()->u_id;
             $checking = $f->selectData('user_property', '', "where u_id = '$uid' and p_id = '$pid' limit 1");
             if($checking->rowCount() == 0){
                 $upid = $f->insertReturnId(array('u_id'=>$uid, 'p_id'=>$pid), 'user_property');
                 $check = $f->insertData(array('u_id'=>$uid, 'amount'=>$data['balance'], 'year'=>date('Y')), 'all_balances');
             }
            }
           }
        }
    }

    if($_POST['upType'] == 'bank'){
        foreach($_POST['upData'] as $data){
           $check = addBank($data['name'], $data['code'], $data['account_no']);
        }
    }

    if($check){
        echo json_encode(array('status'=>200, 'msg'=>'Upload successful'));
    }else{
        echo json_encode(array('status'=>500, 'msg'=>'Something went wrong'));
    }
    
}