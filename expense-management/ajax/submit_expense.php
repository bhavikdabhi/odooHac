<?php
session_start();
include '../includes/db.php';
$user_id = $_SESSION['user_id'];

if(isset($_POST['amount'])){
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    // Receipt Upload
    $receipt_url = null;
    if(isset($_FILES['receipt']) && $_FILES['receipt']['error']==0){
        $target = '../uploads/'.time().'_'.basename($_FILES['receipt']['name']);
        move_uploaded_file($_FILES['receipt']['tmp_name'], $target);
        $receipt_url = $target;
    }

    // Convert Amount Placeholder (same currency)
    $converted_amount = $amount;

    $stmt = $conn->prepare("INSERT INTO expenses (user_id, amount, currency, converted_amount, category, description, date, receipt_url) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("idddssss",$user_id,$amount,$currency,$converted_amount,$category,$description,$date,$receipt_url);
    if($stmt->execute()){
        echo json_encode(['status'=>'success','msg'=>'Expense Submitted']);
    }else{
        echo json_encode(['status'=>'error','msg'=>'Submission Failed']);
    }
}
