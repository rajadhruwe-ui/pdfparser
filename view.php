<?php
include "config.php";

if (!isset($_GET["id"])) {
    die("❌ Invalid request.");
}

$id = intval($_GET["id"]);
$stmt = $conn->prepare("SELECT filename, extracted_text, content FROM pdf_documents WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($filename, $extracted_text, $content);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View PDF | PDF Parser</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>📄 <?= htmlspecialchars($filename) ?></h2>
        <p><strong>Extracted Text:</strong></p>
        <pre><?= htmlspecialchars($extracted_text) ?></pre>

        <a href="download.php?id=<?= $id ?>" class="btn btn-primary">📥 Download PDF</a>
        <a href="dashboard.php" class="btn btn-secondary">🔙 Back</a>
    </div>
</body>
</html>
