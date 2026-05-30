<?php
// ============================================
// AL-SAMAD E.G.H SCHOOL ERP - CONFIG
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_erp');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);
$conn->set_charset("utf8");

// Session
if (session_status() == PHP_SESSION_NONE) session_start();

// Base URL
$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . $host . '/school-erp/');

// Paths
define('ROOT_DIR', __DIR__ . '/../');
define('UPLOAD_DIR', ROOT_DIR . 'assets/uploads/');
define('PHOTO_DIR', UPLOAD_DIR . 'photos/');
define('LOGO_PATH', ROOT_DIR . 'assets/images/logo.png');

// Create directories
if (!file_exists(PHOTO_DIR)) mkdir(PHOTO_DIR, 0777, true);

// ============================================
// LOAD SCHOOL SETTINGS
// ============================================
$settings = [];
$result = $conn->query("SELECT * FROM school_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

define('SCHOOL_NAME', $settings['school_name'] ?? 'Al-Samad E.G.H School');
define('SCHOOL_SHORT', $settings['school_short'] ?? 'Al-Samad E.G.H School');
define('SCHOOL_ABBR', $settings['school_abbr'] ?? 'E.G.H.S');
define('SCHOOL_ADDRESS', $settings['school_address'] ?? 'Baldia, Karachi');
define('SCHOOL_PHONE', $settings['school_phone'] ?? '03482343335');
define('SCHOOL_WHATSAPP', $settings['school_whatsapp'] ?? '03453207748');
define('SCHOOL_EMAIL', $settings['school_email'] ?? 'alsamadeghs@gmail.com');
define('PRINCIPAL_NAME', $settings['principal_name'] ?? 'Ijaz ul Haq Khan');
define('SCHOOL_MOTTO', $settings['school_motto'] ?? 'Education for Excellence');

// ============================================
// FUNCTIONS
// ============================================

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return ($_SESSION['role'] ?? '') == 'admin';
}

function isTeacher() {
    return ($_SESSION['role'] ?? '') == 'teacher';
}

function isParent() {
    return ($_SESSION['role'] ?? '') == 'parent';
}

// ============================================
// PHOTO FUNCTIONS
// ============================================

function getPhotoUrl($photo, $type = 'student') {
    $default = BASE_URL . 'assets/uploads/photos/default.png';
    if (empty($photo) || $photo == 'default.png') return $default;
    $path = PHOTO_DIR . $photo;
    return file_exists($path) ? BASE_URL . 'assets/uploads/photos/' . $photo : $default;
}

function getLogoUrl() {
    return file_exists(LOGO_PATH) ? BASE_URL . 'assets/images/logo.png' : '';
}

function getLogoBase64() {
    if (file_exists(LOGO_PATH)) {
        $type = pathinfo(LOGO_PATH, PATHINFO_EXTENSION);
        return 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents(LOGO_PATH));
    }
    return '';
}

function hasLogo() {
    return file_exists(LOGO_PATH);
}

function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $w) if (!empty($w)) $initials .= strtoupper($w[0]);
    return substr($initials, 0, 2);
}

// ============================================
// GRADE CALCULATION
// ============================================

function calculateGrade($percentage) {
    if ($percentage >= 90) return ['grade' => 'A+', 'remark' => 'Outstanding'];
    if ($percentage >= 80) return ['grade' => 'A', 'remark' => 'Excellent'];
    if ($percentage >= 70) return ['grade' => 'B', 'remark' => 'Very Good'];
    if ($percentage >= 60) return ['grade' => 'C', 'remark' => 'Good'];
    if ($percentage >= 50) return ['grade' => 'D', 'remark' => 'Satisfactory'];
    if ($percentage >= 40) return ['grade' => 'E', 'remark' => 'Needs Improvement'];
    return ['grade' => 'F', 'remark' => 'Fail'];
}

// ============================================
// STATS FUNCTIONS
// ============================================

function getCount($table, $where = '1=1') {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as c FROM $table WHERE $where");
    return $result ? $result->fetch_assoc()['c'] : 0;
}

date_default_timezone_set('Asia/Karachi');

// Principal Photo
define('PRINCIPAL_PHOTO', ROOT_DIR . 'assets/images/principal.png');

function getPrincipalPhoto() {
    if (file_exists(PRINCIPAL_PHOTO)) {
        return BASE_URL . 'assets/images/principal.png';
    }
    return ''; // No photo, initials show honge
}

function hasPrincipalPhoto() {
    return file_exists(PRINCIPAL_PHOTO);
}

?>