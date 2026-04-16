<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'innovator') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update_project'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    // Notice 'stage' is removed from this query as only admins change it now
    $conn->query("UPDATE innovations SET title='$title', short_description='$desc' WHERE user_id='$user_id'");
    echo "<script>alert('Project Details Updated!'); window.location='innovator_dashboard.php';</script>";
}

$project = $conn->query("SELECT * FROM innovations WHERE user_id='$user_id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; box-sizing: border-box; font-family: inherit; }
    </style>
</head>
<body style="background: #f4f7f6; padding: 50px; font-family: 'Segoe UI', sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #800000; margin-bottom: 20px;">Update Startup Profile</h2>
        
        
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold;">Project Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($project['title']); ?>" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold;">Short Pitch / Description</label>
                <textarea name="description" class="form-control" rows="6" required><?php echo htmlspecialchars($project['short_description']); ?></textarea>
            </div>
            
            <button type="submit" name="update_project" class="btn-hero" style="width:100%; border:none; cursor:pointer; padding: 15px; font-weight: bold; background: #800000; color: white; border-radius: 8px;">Save Changes</button>
            <br><br>
            <a href="innovator_dashboard.php" style="display:block; text-align:center; color:#666; text-decoration:none;">Cancel</a>
        </form>
    </div>
</body>
</html>