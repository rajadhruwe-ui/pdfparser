<?php
include "session.php";
include "config.php";

$result = $conn->query("SELECT * FROM user_activity ORDER BY timestamp DESC");

echo "<h2>📊 User Activity Logs</h2><table border='1'>";
echo "<tr><th>User</th><th>Action</th><th>Time</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['user_id']}</td>
        <td>{$row['action']}</td>
        <td>{$row['timestamp']}</td>
    </tr>";
}
echo "</table>";
?>
