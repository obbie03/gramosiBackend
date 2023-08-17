<?php
include('connector.php');

if(isset($_POST['exType'])){
   $check = $f->insertData(array_combine(array('chq', 'amount', 'description', 'paye', 'mode'), 
   array($_POST['exType'], $_POST['Examount'], $_POST['Exdesc'], $_POST['Expaye'], $_POST['cashType'])), 'cashbook');
   if($check){
    echo json_encode(array('status'=>200, 'msg'=>'Successfully added'));
   }else{
    echo json_encode(array('status'=>500, 'msg'=>'Failed to add'));
   }
}

if(isset($_GET['cash'])){
    $expenses = $f->selectJoins("select * from cashbook where cid = 1")->fetchAll();
    $payments = $f->selectJoins("select * from receipts r left join bills b on r.b_id = b.id left join bio_data c on 
    b.customer = c.u_id where b.cid = 1")->fetchAll();
    echo json_encode(array('expenses'=>$expenses, 'payments'=>$payments));
}