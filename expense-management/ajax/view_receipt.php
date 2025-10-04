<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT receipt_blob, receipt_type, receipt_name FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($blob, $type, $name);

    if ($stmt->fetch()) {
        header("Content-Type: " . $type);
        header("Content-Disposition: inline; filename=\"" . $name . "\"");
        echo $blob;
    } else {
        echo "Receipt not found.";
    }
}
