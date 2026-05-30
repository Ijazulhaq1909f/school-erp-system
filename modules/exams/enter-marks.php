<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$exam_name = $_GET['exam'] ?? '';
$class = $_GET['class'] ?? '';
$section = $_GET['section'] ?? '';

$msg = '';

// Save marks
if (isset($_POST['save_marks'])) {
    foreach ($_POST['marks'] as $student_id => $subjects) {
        foreach ($subjects as $exam_id => $marks_obt) {
            $check = $conn->query("SELECT id FROM results WHERE student_id=$student_id AND exam_id=$exam_id");
            if ($check->num_rows > 0) {
                $conn->query("UPDATE results SET marks_obtained=$marks_obt WHERE student_id=$student_id AND exam_id=$exam_id");
            } else {
                $conn->query("INSERT INTO results (student_id, exam_id, subject_id, marks_obtained) VALUES ($student_id, $exam_id, (SELECT subject_id FROM exams WHERE id=$exam_id), $marks_obt)");
            }
        }
    }
    $msg = "<div style='background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:15px;'>✅ Marks saved!</div>";
}

// Get students
$students = $conn->query("SELECT * FROM students WHERE class='$class' AND section='$section' AND status=1 ORDER BY first_name");

// Get exam subjects
$subjects = $conn->query("SELECT e.id as exam_id, s.subject_name, e.max_marks, s.id as subject_id 
                         FROM exams e JOIN subjects s ON e.subject_id=s.id 
                         WHERE e.exam_name='$exam_name' AND e.class_name='$class' AND e.section='$section' 
                         ORDER BY s.subject_name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Enter Marks - <?php echo $exam_name; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .header { background: #1a237e; color: white; padding: 15px; border-radius: 10px; margin-bottom: 15px; }
        .header a { color: white; text-decoration: none; }
        .card { background: white; border-radius: 10px; padding: 20px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; min-width: 600px; }
        th { background: #1a237e; color: white; padding: 8px; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #eee; text-align: center; }
        input[type=number] { width: 60px; padding: 5px; text-align: center; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 12px 30px; background: #27ae60; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; margin-top: 15px; }
    </style>
</head>
<body>

<div class="header">
    <h2>📝 Enter Marks: <?php echo $exam_name; ?> (<?php echo $class.'-'.$section; ?>)</h2>
    <a href="./">← Back</a>
</div>

<?php echo $msg; ?>

<form method="POST">
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <?php 
                    $sub_list = [];
                    while($sub = $subjects->fetch_assoc()) { 
                        $sub_list[] = $sub;
                        echo '<th>'.$sub['subject_name'].'<br><small>('.$sub['max_marks'].')</small></th>';
                    } 
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php while($s = $students->fetch_assoc()): ?>
                    <tr>
                        <td style="text-align:left;"><strong><?php echo $s['first_name'].' '.$s['last_name']; ?></strong></td>
                        <?php foreach($sub_list as $sub): 
                            $existing = $conn->query("SELECT marks_obtained FROM results WHERE student_id={$s['id']} AND exam_id={$sub['exam_id']}")->fetch_assoc();
                            $val = $existing['marks_obtained'] ?? '';
                        ?>
                            <td>
                                <input type="number" name="marks[<?php echo $s['id']; ?>][<?php echo $sub['exam_id']; ?>]" 
                                       value="<?php echo $val; ?>" max="<?php echo $sub['max_marks']; ?>" min="0" step="0.5">
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <?php if($students->num_rows > 0): ?>
        <button type="submit" name="save_marks">💾 Save All Marks</button>
    <?php endif; ?>
</form>

</body>
</html>