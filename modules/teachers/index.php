<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$teachers = $conn->query("SELECT * FROM teachers WHERE status=1 ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teachers - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; margin: 0; padding: 20px; }
        .header { background: #1a237e; color: white; padding: 15px 20px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; }
        .card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #1a237e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f8f9fa; }
        .photo-thumb { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; }
        .btn { padding: 4px 10px; border-radius: 5px; text-decoration: none; color: white; font-size: 10px; margin: 1px; }
        .btn-view { background: #3498db; }
        .btn-edit { background: #f39c12; }
        .btn-del { background: #e74c3c; }
    </style>
</head>
<body>

<div class="header">
    <h2>👩‍🏫 Teachers</h2>
    <div>
        <a href="../../dashboard/">🏠 Home</a>
        <a href="add.php" style="background:#27ae60;">➕ Add Teacher</a>
    </div>
</div>

<div class="card">
    <table>
        <tr><th>Photo</th><th>Emp ID</th><th>Name</th><th>Subject</th><th>Phone</th><th>Salary</th><th>Actions</th></tr>
        <?php while($t = $teachers->fetch_assoc()): ?>
            <tr>
                <td><img src="<?php echo getPhotoUrl($t['photo']); ?>" class="photo-thumb"></td>
                <td><?php echo $t['employee_id']; ?></td>
                <td><strong><?php echo $t['first_name'].' '.$t['last_name']; ?></strong></td>
                <td><?php echo $t['subject_specialty']; ?></td>
                <td><?php echo $t['phone']; ?></td>
                <td>Rs.<?php echo number_format($t['salary']); ?></td>
                <td>
                    <a href="view.php?id=<?php echo $t['id']; ?>" class="btn btn-view">👁️</a>
                    <a href="edit.php?id=<?php echo $t['id']; ?>" class="btn btn-edit">✏️</a>
                    <a href="delete.php?id=<?php echo $t['id']; ?>" class="btn btn-del" onclick="return confirm('Delete?')">🗑️</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>