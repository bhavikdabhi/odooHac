<?php
session_start();
include '../includes/db.php';
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if(isset($_GET['approvals']) && ($_GET['approvals']==1)){
    // Manager/Admin view pending approvals
    if($role=='Manager'){
        $sql = "SELECT e.*, u.name as employee_name FROM expenses e 
                JOIN users u ON u.id=e.user_id 
                WHERE e.status='Pending' AND u.manager_id=$user_id";
    } else { // Admin view all
        $sql = "SELECT e.*, u.name as employee_name FROM expenses e JOIN users u ON u.id=e.user_id WHERE e.status='Pending'";
    }

    $res = $conn->query($sql);
    echo "<table class='table table-bordered'><tr><th>Employee</th><th>Amount</th><th>Category</th><th>Date</th><th>Actions</th></tr>";
    while($row=$res->fetch_assoc()){
        echo "<tr>
                <td>{$row['employee_name']}</td>
                <td>{$row['converted_amount']} {$row['currency']}</td>
                <td>{$row['category']}</td>
                <td>{$row['date']}</td>
                <td>
                    <button class='btn btn-success approve-btn' data-id='{$row['id']}'>Approve</button>
                    <button class='btn btn-danger reject-btn' data-id='{$row['id']}'>Reject</button>
                </td>
              </tr>";
    }
    echo "</table>";
    ?>
    <script>
    $('.approve-btn, .reject-btn').click(function(){
        var expense_id = $(this).data('id');
        var action = $(this).hasClass('approve-btn') ? 'Approved' : 'Rejected';
        var comments = prompt('Add comments (optional)');
        $.post('ajax/approve_expense.php',{expense_id,action,comments},function(res){
            alert(res.msg);
            location.reload();
        },'json');
    });
    </script>
    <?php
} else {
    // Employee view
    $sql = "SELECT * FROM expenses WHERE user_id=$user_id ORDER BY created_at DESC";
    $res = $conn->query($sql);
    echo "<table class='table table-bordered'><tr><th>Amount</th><th>Category</th><th>Date</th><th>Status</th></tr>";
    while($row=$res->fetch_assoc()){
        echo "<tr>
                <td>{$row['converted_amount']} {$row['currency']}</td>
                <td>{$row['category']}</td>
                <td>{$row['date']}</td>
                <td>{$row['status']}</td>
              </tr>";
    }
    echo "</table>";
}
