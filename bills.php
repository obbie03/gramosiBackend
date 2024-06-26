<?php

include('connector.php');

if(isset($_GET['bulkbills'])){
    $uid = $_GET['bulkbills'];
    $cid = getcid($uid);
    $sof = $f->selectData('source_of_fund','', "where c_id='$cid' limit 1")->fetchObject()->id;
    $cc = $f->selectData('collection_center','', "where c_id='$cid' limit 1")->fetchObject()->id;
    $bank = $f->selectData('bank','', "where c_id='$cid' limit 1")->fetchObject()->id;
    $datas = $f->selectJoins('select * from user_property up left join property p on up.p_id = p.id')->fetchAll();
    foreach($datas as $data){
        $c = $f->selectJoins("select * from bills where bank ='$bank'")->rowCount();
        $c++;
        $recNum = 'INV'.$c.'/'.substr(date('Y'),2,4);
        $dat = $f->selectData('chart_of_account', '', 'where account = "'.$data['coa_id'].'" limit 1')->fetchObject();
        $combinedArray = array_combine(array('invoice_no', 'sof_id', 'cc_id','customer', 'bank', 'issuer'), 
                                       array($recNum, $sof, $cc, $data['u_id'], $bank, $uid ));
        $check = $f->insertData($combinedArray, 'bills');
        $bid = $f->selectData('bills', '', "where invoice_no = '$recNum' order by id desc limit 1")->fetchObject();
        $final = array_combine(array('b_id', 'amount', 'coa_id', 'description'), array($bid->id,(floatval($data['payable'])/2),$data['coa_id'],'Bulk bills'));
        $check = $f->insertData($final, 'billsitems');
    }
    echo json_encode(array('status'=>'done'));
}

if(isset($_POST['uidsingle'])){
        $uid = $_POST['uidsingle'];
        $check = true;
        $c = $f->selectJoins("select * from bills where bank ='".$_POST['bank']."'")->rowCount();
        $c++;
        $recNum = 'INV'.$c.'/'.substr(date('Y'),2,4);
        $combinedArray = array_combine(array('invoice_no', 'sof_id', 'cc_id','customer', 'bank', 'issuer'), 
        array($recNum, $_POST['sof'], $_POST['cc'], $_POST['custID'], $_POST['bank'], $uid ));
        $check = $f->insertData($combinedArray, 'bills');
        $bid = $f->selectData('bills', '', "where invoice_no = '$recNum' order by id desc limit 1")->fetchObject();
        foreach($_POST['data'] as $data){
            $final = array_combine(array('b_id', 'amount', 'coa_id', 'description'), array($bid->id, floatval($data['amount']),$data['act'], $data['actDesc']));
            $check = $f->insertData($final, 'billsitems');
         }      
    echo json_encode(array('status'=>'done', 'data'=>$recNum));
}

if(isset($_POST['billID'])){

    if($_POST['recCred'] == 1){

        $year = date('Y');
        $c = $f->selectJoins("select * from receipts where YEAR(date) = '$year' and type = 1")->rowCount();
        $c++;
        $recNum = 'R'.$c.'/'.substr(date('Y'),2,4);
        $combinedArray = array_combine(array('receipt_no', 'b_id', 'issuer', 'amount', 'description', 'type', 'cust'), 
                                        array($recNum, $_POST['billID'], $_POST['recUser'], $_POST['recAmount'], $_POST['recDesc'], 1, $_POST['cust']));
        $check = $f->insertData($combinedArray,'receipts');
        $data = $f-> selectJoins("select * from bills where id = '".$_POST['billID']."' limit 1")->fetchObject();
        $id = $f->insertData(array_combine(array('coa_id', 'amount', 'description', 'paye', 'type', 'cid', 'bank', 'sof', 'cheque', 'proj_id'), 
        array(0, $_POST['recAmount'], 'Receipt for '.$data->invoice_no.':'.$_POST['recDesc'], $_POST['custName'], 'income', 1, 13, $data->sof_id, $recNum, 0)), 'cashbook');

    }else{

        $year = date('Y');
        $c = $f->selectJoins("select * from receipts where YEAR(date) = '$year' and type = 2")->rowCount();
        $c++;
        $recNum = 'CN'.$c.'/'.substr(date('Y'),2,4);
        $combinedArray = array_combine(array('receipt_no', 'b_id', 'issuer', 'amount', 'description', 'type', 'cust'), 
                                        array($recNum, $_POST['billID'], $_POST['recUser'], $_POST['recAmount'], 'Credit note for '.$_POST['invno'].':'.$_POST['recDesc'], 2, $_POST['cust']));
        $check = $f->insertData($combinedArray,'receipts');
    }


    if($check){
        echo json_encode(array('status'=>'done', 'data'=>$recNum));
    }else{
        echo json_encode(array('status'=>'Didnt post'));
    }
}


if(isset($_GET['bills'])){
    $uid = $_GET['bills'];
    $cid = getcid($uid);
    $f->load("SELECT b.*,c.firstname, c.lastname, COALESCE(i.total_amount - COALESCE(r.total_amount, 0), 0) AS amount
    FROM bills AS b
    LEFT JOIN (
        SELECT b_id, SUM(amount) AS total_amount
        FROM billsitems
        GROUP BY b_id 
    ) AS i ON b.id = i.b_id
    LEFT JOIN (
        SELECT b_id, SUM(amount) AS total_amount
        FROM receipts
        GROUP BY b_id
    ) AS r ON b.id = r.b_id left join bio_data c on b.customer = c.u_id
    WHERE COALESCE(i.total_amount, 0) > COALESCE(r.total_amount, 0) and b.cid = '$cid'
    ");
}

if(isset($_GET['bill'])){
    $bid = $_GET['bill'];
    $bills = $f->selectJoins("select * from bills b left join billsitems i on b.id = i.b_id left join bio_data c on b.customer = c.u_id where b.id = '$bid'")->fetchAll();
    $recs = $f->selectJoins("select * from receipts where b_id = '$bid'")->fetchAll();
    echo json_encode(array('bills'=>$bills, 'recs'=>$recs));
}





