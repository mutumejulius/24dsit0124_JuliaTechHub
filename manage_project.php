<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// --- 1. HANDLE STATUS, FEATURE & STAGE UPDATES ---
if (isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $new_stage = mysqli_real_escape_string($conn, $_POST['stage']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Updated query to include 'stage'
    $update_sql = "UPDATE innovations SET status = '$new_status', stage = '$new_stage', is_featured = '$is_featured' WHERE id = '$id'";
    
    if ($conn->query($update_sql)) {
        header("Location: manage_project.php?id=$id&msg=updated");
        exit();
    }
}

// --- 2. HANDLE INTERNAL MESSAGE SENDING ---
if (isset($_POST['send_internal_msg'])) {
    $receiver_id = mysqli_real_escape_string($conn, $_POST['receiver_id']);
    $raw_message = mysqli_real_escape_string($conn, $_POST['message_text']);
    $raw_title = mysqli_real_escape_string($conn, $_POST['project_title']);
    
    $subject_text = "Admin Feedback on: " . $raw_title;
    
    $sql = "INSERT INTO notifications (user_id, message, reply_message, type, is_read, created_at, replied_at) 
            VALUES ('$receiver_id', '$subject_text', '$raw_message', 'admin_msg', 0, NOW(), NOW())";
            
    if ($conn->query($sql)) {
        header("Location: manage_project.php?id=$id&msg=feedback_sent");
        exit();
    }
}

// Handle success alerts
$status_msg = "";
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 'updated') $status_msg = "Project settings and Stage updated!";
    if($_GET['msg'] == 'feedback_sent') $status_msg = "Feedback sent successfully!";
}

$query = $conn->query("SELECT i.*, u.full_name, u.email, u.id as founder_id FROM innovations i 
                       JOIN users u ON i.user_id = u.id WHERE i.id = '$id'");
$project = $query->fetch_assoc();

$project_title_escaped = mysqli_real_escape_string($conn, "Admin Feedback on: " . ($project['title'] ?? ''));
$history_query = $conn->query("SELECT * FROM notifications 
                               WHERE user_id = '{$project['founder_id']}' 
                               AND message = '$project_title_escaped' 
                               ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage | <?php echo htmlspecialchars($project['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; padding: 40px; font-family: 'Segoe UI', sans-serif; }
        .detail-card { background: white; padding: 40px; border-radius: 12px; max-width: 850px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .label { font-weight: bold; color: #800000; text-transform: uppercase; font-size: 0.75rem; display: block; margin-top: 25px; letter-spacing: 1px; }
        .action-panel { background: #fffcf0; padding: 25px; border-radius: 8px; border: 1px solid #ffeaa7; margin-top: 30px; }
        .btn-status { background: #2d3436; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .btn-send { background: #800000; color: white; border: none; padding: 14px 28px; border-radius: 6px; cursor: pointer; width: 100%; margin-top: 15px; font-weight: bold; }
        .success-alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #28a745; }
        .history-item { background: #fcfcfc; border: 1px solid #eee; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="detail-card">
        <a href="admin_dashboard.php" style="text-decoration:none; color:#999;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        
        <?php if(!empty($status_msg)): ?>
            <div class="success-alert"><i class="fas fa-check-circle"></i> <?php echo $status_msg; ?></div>
        <?php endif; ?>

        <h1 style="margin-top:20px; color: #2d3436;"><?php echo htmlspecialchars($project['title']); ?></h1>
        
        <div class="action-panel">
            <h3><i class="fas fa-gavel"></i> Administrative Decision</h3>
            <form method="POST" style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 30px; align-items: flex-end;">
                <div>
                    <label class="label" style="margin:0;">Account Status</label>
                    <select name="status" style="padding: 10px; border-radius: 5px; width: 150px;">
                        <option value="pending" <?php if($project['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="approved" <?php if($project['status'] == 'approved') echo 'selected'; ?>>Approve</option>
                        <option value="rejected" <?php if($project['status'] == 'rejected') echo 'selected'; ?>>Reject</option>
                    </select>
                </div>

                <div>
                    <label class="label" style="margin:0;">Startup Stage (Tracker)</label>
                    <select name="stage" style="padding: 10px; border-radius: 5px; width: 180px;">
                        <option value="Ideation" <?php if($project['stage'] == 'Ideation') echo 'selected'; ?>>Ideation</option>
                        <option value="MVP" <?php if($project['stage'] == 'MVP') echo 'selected'; ?>>MVP</option>
                        <option value="Market Ready" <?php if($project['stage'] == 'Market Ready') echo 'selected'; ?>>Market Ready</option>
                        <option value="Scaling" <?php if($project['stage'] == 'Scaling') echo 'selected'; ?>>Scaling</option>
                    </select>
                </div>
                
                <div style="display:flex; align-items:center; gap: 10px;">
                    <input type="checkbox" name="is_featured" value="1" id="feat" <?php if($project['is_featured']) echo 'checked'; ?>>
                    <label for="feat" style="font-weight:bold; color:#800000; font-size: 0.8rem;">Feature in Hall of Fame</label>
                </div>

                <button type="submit" name="update_status" class="btn-status">Update Project</button>
            </form>
        </div>

        <span class="label">Project Description</span>
        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 10px; border-left: 4px solid #800000;">
            <?php echo nl2br(htmlspecialchars($project['short_description'])); ?>
        </div>

        <div style="margin-top: 30px; padding-top: 30px; border-top: 2px dashed #eee;">
            <h3><i class="fas fa-comment-dots"></i> Send Feedback</h3>
            <form method="POST">
                <input type="hidden" name="receiver_id" value="<?php echo $project['founder_id']; ?>">
                <input type="hidden" name="project_title" value="<?php echo htmlspecialchars($project['title']); ?>">
                <textarea name="message_text" rows="4" placeholder="Advise the founder..." style="width:100%; padding:15px; border-radius:8px; border:1px solid #ddd; margin-top:10px;" required></textarea>
                <button type="submit" name="send_internal_msg" class="btn-send">Submit Feedback</button>
            </form>
        </div>

        <div style="margin-top: 40px;">
            <h3><i class="fas fa-history"></i> Feedback History</h3>
            <?php if ($history_query->num_rows > 0): ?>
                <?php while($h = $history_query->fetch_assoc()): ?>
                    <div class="history-item">
                        <small style="color:#999;"><?php echo date('M d, Y', strtotime($h['created_at'])); ?></small>
                        <p><?php echo nl2br(htmlspecialchars($h['reply_message'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color:#999; font-style:italic;">No feedback sent yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>