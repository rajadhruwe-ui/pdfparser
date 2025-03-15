<?php
$conn = new mysqli("localhost", "root", "", "pdf_database");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
function logAction($user_id, $action, $conn) {
    $stmt = $conn->prepare("INSERT INTO user_activity (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
}

?>
