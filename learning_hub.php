<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learning Hub | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { display: flex; min-height: 100vh; background: #f4f7f6; }
        .main-content { margin-left: 260px; flex: 1; padding: 40px; }
        .sidebar { width: 260px; background: var(--maroon); color: white; padding: 20px; position: fixed; height: 100%; }
        
        .resource-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .resource-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; border-top: 4px solid var(--maroon); }
        .resource-card:hover { transform: translateY(-5px); }
        .card-body { padding: 20px; }
        .category-tag { font-size: 0.7rem; background: #eee; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; font-weight: bold; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>JULIA TECH HUB</h2>
        <ul class="nav-links" style="flex-direction: column; align-items: flex-start; gap: 15px; list-style:none;">
            <li><a href="innovator_dashboard.php"><i class="fas fa-home"></i> Overview</a></li>
            <li><a href="learning_hub.php" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px;"><i class="fas fa-book"></i> Learning Hub</a></li>
            <li><a href="logout.php" style="margin-top: 50px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1 style="margin-bottom: 10px;">Curated Knowledge</h1>
        <p style="color: #666; margin-bottom: 40px;">Expert-led resources to help you build and scale.</p>

        <div class="resource-grid">
            <?php
            $res = $conn->query("SELECT * FROM resources ORDER BY id DESC");
            if ($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    echo "
                    <div class='resource-card'>
                        <div class='card-body'>
                            <span class='category-tag'>{$row['category']}</span>
                            <h3 style='margin: 15px 0 10px;'>{$row['title']}</h3>
                            <p style='font-size: 0.9rem; color: #666; margin-bottom: 20px;'>{$row['description']}</p>
                            <a href='{$row['link_url']}' target='_blank' class='btn-hero' style='display:block; text-align:center; padding: 10px; font-size: 0.8rem; border:none;'>Start Lesson</a>
                        </div>
                    </div>";
                }
            } else {
                echo "<p>No resources uploaded yet. Check back soon!</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>