<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$fees_result = $conn->query("SELECT f.*, s.first_name, s.last_name, s.admission_no, s.class 
                      FROM fees f JOIN students s ON f.student_id=s.id 
                      ORDER BY f.id DESC LIMIT 50");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fees - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .header { background: #1a237e; color: white; padding: 15px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; }
        .header a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; }
        .card { background: white; border-radius: 10px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #1a237e; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; display: inline-block; }
        .paid { background: #d4edda; color: #155724; }
        .pending { background: #fff3cd; color: #856404; }
        .partial { background: #d1ecf1; color: #0c5460; }
        .overdue { background: #f8d7da; color: #721c24; }
        .btn-sm { padding: 5px 10px; border-radius: 5px; text-decoration: none; color: white; font-size: 10px; display: inline-block; margin: 1px; }
        .btn-collect { background: #27ae60; }
        .btn-receipt { background: #1a237e; }
    </style>
</head>
<body>

<div class="header">
    <h2>💰 Fee Management</h2>
    <div>
        <a href="../../dashboard/">🏠 Home</a>
        <a href="collect.php" style="background:#27ae60;">➕ Collect Fee</a>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Class</th>
                <th>Amount</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($fees_result && $fees_result->num_rows > 0): ?>
                <?php while($fee = $fees_result->fetch_assoc()): 
                    $due_amount = $fee['amount'] - $fee['paid_amount'];
                    $status_class = strtolower($fee['status']);
                ?>
                    <tr>
                        <td>
                            <strong><?php echo $fee['first_name'].' '.$fee['last_name']; ?></strong>
                            <br><small><?php echo $fee['admission_no']; ?></small>
                        </td>
                        <td><?php echo $fee['class']; ?></td>
                        <td>Rs.<?php echo number_format($fee['amount']); ?></td>
                        <td style="color:#27ae60;">Rs.<?php echo number_format($fee['paid_amount']); ?></td>
                        <td style="color:<?php echo $due_amount>0?'#e74c3c':'#27ae60'; ?>;">
                            Rs.<?php echo number_format($due_amount); ?>
                        </td>
                        <td><span class="badge <?php echo $status_class; ?>"><?php echo $fee['status']; ?></span></td>
                        <td style="display:flex;gap:4px;">
                            <a href="collect.php?id=<?php echo $fee['id']; ?>" class="btn-sm btn-collect">💵 Pay</a>
                            <a href="receipt.php?id=<?php echo $fee['id']; ?>" class="btn-sm btn-receipt" target="_blank">🧾 Receipt</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;padding:30px;">No fee records found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>