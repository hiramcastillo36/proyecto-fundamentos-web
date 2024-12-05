<?php
    include_once("../bd/CAD.php");

    session_start();

    $post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $cad = new CAD();

    $user = null;

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $user = $cad->getUserById($_SESSION['user_id']);
    }

    if (isset($_POST['email'])) {
        $result = $cad->insertNewsletter($_POST['email']);
    }

    if (isset($_POST['comment']) && $user) {
        $comment = trim($_POST['comment']);
        if (!empty($comment)) {
            $cad->createComment($post_id, $user['id'], $comment);
        }
        // Redirigir para evitar reenvío del formulario
        header("Location: article.php?id=" . $post_id);
        exit();
    }
    if (isset($_POST['like_action']) && $user) {
        $action = $_POST['like_action'];
        if ($action === 'like') {
            $cad->addLike($post_id, $user['id']);
        } else if ($action === 'unlike') {
            $cad->removeLike($post_id, $user['id']);
        }
        // Redirigir para evitar reenvío del formulario
        header("Location: article.php?id=" . $post_id);
        exit();
    }

    unset($_POST['email']);
    unset($_POST['comment']);

    $cad = new CAD();
    $post = $cad->getPost($post_id);

    $allPosts = $cad->getAllPosts(0, 6);

    $userWithNewsletter = $user ? $cad->getUserWithNewsletter($user['id']) : false;
    $isNewsletterSubscriber = $user ? $cad->getUserWithNewsletter($user['id']) : false;
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
        <a href="../index.php" class="active">BYTE Y PIXEL</a>
        <div class="navoptions" id="navOptions">
          <a href="about.php">About</a>
            <?php if ($user) { ?>
                <?php if ($user['role'] === 'admin') { ?>
                    <a href="admin.php">Admin</a>
                <?php } ?>
                <?php if ($userWithNewsletter) { ?>
                    <a href="newsletter.php">Newsletter</a>
                <?php } ?>
                <a href="../bd/logout.php">Logout</a>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php } ?>
        </div>
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
                        <div class="date"><?php echo date("l, F j, Y", strtotime($post['created_at'])); ?> <?php echo $post['read_time']; ?> min read</div>
                    </div>
                    <div class="like-section">
                        <?php
                        $likesCount = $cad->getLikesCount($post_id);
                        $hasLiked = $user ? $cad->hasUserLiked($post_id, $user['id']) : false;
                        ?>
                        <?php if ($user) { ?>
                            <form method="post" class="like-form">
                                <input type="hidden" name="like_action" value="<?php echo $hasLiked ? 'unlike' : 'like'; ?>">
                                <button type="submit" class="like-button <?php echo $hasLiked ? 'liked' : ''; ?>">
                                    <i class="fa fa-heart<?php echo $hasLiked ? '' : '-o'; ?>"></i>
                                    <span><?php echo $likesCount; ?></span>
                                </button>
                            </form>
                        <?php } else { ?>
                            <button class="like-button" disabled>
                                <i class="fa fa-heart-o"></i>
                                <span><?php echo $likesCount; ?></span>
                            </button>
                        <?php } ?>
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
                <?php foreach ($allPosts as $post) { ?>
                        <a href="../pages/article.php?id=<?php echo $post['id']; ?>">
                            <div class="article-preview">
                                <img src="../uploads/<?php echo $post['image']; ?>" alt="Image">
                            <p>
                                <?php echo $post['title']; ?>
                            </p>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </section>

            <section class="comments">
    <h2>Comments</h2>

    <?php if ($user) { ?>
        <div class="comment-form">
            <h3>Leave a comment</h3>
            <form action="article.php?id=<?php echo $post_id; ?>" method="post">
                <textarea placeholder="Write your comment here..." name="comment" required></textarea>
                <button type="submit">Submit</button>
            </form>
        </div>
    <?php } else { ?>
        <div class="login-prompt">
            <p>Please <a href="login.php">login</a> to leave a comment.</p>
        </div>
    <?php } ?>

    <div class="comment-box">
        <?php
        $comments = $cad->getCommentsByPostId($post_id);
        if (!empty($comments)) {
            foreach ($comments as $comment) {
        ?>
            <div class="comment">
                <div class="comment-author"
                         alt="<?php echo htmlspecialchars($comment['author_name']); ?>">
                    <div class="author-info">
                        <div class="author-name"><?php echo htmlspecialchars($comment['author_name']); ?></div>
                        <div class="comment-date"><?php echo date("F j, Y", strtotime($comment['created_at'])); ?></div>
                    </div>
                </div>
                <div class="comment-text">
                    <p><?php echo nl2br(htmlspecialchars($comment['text'])); ?></p>
                </div>
            </div>
        <?php
            }
        } else { ?>
            <p class="no-comments">No comments yet. Be the first to comment!</p>
        <?php } ?>
    </div>
</section>

<? if (!$isNewsletterSubscriber) { ?>
                <section class="newsletter">
                <h2>Sign up for the newsletter</h2>
                <p>If you want to be notified when we publish something new, sign up for the newsletter:</p>

                <?php if (isset($result) && $result) { ?>
                    <div>
                        <p>Thank you!</p>
                    </div>
                <?php } ?>

                <form class="newsletter-form" action="article.php?id=<?php echo $post_id; ?>" method="post">
                    <input type="email" placeholder="Enter your email..." name="email" required>
                    <button type="submit">Sign up</button>
                </form>
            </section>
            <? } ?>
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
