<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// --- HANDLE INTERNAL REPLY ---
if (isset($_POST['send_internal_reply'])) {
    $notif_id = $_POST['notif_id'];
    $reply_text = mysqli_real_escape_string($conn, $_POST['reply_text']);
    
    $sql = "UPDATE notifications SET 
            reply_message = '$reply_text', 
            replied_at = NOW(), 
            is_read = 1 
            WHERE id = '$notif_id'";
            
    if ($conn->query($sql)) {
        $msg_status = "Reply sent to innovator dashboard!";
    }
}

$queries = $conn->query("SELECT n.*, u.full_name FROM notifications n 
                         JOIN users u ON n.user_id = u.id 
                         WHERE n.type = 'mentor_query' 
                         ORDER BY n.replied_at IS NULL DESC, n.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Inbox | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background:#f4f7f6; padding:40px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 900px; margin: 0 auto; }
        
        /* Header Styling */
        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .back-link { color: var(--maroon); text-decoration: none; font-weight: bold; transition: 0.3s; }
        .back-link:hover { opacity: 0.7; }

        /* Success Message Styling */
        .alert-success { 
            background: #d4edda; color: #155724; padding: 15px 25px; border-radius: 8px; 
            margin-bottom: 25px; border-left: 5px solid #28a745; 
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .msg-card { background:white; padding:20px; border-radius:10px; margin-bottom:15px; border-left:5px solid var(--maroon); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .replied { border-left-color: #2ecc71; opacity: 0.9; }
        .reply-box { background:#f9f9f9; padding:15px; border-radius:5px; margin-top:10px; border:1px dashed #ddd; }
        
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index: 1000; }
        .modal-content { background:white; padding:30px; border-radius:10px; width:400px; position: relative; }
        
        textarea { width:100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; margin-top: 10px; font-family: inherit; resize: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-area">
        <h1><i class="fas fa-inbox"></i> Mentor Inquiries</h1>
        <a href="admin_dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <?php if(isset($msg_status)): ?>
        <div class="alert-success">
            <div><i class="fas fa-check-circle"></i> <?php echo $msg_status; ?></div>
            <a href="admin_dashboard.php" class="btn-hero" style="padding: 8px 15px; font-size: 0.8rem; text-decoration: none; background: #28a745; border:none;">Return Home</a>
        </div>
    <?php endif; ?>

    <?php if ($queries->num_rows > 0): ?>
        <?php while($row = $queries->fetch_assoc()): ?>
            <div class="msg-card <?php echo $row['reply_message'] ? 'replied' : ''; ?>">
                <strong>From: <?php echo htmlspecialchars($row['full_name']); ?></strong>
                <p style="margin-top: 10px; color: #444;"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                
                <?php if($row['reply_message']): ?>
                    <div class="reply-box">
                        <small style="color: #2ecc71;"><strong><i class="fas fa-reply"></i> Your Dashboard Reply:</strong></small>
                        <p style="margin-top: 5px;"><?php echo nl2br(htmlspecialchars($row['reply_message'])); ?></p>
                    </div>
                <?php else: ?>
                    <button onclick="openReply(<?php echo $row['id']; ?>)" class="btn-hero" style="padding:8px 18px; font-size:0.8rem; border:none; cursor:pointer; margin-top: 10px;">
                        <i class="fas fa-paper-plane"></i> Reply to Dashboard
                    </button>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; color: #888; margin-top: 50px;">No inquiries found.</p>
    <?php endif; ?>

    <div id="replyModal" class="modal">
        <div class="modal-content">
            <h3>Send Advice</h3>
            <p style="font-size: 0.8rem; color: #666;">This message will appear on the innovator's dashboard.</p>
            <form method="POST">
                <input type="hidden" name="notif_id" id="modal_notif_id">
                <textarea name="reply_text" rows="5" placeholder="Type your response..." required></textarea>
                <button type="submit" name="send_internal_reply" class="btn-hero" style="width:100%; margin-top:15px; border:none; padding: 12px;">Submit Reply</button>
                <button type="button" onclick="closeReply()" style="width:100%; margin-top:10px; background:none; border:none; color:gray; cursor:pointer;">Cancel</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openReply(id) { document.getElementById('modal_notif_id').value = id; document.getElementById('replyModal').style.display='flex'; }
    function closeReply() { document.getElementById('replyModal').style.display='none'; }
    
    // Close modal on outside click
    window.onclick = function(event) {
        let modal = document.getElementById('replyModal');
        if (event.target == modal) { closeReply(); }
    }
</script>

</body>
</html>