<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$date = $_GET['date'] ?? date('Y-m-d');
$class = $_GET['class'] ?? '';
$msg = '';

// Save attendance
if (isset($_POST['save_att'])) {
    foreach ($_POST['status'] as $sid => $status) {
        $check = $conn->query("SELECT id FROM attendance WHERE student_id=$sid AND date='$date'");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE attendance SET status='$status' WHERE student_id=$sid AND date='$date'");
        } else {
            $conn->query("INSERT INTO attendance (student_id, date, status) VALUES ($sid, '$date', '$status')");
        }
    }
    $msg = "<p style='color:green;'>✅ Attendance saved!</p>";
}

$where = $class ? "AND class='$class'" : "";
$students = $conn->query("SELECT * FROM students WHERE status=1 $where ORDER BY class, first_name");

// Stats
$total = $students->num_rows;
$present = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE date='$date' AND status='Present'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .header { background: #1a237e; color: white; padding: 15px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; }
        .header a { color: white; text-decoration: none; padding: 6px 12px; border-radius: 5px; }
        .filters { background: white; padding: 15px; border-radius: 10px; margin-bottom: 15px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .filters input, .filters select { padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .stats { display: flex; gap: 15px; margin-bottom: 15px; }
        .stat { background: white; padding: 15px; border-radius: 8px; text-align: center; flex: 1; }
        .stat h3 { margin: 0; font-size: 20px; }
        .stat small { color: #888; }
        .card { background: white; border-radius: 10px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #1a237e; color: white; padding: 8px; }
        td { padding: 8px; border-bottom: 1px solid #eee; text-align: center; }
        select.status { padding: 4px; border-radius: 4px; }
        button { padding: 10px 25px; background: #27ae60; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; }
.btn-primary { background: #1a237e; color: white; }
        @media (max-width: 600px) { table { font-size: 10px; } th, td { padding: 5px; } }
    </style>
</head>
<body>

<div class="header">
    <h2>📋 Attendance</h2>
    <a href="../../dashboard/">🏠 Home</a>
</div>

<?php echo $msg; ?>

<div class="stats">
    <div class="stat"><h3 style="color:#1a237e;"><?php echo $total; ?></h3><small>Total</small></div>
    <div class="stat"><h3 style="color:#27ae60;"><?php echo $present; ?></h3><small>Present</small></div>
    <div class="stat"><h3 style="color:#e74c3c;"><?php echo $total - $present; ?></h3><small>Absent</small></div>
</div>

<div class="filters">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;">
        <input type="date" name="date" value="<?php echo $date; ?>" onchange="this.form.submit()">
        <select name="class" onchange="this.form.submit()">
            <option value="">All Classes</option>
            <?php foreach(['Nursery','KGI','KGII','1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th'] as $c): ?>
                <option <?php echo $class==$c?'selected':''; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<form method="POST">
    <input type="hidden" name="date" value="<?php echo $date; ?>">
    <div class="card">
        <table>
            <tr><th>Student</th><th>Class</th><th>Status</th></tr>
            <?php while($s = $students->fetch_assoc()): 
                $att = $conn->query("SELECT status FROM attendance WHERE student_id={$s['id']} AND date='$date'")->fetch_assoc();
                $current = $att['status'] ?? 'Present';
            ?>
                <tr>
                    <td><?php echo $s['first_name'].' '.$s['last_name']; ?></td>
                    <td><?php echo $s['class']; ?></td>
                    <td>
                        <select name="status[<?php echo $s['id']; ?>]" class="status" style="background:<?php echo $current=='Present'?'#d4edda':($current=='Absent'?'#f8d7da':'#fff3cd'); ?>">
                            <option value="Present" <?php echo $current=='Present'?'selected':''; ?>>✅ Present</option>
                            <option value="Absent" <?php echo $current=='Absent'?'selected':''; ?>>❌ Absent</option>
                            <option value="Late" <?php echo $current=='Late'?'selected':''; ?>>⏰ Late</option>
                        </select>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <br>
    <button type="submit" name="save_att">💾 Save Attendance</button>
</form>


<?php
// Monthly Report Section
$month = $_GET['month'] ?? date('Y-m');
$student_id = $_GET['student_id'] ?? '';

if (isset($_GET['view']) && $_GET['view'] == 'report') {
    $year = substr($month, 0, 4);
    $mon = substr($month, 5, 2);
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $mon, $year);
    
    $where = $student_id ? "WHERE id=$student_id" : "WHERE status=1";
    $students_list = $conn->query("SELECT * FROM students $where ORDER BY class, first_name");
    
    echo '<div class="card" style="margin-top:20px;">';
    echo '<h3 style="padding:15px;">📊 Monthly Attendance Report - ' . date('F Y', strtotime($month)) . '</h3>';
    echo '<div style="overflow-x:auto;"><table style="font-size:10px;">';
    echo '<tr><th>Student</th><th>Class</th>';
    for($d=1; $d<=$days_in_month; $d++) echo "<th>$d</th>";
    echo '<th>P</th><th>A</th><th>L</th><th>%</th></tr>';
    
    while($s = $students_list->fetch_assoc()) {
        $p = $a = $l = 0;
        echo '<tr><td style="white-space:nowrap;">'.$s['first_name'].'</td><td>'.$s['class'].'</td>';
        
        for($d=1; $d<=$days_in_month; $d++) {
            $date = sprintf('%s-%02d-%02d', $year, $mon, $d);
            $att = $conn->query("SELECT status FROM attendance WHERE student_id={$s['id']} AND date='$date'")->fetch_assoc();
            $status = $att['status'] ?? '';
            
            if($status == 'Present') { $p++; $cls='background:#d4edda;'; $sym='P'; }
            elseif($status == 'Absent') { $a++; $cls='background:#f8d7da;'; $sym='A'; }
            elseif($status == 'Late') { $l++; $cls='background:#fff3cd;'; $sym='L'; }
            else { $cls=''; $sym='-'; }
            
            echo "<td style='$cls padding:2px; text-align:center;'>$sym</td>";
        }
        
        $total = $p + $a + $l;
        $per = $total > 0 ? round(($p / $total) * 100) : 0;
        echo "<td style='color:green;font-weight:bold;'>$p</td>";
        echo "<td style='color:red;font-weight:bold;'>$a</td>";
        echo "<td style='color:orange;font-weight:bold;'>$l</td>";
        echo "<td><strong>$per%</strong></td></tr>";
    }
    echo '</table></div></div>';
}
?>

<!-- Report Filters -->
<div class="card" style="margin-top:15px;">
    <h3 style="padding:15px;">📊 View Attendance Report</h3>
    <form method="GET" style="padding:15px;display:flex;gap:10px;flex-wrap:wrap;">
        <input type="hidden" name="view" value="report">
        <input type="month" name="month" value="<?php echo $month; ?>">
        <select name="student_id">
            <option value="">All Students</option>
            <?php 
            $all_students = $conn->query("SELECT id, first_name, last_name, class FROM students WHERE status=1 ORDER BY first_name");
            while($as = $all_students->fetch_assoc()):
                $sel = $student_id == $as['id'] ? 'selected' : '';
                echo "<option value='{$as['id']}' $sel>{$as['first_name']} ({$as['class']})</option>";
            endwhile;
            ?>
        </select>
        <button type="submit" class="btn btn-primary" style="padding:8px 20px;background:#1a237e;color:white;border:none;border-radius:5px;cursor:pointer;">🔍 View Report</button>
    </form>
</div>

</body>
</html>