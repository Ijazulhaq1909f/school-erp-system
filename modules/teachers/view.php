<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$id = $_GET['id'] ?? 0;
$t = $conn->query("SELECT * FROM teachers WHERE id=$id")->fetch_assoc();
if(!$t) { header('Location: ./'); exit; }
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $t['first_name']; ?> - Teacher Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 25px; text-align: center; }
        .header img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 4px solid white; }
        .body { padding: 20px; }
        .row { display: flex; padding: 10px 0; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; width: 130px; color: #555; font-size: 13px; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; margin: 5px; display: inline-block; font-size: 12px; }
        .btn-edit { background: #f39c12; }
        .btn-back { background: #666; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="<?php echo getPhotoUrl($t['photo']); ?>" onerror="this.src='<?php echo BASE_URL; ?>assets/uploads/photos/default.png'">
        <h2><?php echo $t['first_name'].' '.$t['last_name']; ?></h2>
        <p><?php echo $t['employee_id']; ?></p>
    </div>
    <div class="body">
        <div class="row"><span class="label">Subject</span><span><?php echo $t['subject_specialty']; ?></span></div>
        <div class="row"><span class="label">Qualification</span><span><?php echo $t['qualification']; ?></span></div>
        <div class="row"><span class="label">Phone</span><span><?php echo $t['phone'] ?: 'N/A'; ?></span></div>
        <div class="row"><span class="label">Email</span><span><?php echo $t['email'] ?: 'N/A'; ?></span></div>
        <div class="row"><span class="label">Salary</span><span>Rs. <?php echo number_format($t['salary']); ?></span></div>
        <div class="row"><span class="label">Joining Date</span><span><?php echo $t['joining_date']; ?></span></div>
        <div class="row"><span class="label">Address</span><span><?php echo $t['address'] ?: 'N/A'; ?></span></div>
    </div>
    <div style="padding:15px;text-align:center;">
        <a href="edit.php?id=<?php echo $t['id']; ?>" class="btn btn-edit">✏️ Edit</a>
        <a href="./" class="btn btn-back">← Back to List</a>
    </div>
</div>
</body>
</html>