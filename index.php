<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JULIA TECH HUB | From Idea to IPO</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Smooth scrolling for anchor links */
        html { scroll-behavior: smooth; }
        .nav-links a:hover { color: var(--maroon); border-bottom: 2px solid var(--maroon); }
        .footer-grid a { color: #ccc; text-decoration: none; transition: 0.3s; }
        .footer-grid a:hover { color: white; padding-left: 5px; }
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
                <a href="about.php">About</a>
                <a href="#services">Programs</a>
                <a href="#hall-of-fame">Showcase</a>
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-hero">Register Now</a>
            </div>
        </div>
    </nav>

    <header class="hero">
        <h1>From Idea to <span class="maroon-underline">IPO.</span></h1>
        <p>The premier ICT innovation hub supporting startups through structured training, financial grants, and global exposure.</p>
        <a href="register.php" style="background: var(--maroon); color:white; padding: 15px 30px; text-decoration:none; border-radius:5px; font-weight:bold; display: inline-block; margin-top: 20px;">Register Now</a>
    </header>

    <section id="services" class="section-container">
        <h2 style="text-align: center; color: var(--maroon); margin-bottom: 40px;">Our Programs</h2>
        <div class="grid-3">
            <div class="service-card">
                <i class="fas fa-code" style="font-size: 2rem; color: var(--maroon); margin-bottom: 15px;"></i>
                <h3>Technical Training</h3>
                <p>Coding workshops and business modules led by industry experts.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-chart-line" style="font-size: 2rem; color: var(--maroon); margin-bottom: 15px;"></i>
                <h3>Financial Support</h3>
                <p>Apply for equity-free grants and connect with VC networks.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-trophy" style="font-size: 2rem; color: var(--maroon); margin-bottom: 15px;"></i>
                <h3>Innovation Showcase</h3>
                <p>Secure a spot in our Hall of Fame annually and gain global exposure.</p>
            </div>
        </div>
    </section>

    <section id="hall-of-fame" class="section-container" style="background: #f8fafc;">
        <h2 style="color: var(--maroon); margin-bottom: 30px; text-align: center;">Hall of Fame</h2>
        <div class="grid-3">
            <?php
            $sql = "SELECT i.*, u.full_name FROM innovations i 
                    JOIN users u ON i.user_id = u.id 
                    WHERE i.is_featured = 1 AND i.status = 'approved' LIMIT 3";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '
                    <div class="hof-card" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <small style="color:var(--maroon); font-weight:bold;">'.strtoupper($row['industry']).'</small>
                        <h3 style="margin: 10px 0;">'.htmlspecialchars($row['title']).'</h3>
                        <p style="color:#666; font-style:italic; font-size: 0.9rem;">"'.htmlspecialchars($row['short_description']).'"</p>
                        <hr style="margin: 20px 0; border:0; border-top:1px solid #eee;">
                        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                            <span><i class="fas fa-user"></i> <b>'.htmlspecialchars($row['full_name']).'</b></span>
                            <span style="color:var(--maroon); font-weight: bold;">'.$row['stage'].'</span>
                        </div>
                    </div>';
                }
            } else {
                echo '<div style="grid-column: span 3; text-align: center; color: #999; padding: 40px;">
                        <i class="fas fa-seedling" style="font-size: 3rem; margin-bottom: 10px;"></i>
                        <p>Great innovations are currently in the works. Check back soon!</p>
                      </div>';
            }
            ?>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div>
                <h3>JULIA TECH HUB</h3>
                <p>Supporting the next generation of entrepreneurs since 2026.</p>
                <div style="margin-top: 15px; font-size: 1.2rem;">
                    <i class="fab fa-linkedin" style="margin-right: 15px; cursor: pointer;"></i>
                    <i class="fab fa-twitter" style="margin-right: 15px; cursor: pointer;"></i>
                    <i class="fab fa-instagram" style="cursor: pointer;"></i>
                </div>
            </div>
            <div>
                <h4>Quick Links</h4>
                <p><a href="about.php">About Us</a></p>
                <p><a href="privacy.php">Privacy Policy</a></p>
                <p><a href="#services">Programs</a></p>
            </div>
            <div>
                <h4>Contact Us</h4>
                <p><i class="fas fa-envelope"></i> info@juliatechhub.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Innovation Drive, Tech City</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 Julia Tech Hub. All rights reserved.
        </div>
    </footer>

</body>
</html>