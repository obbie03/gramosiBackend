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
    $bills = $f->selectJoins("SELECT b.*,i.*, c.*
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
    c.*
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
) AS i ON b.id = i.b_id
LEFT JOIN bio_data AS c ON b.customer = c.u_id")->fetchAll();
    $recs = $f->selectJoins("select * from receipts r left join bills b on r.b_id = b.id left join bio_data c on b.customer = c.u_id")->fetchAll();
    echo json_encode(array('bills'=>$bills, 'recs'=>$recs));
}

