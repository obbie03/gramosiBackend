<?php

    include('connector.php');

    if(isset($_POST['name'])){
        $data = [
            'name'=>$_POST['name']
        ];
        $f->insertData($data, 'suppliers');
        echo json_encode(['status'=>200, 'msg'=>'Successful']);
    }

    if(isset($_GET['all'])){
        $f->load("select s.id as ids,s.name, sp.* from suppliers s left join supplier_trans 
        sp on s.id = sp.sid");
    }

    // if(isset($_POST['rec'])){
    //     $data1 = [
    //         'amount'=>$_POST['amu'],
    //         'description'=>$_POST['desc'],
    //         'bank'=>1,
    //         'type'=>'income',
    //         'cid'=>1,
    //         'paye'=>$_POST['nameTOsend'],
    //         'coa_id'=>$_POST['coa'],
    //         'cheque'=>$inv
    //     ];
    //     $f->insertData($data1, 'cashbook');
    //      $data = [
    //          'sid'=>$_POST['sidT'],
    //          'invoice'=>$inv,
    //          'amount'=>$_POST['amu'],
    //          'type'=>2,
    //          'comment'=>$_POST['desc']
    //      ];
    //     $f->insertData($data, 'supplier_trans');
    //      echo json_encode(array('status'=>200, 'msg'=>'Successfully added'));

    // }

    if(isset($_POST['exType'])){
        $id = $f->insertReturnId(array_combine(array('coa_id', 'amount', 'description', 'paye', 'type', 'cid', 'bank', 'sof', 'cheque', 'proj_id', 'updatedBy'), 
        array($_POST['Exbudget'], $_POST['Examount'], $_POST['Exdesc'], $_POST['Expaye'], 'expense', 1, $_POST['cashBook'], $_POST['cashSof'], $_POST['chequeno'], $_POST['exType'], $_POST['updatedBy'])), 'cashbook');
        
        $data = [
             'sid'=>$_POST['sidNames'],
             'invoice'=>$_POST['chequeno'],
             'amount'=>$_POST['Examount'],
             'type'=>2,
             'comment'=>$_POST['Exdesc']
         ];
        $f->insertData($data, 'supplier_trans');

        echo json_encode(array('status'=>200, 'msg'=>'Successfully added'));
     }

    
    if(isset($_POST['coa'])){
        if($_POST['tipo'] == 2){
            $s = $f->selectJoins("select * from supplier_trans where comment like '%Debit note%'")->rowCount();
            $s++;
            $inv =  'DN/'.$s."/".substr(date('Y'),2,4);  
            $data = [
                'sid'=>$_POST['sidT'],
                'invoice'=>$inv,
                'amount'=>$_POST['amu'],
                'type'=>2,
                'comment'=>'Debit note '.$_POST['desc'],
                'coa'=>$_POST['coa']
            ];
           $f->insertData($data, 'supplier_trans');
        }else{
            $s = $f->selectJoins("select * from supplier_trans where type = 1")->rowCount();
            $s++;
            $inv =  'INVs/'.$s."/".substr(date('Y'),2,4);   
            $data = [
                'sid'=>$_POST['sidT'],
                'invoice'=>$inv,
                'amount'=>$_POST['amu'],
                'type'=>1,
                'comment'=>$_POST['desc'],
                'coa'=>$_POST['coa']
            ];
           $f->insertData($data, 'supplier_trans');
        }
         echo json_encode(array('status'=>200, 'msg'=>'Successfully added'));
    }


