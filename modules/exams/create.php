<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$msg = '';

if (isset($_POST['create'])) {
    $exam_name = $_POST['exam_name'];
    $exam_type = $_POST['exam_type'];
    $class = $_POST['class'];
    $section = $_POST['section'];
    $exam_date = $_POST['exam_date'];
    
    // Get subjects for this class
    $class_num = intval(preg_replace('/[^0-9]/', '', $class));
    $group = ($class_num >= 1 && $class_num <= 7) ? '1-7' : (($class_num >= 8 && $class_num <= 10) ? '8-10' : '1-7');
    if (in_array($class, ['Nursery', 'KGI', 'KGII'])) $group = 'Nursery,KGI,KGII';
    
    $subjects = $conn->query("SELECT * FROM subjects WHERE class_group LIKE '%$group%'");
    $count = 0;
    
    while ($sub = $subjects->fetch_assoc()) {
        $conn->query("INSERT INTO exams (exam_name, exam_type, class_name, section, subject_id, exam_date, max_marks, passing_marks) 
                     VALUES ('$exam_name', '$exam_type', '$class', '$section', {$sub['id']}, '$exam_date', {$sub['max_marks']}, {$sub['passing_marks']})");
        $count++;
    }
    
    $msg = "<div style='background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin-bottom:15px;'>✅ Exam created with <strong>$count subjects</strong>!</div>";
}

$existing_exams = $conn->query("SELECT DISTINCT exam_name, exam_type, class_name, section, exam_date FROM exams ORDER BY exam_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Exam - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { background: #1a237e; color: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: white; text-decoration: none; }
        .card { background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { color: #1a237e; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px; color: #333; }
        input, select { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px; }
        input:focus, select:focus { outline: none; border-color: #1a237e; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        button { padding: 12px 30px; background: #27ae60; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; width: 100%; font-weight: bold; }
        button:hover { background: #219a52; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #1a237e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .badge-mid { background: #fff3cd; color: #856404; }
        .badge-final { background: #f8d7da; color: #721c24; }
        .badge-test { background: #d1ecf1; color: #0c5460; }
        .info-box { background: #e8eaf6; padding: 15px; border-radius: 8px; margin: 15px 0; font-size: 13px; }
        .info-box strong { color: #1a237e; }
        @media (max-width: 600px) { .row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">

<div class="header">
    <h2>📝 Exam Management</h2>
    <a href="./">← Back to Exams</a>
</div>

<?php echo $msg; ?>

<div class="card">
    <h2>➕ Create New Exam</h2>
    
    <div class="info-box">
        <strong>📋 Note:</strong> Subjects will be automatically loaded based on the selected class curriculum.
    </div>
    
    <form method="POST">
        <div class="row">
            <div class="form-group">
                <label>📝 Exam Name *</label>
                <input type="text" name="exam_name" placeholder="e.g., Mid Term 2025, Final Term 2025" required>
            </div>
            <div class="form-group">
                <label>📂 Exam Type *</label>
                <select name="exam_type" required>
                    <option value="">Select Type</option>
                    <option value="Mid-Term">Mid Term</option>
                    <option value="Final-Term">Final Term</option>
                    <option value="Test">Monthly Test</option>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group">
                <label>🏫 Class *</label>
                <select name="class" required>
                    <option value="">Select Class</option>
                    <option value="Nursery">Nursery</option>
                    <option value="KGI">KGI</option>
                    <option value="KGII">KGII</option>
                    <?php for($i=1; $i<=10; $i++): 
                        $c = $i . ($i==1?'st':($i==2?'nd':($i==3?'rd':'th')));
                    ?>
                        <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>📋 Section *</label>
                <select name="section" required>
                    <option value="">Select Section</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>📅 Exam Date *</label>
            <input type="date" name="exam_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <button type="submit" name="create">📝 Create Exam</button>
    </form>
</div>

<!-- Existing Exams -->
<div class="card">
    <h2>📋 Existing Exams</h2>
    <?php if($existing_exams->num_rows > 0): ?>
        <table>
            <tr><th>Exam Name</th><th>Type</th><th>Class</th><th>Date</th></tr>
            <?php while($e = $existing_exams->fetch_assoc()): 
                $badge = $e['exam_type'] == 'Mid-Term' ? 'badge-mid' : ($e['exam_type'] == 'Final-Term' ? 'badge-final' : 'badge-test');
            ?>
                <tr>
                    <td><strong><?php echo $e['exam_name']; ?></strong></td>
                    <td><span class="badge <?php echo $badge; ?>"><?php echo $e['exam_type']; ?></span></td>
                    <td><?php echo $e['class_name'].' - '.$e['section']; ?></td>
                    <td><?php echo date('d M Y', strtotime($e['exam_date'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center;color:#999;padding:20px;">No exams created yet</p>
    <?php endif; ?>
</div>

</div>
</body>
</html>