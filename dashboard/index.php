<?php
require_once '../shared/config.php';
require_once '../shared/auth.php';

if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

echo "<!-- DEBUG: Role = " . $_SESSION['role'] . ", Related ID = " . ($_SESSION['related_id'] ?? 'NULL') . " -->";

$role = $_SESSION['role'];

// ============================================
// TEACHER DASHBOARD
// ============================================
if ($role == 'teacher') {
    $teacher_id = $_SESSION['related_id'] ?? 0;
    $teacher = $conn->query("SELECT * FROM teachers WHERE id=$teacher_id")->fetch_assoc();
    
    if (!$teacher) {
        echo "<h2>Teacher not found! Contact admin.</h2>";
        exit();
    }
    
    $my_classes = $conn->query("SELECT * FROM classes WHERE teacher_id=$teacher_id");
    $today_att = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present FROM attendance WHERE date=CURDATE()")->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Teacher Dashboard - <?php echo SCHOOL_SHORT; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; min-height: 100vh; }
            .teacher-header { background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 30px 20px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
            .teacher-header img { width: 90px; height: 90px; border-radius: 50%; border: 4px solid white; object-fit: cover; }
            .teacher-header h1 { margin: 10px 0 5px; font-size: 22px; }
            .teacher-header p { opacity: 0.9; font-size: 13px; }
            .container { max-width: 900px; margin: 20px auto; padding: 0 20px; }
            .school-bar { background: white; padding: 12px 20px; border-radius: 10px; text-align: center; margin-bottom: 20px; font-weight: bold; color: #1a237e; font-size: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
            .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
            .stat { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
            .stat h3 { font-size: 28px; margin: 0; }
            .stat p { color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; }
            .menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; }
            .menu a { background: white; padding: 25px 15px; border-radius: 12px; text-align: center; text-decoration: none; color: #2c3e50; box-shadow: 0 3px 15px rgba(0,0,0,0.08); transition: 0.3s; font-weight: bold; border-top: 4px solid transparent; }
            .menu a:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
            .menu a:nth-child(1) { border-top-color: #3498db; }
            .menu a:nth-child(2) { border-top-color: #e74c3c; }
            .menu a:nth-child(3) { border-top-color: #f39c12; }
            .menu a:nth-child(4) { border-top-color: #9b59b6; }
            .menu a:nth-child(5) { border-top-color: #27ae60; }
            .menu a span { display: block; font-size: 45px; margin-bottom: 10px; }
            .logout-btn { display: block; text-align: center; margin: 25px auto; padding: 10px 30px; background: #e74c3c; color: white; text-decoration: none; border-radius: 8px; width: 200px; }
        </style>
    </head>
    <body>
        <div class="teacher-header">
            <img src="<?php echo getPhotoUrl($teacher['photo']); ?>" onerror="this.style.display='none'" alt="Teacher Photo">
            <h1>Welcome, <?php echo $teacher['first_name'].' '.$teacher['last_name']; ?>!</h1>
            <p><?php echo $teacher['subject_specialty']; ?> | <?php echo SCHOOL_SHORT; ?></p>
        </div>
        
        <div class="container">
            <div class="school-bar">🏫 <?php echo SCHOOL_NAME; ?> | Principal: <?php echo PRINCIPAL_NAME; ?></div>
            
            <div class="stats">
                <div class="stat"><h3 style="color:#27ae60;"><?php echo $today_att['present'] ?? 0; ?>/<?php echo $today_att['total'] ?? 0; ?></h3><p>Today's Attendance</p></div>
                <div class="stat"><h3 style="color:#2196f3;"><?php echo $my_classes->num_rows; ?></h3><p>My Classes</p></div>
            </div>
            
            <div class="menu">
                <a href="../modules/attendance/"><span>📋</span>Mark Attendance</a>
                <a href="../modules/exams/"><span>📝</span>Manage Exams</a>
                <a href="../modules/exams/paper-generator.php"><span>🤖</span>Paper Generator</a>
                <a href="../modules/notices/"><span>📢</span>Notices</a>
                <a href="../modules/teachers/view.php?id=<?php echo $teacher_id; ?>"><span>👤</span>My Profile</a>
            </div>
            
            <a href="<?php echo BASE_URL; ?>logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// ============================================
// PARENT DASHBOARD
// ============================================
if ($role == 'parent') {
    $student_id = $_SESSION['related_id'] ?? 0;
    $student = $conn->query("SELECT * FROM students WHERE id=$student_id")->fetch_assoc();
    
    if (!$student) {
        echo "<h2>Student not found! Contact school administration.</h2>";
        exit();
    }
    
    $att = $conn->query("SELECT COUNT(*) as t, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as p FROM attendance WHERE student_id=$student_id")->fetch_assoc();
    $att_per = $att['t'] > 0 ? round(($att['p']/$att['t'])*100) : 0;
    
    $fee = $conn->query("SELECT COALESCE(SUM(amount),0) as t, COALESCE(SUM(paid_amount),0) as p FROM fees WHERE student_id=$student_id")->fetch_assoc();
    $fee_due = ($fee['t'] ?? 0) - ($fee['p'] ?? 0);
    
    $result = $conn->query("SELECT AVG((r.marks_obtained/e.max_marks)*100) as avg FROM results r JOIN exams e ON r.exam_id=e.id WHERE r.student_id=$student_id ORDER BY e.exam_date DESC LIMIT 1")->fetch_assoc();
    
    $notices = $conn->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 4");
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Parent Portal - <?php echo SCHOOL_SHORT; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; min-height: 100vh; }
            .parent-header { background: linear-gradient(135deg, #8e44ad, #9b59b6); color: white; padding: 30px 20px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
            .parent-header img { width: 100px; height: 100px; border-radius: 50%; border: 5px solid white; object-fit: cover; }
            .parent-header h1 { margin: 12px 0 5px; font-size: 22px; }
            .parent-header p { opacity: 0.9; font-size: 13px; }
            .container { max-width: 650px; margin: 20px auto; padding: 0 20px; }
            .card { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 4px solid #8e44ad; }
            .card h3 { color: #8e44ad; margin-bottom: 15px; font-size: 15px; }
            .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
            .row:last-child { border-bottom: none; }
            .label { color: #888; }
            .value { font-weight: bold; color: #2c3e50; }
            .badge { padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }
            .badge-green { background: #d4edda; color: #155724; }
            .badge-red { background: #f8d7da; color: #721c24; }
            .notice { padding: 12px 15px; border-left: 3px solid #8e44ad; background: #faf5ff; margin-bottom: 8px; border-radius: 5px; }
            .notice strong { display: block; font-size: 13px; margin-bottom: 3px; }
            .notice small { color: #888; font-size: 11px; }
            .logout-btn { display: block; text-align: center; margin: 20px auto; padding: 12px 30px; background: #e74c3c; color: white; text-decoration: none; border-radius: 8px; width: 200px; }
            .school-name { text-align: center; color: #1a237e; font-weight: bold; margin-bottom: 15px; }
        </style>
    </head>
    <body>
        <div class="parent-header">
            <img src="<?php echo getPhotoUrl($student['photo']); ?>" onerror="this.src='<?php echo BASE_URL; ?>assets/uploads/photos/default.png'" alt="Student Photo">
            <h1><?php echo $student['first_name'].' '.$student['last_name']; ?></h1>
            <p><?php echo $student['admission_no']; ?> | Class <?php echo $student['class'].' - Section '.$student['section']; ?></p>
        </div>
        
        <div class="container">
            <div class="school-name">🏫 <?php echo SCHOOL_NAME; ?></div>
            
            <div class="card">
                <h3>📋 Attendance Record</h3>
                <div class="row"><span class="label">Working Days</span><span class="value"><?php echo $att['t']; ?></span></div>
                <div class="row"><span class="label">Days Present</span><span class="value" style="color:#27ae60;"><?php echo $att['p']; ?></span></div>
                <div class="row"><span class="label">Attendance %</span><span class="value"><span class="badge <?php echo $att_per>=75?'badge-green':'badge-red'; ?>"><?php echo $att_per; ?>%</span></span></div>
            </div>
            
            <div class="card">
                <h3>💰 Fee Status</h3>
                <div class="row"><span class="label">Total Fee</span><span class="value">Rs. <?php echo number_format($fee['t']); ?></span></div>
                <div class="row"><span class="label">Amount Paid</span><span class="value" style="color:#27ae60;">Rs. <?php echo number_format($fee['p']); ?></span></div>
                <div class="row"><span class="label">Balance Due</span><span class="value" style="color:<?php echo $fee_due>0?'#e74c3c':'#27ae60'; ?>;">Rs. <?php echo number_format($fee_due); ?> <span class="badge <?php echo $fee_due<=0?'badge-green':'badge-red'; ?>"><?php echo $fee_due<=0 ? 'CLEARED' : 'PENDING'; ?></span></span></div>
            </div>
            
            <div class="card">
                <h3>📊 Academic Performance</h3>
                <div class="row"><span class="label">Average Score</span><span class="value"><?php echo round($result['avg']??0); ?>%</span></div>
            </div>
            
            <div class="card">
                <h3>📢 School Notices</h3>
                <?php if($notices->num_rows > 0): ?>
                    <?php while($n = $notices->fetch_assoc()): ?>
                        <div class="notice"><strong><?php echo $n['title']; ?></strong><small><?php echo date('d M Y', strtotime($n['created_at'])); ?> - <?php echo $n['content']; ?></small></div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color:#888;text-align:center;">No notices yet</p>
                <?php endif; ?>
            </div>
            
            <a href="<?php echo BASE_URL; ?>logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// ============================================
// ADMIN DASHBOARD
// ============================================

$total_students = getCount('students', 'status=1');
$total_teachers = getCount('teachers', 'status=1');
$total_classes = getCount('classes');
$total_revenue = $conn->query("SELECT COALESCE(SUM(paid_amount),0) as total FROM fees WHERE status IN ('Paid','Partial')")->fetch_assoc()['total']; 
$today_attendance = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present FROM attendance WHERE date=CURDATE()")->fetch_assoc();
$attendance_percent = $today_attendance['total'] > 0 ? round(($today_attendance['present'] / $today_attendance['total']) * 100) : 0;

$recent_students = $conn->query("SELECT * FROM students WHERE status=1 ORDER BY id DESC LIMIT 5");
$recent_fees = $conn->query("SELECT f.*, s.first_name, s.last_name FROM fees f JOIN students s ON f.student_id=s.id ORDER BY f.id DESC LIMIT 5");
$notices = $conn->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SCHOOL_SHORT; ?></title>
    <style>
        :root { --primary: #1a237e; --bg: #f0f2f5; --card: white; --text: #2c3e50; --subtext: #666; --border: #e0e0e0; --sidebar-w: 240px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); min-height: 100vh; display: flex; }
        .sidebar { width: var(--sidebar-w); background: var(--primary); color: white; position: fixed; height: 100vh; overflow-y: auto; z-index: 100; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo { width: 60px; height: 60px; border-radius: 50%; object-fit: contain; background: white; padding: 3px; border: 2px solid white; margin-bottom: 10px; }
        .sidebar-logo-placeholder { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; border: 2px solid white; }
        .sidebar h3 { font-size: 13px; margin: 0; } .sidebar small { font-size: 9px; opacity: 0.7; }
        .sidebar-nav { flex: 1; padding: 10px 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px; transition: 0.3s; border-left: 3px solid transparent; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: white; border-left-color: #f39c12; }
        .sidebar-footer { padding: 15px; border-top: 1px solid rgba(255,255,255,0.1); text-align: center; font-size: 10px; }
        .sidebar-footer a { color: rgba(255,255,255,0.7); text-decoration: none; }
        .main { margin-left: var(--sidebar-w); flex: 1; padding: 20px; }
        .topbar { background: var(--card); padding: 15px 25px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); flex-wrap: wrap; gap: 15px; border-bottom: 3px solid #1a237e; }
        .topbar .user-info { display: flex; align-items: center; gap: 10px; font-size: 13px; }
        .topbar .avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #1a237e, #3949ab); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; }
        .school-name-center { text-align: center; flex: 1; min-width: 200px; }
        .school-name-center h1 { font-size: 22px; color: #1a237e; margin: 0; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; background: linear-gradient(135deg, #1a237e, #5c6bc0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .school-name-center p { font-size: 11px; color: #888; margin: 3px 0 0; letter-spacing: 3px; text-transform: uppercase; font-style: italic; }
        .topbar .principal { display: flex; align-items: center; gap: 10px; font-size: 12px; color: #666; background: #f8f9fa; padding: 8px 15px; border-radius: 30px; }
        .topbar .principal img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #f39c12; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: var(--card); padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px; cursor: pointer; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-icon.s1 { background: #e8f5e9; color: #27ae60; } .stat-icon.s2 { background: #e3f2fd; color: #2196f3; }
        .stat-icon.s3 { background: #fff3e0; color: #ff9800; } .stat-icon.s4 { background: #fce4ec; color: #e91e63; }
        .stat-icon.s5 { background: #f3e5f5; color: #9c27b0; } .stat-info h2 { font-size: 22px; } .stat-info p { font-size: 11px; color: var(--subtext); text-transform: uppercase; }
        .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .card { background: var(--card); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 20px; }
        .card-header { padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { font-size: 14px; } .card-header a { font-size: 11px; color: var(--primary); text-decoration: none; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #f8f9fa; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; color: #888; }
        td { padding: 10px; border-bottom: 1px solid #f0f0f0; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .badge-paid { background: #d4edda; color: #155724; } .badge-pending { background: #fff3cd; color: #856404; }
        .quick-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; padding: 15px; }
        .quick-btn { text-align: center; padding: 15px 10px; border-radius: 10px; text-decoration: none; font-size: 11px; font-weight: bold; border: 2px solid var(--border); color: var(--text); transition: 0.3s; }
        .quick-btn:hover { border-color: var(--primary); color: var(--primary); } .quick-btn span { display: block; font-size: 22px; margin-bottom: 5px; }
        .notice-item { padding: 12px 20px; border-bottom: 1px solid var(--border); } .notice-item:last-child { border-bottom: none; }
        .notice-item strong { display: block; font-size: 12px; } .notice-item small { color: var(--subtext); font-size: 10px; }
        .notice-urgent { border-left: 3px solid #e74c3c; } .notice-event { border-left: 3px solid #f39c12; } .notice-general { border-left: 3px solid #3498db; }
        .menu-toggle { display: none; position: fixed; top: 10px; left: 10px; z-index: 200; background: var(--primary); color: white; border: none; width: 40px; height: 40px; border-radius: 8px; font-size: 20px; cursor: pointer; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: 0.3s; } .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; padding-top: 60px; } .menu-toggle { display: block; }
            .content-grid { grid-template-columns: 1fr; } .stats-grid { grid-template-columns: 1fr 1fr; }
            .quick-grid { grid-template-columns: 1fr 1fr; } .school-name-center h1 { font-size: 14px; }
        }
    </style>
</head>
<body>

<button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>

<nav class="sidebar">
    <div class="sidebar-header">
        <?php if(hasLogo()): ?><img src="<?php echo getLogoUrl(); ?>" class="sidebar-logo" alt="Logo">
        <?php else: ?><div class="sidebar-logo-placeholder"><?php echo getInitials(SCHOOL_NAME); ?></div><?php endif; ?>
        <h3><?php echo SCHOOL_SHORT; ?></h3><small>Management System</small>
    </div>
    <div class="sidebar-nav">
    <a href="../dashboard/" class="active">🏠 Dashboard</a>
    <a href="../modules/students/">👨‍🎓 Students</a>
    <a href="../modules/classes/">🚪 Classes</a>
    <a href="../modules/teachers/">👩‍🏫 Teachers</a>
    <a href="../modules/attendance/">📋 Attendance</a>
    <a href="../modules/fees/">💰 Fees</a>
    <a href="../modules/exams/">📝 Exams</a>
    <a href="../modules/notices/">📢 Notices</a>
    
    <!-- NEW: Users Management Link -->
    <a href="../modules/users/" style="border-top:1px solid rgba(255,255,255,0.2);margin-top:10px;padding-top:15px;">👥 Users</a>
</div>
  <a href="../modules/settings/" style="border-top:1px solid rgba(255,255,255,0.2);margin-top:10px;padding-top:15px;">⚙️ Settings</a>
    <div class="sidebar-footer">
        <small>&copy; <?php echo date('Y'); ?> <?php echo SCHOOL_ABBR; ?></small><br>
        <a href="<?php echo BASE_URL; ?>logout.php">🚪 Logout</a>
    </div>
  
</nav>

<div class="main">
    <div class="topbar">
        <div class="user-info">
            <div class="avatar"><?php echo strtoupper($_SESSION['username'][0]); ?></div>
            <div><strong><?php echo ucfirst($_SESSION['username']); ?></strong><small style="color:#888;display:block;"><?php echo ucfirst($_SESSION['role']); ?></small></div>
        </div>
        <div class="school-name-center"><h1><?php echo SCHOOL_NAME; ?></h1><p>"<?php echo SCHOOL_MOTTO; ?>"</p></div>
        <div class="principal">
            <?php if(hasPrincipalPhoto()): ?><img src="<?php echo getPrincipalPhoto(); ?>" alt="Principal">
            <?php else: ?><div style="width:40px;height:40px;border-radius:50%;background:#f39c12;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:16px;"><?php echo getInitials(PRINCIPAL_NAME); ?></div><?php endif; ?>
            <span>Principal<br><strong><?php echo PRINCIPAL_NAME; ?></strong></span>
        </div>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon s1">👨‍🎓</div><div class="stat-info"><h2><?php echo $total_students; ?></h2><p>Students</p></div></div>
        <div class="stat-card"><div class="stat-icon s2">👩‍🏫</div><div class="stat-info"><h2><?php echo $total_teachers; ?></h2><p>Teachers</p></div></div>
        <div class="stat-card"><div class="stat-icon s3">🚪</div><div class="stat-info"><h2><?php echo $total_classes; ?></h2><p>Classes</p></div></div>
        <div class="stat-card"><div class="stat-icon s4">💰</div><div class="stat-info"><h2>Rs. <?php echo number_format($total_revenue/1000); ?>K</h2><p>Revenue</p></div></div>
        <div class="stat-card"><div class="stat-icon s5">📋</div><div class="stat-info"><h2><?php echo $attendance_percent; ?>%</h2><p>Today Att.</p></div></div>
    </div>
    
    <div class="content-grid">
        <div>
            <div class="card">
                <div class="card-header"><h3>👨‍🎓 Recent Admissions</h3><a href="../modules/students/">View All &rarr;</a></div>
                <table><tr><th>Adm No</th><th>Name</th><th>Class</th><th>Phone</th></tr>
                    <?php while($s = $recent_students->fetch_assoc()): ?>
                        <tr><td><?php echo $s['admission_no']; ?></td><td><strong><?php echo $s['first_name'].' '.$s['last_name']; ?></strong></td><td><?php echo $s['class'].'-'.$s['section']; ?></td><td><?php echo $s['phone']; ?></td></tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div class="card">
                <div class="card-header"><h3>💵 Recent Payments</h3><a href="../modules/fees/">View All &rarr;</a></div>
                <table><tr><th>Student</th><th>Amount</th><th>Status</th></tr>
                    <?php while($f = $recent_fees->fetch_assoc()): ?>
                        <tr><td><?php echo $f['first_name'].' '.$f['last_name']; ?></td><td>Rs. <?php echo number_format($f['paid_amount']); ?></td><td><span class="badge badge-<?php echo $f['status']=='Paid'?'paid':'pending'; ?>"><?php echo $f['status']; ?></span></td></tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
        <div>
            <div class="card"><div class="card-header"><h3>⚡ Quick Actions</h3></div>
                <div class="quick-grid">
                    <a href="../modules/students/add.php" class="quick-btn"><span>➕</span>Add Student</a>
                    <a href="../modules/teachers/add.php" class="quick-btn"><span>👩‍🏫</span>Add Teacher</a>
                    <a href="../modules/attendance/" class="quick-btn"><span>📋</span>Attendance</a>
                    <a href="../modules/fees/collect.php" class="quick-btn"><span>💰</span>Collect Fee</a>
                    <a href="../modules/exams/create.php" class="quick-btn"><span>📝</span>New Exam</a>
                    <a href="../modules/notices/" class="quick-btn"><span>📢</span>Post Notice</a>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>📢 Notices</h3><a href="../modules/notices/">View All &rarr;</a></div>
                <?php while($n = $notices->fetch_assoc()): ?>
                    <div class="notice-item notice-<?php echo $n['category']; ?>"><strong><?php echo $n['title']; ?></strong><small><?php echo date('d M Y', strtotime($n['created_at'])); ?></small></div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>