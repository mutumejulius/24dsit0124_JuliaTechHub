<?php
include 'db.php';
session_start();

// Security: Only allow Admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Adding New Resource
if (isset($_POST['add_resource'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    // --- FILE SANITIZATION & UPLOAD ---
    if (isset($_FILES["resource_file"]) && $_FILES["resource_file"]["error"] == 0) {
        $original_name = basename($_FILES["resource_file"]["name"]);
        
        // 1. Remove spaces and replace with underscores
        // 2. Remove special characters that break URLs
        $clean_name = preg_replace("/[^a-zA-Z0-9._-]/", "_", $original_name);
        
        $target_dir = "uploads/";
        // Ensure directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . time() . "_" . $clean_name; 

        if (move_uploaded_file($_FILES["resource_file"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO resources (title, category, link_url, description) 
                    VALUES ('$title', '$cat', '$target_file', '$desc')";
            
            if ($conn->query($sql)) {
                $msg = "Resource uploaded successfully with a clean URL!";
            } else {
                $error_msg = "Database error: " . $conn->error;
            }
        } else {
            $error_msg = "Error moving the file to the uploads folder.";
        }
    } else {
        $error_msg = "Please select a valid file to upload.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Optional: Fetch file path to delete from server
    $res = $conn->query("SELECT link_url FROM resources WHERE id = '$id'");
    if ($row = $res->fetch_assoc()) {
        if (file_exists($row['link_url'])) {
            unlink($row['link_url']);
        }
    }
    
    $conn->query("DELETE FROM resources WHERE id = '$id'");
    header("Location: admin_training.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Training CMS | Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { display: flex; min-height: 100vh; background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 260px; background: #2d3436; color: white; padding: 20px; position: fixed; height: 100%; }
        .main-content { margin-left: 260px; flex: 1; padding: 40px; }
        
        .upload-card { background: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; border-top: 5px solid var(--maroon, #800000); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        
        .resource-list { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .resource-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #eee; }
        .btn-hero { background: var(--maroon, #800000); color: white; padding: 12px 25px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-hero:hover { background: #600000; }
        
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 style="color: white; margin-bottom: 30px;">ADMIN CMS</h2>
        <ul class="nav-links" style="flex-direction: column; align-items: flex-start; gap: 15px; list-style:none; padding:0;">
            <li><a href="admin_dashboard.php" style="color: #bdc3c7; text-decoration:none;"><i class="fas fa-tachometer-alt"></i> Ecosystem</a></li>
            <li><a href="admin_training.php" style="color: #ff7675; text-decoration:none;"><i class="fas fa-graduation-cap"></i> Training CMS</a></li>
            <li><a href="logout.php" style="color: #bdc3c7; text-decoration:none; margin-top: 50px; display:block;"><i class="fas fa-power-off"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Training Content Management</h1>
        <p style="color: #666; margin-bottom: 30px;">Add or remove lessons from the Innovator's Learning Hub.</p>

        <div class="upload-card">
            <h3><i class="fas fa-plus-circle"></i> Add New Lesson</h3>
            
            <?php if(isset($msg)): ?>
                <div class="alert alert-success"><?php echo $msg; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error_msg)): ?>
                <div class="alert alert-error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div class="form-group">
                    <label>Lesson Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Intro to PHP" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" class="form-control">
                        <option>Business</option>
                        <option>Coding</option>
                        <option>Marketing</option>
                        <option>Legal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Resource File (PDF/DOCX)</label>
                    <input type="file" name="resource_file" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Short Description</label>
                    <input type="text" name="description" class="form-control" placeholder="What will they learn?" required>
                </div>
                <button type="submit" name="add_resource" class="btn-hero" style="grid-column: span 2;">
                    <i class="fas fa-upload"></i> Publish to Learning Hub
                </button>
            </form>
        </div>

        <div class="resource-list">
            <div style="background: #fafafa; padding: 15px 20px; font-weight: bold; border-bottom: 2px solid #eee;">Existing Resources</div>
            <?php
            $res = $conn->query("SELECT * FROM resources ORDER BY id DESC");
            if($res->num_rows > 0):
                while($row = $res->fetch_assoc()): ?>
                    <div class='resource-item'>
                        <div>
                            <span style='font-size:0.7rem; font-weight:bold; color:var(--maroon, #800000);'>
                                <?php echo strtoupper(htmlspecialchars($row['category'])); ?>
                            </span>
                            <h4 style='margin: 5px 0;'><?php echo htmlspecialchars($row['title']); ?></h4>
                            <small style="color: #888;"><?php echo htmlspecialchars($row['description']); ?></small>
                        </div>
                        <a href='admin_training.php?delete=<?php echo $row['id']; ?>' 
                           style='color: #ff7675;' 
                           onclick="return confirm('Delete this lesson and remove the file?')">
                           <i class='fas fa-trash-alt'></i>
                        </a>
                    </div>
                <?php endwhile; 
            else: ?>
                <p style="padding: 20px; color: #999; text-align: center;">No resources found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>