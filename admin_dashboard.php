<?php
include 'db.php';
session_start();

// Security: Only allow Admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Handle Broadcast Logic
if (isset($_POST['send_broadcast'])) {
    $msg = mysqli_real_escape_string($conn, $_POST['broadcast_msg']);
    if (!empty($msg)) {
        $conn->query("INSERT INTO broadcasts (message) VALUES ('$msg')");
        $broadcast_success = true;
    }
}

// 2. Handle Feature Toggle
if (isset($_GET['toggle_feature']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $current_status = $_GET['toggle_feature'];
    $new_status = ($current_status == 1) ? 0 : 1;
    
    $conn->query("UPDATE innovations SET is_featured = '$new_status' WHERE id = '$id'");
    header("Location: admin_dashboard.php"); 
    exit();
}

// 3. Handle Delete Logic
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $conn->query("DELETE FROM innovations WHERE id = '$id'");
    header("Location: admin_dashboard.php?msg=deleted");
    exit();
}

// 4. Fetch Counts & Notifications
$unread_query = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE type='mentor_query' AND is_read=0");
$unread_count = $unread_query->fetch_assoc()['total'];

$approved_res = $conn->query("SELECT COUNT(*) as total FROM innovations WHERE status = 'approved'");
$approved_count = $approved_res->fetch_assoc()['total'];

$pending_res = $conn->query("SELECT COUNT(*) as total FROM innovations WHERE status = 'pending'");
$pending_count = $pending_res->fetch_assoc()['total'];

$total_innovators = $conn->query("SELECT id FROM users WHERE role='innovator'")->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { display: flex; min-height: 100vh; background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 260px; background: #2d3436; color: white; padding: 20px; position: fixed; height: 100%; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
        .nav-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .nav-links a { color: #bdc3c7; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; transition: 0.3s; position: relative; }
        .nav-links a:hover, .nav-links a.active { color: white; background: rgba(255,255,255,0.05); border-radius: 5px; }
        .nav-links a.active { border-left: 4px solid var(--maroon); }
        
        .badge { background: #e74c3c; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 50%; position: absolute; right: 10px; }
        .main-content { margin-left: 260px; flex: 1; padding: 40px; }
        
        /* Stats Grid */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; }
        
        /* Table Styles */
        .admin-table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .admin-table th { background: var(--maroon); color: white; padding: 15px; text-align: left; }
        .admin-table td { padding: 15px; border-bottom: 1px solid #eee; }
        
        .status-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; font-weight: bold; text-transform: uppercase; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-rejected { background: #f8d7da; color: #721c24; }

        .btn-feature { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; transition: 0.3s; border: 1px solid #ddd; }
        .is-featured { background: var(--maroon); color: white; border-color: var(--maroon); }
        
        .broadcast-box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 5px solid var(--maroon); }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>HUB ADMIN</h2>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="admin_inbox.php"><i class="fas fa-envelope"></i> Support Inbox <?php if($unread_count > 0) echo "<span class='badge'>$unread_count</span>"; ?></a></li>
            <li><a href="admin_training.php"><i class="fas fa-graduation-cap"></i> Training CMS</a></li>
            <li><a href="logout.php" style="margin-top: 50px; color: #ff7675;"><i class="fas fa-power-off"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Ecosystem Overview</h1>
        <p style="color: #666; margin-bottom: 30px;">Manage project approvals and visibility.</p>

        <div class="stat-grid">
            <div class="stat-card" style="border-top: 4px solid #27ae60;">
                <small>Approved Projects</small>
                <h2><?php echo $approved_count; ?></h2>
            </div>
            <div class="stat-card" style="border-top: 4px solid #f39c12;">
                <small>Pending Review</small>
                <h2><?php echo $pending_count; ?></h2>
            </div>
            <div class="stat-card" style="border-top: 4px solid var(--maroon);">
                <small>Total Innovators</small>
                <h2><?php echo $total_innovators; ?></h2>
            </div>
            <div class="stat-card" style="border-top: 4px solid #3498db;">
                <small>Unread Queries</small>
                <h2><?php echo $unread_count; ?></h2>
            </div>
        </div>

        <div class="broadcast-box">
            <h3><i class="fas fa-bullhorn"></i> Hub Announcement</h3>
            <form method="POST" style="display: flex; gap: 10px; margin-top: 10px;">
                <input type="text" name="broadcast_msg" style="flex:1; padding:10px; border-radius:5px; border:1px solid #ddd;" placeholder="What's happening in the hub?" required>
                <button type="submit" name="send_broadcast" class="btn-hero" style="border:none; cursor:pointer;">Broadcast</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Startup & Industry</th>
                    <th>Founder</th>
                    <th>Status</th>
                    <th>Hall of Fame</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT i.*, u.full_name FROM innovations i JOIN users u ON i.user_id = u.id ORDER BY i.id DESC");
                while($row = $result->fetch_assoc()):
                    $status_class = 'status-' . $row['status'];
                    $is_f = $row['is_featured'];
                ?>
                <tr>
                    <td><b><?php echo $row['title']; ?></b><br><small><?php echo $row['industry']; ?></small></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $row['status']; ?></span></td>
                    <td>
                        <a href="admin_dashboard.php?toggle_feature=<?php echo $is_f; ?>&id=<?php echo $row['id']; ?>" 
                           class="btn-feature <?php echo ($is_f) ? 'is-featured' : ''; ?>">
                           <?php echo ($is_f) ? '<i class="fas fa-star"></i> Featured' : '<i class="far fa-star"></i> Feature'; ?>
                        </a>
                    </td>
                    <td>
                        <a href="manage_project.php?id=<?php echo $row['id']; ?>" style="color: var(--maroon); margin-right: 15px;"><i class="fas fa-edit"></i> Review</a>
                        <a href="admin_dashboard.php?delete_id=<?php echo $row['id']; ?>" style="color: #666;" onclick="return confirm('Delete permanently?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>