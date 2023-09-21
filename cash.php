<?php
include('connector.php');

if(isset($_POST['exType'])){
   $id = $f->insertReturnId(array_combine(array('coa_id', 'amount', 'description', 'paye', 'type', 'cid', 'bank', 'sof', 'cheque', 'date'), 
   array($_POST['exType'], $_POST['Examount'], $_POST['Exdesc'], $_POST['Expaye'], $_POST['cashType'], 1, $_POST['cashBook'], $_POST['cashSof'], $_POST['chequeno'], $_POST['exDate'])), 'cashbook');
    // $f->insertData()
//    if($check){
    echo json_encode(array('status'=>200, 'msg'=>'Successfully added'));
//    }else{
//     echo json_encode(array('status'=>500, 'msg'=>'Failed to add'));
//    }
}

if(isset($_GET['cash'])){
    $expenses = $f->selectJoins("select * from cashbook where cid = 1")->fetchAll();
    $payments = $f->selectJoins("select * from receipts r left join bills b on r.b_id = b.id left join bio_data c on 
    b.customer = c.u_id where b.cid = 1")->fetchAll();
    echo json_encode(array('expenses'=>$expenses, 'payments'=>$payments));
}

if(isset($_POST['budId'])){
   
    $check = $f->insertData(array_combine(array('b_id', 'name', 'description', 'amount', 'addedBy', 'head'), 
    array($_POST['budId'], $_POST['projname'], $_POST['projdesc'], $_POST['projamount'], 1, $_POST['projsub'])), 'projects');
    if($check){
     echo json_encode(array('status'=>200, 'msg'=>'Successfully added'));
    }else{
     echo json_encode(array('status'=>500, 'msg'=>'Failed to add'));
    }

}

if(isset($_GET['proj'])){
    $f->load('select * from projects where b_id = "'.$_GET['proj'].'"');
}