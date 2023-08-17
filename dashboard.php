<?php

include('connector.php');

$r = include('bills.php');

if (isset($_GET['dashboard-content'])) {
    echo "hello brice!";
}