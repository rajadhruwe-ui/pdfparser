<?php
require 'vendor/autoload.php';
include "config.php";
logAction($_SESSION["user_id"], "Uploaded a new PDF", $conn);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdf_file"])) {
    $uploadDir = "uploads/";
    $filePath = $uploadDir . basename($_FILES["pdf_file"]["name"]);

    if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $filePath)) {
        // Extract text
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        // Save to database
        $stmt = $conn->prepare("INSERT INTO pdf_data (filename, file_path, content) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_FILES["pdf_file"]["name"], $filePath, $text);
        $stmt->execute();

        echo "<script>alert('✅ PDF uploaded successfully!'); window.location.href='dashboard.php';</script>";

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('❌ Upload failed!'); window.location.href='dashboard.php';</script>";
    }
    
}
?>
