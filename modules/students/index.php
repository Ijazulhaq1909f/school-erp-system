<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$search = $_GET['search'] ?? '';
$class = $_GET['class'] ?? '';

$where = "WHERE status=1";
if ($search) $where .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR admission_no LIKE '%$search%')";
if ($class) $where .= " AND class='$class'";

$students = $conn->query("SELECT * FROM students $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Students - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; margin: 0; padding: 20px; }
        .header { background: #1a237e; color: white; padding: 15px 20px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .header a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; }
        .btn-primary { background: #1a237e; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-sm { padding: 4px 8px; font-size: 10px; }
        .filters { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        .filters input, .filters select { padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #1a237e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f8f9fa; }
        .photo-thumb { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; }
        .actions { display: flex; gap: 5px; }
        @media (max-width: 768px) { table { font-size: 10px; } th, td { padding: 6px; } }
    </style>
</head>
<body>

<div class="header">
    <h2>👨‍🎓 Students</h2>
    <div>
        <a href="../../dashboard/">🏠 Home</a>
        <a href="add.php" style="background:#27ae60;">➕ Add Student</a>
    </div>
</div>
<?php
// Class-wise student count
$class_counts = $conn->query("SELECT class, COUNT(*) as count FROM students WHERE status=1 GROUP BY class ORDER BY 
    CASE 
        WHEN class='Nursery' THEN 1 WHEN class='KGI' THEN 2 WHEN class='KGII' THEN 3
        ELSE CAST(class AS UNSIGNED) + 3
    END");
?>

<!-- Class Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(100px,1fr));gap:10px;margin-bottom:20px;">
    <?php while($cc = $class_counts->fetch_assoc()): ?>
        <a href="?class=<?php echo $cc['class']; ?>" style="background:white;padding:12px;text-align:center;border-radius:8px;text-decoration:none;color:#2c3e50;box-shadow:0 2px 5px rgba(0,0,0,0.05);">
            <strong style="font-size:20px;display:block;"><?php echo $cc['count']; ?></strong>
            <small style="color:#888;"><?php echo $cc['class']; ?></small>
        </a>
    <?php endwhile; ?>
</div>

<div class="filters">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;">
        <input type="text" name="search" placeholder="Search..." value="<?php echo $search; ?>">
        <select name="class">
            <option value="">All Classes</option>
            <?php foreach(['Nursery','KGI','KGII','1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th'] as $c): ?>
                <option <?php echo $class==$c?'selected':''; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">🔍 Search</button>
    </form>
</div>

<div class="card">
    <table>
        <tr>
            <th>Photo</th><th>Adm No</th><th>Name</th><th>Class</th><th>Father</th><th>Phone</th><th>Actions</th>
        </tr>
        <?php if($students->num_rows > 0): ?>
            <?php while($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo getPhotoUrl($s['photo']); ?>" class="photo-thumb" onerror="this.style.display='none'"></td>
                    <td><strong><?php echo $s['admission_no']; ?></strong></td>
                    <td><?php echo $s['first_name'].' '.$s['last_name']; ?></td>
                    <td><?php echo $s['class'].'-'.$s['section']; ?></td>
                    <td><?php echo $s['father_name']; ?></td>
                    <td><?php echo $s['phone']; ?></td>
                    <td class="actions">
                        <a href="view.php?id=<?php echo $s['id']; ?>" class="btn btn-primary btn-sm">👁️</a>
                        <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn btn-warning btn-sm">✏️</a>
                        <a href="delete.php?id=<?php echo $s['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">🗑️</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" style="text-align:center;padding:30px;">No students found</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>