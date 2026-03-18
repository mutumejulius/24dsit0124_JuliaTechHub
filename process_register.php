<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $title = $_POST['title'];
    $industry = $_POST['industry'];
    $description = $_POST['description'];

    // 1. Insert into Users Table
    $user_sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES ('$full_name', '$email', '$password', 'innovator')";
    
    if ($conn->query($user_sql) === TRUE) {
        $last_id = $conn->insert_id; // Get the ID of the user we just created

        // 2. Insert into Innovations Table linked to that user
        $inn_sql = "INSERT INTO innovations (user_id, title, industry, short_description) VALUES ('$last_id', '$title', '$industry', '$description')";
        
        if ($conn->query($inn_sql) === TRUE) {
            echo "<script>alert('Registration Successful!'); window.location='login.php';</script>";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>