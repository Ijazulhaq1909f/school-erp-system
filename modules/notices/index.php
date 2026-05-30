<?php
require_once '../../shared/config.php';
if (!isLoggedIn()) redirect(BASE_URL . 'login.php');

// Add notice
if (isset($_POST['add'])) {
    $conn->query("INSERT INTO notices (title, content, category, posted_by) VALUES ('{$_POST['title']}', '{$_POST['content']}', '{$_POST['category']}', {$_SESSION['user_id']})");
}

// Delete
if (isset($_GET['del'])) {
    $conn->query("DELETE FROM notices WHERE id={$_GET['del']}");
}

$cat = $_GET['cat'] ?? '';
$where = $cat ? "WHERE category='$cat'" : "";
$notices = $conn->query("SELECT * FROM notices $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notice Board - <?php echo SCHOOL_SHORT; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 15px; }
        .header { background: #1a237e; color: white; padding: 15px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .header a { color: white; text-decoration: none; }
        .grid { display: grid; grid-template-columns: 300px 1fr; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; }
        .notice { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #3498db; }
        .notice.urgent { border-left-color: #e74c3c; background: #fffafa; }
        .notice.event { border-left-color: #f39c12; }
        .filter a { padding: 5px 12px; border-radius: 15px; text-decoration: none; font-size: 12px; margin: 3px; }
        .filter a.active { background: #1a237e; color: white; }
        .filter a { background: #e0e0e0; color: #333; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 8px; }
        button { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="header">
    <h2>📢 Notice Board</h2>
    <a href="../../dashboard/">🏠 Home</a>
</div>

<div class="grid">
    <div>
        <div class="card">
            <h3>📝 Post Notice</h3>
            <form method="POST">
                <input name="title" placeholder="Title" required>
                <select name="category"><option value="general">🔵 General</option><option value="urgent">🔴 Urgent</option><option value="event">🟡 Event</option></select>
                <textarea name="content" rows="3" placeholder="Content..." required></textarea>
                <button type="submit" name="add">📢 Post</button>
            </form>
        </div>
        <div class="filter">
            <a href="./" class="<?php echo !$cat?'active':''; ?>">All</a>
            <a href="?cat=urgent" class="<?php echo $cat=='urgent'?'active':''; ?>">🔴 Urgent</a>
            <a href="?cat=event" class="<?php echo $cat=='event'?'active':''; ?>">🟡 Events</a>
            <a href="?cat=general" class="<?php echo $cat=='general'?'active':''; ?>">🔵 General</a>
        </div>
    </div>
    <div>
        <?php while($n = $notices->fetch_assoc()): ?>
            <div class="notice <?php echo $n['category']; ?>">
                <strong><?php echo $n['title']; ?></strong>
                <small style="color:#888;"> | <?php echo date('d M Y', strtotime($n['created_at'])); ?></small>
                <p><?php echo $n['content']; ?></p>
                <a href="?del=<?php echo $n['id']; ?>" style="color:red;font-size:11px;" onclick="return confirm('Delete?')">🗑️ Delete</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>