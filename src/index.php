<?php

    session_start();

    include_once("bd/conexion.php");
    include_once("bd/CAD.php");

    $conexion = new Conexion();
    $conexion = $conexion->conectar();

    $cad = new CAD();
    $user = null;

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $user = $cad->getUserById($_SESSION['user_id']);
    }

    if (isset($_POST['email'])) {
        try {
            $sql = "INSERT INTO newsletter (email) VALUES (:email)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->execute();
            $result = true;
        } catch (Exception $e) {
            $result = false;
        }


    }


    unset($_POST['email']);

    $cad = new CAD();
    $allPosts = $cad->getAllPosts(0, 6);



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BYTE Y PIXEL - Blog</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=New+York+Extra+Large:wght@700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=SF+Mono:wght@400&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=New+York+Small:wght@400;500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=SF+Pro+Text:wght@400;500;800&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="script" href="script.js">
</head>
<body>
    <div class="container">
      <div class="topnav" id="myTopnav">
        <a href="index.html" class="active">BYTE Y PIXEL</a>
        <div class="navoptions" id="navOptions">
          <a href="pages/about.html">About</a>
            <?php if ($user) { ?>
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

        <main>
            <article class="hero">

                <div class="feature-image">
                    <img src="assets/images/cd036c26e7e1275cacd73edc67b4afe128d222d5.png" alt="Image">
                </div>

                <div class="article-title">
                  <h1>A few words about this blog platform, Ghost, and how this site was made</h1>
                  <p class="subtitle">Why Ghost (& Figma) instead of Medium, WordPress or other options?</p>
                </div>

            </article>

            <div class="line"></div>

            <section class="read-next">
                <h2>All articles</h2>
                <div class="article-grid-home">
                    <?php foreach ($allPosts as $post) { ?>
                        <a href="/pages/article.php?id=<?php echo $post['id']; ?>">
                            <div class="article-preview">
                                <img src="uploads/<?php echo $post['image']; ?>" alt="Image">
                            <p>
                                <?php echo $post['title']; ?>
                            </p>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </section>

            <section class="newsletter">
                <h2>Sign up for the newsletter</h2>
                <p>If you want to be notified when we publish something new, sign up for the newsletter:</p>

                <?php if (isset($result) && $result) { ?>
                    <div>
                        <p>Thank you for signing up!</p>
                    </div>
                <?php } ?>

                <form class="newsletter-form" action="index.php" method="post">
                    <input type="email" placeholder="Enter your email..." name="email" required>
                    <button type="submit">Sign up</button>
                </form>
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
