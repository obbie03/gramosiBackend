<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
ini_set('max_execution_time', 9999);


require 'Core/Functions.php';
use Core\Functions;

$f = new Functions('41.63.9.34', 3306, 'gramosic', 'Obbie03', 'Malumbo@11');

function getcid($uid)
{
    global $f;
    $cid = $f->selectData('authentication', '', "where id='$uid' limit 1")->fetchObject()->c_id;
    return $cid;
}

function addProgram($name)
{
    global $f;
    $name = strtolower($name);
    $prog = $f->selectData('programmes', '', "where lower(name) = '$name' limit 1");
    if ($prog->rowCount() > 0) {
        return $prog->fetchObject()->id;
    }
    $id = $f->insertReturnId(array('c_id' => 1, 'name' => $name), 'programmes');
    return $id;
}

function addCustomer($d)
{
    global $f;
    $names = explode(" ", $d['firstname'] . " " . $d['middlename'] . " " . $d['lastname']);
    if (!$f->checkTableValue($d['account_no'], 'customers', 'account_no')) {
        $id = $f->insertReturnId(array('c_id' => 1, 'email' => $d['account_no'] . '@email.com', 'user_type' => 1, 'password' => '$2y$10$z4MMdmy.BOzmsJINTEheKe3RxdNEbh7PnivEmPhpOReNhcow8dsZK', 'mode' => 0), 'authentication');
        $f->insertData(array('u_id' => $id, 'firstname' => $names[0], 'middlename' => count($names) > 2 ? $names[1] : '', 'lastname' => $names[count($names) - 1], 'nrc' => $d['nrc'], 'phonenumber' => $d['phonenumber'], 'sex' => $d['sex'], 'dob' => $d['dob']), 'bio_data');
        $f->insertData(array('u_id' => $id, 'account_no' => $d['account_no'], 'code' => $d['code']), 'customers');
        return ["status" => true, "msg" => "New Customer Added Successfully!"];
    } else {
        return ["status" => false, "error" => "Account Already Exists!"];
    }
}

function updateCustomer($d)
{
    global $f;
    $acc = $d['account_no'];
    $id = $d['id'];
    $customer = $f->selectData('customers', "", "WHERE account_no = '$acc' and id='$id' limit 1")->fetchAll(PDO::FETCH_ASSOC);
    if (count($customer) > 0) {
        if (!$f->checkTableValue($acc, "customers", "account_no")) {
            return ["status" => false, "error" => "Account Already Exists!"];
        }
    }
    try {
        $accno = $d['account_no'];
        $code = $d['code'];
        $id = $d['id'];
        $fn = $d['firstname'];
        $ln = $d['lastname'];
        $mn = $d['middlename'];
        $nrc = $d['nrc'];
        $sex = $d['sex'];
        $dob = $d['dob'];
        $uid = $customer[0]['u_id'];
        $cust = $f->selectJoins("update customers set account_no='$accno', code='$code' where id=$id");
        $bio_data = $f->selectJoins("update bio_data set firstname='$fn', middlename='$mn', lastname='$ln', nrc='$nrc', sex='$sex', dob='$dob' where u_id=$uid");
        return ["status" => true, "msg" => "Customer Info Updated Successfully!"];
    } catch (\Throwable $th) {
        return ["status" => false, "error" => $th];
    }
}


function getCustomer($type)
{
    global $f;
    $customer = $f->selectJoins("select * from customers inner join bio_data on customers.u_id = bio_data.u_id order by customers.id desc")->fetchAll(PDO::FETCH_ASSOC);
    return $customer;
}

function addPropertyType($name)
{
    global $f;
    $name = strtolower($name);
    $prop = $f->selectData('property_type', '', "where lower(name) = '$name' limit 1");
    if ($prop->rowCount() > 0) {
        return $prop->fetchObject()->id;
    }
    $id = $f->insertReturnId(array('name' => $name), 'property_type');
    return $id;
}

