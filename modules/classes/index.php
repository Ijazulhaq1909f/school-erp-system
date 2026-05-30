<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$classes = $conn->query("SELECT c.*, 
    (SELECT COUNT(*) FROM students WHERE class=c.class_name AND section=c.section AND status=1) as student_count,
    (SELECT CONCAT(first_name,' ',last_name) FROM teachers WHERE id=c.teacher_id) as teacher_name
    FROM classes c ORDER BY 
    CASE 
        WHEN c.class_name='Nursery' THEN 1 WHEN c.class_name='KGI' THEN 2 WHEN c.class_name='KGII' THEN 3
        ELSE CAST(c.class_name AS UNSIGNED) + 3
    END, c.section");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Classes - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .header { background: #1a237e; color: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: white; text-decoration: none; }
        .card { background: white; border-radius: 10px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #1a237e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-yellow { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>

<div class="header">
    <h2>🚪 Class Management</h2>
    <a href="../../dashboard/">🏠 Dashboard</a>
</div>

<div class="card">
    <table>
        <tr><th>Class</th><th>Section</th><th>Room</th><th>Capacity</th><th>Students</th><th>Teacher</th><th>Status</th></tr>
        <?php while($c = $classes->fetch_assoc()): 
            $status = $c['student_count'] >= $c['capacity'] ? 'Full' : 'Available';
            $badge = $status == 'Full' ? 'badge-yellow' : 'badge-green';
        ?>
            <tr>
                <td><strong><?php echo $c['class_name']; ?></strong></td>
                <td><?php echo $c['section']; ?></td>
                <td><?php echo $c['room_no'] ?: '-'; ?></td>
                <td><?php echo $c['capacity']; ?></td>
                <td><?php echo $c['student_count']; ?></td>
                <td><?php echo $c['teacher_name'] ?: '<span style="color:#999;">Not assigned</span>'; ?></td>
                <td><span class="badge <?php echo $badge; ?>"><?php echo $status; ?></span></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>