<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$msg = '';
$edit_id = $_GET['id'] ?? 0;
$fee = null;

if ($edit_id) {
    $fee = $conn->query("SELECT f.*, s.first_name, s.last_name FROM fees f JOIN students s ON f.student_id=s.id WHERE f.id=$edit_id")->fetch_assoc();
}

if (isset($_POST['save'])) {
    $sid = $_POST['student_id'];
    $amt = $_POST['amount'];
    $paid = $_POST['paid_amount'];
    $due_date = $_POST['due_date'];
    $status = $paid >= $amt ? 'Paid' : ($paid > 0 ? 'Partial' : 'Pending');
    
    if ($edit_id) {
        $conn->query("UPDATE fees SET amount=$amt, paid_amount=$paid, due_date='$due_date', status='$status', payment_date=IF('$status'='Paid',CURDATE(),NULL) WHERE id=$edit_id");
    } else {
        $conn->query("INSERT INTO fees (student_id, amount, paid_amount, due_date, status, payment_date) VALUES ($sid, $amt, $paid, '$due_date', '$status', IF('$status'='Paid',CURDATE(),NULL))");
    }
    $msg = "<p style='color:green;'>✅ Saved!</p>";
}

$students = $conn->query("SELECT * FROM students WHERE status=1 ORDER BY first_name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Collect Fee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; }
        h2 { color: #1a237e; }
        .form-group { margin-bottom: 12px; }
        label { font-weight: bold; font-size: 13px; display: block; }
        input, select { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        button { padding: 12px; background: #27ae60; color: white; border: none; border-radius: 8px; width: 100%; cursor: pointer; font-size: 15px; }
        .back { display: block; margin-top: 10px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h2><?php echo $edit_id ? 'Update' : 'Collect'; ?> Fee</h2>
    <?php echo $msg; ?>
    <form method="POST">
        <?php if(!$edit_id): ?>
            <div class="form-group"><label>Student *</label>
                <select name="student_id" required>
                    <option value="">Select</option>
                    <?php while($s = $students->fetch_assoc()): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo $s['admission_no'].' - '.$s['first_name'].' '.$s['last_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        <?php else: ?>
            <p><strong>Student:</strong> <?php echo $fee['first_name'].' '.$fee['last_name']; ?></p>
        <?php endif; ?>
        <div class="row">
            <div class="form-group"><label>Amount *</label><input type="number" name="amount" value="<?php echo $fee['amount']??''; ?>" required></div>
            <div class="form-group"><label>Paid Amount</label><input type="number" name="paid_amount" value="<?php echo $fee['paid_amount']??0; ?>"></div>
        </div>
        <div class="form-group"><label>Due Date</label><input type="date" name="due_date" value="<?php echo $fee['due_date']??date('Y-m-d'); ?>"></div>
        <button type="submit" name="save">💾 Save</button>
    </form>
    <a href="./" class="back">← Back</a>
</div>
</body>
</html>