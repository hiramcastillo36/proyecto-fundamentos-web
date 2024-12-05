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

    // Pagination settings
    $items_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;

    // Get paginated subscribers and total count
    $subscribers = $cad->getNewsletterSubscribers($offset, $items_per_page);
    $total_subscribers = $cad->getTotalSubscribers();
    $total_pages = ceil($total_subscribers / $items_per_page);

    $isNewsletterSubscriber = $user ? $cad->getUserWithNewsletter($user['id']) : false;
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BYTE Y PIXEL - Admin</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        /* Estilos para tabla responsiva */
        .subscribers-table {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .subscribers-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 400px; /* Asegura un mínimo de ancho */
            background-color: white;
        }

        .subscribers-table th,
        .subscribers-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .subscribers-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .subscribers-table tr:hover {
            background-color: #f5f5f5;
        }

        /* Estilos para pantallas pequeñas */
        @media screen and (max-width: 600px) {
            .subscribers-table {
                border: 0;
            }

            .subscribers-table table {
                border: 0;
            }

            .subscribers-table thead {
                border: none;
                clip: rect(0 0 0 0);
                height: 1px;
                margin: -1px;
                overflow: hidden;
                padding: 0;
                position: absolute;
                width: 1px;
            }

            .subscribers-table tr {
                border-bottom: 3px solid #ddd;
                display: block;
                margin-bottom: 0.625em;
                background-color: white;
            }

            .subscribers-table td {
                border-bottom: 1px solid #ddd;
                display: block;
                font-size: 0.8em;
                text-align: right;
                padding: 10px;
            }

            .subscribers-table td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
            }

            .subscribers-table td:last-child {
                border-bottom: 0;
            }

            /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            gap: 8px;
        }

        .pagination a, .pagination span {
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #f5f5f5;
        }

        .pagination .active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination .disabled {
            color: #999;
            pointer-events: none;
        }

        @media screen and (max-width: 600px) {
            .pagination {
                flex-wrap: wrap;
            }

            .pagination a, .pagination span {
                padding: 6px 12px;
                font-size: 0.9em;
            }
        }

        }
    </style>
</head>
<body>
    <div class="container">
    <!-- Vista de Suscriptores -->
    <div class="topnav" id="myTopnav">
        <a href="../index.php" class="active">BYTE Y PIXEL</a>
        <div class="navoptions" id="navOptions">
          <a href="about.php">About</a>
            <?php if ($user) { ?>
                <?php if ($isNewsletterSubscriber) { ?>
                    <a href="newsletter.php">Newsletter</a>
                <?php } ?>
                <?php if ($user['role'] === 'admin') { ?>
                    <a href="admin.php">Admin</a>

                <?php } ?>
                <a href="../bd/logout.php">Logout</a>
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

        <!-- Tabla de Suscriptores -->
        <section>
        <h2 class="section-title">Blog Subscribers</h2>
        <div class="subscribers-table">
            <table>
                <thead>
                    <tr>
                        <th>Email Address</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $subscriber) { ?>
                        <tr>
                            <td data-label="Email Address"><?php echo htmlspecialchars($subscriber['email']); ?></td>
                            <td data-label="Created At"><?php echo htmlspecialchars($subscriber['created_at']); ?></td>
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

    <!-- Vista de Upload Post -->
    <main class="main" style="display: none;">
        <h1 class="welcome">Hello Admi!</h1>

        <nav class="admin-nav">
            <a href="#">BLOG SUBSCRIBER</a>
            <a href="#">UPLOAD POST</a>
        </nav>

        <section>
            <h2 class="section-title">Upload post</h2>
            <div class="upload-form">
                <form>
                    <div class="form-group">
                        <input type="text" placeholder="Title post">
                    </div>

                    <div class="editor">
                        <div class="editor-toolbar">
                            <!-- 20 botones de ejemplo para el editor -->
                            <button class="editor-button">T</button>
                            <button class="editor-button">B</button>
                            <button class="editor-button">I</button>
                            <!-- ... más botones ... -->
                        </div>
                        <div class="editor-content">
                            <h1>An h1 header</h1>
                            <p>Paragraphs are separated by a blank line.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <p class="upload-text">Upload Principal Image</p>
                        <div class="upload-area">
                            <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <p class="upload-text">Drag & drop files or <span style="color: blue;">Browse</span></p>
                            <p class="upload-formats">Supported formats: JPEG, PNG, GIF, MP4, PDF, PSD, AI, Word, PPT</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="text" placeholder="Author">
                    </div>

                    <button type="submit" class="save-button">Save post</button>
                </form>
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
