<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | JULIA TECH HUB</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <nav>
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">J</div>
                <a href="index.php" style="text-decoration:none; color:inherit;">JULIA TECH HUB</a>
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="index.php#services">Programs</a>
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-hero">Register Now</a>
            </div>
        </div>
    </nav>

    <header class="hero" style="padding: 100px 20px;">
        <h1>Our <span class="maroon-underline">Mission.</span></h1>
        <p>Empowering the next generation of ICT leaders through mentorship and resources.</p>
    </header>

    <section class="section-container">
        <div style="max-width: 800px; margin: 0 auto; line-height: 1.8; color: #444;">
            <h2 style="color: var(--maroon); border-left: 5px solid var(--maroon); padding-left: 20px; margin-bottom: 20px;">Who We Are</h2>
            <p>Founded in 2026, <strong>Julia Tech Hub</strong> serves as a bridge between raw innovation and market-ready startups. We believe that great ideas shouldn't die due to a lack of guidance or capital.</p>
            
            <div class="grid-3" style="margin-top: 50px;">
                <div class="service-card">
                    <i class="fas fa-rocket" style="font-size: 2rem; color: var(--maroon);"></i>
                    <h3>Growth</h3>
                    <p>We provide the environment needed for startups to scale rapidly.</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-users" style="font-size: 2rem; color: var(--maroon);"></i>
                    <h3>Community</h3>
                    <p>A network of over 500+ innovators and technical mentors.</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-shield-alt" style="font-size: 2rem; color: var(--maroon);"></i>
                    <h3>Integrity</h3>
                    <p>Transparent funding and ethical business practices.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div>
                <h3>JULIA TECH HUB</h3>
                <p>Supporting the next generation of entrepreneurs.</p>
            </div>
            <div>
                <h4>Links</h4>
                <p><a href="about.php" style="color:#fff; text-decoration:none;">About Us</a></p>
                <p><a href="privacy.php" style="color:#fff; text-decoration:none;">Privacy</a></p>
            </div>
            <div>
                <h4>Contact</h4>
                <p>info@juliatechhub.com</p>
            </div>
        </div>
        <div class="footer-bottom">&copy; 2026 Julia Tech Hub.</div>
    </footer>

</body>
</html>