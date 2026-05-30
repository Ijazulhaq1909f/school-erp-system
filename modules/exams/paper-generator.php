<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$msg = '';
$paper = null;

if (isset($_POST['generate'])) {
    $class = $_POST['class'];
    $subject = $_POST['subject'];
    $chapters = $_POST['chapters'] ?? [];
    
    if (count($chapters) > 0) {
        $paper = [];
        $types = ['MCQ', 'FillBlank', 'TrueFalse', 'Short', 'Long'];
        $limits = [10, 5, 5, 4, 2]; // Questions per type
        
        foreach ($types as $i => $type) {
            $ch_list = "'" . implode("','", $chapters) . "'";
            $q = $conn->query("SELECT * FROM question_bank WHERE class='$class' AND subject='$subject' AND chapter IN ($ch_list) AND type='$type' ORDER BY RAND() LIMIT {$limits[$i]}");
            while ($row = $q->fetch_assoc()) $paper[$type][] = $row;
        }
    } else {
        $msg = "<p style='color:red;'>Please select at least one chapter!</p>";
    }
}

// Get unique chapters for this class/subject
$chapters_query = $conn->query("SELECT DISTINCT chapter FROM question_bank ORDER BY chapter");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Paper Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { background: #1a237e; color: white; padding: 15px; border-radius: 10px; margin-bottom: 15px; }
        .header a { color: white; text-decoration: none; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; }
        .form-group { margin-bottom: 12px; }
        label { font-weight: bold; font-size: 13px; display: block; margin-bottom: 4px; }
        select, input { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; }
        .chapters { display: grid; grid-template-columns: 1fr 1fr; gap: 5px; margin: 10px 0; }
        .chapters label { font-weight: normal; display: flex; align-items: center; gap: 5px; }
        button { padding: 12px 30px; background: #f39c12; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; width: 100%; }
        .paper { background: white; padding: 30px; border-radius: 10px; border: 2px solid #000; font-family: 'Times New Roman'; }
        .paper h2 { text-align: center; }
        .section { margin: 15px 0; }
        .section h3 { border-bottom: 1px dashed #999; }
        .question { margin: 8px 0; }
        .options { margin-left: 20px; font-size: 13px; }
        .print-btn { position: fixed; top: 10px; right: 10px; background: #1a237e; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        @media print { body { background: white; } .header, .card, .print-btn { display: none; } .paper { border: none; } }
    </style>
</head>
<body>
<div class="container">

<div class="header">
    <h2>🤖 Auto Paper Generator</h2>
    <a href="./">← Back to Exams</a>
</div>

<?php echo $msg; ?>

<div class="card">
    <h3>Generate Question Paper</h3>
    <form method="POST">
        <div class="form-group">
            <label>Class *</label>
            <select name="class" required>
                <option value="">Select</option>
                <?php foreach(['1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th'] as $c): ?>
                    <option><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Subject *</label>
            <select name="subject" required>
                <option value="">Select</option>
                <option>Mathematics</option><option>English</option><option>Urdu</option>
                <option>Science</option><option>Physics</option><option>Chemistry</option><option>Biology</option>
            </select>
        </div>
        <div class="form-group">
            <label>Select Chapters:</label>
            <div class="chapters">
                <?php while($ch = $chapters_query->fetch_assoc()): ?>
                    <label><input type="checkbox" name="chapters[]" value="<?php echo $ch['chapter']; ?>"> <?php echo $ch['chapter']; ?></label>
                <?php endwhile; ?>
            </div>
        </div>
        <button type="submit" name="generate">🤖 Generate Paper</button>
    </form>
</div>

<?php if($paper): ?>
    <button class="print-btn" onclick="window.print()">🖨️ Print Paper</button>
    
    <div class="paper">
        <h2><?php echo SCHOOL_NAME; ?></h2>
        <p style="text-align:center;">Subject: <?php echo $_POST['subject']; ?> | Class: <?php echo $_POST['class']; ?> | Marks: 50</p>
        <p style="text-align:center;">Name: ___________ | Roll: ___________ | Date: ___________</p>
        <hr>
        
        <?php $qno = 1; ?>
        <?php if(isset($paper['MCQ']) && count($paper['MCQ']) > 0): ?>
            <div class="section">
                <h3>Q<?php echo $qno++; ?>. MCQs (<?php echo count($paper['MCQ']); ?> Marks)</h3>
                <?php foreach($paper['MCQ'] as $i => $q): ?>
                    <div class="question">
                        <strong><?php echo $i+1; ?>.</strong> <?php echo $q['question']; ?>
                        <div class="options">
                            a) <?php echo $q['option_a']; ?> &nbsp; b) <?php echo $q['option_b']; ?>
                            &nbsp; c) <?php echo $q['option_c']; ?> &nbsp; d) <?php echo $q['option_d']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($paper['FillBlank']) && count($paper['FillBlank']) > 0): ?>
            <div class="section">
                <h3>Q<?php echo $qno++; ?>. Fill in the Blanks (<?php echo count($paper['FillBlank']); ?> Marks)</h3>
                <?php foreach($paper['FillBlank'] as $i => $q): ?>
                    <div class="question"><strong><?php echo $i+1; ?>.</strong> <?php echo $q['question']; ?> = ________</div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($paper['TrueFalse']) && count($paper['TrueFalse']) > 0): ?>
            <div class="section">
                <h3>Q<?php echo $qno++; ?>. True/False (<?php echo count($paper['TrueFalse']); ?> Marks)</h3>
                <?php foreach($paper['TrueFalse'] as $i => $q): ?>
                    <div class="question"><strong><?php echo $i+1; ?>.</strong> <?php echo $q['question']; ?> (True/False)</div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($paper['Short']) && count($paper['Short']) > 0): ?>
            <div class="section">
                <h3>Q<?php echo $qno++; ?>. Short Questions (<?php echo count($paper['Short'])*5; ?> Marks)</h3>
                <?php foreach($paper['Short'] as $i => $q): ?>
                    <div class="question"><strong><?php echo $i+1; ?>.</strong> <?php echo $q['question']; ?> (5)</div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($paper['Long']) && count($paper['Long']) > 0): ?>
            <div class="section">
                <h3>Q<?php echo $qno++; ?>. Long Questions (<?php echo count($paper['Long'])*10; ?> Marks)</h3>
                <?php foreach($paper['Long'] as $i => $q): ?>
                    <div class="question"><strong><?php echo $i+1; ?>.</strong> <?php echo $q['question']; ?> (10)</div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <p style="text-align:center;margin-top:20px;">🌟 BEST OF LUCK! 🌟</p>
    </div>
<?php endif; ?>

</div>
</body>
</html>