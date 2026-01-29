<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
    <link rel="stylesheet" href="CSS/adminDashboard.css">
</head>
<body>
    <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <h1>User Management</h1>
            <button style="padding: 8px 16px; background: #d9534f; color: #fff; border: none; border-radius: 4px; cursor: pointer;" onclick="window.location.href='index.php'">Logout</button>
        </div>

        <div class="table-wrapper">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Birth Date</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
             <tbody id="tableBody">
                    <?php
                    require_once 'config.php';
                    $result = $conn->query("SELECT id, last_name, first_name, middle_name, birthdate, age, gender, phone_number, email, role FROM authentication");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $fullName = $row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name'];
                            $phone = '+63' . $row['phone_number'];
                            echo '<tr>';
                            echo '<td>' . $row['id'] . '</td>';
                            echo '<td>' . htmlspecialchars($fullName) . '</td>';
                            echo '<td>' . htmlspecialchars($row['birthdate']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['age']) . '</td>';
                            echo '<td>' . htmlspecialchars(ucfirst($row['gender'])) . '</td>';
                            echo '<td>' . htmlspecialchars($phone) . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>';
                            echo '<form method="post" action="update_role.php" style="display:inline;">';
                            echo '<input type="hidden" name="user_id" value="' . $row['id'] . '">';
                            echo '<select name="role" onchange="this.form.submit()">';
                            echo '<option value="user"' . ($row['role'] === 'user' ? ' selected' : '') . '>User</option>';
                            echo '<option value="admin"' . ($row['role'] === 'admin' ? ' selected' : '') . '>Admin</option>';
                            echo '</select>';
                            echo '</form>';
                            echo '</td>';
                            echo '<td>';
                            echo '<form method="post" action="delete_user.php" onsubmit="return confirm(\'Are you sure you want to delete this user?\');" style="display:inline;">';
                            echo '<input type="hidden" name="user_id" value="' . $row['id'] . '">';
                            echo '<button type="submit" class="btn btn-delete">Delete</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="9">No users found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

   
</body>
</html>