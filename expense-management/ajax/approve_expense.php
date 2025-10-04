<?php
session_start();
include '../includes/db.php';

if(isset($_POST['expense_id'],$_POST['action'])){
    $expense_id = $_POST['expense_id'];
    $action = $_POST['action'];
    $comments = $_POST['comments'] ?? '';

    $stmt = $conn->prepare("INSERT INTO approvals (expense_id, approver_id, status, comments, approved_at) VALUES (?,?,?,?,NOW())");
    $stmt->bind_param("iiss",$expense_id,$_SESSION['user_id'],$action,$comments);
    $stmt->execute();

    // Update Expense Status (simplified)
    if($action=='Approved'){
        $conn->query("UPDATE expenses SET status='Approved' WHERE id='$expense_id'");
    } else {
        $conn->query("UPDATE expenses SET status='Rejected' WHERE id='$expense_id'");
    }

    echo json_encode(['status'=>'success','msg'=>'Action Recorded']);
}
