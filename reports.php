<?php

include('connector.php');

if(isset($_GET['gl'])){
    $cid = getCid($_GET['gl']);
    $bills = $f->selectJoins("SELECT b.*,i.*, c.*
    FROM bills AS b
    LEFT JOIN (
        SELECT *
        FROM billsitems
    ) AS i ON b.id = i.b_id left join bio_data c on b.customer = c.u_id where b.cid='$cid'")->fetchAll();
    $bids = array_map(function ($bill) {return $bill['id'];}, $bills);
    $final = implode(',', $bids);
    $recs = $f->selectJoins("select * from receipts r left join bills b on r.b_id = b.id left join bio_data c on b.customer = c.u_id where b_id in ($final)")->fetchAll();
    echo json_encode(array('bills'=>$bills, 'recs'=>$recs));
}

if(isset($_GET['state'])){
    $cid = $_GET['state'];
    $bills = $f->selectJoins("SELECT b.*,i.amount, i.coa_id, i.description, c.firstname, c.lastname
    FROM bills AS b
    LEFT JOIN (
        SELECT *
        FROM billsitems
    ) AS i ON b.id = i.b_id left join bio_data c on b.customer = c.u_id where b.customer='$cid'")->fetchAll();
    $bids = array_map(function ($bill) {return $bill['id'];}, $bills);
    $final = implode(',', $bids);
    $recs = $f->selectJoins("select * from receipts r left join bills b on r.b_id = b.id left join bio_data c on b.customer = c.u_id where b_id in ($final)")->fetchAll();
    echo json_encode(array('bills'=>$bills, 'recs'=>$recs));
}

if(isset($_GET['cs'])){
    $cid = getCid($_GET['cs']);
    $bills = $f->selectJoins("SELECT
    b.*,
    i.total_amount,
    c.*,
    ab.amount as balance
FROM
    bills AS b
LEFT JOIN (
    SELECT
        b_id,
        SUM(amount) AS total_amount
    FROM
        billsitems
    GROUP BY
        b_id
) AS i ON b.id = i.b_id left join all_balances ab on b.customer = ab.u_id
LEFT JOIN bio_data AS c ON b.customer = c.u_id")->fetchAll();
    $recs = $f->selectJoins("select * from receipts r left join bills b on r.b_id = b.id left join bio_data c on b.customer = c.u_id")->fetchAll();
    echo json_encode(array('bills'=>$bills, 'recs'=>$recs));
}


if(isset($_GET['tb'])){

        // $cid = getCid($_GET['cs']);
        $cid = 1;
        $bills = $f->selectJoins("SELECT coa_id, SUM(amount) - IFNULL(receipt_amount, 0) 
        AS amount FROM billsitems b LEFT JOIN ( SELECT b_id, amount AS receipt_amount
         FROM receipts r WHERE r.type = 2 ) AS subquery ON b.b_id = subquery.b_id GROUP BY b.coa_id")->fetchAll();
        $recs = $f->selectJoins("select sum(amount) as amount from receipts where type != 2")->fetchAll();
        $supplierData = $f->selectJoins("SELECT coa, type, sum(amount) as amount from supplier_trans GROUP by coa, type")->fetchAll();
        $cash = $f->selectJoins("SELECT SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS difference FROM cashbook")->fetchAll();
        $journal = $f->selectJoins("select * from journals")->fetchAll();
        echo json_encode(array('bills'=>$bills, 'recs'=>$recs, 'supplier'=>$supplierData, 'cash'=>$cash, 'journal'=>$journal));
    
}


if(isset($_GET['randp'])){

    $budget = $f->selectJoins("SELECT * FROM chart_of_account c left join budget b on c.id = b.coa where b.id is not null")->fetchAll();
    $cash = $f->selectJoins("SELECT * FROM cashbook c left join budget b on c.coa_id = b.id where b.id is not null")->fetchAll();
    echo json_encode(['budget'=>$budget, 'cash'=>$cash]);

}

if(isset($_GET['chequeno'])){

    $cheque = $_GET['chequeno'];
    $amount = $_GET['amount'];
    $get  = $f->selectJoins("select * from cashbook where amount = '$amount' and cheque = '$cheque'")->rowCount();

    if($get > 0){
        echo json_encode(['status'=>200]);
    }else{
        echo json_encode(['status'=>400]);
    }
    


}

