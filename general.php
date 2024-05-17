<?php

include('connector.php');

if(isset($_GET['getgen'])){
    $uid = 1;
    $cid = getcid($uid);
    $sof = $f->selectData('source_of_fund', '', "where c_id = '$cid'")->fetchAll();
    $banks = $f->selectData('bank', '', "where c_id = '$cid'")->fetchAll();
    $cc = $f->selectData('collection_center', '', "where c_id = '$cid'")->fetchAll();
    $coa = $f->selectData('chart_of_account', '', "where c_id = '$cid'")->fetchAll();
    $budget = $f->selectData('budget', '', "order by subprogram asc")->fetchAll();
    $dept = $f->selectData('department', '', "")->fetchAll();
    $prog = $f->selectData('programmes', '', "where c_id = '$cid'")->fetchAll();
    $head = $f->selectData('projhead', '', "order by name asc")->fetchAll();
    $sub = $f->selectData('projsub', '', "order by name asc")->fetchAll();
    $suppliers = $f->selectData('suppliers', '', "order by name asc")->fetchAll();
    $cust = $f->selectJoins("select * from customers inner join bio_data on customers.u_id = bio_data.u_id order by customers.id desc")->fetchAll();
    $users = $f->selectJoins("select * from authentication a left join bio_data b on a.id = b.u_id left join user_roles ur on a.id = ur.uid left join roles r on ur.rid = r.id where a.c_id = '$cid' and a.user_type = 2")->fetchAll();
    echo json_encode(array('sof'=>$sof, 'banks'=>$banks, 'cc'=>$cc, 'coa'=>$coa, 'budget'=>$budget, 
    'dept'=>$dept, 'prog'=>$prog, 'head'=>$head, 'sub'=>$sub, 'suppliers'=>$suppliers, 'cust'=>$cust, 'users'=>$users));
}

if(isset($_GET['gen'])){
    $uid = 1;
    $cid = getcid($uid);
    $sof = $f->selectData('source_of_fund', '', "where c_id = '$cid'")->fetchAll();
    $banks = $f->selectData('bank', '', "where c_id = '$cid'")->fetchAll();
    $cc = $f->selectData('collection_center', '', "where c_id = '$cid'")->fetchAll();
    $coa = $f->selectData('chart_of_account', '', "where c_id = '$cid'")->fetchAll();

    $cust = $f->selectJoins("select * from customers inner join bio_data on customers.u_id = bio_data.u_id order by customers.id desc")->fetchAll();
    
    echo json_encode(array('sof'=>$sof, 'banks'=>$banks, 'cc'=>$cc, 'coa'=>$coa, 'cust'=>$cust));
}

if(isset($_GET['uniqueId'])){
    $code = $_GET['uniqueId'];
    $data = $f->selectJoins("SELECT c.*, p.name as programme FROM chart_of_account c left join programmes p on c.programme = p.id WHERE account = '$code'")->fetchAll();
    echo json_encode($data);
}

if(isset($_GET['custName'])){
    // $uid = $_GET['searchUid'];
    $uid = 1;
    $cid = getcid($uid);
    $name = $_GET['custName'];
    $data = $f->selectJoins("SELECT * FROM customers c left join authentication a on c.u_id = a.id left join bio_data b on c.u_id = b.u_id where a.c_id = '$cid' and (b.firstname like '%$name%' or b.lastname like '%$name%')")->fetchAll();
    echo json_encode($data);
}