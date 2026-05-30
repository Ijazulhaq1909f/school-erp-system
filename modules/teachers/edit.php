<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$id = $_GET['id'] ?? 0;
$t = $conn->query("SELECT * FROM teachers WHERE id=$id")->fetch_assoc();
if (!$t) { header('Location: ./'); exit; }

$msg = '';
if (isset($_POST['save'])) {
    $photo = $t['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'TCH_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $photo);
        // Delete old photo
        if ($t['photo'] != 'default.png' && file_exists(PHOTO_DIR . $t['photo'])) {
            unlink(PHOTO_DIR . $t['photo']);
        }
    }
    
    $sql = "UPDATE teachers SET 
            employee_id='{$_POST['employee_id']}',
            first_name='{$_POST['first_name']}',
            last_name='{$_POST['last_name']}',
            email='{$_POST['email']}',
            phone='{$_POST['phone']}',
            qualification='{$_POST['qualification']}',
            subject_specialty='{$_POST['subject_specialty']}',
            salary='{$_POST['salary']}',
            address='{$_POST['address']}',
            photo='$photo'
            WHERE id=$id";
    
    $msg = $conn->query($sql) ? "<p style='color:green;'>✅ Updated!</p>" : "<p style='color:red;'>❌ Error</p>";
    $t = $conn->query("SELECT * FROM teachers WHERE id=$id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Teacher - <?php echo $t['first_name']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; }
        h2 { color: #1a237e; }
        .form-group { margin-bottom: 12px; }
        label { font-weight: bold; font-size: 13px; display: block; margin-bottom: 4px; }
        input, select, textarea { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 13px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        button { padding: 12px 30px; background: #f39c12; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; }
        .back { display: block; margin-top: 10px; text-align: center; }
        .photo-preview { width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 3px solid #1a237e; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>✏️ Edit Teacher</h2>
    <?php echo $msg; ?>
    <form method="POST" enctype="multipart/form-data">
        <div style="text-align:center;">
            <img src="<?php echo getPhotoUrl($t['photo']); ?>" class="photo-preview" id="preview">
            <br><input type="file" name="photo" accept="image/*" onchange="document.getElementById('preview').src=window.URL.createObjectURL(this.files[0])">
            <br><small>Leave empty to keep current photo</small>
        </div>
        <div class="row">
            <div class="form-group"><label>Employee ID *</label><input name="employee_id" value="<?php echo $t['employee_id']; ?>" required></div>
            <div class="form-group"><label>First Name *</label><input name="first_name" value="<?php echo $t['first_name']; ?>" required></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Last Name</label><input name="last_name" value="<?php echo $t['last_name']; ?>"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo $t['email']; ?>"></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Phone</label><input name="phone" value="<?php echo $t['phone']; ?>"></div>
            <div class="form-group"><label>Qualification</label><input name="qualification" value="<?php echo $t['qualification']; ?>"></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Subject Specialty</label><input name="subject_specialty" value="<?php echo $t['subject_specialty']; ?>"></div>
            <div class="form-group"><label>Salary</label><input type="number" name="salary" value="<?php echo $t['salary']; ?>" step="0.01"></div>
        </div>
        <div class="form-group"><label>Address</label><textarea name="address" rows="2"><?php echo $t['address']; ?></textarea></div>
        <button type="submit" name="save">💾 Update Teacher</button>
    </form>
    <a href="./" class="back">← Back to List</a>
</div>
</body>
</html>