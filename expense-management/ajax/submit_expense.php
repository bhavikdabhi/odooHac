<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);
    $currency = trim($_POST['currency']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];

    $receipt_blob = null;
    $receipt_type = null;
    $receipt_name = null;

    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $receipt_blob = file_get_contents($_FILES['receipt']['tmp_name']);
        $receipt_type = $_FILES['receipt']['type'];
        $receipt_name = $_FILES['receipt']['name'];
    }

    $converted_amount = $amount; // Placeholder for real conversion

    $stmt = $conn->prepare("INSERT INTO expenses 
        (user_id, amount, currency, converted_amount, category, description, date, receipt_blob, receipt_type, receipt_name)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'msg' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

   
    $stmt->bind_param("idsdssssss",
        $user_id,
        $amount,
        $currency,
        $converted_amount,
        $category,
        $description,
        $date,
        $receipt_blob,
        $receipt_type,
        $receipt_name
    );

    if ($receipt_blob !== null) {
        $stmt->send_long_data(7, $receipt_blob); // 7 is zero-based index for 8th param
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'msg' => 'Expense Submitted']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Submission Failed: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request']);
}
?>
