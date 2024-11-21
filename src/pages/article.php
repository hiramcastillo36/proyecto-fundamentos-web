<?php
  include_once("../bd/CAD.php");

  $post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (isset($_POST['email'])) {
        $cad = new CAD();
        $result = $cad->insertNewsletter($_POST['email']);
    }

    unset($_POST['email']);

    $cad = new CAD();
    $post = $cad->getPost($post_id);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BYTE Y PIXEL - Blog</title>
    <link rel="stylesheet" href="../styles/styles.css">
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
        <a href="../index.html" class="active">BYTE Y PIXEL</a>
        <div class="navoptions">

          <a href="about.html">About</a>
        </div>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
          <i class="fa fa-bars"></i>
        </a>
      </div>

        <main>
            <article class="hero">
                <div class="article-title">
                  <h1><?php echo $post['title']; ?></h1>
                  <p class="subtitle"><?php echo $post['description']; ?></p>
                </div>


                <div class="feature-image">
                    <img src="../uploads/<?php echo $post['image_url']; ?>" alt="Image">
                </div>


                <div class="article-content">
                    <div class="line"></div>
                    <div class="article-meta">
                      <div class="article-info">
                        <div class="author"><?php echo $post['author_name']; ?></div>
                        <div class="date">April 15, 2020 4 min read</div>
                      </div>
                        <div class="share">
                            <div class="share-icon">
                              <img src="../assets/images/facebook1.svg" alt="Facebook">
                            </div>
                            <div style="height: 50px; width: 1px; background: #eaeaea;"></div>
                            <div class="share-icon">
                              <img src="../assets/images/Vector.svg" alt="Twitter">
                            </div>
                        </div>

                    </div>

                    <div class="article-text">
                      <?php echo $post['body']; ?>
                    </div>

                    <div class="share-box">
                      <div class="share-media">
                        <img src="../assets/images/facebook1.svg" alt="Facebook">
                        <p>
                          Share on Facebook
                        </p>
                      </div>
                      <div style="height: 50px; width: 1px; background: #eaeaea;"></div>
                      <div class="share-media">
                        <img src="../assets/images/Vector.svg" alt="Twitter">
                        <p>
                          Share on Twitter
                        </p>
                      </div>
                  </div>
                </div>
            </article>


            <section class="read-next">
                <div class="line">
                  <div class="eyes">
                      <img src="../assets/images/Group 10.svg" alt="Eye">
                  </div>
              </div>


                <h2>What to read next</h2>
                <div class="article-grid">
                  <a href="../pages/article.php">
                    <div class="article-preview">
                      <img src="../assets/images/5356790d830d59385873020c6e143cf8a787345f.png" alt="Image">
                      <p>
                        Hello world, or, in other words, why this blog exists
                      </p>
                    </div>
                  </a>
                  <a href="../pages/article.php">
                    <div class="article-preview">
                      <img src="../assets/images/5356790d830d59385873020c6e143cf8a787345f.png" alt="Image">
                      <p>
                        Hello world, or, in other words, why this blog exists
                      </p>
                    </div>
                  </a>
                  <a href="../pages/article.php">
                    <div class="article-preview">
                      <img src="../assets/images/5356790d830d59385873020c6e143cf8a787345f.png" alt="Image">
                      <p>
                        Hello world, or, in other words, why this blog exists
                      </p>
                    </div>
                  </a>
                  <a href="../pages/article.php">
                    <div class="article-preview">
                      <img src="../assets/images/5356790d830d59385873020c6e143cf8a787345f.png" alt="Image">
                      <p>
                        Hello world, or, in other words, why this blog exists
                      </p>
                    </div>
                  </a>
                  <a href="../pages/article.php">
                    <div class="article-preview">
                      <img src="../assets/images/5356790d830d59385873020c6e143cf8a787345f.png" alt="Image">
                      <p>
                        Hello world, or, in other words, why this blog exists
                      </p>
                    </div>
                  </a>
                  <a href="../pages/article.php">
                    <div class="article-preview">
                      <img src="../assets/images/5356790d830d59385873020c6e143cf8a787345f.png" alt="Image">
                      <p>
                        Hello world, or, in other words, why this blog exists
                      </p>
                    </div>
                  </a>
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

                <form class="newsletter-form" action="article.php" method="post">
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
