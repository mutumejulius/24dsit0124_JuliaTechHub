<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 150px);
            background-color: var(--light-bg);
            padding: 20px;
        }
        .login-card {
            background: white;
            width: 100%;
            max-width: 450px;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-top: 5px solid var(--maroon);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
        }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header h2 { color: var(--maroon); font-size: 2rem; }
    </style>
</head>
<body>

    <nav>
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">J</div>
                JULIA TECH HUB
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="register.php" class="btn-join">Join Hub</a>
            </div>
        </div>
    </nav>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p style="color: #666; font-size: 0.9rem;">Enter your credentials to access the hub</p>
            </div>

            <form action="process_login.php" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn-hero" style="width: 100%; border:none; cursor: pointer; margin-top: 10px;">
                    Secure Login
                </button>
            </form>

            <div style="text-align: center; margin-top: 25px; font-size: 0.9rem;">
                Don't have an account? <a href="register.php" style="color: var(--maroon); font-weight: bold; text-decoration: none;">Register here</a>
            </div>
        </div>
    </div>

</body>
</html>