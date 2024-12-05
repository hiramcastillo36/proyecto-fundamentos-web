<?php
    session_start();

    include_once '../bd/CAD.php';

    $cad = new CAD();
    $user = null;

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $user = $cad->getUserById($_SESSION['user_id']);
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Post - BYTE Y PIXEL</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="editormd/css/editormd.css" />
</head>
<body>
    <div class="container">
        <div class="topnav" id="myTopnav">
          <a href="../index.php" class="active">BYTE Y PIXEL</a>
          <div class="navoptions">
          <?php if ($user) { ?>

            <?php if ($user['role'] === 'admin') { ?>
            <a href="admin.php">Admin</a>
            <?php } ?>
            <a href="bd/logout.php">Logout</a>
            <?php } else { ?>
            <a href="pages/login.php">Login</a>
            <a href="pages/signup.php">Sign Up</a>
            <?php } ?>

          </div>
          <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
          </a>
        </div>

      <main class="main">
        <section class="section">
            <h2 class="section-title">About The Project</h2>
            <p class="section-text">
                BYTE Y PIXEL is an academic project developed as part of the Web Development Fundamentals course at Universidad Autónoma de San Luis Potosí (UASLP). This blog represents the culmination of learning various web technologies and development principles.
            </p>
            <p class="section-text">
                As a student of Intelligent Systems Engineering, this project combines my interest in technology with the practical application of web development fundamentals, showcasing the skills acquired throughout the course.
            </p>
        </section>

        <section class="section">
            <h2 class="section-title">Project Features</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">HTML5</div>
                    <div class="stat-label">Modern Structure</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">CSS3</div>
                    <div class="stat-label">Responsive Design</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">JS</div>
                    <div class="stat-label">Dynamic Features</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">UI/UX</div>
                    <div class="stat-label">User Experience</div>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Developer</h2>
            <p class="section-text">
                This project was developed by Hiram Castillo, a student of Intelligent Systems Engineering at UASLP. The development process focused on implementing web fundamentals while creating a practical and functional blog platform.
            </p>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image"></div>
                    <h3 class="member-name">Hiram Castillo</h3>
                    <p class="member-role">Student Developer</p>
                    <p class="member-info">Intelligent Systems Engineering<br>UASLP</p>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Technologies Used</h2>
            <p class="section-text">
                This project implements various web development technologies and best practices:
            </p>
            <ul class="tech-list" style="list-style: none; padding: 0;">
                <li style="margin-bottom: 1rem; padding: 1rem; background: #f8f8f8; border-radius: 8px;">
                    <strong>Frontend Development:</strong> HTML5, CSS3, JavaScript
                </li>
                <li style="margin-bottom: 1rem; padding: 1rem; background: #f8f8f8; border-radius: 8px;">
                    <strong>Responsive Design:</strong> Mobile-first approach, flexible layouts
                </li>
                <li style="margin-bottom: 1rem; padding: 1rem; background: #f8f8f8; border-radius: 8px;">
                    <strong>Web Standards:</strong> Modern web development best practices
                </li>
            </ul>
        </section>
    </main>
    </div>
    <footer>
        <div class="footer-categories">
            DIGITAL PRODUCT DESIGN • REMOTE WORK • UX DESIGN • DISTRIBUTED TEAMS
        </div>
        <div class="footer-logo">BYTE Y PIXEL</div>
        <div class="footer-copyright">© 2024 BYTE Y PIXEL All rights reserved.</div>
    </footer>
</body>
</html>
