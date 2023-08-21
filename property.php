<?php

include('connector.php');

if (isset($_GET['property'])) {
    $property = getProperty("property");
    echo json_encode($property);
}

if (isset($_POST['location'])) {
    $d = $_POST;

    if ($d['submission_type'] == 'new-addition') {
        echo json_encode(
            addProperty(
                $d['name'],
                $d['coa_id'],
                $d['type_id'],
                $d['street'],
                $d['town'],
                $d['location'],
                $d['plot'],
                $d['hectares'],
                $d['value'],
                $d['improvement'],
                $d['rate_value'],
                $d['rate'],
                $d['payable'],
                $d['c_id']
            )
        );
    } else if ($_POST['submission_type'] == 'update') {
        echo json_encode(updateProperty($d));
    }
}