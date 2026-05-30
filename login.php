<?php
require_once 'shared/config.php';
require_once 'shared/auth.php';

if (isLoggedIn()) {
    redirect(BASE_URL . 'dashboard/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SCHOOL_SHORT; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1a237e, #3949ab, #5c6bc0);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .login-box {
            background: white; padding: 35px 30px; border-radius: 20px;
            width: 100%; max-width: 420px; text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #1a237e, #3949ab);
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 15px;
            color: white; font-size: 32px; font-weight: bold;
            border: 4px solid #e8eaf6;
        }
        .login-logo img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        h2 { color: #1a237e; font-size: 20px; margin-bottom: 3px; }
        p { color: #888; font-size: 12px; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; margin-bottom: 12px; }
        input:focus { outline: none; border-color: #1a237e; }
        .btn-login { width: 100%; padding: 13px; background: #1a237e; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; }
        .btn-login:hover { background: #283593; }
        .error { background: #fde8e8; color: #c0392b; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; }
        .demo { margin-top: 15px; font-size: 11px; color: #999; background: #f8f9fa; padding: 10px; border-radius: 8px; }
        .demo strong { color: #1a237e; display: block; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <?php if(hasLogo()): ?>
                <img src="<?php echo getLogoUrl(); ?>" alt="Logo">
            <?php else: ?>
                <?php echo getInitials(SCHOOL_NAME); ?>
            <?php endif; ?>
        </div>
        <h2><?php echo SCHOOL_SHORT; ?></h2>
        <p>Management System</p>
        
        <?php if(isset($error)): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required value="admin">
            <input type="password" name="password" placeholder="Password" required value="admin123">
            <button type="submit" name="login" class="btn-login">🔓 Login</button>
        </form>
        
        <div class="demo">
            <strong>Demo Credentials:</strong>
            Admin: admin / admin123<br>
            Teacher: teacher1 / admin123<br>
            Parent: parent1 / admin123
        </div>
    </div>
</body>
</html>