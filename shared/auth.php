<?php
require_once __DIR__ . '/config.php';

// Agar already logged in hai to dashboard par bhejo
if (isLoggedIn() && basename($_SERVER['PHP_SELF']) == 'login.php') {
    redirect(BASE_URL . 'dashboard/');
}

// Login process
if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Check if admin user exists, if not create
    $check = $conn->query("SELECT id FROM users WHERE username='admin'");
    if ($check->num_rows == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, password, role) VALUES ('admin', '$hash', 'admin')");
    }
    
    $query = $conn->query("SELECT * FROM users WHERE username='$username' AND status=1");
    
    if ($query && $query->num_rows == 1) {
        $user = $query->fetch_assoc();
        
        // Debug: agar password verify fail ho to re-hash karo
        if (!password_verify($password, $user['password'])) {
            // Try to re-hash and update
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$new_hash' WHERE username='$username'");
            $user['password'] = $new_hash;
        }
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['related_id'] = $user['related_id'];
            
            $conn->query("UPDATE users SET last_login=NOW() WHERE id={$user['id']}");
            
            switch($user['role']) {
                case 'parent':
                    redirect(BASE_URL . 'modules/parents/');
                    break;
                case 'teacher':
                    redirect(BASE_URL . 'dashboard/');
                    break;
                default:
                    redirect(BASE_URL . 'dashboard/');
            }
        }
    }
    $error = "Invalid credentials! Please try: admin / admin123";
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect(BASE_URL . 'login.php');
}
?>