function getProperty($type)
{
    global $f;
    $property = $f->selectJoins("select property.*, property_type.name as property_type, user_property.u_id as owner_id, bio_data.firstname, bio_data.lastname from property inner join property_type inner join user_property inner join bio_data on property.type_id = property_type.id and property.id = user_property.p_id and bio_data.u_id = user_property.u_id order by property.id desc")->fetchAll(PDO::FETCH_ASSOC);

    $property_types = $f->selectJoins("select * from property_type")->fetchAll(PDO::FETCH_ASSOC);
    $coa = $f->selectJoins("select * from chart_of_account")->fetchAll(PDO::FETCH_ASSOC);
    $cust = $f->selectJoins("select customers.*, bio_data.id as bio_id, bio_data.firstname, bio_data.lastname from customers inner join bio_data on customers.u_id = bio_data.u_id")->fetchAll(PDO::FETCH_ASSOC);
    return [
        "property" => $property,
        "property_type" => $property_types,
        "coa" => $coa,
        "customers" => $cust
    ];
}

function addBank($name, $code, $account)
{
    global $f;
    $name = strtolower($name);
    $bank = $f->selectData('bank', '', "where lower(name) = '$name' limit 1");
    if ($bank->rowCount() > 0) {
        return $bank->fetchObject()->id;
    }
    $f->insertData(array('name' => $name, 'code' => $code, 'account_no' => $account, 'c_id' => 1), 'bank');
    return true;
}

function addProperty($name, $coa, $type, $street, $town, $location, $plot, $hectares, $value, $improvement, $rate_value, $rate, $payable, $customer)
{
    global $f;
    $getCoa = $f->selectData('chart_of_account', '', "where id = '$coa' limit 1");
    $cust_id = $f->selectData('customers', '', "where id = '$customer' limit 1");
    if ($getCoa->rowCount() > 0) {
        $data = array(
            'c_id' => 1,
            'type_id' => $type,
            'coa_id' => $getCoa->fetchObject()->id,
            'street' => $street,
            'town' => $town,
            'name' => $name,
            'location' => $location,
            'plot' => $plot,
            'hectares' => $hectares,
            'value' => $value,
            'improvement' => $improvement,
            'rate_value' => $rate_value,
            'rate' => $rate,
            'payable' => $payable
        );
        $prop = $f->selectData('property', '', "where name = '$name' and town = '$town' and plot = '$plot' and location = '$location' limit 1");
        if ($prop->rowCount() > 0) {
            return $prop->fetchObject()->id;
        }
        $id = $f->insertReturnId($data, 'property');
        if ($id) {
            $f->insertReturnId(["u_id" => $cust_id->fetchObject()->u_id, "p_id" => $id], "user_property");
        }
        return ["status" => true, "id" => $id, "msg" => "New Property Added Successfully!"];
    } else {
        return ["status" => false, "error" => "Chart of account not found!"];
    }
}

function updateProperty($d)
{
    global $f;
    $uid = $d['c_id'];
    $id = $d['id'];
    $type = $d['type_id'];
    $coa = $d['coa_id'];
    $street = $d['street'];
    $town = $d['town'];
    $loc = $d['location'];
    $plot = $d['plot'];
    $hect = $d['hectares'];
    $value = $d['value'];
    $improvement = $d['improvement'];
    $rate_value = $d['rate_value'];
    $rate = $d['rate'];
    $payable = $d['payable'];
    $name = $d['name'];
    $pid = $d['id'];
    $property_user = $f->selectJoins("update user_property set u_id =  '$uid' where p_id = '$id'");
    $prop = $f->selectJoins(
        "update property set type_id = $type, 
        coa_id = '$coa', street = '$street', 
        town = '$town', 
        location='$loc',plot='$plot',
        hectares='$hect',value='$value', 
        improvement='$improvement',
        rate_value='$rate_value',
        rate='$rate',
        rate='$rate',
        name='$name'
        where id = $pid"
    );
    return ["status" => true, "msg" => "Property Updated Successfully!", "pu" => $property_user, "p" => $prop];
}