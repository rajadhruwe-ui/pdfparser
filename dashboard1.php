<?php
session_start();
include "config.php"; // Database connection

// Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$username = $_SESSION["username"] ?? "Guest";
$role = $_SESSION["role"] ?? "user";

// Handle PDF upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdf_file"])) {
    $pdf_name = $_FILES["pdf_file"]["name"];
    $pdf_tmp = $_FILES["pdf_file"]["tmp_name"];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($pdf_name);

    // Read file content
    $pdf_content = file_get_contents($pdf_tmp);

    if (move_uploaded_file($pdf_tmp, $target_file)) {
        // Extract text from PDF
        require 'vendor/autoload.php'; // Ensure pdfparser is installed
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($target_file);
        $extracted_text = $pdf->getText();

        // Save in database (including binary content)
        $stmt = $conn->prepare("INSERT INTO pdf_documents (filename, file_path, extracted_text, content, uploaded_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $pdf_name, $target_file, $extracted_text, $pdf_content, $username);
        $stmt->execute();
        $stmt->close();
        $upload_success = "✅ PDF uploaded successfully!";
    } else {
        $upload_error = "❌ Failed to upload PDF!";
    }
}


// Fetch uploaded PDFs
$search_query = "";
if (isset($_GET["search"])) {
    $search_query = trim($_GET["search"]);
    $stmt = $conn->prepare("SELECT * FROM pdf_documents WHERE filename LIKE ? OR extracted_text LIKE ? OR content LIKE ?");
    $like_search = "%$search_query%";
    $stmt->bind_param("sss", $like_search, $like_search, $like_search);
} else {
    $stmt = $conn->prepare("SELECT * FROM pdf_documents ORDER BY uploaded_at DESC");
}

//file search

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | PDF Parser</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 30px;
        }

        .pdf-list {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2>📄 PDF Parser Dashboard</h2>
            <div>
                <span class="badge bg-primary">Welcome, <?= htmlspecialchars($username) ?> | Role: <?= htmlspecialchars($role) ?></span>
                <?php if ($_SESSION["role"] === "admin"): ?>
                    <a href="manage_users.php" class="btn btn-warning">👥 Manage Users</a>
                <?php endif; ?>

                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
        <hr>

        <!-- Upload Section -->
        <div class="card">
            <div class="card-header">📤 Upload PDF</div>
            <div class="card-body">
                <?php if (isset($upload_success)) echo "<div class='alert alert-success'>$upload_success</div>"; ?>
                <?php if (isset($upload_error)) echo "<div class='alert alert-danger'>$upload_error</div>"; ?>
                <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="pdf_file" accept=".pdf" class="form-control mb-2" required>
                    <button type="submit" class="btn btn-success">Upload PDF</button>
                </form>
            </div>
        </div>

        <!-- Search Section -->
        <div class="mt-4">
            <form action="dashboard.php" method="GET" class="d-flex">
                <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="🔍 Search PDFs..." class="form-control">
                <button type="submit" class="btn btn-primary ms-2">Search</button>
            </form>
        </div>

        <!-- PDF List -->
        <div class="card mt-3">
            <div class="card-header">📋 Uploaded PDFs</div>
            <div class="card-body pdf-list">
                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Uploaded By</th>
                                <th>Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row["filename"]) ?></td>
                                    <td><?=$row["uploaded_by"] ?></td>
                                    <td><?= $row["uploaded_at"] ?></td>
                                    <td>
                                        <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">📄 View</a>
                                        <a href="download.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">📥 Download</a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this PDF?');">🗑️ Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>

                    </table>
                <?php else: ?>
                    <p class="text-muted">No PDFs found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>

<?php $stmt->close();
$conn->close(); ?>