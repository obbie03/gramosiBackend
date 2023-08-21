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