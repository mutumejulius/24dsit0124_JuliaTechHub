<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'];
$innovation = $conn->query("SELECT id FROM innovations WHERE user_id = '$user_id'")->fetch_assoc();

// Security: Check if innovation exists for this user
if (!$innovation) {
    die("Please submit your innovation details first before applying for funding.");
}
$inn_id = $innovation['id'];

// --- HANDLE WITHDRAWAL LOGIC ---
if (isset($_POST['withdraw_app'])) {
    $app_id = $_POST['app_id'];
    $file_to_delete = $_POST['file_path'];

    // Security check: ensure the app belongs to this innovator and is still Pending
    $stmt = $conn->prepare("DELETE FROM funding_applications WHERE id = ? AND innovation_id = ? AND status = 'Pending'");
    $stmt->bind_param("ii", $app_id, $inn_id);
    
    if ($stmt->execute()) {
        // Remove the PDF file from the server
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
        $success = "Application withdrawn successfully.";
    }
    $stmt->close();
}

// --- HANDLE SUBMISSION LOGIC ---
if (isset($_POST['submit_app'])) {
    $amount = $_POST['amount'];
    $equity = $_POST['equity'];
    $funds_usage = mysqli_real_escape_string($conn, $_POST['usage']);
    
    $target_dir = "uploads/decks/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_name = time() . "_" . basename($_FILES["pitch_deck"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["pitch_deck"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO funding_applications (innovation_id, amount_requested, equity_offered, pitch_deck_url, use_of_funds) 
                VALUES ('$inn_id', '$amount', '$equity', '$target_file', '$funds_usage')";
        
        if ($conn->query($sql)) {
            header("Location: apply_funding.php");
            exit();
        }
    } else {
        $error = "File upload failed.";
    }
}

// Get existing application details
$existing = $conn->query("SELECT * FROM funding_applications WHERE innovation_id = '$inn_id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Funding Application | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .apply-card { max-width: 700px; margin: 50px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; }
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; }
        
        .status-banner { padding: 30px; border-radius: 8px; text-align: center; background: #f8f9fa; border: 2px dashed var(--maroon); }
        .feedback-box { background: #fff; border: 1px solid #eee; padding: 15px; border-radius: 8px; margin: 15px 0; text-align: left; border-left: 4px solid var(--maroon); }
        
        .btn-withdraw { background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 15px; font-weight: bold; transition: 0.3s; }
        .btn-withdraw:hover { background: #c0392b; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body style="background: #f4f7f6;">

    <div class="apply-card">
        <h2 style="color: var(--maroon); margin-bottom: 10px;">Funding & Investment</h2>
        <p style="color: #666; margin-bottom: 30px;">Fuel your innovation with the Julia Tech Hub Seed Fund.</p>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($existing): ?>
            <div class="status-banner">
                <small>CURRENT STATUS</small>
                <h2 style="color: var(--maroon); margin: 10px 0;"><?php echo strtoupper($existing['status']); ?></h2>
                <p>Request: <b>$<?php echo number_format($existing['amount_requested']); ?></b></p>
                
                <?php if (!empty($existing['admin_feedback'])): ?>
                    <div class="feedback-box">
                        <strong style="color: var(--maroon); font-size: 0.8rem;"><i class="fas fa-comment-dots"></i> ADMIN FEEDBACK:</strong>
                        <p style="margin-top: 5px; font-style: italic; color: #444;"><?php echo nl2br($existing['admin_feedback']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($existing['status'] == 'Pending'): ?>
                    <form method="POST" onsubmit="return confirm('Withdraw this application? You can then submit a new corrected one.');">
                        <input type="hidden" name="app_id" value="<?php echo $existing['id']; ?>">
                        <input type="hidden" name="file_path" value="<?php echo $existing['pitch_deck_url']; ?>">
                        <button type="submit" name="withdraw_app" class="btn-withdraw">
                            <i class="fas fa-trash-alt"></i> Withdraw Application
                        </button>
                    </form>
                <?php endif; ?>

                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                <a href="innovator_dashboard.php" style="color: var(--maroon); text-decoration: none; font-weight: bold;">← Back to Dashboard</a>
            </div>
        <?php else: ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Funding Amount Requested (USD)</label>
                    <input type="number" name="amount" placeholder="e.g. 5000" required>
                </div>
                <div class="form-group">
                    <label>Equity Offered (%)</label>
                    <input type="number" name="equity" placeholder="e.g. 5" min="0" max="100">
                </div>
                <div class="form-group">
                    <label>Upload Pitch Deck (PDF Only)</label>
                    <input type="file" name="pitch_deck" accept=".pdf" required>
                </div>
                <div class="form-group">
                    <label>How will you use these funds?</label>
                    <textarea name="usage" rows="4" placeholder="Describe your plan..." required></textarea>
                </div>
                <button type="submit" name="submit_app" class="btn-hero" style="width: 100%; border:none; cursor:pointer;">Submit Application</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>