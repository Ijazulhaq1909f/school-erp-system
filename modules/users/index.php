<?php
require_once '../../shared/config.php';
if (!isLoggedIn() || !isAdmin()) redirect(BASE_URL . 'login.php');

$msg = '';

// Add user
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $related_id = $_POST['related_id'] ?: 'NULL';
    
    $check = $conn->query("SELECT id FROM users WHERE username='$username'");
    if ($check->num_rows > 0) {
        $msg = "<p style='color:red;'>Username already exists!</p>";
    } else {
        $conn->query("INSERT INTO users (username, password, role, related_id) VALUES ('$username', '$password', '$role', $related_id)");
        $msg = "<p style='color:green;'>User added!</p>";
    }
}

$users = $conn->query("SELECT u.*, 
    CASE 
        WHEN u.role='teacher' THEN (SELECT CONCAT(first_name,' ',last_name) FROM teachers WHERE id=u.related_id)
        WHEN u.role='parent' THEN (SELECT CONCAT(first_name,' ',last_name) FROM students WHERE id=u.related_id)
        ELSE '-'
    END as related_name
    FROM users u ORDER BY u.id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { background: #1a237e; color: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: white; text-decoration: none; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        h3 { color: #1a237e; margin-top: 0; }
        input, select { padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; width: 100%; margin-bottom: 10px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        button { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1a237e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .badge-admin { background: #f8d7da; color: #721c24; }
        .badge-teacher { background: #d1ecf1; color: #0c5460; }
        .badge-parent { background: #d4edda; color: #155724; }
        @media (max-width: 600px) { .row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">

<div class="header">
    <h2>👥 User Management</h2>
    <a href="../../dashboard/">🏠 Dashboard</a>
</div>

<?php echo $msg; ?>

<div class="card">
    <h3>➕ Add New User</h3>
    <form method="POST">
        <div class="row">
            <input type="text" name="username" placeholder="Username *" required>
            <input type="password" name="password" placeholder="Password *" required>
        </div>
        <div class="row">
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
                <option value="parent">Parent</option>
            </select>
            <input type="number" name="related_id" placeholder="Related ID (Teacher/Student ID)">
        </div>
        <button type="submit" name="add_user">➕ Add User</button>
    </form>
</div>

<div class="card">
    <h3>📋 All Users</h3>
    <table>
        <tr><th>Username</th><th>Role</th><th>Related To</th><th>Status</th></tr>
        <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td><strong><?php echo $u['username']; ?></strong></td>
                <td><span class="badge badge-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span></td>
                <td><?php echo $u['related_name'] ?? '-'; ?></td>
                <td><?php echo $u['status'] ? '✅ Active' : '❌ Inactive'; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>


</div>
</body>
</html>