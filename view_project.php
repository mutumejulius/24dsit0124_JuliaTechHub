<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// --- HANDLE INTERNAL MESSAGE SENDING ---
if (isset($_POST['send_internal_msg'])) {
    // 1. Escape the raw inputs first
    $receiver_id = mysqli_real_escape_string($conn, $_POST['receiver_id']);
    $raw_message = $_POST['message_text']; // Get raw text to combine first
    $raw_title = $_POST['project_title'];
    
    // 2. Build the combined message string
    $combined_text = "Admin Feedback on '$raw_title': " . $raw_message;
    
    // 3. Escape the FINAL combined string before putting it in the query
    $full_msg = mysqli_real_escape_string($conn, $combined_text);
    
    $sql = "INSERT INTO notifications (user_id, message, type, is_read, created_at) 
            VALUES ('$receiver_id', '$full_msg', 'admin_msg', 0, NOW())";
            
    if ($conn->query($sql)) {
        $status_msg = "Reply sent to innovator dashboard!";
    } else {
        $error_log = "Database Error: " . $conn->error;
    }
}

// Fetch details using correct database columns: short_description and long_description
$query = $conn->query("SELECT i.*, u.full_name, u.email, u.id as founder_id FROM innovations i 
                       JOIN users u ON i.user_id = u.id 
                       WHERE i.id = '$id'");
$project = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Project | <?php echo htmlspecialchars($project['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; padding: 40px; font-family: 'Segoe UI', sans-serif; }
        .detail-card { background: white; padding: 40px; border-radius: 12px; max-width: 850px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .label { font-weight: bold; color: #800000; text-transform: uppercase; font-size: 0.75rem; display: block; margin-top: 25px; letter-spacing: 1px; }
        .desc-box { background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid #800000; margin-top: 10px; line-height: 1.6; }
        .compose-box { margin-top: 40px; padding-top: 30px; border-top: 2px dashed #eee; }
        textarea { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-top: 10px; resize: vertical; font-family: inherit; }
        .success-alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .error-alert { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .btn-send { background: #800000; color: white; border: none; padding: 14px 28px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: 600; margin-top: 15px; transition: 0.3s; }
        .btn-send:hover { background: #a00000; }
    </style>
</head>
<body>
    <div class="detail-card">
        <a href="admin_dashboard.php" style="text-decoration:none; color:#999;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        
        <?php if(isset($status_msg)): ?>
            <div class="success-alert"><i class="fas fa-check-circle"></i> <?php echo $status_msg; ?></div>
        <?php endif; ?>
        <?php if(isset($error_log)): ?>
            <div class="error-alert"><i class="fas fa-exclamation-triangle"></i> <?php echo $error_log; ?></div>
        <?php endif; ?>

        <h1 style="margin-top:20px; color: #2d3436;"><?php echo htmlspecialchars($project['title']); ?></h1>
        <div style="width: 50px; height: 3px; background: #800000; margin-bottom: 20px;"></div>
        
        <span class="label">Founder Information</span>
        <p><?php echo htmlspecialchars($project['full_name']); ?> (<?php echo htmlspecialchars($project['email']); ?>)</p>

        <span class="label">Industry & Stage</span>
        <p><strong><?php echo htmlspecialchars($project['industry']); ?></strong> — <?php echo htmlspecialchars($project['stage']); ?></p>

        <span class="label">Quick Summary</span>
        <div class="desc-box">
            <?php echo nl2br(htmlspecialchars($project['short_description'])); ?>
        </div>

        <?php if(!empty($project['long_description'])): ?>
            <span class="label">Full Details</span>
            <div class="desc-box" style="border-left-color: #d1d1d1;">
                <?php echo nl2br(htmlspecialchars($project['long_description'])); ?>
            </div>
        <?php endif; ?>

        <div class="compose-box">
            <h3><i class="fas fa-comment-dots" style="color: #800000;"></i> Send Advice to Founder</h3>
            <form method="POST">
                <input type="hidden" name="receiver_id" value="<?php echo $project['founder_id']; ?>">
                <input type="hidden" name="project_title" value="<?php echo htmlspecialchars($project['title']); ?>">
                <textarea name="message_text" rows="5" placeholder="Enter your response here..." required></textarea>
                <button type="submit" name="send_internal_msg" class="btn-send">
                    <i class="fas fa-paper-plane"></i> Send to Dashboard
                </button>
            </form>
        </div>
    </div>
</body>
</html>