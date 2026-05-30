<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$exams = $conn->query("SELECT DISTINCT exam_name, exam_type, class_name, section, exam_date FROM exams ORDER BY exam_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Exams - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .header { background: #1a237e; color: white; padding: 15px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .header a { color: white; text-decoration: none; padding: 6px 12px; border-radius: 5px; }
        .card { background: white; border-radius: 10px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #1a237e; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .btn { padding: 5px 10px; border-radius: 5px; text-decoration: none; color: white; font-size: 10px; margin: 2px; }
        .btn-marks { background: #27ae60; }
        .btn-result { background: #3498db; }
    </style>
</head>
<body>

<div class="header">
    <h2>📝 Exams</h2>
    <div>
        <a href="../../dashboard/">🏠 Home</a>
        <a href="create.php" style="background:#27ae60;">➕ Create Exam</a>
        <a href="paper-generator.php" style="background:#f39c12;">🤖 Paper Generator</a>
    </div>
</div>

<div class="card">
    <table>
        <tr><th>Exam</th><th>Type</th><th>Class</th><th>Date</th><th>Actions</th></tr>
        <?php while($e = $exams->fetch_assoc()): ?>
            <tr>
                <td><strong><?php echo $e['exam_name']; ?></strong></td>
                <td><?php echo $e['exam_type']; ?></td>
                <td><?php echo $e['class_name'].'-'.$e['section']; ?></td>
                <td><?php echo $e['exam_date']; ?></td>
                <td>
                    <a href="enter-marks.php?exam=<?php echo urlencode($e['exam_name']); ?>&class=<?php echo $e['class_name']; ?>&section=<?php echo $e['section']; ?>" class="btn btn-marks">📝 Marks</a>
                    <a href="results.php?exam=<?php echo urlencode($e['exam_name']); ?>&class=<?php echo $e['class_name']; ?>" class="btn btn-result">📊 Results</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>