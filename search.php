<?php
include "config.php";
logAction($_SESSION["user_id"], "Searched for '$query'", $conn);

if (isset($_GET['query'])) {
    $query = $_GET['query'];

    $sql = "SELECT id, filename, file_path, content FROM pdf_data WHERE content LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $query . "%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Results for: <strong>" . htmlspecialchars($query) . "</strong></h3>";

    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>
                    <a href='" . $row['file_path'] . "' target='_blank'>" . $row['filename'] . "</a> 
                    <p><strong>Snippet:</strong> " . substr($row['content'], 0, 200) . "...</p>
                  </li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No matching PDFs found.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
