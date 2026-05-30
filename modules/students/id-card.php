<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$id = $_GET['id'] ?? 0;
$s = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
if (!$s) { echo "Student not found!"; exit; }
?>
<!DOCTYPE html>
<html>
<head>
    <title>ID Card - <?php echo $s['first_name']; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: #e0e0e0; 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; padding: 20px; 
        }
        .id-card {
            width: 370px; background: white; border-radius: 15px; 
            overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .card-header {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            padding: 20px 15px 10px; text-align: center; color: white;
        }
        .card-header img { 
            width: 50px; height: 50px; border-radius: 50%; 
            object-fit: contain; background: white; padding: 3px; 
            border: 2px solid white; margin-bottom: 8px; 
        }
        .card-header h3 { font-size: 13px; letter-spacing: 1px; }
        .card-header small { font-size: 9px; opacity: 0.8; }
        .card-badge {
            display: inline-block; background: #c62828; color: white;
            padding: 4px 20px; border-radius: 20px; font-size: 10px;
            letter-spacing: 2px; margin-top: 8px;
        }
        .photo-section { text-align: center; padding: 20px; background: white; }
        .student-photo {
            width: 100px; height: 100px; border-radius: 50%;
            border: 4px solid #1a237e; object-fit: cover; margin-bottom: 10px;
        }
        .student-name { font-size: 18px; font-weight: bold; color: #1a237e; }
        .info-section { padding: 5px 20px 15px; }
        .info-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px dashed #e0e0e0; font-size: 12px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #888; font-weight: 600; }
        .info-value { color: #2c3e50; font-weight: bold; }
        .barcode { 
            background: #f8f9fa; padding: 10px; text-align: center;
            font-family: 'Courier New', monospace; font-size: 13px;
            letter-spacing: 3px; border-top: 2px solid #1a237e;
        }
        .footer { background: #1a237e; color: white; text-align: center; padding: 8px; font-size: 9px; }
        .print-btn { position: fixed; top: 15px; right: 15px; background: #1a237e; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; z-index: 1000; }
        .back-btn { position: fixed; top: 15px; left: 15px; background: #666; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; text-decoration: none; font-size: 13px; z-index: 1000; }
        @media print { body { background: white; padding: 0; } .id-card { box-shadow: none; } .print-btn, .back-btn { display: none; } }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">🖨️ Print</button>
<a href="view.php?id=<?php echo $s['id']; ?>" class="back-btn">← Back</a>

<div class="id-card">
    <div class="card-header">
        <?php if(hasLogo()): ?><img src="<?php echo getLogoUrl(); ?>" alt="Logo"><?php endif; ?>
        <h3><?php echo SCHOOL_SHORT; ?></h3>
        <small><?php echo SCHOOL_ADDRESS; ?></small>
        <div class="card-badge">STUDENT ID CARD</div>
    </div>
    
    <div class="photo-section">
        <img src="<?php echo getPhotoUrl($s['photo']); ?>" class="student-photo" onerror="this.src='<?php echo BASE_URL; ?>assets/uploads/photos/default.png'">
        <div class="student-name"><?php echo $s['first_name'].' '.$s['last_name']; ?></div>
    </div>
    
    <div class="info-section">
        <div class="info-row"><span class="info-label">Admission No</span><span class="info-value"><?php echo $s['admission_no']; ?></span></div>
        <div class="info-row"><span class="info-label">Class</span><span class="info-value"><?php echo $s['class'].' - '.$s['section']; ?></span></div>
        <div class="info-row"><span class="info-label">Father Name</span><span class="info-value"><?php echo $s['father_name']; ?></span></div>
        <div class="info-row"><span class="info-label">Phone</span><span class="info-value"><?php echo $s['phone'] ?: 'N/A'; ?></span></div>
        <div class="info-row"><span class="info-label">DOB</span><span class="info-value"><?php echo $s['dob']; ?></span></div>
        <div class="info-row"><span class="info-label">Valid Until</span><span class="info-value"><?php echo date('d M Y', strtotime('+1 year')); ?></span></div>
    </div>
    
    <div class="barcode">* <?php echo $s['admission_no']; ?> *</div>
    
    <div class="footer">
        📞 <?php echo SCHOOL_PHONE; ?> | Principal: <?php echo PRINCIPAL_NAME; ?>
    </div>
</div>

</body>
</html>