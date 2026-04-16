<?php
include 'db.php';
session_start();

// Security: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['file'])) {
    $file_path = $_GET['file'];
    
    // Capture the return_id for the anchor link
    $return_id = isset($_GET['return_id']) ? $_GET['return_id'] : '';
    $back_url = "admin_funding.php" . ($return_id ? "#app-" . $return_id : "");

    // Security: Prevent directory traversal by checking if the path starts with 'uploads/'
    if (strpos($file_path, 'uploads/') === 0 && file_exists($file_path)) {
        // Redirect to the actual file if it exists
        header("Location: " . $file_path);
        exit();
    } else {
        // Show a professional error message if file is missing
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>File Not Found | Julia Tech Hub</title>
            <link rel='stylesheet' href='css/style.css'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
            <style>
                body { display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; font-family: 'Segoe UI', sans-serif; margin: 0; }
                .error-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 450px; border-top: 5px solid #e74c3c; }
                .error-icon { font-size: 5rem; color: #e74c3c; margin-bottom: 20px; }
                h2 { color: #2d3436; margin-top: 0; }
                p { color: #636e72; line-height: 1.6; }
                .btn-back { 
                    display: inline-block; 
                    margin-top: 25px; 
                    padding: 12px 25px; 
                    background: #2d3436; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 6px; 
                    font-weight: bold;
                    transition: background 0.3s;
                }
                .btn-back:hover { background: #000; }
            </style>
        </head>
        <body>
            <div class='error-container'>
                <i class='fas fa-file-circle-exclamation error-icon'></i>
                <h2>Pitch Deck Missing</h2>
                <p>We're sorry, but the requested pitch deck file could not be found on our server. The innovator might have replaced the file or it may have been moved.</p>
                <a href='" . htmlspecialchars($back_url) . "' class='btn-back'>
                    <i class='fas fa-arrow-left'></i> Go Back to Application
                </a>
            </div>
        </body>
        </html>";
        exit();
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>