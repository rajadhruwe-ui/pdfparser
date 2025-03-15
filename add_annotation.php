<?php
include "session.php";
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdf_id = $_POST["pdf_id"];
    $user_id = $_SESSION["user_id"];
    $comment = $_POST["comment"];

    $stmt = $conn->prepare("INSERT INTO pdf_annotations (pdf_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $pdf_id, $user_id, $comment);
    $stmt->execute();

    header("Location: view.php?id=$pdf_id");
    exit();
}
?>
