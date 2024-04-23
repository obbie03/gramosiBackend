<?php
include('connector.php');

if(isset($_POST['exType'])){
    $cashbook = $_POST['cashBook'];
    $count = 0;
    foreach($_POST['Exbudget'] as $budget){
        if($_POST['cashType'] == 'expense'){
            $s = $f->selectJoins("select * from supplier_trans where type = 2")->rowCount();
            $s++;
            $inv =  'INVs/'.$s."/".substr(date('Y'),2,4);     
             $data2 = [
                 'sid'=>$_POST['Expaye'],
                 'invoice'=>$inv,
                 'amount'=>$_POST['Examount'][$count],
                 'type'=>1,
                 'comment'=>'invoice for '.$_POST['chequeno']
             ];
            $f->insertData($data2, 'supplier_trans');
            $data3 = [
                'sid'=>$_POST['Expaye'],
                'invoice'=>$_POST['chequeno'],
                'amount'=>$_POST['Examount'][$count],
                'type'=>2,
                'comment'=>$_POST['Exdesc']
            ];
                $f->insertData($data3, 'supplier_trans');
           }else{
            $cashbook = 13;
            $year = date('Y');
            $c = $f->selectJoins("select * from receipts where YEAR(date) = '$year' and type = 1")->rowCount();
            $c++;
            $recNum = 'R'.$c.'/'.substr(date('Y'),2,4);
            $combinedArray = array_combine(array('receipt_no', 'b_id', 'issuer', 'amount', 'description'), 
                                            array($recNum, $_POST['Expaye'], $_POST['updatedBy'], $_POST['Examount'][$count], $_POST['Exdesc']));
            $check = $f->insertData($combinedArray,'receipts');
           }
       $id = $f->insertReturnId(array_combine(array('coa_id', 'amount', 'description', 'paye', 'type', 'cid', 'bank', 'sof', 'cheque', 'date', 'proj_id', 'updatedBy'), 
       array($budget, $_POST['Examount'][$count], $_POST['Exdesc'], $_POST['Expaye'], $_POST['cashType'], 1, $cashbook, $_POST['cashSof'], $_POST['chequeno'], $_POST['exDate'], $_POST['exType'][$count], $_POST['updatedBy'])), 'cashbook');
        $count++;
    }
    echo json_encode(array('status'=>200, 'msg'=>'Successfully added'));
}

if(isset($_GET['cash'])){
    // $cash = $f->selectJoins("select * from cashbook where cid = 1 and status = 3 order by id asc")->fetchAll();
    $cash = $f->selectJoins("select * from cashbook where cid = 1 order by id asc")->fetchAll();
    echo json_encode(array('cash'=>$cash));
}

if(isset($_GET['trans'])){

    $uid = $_GET['trans'];
    $role = $f->selectJoins("select r.hierarchy from user_roles u left join roles r on r.id =u.rid where u.uid = '$uid' limit 1")->fetchObject();
    $rid = $role->hierarchy;

    $cash = $f->selectJoins("select * from cashbook where cid = 1 and status = '$rid' and type !='income' order by id asc")->fetchAll();
    echo json_encode(array('cash'=>$cash));

}

if(isset($_GET['details'])){
    $id = $_GET['details'];
    $budget = $f->selectJoins("select c.account, c.name as chart, p.name as prog, b.subprogram from budget b left join chart_of_account c on c.id = b.coa left join programmes p 
                on b.program = p.id where b.id in (select coa_id from cashbook where id = '$id')")->fetchAll();
    $actions = $f->selectJoins("select * from actions where cid = '$id'")->fetchAll();
    $cash = $f->selectJoins("select * from cashBook where id = '$id'")->fetchAll();
    echo json_encode(['other'=>$budget, 'actions'=>$actions, 'cash'=>$cash]);
}

if(isset($_POST['fromCash'])){
    $data = [
        'amount'=>$_POST['toAmount'],
        'description'=>$_POST['cashdesc'],
        'bank'=>$_POST['fromCash'],
        'type'=>'expense',
        'cid'=>1,
        'paye'=>'Transfer',
    ];
    $data2 = [
        'amount'=>$_POST['toAmount'],
        'description'=>$_POST['cashdesc'],
        'bank'=>$_POST['toCash'],
        'type'=>'income',
        'cid'=>1,
        'paye'=>'Transfer',
    ];
    $f->insertData($data, 'cashbook');
    $f->insertData($data2, 'cashbook');
    echo json_encode(array('status'=>200, 'msg'=>'Successfully added'));
}

if(isset($_POST['commForAct'])){

    $comm = $_POST['commForAct'];
    $approved = $_POST['approved'];
    $userFor = $_POST['userFor'];
    $cashId = $_POST['cashId'];

    if($approved == 0){
        $f->runQuery("update cashBook set status = status + 1 where id = '$cashId'");
    }else{
        $f->runQuery("update cashBook set status = '$approved' where id = '$cashId'");
    }   

    $f->runQuery("insert into actions (uid, cid, text) values ('$userFor', '$cashId', '$comm')");

    echo json_encode(['status'=>200]);


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