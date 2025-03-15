<?php
require 'vendor/autoload.php'; // Load Smalot PDFParser

use Smalot\PdfParser\Parser;

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL password
$dbname = "pdf_database";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Specify the PDF file path
#$pdfPath = "C:\\xampp\\htdocs\\pdfparser\\sample.pdf";
$pdfPath = "C:/xampp/htdocs/pdfparser/sample.pdf";
if (!file_exists($pdfPath)) {
    die("❌ Error: PDF file not found!");
}

// Parse the PDF file
$parser = new Parser();
$pdf = $parser->parseFile($pdfPath);
$text = $pdf->getText();

// Insert data into MySQL
$stmt = $conn->prepare("INSERT INTO pdf_data (filename, content) VALUES (?, ?)");
$stmt->bind_param("ss", $pdfPath, $text);

if ($stmt->execute()) {
    echo "✅ PDF data stored successfully!";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
