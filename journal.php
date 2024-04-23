<?php

include('connector.php');


if(isset($_POST['fromcoa'])){

    $data = [
        'from_coa'=>$_POST['fromcoa'],
        'to_coa'=>$_POST['tocoa'],
        'amount'=>$_POST['amount'],
        'description'=>$_POST['desc']
    ];
    $f->insertData($data, 'journals');
    $load = $f->selectJoins("select * from journals");
    echo json_encode(array('status'=>200, 'msg'=>'Successfully added', 'data'=>$load));

}

if(isset($_GET['all'])){
    $f->load("select * from journals");
}