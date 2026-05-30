<?php
require_once '../../shared/config.php';
if (!isLoggedIn() || !isAdmin()) redirect(BASE_URL . 'login.php');

$msg = '';

// Save settings
if (isset($_POST['save_settings'])) {
    foreach ($_POST as $key => $value) {
        if ($key != 'save_settings') {
            $val = sanitize($value);
            $conn->query("UPDATE school_settings SET setting_value='$val' WHERE setting_key='$key'");
        }
    }
    $msg = "<div style='background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin-bottom:20px;'>✅ Settings updated! Refresh page to see changes.</div>";
}

// Load settings
$settings = [];
$result = $conn->query("SELECT * FROM school_settings");
while($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 700px; margin: 0 auto; }
        .header { background: #1a237e; color: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: white; text-decoration: none; }
        .card { background: white; padding: 25px; border-radius: 10px; margin-bottom: 20px; }
        h3 { color: #1a237e; margin-top: 0; border-bottom: 2px solid #e8eaf6; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px; color: #555; }
        input, textarea { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 14px; }
        input:focus, textarea:focus { outline: none; border-color: #1a237e; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        button { padding: 12px 30px; background: #1a237e; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; }
        button:hover { background: #283593; }
        .info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #666; }
        @media (max-width: 600px) { .row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">

<div class="header">
    <h2>⚙️ System Settings</h2>
    <a href="../../dashboard/">🏠 Dashboard</a>
</div>

<?php echo $msg; ?>

<form method="POST">
    <!-- School Information -->
    <div class="card">
        <h3>🏫 School Information</h3>
        <div class="form-group"><label>School Full Name</label><input name="school_name" value="<?php echo $settings['school_name']; ?>"></div>
        <div class="form-group"><label>School Short Name</label><input name="school_short" value="<?php echo $settings['school_short']; ?>"></div>
        <div class="form-group"><label>School Abbreviation</label><input name="school_abbr" value="<?php echo $settings['school_abbr']; ?>"></div>
        <div class="form-group"><label>School Motto</label><input name="school_motto" value="<?php echo $settings['school_motto']; ?>"></div>
        <div class="form-group"><label>Address</label><textarea name="school_address" rows="2"><?php echo $settings['school_address']; ?></textarea></div>
        <div class="row">
            <div class="form-group"><label>Phone</label><input name="school_phone" value="<?php echo $settings['school_phone']; ?>"></div>
            <div class="form-group"><label>WhatsApp</label><input name="school_whatsapp" value="<?php echo $settings['school_whatsapp']; ?>"></div>
        </div>
        <div class="form-group"><label>Email</label><input name="school_email" value="<?php echo $settings['school_email']; ?>"></div>
    </div>
    
    <!-- Principal -->
    <div class="card">
        <h3>👨‍💼 Principal Information</h3>
        <div class="form-group"><label>Principal Name</label><input name="principal_name" value="<?php echo $settings['principal_name']; ?>"></div>
    </div>
    
    <!-- Session -->
    <div class="card">
        <h3>📅 Academic Session</h3>
        <div class="form-group"><label>Current Session</label><input name="current_session" value="<?php echo $settings['current_session']; ?>"></div>
    </div>
    
    <button type="submit" name="save_settings">💾 Save All Settings</button>
</form>

</div>
</body>
</html>