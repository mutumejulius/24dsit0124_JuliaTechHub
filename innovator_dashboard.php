<?php
include 'db.php';
session_start();

// Security: If not logged in or not an innovator, kick them back to login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'innovator') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- HANDLE MENTOR MESSAGE SUBMISSION (With Redirect to prevent duplicates) ---
if (isset($_POST['send_mentor_msg'])) {
    $msg_content = mysqli_real_escape_string($conn, $_POST['mentor_query']);
    $sender_name = mysqli_real_escape_string($conn, $_SESSION['user_name']);
    
    $sql_msg = "INSERT INTO notifications (user_id, message, type, is_read, created_at) 
                VALUES ('$user_id', 'Support Request from $sender_name: $msg_content', 'mentor_query', 0, NOW())";
    
    if ($conn->query($sql_msg)) {
        // Redirect back to same page with a success parameter
        header("Location: innovator_dashboard.php?msg=sent");
        exit();
    }
}

// Check for success message from redirect
if (isset($_GET['msg']) && $_GET['msg'] == 'sent') {
    $msg_sent = "Your message has been sent to the Hub mentors!";
}

// Fetch User's Innovation Details
$sql = "SELECT * FROM innovations WHERE user_id = '$user_id' LIMIT 1";
$result = $conn->query($sql);
$innovation = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --maroon: #800000; }
        body { display: flex; min-height: 100vh; background: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; }
        .sidebar { width: 260px; background: var(--maroon); color: white; padding: 20px; position: fixed; height: 100%; box-sizing: border-box; }
        .sidebar h2 { font-size: 1.2rem; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; text-align: center; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 5px; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.2); }
        .main-content { margin-left: 260px; flex: 1; padding: 40px; box-sizing: border-box; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-top: 4px solid var(--maroon); }
        .stat-card small { color: #888; text-transform: uppercase; font-size: 0.7rem; font-weight: bold; }
        .progress-container { background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:center; }
        .modal-content { background:white; padding:30px; border-radius:12px; width:450px; position:relative; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .btn-hero { background: var(--maroon); color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-hero:hover { background: #a00000; }
        .feedback-item { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #eee; border-left: 4px solid #ddd; }
        .feedback-replied { border-left-color: var(--maroon); background: #fff9f9; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>JULIA TECH HUB</h2>
        <ul class="sidebar-menu">
            <li><a href="innovator_dashboard.php" class="active"><i class="fas fa-home"></i> Overview</a></li>
            <li><a href="edit_project.php"><i class="fas fa-lightbulb"></i> My Project</a></li>
            <li><a href="learning_hub.php"><i class="fas fa-book"></i> Learning Hub</a></li>
            <li><a href="apply_funding.php"><i class="fas fa-hand-holding-usd"></i> Funding App</a></li>
            <li><a href="logout.php" style="margin-top: 50px; color: #ffb3b3;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header-bar">
            <h1>Welcome, <?php echo explode(' ', $_SESSION['user_name'] ?? 'Innovator')[0]; ?>!</h1>
            <div class="user-profile">
                <span class="status-badge <?php echo (($innovation['status'] ?? '') == 'approved') ? 'status-approved' : 'status-pending'; ?>">
                    <?php echo strtoupper($innovation['status'] ?? 'PENDING'); ?> ACCOUNT
                </span>
            </div>
        </div>

        <?php if (isset($msg_sent)): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 5px solid #28a745;">
                <i class="fas fa-check-circle"></i> <?php echo $msg_sent; ?>
            </div>
        <?php endif; ?>

        <div class="stat-grid">
            <div class="stat-card"><small>Project Title</small><h3><?php echo htmlspecialchars($innovation['title'] ?? 'No Project Linked'); ?></h3></div>
            <div class="stat-card"><small>Industry</small><h3><?php echo htmlspecialchars($innovation['industry'] ?? 'General'); ?></h3></div>
            <div class="stat-card"><small>Current Stage</small><h3><?php echo htmlspecialchars($innovation['stage'] ?? 'Setup'); ?></h3></div>
        </div>

        <div class="progress-container">
            <h3 style="margin-bottom: 20px;">Startup Progress Tracker</h3>
            <div style="background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
                <?php
                    $width = "25%";
                    $current_stage = $innovation['stage'] ?? '';
                    if($current_stage == 'MVP') $width = "50%";
                    if($current_stage == 'Market Ready') $width = "75%";
                    if($current_stage == 'Scaling') $width = "100%";
                ?>
                <div style="background: var(--maroon); width: <?php echo $width; ?>; height: 100%; transition: 0.8s;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.8rem; font-weight: bold;">
                <span style="color: var(--maroon)">Ideation</span>
                <span>MVP</span>
                <span>Market Ready</span>
                <span>Scaling</span>
            </div>
        </div>

        <div style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="progress-container">
                <h4 style="color: var(--maroon);"><i class="fas fa-bullhorn"></i> Hub Announcements</h4>
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                <?php
                $broadcasts = $conn->query("SELECT * FROM broadcasts ORDER BY created_at DESC LIMIT 3");
                if ($broadcasts && $broadcasts->num_rows > 0) {
                    while($msg = $broadcasts->fetch_assoc()) {
                        echo "<div style='margin-bottom: 15px; border-left: 3px solid var(--maroon); padding-left: 15px;'>
                                <small style='color: #888;'>".date('M d', strtotime($msg['created_at']))."</small>
                                <p style='margin-top: 5px; font-size: 0.9rem;'>".htmlspecialchars($msg['message'])."</p>
                              </div>";
                    }
                } else {
                    echo "<p style='color:#999; font-style: italic;'>No announcements.</p>";
                }
                ?>
            </div>

            <div class="progress-container">
                <h4 style="color: var(--maroon);"><i class="fas fa-user-graduate"></i> Mentor Advice & Feedback</h4>
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                <?php
                // Fetch both admin messages and user's own queries (to show pending ones)
                $feedback = $conn->query("SELECT * FROM notifications 
                                         WHERE user_id = '$user_id' 
                                         AND (type = 'admin_msg' OR type = 'mentor_query') 
                                         ORDER BY created_at DESC LIMIT 5");

                if ($feedback && $feedback->num_rows > 0) {
                    while($f = $feedback->fetch_assoc()) {
                        $has_reply = !empty($f['reply_message']);
                        $card_class = $has_reply ? 'feedback-replied' : '';
                        
                        echo "
                        <div class='feedback-item $card_class'>
                            <small style='color: #888;'><i class='fas fa-clock'></i> ".date('M d, g:i a', strtotime($f['created_at']))."</small>
                            <p style='margin-top: 5px; font-size: 0.9rem; color: #555;'>
                                <strong>Your Query:</strong> ".htmlspecialchars(str_replace("Support Request from ".$_SESSION['user_name'].": ", "", $f['message']))."
                            </p>";
                        
                        if ($has_reply) {
                            echo "
                            <div style='margin-top: 10px; border-top: 1px dashed #ddd; padding-top: 10px;'>
                                <p style='font-weight: bold; color: var(--maroon); margin-bottom: 2px;'>Official Hub Response:</p>
                                <p style='font-size: 0.9rem; color: #333; line-height:1.4;'>".nl2br(htmlspecialchars($f['reply_message']))."</p>
                            </div>";
                        } else {
                            echo "<p style='font-size: 0.8rem; color: #f39c12; font-style: italic; margin-top:5px;'><i class='fas fa-hourglass-half'></i> Pending mentor review...</p>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<div style='text-align:center; padding: 20px;'><i class='fas fa-comment-slash' style='color:#ddd; font-size: 2rem;'></i><p style='color:#999; font-size:0.85rem; margin-top:10px;'>No inquiries yet.</p></div>";
                }
                ?>
                <div style="text-align: center; margin-top: 10px; border-top: 1px solid #eee; padding-top: 15px;">
                     <a href="javascript:void(0)" onclick="openMentorModal()" style="color: var(--maroon); font-weight: bold; text-decoration: none; font-size: 0.85rem;">
                        <i class="fas fa-plus-circle"></i> Ask a New Question
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="mentorModal" class="modal">
        <div class="modal-content">
            <span onclick="closeMentorModal()" style="position:absolute; right:20px; top:10px; cursor:pointer; font-size:1.5rem; color:#aaa;">&times;</span>
            <h3 style="color:var(--maroon);">Ask a Mentor</h3>
            <form method="POST">
                <textarea name="mentor_query" rows="5" placeholder="Describe your challenge..." style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:15px; font-family:inherit;" required></textarea>
                <button type="submit" name="send_mentor_msg" class="btn-hero" style="width:100%; margin-top:15px;">Submit Inquiry</button>
            </form>
        </div>
    </div>

    <script>
        function openMentorModal() { document.getElementById('mentorModal').style.display = 'flex'; }
        function closeMentorModal() { document.getElementById('mentorModal').style.display = 'none'; }
        window.onclick = function(event) {
            let modal = document.getElementById('mentorModal');
            if (event.target == modal) { modal.style.display = "none"; }
        }
    </script>
</body>
</html>