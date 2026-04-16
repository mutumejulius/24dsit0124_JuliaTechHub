<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture and Escape inputs
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $title     = mysqli_real_escape_string($conn, $_POST['title']);
    $industry  = mysqli_real_escape_string($conn, $_POST['industry']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // 2. CHECK IF EMAIL ALREADY EXISTS
    $check_email = $conn->query("SELECT email FROM users WHERE email = '$email'");
    
    if ($check_email->num_rows > 0) {
        // Email found! Stop the registration and alert the user.
        echo "<script>alert('Error: This email is already registered. Please use a different one or log in.'); window.history.back();</script>";
        exit(); 
    }

    // 3. Insert into Users Table (Only runs if email is unique)
    $user_sql = "INSERT INTO users (full_name, email, password_hash, role) 
                 VALUES ('$full_name', '$email', '$password', 'innovator')";
    
    if ($conn->query($user_sql) === TRUE) {
        $last_id = $conn->insert_id; 

        // 4. Insert into Innovations Table
        $inn_sql = "INSERT INTO innovations (user_id, title, industry, short_description) 
                    VALUES ('$last_id', '$title', '$industry', '$description')";
        
        if ($conn->query($inn_sql) === TRUE) {
            echo "<script>alert('Registration Successful!'); window.location='login.php';</script>";
        } else {
            echo "Error inserting innovation: " . $conn->error;
        }
    } else {
        echo "Error creating user: " . $conn->error;
    }
}
?>