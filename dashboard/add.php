<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$msg = '';
if (isset($_POST['save'])) {
    $photo = 'default.png';
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'TCH_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $photo);
    }
    
    $sql = "INSERT INTO teachers (employee_id, first_name, last_name, email, phone, qualification, subject_specialty, salary, address, photo, joining_date) 
            VALUES ('{$_POST['employee_id']}', '{$_POST['first_name']}', '{$_POST['last_name']}', '{$_POST['email']}', '{$_POST['phone']}', '{$_POST['qualification']}', '{$_POST['subject_specialty']}', '{$_POST['salary']}', '{$_POST['address']}', '$photo', CURDATE())";
    
    $msg = $conn->query($sql) ? "<p style='color:green;'>✅ Teacher added!</p>" : "<p style='color:red;'>❌ Error</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Teacher</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; }
        h2 { color: #1a237e; }
        .form-group { margin-bottom: 12px; }
        label { font-weight: bold; font-size: 13px; display: block; }
        input, select, textarea { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 13px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        button { padding: 12px 30px; background: #27ae60; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; }
        .back { display: block; margin-top: 10px; text-align: center; }
        .photo-preview { width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 3px solid #1a237e; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>➕ Add New Teacher</h2>
    <?php echo $msg; ?>
    <form method="POST" enctype="multipart/form-data">
        <div style="text-align:center;">
            <img src="<?php echo BASE_URL; ?>assets/uploads/photos/default.png" class="photo-preview" id="preview">
            <br><input type="file" name="photo" accept="image/*" onchange="document.getElementById('preview').src=window.URL.createObjectURL(this.files[0])">
        </div>
        <div class="row">
            <div class="form-group"><label>Employee ID *</label><input name="employee_id" required></div>
            <div class="form-group"><label>First Name *</label><input name="first_name" required></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Last Name</label><input name="last_name"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email"></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Phone</label><input name="phone"></div>
            <div class="form-group"><label>Qualification</label><input name="qualification"></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Subject Specialty</label><input name="subject_specialty"></div>
            <div class="form-group"><label>Salary</label><input type="number" name="salary" step="0.01"></div>
        </div>
        <div class="form-group"><label>Address</label><textarea name="address" rows="2"></textarea></div>
        <button type="submit" name="save">💾 Save Teacher</button>
    </form>
    <a href="./" class="back">← Back to List</a>
</div>
</body>
</html>