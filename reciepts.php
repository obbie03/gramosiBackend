<?php

include('connector.php');



if (isset($_GET['receipts'])) {
    $r = $f->load("SELECT * FROM receipts");
    // echo $r;
}

if (isset($_GET['get_receipt'])) {
    $rid = $_GET['get_receipt'];
    $r = $f->selectJoins("SELECT * FROM receipts WHERE id = '$rid'")->fetchAll(PDO::FETCH_ASSOC);
    $bid = $r[0]['b_id'];
    $b = $f->selectJoins("SELECT * FROM bills WHERE id = '$bid'")->fetchAll(PDO::FETCH_ASSOC)[0];
    $items = $f->selectJoins("SELECT * FROM billsitems WHERE b_id = '$bid'")->fetchAll(PDO::FETCH_ASSOC);
    $total = 0;
    foreach ($items as $item) {
        $total += floatval($item['amount']);
    }
    $cid = $b['customer'];
    $c = $f->selectJoins("SELECT * FROM customers WHERE id = '$cid'")->fetchAll(PDO::FETCH_ASSOC)[0];
    echo json_encode(array("receipt" => $r[0], "bill" => $b, "customer" => $c, "total_amount" => $total));
}

