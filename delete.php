<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    die("❌ Invalid request.");
}

$file_id = intval($_GET["id"]);
$username = $_SESSION["username"];

// Check if the logged-in user uploaded this PDF
$stmt = $conn->prepare("SELECT file_path FROM pdf_documents WHERE id = ? AND uploaded_by = ?");
$stmt->bind_param("is", $file_id, $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    die("❌ You do not have permission to delete this file.");
}

$stmt->bind_result($file_path);
$stmt->fetch();
$stmt->close();

// Delete the file from storage
if (file_exists($file_path)) {
    unlink($file_path);
}

// Delete the record from the database
$stmt = $conn->prepare("DELETE FROM pdf_documents WHERE id = ? AND uploaded_by = ?");
$stmt->bind_param("is", $file_id, $username);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php?msg=PDF deleted successfully");
exit();
?>
