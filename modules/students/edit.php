<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$id = $_GET['id'] ?? 0;
$s = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
if (!$s) { header('Location: ./'); exit; }

$msg = '';
if (isset($_POST['save'])) {
    $photo = $s['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'STD_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $photo);
        if ($s['photo'] != 'default.png' && file_exists(PHOTO_DIR . $s['photo'])) {
            unlink(PHOTO_DIR . $s['photo']);
        }
    }
    
    $sql = "UPDATE students SET 
            admission_no='{$_POST['admission_no']}',
            first_name='{$_POST['first_name']}',
            last_name='{$_POST['last_name']}',
            father_name='{$_POST['father_name']}',
            mother_name='{$_POST['mother_name']}',
            dob='{$_POST['dob']}',
            gender='{$_POST['gender']}',
            class='{$_POST['class']}',
            section='{$_POST['section']}',
            phone='{$_POST['phone']}',
            email='{$_POST['email']}',
            address='{$_POST['address']}',
            photo='$photo'
            WHERE id=$id";
    
    $msg = $conn->query($sql) ? "<p style='color:green;'>✅ Updated!</p>" : "<p style='color:red;'>❌ Error</p>";
    $s = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Student - <?php echo $s['first_name']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; }
        h2 { color: #1a237e; }
        .form-group { margin-bottom: 12px; }
        label { font-weight: bold; font-size: 13px; display: block; }
        input, select, textarea { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        button { padding: 12px 30px; background: #f39c12; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; }
        .back { display: block; margin-top: 10px; text-align: center; }
        .photo-preview { width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 3px solid #1a237e; }
    </style>
</head>
<body>
<div class="container">
    <h2>✏️ Edit Student</h2>
    <?php echo $msg; ?>
    <form method="POST" enctype="multipart/form-data">
        <div style="text-align:center;">
            <img src="<?php echo getPhotoUrl($s['photo']); ?>" class="photo-preview" id="preview">
            <br><input type="file" name="photo" accept="image/*" onchange="document.getElementById('preview').src=window.URL.createObjectURL(this.files[0])">
        </div>
        <div class="row">
            <div class="form-group"><label>Admission No *</label><input name="admission_no" value="<?php echo $s['admission_no']; ?>" required></div>
            <div class="form-group"><label>First Name *</label><input name="first_name" value="<?php echo $s['first_name']; ?>" required></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Last Name</label><input name="last_name" value="<?php echo $s['last_name']; ?>"></div>
            <div class="form-group"><label>Father Name</label><input name="father_name" value="<?php echo $s['father_name']; ?>"></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Mother Name</label><input name="mother_name" value="<?php echo $s['mother_name']; ?>"></div>
            <div class="form-group"><label>DOB</label><input type="date" name="dob" value="<?php echo $s['dob']; ?>"></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Gender</label><select name="gender"><option <?php echo $s['gender']=='Male'?'selected':''; ?>>Male</option><option <?php echo $s['gender']=='Female'?'selected':''; ?>>Female</option></select></div>
            <div class="form-group"><label>Phone</label><input name="phone" value="<?php echo $s['phone']; ?>"></div>
        </div>
        <div class="row">
            <div class="form-group"><label>Class</label>
                <select name="class">
                    <?php foreach(['Nursery','KGI','KGII','1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th'] as $c): ?>
                        <option <?php echo $s['class']==$c?'selected':''; ?>><?php echo $c; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Section</label><select name="section"><option <?php echo $s['section']=='A'?'selected':''; ?>>A</option><option <?php echo $s['section']=='B'?'selected':''; ?>>B</option><option <?php echo $s['section']=='C'?'selected':''; ?>>C</option></select></div>
        </div>
        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo $s['email']; ?>"></div>
        <div class="form-group"><label>Address</label><textarea name="address" rows="2"><?php echo $s['address']; ?></textarea></div>
        <button type="submit" name="save">💾 Update Student</button>
    </form>
    <a href="./" class="back">← Back to List</a>
</div>
</body>
</html>