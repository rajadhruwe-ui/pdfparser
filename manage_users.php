<?php
session_start();
include "config.php";

// Check if the user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT id, username, role FROM users ORDER BY id ASC");

// Handle delete request
if (isset($_GET["delete"])) {
    $user_id = intval($_GET["delete"]);
    if ($user_id != $_SESSION["user_id"]) { // Prevent self-deletion
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        header("Location: manage_users.php?msg=User deleted successfully");
        exit();
    } else {
        header("Location: manage_users.php?error=You cannot delete yourself.");
        exit();
    }
}

// Handle role update request
if (isset($_POST["update_role"])) {
    $user_id = intval($_POST["user_id"]);
    $new_role = $_POST["new_role"];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    header("Location: manage_users.php?msg=User role updated successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | PDF Parser</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>👥 Manage Users</h2>
        <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        <hr>

        <?php if (isset($_GET["msg"])) echo "<div class='alert alert-success'>{$_GET['msg']}</div>"; ?>
        <?php if (isset($_GET["error"])) echo "<div class='alert alert-danger'>{$_GET['error']}</div>"; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["id"] ?></td>
                        <td><?= htmlspecialchars($row["username"]) ?></td>
                        <td>
                            <form action="manage_users.php" method="POST" class="d-inline">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <select name="new_role" class="form-select d-inline w-auto">
                                    <option value="user" <?= ($row["role"] == "user") ? "selected" : "" ?>>User</option>
                                    <option value="admin" <?= ($row["role"] == "admin") ? "selected" : "" ?>>Admin</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-primary btn-sm">🔄 Update</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($row["id"] != $_SESSION["user_id"]): // Prevent self-delete ?>
                                <a href="manage_users.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">🗑️ Delete</a>
                            <?php else: ?>
                                <span class="text-muted">Cannot delete self</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
