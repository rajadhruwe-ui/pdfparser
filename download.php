<?php
include "config.php";

if (!isset($_GET["id"])) {
    die("❌ Invalid request.");
}

$id = intval($_GET["id"]);
$stmt = $conn->prepare("SELECT filename, content FROM pdf_documents WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($filename, $content);
$stmt->fetch();
$stmt->close();

if (!$content) {
    die("❌ PDF not found.");
}

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
echo $content;
?>
