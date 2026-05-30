<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$fee_id = $_GET['id'] ?? 0;
$fee = $conn->query("SELECT f.*, s.first_name, s.last_name, s.admission_no, s.class, s.section, s.father_name 
                     FROM fees f JOIN students s ON f.student_id=s.id WHERE f.id=$fee_id")->fetch_assoc();

if (!$fee) { echo "Fee record not found!"; exit; }

$due = $fee['amount'] - $fee['paid_amount'];
$receipt_no = 'RCP-' . date('Ymd') . '-' . str_pad($fee_id, 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fee Receipt - <?php echo $fee['first_name']; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; background: #e0e0e0; padding: 20px; }
        .receipt { max-width: 650px; margin: 0 auto; background: white; border: 2px solid #333; }
        .receipt-header { background: #1a237e; color: white; padding: 15px; text-align: center; }
        .receipt-header h2 { font-size: 16px; margin-bottom: 3px; }
        .receipt-header p { font-size: 10px; opacity: 0.8; }
        .receipt-body { padding: 20px; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #ccc; font-size: 13px; }
        .label { font-weight: bold; color: #555; }
        .value { font-weight: bold; }
        .amount-box { text-align: center; padding: 15px; margin: 15px 0; border: 2px solid #1a237e; background: #f8f9fa; }
        .amount-box h1 { font-size: 32px; color: #1a237e; }
        .status { text-align: center; padding: 10px; font-size: 14px; font-weight: bold; }
        .paid { color: #27ae60; }
        .pending { color: #e74c3c; }
        .partial { color: #f39c12; }
        .footer { text-align: center; padding: 15px; border-top: 1px solid #eee; font-size: 10px; color: #999; }
        .signatures { display: flex; justify-content: space-around; margin-top: 30px; text-align: center; }
        .sig-line { border-top: 1px solid #333; width: 120px; padding-top: 5px; font-size: 11px; }
        .print-btn { position: fixed; top: 10px; right: 10px; background: #1a237e; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .back-btn { position: fixed; top: 10px; left: 10px; background: #666; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 13px; }
        @media print { body { background: white; padding: 0; } .receipt { border: 1px solid #ccc; } .print-btn, .back-btn { display: none; } }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>
<a href="./" class="back-btn">← Back</a>

<div class="receipt">
    <div class="receipt-header">
        <h2><?php echo SCHOOL_NAME; ?></h2>
        <p><?php echo SCHOOL_ADDRESS; ?> | 📞 <?php echo SCHOOL_PHONE; ?></p>
        <p><?php echo SCHOOL_EMAIL; ?></p>
        <h3 style="margin-top:8px;">FEE PAYMENT RECEIPT</h3>
    </div>
    
    <div class="receipt-body">
        <div class="row"><span class="label">Receipt No:</span><span class="value"><?php echo $receipt_no; ?></span></div>
        <div class="row"><span class="label">Date:</span><span class="value"><?php echo date('d M Y'); ?></span></div>
        <div class="row"><span class="label">Student Name:</span><span class="value"><?php echo $fee['first_name'].' '.$fee['last_name']; ?></span></div>
        <div class="row"><span class="label">Admission No:</span><span class="value"><?php echo $fee['admission_no']; ?></span></div>
        <div class="row"><span class="label">Father Name:</span><span class="value"><?php echo $fee['father_name']; ?></span></div>
        <div class="row"><span class="label">Class:</span><span class="value"><?php echo $fee['class'].' - '.$fee['section']; ?></span></div>
        
        <div class="amount-box">
            <p style="font-size:12px;color:#888;">TOTAL AMOUNT PAID</p>
            <h1>Rs. <?php echo number_format($fee['paid_amount']); ?>/-</h1>
        </div>
        
        <div class="row"><span class="label">Total Fee:</span><span>Rs. <?php echo number_format($fee['amount']); ?>/-</span></div>
        <div class="row"><span class="label">Amount Paid:</span><span style="color:#27ae60;">Rs. <?php echo number_format($fee['paid_amount']); ?>/-</span></div>
        <div class="row"><span class="label">Balance Due:</span><span style="color:<?php echo $due>0?'#e74c3c':'#27ae60'; ?>;">Rs. <?php echo number_format($due); ?>/-</span></div>
        <div class="row"><span class="label">Payment Method:</span><span><?php echo $fee['payment_method'] ?: 'Cash'; ?></span></div>
        <div class="row"><span class="label">Due Date:</span><span><?php echo $fee['due_date'] ? date('d M Y', strtotime($fee['due_date'])) : 'N/A'; ?></span></div>
        <div class="row"><span class="label">Payment Date:</span><span><?php echo $fee['payment_date'] ? date('d M Y', strtotime($fee['payment_date'])) : 'N/A'; ?></span></div>
        
        <div class="status <?php echo strtolower($fee['status']); ?>">
            Status: <?php echo strtoupper($fee['status']); ?>
        </div>
        
        <div style="background:#f8f9fa;padding:10px;border-radius:5px;margin:10px 0;font-size:11px;">
            <strong>Amount in Words:</strong> <?php echo convertToWords($fee['paid_amount']); ?> Rupees Only
        </div>
        
        <div class="signatures">
            <div class="sig-line">Parent/Guardian</div>
            <div class="sig-line">Accountant</div>
            <div class="sig-line">Principal<br><small><?php echo PRINCIPAL_NAME; ?></small></div>
        </div>
    </div>
    
    <div class="footer">
        This is a computer generated receipt | <?php echo SCHOOL_SHORT; ?> | <?php echo date('d M Y'); ?>
    </div>
</div>

</body>
</html>

<?php
function convertToWords($number) {
    $words = ['Zero','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten',
              'Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
    $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
    
    if ($number < 20) return $words[$number];
    if ($number < 100) return $tens[floor($number/10)] . ($number%10 ? ' '.$words[$number%10] : '');
    if ($number < 1000) return $words[floor($number/100)].' Hundred'.($number%100 ? ' and '.convertToWords($number%100) : '');
    if ($number < 100000) return convertToWords(floor($number/1000)).' Thousand'.($number%1000 ? ' '.convertToWords($number%1000) : '');
    return $number;
}
?>