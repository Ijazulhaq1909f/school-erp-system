<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

$msg = '';

if (isset($_POST['save'])) {
    $photo = 'default.png';
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'STD_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $photo);
    }
    
    $sql = "INSERT INTO students (admission_no, first_name, last_name, father_name, mother_name, dob, gender, class, section, phone, email, address, photo, admission_date) 
            VALUES ('{$_POST['admission_no']}', '{$_POST['first_name']}', '{$_POST['last_name']}', '{$_POST['father_name']}', '{$_POST['mother_name']}', '{$_POST['dob']}', '{$_POST['gender']}', '{$_POST['class']}', '{$_POST['section']}', '{$_POST['phone']}', '{$_POST['email']}', '{$_POST['address']}', '$photo', CURDATE())";
    
    if ($conn->query($sql)) {
        $msg = "<div style='background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin-bottom:15px;'>✅ Student added successfully! <a href='./'>View List</a></div>";
    } else {
        $msg = "<div style='background:#f8d7da;color:#721c24;padding:12px;border-radius:8px;margin-bottom:15px;'>❌ Error: " . $conn->error . "</div>";
    }
}

// Generate auto admission number
$last = $conn->query("SELECT admission_no FROM students ORDER BY id DESC LIMIT 1")->fetch_assoc();
$last_no = $last ? intval(substr($last['admission_no'], 3)) : 0;
$new_no = 'ADM' . str_pad($last_no + 1, 3, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 750px; margin: 0 auto; }
        .header { background: #1a237e; color: white; padding: 15px 25px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header a { color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 6px; font-size: 13px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); }
        h2 { color: #1a237e; margin-bottom: 5px; }
        .section-title { color: #1a237e; font-size: 16px; font-weight: bold; margin: 25px 0 15px; padding-bottom: 8px; border-bottom: 2px solid #e8eaf6; }
        .section-title span { margin-right: 8px; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: 600; font-size: 13px; display: block; margin-bottom: 5px; color: #444; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: 0.3s; font-family: inherit; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #1a237e; box-shadow: 0 0 0 3px rgba(26,35,126,0.1); }
        textarea { resize: vertical; min-height: 60px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        
        /* Photo Upload */
        .photo-section { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 10px; border: 2px dashed #ccc; }
        .photo-preview { width: 120px; height: 120px; border-radius: 10px; object-fit: cover; border: 3px solid #1a237e; cursor: pointer; flex-shrink: 0; background: #e0e0e0; }
        .photo-info { flex: 1; }
        .photo-info label { margin-bottom: 8px; }
        .photo-info input[type="file"] { padding: 8px; }
        .photo-info small { color: #999; font-size: 11px; display: block; margin-top: 5px; }
        
        /* Buttons */
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 12px 25px; border: none; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; transition: 0.3s; }
        .btn-save { background: #27ae60; color: white; flex: 1; }
        .btn-save:hover { background: #219a52; }
        .btn-reset { background: #e0e0e0; color: #333; }
        .btn-back { background: #666; color: white; display: block; text-align: center; margin-top: 10px; }
        
        @media (max-width: 600px) {
            .row, .row-3 { grid-template-columns: 1fr; }
            .photo-section { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
<div class="container">

<div class="header">
    <h2>➕ Add New Student</h2>
    <div>
        <a href="./">📋 View All</a>
    </div>
</div>

<?php echo $msg; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="card">
        
        <!-- Photo Upload -->
        <div class="photo-section">
            <img src="<?php echo BASE_URL; ?>assets/uploads/photos/default.png" class="photo-preview" id="preview" onclick="document.getElementById('photoInput').click()" alt="Photo Preview">
            <div class="photo-info">
                <label>📸 Student Photo</label>
                <input type="file" name="photo" id="photoInput" accept="image/*" onchange="document.getElementById('preview').src=window.URL.createObjectURL(this.files[0])">
                <small>Max size: 2MB | JPG, PNG, GIF | Click on image to change</small>
            </div>
        </div>
        
        <!-- Personal Information -->
        <div class="section-title"><span>👤</span> Personal Information</div>
        
        <div class="row">
            <div class="form-group">
                <label>Admission No *</label>
                <input type="text" name="admission_no" value="<?php echo $new_no; ?>" required readonly style="background:#f0f4ff;font-weight:bold;color:#1a237e;">
            </div>
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" placeholder="Enter first name" required>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Enter last name">
            </div>
            <div class="form-group">
                <label>Father Name *</label>
                <input type="text" name="father_name" placeholder="Enter father name" required>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group">
                <label>Mother Name</label>
                <input type="text" name="mother_name" placeholder="Enter mother name">
            </div>
            <div class="form-group">
                <label>Date of Birth *</label>
                <input type="date" name="dob" required>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group">
                <label>Gender *</label>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">👦 Male</option>
                    <option value="Female">👧 Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="03XX-XXXXXXX">
            </div>
        </div>
        
        <!-- Academic Information -->
        <div class="section-title"><span>🏫</span> Academic Information</div>
        
        <div class="row">
            <div class="form-group">
                <label>Class *</label>
                <select name="class" id="class_select" required onchange="loadSections()">
                    <option value="">Select Class</option>
                    <option value="Nursery">Nursery</option>
                    <option value="KGI">KGI</option>
                    <option value="KGII">KGII</option>
                    <?php for($i=1; $i<=10; $i++): 
                        $c = $i . ($i==1?'st':($i==2?'nd':($i==3?'rd':'th')));
                    ?>
                        <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section *</label>
                <select name="section" id="section_select" required>
                    <option value="">First select class</option>
                </select>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="section-title"><span>📞</span> Contact Information</div>
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="example@email.com">
        </div>
        
        <div class="form-group">
            <label>Home Address</label>
            <textarea name="address" rows="2" placeholder="Enter complete address"></textarea>
        </div>
        
        <!-- Buttons -->
        <div class="btn-group">
            <button type="submit" name="save" class="btn btn-save">💾 Save Student</button>
            <button type="reset" class="btn btn-reset">🔄 Reset Form</button>
        </div>
    </div>
</form>

<a href="./" class="btn btn-back">← Back to Students List</a>

</div>

<script>
function loadSections() {
    var cls = document.getElementById('class_select').value;
    var sec = document.getElementById('section_select');
    sec.innerHTML = '<option value="">Select Section</option>';
    
    if (!cls) return;
    
    var sections = ['A', 'B', 'C'];
    
    // Nursery, KGI, KGII usually have A,B only
    if (cls === 'Nursery' || cls === 'KGI' || cls === 'KGII') {
        sections = ['A', 'B'];
    }
    
    // 9th, 10th have more sections
    if (cls === '9th' || cls === '10th') {
        sections = ['A', 'B', 'C', 'D'];
    }
    
    sections.forEach(function(s) {
        var opt = document.createElement('option');
        opt.value = s;
        opt.textContent = 'Section ' + s;
        sec.appendChild(opt);
    });
}

// Auto-load sections if class pre-selected
window.onload = function() {
    var cls = document.getElementById('class_select').value;
    if (cls) loadSections();
};
</script>

</body>
</html>
