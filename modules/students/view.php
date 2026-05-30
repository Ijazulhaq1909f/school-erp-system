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
    <title><?php echo $s['first_name']; ?> - Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .profile-header { background: linear-gradient(135deg, #1a237e, #3949ab); color: white; padding: 30px; text-align: center; }
        .profile-photo { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid white; }
        .profile-body { padding: 20px; }
        .row { display: flex; padding: 10px 0; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; width: 120px; color: #555; }
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; display: inline-block; margin: 5px; }
        .btn-edit { background: #f39c12; }
        .btn-back { background: #666; }
        .btn-id { background: #1a237e; }
    </style>
</head>
<body>
<div class="container">
    <div class="profile-header">
        <img src="<?php echo getPhotoUrl($s['photo']); ?>" class="profile-photo" onerror="this.src='<?php echo BASE_URL; ?>assets/uploads/photos/default.png'">
        <h2><?php echo $s['first_name'].' '.$s['last_name']; ?></h2>
        <p><?php echo $s['admission_no']; ?></p>
    </div>
    <div class="profile-body">
        <div class="row"><span class="label">Father Name</span><span><?php echo $s['father_name']; ?></span></div>
        <div class="row"><span class="label">Mother Name</span><span><?php echo $s['mother_name'] ?: 'N/A'; ?></span></div>
        <div class="row"><span class="label">Class</span><span><?php echo $s['class'].' - '.$s['section']; ?></span></div>
        <div class="row"><span class="label">DOB</span><span><?php echo $s['dob']; ?></span></div>
        <div class="row"><span class="label">Gender</span><span><?php echo $s['gender']; ?></span></div>
        <div class="row"><span class="label">Phone</span><span><?php echo $s['phone'] ?: 'N/A'; ?></span></div>
        <div class="row"><span class="label">Email</span><span><?php echo $s['email'] ?: 'N/A'; ?></span></div>
        <div class="row"><span class="label">Address</span><span><?php echo $s['address'] ?: 'N/A'; ?></span></div>
        <div class="row"><span class="label">Admission Date</span><span><?php echo $s['admission_date']; ?></span></div>
    </div>
    <div style="padding:20px;text-align:center;">
        <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn btn-edit">✏️ Edit</a>
        <a href="id-card.php?id=<?php echo $s['id']; ?>" class="btn btn-id" target="_blank">🪪 ID Card</a>
        <a href="./" class="btn btn-back">← Back</a>
    </div>
</div>
</body>
</html>