<?php

include('connector.php');

if (isset($_GET['allcustomers'])) {
    $cust = getCustomer("allcustomers");
    echo json_encode($cust);
}

if (isset($_POST['firstname'])) {
    if ($_POST['submission_type'] == 'new-addition') {
        echo json_encode(addCustomer($_POST));
    } else if ($_POST['submission_type'] == 'update') {
        echo json_encode(updateCustomer($_POST));
    }
}

if(isset($_GET['custState'])){

    $cid = $_GET['custState'];

    $bills = $f->selectJoins("select * from bills b left join billsitems i on b.id = i.b_id left join bio_data c on b.customer = c.u_id where b.customer = '$cid'")->fetchAll();
    $recs = $f->selectJoins("select * from receipts where cust = '$cid'")->fetchAll();
    echo json_encode(array('bills'=>$bills, 'receipts'=>$recs));

}

if(isset($_POST['Examount'])){
        $year = date('Y');
        $c = $f->selectJoins("select * from receipts where YEAR(date) = '$year' and type = 1")->rowCount();
        $c++;
        $recNum = 'R'.$c.'/'.substr(date('Y'),2,4);
        $combinedArray = array_combine(array('receipt_no', 'b_id', 'issuer', 'amount', 'description', 'cust'), 
                                        array($recNum, 0, 1, $_POST['Examount'], $_POST['Exdesc'], $_POST['cidNames']));
        $check = $f->insertData($combinedArray,'receipts');
        $id = $f->insertData(array_combine(array('coa_id', 'amount', 'description', 'paye', 'type', 'cid', 'bank', 'sof', 'cheque', 'proj_id'), 
        array($_POST['Exbudget'], $_POST['Examount'], 'Receipt', $_POST['Expaye'], 'income', 1, 13, $_POST['cashSof'], $_POST['chequeno'], $_POST['exType'])), 'cashbook');
    if($check){
        echo json_encode(array('status'=>200 , 'data'=>$recNum));
    }else{
        echo json_encode(array('status'=>'Didnt post'));
    }
    
}