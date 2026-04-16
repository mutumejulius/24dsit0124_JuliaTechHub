<?php
include 'db.php';
session_start();

// Security: Only allow Admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// --- UPDATE LOGIC WITH FEEDBACK ---
if (isset($_POST['update_status'])) {
    $app_id = $_POST['app_id'];
    $new_status = $_POST['new_status'];
    $feedback = mysqli_real_escape_string($conn, $_POST['admin_feedback']);
    
    if($conn->query("UPDATE funding_applications SET status = '$new_status', admin_feedback = '$feedback' WHERE id = '$app_id'")) {
        header("Location: admin_funding.php?msg=updated#app-$app_id");
        exit();
    }
}

$apps = $conn->query("SELECT f.*, i.title, u.full_name FROM funding_applications f 
                      JOIN innovations i ON f.innovation_id = i.id 
                      JOIN users u ON i.user_id = u.id 
                      ORDER BY f.applied_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Funding | Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html { scroll-behavior: smooth; } /* Makes the jump to the specific row smooth */
        body { background:#f0f2f5; padding: 40px; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .success-alert { 
            background: #d4edda; color: #155724; padding: 15px; 
            border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #28a745;
            display: flex; align-items: center; gap: 10px;
        }

        .app-row { 
            background: white; padding: 25px; margin-bottom: 20px; border-radius: 10px; 
            display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 6px solid #ccc; 
        }
        /* Highlight the row when returning from a link */
        .app-row:target { border-left-width: 10px; box-shadow: 0 0 10px rgba(52, 152, 219, 0.5); }

        .row-top { display: flex; justify-content: space-between; align-items: flex-start; width: 100%; }
        .status-Pending { border-left-color: #f39c12; }
        .status-Approved { border-left-color: #27ae60; }
        .status-Rejected { border-left-color: #e74c3c; }
        .status-Under\ Review { border-left-color: #3498db; }
        
        .btn-back-link { color: var(--maroon); text-decoration: none; font-weight: bold; }
        textarea.feedback-input { 
            width: 100%; height: 80px; margin: 10px 0; padding: 10px; 
            border-radius: 6px; border: 1px solid #ddd; font-family: inherit; resize: vertical;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1 style="color: #2d3436;">Funding Applications</h1>
            <a href="admin_dashboard.php" class="btn-back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="success-alert">
                <i class="fas fa-check-circle"></i> Application updated successfully!
            </div>
        <?php endif; ?>

        <?php if ($apps->num_rows > 0): ?>
            <?php while($row = $apps->fetch_assoc()): ?>
                <div class="app-row status-<?php echo $row['status']; ?>" id="app-<?php echo $row['id']; ?>">
                    <div class="row-top">
                        <div>
                            <h3 style="margin: 0; color: var(--maroon);"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p style="margin: 5px 0; color: #666;">Founder: <strong><?php echo htmlspecialchars($row['full_name']); ?></strong></p>
                            <p style="font-size: 1.1rem; margin: 5px 0;">Request: <strong>$<?php echo number_format($row['amount_requested']); ?></strong></p>
                            
                            <a href="view_deck.php?file=<?php echo urlencode($row['pitch_deck_url']); ?>&return_id=<?php echo $row['id']; ?>" target="_blank" style="color: #3498db; text-decoration: none; font-size: 0.9rem;">
                                <i class="fas fa-file-pdf"></i> View Pitch Deck
                            </a>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: #999;">Applied on: <?php echo date('M d, Y', strtotime($row['applied_at'])); ?></span>
                        </div>
                    </div>
                    
                    <form method="POST" style="background: #f9f9f9; padding: 15px; border-radius: 8px; width: 100%; margin-top: 15px;">
                        <input type="hidden" name="app_id" value="<?php echo $row['id']; ?>">
                        <textarea name="admin_feedback" class="feedback-input"><?php echo htmlspecialchars($row['admin_feedback']); ?></textarea>
                        
                        <div style="display:flex; gap:10px; align-items: center;">
                            <select name="new_status" class="form-control" style="flex: 1; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                                <option <?php if($row['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                <option <?php if($row['status']=='Under Review') echo 'selected'; ?>>Under Review</option>
                                <option <?php if($row['status']=='Approved') echo 'selected'; ?>>Approved</option>
                                <option <?php if($row['status']=='Rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-hero" style="padding: 10px 20px; border:none; cursor:pointer; font-weight: bold;">
                                Update Application
                            </button>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>