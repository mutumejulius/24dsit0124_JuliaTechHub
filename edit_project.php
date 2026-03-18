<?php
include 'db.php';
session_start();
$user_id = $_SESSION['user_id'];

if (isset($_POST['update_project'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $stage = $_POST['stage'];

    $conn->query("UPDATE innovations SET title='$title', short_description='$desc', stage='$stage' WHERE user_id='$user_id'");
    echo "<script>alert('Project Updated!'); window.location='innovator_dashboard.php';</script>";
}

$project = $conn->query("SELECT * FROM innovations WHERE user_id='$user_id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background: #f4f7f6; padding: 50px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="color: var(--maroon); margin-bottom: 20px;">Update Startup Profile</h2>
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold;">Project Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $project['title']; ?>" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold;">Current Stage</label>
                <select name="stage" class="form-control">
                    <option <?php if($project['stage'] == 'Ideation') echo 'selected'; ?>>Ideation</option>
                    <option <?php if($project['stage'] == 'MVP') echo 'selected'; ?>>MVP</option>
                    <option <?php if($project['stage'] == 'Market Ready') echo 'selected'; ?>>Market Ready</option>
                    <option <?php if($project['stage'] == 'Scaling') echo 'selected'; ?>>Scaling</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold;">Short Pitch</label>
                <textarea name="description" class="form-control" rows="4"><?php echo $project['short_description']; ?></textarea>
            </div>
            <button type="submit" name="update_project" class="btn-hero" style="width:100%; border:none; cursor:pointer;">Save Changes</button>
            <br><br>
            <a href="innovator_dashboard.php" style="display:block; text-align:center; color:#666; text-decoration:none;">Cancel</a>
        </form>
    </div>
</body>
</html>