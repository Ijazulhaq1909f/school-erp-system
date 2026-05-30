<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$exam_name = $_GET['exam'] ?? '';
$class = $_GET['class'] ?? '';

// Get all students with their attendance stats
$students = $conn->query("SELECT s.*, 
    (SELECT COUNT(*) FROM attendance WHERE student_id=s.id) as total_days,
    (SELECT COUNT(*) FROM attendance WHERE student_id=s.id AND status='Present') as present_days,
    (SELECT COUNT(*) FROM attendance WHERE student_id=s.id AND status='Absent') as absent_days,
    (SELECT COUNT(*) FROM attendance WHERE student_id=s.id AND status='Late') as late_days
    FROM students s WHERE s.class='$class' AND s.status=1 ORDER BY s.first_name");

// Get exam results
$exam_query = $conn->query("SELECT DISTINCT exam_name, exam_date FROM exams WHERE exam_name='$exam_name' AND class_name='$class'");
$exam_info = $exam_query->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Results - <?php echo $class; ?> - <?php echo $exam_name; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .header { background: #1a237e; color: white; padding: 15px; border-radius: 10px; margin-bottom: 15px; }
        .header a { color: white; text-decoration: none; }
        .card { background: white; border-radius: 10px; overflow: hidden; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #1a237e; color: white; padding: 8px 5px; }
        td { padding: 8px 5px; text-align: center; border-bottom: 1px solid #eee; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
        .btn { padding: 6px 12px; border-radius: 5px; text-decoration: none; color: white; font-size: 10px; }
        .btn-print { background: #1a237e; }
        .btn-report { background: #27ae60; }
        @media print { .header, .btn { display: none; } }
    </style>
</head>
<body>

<div class="header">
    <h2>📊 Exam Results: <?php echo $exam_name; ?> - <?php echo $class; ?></h2>
    <div>
        <button onclick="window.print()" style="padding:8px 15px;background:white;color:#1a237e;border:none;border-radius:5px;cursor:pointer;">🖨️ Print</button>
        <a href="./">← Back</a>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Subjects</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Result</th>
                <th colspan="3">📋 Attendance</th>
                <th>Action</th>
            </tr>
            <tr>
                <th></th><th></th><th></th><th></th><th></th><th></th>
                <th>Total</th><th>Present</th><th>%</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php while($s = $students->fetch_assoc()): 
                // Calculate marks
                $marks_query = $conn->query("SELECT SUM(r.marks_obtained) as obt, SUM(e.max_marks) as max 
                    FROM results r JOIN exams e ON r.exam_id=e.id 
                    WHERE r.student_id={$s['id']} AND e.exam_name='$exam_name'");
                $marks = $marks_query->fetch_assoc();
                $obt = $marks['obt'] ?? 0;
                $max = $marks['max'] ?? 0;
                $per = $max > 0 ? round(($obt/$max)*100) : 0;
                
                // Grade
                $grade = $per>=90?'A+':($per>=80?'A':($per>=70?'B':($per>=60?'C':($per>=50?'D':($per>=40?'E':'F')))));
                
                // Attendance
                $total_days = $s['total_days'] ?? 0;
                $present_days = $s['present_days'] ?? 0;
                $att_per = $total_days > 0 ? round(($present_days/$total_days)*100) : 0;
            ?>
                <tr>
                    <td><?php echo $s['admission_no']; ?></td>
                    <td style="text-align:left;"><strong><?php echo $s['first_name'].' '.$s['last_name']; ?></strong></td>
                    <td><?php echo $obt; ?>/<?php echo $max; ?></td>
                    <td><strong><?php echo $per; ?>%</strong></td>
                    <td><strong><?php echo $grade; ?></strong></td>
                    <td class="<?php echo $per>=40?'pass':'fail'; ?>"><?php echo $per>=40?'PASS':'FAIL'; ?></td>
                    <td><?php echo $total_days; ?></td>
                    <td style="color:green;"><?php echo $present_days; ?></td>
                    <td><strong><?php echo $att_per; ?>%</strong></td>
                    <td>
                        <a href="student-report.php?student_id=<?php echo $s['id']; ?>&exam=<?php echo urlencode($exam_name); ?>" class="btn btn-report" target="_blank">📄 Full Report</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>