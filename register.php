<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join the Hub | Julia Tech Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Specific styles for the registration layout */
        .reg-wrapper {
            display: flex;
            min-height: calc(100vh - 80px);
        }
        .reg-info {
            flex: 1;
            background: var(--maroon);
            color: white;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .reg-form-container {
            flex: 1.5;
            background: white;
            padding: 60px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-main);
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--maroon);
        }
        @media (max-width: 768px) {
            .reg-wrapper { flex-direction: column; }
            .reg-info { padding: 40px; }
        }
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
                <a href="index.php">Back to Home</a>
                <a href="login.php" class="btn-login">Login</a>
            </div>
        </div>
    </nav>

    <div class="reg-wrapper">
        <div class="reg-info">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Ready to Scale?</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">
                Join hundreds of innovators getting the support they need to turn code into companies.
            </p>
            <ul style="list-style: none; line-height: 2.5;">
                <li><i class="fas fa-check-circle"></i> Access to Equity-Free Grants</li>
                <li><i class="fas fa-check-circle"></i> 1-on-1 Mentorship</li>
                <li><i class="fas fa-check-circle"></i> Premium Training Resources</li>
            </ul>
        </div>

        <div class="reg-form-container">
            <h3 style="margin-bottom: 30px; font-size: 1.8rem; color: var(--maroon);">Register Your Innovation</h3>
            
            <form action="process_register.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                <div class="form-group">
                    <label>Innovation Name (Startup Title)</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. EcoCharge" required>
                </div>

                <div class="form-group">
                    <label>Industry</label>
                    <select name="industry" class="form-control">
                        <option value="FinTech">FinTech</option>
                        <option value="AgriTech">AgriTech</option>
                        <option value="HealthTech">HealthTech</option>
                        <option value="GreenTech">GreenTech</option>
                        <option value="EduTech">EduTech</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Short Pitch (Description)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="What problem are you solving?"></textarea>
                </div>

                <button type="submit" class="btn-hero" style="width: 100%; margin-top: 10px; cursor: pointer;">Create My Account</button>
            </form>
        </div>
    </div>

</body>
</html>