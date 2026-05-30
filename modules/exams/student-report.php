<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$student_id = $_GET['student_id'] ?? 0;
$exam_name = $_GET['exam'] ?? '';

$s = $conn->query("SELECT * FROM students WHERE id=$student_id")->fetch_assoc();
if(!$s) exit('Student not found');

// Marks
$marks_query = $conn->query("SELECT s.subject_name, e.max_marks, r.marks_obtained, e.passing_marks
    FROM results r JOIN exams e ON r.exam_id=e.id JOIN subjects s ON r.subject_id=s.id 
    WHERE r.student_id=$student_id AND e.exam_name='$exam_name'");
$total_obt = 0; $total_max = 0;
$subjects = [];
while($m = $marks_query->fetch_assoc()) {
    $subjects[] = $m;
    $total_obt += $m['marks_obtained'];
    $total_max += $m['max_marks'];
}
$per = $total_max > 0 ? round(($total_obt/$total_max)*100) : 0;
$grade = $per>=90?'A+':($per>=80?'A':($per>=70?'B':($per>=60?'C':($per>=50?'D':($per>=40?'E':'F')))));

// Attendance
$att_query = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN status='Absent' THEN 1 ELSE 0 END) as absent,
    SUM(CASE WHEN status='Late' THEN 1 ELSE 0 END) as late
    FROM attendance WHERE student_id=$student_id");
$att = $att_query->fetch_assoc();
$att_per = $att['total'] > 0 ? round(($att['present']/$att['total'])*100) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Report Card - <?php echo $s['first_name']; ?></title>
    <style>
        body { font-family: 'Times New Roman', serif; background: #e0e0e0; padding: 20px; }
        .report { max-width: 700px; margin: 0 auto; background: white; padding: 20px; border: 2px solid #000; }
        .header { text-align: center; border-bottom: 2px solid #1a237e; padding-bottom: 10px; }
        .header img { width: 60px; height: 60px; border-radius: 50%; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #1a237e; color: white; padding: 8px; font-size: 11px; }
        td { padding: 6px; border: 1px solid #ddd; text-align: center; font-size: 12px; }
        .total { font-weight: bold; background: #e8eaf6; }
        .result-box { display: inline-block; padding: 10px 30px; border: 2px solid #1a237e; margin: 10px; text-align: center; }
        .grade { font-size: 36px; font-weight: bold; color: #c62828; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 10px 20px; background: #1a237e; color: white; border: none; cursor: pointer; }
        @media print { body { background: white; padding: 0; } .report { border: 1px solid #ccc; } .print-btn { display: none; } }
    </style>
</head>
<body>
<button class="print-btn" onclick="window.print()">🖨️ Print</button>

<div class="report">
    <div class="header">
        <?php if(hasLogo()): ?><img src="<?php echo getLogoUrl(); ?>"><?php endif; ?>
        <h2><?php echo SCHOOL_NAME; ?></h2>
        <p><?php echo SCHOOL_ADDRESS; ?> | <?php echo SCHOOL_PHONE; ?></p>
        <h3>PROGRESS REPORT CARD</h3>
        <p>Session: <?php echo date('Y'); ?>-<?php echo date('Y')+1; ?></p>
    </div>
    
    <table>
        <tr><td><strong>Name:</strong> <?php echo $s['first_name'].' '.$s['last_name']; ?></td><td><strong>Roll No:</strong> <?php echo $s['admission_no']; ?></td></tr>
        <tr><td><strong>Class:</strong> <?php echo $s['class'].' - '.$s['section']; ?></td><td><strong>Exam:</strong> <?php echo $exam_name; ?></td></tr>
    </table>
    
    <h4>📝 Subject Marks</h4>
    <table>
        <tr><th>Subject</th><th>Max Marks</th><th>Obtained</th><th>Status</th></tr>
        <?php foreach($subjects as $sub): 
            $pass = $sub['marks_obtained'] >= $sub['passing_marks'];
        ?>
            <tr>
                <td><?php echo $sub['subject_name']; ?></td>
                <td><?php echo $sub['max_marks']; ?></td>
                <td><?php echo $sub['marks_obtained']; ?></td>
                <td style="color:<?php echo $pass?'green':'red'; ?>;"><?php echo $pass?'PASS':'FAIL'; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="total"><td>Total</td><td><?php echo $total_max; ?></td><td><?php echo $total_obt; ?></td><td><?php echo $per; ?>%</td></tr>
    </table>
    
    <h4>📋 Attendance Record</h4>
    <table>
        <tr><th>Total Days</th><th>Present</th><th>Absent</th><th>Late</th><th>Percentage</th></tr>
        <tr>
            <td><?php echo $att['total']; ?></td>
            <td style="color:green;"><?php echo $att['present']; ?></td>
            <td style="color:red;"><?php echo $att['absent']; ?></td>
            <td style="color:orange;"><?php echo $att['late']; ?></td>
            <td><strong><?php echo $att_per; ?>%</strong></td>
        </tr>
    </table>
    
    <div style="display:flex; justify-content:space-around; margin-top:20px;">
        <div class="result-box">
            <small>Percentage</small><br>
            <strong><?php echo $per; ?>%</strong>
        </div>
        <div class="result-box">
            <small>Attendance</small><br>
            <strong><?php echo $att_per; ?>%</strong>
        </div>
        <div class="result-box">
            <small>GRADE</small><br>
            <div class="grade"><?php echo $grade; ?></div>
        </div>
    </div>
    
    <p style="text-align:center; color:<?php echo $per>=40?'green':'red'; ?>; font-size:16px; font-weight:bold; margin-top:15px;">
        <?php echo $per>=40 && $att_per>=75 ? '✅ PASSED' : '❌ FAILED'; ?>
    </p>
    
    <div style="display:flex; justify-content:space-between; margin-top:40px;">
        <div style="text-align:center;"><hr width="100"><small>Class Teacher</small></div>
        <div style="text-align:center;"><hr width="100"><small>Principal<br><?php echo PRINCIPAL_NAME; ?></small></div>
        <div style="text-align:center;"><hr width="100"><small>Parent</small></div>
    </div>
</div>
</body>
</html>