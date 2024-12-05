<?php
    session_start();

    include_once '../bd/CAD.php';

    $cad = new CAD();
    $user = null;

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $user = $cad->getUserById($_SESSION['user_id']);
    }

    if ($user === null || $user['role'] !== 'admin') {
        header('Location: ../index.php');
        exit;
    }

    // Handle post deletion
    if (isset($_POST['delete_post']) && isset($_POST['post_id'])) {
        $post_id = (int)$_POST['post_id'];
        $deleted = $cad->deletePost($post_id);
        if ($deleted) {
            $_SESSION['message'] = "Post deleted successfully";
        } else {
            $_SESSION['error'] = "Error deleting post";
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Pagination settings
    $items_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;

    // Get paginated posts and total count
    $posts = $cad->getPosts($offset, $items_per_page);
    $total_posts = $cad->getTotalPosts();
    $total_pages = ceil($total_posts / $items_per_page);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BYTE Y PIXEL - Admin</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        .posts-table {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .posts-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 400px;
            background-color: white;
        }

        .posts-table th,
        .posts-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .posts-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .posts-table tr:hover {
            background-color: #f5f5f5;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        @media screen and (max-width: 600px) {
            .posts-table thead {
                display: none;
            }

            .posts-table tr {
                display: block;
                margin-bottom: 0.625em;
                border-bottom: 3px solid #ddd;
            }

            .posts-table td {
                display: block;
                text-align: right;
                padding: 10px;
                position: relative;
                padding-left: 50%;
            }

            .posts-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
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
                <a href="bd/logout.php">Logout</a>
                <?php } else { ?>
                    <a href="pages/login.php">Login</a>
                    <a href="pages/signup.php">Sign Up</a>
                <?php } ?>
            </div>
        </div>

        <main class="main">
            <h1 class="welcome">Hello Admin!</h1>

            <nav class="admin-nav">
                <a href="admin.php">MANAGE POSTS</a>
                <a href="admin2.php">BLOG SUBSCRIBERS</a>
                <a href="admin3.php">UPLOAD POST</a>
            </nav>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <section>
                <h2 class="section-title">Manage Posts</h2>
                <div class="posts-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post) { ?>
                                <tr>
                                    <td data-label="Title"><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td data-label="Author"><?php echo htmlspecialchars($post['author_name']); ?></td>
                                    <td data-label="Created At"><?php echo htmlspecialchars($post['created_at']); ?></td>
                                    <td data-label="Actions">
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <button type="submit" name="delete_post" class="delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($total_pages > 1): ?>
                        <?php if ($page > 1): ?>
                            <a href="?page=1">&laquo; First</a>
                            <a href="?page=<?php echo $page - 1; ?>">&lsaquo; Previous</a>
                        <?php else: ?>
                            <span class="disabled">&laquo; First</span>
                            <span class="disabled">&lsaquo; Previous</span>
                        <?php endif; ?>

                        <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $start + 4);
                            $start = max(1, $end - 4);

                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $page) {
                                    echo "<span class=\"active\">$i</span>";
                                } else {
                                    echo "<a href=\"?page=$i\">$i</a>";
                                }
                            }
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>">Next &rsaquo;</a>
                            <a href="?page=<?php echo $total_pages; ?>">Last &raquo;</a>
                        <?php else: ?>
                            <span class="disabled">Next &rsaquo;</span>
                            <span class="disabled">Last &raquo;</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
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
