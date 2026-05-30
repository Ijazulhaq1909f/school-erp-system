<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

// For demo: show first student's data
$student = $conn->query("SELECT * FROM students WHERE status=1 LIMIT 1")->fetch_assoc();

if ($student) {
    $sid = $student['id'];
    $att = $conn->query("SELECT COUNT(*) as t, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as p FROM attendance WHERE student_id=$sid")->fetch_assoc();
    $att_percent = $att['t'] > 0 ? round(($att['p']/$att['t'])*100) : 0;
    $fee = $conn->query("SELECT SUM(amount) as t, SUM(paid_amount) as p FROM fees WHERE student_id=$sid")->fetch_assoc();
    $fee_due = ($fee['t'] ?? 0) - ($fee['p'] ?? 0);
    $result = $conn->query("SELECT AVG((marks_obtained/max_marks)*100) as avg FROM results r JOIN exams e ON r.exam_id=e.id WHERE r.student_id=$sid")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parent Portal - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: #1a237e; color: white; padding: 20px; border-radius: 15px; text-align: center; margin-bottom: 20px; }
        .header img { width: 80px; height: 80px; border-radius: 50%; border: 3px solid white; object-fit: cover; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h3 { margin-top: 0; color: #1a237e; }
        .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .label { color: #888; }
        .value { font-weight: bold; }
        .badge { padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .btn { display: block; padding: 12px; background: #1a237e; color: white; text-align: center; border-radius: 8px; text-decoration: none; margin: 10px 0; }
    </style>
</head>
<body>
<div class="container">

<div class="header">
    <img src="<?php echo getPhotoUrl($student['photo']); ?>" onerror="this.src='<?php echo BASE_URL; ?>assets/uploads/photos/default.png'">
    <h2><?php echo $student['first_name'].' '.$student['last_name']; ?></h2>
    <p><?php echo $student['admission_no']; ?> | <?php echo $student['class'].'-'.$student['section']; ?></p>
</div>

<div class="card">
    <h3>📋 Attendance</h3>
    <div class="row"><span class="label">Total Days</span><span class="value"><?php echo $att['t']; ?></span></div>
    <div class="row"><span class="label">Present</span><span class="value"><?php echo $att['p']; ?></span></div>
    <div class="row"><span class="label">Percentage</span><span class="value" style="color:<?php echo $att_percent>75?'green':'red'; ?>;"><?php echo $att_percent; ?>%</span></div>
</div>

<div class="card">
    <h3>💰 Fee Status</h3>
    <div class="row"><span class="label">Total Fee</span><span class="value">Rs.<?php echo number_format($fee['t']??0); ?></span></div>
    <div class="row"><span class="label">Paid</span><span class="value" style="color:green;">Rs.<?php echo number_format($fee['p']??0); ?></span></div>
    <div class="row"><span class="label">Due</span><span class="value" style="color:<?php echo $fee_due>0?'red':'green'; ?>;">Rs.<?php echo number_format($fee_due); ?></span></div>
</div>

<div class="card">
    <h3>📊 Academic Result</h3>
    <div class="row"><span class="label">Average</span><span class="value"><?php echo round($result['avg']??0); ?>%</span></div>
</div>

<a href="../../dashboard/" class="btn">🏠 Go to Dashboard</a>
<a href="../../login.php?logout=1" class="btn" style="background:#e74c3c;">🚪 Logout</a>

</div>
</body>
</html>