<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// --- 1. HANDLE STATUS & FEATURE UPDATES ---
if (isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $update_sql = "UPDATE innovations SET status = '$new_status', is_featured = '$is_featured' WHERE id = '$id'";
    
    if ($conn->query($update_sql)) {
        $status_msg = "Project status updated successfully!";
    } else {
        $error_log = "Error updating status: " . $conn->error;
    }
}

// --- 2. HANDLE INTERNAL MESSAGE SENDING ---
if (isset($_POST['send_internal_msg'])) {
    $receiver_id = mysqli_real_escape_string($conn, $_POST['receiver_id']);
    $raw_message = $_POST['message_text'];
    $raw_title = $_POST['project_title'];
    $combined_text = "Admin Feedback on '$raw_title': " . $raw_message;
    $full_msg = mysqli_real_escape_string($conn, $combined_text);
    
    $sql = "INSERT INTO notifications (user_id, message, type, is_read, created_at) 
            VALUES ('$receiver_id', '$full_msg', 'admin_msg', 0, NOW())";
            
    if ($conn->query($sql)) {
        $status_msg = "Feedback sent to innovator dashboard!";
    } else {
        $error_log = "Message Error: " . $conn->error;
    }
}

// Fetch refreshed project details
$query = $conn->query("SELECT i.*, u.full_name, u.email, u.id as founder_id FROM innovations i 
                       JOIN users u ON i.user_id = u.id WHERE i.id = '$id'");
$project = $query->fetch_assoc();
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
        .desc-box { background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid #800000; margin-top: 10px; line-height: 1.6; }
        .action-panel { background: #fffcf0; padding: 25px; border-radius: 8px; border: 1px solid #ffeaa7; margin-top: 30px; }
        .compose-box { margin-top: 30px; padding-top: 30px; border-top: 2px dashed #eee; }
        textarea { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-top: 10px; resize: vertical; }
        .btn-status { background: #2d3436; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .btn-send { background: #800000; color: white; border: none; padding: 14px 28px; border-radius: 6px; cursor: pointer; width: 100%; margin-top: 15px; }
        .success-alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="detail-card">
        <a href="admin_dashboard.php" style="text-decoration:none; color:#999;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        
        <?php if(isset($status_msg)): ?>
            <div class="success-alert"><i class="fas fa-check-circle"></i> <?php echo $status_msg; ?></div>
        <?php endif; ?>

        <h1 style="margin-top:20px; color: #2d3436;"><?php echo htmlspecialchars($project['title']); ?></h1>
        
        <span class="label">Founder</span>
        <p><?php echo htmlspecialchars($project['full_name']); ?> (<?php echo htmlspecialchars($project['email']); ?>)</p>

        <div class="action-panel">
            <h3><i class="fas fa-gavel"></i> Administrative Decision</h3>
            <form method="POST" style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center;">
                <div>
                    <label class="label" style="margin:0;">Project Status</label>
                    <select name="status" style="padding: 10px; border-radius: 5px; margin-top: 5px;">
                        <option value="pending" <?php if($project['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="approved" <?php if($project['status'] == 'approved') echo 'selected'; ?>>Approve</option>
                        <option value="rejected" <?php if($project['status'] == 'rejected') echo 'selected'; ?>>Reject</option>
                    </select>
                </div>
                
                <div style="display:flex; align-items:center; gap: 10px; margin-top: 20px;">
                    <input type="checkbox" name="is_featured" value="1" id="feat" <?php if($project['is_featured']) echo 'checked'; ?>>
                    <label for="feat" style="font-weight:bold; color:var(--maroon);">Show in Hall of Fame</label>
                </div>

                <button type="submit" name="update_status" class="btn-status" style="margin-top: 20px;">Save Changes</button>
            </form>
        </div>

        <span class="label">Project Description</span>
        <div class="desc-box"><?php echo nl2br(htmlspecialchars($project['short_description'])); ?></div>

        <div class="compose-box">
            <h3><i class="fas fa-comment-dots"></i> Send Feedback</h3>
            <form method="POST">
                <input type="hidden" name="receiver_id" value="<?php echo $project['founder_id']; ?>">
                <input type="hidden" name="project_title" value="<?php echo htmlspecialchars($project['title']); ?>">
                <textarea name="message_text" rows="4" placeholder="Advise the founder on next steps..." required></textarea>
                <button type="submit" name="send_internal_msg" class="btn-send">
                    <i class="fas fa-paper-plane"></i> Submit Feedback
                </button>
            </form>
        </div>
    </div>
</body>
</html>